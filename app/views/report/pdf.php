<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<style>
  body{font-family:DejaVu Sans, sans-serif;color:#111;font-size:12px;}
  h1{font-size:18px;margin:0 0 4px;} .muted{color:#666;}
  table{width:100%;border-collapse:collapse;margin-top:10px;}
  th,td{border:1px solid #ccc;padding:5px 7px;text-align:left;}
  th{background:#0b2447;color:#fff;}
  .r{text-align:right;} .sum{font-weight:bold;}
</style>
<h1>Laporan Penjualan — <?= e(Config::get('app_name')) ?></h1>
<div class="muted">Periode: <?= e(date('d M Y', strtotime($from))) ?> s/d <?= e(date('d M Y', strtotime($to))) ?></div>
<table>
  <tr><td>Total Omzet</td><td class="r"><?= rupiah($summary['revenue']) ?></td>
      <td>Transaksi</td><td class="r"><?= (int)$summary['count'] ?></td>
      <td>Item Terjual</td><td class="r"><?= (int)$summary['items'] ?></td></tr>
</table>
<h3>Rincian Transaksi</h3>
<table>
  <thead><tr><th>No. Pesanan</th><th>Tgl Bayar</th><th>Meja</th><th>Kasir</th><th class="r">Item</th><th class="r">Total</th></tr></thead>
  <tbody>
    <?php foreach ($orders as $o): ?>
      <tr><td><?= e($o['order_number']) ?></td><td><?= e(date('d/m/Y H:i', strtotime($o['paid_at']))) ?></td>
        <td><?= e($o['table_number'] ?? '-') ?></td><td><?= e($o['cashier'] ?? '-') ?></td>
        <td class="r"><?= (int)$o['item_count'] ?></td><td class="r"><?= rupiah($o['total']) ?></td></tr>
    <?php endforeach; ?>
    <tr class="sum"><td colspan="5" class="r">TOTAL</td><td class="r"><?= rupiah($summary['revenue']) ?></td></tr>
  </tbody>
</table>
