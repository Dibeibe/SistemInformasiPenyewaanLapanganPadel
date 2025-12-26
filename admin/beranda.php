<?php
require_once "../konfigurasi/koneksi.php";
require_once "../konfigurasi/fungsi.php";
require_once "../konfigurasi/otentikasi.php";

// Khusus admin & petugas
wajib_peran(['admin','petugas']);

$judul = "Dashboard Admin";
require_once "../tampilan/header.php";
require_once "../tampilan/navbar.php";

// ===================== RINGKASAN DATA =====================
$tanggal = date('Y-m-d');

// Jumlah pesanan hari ini
$stmt = $koneksi->prepare("SELECT COUNT(*) FROM pesanan WHERE tanggal = ?");
$stmt->execute([$tanggal]);
$pesanan = (int)$stmt->fetchColumn();

// Total pendapatan hari ini
$stmt = $koneksi->prepare("
    SELECT COALESCE(SUM(pb.jumlah),0)
    FROM pembayaran pb
    JOIN pesanan p ON p.id = pb.pesanan_id
    WHERE p.tanggal = ? AND pb.status_bayar='sudah_bayar'
");
$stmt->execute([$tanggal]);
$pendapatan = (int)$stmt->fetchColumn();

// Jumlah lapangan aktif
$stmt = $koneksi->query("SELECT COUNT(*) FROM lapangan WHERE aktif=1");
$lapangan = (int)$stmt->fetchColumn();
?>

<main class="konten">
  <section class="container">
    <h1>Dashboard Admin</h1>
    <p class="meta">Ringkasan Data Hari Ini (<?= format_tanggal_indonesia($tanggal) ?>)</p>

    <div class="grid-lapangan" style="margin-top:24px; grid-template-columns: repeat(auto-fit, minmax(240px,1fr));">

      <div class="lapangan-card">
        <div class="lapangan-body">
          <h3>Total Pesanan Hari Ini</h3>
          <p class="price" style="font-size:28px;"><?= $pesanan ?></p>
        </div>
      </div>

      <div class="lapangan-card">
        <div class="lapangan-body">
          <h3>Pendapatan Hari Ini</h3>
          <p class="price" style="font-size:28px;"><?= format_rupiah($pendapatan) ?></p>
        </div>
      </div>

      <div class="lapangan-card">
        <div class="lapangan-body">
          <h3>Lapangan Aktif</h3>
          <p class="price" style="font-size:28px;"><?= $lapangan ?></p>
        </div>
      </div>

    </div>

    <div class="hero-actions" style="margin-top:28px;">
  <a class="btn" href="data_lapangan.php">Kelola Lapangan</a>
  <a class="outline" href="data_pesanan.php">Data Pesanan</a>
  <a class="outline" href="pembayaran.php">Pembayaran</a>
  <a class="outline" href="laporan.php">Laporan Pendapatan</a>
  <a class="outline" href="../halaman/keluar.php">Keluar</a>
</div>


  </section>
</main>

<?php require_once "../tampilan/footer.php"; ?>
