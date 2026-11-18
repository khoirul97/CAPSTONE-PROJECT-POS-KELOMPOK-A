<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;

use App\Laravue\Acl;
use App\Laravue\Models\Role;
use App\Laravue\Models\Permission;
use App\Laravue\Models\User;


class CreateUsersTable extends Migration
{
     public function up(): void
    {
        // ============================================================
        // TABEL: users
        // ============================================================
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->boolean('active')->default(true);
            $table->string('status')->default('active');
            $table->string('jabatan')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->string('device')->nullable();
            $table->string('browser')->nullable();
            $table->string('last_login_location')->nullable();
            $table->string('platform')->nullable();
            $table->boolean('force_logout')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });

        if (!Schema::hasTable('kategori')) {
            Schema::create('kategori', function (Blueprint $table) {
                $table->id();
                $table->string('nama')->unique();
                $table->text('deskripsi')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }


        // =====================================================
        // 2. TABLE: PRODUK
        // =====================================================

        if (!Schema::hasTable('produk')) {
            Schema::create('produk', function (Blueprint $table) {
                $table->id();
                $table->foreignId('kategori_id')
                    ->nullable()
                    ->constrained('kategori')
                    ->nullOnDelete();
                $table->string('kode')->unique();
                $table->string('nama');
                $table->text('deskripsi')->nullable();
                $table->decimal('harga_beli', 15, 2)->default(0);
                $table->decimal('harga_jual', 15, 2)->default(0);
                $table->integer('stok')->default(0);
                $table->integer('stok_minimal')->default(0);
                $table->string('satuan')->default('pcs');
                $table->string('gambar')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->index('nama');
                $table->index('is_active');
            });
        }


        // =====================================================
        // 3. TABLE: TRANSAKSI
        // =====================================================

        if (!Schema::hasTable('transaksi')) {
            Schema::create('transaksi', function (Blueprint $table) {
                $table->id();
                $table->string('kode')->unique();
                $table->foreignId('user_id')
                    ->constrained('users')
                    ->restrictOnDelete();
                $table->decimal('subtotal', 15, 2)->default(0);
                $table->decimal('diskon', 15, 2)->default(0);
                $table->decimal('pajak', 15, 2)->default(0);
                $table->decimal('total', 15, 2)->default(0);
                $table->decimal('bayar', 15, 2)->default(0);
                $table->decimal('kembalian', 15, 2)->default(0);
                $table->enum('metode_pembayaran', ['cash', 'transfer', 'qris'])
                    ->default('cash');
                $table->enum('status', ['pending', 'selesai', 'batal'])
                    ->default('selesai');
                $table->text('catatan')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index('kode');
                $table->index('status');
                $table->index('created_at');
            });
        }


        // =====================================================
        // 4. TABLE: TRANSAKSI_DETAIL
        // =====================================================

        if (!Schema::hasTable('transaksi_detail')) {
            Schema::create('transaksi_detail', function (Blueprint $table) {
                $table->id();
                $table->foreignId('transaksi_id')
                    ->constrained('transaksi')
                    ->cascadeOnDelete();
                $table->foreignId('produk_id')
                    ->constrained('produk')
                    ->restrictOnDelete();
                $table->string('nama_produk');
                $table->decimal('harga', 15, 2)->default(0);
                $table->integer('qty')->default(1);
                $table->decimal('subtotal', 15, 2)->default(0);
                $table->timestamps();
                $table->softDeletes();

                $table->index('transaksi_id');
                $table->index('produk_id');
            });
        }


        // =====================================================
        // 5. TABLE: STOK_MUTASI
        // =====================================================

        if (!Schema::hasTable('stok_mutasi')) {
            Schema::create('stok_mutasi', function (Blueprint $table) {
                $table->id();
                $table->foreignId('produk_id')
                    ->constrained('produk')
                    ->cascadeOnDelete();
                $table->foreignId('user_id')
                    ->constrained('users')
                    ->restrictOnDelete();
                $table->enum('tipe', ['masuk', 'keluar', 'penyesuaian']);
                $table->integer('jumlah');
                $table->integer('stok_sebelum');
                $table->integer('stok_sesudah');
                $table->string('referensi')->nullable();
                $table->text('keterangan')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index('produk_id');
                $table->index('tipe');
                $table->index('created_at');
            });
        }


        // =====================================================
        // 6. TABLE: PENGATURAN
        // =====================================================

        if (!Schema::hasTable('pengaturan')) {
            Schema::create('pengaturan', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->string('tipe')->default('string');
                $table->timestamps();
                $table->softDeletes();
            });
        }


        Schema::create('sesi_kasir', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('user_tutup_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('waktu_buka')->useCurrent();
            $table->timestamp('waktu_tutup')->nullable();

            $table->decimal('kas_awal', 15, 2)->default(0);
            $table->decimal('kas_akhir_sistem', 15, 2)->default(0);
            $table->decimal('kas_akhir_fisik', 15, 2)->nullable();
            $table->decimal('selisih', 15, 2)->default(0);

            $table->integer('total_transaksi')->default(0);
            $table->decimal('total_penjualan', 15, 2)->default(0);
            $table->decimal('total_tunai', 15, 2)->default(0);
            $table->decimal('total_non_tunai', 15, 2)->default(0);

            $table->enum('status', ['open', 'closed'])->default('open');
            $table->text('catatan_buka')->nullable();
            $table->text('catatan_tutup')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('waktu_buka');
            $table->index('user_id');
        });


        // =====================================================
        // TAMBAH KOLOM sesi_kasir_id DI TRANSAKSI
        // =====================================================

        Schema::table('transaksi', function (Blueprint $table) {
            $table->foreignId('sesi_kasir_id')
                ->nullable()
                ->after('user_id')
                ->constrained('sesi_kasir')
                ->nullOnDelete();

            $table->index('sesi_kasir_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         $defaultEmails = [
            'admin@pos.local',
            'manager@pos.local',
            'kasir@pos.local',
        ];

        User::whereIn('email', $defaultEmails)->delete();

        foreach (Acl::roles() as $roleName) {
            try {
                $role = Role::findByName($roleName, 'api');
                $role->syncPermissions([]);
            } catch (\Throwable $e) {}
        }

        foreach (Acl::permissions() as $permissionName) {
            try {
                $permission = Permission::findByName($permissionName, 'api');
                $permission->delete();
            } catch (\Throwable $e) {}
        }

        foreach (Acl::roles() as $roleName) {
            try {
                $role = Role::findByName($roleName, 'api');
                $role->delete();
            } catch (\Throwable $e) {}
        }

        Schema::dropIfExists('stok_mutasi');
        Schema::dropIfExists('transaksi_detail');
        Schema::dropIfExists('transaksi');
        Schema::dropIfExists('produk');
        Schema::dropIfExists('kategori');
        Schema::dropIfExists('pengaturan');

        Schema::dropIfExists('users');
    }
}
