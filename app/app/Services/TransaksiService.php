<?php

namespace App\Services;

use App\Laravue\Models\Produk;
use App\Laravue\Models\SesiKasir;
use App\Laravue\Models\StokMutasi;
use App\Laravue\Models\Transaksi;
use App\Laravue\Models\TransaksiDetail;
use Illuminate\Support\Facades\DB;

class TransaksiService
{
    /**
     * Simpan transaksi penjualan.
     */
    public function simpan($userId, array $data)
    {
        $sesi = SesiKasir::sesiAktif($userId);

        if (!$sesi) {
            throw new \Exception(
                'Anda belum membuka kas. Buka kas terlebih dahulu.'
            );
        }

        if (empty($data['items']) || !is_array($data['items'])) {
            throw new \Exception('Item transaksi belum diisi.');
        }

        return DB::transaction(function () use ($userId, $data, $sesi) {
            /*
             * Gabungkan produk yang sama agar validasi stok tidak dilakukan
             * secara terpisah untuk baris produk yang identik.
             */
            $qtyPerProduk = [];

            foreach ($data['items'] as $item) {
                if (
                    !isset($item['produk_id']) ||
                    !isset($item['qty']) ||
                    !is_numeric($item['qty'])
                ) {
                    throw new \Exception('Data item transaksi tidak valid.');
                }

                $produkId = (int) $item['produk_id'];
                $qtyInput = (float) $item['qty'];

                if ($produkId <= 0 || $qtyInput <= 0 || floor($qtyInput) != $qtyInput) {
                    throw new \Exception('Produk dan qty transaksi tidak valid.');
                }

                if (!isset($qtyPerProduk[$produkId])) {
                    $qtyPerProduk[$produkId] = 0;
                }

                $qtyPerProduk[$produkId] += (int) $qtyInput;
            }

            // Urutkan ID untuk mengurangi risiko deadlock saat transaksi bersamaan.
            ksort($qtyPerProduk);

            $produkList = Produk::whereIn('id', array_keys($qtyPerProduk))
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $subtotal = 0;
            $items = [];

            foreach ($qtyPerProduk as $produkId => $qty) {
                $produk = $produkList->get($produkId);

                if (!$produk) {
                    throw new \Exception('Produk dengan ID ' . $produkId . ' tidak ditemukan.');
                }

                if ((int) $produk->stok < $qty) {
                    throw new \Exception(
                        "Stok produk {$produk->nama} tidak mencukupi. " .
                        "Tersedia: {$produk->stok}, diperlukan: {$qty}."
                    );
                }

                $harga = round((float) $produk->harga_jual, 2);
                $itemSubtotal = round($harga * $qty, 2);

                $subtotal += $itemSubtotal;

                $items[] = [
                    'produk' => $produk,
                    'qty' => $qty,
                    'harga' => $harga,
                    'subtotal' => $itemSubtotal,
                ];
            }

            $subtotal = round($subtotal, 2);
            $diskon = round((float) ($data['diskon'] ?? 0), 2);
            $pajak = round((float) ($data['pajak'] ?? 0), 2);

            if ($diskon < 0 || $diskon > $subtotal) {
                throw new \Exception(
                    'Diskon tidak boleh kurang dari 0 atau melebihi subtotal.'
                );
            }

            if ($pajak < 0) {
                throw new \Exception('Pajak tidak boleh kurang dari 0.');
            }

            // Pajak merupakan komponen tagihan, bukan pendapatan penjualan.
            $total = round($subtotal - $diskon + $pajak, 2);

            if ($total < 0) {
                throw new \Exception('Total transaksi tidak boleh kurang dari 0.');
            }

            $bayar = round((float) ($data['bayar'] ?? $total), 2);

            if ($bayar < 0) {
                throw new \Exception('Jumlah bayar tidak boleh kurang dari 0.');
            }

            $kembalian = round($bayar - $total, 2);

            if ($kembalian < 0) {
                throw new \Exception('Jumlah bayar kurang dari total.');
            }

            /*
             * Buat transaksi setelah semua validasi lolos.
             */
            $transaksi = Transaksi::create([
                'kode' => Transaksi::generateKode(),
                'user_id' => $userId,
                'sesi_kasir_id' => $sesi->id,
                'subtotal' => $subtotal,
                'diskon' => $diskon,
                'pajak' => $pajak,
                'total' => $total,
                'bayar' => $bayar,
                'kembalian' => $kembalian,
                'metode_pembayaran' => $data['metode_pembayaran'] ?? 'cash',
                'status' => 'selesai',
                'catatan' => $data['catatan'] ?? null,
            ]);

            /*
             * Buat detail dan keluarkan stok.
             * Stok tetap diubah melalui StokMutasi sebagai satu-satunya pintu.
             */
            foreach ($items as $item) {
                TransaksiDetail::create([
                    'transaksi_id' => $transaksi->id,
                    'produk_id' => $item['produk']->id,
                    'nama_produk' => $item['produk']->nama,
                    'harga' => $item['harga'],
                    'qty' => $item['qty'],
                    'subtotal' => $item['subtotal'],
                ]);

                StokMutasi::catat(
                    $item['produk'],
                    $userId,
                    'keluar',
                    $item['qty'],
                    $transaksi->kode,
                    'Penjualan'
                );
            }

            return $transaksi->load([
                'detail',
                'kasir',
            ]);
        });
    }
    
    /**
     * Batalkan transaksi dan kembalikan stok.
     */
    public function batal($transaksiId, $userId)
    {
        return DB::transaction(function () use ($transaksiId, $userId) {

            // Pastikan user yang melakukan pembatalan valid.
            $user = \App\Laravue\Models\User::find($userId);

            if (!$user) {
                throw new \Exception(
                    'Pengguna yang membatalkan transaksi tidak ditemukan. Silakan login ulang.'
                );
            }

            $transaksi = Transaksi::with('detail')
                ->lockForUpdate()
                ->findOrFail($transaksiId);

            if ($transaksi->status === 'batal') {
                throw new \Exception('Transaksi sudah dibatalkan.');
            }

            if ($transaksi->status !== 'selesai') {
                throw new \Exception('Transaksi tidak dapat dibatalkan.');
            }

            foreach ($transaksi->detail as $detail) {

                $produk = Produk::where('id', $detail->produk_id)
                    ->lockForUpdate()
                    ->first();

                if (!$produk) {
                    throw new \Exception(
                        'Produk ID ' . $detail->produk_id . ' tidak ditemukan.'
                    );
                }

                StokMutasi::catat(
                    $produk,
                    $user->id,
                    'masuk',
                    (int) $detail->qty,
                    $transaksi->kode,
                    'Pembatalan transaksi'
                );
            }

            $transaksi->status = 'batal';
            $transaksi->save();

            return $transaksi->load([
                'detail',
                'kasir',
            ]);
        });
    }

}
