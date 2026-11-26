<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<?php $labels = ['open' => 'Menunggu diproses', 'paid' => 'Sudah dibayar', 'cancelled' => 'Dibatalkan']; ?>
<div class="confirm-box">
  <div class="ok-ic"><ion-icon name="checkmark-outline"></ion-icon></div>
  <h1 class="shop-title" style="font-size:28px;">Pesanan Diterima!</h1>
  <p class="shop-sub" style="margin-bottom:6px;">Tunjukkan nomor pesanan ini ke kasir untuk pembayaran.</p>
  <div class="ordno"><?= e($order['order_number']) ?></div>
  <span class="status-pill <?= e($order['status']) ?>"><?= e($labels[$order['status']] ?? $order['status']) ?></span>

  <div style="text-align:left;margin:24px auto 0;max-width:400px;">
    <div style="display:flex;justify-content:space-between;font-size:14px;color:var(--muted);margin-bottom:8px;">
      <span><?= $order['order_type'] === 'takeaway' ? 'Bawa Pulang' : 'Makan di Tempat' ?><?= $order['table_number'] ? ' · Meja ' . e($order['table_number']) : '' ?></span>
      <span>a.n. <?= e($order['customer_name']) ?></span>
    </div>
    <?php foreach ($order['items'] as $it): ?>
      <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--line);font-size:14px;">
        <span><?= (int)$it['qty'] ?>× <?= e($it['item_name']) ?></span>
        <span><?= rupiah($it['subtotal']) ?></span>
      </div>
    <?php endforeach; ?>
    <div style="margin-top:12px;font-size:13.5px;color:var(--muted);">
      <div style="display:flex;justify-content:space-between;padding:3px 0;"><span>Subtotal</span><span><?= rupiah($order['subtotal']) ?></span></div>
      <?php if ((float)$order['discount'] > 0): ?><div style="display:flex;justify-content:space-between;padding:3px 0;color:#157347;"><span>Diskon<?= $order['voucher_code'] ? ' ('.e($order['voucher_code']).')' : '' ?></span><span>&minus; <?= rupiah($order['discount']) ?></span></div><?php endif; ?>
      <div style="display:flex;justify-content:space-between;padding:3px 0;"><span>Pajak</span><span><?= rupiah($order['tax']) ?></span></div>
      <div style="display:flex;justify-content:space-between;padding:3px 0;"><span>Service</span><span><?= rupiah($order['service_charge']) ?></span></div>
    </div>
    <div style="display:flex;justify-content:space-between;margin-top:8px;padding-top:10px;border-top:1px solid var(--line);font-weight:800;font-size:19px;">
      <span>Total</span><span><?= rupiah($order['total']) ?></span>
    </div>
  </div>

  <div style="display:flex;gap:10px;justify-content:center;margin-top:26px;flex-wrap:wrap;">
    <a href="<?= url('pantau') ?>?order_number=<?= e(urlencode($order['order_number'])) ?>" class="btn ghost">Lacak Status</a>
    <a href="<?= url('') ?>#menu" class="btn">Pesan Lagi</a>
  </div>
</div>
