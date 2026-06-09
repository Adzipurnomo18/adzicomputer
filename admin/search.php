<?php
require __DIR__ . '/guard.php';
require __DIR__ . '/partials/layout.php';
require __DIR__ . '/partials/income.php';

$q = trim($_GET['q'] ?? '');
$like = '%' . $q . '%';
$messages = [];
$invoices = [];
$gallery = [];

if ($q !== '') {
  $st = $pdo->prepare('SELECT * FROM messages WHERE name LIKE ? OR email LIKE ? OR content LIKE ? ORDER BY created_at DESC LIMIT 12');
  $st->execute([$like, $like, $like]);
  $messages = $st->fetchAll(PDO::FETCH_ASSOC);

  $st = $pdo->prepare("
    SELECT i.*, COALESCE(SUM(it.qty * CASE
      WHEN (it.part_price + it.service_price) > 0 THEN (it.part_price + it.service_price)
      ELSE it.unit_price
    END), 0) AS subtotal
    FROM service_invoices i
    LEFT JOIN service_invoice_items it ON it.invoice_id = i.id
    WHERE i.invoice_no LIKE ? OR i.customer_name LIKE ? OR i.customer_phone LIKE ? OR i.device_type LIKE ? OR i.device_model LIKE ? OR i.complaint LIKE ?
    GROUP BY i.id
    ORDER BY i.service_date DESC, i.id DESC
    LIMIT 12
  ");
  $st->execute([$like, $like, $like, $like, $like, $like]);
  $invoices = $st->fetchAll(PDO::FETCH_ASSOC);

  $st = $pdo->prepare('SELECT * FROM gallery WHERE caption LIKE ? ORDER BY created_at DESC LIMIT 12');
  $st->execute([$like]);
  $gallery = $st->fetchAll(PDO::FETCH_ASSOC);
}

$totalResults = count($messages) + count($invoices) + count($gallery);
admin_page_start('Pencarian', 'search', 'Hasil pencarian data admin');
?>
      <section class="admin-card admin-section">
        <form class="admin-form search-page-form" method="get">
          <label>Cari Data
            <input type="search" name="q" value="<?= h($q) ?>" placeholder="Nama pelanggan, no nota, pesan, caption..." autofocus>
          </label>
          <button class="cta" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
        </form>
        <p class="admin-muted">Ditemukan <?= h((string)$totalResults) ?> hasil untuk "<?= h($q) ?>".</p>
      </section>

      <?php if($q === ''): ?>
        <section class="admin-card admin-section" style="margin-top:16px">
          <p class="admin-muted">Masukkan kata kunci untuk mencari pesan, nota service, atau caption galeri.</p>
        </section>
      <?php else: ?>
        <section class="admin-latest-grid" style="margin-top:16px">
          <article class="admin-card admin-section">
            <div class="panel-head"><h2>Nota Service</h2><a href="invoices.php">Buka Nota</a></div>
            <div class="latest-list">
              <?php if(!$invoices): ?><p class="admin-muted">Tidak ada nota cocok.</p><?php endif; ?>
              <?php foreach($invoices as $invoice): $total = max(0, (int)$invoice['subtotal'] - (int)$invoice['discount']); ?>
                <div class="latest-item">
                  <span class="latest-icon red"><i class="fa-regular fa-file-lines"></i></span>
                  <div><strong><?= h($invoice['invoice_no']) ?></strong><p><?= h($invoice['customer_name']) ?> · <?= h($invoice['device_type']) ?> <?= h($invoice['device_model']) ?></p></div>
                  <a class="icon-action" href="invoices.php?edit=<?= h((string)$invoice['id']) ?>" title="Edit nota"><i class="fa-regular fa-pen-to-square"></i></a>
                  <b><?= h(admin_rupiah($total)) ?></b>
                </div>
              <?php endforeach; ?>
            </div>
          </article>

          <article class="admin-card admin-section">
            <div class="panel-head"><h2>Pesan</h2><a href="messages.php">Buka Pesan</a></div>
            <div class="latest-list">
              <?php if(!$messages): ?><p class="admin-muted">Tidak ada pesan cocok.</p><?php endif; ?>
              <?php foreach($messages as $message): ?>
                <div class="latest-item">
                  <span class="latest-icon"><i class="fa-solid fa-user"></i></span>
                  <div><strong><?= h($message['name']) ?></strong><p><?= h(substr($message['content'], 0, 90)) ?></p></div>
                  <?php if(!$message['is_read']): ?><em>Baru</em><?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          </article>
        </section>

        <section class="admin-card admin-section" style="margin-top:16px">
          <div class="panel-head"><h2>Galeri</h2><a href="gallery.php">Buka Galeri</a></div>
          <div class="latest-list">
            <?php if(!$gallery): ?><p class="admin-muted">Tidak ada galeri cocok.</p><?php endif; ?>
            <?php foreach($gallery as $item): ?>
              <div class="latest-item">
                <span class="latest-icon"><i class="fa-regular fa-images"></i></span>
                <div><strong><?= h($item['caption'] ?: 'Tanpa caption') ?></strong><p><?= h($item['created_at']) ?></p></div>
              </div>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endif; ?>
<?php admin_page_end(); ?>
