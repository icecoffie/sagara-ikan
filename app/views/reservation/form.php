<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<?php
$isEdit = ($mode === 'edit');
$action = $isEdit ? url('reservation/update/' . $item['id']) : url('reservation/store');
$val = fn($k, $d = '') => e($item[$k] ?? $d);
$cur = fn($k, $v) => (string)($item[$k] ?? '') === (string)$v ? 'selected' : '';
?>
<div class="panel" style="max-width:760px;">
  <div class="panel-head"><h2><?= $isEdit ? 'Ubah' : 'Tambah' ?> Reservasi</h2>
    <a href="<?= url('reservation') ?>" class="btn secondary sm"><ion-icon name="arrow-back-outline"></ion-icon> Kembali</a>
  </div>
  <div class="panel-body">
    <form method="post" action="<?= $action ?>">
      <?= Csrf::field() ?>
      <div class="form-grid">
        <div class="field">
          <label for="customer_name">Nama Tamu</label>
          <input type="text" id="customer_name" name="customer_name" value="<?= $val('customer_name') ?>" required>
          <?php if (!empty($errors['customer_name'])): ?><span class="err"><?= e($errors['customer_name']) ?></span><?php endif; ?>
        </div>
        <div class="field">
          <label for="phone">Telepon</label>
          <input type="text" id="phone" name="phone" value="<?= $val('phone') ?>" placeholder="08xxxxxxxxxx">
        </div>
        <div class="field">
          <label for="reservation_time">Waktu Reservasi</label>
          <input type="datetime-local" id="reservation_time" name="reservation_time" value="<?= $val('reservation_time') ?>" required>
          <?php if (!empty($errors['reservation_time'])): ?><span class="err"><?= e($errors['reservation_time']) ?></span><?php endif; ?>
        </div>
        <div class="field">
          <label for="party_size">Jumlah Tamu</label>
          <input type="number" id="party_size" name="party_size" min="1" value="<?= e($item['party_size'] ?? 2) ?>" required>
        </div>
        <div class="field">
          <label for="table_id">Meja</label>
          <select id="table_id" name="table_id">
            <option value="">— Belum ditentukan —</option>
            <?php foreach ($tables as $t): ?>
              <option value="<?= $t['id'] ?>" <?= $cur('table_id', $t['id']) ?>><?= e($t['table_number']) ?> (<?= (int)$t['capacity'] ?> org)</option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="field">
          <label for="status">Status</label>
          <select id="status" name="status">
            <option value="pending" <?= $cur('status','pending') ?>>Pending</option>
            <option value="confirmed" <?= $cur('status','confirmed') ?>>Dikonfirmasi</option>
            <option value="cancelled" <?= $cur('status','cancelled') ?>>Dibatalkan</option>
          </select>
        </div>
        <div class="field full">
          <label for="notes">Catatan</label>
          <textarea id="notes" name="notes" rows="2"><?= $val('notes') ?></textarea>
        </div>
      </div>
      <div class="form-actions">
        <button class="btn" type="submit"><ion-icon name="save-outline"></ion-icon> Simpan</button>
        <a class="btn secondary" href="<?= url('reservation') ?>">Batal</a>
      </div>
    </form>
  </div>
</div>
