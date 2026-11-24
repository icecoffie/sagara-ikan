<?php if (!defined('SAGARA_APP')) { exit; } ?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($title) ?></title>
<link rel="stylesheet" href="<?= asset('assets/css/app.css') ?>"></head>
<body><div class="auth-wrap"><div class="auth-card" style="text-align:center;">
<div style="font-size:64px;font-weight:700;font-family:'Playfair Display',serif;color:var(--clr-primary);"><?= e($code) ?></div>
<h2 style="margin:8px 0;color:var(--clr-secondary);"><?= e($title) ?></h2>
<p style="color:var(--clr-muted);margin-bottom:18px;"><?= e($message ?: 'Maaf, terjadi masalah.') ?></p>
<a class="btn block" href="<?= url('') ?>">Ke Beranda</a>
</div></div></body></html>
