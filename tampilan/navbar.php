<?php
if (session_status() == PHP_SESSION_NONE) session_start();
require_once "../konfigurasi/fungsi.php"; // kalau $BASE didefinisikan di sini

$peran = $_SESSION['pengguna']['peran'] ?? 'tamu';
?>

<header class="navbar">
  <div class="nav-inner container">
    <a class="brand" href="<?= $BASE ?>/beranda.php">SewaPadel</a>

    <nav class="menu">
      <?php if ($peran === 'admin' || $peran === 'petugas'): ?>
        <!-- Jika Admin atau Petugas -->
      <?php elseif ($peran === 'pengguna'): ?>
        <!-- Jika Pengguna Biasa -->
        <a href="<?= $BASE ?>/beranda.php">Beranda</a>
        <a href="<?= $BASE ?>/cari_jadwal.php">Cari Jadwal</a>
        <a href="<?= $BASE ?>/pesan_lapangan.php">Pesan Lapangan</a>
        <a href="<?= $BASE ?>/riwayat_pesanan.php">Riwayat</a>
        <a class="btn small outline" href="<?= $BASE ?>/keluar.php">Keluar</a>

      <?php else: ?>
        <!-- Jika Belum Login -->
        <a class="btn small" href="<?= $BASE ?>/masuk.php">Masuk</a>
        <a class="btn small outline" href="<?= $BASE ?>/daftar.php">Daftar</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
