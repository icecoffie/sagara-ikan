<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — <?= e($appName) ?></title>
  <link rel="icon" href="<?= asset('assets/logo.png') ?>" type="image/png">
  <link rel="stylesheet" href="<?= asset('assets/css/app.css') ?>">
</head>
<body>
  <div class="auth-wrap">
    <div class="auth-card">
      <div class="brand">
        <img src="<?= asset('assets/logo.png') ?>" alt="logo">
        <h1><?= e($appName) ?></h1>
        <p>Masuk ke panel manajemen restoran</p>
      </div>

      <?php if (!empty($error)): ?>
        <div class="alert error"><?= e($error) ?></div>
      <?php endif; ?>

      <form method="post" action="<?= url('login') ?>">
        <?= Csrf::field() ?>
        <div class="field">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" value="<?= e($oldEmail) ?>" placeholder="nama@sagararesto.test" required autofocus>
        </div>
        <div class="field">
          <label for="password">Kata Sandi</label>
          <input type="password" id="password" name="password" placeholder="••••••••" required>
        </div>
        <button type="submit" class="btn block" style="margin-top:8px;">
          <ion-icon name="log-in-outline"></ion-icon> Masuk
        </button>
      </form>

      <div class="auth-demo">
        <b>Akun demo:</b>
        <table>
          <tr><td>admin@sagararesto.test</td><td>Admin#2026</td></tr>
          <tr><td>manajer@sagararesto.test</td><td>Manajer#2026</td></tr>
          <tr><td>kasir@sagararesto.test</td><td>Kasir#2026</td></tr>
          <tr><td>pelayan@sagararesto.test</td><td>Pelayan#2026</td></tr>
        </table>
      </div>

      <p style="text-align:center;margin-top:18px;font-size:14px;">
        <a href="<?= url('') ?>">&larr; Kembali ke situs</a>
      </p>
    </div>
  </div>
  <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
