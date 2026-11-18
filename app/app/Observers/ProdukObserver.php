<?php

namespace App\Observers;

use App\Laravue\Models\Produk;
use App\Laravue\Models\StokMutasi;
use Illuminate\Support\Facades\Auth;

class ProdukObserver
{
    /**
     * Produk baru dibuat.
     *
     * Jika produk memiliki stok awal > 0,
     * catat sebagai mutasi stok awal.
     */
    public function created(Produk $produk): void
    {
        if ((int) $produk->stok <= 0) {
            return;
        }

        StokMutasi::create([
            'produk_id' => $produk->id,
            'user_id' => Auth::id() ?? 1,
            'tipe' => 'masuk',
            'jumlah' => $produk->stok,
            'stok_sebelum' => 0,
            'stok_sesudah' => $produk->stok,
            'referensi' => $produk->kode,
            'keterangan' => 'Stok awal produk baru',
        ]);
    }

    /**
     * Jangan melakukan apa-apa ketika stok produk berubah.
     *
     * Perubahan stok harus selalu melalui:
     * StokMutasi::catat()
     */
    public function updated(Produk $produk): void
    {
        // Sengaja kosong.
        //
        // Jangan membuat StokMutasi di sini karena
        // StokMutasi::catat() sudah menangani:
        // 1. perubahan stok
        // 2. pencatatan mutasi
        //
        // Jika observer ikut mencatat perubahan stok,
        // transaksi akan menghasilkan mutasi ganda.
    }
}