<?php
$waText = rawurlencode('Halo ADZI Computer, saya ingin konsultasi servis.');
$waNumber = preg_replace('/\D+/', '', $CONFIG['wa']);
if (str_starts_with($waNumber, '0')) {
  $waNumber = '62' . substr($waNumber, 1);
}
$waLink = 'https://wa.me/' . $waNumber . '?text=' . $waText;

$galleryItems = $pdo->query("SELECT * FROM gallery WHERE type = 'image' ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
$allGalleryItems = $pdo->query("SELECT * FROM gallery WHERE type = 'image' ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$galleryCount = count($allGalleryItems);

$testimonialRows = $pdo->query("SELECT * FROM testimonials ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$testimonialFiles = glob(__DIR__ . '/../assets/img/gambar/testimoni*.{png,jpg,jpeg,webp}', GLOB_BRACE) ?: [];
natsort($testimonialFiles);
$uploadedTestimonials = array_map(function($item, $index){
  return [
    'src' => 'uploads/testimonials/' . $item['image'],
    'alt' => $item['caption'] ?: 'Testimoni pelanggan ADZI Computer ' . ($index + 1),
  ];
}, $testimonialRows, array_keys($testimonialRows));
$defaultTestimonials = array_values(array_map(function($file, $index){
  return [
    'src' => 'assets/img/gambar/' . basename($file),
    'alt' => 'Testimoni pelanggan ADZI Computer ' . ($index + 1),
  ];
}, array_values($testimonialFiles), array_keys(array_values($testimonialFiles))));
$testimonialImages = array_merge($uploadedTestimonials, $defaultTestimonials);
$testimonialCount = count($testimonialImages);

$services = [
  [
    'icon' => 'assets/img/icon/icon_laptop.png',
    'title' => 'Service Laptop',
    'desc' => 'Perbaikan berbagai kerusakan laptop dengan teknisi ahli.',
    'items' => ['Ganti layar', 'Perbaikan motherboard', 'Cleaning & thermal', 'Install ulang OS'],
    'price' => 'Rp 150.000',
  ],
  [
    'icon' => 'assets/img/icon/icon_pc.png',
    'title' => 'Service PC',
    'desc' => 'Rakit, upgrade, dan perbaikan PC sesuai kebutuhan Anda.',
    'items' => ['Rakit PC', 'Upgrade performa', 'Perbaikan hardware', 'Install software'],
    'price' => 'Rp 120.000',
  ],
  [
    'icon' => 'assets/img/icon/icon_ssd.png',
    'title' => 'LCD / Screen',
    'desc' => 'Ganti layar laptop dan perbaikan berbagai merek.',
    'items' => ['Ganti LCD/LED', 'Perbaikan backlight', 'Flicker / bergaris', 'Pengecekan layar'],
    'price' => 'Rp 150.000',
  ],
  [
    'icon' => 'assets/img/icon/icon_cpu_chip.png',
    'title' => 'Elektronik Lain',
    'desc' => 'Perbaikan perangkat elektronik rumah dan kantor.',
    'items' => ['TV LED / LCD', 'Printer', 'Monitor', 'PSU & UPS'],
    'price' => 'Rp 100.000',
  ],
];

$processSteps = [
  ['Konsultasi', 'Konsultasikan kerusakan via WhatsApp atau langsung ke workshop.'],
  ['Diagnosa', 'Pengecekan perangkat untuk memastikan sumber masalah.'],
  ['Estimasi', 'Biaya dan waktu pengerjaan disampaikan sebelum perbaikan.'],
  ['Pengerjaan', 'Perbaikan dilakukan rapi, teliti, dan profesional.'],
  ['Selesai & Garansi', 'Perangkat siap digunakan dengan garansi hingga 30 hari.'],
];
?>

<section class="home-hero">
  <div class="container hero-grid">
    <div class="hero-copy reveal">
      <span class="rating-badge"><i class="fa-solid fa-star"></i> Rating 5.0 <b></b> Cepat, Rapi, Bergaransi</span>
      <h1>
        <span class="hero-title-main">Solusi Cepat &amp; Terpercaya</span>
        <span class="hero-title-main">untuk</span>
        <span class="hero-title-accent">Laptop, PC, dan Elektronik Anda</span>
      </h1>
      <p>Kami siap membantu kebutuhan perbaikan perangkat Anda dengan pemeriksaan jelas, pengerjaan rapi, dan garansi service.</p>

      <div class="hero-actions">
        <a class="cta cta-primary" href="<?= h($waLink) ?>" target="_blank"><i class="fa-brands fa-whatsapp"></i> Chat WhatsApp</a>
        <a class="btn" href="#layanan"><i class="fa-solid fa-list"></i> Lihat Layanan</a>
        <a class="btn" href="<?= h($CONFIG['maps']) ?>" target="_blank"><i class="fa-solid fa-location-dot"></i> Lokasi Kami</a>
      </div>
    </div>

    <div class="hero-visual reveal">
      <img src="assets/img/gambar/adzicomputer.png" alt="Teknisi ADZI Computer memperbaiki laptop">
      <article class="hero-note">
        <strong>Teknisi Profesional</strong>
        <span><i class="fa-regular fa-circle-check"></i> Berpengalaman</span>
        <span><i class="fa-regular fa-circle-check"></i> Bersertifikat</span>
        <span><i class="fa-regular fa-circle-check"></i> Rapi &amp; Teliti</span>
      </article>
    </div>
  </div>
</section>

<section class="stats-wrap">
  <div class="container stats-bar reveal">
    <article><i class="fa-solid fa-screwdriver-wrench"></i><div><strong data-count-to="<?= h((string)max($galleryCount, 1)) ?>" data-count-suffix="+">0+</strong><span>Hasil Service Tercatat</span></div></article>
    <article><i class="fa-solid fa-laptop"></i><div><strong data-count-to="<?= h((string)max($galleryCount, 1)) ?>" data-count-suffix="+">0+</strong><span>Galeri Perbaikan</span></div></article>
    <article><i class="fa-regular fa-face-smile"></i><div><strong data-count-to="98" data-count-suffix="%">0%</strong><span>Pelanggan Puas</span></div></article>
    <article><i class="fa-solid fa-shield-halved"></i><div><strong data-count-to="30" data-count-suffix=" Hari">0 Hari</strong><span>Garansi Service</span></div></article>
  </div>
</section>

<section class="services-section" id="layanan">
  <div class="container">
    <div class="section-head reveal">
      <div>
        <span>LAYANAN KAMI</span>
        <h2>Service <em>Profesional</em> dengan Hasil Terbaik</h2>
      </div>
      <a href="<?= h($basePath . '/katalog') ?>">Lihat Semua Layanan <i class="fa-solid fa-arrow-right"></i></a>
    </div>

    <div class="service-grid">
      <?php foreach ($services as $service): ?>
        <article class="service-card reveal">
          <div class="service-icon"><img src="<?= h($service['icon']) ?>" alt=""></div>
          <h3><?= h($service['title']) ?></h3>
          <p><?= h($service['desc']) ?></p>
          <ul>
            <?php foreach ($service['items'] as $item): ?>
              <li><?= h($item) ?></li>
            <?php endforeach; ?>
          </ul>
          <div class="service-price">Mulai <strong><?= h($service['price']) ?></strong></div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="latest-section">
  <div class="container">
    <div class="section-head reveal">
      <div>
        <span>HASIL SERVICE TERBARU</span>
        <h2>Beberapa Hasil Service Terbaru Kami</h2>
      </div>
      <button class="panel-link" type="button" data-open-gallery="repair-gallery">Lihat Semua Hasil <i class="fa-solid fa-arrow-right"></i></button>
    </div>

    <div class="latest-service-grid gallery-grid reveal">
      <?php if ($galleryItems): foreach ($galleryItems as $it): $full = 'uploads/gallery/' . $it['path']; ?>
        <article class="latest-card">
          <div class="latest-thumb">
            <img src="<?= h($full) ?>" alt="<?= h($it['caption'] ?: 'Hasil service ADZI Computer') ?>" data-full="<?= h($full) ?>">
            <span><?= h(date('d M Y', strtotime($it['created_at']))) ?></span>
          </div>
          <div class="latest-body">
            <h3><?= h($it['caption'] ?: 'Hasil service ADZI Computer') ?></h3>
            <small>Selesai</small>
          </div>
        </article>
      <?php endforeach; else: ?>
        <article class="latest-card empty">
          <div class="latest-thumb"><img src="assets/img/gambar/07_before_after_gallery.png" alt="Hasil service ADZI Computer"></div>
          <div class="latest-body">
            <h3>Belum ada data galeri terbaru</h3>
            <small>Tambahkan dari admin</small>
          </div>
        </article>
      <?php endif; ?>
    </div>
  </div>
</section>

<section class="why-section" id="tentang">
  <div class="container why-layout">
    <div class="why-copy reveal">
      <span>KENAPA MEMILIH ADZI COMPUTER?</span>
      <h2>Komitmen Kami untuk Hasil Terbaik</h2>
      <p>Kami mengutamakan pemeriksaan yang jelas, komunikasi biaya yang transparan, dan hasil perbaikan yang bisa dipertanggungjawabkan.</p>
    </div>
    <div class="why-cards reveal">
      <article><img src="assets/img/icon/icon_warranty.png" alt=""><h3>Teknisi Berpengalaman</h3><p>Ditangani teknisi profesional dan berpengalaman di bidangnya.</p></article>
      <article><img src="assets/img/icon/icon_speed.png" alt=""><h3>Pengerjaan Cepat</h3><p>Proses perbaikan cepat tanpa mengurangi kualitas hasil.</p></article>
      <article><img src="assets/img/icon/icon_cpu_chip.png" alt=""><h3>Sparepart Berkualitas</h3><p>Menggunakan sparepart original dan berkualitas tinggi.</p></article>
      <article><img src="assets/img/icon/icon_tools.png" alt=""><h3>Garansi Service</h3><p>Semua layanan bergaransi hingga 30 hari untuk ketenangan Anda.</p></article>
    </div>
  </div>
</section>

<section class="info-section">
  <div class="container info-grid">
    <article class="info-panel testimonial reveal">
      <div class="panel-head">
        <div><span>TESTIMONI PELANGGAN</span><h2>Apa Kata Mereka?</h2></div>
        <button class="panel-link" type="button" data-open-gallery="testimonial-gallery">Lihat Semua <i class="fa-solid fa-arrow-right"></i></button>
      </div>
      <?php if ($testimonialImages): ?>
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
      <?php else: ?>
        <p class="muted-empty">Belum ada testimoni pelanggan.</p>
      <?php endif; ?>
    </article>

    <article class="info-panel process reveal">
      <div class="panel-head"><div><span>PROSES SERVICE KAMI</span><h2>Mudah, Cepat &amp; Transparan</h2></div></div>
      <ol>
        <?php foreach ($processSteps as $index => $step): ?>
          <li><b><?= h(str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT)) ?></b><div><strong><?= h($step[0]) ?></strong><p><?= h($step[1]) ?></p></div></li>
        <?php endforeach; ?>
      </ol>
    </article>

    <article class="info-panel location reveal">
      <div class="panel-head"><div><span>LOKASI KAMI</span><h2>Kunjungi Workshop</h2></div></div>
      <p><?= h($CONFIG['address']) ?></p>
      <iframe
        class="home-map"
        title="Lokasi ADZI Computer"
        src="<?= h($CONFIG['maps_embed']) ?>"
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"
        allowfullscreen
      ></iframe>
      <a class="btn" href="<?= h($CONFIG['maps']) ?>" target="_blank"><i class="fa-solid fa-map-location-dot"></i> Buka di Google Maps</a>
    </article>
  </div>
</section>

<div class="inline-gallery-modal" data-gallery-modal="repair-gallery" aria-hidden="true">
  <div class="inline-gallery-box" role="dialog" aria-modal="true" aria-label="Semua galeri hasil perbaikan">
    <div class="inline-gallery-head">
      <div><span>HASIL SERVICE TERBARU</span><h2>Semua Hasil Service</h2></div>
      <button type="button" data-close-gallery aria-label="Tutup"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="inline-gallery-grid gallery-grid">
      <?php if ($allGalleryItems): foreach ($allGalleryItems as $it): $full = 'uploads/gallery/' . $it['path']; ?>
        <div class="inline-gallery-item">
          <img src="<?= h($full) ?>" alt="<?= h($it['caption'] ?: 'Hasil service ADZI Computer') ?>" data-full="<?= h($full) ?>">
        </div>
      <?php endforeach; else: ?>
        <div class="inline-gallery-item">
          <img src="assets/img/gambar/07_before_after_gallery.png" alt="Hasil service ADZI Computer">
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
    <div class="whatsapp-art"><i class="fa-brands fa-whatsapp"></i></div>
    <div>
      <h2>Butuh Bantuan Sekarang?<br>Chat kami di <span>WhatsApp!</span></h2>
      <small><i class="fa-regular fa-circle-check"></i> Respon Cepat <i class="fa-regular fa-circle-check"></i> Ramah <i class="fa-regular fa-circle-check"></i> Profesional</small>
    </div>
    <a class="cta cta-large" href="<?= h($waLink) ?>" target="_blank"><i class="fa-brands fa-whatsapp"></i> Chat WhatsApp Sekarang</a>
    <img class="qr-code" src="assets/img/gambar/10_qr_code.png" alt="QR WhatsApp ADZI Computer">
  </div>
</section>
