<?php
require __DIR__.'/../inc/boot.php';
if(!empty($_SESSION['admin'])){ header('Location: dashboard.php'); exit; }
$err = null;
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $u = trim($_POST['u'] ?? '');
  $p = trim($_POST['p'] ?? '');
  if($u === $CONFIG['admin_user'] && password_verify($p, $CONFIG['admin_pass_hash'])){
    $_SESSION['admin'] = ['u'=>$u,'t'=>time()];
    header('Location: dashboard.php'); exit;
  }
  $err = 'Login gagal.';
}
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
  <title>Login Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/base.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-body">
  <div class="admin-shell narrow">
    <section class="admin-card admin-login-card">
      <img class="admin-login-logo" src="../assets/img/gambar/01_logo.png" alt="<?= h($CONFIG['brand']) ?>">
      <h1>Login Admin</h1>
      <p>Masuk untuk mengelola galeri dan pesan pelanggan.</p>
      <?php if($err): ?><div class="admin-alert error"><?= h($err) ?></div><?php endif; ?>
      <form method="post" class="admin-form">
        <label>Username
          <input name="u" autocomplete="username" required>
        </label>
        <label>Password
          <input name="p" type="password" autocomplete="current-password" required>
        </label>
        <button class="cta" type="submit"><i class="fa-solid fa-right-to-bracket"></i> Masuk</button>
      </form>
    </section>
  </div>
</body>
</html>
