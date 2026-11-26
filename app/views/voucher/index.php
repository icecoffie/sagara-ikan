<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<div class="panel">
  <div class="panel-head">
    <h2>Daftar Voucher</h2>
    <div class="toolbar">
      <form method="get" action="<?= url('voucher') ?>">
        <input type="text" name="q" value="<?= e($q) ?>" placeholder="Cari kode/deskripsi...">
        <button class="btn secondary sm" type="submit"><ion-icon name="search-outline"></ion-icon></button>
      </form>
      <?php if (can('voucher.create')): ?><a href="<?= url('voucher/create') ?>" class="btn sm"><ion-icon name="add-outline"></ion-icon> Tambah</a><?php endif; ?>
    </div>
  </div>
  <div class="panel-body" style="padding:0;">
    <div class="table-wrap"><table class="data">
      <thead><tr><th>Kode</th><th>Deskripsi</th><th>Potongan</th><th>Min. Belanja</th><th>Status</th><th class="actions">Aksi</th></tr></thead>
      <tbody>
        <?php if ($items): foreach ($items as $v): ?>
          <tr>
            <td><b><code><?= e($v['code']) ?></code></b></td>
            <td style="color:var(--clr-muted);font-size:13px;"><?= e($v['description']) ?></td>
            <td><?= $v['type']==='percent' ? (int)$v['value'].'%' : rupiah($v['value']) ?></td>
            <td><?= (float)$v['min_total'] > 0 ? rupiah($v['min_total']) : '-' ?></td>
            <td><span class="badge <?= $v['is_active'] ? 'green' : 'gray' ?>"><?= $v['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
            <td class="actions">
              <?php if (can('voucher.update')): ?><a class="btn secondary sm" href="<?= url('voucher/edit/' . $v['id']) ?>"><ion-icon name="create-outline"></ion-icon></a><?php endif; ?>
              <?php if (can('voucher.delete')): ?>
                <form method="post" action="<?= url('voucher/delete/' . $v['id']) ?>" style="display:inline" onsubmit="return confirm('Hapus voucher ini?')"><?= Csrf::field() ?><button class="btn danger sm"><ion-icon name="trash-outline"></ion-icon></button></form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="6" class="empty">Belum ada voucher.</td></tr>
        <?php endif; ?>
      </tbody>
    </table></div>
  </div>
</div>
<?= pagination_links($p, 'voucher') ?>
