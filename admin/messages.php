<?php
require __DIR__ . '/guard.php';
require __DIR__ . '/partials/layout.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['read']) && ctype_digit($_POST['read'])) {
    $pdo->prepare('UPDATE messages SET is_read = 1 WHERE id = ?')->execute([(int)$_POST['read']]);
  }
  if (isset($_POST['del']) && ctype_digit($_POST['del'])) {
    $pdo->prepare('DELETE FROM messages WHERE id = ?')->execute([(int)$_POST['del']]);
  }
  header('Location: messages.php'); exit;
}

$filter = $_GET['filter'] ?? '';
if ($filter === 'unread') {
  $rows = $pdo->query('SELECT * FROM messages WHERE is_read = 0 ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
  $subtitle = 'Pesan pelanggan yang belum dibaca';
} else {
  $rows = $pdo->query('SELECT * FROM messages ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
  $subtitle = 'Kelola pesan pelanggan';
}
admin_page_start('Pesan Masuk', 'messages', $subtitle);
?>

    <section class="admin-card admin-section">
      <div class="panel-head">
        <h2><?= $filter === 'unread' ? 'Pesan Belum Dibaca' : 'Daftar Pesan' ?></h2>
        <?php if($filter === 'unread'): ?><a href="messages.php">Lihat Semua</a><?php endif; ?>
      </div>
      <?php if(!$rows): ?>
        <p class="admin-muted">Belum ada pesan masuk.</p>
      <?php else: ?>
        <div class="admin-table">
          <div class="admin-table-head">
            <span>Waktu</span>
            <span>Nama</span>
            <span>Email</span>
            <span>Pesan</span>
            <span>Status</span>
            <span>Aksi</span>
          </div>

          <?php foreach ($rows as $r): ?>
            <div class="admin-table-row">
              <span data-label="Waktu"><?= h($r['created_at']) ?></span>
              <span data-label="Nama"><?= h($r['name']) ?></span>
              <span data-label="Email"><?= h($r['email']) ?></span>
              <span data-label="Pesan"><?= nl2br(h($r['content'])) ?></span>
              <span data-label="Status">
                <?php if($r['is_read']): ?>
                  <span class="status-badge read">Terbaca</span>
                <?php else: ?>
                  <span class="status-badge">Baru</span>
                <?php endif; ?>
              </span>
              <span data-label="Aksi">
                <div class="actions-inline">
                  <?php if(!$r['is_read']): ?>
                    <form method="post">
                      <input type="hidden" name="read" value="<?= h((string)$r['id']) ?>">
                      <button class="btn" type="submit">Tandai baca</button>
                    </form>
                  <?php endif; ?>
                  <a class="btn" href="mailto:<?= h($r['email']) ?>?subject=Balasan%20dari%20ADZI%20Computer">Balas</a>
                  <form method="post" onsubmit="return confirm('Hapus pesan dari: <?= h($r['name']) ?> ?')">
                    <input type="hidden" name="del" value="<?= h((string)$r['id']) ?>">
                    <button class="btn danger" type="submit">Hapus</button>
                  </form>
                </div>
              </span>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
<?php admin_page_end(); ?>
