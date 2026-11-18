<?php
/**
 * File UserController.php
 *
 * @author Tuan Duong <bacduong@gmail.com>
 * @package Laravue
 * @version 1.0
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Laravue\Models\SesiKasir;
use App\Services\SesiKasirService;
use Illuminate\Http\Request;

class SesiKasirController extends BaseController
{
    protected $service;

    public function __construct(SesiKasirService $service)
    {
        $this->service = $service;
    }

    /**
     * Cek sesi aktif user yang login.
     */
    public function aktif(Request $request)
    {
        $sesi = SesiKasir::sesiAktif($request->user()->id);

        return response()->json([
            'data' => $sesi ? $sesi->load('kasir') : null,
        ]);
    }

    /**
     * Buka sesi kasir.
     */
    public function buka(Request $request)
    {
        $validated = $request->validate([
            'kas_awal' => 'required|numeric|min:0',
            'catatan'  => 'nullable|string|max:500',
        ]);

        try {
            $sesi = $this->service->buka(
                $request->user()->id,
                $validated['kas_awal'],
                $validated['catatan'] ?? null
            );

            return response()->json([
                'message' => 'Sesi kasir berhasil dibuka.',
                'data'    => $sesi,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Tutup sesi kasir.
     */
    public function tutup(Request $request, $id)
    {
        $validated = $request->validate([
            'kas_akhir_fisik' => 'required|numeric|min:0',
            'catatan'         => 'nullable|string|max:500',
        ]);

        try {
            $sesi = $this->service->tutup(
                $id,
                $request->user()->id,
                $validated['kas_akhir_fisik'],
                $validated['catatan'] ?? null
            );

            return response()->json([
                'message'   => 'Sesi kasir berhasil ditutup.',
                'data'      => $sesi,
                'ringkasan' => [
                    'kas_awal'         => $sesi->kas_awal,
                    'total_tunai'      => $sesi->total_tunai,
                    'kas_akhir_sistem' => $sesi->kas_akhir_sistem,
                    'kas_akhir_fisik'  => $sesi->kas_akhir_fisik,
                    'selisih'          => $sesi->selisih,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Riwayat sesi kasir.
     */
    public function index(Request $request)
    {
        $query = SesiKasir::with(['kasir', 'penutup'])
            ->orderByDesc('waktu_buka');

        if ($request->filled('dari')) {
            $query->whereDate('waktu_buka', '>=', $request->dari);
        }

        if ($request->filled('sampai')) {
            $query->whereDate('waktu_buka', '<=', $request->sampai);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json(
            $query->paginate($request->get('per_page', 15))
        );
    }

    /**
     * Detail sesi kasir.
     */
    public function show($id)
    {
        $sesi = SesiKasir::with([
            'kasir',
            'penutup',
            'transaksi.detail.produk',
        ])->findOrFail($id);

        return response()->json(['data' => $sesi]);
    }
}