<?php

namespace App\Laravue\Models;

use App\Laravue\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';

    protected $fillable = [
        'key',
        'value',
        'tipe',
    ];

    // =====================================================
    // HELPER
    // =====================================================

    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value, $tipe = 'string')
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'tipe' => $tipe]
        );
    }

    public static function semua()
    {
        return self::pluck('value', 'key')->toArray();
    }
}