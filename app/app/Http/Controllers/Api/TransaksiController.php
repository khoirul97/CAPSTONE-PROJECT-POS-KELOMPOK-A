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
use App\Laravue\Models\Transaksi;
use App\Services\TransaksiService;
use Illuminate\Http\Request;

class TransaksiController extends BaseController
{
    protected $service;

    public function __construct(TransaksiService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $query = Transaksi::with(['kasir', 'sesiKasir'])
            ->orderByDesc('created_at');

        if ($request->filled('dari')) {
            $query->whereDate('created_at', '>=', $request->dari);
        }

        if ($request->filled('sampai')) {
            $query->whereDate('created_at', '<=', $request->sampai);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('sesi_kasir_id')) {
            $query->where('sesi_kasir_id', $request->sesi_kasir_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        return response()->json(
            $query->paginate($request->get('per_page', 15))
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items'                  => 'required|array|min:1',
            'items.*.produk_id'      => 'required|exists:produk,id',
            'items.*.qty'            => 'required|integer|min:1',
            'diskon'                 => 'nullable|numeric|min:0',
            'pajak'                  => 'nullable|numeric|min:0',
            'bayar'                  => 'nullable|numeric|min:0',
            'metode_pembayaran'      => 'nullable|in:cash,transfer,qris',
            'catatan'                => 'nullable|string',
        ]);

        try {
            $transaksi = $this->service->simpan(
                $request->user()->id,
                $validated
            );

            return response()->json([
                'message' => 'Transaksi berhasil.',
                'data'    => $transaksi,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function show($id)
    {
        $query = Transaksi::with([
            'kasir',
            'sesiKasir',
            'detail.produk',
        ]);

        $user = $requestUser = request()->user();

        if ($user && $user->isKasir()) {
            $query->where('user_id', $user->id);
        }

        $transaksi = $query->findOrFail($id);

        return response()->json([
            'data' => $transaksi,
        ]);
    }

        
    public function batal(Request $request, $id)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'message' => 'Sesi login tidak valid. Silakan login kembali.',
                ], 401);
            }

            $transaksi = $this->service->batal(
                $id,
                $user->id
            );

            return response()->json([
                'message' => 'Transaksi berhasil dibatalkan.',
                'data' => $transaksi,
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}