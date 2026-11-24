<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<h1 class="shop-title">Keranjang Belanja</h1>
<p class="shop-sub">Periksa pesananmu sebelum lanjut ke checkout.</p>

<?php if (!$items): ?>
  <div class="cart-card">
    <div class="cart-empty">
      <ion-icon name="cart-outline"></ion-icon>
      <p style="margin:12px 0 18px;">Keranjangmu masih kosong.</p>
      <a href="<?= url('') ?>#menu" class="btn">Lihat Menu</a>
    </div>
  </div>
<?php else: ?>
  <div class="cart-card">
    <form method="post" action="<?= url('cart/update') ?>">
      <?= Csrf::field() ?>
      <?php foreach ($items as $it): ?>
        <div class="cart-row">
          <img src="<?= e(menu_image_url($it['image'])) ?>" alt="<?= e($it['name']) ?>">
          <div>
            <div class="ci-name"><?= e($it['name']) ?></div>
            <div class="ci-price"><?= rupiah($it['price']) ?> / porsi</div>
          </div>
          <div class="qty-form">
            <input type="number" name="qty[<?= $it['id'] ?>]" value="<?= (int)$it['qty'] ?>" min="0" aria-label="Jumlah">
          </div>
          <div style="display:flex;align-items:center;gap:14px;">
            <span class="ci-sub"><?= rupiah($it['subtotal']) ?></span>
          </div>
        </div>
      <?php endforeach; ?>
      <div class="cart-summary">
        <div style="display:flex;gap:10px;">
          <button class="btn ghost" type="submit"><ion-icon name="refresh-outline"></ion-icon> Perbarui</button>
          <a class="btn ghost" href="<?= url('') ?>#menu">Tambah Menu</a>
        </div>
        <div style="display:flex;align-items:center;gap:20px;">
          <div>Total: <span class="total"><?= rupiah($total) ?></span></div>
          <a class="btn" href="<?= url('cart/checkout') ?>">Checkout <ion-icon name="arrow-forward-outline"></ion-icon></a>
        </div>
      </div>
    </form>
  </div>
  <div style="margin-top:14px;display:flex;gap:10px;flex-wrap:wrap;">
    <?php foreach ($items as $it): ?>
      <form method="post" action="<?= url('cart/remove/' . $it['id']) ?>">
        <?= Csrf::field() ?>
        <button class="btn ghost" type="submit" style="font-size:12px;padding:7px 12px;"><ion-icon name="trash-outline"></ion-icon> Hapus <?= e($it['name']) ?></button>
      </form>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
