# Sagara Resto — Sistem Informasi Restoran

Aplikasi manajemen restoran seafood **Sagara Ikan**, dibangun dengan **PHP native (PHP 8.x) + MySQL (PDO)** tanpa framework, siap di-deploy ke **cPanel shared hosting**. Landing page publik lama dipertahankan sebagai halaman depan dengan menu yang diambil langsung dari database.

**Live URL:** `{{LIVE_URL}}` _(isi setelah deploy)_

---

## Fitur

- **Landing page publik** (desain editorial, tema biru laut) dengan section menu dari database.
- **Autentikasi & sesi** dengan `password_hash`/`password_verify`, CSRF token di semua form POST, escaping output.
- **RBAC granular** (4 role, 31 permission `resource.action`) yang **dikelola lewat UI** — centang izin per role, efek langsung.
- **5 modul CRUD penuh**: Kategori, Menu (dengan upload gambar), Meja, Reservasi, Pesanan (+ item).
- **Point of Sale**: buat pesanan, tambah/hapus item (total otomatis), bayar, batalkan, **cetak struk** (print browser).
- **Dashboard** per role: kartu statistik + grafik omzet 7 hari (Chart.js).
- **Laporan penjualan**: filter tanggal, ringkasan, menu terlaris, **export CSV** (native) + PDF opsional (dompdf) + cetak.
- **Activity log** (khusus Admin): mencatat login, create/update/delete, pembayaran, export.
- Search + filter + pagination di semua tabel CRUD.

## Hirarki Pengguna (seperti web app restoran)

- **Pembeli / Pelanggan (tanpa login):** sisi publik. Lihat menu → masukkan ke **keranjang** → **checkout** (makan di tempat dengan nomor meja, atau bawa pulang) → pesan **tanpa akun**. Dapat halaman **konfirmasi** (nomor pesanan) dan **lacak status**. Pesanan otomatis masuk ke panel staf.
- **Staf (login):** mengelola operasional lewat panel. Pesanan pelanggan muncul dengan label **"Online"** dan siap diproses/dibayar kasir.

## Role Staf & Hak Akses (ringkas)

| Role | Akses utama |
|------|-------------|
| **Admin** | Semua fitur + kelola Users, Roles, dan assign Permission via UI |
| **Manajer** | CRUD Kategori/Menu/Meja/Reservasi, lihat Pesanan & laporan/dashboard; tanpa kelola user/role |
| **Kasir** | Kelola & bayar Pesanan (termasuk pesanan online pelanggan), cetak struk, lihat Menu & Meja |
| **Pelayan** | Buat/ubah Pesanan, kelola Reservasi, ubah status Meja; tanpa bayar/hapus transaksi |

## Akun Demo (dari `seed.sql`)

| Email | Password | Role |
|-------|----------|------|
| admin@sagararesto.test | `Admin#2026` | Admin |
| manajer@sagararesto.test | `Manajer#2026` | Manajer |
| kasir@sagararesto.test | `Kasir#2026` | Kasir |
| pelayan@sagararesto.test | `Pelayan#2026` | Pelayan |

---

## Struktur Folder

```
public_html/          # webroot (docroot cPanel diarahkan ke sini)
  index.php           # front controller
  .htaccess           # routing + proteksi + hardening
  assets/             # css, gambar, uploads/
app/                  # kode aplikasi (di luar webroot = lebih aman)
  bootstrap.php
  config/             # config.php (git-ignored), config.sample.php
  core/               # Database, Auth, Csrf, Router, Controller, Model, View, helpers
  controllers/
  models/
  views/              # layouts, auth, dashboard, menu, category, table, ...
database/
  schema.sql          # struktur tabel
  seed.sql            # data demo + akun
docs/                 # dokumentasi (.docx) + generator + screenshots
```

> Aplikasi juga tetap berjalan jika **seluruh isi** diupload ke dalam `public_html` (layout flattened) — `index.php` mencari `app/` di dua lokasi, dan `.htaccess` memblokir akses langsung ke `app/`, `database/`, dan file sensitif.

---

## Menjalankan Secara Lokal (dari nol)

Butuh: PHP 8.x (ekstensi `pdo_mysql`), MySQL/MariaDB.

1. **Buat database & import skema + seed**

   ```bash
   mysql -u root -e "CREATE DATABASE sagara_resto CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   mysql -u root sagara_resto < database/schema.sql
   mysql -u root sagara_resto < database/seed.sql
   ```

   Atau via **phpMyAdmin**: buat DB `sagara_resto`, pilih, lalu Import `schema.sql` kemudian `seed.sql`.

2. **Konfigurasi**

   ```bash
   cp app/config/config.sample.php app/config/config.php
   # edit app/config/config.php -> sesuaikan host/name/user/pass DB
   ```

3. **Jalankan server**

   ```bash
   php -S localhost:8000 -t public_html
   ```

4. Buka **http://localhost:8000** (landing publik) dan **http://localhost:8000/index.php/login** untuk masuk. Coba login dengan tiap akun demo untuk melihat perbedaan hak akses.

---

## PDF Laporan (opsional)

Export CSV & cetak selalu tersedia tanpa dependency. Untuk export **PDF**:

```bash
composer require dompdf/dompdf
```

Jika `vendor/` tidak ada, tombol PDF akan memberi pesan dan aplikasi tetap berfungsi penuh (zero-dependency untuk fitur inti).

---

## Keamanan

- Query 100% **PDO prepared statements**.
- **CSRF token** divalidasi pada setiap POST (gagal → 419).
- Password di-hash (`PASSWORD_DEFAULT` / bcrypt).
- Semua output di-escape (`htmlspecialchars`).
- Guard akses per halaman: tanpa izin → **403**; menu & tombol aksi otomatis disembunyikan.
- Folder `app/`, `config/`, `database/` diblokir dari akses web; folder upload tidak boleh mengeksekusi script.

## Deploy

Lihat **[DEPLOYMENT.md](DEPLOYMENT.md)** untuk langkah lengkap cPanel (subdomain, upload, database, konfigurasi, verifikasi) + catatan alternatif (Railway/Render).

## Dokumentasi

Dokumentasi lengkap (deskripsi, matriks RBAC, struktur DB/ERD, screenshot, akun demo, hosting) ada di `docs/Dokumentasi-Sagara-Resto.docx`, dihasilkan oleh `docs/generate_docs.py`. Lihat `docs/README.md`.
