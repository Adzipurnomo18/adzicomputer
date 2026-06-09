<?php
require __DIR__ . '/guard.php';
require __DIR__ . '/partials/layout.php';
require __DIR__ . '/partials/income.php';

function rupiah_dashboard($value) {
  return admin_rupiah($value);
}

function admin_short_text($text, int $limit = 76): string {
  $text = trim((string)$text);
  return strlen($text) > $limit ? substr($text, 0, $limit - 3) . '...' : $text;
}

$galleryCount = (int)$pdo->query('SELECT COUNT(*) FROM gallery')->fetchColumn();
$uploadedTestimonialCount = (int)$pdo->query('SELECT COUNT(*) FROM testimonials')->fetchColumn();
$defaultTestimonialCount = count(glob(__DIR__ . '/../assets/img/gambar/testimoni*.{png,jpg,jpeg,webp}', GLOB_BRACE) ?: []);
$testimonialCount = $uploadedTestimonialCount + $defaultTestimonialCount;
$testimonialRating = $testimonialCount > 0 ? '5.0' : '0.0';
$messageCount = (int)$pdo->query('SELECT COUNT(*) FROM messages')->fetchColumn();
$unreadCount = (int)$pdo->query('SELECT COUNT(*) FROM messages WHERE is_read = 0')->fetchColumn();
$readCount = max(0, $messageCount - $unreadCount);

$messageTrendRows = $pdo->query("
  SELECT date(created_at) AS day, COUNT(*) AS total
  FROM messages
  WHERE date(created_at) >= date('now', '-6 days')
  GROUP BY date(created_at)
  ORDER BY day ASC
")->fetchAll(PDO::FETCH_KEY_PAIR);

$messageTrend = [];
$trendMax = 1;
for ($i = 6; $i >= 0; $i--) {
  $date = (new DateTimeImmutable('today'))->modify("-{$i} days");
  $key = $date->format('Y-m-d');
  $value = (int)($messageTrendRows[$key] ?? 0);
  $trendMax = max($trendMax, $value);
  $messageTrend[] = ['label' => $date->format('d/m'), 'value' => $value];
}

$totalTracked = $galleryCount + $testimonialCount + $messageCount;
$chartBase = max(1, $totalTracked);
$galleryPercent = round(($galleryCount / $chartBase) * 100, 1);
$testimonialPercent = round(($testimonialCount / $chartBase) * 100, 1);
$messagePercent = round(($messageCount / $chartBase) * 100, 1);
$unreadPercent = $messageCount > 0 ? round(($unreadCount / $messageCount) * 100) : 0;
$averagePerDay = round(array_sum(array_column($messageTrend, 'value')) / 7, 2);

$latestMessages = $pdo->query('SELECT * FROM messages ORDER BY created_at DESC LIMIT 2')->fetchAll(PDO::FETCH_ASSOC);
$latestInvoices = $pdo->query("
  SELECT i.*, COALESCE(SUM(it.qty * CASE
    WHEN (it.part_price + it.service_price) > 0 THEN (it.part_price + it.service_price)
    ELSE it.unit_price
  END), 0) AS subtotal
  FROM service_invoices i
  LEFT JOIN service_invoice_items it ON it.invoice_id = i.id
  GROUP BY i.id
  ORDER BY i.service_date DESC, i.id DESC
  LIMIT 2
")->fetchAll(PDO::FETCH_ASSOC);

$allIncomeRows = admin_income_rows($pdo);
$allIncomeSummary = admin_income_summary($allIncomeRows);
$monthStart = (new DateTimeImmutable('first day of this month'))->format('Y-m-d');
$today = (new DateTimeImmutable('today'))->format('Y-m-d');
$monthIncomeSummary = admin_income_summary(admin_income_rows($pdo, $monthStart, $today));

admin_page_start('Dashboard', 'dashboard', 'Selamat datang kembali, Admin');
?>
      <section class="admin-kpi-grid">
        <article class="admin-kpi-card">
          <div><i class="fa-regular fa-images"></i></div>
          <span>Item Galeri</span>
          <strong><?= h((string)$galleryCount) ?></strong>
          <p>Total item galeri aktif</p>
          <small class="trend up"><i class="fa-solid fa-arrow-up"></i> Aktif</small>
          <svg class="kpi-spark red" viewBox="0 0 180 52" aria-hidden="true">
            <path class="spark-fill" d="M4 44 C28 42 34 41 48 42 C63 44 70 31 88 32 C105 33 111 43 126 39 C143 34 149 18 166 23 C174 25 176 28 180 27 L180 52 L4 52 Z"></path>
            <path class="spark-line" d="M4 44 C28 42 34 41 48 42 C63 44 70 31 88 32 C105 33 111 43 126 39 C143 34 149 18 166 23 C174 25 176 28 180 27"></path>
          </svg>
        </article>
        <article class="admin-kpi-card">
          <div><i class="fa-regular fa-star"></i></div>
          <span>Rating Rata-rata</span>
          <strong><?= h($testimonialRating) ?></strong>
          <p>Dari <?= h((string)$testimonialCount) ?> testimoni</p>
          <small class="trend good"><i class="fa-solid fa-arrow-up"></i> Stabil</small>
          <svg class="kpi-spark green" viewBox="0 0 180 52" aria-hidden="true">
            <path class="spark-fill" d="M4 45 C24 44 35 40 49 42 C64 45 75 44 88 38 C104 31 115 40 130 36 C146 32 149 17 165 20 C173 21 176 28 180 26 L180 52 L4 52 Z"></path>
            <path class="spark-line" d="M4 45 C24 44 35 40 49 42 C64 45 75 44 88 38 C104 31 115 40 130 36 C146 32 149 17 165 20 C173 21 176 28 180 26"></path>
          </svg>
        </article>
        <article class="admin-kpi-card">
          <div><i class="fa-regular fa-envelope"></i></div>
          <span>Total Pesan</span>
          <strong><?= h((string)$messageCount) ?></strong>
          <p>Pesan dari pelanggan</p>
          <small class="trend up"><i class="fa-solid fa-arrow-up"></i> <?= h((string)$unreadPercent) ?>%</small>
          <svg class="kpi-spark red" viewBox="0 0 180 52" aria-hidden="true">
            <path class="spark-fill" d="M4 45 C24 42 32 44 48 43 C66 41 70 34 86 36 C104 39 112 41 128 33 C146 24 154 19 169 25 C176 28 178 30 180 29 L180 52 L4 52 Z"></path>
            <path class="spark-line" d="M4 45 C24 42 32 44 48 43 C66 41 70 34 86 36 C104 39 112 41 128 33 C146 24 154 19 169 25 C176 28 178 30 180 29"></path>
          </svg>
        </article>
        <article class="admin-kpi-card">
          <div><i class="fa-regular fa-bell"></i></div>
          <span>Pesan Baru</span>
          <strong><?= h((string)$unreadCount) ?></strong>
          <p>Belum dibaca</p>
          <small class="trend up"><i class="fa-solid fa-arrow-up"></i> <?= h((string)$unreadPercent) ?>%</small>
          <svg class="kpi-spark red" viewBox="0 0 180 52" aria-hidden="true">
            <path class="spark-fill" d="M4 46 C22 41 33 42 48 43 C64 44 72 34 90 33 C107 32 112 40 128 37 C145 34 151 21 166 23 C174 24 177 29 180 28 L180 52 L4 52 Z"></path>
            <path class="spark-line" d="M4 46 C22 41 33 42 48 43 C64 44 72 34 90 33 C107 32 112 40 128 37 C145 34 151 21 166 23 C174 24 177 29 180 28"></path>
          </svg>
        </article>
      </section>

      <section class="admin-income-strip">
        <article>
          <span>Total Pemasukan</span>
          <strong><?= h(admin_rupiah($allIncomeSummary['total'])) ?></strong>
          <small>Dari <?= h((string)$allIncomeSummary['count']) ?> nota service</small>
        </article>
        <article>
          <span>Pemasukan Bulan Ini</span>
          <strong><?= h(admin_rupiah($monthIncomeSummary['total'])) ?></strong>
          <small>Periode <?= h($monthStart) ?> sampai <?= h($today) ?></small>
        </article>
        <article>
          <span>Rata-rata Nilai Nota</span>
          <strong><?= h(admin_rupiah($allIncomeSummary['average'])) ?></strong>
          <small>Semua nota dianggap pemasukan</small>
        </article>
        <a href="reports_income.php"><i class="fa-solid fa-file-invoice-dollar"></i> Lihat Laporan</a>
      </section>

      <section class="admin-dashboard-grid">
        <article class="admin-card admin-chart-panel">
          <div class="panel-head">
            <div><h2>Tren Pesan Masuk</h2><p>Aktivitas pesan 7 hari terakhir.</p></div>
            <span>7 Hari Terakhir</span>
          </div>
          <div class="admin-line-chart" aria-label="Tren pesan masuk">
            <?php foreach($messageTrend as $item): $height = max(8, round(($item['value'] / $trendMax) * 100)); ?>
              <div class="line-point" style="--point:<?= h((string)$height) ?>%">
                <b><?= h((string)$item['value']) ?></b>
                <i></i>
                <small><?= h($item['label']) ?></small>
              </div>
            <?php endforeach; ?>
          </div>
          <div class="chart-summary-strip">
            <span><i class="fa-regular fa-envelope"></i><b>Total Pesan</b><strong><?= h((string)$messageCount) ?></strong></span>
            <span><i class="fa-regular fa-circle-check"></i><b>Pesan Dibaca</b><strong><?= h((string)$readCount) ?></strong></span>
            <span><i class="fa-solid fa-circle"></i><b>Belum Dibaca</b><strong><?= h((string)$unreadCount) ?></strong></span>
            <span><i class="fa-solid fa-arrow-trend-up"></i><b>Rata-rata / Hari</b><strong><?= h((string)$averagePerDay) ?></strong></span>
          </div>
        </article>

        <article class="admin-card admin-composition-panel">
          <div class="panel-head"><div><h2>Komposisi Data</h2><p>Konten aktif dan pesan pelanggan.</p></div></div>
          <div class="admin-donut-layout">
            <div class="admin-donut" style="--gallery:<?= h((string)$galleryPercent) ?>%;--testimonial:<?= h((string)$testimonialPercent) ?>%;" aria-label="Komposisi data dashboard">
              <span><?= h((string)$totalTracked) ?><small>Total</small></span>
            </div>
            <div class="admin-chart-legend percent">
              <span><i class="legend-red"></i>Galeri <b><?= h((string)$galleryCount) ?></b><em><?= h((string)$galleryPercent) ?>%</em></span>
              <span><i class="legend-dark"></i>Testimoni <b><?= h((string)$testimonialCount) ?></b><em><?= h((string)$testimonialPercent) ?>%</em></span>
              <span><i class="legend-soft"></i>Pesan <b><?= h((string)$messageCount) ?></b><em><?= h((string)$messagePercent) ?>%</em></span>
            </div>
          </div>
          <div class="admin-progress-block modern">
            <div><span>Pesan Belum Dibaca</span><b><?= h((string)$unreadPercent) ?>%</b></div>
            <i><span style="width:<?= h((string)$unreadPercent) ?>%"></span></i>
            <small><?= h((string)$unreadCount) ?> dari <?= h((string)$messageCount) ?> pesan belum dibaca.</small>
          </div>
        </article>
      </section>

      <section class="admin-latest-grid">
        <article class="admin-card admin-section">
          <div class="panel-head"><h2>Pesan Terbaru</h2><a href="messages.php">Lihat Semua</a></div>
          <div class="latest-list">
            <?php if(!$latestMessages): ?>
              <p class="admin-muted">Belum ada pesan masuk.</p>
            <?php else: foreach($latestMessages as $message): ?>
              <div class="latest-item">
                <span class="latest-icon"><i class="fa-solid fa-user"></i></span>
                <div>
                  <strong><?= h($message['name']) ?></strong>
                  <p><?= h(admin_short_text($message['content'])) ?></p>
                </div>
                <?php if(!$message['is_read']): ?><em>Baru</em><?php endif; ?>
              </div>
            <?php endforeach; endif; ?>
          </div>
        </article>

        <article class="admin-card admin-section">
          <div class="panel-head"><h2>Nota Terbaru</h2><a href="invoices.php">Lihat Semua</a></div>
          <div class="latest-list">
            <?php if(!$latestInvoices): ?>
              <p class="admin-muted">Belum ada nota service.</p>
            <?php else: foreach($latestInvoices as $invoice): $total = max(0, (int)$invoice['subtotal'] - (int)$invoice['discount']); ?>
              <div class="latest-item">
                <span class="latest-icon red"><i class="fa-regular fa-file-lines"></i></span>
                <div>
                  <strong><?= h($invoice['invoice_no']) ?></strong>
                  <p><?= h($invoice['customer_name']) ?> · <?= h($invoice['service_date']) ?></p>
                </div>
                <b><?= h(rupiah_dashboard($total)) ?></b>
              </div>
            <?php endforeach; endif; ?>
          </div>
        </article>
      </section>
<?php admin_page_end(); ?>
