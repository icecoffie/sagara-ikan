# Dokumentasi Sagara Resto

Folder ini berisi generator dokumentasi Word (.docx) lengkap beserta screenshot otomatis.

## Hasil
- `Dokumentasi-Sagara-Resto.docx` — dokumen utama (cover, deskripsi & fitur, matriks RBAC, struktur database + ERD, screenshot, akun demo, info hosting).
- `screenshots/` — screenshot aplikasi (landing, login, dashboard tiap role, CRUD, RBAC, struk, laporan, log).

## Cara generate
Butuh Python 3.8+.

```bash
# 1. (opsional) buat virtualenv
python3 -m venv docs/.venv && source docs/.venv/bin/activate

# 2. install dependency
pip install python-docx playwright
python -m playwright install chromium

# 3. pastikan app berjalan lokal (terminal lain)
php -S localhost:8000 -t public_html

# 4. generate screenshot + docx
python docs/generate_docs.py --base-url http://localhost:8000
```

Jika `playwright`/Chromium belum terpasang atau jaringan OFF, skrip tetap membuat `.docx`
tanpa screenshot dan menuliskan instruksi melengkapi di bagian terkait.
Jika `python-docx` belum ada, skrip mencetak perintah instalasinya dan berhenti dengan rapi.
