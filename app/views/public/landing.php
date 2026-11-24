<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<?php
$galleryImages = ['tuna.png', 'salmon.png', 'sarden.png', 'mackerel1.png', 'all.png', '3.png'];
$dishImage = function (array $m): string {
    if (!empty($m['image'])) {
        if (is_file(ROOT_PATH . '/public_html/assets/uploads/' . $m['image'])) {
            return asset('assets/uploads/' . $m['image']);
        }
        if (is_file(ROOT_PATH . '/public_html/assets/' . $m['image'])) {
            return asset('assets/' . $m['image']);
        }
    }
    return asset('assets/all.png');
};
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sagara Ikan — Rasakan Bahasa Rasa Laut</title>
  <meta name="description" content="Sagara Ikan — restoran seafood dengan ikan segar pilihan: tuna, sarden, salmon, dan mackerel. Rasa premium, suasana elegan.">
  <link rel="icon" href="<?= asset('assets/logo.png') ?>" type="image/png">
  <link rel="stylesheet" href="<?= asset('assets/css/landing.css') ?>">
</head>
<body>

  <header class="nav">
    <div class="wrap nav-inner">
      <nav class="nav-links" id="navLinks">
        <a href="#beranda">Beranda</a>
        <a href="#menu">Menu</a>
        <a href="#tentang">Tentang</a>
        <a href="#galeri">Galeri</a>
      </nav>
      <a href="#beranda" class="nav-brand"><img src="<?= asset('assets/logo.png') ?>" alt="Sagara">Sagara<b>.</b></a>
      <div class="nav-right">
        <a href="#kontak">Kontak</a>
        <a href="<?= url('login') ?>" class="btn" style="padding:11px 22px;">Login</a>
        <button class="nav-toggle" onclick="document.getElementById('navLinks').classList.toggle('open')"><ion-icon name="menu-outline"></ion-icon></button>
      </div>
    </div>
  </header>

  <section id="beranda" class="hero wrap">
    <span class="pill">Segar dari Laut, Disajikan dengan Cinta</span>
    <h1>Rasakan <em>Bahasa</em><br>Rasa Laut</h1>
    <div class="hero-media">
      <a href="#menu" class="hero-cta"><ion-icon name="arrow-down-outline"></ion-icon>Jelajahi<br>Menu</a>
      <img src="<?= asset('assets/all1.png') ?>" alt="Hidangan Sagara Ikan">
    </div>
  </section>

  <section id="tentang" class="sec wrap">
    <div class="about-grid">
      <div>
        <p class="lead">Memadukan <b>warisan rasa</b> laut nusantara</p>
        <span class="pill">Kenal Sagara</span>
        <h2 class="sec-title">Memadukan tradisi &amp; inovasi untuk pengalaman bersantap tak terlupakan</h2>
        <p class="sec-sub">Dari pemilihan ikan segar setiap pagi hingga teknik masak presisi, kami menjaga tiap detail agar Anda merasakan yang terbaik renyah di luar, juicy di dalam.</p>
        <div class="about-actions">
          <a href="#menu" class="btn">Cerita Kami</a>
          <div class="avatars">
            <img src="<?= asset('assets/client.jpeg') ?>" alt="">
            <img src="<?= asset('assets/client.jpeg') ?>" alt="">
            <img src="<?= asset('assets/client.jpeg') ?>" alt="">
          </div>
          <div><b>Pelanggan Puas</b>Ribuan ulasan dari tamu kami</div>
        </div>
      </div>
      <div class="about-media"><img src="<?= asset('assets/4.png') ?>" alt="Suasana Sagara Ikan"></div>
    </div>
  </section>

  <section id="menu" class="sec wrap" style="padding-top:0;">
    <div class="menu-top">
      <div class="sec-head">
        <span class="pill">Jelajahi Menu</span>
        <h2 class="sec-title">Menu favoritmu menanti</h2>
      </div>
      <p class="sec-sub" style="max-width:360px;">Pilih dari beragam olahan ikan segar kami tuna, salmon, sarden, hingga mackerel.</p>
    </div>

    <ul class="cat-tabs" id="catTabs">
      <li class="active" data-cat="all">Semua</li>
      <?php foreach ($categories as $c): ?>
        <li data-cat="<?= e($c['slug']) ?>"><?= e($c['name']) ?></li>
      <?php endforeach; ?>
    </ul>

    <div class="menu-grid">
      <?php if ($menu): foreach ($menu as $m): ?>
        <article class="dish" data-cat="<?= e($m['category_slug']) ?>">
          <img src="<?= e($dishImage($m)) ?>" alt="<?= e($m['name']) ?>">
          <div class="body">
            <div class="tag"><?= e($m['category_name']) ?></div>
            <h3><?= e($m['name']) ?></h3>
            <div class="row">
              <span class="price"><?= rupiah($m['price']) ?></span>
              <a href="<?= url('login') ?>" class="order">Pesan <ion-icon name="arrow-forward-outline"></ion-icon></a>
            </div>
          </div>
        </article>
      <?php endforeach; else: ?>
        <p class="sec-sub">Menu belum tersedia. Silakan hubungi admin untuk mengisi data menu.</p>
      <?php endif; ?>
    </div>
  </section>

  <section class="wrap">
    <div class="dark reserve">
      <div class="reserve-photos">
        <img src="<?= asset('assets/review.png') ?>" alt="">
        <img src="<?= asset('assets/salmon1.png') ?>" alt="">
        <img src="<?= asset('assets/tuna1.png') ?>" alt="">
      </div>
      <div>
        <span class="pill on-dark">Reservasi Meja</span>
        <h2>Pesan tempatmu! kebahagiaan bersantap yang sempurna</h2>
        <p class="sec-sub">Amankan meja favorit Anda untuk momen spesial bersama keluarga dan sahabat.</p>
        <a href="<?= url('login') ?>" class="btn" style="margin-top:26px;">Reservasi Sekarang</a>
      </div>
    </div>
  </section>

  <section id="galeri" class="gallery wrap">
    <span class="pill">Sajian untuk Mata</span>
    <h2 class="sec-title" style="margin-top:16px;">Rasakan suasananya lewat galeri kami</h2>
    <div class="polaroids">
      <?php
        $labels = ['Tuna', 'Salmon', 'Sarden', 'Mackerel', 'Paket Komplit', 'Spesial'];
        foreach ($galleryImages as $i => $img):
      ?>
        <figure class="polaroid">
          <img src="<?= asset('assets/' . $img) ?>" alt="<?= e($labels[$i]) ?>">
          <span><?= e($labels[$i]) ?></span>
        </figure>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="sec wrap" style="padding-top:0;">
    <div class="reviews-grid">
      <div class="sec-head">
        <span class="pill">Ulasan Pelanggan</span>
        <h2 class="sec-title">Setiap kunjungan jadi kenangan indah</h2>
      </div>
      <div class="review-cards">
        <div class="review">
          <p>“Ikannya segar banget, bumbunya meresap, dan porsinya pas. Wajib dikunjungi buat pecinta ikan!”</p>
          <div class="who"><span class="stars">★ 4.9</span><b>Mamat</b><small>Bekasi, ID</small></div>
        </div>
        <div class="review dark-card">
          <p>“Pelayanan ramah, hidangan disajikan hangat dan cantik. Bahan-bahannya benar-benar segar.”</p>
          <div class="who"><span class="stars">★ 5.0</span><b>Sophia</b><small>Jakarta, ID</small></div>
        </div>
        <div class="review">
          <p>“Kami rayakan anniversary di sini, dari dekorasi sampai dessert semuanya sempurna.”</p>
          <div class="who"><span class="stars">★ 4.8</span><b>Emily</b><small>Bandung, ID</small></div>
        </div>
      </div>
    </div>
  </section>

  <footer id="kontak" class="foot">
    <div class="wrap foot-top">
      <div>
        <div class="brand-big">Sagara<b>.</b></div>
        <p>Destinasi seafood dengan sentuhan elegan. Kami meramu rasa laut menjadi pengalaman bersantap yang hangat dan berkesan.</p>
        <div class="foot-socials">
          <a href="https://instagram.com/msync.tech/" aria-label="Instagram"><ion-icon name="logo-instagram"></ion-icon></a>
          <a href="https://wa.me/6285187732004" aria-label="WhatsApp"><ion-icon name="logo-whatsapp"></ion-icon></a>
          <a href="https://x.com/msyncq" aria-label="X"><ion-icon name="logo-x"></ion-icon></a>
        </div>
      </div>
      <div>
        <h4>Jelajahi</h4>
        <ul>
          <li><a href="#beranda">Beranda</a></li>
          <li><a href="#menu">Menu</a></li>
          <li><a href="#tentang">Tentang</a></li>
          <li><a href="#galeri">Galeri</a></li>
          <li><a href="<?= url('login') ?>">Login Staf</a></li>
        </ul>
      </div>
      <div>
        <h4>Kontak</h4>
        <ul>
          <li>Jl. Samudera No. 16, Alaska</li>
          <li>+62 851-8773-2004</li>
          <li>Senin–Sabtu, 10.00–22.00</li>
        </ul>
      </div>
      <div>
        <h4>Kabar tersaji langsung ke inbox Anda</h4>
        <p style="margin-top:4px;">Promo spesial &amp; menu baru, tanpa spam.</p>
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
    (function () {
      var tabs = document.querySelectorAll('#catTabs li');
      var items = document.querySelectorAll('.menu-grid .dish');
      tabs.forEach(function (t) {
        t.addEventListener('click', function () {
          tabs.forEach(function (x) { x.classList.remove('active'); });
          t.classList.add('active');
          var cat = t.getAttribute('data-cat');
          items.forEach(function (it) {
            it.style.display = (cat === 'all' || it.getAttribute('data-cat') === cat) ? '' : 'none';
          });
        });
      });
    })();
  </script>
  <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
