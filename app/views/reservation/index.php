<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<?php $rb = ['pending' => 'amber', 'confirmed' => 'green', 'cancelled' => 'red']; ?>
<div class="panel">
  <div class="panel-head">
    <h2>Daftar Reservasi</h2>
    <div class="toolbar">
      <form method="get" action="<?= url('reservation') ?>">
        <input type="text" name="q" value="<?= e($filters['q']) ?>" placeholder="Cari nama/telepon...">
        <select name="status">
          <option value="">Semua Status</option>
          <?php foreach (['pending'=>'Pending','confirmed'=>'Dikonfirmasi','cancelled'=>'Dibatalkan'] as $k=>$v): ?>
            <option value="<?= $k ?>" <?= $filters['status']===$k?'selected':'' ?>><?= $v ?></option>
          <?php endforeach; ?>
        </select>
        <button class="btn secondary sm" type="submit"><ion-icon name="funnel-outline"></ion-icon> Filter</button>
      </form>
      <?php if (can('reservation.create')): ?><a href="<?= url('reservation/create') ?>" class="btn sm"><ion-icon name="add-outline"></ion-icon> Tambah</a><?php endif; ?>
    </div>
  </div>
  <div class="panel-body" style="padding:0;">
    <div class="table-wrap"><table class="data">
      <thead><tr><th>Tamu</th><th>Telepon</th><th>Meja</th><th>Waktu</th><th>Jml</th><th>Status</th><th class="actions">Aksi</th></tr></thead>
      <tbody>
        <?php if ($items): foreach ($items as $r): ?>
          <tr>
            <td><b><?= e($r['customer_name']) ?></b><?php if (!empty($r['notes'])): ?><div style="font-size:12px;color:var(--clr-muted)"><?= e(mb_strimwidth($r['notes'],0,40,'…')) ?></div><?php endif; ?></td>
            <td><?= e($r['phone'] ?: '-') ?></td>
            <td><?= e($r['table_number'] ?? '-') ?></td>
            <td><?= e(date('d M Y, H:i', strtotime($r['reservation_time']))) ?></td>
            <td><?= (int)$r['party_size'] ?></td>
            <td><span class="badge <?= $rb[$r['status']] ?? 'gray' ?>"><?= e(ucfirst($r['status'])) ?></span></td>
            <td class="actions">
              <?php if (can('reservation.update')): ?><a class="btn secondary sm" href="<?= url('reservation/edit/' . $r['id']) ?>"><ion-icon name="create-outline"></ion-icon></a><?php endif; ?>
              <?php if (can('reservation.delete')): ?>
                <form method="post" action="<?= url('reservation/delete/' . $r['id']) ?>" style="display:inline" onsubmit="return confirm('Hapus reservasi ini?')"><?= Csrf::field() ?><button class="btn danger sm"><ion-icon name="trash-outline"></ion-icon></button></form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="7" class="empty">Tidak ada reservasi.</td></tr>
        <?php endif; ?>
      </tbody>
    </table></div>
  </div>
</div>
<?= pagination_links($p, 'reservation') ?>
