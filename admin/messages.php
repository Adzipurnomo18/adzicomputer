<?php
require __DIR__ . '/guard.php';

/* ---- Actions ---- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Tandai baca
  if (isset($_POST['read']) && ctype_digit($_POST['read'])) {
    $pdo->prepare('UPDATE messages SET is_read = 1 WHERE id = ?')->execute([(int)$_POST['read']]);
  }
  // Hapus pesan
  if (isset($_POST['del']) && ctype_digit($_POST['del'])) {
    $pdo->prepare('DELETE FROM messages WHERE id = ?')->execute([(int)$_POST['del']]);
  }
  // Hindari resubmit form saat refresh
  header('Location: messages.php'); exit;
}

/* ---- Data ---- */
$rows = $pdo->query('SELECT * FROM messages ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="id">
<head>
  <!-- FAVICONS (Admin) -->
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <link rel="icon" type="image/png" sizes="32x32" href="../favicon-32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="../favicon-16.png">
  <link rel="apple-touch-icon" sizes="180x180" href="../apple-touch-icon.png">
  <link rel="manifest" href="../site.webmanifest">
  <meta name="theme-color" content="#0a0b10">

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin · Pesan</title>
  <link rel="stylesheet" href="../assets/css/base.css">
  <style>
    .actions-inline{display:flex;gap:8px;flex-wrap:wrap}
    .btn.danger{border-color:#ff6b81;color:#ffdfe3}
    .btn.danger:hover{background:rgba(255,107,129,.15)}
    .status-badge{padding:6px 10px;border-radius:10px;border:1px solid var(--stroke);display:inline-block}
  </style>
</head>
<body>
  <div class="container" style="padding-top:120px">
    <div class="panel">
      <div style="display:flex; align-items:center; justify-content:space-between; gap:12px">
        <h2>Pesan Masuk</h2>
        <div>
          <a class="btn" href="dashboard.php">Dashboard</a>
          <a class="btn" href="gallery.php">Galeri</a>
          <a class="btn" href="logout.php">Logout</a>
        </div>
      </div>

      <div class="table">
        <div class="t-head">
          <span>Waktu</span>
          <span>Nama</span>
          <span>Email</span>
          <span>Pesan</span>
          <span>Status</span>
          <span>Aksi</span>
        </div>

        <?php foreach ($rows as $r): ?>
          <div class="t-row">
            <span><?= h($r['created_at']) ?></span>
            <span><?= h($r['name']) ?></span>
            <span><?= h($r['email']) ?></span>
            <span><?= nl2br(h($r['content'])) ?></span>
            <span>
              <?php if($r['is_read']): ?>
                <span class="status-badge">Terbaca</span>
              <?php else: ?>
                <span class="status-badge">Baru</span>
              <?php endif; ?>
            </span>
            <span>
              <div class="actions-inline">
                <?php if(!$r['is_read']): ?>
                  <form method="post">
                    <input type="hidden" name="read" value="<?= $r['id'] ?>">
                    <button class="btn" type="submit">Tandai baca</button>
                  </form>
                <?php endif; ?>

                <a class="btn" href="mailto:<?= h($r['email']) ?>?subject=Balasan%20dari%20Adzp18">Balas</a>

                <!-- Hapus -->
                <form method="post" onsubmit="return confirm('Hapus pesan dari: <?= addslashes($r['name']) ?> ?')">
                  <input type="hidden" name="del" value="<?= $r['id'] ?>">
                  <button class="btn danger" type="submit">Hapus</button>
                </form>
              </div>
            </span>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</body>
</html>
