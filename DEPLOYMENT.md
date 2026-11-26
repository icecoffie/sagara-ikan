# Deployment — cPanel Shared Hosting

Panduan langkah demi langkah men-deploy **Sagara Resto** ke cPanel. Target: **`https://sagara.msync.my.id`** (subdomain `sagara` pada `msync.my.id`).

Rekomendasi: letakkan folder `app/` **di luar** webroot agar kode aplikasi tidak bisa diakses langsung. cPanel mengizinkan docroot subdomain diarahkan ke subfolder.

---

## 1. Buat Subdomain

1. cPanel → **Domains / Subdomains**.
2. Buat subdomain `sagara` pada domain `msync.my.id` → `https://sagara.msync.my.id`.
3. Biarkan **Document Root** subdomain pada folder bawaannya (mis. `public_html/sagara.msync.my.id`). Seluruh isi aplikasi diletakkan **langsung** di folder itu.
   - Semua file aplikasi (satu folder) diletakkan langsung di docroot, mis.:
     ```
     /home/USER/public_html/sagara.msync.my.id/   <- docroot subdomain
       index.php  .htaccess  assets/  app/  database/  docs/  style.css ...
     ```

## 2. Upload File

**Opsi A — ZIP (tercepat):**
1. Buat ZIP dari **isi** proyek (index.php, .htaccess, app/, assets/, database/, dll — **jangan** sertakan `app/config/config.php` asli maupun `vendor/`).
2. cPanel → **File Manager** → masuk ke folder docroot subdomain → **Upload** ZIP → **Extract**. `index.php` harus berada tepat di root folder itu.

**Opsi B — Git:** cPanel → **Git Version Control** → clone `https://github.com/icecoffie/sagara-ikan.git`, lalu arahkan Document Root subdomain ke folder hasil clone (karena `index.php` ada di root repo).

> Semua dalam **satu folder** (docroot = root repo). `.htaccess` memblokir akses langsung ke `app/`, `database/`, dan `*.sql`; file config juga dijaga konstanta `SAGARA_APP`.

## 3. Buat Database MySQL

1. cPanel → **MySQL® Databases**.
2. **Create New Database**: mis. `USER_sagara`.
3. **Add New User** + password kuat, lalu **Add User To Database** dengan **ALL PRIVILEGES**.
4. Catat: nama DB (`USER_sagara`), user (`USER_sagaradbuser`), password.

## 4. Import Skema + Seed

1. cPanel → **phpMyAdmin** → pilih database `USER_sagara`.
2. Tab **Import** → pilih `database/schema.sql` → **Go**.
3. Ulangi Import untuk `database/seed.sql`.
4. Pastikan muncul tabel: `roles, permissions, role_permissions, users, categories, menu_items, dining_tables, reservations, orders, order_items, activity_logs`.

## 5. Konfigurasi Aplikasi

1. File Manager → `app/config/` → salin `config.sample.php` menjadi `config.php`.
2. Edit `config.php`:
   ```php
   'db' => [
       'host' => 'localhost',
       'name' => 'USER_sagara',
       'user' => 'USER_sagaradbuser',
       'pass' => 'PASSWORD_DB_ANDA',
       'charset' => 'utf8mb4',
   ],
   'base_url' => '',          // biarkan kosong untuk auto-detect
   'debug'    => false,       // WAJIB false di produksi
   ```

## 6. Izin Folder Upload

1. File Manager → `assets/uploads/`.
2. Set permission folder ke **0755** (atau 0775 jika perlu). File `.htaccess` di dalamnya sudah mencegah eksekusi script.

## 7. Verifikasi (Checklist)

- [ ] Buka `https://sagara.msync.my.id` → landing publik tampil, section menu berisi data.
- [ ] Buka `https://sagara.msync.my.id/index.php/login`.
- [ ] Login **admin@sagararesto.app / `Admin#2026`** → dashboard + grafik tampil.
- [ ] Login **manajer** → bisa CRUD menu/kategori, tidak ada menu Pengguna/Role.
- [ ] Login **kasir** → bisa buat & bayar pesanan, cetak struk.
- [ ] Login **pelayan** → bisa ambil pesanan & reservasi, tidak bisa bayar.
- [ ] Akses URL terlarang (mis. pelayan ke `/index.php/user`) → **403**.
- [ ] Upload gambar menu berhasil tampil.
- [ ] Export CSV laporan terunduh.
- [ ] **Ganti semua password akun demo** setelah verifikasi.

## 8. Hardening Produksi

- `debug => false` di `config.php`.
- Pastikan `app/config/config.php` **tidak** ter-commit ke git publik.
- Gunakan HTTPS (AutoSSL / Let's Encrypt di cPanel).
- Ganti password demo & buat akun admin baru.

---

## Alternatif: Railway / Render (singkat)

Untuk PaaS (bukan cPanel):

1. **Database**: buat instance MySQL (Railway MySQL plugin / Render + layanan MySQL eksternal seperti PlanetScale/Aiven). Catat kredensial.
2. **Import** `schema.sql` lalu `seed.sql` ke DB tersebut (CLI `mysql` atau GUI).
3. **App**: deploy repo. Jalankan PHP built-in server dari root repo:
   ```bash
   php -S 0.0.0.0:$PORT
   ```
   (atau gunakan image PHP Apache dengan docroot = root repo tempat `index.php` berada).
4. Set kredensial DB lewat **environment**; sesuaikan `app/config/config.php` membaca `getenv()` bila ingin (opsional), atau buat `config.php` saat build.
5. `base_url` biarkan kosong (auto-detect). Buka URL yang diberikan platform = `https://sagara.msync.my.id`.

> Jangan commit kredensial asli. Gunakan environment variables / secret manager platform.

---

## Cloudflare (DNS untuk msync.my.id)

> Penting: aplikasi ini **PHP + MySQL**, jadi **tidak bisa di-host di Cloudflare Pages/Workers** (hanya static/JS). Cloudflare di sini berperan sebagai **DNS + proxy + SSL**; aplikasinya tetap berjalan di server PHP (cPanel/VPS).

1. Siapkan hosting PHP untuk aplikasi (cPanel shared hosting sesuai panduan di atas, atau VPS dengan PHP 8 + MySQL). Catat **alamat IP** server tersebut.
2. Masuk **Cloudflare → pilih domain `msync.my.id` → DNS → Records → Add record**:
   - **Type**: `A` — **Name**: `sagara` — **IPv4**: `<IP server PHP kamu>` — **Proxy**: ON (awan oранye).
   - (Jika host memberi hostname, pakai **CNAME** `sagara` → `host-kamu.tld` dengan Proxy ON.)
3. **Cloudflare → SSL/TLS**: set mode **Full** (atau **Full (strict)** jika server punya sertifikat valid). Aktifkan **Always Use HTTPS**.
4. Tunggu propagasi DNS, lalu buka **https://sagara.msync.my.id**.
5. Di sisi server PHP, pastikan dokumen root subdomain mengarah ke folder tempat `index.php` berada (lihat langkah cPanel di atas).

Jika kamu belum punya hosting PHP sama sekali, pilihan termurah: cPanel shared hosting yang mendukung PHP 8 + MySQL, lalu arahkan DNS-nya lewat Cloudflare seperti di atas.
