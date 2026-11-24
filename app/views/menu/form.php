<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<?php
$isEdit = ($mode === 'edit');
$action = $isEdit ? url('menu/update/' . $item['id']) : url('menu/store');
$val = fn($k, $d = '') => e($item[$k] ?? $d);
?>
<div class="panel" style="max-width:760px;">
  <div class="panel-head"><h2><?= $isEdit ? 'Ubah' : 'Tambah' ?> Menu</h2>
    <a href="<?= url('menu') ?>" class="btn secondary sm"><ion-icon name="arrow-back-outline"></ion-icon> Kembali</a>
  </div>
  <div class="panel-body">
    <form method="post" action="<?= $action ?>" enctype="multipart/form-data">
      <?= Csrf::field() ?>
      <div class="form-grid">
        <div class="field">
          <label for="name">Nama Menu</label>
          <input type="text" id="name" name="name" value="<?= $val('name') ?>" required>
          <?php if (!empty($errors['name'])): ?><span class="err"><?= e($errors['name']) ?></span><?php endif; ?>
        </div>
        <div class="field">
          <label for="category_id">Kategori</label>
          <select id="category_id" name="category_id" required>
            <option value="">— Pilih —</option>
            <?php foreach ($categories as $c): ?>
              <option value="<?= $c['id'] ?>" <?= (string)($item['category_id'] ?? '') === (string)$c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
          <?php if (!empty($errors['category_id'])): ?><span class="err"><?= e($errors['category_id']) ?></span><?php endif; ?>
        </div>
        <div class="field">
          <label for="price">Harga (Rp)</label>
          <input type="number" id="price" name="price" step="1000" min="0" value="<?= e($item['price'] ?? '') ?>" required>
          <?php if (!empty($errors['price'])): ?><span class="err"><?= e($errors['price']) ?></span><?php endif; ?>
        </div>
        <div class="field">
          <label>Ketersediaan</label>
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:400;">
            <input type="checkbox" name="is_available" value="1" style="width:auto" <?= (!isset($item['is_available']) || $item['is_available']) ? 'checked' : '' ?>> Tersedia untuk dipesan
          </label>
        </div>
        <div class="field full">
          <label for="description">Deskripsi</label>
          <textarea id="description" name="description" rows="3"><?= $val('description') ?></textarea>
        </div>
        <div class="field full">
          <label for="image">Gambar</label>
          <?php if (!empty($item['image'])): ?>
            <img src="<?= e(menu_image_url($item['image'])) ?>" alt="" style="width:120px;height:120px;object-fit:cover;border-radius:10px;border:1px solid var(--clr-border);margin-bottom:8px;">
          <?php endif; ?>
          <input type="file" id="image" name="image" accept="image/*">
          <span class="hint">JPG/PNG/WEBP/GIF, maks 3MB. Kosongkan untuk tidak mengubah.</span>
          <?php if (!empty($errors['image'])): ?><span class="err"><?= e($errors['image']) ?></span><?php endif; ?>
        </div>
      </div>
      <div class="form-actions">
        <button class="btn" type="submit"><ion-icon name="save-outline"></ion-icon> Simpan</button>
        <a class="btn secondary" href="<?= url('menu') ?>">Batal</a>
      </div>
    </form>
  </div>
</div>
