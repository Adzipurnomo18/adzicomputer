<?php
$active = $_GET['page'] ?? 'home';
$allowed = ['home','katalog','kontak'];
if(!in_array($active,$allowed)) $active='home';
$pageCss = "<link rel=\"stylesheet\" href=\"assets/css/{$active}.css\"/>";
$pageJs  = "<script src=\"assets/js/{$active}.js\"></script>";
include __DIR__.'/inc/header.php';
include __DIR__.'/pages/'.$active.'.php';
include __DIR__.'/inc/footer.php';
