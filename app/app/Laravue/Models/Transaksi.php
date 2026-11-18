<?php

namespace App\Laravue\Models;

use App\Laravue\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaksi extends Model
{
    use SoftDeletes;

    protected $table = 'transaksi';

    protected $fillable = [
        'kode',
        'user_id',
        'sesi_kasir_id',
        'subtotal',
        'diskon',
        'pajak',
        'total',
        'bayar',
        'kembalian',
        'metode_pembayaran',
        'status',
        'catatan',
    ];

    protected $casts = [
        'subtotal'  => 'decimal:2',
        'diskon'    => 'decimal:2',
        'pajak'     => 'decimal:2',
        'total'     => 'decimal:2',
        'bayar'     => 'decimal:2',
        'kembalian' => 'decimal:2',
    ];

    // =====================================================
    // RELASI
    // =====================================================

    public function kasir()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sesiKasir()
    {
        return $this->belongsTo(SesiKasir::class, 'sesi_kasir_id');
    }

    public function detail()
    {
        return $this->hasMany(TransaksiDetail::class, 'transaksi_id');
    }

    // =====================================================
    // SCOPE
    // =====================================================

    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }

    public function scopeHariIni($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopePeriode($query, $dari, $sampai)
    {
        return $query->whereBetween('created_at', [$dari, $sampai]);
    }

    // =====================================================
    // HELPER
    // =====================================================

    public static function generateKode()
    {
        $prefix = 'TRX-' . now()->format('Ymd') . '-';

        $last = self::where('kode', 'like', $prefix . '%')
            ->lockForUpdate()
            ->orderByDesc('kode')
            ->first();

        $sequence = $last
            ? ((int) substr($last->kode, -4)) + 1
            : 1;

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}