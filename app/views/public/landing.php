<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<?php $appMockup = asset('assets/' . rawurlencode('mockup app sagara ikan.png')); ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sagara Ikan — Rasa Laut Terbaik</title>
  <meta name="description" content="Sagara Ikan — restoran seafood modern dengan ikan segar pilihan: tuna, sarden, salmon, dan mackerel. Pesan online tanpa login.">
  <link rel="icon" href="<?= asset('assets/logo.png') ?>" type="image/png">
  <link rel="stylesheet" href="<?= asset('assets/css/modern.css') ?>">
  <noscript><style>.reveal{opacity:1!important;transform:none!important;}</style></noscript>
</head>
<body>

  <header class="nav" id="nav">
    <div class="wrap nav-inner">
      <a href="#beranda" class="nav-brand"><img src="<?= asset('assets/logo.png') ?>" alt="Sagara">Sagara<b>.</b></a>
      <nav class="nav-links" id="navLinks">
        <a href="#beranda">Beranda</a>
        <a href="<?= url('menu') ?>">Menu</a>
        <a href="#tentang">Tentang</a>
        <a href="#galeri">Ulasan</a>
        <a href="#kontak">Kontak</a>
        <a href="<?= url('login') ?>" class="btn nav-login-mobile">Login Staf</a>
      </nav>
      <div class="nav-right">
        <a href="<?= url('keranjang') ?>" class="cart-link"><ion-icon name="cart-outline"></ion-icon> <span class="cl-text">Keranjang</span><?php if (!empty($cartCount)): ?><span class="cart-badge"><?= (int)$cartCount ?></span><?php endif; ?></a>
        <a href="<?= url('login') ?>" class="btn nav-login-desktop">Login Staf</a>
        <button class="nav-toggle" id="navToggle" aria-label="Menu" onclick="toggleNav(this)"><ion-icon name="menu-outline"></ion-icon><ion-icon name="close-outline"></ion-icon></button>
      </div>
    </div>
  </header>

  <?php foreach (($flashes ?? []) as $ftype => $fmsg): ?>
    <div class="toast <?= e($ftype) ?>" id="toast"><?= e($fmsg) ?></div>
  <?php endforeach; ?>

  <!-- Hero -->
  <section id="beranda" class="hero">
    <div class="wrap hero-inner">
      <div class="hero-copy reveal">
        <span class="pill">Segar dari Laut Setiap Hari</span>
        <h1 style="margin-top:18px;">Nikmati <span class="hl">Rasa Laut</span><br>Terbaik di Kota</h1>
        <p>Ikan segar pilihan tuna, sarden, salmon, hingga mackerel, dimasak dengan teknik terbaik. Pesan online tanpa perlu login.</p>
        <div class="hero-actions">
          <a href="<?= url('menu') ?>" class="btn lg">Pesan Sekarang <ion-icon name="arrow-forward-outline"></ion-icon></a>
          <a href="#menu" class="btn ghost lg">Lihat Menu</a>
        </div>
        <div class="hero-trust">
          <div class="t"><b>4.9★</b><span>Rating pelanggan</span></div>
          <div class="t"><b>10rb+</b><span>Pelanggan puas</span></div>
          <div class="t"><b>30+</b><span>Menu seafood</span></div>
        </div>
      </div>
      <div class="hero-visual reveal d1">
        <div class="glow"></div>
        <img class="cans" src="<?= asset('assets/all1.png') ?>" alt="Hidangan Sagara Ikan">
        <div class="hero-chip one"><ion-icon name="fish-outline"></ion-icon><div><b>Ikan Segar</b><span>Setiap hari</span></div></div>
        <div class="hero-chip two"><ion-icon name="time-outline"></ion-icon><div><b>15–20 mnt</b><span>Siap disajikan</span></div></div>
      </div>
    </div>
  </section>

  <!-- Spesial -->
  <section class="sec wrap">
    <div class="specials">
      <div class="reveal">
        <span class="pill">Promo Hari Ini</span>
        <h2 class="sec-title">Tawaran spesial yang sayang dilewatkan</h2>
        <p class="sec-sub">Harga spesial untuk menu favorit, tersedia selama persediaan masih ada.</p>
        <a href="<?= url('menu') ?>" class="btn" style="margin-top:22px;">Pesan Sekarang</a>
      </div>
      <div class="specials-card reveal d1">
        <div class="row"><span class="n">Salmon Butter Lemon</span><span class="p">Rp110.000</span></div>
        <div class="row"><span class="n">Tuna Oseng</span><span class="p">Rp80.000</span></div>
        <div class="row"><span class="n">Mackerel Telur Asin</span><span class="p">Rp89.000</span></div>
        <div class="row"><span class="n">Es Teh Anget</span><span class="p">Gratis</span></div>
      </div>
    </div>
  </section>

  <!-- Tentang -->
  <section id="tentang" class="sec wrap" style="padding-top:0;">
    <div class="about">
      <div class="about-visual reveal">
        <img src="<?= asset('assets/4.png') ?>" alt="Dapur Sagara Ikan">
        <div class="about-stat"><ion-icon name="ribbon-outline"></ion-icon><div><b>15+ Thn</b><span>Pengalaman</span></div></div>
      </div>
      <div class="reveal d1">
        <span class="pill">Kenal Sagara</span>
        <h2 class="sec-title">Memadukan tradisi &amp; inovasi rasa laut</h2>
        <p class="sec-sub">
          Resto Sagara Ikan lahir dari kecintaan pada hasil laut nusantara. Berawal dari warung kecil di
          tepi pantai, kami tumbuh menjadi restoran seafood yang mengutamakan kesegaran, kebersihan, dan
          keramahan. Setiap pagi ikan tuna, sarden, salmon, dan mackerel kami pilih langsung dari nelayan lokal.
        </p>
        <ul class="about-list">
          <li><ion-icon name="checkmark-circle-outline"></ion-icon> Ikan segar pilihan, disortir setiap pagi.</li>
          <li><ion-icon name="checkmark-circle-outline"></ion-icon> Teknik masak presisi renyah di luar, juicy di dalam.</li>
          <li><ion-icon name="checkmark-circle-outline"></ion-icon> Resep warisan keluarga dengan sentuhan modern.</li>
          <li><ion-icon name="checkmark-circle-outline"></ion-icon> Suasana hangat, bersih, dan berkelas.</li>
        </ul>
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
          <a href="<?= url('tentang') ?>" class="btn">Selengkapnya <ion-icon name="arrow-forward-outline"></ion-icon></a>
          <a href="#menu" class="btn ghost">Jelajahi Menu</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Menu -->
  <section id="menu" class="sec wrap" style="padding-top:0;">
    <div class="menu-head reveal">
      <div class="sec-head">
        <span class="pill">Menu Unggulan</span>
        <h2 class="sec-title">Menu favoritmu menanti</h2>
      </div>
      <a href="<?= url('menu') ?>" class="btn ghost">Lihat Semua <ion-icon name="arrow-forward-outline"></ion-icon></a>
    </div>

    <ul class="cat-tabs reveal" id="catTabs">
      <li class="active" data-cat="all">Semua</li>
      <?php foreach ($categories as $c): ?>
        <li data-cat="<?= e($c['slug']) ?>"><?= e($c['name']) ?></li>
      <?php endforeach; ?>
    </ul>

    <div class="menu-grid">
      <?php if ($menu): foreach ($menu as $i => $m): ?>
        <article class="menu-item reveal <?= 'd' . (($i % 3) + 1) ?>" data-cat="<?= e($m['category_slug']) ?>">
          <a href="<?= url('menu/item/' . $m['id']) ?>" class="mi-img"><img src="<?= e(menu_image_url($m['image'])) ?>" alt="<?= e($m['name']) ?>"></a>
          <div class="mi-body">
            <a href="<?= url('menu/item/' . $m['id']) ?>" class="mi-title-link"><h3 class="title"><?= e($m['name']) ?></h3></a>
            <div class="order">
              <span class="price"><?= rupiah($m['price']) ?></span>
              <form method="post" action="<?= url('keranjang/add/' . $m['id']) ?>" class="order-form"><?= Csrf::field() ?><button type="submit" class="btn-menu">Pesan</button></form>
            </div>
          </div>
        </article>
      <?php endforeach; else: ?>
        <p class="sec-sub">Menu belum tersedia.</p>
      <?php endif; ?>
    </div>
  </section>

  <!-- Reviews -->
  <section id="galeri" class="sec wrap" style="padding-top:0;">
    <div class="reviews">
      <div class="sec-head reveal">
        <span class="pill">Ulasan Pelanggan</span>
        <h2 class="sec-title">Setiap kunjungan jadi kenangan</h2>
        <p class="sec-sub">Ribuan pelanggan mempercayai Sagara Ikan untuk momen spesial mereka.</p>
        <img class="reviews-photo" src="<?= asset('assets/review.png') ?>" alt="Hidangan Sagara Ikan">
      </div>
      <div class="review-cards">
        <div class="review reveal">
          <p>“Ikannya segar banget, bumbunya meresap, porsinya pas. Wajib dikunjungi!”</p>
          <div class="who"><span class="stars">★ 4.9</span><b>Mamat</b><small>Bekasi, ID</small></div>
        </div>
        <div class="review reveal d1">
          <p>“Pelayanan ramah, hidangan hangat dan cantik. Bahannya benar-benar segar.”</p>
          <div class="who"><span class="stars">★ 5.0</span><b>Sophia</b><small>Jakarta, ID</small></div>
        </div>
        <div class="review reveal d2">
          <p>“Rayakan anniversary di sini, dari dekorasi sampai dessert semuanya sempurna.”</p>
          <div class="who"><span class="stars">★ 4.8</span><b>Emily</b><small>Bandung, ID</small></div>
        </div>
      </div>
    </div>
  </section>

  <!-- App band -->
  <section class="wrap" style="margin-bottom:20px;">
    <div class="app-band reveal">
      <div class="av"><img src="<?= $appMockup ?>" alt="Aplikasi Sagara Ikan"></div>
      <div>
        <span class="pill light">Aplikasi Sagara</span>
        <h3 style="margin-top:14px;">Unduh Aplikasi Sagara Ikan</h3>
        <p>Pesan lebih cepat, simpan favorit, dan nikmati promo eksklusif hanya di aplikasi.</p>
        <div class="store-badges">
          <a href="#" class="store-badge"><ion-icon name="logo-google-playstore"></ion-icon><div><small>Tersedia di</small><b>Google Play</b></div></a>
          <a href="#" class="store-badge"><ion-icon name="logo-apple"></ion-icon><div><small>Unduh di</small><b>App Store</b></div></a>
        </div>
      </div>
    </div>
  </section>

  <!-- Kontak / Kritik & Saran -->
  <section id="kontak" class="sec wrap">
    <div class="contact">
      <div class="contact-card reveal">
        <span class="pill">Kritik &amp; Saran</span>
        <h2 class="sec-title">Hubungi Kami</h2>
        <p class="sec-sub">Ada pertanyaan, reservasi, keluhan, atau saran? Kirim pesan lewat formulir di bawah dan tim kami akan segera membalas.</p>
        <form class="contact-form" onsubmit="this.reset();alert('Terima kasih! Pesan Anda telah kami terima.');return false;">
          <div class="field"><label for="c-nama">Nama</label><input type="text" id="c-nama" placeholder="Nama lengkap" required></div>
          <div class="row2">
            <div class="field"><label for="c-email">Email</label><input type="email" id="c-email" placeholder="nama@email.com" required></div>
            <div class="field"><label for="c-telp">Telepon</label><input type="tel" id="c-telp" placeholder="08xxxxxxxxxx"></div>
          </div>
          <div class="field"><label for="c-pesan">Pesan</label><textarea id="c-pesan" rows="4" placeholder="Tulis pesan, keluhan, atau saran kamu..." required></textarea></div>
          <button type="submit" class="btn lg block"><ion-icon name="send-outline"></ion-icon> Kirim Pesan</button>
        </form>
      </div>
      <div class="contact-side reveal d1">
        <div class="info-line"><ion-icon name="location-outline"></ion-icon><div><b>Alamat</b><span>Jl. Samudera No. 16, Alaska</span></div></div>
        <div class="info-line"><ion-icon name="call-outline"></ion-icon><div><b>Telepon</b><span>+62 851-8773-2004</span></div></div>
        <div class="info-line"><ion-icon name="time-outline"></ion-icon><div><b>Jam Operasional</b><span>Senin–Sabtu, 10.00–22.00 WIB</span></div></div>
        <img class="map" src="<?= asset('assets/lokasi.png') ?>" alt="Peta lokasi Sagara Ikan">
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="foot">
    <div class="wrap foot-top">
      <div>
        <div class="brand-big">Sagara<b>.</b></div>
        <p>Destinasi seafood modern dengan sentuhan elegan. Rasa laut yang hangat dan berkesan.</p>
        <div class="foot-socials">
          <a href="https://instagram.com/msync.tech/" aria-label="Instagram"><ion-icon name="logo-instagram"></ion-icon></a>
          <a href="https://wa.me/6285187732004" aria-label="WhatsApp"><ion-icon name="logo-whatsapp"></ion-icon></a>
          <a href="https://x.com/msyncq" aria-label="X"><ion-icon name="logo-x"></ion-icon></a>
        </div>
      </div>
      <div><h4>Jelajahi</h4><ul>
        <li><a href="#beranda">Beranda</a></li>
        <li><a href="<?= url('menu') ?>">Menu &amp; Pesan</a></li>
        <li><a href="#tentang">Tentang</a></li>
        <li><a href="<?= url('pantau') ?>">Lacak Pesanan</a></li>
        <li><a href="<?= url('login') ?>">Login Staf</a></li>
      </ul></div>
      <div><h4>Kontak</h4><ul>
        <li>Jl. Samudera No. 16, Alaska</li>
        <li>+62 851-8773-2004</li>
        <li>Senin–Sabtu, 10.00–22.00</li>
      </ul></div>
      <div>
        <h4>Kabar &amp; promo ke inbox Anda</h4>
        <p style="margin-top:2px;">Menu baru &amp; promo spesial, tanpa spam.</p>
        <form class="newsletter" onsubmit="this.reset();alert('Terima kasih sudah berlangganan!');return false;">
          <input type="email" placeholder="Email Anda..." required>
          <button class="btn" type="submit">Langganan</button>
        </form>
      </div>
    </div>
    <div class="wrap foot-bottom">
      <span>&copy; <?= date('Y') ?> Sagara Ikan — Malika Shakila</span>
      <span>Kebijakan Privasi · Syarat &amp; Ketentuan</span>
    </div>
  </footer>

  <script>
    function toggleNav(btn){
      var links = document.getElementById('navLinks');
      var open = links.classList.toggle('open');
      btn.classList.toggle('active', open);
    }
    (function () {
      document.querySelectorAll('#navLinks a').forEach(function(a){
        a.addEventListener('click', function(){
          document.getElementById('navLinks').classList.remove('open');
          var t = document.getElementById('navToggle'); if (t) t.classList.remove('active');
        });
      });
      var tabs = document.querySelectorAll('#catTabs li');
      var items = document.querySelectorAll('.menu-grid .menu-item');
      tabs.forEach(function (t) { t.addEventListener('click', function () {
        tabs.forEach(function (x) { x.classList.remove('active'); }); t.classList.add('active');
        var c = t.getAttribute('data-cat');
        items.forEach(function (it) { it.style.display = (c === 'all' || it.getAttribute('data-cat') === c) ? '' : 'none'; });
      }); });
      var toast = document.getElementById('toast');
      if (toast) setTimeout(function(){ toast.classList.add('hide'); }, 3200);
      var nav = document.getElementById('nav');
      window.addEventListener('scroll', function () { nav.classList.toggle('scrolled', window.scrollY > 20); }, { passive: true });
      var rv = document.querySelectorAll('.reveal');
      if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (es) { es.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); } }); }, { threshold: .12, rootMargin: '0px 0px -40px 0px' });
        rv.forEach(function (el) { io.observe(el); });
      } else { rv.forEach(function (el) { el.classList.add('in'); }); }
    })();
  </script>
  <script src="<?= asset('assets/js/shop.js') ?>"></script>
  <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
