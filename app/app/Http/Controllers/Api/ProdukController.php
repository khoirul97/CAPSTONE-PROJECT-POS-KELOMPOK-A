<?php
/**
 * File ProdukController.php
 *
 * @author Tuan Duong <bacduong@gmail.com>
 * @package Laravue
 * @version 1.0
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Laravue\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdukController extends BaseController
{
    public function index(Request $request)
    {
        $query = Produk::with('kategori');

        if ($request->filled('q')) {
            $query->cari($request->q);
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->boolean('stok_rendah')) {
            $query->stokRendah();
        }

        if ($request->filled('is_active')) {
            $query->where(
                'is_active',
                $request->boolean('is_active')
            );
        }

        $perPage = (int) $request->get('per_page', 20);

        // Maksimal 20 produk per halaman
        $perPage = min(max($perPage, 1), 20);

        $produk = $query
            ->orderBy('nama')
            ->paginate($perPage);

        /*
         * Tambahkan URL gambar yang siap digunakan oleh Vue.
         */
        $produk->getCollection()->transform(function ($item) {
            $item->gambar_url = $item->gambar
                ? Storage::disk('public')->url($item->gambar)
                : null;

            return $item;
        });

        return response()->json($produk);
    }

    /**
     * Upload gambar produk ke storage Laravel.
     *
     * @return string|null Path relatif gambar (contoh: uploads/produk/xxx.jpg)
     */
    private function uploadGambar(Request $request)
    {
        if (!$request->hasFile('gambar')) {
            return null;
        }

        $file = $request->file('gambar');

        // Simpan ke storage/app/public/uploads/produk
        // Return path relatif: uploads/produk/namafile.jpg
        $path = $file->store('uploads/produk', 'public');

        return $path;
    }

    /**
     * Hapus file gambar produk dari storage.
     */
    private function hapusGambar($path)
    {
        if (!$path) {
            return;
        }

        // Normalisasi path
        $relativePath = ltrim(
            str_replace('\\', '/', $path),
            '/'
        );

        // Pastikan hanya menghapus dari folder uploads/produk
        if (strpos($relativePath, 'uploads/produk/') !== 0) {
            return;
        }

        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_id'  => 'nullable|exists:kategori,id',
            'kode'         => 'nullable|string|unique:produk,kode',
            'nama'         => 'required|string|max:150',
            'deskripsi'    => 'nullable|string',
            'harga_beli'   => 'required|numeric|min:0',
            'harga_jual'   => 'required|numeric|min:0',
            'stok'         => 'required|integer|min:0',
            'stok_minimal' => 'nullable|integer|min:0',
            'satuan'       => 'nullable|string|max:20',
            'gambar'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active'    => 'nullable|boolean',
        ]);

        if (empty($validated['kode'])) {
            $validated['kode'] = Produk::generateKode();
        }

        // Jangan masukkan UploadedFile langsung ke create()
        unset($validated['gambar']);

        $gambar = $this->uploadGambar($request);

        if ($gambar) {
            $validated['gambar'] = $gambar;
        }

        $produk = Produk::create($validated);

        return response()->json([
            'message' => 'Produk berhasil ditambahkan.',
            'data'    => $produk->load('kategori'),
        ], 201);
    }

    public function show($id)
    {
        $produk = Produk::with([
            'kategori',
            'stokMutasi' => function ($q) {
                $q->latest()->limit(20);
            },
        ])->findOrFail($id);

        // Tambahkan URL gambar
        $produk->gambar_url = $produk->gambar
            ? Storage::disk('public')->url($produk->gambar)
            : null;

        return response()->json([
            'data' => $produk,
        ]);
    }

    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        $validated = $request->validate([
            'kategori_id'  => 'nullable|exists:kategori,id',
            'kode'         => 'nullable|string|unique:produk,kode,' . $id,
            'nama'         => 'required|string|max:150',
            'deskripsi'    => 'nullable|string',
            'harga_beli'   => 'required|numeric|min:0',
            'harga_jual'   => 'required|numeric|min:0',
            'stok_minimal' => 'nullable|integer|min:0',
            'satuan'       => 'nullable|string|max:20',
            'gambar'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active'    => 'nullable|boolean',
        ]);

        // Jangan masukkan UploadedFile langsung ke update()
        unset($validated['gambar']);

        /*
         * Jika ada gambar baru:
         * 1. Upload gambar baru
         * 2. Hapus gambar lama
         * 3. Simpan path gambar baru
         */
        if ($request->hasFile('gambar')) {
            $gambarLama = $produk->gambar;

            $gambarBaru = $this->uploadGambar($request);

            if ($gambarBaru) {
                $validated['gambar'] = $gambarBaru;

                $this->hapusGambar($gambarLama);
            }
        }

        $produk->update($validated);

        $produk = $produk->fresh()->load('kategori');

        // Tambahkan URL gambar
        $produk->gambar_url = $produk->gambar
            ? Storage::disk('public')->url($produk->gambar)
            : null;

        return response()->json([
            'message' => 'Produk berhasil diperbarui.',
            'data'    => $produk,
        ]);
    }

    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);

        // Cek apakah sudah pernah dipakai transaksi
        if ($produk->transaksiDetail()->count() > 0) {
            return response()->json([
                'message' =>
                    'Produk tidak bisa dihapus karena sudah pernah ditransaksikan.',
            ], 422);
        }

        // Hapus file gambar
        $this->hapusGambar($produk->gambar);

        $produk->delete();

        return response()->json([
            'message' => 'Produk berhasil dihapus.',
        ]);
    }
}