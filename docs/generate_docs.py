#!/usr/bin/env python3
import argparse
import datetime
import os
import re
import sys

HERE = os.path.dirname(os.path.abspath(__file__))
ROOT = os.path.dirname(HERE)
SCHEMA = os.path.join(ROOT, "database", "schema.sql")
SEED = os.path.join(ROOT, "database", "seed.sql")
SHOTS = os.path.join(HERE, "screenshots")
OUTPUT = os.path.join(HERE, "Dokumentasi-Sagara-Resto.docx")

DEMO_ACCOUNTS = [
    ("Admin", "admin@sagararesto.app", "Admin#2026"),
    ("Manajer", "manajer@sagararesto.app", "Manajer#2026"),
    ("Kasir", "kasir@sagararesto.app", "Kasir#2026"),
    ("Pelayan", "pelayan@sagararesto.app", "Pelayan#2026"),
]

ROLE_SUMMARY = {
    "admin": "Akses penuh ke seluruh fitur, termasuk mengelola Pengguna, Role, dan menetapkan Permission ke Role melalui halaman UI.",
    "manajer": "CRUD penuh Kategori, Menu, Meja, dan Reservasi; melihat Pesanan serta laporan/dashboard. Tidak dapat mengelola user/role.",
    "kasir": "Mengelola Pesanan (buat, tambah item, bayar, cetak struk) serta melihat Menu & Meja. Tidak dapat menghapus menu/kategori atau mengelola user.",
    "pelayan": "Membuat & memperbarui Pesanan, mengelola Reservasi, dan mengubah status Meja. Tidak dapat membayar/menghapus transaksi atau mengelola menu/user.",
}

FEATURES = [
    ("Landing page publik", "Halaman depan restoran dengan desain editorial bertema biru laut; section menu diambil langsung dari database."),
    ("Pemesanan pelanggan tanpa login", "Pembeli (guest) dapat menambah menu ke keranjang, checkout (makan di tempat/bawa pulang), dan memesan tanpa akun. Pesanan otomatis masuk ke panel kasir, lengkap dengan halaman konfirmasi & lacak status via nomor pesanan."),
    ("Autentikasi & keamanan", "Login berbasis sesi, password_hash/verify, CSRF token di semua form POST, escaping output, dan guard akses per halaman."),
    ("RBAC granular via UI", "4 role dan 31 permission format resource.action. Admin dapat mencentang izin per role lewat halaman; perubahan langsung berlaku."),
    ("CRUD Kategori", "Kelola kategori menu: nama, slug otomatis, deskripsi, status aktif."),
    ("CRUD Menu", "Kelola item menu lengkap dengan harga, kategori (FK), deskripsi, upload gambar, dan ketersediaan."),
    ("CRUD Meja", "Kelola meja: nomor, kapasitas, lokasi (indoor/outdoor), dan status (available/occupied/reserved)."),
    ("CRUD Reservasi", "Kelola reservasi tamu: nama, telepon, meja, waktu, jumlah tamu, status, catatan."),
    ("Pesanan & Point of Sale", "Buat pesanan, tambah/hapus item dengan total dihitung otomatis, bayar, batalkan, dan hapus."),
    ("Bonus — Dashboard", "Kartu statistik menyesuaikan izin + grafik omzet 7 hari terakhir (Chart.js)."),
    ("Bonus — Cetak struk", "Halaman struk printable (window.print) berisi detail pesanan, item, total, kasir, dan waktu. Zero-dependency."),
    ("Bonus — Export laporan", "Laporan penjualan dengan filter tanggal, export CSV native (selalu jalan), PDF opsional (dompdf), dan cetak."),
    ("Bonus — Search/Filter/Pagination", "Tersedia di seluruh tabel CRUD."),
    ("Bonus — Activity log", "Mencatat login, create/update/delete penting, pembayaran, dan export. Halaman log khusus Admin."),
]

SCREENSHOT_PLAN = [
    ("01-landing.png", "Landing page publik (menu dari database)", None, "/index.php/"),
    ("02-login.png", "Halaman login", None, "/index.php/login"),
    ("03-dashboard-admin.png", "Dashboard — Admin (statistik + grafik omzet)", "admin", "/index.php/dashboard"),
    ("04-menu-list.png", "Daftar Menu (search, filter, pagination)", "admin", "/index.php/menus"),
    ("05-menu-form.png", "Form Tambah Menu (upload gambar)", "admin", "/index.php/menus/create"),
    ("06-category-list.png", "Daftar Kategori", "admin", "/index.php/category"),
    ("07-table-list.png", "Daftar Meja", "admin", "/index.php/table"),
    ("08-reservation-list.png", "Daftar Reservasi", "admin", "/index.php/reservation"),
    ("09-order-list.png", "Daftar Pesanan", "admin", "/index.php/order"),
    ("10-order-show.png", "Detail Pesanan (POS: tambah item, bayar)", "admin", "/index.php/order/show/1"),
    ("11-receipt.png", "Struk / Invoice (printable)", "admin", "/index.php/order/receipt/1"),
    ("12-role-list.png", "Daftar Role & Izin (RBAC)", "admin", "/index.php/role"),
    ("13-role-edit.png", "Kelola Izin per Role (centang permission)", "admin", "/index.php/role/edit/2"),
    ("14-user-list.png", "Manajemen Pengguna", "admin", "/index.php/user"),
    ("15-report.png", "Laporan Penjualan + export CSV", "admin", "/index.php/report"),
    ("16-log.png", "Activity Log", "admin", "/index.php/log"),
    ("17-dashboard-manajer.png", "Dashboard — Manajer", "manajer", "/index.php/dashboard"),
    ("18-dashboard-kasir.png", "Dashboard — Kasir", "kasir", "/index.php/dashboard"),
    ("19-dashboard-pelayan.png", "Dashboard — Pelayan", "pelayan", "/index.php/dashboard"),
    ("20-cart.png", "Keranjang pelanggan (tanpa login)", "shop", "/index.php/keranjang"),
    ("21-checkout.png", "Checkout pelanggan (dine-in / takeaway)", "shop", "/index.php/keranjang/checkout"),
    ("22-confirm.png", "Konfirmasi pesanan pelanggan", "shop", "/index.php/keranjang/confirm/ORD-0011"),
    ("23-track.png", "Lacak status pesanan", "shop", "/index.php/pantau?order_number=ORD-0011"),
]


def parse_schema(path):
    if not os.path.isfile(path):
        return []
    sql = open(path, encoding="utf-8").read()
    tables = []
    for m in re.finditer(r"CREATE TABLE `(\w+)`\s*\((.*?)\)\s*ENGINE", sql, re.S):
        name, body = m.group(1), m.group(2)
        cols, fks = [], []
        for raw in body.splitlines():
            line = raw.strip().rstrip(",")
            if not line:
                continue
            if line.startswith("`"):
                cmatch = re.match(r"`(\w+)`\s+(.+)$", line)
                if cmatch:
                    col = cmatch.group(1)
                    rest = cmatch.group(2)
                    typ = re.split(r"\s+(NOT NULL|NULL|DEFAULT|AUTO_INCREMENT|PRIMARY)", rest)[0].strip()
                    notes = []
                    if "NOT NULL" in rest:
                        notes.append("NOT NULL")
                    if "AUTO_INCREMENT" in rest:
                        notes.append("AI")
                    dflt = re.search(r"DEFAULT\s+([^,]+)", rest)
                    if dflt:
                        notes.append("default " + dflt.group(1).strip())
                    cols.append((col, typ, ", ".join(notes)))
            fk = re.search(r"FOREIGN KEY \(`(\w+)`\)\s+REFERENCES `(\w+)` \(`(\w+)`\)(.*)", line)
            if fk:
                fks.append((fk.group(1), fk.group(2), fk.group(3), fk.group(4).strip()))
        tables.append({"name": name, "cols": cols, "fks": fks})
    return tables


def parse_rbac(path):
    if not os.path.isfile(path):
        return [], [], {}
    sql = open(path, encoding="utf-8").read()

    def region(start_marker, end_marker):
        s = sql.find(start_marker)
        if s < 0:
            return ""
        e = sql.find(end_marker, s + len(start_marker))
        return sql[s:e if e > 0 else len(sql)]

    roles = []
    roles_region = region("INSERT INTO `roles`", "INSERT INTO `permissions`")
    for r in re.finditer(r"\(\s*\d+,\s*'([^']+)',\s*'([^']+)',\s*'([^']*)'\s*\)", roles_region):
        roles.append({"name": r.group(1), "slug": r.group(2), "desc": r.group(3)})

    perms = []
    perms_region = region("INSERT INTO `permissions`", "INSERT INTO `role_permissions`")
    for r in re.finditer(r"\('([^']+)',\s*'([^']+)',\s*'([^']+)',\s*'([^']+)'\)", perms_region):
        perms.append({"name": r.group(1), "slug": r.group(2), "resource": r.group(3), "action": r.group(4)})
    role_perms = {}
    for rr in roles:
        role_perms[rr["slug"]] = set()
    all_slugs = {p["slug"] for p in perms}
    for blk in re.finditer(r"SELECT\s+(\d+),\s*p\.id\s+FROM permissions p(.*?);", sql, re.S):
        role_id = int(blk.group(1))
        slug = roles[role_id - 1]["slug"] if role_id - 1 < len(roles) else str(role_id)
        tail = blk.group(2)
        in_match = re.search(r"IN\s*\((.*?)\)", tail, re.S)
        if in_match:
            chosen = set(re.findall(r"'([^']+)'", in_match.group(1)))
        else:
            chosen = set(all_slugs)
        role_perms[slug] = chosen
    return roles, perms, role_perms


def take_screenshots(base_url):
    try:
        from playwright.sync_api import sync_playwright
    except Exception:
        return False, "playwright belum terpasang"
    os.makedirs(SHOTS, exist_ok=True)
    taken = []
    try:
        with sync_playwright() as pw:
            browser = pw.chromium.launch()
            sessions = {}

            def ctx_for(role):
                if role is None:
                    return browser.new_context(viewport={"width": 1366, "height": 900})
                if role in sessions:
                    return sessions[role]
                if role == "shop":
                    c = browser.new_context(viewport={"width": 1366, "height": 900})
                    page = c.new_page()
                    page.goto(base_url + "/index.php/", wait_until="networkidle")
                    for idx in (0, 3):
                        btns = page.query_selector_all(".order-form button")
                        if idx < len(btns):
                            btns[idx].click()
                            page.wait_for_load_state("networkidle")
                    page.close()
                    sessions[role] = c
                    return c
                c = browser.new_context(viewport={"width": 1366, "height": 900})
                page = c.new_page()
                page.goto(base_url + "/index.php/login")
                acct = next(a for a in DEMO_ACCOUNTS if a[0].lower() == role)
                page.fill("#email", acct[1])
                page.fill("#password", acct[2])
                page.click("button[type=submit]")
                page.wait_for_load_state("networkidle")
                page.close()
                sessions[role] = c
                return c

            for fname, _cap, role, path in SCREENSHOT_PLAN:
                try:
                    c = ctx_for(role)
                    page = c.new_page()
                    page.goto(base_url + path, wait_until="networkidle")
                    page.add_style_tag(content=".reveal{opacity:1!important;transform:none!important;transition:none!important;}")
                    page.wait_for_timeout(800)
                    page.screenshot(path=os.path.join(SHOTS, fname), full_page=True)
                    page.close()
                    taken.append(fname)
                except Exception as e:
                    print("  ! gagal screenshot %s: %s" % (fname, e))
            browser.close()
    except Exception as e:
        return False, "browser error: %s (jalankan: python -m playwright install chromium)" % e
    return True, taken


def build_docx(tables, roles, perms, role_perms, shots_ok, base_url):
    try:
        from docx import Document
        from docx.shared import Pt, RGBColor, Inches
        from docx.enum.text import WD_ALIGN_PARAGRAPH
    except Exception:
        print("python-docx belum terpasang. Install dulu:")
        print("  pip install python-docx")
        print("Lalu jalankan ulang skrip ini.")
        return False

    NAVY = RGBColor(0x0B, 0x24, 0x47)
    BLUE = RGBColor(0x2A, 0xA7, 0xFF)
    doc = Document()

    def h(text, size=16, color=NAVY, align=None, bold=True, space_before=10):
        p = doc.add_paragraph()
        if align:
            p.alignment = align
        p.paragraph_format.space_before = Pt(space_before)
        r = p.add_run(text)
        r.bold = bold
        r.font.size = Pt(size)
        r.font.color.rgb = color
        return p

    # Cover
    for _ in range(6):
        doc.add_paragraph()
    h("DOKUMENTASI APLIKASI", 20, BLUE, WD_ALIGN_PARAGRAPH.CENTER)
    h("Sagara Resto", 34, NAVY, WD_ALIGN_PARAGRAPH.CENTER)
    h("Sistem Informasi Restoran — PHP Native + MySQL", 14, NAVY, WD_ALIGN_PARAGRAPH.CENTER, bold=False)
    doc.add_paragraph()
    h("Restoran Seafood Sagara Ikan", 13, NAVY, WD_ALIGN_PARAGRAPH.CENTER, bold=False)
    h("Tanggal: " + datetime.date.today().strftime("%d %B %Y"), 12, NAVY, WD_ALIGN_PARAGRAPH.CENTER, bold=False)
    h("Penyusun: Malika Shakila", 12, NAVY, WD_ALIGN_PARAGRAPH.CENTER, bold=False)
    doc.add_page_break()

    # 1. Deskripsi & fitur
    h("1. Deskripsi Aplikasi & Fitur", 18)
    doc.add_paragraph(
        "Sagara Resto adalah sistem informasi restoran berbasis web untuk restoran seafood "
        "Sagara Ikan. Dibangun dengan PHP native (tanpa framework) dan MySQL (PDO), aplikasi "
        "mudah di-deploy ke cPanel shared hosting. Landing page publik lama dipertahankan "
        "dengan section menu yang diambil langsung dari database."
    )
    h("Daftar Fitur (termasuk bonus)", 14)
    for title, desc in FEATURES:
        p = doc.add_paragraph(style="List Bullet")
        r = p.add_run(title + ": ")
        r.bold = True
        p.add_run(desc)

    h("Penjelasan Peran (Role)", 14)
    for rr in roles:
        p = doc.add_paragraph(style="List Bullet")
        r = p.add_run(rr["name"] + ": ")
        r.bold = True
        p.add_run(ROLE_SUMMARY.get(rr["slug"], rr["desc"]))

    # RBAC matrix
    h("Matriks RBAC (Role × Permission)", 14)
    if perms and roles:
        order = ["admin", "manajer", "kasir", "pelayan"]
        role_cols = [s for s in order if s in role_perms] or list(role_perms.keys())
        table = doc.add_table(rows=1, cols=1 + len(role_cols))
        table.style = "Light Grid Accent 1"
        hdr = table.rows[0].cells
        hdr[0].text = "Permission"
        for i, s in enumerate(role_cols):
            hdr[i + 1].text = s.capitalize()
        for pr in perms:
            cells = table.add_row().cells
            cells[0].text = pr["slug"]
            for i, s in enumerate(role_cols):
                has = s == "admin" or pr["slug"] in role_perms.get(s, set())
                cells[i + 1].text = "✓" if has else "–"
        doc.add_paragraph("Catatan: role admin selalu memiliki seluruh permission.").italic = True
    else:
        doc.add_paragraph("(Data permission tidak terbaca dari seed.sql.)")

    doc.add_page_break()

    # 2. Struktur database
    h("2. Struktur Database", 18)
    if tables:
        doc.add_paragraph("Database terdiri dari %d tabel (engine InnoDB, utf8mb4)." % len(tables))
        for t in tables:
            h("Tabel: " + t["name"], 13, BLUE)
            tb = doc.add_table(rows=1, cols=3)
            tb.style = "Light List Accent 1"
            hc = tb.rows[0].cells
            hc[0].text, hc[1].text, hc[2].text = "Kolom", "Tipe", "Keterangan"
            for col, typ, notes in t["cols"]:
                c = tb.add_row().cells
                c[0].text, c[1].text, c[2].text = col, typ, notes
            if t["fks"]:
                for col, rt, rc, extra in t["fks"]:
                    doc.add_paragraph("FK: %s → %s(%s) %s" % (col, rt, rc, extra), style="List Bullet")
        # ERD
        h("Entity Relationship Diagram (ERD)", 14)
        doc.add_paragraph(
            "Relasi antar tabel (notasi Mermaid; dapat dirender di mermaid.live atau mermaid-cli):"
        )
        lines = ["erDiagram"]
        for t in tables:
            for col, rt, rc, _ in t["fks"]:
                lines.append("  %s ||--o{ %s : %s" % (rt, t["name"], col))
        code = doc.add_paragraph("\n".join(lines))
        code.runs[0].font.name = "Courier New"
        code.runs[0].font.size = Pt(9)
    else:
        doc.add_paragraph("(schema.sql tidak ditemukan.)")

    doc.add_page_break()

    # 3. Screenshots
    h("3. Tangkapan Layar Aplikasi", 18)
    from docx.shared import Inches
    any_shot = False
    for fname, cap, _role, _path in SCREENSHOT_PLAN:
        fpath = os.path.join(SHOTS, fname)
        if os.path.isfile(fpath):
            any_shot = True
            h(cap, 12, BLUE, bold=True, space_before=8)
            try:
                doc.add_picture(fpath, width=Inches(6.2))
            except Exception as e:
                doc.add_paragraph("(gagal menyisipkan %s: %s)" % (fname, e))
    if not any_shot:
        doc.add_paragraph(
            "Screenshot belum tersedia. Untuk melengkapi bagian ini, jalankan aplikasi lokal lalu "
            "regenerate dokumen:"
        )
        for cmd in [
            "pip install python-docx playwright",
            "python -m playwright install chromium",
            "php -S localhost:8000   # terminal terpisah",
            "python docs/generate_docs.py --base-url http://localhost:8000",
        ]:
            doc.add_paragraph(cmd, style="List Bullet")
        doc.add_paragraph("Screenshot yang akan dihasilkan:")
        for fname, cap, _r, _p in SCREENSHOT_PLAN:
            doc.add_paragraph("%s — %s" % (fname, cap), style="List Bullet")

    doc.add_page_break()

    # 4. Akun demo
    h("4. Akun Demo", 18)
    tb = doc.add_table(rows=1, cols=3)
    tb.style = "Light Grid Accent 1"
    hc = tb.rows[0].cells
    hc[0].text, hc[1].text, hc[2].text = "Email", "Password", "Role"
    for role, email, pw in DEMO_ACCOUNTS:
        c = tb.add_row().cells
        c[0].text, c[1].text, c[2].text = email, pw, role
    doc.add_paragraph("Disarankan mengganti password akun demo setelah deploy.").italic = True

    # 5. Hosting
    h("5. Informasi Hosting", 18)
    doc.add_paragraph("URL Live (isi setelah deploy): {{LIVE_URL}}")
    doc.add_paragraph("Nama subdomain (isi sendiri): __________________")
    doc.add_paragraph(
        "Langkah deploy lengkap ke cPanel (subdomain, upload, import database, konfigurasi, "
        "verifikasi) terdapat di berkas DEPLOYMENT.md pada repositori."
    )

    doc.save(OUTPUT)
    print("Dokumen dibuat: " + OUTPUT)
    return True


def main():
    ap = argparse.ArgumentParser(description="Generate dokumentasi Word Sagara Resto.")
    ap.add_argument("--base-url", default="http://localhost:8000")
    ap.add_argument("--no-screenshots", action="store_true")
    args = ap.parse_args()

    print("Membaca struktur database...")
    tables = parse_schema(SCHEMA)
    roles, perms, role_perms = parse_rbac(SEED)
    print("  tabel: %d | role: %d | permission: %d" % (len(tables), len(roles), len(perms)))

    shots_ok = False
    if not args.no_screenshots:
        print("Mengambil screenshot via Playwright (%s)..." % args.base_url)
        ok, info = take_screenshots(args.base_url)
        shots_ok = ok
        if ok:
            print("  screenshot tersimpan: %d" % len(info))
        else:
            print("  dilewati: %s" % info)
            print("  Dokumen tetap dibuat; bagian screenshot akan berisi instruksi melengkapi.")

    ok = build_docx(tables, roles, perms, role_perms, shots_ok, args.base_url)
    sys.exit(0 if ok else 1)


if __name__ == "__main__":
    main()
