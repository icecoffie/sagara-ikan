<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<a href="<?= url('pesanan') ?>" class="back-link"><ion-icon name="arrow-back-outline"></ion-icon> Kembali ke Menu</a>

<div class="item-detail">
  <div class="item-photo"><img src="<?= e(menu_image_url($item['image'])) ?>" alt="<?= e($item['name']) ?>"></div>
  <div class="item-info">
    <span class="pill"><?= e($item['category_name']) ?></span>
    <h1 class="item-name"><?= e($item['name']) ?></h1>
    <div class="item-price"><?= rupiah($item['price']) ?></div>
    <p class="item-desc"><?= e($item['description'] ?: 'Hidangan laut segar pilihan, dimasak dengan bumbu khas Sagara Ikan.') ?></p>

    <form method="post" action="<?= url('cart/add/' . $item['id']) ?>" class="item-order">
      <?= Csrf::field() ?>
      <input type="hidden" name="back" value="cart">
      <div class="qty-stepper">
        <button type="button" onclick="stepQty(-1)">&minus;</button>
        <input type="number" id="qty" name="qty" value="1" min="1" max="99">
        <button type="button" onclick="stepQty(1)">+</button>
      </div>
      <button type="submit" class="btn btn-lg"><ion-icon name="cart-outline"></ion-icon> Tambah ke Keranjang</button>
    </form>

    <div class="item-meta">
      <div><ion-icon name="fish-outline"></ion-icon> Ikan segar harian</div>
      <div><ion-icon name="time-outline"></ion-icon> Siap 15–20 menit</div>
      <div><ion-icon name="bag-handle-outline"></ion-icon> Dine-in / bawa pulang</div>
    </div>
  </div>
</div>

<?php if ($related): ?>
<div class="related">
  <h2 class="sec-title" style="font-size:28px;">Mungkin kamu suka</h2>
  <div class="menu-grid">
    <?php foreach ($related as $m): ?>
      <article class="menu-item">
        <a href="<?= url('pesanan/item/' . $m['id']) ?>" class="mi-img"><img src="<?= e(menu_image_url($m['image'])) ?>" alt="<?= e($m['name']) ?>"></a>
        <div class="mi-body">
          <a href="<?= url('pesanan/item/' . $m['id']) ?>" class="mi-title-link"><h3 class="title"><?= e($m['name']) ?></h3></a>
          <div class="order">
            <span class="price"><?= rupiah($m['price']) ?></span>
            <form method="post" action="<?= url('cart/add/' . $m['id']) ?>" class="order-form"><?= Csrf::field() ?><button class="btn-menu">Tambah</button></form>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<script>
function stepQty(d){ var i=document.getElementById('qty'); var v=parseInt(i.value||'1',10)+d; if(v<1)v=1; if(v>99)v=99; i.value=v; }
</script>
