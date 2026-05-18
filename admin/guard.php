<?php
require __DIR__.'/../inc/boot.php';
if(empty($_SESSION['admin'])){ header('Location: index.php'); exit; }
