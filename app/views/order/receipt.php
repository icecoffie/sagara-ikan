<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Struk <?= e($order['order_number']) ?></title>
  <style>
    *{margin:0;padding:0;box-sizing:border-box;font-family:'Courier New',monospace;}
    body{background:#eef4fb;padding:24px;color:#111;}
    .receipt{background:#fff;max-width:340px;margin:0 auto;padding:24px;border-radius:8px;box-shadow:0 6px 20px rgba(0,0,0,.12);}
    .center{text-align:center;}
    .brand{font-size:20px;font-weight:bold;letter-spacing:1px;}
    .muted{color:#555;font-size:12px;}
    hr{border:none;border-top:1px dashed #999;margin:12px 0;}
    table{width:100%;border-collapse:collapse;font-size:13px;}
    td{padding:3px 0;vertical-align:top;}
    .r{text-align:right;}
    .total{font-size:16px;font-weight:bold;}
    .meta{font-size:12px;margin:3px 0;}
    .actions{max-width:340px;margin:16px auto 0;display:flex;gap:8px;}
    .btn{flex:1;text-align:center;padding:11px;border-radius:8px;border:0;cursor:pointer;font-size:14px;font-weight:bold;text-decoration:none;font-family:sans-serif;}
    .btn.print{background:#2aa7ff;color:#fff;}
    .btn.back{background:#e7eefa;color:#0b2447;}
    @media print{body{background:#fff;padding:0;}.receipt{box-shadow:none;max-width:100%;}.no-print{display:none;}}
  </style>
</head>
<body>
  <div class="receipt">
    <div class="center">
      <div class="brand"><?= e($appName) ?></div>
      <div class="muted">Jl. Samudera No. 16, Alaska</div>
      <div class="muted">+62 851-8773-2004</div>
    </div>
    <hr>
    <div class="meta">No. &nbsp;: <?= e($order['order_number']) ?></div>
    <div class="meta">Tgl &nbsp;: <?= e(date('d/m/Y H:i', strtotime($order['paid_at'] ?: $order['created_at']))) ?></div>
    <div class="meta">Tipe &nbsp;: <?= $order['order_type'] === 'takeaway' ? 'Bawa Pulang' : 'Makan di Tempat' ?></div>
    <div class="meta">Meja &nbsp;: <?= e($order['table_number'] ?? '-') ?></div>
    <?php if (!empty($order['customer_name'])): ?><div class="meta">Tamu &nbsp;: <?= e($order['customer_name']) ?></div><?php endif; ?>
    <div class="meta">Kasir : <?= e($order['cashier'] ?? ($order['source'] === 'customer' ? 'Online' : '-')) ?></div>
    <div class="meta">Status: <?= e(ucfirst($order['status'])) ?></div>
    <hr>
    <table>
      <?php foreach ($order['items'] as $it): ?>
        <tr>
          <td><?= e($it['item_name']) ?><br><span class="muted"><?= (int)$it['qty'] ?> x <?= rupiah($it['price']) ?></span></td>
          <td class="r"><?= rupiah($it['subtotal']) ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
    <hr>
    <table>
      <tr class="total"><td>TOTAL</td><td class="r"><?= rupiah($order['total']) ?></td></tr>
    </table>
    <hr>
    <div class="center muted">Terima kasih atas kunjungan Anda!<br>Sampai jumpa lagi di <?= e($appName) ?></div>
  </div>
  <div class="actions no-print">
    <a href="<?= url('order/show/' . $order['id']) ?>" class="btn back">Kembali</a>
    <button class="btn print" onclick="window.print()">Cetak</button>
  </div>
</body>
</html>
