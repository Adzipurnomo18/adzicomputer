<?php
require __DIR__ . '/guard.php';
require __DIR__ . '/partials/layout.php';
require __DIR__ . '/partials/income.php';

$defaultStart = (new DateTimeImmutable('first day of this month'))->format('Y-m-d');
$defaultEnd = (new DateTimeImmutable('today'))->format('Y-m-d');
$startDate = trim($_GET['start'] ?? $defaultStart);
$endDate = trim($_GET['end'] ?? $defaultEnd);
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) $startDate = $defaultStart;
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) $endDate = $defaultEnd;
if ($startDate > $endDate) {
  [$startDate, $endDate] = [$endDate, $startDate];
}

$rows = admin_income_rows($pdo, $startDate, $endDate);
$summary = admin_income_summary($rows);
$printUrl = 'reports_income_print.php?start=' . rawurlencode($startDate) . '&end=' . rawurlencode($endDate);

admin_page_start('Laporan Pemasukan', 'reports_income', 'Laporan pemasukan dari nota service');
?>
      <section class="admin-card admin-section report-filter-card">
        <form method="get" class="admin-form report-filter-form">
          <label>Tanggal Awal
            <input type="date" name="start" value="<?= h($startDate) ?>">
          </label>
          <label>Tanggal Akhir
            <input type="date" name="end" value="<?= h($endDate) ?>">
          </label>
          <button class="cta" type="submit"><i class="fa-solid fa-filter"></i> Terapkan Filter</button>
          <a class="btn" href="<?= h($printUrl) ?>" target="_blank"><i class="fa-solid fa-print"></i> Cetak / Save PDF</a>
        </form>
      </section>

      <section class="admin-income-strip">
        <article>
          <span>Total Pemasukan</span>
          <strong><?= h(admin_rupiah($summary['total'])) ?></strong>
          <small>Periode <?= h($startDate) ?> sampai <?= h($endDate) ?></small>
        </article>
        <article>
          <span>Jumlah Nota</span>
          <strong><?= h((string)$summary['count']) ?></strong>
          <small>Nota service tersimpan</small>
        </article>
        <article>
          <span>Rata-rata Nota</span>
          <strong><?= h(admin_rupiah($summary['average'])) ?></strong>
          <small>Total pemasukan dibagi jumlah nota</small>
        </article>
      </section>

      <section class="admin-card admin-section">
        <div class="panel-head">
          <div>
            <h2>Daftar Pemasukan</h2>
            <p>Semua nota yang tersimpan dihitung sebagai pemasukan.</p>
          </div>
        </div>

        <?php if(!$rows): ?>
          <p class="admin-muted">Belum ada pemasukan pada periode ini.</p>
        <?php else: ?>
          <div class="admin-table income-table">
            <div class="admin-table-head">
              <span>No. Nota</span>
              <span>Tanggal</span>
              <span>Pelanggan</span>
              <span>Perangkat</span>
              <span>Subtotal</span>
              <span>Diskon</span>
              <span>Total</span>
              <span>Aksi</span>
            </div>
            <?php foreach($rows as $row): ?>
              <div class="admin-table-row">
                <span data-label="No. Nota"><?= h($row['invoice_no']) ?></span>
                <span data-label="Tanggal"><?= h($row['service_date']) ?></span>
                <span data-label="Pelanggan"><?= h($row['customer_name']) ?></span>
                <span data-label="Perangkat"><?= h(trim($row['device_type'] . ' ' . $row['device_model'])) ?></span>
                <span data-label="Subtotal"><?= h(admin_rupiah($row['subtotal'])) ?></span>
                <span data-label="Diskon"><?= h(admin_rupiah($row['discount'])) ?></span>
                <span data-label="Total"><strong><?= h(admin_rupiah($row['total_income'])) ?></strong></span>
                <span data-label="Aksi">
                  <div class="invoice-actions">
                    <a class="icon-action" href="invoices_print.php?id=<?= h((string)$row['id']) ?>" target="_blank" title="Cetak nota" aria-label="Cetak nota <?= h($row['invoice_no']) ?>"><i class="fa-solid fa-print"></i></a>
                    <a class="icon-action" href="invoices.php?edit=<?= h((string)$row['id']) ?>" title="Edit nota" aria-label="Edit nota <?= h($row['invoice_no']) ?>"><i class="fa-regular fa-pen-to-square"></i></a>
                  </div>
                </span>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </section>
<?php admin_page_end(); ?>
