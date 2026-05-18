<?php
require __DIR__.'/guard.php';
$galleryCount = (int)$pdo->query('SELECT COUNT(*) FROM gallery')->fetchColumn();
$messageCount = (int)$pdo->query('SELECT COUNT(*) FROM messages')->fetchColumn();
$unreadCount = (int)$pdo->query('SELECT COUNT(*) FROM messages WHERE is_read = 0')->fetchColumn();
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
      <nav class="admin-nav">
        <a class="btn" href="gallery.php">Galeri</a>
        <a class="btn" href="messages.php">Pesan</a>
        <a class="cta" href="logout.php">Logout</a>
      </nav>
    </header>

    <section class="admin-card admin-section">
      <h2>Ringkasan</h2>
      <p class="admin-muted">Kelola gambar/video galeri serta pesan yang masuk dari formulir kontak.</p>
      <div class="admin-stat-grid">
        <article class="admin-stat"><i class="fa-regular fa-images"></i><strong><?= h((string)$galleryCount) ?></strong><span>Item Galeri</span></article>
        <article class="admin-stat"><i class="fa-regular fa-envelope"></i><strong><?= h((string)$messageCount) ?></strong><span>Total Pesan</span></article>
        <article class="admin-stat"><i class="fa-regular fa-bell"></i><strong><?= h((string)$unreadCount) ?></strong><span>Pesan Baru</span></article>
      </div>
    </section>
  </div>
</body>
</html>
