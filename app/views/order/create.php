<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<div class="panel" style="max-width:560px;">
  <div class="panel-head"><h2>Pesanan Baru</h2>
    <a href="<?= url('order') ?>" class="btn secondary sm"><ion-icon name="arrow-back-outline"></ion-icon> Kembali</a>
  </div>
  <div class="panel-body">
    <form method="post" action="<?= url('order/store') ?>">
      <?= Csrf::field() ?>
      <div class="field">
        <label for="table_id">Meja</label>
        <select id="table_id" name="table_id">
          <option value="">— Tanpa meja (takeaway) —</option>
          <?php foreach ($tables as $t): ?>
            <option value="<?= $t['id'] ?>"><?= e($t['table_number']) ?> (<?= (int)$t['capacity'] ?> org) — <?= e(ucfirst($t['status'])) ?></option>
          <?php endforeach; ?>
        </select>
        <span class="hint">Setelah dibuat, Anda bisa menambahkan item menu.</span>
      </div>
      <div class="form-actions">
        <button class="btn" type="submit"><ion-icon name="add-circle-outline"></ion-icon> Buat Pesanan</button>
      </div>
    </form>
  </div>
</div>
