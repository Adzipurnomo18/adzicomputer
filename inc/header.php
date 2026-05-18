<?php
require __DIR__ . '/boot.php';

// halaman aktif (untuk highlight & data-page)
$active = $_GET['page'] ?? 'home';

// judul dokumen (pakai $title jika ada)
$docTitle = ($title ?? $CONFIG['brand']) . ' | ' . $CONFIG['brand'];

// auto-include CSS per halaman: assets/css/{home|katalog|kontak}.css jika ada
$perPageCssTag = '';
$abs = __DIR__ . '/../assets/css/' . $active . '.css';
if (is_file($abs)) {
  $perPageCssTag = '<link rel="stylesheet" href="assets/css/' . htmlspecialchars($active, ENT_QUOTES, 'UTF-8') . '.css"/>';
}
?>
<!doctype html>
<html lang="id">
<head>
  <!-- FAVICONS -->
  <link rel="icon" type="image/x-icon" href="favicon.ico">
  <link rel="icon" type="image/png" sizes="32x32" href="favicon-32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="favicon-16.png">
  <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
  <link rel="manifest" href="site.webmanifest">
  <meta name="theme-color" content="#0a0b10">

  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title><?= h($docTitle) ?></title>
  <meta name="description" content="Service laptop & elektronik cepat, rapi, bergaransi. Cek katalog dan hubungi kami."/>

  <!-- Fonts & Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

  <!-- Base CSS -->
  <link rel="stylesheet" href="assets/css/base.css"/>
  <!-- Page-scoped CSS dari variabel lama (jika kamu pakai) -->
  <?php if (!empty($pageCss)) echo $pageCss; ?>
  <!-- Auto page CSS: home.css / katalog.css / kontak.css -->
  <?= $perPageCssTag ?>
</head>
<link rel="stylesheet" href="assets/css/lightbox.css"/>

<!-- data-page dipakai untuk selector animasi -->
<body data-page="<?= h($active) ?>">
  <style>
    /* mobile responsive: seragamkan ukuran tombol & link di navbar */
    @media (max-width: 768px) {
      .nav { 
        padding: 10px 12px !important; 
        gap: 4px !important;
        top: 12px !important;
      }
      /* Standarkan semua link & tombol di nav */
      .nav .link,
      .nav .cta,
      .nav .nav-burger {
        padding: 8px 12px !important;
        font-size: 11px !important;
        min-height: 34px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        white-space: nowrap !important;
        flex-shrink: 0 !important;
      }
      .nav .link { border-radius: 10px !important; }
      .nav .cta { gap: 3px !important; }
      .nav .cta i,
      .nav .link i { font-size: 11px !important; }
      .nav .nav-burger { padding: 6px !important; }
      .brand { font-size: 11px !important; max-width: 45% !important; }
    }
  </style>
  <div class="bg-orb"></div>
  <canvas id="particles"></canvas>
  <div class="scan"></div>

  <nav class="nav" id="nav">
    <div class="brand"><i class="fa-solid fa-screwdriver-wrench"></i> <?= h($CONFIG['brand']) ?></div>
    <a href="index.php?page=home"    class="link <?= $active==='home'    ? 'active' : '' ?>">Home</a>
    <a href="index.php?page=katalog" class="link <?= $active==='katalog' ? 'active' : '' ?>">Katalog Servis</a>
    <a href="index.php?page=kontak"  class="link <?= $active==='kontak'  ? 'active' : '' ?>">Kontak</a>
    <div class="spacer"></div>
    <a class="cta" href="https://wa.me/<?= h($CONFIG['wa']) ?>?text=Halo%2C%20butuh%20servis" target="_blank">
      <i class="fa-brands fa-whatsapp"></i> Chat WhatsApp
    </a>
  </nav>
  <!-- backdrop untuk menutup menu mobile ketika terbuka -->
  <div class="nav-backdrop" aria-hidden="true"></div>

  <main>
