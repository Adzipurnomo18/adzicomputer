<?php
require __DIR__.'/guard.php';
require __DIR__ . '/partials/layout.php';
$uploadDir = __DIR__.'/../uploads/gallery';
if(!is_dir($uploadDir)) mkdir($uploadDir,0777,true);
$testimonialDir = __DIR__.'/../uploads/testimonials';
if(!is_dir($testimonialDir)) mkdir($testimonialDir,0777,true);

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

if(isset($_POST['add_testimonial'])){
  if(!empty($_FILES['testimonial_img']['name'])){
    $ext = strtolower(pathinfo($_FILES['testimonial_img']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp'];
    if(in_array($ext,$allowed)){
      $fname = uniqid('testimonial_').'.'.$ext;
      move_uploaded_file($_FILES['testimonial_img']['tmp_name'], $testimonialDir.'/'.$fname);
      $st = $pdo->prepare('INSERT INTO testimonials(image,caption) VALUES(?,?)');
      $st->execute([$fname, $_POST['caption'] ?? '']);
      flash('ok','Testimoni pelanggan ditambahkan.');
      header('Location: gallery.php'); exit;
    } else {
      flash('err','Format testimoni tidak didukung.');
    }
  }
}

if(isset($_POST['edit_caption']) && ctype_digit($_POST['edit_caption'])){
  $id = (int)$_POST['edit_caption'];
  $caption = trim($_POST['caption'] ?? '');
  $st = $pdo->prepare('UPDATE gallery SET caption = ? WHERE id = ?');
  $st->execute([$caption, $id]);
  flash('ok','Keterangan galeri diperbarui.');
  header('Location: gallery.php'); exit;
}

if(isset($_POST['edit_testimonial']) && ctype_digit($_POST['edit_testimonial'])){
  $id = (int)$_POST['edit_testimonial'];
  $caption = trim($_POST['caption'] ?? '');
  $st = $pdo->prepare('UPDATE testimonials SET caption = ? WHERE id = ?');
  $st->execute([$caption, $id]);
  flash('ok','Keterangan testimoni diperbarui.');
  header('Location: gallery.php'); exit;
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

if(isset($_POST['del_testimonial']) && ctype_digit($_POST['del_testimonial'])){
  $id = (int)$_POST['del_testimonial'];
  $it = $pdo->prepare('SELECT * FROM testimonials WHERE id=?');
  $it->execute([$id]);
  $testimonial = $it->fetch(PDO::FETCH_ASSOC);
  if($testimonial){
    if(is_file($testimonialDir.'/'.$testimonial['image'])) unlink($testimonialDir.'/'.$testimonial['image']);
    $pdo->prepare('DELETE FROM testimonials WHERE id=?')->execute([$id]);
    flash('ok','Testimoni dihapus.');
  }
  header('Location: gallery.php'); exit;
}

$ok = flash('ok');
$err = flash('err');
$items = $pdo->query('SELECT * FROM gallery ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
$testimonials = $pdo->query('SELECT * FROM testimonials ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
admin_page_start('Galeri', 'gallery', 'Kelola galeri dan testimoni');
?>

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

      <form method="post" enctype="multipart/form-data" class="admin-card admin-section admin-form">
        <input type="hidden" name="add_testimonial" value="1">
        <h2>Tambah Testimoni Pelanggan</h2>
        <label>File Testimoni
          <input type="file" name="testimonial_img" accept=".jpg,.jpeg,.png,.webp" required>
        </label>
        <label>Caption
          <input name="caption" placeholder="Keterangan testimoni">
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
                <form method="post" class="caption-edit-form" id="edit-caption-<?= h((string)$it['id']) ?>">
                  <input type="hidden" name="edit_caption" value="<?= h((string)$it['id']) ?>">
                  <label class="sr-only" for="caption-<?= h((string)$it['id']) ?>">Keterangan galeri</label>
                  <input
                    id="caption-<?= h((string)$it['id']) ?>"
                    name="caption"
                    value="<?= h($it['caption']) ?>"
                    placeholder="<?= h(ucfirst($it['type'])) ?>"
                    maxlength="160"
                  >
                </form>
                <div class="gallery-item-actions">
                  <button class="btn compact" type="submit" form="edit-caption-<?= h((string)$it['id']) ?>">Simpan</button>
                  <form method="post" class="delete-gallery-form" onsubmit="return confirm('Hapus item ini?')">
                    <input type="hidden" name="del" value="<?= h((string)$it['id']) ?>">
                    <button class="btn danger compact" type="submit">Hapus</button>
                  </form>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>

    <section class="admin-card admin-section" style="margin-top:18px">
      <h2>List Testimoni Pelanggan</h2>
      <?php if(!$testimonials): ?>
        <p class="admin-muted">Belum ada testimoni pelanggan tambahan.</p>
      <?php else: ?>
        <div class="admin-gallery">
          <?php foreach($testimonials as $it): ?>
            <article class="admin-gallery-item">
              <div class="admin-thumb">
                <img src="../uploads/testimonials/<?= h($it['image']) ?>" alt="<?= h($it['caption'] ?: 'Testimoni pelanggan') ?>">
              </div>
              <div class="admin-meta">
                <form method="post" class="caption-edit-form" id="edit-testimonial-<?= h((string)$it['id']) ?>">
                  <input type="hidden" name="edit_testimonial" value="<?= h((string)$it['id']) ?>">
                  <label class="sr-only" for="testimonial-caption-<?= h((string)$it['id']) ?>">Keterangan testimoni</label>
                  <input
                    id="testimonial-caption-<?= h((string)$it['id']) ?>"
                    name="caption"
                    value="<?= h($it['caption']) ?>"
                    placeholder="Testimoni"
                    maxlength="160"
                  >
                </form>
                <div class="gallery-item-actions">
                  <button class="btn compact" type="submit" form="edit-testimonial-<?= h((string)$it['id']) ?>">Simpan</button>
                  <form method="post" class="delete-gallery-form" onsubmit="return confirm('Hapus testimoni ini?')">
                    <input type="hidden" name="del_testimonial" value="<?= h((string)$it['id']) ?>">
                    <button class="btn danger compact" type="submit">Hapus</button>
                  </form>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
<?php admin_page_end(); ?>
