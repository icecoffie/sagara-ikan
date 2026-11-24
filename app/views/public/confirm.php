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
    <div style="display:flex;justify-content:space-between;margin-top:14px;font-weight:700;font-size:18px;">
      <span>Total</span><span><?= rupiah($order['total']) ?></span>
    </div>
  </div>

  <div style="display:flex;gap:10px;justify-content:center;margin-top:26px;flex-wrap:wrap;">
    <a href="<?= url('cart/track') ?>?order_number=<?= e(urlencode($order['order_number'])) ?>" class="btn ghost">Lacak Status</a>
    <a href="<?= url('') ?>#menu" class="btn">Pesan Lagi</a>
  </div>
</div>
