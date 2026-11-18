<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Laravue\Models\Pengaturan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengaturanController extends BaseController
{
    public function index()
    {
        return response()->json([
            'data' => Pengaturan::orderBy('key')->get(),
        ]);
    }

    /**
     * Update pengaturan biasa
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings'            => 'required|array',
            'settings.*.key'      => 'required|string',
            'settings.*.value'    => 'nullable',
            'settings.*.tipe'     => 'nullable|string|in:string,number,boolean,json,image',
        ]);

        foreach ($validated['settings'] as $item) {
            Pengaturan::set(
                $item['key'],
                $item['value'] ?? null,
                $item['tipe'] ?? 'string'
            );
        }

        return response()->json([
            'message' => 'Pengaturan berhasil diperbarui.',
            'data'    => Pengaturan::all(),
        ]);
    }

    /**
     * Upload / ganti logo kedai
     */
    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ], [
            'logo.required' => 'Logo kedai wajib dipilih.',
            'logo.image'    => 'File harus berupa gambar.',
            'logo.mimes'    => 'Format logo harus JPG, JPEG, PNG, atau WEBP.',
            'logo.max'      => 'Ukuran logo maksimal 2 MB.',
        ]);

        $setting = Pengaturan::where('key', 'logo_kedai')->first();

        // Hapus logo lama jika ada
        if ($setting && $setting->value) {
            $oldPath = $setting->value;

            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        // Simpan logo baru
        // Hasil: logo-kedai/namafile.png
        $path = $request->file('logo')->store(
            'logo-kedai',
            'public'
        );

        // Simpan PATH saja ke database
        $setting = Pengaturan::updateOrCreate(
            ['key' => 'logo_kedai'],
            [
                'value' => $path,
                'tipe'  => 'image',
            ]
        );

        return response()->json([
            'message' => 'Logo kedai berhasil diupload.',
            'data' => [
                'key'   => $setting->key,
                'value' => $setting->value,
                'tipe'  => $setting->tipe,
            ],
        ]);
    }

    /**
     * Hapus logo kedai
     */
    public function deleteLogo()
    {
        $setting = Pengaturan::where('key', 'logo_kedai')->first();

        if (!$setting || !$setting->value) {
            return response()->json([
                'message' => 'Logo kedai belum tersedia.',
            ]);
        }

        $oldPath = $setting->value;

        if (Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        $setting->update([
            'value' => '',
            'tipe'  => 'image',
        ]);

        return response()->json([
            'message' => 'Logo kedai berhasil dihapus.',
        ]);
    }
}
