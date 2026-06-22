<?php
// Config dasar
$CONFIG = [
  'brand'   => 'Service Laptop & Elektronik Adzp18',
  'email'   => 'sadzypurnomo@gmail.com',
  'phone'   => '085880495407',
  'wa'      => '085880495407',
  'address' => 'Perumahan Permata Land, Blok Ruby H No.5, Bagan Pete, Kec. Kota Baru, Kota Jambi, Jambi 36361',
  'maps'    => 'https://maps.app.goo.gl/aX718QCLTx9SyhN2A',
  'maps_embed' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.133870883912!2d103.55593239999999!3d-1.6649658999999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e2587e31244add1%3A0x8701b461d37129c4!2sService%20Laptop%20dan%20Elektronik!5e0!3m2!1sid!2sid!4v1762330598318!5m2!1sid!2sid',
  'admin_user' => 'adzp18',
  // password hashed: ganti via password_hash('passwordBaru', PASSWORD_BCRYPT)
  'admin_pass_hash' => '$2y$10$fqhrx5Z2FXxLZVcJ6qan4e9N18btTdt.rZFoD4S9zBEgaUXYxJXiy', // default: Adzi@2025
];

// SQLite connect
$dbFile = __DIR__ . '/../data/app.db';
if (!is_dir(dirname($dbFile))) mkdir(dirname($dbFile), 0777, true);
$pdo = new PDO('sqlite:' . $dbFile);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec('PRAGMA foreign_keys = ON');

// Auto-migrate tables (idempotent)
$pdo->exec("CREATE TABLE IF NOT EXISTS gallery (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  type TEXT NOT NULL CHECK(type IN ('image','video')),
  path TEXT NOT NULL,
  caption TEXT DEFAULT '',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");
$pdo->exec("CREATE TABLE IF NOT EXISTS messages (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  email TEXT NOT NULL,
  content TEXT NOT NULL,
  is_read INTEGER DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");
$pdo->exec("CREATE TABLE IF NOT EXISTS testimonials (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  image TEXT NOT NULL,
  caption TEXT DEFAULT '',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");
$pdo->exec("CREATE TABLE IF NOT EXISTS admins (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  username TEXT NOT NULL UNIQUE,
  display_name TEXT NOT NULL DEFAULT 'Admin',
  password_hash TEXT NOT NULL,
  role TEXT NOT NULL DEFAULT 'Super Admin',
  is_active INTEGER NOT NULL DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");
$adminCount = (int)$pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn();
if ($adminCount === 0) {
  $st = $pdo->prepare('INSERT INTO admins(username, display_name, password_hash, role) VALUES(?,?,?,?)');
  $st->execute([$CONFIG['admin_user'], 'Admin', $CONFIG['admin_pass_hash'], 'Super Admin']);
}
$pdo->exec("CREATE TABLE IF NOT EXISTS service_invoices (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  invoice_no TEXT NOT NULL UNIQUE,
  service_date DATE NOT NULL,
  customer_name TEXT NOT NULL,
  customer_phone TEXT DEFAULT '',
  customer_address TEXT DEFAULT '',
  device_type TEXT NOT NULL,
  device_model TEXT DEFAULT '',
  complaint TEXT DEFAULT '',
  discount INTEGER DEFAULT 0,
  warranty_days INTEGER DEFAULT 30,
  notes TEXT DEFAULT '',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");
$pdo->exec("CREATE TABLE IF NOT EXISTS service_invoice_items (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  invoice_id INTEGER NOT NULL,
  description TEXT NOT NULL,
  detail TEXT DEFAULT '',
  qty INTEGER NOT NULL DEFAULT 1,
  part_price INTEGER NOT NULL DEFAULT 0,
  service_price INTEGER NOT NULL DEFAULT 0,
  unit_price INTEGER NOT NULL DEFAULT 0,
  position INTEGER NOT NULL DEFAULT 1,
  FOREIGN KEY(invoice_id) REFERENCES service_invoices(id) ON DELETE CASCADE
)");
$invoiceItemColumns = $pdo->query("PRAGMA table_info(service_invoice_items)")->fetchAll(PDO::FETCH_COLUMN, 1);
if (!in_array('part_price', $invoiceItemColumns, true)) {
  $pdo->exec("ALTER TABLE service_invoice_items ADD COLUMN part_price INTEGER NOT NULL DEFAULT 0");
}
if (!in_array('service_price', $invoiceItemColumns, true)) {
  $pdo->exec("ALTER TABLE service_invoice_items ADD COLUMN service_price INTEGER NOT NULL DEFAULT 0");
}
if (!in_array('part_capital_price', $invoiceItemColumns, true)) {
  $pdo->exec("ALTER TABLE service_invoice_items ADD COLUMN part_capital_price INTEGER NOT NULL DEFAULT 0");
}
$pdo->exec("UPDATE service_invoice_items
  SET service_price = unit_price
  WHERE service_price = 0 AND part_price = 0 AND unit_price > 0");

// helper
function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

// flash message (simple)
session_start();
function flash($key,$val=null){
  if($val===null){ $v = $_SESSION['flash'][$key] ?? null; unset($_SESSION['flash'][$key]); return $v; }
  $_SESSION['flash'][$key] = $val;
}
