<?php
require __DIR__ . '/guard.php';
require __DIR__ . '/partials/layout.php';

$errors = [];
$editId = isset($_GET['edit']) && ctype_digit($_GET['edit']) ? (int)$_GET['edit'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['delete']) && ctype_digit($_POST['delete'])) {
    $id = (int)$_POST['delete'];
    $activeCount = (int)$pdo->query('SELECT COUNT(*) FROM admins WHERE is_active = 1')->fetchColumn();
    $target = $pdo->prepare('SELECT * FROM admins WHERE id = ?');
    $target->execute([$id]);
    $admin = $target->fetch(PDO::FETCH_ASSOC);
    if (!$admin) {
      flash('err', 'Akun admin tidak ditemukan.');
    } elseif ($activeCount <= 1 && (int)$admin['is_active'] === 1) {
      flash('err', 'Tidak bisa menghapus admin aktif terakhir.');
    } else {
      $pdo->prepare('DELETE FROM admins WHERE id = ?')->execute([$id]);
      flash('ok', 'Akun admin dihapus.');
    }
    header('Location: admins.php'); exit;
  }

  $id = isset($_POST['id']) && ctype_digit($_POST['id']) ? (int)$_POST['id'] : 0;
  $username = trim($_POST['username'] ?? '');
  $displayName = trim($_POST['display_name'] ?? '');
  $role = trim($_POST['role'] ?? 'Super Admin');
  $password = (string)($_POST['password'] ?? '');
  $isActive = isset($_POST['is_active']) ? 1 : 0;

  if ($username === '' || !preg_match('/^[A-Za-z0-9_.-]{3,40}$/', $username)) {
    $errors[] = 'Username wajib 3-40 karakter dan hanya boleh huruf, angka, titik, underscore, atau strip.';
  }
  if ($displayName === '') $errors[] = 'Nama tampilan wajib diisi.';
  if ($id === 0 && strlen($password) < 6) $errors[] = 'Password akun baru minimal 6 karakter.';
  if ($id > 0 && $password !== '' && strlen($password) < 6) $errors[] = 'Password baru minimal 6 karakter.';

  if (!$errors) {
    try {
      if ($id > 0) {
        if ($password !== '') {
          $st = $pdo->prepare('UPDATE admins SET username = ?, display_name = ?, role = ?, is_active = ?, password_hash = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?');
          $st->execute([$username, $displayName, $role, $isActive, password_hash($password, PASSWORD_BCRYPT), $id]);
        } else {
          $st = $pdo->prepare('UPDATE admins SET username = ?, display_name = ?, role = ?, is_active = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?');
          $st->execute([$username, $displayName, $role, $isActive, $id]);
        }
        if (($_SESSION['admin']['id'] ?? null) === $id) {
          $_SESSION['admin']['u'] = $username;
          $_SESSION['admin']['name'] = $displayName;
          $_SESSION['admin']['role'] = $role;
        }
        flash('ok', 'Akun admin diperbarui.');
      } else {
        $st = $pdo->prepare('INSERT INTO admins(username, display_name, role, is_active, password_hash) VALUES(?,?,?,?,?)');
        $st->execute([$username, $displayName, $role, $isActive, password_hash($password, PASSWORD_BCRYPT)]);
        flash('ok', 'Akun admin ditambahkan.');
      }
      header('Location: admins.php'); exit;
    } catch (Throwable $e) {
      $errors[] = str_contains($e->getMessage(), 'UNIQUE') ? 'Username sudah dipakai.' : 'Gagal menyimpan akun admin.';
    }
  }
}

$editing = null;
if ($editId > 0) {
  $st = $pdo->prepare('SELECT * FROM admins WHERE id = ?');
  $st->execute([$editId]);
  $editing = $st->fetch(PDO::FETCH_ASSOC) ?: null;
}
if (!$editing) {
  $editing = ['id' => 0, 'username' => '', 'display_name' => '', 'role' => 'Super Admin', 'is_active' => 1];
}

$ok = flash('ok');
$err = flash('err');
$rows = $pdo->query('SELECT * FROM admins ORDER BY created_at DESC, id DESC')->fetchAll(PDO::FETCH_ASSOC);

admin_page_start('Akun Admin', 'admins', 'Kelola akun login admin');
?>
      <?php if($ok): ?><div class="admin-alert success"><?= h($ok) ?></div><?php endif; ?>
      <?php if($err): ?><div class="admin-alert error"><?= h($err) ?></div><?php endif; ?>
      <?php foreach($errors as $error): ?><div class="admin-alert error"><?= h($error) ?></div><?php endforeach; ?>

      <section class="admin-grid">
        <form method="post" class="admin-card admin-section admin-form">
          <input type="hidden" name="id" value="<?= h((string)$editing['id']) ?>">
          <h2><?= (int)$editing['id'] > 0 ? 'Edit Akun Admin' : 'Tambah Akun Admin' ?></h2>
          <label>Username
            <input name="username" value="<?= h($editing['username']) ?>" autocomplete="username" required>
          </label>
          <label>Nama Tampilan
            <input name="display_name" value="<?= h($editing['display_name']) ?>" required>
          </label>
          <label>Role
            <input name="role" value="<?= h($editing['role']) ?>" required>
          </label>
          <label>Password <?= (int)$editing['id'] > 0 ? 'Baru' : '' ?>
            <input name="password" type="password" autocomplete="new-password" <?= (int)$editing['id'] === 0 ? 'required' : '' ?>>
          </label>
          <label class="checkline">
            <input type="checkbox" name="is_active" value="1" <?= (int)$editing['is_active'] === 1 ? 'checked' : '' ?>>
            <span>Akun aktif</span>
          </label>
          <div class="admin-actions">
            <button class="cta" type="submit"><i class="fa-regular fa-floppy-disk"></i> Simpan Akun</button>
            <?php if((int)$editing['id'] > 0): ?><a class="btn" href="admins.php"><i class="fa-solid fa-plus"></i> Akun Baru</a><?php endif; ?>
          </div>
        </form>

        <section class="admin-card admin-section">
          <h2>Catatan Keamanan</h2>
          <p class="admin-muted">Gunakan password minimal 6 karakter. Jangan hapus semua akun aktif karena panel membutuhkan minimal satu akun admin untuk login.</p>
        </section>
      </section>

      <section class="admin-card admin-section" style="margin-top:18px">
        <h2>Daftar Akun Admin</h2>
        <?php if(!$rows): ?>
          <p class="admin-muted">Belum ada akun admin.</p>
        <?php else: ?>
          <div class="admin-table admins-table">
            <div class="admin-table-head">
              <span>Username</span>
              <span>Nama</span>
              <span>Role</span>
              <span>Status</span>
              <span>Dibuat</span>
              <span>Aksi</span>
            </div>
            <?php foreach($rows as $row): ?>
              <div class="admin-table-row">
                <span data-label="Username"><?= h($row['username']) ?></span>
                <span data-label="Nama"><?= h($row['display_name']) ?></span>
                <span data-label="Role"><?= h($row['role']) ?></span>
                <span data-label="Status">
                  <span class="status-badge <?= (int)$row['is_active'] === 1 ? 'read' : '' ?>"><?= (int)$row['is_active'] === 1 ? 'Aktif' : 'Nonaktif' ?></span>
                </span>
                <span data-label="Dibuat"><?= h($row['created_at']) ?></span>
                <span data-label="Aksi">
                  <div class="invoice-actions">
                    <a class="icon-action" href="admins.php?edit=<?= h((string)$row['id']) ?>" title="Edit akun" aria-label="Edit akun <?= h($row['username']) ?>"><i class="fa-regular fa-pen-to-square"></i></a>
                    <form method="post" onsubmit="return confirm('Hapus akun admin <?= h($row['username']) ?> ?')">
                      <input type="hidden" name="delete" value="<?= h((string)$row['id']) ?>">
                      <button class="icon-action danger" type="submit" title="Hapus akun" aria-label="Hapus akun <?= h($row['username']) ?>"><i class="fa-solid fa-trash"></i></button>
                    </form>
                  </div>
                </span>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </section>
<?php admin_page_end(); ?>
