<?php
require __DIR__ . '/boot.php';

$active = $_GET['page'] ?? 'home';
$docTitle = ($title ?? $CONFIG['brand']) . ' | ' . $CONFIG['brand'];
$waNumber = preg_replace('/\D+/', '', $CONFIG['wa']);
if (str_starts_with($waNumber, '0')) {
  $waNumber = '62' . substr($waNumber, 1);
}

$perPageCssTag = '';
$abs = __DIR__ . '/../assets/css/' . $active . '.css';
if (is_file($abs)) {
  $perPageCssTag = '<link rel="stylesheet" href="assets/css/' . htmlspecialchars($active, ENT_QUOTES, 'UTF-8') . '.css"/>';
}
?>
<!doctype html>
<html lang="id">
<head>
  <link rel="icon" type="image/x-icon" href="favicon.ico?v=3">
  <link rel="shortcut icon" type="image/x-icon" href="favicon.ico?v=3">
  <link rel="icon" type="image/png" sizes="32x32" href="favicon-32.png?v=3">
  <link rel="icon" type="image/png" sizes="16x16" href="favicon-16.png?v=3">
  <link rel="apple-touch-icon" href="apple-touch-icon.png?v=3">
  <link rel="manifest" href="site.webmanifest">
  <meta name="theme-color" content="#ef1212">

  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title><?= h($docTitle) ?></title>
  <meta name="description" content="Service laptop, PC, dan elektronik cepat, rapi, transparan, dan bergaransi."/>
  <script>
    (function(){
      try {
        var saved = localStorage.getItem('adzi-theme');
        if (saved === 'dark') {
          document.documentElement.classList.add('theme-dark');
          document.write('<link rel="preload" as="image" href="assets/img/gambar/01_logo_dark.png">');
        }
      } catch (e) {}
    })();
  </script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link rel="stylesheet" href="assets/css/base.css"/>
  <link rel="stylesheet" href="assets/css/lightbox.css"/>
  <?= $perPageCssTag ?>
</head>
<body data-page="<?= h($active) ?>">
  <nav class="nav" id="nav">
    <a class="brand" href="index.php?page=home" aria-label="<?= h($CONFIG['brand']) ?>">
      <img
        src="assets/img/gambar/01_logo.png"
        alt="<?= h($CONFIG['brand']) ?>"
        data-logo-light="assets/img/gambar/01_logo.png"
        data-logo-dark="assets/img/gambar/01_logo_dark.png"
      >
    </a>

    <div class="nav-menu" id="nav-menu">
      <a href="index.php?page=home" class="link <?= $active==='home' ? 'active' : '' ?>">
        <i class="fa-solid fa-house nav-icon" aria-hidden="true"></i>
        <span class="nav-label-full">Home</span>
        <span class="nav-label-short">Home</span>
      </a>
      <a href="index.php?page=home#layanan" class="link">
        <i class="fa-solid fa-screwdriver-wrench nav-icon" aria-hidden="true"></i>
        <span class="nav-label-full">Layanan</span>
        <span class="nav-label-short">Layanan</span>
      </a>
      <a href="index.php?page=katalog" class="link <?= $active==='katalog' ? 'active' : '' ?>">
        <i class="fa-solid fa-list-check nav-icon" aria-hidden="true"></i>
        <span class="nav-label-full">Katalog Servis</span>
        <span class="nav-label-short">Katalog</span>
      </a>
      <a href="index.php?page=home#tentang" class="link">
        <i class="fa-solid fa-circle-info nav-icon" aria-hidden="true"></i>
        <span class="nav-label-full">Tentang Kami</span>
        <span class="nav-label-short">Tentang</span>
      </a>
      <a href="index.php?page=kontak" class="link <?= $active==='kontak' ? 'active' : '' ?>">
        <i class="fa-solid fa-location-dot nav-icon" aria-hidden="true"></i>
        <span class="nav-label-full">Kontak</span>
        <span class="nav-label-short">Kontak</span>
      </a>
    </div>

    <a class="cta nav-cta" href="https://wa.me/<?= h($waNumber) ?>?text=Halo%2C%20butuh%20servis" target="_blank">
      <i class="fa-brands fa-whatsapp"></i> Chat WhatsApp
    </a>

    <button class="theme-toggle" type="button" aria-label="Aktifkan dark mode" aria-pressed="false" data-theme-toggle>
      <i class="fa-regular fa-moon"></i>
    </button>

    <button class="nav-burger" type="button" aria-label="Buka menu" aria-controls="nav-menu" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
  </nav>
  <div class="nav-backdrop" aria-hidden="true"></div>

  <main>
