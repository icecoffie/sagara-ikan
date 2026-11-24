<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<div class="panel">
  <div class="panel-head">
    <h2>Daftar Menu</h2>
    <div class="toolbar">
      <form method="get" action="<?= url('menu') ?>">
        <input type="text" name="q" value="<?= e($filters['q']) ?>" placeholder="Cari menu...">
        <select name="category_id">
          <option value="">Semua Kategori</option>
          <?php foreach ($categories as $c): ?>
            <option value="<?= $c['id'] ?>" <?= (string)$filters['category_id'] === (string)$c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
        <select name="available">
          <option value="">Semua Status</option>
          <option value="1" <?= $filters['available'] === '1' ? 'selected' : '' ?>>Tersedia</option>
          <option value="0" <?= $filters['available'] === '0' ? 'selected' : '' ?>>Tidak Tersedia</option>
        </select>
        <button class="btn secondary sm" type="submit"><ion-icon name="search-outline"></ion-icon> Filter</button>
      </form>
      <?php if (can('menu.create')): ?>
        <a href="<?= url('menu/create') ?>" class="btn sm"><ion-icon name="add-outline"></ion-icon> Tambah</a>
      <?php endif; ?>
    </div>
  </div>
  <div class="panel-body" style="padding:0;">
    <div class="table-wrap">
      <table class="data">
        <thead><tr><th></th><th>Nama</th><th>Kategori</th><th>Harga</th><th>Status</th><th class="actions">Aksi</th></tr></thead>
        <tbody>
          <?php if ($items): foreach ($items as $m): ?>
            <tr>
              <td><img class="thumb" src="<?= e(menu_image_url($m['image'])) ?>" alt=""></td>
              <td><b><?= e($m['name']) ?></b></td>
              <td><?= e($m['category_name']) ?></td>
              <td><?= rupiah($m['price']) ?></td>
              <td><span class="badge <?= $m['is_available'] ? 'green' : 'gray' ?>"><?= $m['is_available'] ? 'Tersedia' : 'Habis' ?></span></td>
              <td class="actions">
                <?php if (can('menu.update')): ?><a class="btn secondary sm" href="<?= url('menu/edit/' . $m['id']) ?>"><ion-icon name="create-outline"></ion-icon></a><?php endif; ?>
                <?php if (can('menu.delete')): ?>
                  <form method="post" action="<?= url('menu/delete/' . $m['id']) ?>" style="display:inline" onsubmit="return confirm('Hapus menu ini?')">
                    <?= Csrf::field() ?><button class="btn danger sm" type="submit"><ion-icon name="trash-outline"></ion-icon></button>
                  </form>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; else: ?>
            <tr><td colspan="6" class="empty">Tidak ada menu.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?= pagination_links($p, 'menu') ?>
