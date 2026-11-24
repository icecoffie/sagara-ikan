<?php if (!defined('SAGARA_APP')) { exit; } ?>
<div class="panel" style="max-width:560px;margin:40px auto;text-align:center;">
  <div class="panel-body">
    <div style="font-size:64px;font-weight:700;font-family:'Playfair Display',serif;color:var(--clr-primary);"><?= e($code) ?></div>
    <h2 style="margin:8px 0;"><?= e($title) ?></h2>
    <p style="color:var(--clr-muted);margin-bottom:18px;"><?= e($message ?: 'Maaf, terjadi masalah.') ?></p>
    <a class="btn" href="<?= url('dashboard') ?>"><ion-icon name="home-outline"></ion-icon> Kembali ke Dashboard</a>
  </div>
</div>
