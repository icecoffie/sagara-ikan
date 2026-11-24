<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<div class="panel">
  <div class="panel-head">
    <h2>Daftar Kategori</h2>
    <div class="toolbar">
      <form method="get" action="<?= url('category') ?>">
        <input type="text" name="q" value="<?= e($q) ?>" placeholder="Cari kategori...">
        <button class="btn secondary sm" type="submit"><ion-icon name="search-outline"></ion-icon></button>
      </form>
      <?php if (can('category.create')): ?>
        <a href="<?= url('category/create') ?>" class="btn sm"><ion-icon name="add-outline"></ion-icon> Tambah</a>
      <?php endif; ?>
    </div>
  </div>
  <div class="panel-body" style="padding:0;">
    <div class="table-wrap">
      <table class="data">
        <thead><tr><th>Nama</th><th>Slug</th><th>Jumlah Menu</th><th>Status</th><th class="actions">Aksi</th></tr></thead>
        <tbody>
          <?php if ($items): foreach ($items as $c): ?>
            <tr>
              <td><b><?= e($c['name']) ?></b><?php if (!empty($c['description'])): ?><div style="font-size:12px;color:var(--clr-muted)"><?= e(mb_strimwidth($c['description'],0,60,'…')) ?></div><?php endif; ?></td>
              <td><code style="font-size:12px;"><?= e($c['slug']) ?></code></td>
              <td><?= (int)($c['item_count'] ?? 0) ?></td>
              <td><span class="badge <?= $c['is_active'] ? 'green' : 'gray' ?>"><?= $c['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
              <td class="actions">
                <?php if (can('category.update')): ?><a class="btn secondary sm" href="<?= url('category/edit/' . $c['id']) ?>"><ion-icon name="create-outline"></ion-icon></a><?php endif; ?>
                <?php if (can('category.delete')): ?>
                  <form method="post" action="<?= url('category/delete/' . $c['id']) ?>" style="display:inline" onsubmit="return confirm('Hapus kategori ini?')">
                    <?= Csrf::field() ?><button class="btn danger sm" type="submit"><ion-icon name="trash-outline"></ion-icon></button>
                  </form>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; else: ?>
            <tr><td colspan="5" class="empty">Tidak ada kategori.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?= pagination_links($p, 'category') ?>
