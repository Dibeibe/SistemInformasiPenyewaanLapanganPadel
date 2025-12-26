<?php
if (session_status() == PHP_SESSION_NONE) session_start();
?>
<div class="bg-dark text-white p-3 vh-100" style="width: 250px; position: fixed;">
  <h4 class="mb-4">Panel Admin</h4>
  <ul class="nav flex-column">
    <li class="nav-item mb-2"><a href="dashboard.php" class="nav-link text-white">🏠 Dashboard</a></li>
    <li class="nav-item mb-2"><a href="data_lapangan.php" class="nav-link text-white">🏟️ Lapangan</a></li>
    <li class="nav-item mb-2"><a href="foto_lapangan.php" class="nav-link text-white">🖼️ Foto Lapangan</a></li>
    <li class="nav-item mb-2"><a href="blok_jadwal.php" class="nav-link text-white">⛔ Blok Jadwal</a></li>
    <li class="nav-item mb-2"><a href="data_pesanan.php" class="nav-link text-white">📋 Pesanan</a></li>
    <li class="nav-item mb-2"><a href="pembayaran.php" class="nav-link text-white">💳 Pembayaran</a></li>
    <li class="nav-item mb-2"><a href="laporan.php" class="nav-link text-white">📈 Laporan</a></li>
    <li class="nav-item mb-2"><a href="data_pengguna.php" class="nav-link text-white">👥 Pengguna</a></li>
    <li class="nav-item mt-3"><a href="../halaman/keluar.php" class="btn btn-danger w-100">Keluar</a></li>
  </ul>
</div>
<div style="margin-left:260px; padding:20px;">
