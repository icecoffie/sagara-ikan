<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<?php $o = fn($k, $d = '') => e($old[$k] ?? $d); ?>
<h1 class="shop-title">Checkout</h1>
<p class="shop-sub">Tanpa perlu login. Isi data singkat lalu pesananmu langsung diteruskan ke kasir.</p>

<div class="checkout-grid">
  <form method="post" action="<?= url('keranjang/place') ?>" class="form-card">
    <?= Csrf::field() ?>
    <div class="field">
      <label>Tipe Pesanan</label>
      <div class="otype">
        <label><input type="radio" name="order_type" value="dine_in" <?= ($old['order_type'] ?? 'dine_in') !== 'takeaway' ? 'checked' : '' ?> onchange="toggleTable()"><span><ion-icon name="restaurant-outline"></ion-icon> Makan di Tempat</span></label>
        <label><input type="radio" name="order_type" value="takeaway" <?= ($old['order_type'] ?? '') === 'takeaway' ? 'checked' : '' ?> onchange="toggleTable()"><span><ion-icon name="bag-handle-outline"></ion-icon> Bawa Pulang</span></label>
      </div>
    </div>
    <div class="field">
      <label for="customer_name">Nama</label>
      <input type="text" id="customer_name" name="customer_name" value="<?= $o('customer_name') ?>" required>
      <?php if (!empty($errors['customer_name'])): ?><span class="err"><?= e($errors['customer_name']) ?></span><?php endif; ?>
    </div>
    <div class="field">
      <label for="customer_phone">No. WhatsApp / Telepon (opsional)</label>
      <input type="text" id="customer_phone" name="customer_phone" value="<?= $o('customer_phone') ?>" placeholder="08xxxxxxxxxx">
    </div>
    <div class="field" id="tableField">
      <label for="table_id">Nomor Meja</label>
      <select id="table_id" name="table_id">
        <option value="">— Pilih meja —</option>
        <?php foreach ($tables as $t): ?>
          <option value="<?= $t['id'] ?>" <?= (string)($old['table_id'] ?? '') === (string)$t['id'] ? 'selected' : '' ?>><?= e($t['table_number']) ?> (<?= (int)$t['capacity'] ?> org)</option>
        <?php endforeach; ?>
      </select>
      <?php if (!empty($errors['table_id'])): ?><span class="err"><?= e($errors['table_id']) ?></span><?php endif; ?>
    </div>
    <div class="field">
      <label for="notes">Catatan (opsional)</label>
      <textarea id="notes" name="notes" rows="2" placeholder="mis. tidak pedas"><?= $o('notes') ?></textarea>
    </div>
    <div class="field">
      <label for="voucher_code">Kode Voucher (opsional)</label>
      <input type="text" id="voucher_code" name="voucher_code" value="<?= $o('voucher_code') ?>" placeholder="mis. SAGARA10" style="text-transform:uppercase;">
      <span style="font-size:12.5px;color:var(--muted);">Punya kode promo? Masukkan di sini.</span>
    </div>
    <button class="btn block" type="submit" style="width:100%;justify-content:center;"><ion-icon name="checkmark-circle-outline"></ion-icon> Kirim Pesanan</button>
  </form>

  <div class="form-card">
    <h3 style="font-family:var(--display);font-size:22px;margin-bottom:14px;">Ringkasan</h3>
    <?php foreach ($items as $it): ?>
      <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--line);font-size:14px;">
        <span><?= (int)$it['qty'] ?>× <?= e($it['name']) ?></span>
        <span><?= rupiah($it['subtotal']) ?></span>
      </div>
    <?php endforeach; ?>
    <div style="margin-top:14px;font-size:14px;color:var(--muted);">
      <div style="display:flex;justify-content:space-between;padding:4px 0;"><span>Subtotal</span><span><?= rupiah($total) ?></span></div>
      <div style="display:flex;justify-content:space-between;padding:4px 0;"><span>Pajak (<?= (int)($taxPct ?? 0) ?>%)</span><span><?= rupiah($tax ?? 0) ?></span></div>
      <div style="display:flex;justify-content:space-between;padding:4px 0;"><span>Service (<?= (int)($svcPct ?? 0) ?>%)</span><span><?= rupiah($service ?? 0) ?></span></div>
    </div>
    <div style="display:flex;justify-content:space-between;margin-top:8px;padding-top:10px;border-top:1px solid var(--line);font-weight:800;font-size:19px;">
      <span>Total</span><span><?= rupiah($total + ($tax ?? 0) + ($service ?? 0)) ?></span>
    </div>
    <p style="font-size:12px;color:var(--muted);margin-top:6px;">*Diskon voucher (jika ada) dihitung saat pesanan dibuat.</p>
    <a href="<?= url('keranjang') ?>" class="btn ghost" style="margin-top:14px;width:100%;justify-content:center;">Kembali ke Keranjang</a>
  </div>
</div>

<script>
function toggleTable(){
  var dine = document.querySelector('input[name=order_type][value=dine_in]').checked;
  document.getElementById('tableField').style.display = dine ? '' : 'none';
}
toggleTable();
</script>
