<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$BASE = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= isset($judul) ? htmlspecialchars($judul).' · ' : '' ?>SewaPadel</title>
  <link rel="stylesheet" href="<?= $BASE ?>/../aset/css/gaya.css">
  <link rel="icon" href="<?= $BASE ?>/../aset/gambar/logo.png">
</head>
<body>
  <!-- Wrapper flex agar footer nempel bawah -->
  <div class="site">
