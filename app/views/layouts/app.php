<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

$u = Auth::user();
$appName = Config::get('app_name', 'Sagara Resto');
$current = trim($_SERVER['PATH_INFO'] ?? '', '/');
$seg = explode('/', $current)[0] ?? '';

$nav = [
  'Utama' => [
    ['dashboard', 'Dashboard', 'grid-outline', null],
  ],
  'Operasional' => [
    ['order', 'Pesanan', 'receipt-outline', 'order.view'],
    ['reservation', 'Reservasi', 'calendar-outline', 'reservation.view'],
    ['table', 'Meja', 'restaurant-outline', 'table.view'],
  ],
  'Katalog' => [
    ['menus', 'Menu', 'fast-food-outline', 'menu.view'],
    ['category', 'Kategori', 'pricetags-outline', 'category.view'],
  ],
  'Analitik' => [
    ['report', 'Laporan', 'bar-chart-outline', 'report.view'],
  ],
  'Administrasi' => [
    ['user', 'Pengguna', 'people-outline', 'user.view'],
    ['role', 'Role & Izin', 'shield-checkmark-outline', 'role.view'],
    ['log', 'Log Aktivitas', 'time-outline', 'log.view'],
  ],
];
$flashes = flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle ? "$pageTitle — $appName" : $appName) ?></title>
  <link rel="icon" href="<?= asset('assets/logo.png') ?>" type="image/png">
  <link rel="stylesheet" href="<?= asset('assets/css/app.css') ?>">
</head>
<body>
<div class="app-shell">
  <aside class="sidebar" id="sidebar">
    <a href="<?= url('dashboard') ?>" class="brand">
      <img src="<?= asset('assets/logo.png') ?>" alt="logo">
      <span><?= e($appName) ?></span>
    </a>
    <nav>
      <?php foreach ($nav as $group => $links): ?>
        <?php
          $visible = array_filter($links, fn($l) => $l[3] === null || can($l[3]));
          if (!$visible) continue;
        ?>
        <div class="nav-group"><?= e($group) ?></div>
        <?php foreach ($visible as $l): [$route, $label, $icon] = $l; ?>
          <a class="nav-link <?= $seg === $route ? 'active' : '' ?>" href="<?= url($route) ?>">
            <ion-icon name="<?= e($icon) ?>"></ion-icon><span><?= e($label) ?></span>
          </a>
        <?php endforeach; ?>
      <?php endforeach; ?>
    </nav>
  </aside>

  <div class="main">
    <header class="topbar">
      <div style="display:flex;align-items:center;gap:12px;">
        <button class="menu-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">
          <ion-icon name="menu-outline"></ion-icon>
        </button>
        <div class="page-title"><?= e($pageTitle ?: 'Dashboard') ?></div>
      </div>
      <div class="user-box">
        <a href="<?= base_url() ?>/index.php" target="_blank" class="btn secondary sm" title="Lihat situs publik">
          <ion-icon name="open-outline"></ion-icon> Situs
        </a>
        <span><?= e($u['name']) ?></span>
        <span class="role-pill"><?= e($u['role_name']) ?></span>
        <a href="<?= url('logout') ?>" class="btn secondary sm"><ion-icon name="log-out-outline"></ion-icon> Keluar</a>
      </div>
    </header>

    <main class="content">
      <?php foreach ($flashes as $type => $msg): ?>
        <div class="alert <?= e($type) ?>"><?= e($msg) ?></div>
      <?php endforeach; ?>
      <?= $content ?>
    </main>
  </div>
</div>
<script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<?= $pageScripts ?? '' ?>
</body>
</html>
