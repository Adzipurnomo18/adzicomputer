<?php
$allowed = ['home','katalog','kontak'];
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '.' || $basePath === '/') {
  $basePath = '';
}
if ($basePath !== '' && $requestPath === $basePath) {
  $requestPath = '/';
} elseif ($basePath !== '' && str_starts_with($requestPath, $basePath . '/')) {
  $requestPath = substr($requestPath, strlen($basePath));
}
$path = trim($requestPath, '/');
$active = $_GET['page'] ?? ($path === '' ? 'home' : $path);
if(!in_array($active,$allowed,true)) $active='home';
$pageJsPath = __DIR__ . "/assets/js/{$active}.js";
$pageJsVersion = is_file($pageJsPath) ? filemtime($pageJsPath) : time();
$pageJs  = "<script src=\"assets/js/{$active}.js?v={$pageJsVersion}\"></script>";
include __DIR__.'/inc/header.php';
include __DIR__.'/pages/'.$active.'.php';
include __DIR__.'/inc/footer.php';
