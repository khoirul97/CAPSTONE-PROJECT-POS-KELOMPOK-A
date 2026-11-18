<?php

namespace App\Laravue\Models;

use App\Laravue\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SesiKasir extends Model
{
    use SoftDeletes;

    protected $table = 'sesi_kasir';

    protected $fillable = [
        'kode',
        'user_id',
        'user_tutup_id',
        'waktu_buka',
        'waktu_tutup',
        'kas_awal',
        'kas_akhir_sistem',
        'kas_akhir_fisik',
        'selisih',
        'total_transaksi',
        'total_penjualan',
        'total_tunai',
        'total_non_tunai',
        'status',
        'catatan_buka',
        'catatan_tutup',
    ];

    protected $casts = [
        'waktu_buka'  => 'datetime',
        'waktu_tutup' => 'datetime',
    ];

    // =====================================================
    // RELASI
    // =====================================================

    public function kasir()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function penutup()
    {
        return $this->belongsTo(User::class, 'user_tutup_id');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'sesi_kasir_id');
    }

    // =====================================================
    // SCOPE
    // =====================================================

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeUntukUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // =====================================================
    // HELPER
    // =====================================================

    public static function sesiAktif($userId)
    {
        return self::open()
            ->untukUser($userId)
            ->latest('waktu_buka')
            ->first();
    }

    public function hitungRingkasan()
    {
        $query = $this->transaksi()->where('status', 'selesai');

        $this->total_transaksi = (clone $query)->count();
        $this->total_penjualan = (clone $query)->sum('total');
        $this->total_tunai     = (clone $query)
            ->where('metode_pembayaran', 'cash')
            ->sum('total');
        $this->total_non_tunai = (clone $query)
            ->where('metode_pembayaran', '!=', 'cash')
            ->sum('total');

        $this->kas_akhir_sistem = $this->kas_awal + $this->total_tunai;

        return $this;
    }
}