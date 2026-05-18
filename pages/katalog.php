<?php
$services = [
  [
    'badge' => 'Paling Laris',
    'icon' => 'fa-solid fa-battery-three-quarters',
    'title' => 'Ganti Baterai',
    'price' => 'Rp 250.000-650.000',
    'items' => ['Original/OEM bergaransi', 'Free pemasangan & uji health', 'Kalibrasi & tips perawatan'],
  ],
  [
    'icon' => 'fa-solid fa-wind',
    'title' => 'Pembersihan & Thermal',
    'price' => 'Rp 150.000-350.000',
    'items' => ['Ganti thermal paste/pads', 'Perapian kipas & airflow', 'Stress-test & monitoring'],
  ],
  [
    'icon' => 'fa-solid fa-bug-slash',
    'title' => 'Install Ulang & Optimasi',
    'price' => 'Rp 120.000-280.000',
    'items' => ['Windows/Linux, driver lengkap', 'Backup data & hardening', 'Bundle app kerja/sekolah'],
  ],
  [
    'icon' => 'fa-solid fa-memory',
    'title' => 'Upgrade SSD/RAM',
    'price' => 'Jasa Rp 300.000-700.000',
    'items' => ['Kompatibilitas terjamin', 'Clone data & aktivasi TRIM', 'Garansi performa'],
  ],
  [
    'icon' => 'fa-solid fa-keyboard',
    'title' => 'Ganti Keyboard/LCD',
    'price' => 'Mulai Rp 150.000',
    'items' => ['Sparepart bergaransi', 'Perakitan presisi', 'Pembersihan internal'],
  ],
  [
    'icon' => 'fa-solid fa-stethoscope',
    'title' => 'Diagnosa Board',
    'price' => 'Mulai Rp 100.000',
    'items' => ['Short, korosi, mati total', 'Reball/IC replace case by case', 'Estimasi sebelum dikerjakan'],
  ],
  [
    'badge' => 'Best Seller',
    'icon' => 'fa-solid fa-laptop-code',
    'title' => 'Develop Website',
    'price' => 'Mulai Rp 150.000',
    'items' => ['Landing page / company profile', 'HTML, CSS, PHP, JavaScript', 'Laravel', 'Codeigniter', 'Backend/Frontend'],
  ],
];
?>

<section class="section-katalog">
  <div class="container">
    <div class="catalog-head reveal">
      <span>KATALOG SERVIS</span>
      <h1>Pilih Layanan yang Anda Butuhkan</h1>
      <p>Harga bersifat indikatif. Estimasi final diberikan setelah pengecekan kondisi perangkat.</p>
    </div>

    <div class="catalog-grid">
      <?php foreach ($services as $service): ?>
        <article class="catalog-card reveal">
          <?php if (!empty($service['badge'])): ?>
            <span class="chip"><?= h($service['badge']) ?></span>
          <?php endif; ?>
          <div class="catalog-icon"><i class="<?= h($service['icon']) ?>"></i></div>
          <h2><?= h($service['title']) ?></h2>
          <div class="price"><?= h($service['price']) ?></div>
          <ul>
            <?php foreach ($service['items'] as $item): ?>
              <li><?= h($item) ?></li>
            <?php endforeach; ?>
          </ul>
          <a class="catalog-action" href="https://wa.me/<?= h(preg_replace('/^0/', '62', preg_replace('/\D+/', '', $CONFIG['wa']))) ?>?text=Halo%2C%20saya%20ingin%20konsultasi%20<?= rawurlencode($service['title']) ?>" target="_blank">
            Konsultasi <i class="fa-brands fa-whatsapp"></i>
          </a>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
