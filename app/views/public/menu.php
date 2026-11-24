<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<div class="shop-head">
  <span class="pill">Jelajahi Menu</span>
  <h1 class="sec-title">Menu &amp; Pesan</h1>
  <p class="sec-sub">Pilih hidangan favoritmu, masukkan ke keranjang, lalu checkout tanpa perlu login.</p>
</div>

<ul class="cat-tabs" id="catTabs">
  <li class="active" data-cat="all">Semua</li>
  <?php foreach ($categories as $c): ?>
    <li data-cat="<?= e($c['slug']) ?>"><?= e($c['name']) ?></li>
  <?php endforeach; ?>
</ul>

<div class="menu-grid">
  <?php if ($items): foreach ($items as $m): ?>
    <?php $slug = Database::scalar('SELECT slug FROM categories WHERE id=?', [$m['category_id']]); ?>
    <article class="menu-item" data-cat="<?= e($slug) ?>">
      <a href="<?= url('pesanan/item/' . $m['id']) ?>" class="mi-img"><img src="<?= e(menu_image_url($m['image'])) ?>" alt="<?= e($m['name']) ?>"></a>
      <div class="mi-body">
        <a href="<?= url('pesanan/item/' . $m['id']) ?>" class="mi-title-link"><h3 class="title"><?= e($m['name']) ?></h3></a>
        <p class="mi-desc"><?= e(mb_strimwidth((string)$m['description'], 0, 70, '…')) ?></p>
        <div class="order">
          <span class="price"><?= rupiah($m['price']) ?></span>
          <form method="post" action="<?= url('cart/add/' . $m['id']) ?>" class="order-form">
            <?= Csrf::field() ?>
            <button type="submit" class="btn-menu">Tambah</button>
          </form>
        </div>
      </div>
    </article>
  <?php endforeach; else: ?>
    <p class="sec-sub">Menu belum tersedia.</p>
  <?php endif; ?>
</div>

<script>
(function(){
  var tabs=document.querySelectorAll('#catTabs li');
  var items=document.querySelectorAll('.menu-grid .menu-item');
  tabs.forEach(function(t){ t.addEventListener('click',function(){
    tabs.forEach(function(x){x.classList.remove('active');}); t.classList.add('active');
    var c=t.getAttribute('data-cat');
    items.forEach(function(it){ it.style.display=(c==='all'||it.getAttribute('data-cat')===c)?'':'none'; });
  });});
})();
</script>
