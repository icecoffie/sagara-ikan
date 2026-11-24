<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<?php $ob = ['open' => 'amber', 'paid' => 'green', 'cancelled' => 'red']; ?>
<?php $otype = ['dine_in' => 'Dine-in', 'takeaway' => 'Takeaway']; ?>
<div class="panel">
  <div class="panel-head">
    <h2>Daftar Pesanan</h2>
    <div class="toolbar">
      <form method="get" action="<?= url('order') ?>">
        <input type="text" name="q" value="<?= e($filters['q']) ?>" placeholder="No. pesanan...">
        <input type="date" name="date" value="<?= e($filters['date']) ?>">
        <select name="status">
          <option value="">Semua Status</option>
          <?php foreach (['open'=>'Open','paid'=>'Lunas','cancelled'=>'Batal'] as $k=>$v): ?>
            <option value="<?= $k ?>" <?= $filters['status']===$k?'selected':'' ?>><?= $v ?></option>
          <?php endforeach; ?>
        </select>
        <button class="btn secondary sm" type="submit"><ion-icon name="funnel-outline"></ion-icon> Filter</button>
      </form>
      <?php if (can('order.create')): ?><a href="<?= url('order/create') ?>" class="btn sm"><ion-icon name="add-outline"></ion-icon> Pesanan Baru</a><?php endif; ?>
    </div>
  </div>
  <div class="panel-body" style="padding:0;">
    <div class="table-wrap"><table class="data">
      <thead><tr><th>No. Pesanan</th><th>Tipe</th><th>Meja</th><th>Pelanggan / Oleh</th><th>Status</th><th>Total</th><th>Waktu</th><th class="actions">Aksi</th></tr></thead>
      <tbody>
        <?php if ($items): foreach ($items as $o): ?>
          <tr>
            <td><b><?= e($o['order_number']) ?></b>
              <?php if (($o['source'] ?? 'staff') === 'customer'): ?><div><span class="badge blue" style="font-size:11px;"><ion-icon name="globe-outline" style="vertical-align:-2px"></ion-icon> Online</span></div><?php endif; ?>
            </td>
            <td><span class="badge gray"><?= e($otype[$o['order_type'] ?? 'dine_in'] ?? '-') ?></span></td>
            <td><?= e($o['table_number'] ?? '-') ?></td>
            <td><?= e(($o['source'] ?? 'staff') === 'customer' ? ($o['customer_name'] ?: 'Pelanggan') : ($o['cashier'] ?? '-')) ?></td>
            <td><span class="badge <?= $ob[$o['status']] ?? 'gray' ?>"><?= e(ucfirst($o['status'])) ?></span></td>
            <td><?= rupiah($o['total']) ?></td>
            <td><?= e(date('d M H:i', strtotime($o['created_at']))) ?></td>
            <td class="actions">
              <a class="btn secondary sm" href="<?= url('order/show/' . $o['id']) ?>"><ion-icon name="eye-outline"></ion-icon></a>
              <?php if ($o['status'] === 'paid'): ?><a class="btn secondary sm" href="<?= url('order/receipt/' . $o['id']) ?>" target="_blank"><ion-icon name="print-outline"></ion-icon></a><?php endif; ?>
              <?php if (can('order.delete')): ?>
                <form method="post" action="<?= url('order/delete/' . $o['id']) ?>" style="display:inline" onsubmit="return confirm('Hapus pesanan ini permanen?')"><?= Csrf::field() ?><button class="btn danger sm"><ion-icon name="trash-outline"></ion-icon></button></form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="8" class="empty">Tidak ada pesanan.</td></tr>
        <?php endif; ?>
      </tbody>
    </table></div>
  </div>
</div>
<?= pagination_links($p, 'order') ?>
