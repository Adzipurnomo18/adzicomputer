<?php
require __DIR__ . '/guard.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['read']) && ctype_digit($_POST['read'])) {
    $pdo->prepare('UPDATE messages SET is_read = 1 WHERE id = ?')->execute([(int)$_POST['read']]);
  }
  if (isset($_POST['del']) && ctype_digit($_POST['del'])) {
    $pdo->prepare('DELETE FROM messages WHERE id = ?')->execute([(int)$_POST['del']]);
  }
  header('Location: messages.php'); exit;
}

$rows = $pdo->query('SELECT * FROM messages ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
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
  <title>Admin - Pesan</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/base.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-body">
  <div class="admin-shell">
    <header class="admin-topbar">
      <div class="admin-brand">
        <img src="../assets/img/gambar/01_logo.png" alt="<?= h($CONFIG['brand']) ?>">
        <div><span>ADMIN PANEL</span><h1>Pesan Masuk</h1></div>
      </div>
    </header>
    <nav class="admin-nav">
      <a class="btn" href="dashboard.php"><i class="fa-solid fa-chart-line"></i><span>Dashboard</span></a>
      <a class="btn" href="gallery.php"><i class="fa-regular fa-images"></i><span>Galeri</span></a>
      <a class="btn active" href="messages.php"><i class="fa-regular fa-envelope"></i><span>Pesan</span></a>
      <a class="cta" href="logout.php" data-confirm-logout><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a>
    </nav>

    <section class="admin-card admin-section">
      <h2>Daftar Pesan</h2>
      <?php if(!$rows): ?>
        <p class="admin-muted">Belum ada pesan masuk.</p>
      <?php else: ?>
        <div class="admin-table">
          <div class="admin-table-head">
            <span>Waktu</span>
            <span>Nama</span>
            <span>Email</span>
            <span>Pesan</span>
            <span>Status</span>
            <span>Aksi</span>
          </div>

          <?php foreach ($rows as $r): ?>
            <div class="admin-table-row">
              <span data-label="Waktu"><?= h($r['created_at']) ?></span>
              <span data-label="Nama"><?= h($r['name']) ?></span>
              <span data-label="Email"><?= h($r['email']) ?></span>
              <span data-label="Pesan"><?= nl2br(h($r['content'])) ?></span>
              <span data-label="Status">
                <?php if($r['is_read']): ?>
                  <span class="status-badge read">Terbaca</span>
                <?php else: ?>
                  <span class="status-badge">Baru</span>
                <?php endif; ?>
              </span>
              <span data-label="Aksi">
                <div class="actions-inline">
                  <?php if(!$r['is_read']): ?>
                    <form method="post">
                      <input type="hidden" name="read" value="<?= h((string)$r['id']) ?>">
                      <button class="btn" type="submit">Tandai baca</button>
                    </form>
                  <?php endif; ?>
                  <a class="btn" href="mailto:<?= h($r['email']) ?>?subject=Balasan%20dari%20ADZI%20Computer">Balas</a>
                  <form method="post" onsubmit="return confirm('Hapus pesan dari: <?= h($r['name']) ?> ?')">
                    <input type="hidden" name="del" value="<?= h((string)$r['id']) ?>">
                    <button class="btn danger" type="submit">Hapus</button>
                  </form>
                </div>
              </span>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../assets/js/admin.js"></script>
</body>
</html>
