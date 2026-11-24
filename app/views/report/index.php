<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<div class="panel no-print">
  <div class="panel-head">
    <h2>Filter Laporan</h2>
    <div class="toolbar">
      <form method="get" action="<?= url('report') ?>">
        <label style="font-size:13px;">Dari <input type="date" name="from" value="<?= e($from) ?>"></label>
        <label style="font-size:13px;">Sampai <input type="date" name="to" value="<?= e($to) ?>"></label>
        <button class="btn secondary sm" type="submit"><ion-icon name="funnel-outline"></ion-icon> Terapkan</button>
      </form>
      <?php if (can('report.export')): ?>
        <a class="btn sm" href="<?= url('report/export') ?>?<?= e(http_build_query(['from'=>$from,'to'=>$to])) ?>"><ion-icon name="download-outline"></ion-icon> CSV</a>
        <a class="btn secondary sm" href="<?= url('report/pdf') ?>?<?= e(http_build_query(['from'=>$from,'to'=>$to])) ?>"><ion-icon name="document-outline"></ion-icon> PDF</a>
      <?php endif; ?>
      <button class="btn secondary sm" onclick="window.print()"><ion-icon name="print-outline"></ion-icon> Cetak</button>
    </div>
  </div>
</div>

<h3 style="margin:4px 0 14px;">Laporan Penjualan <?= e(date('d M Y', strtotime($from))) ?> &ndash; <?= e(date('d M Y', strtotime($to))) ?></h3>

<div class="cards" style="margin-bottom:22px;">
  <div class="card stat-card"><div class="icon green"><ion-icon name="cash-outline"></ion-icon></div>
    <div><div class="label">Total Omzet</div><div class="value" style="font-size:20px;"><?= rupiah($summary['revenue']) ?></div></div></div>
  <div class="card stat-card"><div class="icon"><ion-icon name="receipt-outline"></ion-icon></div>
    <div><div class="label">Transaksi</div><div class="value"><?= (int)$summary['count'] ?></div></div></div>
  <div class="card stat-card"><div class="icon amber"><ion-icon name="fast-food-outline"></ion-icon></div>
    <div><div class="label">Item Terjual</div><div class="value"><?= (int)$summary['items'] ?></div></div></div>
  <div class="card stat-card"><div class="icon navy"><ion-icon name="stats-chart-outline"></ion-icon></div>
    <div><div class="label">Rata-rata/Transaksi</div><div class="value" style="font-size:18px;"><?= rupiah($summary['avg']) ?></div></div></div>
</div>

<div style="display:grid;grid-template-columns:1.3fr 1fr;gap:20px;align-items:start;" class="report-split">
  <div class="panel">
    <div class="panel-head"><h2>Omzet Harian</h2></div>
    <div class="panel-body" style="padding:0;">
      <div class="table-wrap"><table class="data">
        <thead><tr><th>Tanggal</th><th>Transaksi</th><th>Omzet</th></tr></thead>
        <tbody>
          <?php if ($daily): foreach ($daily as $d): ?>
            <tr><td><?= e(date('d M Y', strtotime($d['d']))) ?></td><td><?= (int)$d['c'] ?></td><td><?= rupiah($d['t']) ?></td></tr>
          <?php endforeach; else: ?>
            <tr><td colspan="3" class="empty">Tidak ada transaksi pada rentang ini.</td></tr>
          <?php endif; ?>
        </tbody>
      </table></div>
    </div>
  </div>

  <div class="panel">
    <div class="panel-head"><h2>Menu Terlaris</h2></div>
    <div class="panel-body" style="padding:0;">
      <div class="table-wrap"><table class="data">
        <thead><tr><th>Menu</th><th>Qty</th><th>Omzet</th></tr></thead>
        <tbody>
          <?php if ($topItems): foreach ($topItems as $t): ?>
            <tr><td><?= e($t['item_name']) ?></td><td><?= (int)$t['qty'] ?></td><td><?= rupiah($t['revenue']) ?></td></tr>
          <?php endforeach; else: ?>
            <tr><td colspan="3" class="empty">Belum ada data.</td></tr>
          <?php endif; ?>
        </tbody>
      </table></div>
    </div>
  </div>
</div>

<div class="panel">
  <div class="panel-head"><h2>Rincian Transaksi</h2></div>
  <div class="panel-body" style="padding:0;">
    <div class="table-wrap"><table class="data">
      <thead><tr><th>No. Pesanan</th><th>Tanggal Bayar</th><th>Meja</th><th>Kasir</th><th>Item</th><th>Total</th></tr></thead>
      <tbody>
        <?php if ($orders): foreach ($orders as $o): ?>
          <tr>
            <td><?= e($o['order_number']) ?></td>
            <td><?= e(date('d M Y H:i', strtotime($o['paid_at']))) ?></td>
            <td><?= e($o['table_number'] ?? '-') ?></td>
            <td><?= e($o['cashier'] ?? '-') ?></td>
            <td><?= (int)$o['item_count'] ?></td>
            <td><?= rupiah($o['total']) ?></td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="6" class="empty">Tidak ada transaksi.</td></tr>
        <?php endif; ?>
      </tbody>
    </table></div>
  </div>
</div>
