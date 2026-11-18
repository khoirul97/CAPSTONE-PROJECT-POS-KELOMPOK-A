# Capstone Project POS

Aplikasi **Point of Sale (POS)** berbasis Laravel dan Vue.js.

# Proyek ini dibuat untuk memenuhi tugas Mata Kuliah Capstone Project
# Anggota kelompok
```bash
1	ANNISA NUR RAHMAWATI	048874625
2	DENIATI	                054280496
3	DEVI ANTIKA SARI        050005701
4	KHOIRUL NURYAHYA        052171869
5	WAHONO WIBOWO	        049148853
```

## 📋 Persyaratan Sistem

Sebelum menjalankan project, pastikan perangkat sudah memiliki:

| Software           | Versi                                  |
| ------------------ | -------------------------------------- |
| XAMPP              | **7.4.33**                             |
| PHP                | **7.4.x**                              |
| Composer           | Versi yang kompatibel dengan PHP 7.4   |
| NVM                | Terbaru                                |
| Node.js            | **v16.17.1**                           |
| NPM                | Mengikuti Node.js v16.17.1             |
| Visual Studio Code | Terbaru / versi yang mendukung project |
| MySQL              | Mengikuti versi MySQL dari XAMPP       |
| Git                | Terbaru                                |

> **Penting:** Project ini menggunakan **Node.js v16.17.1**. Pastikan versi Node.js yang aktif adalah versi tersebut sebelum menjalankan `npm install` atau perintah `npm`.

---

# 1. Persiapan XAMPP

Install **XAMPP 7.4.33**.

Pastikan service berikut dapat dijalankan:

* Apache
* MySQL

PHP yang digunakan harus berasal dari XAMPP 7.4.33.

Untuk memastikan versi PHP:

```bash
php -v
```

Hasil yang diharapkan kurang lebih:

```text
PHP 7.4.x
```

Cek juga versi Composer:

```bash
composer -V
```

---

# 2. Persiapan NVM dan Node.js

Project ini menggunakan:

```text
Node.js v16.17.1
```

Pastikan NVM sudah terinstall.

Cek NVM:

```bash
nvm --version
```

Install Node.js v16.17.1:

```bash
nvm install 16.17.1
```

Gunakan Node.js v16.17.1:

```bash
nvm use 16.17.1
```

Kemudian pastikan versi Node.js:

```bash
node -v
```

Hasil:

```text
v16.17.1
```

Cek NPM:

```bash
npm -v
```

> **Penting:** Jangan menggunakan Node.js versi lain untuk project ini. Gunakan **Node.js v16.17.1**.

---

# 3. Clone Repository

Simpan project di folder kerja Anda (jangan di `C:\Windows\System32`), lalu clone repository GitHub:

```cmd
cd /d "%USERPROFILE%\Documents"
git clone https://github.com/khoirul97/CAPSTONE-PROJECT-POS-KELOMPOK-A.git
```

Setelah proses clone selesai, akan terbentuk direktori:

```text
CAPSTONE-PROJECT-POS-KELOMPOK-A
```

Masuk ke direktori project:

```cmd
cd CAPSTONE-PROJECT-POS-KELOMPOK-A
```

---

# 4. Masuk ke Direktori App

Project utama berada di dalam direktori `app`.

Jalankan:

```bash
cd app
```

Struktur direktori kurang lebih:

```text
capstone-project-pos/
└── app/
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── public/
    ├── resources/
    ├── routes/
    ├── storage/
    ├── vendor/
    ├── .env.example
    ├── artisan
    ├── composer.json
    └── package.json
```

---

# 5. Membuat File `.env`

Di dalam direktori `app`, cari file:

```text
.env.example
```

Copy file tersebut kemudian rename menjadi:

```text
.env
```

Contoh menggunakan terminal:

### Windows

```bash
copy .env.example .env
```

### Linux / macOS

```bash
cp .env.example .env
```

---

# 6. Konfigurasi `.env`

Buka file:

```text
app/.env
```

Kemudian sesuaikan konfigurasi project dan database.

Contoh:

```env
APP_NAME="Capstone Project POS"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=capstone_project_pos
DB_USERNAME=root
DB_PASSWORD=
```

Sesuaikan bagian database dengan konfigurasi MySQL masing-masing.

### Konfigurasi MySQL

Default XAMPP:

```text
Host     : localhost
User     : root
Password : kosong
Port     : 3306
```

Jika MySQL Anda menggunakan password, isi:

```env
DB_PASSWORD=password_mysql_anda
```

Contoh:

```env
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=capstone_project_pos
DB_USERNAME=root
DB_PASSWORD=
```

> Nama database dapat disesuaikan dengan database yang digunakan pada komputer masing-masing.

Pastikan database tersebut sudah dibuat di MySQL/phpMyAdmin.

---

# 7. Install Dependency Laravel

Masih berada di direktori:

```text
capstone-project-pos/app
```

Jalankan:

```bash
composer install
```

Composer akan menginstall seluruh dependency PHP yang dibutuhkan oleh project.

Setelah selesai, direktori:

```text
vendor/
```

akan terbentuk.

---

# 8. Generate Application Key

Setelah `composer install` selesai, jalankan:

```bash
php artisan key:generate
```

Jika berhasil, file `.env` akan memiliki:

```env
APP_KEY=base64:...
```

---

# 9. Migration dan Seeder Database

Pastikan:

1. MySQL sudah berjalan melalui XAMPP.
2. Database sudah dibuat.
3. Konfigurasi database pada `.env` sudah benar.

Kemudian jalankan:

```bash
php artisan migrate --seed
```

Perintah tersebut akan:

* membuat tabel database;
* menjalankan migration;
* menjalankan seeder;
* memasukkan data awal yang dibutuhkan aplikasi.

---

# 10. Storage Link

Jalankan:

```bash
php artisan storage:link
```

Perintah tersebut membuat symbolic link dari:

```text
public/storage
```

ke:

```text
storage/app/public
```

Storage link diperlukan agar file yang tersimpan pada Laravel Storage dapat diakses melalui browser.

---

# 11. Menjalankan Backend Laravel

Setelah seluruh konfigurasi selesai, jalankan:

```bash
php artisan serve
```

Biasanya Laravel akan berjalan pada:

```text
http://127.0.0.1:8000
```

atau:

```text
http://localhost:8000
```

Terminal biasanya akan menampilkan:

```text
INFO  Server running on [http://127.0.0.1:8000].
```

> **Catatan:** Port dapat berbeda jika port `8000` sedang digunakan.

Contoh menjalankan Laravel pada port tertentu:

```bash
php artisan serve --port=8080
```

Kemudian aplikasi dapat diakses melalui:

```text
http://localhost:8080
```

---

# 12. Install Dependency Frontend

Frontend berada di project yang sama, yaitu direktori:

```text
capstone-project-pos/app
```

Pastikan masih berada di direktori tersebut.

Kemudian jalankan:

```bash
npm install
```

Pastikan Node.js yang digunakan adalah:

```text
v16.17.1
```

Cek terlebih dahulu:

```bash
node -v
```

Hasil harus:

```text
v16.17.1
```

---

# 13. Menjalankan Frontend

## Development + Hot Reload

Untuk development dengan **hot reload**, gunakan:

```bash
npm run hot
```

Hot reload akan membantu proses development karena perubahan pada source code frontend dapat langsung diterapkan tanpa harus menjalankan build production setiap kali melakukan perubahan.

---

## Development Tanpa Hot Reload

Untuk development tanpa hot reload:

```bash
npm run dev
```

---

## Build Production

Untuk membuat build frontend production:

```bash
npm run production
```

Build hasil production akan dibuat sesuai konfigurasi webpack project.

---

# 14. Akses Aplikasi

Setelah Laravel berhasil dijalankan:

```bash
php artisan serve
```

akses aplikasi melalui browser menggunakan alamat yang ditampilkan oleh Laravel.

Default:

```text
http://localhost:8000
```

atau:

```text
http://127.0.0.1:8000
```

Jika menggunakan port lain, sesuaikan dengan port yang ditampilkan pada terminal.

Contoh:

```bash
php artisan serve --port=8080
```

maka akses:

```text
http://localhost:8080
```

---

# 15. Urutan Instalasi Singkat

Jika semua requirement sudah tersedia, urutan instalasi secara keseluruhan adalah:

```cmd
git clone https://github.com/khoirul97/CAPSTONE-PROJECT-POS-KELOMPOK-A.git

cd CAPSTONE-PROJECT-POS-KELOMPOK-A

cd app
```

Buat `.env` (Windows Command Prompt):

```cmd
copy .env.example .env
```

Kemudian pastikan MySQL sudah berjalan, buat database `capstone_project_pos` melalui phpMyAdmin, dan sesuaikan konfigurasi database pada `.env`. Jangan mengunggah file `.env` ke GitHub.

Install backend:

```bash
composer install
```

Generate application key:

```bash
php artisan key:generate
```

Migration dan seed:

```bash
php artisan migrate --seed
```

Storage:

```bash
php artisan storage:link
```

Install frontend:

```bash
npm install
```

Jalankan Laravel:

```bash
php artisan serve
```

Untuk development frontend dengan hot reload:

```bash
npm run hot
```

---

# 16. Struktur Direktori

Struktur utama project:

```text
capstone-project-pos/
│
└── app/
    │
    ├── app/
    │   ├── Console/
    │   ├── Exceptions/
    │   ├── Http/
    │   ├── Laravue/
    │   ├── Models/
    │   └── Providers/
    │
    ├── bootstrap/
    │
    ├── config/
    │
    ├── database/
    │   ├── factories/
    │   ├── migrations/
    │   └── seeders/
    │
    ├── public/
    │
    ├── resources/
    │   ├── js/
    │   ├── lang/
    │   └── views/
    │
    ├── routes/
    │
    ├── storage/
    │
    ├── vendor/
    │
    ├── .env
    ├── .env.example
    ├── artisan
    ├── composer.json
    ├── package.json
    └── webpack.mix.js
```

---

# 17. Troubleshooting

## PHP tidak ditemukan

Jika muncul:

```text
'php' is not recognized as an internal or external command
```

pastikan PHP XAMPP sudah masuk ke environment variable `PATH`.

Untuk XAMPP 7.4.33 biasanya lokasi PHP:

```text
C:\xampp\php
```

Kemudian tutup dan buka kembali terminal.

Cek:

```bash
php -v
```

---

## Node.js bukan v16.17.1

Cek:

```bash
node -v
```

Jika bukan:

```text
v16.17.1
```

gunakan NVM:

```bash
nvm use 16.17.1
```

Kemudian cek kembali:

```bash
node -v
```

---

## MySQL tidak dapat terhubung

Pastikan MySQL pada XAMPP sudah **Running**.

Periksa konfigurasi `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=capstone_project_pos
DB_USERNAME=root
DB_PASSWORD=
```

Kemudian jalankan kembali:

```bash
php artisan migrate --seed
```

---

## Application key belum tersedia

Jalankan:

```bash
php artisan key:generate
```

---

## Storage tidak dapat diakses

Jalankan:

```bash
php artisan storage:link
```

Jika sebelumnya sudah pernah membuat link dan mengalami masalah, periksa direktori:

```text
public/storage
```

dan:

```text
storage/app/public
```

---

# 18. Perintah Development

### Backend

```bash
php artisan serve
```

### Frontend — Hot Reload

```bash
npm run hot
```

### Frontend — Development

```bash
npm run dev
```

### Frontend — Production Build

```bash
npm run production
```

---

# 19. Catatan Penting

* Gunakan **XAMPP 7.4.33**.
* Gunakan **PHP 7.4.x**.
* Gunakan **Node.js v16.17.1**.
* Node.js sebaiknya dikelola menggunakan **NVM**.
* Jangan commit file `.env` ke repository.
* Pastikan MySQL sudah aktif sebelum menjalankan migration.
* Jalankan seluruh perintah Laravel dan NPM dari direktori:

```text
capstone-project-pos/app
```

* Port Laravel mengikuti port yang ditampilkan oleh `php artisan serve`.
* Untuk development frontend dengan hot reload gunakan:

```bash
npm run hot
```

---

# 20. Quick Start

Untuk developer yang sudah menyiapkan seluruh requirement:

```cmd
cd /d "%USERPROFILE%\Documents"

git clone https://github.com/khoirul97/CAPSTONE-PROJECT-POS-KELOMPOK-A.git

cd CAPSTONE-PROJECT-POS-KELOMPOK-A\app

copy .env.example .env

composer install

php artisan key:generate

php artisan migrate --seed

php artisan storage:link

npm install

php artisan test

php artisan serve
```

Kemudian, pada terminal lain:

```cmd
cd /d "%USERPROFILE%\Documents\CAPSTONE-PROJECT-POS-KELOMPOK-A\app"

nvm use 16.17.1

npm run hot
```

Buka browser:

```text
http://localhost:8000
```

**Capstone Project POS — Ready for Development 🚀**
