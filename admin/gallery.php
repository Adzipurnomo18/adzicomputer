<?php
require __DIR__.'/guard.php';
$uploadDir = __DIR__.'/../uploads/gallery';
if(!is_dir($uploadDir)) mkdir($uploadDir,0777,true);

// handle add image
if(isset($_POST['add_image'])){
  if(!empty($_FILES['img']['name'])){
    $ext = strtolower(pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp'];
    if(in_array($ext,$allowed)){
      $fname = uniqid('img_').'.'.$ext;
      move_uploaded_file($_FILES['img']['tmp_name'], $uploadDir.'/'.$fname);
      $st=$pdo->prepare('INSERT INTO gallery(type,path,caption) VALUES("image",?,?)');
      $st->execute([$fname, $_POST['caption']??'']);
      flash('ok','Gambar diupload.');
      header('Location: gallery.php'); exit;
    } else flash('err','Format tidak didukung');
  }
}
// handle add video (YouTube id)
if(isset($_POST['add_video'])){
  $id = trim($_POST['youtube_id']??'');
  if($id){ $st=$pdo->prepare('INSERT INTO gallery(type,path,caption) VALUES("video",?,?)'); $st->execute([$id, $_POST['caption']??'']); flash('ok','Video ditambahkan.'); header('Location: gallery.php'); exit; }
}
// handle delete
if(isset($_POST['del']) && ctype_digit($_POST['del'])){
  $id=(int)$_POST['del'];
  $it=$pdo->query('SELECT * FROM gallery WHERE id='.$id)->fetch(PDO::FETCH_ASSOC);
  if($it){
    if($it['type']==='image' && is_file($uploadDir.'/'.$it['path'])) unlink($uploadDir.'/'.$it['path']);
    $pdo->prepare('DELETE FROM gallery WHERE id=?')->execute([$id]);
    flash('ok','Item dihapus.');
  }
  header('Location: gallery.php'); exit;
}
$ok=flash('ok'); $err=flash('err');
$items=$pdo->query('SELECT * FROM gallery ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
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

<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin · Galeri</title>
<link rel="stylesheet" href="../assets/css/base.css">
</head>
<body>
  <div class="container" style="padding-top:120px">
    <div class="panel">
      <div style="display:flex; align-items:center; justify-content:space-between; gap:12px">
        <h2>Galeri</h2>
        <div><a class="btn" href="dashboard.php">Dashboard</a> <a class="btn" href="messages.php">Pesan</a> <a class="btn" href="logout.php">Logout</a></div>
      </div>
      <?php if($ok):?><div class="alert success"><?=h($ok)?></div><?php endif; ?>
      <?php if($err):?><div class="alert error"><?=h($err)?></div><?php endif; ?>

      <h3>Tambah Gambar</h3>
      <form method="post" enctype="multipart/form-data" class="panel">
        <input type="hidden" name="add_image" value="1">
        <div class="row">
          <div><label>File Gambar (jpg/png/webp)<br><input type="file" name="img" required></label></div>
          <div><label>Caption<br><input name="caption" placeholder="keterangan"></label></div>
        </div>
        <button class="cta" type="submit">Upload</button>
      </form>

      <h3>Tambah Video (YouTube)</h3>
      <form method="post" class="panel">
        <input type="hidden" name="add_video" value="1">
        <div class="row">
          <div><label>YouTube ID<br><input name="youtube_id" placeholder="mis: dQw4w9WgXcQ" required></label></div>
          <div><label>Caption<br><input name="caption"></label></div>
        </div>
        <button class="cta" type="submit">Tambah</button>
      </form>

      <h3>List Galeri</h3>
      <div class="grid-gallery">
        <?php foreach($items as $it): ?>
          <div class="gal-item">
            <div class="thumb">
              <?php if($it['type']==='image'): ?>
                <img src="../uploads/gallery/<?=h($it['path'])?>" alt="<?=h($it['caption'])?>">
              <?php else: ?>
                <iframe src="https://www.youtube.com/embed/<?=h($it['path'])?>" title="yt" frameborder="0" allowfullscreen></iframe>
              <?php endif; ?>
            </div>
            <div class="meta">
              <div class="caption"><?=h($it['caption'])?></div>
              <form method="post" onsubmit="return confirm('Hapus item ini?')">
                <input type="hidden" name="del" value="<?=$it['id']?>">
                <button class="btn" type="submit">Hapus</button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</body></html>
