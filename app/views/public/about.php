<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<section class="sec" style="padding-top:12px;padding-bottom:40px;text-align:center;">
  <span class="pill">Tentang Kami</span>
  <h1 class="shop-title" style="margin-top:14px;">Resto Sagara Ikan</h1>
  <p class="sec-sub" style="max-width:740px;margin:14px auto 0;">
    Resto Sagara Ikan adalah restoran seafood yang menghadirkan kehangatan laut nusantara ke meja Anda.
    Kami percaya hidangan terbaik lahir dari bahan paling segar, diolah dengan sepenuh hati, dan
    disajikan dengan keramahan khas pesisir.
  </p>
</section>

<section class="about" style="margin-bottom:56px;">
  <div class="about-visual">
    <img src="<?= asset('assets/review.png') ?>" alt="Suasana Resto Sagara Ikan">
    <div class="about-stat"><ion-icon name="ribbon-outline"></ion-icon><div><b>15+ Thn</b><span>Melayani</span></div></div>
  </div>
  <div>
    <span class="pill">Cerita Kami</span>
    <h2 class="sec-title">Dari laut, langsung untuk meja Anda</h2>
    <p class="sec-sub">
      Berawal dari sebuah warung kecil di tepi pantai, Resto Sagara Ikan tumbuh dari kecintaan pada
      hasil laut dan keinginan membagikannya kepada lebih banyak orang. Setiap pagi kami memilih ikan
      tuna, sarden, salmon, dan mackerel terbaik langsung dari nelayan lokal.
    </p>
    <p class="sec-sub">
      Dapur kami memadukan resep warisan keluarga dengan sentuhan modern renyah di luar, juicy di
      dalam sehingga setiap suapan terasa segar, bersih, dan berkelas. Bagi kami, menyajikan makanan
      bukan sekadar bisnis, melainkan cara menghadirkan kebahagiaan.
    </p>
  </div>
</section>

<div class="stat-band">
  <div class="s"><b>15+</b><span>Tahun pengalaman</span></div>
  <div class="s"><b>30+</b><span>Menu seafood</span></div>
  <div class="s"><b>10rb+</b><span>Pelanggan puas</span></div>
  <div class="s"><b>4.9★</b><span>Rating rata-rata</span></div>
</div>

<section class="sec" style="padding-bottom:10px;">
  <div class="sec-head center"><span class="pill">Nilai Kami</span><h2 class="sec-title">Yang selalu kami pegang</h2></div>
  <div class="value-grid">
    <div class="value-card"><div class="vi"><ion-icon name="fish-outline"></ion-icon></div><h3>Kesegaran</h3><p>Ikan dipilih setiap hari dari nelayan lokal, tanpa kompromi pada kualitas.</p></div>
    <div class="value-card"><div class="vi"><ion-icon name="leaf-outline"></ion-icon></div><h3>Berkelanjutan</h3><p>Kami mendukung nelayan dan praktik tangkap yang bertanggung jawab terhadap laut.</p></div>
    <div class="value-card"><div class="vi"><ion-icon name="heart-outline"></ion-icon></div><h3>Keramahan</h3><p>Pelayanan hangat dan tulus, membuat setiap tamu merasa seperti keluarga.</p></div>
    <div class="value-card"><div class="vi"><ion-icon name="sparkles-outline"></ion-icon></div><h3>Konsistensi</h3><p>Rasa yang terjaga di setiap kunjungan, dari dapur hingga ke piring Anda.</p></div>
  </div>
</section>

<section class="sec" style="padding-top:20px;">
  <div class="about" style="align-items:center;">
    <div>
      <span class="pill">Kenapa Sagara</span>
      <h2 class="sec-title">Pengalaman bersantap yang utuh</h2>
      <ul class="about-list">
        <li><ion-icon name="checkmark-circle-outline"></ion-icon> Bahan segar harian dari nelayan tepercaya.</li>
        <li><ion-icon name="checkmark-circle-outline"></ion-icon> Dapur bersih dengan standar higienitas tinggi.</li>
        <li><ion-icon name="checkmark-circle-outline"></ion-icon> Pesan online tanpa login makan di tempat atau bawa pulang.</li>
        <li><ion-icon name="checkmark-circle-outline"></ion-icon> Suasana nyaman, cocok untuk keluarga &amp; acara spesial.</li>
      </ul>
    </div>
    <div class="about-visual">
      <img src="<?= asset('assets/all.png') ?>" alt="Hidangan Sagara Ikan">
    </div>
  </div>
</section>

<section class="sec" style="text-align:center;padding-top:10px;">
  <h2 class="sec-title">Siap mencicipi laut terbaik?</h2>
  <p class="sec-sub" style="max-width:560px;margin:12px auto 24px;">Jelajahi menu kami dan pesan favoritmu sekarang tanpa perlu login.</p>
  <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
    <a href="<?= url('menu') ?>" class="btn lg">Lihat Menu <ion-icon name="arrow-forward-outline"></ion-icon></a>
    <a href="<?= url('') ?>" class="btn ghost lg">Kembali ke Beranda</a>
  </div>
</section>
