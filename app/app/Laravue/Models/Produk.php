<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produk extends Model
{
    use SoftDeletes;

    protected $table = 'produk';

    protected $fillable = [
        'kategori_id',
        'kode',
        'nama',
        'deskripsi',
        'harga_beli',
        'harga_jual',
        'stok',
        'stok_minimal',
        'satuan',
        'gambar',
        'is_active',
    ];

    protected $casts = [
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'gambar_url',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function stokMutasi()
    {
        return $this->hasMany(StokMutasi::class, 'produk_id');
    }

    public function transaksiDetail()
    {
        return $this->hasMany(TransaksiDetail::class, 'produk_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeStokRendah($query)
    {
        return $query->whereColumn(
            'stok',
            '<=',
            'stok_minimal'
        );
    }

    public function scopeCari($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where(
                'nama',
                'like',
                "%{$keyword}%"
            )->orWhere(
                'kode',
                'like',
                "%{$keyword}%"
            );
        });
    }

    public static function generateKode()
    {
        $prefix = 'PRD-' . now()->format('Ymd') . '-';

        $last = self::where(
            'kode',
            'like',
            $prefix . '%'
        )
            ->orderByDesc('kode')
            ->first();

        $sequence = $last
            ? ((int) substr($last->kode, -4)) + 1
            : 1;

        return $prefix .
            str_pad(
                $sequence,
                4,
                '0',
                STR_PAD_LEFT
            );
    }

    public function stokRendah()
    {
        return $this->stok <= $this->stok_minimal;
    }

    /**
     * URL gambar produk.
     */
    public function getGambarUrlAttribute()
    {
        if (!$this->gambar) {
            return null;
        }

        return url($this->gambar);
    }
}