# Sagara Ikan — Sistem Informasi Restoran

Aplikasi restoran seafood **Sagara Ikan**: landing page modern + **pemesanan online tanpa login** untuk pelanggan, dan **panel manajemen (RBAC)** untuk staf. Dibangun dengan **PHP native (PHP 8.x) + MySQL (PDO)** tanpa framework, siap di-deploy ke **cPanel shared hosting**.

- **Target deploy:** `https://sagara.msync.web.id`
- **Stack:** PHP 8 (zero-dependency untuk fitur inti), MySQL/MariaDB, Chart.js & Ionicons via CDN.

---

## Fitur

**Sisi Pelanggan (publik, tanpa login)**
- Landing page modern (tema gradient biru laut) dengan menu unggulan dari database.
- Halaman menu lengkap `/pesanan` + halaman **detail item** `/pesanan/item/{id}`.
- **Keranjang ala marketplace**: stepper − / + per item, hapus otomatis saat di bawah 1, total terhitung otomatis (AJAX, tanpa reload).
- **Checkout tanpa akun**: makan di tempat (pilih meja) atau bawa pulang → dapat nomor pesanan + **lacak status**.
- Pesanan pelanggan otomatis masuk ke panel staf (ditandai "Online").

**Sisi Staf (login)**
- **RBAC granular** (4 role, 31 permission `resource.action`) yang **dikelola lewat UI** — centang izin per role, langsung berlaku.
- **5 CRUD penuh**: Kategori, Menu (upload gambar), Meja, Reservasi, Pesanan (+ item).
- **Point of sale**: buat/ubah pesanan, total otomatis, bayar, batalkan, **cetak struk**.
- **Dashboard** per role: kartu statistik + grafik omzet 7 hari (Chart.js).
- **Laporan penjualan**: filter tanggal, ringkasan, menu terlaris, **export CSV** (PDF opsional via dompdf).
- **Activity log** (khusus Admin), search + filter + pagination di semua tabel.

---

## Hirarki Pengguna

| Peran | Akses |
|------|-------|
| **Pembeli (tanpa login)** | Lihat menu, keranjang, checkout (dine-in/takeaway), lacak pesanan |
| **Admin** | Semua fitur + kelola Users, Roles, dan assign Permission via UI |
| **Manajer** | CRUD Kategori/Menu/Meja/Reservasi, lihat Pesanan & laporan/dashboard |
| **Kasir** | Kelola & bayar Pesanan (termasuk pesanan online), cetak struk, lihat Menu & Meja |
| **Pelayan** | Buat/ubah Pesanan, kelola Reservasi, ubah status Meja; tanpa bayar/hapus transaksi |

> **Akun demo** (untuk login staf) sengaja **tidak ditampilkan** di halaman login. Kredensialnya ada di bagian [Akun Demo](#akun-demo) di bawah.

---

## Struktur Folder

```
public_html/            # webroot (docroot subdomain diarahkan ke sini)
  index.php             # front controller
  .htaccess             # routing + proteksi + hardening
  assets/               # css (modern.css, app.css), js, gambar, uploads/
app/                    # kode aplikasi (di luar webroot = lebih aman)
  bootstrap.php
  config/               # config.php (git-ignored), config.sample.php
  core/                 # Database, Auth, Csrf, Router, Controller, Model, View, helpers
  controllers/  models/  views/
database/
  schema.sql            # struktur tabel
  seed.sql              # data demo + akun
docs/                   # dokumentasi (.docx) + generator + screenshots
```

> Aplikasi tetap jalan jika seluruh isi diupload ke dalam `public_html` (layout flattened) — `index.php` mencari `app/` di dua lokasi, dan `.htaccess` memblokir akses langsung ke `app/`, `database/`, dan file sensitif.

---

## Menjalankan Secara Lokal

Butuh PHP 8.x (ekstensi `pdo_mysql`) dan MySQL/MariaDB.

```bash
# 1. Buat database + import skema & seed
mysql -u root -e "CREATE DATABASE sagara_resto CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root sagara_resto < database/schema.sql
mysql -u root sagara_resto < database/seed.sql

# 2. Konfigurasi
cp app/config/config.sample.php app/config/config.php
#   edit app/config/config.php -> sesuaikan host/name/user/pass DB

# 3. Jalankan
php -S localhost:8000 -t public_html
```

Buka **http://localhost:8000** (publik) dan **http://localhost:8000/index.php/login** (staf).

---

## Akun Demo

Login staf: **http://localhost:8000/index.php/login** (atau `https://sagara.msync.web.id/index.php/login`).

| Role | Email | Password |
|------|-------|----------|
| Admin | `admin@sagararesto.app` | `Admin#2026` |
| Manajer | `manajer@sagararesto.app` | `Manajer#2026` |
| Kasir | `kasir@sagararesto.app` | `Kasir#2026` |
| Pelayan | `pelayan@sagararesto.app` | `Pelayan#2026` |

> Ganti password ini setelah deploy ke produksi.

---

## Keamanan

- Query 100% **PDO prepared statements**.
- **CSRF token** divalidasi pada setiap POST (gagal → 419).
- Password di-hash (`PASSWORD_DEFAULT` / bcrypt).
- Output di-escape (`htmlspecialchars`); guard akses per halaman (tanpa izin → **403**).
- Folder `app/`, `config/`, `database/` diblokir dari web; folder upload tidak boleh mengeksekusi script.

---

## Deploy

Panduan lengkap cPanel (subdomain `sagara.msync.web.id`, upload, import DB, konfigurasi, verifikasi) ada di **[DEPLOYMENT.md](DEPLOYMENT.md)**.

Export PDF laporan bersifat opsional: `composer require dompdf/dompdf`. Tanpa Composer, CSV & cetak tetap berfungsi penuh.

## Dokumentasi

Dokumentasi lengkap (deskripsi, matriks RBAC, struktur DB/ERD, screenshot, akun demo, hosting) ada di `docs/Dokumentasi-Sagara-Resto.docx` — lihat `docs/README.md` untuk cara generate.
