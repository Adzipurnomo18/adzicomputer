<?php
$waText = rawurlencode('Halo ADZI Computer, saya ingin konsultasi servis.');
$waNumber = preg_replace('/\D+/', '', $CONFIG['wa']);
if (str_starts_with($waNumber, '0')) {
  $waNumber = '62' . substr($waNumber, 1);
}
$waLink = 'https://wa.me/' . $waNumber . '?text=' . $waText;
$galleryItems = $pdo->query("SELECT * FROM gallery WHERE type = 'image' ORDER BY created_at DESC LIMIT 4")->fetchAll(PDO::FETCH_ASSOC);
$allGalleryItems = $pdo->query("SELECT * FROM gallery WHERE type = 'image' ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$testimonialFiles = glob(__DIR__ . '/../assets/img/gambar/testimoni*.{png,jpg,jpeg,webp}', GLOB_BRACE) ?: [];
natsort($testimonialFiles);
$testimonialImages = array_values(array_map(function($file, $index){
  return [
    'src' => 'assets/img/gambar/' . basename($file),
    'alt' => 'Testimoni pelanggan ADZI Computer ' . ($index + 1),
  ];
}, array_values($testimonialFiles), array_keys(array_values($testimonialFiles))));
?>

<section class="home-hero">
  <div class="container hero-grid">
    <div class="hero-copy reveal">
      <span class="rating-badge"><i class="fa-solid fa-star"></i> Rating 5.0 <b></b> Cepat, Rapi, Bergaransi</span>
      <h1>SOLUSI CEPAT &amp; TERPERCAYA UNTUK <span>LAPTOP,</span> PC, DAN <span>ELEKTRONIK</span></h1>
      <p>Kami siap membantu semua kebutuhan perbaikan perangkat Anda dengan hasil terbaik dan bergaransi.</p>

      <div class="hero-actions">
        <a class="cta cta-primary" href="<?= h($waLink) ?>" target="_blank"><i class="fa-brands fa-whatsapp"></i> Chat WhatsApp</a>
        <a class="btn" href="#layanan"><i class="fa-solid fa-list"></i> Lihat Layanan</a>
        <a class="btn" href="<?= h($CONFIG['maps']) ?>" target="_blank"><i class="fa-solid fa-location-dot"></i> Lokasi Kami</a>
      </div>

      <div class="feature-list">
        <article class="feature-card">
          <i class="fa-regular fa-circle-check"></i>
          <strong>Bergaransi</strong>
          <span>Garansi servis hingga 30 hari</span>
        </article>
        <article class="feature-card">
          <i class="fa-regular fa-clock"></i>
          <strong>Cepat &amp; Tepat</strong>
          <span>Pengerjaan cepat dan profesional</span>
        </article>
        <article class="feature-card">
          <i class="fa-solid fa-gear"></i>
          <strong>Sparepart Original</strong>
          <span>Kualitas terbaik dan bergaransi</span>
        </article>
        <article class="feature-card">
          <i class="fa-regular fa-clipboard"></i>
          <strong>Harga Transparan</strong>
          <span>Tanpa biaya tersembunyi</span>
        </article>
      </div>
    </div>

    <div class="hero-visual reveal">
      <div class="circuit-lines" aria-hidden="true"></div>
      <img src="assets/img/gambar/02_hero_laptop_pc_cutout.png" alt="Laptop dan PC gaming merah">
    </div>
  </div>
</section>

<section class="stats-wrap">
  <div class="container stats-bar reveal">
    <article><i class="fa-solid fa-users"></i><div><strong data-count-to="300" data-count-suffix="+">0+</strong><span>Pelanggan Puas</span></div></article>
    <article><i class="fa-solid fa-screwdriver-wrench"></i><div><strong data-count-to="350" data-count-suffix="+">0+</strong><span>Perangkat Diperbaiki</span></div></article>
    <article><i class="fa-solid fa-star"></i><div><strong data-count-to="5" data-count-decimals="1">0.0</strong><span>Rating Pelanggan</span></div></article>
    <article><i class="fa-regular fa-clock"></i><div><strong data-count-to="30" data-count-suffix=" Hari">0 Hari</strong><span>Garansi Servis</span></div></article>
  </div>
</section>

<section class="services-section" id="layanan">
  <div class="container">
    <div class="section-heading reveal">
      <span>LAYANAN KAMI</span>
      <h2>Service <em>Profesional &amp; Bergaransi</em></h2>
    </div>

    <div class="service-grid">
      <article class="service-card reveal">
        <div class="service-icon"><img src="assets/img/icon/icon_laptop.png" alt=""></div>
        <h3>Service Laptop</h3>
        <p>Perbaikan berbagai kerusakan laptop dengan teknisi ahli.</p>
        <ul>
          <li>Ganti layar</li>
          <li>Perbaikan motherboard</li>
          <li>Cleaning &amp; thermal</li>
          <li>Install ulang OS</li>
        </ul>
        <div class="service-price">Mulai <strong>Rp 150.000</strong></div>
        <a href="index.php?page=katalog">Lihat Detail <i class="fa-solid fa-arrow-right"></i></a>
      </article>

      <article class="service-card reveal">
        <div class="service-icon"><img src="assets/img/icon/icon_pc.png" alt=""></div>
        <h3>Service PC</h3>
        <p>Rakit, upgrade, dan perbaikan PC semua kebutuhan.</p>
        <ul>
          <li>Rakit PC</li>
          <li>Upgrade performa</li>
          <li>Perbaikan hardware</li>
          <li>Install software</li>
        </ul>
        <div class="service-price">Mulai <strong>Rp 120.000</strong></div>
        <a href="index.php?page=katalog">Lihat Detail <i class="fa-solid fa-arrow-right"></i></a>
      </article>

      <article class="service-card reveal">
        <div class="service-icon"><img src="assets/img/icon/icon_ssd.png" alt=""></div>
        <h3>LCD / Screen</h3>
        <p>Ganti layar laptop dan perbaikan berbagai merk.</p>
        <ul>
          <li>Ganti LCD/LED</li>
          <li>Perbaikan backlight</li>
          <li>Flicker / bergaris</li>
          <li>Pengecekan layar</li>
        </ul>
        <div class="service-price">Mulai <strong>Rp 150.000</strong></div>
        <a href="index.php?page=katalog">Lihat Detail <i class="fa-solid fa-arrow-right"></i></a>
      </article>

      <article class="service-card reveal">
        <div class="service-icon"><img src="assets/img/icon/icon_cpu_chip.png" alt=""></div>
        <h3>Elektronik Lain</h3>
        <p>Perbaikan berbagai perangkat elektronik rumah &amp; kantor.</p>
        <ul>
          <li>TV LED / LCD</li>
          <li>Printer</li>
          <li>Monitor</li>
          <li>PSU &amp; UPS</li>
        </ul>
        <div class="service-price">Mulai <strong>Rp 100.000</strong></div>
        <a href="index.php?page=katalog">Lihat Detail <i class="fa-solid fa-arrow-right"></i></a>
      </article>
    </div>
  </div>
</section>

<section class="why-section" id="tentang">
  <div class="container why-grid">
    <div class="why-copy reveal">
      <span>KENAPA MEMILIH KAMI</span>
      <h2>Komitmen Kami untuk Hasil Terbaik</h2>
      <p>Kami memberikan pelayanan terbaik dengan mengutamakan kualitas, kecepatan, dan kepuasan pelanggan.</p>
    </div>

    <div class="why-cards">
      <article class="why-card reveal"><img src="assets/img/icon/icon_tools.png" alt=""><div><h3>Teknisi Berpengalaman</h3><p>Didukung teknisi profesional dan berpengalaman di bidangnya.</p></div></article>
      <article class="why-card reveal"><img src="assets/img/icon/icon_cpu_chip.png" alt=""><div><h3>Sparepart Berkualitas</h3><p>Menggunakan sparepart original dan berkualitas tinggi.</p></div></article>
      <article class="why-card reveal"><img src="assets/img/icon/icon_speed.png" alt=""><div><h3>Pengerjaan Cepat</h3><p>Proses perbaikan cepat tanpa mengurangi kualitas hasil.</p></div></article>
      <article class="why-card reveal"><img src="assets/img/icon/icon_warranty.png" alt=""><div><h3>Garansi Servis</h3><p>Semua layanan bergaransi hingga 30 hari untuk ketenangan Anda.</p></div></article>
    </div>
  </div>
</section>

<section class="proof-section">
  <div class="container proof-grid">
    <article class="proof-panel reveal">
      <div class="panel-head">
        <div><span>GALERI HASIL PERBAIKAN</span><h2>Before &amp; After</h2></div>
        <button class="panel-link" type="button" data-open-gallery="repair-gallery">Lihat Semua <i class="fa-solid fa-arrow-right"></i></button>
      </div>
      <div class="before-gallery gallery-grid">
        <?php if ($galleryItems): foreach ($galleryItems as $it): $full = 'uploads/gallery/' . $it['path']; ?>
          <div class="gallery-card">
            <img src="<?= h($full) ?>" alt="<?= h($it['caption'] ?: 'Hasil perbaikan') ?>" data-full="<?= h($full) ?>">
          </div>
        <?php endforeach; else: ?>
          <img src="assets/img/gambar/07_before_after_gallery.png" alt="Before after servis laptop">
        <?php endif; ?>
      </div>
      <div class="slider-dots"><b></b><i></i><i></i></div>
    </article>

    <article class="proof-panel testimonial reveal">
      <div class="panel-head">
        <div><span>TESTIMONI PELANGGAN</span><h2>Apa Kata Mereka?</h2></div>
        <button class="panel-link" type="button" data-open-gallery="testimonial-gallery">Lihat Semua <i class="fa-solid fa-arrow-right"></i></button>
      </div>
      <div class="testimonial-slider" data-testimonial-slider>
        <button class="testimonial-nav prev" type="button" aria-label="Testimoni sebelumnya"><i class="fa-solid fa-chevron-left"></i></button>
        <div class="testimonial-track">
          <?php foreach ($testimonialImages as $index => $image): ?>
            <img class="<?= $index === 0 ? 'active' : '' ?>" src="<?= h($image['src']) ?>" alt="<?= h($image['alt']) ?>">
          <?php endforeach; ?>
        </div>
        <button class="testimonial-nav next" type="button" aria-label="Testimoni berikutnya"><i class="fa-solid fa-chevron-right"></i></button>
      </div>
      <div class="slider-dots testimonial-dots">
        <?php foreach ($testimonialImages as $index => $image): ?>
          <button class="<?= $index === 0 ? 'active' : '' ?>" type="button" aria-label="Tampilkan testimoni <?= h((string)($index + 1)) ?>"></button>
        <?php endforeach; ?>
      </div>
    </article>
  </div>
</section>

<div class="inline-gallery-modal" data-gallery-modal="repair-gallery" aria-hidden="true">
  <div class="inline-gallery-box" role="dialog" aria-modal="true" aria-label="Semua galeri hasil perbaikan">
    <div class="inline-gallery-head">
      <div><span>GALERI HASIL PERBAIKAN</span><h2>Semua Before &amp; After</h2></div>
      <button type="button" data-close-gallery aria-label="Tutup"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="inline-gallery-grid gallery-grid">
      <?php if ($allGalleryItems): foreach ($allGalleryItems as $it): $full = 'uploads/gallery/' . $it['path']; ?>
        <div class="inline-gallery-item">
          <img src="<?= h($full) ?>" alt="<?= h($it['caption'] ?: 'Hasil perbaikan') ?>" data-full="<?= h($full) ?>">
        </div>
      <?php endforeach; else: ?>
        <div class="inline-gallery-item">
          <img src="assets/img/gambar/07_before_after_gallery.png" alt="Before after servis laptop">
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="inline-gallery-modal" data-gallery-modal="testimonial-gallery" aria-hidden="true">
  <div class="inline-gallery-box" role="dialog" aria-modal="true" aria-label="Semua testimoni pelanggan">
    <div class="inline-gallery-head">
      <div><span>TESTIMONI PELANGGAN</span><h2>Semua Testimoni</h2></div>
      <button type="button" data-close-gallery aria-label="Tutup"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="inline-gallery-grid testimonial-all-grid gallery-grid">
      <?php foreach ($testimonialImages as $image): ?>
        <div class="inline-gallery-item">
          <img src="<?= h($image['src']) ?>" alt="<?= h($image['alt']) ?>" data-full="<?= h($image['src']) ?>">
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<section class="cta-section">
  <div class="container cta-panel reveal">
    <div>
      <h2>Butuh Bantuan Sekarang?<br>Chat kami di <span>WhatsApp!</span></h2>
      <p>Tim kami siap membantu dan memberikan solusi terbaik untuk perangkat Anda.</p>
    </div>
    <div class="cta-center">
      <a class="cta cta-large" href="<?= h($waLink) ?>" target="_blank"><i class="fa-brands fa-whatsapp"></i> Chat WhatsApp Sekarang</a>
      <small>Respon cepat <b></b> Ramah <b></b> Profesional</small>
    </div>
    <img class="qr-code" src="assets/img/gambar/10_qr_code.png" alt="QR WhatsApp ADZI Computer">
  </div>
</section>
