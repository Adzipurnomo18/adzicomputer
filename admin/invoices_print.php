<?php
require __DIR__ . '/guard.php';

function rupiah($value) {
  return 'Rp ' . number_format((int)$value, 0, ',', '.');
}

function tanggal_id($date) {
  $months = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
  ];
  $dt = DateTimeImmutable::createFromFormat('Y-m-d', (string)$date) ?: new DateTimeImmutable((string)$date);
  return $dt->format('d') . ' ' . $months[(int)$dt->format('m')] . ' ' . $dt->format('Y');
}

$id = isset($_GET['id']) && ctype_digit($_GET['id']) ? (int)$_GET['id'] : 0;
$st = $pdo->prepare('SELECT * FROM service_invoices WHERE id = ?');
$st->execute([$id]);
$invoice = $st->fetch(PDO::FETCH_ASSOC);
if (!$invoice) {
  http_response_code(404);
  echo 'Nota tidak ditemukan.';
  exit;
}

$itemSt = $pdo->prepare('SELECT * FROM service_invoice_items WHERE invoice_id = ? ORDER BY position ASC, id ASC');
$itemSt->execute([$id]);
$items = $itemSt->fetchAll(PDO::FETCH_ASSOC);
$subtotal = 0;
foreach ($items as $item) {
  $unitTotal = (int)($item['part_price'] ?? 0) + (int)($item['service_price'] ?? 0);
  if ($unitTotal === 0) $unitTotal = (int)($item['unit_price'] ?? 0);
  $subtotal += (int)$item['qty'] * $unitTotal;
}
$discount = (int)$invoice['discount'];
$total = max(0, $subtotal - $discount);
$notes = trim((string)$invoice['notes']) !== '' ? preg_split('/\R+/', trim((string)$invoice['notes'])) : [];
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Nota <?= h($invoice['invoice_no']) ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/base.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="invoice-print-body">
  <div class="print-toolbar">
    <a class="btn" href="invoices.php?edit=<?= h((string)$invoice['id']) ?>"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    <button class="cta" type="button" onclick="window.print()"><i class="fa-solid fa-file-pdf"></i> Cetak / Save PDF</button>
  </div>

  <main class="invoice-page">
    <span class="invoice-circuit invoice-circuit-left" aria-hidden="true"></span>
    <span class="invoice-circuit invoice-circuit-right" aria-hidden="true"></span>
    <span class="invoice-brand-watermark" aria-hidden="true"></span>
    <header class="invoice-header">
      <div class="invoice-logo-block">
        <img src="../assets/img/gambar/01_logo.png" alt="<?= h($CONFIG['brand']) ?>">
      </div>
      <div class="invoice-title-block">
        <h1>NOTA <span>SERVICE</span></h1>
        <p>No. <?= h($invoice['invoice_no']) ?></p>
      </div>
      <div class="invoice-date-badge">
        <i class="fa-regular fa-calendar-days"></i>
        <div>
          <span>Tanggal Service</span>
          <strong><?= h(tanggal_id($invoice['service_date'])) ?></strong>
        </div>
      </div>
    </header>

    <section class="invoice-info-grid">
      <article class="invoice-info-card">
        <h2><i class="fa-solid fa-user"></i> Data Pelanggan</h2>
        <dl>
          <dt>Nama</dt><dd><?= h($invoice['customer_name']) ?></dd>
          <dt>No. WhatsApp</dt><dd><?= h($invoice['customer_phone'] ?: '-') ?></dd>
          <dt>Alamat</dt><dd><?= h($invoice['customer_address'] ?: '-') ?></dd>
        </dl>
      </article>
      <article class="invoice-info-card">
        <h2><i class="fa-solid fa-laptop"></i> Data Perangkat</h2>
        <dl>
          <dt>Perangkat</dt><dd><?= h($invoice['device_type']) ?></dd>
          <dt>Merk / Tipe</dt><dd><?= h($invoice['device_model'] ?: '-') ?></dd>
          <dt>Keluhan</dt><dd><?= h($invoice['complaint'] ?: '-') ?></dd>
        </dl>
      </article>
    </section>

    <section class="invoice-items-table">
      <table>
        <thead>
          <tr>
            <th>No</th>
            <th>Deskripsi Layanan</th>
            <th>Keterangan</th>
            <th>Qty</th>
            <th>Harga Part</th>
            <th>Harga Jasa</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($items as $idx => $item):
            $partPrice = (int)($item['part_price'] ?? 0);
            $servicePrice = (int)($item['service_price'] ?? 0);
            if ($partPrice === 0 && $servicePrice === 0 && (int)($item['unit_price'] ?? 0) > 0) {
              $servicePrice = (int)$item['unit_price'];
            }
            $lineTotal = (int)$item['qty'] * ($partPrice + $servicePrice);
          ?>
            <tr>
              <td><?= h((string)($idx + 1)) ?></td>
              <td><?= h($item['description']) ?></td>
              <td><?= h($item['detail'] ?: '-') ?></td>
              <td><?= h((string)$item['qty']) ?></td>
              <td><?= h(rupiah($partPrice)) ?></td>
              <td><?= h(rupiah($servicePrice)) ?></td>
              <td><?= h(rupiah($lineTotal)) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>

    <section class="invoice-bottom-grid">
      <article class="invoice-note-card">
        <h2><i class="fa-regular fa-file-lines"></i> Catatan</h2>
        <ul>
          <?php foreach($notes as $note): ?>
            <li><?= h($note) ?></li>
          <?php endforeach; ?>
          <?php if(!$notes): ?>
            <li>Garansi service berlaku <?= h((string)$invoice['warranty_days']) ?> hari sejak tanggal service.</li>
            <li>Simpan nota ini sebagai bukti pembayaran.</li>
          <?php endif; ?>
        </ul>
      </article>

      <article class="invoice-total-card">
        <div><span>Subtotal</span><strong><?= h(rupiah($subtotal)) ?></strong></div>
        <div><span>Diskon</span><strong><?= h(rupiah($discount)) ?></strong></div>
        <hr>
        <div class="grand-total"><span>Total Bayar</span><strong><?= h(rupiah($total)) ?></strong></div>
      </article>
    </section>

    <footer class="invoice-footer">
      <div class="invoice-benefits">
        <div><i class="fa-solid fa-shield-halved"></i><strong>Garansi Service</strong><span><?= h((string)$invoice['warranty_days']) ?> Hari</span></div>
        <div><i class="fa-solid fa-headset"></i><strong>Layanan Cepat</strong><span>Responsif & Profesional</span></div>
        <div><i class="fa-regular fa-thumbs-up"></i><strong>Kualitas Terbaik</strong><span>Pengerjaan Rapi & Aman</span></div>
      </div>
      <div class="invoice-signature">
        <span>Hormat kami,</span>
        <?php if(is_file(__DIR__ . '/../assets/img/gambar/ttd-transparent.png')): ?>
          <img src="../assets/img/gambar/ttd-transparent.png" alt="Tanda tangan">
        <?php elseif(is_file(__DIR__ . '/../ttd.png')): ?>
          <img src="../ttd.png" alt="Tanda tangan">
        <?php endif; ?>
        <strong>ADZI Computer</strong>
      </div>
    </footer>
  </main>
</body>
</html>
