<?php
require_once "../konfigurasi/koneksi.php";
require_once "../konfigurasi/fungsi.php";
require_once "../konfigurasi/otentikasi.php";

wajib_login();

if (!isset($_GET['id'])) {
    redirect('riwayat_pesanan.php');
}

$idPesanan  = (int)$_GET['id'];
$idPengguna = (int)($_SESSION['pengguna']['id'] ?? 0);

// Ambil data pesanan + lapangan + pembayaran (kalau ada)
$sql = "
SELECT p.*,
       l.nama         AS nama_lapangan,
       l.jenis        AS jenis_lapangan,
       l.lokasi       AS lokasi_lapangan,
       l.harga_per_jam,
       pb.id          AS pembayaran_id,
       pb.status_bayar,
       pb.jumlah      AS jumlah_bayar,
       pb.bukti_path,
       pb.dibayar_pada
FROM pesanan p
JOIN lapangan l     ON l.id = p.lapangan_id
LEFT JOIN pembayaran pb ON pb.pesanan_id = p.id
WHERE p.id = ? AND p.pengguna_id = ?
LIMIT 1
";

$stmt = $koneksi->prepare($sql);
$stmt->execute([$idPesanan, $idPengguna]);
$pesanan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pesanan) {
    set_flash('error', 'Pesanan tidak ditemukan.');
    redirect('riwayat_pesanan.php');
}

$judul = "Detail Pesanan";
require_once "../tampilan/header.php";
require_once "../tampilan/navbar.php";

$BASE_URL_UPLOAD = "../aset/uploads/";
?>

<main class="konten">
  <section class="container" style="max-width:900px;">
    <h1>Detail Pesanan #<?= (int)$pesanan['id'] ?></h1>
    <p class="meta">
      Lapangan: <strong><?= htmlspecialchars($pesanan['nama_lapangan']) ?></strong>
      <?php if (!empty($pesanan['jenis_lapangan'])): ?>
        · Jenis: <?= htmlspecialchars(ucfirst($pesanan['jenis_lapangan'])) ?>
      <?php endif; ?>
      <?php if (!empty($pesanan['lokasi_lapangan'])): ?>
        · Lokasi: <?= htmlspecialchars($pesanan['lokasi_lapangan']) ?>
      <?php endif; ?>
    </p>

    <!-- BAGIAN INFO PESANAN – RATA KIRI -->
    <div class="table-card mt-3">
      <table class="table" style="width:100%; border-collapse:collapse;">
        <tbody>
          <tr>
            <td style="width:220px; font-weight:600; padding:6px 0; text-align:left;">Tanggal</td>
            <td style="padding:6px 0; text-align:left;"><?= format_tanggal_indonesia($pesanan['tanggal']) ?></td>
          </tr>
          <tr>
            <td style="font-weight:600; padding:6px 0; text-align:left;">Jam</td>
            <td style="padding:6px 0; text-align:left;">
              <?= htmlspecialchars(substr($pesanan['jam_mulai'],0,5)) ?>
              -
              <?= htmlspecialchars(substr($pesanan['jam_selesai'],0,5)) ?>
            </td>
          </tr>
          <tr>
            <td style="font-weight:600; padding:6px 0; text-align:left;">Durasi</td>
            <td style="padding:6px 0; text-align:left;"><?= htmlspecialchars($pesanan['durasi_jam']) ?> jam</td>
          </tr>
          <tr>
            <td style="font-weight:600; padding:6px 0; text-align:left;">Total Bayar</td>
            <td style="padding:6px 0; text-align:left;"><?= format_rupiah($pesanan['total_bayar']) ?></td>
          </tr>
          <tr>
            <td style="font-weight:600; padding:6px 0; text-align:left;">Status Pesanan</td>
            <td style="padding:6px 0; text-align:left;"><?= htmlspecialchars(ucfirst($pesanan['status_pesanan'] ?? 'menunggu')) ?></td>
          </tr>
          <tr>
            <td style="font-weight:600; padding:6px 0; text-align:left;">Status Pembayaran</td>
            <td style="padding:6px 0; text-align:left;"><?= htmlspecialchars($pesanan['status_bayar'] ?? 'belum_bayar') ?></td>
          </tr>
          <?php if (!empty($pesanan['catatan'])): ?>
          <tr>
            <td style="font-weight:600; padding:6px 0; text-align:left;">Catatan</td>
            <td style="padding:6px 0; text-align:left;"><?= nl2br(htmlspecialchars($pesanan['catatan'])) ?></td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="mt-4">
      <h2>Bukti Pembayaran</h2>

      <?php if (empty($pesanan['bukti_path'])): ?>
        <p class="meta">Belum ada bukti pembayaran yang diunggah.</p>
      <?php else: ?>
        <p class="meta">Berikut bukti pembayaran yang Anda kirim:</p>
        <div style="margin-top:8px;">
          <img src="<?= $BASE_URL_UPLOAD . htmlspecialchars($pesanan['bukti_path']) ?>"
               alt="Bukti pembayaran"
               style="max-width:100%;max-height:420px;border-radius:10px;border:1px solid #e2e8f0;object-fit:contain;">
        </div>
        <p style="margin-top:8px;">
          <a class="outline small"
             href="<?= $BASE_URL_UPLOAD . htmlspecialchars($pesanan['bukti_path']) ?>"
             target="_blank">
            Lihat ukuran penuh
          </a>
        </p>
        <?php if (!empty($pesanan['dibayar_pada'])): ?>
          <p class="meta">Dibayar pada: <?= htmlspecialchars($pesanan['dibayar_pada']) ?></p>
        <?php endif; ?>
      <?php endif; ?>
    </div>

    <div class="hero-actions mt-4">
      <a class="outline" href="riwayat_pesanan.php">Kembali ke Riwayat</a>
    </div>
  </section>
</main>

<?php require_once "../tampilan/footer.php"; ?>
