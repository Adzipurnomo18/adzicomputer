<?php
$active = $_GET['page'] ?? 'home';
$allowed = ['home','katalog','kontak'];
if(!in_array($active,$allowed)) $active='home';
$pageJs  = "<script src=\"assets/js/{$active}.js\"></script>";
include __DIR__.'/inc/header.php';
include __DIR__.'/pages/'.$active.'.php';
include __DIR__.'/inc/footer.php';
