<?php
session_start();

// Kalau admin atau petugas → masuk ke panel admin
if (isset($_SESSION['peran']) && ($_SESSION['peran'] === 'admin' || $_SESSION['peran'] === 'petugas')) {
    header("Location: admin/dashboard.php");
    exit;
}

// Kalau pengguna biasa atau belum login → ke beranda
header("Location: halaman/beranda.php");
exit;
