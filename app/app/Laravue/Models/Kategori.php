<?php

namespace App\Laravue\Models;

use App\Laravue\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kategori extends Model
{
    use SoftDeletes;

    protected $table = 'kategori';

    protected $fillable = [
        'nama',
        'deskripsi',
    ];

    // =====================================================
    // RELASI
    // =====================================================

    public function produk()
    {
        return $this->hasMany(Produk::class, 'kategori_id');
    }

    // =====================================================
    // SCOPE
    // =====================================================

    public function scopeAktif($query)
    {
        return $query->whereNull('deleted_at');
    }
}