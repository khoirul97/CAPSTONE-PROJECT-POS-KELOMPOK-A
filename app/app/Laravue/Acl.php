<?php

namespace App\Laravue;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

final class Acl
{
    // =========================================================
    // ROLES
    // =========================================================

    public const ROLE_ADMIN = 'admin';
    public const ROLE_KASIR = 'kasir';
    public const ROLE_MANAGER = 'manager';


    // =========================================================
    // VIEW MENU PERMISSIONS
    // =========================================================

    public const PERMISSION_VIEW_MENU_DASHBOARD =
        'view menu dashboard';

    public const PERMISSION_VIEW_MENU_PRODUK =
        'view menu produk';

    public const PERMISSION_VIEW_MENU_KATEGORI =
        'view menu kategori';

    public const PERMISSION_VIEW_MENU_STOK =
        'view menu stok';

    public const PERMISSION_VIEW_MENU_TRANSAKSI =
        'view menu transaksi';

    public const PERMISSION_VIEW_MENU_SESI_KASIR =
        'view menu sesi kasir';

    public const PERMISSION_VIEW_MENU_LAPORAN =
        'view menu laporan';

    public const PERMISSION_VIEW_MENU_USER =
        'view menu user';

    public const PERMISSION_VIEW_MENU_PENGATURAN =
        'view menu pengaturan';

    public const PERMISSION_VIEW_MENU_ADMINISTRATOR =
        'view menu administrator';

    public const PERMISSION_VIEW_MENU_PERMISSION =
        'view menu permission';


    // =========================================================
    // CONTENT MANAGE PERMISSIONS
    // =========================================================

    public const PERMISSION_PRODUK_MANAGE =
        'manage produk';

    public const PERMISSION_KATEGORI_MANAGE =
        'manage kategori';

    public const PERMISSION_STOK_MANAGE =
        'manage stok';

    public const PERMISSION_TRANSAKSI_MANAGE =
        'manage transaksi';

    public const PERMISSION_PENGATURAN_MANAGE =
        'manage pengaturan';


    // =========================================================
    // SESI KASIR PERMISSIONS
    // =========================================================

    public const PERMISSION_SESI_KASIR_VIEW =
        'view sesi kasir';

    public const PERMISSION_SESI_KASIR_OPEN =
        'open sesi kasir';

    public const PERMISSION_SESI_KASIR_CLOSE =
        'close sesi kasir';


    // =========================================================
    // USER / SYSTEM PERMISSIONS
    // =========================================================

    public const PERMISSION_USER_VIEW =
        'view user';

    public const PERMISSION_USER_PROFILE =
        'manage own profile';

    public const PERMISSION_USER_MANAGE =
        'manage user';

    public const PERMISSION_PERMISSION_MANAGE =
        'manage permission';


    // =========================================================
    // ALL PERMISSIONS
    // =========================================================

    public static function permissions(array $exclusives = []): array
    {
        try {
            $class = new \ReflectionClass(__CLASS__);

            $constants = $class->getConstants();

            $permissions = Arr::where(
                $constants,
                function ($value, $key) use ($exclusives) {
                    return !in_array($value, $exclusives, true)
                        && Str::startsWith($key, 'PERMISSION_');
                }
            );

            return array_values($permissions);
        } catch (\ReflectionException $exception) {
            return [];
        }
    }


    // =========================================================
    // VIEW MENU PERMISSIONS ONLY
    // =========================================================

    public static function menuPermissions(): array
    {
        try {
            $class = new \ReflectionClass(__CLASS__);

            $constants = $class->getConstants();

            $permissions = Arr::where(
                $constants,
                function ($value, $key) {
                    return Str::startsWith(
                        $key,
                        'PERMISSION_VIEW_MENU_'
                    );
                }
            );

            return array_values($permissions);
        } catch (\ReflectionException $exception) {
            return [];
        }
    }


    // =========================================================
    // MANAGE PERMISSIONS ONLY
    // =========================================================

    public static function managePermissions(): array
    {
        try {
            $class = new \ReflectionClass(__CLASS__);

            $constants = $class->getConstants();

            $permissions = Arr::where(
                $constants,
                function ($value, $key) {
                    return Str::startsWith(
                        $key,
                        'PERMISSION_'
                    )
                    && Str::contains($key, '_MANAGE');
                }
            );

            return array_values($permissions);
        } catch (\ReflectionException $exception) {
            return [];
        }
    }


    // =========================================================
    // ROLES
    // =========================================================

    public static function roles(): array
    {
        try {
            $class = new \ReflectionClass(__CLASS__);

            $constants = $class->getConstants();

            $roles = Arr::where(
                $constants,
                function ($value, $key) {
                    return Str::startsWith($key, 'ROLE_');
                }
            );

            return array_values($roles);
        } catch (\ReflectionException $exception) {
            return [];
        }
    }
}
