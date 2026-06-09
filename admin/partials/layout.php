<?php
function admin_table_count(PDO $pdo, string $table, string $where = ''): int {
  try {
    return (int)$pdo->query('SELECT COUNT(*) FROM ' . $table . ($where ? ' WHERE ' . $where : ''))->fetchColumn();
  } catch (Throwable $e) {
    return 0;
  }
}

function admin_page_start(string $pageTitle, string $active, string $subtitle = ''): void {
  global $pdo, $CONFIG;
  $unreadCount = admin_table_count($pdo, 'messages', 'is_read = 0');
  $invoiceCount = admin_table_count($pdo, 'service_invoices');
  $adminName = $_SESSION['admin']['name'] ?? ($_SESSION['admin']['u'] ?? 'Admin');
  $adminRole = $_SESSION['admin']['role'] ?? 'Super Admin';
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
  <title>Admin - <?= h($pageTitle) ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/base.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-body admin-layout-body">
  <aside class="admin-sidebar">
    <a class="admin-sidebar-logo" href="dashboard.php" aria-label="Dashboard admin">
      <img src="../assets/img/gambar/01_logo.png" alt="<?= h($CONFIG['brand']) ?>">
    </a>
    <nav class="admin-side-nav" aria-label="Menu admin">
      <a class="<?= $active === 'dashboard' ? 'active' : '' ?>" href="dashboard.php"><i class="fa-solid fa-house"></i><span>Dashboard</span></a>
      <span class="nav-section">Pengelolaan</span>
      <a class="<?= $active === 'gallery' ? 'active' : '' ?>" href="gallery.php"><i class="fa-regular fa-images"></i><span>Galeri</span></a>
      <a class="<?= $active === 'messages' ? 'active' : '' ?>" href="messages.php"><i class="fa-regular fa-envelope"></i><span>Pesan</span><?php if($unreadCount): ?><b><?= h((string)$unreadCount) ?></b><?php endif; ?></a>
      <a class="<?= $active === 'invoices' ? 'active' : '' ?>" href="invoices.php"><i class="fa-regular fa-file-lines"></i><span>Nota Service</span><?php if($invoiceCount): ?><b><?= h((string)$invoiceCount) ?></b><?php endif; ?></a>
      <span class="nav-section">Data Master</span>
      <span class="disabled"><i class="fa-regular fa-user"></i><span>Pelanggan</span></span>
      <span class="disabled"><i class="fa-solid fa-screwdriver-wrench"></i><span>Layanan</span></span>
      <span class="disabled"><i class="fa-solid fa-laptop"></i><span>Perangkat</span></span>
      <span class="nav-section">Laporan</span>
      <a class="side-report-link <?= $active === 'reports_income' ? 'active' : '' ?>" href="reports_income.php"><i class="fa-solid fa-chart-column"></i><span>Laporan Pemasukan</span></a>
      <span class="nav-section">Pengaturan</span>
      <span class="disabled"><i class="fa-solid fa-gear"></i><span>Pengaturan Website</span></span>
      <a class="side-admins-link <?= $active === 'admins' ? 'active' : '' ?>" href="admins.php"><i class="fa-solid fa-user-gear"></i><span>Akun Admin</span></a>
      <a href="logout.php" data-confirm-logout><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a>
    </nav>
  </aside>

  <div class="admin-main">
    <header class="admin-appbar">
      <div class="admin-page-id">
        <button class="admin-menu-button" type="button" aria-label="Buka menu"><i class="fa-solid fa-bars"></i></button>
        <div>
          <h1><?= h($pageTitle) ?></h1>
          <p>Admin / <?= h($pageTitle) ?></p>
        </div>
      </div>
      <form class="admin-search" method="get" action="search.php">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="search" name="q" placeholder="Cari data, pesanan, pelanggan..." aria-label="Pencarian admin" value="<?= h($_GET['q'] ?? '') ?>">
        <kbd>Ctrl + K</kbd>
      </form>
      <div class="admin-user-area">
        <a class="admin-bell" href="messages.php?filter=unread" title="Pesan belum dibaca" aria-label="Buka pesan belum dibaca"><i class="fa-regular fa-bell"></i><?php if($unreadCount): ?><b><?= h((string)$unreadCount) ?></b><?php endif; ?></a>
        <button class="admin-profile-trigger" type="button" aria-expanded="false" aria-haspopup="true">
          <span class="admin-avatar"><i class="fa-solid fa-user"></i></span>
          <span><strong><?= h(ucfirst((string)$adminName)) ?></strong><small><?= h($adminRole) ?></small></span>
          <i class="fa-solid fa-chevron-down"></i>
        </button>
        <div class="admin-profile-menu" hidden>
          <strong><?= h(ucfirst((string)$adminName)) ?></strong>
          <small><?= h($adminRole) ?></small>
          <a href="logout.php" data-confirm-logout><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
      </div>
    </header>

    <main class="admin-shell">
      <section class="admin-page-hero">
        <div>
          <h2><?= h($subtitle ?: $pageTitle) ?></h2>
          <p>Kelola layanan dan pantau aktivitas ADZI Computer dengan mudah.</p>
        </div>
        <?php if($active === 'invoices' || $active === 'dashboard'): ?>
          <a class="cta" href="invoices.php"><i class="fa-solid fa-plus"></i> Buat Nota</a>
        <?php endif; ?>
      </section>
<?php
}

function admin_page_end(): void {
?>
    </main>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../assets/js/admin.js"></script>
</body>
</html>
<?php
}
?>
