<?php
require __DIR__ . '/guard.php';
require __DIR__ . '/partials/layout.php';

function rupiah($value) {
  return 'Rp ' . number_format((int)$value, 0, ',', '.');
}

function angka_ribuan($value) {
  return number_format((int)$value, 0, ',', '.');
}

function next_invoice_no(PDO $pdo, string $date): string {
  $dt = DateTimeImmutable::createFromFormat('Y-m-d', $date) ?: new DateTimeImmutable('today');
  $prefix = 'INV/ADZI/' . $dt->format('m/Y') . '/';
  $st = $pdo->prepare("SELECT invoice_no FROM service_invoices WHERE invoice_no LIKE ? ORDER BY invoice_no DESC LIMIT 1");
  $st->execute([$prefix . '%']);
  $last = (string)$st->fetchColumn();
  $seq = 1;
  if ($last && preg_match('/(\d+)$/', $last, $m)) {
    $seq = (int)$m[1] + 1;
  }
  return $prefix . str_pad((string)$seq, 4, '0', STR_PAD_LEFT);
}

function invoice_items_total(array $items): int {
  $total = 0;
  foreach ($items as $item) {
    $unitTotal = (int)($item['part_price'] ?? 0) + (int)($item['service_price'] ?? 0);
    if ($unitTotal === 0) $unitTotal = (int)($item['unit_price'] ?? 0);
    $total += (int)$item['qty'] * $unitTotal;
  }
  return $total;
}

$editId = isset($_GET['edit']) && ctype_digit($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editing = null;
$editingItems = [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['delete']) && ctype_digit($_POST['delete'])) {
    $pdo->prepare('DELETE FROM service_invoices WHERE id = ?')->execute([(int)$_POST['delete']]);
    flash('ok', 'Nota berhasil dihapus.');
    header('Location: invoices.php'); exit;
  }

  $id = isset($_POST['id']) && ctype_digit($_POST['id']) ? (int)$_POST['id'] : 0;
  $serviceDate = trim($_POST['service_date'] ?? date('Y-m-d'));
  $invoiceNo = trim($_POST['invoice_no'] ?? '');
  $customerName = trim($_POST['customer_name'] ?? '');
  $customerPhone = trim($_POST['customer_phone'] ?? '');
  $customerAddress = trim($_POST['customer_address'] ?? '');
  $deviceType = trim($_POST['device_type'] ?? '');
  $deviceModel = trim($_POST['device_model'] ?? '');
  $complaint = trim($_POST['complaint'] ?? '');
  $discount = max(0, (int)preg_replace('/\D+/', '', $_POST['discount'] ?? '0'));
  $warrantyDays = max(0, (int)($_POST['warranty_days'] ?? 30));
  $notes = trim($_POST['notes'] ?? '');

  if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $serviceDate)) $errors[] = 'Tanggal service tidak valid.';
  if ($customerName === '') $errors[] = 'Nama pelanggan wajib diisi.';
  if ($deviceType === '') $errors[] = 'Jenis perangkat wajib diisi.';
  if ($invoiceNo === '') $invoiceNo = next_invoice_no($pdo, $serviceDate);

  $items = [];
  $descriptions = $_POST['item_description'] ?? [];
  $details = $_POST['item_detail'] ?? [];
  $qtys = $_POST['item_qty'] ?? [];
  $partCapitalPrices = $_POST['item_part_capital_price'] ?? [];
  $partPrices = $_POST['item_part_price'] ?? [];
  $servicePrices = $_POST['item_service_price'] ?? [];
  foreach ($descriptions as $idx => $description) {
    $description = trim((string)$description);
    $detail = trim((string)($details[$idx] ?? ''));
    $qty = max(1, (int)($qtys[$idx] ?? 1));
    $partCapitalPrice = max(0, (int)preg_replace('/\D+/', '', (string)($partCapitalPrices[$idx] ?? '0')));
    $partPrice = max(0, (int)preg_replace('/\D+/', '', (string)($partPrices[$idx] ?? '0')));
    $servicePrice = max(0, (int)preg_replace('/\D+/', '', (string)($servicePrices[$idx] ?? '0')));
    if ($description === '' && $detail === '' && $partPrice === 0 && $servicePrice === 0) continue;
    if ($description === '') {
      $errors[] = 'Deskripsi layanan pada item #' . ((int)$idx + 1) . ' wajib diisi.';
      continue;
    }
    $items[] = [
      'description' => $description,
      'detail' => $detail,
      'qty' => $qty,
      'part_capital_price' => $partCapitalPrice,
      'part_price' => $partPrice,
      'service_price' => $servicePrice,
      'unit_price' => $partPrice + $servicePrice,
      'position' => count($items) + 1,
    ];
  }
  if (!$items) $errors[] = 'Minimal satu item layanan wajib diisi.';

  if (!$errors) {
    try {
      $pdo->beginTransaction();
      if ($id > 0) {
        $st = $pdo->prepare("UPDATE service_invoices SET
          invoice_no = ?, service_date = ?, customer_name = ?, customer_phone = ?, customer_address = ?,
          device_type = ?, device_model = ?, complaint = ?, discount = ?, warranty_days = ?, notes = ?,
          updated_at = CURRENT_TIMESTAMP
          WHERE id = ?");
        $st->execute([$invoiceNo, $serviceDate, $customerName, $customerPhone, $customerAddress, $deviceType, $deviceModel, $complaint, $discount, $warrantyDays, $notes, $id]);
        $pdo->prepare('DELETE FROM service_invoice_items WHERE invoice_id = ?')->execute([$id]);
        $invoiceId = $id;
      } else {
        $st = $pdo->prepare("INSERT INTO service_invoices
          (invoice_no, service_date, customer_name, customer_phone, customer_address, device_type, device_model, complaint, discount, warranty_days, notes)
          VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        $st->execute([$invoiceNo, $serviceDate, $customerName, $customerPhone, $customerAddress, $deviceType, $deviceModel, $complaint, $discount, $warrantyDays, $notes]);
        $invoiceId = (int)$pdo->lastInsertId();
      }

      $itemSt = $pdo->prepare("INSERT INTO service_invoice_items
        (invoice_id, description, detail, qty, part_capital_price, part_price, service_price, unit_price, position) VALUES (?,?,?,?,?,?,?,?,?)");
      foreach ($items as $item) {
        $itemSt->execute([$invoiceId, $item['description'], $item['detail'], $item['qty'], $item['part_capital_price'], $item['part_price'], $item['service_price'], $item['unit_price'], $item['position']]);
      }
      $pdo->commit();
      flash('ok', $id > 0 ? 'Nota berhasil diperbarui.' : 'Nota berhasil dibuat.');
      header('Location: invoices.php'); exit;
    } catch (Throwable $e) {
      if ($pdo->inTransaction()) $pdo->rollBack();
      $errors[] = str_contains($e->getMessage(), 'UNIQUE') ? 'Nomor nota sudah dipakai.' : 'Gagal menyimpan nota.';
    }
  }

  $editing = [
    'id' => $id,
    'invoice_no' => $invoiceNo,
    'service_date' => $serviceDate,
    'customer_name' => $customerName,
    'customer_phone' => $customerPhone,
    'customer_address' => $customerAddress,
    'device_type' => $deviceType,
    'device_model' => $deviceModel,
    'complaint' => $complaint,
    'discount' => $discount,
    'warranty_days' => $warrantyDays,
    'notes' => $notes,
  ];
  $editingItems = $items;
} elseif ($editId > 0) {
  $st = $pdo->prepare('SELECT * FROM service_invoices WHERE id = ?');
  $st->execute([$editId]);
  $editing = $st->fetch(PDO::FETCH_ASSOC);
  if ($editing) {
    $itemSt = $pdo->prepare('SELECT * FROM service_invoice_items WHERE invoice_id = ? ORDER BY position ASC, id ASC');
    $itemSt->execute([$editId]);
    $editingItems = $itemSt->fetchAll(PDO::FETCH_ASSOC);
  }
}

if (!$editing) {
  $today = date('Y-m-d');
  $editing = [
    'id' => 0,
    'invoice_no' => next_invoice_no($pdo, $today),
    'service_date' => $today,
    'customer_name' => '',
    'customer_phone' => '',
    'customer_address' => '',
    'device_type' => '',
    'device_model' => '',
    'complaint' => '',
    'discount' => 0,
    'warranty_days' => 30,
    'notes' => "Garansi service berlaku 30 hari sejak tanggal service.\nSimpan nota ini sebagai bukti pembayaran.\nTerima kasih telah mempercayakan perbaikan perangkat Anda kepada ADZI Computer.",
  ];
  $editingItems = [
    ['description' => '', 'detail' => '', 'qty' => 1, 'part_capital_price' => 0, 'part_price' => 0, 'service_price' => 0, 'unit_price' => 0],
  ];
}

$ok = flash('ok');
$rows = $pdo->query("
  SELECT i.*, COALESCE(SUM(it.qty * CASE
    WHEN (it.part_price + it.service_price) > 0 THEN (it.part_price + it.service_price)
    ELSE it.unit_price
  END), 0) AS subtotal, COUNT(it.id) AS item_count
  FROM service_invoices i
  LEFT JOIN service_invoice_items it ON it.invoice_id = i.id
  GROUP BY i.id
  ORDER BY i.service_date DESC, i.id DESC
")->fetchAll(PDO::FETCH_ASSOC);
admin_page_start('Nota Service', 'invoices', (int)$editing['id'] > 0 ? 'Edit nota service' : 'Buat nota service');
?>

    <?php if($ok): ?><div class="admin-alert success"><?= h($ok) ?></div><?php endif; ?>
    <?php foreach($errors as $err): ?><div class="admin-alert error"><?= h($err) ?></div><?php endforeach; ?>

    <section class="admin-card admin-section">
      <h2><?= (int)$editing['id'] > 0 ? 'Edit Nota' : 'Tambah Nota' ?></h2>
      <form method="post" class="admin-form invoice-form">
        <input type="hidden" name="id" value="<?= h((string)$editing['id']) ?>">
        <div class="invoice-form-grid">
          <label>No. Nota
            <input name="invoice_no" value="<?= h($editing['invoice_no']) ?>" required>
          </label>
          <label>Tanggal Service
            <input name="service_date" type="date" value="<?= h($editing['service_date']) ?>" required>
          </label>
          <label>Nama Pelanggan
            <input name="customer_name" value="<?= h($editing['customer_name']) ?>" required>
          </label>
          <label>No. WhatsApp
            <input name="customer_phone" value="<?= h($editing['customer_phone']) ?>">
          </label>
          <label class="wide">Alamat
            <input name="customer_address" value="<?= h($editing['customer_address']) ?>" placeholder="-">
          </label>
          <label>Perangkat
            <input name="device_type" value="<?= h($editing['device_type']) ?>" placeholder="Laptop / PC / Printer" required>
          </label>
          <label>Merk / Tipe
            <input name="device_model" value="<?= h($editing['device_model']) ?>">
          </label>
          <label class="wide">Keluhan
            <input name="complaint" value="<?= h($editing['complaint']) ?>">
          </label>
        </div>

        <div class="invoice-items-head">
          <h3>Item Layanan</h3>
          <button class="btn compact" type="button" data-add-invoice-row><i class="fa-solid fa-plus"></i> Tambah Item</button>
        </div>
        <div class="invoice-items" data-invoice-items>
          <?php foreach($editingItems as $item): ?>
            <div class="invoice-item-row">
              <label>Deskripsi
                <input name="item_description[]" value="<?= h($item['description']) ?>" placeholder="Upgrade HDD ke SSD">
              </label>
              <label>Keterangan
                <input name="item_detail[]" value="<?= h($item['detail']) ?>" placeholder="Penggantian HDD ke SSD baru">
              </label>
              <label>Qty
                <input name="item_qty[]" inputmode="numeric" pattern="[0-9]*" value="<?= h((string)$item['qty']) ?>" data-digits-only>
              </label>
              <?php
                $partPrice = (int)($item['part_price'] ?? 0);
                $servicePrice = (int)($item['service_price'] ?? 0);
                if ($partPrice === 0 && $servicePrice === 0 && (int)($item['unit_price'] ?? 0) > 0) {
                  $servicePrice = (int)$item['unit_price'];
                }
              ?>
              <label>Harga Modal Part
                <input name="item_part_capital_price[]" inputmode="numeric" pattern="[0-9.]*" value="<?= h(angka_ribuan($item['part_capital_price'] ?? 0)) ?>" placeholder="300.000" data-money-input>
              </label>
              <label>Harga Part (Up)
                <input name="item_part_price[]" inputmode="numeric" pattern="[0-9.]*" value="<?= h(angka_ribuan($partPrice)) ?>" placeholder="400.000" data-money-input>
              </label>
              <label>Harga Jasa
                <input name="item_service_price[]" inputmode="numeric" pattern="[0-9.]*" value="<?= h(angka_ribuan($servicePrice)) ?>" placeholder="150.000" data-money-input>
              </label>
              <button class="btn danger compact" type="button" data-remove-invoice-row><i class="fa-solid fa-trash"></i></button>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="invoice-form-grid invoice-total-fields">
          <label>Diskon
            <input name="discount" inputmode="numeric" pattern="[0-9.]*" value="<?= h(angka_ribuan($editing['discount'])) ?>" data-money-input>
          </label>
          <label>Garansi Service (hari)
            <input name="warranty_days" inputmode="numeric" pattern="[0-9]*" value="<?= h((string)$editing['warranty_days']) ?>" data-digits-only>
          </label>
          <label class="wide">Catatan
            <textarea name="notes" rows="4"><?= h($editing['notes']) ?></textarea>
          </label>
        </div>

        <div class="admin-actions">
          <button class="cta" type="submit"><i class="fa-regular fa-floppy-disk"></i> Simpan Nota</button>
          <?php if((int)$editing['id'] > 0): ?>
            <a class="btn" href="invoices_print.php?id=<?= h((string)$editing['id']) ?>" target="_blank"><i class="fa-solid fa-print"></i> Cetak PDF</a>
            <a class="btn" href="invoices.php"><i class="fa-solid fa-plus"></i> Nota Baru</a>
          <?php endif; ?>
        </div>
      </form>
    </section>

    <section class="admin-card admin-section" style="margin-top:18px">
      <h2>Daftar Nota</h2>
      <?php if(!$rows): ?>
        <p class="admin-muted">Belum ada nota service.</p>
      <?php else: ?>
        <div class="admin-table invoice-table">
          <div class="admin-table-head">
            <span>No. Nota</span>
            <span>Tanggal</span>
            <span>Pelanggan</span>
            <span>Perangkat</span>
            <span>Total</span>
            <span>Aksi</span>
          </div>
          <?php foreach($rows as $row): $total = max(0, (int)$row['subtotal'] - (int)$row['discount']); ?>
            <div class="admin-table-row">
              <span data-label="No. Nota"><?= h($row['invoice_no']) ?></span>
              <span data-label="Tanggal"><?= h($row['service_date']) ?></span>
              <span data-label="Pelanggan"><?= h($row['customer_name']) ?></span>
              <span data-label="Perangkat"><?= h(trim($row['device_type'] . ' ' . $row['device_model'])) ?></span>
              <span data-label="Total"><strong><?= h(rupiah($total)) ?></strong></span>
              <span data-label="Aksi">
                <div class="invoice-actions">
                  <a class="icon-action" href="invoices.php?edit=<?= h((string)$row['id']) ?>" title="Edit nota" aria-label="Edit nota <?= h($row['invoice_no']) ?>"><i class="fa-regular fa-pen-to-square"></i></a>
                  <a class="icon-action" href="invoices_print.php?id=<?= h((string)$row['id']) ?>" target="_blank" title="Cetak nota" aria-label="Cetak nota <?= h($row['invoice_no']) ?>"><i class="fa-solid fa-print"></i></a>
                  <form method="post" onsubmit="return confirm('Hapus nota <?= h($row['invoice_no']) ?> ?')">
                    <input type="hidden" name="delete" value="<?= h((string)$row['id']) ?>">
                    <button class="icon-action danger" type="submit" title="Hapus nota" aria-label="Hapus nota <?= h($row['invoice_no']) ?>"><i class="fa-solid fa-trash"></i></button>
                  </form>
                </div>
              </span>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
  <script>
    (function(){
      const wrap = document.querySelector('[data-invoice-items]');
      const add = document.querySelector('[data-add-invoice-row]');
      if(!wrap || !add) return;

      function onlyDigits(value){
        return String(value || '').replace(/\D+/g, '');
      }

      function formatMoney(value){
        const digits = onlyDigits(value);
        return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
      }

      function bindNumericInput(input){
        if(input.dataset.moneyInput !== undefined) {
          input.value = formatMoney(input.value);
          input.addEventListener('input', function(){
            input.value = formatMoney(input.value);
          });
          input.addEventListener('paste', function(){
            setTimeout(function(){ input.value = formatMoney(input.value); }, 0);
          });
          return;
        }

        if(input.dataset.digitsOnly !== undefined) {
          input.value = onlyDigits(input.value);
          input.addEventListener('input', function(){
            input.value = onlyDigits(input.value);
          });
          input.addEventListener('paste', function(){
            setTimeout(function(){ input.value = onlyDigits(input.value); }, 0);
          });
        }
      }

      document.querySelectorAll('[data-money-input],[data-digits-only]').forEach(bindNumericInput);

      function bindRemove(row){
        const button = row.querySelector('[data-remove-invoice-row]');
        if(!button) return;
        button.addEventListener('click', function(){
          if(wrap.querySelectorAll('.invoice-item-row').length <= 1) {
            row.querySelectorAll('input').forEach(function(input){
              input.value = input.name === 'item_qty[]' ? '1' : '';
              if(input.dataset.moneyInput !== undefined) input.value = '0';
            });
            return;
          }
          row.remove();
        });
      }

      wrap.querySelectorAll('.invoice-item-row').forEach(bindRemove);
      add.addEventListener('click', function(){
        const source = wrap.querySelector('.invoice-item-row');
        const clone = source.cloneNode(true);
        clone.querySelectorAll('input').forEach(function(input){
          input.value = input.name === 'item_qty[]' ? '1' : '';
          if(input.dataset.moneyInput !== undefined) input.value = '0';
          bindNumericInput(input);
        });
        wrap.appendChild(clone);
        bindRemove(clone);
      });
    })();
  </script>
<?php admin_page_end(); ?>
