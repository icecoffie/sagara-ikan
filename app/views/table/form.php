<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<?php
$isEdit = ($mode === 'edit');
$action = $isEdit ? url('table/update/' . $item['id']) : url('table/store');
$val = fn($k, $d = '') => e($item[$k] ?? $d);
$cur = fn($k, $v) => (string)($item[$k] ?? '') === (string)$v ? 'selected' : '';
?>
<div class="panel" style="max-width:640px;">
  <div class="panel-head"><h2><?= $isEdit ? 'Ubah' : 'Tambah' ?> Meja</h2>
    <a href="<?= url('table') ?>" class="btn secondary sm"><ion-icon name="arrow-back-outline"></ion-icon> Kembali</a>
  </div>
  <div class="panel-body">
    <form method="post" action="<?= $action ?>">
      <?= Csrf::field() ?>
      <div class="form-grid">
        <div class="field">
          <label for="table_number">Nomor Meja</label>
          <input type="text" id="table_number" name="table_number" value="<?= $val('table_number') ?>" placeholder="T-07" required>
          <?php if (!empty($errors['table_number'])): ?><span class="err"><?= e($errors['table_number']) ?></span><?php endif; ?>
        </div>
        <div class="field">
          <label for="capacity">Kapasitas</label>
          <input type="number" id="capacity" name="capacity" min="1" value="<?= e($item['capacity'] ?? 2) ?>" required>
        </div>
        <div class="field">
          <label for="location">Lokasi</label>
          <select id="location" name="location">
            <option value="indoor" <?= $cur('location','indoor') ?>>Indoor</option>
            <option value="outdoor" <?= $cur('location','outdoor') ?>>Outdoor</option>
          </select>
        </div>
        <div class="field">
          <label for="status">Status</label>
          <select id="status" name="status">
            <option value="available" <?= $cur('status','available') ?>>Tersedia</option>
            <option value="occupied" <?= $cur('status','occupied') ?>>Terisi</option>
            <option value="reserved" <?= $cur('status','reserved') ?>>Direservasi</option>
          </select>
        </div>
      </div>
      <div class="form-actions">
        <button class="btn" type="submit"><ion-icon name="save-outline"></ion-icon> Simpan</button>
        <a class="btn secondary" href="<?= url('table') ?>">Batal</a>
      </div>
    </form>
  </div>
</div>
