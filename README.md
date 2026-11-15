# WarungU — Kasir dan Manajemen Stok untuk Beberapa Warung

Aplikasi web kasir untuk warung. Satu pemilik dapat mengelola beberapa warung, dan pegawai bertugas sebagai kasir.
Dibuat sebagai Tugas Project mata kuliah **Cyber Security** (Pengembangan dan Pengamanan Aplikasi Web).

- **URL aplikasi:** `[ISI: https://nama-proyek.vercel.app]`
- **Akun uji coba:** tercantum di laporan PDF (tidak ditulis di repository).

## Fitur

| Peran | Fitur |
|---|---|
| Semua | Login, logout, ubah profil dan password, "Ingat saya" 30 hari |
| Pengunjung | Pendaftaran akun pegawai (menunggu persetujuan pemilik) |
| Pegawai (kasir) | Katalog barang, pencarian nama atau barcode, keranjang, pembayaran tunai atau QRIS, kembalian otomatis, riwayat penjualan dan laporan warungnya |
| Pemilik | Semua fitur pegawai untuk seluruh warung, serta CRUD barang, kategori, pegawai, dan warung, tambah stok, persetujuan atau penolakan pendaftar, aktif atau nonaktifkan pegawai, dashboard, laporan, dan ekspor CSV |

Aktivitas penting (login, transaksi, stok, pengelolaan pegawai) dicatat di tabel `tb_log_aktivitas`.

## Teknologi

- Laravel 12 (PHP 8.2 atau lebih baru), Blade, Bootstrap 5.3, Chart.js, Font Awesome
- Database MySQL (lokal) / TiDB Cloud Starter yang kompatibel dengan MySQL (online)
- Hosting: Vercel (container Docker dengan FrankenPHP)

## Instalasi dan menjalankan secara lokal

1. Pasang PHP 8.2+, Composer, dan MySQL (misalnya XAMPP). Letakkan proyek **di luar** folder `htdocs`.
2. `composer install`
3. `copy .env.example .env` (Windows) atau `cp .env.example .env`, lalu `php artisan key:generate`
4. Buat database kosong `dbwarungu`, lalu isi `.env` (lihat tabel di bawah).
5. `php artisan migrate --seed`
6. `php artisan serve`, lalu buka `http://127.0.0.1:8000`

## Konfigurasi environment variable

Nilai rahasia **tidak** ada di repository. Salin dari `.env.example`, lalu isi nilainya sendiri.

| Variabel | Keterangan |
|---|---|
| `APP_KEY` | Kunci aplikasi, dibuat dengan `php artisan key:generate` |
| `APP_ENV`, `APP_DEBUG` | Lokal boleh `local` dan `false`. Di server online wajib `production` dan `false` |
| `APP_URL` | Alamat aplikasi |
| `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Koneksi database |
| `MYSQL_ATTR_SSL_CA` | Lokasi sertifikat CA. Wajib untuk database online (TLS) |
| `SESSION_DRIVER`, `CACHE_STORE` | `database` (agar sesi dan cache bersama di lingkungan serverless) |
| `SESSION_SECURE_COOKIE` | `true` saat memakai HTTPS |
| `SEED_PASSWORD_PEMILIK`, `SEED_PASSWORD_PEGAWAI` | Password akun demo untuk `db:seed`. Jika kosong dibuat acak dan ditampilkan sekali |

## Penerapan keamanan

| Aspek | Penerapan | Lokasi |
|---|---|---|
| Hash password | bcrypt bawaan Laravel | `AuthController`, `PegawaiController` |
| SQL Injection | Eloquent dan query terparameter | seluruh controller |
| XSS | Escape otomatis Blade `{{ }}`, CSP dibatasi ke CDN yang dipakai | views, `SecurityHeaders` |
| CSRF | Token CSRF di semua form, hapus dan logout memakai metode POST atau DELETE | views, `routes/web.php` |
| Sesi | Sesi di database, cookie `HttpOnly`, `SameSite=Lax`, `Secure` di produksi, ID sesi diganti saat login | `config/session.php`, `AuthController` |
| Kontrol akses | Middleware `pemilik` dan `aktif`, data warung dibatasi per peran | `routes/web.php`, middleware |
| Harga tidak dipercaya dari browser | Harga, total, dan kembalian dihitung server dari database | `KasirController::proses` |
| Rate limiting | Login: 5 percobaan gagal per menit per username dan IP. Pendaftaran: 5 per menit | `AuthController`, `routes/web.php` |
| Aturan password | 8 sampai 72 karakter, huruf dan angka, bukan username, bukan kata umum | `app/Rules/PasswordKuat.php` |
| Akun tidak aktif | Dicek di setiap permintaan, termasuk cookie "Ingat saya" | `EnsureUserAktif` |
| Header keamanan | CSP, X-Frame-Options, nosniff, Referrer-Policy, Permissions-Policy, HSTS | `SecurityHeaders` |
| Rahasia | Hanya di environment variable, `.env` di `.gitignore`, password seeder tidak ada di kode | `.env.example`, `DatabaseSeeder` |
| Pesan error | Detail error tidak ditampilkan ke pengguna, hanya ke log server | `KasirController`, `APP_DEBUG=false` |

## Penggunaan AI agent

Alat: **Claude (Anthropic)**, dipakai lewat percakapan (claude.ai).

| Bagian pekerjaan | Bantuan AI |
|---|---|
| Audit keamanan kode awal | Membaca kode, menyusun daftar temuan |
| Perbaikan keamanan (6 langkah) | Menulis perubahan kode, skrip uji otomatis, dan bukti sebelum dan sesudah |
| Skema database dan data awal | Menulis `dbwarungu.sql` dan seeder |
| Pemeriksaan tampilan | Audit kontras otomatis dan pemeriksaan pola desain. Desain visualnya dibuat sendiri oleh mahasiswa |
| Konfigurasi deploy | Dockerfile dan konfigurasi Vercel mengikuti panduan resmi |

Contoh prompt utama:
1. "bertindahlah seperti web developer senior, detail tugas nya seperti di bawah ..." (lampiran: soal tugas)
2. "coba pahami [proyek Laravel] ... untuk keamanannya lebih baik sedikit saja"
3. "untuk langkah selanjutnya harus mikir dulu sebelum ngoding, sesimple mungkin, ubah yang diminta saja, tentukan tanda beres lalu kerjakan sampai lolos tes"

Cara meninjau dan memverifikasi: `[ISI: jelaskan dengan jujur bagian mana yang kamu baca, jalankan, dan uji sendiri secara manual]`

## Hasil self-testing keamanan

Uji otomatis dijalankan dengan permintaan HTTP sungguhan dan hasilnya dicek langsung di database. Keluarannya ada di `docs/bukti-pengujian/`.

| Kelompok uji | Skenario | Sebelum | Sesudah |
|---|---|---|---|
| Harga dan transaksi (harga palsu, stok palsu, barang warung lain, input tidak valid) | 8 | 5 gagal | semua lolos |
| Hak akses barang (pegawai melihat, mengubah, menghapus) | 11 | 5 gagal | semua lolos |
| Login dan sesi (rate limit, logout, "Ingat saya") | 9 | 4 gagal | semua lolos |
| Konfigurasi produksi dan pesan error | 8 | 4 gagal | semua lolos |
| Pendaftaran, persetujuan, nonaktif, aturan password | 19 | 11 gagal* | semua lolos |
| Header keamanan dan proxy HTTPS | 9 | — | semua lolos |

\* Sebagian skenario pada versi lama lolos hanya karena fiturnya belum ada. Rinciannya di berkas bukti.

Verifikasi manual dan alat tambahan oleh mahasiswa: `[ISI: hasil securityheaders.com, SSL Labs, OWASP ZAP, composer audit, pemindaian rahasia di riwayat git]`
