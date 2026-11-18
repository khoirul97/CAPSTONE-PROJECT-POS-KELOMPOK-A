<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Laravue\Models\Produk;
use App\Laravue\Models\StokMutasi;
use Illuminate\Http\Request;

class StokMutasiController extends BaseController
{
    /**
     * Daftar mutasi stok.
     */
    public function index(Request $request)
    {
        $query = StokMutasi::with([
            'produk',
            'user',
        ])->orderByDesc('id');

        if ($request->filled('produk_id')) {
            $query->where(
                'produk_id',
                $request->produk_id
            );
        }

        if ($request->filled('tipe')) {
            $query->where(
                'tipe',
                $request->tipe
            );
        }

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {
                $q->where('referensi', 'like', "%{$keyword}%")
                    ->orWhere('keterangan', 'like', "%{$keyword}%")
                    ->orWhereHas('produk', function ($produk) use ($keyword) {
                        $produk->where('nama', 'like', "%{$keyword}%")
                            ->orWhere('kode', 'like', "%{$keyword}%");
                    });
            });
        }

        return response()->json(
            $query->paginate(
                $request->get('limit', 15)
            )
        );
    }

    /**
     * Stok masuk.
     */
    public function masuk(Request $request)
    {
        $validated = $request->validate([
            'produk_id' => [
                'required',
                'integer',
                'exists:produk,id',
            ],
            'jumlah' => [
                'required',
                'integer',
                'min:1',
            ],
            'referensi' => [
                'nullable',
                'string',
                'max:100',
            ],
            'keterangan' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        $produk = Produk::findOrFail(
            $validated['produk_id']
        );

        $mutasi = StokMutasi::catat(
            $produk,
            $request->user()->id,
            'masuk',
            (int) $validated['jumlah'],
            $validated['referensi'] ?? null,
            $validated['keterangan'] ?? null
        );

        return response()->json([
            'success' => true,
            'message' => 'Stok berhasil ditambahkan.',
            'data' => $mutasi->load([
                'produk',
                'user',
            ]),
        ]);
    }

    /**
     * Penyesuaian stok.
     *
     * stok_baru adalah nilai stok akhir yang diinginkan.
     */
    public function penyesuaian(Request $request)
    {
        $validated = $request->validate([
            'produk_id' => [
                'required',
                'integer',
                'exists:produk,id',
            ],
            'stok_baru' => [
                'required',
                'integer',
                'min:0',
            ],
            'keterangan' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        $produk = Produk::findOrFail(
            $validated['produk_id']
        );

        $mutasi = StokMutasi::catat(
            $produk,
            $request->user()->id,
            'penyesuaian',
            (int) $validated['stok_baru'],
            null,
            $validated['keterangan']
                ?? 'Penyesuaian stok'
        );

        return response()->json([
            'success' => true,
            'message' => 'Stok berhasil disesuaikan.',
            'data' => $mutasi->load([
                'produk',
                'user',
            ]),
        ]);
    }
}