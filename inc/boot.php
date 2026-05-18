<?php
// Config dasar
$CONFIG = [
  'brand'   => 'Service Laptop & Elektronik Adzp18',
  'email'   => 'sadzypurnomo@gmail.com',
  'phone'   => '085880495407',
  'wa'      => '085880495407',
  'address' => 'Perumahan Permata Land, Blok Ruby H No.5, Bagan Pete, Kec. Kota Baru, Kota Jambi, Jambi 36361',
  'maps'    => 'https://maps.app.goo.gl/aX718QCLTx9SyhN2A',
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

// helper
function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

// flash message (simple)
session_start();
function flash($key,$val=null){
  if($val===null){ $v = $_SESSION['flash'][$key] ?? null; unset($_SESSION['flash'][$key]); return $v; }
  $_SESSION['flash'][$key] = $val;
}
