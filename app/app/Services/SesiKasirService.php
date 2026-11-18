<?php

namespace App\Services;

use App\Laravue\Models\SesiKasir;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SesiKasirService
{
    public function buka($userId, $kasAwal, $catatan = null)
    {
        $existing = SesiKasir::sesiAktif($userId);

        if ($existing) {
            throw new \Exception(
                'Masih ada sesi kasir yang belum ditutup. ' .
                'Tutup sesi terlebih dahulu.'
            );
        }

        return DB::transaction(function () use ($userId, $kasAwal, $catatan) {
            return SesiKasir::create([
                'kode'         => $this->generateKode(),
                'user_id'      => $userId,
                'waktu_buka'   => now(),
                'kas_awal'     => $kasAwal,
                'status'       => 'open',
                'catatan_buka' => $catatan,
            ]);
        });
    }

    public function tutup($sesiId, $userId, $kasFisik, $catatan = null)
    {
        $sesi = SesiKasir::findOrFail($sesiId);

        if ($sesi->status !== 'open') {
            throw new \Exception('Sesi kasir sudah ditutup.');
        }

        return DB::transaction(function () use ($sesi, $userId, $kasFisik, $catatan) {
            // Simpan waktu buka asli sebelum melakukan perubahan apa pun.
            $waktuBuka = $sesi->waktu_buka;

            $sesi->hitungRingkasan();

            $sesi->kas_akhir_fisik = $kasFisik;
            $sesi->selisih = $kasFisik - $sesi->kas_akhir_sistem;
            $sesi->user_tutup_id = $userId;
            $sesi->waktu_buka = $waktuBuka;
            $sesi->waktu_tutup = now();
            $sesi->status = 'closed';
            $sesi->catatan_tutup = $catatan;

            $sesi->save();

            return $sesi->fresh();
        });
    }

    private function generateKode()
    {
        $prefix = 'SESI-' . now()->format('Ymd') . '-';

        $last = SesiKasir::where('kode', 'like', $prefix . '%')
            ->lockForUpdate()
            ->orderByDesc('kode')
            ->first();

        $sequence = $last
            ? ((int) Str::afterLast($last->kode, '-')) + 1
            : 1;

        return $prefix . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }
}