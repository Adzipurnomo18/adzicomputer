<?php
require __DIR__ . '/guard.php';
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
$backUrl = 'reports_income.php?start=' . rawurlencode($startDate) . '&end=' . rawurlencode($endDate);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Laporan Pemasukan <?= h($startDate) ?> - <?= h($endDate) ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/base.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="report-print-body">
  <div class="print-toolbar">
    <a class="btn" href="<?= h($backUrl) ?>"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    <button class="cta" type="button" onclick="window.print()"><i class="fa-solid fa-file-pdf"></i> Cetak / Save PDF</button>
  </div>

  <main class="report-page">
    <header class="report-header">
      <img src="../assets/img/gambar/01_logo.png" alt="ADZI Computer">
      <div>
        <h1>Laporan Pemasukan</h1>
        <p>Periode <?= h($startDate) ?> sampai <?= h($endDate) ?></p>
      </div>
    </header>

    <section class="report-summary">
      <article><span>Total Pemasukan</span><strong><?= h(admin_rupiah($summary['total'])) ?></strong></article>
      <article><span>Keuntungan Bersih</span><strong><?= h(admin_rupiah($summary['net_profit'])) ?></strong></article>
      <article><span>Jumlah Nota</span><strong><?= h((string)$summary['count']) ?></strong></article>
      <article><span>Rata-rata Nota</span><strong><?= h(admin_rupiah($summary['average'])) ?></strong></article>
    </section>

    <section class="report-table-wrap">
      <table class="report-table">
        <thead>
          <tr>
            <th>No</th>
            <th>No. Nota</th>
            <th>Tanggal</th>
            <th>Pelanggan</th>
            <th>Perangkat</th>
            <th>Subtotal</th>
            <th>Diskon</th>
            <th>Total Pemasukan</th>
            <th>Keuntungan</th>
          </tr>
        </thead>
        <tbody>
          <?php if(!$rows): ?>
            <tr><td colspan="9" class="empty">Belum ada pemasukan pada periode ini.</td></tr>
          <?php else: foreach($rows as $idx => $row): ?>
            <tr>
              <td><?= h((string)($idx + 1)) ?></td>
              <td><?= h($row['invoice_no']) ?></td>
              <td><?= h($row['service_date']) ?></td>
              <td><?= h($row['customer_name']) ?></td>
              <td><?= h(trim($row['device_type'] . ' ' . $row['device_model'])) ?></td>
              <td><?= h(admin_rupiah($row['subtotal'])) ?></td>
              <td><?= h(admin_rupiah($row['discount'])) ?></td>
              <td><strong><?= h(admin_rupiah($row['total_income'])) ?></strong></td>
              <td><strong><?= h(admin_rupiah($row['net_profit'])) ?></strong></td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </section>

    <footer class="report-footer">
      <span>Dicetak pada <?= h((new DateTimeImmutable('now'))->format('Y-m-d H:i')) ?></span>
      <strong>ADZI Computer</strong>
    </footer>
  </main>
</body>
</html>
