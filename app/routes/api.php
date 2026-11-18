<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Resources\UserResource;
use App\Laravue\Acl;

Route::namespace('Api')->group(function () {

    // =========================================================
    // PUBLIC
    // =========================================================

    Route::post(
        'auth/login',
        'AuthController@login'
    );


    // =========================================================
    // AUTHENTICATED
    // =========================================================

    Route::group([
        'middleware' => 'auth:sanctum',
    ], function () {

        // =====================================================
        // AUTH
        // =====================================================

        Route::get(
            'auth/user',
            'AuthController@user'
        );

        Route::post(
            'auth/logout',
            'AuthController@logout'
        );


        // =====================================================
        // CURRENT USER
        // =====================================================

        Route::get(
            'user',
            function (Request $request) {
                return new UserResource(
                    $request->user()
                );
            }
        );

        Route::get(
            'user/profile',
            'UserController@profile'
        );

        Route::put(
            'user/profile',
            'UserController@updateProfile'
        )->middleware(
            'permission:' . Acl::PERMISSION_USER_PROFILE
        );

        Route::put(
            'user/password',
            'UserController@updateMyPassword'
        )->middleware(
            'permission:' . Acl::PERMISSION_USER_PROFILE
        );


        // =====================================================
        // USER SYSTEM DATA
        // =====================================================

        Route::get(
            'users/statistics',
            'UserController@statistics'
        )->middleware(
            'permission:' . Acl::PERMISSION_USER_VIEW
        );

        Route::get(
            'users/for-signature',
            'UserController@getUsersForSignature'
        )->middleware(
            'permission:' . Acl::PERMISSION_USER_VIEW
        );


        // =====================================================
        // ADMINISTRATOR - USERS
        // =====================================================

        Route::get(
            'users',
            'UserController@index'
        )->middleware(
            'permission:' . Acl::PERMISSION_VIEW_MENU_ADMINISTRATOR
        );

        Route::get(
            'users/{user}',
            'UserController@show'
        )->middleware(
            'permission:' . Acl::PERMISSION_VIEW_MENU_ADMINISTRATOR
        );

        Route::post(
            'users',
            'UserController@store'
        )->middleware(
            'permission:' . Acl::PERMISSION_USER_MANAGE
        );

        Route::put(
            'users/{user}',
            'UserController@update'
        )->middleware(
            'permission:' . Acl::PERMISSION_USER_MANAGE
        );

        Route::patch(
            'users/{user}',
            'UserController@update'
        )->middleware(
            'permission:' . Acl::PERMISSION_USER_MANAGE
        );

        Route::delete(
            'users/{user}',
            'UserController@destroy'
        )->middleware(
            'permission:' . Acl::PERMISSION_USER_MANAGE
        );


        // =====================================================
        // LEGACY USER ENDPOINTS
        // =====================================================

        Route::put(
            'users/update_registrations/{user}',
            'UserController@updateRegistrasi'
        )->middleware(
            'permission:' . Acl::PERMISSION_USER_MANAGE
        );

        Route::put(
            'users/update_status/{id}',
            'UserController@updateStatus'
        )->middleware(
            'permission:' . Acl::PERMISSION_USER_MANAGE
        );

        Route::put(
            'users/update_password/{id}',
            'UserController@updatePassword'
        )->middleware(
            'permission:' . Acl::PERMISSION_USER_MANAGE
        );


        // =====================================================
        // USER PERMISSIONS
        // =====================================================

        Route::get(
            'users/{user}/permissions',
            'UserController@permissions'
        )->middleware(
            'permission:' . Acl::PERMISSION_PERMISSION_MANAGE
        );

        Route::put(
            'users/{user}/permissions',
            'UserController@updatePermissions'
        )->middleware(
            'permission:' . Acl::PERMISSION_PERMISSION_MANAGE
        );


        // =====================================================
        // ACTIVE USERS
        // =====================================================

        Route::get(
            'active-users',
            'ActiveUsersController@index'
        )->middleware(
            'permission:' . Acl::PERMISSION_VIEW_MENU_ADMINISTRATOR
        );

        Route::post(
            'active-users/logout/{user}',
            'ActiveUsersController@logoutUser'
        )->middleware(
            'permission:' . Acl::PERMISSION_USER_MANAGE
        );

        Route::post(
            'active-users/logout-all',
            'ActiveUsersController@forceLogoutAll'
        )->middleware(
            'permission:' . Acl::PERMISSION_USER_MANAGE
        );

        Route::post(
            'active-users/reset-logout-all',
            'ActiveUsersController@resetForceLogoutAll'
        )->middleware(
            'permission:' . Acl::PERMISSION_USER_MANAGE
        );

        Route::post(
            'active-users/activate/{user}',
            'ActiveUsersController@activateUser'
        )->middleware(
            'permission:' . Acl::PERMISSION_USER_MANAGE
        );

        Route::post(
            'active-users/deactivate/{user}',
            'ActiveUsersController@deactivateUser'
        )->middleware(
            'permission:' . Acl::PERMISSION_USER_MANAGE
        );


        // =====================================================
        // ROLES
        // =====================================================

        Route::get(
            'roles',
            'RoleController@index'
        )->middleware(
            'permission:' . Acl::PERMISSION_VIEW_MENU_PERMISSION
        );

        Route::get(
            'roles/{role}',
            'RoleController@show'
        )->middleware(
            'permission:' . Acl::PERMISSION_VIEW_MENU_PERMISSION
        );

        Route::post(
            'roles',
            'RoleController@store'
        )->middleware(
            'permission:' . Acl::PERMISSION_PERMISSION_MANAGE
        );

        Route::put(
            'roles/{role}',
            'RoleController@update'
        )->middleware(
            'permission:' . Acl::PERMISSION_PERMISSION_MANAGE
        );

        Route::patch(
            'roles/{role}',
            'RoleController@update'
        )->middleware(
            'permission:' . Acl::PERMISSION_PERMISSION_MANAGE
        );

        Route::delete(
            'roles/{role}',
            'RoleController@destroy'
        )->middleware(
            'permission:' . Acl::PERMISSION_PERMISSION_MANAGE
        );

        Route::get(
            'roles/{role}/permissions',
            'RoleController@permissions'
        )->middleware(
            'permission:' . Acl::PERMISSION_PERMISSION_MANAGE
        );


        // =====================================================
        // PERMISSIONS
        // =====================================================

        Route::get(
            'permissions',
            'PermissionController@index'
        )->middleware(
            'permission:' . Acl::PERMISSION_VIEW_MENU_PERMISSION
        );

        Route::get(
            'permissions/{permission}',
            'PermissionController@show'
        )->middleware(
            'permission:' . Acl::PERMISSION_VIEW_MENU_PERMISSION
        );

        Route::post(
            'permissions',
            'PermissionController@store'
        )->middleware(
            'permission:' . Acl::PERMISSION_PERMISSION_MANAGE
        );

        Route::put(
            'permissions/{permission}',
            'PermissionController@update'
        )->middleware(
            'permission:' . Acl::PERMISSION_PERMISSION_MANAGE
        );

        Route::patch(
            'permissions/{permission}',
            'PermissionController@update'
        )->middleware(
            'permission:' . Acl::PERMISSION_PERMISSION_MANAGE
        );

        Route::delete(
            'permissions/{permission}',
            'PermissionController@destroy'
        )->middleware(
            'permission:' . Acl::PERMISSION_PERMISSION_MANAGE
        );


        // =====================================================
        // KATEGORI
        // =====================================================

        Route::prefix('kategori')->group(function () {

            Route::get(
                '/',
                'KategoriController@index'
            )->middleware(
                'permission:' . Acl::PERMISSION_VIEW_MENU_KATEGORI
            );

            Route::get(
                '/stok',
                'KategoriController@index'
            )->middleware(
                'permission:' . Acl::PERMISSION_VIEW_MENU_LAPORAN
            );

            Route::post(
                '/',
                'KategoriController@store'
            )->middleware(
                'permission:' . Acl::PERMISSION_KATEGORI_MANAGE
            );

            Route::get(
                '{id}',
                'KategoriController@show'
            )->middleware(
                'permission:' . Acl::PERMISSION_VIEW_MENU_KATEGORI
            );

            Route::put(
                '{id}',
                'KategoriController@update'
            )->middleware(
                'permission:' . Acl::PERMISSION_KATEGORI_MANAGE
            );

            Route::delete(
                '{id}',
                'KategoriController@destroy'
            )->middleware(
                'permission:' . Acl::PERMISSION_KATEGORI_MANAGE
            );
        });


        // =====================================================
        // PRODUK
        // =====================================================

        Route::prefix('produk')->group(function () {

            Route::get(
                '/',
                'ProdukController@index'
            )->middleware(
                'permission:' . Acl::PERMISSION_VIEW_MENU_PRODUK
            );

            Route::get(
                '/transaksi',
                'ProdukController@index'
            )->middleware(
                'permission:' . Acl::PERMISSION_VIEW_MENU_TRANSAKSI
            );

            Route::post(
                '/',
                'ProdukController@store'
            )->middleware(
                'permission:' . Acl::PERMISSION_PRODUK_MANAGE
            );

            Route::get(
                '{id}',
                'ProdukController@show'
            )->middleware(
                'permission:' . Acl::PERMISSION_VIEW_MENU_PRODUK
            );

            Route::put(
                '{id}',
                'ProdukController@update'
            )->middleware(
                'permission:' . Acl::PERMISSION_PRODUK_MANAGE
            );

            Route::delete(
                '{id}',
                'ProdukController@destroy'
            )->middleware(
                'permission:' . Acl::PERMISSION_PRODUK_MANAGE
            );
        });


        // =====================================================
        // TRANSAKSI
        // =====================================================

        Route::prefix('transaksi')->group(function () {

            Route::get(
                '/',
                'TransaksiController@index'
            )->middleware(
                'permission:' . Acl::PERMISSION_VIEW_MENU_TRANSAKSI
            );

            Route::post(
                '/',
                'TransaksiController@store'
            )->middleware(
                'permission:' . Acl::PERMISSION_TRANSAKSI_MANAGE
            );

            Route::get(
                '{id}',
                'TransaksiController@show'
            )->middleware(
                'permission:' . Acl::PERMISSION_VIEW_MENU_TRANSAKSI
            );

            Route::post(
                '{id}/batal',
                'TransaksiController@batal'
            )->middleware(
                'permission:' . Acl::PERMISSION_TRANSAKSI_MANAGE
            );
        });


        // =====================================================
        // STOK MUTASI
        // =====================================================

        Route::prefix('stok-mutasi')->group(function () {

            Route::get(
                '/',
                'StokMutasiController@index'
            )->middleware(
                'permission:' . Acl::PERMISSION_VIEW_MENU_STOK
            );

            Route::post(
                'masuk',
                'StokMutasiController@masuk'
            )->middleware(
                'permission:' . Acl::PERMISSION_STOK_MANAGE
            );

            Route::post(
                'penyesuaian',
                'StokMutasiController@penyesuaian'
            )->middleware(
                'permission:' . Acl::PERMISSION_STOK_MANAGE
            );
        });


        // =====================================================
        // PENGATURAN
        // =====================================================

        Route::prefix('pengaturan')->group(function () {

            Route::get(
                '/',
                'PengaturanController@index'
            )->middleware(
                'permission:' . Acl::PERMISSION_VIEW_MENU_PENGATURAN
            );

            Route::get(
                '/struk',
                'PengaturanController@index'
            )->middleware(
                'permission:' . Acl::PERMISSION_VIEW_MENU_TRANSAKSI
            );

            Route::post(
                '/',
                'PengaturanController@update'
            )->middleware(
                'permission:' . Acl::PERMISSION_PENGATURAN_MANAGE
            );

            // Upload logo kedai
            Route::post(
                '/logo',
                'PengaturanController@uploadLogo'
            )->middleware(
                'permission:' . Acl::PERMISSION_PENGATURAN_MANAGE
            );

            // Hapus logo kedai
            Route::delete(
                '/logo',
                'PengaturanController@deleteLogo'
            )->middleware(
                'permission:' . Acl::PERMISSION_PENGATURAN_MANAGE
            );
        });


        // =====================================================
        // SESI KASIR
        // =====================================================

        Route::prefix('sesi-kasir')->group(function () {

            Route::get(
                'aktif',
                'SesiKasirController@aktif'
            )->middleware(
                'permission:' . Acl::PERMISSION_SESI_KASIR_VIEW
            );

            Route::post(
                'buka',
                'SesiKasirController@buka'
            )->middleware(
                'permission:' . Acl::PERMISSION_SESI_KASIR_OPEN
            );

            Route::post(
                '{id}/tutup',
                'SesiKasirController@tutup'
            )->middleware(
                'permission:' . Acl::PERMISSION_SESI_KASIR_CLOSE
            );

            Route::get(
                '/',
                'SesiKasirController@index'
            )->middleware(
                'permission:' . Acl::PERMISSION_SESI_KASIR_VIEW
            );

            Route::get(
                '{id}',
                'SesiKasirController@show'
            )->middleware(
                'permission:' . Acl::PERMISSION_SESI_KASIR_VIEW
            );
        });


        // =====================================================
        // LAPORAN
        // =====================================================

        Route::prefix('laporan')->group(function () {

            Route::get(
                'dashboard',
                'LaporanController@dashboard'
            )->middleware(
                'permission:' . Acl::PERMISSION_VIEW_MENU_DASHBOARD
            );

            Route::get(
                'harian',
                'LaporanController@harian'
            )->middleware(
                'permission:' . Acl::PERMISSION_VIEW_MENU_LAPORAN
            );

            Route::get(
                'per-shift',
                'LaporanController@perShift'
            )->middleware(
                'permission:' . Acl::PERMISSION_VIEW_MENU_LAPORAN
            );

            Route::get(
                'per-produk',
                'LaporanController@perProduk'
            )->middleware(
                'permission:' . Acl::PERMISSION_VIEW_MENU_LAPORAN
            );

            Route::get(
                'per-kategori',
                'LaporanController@perKategori'
            )->middleware(
                'permission:' . Acl::PERMISSION_VIEW_MENU_LAPORAN
            );

            Route::get(
                'transaksi',
                'LaporanController@transaksi'
            )->middleware(
                'permission:' . Acl::PERMISSION_VIEW_MENU_LAPORAN
            );

            Route::get(
                'laba-rugi',
                'LaporanController@labaRugi'
            )->middleware(
                'permission:' . Acl::PERMISSION_VIEW_MENU_LAPORAN
            );

            Route::get(
                'stok',
                'LaporanController@stok'
            )->middleware(
                'permission:' . Acl::PERMISSION_VIEW_MENU_LAPORAN
            );

            Route::get(
                'kasir',
                'LaporanController@kasir'
            )->middleware(
                'permission:' . Acl::PERMISSION_VIEW_MENU_LAPORAN
            );
        });


        // =====================================================
        // LOGS
        // =====================================================

        Route::get(
            'logs',
            'LogController@index'
        )->middleware(
            'permission:' . Acl::PERMISSION_PERMISSION_MANAGE
        );

        Route::get(
            'logs/content',
            'LogController@getLogContent'
        )->middleware(
            'permission:' . Acl::PERMISSION_PERMISSION_MANAGE
        );

        Route::post(
            'logs/clear',
            'LogController@clearLog'
        )->middleware(
            'permission:' . Acl::PERMISSION_PERMISSION_MANAGE
        );

        Route::get(
            'logs/download',
            'LogController@downloadLog'
        )->middleware(
            'permission:' . Acl::PERMISSION_PERMISSION_MANAGE
        );


        // =====================================================
        // ACTIVITY LOG
        // =====================================================

        Route::get(
            'activity-logs/recent',
            'ActivityLogController@recent'
        )->middleware(
            'permission:' . Acl::PERMISSION_VIEW_MENU_ADMINISTRATOR
        );

        Route::get(
            'activity-logs',
            'ActivityLogController@index'
        )->middleware(
            'permission:' . Acl::PERMISSION_VIEW_MENU_ADMINISTRATOR
        );
    });
});
