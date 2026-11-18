<?php

namespace App\Http\Controllers\Api;

use App\Services\LaporanService;
use Illuminate\Http\Request;

class LaporanController extends BaseController
{
    protected $service;

    public function __construct(LaporanService $service)
    {
        $this->service = $service;
    }

    /**
     * Dashboard hari ini.
     */
    public function dashboard()
    {
        return response()->json([
            'data' => $this->service->dashboardHariIni(),
        ]);
    }

    /**
     * Laporan harian.
     */
    public function harian(Request $request)
    {
        $validated = $request->validate([
            'dari' => 'required|date',
            'sampai' => 'required|date|after_or_equal:dari',
            'user_id' => 'nullable|integer|exists:users,id',
        ]);

        return response()->json([
            'data' => $this->service->harian(
                $validated['dari'],
                $validated['sampai'],
                $validated['user_id'] ?? null
            ),
        ]);
    }

    /**
     * Laporan per shift.
     */
    public function perShift(Request $request)
    {
        $validated = $request->validate([
            'dari' => 'required|date',
            'sampai' => 'required|date|after_or_equal:dari',
        ]);

        return response()->json([
            'data' => $this->service->perShift(
                $validated['dari'],
                $validated['sampai']
            ),
        ]);
    }

    /**
     * Laporan per produk.
     */
    public function perProduk(Request $request)
    {
        $validated = $request->validate([
            'dari' => 'required|date',
            'sampai' => 'required|date|after_or_equal:dari',
            'limit' => 'nullable|integer|min:1|max:500',
        ]);

        return response()->json([
            'data' => $this->service->perProduk(
                $validated['dari'],
                $validated['sampai'],
                $validated['limit'] ?? 50
            ),
        ]);
    }

    /**
     * Laporan per kategori.
     */
    public function perKategori(Request $request)
    {
        $validated = $request->validate([
            'dari' => 'nullable|date',
            'sampai' => 'nullable|date|after_or_equal:dari',
            'keyword' => 'nullable|string|max:100',
            'page' => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        return response()->json([
            'data' => $this->service->perKategori(
                $validated['dari'] ?? null,
                $validated['sampai'] ?? null,
                $validated['keyword'] ?? null,
                $validated['page'] ?? 1,
                $validated['limit'] ?? 15
            ),
        ]);
    }

    /**
     * Laporan laba rugi.
     */
    public function labaRugi(Request $request)
    {
        $validated = $request->validate([
            'dari' => 'required|date',
            'sampai' => 'required|date|after_or_equal:dari',
        ]);

        return response()->json([
            'data' => $this->service->labaRugi(
                $validated['dari'],
                $validated['sampai']
            ),
        ]);
    }

    /**
     * Laporan transaksi.
     */
    public function transaksi(Request $request)
    {
        $validated = $request->validate([
            'dari' => 'required|date',
            'sampai' => 'required|date|after_or_equal:dari',
            'user_id' => 'nullable|integer|exists:users,id',
            'page' => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        return response()->json([
            'data' => $this->service->transaksi(
                $validated['dari'],
                $validated['sampai'],
                $validated['page'] ?? 1,
                $validated['limit'] ?? 20,
                $validated['user_id'] ?? null
            ),
        ]);
    }

    /**
     * Daftar kasir untuk filter.
     */
    public function kasir()
    {
        return response()->json(
            $this->service->daftarKasir()
        );
    }

    /**
     * Laporan stok.
     */
    public function stok(Request $request)
    {
        $validated = $request->validate([
            'keyword' => 'nullable|string|max:100',
            'kategori_id' => 'nullable|integer',
            'page' => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        return response()->json([
            'data' => $this->service->stok(
                $validated['keyword'] ?? null,
                $validated['kategori_id'] ?? null,
                $validated['page'] ?? 1,
                $validated['limit'] ?? 20
            ),
        ]);
    }
}