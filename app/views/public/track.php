<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<?php $labels = ['open' => 'Menunggu diproses', 'paid' => 'Sudah dibayar', 'cancelled' => 'Dibatalkan']; ?>
<h1 class="shop-title">Lacak Pesanan</h1>
<p class="shop-sub">Masukkan nomor pesanan untuk melihat statusnya.</p>

<form method="post" action="<?= url('pantau') ?>" class="form-card" style="max-width:520px;display:flex;gap:10px;align-items:flex-end;">
  <?= Csrf::field() ?>
  <div class="field" style="flex:1;margin:0;">
    <label for="order_number">Nomor Pesanan</label>
    <input type="text" id="order_number" name="order_number" value="<?= e($_GET['order_number'] ?? '') ?>" placeholder="ORD-XXXXXXXX-XXXXX" required>
  </div>
  <button class="btn" type="submit"><ion-icon name="search-outline"></ion-icon> Cek</button>
</form>

<?php if ($searched): ?>
  <?php if ($order): ?>
    <div class="confirm-box" style="margin-top:26px;">
      <div class="ordno"><?= e($order['order_number']) ?></div>
      <span class="status-pill <?= e($order['status']) ?>"><?= e($labels[$order['status']] ?? $order['status']) ?></span>
      <div style="text-align:left;margin:24px auto 0;max-width:400px;">
        <div style="font-size:14px;color:var(--muted);margin-bottom:8px;">
          <?= $order['order_type'] === 'takeaway' ? 'Bawa Pulang' : 'Makan di Tempat' ?><?= $order['table_number'] ? ' · Meja ' . e($order['table_number']) : '' ?> · a.n. <?= e($order['customer_name']) ?>
        </div>
        <?php foreach ($order['items'] as $it): ?>
          <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--line);font-size:14px;">
            <span><?= (int)$it['qty'] ?>× <?= e($it['item_name']) ?></span><span><?= rupiah($it['subtotal']) ?></span>
          </div>
        <?php endforeach; ?>
        <div style="display:flex;justify-content:space-between;margin-top:14px;font-weight:700;font-size:18px;">
          <span>Total</span><span><?= rupiah($order['total']) ?></span>
        </div>
      </div>
    </div>
  <?php else: ?>
    <div class="site-alert error" style="margin-top:22px;">Pesanan tidak ditemukan. Periksa kembali nomor pesananmu.</div>
  <?php endif; ?>
<?php endif; ?>
