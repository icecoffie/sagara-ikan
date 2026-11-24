# Deployment — cPanel Shared Hosting

Panduan langkah demi langkah men-deploy **Sagara Resto** ke cPanel. Placeholder yang Anda isi sendiri: **`{{LIVE_URL}}`** dan **nama subdomain**.

Rekomendasi: letakkan folder `app/` **di luar** webroot agar kode aplikasi tidak bisa diakses langsung. cPanel mengizinkan docroot subdomain diarahkan ke subfolder.

---

## 1. Buat Subdomain

1. cPanel → **Domains / Subdomains**.
2. Buat subdomain, mis. `resto` → `{{LIVE_URL}}` = `https://resto.domainanda.com`.
3. Saat membuat, atur **Document Root** ke folder khusus, mis. `sagara/public_html`.
   - Dengan begitu struktur di server menjadi:
     ```
     /home/USER/sagara/           <- hasil upload repo (app/, database/, public_html/)
     /home/USER/sagara/public_html   <- docroot subdomain
     ```

## 2. Upload File

**Opsi A — ZIP (tercepat):**
1. Buat ZIP dari isi proyek (sertakan `app/`, `public_html/`, `database/`, `README.md`, dll — **jangan** sertakan `app/config/config.php` asli maupun `vendor/`).
2. cPanel → **File Manager** → masuk ke `/home/USER/sagara/` → **Upload** ZIP → **Extract**.

**Opsi B — FTP:** upload seluruh folder proyek ke `/home/USER/sagara/` via FileZilla.

> Jika hosting Anda tidak mengizinkan docroot ke subfolder, upload **isi** `public_html/` ke `public_html` bawaan, dan folder `app/` + `database/` ke dalamnya juga. Aplikasi tetap jalan (layout flattened) dan `.htaccess` memblokir akses langsung ke `app/`, `database/`, dan `*.sql`.

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

1. File Manager → `public_html/assets/uploads/`.
2. Set permission folder ke **0755** (atau 0775 jika perlu). File `.htaccess` di dalamnya sudah mencegah eksekusi script.

## 7. Verifikasi (Checklist)

- [ ] Buka `{{LIVE_URL}}` → landing publik tampil, section menu berisi data.
- [ ] Buka `{{LIVE_URL}}/index.php/login`.
- [ ] Login **admin@sagararesto.test / `Admin#2026`** → dashboard + grafik tampil.
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
3. **App**: deploy repo. Jalankan PHP built-in server mengarah ke `public_html`:
   ```bash
   php -S 0.0.0.0:$PORT -t public_html
   ```
   (atau gunakan image PHP Apache dengan docroot `public_html`).
4. Set kredensial DB lewat **environment**; sesuaikan `app/config/config.php` membaca `getenv()` bila ingin (opsional), atau buat `config.php` saat build.
5. `base_url` biarkan kosong (auto-detect). Buka URL yang diberikan platform = `{{LIVE_URL}}`.

> Jangan commit kredensial asli. Gunakan environment variables / secret manager platform.
