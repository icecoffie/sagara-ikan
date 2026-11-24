<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<?php
$ob = ['open' => 'amber', 'paid' => 'green', 'cancelled' => 'red'];
$isOpen = $order['status'] === 'open';
?>
<div class="panel-head" style="background:#fff;border:1px solid var(--clr-border);border-radius:10px;margin-bottom:20px;">
  <div>
    <h2 style="margin-bottom:4px;"><?= e($order['order_number']) ?>
      <span class="badge <?= $ob[$order['status']] ?? 'gray' ?>" style="vertical-align:middle;"><?= e(ucfirst($order['status'])) ?></span>
    </h2>
    <div style="color:var(--clr-muted);font-size:14px;">
      Meja <?= e($order['table_number'] ?? '-') ?> · Kasir <?= e($order['cashier'] ?? '-') ?> · <?= e(date('d M Y, H:i', strtotime($order['created_at']))) ?>
    </div>
  </div>
  <div class="toolbar">
    <a href="<?= url('order') ?>" class="btn secondary sm"><ion-icon name="arrow-back-outline"></ion-icon> Daftar</a>
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
        <tfoot><tr style="font-weight:700;background:#f6f9fd;"><td colspan="3" style="text-align:right;">Total</td><td colspan="<?= ($isOpen && can('order.update')) ? 2 : 1 ?>"><?= rupiah($order['total']) ?></td></tr></tfoot>
      </table></div>
    </div>

    <?php if ($isOpen && ($order['total'] > 0) && (can('order.pay') || can('order.update'))): ?>
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
              <div class="mp-price"><?= rupiah($m['price']) ?></div>
            </button>
          </form>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>

<script>
function filterMenu(q){
  q = q.toLowerCase();
  document.querySelectorAll('.mp-item').forEach(function(el){
    el.style.display = el.getAttribute('data-name').indexOf(q) > -1 ? '' : 'none';
  });
}
</script>
