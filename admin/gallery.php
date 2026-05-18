<?php
require __DIR__.'/guard.php';
$uploadDir = __DIR__.'/../uploads/gallery';
if(!is_dir($uploadDir)) mkdir($uploadDir,0777,true);

if(isset($_POST['add_image'])){
  if(!empty($_FILES['img']['name'])){
    $ext = strtolower(pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp'];
    if(in_array($ext,$allowed)){
      $fname = uniqid('img_').'.'.$ext;
      move_uploaded_file($_FILES['img']['tmp_name'], $uploadDir.'/'.$fname);
      $st = $pdo->prepare('INSERT INTO gallery(type,path,caption) VALUES("image",?,?)');
      $st->execute([$fname, $_POST['caption'] ?? '']);
      flash('ok','Gambar diupload.');
      header('Location: gallery.php'); exit;
    } else {
      flash('err','Format tidak didukung.');
    }
  }
}

if(isset($_POST['add_video'])){
  $id = trim($_POST['youtube_id'] ?? '');
  if($id){
    $st = $pdo->prepare('INSERT INTO gallery(type,path,caption) VALUES("video",?,?)');
    $st->execute([$id, $_POST['caption'] ?? '']);
    flash('ok','Video ditambahkan.');
    header('Location: gallery.php'); exit;
  }
}

if(isset($_POST['del']) && ctype_digit($_POST['del'])){
  $id = (int)$_POST['del'];
  $it = $pdo->query('SELECT * FROM gallery WHERE id='.$id)->fetch(PDO::FETCH_ASSOC);
  if($it){
    if($it['type'] === 'image' && is_file($uploadDir.'/'.$it['path'])) unlink($uploadDir.'/'.$it['path']);
    $pdo->prepare('DELETE FROM gallery WHERE id=?')->execute([$id]);
    flash('ok','Item dihapus.');
  }
  header('Location: gallery.php'); exit;
}

$ok = flash('ok');
$err = flash('err');
$items = $pdo->query('SELECT * FROM gallery ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
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
  <title>Admin - Galeri</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/base.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-body">
  <div class="admin-shell">
    <header class="admin-topbar">
      <div class="admin-brand">
        <img src="../assets/img/gambar/01_logo.png" alt="<?= h($CONFIG['brand']) ?>">
        <div><span>ADMIN PANEL</span><h1>Galeri</h1></div>
      </div>
      <nav class="admin-nav">
        <a class="btn" href="dashboard.php">Dashboard</a>
        <a class="btn" href="messages.php">Pesan</a>
        <a class="cta" href="logout.php">Logout</a>
      </nav>
    </header>

    <?php if($ok): ?><div class="admin-alert success"><?= h($ok) ?></div><?php endif; ?>
    <?php if($err): ?><div class="admin-alert error"><?= h($err) ?></div><?php endif; ?>

    <section class="admin-grid">
      <form method="post" enctype="multipart/form-data" class="admin-card admin-section admin-form">
        <input type="hidden" name="add_image" value="1">
        <h2>Tambah Gambar</h2>
        <label>File Gambar
          <input type="file" name="img" accept=".jpg,.jpeg,.png,.webp" required>
        </label>
        <label>Caption
          <input name="caption" placeholder="Keterangan gambar">
        </label>
        <button class="cta" type="submit"><i class="fa-solid fa-upload"></i> Upload</button>
      </form>

      <form method="post" class="admin-card admin-section admin-form">
        <input type="hidden" name="add_video" value="1">
        <h2>Tambah Video YouTube</h2>
        <label>YouTube ID
          <input name="youtube_id" placeholder="mis: dQw4w9WgXcQ" required>
        </label>
        <label>Caption
          <input name="caption" placeholder="Keterangan video">
        </label>
        <button class="cta" type="submit"><i class="fa-solid fa-plus"></i> Tambah</button>
      </form>
    </section>

    <section class="admin-card admin-section" style="margin-top:18px">
      <h2>List Galeri</h2>
      <?php if(!$items): ?>
        <p class="admin-muted">Belum ada item galeri.</p>
      <?php else: ?>
        <div class="admin-gallery">
          <?php foreach($items as $it): ?>
            <article class="admin-gallery-item">
              <div class="admin-thumb">
                <?php if($it['type'] === 'image'): ?>
                  <img src="../uploads/gallery/<?= h($it['path']) ?>" alt="<?= h($it['caption']) ?>">
                <?php else: ?>
                  <iframe src="https://www.youtube.com/embed/<?= h($it['path']) ?>" title="<?= h($it['caption'] ?: 'Video') ?>" allowfullscreen></iframe>
                <?php endif; ?>
              </div>
              <div class="admin-meta">
                <div class="admin-caption"><?= h($it['caption'] ?: ucfirst($it['type'])) ?></div>
                <form method="post" onsubmit="return confirm('Hapus item ini?')">
                  <input type="hidden" name="del" value="<?= h((string)$it['id']) ?>">
                  <button class="btn danger" type="submit">Hapus</button>
                </form>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
  </div>
</body>
</html>
