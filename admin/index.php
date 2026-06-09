<?php
require __DIR__.'/../inc/boot.php';
if(!empty($_SESSION['admin'])){ header('Location: dashboard.php'); exit; }
$err = null;
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $u = trim($_POST['u'] ?? '');
  $p = trim($_POST['p'] ?? '');
  $admin = null;
  try {
    $st = $pdo->prepare('SELECT * FROM admins WHERE username = ? AND is_active = 1 LIMIT 1');
    $st->execute([$u]);
    $admin = $st->fetch(PDO::FETCH_ASSOC);
  } catch (Throwable $e) {
    $admin = null;
  }
  if($admin && password_verify($p, $admin['password_hash'])){
    $_SESSION['admin'] = ['id'=>(int)$admin['id'],'u'=>$admin['username'],'name'=>$admin['display_name'],'role'=>$admin['role'],'t'=>time()];
    header('Location: dashboard.php'); exit;
  }
  if(!$admin && $u === $CONFIG['admin_user'] && password_verify($p, $CONFIG['admin_pass_hash'])){
    $_SESSION['admin'] = ['id'=>0,'u'=>$u,'name'=>'Admin','role'=>'Super Admin','t'=>time()];
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
<body class="admin-body login-page">
  <main class="login-shell" aria-label="Halaman login admin">
    <section class="login-card">
      <div class="login-info-panel">
        <span class="login-dot-grid top" aria-hidden="true"></span>
        <img class="login-brand-logo" src="../assets/img/gambar/01_logo.png" alt="<?= h($CONFIG['brand']) ?>">
        <div class="login-copy">
          <h1>Selamat Datang di<br>Admin Panel <span>ADZI Computer</span></h1>
          <p>Kelola galeri, pesanan, pelanggan, dan data layanan dengan mudah dan efisien.</p>
        </div>
        <div class="login-benefits" aria-label="Keunggulan panel admin">
          <div class="login-benefit">
            <span><i class="fa-solid fa-shield-halved"></i></span>
            <div>
              <strong>Aman &amp; Terpercaya</strong>
              <p>Sistem keamanan berlapis untuk melindungi data Anda.</p>
            </div>
          </div>
          <div class="login-benefit">
            <span><i class="fa-solid fa-chart-line"></i></span>
            <div>
              <strong>Dashboard Lengkap</strong>
              <p>Pantau semua aktivitas bisnis dalam satu dashboard.</p>
            </div>
          </div>
          <div class="login-benefit">
            <span><i class="fa-solid fa-bolt"></i></span>
            <div>
              <strong>Cepat &amp; Efisien</strong>
              <p>Kelola pesanan dan layanan lebih cepat dan terstruktur.</p>
            </div>
          </div>
        </div>
        <div class="login-device-scene" aria-hidden="true">
          <img src="../assets/img/gambar/02_hero_laptop_pc_cutout.png" alt="">
        </div>
        <span class="login-dot-grid bottom" aria-hidden="true"></span>
      </div>

      <div class="login-form-panel">
        <div class="login-icon-badge" aria-hidden="true"><i class="fa-solid fa-shield-halved"></i></div>
        <div class="login-form-head">
          <h2>Login <span>Admin</span></h2>
          <p>Masuk untuk mengelola galeri dan pesan pelanggan.</p>
        </div>
        <?php if($err): ?><div class="admin-alert error"><?= h($err) ?></div><?php endif; ?>
        <form method="post" class="admin-form login-form">
          <label>Username
            <span class="login-field">
              <i class="fa-regular fa-user"></i>
              <input name="u" autocomplete="username" placeholder="Masukkan username Anda" required>
            </span>
          </label>
          <label>Password
            <span class="login-field">
              <i class="fa-solid fa-lock"></i>
              <input id="adminPassword" name="p" type="password" autocomplete="current-password" placeholder="Masukkan password Anda" required>
              <button class="login-password-toggle" type="button" aria-label="Tampilkan password" aria-controls="adminPassword">
                <i class="fa-regular fa-eye"></i>
              </button>
            </span>
          </label>
          <div class="login-options">
            <label class="login-remember"><input type="checkbox" name="remember" checked> Ingat saya</label>
            <a href="#" aria-label="Lupa password">Lupa password?</a>
          </div>
          <button class="cta login-submit" type="submit"><i class="fa-solid fa-right-to-bracket"></i> Masuk ke Dashboard</button>
        </form>
        <div class="login-divider"><span>atau</span></div>
        <button class="login-google" type="button" disabled><i class="fa-brands fa-google"></i> Login dengan Google</button>
        <p class="login-note"><i class="fa-solid fa-lock"></i> Akses terbatas untuk admin. Semua aktivitas akan dicatat.</p>
        <p class="login-copyright">&copy; 2026 ADZI Computer. All rights reserved.</p>
      </div>
    </section>
  </main>
  <script>
    const passwordToggle = document.querySelector('.login-password-toggle');
    const passwordInput = document.getElementById('adminPassword');
    if (passwordToggle && passwordInput) {
      passwordToggle.addEventListener('click', () => {
        const isHidden = passwordInput.type === 'password';
        passwordInput.type = isHidden ? 'text' : 'password';
        passwordToggle.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
        passwordToggle.innerHTML = isHidden ? '<i class="fa-regular fa-eye-slash"></i>' : '<i class="fa-regular fa-eye"></i>';
      });
    }
  </script>
</body>
</html>
