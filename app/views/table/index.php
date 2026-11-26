<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<?php $stBadge = ['available' => 'green', 'occupied' => 'red', 'reserved' => 'amber']; ?>
<div class="panel">
  <div class="panel-head">
    <h2>Daftar Meja</h2>
    <div class="toolbar">
      <form method="get" action="<?= url('table') ?>">
        <input type="text" name="q" value="<?= e($filters['q']) ?>" placeholder="Cari no. meja...">
        <select name="location">
          <option value="">Semua Lokasi</option>
          <option value="indoor" <?= $filters['location']==='indoor'?'selected':'' ?>>Indoor</option>
          <option value="outdoor" <?= $filters['location']==='outdoor'?'selected':'' ?>>Outdoor</option>
        </select>
        <select name="status">
          <option value="">Semua Status</option>
          <?php foreach (['available'=>'Tersedia','occupied'=>'Terisi','reserved'=>'Direservasi'] as $k=>$v): ?>
            <option value="<?= $k ?>" <?= $filters['status']===$k?'selected':'' ?>><?= $v ?></option>
          <?php endforeach; ?>
        </select>
        <button class="btn secondary sm" type="submit"><ion-icon name="funnel-outline"></ion-icon> Filter</button>
      </form>
      <?php if (can('table.create')): ?><a href="<?= url('table/create') ?>" class="btn sm"><ion-icon name="add-outline"></ion-icon> Tambah</a><?php endif; ?>
    </div>
  </div>
  <div class="panel-body" style="padding:0;">
    <div class="table-wrap"><table class="data">
      <thead><tr><th>No. Meja</th><th>Kapasitas</th><th>Lokasi</th><th>Status</th><th class="actions">Aksi</th></tr></thead>
      <tbody>
        <?php if ($items): foreach ($items as $t): ?>
          <tr>
            <td><b><?= e($t['table_number']) ?></b></td>
            <td><?= (int)$t['capacity'] ?> orang</td>
            <td><?= e(ucfirst($t['location'])) ?></td>
            <td>
              <?php if (can('table.update')): ?>
                <form method="post" action="<?= url('table/setStatus/' . $t['id']) ?>" class="status-quick">
                  <?= Csrf::field() ?>
                  <span class="status-dot <?= $stBadge[$t['status']] ?? 'gray' ?>"></span>
                  <select name="status" onchange="this.form.submit()" class="status-select st-<?= $stBadge[$t['status']] ?? 'gray' ?>">
                    <?php foreach (['available'=>'Tersedia','occupied'=>'Terisi','reserved'=>'Direservasi'] as $k=>$v): ?>
                      <option value="<?= $k ?>" <?= $t['status']===$k?'selected':'' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                  </select>
                </form>
              <?php else: ?>
                <span class="badge <?= $stBadge[$t['status']] ?? 'gray' ?>"><?= e(ucfirst($t['status'])) ?></span>
              <?php endif; ?>
            </td>
            <td class="actions">
              <?php if (can('table.update')): ?><a class="btn secondary sm" href="<?= url('table/edit/' . $t['id']) ?>"><ion-icon name="create-outline"></ion-icon></a><?php endif; ?>
              <?php if (can('table.delete')): ?>
                <form method="post" action="<?= url('table/delete/' . $t['id']) ?>" style="display:inline" onsubmit="return confirm('Hapus meja ini?')"><?= Csrf::field() ?><button class="btn danger sm"><ion-icon name="trash-outline"></ion-icon></button></form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="5" class="empty">Tidak ada meja.</td></tr>
        <?php endif; ?>
      </tbody>
    </table></div>
  </div>
</div>
<?= pagination_links($p, 'table') ?>
