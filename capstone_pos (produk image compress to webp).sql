-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 05 Okt 2026 pada 06.38
-- Versi server: 10.4.27-MariaDB-log
-- Versi PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `capstone_project_pos`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `module` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `module`, `description`, `subject_type`, `subject_id`, `properties`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(1, NULL, 'created', 'users', 'Menambahkan users \"Super Admin\"', 'App\\Laravue\\Models\\User', 1, '{\"attributes\":{\"active\":true,\"status\":\"active\",\"name\":\"Super Admin\",\"email\":\"superadmin@cp.com\",\"updated_at\":\"2026-10-05 11:30:09\",\"created_at\":\"2026-10-05 11:30:09\",\"id\":1}}', NULL, NULL, '2026-10-05 04:30:09', '2026-10-05 04:30:09'),
(2, NULL, 'created', 'users', 'Menambahkan users \"Manager\"', 'App\\Laravue\\Models\\User', 2, '{\"attributes\":{\"active\":true,\"status\":\"active\",\"name\":\"Manager\",\"email\":\"manager@cp.com\",\"jabatan\":\"Manager\",\"updated_at\":\"2026-10-05 11:30:09\",\"created_at\":\"2026-10-05 11:30:09\",\"id\":2}}', NULL, NULL, '2026-10-05 04:30:09', '2026-10-05 04:30:09'),
(3, NULL, 'created', 'users', 'Menambahkan users \"Kasir\"', 'App\\Laravue\\Models\\User', 3, '{\"attributes\":{\"active\":true,\"status\":\"active\",\"name\":\"Kasir\",\"email\":\"kasir@cp.com\",\"jabatan\":\"Kasir\",\"updated_at\":\"2026-10-05 11:30:09\",\"created_at\":\"2026-10-05 11:30:09\",\"id\":3}}', NULL, NULL, '2026-10-05 04:30:09', '2026-10-05 04:30:09'),
(4, NULL, 'updated', 'pengaturan', 'Mengubah pengaturan \"nama_kedai\"', 'App\\Laravue\\Models\\Pengaturan', 1, '{\"changes\":{\"value\":\"Kedai Miss Mojito\",\"updated_at\":\"2026-10-05 11:30:09\"}}', NULL, NULL, '2026-10-05 04:30:09', '2026-10-05 04:30:09'),
(5, NULL, 'updated', 'pengaturan', 'Mengubah pengaturan \"alamat_kedai\"', 'App\\Laravue\\Models\\Pengaturan', 2, '{\"changes\":{\"value\":\"Jl. In Aja Dulu No. 123, Bandar Lampung\",\"updated_at\":\"2026-10-05 11:30:09\"}}', NULL, NULL, '2026-10-05 04:30:09', '2026-10-05 04:30:09'),
(6, NULL, 'updated', 'pengaturan', 'Mengubah pengaturan \"telepon_kedai\"', 'App\\Laravue\\Models\\Pengaturan', 3, '{\"changes\":{\"value\":\"0812-3456-7890\",\"updated_at\":\"2026-10-05 11:30:09\"}}', NULL, NULL, '2026-10-05 04:30:09', '2026-10-05 04:30:09'),
(7, NULL, 'created', 'pengaturan', 'Menambahkan pengaturan \"email_kedai\"', 'App\\Laravue\\Models\\Pengaturan', 8, '{\"attributes\":{\"key\":\"email_kedai\",\"value\":\"info@kedaimissmojito.com\",\"tipe\":\"string\",\"updated_at\":\"2026-10-05 11:30:09\",\"created_at\":\"2026-10-05 11:30:09\",\"id\":8}}', NULL, NULL, '2026-10-05 04:30:09', '2026-10-05 04:30:09'),
(8, NULL, 'updated', 'pengaturan', 'Mengubah pengaturan \"logo_kedai\"', 'App\\Laravue\\Models\\Pengaturan', 6, '{\"changes\":{\"tipe\":\"image\",\"updated_at\":\"2026-10-05 11:30:09\"}}', NULL, NULL, '2026-10-05 04:30:09', '2026-10-05 04:30:09'),
(9, NULL, 'updated', 'pengaturan', 'Mengubah pengaturan \"struk_footer\"', 'App\\Laravue\\Models\\Pengaturan', 7, '{\"changes\":{\"value\":\"Terima kasih telah berkunjung \\ud83d\\ude4f\",\"updated_at\":\"2026-10-05 11:30:09\"}}', NULL, NULL, '2026-10-05 04:30:09', '2026-10-05 04:30:09'),
(10, NULL, 'created', 'pengaturan', 'Menambahkan pengaturan \"struk_header\"', 'App\\Laravue\\Models\\Pengaturan', 9, '{\"attributes\":{\"key\":\"struk_header\",\"value\":\"Kedai Miss Mojito\",\"tipe\":\"string\",\"updated_at\":\"2026-10-05 11:30:09\",\"created_at\":\"2026-10-05 11:30:09\",\"id\":9}}', NULL, NULL, '2026-10-05 04:30:09', '2026-10-05 04:30:09'),
(11, NULL, 'created', 'pengaturan', 'Menambahkan pengaturan \"kas_awal_default\"', 'App\\Laravue\\Models\\Pengaturan', 10, '{\"attributes\":{\"key\":\"kas_awal_default\",\"value\":\"100000\",\"tipe\":\"number\",\"updated_at\":\"2026-10-05 11:30:09\",\"created_at\":\"2026-10-05 11:30:09\",\"id\":10}}', NULL, NULL, '2026-10-05 04:30:09', '2026-10-05 04:30:09');

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id`, `nama`, `deskripsi`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Soda Series Miss Mojito', 'Soda Series Miss Mojito : Leci Mojito, Strawbery Mojito, Mango Soda, Morning Sunrise, Tropical Sunset', '2026-10-01 05:16:05', '2026-10-01 05:16:05', NULL),
(2, 'Spesial Float', 'Spesial Float : Leci Mojito Float, Mango Soda Float, Real Mango Float, Mix Berry Float, Tropical Sunset', '2026-10-01 05:16:40', '2026-10-01 05:16:40', NULL),
(3, 'Menu Makanan', 'Menu Makanan : Dimsum Kuro (original, orenji sauce, katsuo), Dimsum Orenji Sauce isi 6, Dimsum Goreng isi 2, Cireng Kuah Creamy, Dimsum Kuah Creamy, Dimsum Goreng Kuah Creamy', '2026-10-01 05:19:33', '2026-10-01 05:19:33', NULL),
(4, 'Miss Mojito Jus', 'Miss Mojito Jus : Jus mangga, melon, alpukat, strawberry, nanas, apel, sirsak, jambu merah, dan naga', '2026-10-01 05:20:10', '2026-10-01 05:20:10', NULL),
(5, 'Mix Jus', 'Mix Jus : Mix berry, mangga + alpukat, strawberry + pisang, naga + pisang, mangga + pisang, alpukat + pisang, nanas + strawberry, mangga + nanas + strawberry, mangga + nanas, strawberry + apel, mangga + strawberry', '2026-10-01 05:21:05', '2026-10-01 05:21:05', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_03_16_102314_add_simple_role_to_user_table', 1),
(4, '2019_04_20_125200_create_permission_tables', 1),
(5, '2019_04_20_130706_setup_role_permissions', 1),
(6, '2019_08_19_000000_create_failed_jobs_table', 1),
(7, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(8, '2020_03_25_170854_remove_passport', 1),
(9, '2026_09_11_154624_create_activity_logs_table', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` int(10) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Laravue\\Models\\User', 1),
(2, 'App\\Laravue\\Models\\User', 3),
(3, 'App\\Laravue\\Models\\User', 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengaturan`
--

CREATE TABLE `pengaturan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `tipe` varchar(255) NOT NULL DEFAULT 'string',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pengaturan`
--

INSERT INTO `pengaturan` (`id`, `key`, `value`, `tipe`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'nama_kedai', 'Kedai Miss Mojito', 'string', '2026-10-05 04:30:08', '2026-10-05 04:30:09', NULL),
(2, 'alamat_kedai', 'Jl. In Aja Dulu No. 123, Bandar Lampung', 'string', '2026-10-05 04:30:08', '2026-10-05 04:30:09', NULL),
(3, 'telepon_kedai', '0812-3456-7890', 'string', '2026-10-05 04:30:08', '2026-10-05 04:30:09', NULL),
(4, 'pajak_persen', '0', 'number', '2026-10-05 04:30:08', '2026-10-05 04:30:08', NULL),
(5, 'mata_uang', 'IDR', 'string', '2026-10-05 04:30:08', '2026-10-05 04:30:08', NULL),
(6, 'logo_kedai', '', 'image', '2026-10-05 04:30:08', '2026-10-05 04:30:09', NULL),
(7, 'struk_footer', 'Terima kasih telah berkunjung 🙏', 'string', '2026-10-05 04:30:08', '2026-10-05 04:30:09', NULL),
(8, 'email_kedai', 'info@kedaimissmojito.com', 'string', '2026-10-05 04:30:09', '2026-10-05 04:30:09', NULL),
(9, 'struk_header', 'Kedai Miss Mojito', 'string', '2026-10-05 04:30:09', '2026-10-05 04:30:09', NULL),
(10, 'kas_awal_default', '100000', 'number', '2026-10-05 04:30:09', '2026-10-05 04:30:09', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `permissions`
--

CREATE TABLE `permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'view menu dashboard', 'api', '2026-10-05 04:30:07', '2026-10-05 04:30:07'),
(2, 'view menu produk', 'api', '2026-10-05 04:30:07', '2026-10-05 04:30:07'),
(3, 'view menu kategori', 'api', '2026-10-05 04:30:07', '2026-10-05 04:30:07'),
(4, 'view menu stok', 'api', '2026-10-05 04:30:07', '2026-10-05 04:30:07'),
(5, 'view menu transaksi', 'api', '2026-10-05 04:30:07', '2026-10-05 04:30:07'),
(6, 'view menu sesi kasir', 'api', '2026-10-05 04:30:07', '2026-10-05 04:30:07'),
(7, 'view menu laporan', 'api', '2026-10-05 04:30:07', '2026-10-05 04:30:07'),
(8, 'view menu user', 'api', '2026-10-05 04:30:07', '2026-10-05 04:30:07'),
(9, 'view menu pengaturan', 'api', '2026-10-05 04:30:07', '2026-10-05 04:30:07'),
(10, 'view menu administrator', 'api', '2026-10-05 04:30:07', '2026-10-05 04:30:07'),
(11, 'view menu permission', 'api', '2026-10-05 04:30:07', '2026-10-05 04:30:07'),
(12, 'manage produk', 'api', '2026-10-05 04:30:07', '2026-10-05 04:30:07'),
(13, 'manage kategori', 'api', '2026-10-05 04:30:07', '2026-10-05 04:30:07'),
(14, 'manage stok', 'api', '2026-10-05 04:30:07', '2026-10-05 04:30:07'),
(15, 'manage transaksi', 'api', '2026-10-05 04:30:07', '2026-10-05 04:30:07'),
(16, 'manage pengaturan', 'api', '2026-10-05 04:30:08', '2026-10-05 04:30:08'),
(17, 'view sesi kasir', 'api', '2026-10-05 04:30:08', '2026-10-05 04:30:08'),
(18, 'open sesi kasir', 'api', '2026-10-05 04:30:08', '2026-10-05 04:30:08'),
(19, 'close sesi kasir', 'api', '2026-10-05 04:30:08', '2026-10-05 04:30:08'),
(20, 'view user', 'api', '2026-10-05 04:30:08', '2026-10-05 04:30:08'),
(21, 'manage own profile', 'api', '2026-10-05 04:30:08', '2026-10-05 04:30:08'),
(22, 'manage user', 'api', '2026-10-05 04:30:08', '2026-10-05 04:30:08'),
(23, 'manage permission', 'api', '2026-10-05 04:30:08', '2026-10-05 04:30:08');

-- --------------------------------------------------------

--
-- Struktur dari tabel `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `produk`
--

CREATE TABLE `produk` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kategori_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kode` varchar(255) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga_beli` decimal(15,2) NOT NULL DEFAULT 0.00,
  `harga_jual` decimal(15,2) NOT NULL DEFAULT 0.00,
  `stok` int(11) NOT NULL DEFAULT 0,
  `stok_minimal` int(11) NOT NULL DEFAULT 0,
  `satuan` varchar(255) NOT NULL DEFAULT 'pcs',
  `gambar` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `produk`
--

INSERT INTO `produk` (`id`, `kategori_id`, `kode`, `nama`, `deskripsi`, `harga_beli`, `harga_jual`, `stok`, `stok_minimal`, `satuan`, `gambar`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'PRD-20261001-0001', 'LECI MOJITO', 'Soda Series Miss Mojito : LECI MOJITO', '7000.00', '10000.00', 20, 20, 'pcs', 'uploads/produk/7VI3vZo3mT5NclNv1JzRejPWGpuPKbY3CubYI7YD.webp', 1, '2026-10-01 05:59:36', '2026-10-01 06:02:30', NULL),
(2, 1, 'PRD-20261001-0002', 'STRAWBERRY MOJITO', 'Soda Series Miss Mojito : STRAWBERRY MOJITO', '7000.00', '10000.00', 20, 20, 'pcs', 'uploads/produk/4QTayFieMZBGgtk5aP0D1PL37EIwRx5MRaozdsPd.webp', 1, '2026-10-01 06:10:32', '2026-10-01 06:10:32', NULL),
(3, 1, 'PRD-20261001-0003', 'MANGO SODA', 'Soda Series Miss Mojito : MANGO SODA', '7000.00', '10000.00', 20, 20, 'pcs', 'uploads/produk/dBTa3c0THhSbIV6H7bJ8G0kXhg6yfyvtMI8s3iVv.webp', 1, '2026-10-01 06:15:21', '2026-10-01 06:15:21', NULL),
(4, 1, 'PRD-20261001-0004', 'MORNING SUNRISE', 'Soda Series Miss Mojito : MORNING SUNRISE', '7000.00', '10000.00', 20, 20, 'pcs', 'uploads/produk/757Ni3qqcHkxfBp1708fM8KLGy8ceHKm9t8dPFbx.webp', 1, '2026-10-01 06:28:33', '2026-10-01 06:28:33', NULL),
(5, 1, 'PRD-20261001-0005', 'TROPICAL SUNSET', 'Soda Series Miss Mojito : TROPICAL SUNSET', '7000.00', '10000.00', 20, 20, 'pcs', 'uploads/produk/3n2BCHs0atd7N2lTeAHDs78lwD4fyhLq1EA7TwIt.webp', 1, '2026-10-01 06:36:56', '2026-10-01 06:36:56', NULL),
(6, 2, 'PRD-20261003-0001', 'LECI MOJITO FLOAT', 'Spesial Float : Leci Mojito Float', '10000.00', '13000.00', 20, 20, 'pcs', 'uploads/produk/XbLeSS7YEibxCPU5gFziLnr1wN9iMLmww7ONXWam.webp', 1, '2026-10-02 17:17:08', '2026-10-02 17:17:49', NULL),
(7, 2, 'PRD-20261003-0002', 'MANGO SODA FLOAT', 'Spesial Float : Mango Soda Float', '10000.00', '13000.00', 20, 20, 'pcs', 'uploads/produk/9RctFG9xbaZw7xt2HKPjmTwQhdxo5hmrY680uewU.webp', 1, '2026-10-02 17:18:57', '2026-10-02 17:40:48', NULL),
(8, 2, 'PRD-20261003-0003', 'REAL MANGO FLOAT', 'Spesial Float : Real Mango Float', '10000.00', '13000.00', 20, 20, 'pcs', 'uploads/produk/Qh4lF0DcwricJ9TyEyjhitjt87i3y29no2prLohn.webp', 1, '2026-10-02 17:20:13', '2026-10-02 18:01:18', NULL),
(9, 2, 'PRD-20261003-0004', 'MIX BERRY FLOAT', 'Spesial Float : Mix Berry Float', '10000.00', '13000.00', 20, 20, 'pcs', 'uploads/produk/II69SBUreppEjfoIqQcYrKs2Y0DI4QfhHiNH65EA.webp', 1, '2026-10-02 17:21:03', '2026-10-02 17:50:10', NULL),
(10, 2, 'PRD-20261003-0005', 'TROPICAL SUNSET FLOAT', 'Spesial Float : Tropical Sunset Float', '10000.00', '13000.00', 20, 20, 'pcs', 'uploads/produk/n21sXVguSd6nAorWoxuGOVntqGIdqbj8eJRMpNnv.webp', 1, '2026-10-02 17:21:38', '2026-10-02 18:05:07', NULL),
(11, 3, 'PRD-20261003-0006', 'DiMSUM KURO (original, orenji sauce, katsuo)', 'Dimsum Kuro (original, orenji sauce, katsuo)', '15000.00', '18000.00', 20, 20, 'pcs', 'uploads/produk/NFEfrKGNQqcDaWHsuT7lMCbm7o70iM4MpAs3e3Dy.webp', 1, '2026-10-02 17:22:24', '2026-10-02 19:22:07', NULL),
(12, 3, 'PRD-20261003-0007', 'DIMSUM ORENJI SAUCE ISI 6', 'Dimsum Orenji Sauce isi 6', '20000.00', '23000.00', 20, 20, 'pcs', 'uploads/produk/Y6pvhatiqC4qV8gJ0o7y1TbmnJduIAJaLkwmYmOB.webp', 1, '2026-10-02 17:23:17', '2026-10-02 19:22:28', NULL),
(13, 3, 'PRD-20261003-0008', 'DIMSUM GORENG ISI 2', 'Dimsum Goreng isi 2', '10000.00', '13000.00', 20, 20, 'pcs', 'uploads/produk/rTE05qQtjVlGWZ9rl8G0aliqbJoH1SrN2n7Cjgpc.webp', 1, '2026-10-02 17:24:47', '2026-10-02 19:21:42', NULL),
(14, 3, 'PRD-20261003-0009', 'CIRENG KUAH CREAMY', 'Cireng Kuah Creamy', '12000.00', '15000.00', 20, 20, 'pcs', 'uploads/produk/DwAllVsXMZtZ6v0yP8oSVblVjcx8bJWkgx3d1P87.webp', 1, '2026-10-02 17:25:36', '2026-10-02 19:21:33', NULL),
(15, 3, 'PRD-20261003-0010', 'DIMSUM KUAH CREAMY', 'Dimsum Kuah Creamy', '14000.00', '17000.00', 20, 20, 'pcs', 'uploads/produk/DisXrGxtDTRvTdiPLNMfK30azJAf9WFET9KG1Gvx.webp', 1, '2026-10-02 17:26:20', '2026-10-02 19:21:51', NULL),
(16, NULL, 'PRD-20261003-0011', 'DIMSUM GORENG KUAH CREAMY', 'Dimsum Goreng Kuah Creamy', '18000.00', '21000.00', 20, 20, 'pcs', 'uploads/produk/r9gj1vKytg9pNKxUKtt5OazilMQknk1uTl4dvuXD.webp', 1, '2026-10-02 17:27:05', '2026-10-03 09:36:13', NULL),
(17, 4, 'PRD-20261003-0012', 'JUS MANGGA', 'jus mangga', '7000.00', '10000.00', 20, 20, 'pcs', 'uploads/produk/AGmqDT36tNl1NT35X5jo4euxB0TTGz2H8qzHZMFz.webp', 1, '2026-10-02 17:27:57', '2026-10-02 19:20:04', NULL),
(18, 4, 'PRD-20261003-0013', 'JUS MELON', 'jus melon', '7000.00', '10000.00', 20, 20, 'pcs', 'uploads/produk/ZyTuKfAqavwT34SMUzjNl4WPYOCkIVgJ4UAhGHhG.webp', 1, '2026-10-02 17:28:32', '2026-10-02 19:20:25', NULL),
(19, 4, 'PRD-20261003-0014', 'JUS ALPUKAT', 'jus alpukat', '7000.00', '10000.00', 20, 20, 'pcs', 'uploads/produk/XlIy4OJdHyOpJUlvULJN1ReRfteryBZSsRlTB7tG.webp', 1, '2026-10-02 17:29:04', '2026-10-02 19:19:14', NULL),
(20, 4, 'PRD-20261003-0015', 'JUS STRAWBERRY', 'jus strawberry', '7000.00', '10000.00', 20, 20, 'pcs', 'uploads/produk/rEalk4eqlOM53YUgwALEFl1fG6KDYOUzc9sULnwc.webp', 1, '2026-10-02 17:29:36', '2026-10-02 19:21:12', NULL),
(21, 4, 'PRD-20261003-0016', 'JUS NANAS', 'jus nanas', '7000.00', '10000.00', 20, 20, 'pcs', 'uploads/produk/pAoemMtlDzO7bNPEIPlcyzRLDkNBW0wAcNtpGWDP.webp', 1, '2026-10-02 17:30:16', '2026-10-02 19:20:44', NULL),
(22, 4, 'PRD-20261003-0017', 'JUS APEL', 'jus apel', '7000.00', '10000.00', 20, 20, 'pcs', 'uploads/produk/Ivok7V2zOfbJZm42UVuRosaqUsDi8GuFlKs3cl7r.webp', 1, '2026-10-02 17:30:47', '2026-10-02 19:19:25', NULL),
(23, 4, 'PRD-20261003-0018', 'JUS SIRSAK', 'jus sirsak', '7000.00', '10000.00', 20, 20, 'pcs', 'uploads/produk/qqr44JTtqSlYCCojbC5GVKht57XRb1hB94q17Fyl.webp', 1, '2026-10-02 17:31:23', '2026-10-02 19:20:58', NULL),
(24, 4, 'PRD-20261003-0019', 'JUS JAMBU MERAH', 'jus jambu merah', '7000.00', '10000.00', 20, 20, 'pcs', 'uploads/produk/3FCGxK0QuD8uczFd4CFnAdyFQN2XaRskHrun8Eag.webp', 1, '2026-10-02 17:31:50', '2026-10-02 19:19:46', NULL),
(25, 4, 'PRD-20261003-0020', 'JUS BUAH NAGA', 'jus buah naga', '7000.00', '10000.00', 20, 20, 'pcs', 'uploads/produk/N6xmdyP2IYPhtFcAvzj6vBqTsxWBwfA4D6wRapI9.webp', 1, '2026-10-02 17:32:13', '2026-10-02 19:19:35', NULL),
(26, 5, 'PRD-20261003-0021', 'mix berry jus', 'mix berry jus', '10000.00', '13000.00', 20, 20, 'pcs', 'uploads/produk/TKhSks3bN4WZKu1Fef1CHlKMtqbRderBql6TInKd.webp', 1, '2026-10-02 17:33:20', '2026-10-02 19:23:07', NULL),
(27, 5, 'PRD-20261003-0022', 'mix jus mangga + alpukat', 'mix jus mangga + alpukat', '10000.00', '13000.00', 20, 20, 'pcs', 'uploads/produk/EnTa1vmY5tT2TeqCVC8pf3kw1BsxN7NyT80HAqyK.webp', 1, '2026-10-02 17:34:16', '2026-10-02 19:23:44', NULL),
(28, 5, 'PRD-20261003-0023', 'mix jus naga + pisang', 'mix jus naga + pisang', '10000.00', '13000.00', 20, 20, 'pcs', 'uploads/produk/zIm07vumvG0j54P6JOOqMsjeD5zchzzrmJrXvHq8.webp', 1, '2026-10-02 17:35:31', '2026-10-02 19:25:01', NULL),
(29, 5, 'PRD-20261003-0024', 'mix jus mangga + nanas + strawberry', 'mix jus mangga + nanas + strawberry', '10000.00', '13000.00', 20, 20, 'pcs', 'uploads/produk/EFL3qlzWB6MVav6Cr58uqEllsUT2TC1tXlLO7NLz.webp', 1, '2026-10-02 17:36:05', '2026-10-02 19:24:17', NULL),
(30, 5, 'PRD-20261003-0025', 'mix jus mangga + nanas', 'mix jus mangga + nanas', '10000.00', '13000.00', 20, 20, 'pcs', 'uploads/produk/H04AjV0JmEG8aWUxZLTcEl1ZAmWYC4vVR1hIAlCf.webp', 1, '2026-10-02 17:36:34', '2026-10-02 19:23:58', NULL),
(31, 5, 'PRD-20261003-0026', 'mix jus mangga + strawberry', 'mix jus mangga + strawberry', '10000.00', '13000.00', 20, 20, 'pcs', 'uploads/produk/wF2p4rrzQoYQfSbPNFyo4zYJJ9WppNSpeqolRDok.webp', 1, '2026-10-02 17:36:59', '2026-10-02 19:24:47', NULL),
(32, 5, 'PRD-20261003-0027', 'mix jus strawberry + pisang', 'mix jus strawberry + pisang', '10000.00', '13000.00', 20, 20, 'pcs', 'uploads/produk/ES92RhxiRL1yjfvN9APC1s7XI6G3ot9r0uFzxGOA.webp', 1, '2026-10-02 18:37:57', '2026-10-02 19:26:10', NULL),
(33, 5, 'PRD-20261003-0028', 'mix jus naga + pisang', 'mix jus naga + pisang', '10000.00', '13000.00', 20, 20, 'pcs', NULL, 1, '2026-10-02 18:39:05', '2026-10-02 19:25:27', '2026-10-02 19:25:27'),
(34, 5, 'PRD-20261003-0029', 'mix jus mangga + pisang', 'mix jus mangga + pisang', '10000.00', '13000.00', 20, 20, 'pcs', 'uploads/produk/3TUkgHG65gXHaWD7VA8BOBSNUds88l5UEdmF3YBS.webp', 1, '2026-10-02 18:40:02', '2026-10-02 19:24:30', NULL),
(35, 5, 'PRD-20261003-0030', 'mix jus alpukat + pisang', 'mix jus alpukat + pisang', '10000.00', '13000.00', 20, 20, 'pcs', 'uploads/produk/4AQgyAqGuC21miz6iATC1TZxukMTcYTxeINETcb6.webp', 1, '2026-10-02 18:40:50', '2026-10-02 19:23:28', NULL),
(36, 5, 'PRD-20261003-0031', 'mix jus nanas + strawberry', 'mix jus nanas + strawberry', '10000.00', '13000.00', 20, 20, 'pcs', 'uploads/produk/Kdh4WeWN3XnZRltodr8sJ3zGiIbTJUozzW70osdP.webp', 1, '2026-10-02 18:45:21', '2026-10-02 19:25:40', NULL),
(37, 5, 'PRD-20261003-0032', 'mix jus strawberry + apel', 'mix jus strawberry + apel', '10000.00', '13000.00', 20, 20, 'pcs', 'uploads/produk/RIOVhZBPmtWMCH6GYtneDOjPVCImyCVL6DK5spsT.webp', 1, '2026-10-02 18:46:55', '2026-10-02 19:25:54', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'api', '2026-10-05 04:30:05', '2026-10-05 04:30:05'),
(2, 'kasir', 'api', '2026-10-05 04:30:07', '2026-10-05 04:30:07'),
(3, 'manager', 'api', '2026-10-05 04:30:07', '2026-10-05 04:30:07');

-- --------------------------------------------------------

--
-- Struktur dari tabel `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `sesi_kasir`
--

CREATE TABLE `sesi_kasir` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `user_tutup_id` bigint(20) UNSIGNED DEFAULT NULL,
  `waktu_buka` timestamp NOT NULL DEFAULT current_timestamp(),
  `waktu_tutup` timestamp NULL DEFAULT NULL,
  `kas_awal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `kas_akhir_sistem` decimal(15,2) NOT NULL DEFAULT 0.00,
  `kas_akhir_fisik` decimal(15,2) DEFAULT NULL,
  `selisih` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_transaksi` int(11) NOT NULL DEFAULT 0,
  `total_penjualan` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_tunai` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_non_tunai` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('open','closed') NOT NULL DEFAULT 'open',
  `catatan_buka` text DEFAULT NULL,
  `catatan_tutup` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `stok_mutasi`
--

CREATE TABLE `stok_mutasi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `produk_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `tipe` enum('masuk','keluar','penyesuaian') NOT NULL,
  `jumlah` int(11) NOT NULL,
  `stok_sebelum` int(11) NOT NULL,
  `stok_sesudah` int(11) NOT NULL,
  `referensi` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `sesi_kasir_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `diskon` decimal(15,2) NOT NULL DEFAULT 0.00,
  `pajak` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `bayar` decimal(15,2) NOT NULL DEFAULT 0.00,
  `kembalian` decimal(15,2) NOT NULL DEFAULT 0.00,
  `metode_pembayaran` enum('cash','transfer','qris') NOT NULL DEFAULT 'cash',
  `status` enum('pending','selesai','batal') NOT NULL DEFAULT 'selesai',
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi_detail`
--

CREATE TABLE `transaksi_detail` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `transaksi_id` bigint(20) UNSIGNED NOT NULL,
  `produk_id` bigint(20) UNSIGNED NOT NULL,
  `nama_produk` varchar(255) NOT NULL,
  `harga` decimal(15,2) NOT NULL DEFAULT 0.00,
  `qty` int(11) NOT NULL DEFAULT 1,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `jabatan` varchar(255) DEFAULT NULL,
  `whatsapp_number` varchar(255) DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `device` varchar(255) DEFAULT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `last_login_location` varchar(255) DEFAULT NULL,
  `platform` varchar(255) DEFAULT NULL,
  `force_logout` tinyint(1) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `avatar`, `active`, `status`, `jabatan`, `whatsapp_number`, `last_login_at`, `last_login_ip`, `device`, `browser`, `last_login_location`, `platform`, `force_logout`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'superadmin@cp.com', NULL, '$2y$10$nNlwRLYFaVDeaZJV2sYX1uLTkO38xuxDcSK3qX5p0txBODR4NCdjS', NULL, 1, 'active', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '2026-10-05 04:30:09', '2026-10-05 04:30:09'),
(2, 'Manager', 'manager@cp.com', NULL, '$2y$10$qk4sSyt7v8iCij1YcGI/ku/MKalGXlxvpeMLCQPcjHuuuWQ/7nsia', NULL, 1, 'active', 'Manager', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '2026-10-05 04:30:09', '2026-10-05 04:30:09'),
(3, 'Kasir', 'kasir@cp.com', NULL, '$2y$10$oJrlHpiz17PlDu6RO/5Z4OrbyLfiJ2GLsSlPhEJU67WCnk3MtGIG.', NULL, 1, 'active', 'Kasir', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '2026-10-05 04:30:09', '2026-10-05 04:30:09');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_user_id_index` (`user_id`),
  ADD KEY `activity_logs_action_index` (`action`),
  ADD KEY `activity_logs_module_index` (`module`),
  ADD KEY `activity_logs_subject_id_index` (`subject_id`),
  ADD KEY `activity_logs_created_at_index` (`created_at`),
  ADD KEY `activity_logs_subject_type_subject_id_index` (`subject_type`,`subject_id`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kategori_nama_unique` (`nama`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indeks untuk tabel `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indeks untuk tabel `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indeks untuk tabel `pengaturan`
--
ALTER TABLE `pengaturan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pengaturan_key_unique` (`key`);

--
-- Indeks untuk tabel `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indeks untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `produk_kode_unique` (`kode`),
  ADD KEY `produk_kategori_id_foreign` (`kategori_id`),
  ADD KEY `produk_nama_index` (`nama`),
  ADD KEY `produk_is_active_index` (`is_active`);

--
-- Indeks untuk tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indeks untuk tabel `sesi_kasir`
--
ALTER TABLE `sesi_kasir`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sesi_kasir_kode_unique` (`kode`),
  ADD KEY `sesi_kasir_user_tutup_id_foreign` (`user_tutup_id`),
  ADD KEY `sesi_kasir_status_index` (`status`),
  ADD KEY `sesi_kasir_waktu_buka_index` (`waktu_buka`),
  ADD KEY `sesi_kasir_user_id_index` (`user_id`);

--
-- Indeks untuk tabel `stok_mutasi`
--
ALTER TABLE `stok_mutasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stok_mutasi_user_id_foreign` (`user_id`),
  ADD KEY `stok_mutasi_produk_id_index` (`produk_id`),
  ADD KEY `stok_mutasi_tipe_index` (`tipe`),
  ADD KEY `stok_mutasi_created_at_index` (`created_at`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaksi_kode_unique` (`kode`),
  ADD KEY `transaksi_user_id_foreign` (`user_id`),
  ADD KEY `transaksi_kode_index` (`kode`),
  ADD KEY `transaksi_status_index` (`status`),
  ADD KEY `transaksi_created_at_index` (`created_at`),
  ADD KEY `transaksi_sesi_kasir_id_index` (`sesi_kasir_id`);

--
-- Indeks untuk tabel `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaksi_detail_transaksi_id_index` (`transaksi_id`),
  ADD KEY `transaksi_detail_produk_id_index` (`produk_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `pengaturan`
--
ALTER TABLE `pengaturan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `produk`
--
ALTER TABLE `produk`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT untuk tabel `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `sesi_kasir`
--
ALTER TABLE `sesi_kasir`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `stok_mutasi`
--
ALTER TABLE `stok_mutasi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `produk_kategori_id_foreign` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `sesi_kasir`
--
ALTER TABLE `sesi_kasir`
  ADD CONSTRAINT `sesi_kasir_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `sesi_kasir_user_tutup_id_foreign` FOREIGN KEY (`user_tutup_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `stok_mutasi`
--
ALTER TABLE `stok_mutasi`
  ADD CONSTRAINT `stok_mutasi_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stok_mutasi_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_sesi_kasir_id_foreign` FOREIGN KEY (`sesi_kasir_id`) REFERENCES `sesi_kasir` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transaksi_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  ADD CONSTRAINT `transaksi_detail_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`),
  ADD CONSTRAINT `transaksi_detail_transaksi_id_foreign` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
