<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<?php
$isEdit = ($mode === 'edit');
$action = $isEdit ? url('voucher/update/' . $item['id']) : url('voucher/store');
$val = fn($k, $d = '') => e($item[$k] ?? $d);
$cur = fn($k, $v) => (string)($item[$k] ?? '') === (string)$v ? 'selected' : '';
?>
<div class="panel" style="max-width:640px;">
  <div class="panel-head"><h2><?= $isEdit ? 'Ubah' : 'Tambah' ?> Voucher</h2>
    <a href="<?= url('voucher') ?>" class="btn secondary sm"><ion-icon name="arrow-back-outline"></ion-icon> Kembali</a>
  </div>
  <div class="panel-body">
    <form method="post" action="<?= $action ?>">
      <?= Csrf::field() ?>
      <div class="form-grid">
        <div class="field">
          <label for="code">Kode</label>
          <input type="text" id="code" name="code" value="<?= $val('code') ?>" placeholder="SAGARA10" style="text-transform:uppercase" required>
          <?php if (!empty($errors['code'])): ?><span class="err"><?= e($errors['code']) ?></span><?php endif; ?>
        </div>
        <div class="field">
          <label for="type">Tipe</label>
          <select id="type" name="type">
            <option value="amount" <?= $cur('type','amount') ?>>Potongan (Rp)</option>
            <option value="percent" <?= $cur('type','percent') ?>>Persentase (%)</option>
          </select>
        </div>
        <div class="field">
          <label for="value">Nilai</label>
          <input type="number" id="value" name="value" step="1" min="0" value="<?= e($item['value'] ?? '') ?>" required>
          <span class="hint">Rp untuk potongan, atau angka % untuk persentase.</span>
          <?php if (!empty($errors['value'])): ?><span class="err"><?= e($errors['value']) ?></span><?php endif; ?>
        </div>
        <div class="field">
          <label for="min_total">Min. Belanja (Rp)</label>
          <input type="number" id="min_total" name="min_total" step="1000" min="0" value="<?= e($item['min_total'] ?? 0) ?>">
        </div>
        <div class="field full">
          <label for="description">Deskripsi</label>
          <input type="text" id="description" name="description" value="<?= $val('description') ?>" placeholder="Diskon 10% semua menu">
        </div>
        <div class="field">
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:400;">
            <input type="checkbox" name="is_active" value="1" style="width:auto" <?= (!isset($item['is_active']) || $item['is_active']) ? 'checked' : '' ?>> Aktif
          </label>
        </div>
      </div>
      <div class="form-actions">
        <button class="btn" type="submit"><ion-icon name="save-outline"></ion-icon> Simpan</button>
        <a class="btn secondary" href="<?= url('voucher') ?>">Batal</a>
      </div>
    </form>
  </div>
</div>
