<div align="center">

# Sistem Manajemen Kos

**Aplikasi web untuk manajemen operasional rumah kos — kamar, penghuni, transaksi, dan laporan keuangan.**

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16+-4169E1?style=flat-square&logo=postgresql&logoColor=white)](https://postgresql.org)
[![Livewire](https://img.shields.io/badge/Livewire-3.x-FB70A9?style=flat-square&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-MIT-239ba7?style=flat-square)](LICENSE)

[Tentang](#tentang) · [Fitur](#fitur) · [Tech Stack](#tech-stack) · [Instalasi](#instalasi) · [Penggunaan](#penggunaan) · [Struktur Proyek](#struktur-proyek)

</div>

---

## Tentang

Proyek skripsi ini adalah sistem informasi manajemen kos berbasis web yang dirancang untuk membantu pemilik kos dalam mengelola operasional sehari-hari secara digital. Sistem ini menggantikan pencatatan manual dengan antarmuka yang terstruktur, aman, dan mudah digunakan.

Dibangun menggunakan **Laravel 12 + Livewire + PostgreSQL** dengan metodologi pengembangan **Prototyping**.

**Dikembangkan oleh:** Yudistio Izza Al Farisi — Informatika, Universitas Singaperbangsa Karawang (2021–2026)

---

## Fitur

| Modul | Deskripsi |
|---|---|
| **Manajemen Kamar** | Layout kamar interaktif visual (mirip booking kursi bioskop) |
| **Manajemen Penghuni** | Data penghuni, kontrak sewa, dan riwayat hunian |
| **Transaksi** | Pencatatan pemasukan dan pengeluaran operasional |
| **Laporan Keuangan** | Rekap bulanan dengan ekspor PDF & Excel |
| **Role-Based Access Control** | Pembagian hak akses: Admin dan Pemilik |
| **Audit Trail** | Log seluruh aktivitas pengguna di sistem |
| **Autentikasi** | Login, register, dan manajemen sesi via Laravel Fortify |

---

## Tech Stack

- **Backend:** Laravel 12, PHP 8.2+, Laravel Fortify, Livewire Volt
- **Frontend:** Livewire Flux, Tailwind CSS, Vite
- **Database:** PostgreSQL 16+
- **Export:** DomPDF (PDF), Maatwebsite Excel
- **Dev Tools:** Laravel Pail, Laravel Pint, PHPUnit

---

## Instalasi

### Prasyarat

Pastikan environment kamu sudah memiliki tools berikut sebelum memulai:

| Tool | Versi Minimum | Cek Versi |
|---|---|---|
| PHP | 8.2+ | `php -v` |
| Composer | 2.x | `composer -V` |
| Node.js | 18+ | `node -v` |
| npm | 9+ | `npm -v` |
| PostgreSQL | 14+ | `psql --version` |
| Git | - | `git --version` |

---

### Langkah 1 — Clone Repository

```bash
git clone https://github.com/Yudistioizza/skripsi_kos.git
cd skripsi_kos
```

---

### Langkah 2 — Install Dependency PHP

```bash
composer install
```

> Proses ini mengunduh semua package Laravel dan dependency lainnya dari `composer.json`.

---

### Langkah 3 — Konfigurasi Environment

Salin file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Kemudian buka file `.env` dan sesuaikan konfigurasi database:

```env
APP_NAME="Manajemen Kos"
APP_ENV=local
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=skripsi_kos
DB_USERNAME=postgres
DB_PASSWORD=your_password_here
```

---

### Langkah 4 — Buat Database PostgreSQL

Buka terminal PostgreSQL atau tools seperti pgAdmin, lalu buat database baru:

```sql
CREATE DATABASE skripsi_kos;
```

Atau via terminal:

```bash
psql -U postgres -c "CREATE DATABASE skripsi_kos;"
```

---

### Langkah 5 — Generate Application Key

```bash
php artisan key:generate
```

---

### Langkah 6 — Jalankan Migrasi & Seeder

```bash
# Buat semua tabel database
php artisan migrate

# (Opsional) Isi data awal / dummy data
php artisan db:seed
```

Untuk reset dan mulai ulang dari awal:

```bash
php artisan migrate:fresh --seed
```

---

### Langkah 7 — Install Dependency Frontend

```bash
npm install
```

---

### Langkah 8 — Storage Link

```bash
php artisan storage:link
```

---

### Langkah 9 — Jalankan Development Server

**Opsi A — Jalankan semua sekaligus (direkomendasikan):**

```bash
composer run dev
```

Perintah ini akan menjalankan secara bersamaan:
- `php artisan serve` → Laravel dev server di `http://localhost:8000`
- `npm run dev` → Vite HMR untuk asset frontend
- `php artisan queue:listen` → Queue worker

**Opsi B — Jalankan terpisah:**

```bash
# Terminal 1 — Laravel server
php artisan serve

# Terminal 2 — Vite (frontend assets)
npm run dev
```

---

### Langkah 10 — Akses Aplikasi

Buka browser dan akses:

```
http://localhost:8000
```

Buat akun baru melalui halaman register, atau gunakan seeder jika tersedia.

---

## Penggunaan

### Role & Akses

| Role | Akses |
|---|---|
| **Admin** | Akses penuh: kamar, penghuni, transaksi, laporan, pengaturan, audit trail |
| **Pemilik** | Akses terbatas sesuai konfigurasi |

### Alur Umum

1. Login / Register akun
2. Tambahkan data kamar via modul **Manajemen Kamar**
3. Daftarkan penghuni dan assign ke kamar
4. Catat transaksi masuk/keluar tiap bulan
5. Generate laporan keuangan di modul **Laporan**

---

## Struktur Proyek

```
skripsi_kos/
├── app/
│   ├── Http/           # Controllers, Middleware, Requests
│   ├── Livewire/       # Livewire components
│   └── Models/         # Eloquent models
├── database/
│   ├── migrations/     # Skema tabel
│   └── seeders/        # Data awal
├── resources/
│   ├── views/          # Blade templates & Livewire views
│   └── css/            # Stylesheet
├── routes/
│   └── web.php         # Routing aplikasi
├── .env.example        # Template konfigurasi environment
├── composer.json       # PHP dependencies
└── package.json        # Node.js dependencies
```

---

## Troubleshooting

**Error: `SQLSTATE[08006] connection refused`**
Pastikan service PostgreSQL berjalan dan konfigurasi `DB_*` di `.env` sudah benar.

**Error: `Class "XXX" not found` setelah migrate**
Jalankan `composer dump-autoload` untuk refresh autoloader.

**Halaman blank setelah login**
Pastikan `npm run dev` atau `npm run build` sudah dijalankan agar asset Vite tersedia.

**Permission error pada storage**
```bash
chmod -R 775 storage bootstrap/cache
```

---

## Lisensi

Proyek ini menggunakan lisensi [MIT](LICENSE). Bebas digunakan untuk keperluan akademik dan pembelajaran.

---

<div align="center">

Dikembangkan oleh **[Yudistio Izza Al Farisi](https://github.com/Yudistioizza)** · Universitas Singaperbangsa Karawang · 2025

</div>
