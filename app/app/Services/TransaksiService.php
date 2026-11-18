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

        return DB::transaction(function () use (
            $userId,
            $data,
            $sesi
        ) {
            $subtotal = 0;
            $items = [];

            /*
             * Validasi seluruh item terlebih dahulu.
             */
            foreach ($data['items'] as $item) {
                $produk = Produk::lockForUpdate()
                    ->findOrFail($item['produk_id']);

                $qty = (int) $item['qty'];

                if ($qty <= 0) {
                    throw new \Exception(
                        "Qty produk {$produk->nama} harus lebih dari 0."
                    );
                }

                if ((int) $produk->stok < $qty) {
                    throw new \Exception(
                        "Stok produk {$produk->nama} tidak mencukupi. " .
                        "Tersedia: {$produk->stok}, diperlukan: {$qty}."
                    );
                }

                $harga = (float) $produk->harga_jual;
                $itemSubtotal = $harga * $qty;

                $subtotal += $itemSubtotal;

                $items[] = [
                    'produk' => $produk,
                    'qty' => $qty,
                    'harga' => $harga,
                    'subtotal' => $itemSubtotal,
                ];
            }

            $diskon = (float) ($data['diskon'] ?? 0);
            $pajak = (float) ($data['pajak'] ?? 0);

            $total = $subtotal - $diskon + $pajak;

            if ($total < 0) {
                throw new \Exception(
                    'Total transaksi tidak boleh kurang dari 0.'
                );
            }

            $bayar = (float) ($data['bayar'] ?? $total);

            $kembalian = $bayar - $total;

            if ($kembalian < 0) {
                throw new \Exception(
                    'Jumlah bayar kurang dari total.'
                );
            }

            /*
             * Buat transaksi.
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
                'metode_pembayaran' =>
                    $data['metode_pembayaran'] ?? 'cash',
                'status' => 'selesai',
                'catatan' => $data['catatan'] ?? null,
            ]);

            /*
             * Buat detail dan keluarkan stok.
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

                /*
                 * Satu-satunya pintu perubahan stok.
                 */
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
    public function batal($userId, $transaksiId)
    {
        return DB::transaction(function () use (
            $userId,
            $transaksiId
        ) {
            $transaksi = Transaksi::with('detail')
                ->lockForUpdate()
                ->findOrFail($transaksiId);

            if ($transaksi->status === 'batal') {
                throw new \Exception(
                    'Transaksi sudah dibatalkan.'
                );
            }

            if ($transaksi->status !== 'selesai') {
                throw new \Exception(
                    'Transaksi tidak dapat dibatalkan.'
                );
            }

            foreach ($transaksi->detail as $detail) {
                $produk = Produk::findOrFail(
                    $detail->produk_id
                );

                StokMutasi::catat(
                    $produk,
                    $userId,
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