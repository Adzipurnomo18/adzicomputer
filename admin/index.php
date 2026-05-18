<?php
require __DIR__.'/../inc/boot.php';
if(!empty($_SESSION['admin'])){ header('Location: dashboard.php'); exit; }
$err=null;
if($_SERVER['REQUEST_METHOD']==='POST'){
  $u = trim($_POST['u']??''); $p = trim($_POST['p']??'');
  if($u===$CONFIG['admin_user'] && password_verify($p,$CONFIG['admin_pass_hash'])){
    $_SESSION['admin']=['u'=>$u,'t'=>time()]; header('Location: dashboard.php'); exit;
  }
  $err='Login gagal.';
}
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

<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login Admin</title>
<link rel="stylesheet" href="../assets/css/base.css">
</head>
<body>
  <div class="container" style="padding-top:120px; max-width:520px">
    <div class="panel">
      <h2>Login Admin</h2>
      <?php if($err):?><div class="alert error"><?= h($err) ?></div><?php endif; ?>
      <form method="post"><label>Username<br><input name="u" required></label><br><br><label>Password<br><input name="p" type="password" required></label><br><br>
        <button class="cta" type="submit">Masuk</button>
      </form>
    </div>
  </div>
</body></html>
