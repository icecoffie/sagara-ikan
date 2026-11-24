<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<?php
$isEdit = ($mode === 'edit');
$action = $isEdit ? url('role/update/' . $item['id']) : url('role/store');
$val = fn($k, $d = '') => e($item[$k] ?? $d);
$isAdmin = ($item['slug'] ?? '') === 'admin';
$canEdit = can('role.update');
$assigned = $assigned ?? [];
$resLabels = [
  'dashboard' => 'Dashboard', 'category' => 'Kategori', 'menu' => 'Menu', 'table' => 'Meja',
  'reservation' => 'Reservasi', 'order' => 'Pesanan', 'report' => 'Laporan',
  'user' => 'Pengguna', 'role' => 'Role & Izin', 'log' => 'Log Aktivitas',
];
?>
<div class="panel" style="max-width:900px;">
  <div class="panel-head"><h2><?= $isEdit ? 'Kelola Role' : 'Tambah Role' ?></h2>
    <a href="<?= url('role') ?>" class="btn secondary sm"><ion-icon name="arrow-back-outline"></ion-icon> Kembali</a>
  </div>
  <div class="panel-body">
    <form method="post" action="<?= $action ?>">
      <?= Csrf::field() ?>
      <div class="form-grid">
        <div class="field">
          <label for="name">Nama Role</label>
          <input type="text" id="name" name="name" value="<?= $val('name') ?>" <?= $canEdit ? '' : 'disabled' ?> required>
          <?php if (!empty($errors['name'])): ?><span class="err"><?= e($errors['name']) ?></span><?php endif; ?>
        </div>
        <div class="field">
          <label for="description">Deskripsi</label>
          <input type="text" id="description" name="description" value="<?= $val('description') ?>" <?= $canEdit ? '' : 'disabled' ?>>
        </div>
      </div>

      <h3 style="margin:22px 0 12px;font-size:16px;">Hak Akses (Permissions)</h3>
      <?php if ($isAdmin): ?>
        <div class="alert info">Role admin otomatis memiliki seluruh izin dan tidak dapat dibatasi.</div>
      <?php endif; ?>

      <div class="perm-grid">
        <?php foreach ($grouped as $resource => $perms): ?>
          <div class="perm-resource">
            <h4>
              <label style="font-weight:600;cursor:pointer;">
                <input type="checkbox" class="res-toggle" data-res="<?= e($resource) ?>" <?= ($isAdmin || !$canEdit) ? 'disabled' : '' ?>>
                <?= e($resLabels[$resource] ?? ucfirst($resource)) ?>
              </label>
            </h4>
            <?php foreach ($perms as $perm): ?>
              <label>
                <input type="checkbox" name="permissions[]" value="<?= $perm['id'] ?>"
                  class="perm-check res-<?= e($resource) ?>"
                  <?= ($isAdmin || in_array((int)$perm['id'], $assigned, true)) ? 'checked' : '' ?>
                  <?= ($isAdmin || !$canEdit) ? 'disabled' : '' ?>>
                <?= e(ucfirst($perm['action'])) ?>
              </label>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
      </div>

      <?php if ($canEdit): ?>
      <div class="form-actions">
        <button class="btn" type="submit"><ion-icon name="save-outline"></ion-icon> Simpan Izin</button>
        <a class="btn secondary" href="<?= url('role') ?>">Batal</a>
      </div>
      <?php endif; ?>
    </form>
  </div>
</div>

<script>
document.querySelectorAll('.res-toggle').forEach(function(t){
  var res = t.getAttribute('data-res');
  var boxes = document.querySelectorAll('.res-' + res);
  function sync(){ var all = Array.from(boxes).every(function(b){return b.checked;}); t.checked = all; }
  sync();
  t.addEventListener('change', function(){ boxes.forEach(function(b){ b.checked = t.checked; }); });
  boxes.forEach(function(b){ b.addEventListener('change', sync); });
});
</script>
