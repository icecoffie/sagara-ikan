<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<?php $appMockup = asset('assets/' . rawurlencode('mockup app sagara ikan.png')); ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Resto Sagara Ikan</title>
  <link rel="stylesheet" href="<?= asset('style.css') ?>"/>
  <meta name="description" content="Sagara Ikan — restoran seafood dengan pilihan ikan segar: tuna, sarden, salmon, dan mackerel. Rasa premium, harga bersahabat."/>
  <link rel="icon" href="<?= asset('assets/logo.png') ?>" type="image/png"/>
  <noscript><style>.reveal{opacity:1 !important;transform:none !important;}</style></noscript>
</head>
<body>

  <!-- Navigasi -->
  <div class="menu">
    <nav>
      <input type="checkbox" id="check"/>
      <label for="check" class="checkbtn"><ion-icon name="grid-outline"></ion-icon></label>
      <a href="#hero-section" class="brand"><img src="<?= asset('assets/logo.png') ?>" alt="Sagara Ikan" class="logo"/></a>
      <ul>
        <li><a href="#hero-section" class="active">Beranda</a></li>
        <li><a href="#about">Tentang</a></li>
        <li><a href="#menu">Menu</a></li>
        <li><a href="#testimonial">Ulasan</a></li>
        <li><a href="#app">Aplikasi</a></li>
        <li><a href="#contact">Kontak</a></li>
        <li><a href="<?= url('cart') ?>" class="nav-cart"><ion-icon name="cart-outline"></ion-icon> Keranjang<?php if (!empty($cartCount)): ?><span class="cart-badge"><?= (int)$cartCount ?></span><?php endif; ?></a></li>
        <li><a href="<?= url('login') ?>" class="btn nav-login">Login Staf</a></li>
      </ul>
    </nav>
  </div>

  <?php foreach (($flashes ?? []) as $ftype => $fmsg): ?>
    <div class="toast <?= e($ftype) ?>" id="toast"><?= e($fmsg) ?></div>
  <?php endforeach; ?>

  <!-- Hero -->
  <section class="section flex" id="hero-section">
    <div class="text reveal">
      <h1 class="primary">Selamat Datang di <span>Resto Sagara Ikan</span></h1>
      <p class="tertiary">Nikmati hidangan laut berkualitas premium dari ikan pilihan: tuna, sarden, salmon, hingga mackerel. Dimasak dengan teknik terbaik, disajikan hangat, siap memanjakan lidah Anda.</p>
      <a href="#menu" class="btn">Jelajahi Menu</a>
    </div>
    <div class="visual reveal delay1"><img src="<?= asset('assets/all1.png') ?>" alt="Hidangan seafood Sagara Ikan"/></div>
  </section>

  <!-- Informasi Utama -->
  <main class="section" id="main-info" aria-label="Informasi Utama">
    <div class="container flex main-split">
      <section class="main-welcome reveal" aria-labelledby="welcome-title">
        <h2 id="welcome-title" class="secondary">Tawaran Spesial!</h2>
        <p class="tertiary">Di Sagara Ikan, kami menyajikan cita rasa laut yang elegan. Hidangan kami diracik dari ikan segar pilihan dengan teknik masak presisi renyah di luar, juicy di dalam. Nikmati pengalaman makan yang hangat, bersih, dan berkelas.</p>
        <a href="#about" class="btn">Kenal Lebih Dekat</a>
      </section>
      <aside class="main-specials reveal delay1" aria-labelledby="specials-title">
        <h3 id="specials-title" class="secondary small-title">Spesial Hari Ini</h3>
        <ul class="specials-list">
          <li><span>Salmon Butter Lemon</span> <span class="badge">Rp110.000</span></li>
          <li><span>Tuna Oseng</span> <span class="badge">Rp80.000</span></li>
          <li><span>Mackerel Telur Asin</span> <span class="badge">Rp89.000</span></li>
          <li><span>Es Teh Anget</span> <span class="badge">Gratis</span></li>
        </ul>
        <p class="note">* Tersedia sepanjang hari atau selama persediaan masih ada.</p>
      </aside>
    </div>
  </main>

  <!-- About -->
  <section class="section" id="about">
    <div class="container flex">
      <div class="visual reveal"><img src="<?= asset('assets/4.png') ?>" alt="Dapur Sagara Ikan"/></div>
      <div class="text reveal delay1">
        <div class="secondary">Tentang Restoran Kami</div>
        <h2 class="primary">Ikan fresh</h2>
        <h3 class="tertiary">Cita rasa laut yang elegan</h3>
        <p>Sagara Ikan menghadirkan sensasi kuliner laut yang berkelas. Dari pemilihan bahan baku hingga ke piring Anda, setiap detail kami jaga agar Anda merasakan rasa terbaik renyah di luar, juicy di dalam.</p>
        <a href="#menu" class="btn">Lihat Menu</a>
      </div>
    </div>
  </section>

  <!-- Menu (dari database, maksimal 6 unggulan) -->
  <section class="section" id="menu">
    <div class="container">
      <ul class="category reveal" id="catTabs">
        <li class="active" data-cat="all">Semua</li>
        <?php foreach ($categories as $c): ?>
          <li data-cat="<?= e($c['slug']) ?>"><?= e($c['name']) ?></li>
        <?php endforeach; ?>
      </ul>

      <div class="container">
        <div class="restaurant-menu">
          <?php if ($menu): foreach ($menu as $m): ?>
            <div class="menu-item reveal" data-cat="<?= e($m['category_slug']) ?>">
              <a href="<?= url('pesanan/item/' . $m['id']) ?>"><img src="<?= e(menu_image_url($m['image'])) ?>" alt="<?= e($m['name']) ?>"/></a>
              <a href="<?= url('pesanan/item/' . $m['id']) ?>" class="menu-title-link"><div class="title"><?= e($m['name']) ?></div></a>
              <div class="order flex">
                <div class="price"><?= rupiah($m['price']) ?></div>
                <form method="post" action="<?= url('cart/add/' . $m['id']) ?>" class="order-form">
                  <?= Csrf::field() ?>
                  <button type="submit" class="btn btn-menu">Pesan Sekarang</button>
                </form>
              </div>
            </div>
          <?php endforeach; else: ?>
            <p class="tertiary">Menu belum tersedia.</p>
          <?php endif; ?>
        </div>
        <div style="text-align:center;margin-top:30px;">
          <a href="<?= url('pesanan') ?>" class="btn">Lihat Semua Menu</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Testimonial -->
  <section class="section" id="testimonial">
    <div class="container flex">
      <div class="text reveal">
        <h2 class="secondary">Apa kata mereka tentang kami?</h2>
        <p>“Ikannya segar banget, bumbunya meresap, dan porsinya pas. Tempat yang wajib dikunjungi buat pecinta ikan!”</p>
        <div class="user flex">
          <img src="<?= asset('assets/client.jpeg') ?>" alt="Pelanggan Sagara Ikan"/>
          <div class="name"><div class="title">Mamat</div><div class="location">Planet Bekasi</div></div>
        </div>
      </div>
      <div class="visual reveal delay1"><img src="<?= asset('assets/review.png') ?>" alt="Suasana makan di Sagara Ikan"/></div>
    </div>
  </section>

  <!-- App Section -->
  <section class="section" id="app">
    <div class="container flex">
      <div class="visual reveal"><img src="<?= $appMockup ?>" alt="Aplikasi Sagara Ikan"/></div>
      <div class="text reveal delay1">
        <h2 class="secondary">Unduh Aplikasi Sagara Ikan</h2>
        <p>Pesan lebih cepat, simpan favorit, dan nikmati promo eksklusif hanya di aplikasi.</p>
        <div class="download flex">
          <div class="app-store"><ion-icon name="logo-google-playstore"></ion-icon><p>TERSEDIA DI <br/><span>Google Play</span></p></div>
          <div class="app-store"><ion-icon name="logo-apple"></ion-icon><p>UNDUH DI <br/><span>App Store</span></p></div>
        </div>
      </div>
    </div>
  </section>

  <!-- Kontak -->
  <section class="section" id="contact" aria-label="Kontak Sagara Ikan">
    <div class="container flex contact">
      <div class="contact-card reveal">
        <h2 class="secondary">Hubungi Kami</h2>
        <p class="tertiary">Ada pertanyaan, reservasi, atau butuh bantuan? Kirim pesan lewat formulir di bawah.</p>
        <form class="contact-form" action="#" method="post" onsubmit="alert('Terima kasih! Pesan Anda telah kami terima.');return false;">
          <div class="input-group"><label for="nama">Nama</label><input type="text" id="nama" name="nama" placeholder="Nama lengkap" required/></div>
          <div class="input-row">
            <div class="input-group"><label for="email">Email</label><input type="email" id="email" name="email" placeholder="nama@email.com" required/></div>
            <div class="input-group"><label for="telepon">Telepon</label><input type="tel" id="telepon" name="telepon" placeholder="08xxxxxxxxxx"/></div>
          </div>
          <div class="input-group"><label for="pesan">Pesan</label><textarea id="pesan" name="pesan" rows="5" placeholder="Tulis pesan kamu di sini..." required></textarea></div>
          <button type="submit" class="btn">Kirim Pesan</button>
        </form>
      </div>
      <div class="contact-info reveal delay1">
        <div class="info-line"><ion-icon name="location-outline"></ion-icon><div><strong>Alamat</strong><p>Jl. Samudera No. 16, Alaska</p></div></div>
        <div class="info-line"><ion-icon name="call-outline"></ion-icon><div><strong>Telepon</strong><p>+62 851-8773-2004</p></div></div>
        <div class="info-line"><ion-icon name="time-outline"></ion-icon><div><strong>Jam Operasional</strong><p>Senin–Sabtu, 10.00–22.00 WIB</p></div></div>
        <img src="<?= asset('assets/lokasi.png') ?>" class="map" alt="Peta lokasi Sagara Ikan"/>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="container flex">
      <div class="footer-about">
        <h2>Tentang</h2>
        <p>Sagara Ikan adalah destinasi seafood dengan sentuhan elegan. Kami meramu rasa laut menjadi pengalaman makan yang hangat dan berkesan.</p>
        <div class="footer-socials">
          <a href="https://instagram.com/msync.tech/" aria-label="Instagram"><ion-icon name="logo-instagram"></ion-icon></a>
          <a href="https://wa.me/6285187732004" aria-label="WhatsApp"><ion-icon name="logo-whatsapp"></ion-icon></a>
          <a href="https://x.com/msyncq" aria-label="X"><ion-icon name="logo-x"></ion-icon></a>
        </div>
      </div>
      <div class="footer-category">
        <h2>Menu Kami</h2>
        <ul>
          <?php foreach (array_slice($categories, 0, 4) as $c): ?><li>Ikan <?= e($c['name']) ?></li><?php endforeach; ?>
          <?php if (!$categories): ?><li>Seafood</li><?php endif; ?>
        </ul>
      </div>
      <div class="quick-links">
        <h2>Tautan Cepat</h2>
        <ul>
          <li><a href="#about">Tentang Kami</a></li>
          <li><a href="#contact">Kontak</a></li>
          <li><a href="#menu">Menu</a></li>
          <li><a href="<?= url('cart') ?>">Pemesanan</a></li>
          <li><a href="<?= url('cart/track') ?>">Lacak Pesanan</a></li>
        </ul>
      </div>
      <div class="get-in-touch">
        <h2>Hubungi Kami</h2>
        <ul><li>WhatsApp</li><li>Pusat Bantuan</li><li>Kritik & Saran</li><li><a href="<?= url('login') ?>" style="color:inherit">Login Staf</a></li></ul>
      </div>
    </div>
    <div class="copyright">
      <p>&copy; <?= date('Y') ?> Sagara Ikan - Malika Shakila</p>
      <ul class="legal-nav"><li><a href="#">Kebijakan Privasi</a></li><li><a href="#">Syarat & Ketentuan</a></li></ul>
    </div>
  </footer>

  <script>
    (function(){
      var tabs = document.querySelectorAll('#catTabs li');
      var items = document.querySelectorAll('.restaurant-menu .menu-item');
      tabs.forEach(function(t){
        t.addEventListener('click', function(){
          tabs.forEach(function(x){x.classList.remove('active');});
          t.classList.add('active');
          var cat = t.getAttribute('data-cat');
          items.forEach(function(it){
            it.style.display = (cat === 'all' || it.getAttribute('data-cat') === cat) ? '' : 'none';
          });
        });
      });
      var toast = document.getElementById('toast');
      if (toast) { setTimeout(function(){ toast.classList.add('hide'); }, 3200); }

      var menu = document.querySelector('.menu');
      window.addEventListener('scroll', function(){ menu.classList.toggle('scrolled', window.scrollY > 20); }, { passive:true });

      var reveals = document.querySelectorAll('.reveal');
      if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function(entries){
          entries.forEach(function(en){ if (en.isIntersecting){ en.target.classList.add('in'); io.unobserve(en.target); } });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
        reveals.forEach(function(el){ io.observe(el); });
      } else {
        reveals.forEach(function(el){ el.classList.add('in'); });
      }
    })();
  </script>
  <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
