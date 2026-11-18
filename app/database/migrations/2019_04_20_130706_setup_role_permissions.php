<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

use App\Laravue\Acl;
use App\Laravue\Models\Role;
use App\Laravue\Models\Permission;
use App\Laravue\Models\User;

class SetupRolePermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // =====================================================
        // 7. CREATE ROLES
        // =====================================================

        foreach (Acl::roles() as $roleName) {
            Role::findOrCreate($roleName, 'api');
        }


        // =====================================================
        // 8. CREATE ALL PERMISSIONS
        // =====================================================

        foreach (Acl::permissions() as $permissionName) {
            Permission::findOrCreate($permissionName, 'api');
        }


        // =====================================================
        // 9. ADMIN — SEMUA PERMISSION
        // =====================================================

        $adminRole = Role::findByName(Acl::ROLE_ADMIN, 'api');
        $adminRole->syncPermissions(Acl::permissions());


        // =====================================================
        // 10. MANAGER
        // =====================================================

        $managerRole = Role::findByName(Acl::ROLE_MANAGER, 'api');

        $managerPermissions = [
            // View menu
            // Acl::PERMISSION_VIEW_MENU_DASHBOARD,
            // Acl::PERMISSION_VIEW_MENU_PRODUK,
            // Acl::PERMISSION_VIEW_MENU_KATEGORI,
            // Acl::PERMISSION_VIEW_MENU_STOK,
            // Acl::PERMISSION_VIEW_MENU_TRANSAKSI,
            // Acl::PERMISSION_VIEW_MENU_SESI_KASIR,
            // Acl::PERMISSION_VIEW_MENU_LAPORAN,
            // Acl::PERMISSION_VIEW_MENU_PENGATURAN,

            // // Manage
            // Acl::PERMISSION_PRODUK_MANAGE,
            // Acl::PERMISSION_KATEGORI_MANAGE,
            // Acl::PERMISSION_STOK_MANAGE,
            // Acl::PERMISSION_LAPORAN_MANAGE,
            // Acl::PERMISSION_PENGATURAN_MANAGE,

            // // Sesi kasir
            // Acl::PERMISSION_SESI_KASIR_VIEW,

            // // User
            // Acl::PERMISSION_USER_VIEW,
            // Acl::PERMISSION_USER_PROFILE,
        ];

        $managerRole->syncPermissions($managerPermissions);


        // =====================================================
        // 11. KASIR
        // =====================================================

        $kasirRole = Role::findByName(Acl::ROLE_KASIR, 'api');

        $kasirPermissions = [
            // // View menu
            // Acl::PERMISSION_VIEW_MENU_DASHBOARD,
            // Acl::PERMISSION_VIEW_MENU_PRODUK,
            // Acl::PERMISSION_VIEW_MENU_STOK,
            // Acl::PERMISSION_VIEW_MENU_TRANSAKSI,
            // Acl::PERMISSION_VIEW_MENU_SESI_KASIR,

            // // Manage transaksi
            // Acl::PERMISSION_TRANSAKSI_MANAGE,

            // // Sesi kasir
            // Acl::PERMISSION_SESI_KASIR_VIEW,
            // Acl::PERMISSION_SESI_KASIR_OPEN,
            // Acl::PERMISSION_SESI_KASIR_CLOSE,

            // // User
            // Acl::PERMISSION_USER_PROFILE,
        ];

        $kasirRole->syncPermissions($kasirPermissions);


        


        // =====================================================
        // 13. PENGATURAN DEFAULT
        // =====================================================

        $defaultSettings = [
            ['key' => 'nama_kedai',    'value' => 'Kedai Mitra', 'tipe' => 'string'],
            ['key' => 'alamat_kedai',  'value' => 'Bandar Lampung', 'tipe' => 'string'],
            ['key' => 'telepon_kedai', 'value' => '', 'tipe' => 'string'],
            ['key' => 'pajak_persen',  'value' => '0', 'tipe' => 'number'],
            ['key' => 'mata_uang',     'value' => 'IDR', 'tipe' => 'string'],
            ['key' => 'logo_kedai',    'value' => '', 'tipe' => 'string'],
            ['key' => 'struk_footer',  'value' => 'Terima kasih telah berbelanja', 'tipe' => 'string'],
        ];

        foreach ($defaultSettings as $setting) {
            DB::table('pengaturan')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /*
         * Karena migration ini digunakan untuk setup permission,
         * jangan menghapus kolom users.role secara otomatis.
         *
         * Struktur users bukan tanggung jawab migration ini.
         */

        foreach (Acl::roles() as $roleName) {

            try {
                $role = Role::findByName(
                    $roleName,
                    'api'
                );

                $role->syncPermissions([]);
            } catch (\Throwable $e) {
                // Role belum ada, abaikan.
            }
        }

        foreach (Acl::permissions() as $permissionName) {

            try {
                $permission = Permission::findByName(
                    $permissionName,
                    'api'
                );

                $permission->delete();
            } catch (\Throwable $e) {
                // Permission belum ada, abaikan.
            }
        }

        foreach (Acl::roles() as $roleName) {

            try {
                $role = Role::findByName(
                    $roleName,
                    'api'
                );

                $role->delete();
            } catch (\Throwable $e) {
                // Role belum ada, abaikan.
            }
        }
    }
}