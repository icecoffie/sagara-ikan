<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<?php $flashes = flash(); $cc = cart_count(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle ? $pageTitle . ' — Sagara Ikan' : 'Sagara Ikan') ?></title>
  <link rel="icon" href="<?= asset('assets/logo.png') ?>" type="image/png">
  <link rel="stylesheet" href="<?= asset('assets/css/modern.css') ?>">
</head>
<body>
  <header class="nav" id="nav">
    <div class="wrap nav-inner">
      <a href="<?= url('') ?>" class="nav-brand"><img src="<?= asset('assets/logo.png') ?>" alt="Sagara">Sagara<b>.</b></a>
      <nav class="nav-links" id="navLinks">
        <a href="<?= url('') ?>">Beranda</a>
        <a href="<?= url('menu') ?>">Menu</a>
        <a href="<?= url('pantau') ?>">Lacak Pesanan</a>
        <a href="<?= url('login') ?>" class="btn nav-login-mobile">Login Staf</a>
      </nav>
      <div class="nav-right">
        <a href="<?= url('keranjang') ?>" class="cart-link"><ion-icon name="cart-outline"></ion-icon> <span class="cl-text">Keranjang</span><?php if ($cc): ?><span class="cart-badge"><?= $cc ?></span><?php endif; ?></a>
        <a href="<?= url('login') ?>" class="btn nav-login-desktop">Login Staf</a>
        <button class="nav-toggle" id="navToggle" aria-label="Menu" onclick="var l=document.getElementById('navLinks');this.classList.toggle('active',l.classList.toggle('open'));"><ion-icon name="menu-outline"></ion-icon><ion-icon name="close-outline"></ion-icon></button>
      </div>
    </div>
  </header>

  <main class="wrap" style="padding-top:36px;padding-bottom:70px;min-height:60vh;">
    <?php foreach ($flashes as $type => $msg): ?>
      <div class="site-alert <?= e($type) ?>"><?= e($msg) ?></div>
    <?php endforeach; ?>
    <?= $content ?>
  </main>

  <footer class="foot">
    <div class="wrap foot-bottom" style="border:0;">
      <span>&copy; <?= date('Y') ?> Sagara Ikan — Malika Shakila</span>
      <span><a href="<?= url('') ?>" style="color:var(--on-dark)">Kembali ke Beranda</a></span>
    </div>
  </footer>

  <script>
    (function(){ var n=document.getElementById('nav'); window.addEventListener('scroll',function(){n.classList.toggle('scrolled',window.scrollY>20);},{passive:true}); })();
  </script>
  <script src="<?= asset('assets/js/shop.js') ?>"></script>
  <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
