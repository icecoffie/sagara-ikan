<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<?php
$ob = ['open' => 'amber', 'paid' => 'green', 'cancelled' => 'red'];
$kb = ['queued' => 'amber', 'cooking' => 'blue', 'ready' => 'green', 'served' => 'gray'];
$kl = ['queued' => 'Antre', 'cooking' => 'Dimasak', 'ready' => 'Siap', 'served' => 'Disajikan'];
$isOpen = $order['status'] === 'open';
$otypes = ['dine_in' => 'Makan di tempat', 'takeaway' => 'Bawa pulang'];
$taxPct = (float) Config::get('tax_percent', 0);
$svcPct = (float) Config::get('service_percent', 0);
?>
<div class="panel-head" style="background:#fff;border:1px solid var(--clr-border);border-radius:10px;margin-bottom:20px;">
  <div>
    <h2 style="margin-bottom:4px;"><?= e($order['order_number']) ?>
      <span class="badge <?= $ob[$order['status']] ?? 'gray' ?>" style="vertical-align:middle;"><?= e(ucfirst($order['status'])) ?></span>
      <span class="badge <?= $kb[$order['kitchen_status']] ?? 'gray' ?>" style="vertical-align:middle;">Dapur: <?= e($kl[$order['kitchen_status']] ?? $order['kitchen_status']) ?></span>
    </h2>
    <div style="color:var(--clr-muted);font-size:14px;">
      <span class="badge <?= ($order['source'] ?? 'staff') === 'customer' ? 'blue' : 'gray' ?>" style="font-size:11px;"><?= ($order['source'] ?? 'staff') === 'customer' ? 'Pesanan Online' : 'Staf' ?></span>
      · <?= e($otypes[$order['order_type'] ?? 'dine_in'] ?? '-') ?>
      · Meja <?= e($order['table_number'] ?? '-') ?>
      <?php if (($order['source'] ?? 'staff') === 'customer'): ?>
        · Pelanggan: <b><?= e($order['customer_name']) ?></b><?= $order['customer_phone'] ? ' (' . e($order['customer_phone']) . ')' : '' ?>
      <?php else: ?>
        · Kasir <?= e($order['cashier'] ?? '-') ?>
      <?php endif; ?>
      · <?= e(date('d M Y, H:i', strtotime($order['created_at']))) ?>
    </div>
  </div>
  <div class="toolbar">
    <a href="<?= url('order') ?>" class="btn secondary sm"><ion-icon name="arrow-back-outline"></ion-icon> Daftar</a>
    <?php if (can('kitchen.view')): ?><a href="<?= url('kitchen') ?>" class="btn secondary sm"><ion-icon name="flame-outline"></ion-icon> Dapur</a><?php endif; ?>
    <?php if ($order['status'] === 'paid'): ?>
      <a href="<?= url('order/receipt/' . $order['id']) ?>" target="_blank" class="btn sm"><ion-icon name="print-outline"></ion-icon> Cetak Struk</a>
    <?php endif; ?>
  </div>
</div>

<div class="order-layout">
  <div class="panel">
    <div class="panel-head"><h2>Item Pesanan</h2></div>
    <div class="panel-body" style="padding:0;">
      <div class="table-wrap"><table class="data">
        <thead><tr><th>Item</th><th>Harga</th><th>Qty</th><th>Subtotal</th><?php if ($isOpen && can('order.update')): ?><th></th><?php endif; ?></tr></thead>
        <tbody>
          <?php if (!empty($order['items'])): foreach ($order['items'] as $it): ?>
            <tr>
              <td><b><?= e($it['item_name']) ?></b></td>
              <td><?= rupiah($it['price']) ?></td>
              <td><?= (int)$it['qty'] ?></td>
              <td><?= rupiah($it['subtotal']) ?></td>
              <?php if ($isOpen && can('order.update')): ?>
                <td class="actions"><form method="post" action="<?= url('order/removeItem/' . $it['id']) ?>" onsubmit="return confirm('Hapus item?')"><?= Csrf::field() ?><button class="btn danger sm"><ion-icon name="close-outline"></ion-icon></button></form></td>
              <?php endif; ?>
            </tr>
          <?php endforeach; else: ?>
            <tr><td colspan="5" class="empty">Belum ada item.</td></tr>
          <?php endif; ?>
        </tbody>
      </table></div>
    </div>

    <div class="panel-body" style="border-top:1px solid var(--clr-border);">
      <div class="bill">
        <div><span>Subtotal</span><span><?= rupiah($order['subtotal']) ?></span></div>
        <?php if ((float)$order['discount'] > 0): ?>
          <div class="disc"><span>Diskon<?= $order['voucher_code'] ? ' ('.e($order['voucher_code']).')' : '' ?></span><span>&minus; <?= rupiah($order['discount']) ?></span></div>
        <?php endif; ?>
        <div><span>Pajak (<?= (int)$taxPct ?>%)</span><span><?= rupiah($order['tax']) ?></span></div>
        <div><span>Service (<?= (int)$svcPct ?>%)</span><span><?= rupiah($order['service_charge']) ?></span></div>
        <div class="grand"><span>Total</span><span><?= rupiah($order['total']) ?></span></div>
        <div class="split" id="splitRow" style="display:none;"><span>Per orang (<span id="splitN">2</span> org)</span><span id="splitVal">-</span></div>
      </div>
      <?php if ((float)$order['total'] > 0): ?>
      <div style="display:flex;align-items:center;gap:8px;margin-top:10px;font-size:14px;color:var(--clr-muted);">
        <ion-icon name="people-outline"></ion-icon> Split bill:
        <input type="number" id="splitInput" min="1" value="2" style="width:64px;padding:5px 8px;border:1px solid var(--clr-border);border-radius:7px;" oninput="doSplit()">
        <button class="btn secondary sm" type="button" onclick="doSplit(true)">Bagi</button>
      </div>
      <?php endif; ?>
    </div>

    <?php if ($isOpen && can('order.update')): ?>
    <div class="panel-body" style="border-top:1px solid var(--clr-border);display:grid;grid-template-columns:1fr 1fr;gap:12px;">
      <form method="post" action="<?= url('order/voucher/' . $order['id']) ?>">
        <?= Csrf::field() ?>
        <label style="font-size:13px;font-weight:600;">Kode Voucher</label>
        <div style="display:flex;gap:6px;margin-top:4px;">
          <input type="text" name="voucher_code" value="<?= e($order['voucher_code']) ?>" placeholder="SAGARA10" style="flex:1;padding:8px 10px;border:1px solid var(--clr-border);border-radius:7px;text-transform:uppercase;">
          <button class="btn sm" type="submit">Pakai</button>
        </div>
      </form>
      <form method="post" action="<?= url('order/discount/' . $order['id']) ?>">
        <?= Csrf::field() ?>
        <label style="font-size:13px;font-weight:600;">Diskon Manual (Rp)</label>
        <div style="display:flex;gap:6px;margin-top:4px;">
          <input type="number" name="discount" min="0" step="1000" value="<?= $order['voucher_code'] ? 0 : (int)$order['discount'] ?>" style="flex:1;padding:8px 10px;border:1px solid var(--clr-border);border-radius:7px;">
          <button class="btn secondary sm" type="submit">Set</button>
        </div>
      </form>
    </div>
    <?php endif; ?>

    <?php if ($isOpen && ((float)$order['total'] > 0) && (can('order.pay') || can('order.update'))): ?>
    <div class="panel-body" style="display:flex;gap:10px;border-top:1px solid var(--clr-border);">
      <?php if (can('order.pay')): ?>
        <form method="post" action="<?= url('order/pay/' . $order['id']) ?>" onsubmit="return confirm('Proses pembayaran?')"><?= Csrf::field() ?><button class="btn success"><ion-icon name="cash-outline"></ion-icon> Bayar (<?= rupiah($order['total']) ?>)</button></form>
      <?php endif; ?>
      <?php if (can('order.update')): ?>
        <form method="post" action="<?= url('order/cancel/' . $order['id']) ?>" onsubmit="return confirm('Batalkan pesanan ini?')"><?= Csrf::field() ?><button class="btn secondary"><ion-icon name="close-circle-outline"></ion-icon> Batalkan</button></form>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>

  <?php if ($isOpen && can('order.update')): ?>
  <div class="panel">
    <div class="panel-head"><h2>Tambah Item</h2></div>
    <div class="panel-body">
      <input type="text" id="menuSearch" placeholder="Cari menu..." style="width:100%;padding:9px 12px;border:1px solid var(--clr-border);border-radius:8px;margin-bottom:12px;" onkeyup="filterMenu(this.value)">
      <div class="menu-pick">
        <?php foreach ($menu as $m): ?>
          <form method="post" action="<?= url('order/addItem/' . $order['id']) ?>" class="mp-item" data-name="<?= e(strtolower($m['name'])) ?>">
            <?= Csrf::field() ?>
            <input type="hidden" name="menu_item_id" value="<?= $m['id'] ?>">
            <input type="hidden" name="qty" value="1">
            <button type="submit">
              <div class="mp-name"><?= e($m['name']) ?></div>
              <div class="mp-price"><?= rupiah($m['price']) ?><?= $m['stock'] !== null ? ' · stok '.(int)$m['stock'] : '' ?></div>
            </button>
          </form>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>

<script>
var GRAND = <?= (float)$order['total'] ?>;
function filterMenu(q){ q=q.toLowerCase(); document.querySelectorAll('.mp-item').forEach(function(el){ el.style.display = el.getAttribute('data-name').indexOf(q)>-1?'':'none'; }); }
function doSplit(force){
  var n = Math.max(1, parseInt(document.getElementById('splitInput').value||'1',10));
  var per = Math.ceil(GRAND / n);
  document.getElementById('splitN').textContent = n;
  document.getElementById('splitVal').textContent = 'Rp' + per.toLocaleString('id-ID');
  document.getElementById('splitRow').style.display = '';
}
</script>
