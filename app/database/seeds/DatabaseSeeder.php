<?php

use App\Laravue\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Laravue\Models\Permission;
use App\Laravue\Models\Role;
use App\Laravue\Models\Kategori;
use App\Laravue\Models\Produk;
use App\Laravue\Models\Pengaturan;
use App\Laravue\Acl;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {        
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@dummy.com',
            'password' => Hash::make('dummy'),
            'password' => Hash::make('dummy'),
            'jabatan' => 'Owner',
        ]);
        $manager = User::create([
            'name' => 'Manager',
            'email' => 'manager@dummy.com',
            'password' => Hash::make('dummy'),
            'jabatan' => 'Manager',
        ]);
        $kasir = User::create([
            'name' => 'Kasir',
            'email' => 'kasir@dummy.com',
            'password' => Hash::make('dummy'),
            'jabatan' => 'Kasir',
        ]);

        $adminRole = Role::findByName(\App\Laravue\Acl::ROLE_ADMIN);
        $managerRole = Role::findByName(\App\Laravue\Acl::ROLE_MANAGER);
        $kasirRole = Role::findByName(\App\Laravue\Acl::ROLE_KASIR);
        
        //SUPERADMIN
        $admin->syncRoles($adminRole);

        //MANAGER
        $manager->syncRoles($managerRole);

        //KASIR
        $kasir->syncRoles($kasirRole);


        $kategoris = [
            [
                'id' => 1,
                'nama' => 'Soda Series Miss Mojito',
                'deskripsi' => 'Soda Series Miss Mojito : Leci Mojito, Strawbery Mojito, Mango Soda, Morning Sunrise, Tropical Sunset',
                'created_at' => '2026-10-01 12:16:05',
                'updated_at' => '2026-10-01 12:16:05',
                'deleted_at' => null,
            ],
            [
                'id' => 2,
                'nama' => 'Spesial Float',
                'deskripsi' => 'Spesial Float : Leci Mojito Float, Mango Soda Float, Real Mango Float, Mix Berry Float, Tropical Sunset',
                'created_at' => '2026-10-01 12:16:40',
                'updated_at' => '2026-10-01 12:16:40',
                'deleted_at' => null,
            ],
            [
                'id' => 3,
                'nama' => 'Menu Makanan',
                'deskripsi' => 'Menu Makanan : Dimsum Kuro (original, orenji sauce, katsuo), Dimsum Orenji Sauce isi 6, Dimsum Goreng isi 2, Cireng Kuah Creamy, Dimsum Kuah Creamy, Dimsum Goreng Kuah Creamy',
                'created_at' => '2026-10-01 12:19:33',
                'updated_at' => '2026-10-01 12:19:33',
                'deleted_at' => null,
            ],
            [
                'id' => 4,
                'nama' => 'Miss Mojito Jus',
                'deskripsi' => 'Miss Mojito Jus : Jus mangga, melon, alpukat, strawberry, nanas, apel, sirsak, jambu merah, dan naga',
                'created_at' => '2026-10-01 12:20:10',
                'updated_at' => '2026-10-01 12:20:10',
                'deleted_at' => null,
            ],
            [
                'id' => 5,
                'nama' => 'Mix Jus',
                'deskripsi' => 'Mix Jus : Mix berry, mangga + alpukat, strawberry + pisang, naga + pisang, mangga + pisang, alpukat + pisang, nanas + strawberry, mangga + nanas + strawberry, mangga + nanas, strawberry + apel, mangga + strawberry',
                'created_at' => '2026-10-01 12:21:05',
                'updated_at' => '2026-10-01 12:21:05',
                'deleted_at' => null,
            ],
        ];

        DB::table('kategori')->insert($kategoris);

                $produks = [
            ['id' => 1,  'kategori_id' => 1,    'kode' => 'PRD-20261001-0001', 'nama' => 'LECI MOJITO', 'deskripsi' => 'Soda Series Miss Mojito : LECI MOJITO', 'harga_beli' => 7000,  'harga_jual' => 10000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/7VI3vZo3mT5NclNv1JzRejPWGpuPKbY3CubYI7YD.webp', 'is_active' => 1, 'created_at' => '2026-10-01 12:59:36', 'updated_at' => '2026-10-01 13:02:30', 'deleted_at' => null],
            ['id' => 2,  'kategori_id' => 1,    'kode' => 'PRD-20261001-0002', 'nama' => 'STRAWBERRY MOJITO', 'deskripsi' => 'Soda Series Miss Mojito : STRAWBERRY MOJITO', 'harga_beli' => 7000,  'harga_jual' => 10000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/4QTayFieMZBGgtk5aP0D1PL37EIwRx5MRaozdsPd.webp', 'is_active' => 1, 'created_at' => '2026-10-01 13:10:32', 'updated_at' => '2026-10-01 13:10:32', 'deleted_at' => null],
            ['id' => 3,  'kategori_id' => 1,    'kode' => 'PRD-20261001-0003', 'nama' => 'MANGO SODA', 'deskripsi' => 'Soda Series Miss Mojito : MANGO SODA', 'harga_beli' => 7000,  'harga_jual' => 10000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/dBTa3c0THhSbIV6H7bJ8G0kXhg6yfyvtMI8s3iVv.webp', 'is_active' => 1, 'created_at' => '2026-10-01 13:15:21', 'updated_at' => '2026-10-01 13:15:21', 'deleted_at' => null],
            ['id' => 4,  'kategori_id' => 1,    'kode' => 'PRD-20261001-0004', 'nama' => 'MORNING SUNRISE', 'deskripsi' => 'Soda Series Miss Mojito : MORNING SUNRISE', 'harga_beli' => 7000,  'harga_jual' => 10000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/757Ni3qqcHkxfBp1708fM8KLGy8ceHKm9t8dPFbx.webp', 'is_active' => 1, 'created_at' => '2026-10-01 13:28:33', 'updated_at' => '2026-10-01 13:28:33', 'deleted_at' => null],
            ['id' => 5,  'kategori_id' => 1,    'kode' => 'PRD-20261001-0005', 'nama' => 'TROPICAL SUNSET', 'deskripsi' => 'Soda Series Miss Mojito : TROPICAL SUNSET', 'harga_beli' => 7000,  'harga_jual' => 10000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/3n2BCHs0atd7N2lTeAHDs78lwD4fyhLq1EA7TwIt.webp', 'is_active' => 1, 'created_at' => '2026-10-01 13:36:56', 'updated_at' => '2026-10-01 13:36:56', 'deleted_at' => null],
            ['id' => 6,  'kategori_id' => 2,    'kode' => 'PRD-20261003-0001', 'nama' => 'LECI MOJITO FLOAT', 'deskripsi' => 'Spesial Float : Leci Mojito Float', 'harga_beli' => 10000, 'harga_jual' => 13000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/XbLeSS7YEibxCPU5gFziLnr1wN9iMLmww7ONXWam.webp', 'is_active' => 1, 'created_at' => '2026-10-03 00:17:08', 'updated_at' => '2026-10-03 00:17:49', 'deleted_at' => null],
            ['id' => 7,  'kategori_id' => 2,    'kode' => 'PRD-20261003-0002', 'nama' => 'MANGO SODA FLOAT', 'deskripsi' => 'Spesial Float : Mango Soda Float', 'harga_beli' => 10000, 'harga_jual' => 13000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/9RctFG9xbaZw7xt2HKPjmTwQhdxo5hmrY680uewU.webp', 'is_active' => 1, 'created_at' => '2026-10-03 00:18:57', 'updated_at' => '2026-10-03 00:40:48', 'deleted_at' => null],
            ['id' => 8,  'kategori_id' => 2,    'kode' => 'PRD-20261003-0003', 'nama' => 'REAL MANGO FLOAT', 'deskripsi' => 'Spesial Float : Real Mango Float', 'harga_beli' => 10000, 'harga_jual' => 13000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/Qh4lF0DcwricJ9TyEyjhitjt87i3y29no2prLohn.webp', 'is_active' => 1, 'created_at' => '2026-10-03 00:20:13', 'updated_at' => '2026-10-03 01:01:18', 'deleted_at' => null],
            ['id' => 9,  'kategori_id' => 2,    'kode' => 'PRD-20261003-0004', 'nama' => 'MIX BERRY FLOAT', 'deskripsi' => 'Spesial Float : Mix Berry Float', 'harga_beli' => 10000, 'harga_jual' => 13000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/II69SBUreppEjfoIqQcYrKs2Y0DI4QfhHiNH65EA.webp', 'is_active' => 1, 'created_at' => '2026-10-03 00:21:03', 'updated_at' => '2026-10-03 00:50:10', 'deleted_at' => null],
            ['id' => 10, 'kategori_id' => 2,    'kode' => 'PRD-20261003-0005', 'nama' => 'TROPICAL SUNSET FLOAT', 'deskripsi' => 'Spesial Float : Tropical Sunset Float', 'harga_beli' => 10000, 'harga_jual' => 13000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/n21sXVguSd6nAorWoxuGOVntqGIdqbj8eJRMpNnv.webp', 'is_active' => 1, 'created_at' => '2026-10-03 00:21:38', 'updated_at' => '2026-10-03 01:05:07', 'deleted_at' => null],
            ['id' => 11, 'kategori_id' => 3,    'kode' => 'PRD-20261003-0006', 'nama' => 'DiMSUM KURO (original, orenji sauce, katsuo)', 'deskripsi' => 'Dimsum Kuro (original, orenji sauce, katsuo)', 'harga_beli' => 15000, 'harga_jual' => 18000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/NFEfrKGNQqcDaWHsuT7lMCbm7o70iM4MpAs3e3Dy.webp', 'is_active' => 1, 'created_at' => '2026-10-03 00:22:24', 'updated_at' => '2026-10-03 02:22:07', 'deleted_at' => null],
            ['id' => 12, 'kategori_id' => 3,    'kode' => 'PRD-20261003-0007', 'nama' => 'DIMSUM ORENJI SAUCE ISI 6', 'deskripsi' => 'Dimsum Orenji Sauce isi 6', 'harga_beli' => 20000, 'harga_jual' => 23000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/Y6pvhatiqC4qV8gJ0o7y1TbmnJduIAJaLkwmYmOB.webp', 'is_active' => 1, 'created_at' => '2026-10-03 00:23:17', 'updated_at' => '2026-10-03 02:22:28', 'deleted_at' => null],
            ['id' => 13, 'kategori_id' => 3,    'kode' => 'PRD-20261003-0008', 'nama' => 'DIMSUM GORENG ISI 2', 'deskripsi' => 'Dimsum Goreng isi 2', 'harga_beli' => 10000, 'harga_jual' => 13000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/rTE05qQtjVlGWZ9rl8G0aliqbJoH1SrN2n7Cjgpc.webp', 'is_active' => 1, 'created_at' => '2026-10-03 00:24:47', 'updated_at' => '2026-10-03 02:21:42', 'deleted_at' => null],
            ['id' => 14, 'kategori_id' => 3,    'kode' => 'PRD-20261003-0009', 'nama' => 'CIRENG KUAH CREAMY', 'deskripsi' => 'Cireng Kuah Creamy', 'harga_beli' => 12000, 'harga_jual' => 15000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/DwAllVsXMZtZ6v0yP8oSVblVjcx8bJWkgx3d1P87.webp', 'is_active' => 1, 'created_at' => '2026-10-03 00:25:36', 'updated_at' => '2026-10-03 02:21:33', 'deleted_at' => null],
            ['id' => 15, 'kategori_id' => 3,    'kode' => 'PRD-20261003-0010', 'nama' => 'DIMSUM KUAH CREAMY', 'deskripsi' => 'Dimsum Kuah Creamy', 'harga_beli' => 14000, 'harga_jual' => 17000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/DisXrGxtDTRvTdiPLNMfK30azJAf9WFET9KG1Gvx.webp', 'is_active' => 1, 'created_at' => '2026-10-03 00:26:20', 'updated_at' => '2026-10-03 02:21:51', 'deleted_at' => null],
            ['id' => 16, 'kategori_id' => 3,    'kode' => 'PRD-20261003-0011', 'nama' => 'DIMSUM GORENG KUAH CREAMY', 'deskripsi' => 'Dimsum Goreng Kuah Creamy', 'harga_beli' => 18000, 'harga_jual' => 21000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/r9gj1vKytg9pNKxUKtt5OazilMQknk1uTl4dvuXD.webp', 'is_active' => 1, 'created_at' => '2026-10-03 00:27:05', 'updated_at' => '2026-10-03 16:36:13', 'deleted_at' => null],
            ['id' => 17, 'kategori_id' => 4,    'kode' => 'PRD-20261003-0012', 'nama' => 'JUS MANGGA', 'deskripsi' => 'jus mangga', 'harga_beli' => 7000,  'harga_jual' => 10000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/AGmqDT36tNl1NT35X5jo4euxB0TTGz2H8qzHZMFz.webp', 'is_active' => 1, 'created_at' => '2026-10-03 00:27:57', 'updated_at' => '2026-10-03 02:20:04', 'deleted_at' => null],
            ['id' => 18, 'kategori_id' => 4,    'kode' => 'PRD-20261003-0013', 'nama' => 'JUS MELON', 'deskripsi' => 'jus melon', 'harga_beli' => 7000,  'harga_jual' => 10000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/ZyTuKfAqavwT34SMUzjNl4WPYOCkIVgJ4UAhGHhG.webp', 'is_active' => 1, 'created_at' => '2026-10-03 00:28:32', 'updated_at' => '2026-10-03 02:20:25', 'deleted_at' => null],
            ['id' => 19, 'kategori_id' => 4,    'kode' => 'PRD-20261003-0014', 'nama' => 'JUS ALPUKAT', 'deskripsi' => 'jus alpukat', 'harga_beli' => 7000,  'harga_jual' => 10000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/XlIy4OJdHyOpJUlvULJN1ReRfteryBZSsRlTB7tG.webp', 'is_active' => 1, 'created_at' => '2026-10-03 00:29:04', 'updated_at' => '2026-10-03 02:19:14', 'deleted_at' => null],
            ['id' => 20, 'kategori_id' => 4,    'kode' => 'PRD-20261003-0015', 'nama' => 'JUS STRAWBERRY', 'deskripsi' => 'jus strawberry', 'harga_beli' => 7000,  'harga_jual' => 10000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/rEalk4eqlOM53YUgwALEFl1fG6KDYOUzc9sULnwc.webp', 'is_active' => 1, 'created_at' => '2026-10-03 00:29:36', 'updated_at' => '2026-10-03 02:21:12', 'deleted_at' => null],
            ['id' => 21, 'kategori_id' => 4,    'kode' => 'PRD-20261003-0016', 'nama' => 'JUS NANAS', 'deskripsi' => 'jus nanas', 'harga_beli' => 7000,  'harga_jual' => 10000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/pAoemMtlDzO7bNPEIPlcyzRLDkNBW0wAcNtpGWDP.webp', 'is_active' => 1, 'created_at' => '2026-10-03 00:30:16', 'updated_at' => '2026-10-03 02:20:44', 'deleted_at' => null],
            ['id' => 22, 'kategori_id' => 4,    'kode' => 'PRD-20261003-0017', 'nama' => 'JUS APEL', 'deskripsi' => 'jus apel', 'harga_beli' => 7000,  'harga_jual' => 10000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/Ivok7V2zOfbJZm42UVuRosaqUsDi8GuFlKs3cl7r.webp', 'is_active' => 1, 'created_at' => '2026-10-03 00:30:47', 'updated_at' => '2026-10-03 02:19:25', 'deleted_at' => null],
            ['id' => 23, 'kategori_id' => 4,    'kode' => 'PRD-20261003-0018', 'nama' => 'JUS SIRSAK', 'deskripsi' => 'jus sirsak', 'harga_beli' => 7000,  'harga_jual' => 10000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/qqr44JTtqSlYCCojbC5GVKht57XRb1hB94q17Fyl.webp', 'is_active' => 1, 'created_at' => '2026-10-03 00:31:23', 'updated_at' => '2026-10-03 02:20:58', 'deleted_at' => null],
            ['id' => 24, 'kategori_id' => 4,    'kode' => 'PRD-20261003-0019', 'nama' => 'JUS JAMBU MERAH', 'deskripsi' => 'jus jambu merah', 'harga_beli' => 7000,  'harga_jual' => 10000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/3FCGxK0QuD8uczFd4CFnAdyFQN2XaRskHrun8Eag.webp', 'is_active' => 1, 'created_at' => '2026-10-03 00:31:50', 'updated_at' => '2026-10-03 02:19:46', 'deleted_at' => null],
            ['id' => 25, 'kategori_id' => 4,    'kode' => 'PRD-20261003-0020', 'nama' => 'JUS BUAH NAGA', 'deskripsi' => 'jus buah naga', 'harga_beli' => 7000,  'harga_jual' => 10000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/N6xmdyP2IYPhtFcAvzj6vBqTsxWBwfA4D6wRapI9.webp', 'is_active' => 1, 'created_at' => '2026-10-03 00:32:13', 'updated_at' => '2026-10-03 02:19:35', 'deleted_at' => null],
            ['id' => 26, 'kategori_id' => 5,    'kode' => 'PRD-20261003-0021', 'nama' => 'mix berry jus', 'deskripsi' => 'mix berry jus', 'harga_beli' => 10000, 'harga_jual' => 13000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/TKhSks3bN4WZKu1Fef1CHlKMtqbRderBql6TInKd.webp', 'is_active' => 1, 'created_at' => '2026-10-03 00:33:20', 'updated_at' => '2026-10-03 02:23:07', 'deleted_at' => null],
            ['id' => 27, 'kategori_id' => 5,    'kode' => 'PRD-20261003-0022', 'nama' => 'mix jus mangga + alpukat', 'deskripsi' => 'mix jus mangga + alpukat', 'harga_beli' => 10000, 'harga_jual' => 13000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/EnTa1vmY5tT2TeqCVC8pf3kw1BsxN7NyT80HAqyK.webp', 'is_active' => 1, 'created_at' => '2026-10-03 00:34:16', 'updated_at' => '2026-10-03 02:23:44', 'deleted_at' => null],
            ['id' => 28, 'kategori_id' => 5,    'kode' => 'PRD-20261003-0023', 'nama' => 'mix jus naga + pisang', 'deskripsi' => 'mix jus naga + pisang', 'harga_beli' => 10000, 'harga_jual' => 13000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/zIm07vumvG0j54P6JOOqMsjeD5zchzzrmJrXvHq8.webp', 'is_active' => 1, 'created_at' => '2026-10-03 00:35:31', 'updated_at' => '2026-10-03 02:25:01', 'deleted_at' => null],
            ['id' => 29, 'kategori_id' => 5,    'kode' => 'PRD-20261003-0024', 'nama' => 'mix jus mangga + nanas + strawberry', 'deskripsi' => 'mix jus mangga + nanas + strawberry', 'harga_beli' => 10000, 'harga_jual' => 13000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/EFL3qlzWB6MVav6Cr58uqEllsUT2TC1tXlLO7NLz.webp', 'is_active' => 1, 'created_at' => '2026-10-03 00:36:05', 'updated_at' => '2026-10-03 02:24:17', 'deleted_at' => null],
            ['id' => 30, 'kategori_id' => 5,    'kode' => 'PRD-20261003-0025', 'nama' => 'mix jus mangga + nanas', 'deskripsi' => 'mix jus mangga + nanas', 'harga_beli' => 10000, 'harga_jual' => 13000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/H04AjV0JmEG8aWUxZLTcEl1ZAmWYC4vVR1hIAlCf.webp', 'is_active' => 1, 'created_at' => '2026-10-03 00:36:34', 'updated_at' => '2026-10-03 02:23:58', 'deleted_at' => null],
            ['id' => 31, 'kategori_id' => 5,    'kode' => 'PRD-20261003-0026', 'nama' => 'mix jus mangga + strawberry', 'deskripsi' => 'mix jus mangga + strawberry', 'harga_beli' => 10000, 'harga_jual' => 13000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/wF2p4rrzQoYQfSbPNFyo4zYJJ9WppNSpeqolRDok.webp', 'is_active' => 1, 'created_at' => '2026-10-03 00:36:59', 'updated_at' => '2026-10-03 02:24:47', 'deleted_at' => null],
            ['id' => 32, 'kategori_id' => 5,    'kode' => 'PRD-20261003-0027', 'nama' => 'mix jus strawberry + pisang', 'deskripsi' => 'mix jus strawberry + pisang', 'harga_beli' => 10000, 'harga_jual' => 13000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/ES92RhxiRL1yjfvN9APC1s7XI6G3ot9r0uFzxGOA.webp', 'is_active' => 1, 'created_at' => '2026-10-03 01:37:57', 'updated_at' => '2026-10-03 02:26:10', 'deleted_at' => null],
            ['id' => 33, 'kategori_id' => 5,    'kode' => 'PRD-20261003-0028', 'nama' => 'mix jus naga + pisang', 'deskripsi' => 'mix jus naga + pisang', 'harga_beli' => 10000, 'harga_jual' => 13000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => null, 'is_active' => 1, 'created_at' => '2026-10-03 01:39:05', 'updated_at' => '2026-10-03 02:25:27', 'deleted_at' => '2026-10-03 02:25:27'],
            ['id' => 34, 'kategori_id' => 5,    'kode' => 'PRD-20261003-0029', 'nama' => 'mix jus mangga + pisang', 'deskripsi' => 'mix jus mangga + pisang', 'harga_beli' => 10000, 'harga_jual' => 13000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/3TUkgHG65gXHaWD7VA8BOBSNUds88l5UEdmF3YBS.webp', 'is_active' => 1, 'created_at' => '2026-10-03 01:40:02', 'updated_at' => '2026-10-03 02:24:30', 'deleted_at' => null],
            ['id' => 35, 'kategori_id' => 5,    'kode' => 'PRD-20261003-0030', 'nama' => 'mix jus alpukat + pisang', 'deskripsi' => 'mix jus alpukat + pisang', 'harga_beli' => 10000, 'harga_jual' => 13000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/4AQgyAqGuC21miz6iATC1TZxukMTcYTxeINETcb6.webp', 'is_active' => 1, 'created_at' => '2026-10-03 01:40:50', 'updated_at' => '2026-10-03 02:23:28', 'deleted_at' => null],
            ['id' => 36, 'kategori_id' => 5,    'kode' => 'PRD-20261003-0031', 'nama' => 'mix jus nanas + strawberry', 'deskripsi' => 'mix jus nanas + strawberry', 'harga_beli' => 10000, 'harga_jual' => 13000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/Kdh4WeWN3XnZRltodr8sJ3zGiIbTJUozzW70osdP.webp', 'is_active' => 1, 'created_at' => '2026-10-03 01:45:21', 'updated_at' => '2026-10-03 02:25:40', 'deleted_at' => null],
            ['id' => 37, 'kategori_id' => 5,    'kode' => 'PRD-20261003-0032', 'nama' => 'mix jus strawberry + apel', 'deskripsi' => 'mix jus strawberry + apel', 'harga_beli' => 10000, 'harga_jual' => 13000, 'stok' => 20, 'stok_minimal' => 20, 'satuan' => 'pcs', 'gambar' => 'uploads/produk/RIOVhZBPmtWMCH6GYtneDOjPVCImyCVL6DK5spsT.webp', 'is_active' => 1, 'created_at' => '2026-10-03 01:46:55', 'updated_at' => '2026-10-03 02:25:54', 'deleted_at' => null],
        ];

        DB::table('produk')->insert($produks);

        $settings = [
            [
                'key'   => 'nama_kedai',
                'value' => 'Kedai Miss Mojito',
                'tipe'  => 'string',
            ],
            [
                'key'   => 'alamat_kedai',
                'value' => 'Jl. In Aja Dulu No. 123, Bandar Lampung',
                'tipe'  => 'string',
            ],
            [
                'key'   => 'telepon_kedai',
                'value' => '0812-3456-7890',
                'tipe'  => 'string',
            ],
            [
                'key'   => 'email_kedai',
                'value' => 'info@kedaimissmojito.com',
                'tipe'  => 'string',
            ],
            [
                'key'   => 'pajak_persen',
                'value' => '0',
                'tipe'  => 'number',
            ],
            [
                'key'   => 'mata_uang',
                'value' => 'IDR',
                'tipe'  => 'string',
            ],
            [
                'key'   => 'logo_kedai',
                'value' => '',
                'tipe'  => 'image',
            ],
            [
                'key'   => 'struk_footer',
                'value' => 'Terima kasih telah berkunjung 🙏',
                'tipe'  => 'string',
            ],
            [
                'key'   => 'struk_header',
                'value' => 'Kedai Miss Mojito',
                'tipe'  => 'string',
            ],
            [
                'key'   => 'kas_awal_default',
                'value' => '100000',
                'tipe'  => 'number',
            ],
        ];

        foreach ($settings as $setting) {
            Pengaturan::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info(
            '✅ PengaturanSeeder: ' . count($settings) . ' pengaturan berhasil dibuat.'
        );
        
        // $this->call(UsersTableSeeder::class);
    }
}
