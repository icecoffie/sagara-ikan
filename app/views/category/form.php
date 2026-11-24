<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<?php
$isEdit = ($mode === 'edit');
$action = $isEdit ? url('category/update/' . $item['id']) : url('category/store');
$val = fn($k, $d = '') => e($item[$k] ?? $d);
?>
<div class="panel" style="max-width:640px;">
  <div class="panel-head"><h2><?= $isEdit ? 'Ubah' : 'Tambah' ?> Kategori</h2>
    <a href="<?= url('category') ?>" class="btn secondary sm"><ion-icon name="arrow-back-outline"></ion-icon> Kembali</a>
  </div>
  <div class="panel-body">
    <form method="post" action="<?= $action ?>">
      <?= Csrf::field() ?>
      <div class="field">
        <label for="name">Nama Kategori</label>
        <input type="text" id="name" name="name" value="<?= $val('name') ?>" required>
        <?php if (!empty($errors['name'])): ?><span class="err"><?= e($errors['name']) ?></span><?php endif; ?>
      </div>
      <div class="field">
        <label for="description">Deskripsi</label>
        <textarea id="description" name="description" rows="3"><?= $val('description') ?></textarea>
      </div>
      <div class="field">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
          <input type="checkbox" name="is_active" value="1" style="width:auto" <?= (!isset($item['is_active']) || $item['is_active']) ? 'checked' : '' ?>> Aktif
        </label>
      </div>
      <div class="form-actions">
        <button class="btn" type="submit"><ion-icon name="save-outline"></ion-icon> Simpan</button>
        <a class="btn secondary" href="<?= url('category') ?>">Batal</a>
      </div>
    </form>
  </div>
</div>
