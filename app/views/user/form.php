<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<?php
$isEdit = ($mode === 'edit');
$action = $isEdit ? url('user/update/' . $item['id']) : url('user/store');
$val = fn($k, $d = '') => e($item[$k] ?? $d);
?>
<div class="panel" style="max-width:640px;">
  <div class="panel-head"><h2><?= $isEdit ? 'Ubah' : 'Tambah' ?> Pengguna</h2>
    <a href="<?= url('user') ?>" class="btn secondary sm"><ion-icon name="arrow-back-outline"></ion-icon> Kembali</a>
  </div>
  <div class="panel-body">
    <form method="post" action="<?= $action ?>">
      <?= Csrf::field() ?>
      <div class="form-grid">
        <div class="field">
          <label for="name">Nama</label>
          <input type="text" id="name" name="name" value="<?= $val('name') ?>" required>
          <?php if (!empty($errors['name'])): ?><span class="err"><?= e($errors['name']) ?></span><?php endif; ?>
        </div>
        <div class="field">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" value="<?= $val('email') ?>" required>
          <?php if (!empty($errors['email'])): ?><span class="err"><?= e($errors['email']) ?></span><?php endif; ?>
        </div>
        <div class="field">
          <label for="role_id">Role</label>
          <select id="role_id" name="role_id" required>
            <option value="">— Pilih —</option>
            <?php foreach ($roles as $r): ?><option value="<?= $r['id'] ?>" <?= (string)($item['role_id']??'')===(string)$r['id']?'selected':'' ?>><?= e($r['name']) ?></option><?php endforeach; ?>
          </select>
          <?php if (!empty($errors['role_id'])): ?><span class="err"><?= e($errors['role_id']) ?></span><?php endif; ?>
        </div>
        <div class="field">
          <label for="password">Kata Sandi <?= $isEdit ? '(kosongkan jika tetap)' : '' ?></label>
          <input type="password" id="password" name="password" <?= $isEdit ? '' : 'required' ?> placeholder="Minimal 6 karakter">
          <?php if (!empty($errors['password'])): ?><span class="err"><?= e($errors['password']) ?></span><?php endif; ?>
        </div>
        <div class="field">
          <label>Status</label>
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:400;">
            <input type="checkbox" name="is_active" value="1" style="width:auto" <?= (!isset($item['is_active']) || $item['is_active']) ? 'checked' : '' ?>> Akun aktif
          </label>
        </div>
      </div>
      <div class="form-actions">
        <button class="btn" type="submit"><ion-icon name="save-outline"></ion-icon> Simpan</button>
        <a class="btn secondary" href="<?= url('user') ?>">Batal</a>
      </div>
    </form>
  </div>
</div>
