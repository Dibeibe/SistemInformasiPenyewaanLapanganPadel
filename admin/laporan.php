<?php
require_once "../konfigurasi/koneksi.php";   // $koneksi = PDO
require_once "../konfigurasi/fungsi.php";
require_once "../konfigurasi/otentikasi.php";

wajib_peran(['admin','petugas']);

$judul = "Laporan Pendapatan";
require_once "../tampilan/header.php";
require_once "../tampilan/navbar.php";

/*
  Ambil total pendapatan per-tanggal hanya yang status_bayar = sudah_bayar
*/
$sql = "
SELECT DATE(pb.dibayar_pada) AS tanggal,
       SUM(pb.jumlah) AS total
FROM pembayaran pb
WHERE pb.status_bayar = 'sudah_bayar'
GROUP BY DATE(pb.dibayar_pada)
ORDER BY tanggal DESC
";
$data = $koneksi->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="konten">
  <section class="container">
    <h1>Laporan Pendapatan</h1>
    <p class="meta">Rekap pendapatan dari transaksi yang sudah dibayar.</p>

    <div class="hero-actions" style="margin-top:14px;">
      <a class="btn small" href="export_excel.php">Export ke Excel</a>
      <a class="outline small" href="beranda.php">Kembali</a>
    </div>

    <div class="table-card mt-3">
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th style="width:180px;">Tanggal</th>
              <th>Total Pendapatan</th>
            </tr>
          </thead>
          <tbody>
          <?php if (empty($data)): ?>
            <tr><td colspan="2" class="empty">Belum ada transaksi.</td></tr>
          <?php else: ?>
            <?php foreach ($data as $row): ?>
              <tr>
                <td><?= htmlspecialchars(format_tanggal_indonesia($row['tanggal'])) ?></td>
                <td><?= format_rupiah($row['total']) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</main>

<?php require_once "../tampilan/footer.php"; ?>
