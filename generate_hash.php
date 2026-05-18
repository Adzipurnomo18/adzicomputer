<?php
// generate_hash.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $pw = $_POST['password'] ?? '';
  if ($pw === '') {
    $error = "Isi password terlebih dahulu.";
  } else {
    // cost default 10, bisa ditingkatkan jika server cukup kuat
    $options = ['cost' => 10];
    $hash = password_hash($pw, PASSWORD_BCRYPT, $options);
  }
}
?>
<!doctype html>
<html lang="id">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Generate Password Hash</title></head>
<body style="font-family:system-ui,Arial;padding:24px">
  <h2>Generate bcrypt hash (PHP)</h2>
  <form method="post">
    <label>Password:<br><input type="text" name="password" style="width:320px" required></label>
    <button type="submit">Generate</button>
  </form>

  <?php if(!empty($error)): ?>
    <p style="color:crimson"><?=htmlspecialchars($error)?></p>
  <?php endif; ?>

  <?php if(!empty($hash)): ?>
    <h3>Hash (copy ini ke inc/boot.php → admin_pass_hash):</h3>
    <pre style="background:#f4f4f4;padding:12px;border-radius:6px;overflow:auto"><?=htmlspecialchars($hash)?></pre>
    <p><strong>Contoh penggantian di inc/boot.php:</strong></p>
    <code>$CONFIG['admin_pass_hash'] = '<?=htmlspecialchars($hash)?>';</code>
  <?php endif; ?>
</body>
</html>
