# WarungU Laravel

Versi Laravel dari aplikasi WarungU PHP Native — **identik pixel-perfect** dengan tampilan dan fungsi aslinya.

---

## 🚀 Cara Install & Jalankan

### Langkah 1 — Buat project Laravel baru

```bash
composer create-project laravel/laravel warungu-app "^10.0"
cd warungu-app
```

### Langkah 2 — Salin semua file dari zip ini

Salin **semua isi folder `warungu_laravel_v2/`** ke dalam folder `warungu-app/` yang baru dibuat.
File yang ada di zip akan **menimpa** file default Laravel (itu memang disengaja).

```
warungu_laravel_v2/
├── app/               → salin ke warungu-app/app/
├── database/          → salin ke warungu-app/database/
├── resources/         → salin ke warungu-app/resources/
├── routes/            → salin ke warungu-app/routes/
├── config/            → salin ke warungu-app/config/
├── public/css/        → salin ke warungu-app/public/css/
├── public/js/         → salin ke warungu-app/public/js/
├── .env.example       → salin ke warungu-app/
└── composer.json      → salin ke warungu-app/
```

### Langkah 3 — Setup environment

```bash
cp .env.example .env
php artisan key:generate
```

### Langkah 4 — Konfigurasi database

Edit file `.env`, sesuaikan dengan MySQL kamu:

```env
DB_DATABASE=dbwarung_laravel
DB_USERNAME=root
DB_PASSWORD=           ← isi password MySQL kamu (kosong jika tidak ada)
```

Buat database baru di phpMyAdmin atau terminal:

```sql
CREATE DATABASE dbwarung_laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

### Langkah 5 — Migrasi & Seed data

```bash
php artisan migrate
php artisan db:seed
```

### Langkah 6 — Jalankan server

```bash
php artisan serve
```

Buka browser: **http://localhost:8000**

---

## 🔑 Akun Login Default

| Username  | Password   | Role    | Warung         |
|-----------|------------|---------|----------------|
| `pemilik` | `password` | Pemilik | Warung Berkah  |
| `pegawai1`| `password` | Pegawai | Warung Berkah  |
| `pegawai2`| `password` | Pegawai | Warung Makmur  |
| `pegawai3`| `password` | Pegawai | Warung Bogor   |

---

## 📁 Struktur File

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── BaseController.php       ← helper rupiah(), catatLog(), getFilterWarung()
│   │   ├── AuthController.php       ← login, logout, register
│   │   ├── DashboardController.php  ← dashboard + grafik
│   │   ├── KasirController.php      ← POS + proses transaksi JSON
│   │   ├── PenjualanController.php  ← list + detail transaksi
│   │   ├── BarangController.php     ← CRUD barang + stok masuk
│   │   ├── PegawaiController.php    ← manajemen pegawai
│   │   ├── WarungController.php     ← CRUD warung
│   │   ├── KategoriController.php   ← CRUD kategori
│   │   └── LaporanController.php    ← laporan + export CSV
│   └── Middleware/
│       └── PemilikMiddleware.php    ← cek role pemilik
├── Models/
│   ├── User.php
│   ├── Warung.php
│   ├── Barang.php
│   ├── Kategori.php
│   ├── Transaksi.php
│   ├── DetailTransaksi.php
│   └── StokMasuk.php
database/
├── migrations/
│   └── 2024_01_01_..._create_warungu_tables.php
└── seeders/
    └── DatabaseSeeder.php
resources/views/
├── layouts/app.blade.php         ← layout utama (sidebar + topbar)
├── partials/filter_warung.blade  ← filter warung pemilik
├── auth/login.blade.php
├── auth/register.blade.php
├── dashboard/index.blade.php
├── kasir/index.blade.php         ← halaman POS full (standalone, bukan extends)
├── penjualan/index.blade.php
├── penjualan/detail.blade.php
├── barang/index.blade.php
├── barang/tambah.blade.php
├── barang/edit.blade.php
├── barang/tambah_stok.blade.php
├── pegawai/index.blade.php
├── pegawai/edit.blade.php
├── warung/index.blade.php
├── warung/edit.blade.php
├── kategori/index.blade.php
├── kategori/edit.blade.php
└── laporan/index.blade.php
public/
├── css/style.css     ← CSS identik dengan native (copy langsung)
└── js/main.js        ← JS identik dengan native (copy langsung)
routes/web.php
```

---

## ✅ Fitur Identik dengan Native

| Fitur                          | Native PHP | Laravel |
|--------------------------------|-----------|---------|
| Login / logout / remember me   | ✅        | ✅      |
| Dashboard + grafik 7 hari      | ✅        | ✅      |
| Pie chart metode pembayaran    | ✅        | ✅      |
| Kasir POS (full identical)     | ✅        | ✅      |
| Metode Cash & QRIS             | ✅        | ✅      |
| Scan barcode                   | ✅        | ✅      |
| Jual per satuan / ketengan     | ✅        | ✅      |
| Struk cetak                    | ✅        | ✅      |
| CRUD Barang                    | ✅        | ✅      |
| Tambah stok + riwayat          | ✅        | ✅      |
| Hapus barang (cek FK)          | ✅        | ✅      |
| Data Penjualan + filter metode | ✅        | ✅      |
| Detail transaksi / struk       | ✅        | ✅      |
| Laporan 4 jenis + export CSV   | ✅        | ✅      |
| Manajemen Pegawai              | ✅        | ✅      |
| Manajemen Warung               | ✅        | ✅      |
| Manajemen Kategori             | ✅        | ✅      |
| Filter warung pemilik          | ✅        | ✅      |
| Bug warung pemilik di kasir    | ✅ Fixed  | ✅ Fixed |

---

## ❗ Troubleshooting

**`php artisan migrate` error foreign key**
→ Pastikan urutan tabel benar. Migration sudah diurutkan dengan benar di satu file.

**Halaman putih / error 500**
→ Cek `storage/logs/laravel.log`. Pastikan `php artisan key:generate` sudah dijalankan.

**Login tidak bisa**
→ Pastikan seeder sudah dijalankan: `php artisan db:seed`

**Style tidak muncul**
→ Pastikan `public/css/style.css` dan `public/js/main.js` sudah ada.
