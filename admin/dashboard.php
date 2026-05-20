<?php
require __DIR__.'/guard.php';
$galleryCount = (int)$pdo->query('SELECT COUNT(*) FROM gallery')->fetchColumn();
$uploadedTestimonialCount = (int)$pdo->query('SELECT COUNT(*) FROM testimonials')->fetchColumn();
$defaultTestimonialCount = count(glob(__DIR__ . '/../assets/img/gambar/testimoni*.{png,jpg,jpeg,webp}', GLOB_BRACE) ?: []);
$testimonialCount = $uploadedTestimonialCount + $defaultTestimonialCount;
$testimonialRating = $testimonialCount > 0 ? '5.0' : '0.0';
$messageCount = (int)$pdo->query('SELECT COUNT(*) FROM messages')->fetchColumn();
$unreadCount = (int)$pdo->query('SELECT COUNT(*) FROM messages WHERE is_read = 0')->fetchColumn();
$readCount = max(0, $messageCount - $unreadCount);

$messageTrendRows = $pdo->query("
  SELECT date(created_at) AS day, COUNT(*) AS total
  FROM messages
  WHERE date(created_at) >= date('now', '-6 days')
  GROUP BY date(created_at)
  ORDER BY day ASC
")->fetchAll(PDO::FETCH_KEY_PAIR);

$messageTrend = [];
$trendMax = 1;
for ($i = 6; $i >= 0; $i--) {
  $date = (new DateTimeImmutable('today'))->modify("-{$i} days");
  $key = $date->format('Y-m-d');
  $value = (int)($messageTrendRows[$key] ?? 0);
  $trendMax = max($trendMax, $value);
  $messageTrend[] = [
    'label' => $date->format('d/m'),
    'value' => $value,
  ];
}

$totalTracked = $galleryCount + $testimonialCount + $messageCount;
$chartBase = max(1, $totalTracked);
$galleryPercent = round(($galleryCount / $chartBase) * 100, 1);
$testimonialPercent = round(($testimonialCount / $chartBase) * 100, 1);
$unreadPercent = $messageCount > 0 ? round(($unreadCount / $messageCount) * 100) : 0;
?>
<!doctype html>
<html lang="id">
<head>
  <link rel="icon" type="image/x-icon" href="../favicon.ico?v=3">
  <link rel="shortcut icon" type="image/x-icon" href="../favicon.ico?v=3">
  <link rel="icon" type="image/png" sizes="32x32" href="../favicon-32.png?v=3">
  <link rel="apple-touch-icon" href="../apple-touch-icon.png?v=3">
  <meta name="theme-color" content="#ef1212">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin - Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/base.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-body">
  <div class="admin-shell">
    <header class="admin-topbar">
      <div class="admin-brand">
        <img src="../assets/img/gambar/01_logo.png" alt="<?= h($CONFIG['brand']) ?>">
        <div><span>ADMIN PANEL</span><h1>Dashboard</h1></div>
      </div>
    </header>
    <nav class="admin-nav">
      <a class="btn active" href="dashboard.php"><i class="fa-solid fa-chart-line"></i><span>Dashboard</span></a>
      <a class="btn" href="gallery.php"><i class="fa-regular fa-images"></i><span>Galeri</span></a>
      <a class="btn" href="messages.php"><i class="fa-regular fa-envelope"></i><span>Pesan</span></a>
      <a class="cta" href="logout.php" data-confirm-logout><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a>
    </nav>

    <section class="admin-card admin-section">
      <h2>Ringkasan</h2>
      <p class="admin-muted">Kelola galeri, testimoni pelanggan, serta pesan yang masuk dari formulir kontak.</p>
      <div class="admin-stat-grid">
        <article class="admin-stat"><i class="fa-regular fa-images"></i><strong><?= h((string)$galleryCount) ?></strong><span>Item Galeri</span></article>
        <article class="admin-stat"><i class="fa-regular fa-star"></i><strong><?= h($testimonialRating) ?></strong><span><?= h((string)$testimonialCount) ?> Testimoni Bintang 5</span></article>
        <article class="admin-stat"><i class="fa-regular fa-envelope"></i><strong><?= h((string)$messageCount) ?></strong><span>Total Pesan</span></article>
        <article class="admin-stat"><i class="fa-regular fa-bell"></i><strong><?= h((string)$unreadCount) ?></strong><span>Pesan Baru</span></article>
      </div>
    </section>

    <section class="admin-chart-grid">
      <article class="admin-card admin-chart-card">
        <div class="admin-chart-head">
          <div>
            <span>7 Hari Terakhir</span>
            <h2>Tren Pesan Masuk</h2>
          </div>
          <strong><?= h((string)array_sum(array_column($messageTrend, 'value'))) ?></strong>
        </div>
        <div class="admin-bar-chart" aria-label="Grafik pesan masuk 7 hari terakhir">
          <?php foreach($messageTrend as $item): $height = max(8, round(($item['value'] / $trendMax) * 100)); ?>
            <div class="admin-bar-item">
              <span class="admin-bar-value"><?= h((string)$item['value']) ?></span>
              <i style="height:<?= h((string)$height) ?>%"></i>
              <small><?= h($item['label']) ?></small>
            </div>
          <?php endforeach; ?>
        </div>
      </article>

      <article class="admin-card admin-chart-card">
        <div class="admin-chart-head">
          <div>
            <span>Komposisi Data</span>
            <h2>Konten & Pesan</h2>
          </div>
          <strong><?= h((string)$totalTracked) ?></strong>
        </div>
        <div class="admin-donut-layout">
          <div class="admin-donut" style="--gallery:<?= h((string)$galleryPercent) ?>%;--testimonial:<?= h((string)$testimonialPercent) ?>%;" aria-label="Komposisi data dashboard">
            <span><?= h((string)$totalTracked) ?><small>Total</small></span>
          </div>
          <div class="admin-chart-legend">
            <span><i class="legend-red"></i>Galeri <b><?= h((string)$galleryCount) ?></b></span>
            <span><i class="legend-dark"></i>Testimoni <b><?= h((string)$testimonialCount) ?></b></span>
            <span><i class="legend-soft"></i>Pesan <b><?= h((string)$messageCount) ?></b></span>
          </div>
        </div>
        <div class="admin-progress-block">
          <div><span>Pesan belum dibaca</span><b><?= h((string)$unreadPercent) ?>%</b></div>
          <i><span style="width:<?= h((string)$unreadPercent) ?>%"></span></i>
          <small><?= h((string)$unreadCount) ?> baru dari <?= h((string)$messageCount) ?> pesan, <?= h((string)$readCount) ?> sudah dibaca.</small>
        </div>
      </article>
    </section>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../assets/js/admin.js"></script>
</body>
</html>
