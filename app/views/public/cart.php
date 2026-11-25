<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<h1 class="shop-title">Keranjang Belanja</h1>
<p class="shop-sub">Atur jumlah pesananmu. Total terhitung otomatis.</p>

<div id="cartWrap">
  <?php if (!$items): ?>
    <div class="cart-card">
      <div class="cart-empty" id="cartEmpty">
        <ion-icon name="cart-outline"></ion-icon>
        <p style="margin:12px 0 18px;">Keranjangmu masih kosong.</p>
        <a href="<?= url('pesanan') ?>" class="btn">Lihat Menu</a>
      </div>
    </div>
  <?php else: ?>
    <div class="cart-card" id="cartCard" data-csrf="<?= e(Csrf::token()) ?>" data-url="<?= url('cart/qty') ?>">
      <?php foreach ($items as $it): ?>
        <div class="cart-row" data-id="<?= $it['id'] ?>">
          <img src="<?= e(menu_image_url($it['image'])) ?>" alt="<?= e($it['name']) ?>">
          <div class="ci-info">
            <div class="ci-name"><?= e($it['name']) ?></div>
            <div class="ci-price"><?= rupiah($it['price']) ?> / porsi</div>
          </div>
          <div class="qty-stepper">
            <button type="button" class="q-dec" aria-label="Kurangi">&minus;</button>
            <input class="q-val" value="<?= (int)$it['qty'] ?>" readonly>
            <button type="button" class="q-inc" aria-label="Tambah">+</button>
          </div>
          <div class="ci-sub"><?= rupiah($it['subtotal']) ?></div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="cart-summary" id="cartSummary">
      <a class="btn ghost" href="<?= url('pesanan') ?>"><ion-icon name="add-outline"></ion-icon> Tambah Menu</a>
      <div style="display:flex;align-items:center;gap:20px;">
        <div>Total: <span class="total" id="cartTotal"><?= rupiah($total) ?></span></div>
        <a class="btn" href="<?= url('cart/checkout') ?>">Checkout <ion-icon name="arrow-forward-outline"></ion-icon></a>
      </div>
    </div>
  <?php endif; ?>
</div>

<script>
(function () {
  var card = document.getElementById('cartCard');
  if (!card) return;
  var csrf = card.getAttribute('data-csrf');
  var base = card.getAttribute('data-url');

  function setBadge(n) {
    document.querySelectorAll('.cart-link').forEach(function (link) {
      var b = link.querySelector('.cart-badge');
      if (!b && n > 0) { b = document.createElement('span'); b.className = 'cart-badge'; link.appendChild(b); }
      if (b) { if (n > 0) b.textContent = n; else b.remove(); }
    });
  }
  function showEmpty() {
    document.getElementById('cartWrap').innerHTML =
      '<div class="cart-card"><div class="cart-empty"><ion-icon name="cart-outline"></ion-icon>' +
      '<p style="margin:12px 0 18px;">Keranjangmu masih kosong.</p>' +
      '<a href="<?= url('pesanan') ?>" class="btn">Lihat Menu</a></div></div>';
  }

  function change(row, op) {
    var id = row.getAttribute('data-id');
    row.querySelectorAll('button').forEach(function (b) { b.disabled = true; });
    var body = new URLSearchParams(); body.append('_csrf_token', csrf); body.append('op', op);
    fetch(base + '/' + id, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: body })
      .then(function (r) { return r.json(); })
      .then(function (d) {
        if (!d || !d.ok) return;
        setBadge(d.count);
        document.getElementById('cartTotal').textContent = d.total;
        if (d.removed) {
          row.style.transition = 'opacity .25s, transform .25s';
          row.style.opacity = '0'; row.style.transform = 'translateX(-12px)';
          setTimeout(function () { row.remove(); if (d.empty) showEmpty(); }, 250);
        } else {
          row.querySelector('.q-val').value = d.qty;
          row.querySelector('.ci-sub').textContent = d.subtotal;
          row.querySelectorAll('button').forEach(function (b) { b.disabled = false; });
        }
      })
      .catch(function () { location.reload(); });
  }

  card.addEventListener('click', function (e) {
    var dec = e.target.closest('.q-dec'); var inc = e.target.closest('.q-inc');
    if (dec) change(dec.closest('.cart-row'), 'dec');
    else if (inc) change(inc.closest('.cart-row'), 'inc');
  });
})();
</script>
