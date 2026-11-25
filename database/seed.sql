-- ============================================================
--  Sagara Resto — Seed data (demo accounts + sample content)
--
--  Import AFTER schema.sql, into the same database.
--
--  Demo accounts (plaintext passwords for documentation):
--    admin@sagararesto.app    / Admin#2026     (Admin)
--    manajer@sagararesto.app  / Manajer#2026   (Manajer)
--    kasir@sagararesto.app    / Kasir#2026     (Kasir)
--    pelayan@sagararesto.app  / Pelayan#2026   (Pelayan)
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
-- Use DELETE (not TRUNCATE) so re-seeding works on hosts that reject
-- TRUNCATE on tables referenced by a foreign key (MariaDB error #1701).
DELETE FROM activity_logs;
DELETE FROM order_items;
DELETE FROM orders;
DELETE FROM reservations;
DELETE FROM dining_tables;
DELETE FROM menu_items;
DELETE FROM categories;
DELETE FROM role_permissions;
DELETE FROM permissions;
DELETE FROM users;
DELETE FROM roles;
ALTER TABLE roles            AUTO_INCREMENT = 1;
ALTER TABLE permissions      AUTO_INCREMENT = 1;
ALTER TABLE users            AUTO_INCREMENT = 1;
ALTER TABLE categories       AUTO_INCREMENT = 1;
ALTER TABLE menu_items       AUTO_INCREMENT = 1;
ALTER TABLE dining_tables    AUTO_INCREMENT = 1;
ALTER TABLE reservations     AUTO_INCREMENT = 1;
ALTER TABLE orders           AUTO_INCREMENT = 1;
ALTER TABLE order_items      AUTO_INCREMENT = 1;
ALTER TABLE activity_logs    AUTO_INCREMENT = 1;
SET FOREIGN_KEY_CHECKS = 1;

-- ---------------- Roles ----------------
INSERT INTO `roles` (`id`, `name`, `slug`, `description`) VALUES
(1, 'Admin',   'admin',   'Akses penuh termasuk kelola pengguna, role, dan izin'),
(2, 'Manajer', 'manajer', 'Kelola katalog, meja, reservasi; lihat pesanan & laporan'),
(3, 'Kasir',   'kasir',   'Kelola pesanan, pembayaran, dan cetak struk'),
(4, 'Pelayan', 'pelayan', 'Ambil pesanan, kelola reservasi, ubah status meja');

-- ---------------- Permissions ----------------
INSERT INTO `permissions` (`name`, `slug`, `resource`, `action`) VALUES
('Lihat Dashboard',      'dashboard.view',    'dashboard',   'view'),
('Lihat Kategori',       'category.view',     'category',    'view'),
('Tambah Kategori',      'category.create',   'category',    'create'),
('Ubah Kategori',        'category.update',   'category',    'update'),
('Hapus Kategori',       'category.delete',   'category',    'delete'),
('Lihat Menu',           'menu.view',         'menu',        'view'),
('Tambah Menu',          'menu.create',       'menu',        'create'),
('Ubah Menu',            'menu.update',       'menu',        'update'),
('Hapus Menu',           'menu.delete',       'menu',        'delete'),
('Lihat Meja',           'table.view',        'table',       'view'),
('Tambah Meja',          'table.create',      'table',       'create'),
('Ubah Meja',            'table.update',      'table',       'update'),
('Hapus Meja',           'table.delete',      'table',       'delete'),
('Lihat Reservasi',      'reservation.view',  'reservation', 'view'),
('Tambah Reservasi',     'reservation.create','reservation', 'create'),
('Ubah Reservasi',       'reservation.update','reservation', 'update'),
('Hapus Reservasi',      'reservation.delete','reservation', 'delete'),
('Lihat Pesanan',        'order.view',        'order',       'view'),
('Buat Pesanan',         'order.create',      'order',       'create'),
('Ubah Pesanan',         'order.update',      'order',       'update'),
('Bayar Pesanan',        'order.pay',         'order',       'pay'),
('Hapus Pesanan',        'order.delete',      'order',       'delete'),
('Lihat Laporan',        'report.view',       'report',      'view'),
('Export Laporan',       'report.export',     'report',      'export'),
('Lihat Pengguna',       'user.view',         'user',        'view'),
('Tambah Pengguna',      'user.create',       'user',        'create'),
('Ubah Pengguna',        'user.update',       'user',        'update'),
('Hapus Pengguna',       'user.delete',       'user',        'delete'),
('Lihat Role & Izin',    'role.view',         'role',        'view'),
('Kelola Role & Izin',   'role.update',       'role',        'update'),
('Lihat Log Aktivitas',  'log.view',          'log',         'view');

-- ---------------- Role ↔ Permission ----------------
-- Admin: every permission
INSERT INTO `role_permissions` (role_id, permission_id)
SELECT 1, p.id FROM permissions p;

-- Manajer: catalog/tables/reservations CRUD + view orders + dashboard/reports
INSERT INTO `role_permissions` (role_id, permission_id)
SELECT 2, p.id FROM permissions p WHERE p.slug IN (
  'dashboard.view',
  'category.view','category.create','category.update','category.delete',
  'menu.view','menu.create','menu.update','menu.delete',
  'table.view','table.create','table.update','table.delete',
  'reservation.view','reservation.create','reservation.update','reservation.delete',
  'order.view',
  'report.view','report.export'
);

-- Kasir: manage & pay orders, print receipts; read menu & tables
INSERT INTO `role_permissions` (role_id, permission_id)
SELECT 3, p.id FROM permissions p WHERE p.slug IN (
  'dashboard.view',
  'menu.view',
  'table.view',
  'order.view','order.create','order.update','order.pay'
);

-- Pelayan: take/update orders, manage reservations, change table status
INSERT INTO `role_permissions` (role_id, permission_id)
SELECT 4, p.id FROM permissions p WHERE p.slug IN (
  'dashboard.view',
  'menu.view',
  'table.view','table.update',
  'reservation.view','reservation.create','reservation.update','reservation.delete',
  'order.view','order.create','order.update'
);

-- ---------------- Users ----------------
INSERT INTO `users` (`name`, `email`, `password`, `role_id`, `is_active`) VALUES
('Administrator', 'admin@sagararesto.app',   '$2y$12$r/QGgq29nq7SDcUDCANsSeUYKWrXxTrk8aHohAvgLdcW3re/bvGUm', 1, 1),
('Maya Manajer',  'manajer@sagararesto.app', '$2y$12$gF8XR05HBbjni6uSbpOdNusirIgm1BLzhy5YTyyAoaWJKW1/7nvcm', 2, 1),
('Kiki Kasir',    'kasir@sagararesto.app',   '$2y$12$q6mktnURaxAgrHOabFU6POm2EKCVekiTD1Cl7x0WDC/MOELNvtzeu', 3, 1),
('Pandu Pelayan', 'pelayan@sagararesto.app', '$2y$12$e33.g4gko0iZdqzJ94N/2eNEWgy0AJrvOeC9/OWDB2dq2Zl1fIr/a', 4, 1);

-- ---------------- Categories ----------------
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `is_active`) VALUES
(1, 'Tuna',     'tuna',     'Olahan tuna segar pilihan',            1),
(2, 'Sarden',   'sarden',   'Sarden dengan bumbu kaya rasa',        1),
(3, 'Salmon',   'salmon',   'Salmon premium untuk hidangan mewah',  1),
(4, 'Mackerel', 'mackerel', 'Mackerel gurih dengan beragam saus',   1),
(5, 'Paket',    'paket',    'Paket hemat untuk berbagi',            1),
(6, 'Minuman',  'minuman',  'Aneka minuman segar penyegar dahaga', 1);

-- ---------------- Menu items ----------------
INSERT INTO `menu_items` (`id`, `name`, `category_id`, `price`, `description`, `image`, `is_available`) VALUES
(1,  'Tuna Oseng',                   1, 89000,  'Tuna oseng bumbu rempah, pedas gurih',          'tuna.png',      1),
(2,  'Tuna Bakar Madu',              1, 95000,  'Tuna bakar glasir madu, manis legit',           'tuna1.png',     1),
(3,  'Sarden Samyang Gochujang',     2, 56000,  'Sarden saus samyang gochujang super pedas',     'sarden.png',    1),
(4,  'Sarden Balado',                2, 52000,  'Sarden balado khas nusantara',                  'sarden1.png',   1),
(5,  'Salmon Butter Lemon',          3, 119000, 'Salmon panggang butter lemon segar',            'salmon.png',    1),
(6,  'Salmon Teriyaki',              3, 125000, 'Salmon saus teriyaki ala Jepang',               'salmon1.png',   1),
(7,  'Mackerel Telur Asin',          4, 99000,  'Mackerel crispy saus telur asin creamy',        'mackerel1.png', 1),
(8,  'Mackerel Saus Padang',         4, 92000,  'Mackerel saus padang menggugah selera',         'mackerel.png',  1),
(9,  'Paket Mackerel + Sarden',      5, 160000, 'Kombinasi mackerel & sarden untuk berdua',      '22.png',        1),
(10, 'Paket Salmon + Mackerel + Tuna',5,300000, 'Tiga ikan premium dalam satu paket',            '3.png',         1),
(11, 'Paket Keluarga Komplit',       5, 350000, 'Paket besar untuk keluarga, semua favorit',     'all.png',       1),
(12, 'Paket Hemat Berdua',           5, 150000, 'Pilihan hemat untuk makan berdua',              'all2.png',      1),
(13, 'Es Teh Anget',                 6, 8000,   'Teh manis hangat, teman setia semua hidangan',  'drink_esteh.png', 1),
(14, 'Es Jeruk Samudera',            6, 12000,  'Jeruk nipis segar dengan es, asam manis pas',   'drink_jeruk.png', 1),
(15, 'Soda Gembira Laut',            6, 15000,  'Soda susu merah muda yang menyegarkan',         'drink_soda.png',  1),
(16, 'Es Kopi Susu Pelaut',          6, 18000,  'Kopi susu gula aren dingin, pelepas penat',     'drink_kopi.png',  1);

-- ---------------- Dining tables ----------------
INSERT INTO `dining_tables` (`id`, `table_number`, `capacity`, `location`, `status`) VALUES
(1, 'T-01', 2, 'indoor',  'available'),
(2, 'T-02', 4, 'indoor',  'occupied'),
(3, 'T-03', 4, 'outdoor', 'available'),
(4, 'T-04', 6, 'outdoor', 'occupied'),
(5, 'T-05', 2, 'indoor',  'reserved'),
(6, 'T-06', 8, 'outdoor', 'available');

-- ---------------- Reservations ----------------
INSERT INTO `reservations` (`customer_name`, `phone`, `table_id`, `reservation_time`, `party_size`, `status`, `notes`) VALUES
('Rani Pratiwi',  '081200010001', 5, DATE_ADD(NOW(), INTERVAL 1 DAY),  2, 'confirmed', 'Dekat jendela'),
('Budi Santoso',  '081200010002', 6, DATE_ADD(NOW(), INTERVAL 2 DAY),  8, 'pending',   'Acara ulang tahun'),
('Clara Wijaya',  '081200010003', 3, DATE_ADD(NOW(), INTERVAL 3 DAY),  4, 'pending',   NULL),
('Dedi Kurniawan','081200010004', NULL, DATE_SUB(NOW(), INTERVAL 1 DAY),3, 'cancelled', 'Batal mendadak');

-- ---------------- Orders + items (last 7 days for the chart) ----------------
INSERT INTO `orders` (`id`, `order_number`, `table_id`, `user_id`, `status`, `total`, `paid_at`, `created_at`) VALUES
(1,  'ORD-0001', 1, 3, 'paid',   178000, DATE_SUB(NOW(), INTERVAL 6 DAY), DATE_SUB(NOW(), INTERVAL 6 DAY)),
(2,  'ORD-0002', 2, 3, 'paid',   119000, DATE_SUB(NOW(), INTERVAL 6 DAY), DATE_SUB(NOW(), INTERVAL 6 DAY)),
(3,  'ORD-0003', 3, 3, 'paid',   160000, DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY)),
(4,  'ORD-0004', 2, 3, 'paid',   198000, DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_SUB(NOW(), INTERVAL 4 DAY)),
(5,  'ORD-0005', 4, 3, 'paid',   220000, DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY)),
(6,  'ORD-0006', 1, 3, 'paid',   300000, DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY)),
(7,  'ORD-0007', 3, 3, 'paid',   196000, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY)),
(8,  'ORD-0008', 2, 3, 'paid',   119000, NOW(), NOW()),
(9,  'ORD-0009', 4, 4, 'open',   89000,  NULL, NOW()),
(10, 'ORD-0010', 5, 4, 'open',   150000, NULL, NOW());

-- Customer self-service orders (no login, source = customer)
INSERT INTO `orders` (`id`, `order_number`, `table_id`, `user_id`, `source`, `order_type`, `customer_name`, `customer_phone`, `status`, `total`, `paid_at`, `created_at`) VALUES
(11, 'ORD-0011', 3, NULL, 'customer', 'dine_in',  'Nadia Putri', '081299990001', 'open', 211000, NULL, NOW()),
(12, 'ORD-0012', NULL, NULL, 'customer', 'takeaway', 'Yoga Pratama', '081299990002', 'open', 95000, NULL, NOW());

INSERT INTO `order_items` (`order_id`, `menu_item_id`, `item_name`, `qty`, `price`, `subtotal`) VALUES
(1, 1, 'Tuna Oseng', 2, 89000, 178000),
(2, 5, 'Salmon Butter Lemon', 1, 119000, 119000),
(3, 9, 'Paket Mackerel + Sarden', 1, 160000, 160000),
(4, 7, 'Mackerel Telur Asin', 2, 99000, 198000),
(5, 6, 'Salmon Teriyaki', 1, 125000, 125000),
(5, 2, 'Tuna Bakar Madu', 1, 95000, 95000),
(6, 10, 'Paket Salmon + Mackerel + Tuna', 1, 300000, 300000),
(7, 4, 'Sarden Balado', 2, 52000, 104000),
(7, 8, 'Mackerel Saus Padang', 1, 92000, 92000),
(8, 5, 'Salmon Butter Lemon', 1, 119000, 119000),
(9, 1, 'Tuna Oseng', 1, 89000, 89000),
(10, 12, 'Paket Hemat Berdua', 1, 150000, 150000),
(11, 5, 'Salmon Butter Lemon', 1, 119000, 119000),
(11, 8, 'Mackerel Saus Padang', 1, 92000, 92000),
(12, 2, 'Tuna Bakar Madu', 1, 95000, 95000);

-- ---------------- Activity log sample ----------------
INSERT INTO `activity_logs` (`user_id`, `action`, `detail`, `ip_address`, `created_at`) VALUES
(1, 'login',  'User masuk: admin@sagararesto.app', '127.0.0.1', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(3, 'order.pay', 'Pembayaran pesanan ORD-0006',     '127.0.0.1', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(2, 'menu.create', 'Menambah menu baru',            '127.0.0.1', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(4, 'reservation.create', 'Reservasi untuk Rani Pratiwi', '127.0.0.1', NOW());
