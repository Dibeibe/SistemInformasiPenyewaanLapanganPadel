<?php
require_once "../konfigurasi/koneksi.php";
require_once "../konfigurasi/fungsi.php";
require_once "../konfigurasi/otentikasi.php";

wajib_login();

$idPengguna = (int)($_SESSION['pengguna']['id'] ?? 0);

// Ambil semua pesanan milik user + status pembayaran
$sql = "
SELECT p.*,
       l.nama        AS nama_lapangan,
       l.jenis       AS jenis_lapangan,
       pb.status_bayar,
       pb.bukti_path
FROM pesanan p
JOIN lapangan l       ON l.id = p.lapangan_id
LEFT JOIN pembayaran pb ON pb.pesanan_id = p.id
WHERE p.pengguna_id = ?
ORDER BY p.tanggal DESC, p.jam_mulai DESC
";

$stmt = $koneksi->prepare($sql);
$stmt->execute([$idPengguna]);
$pesanan = $stmt->fetchAll(PDO::FETCH_ASSOC);

$judul = "Riwayat Pesanan";
require_once "../tampilan/header.php";
require_once "../tampilan/navbar.php";
?>

<main class="konten">
  <section class="container">
    <h1>Riwayat Pesanan Lapangan</h1>
    <p class="meta">Berikut adalah daftar pesanan lapangan yang pernah Anda buat.</p>

    <?php if ($msg = get_flash('sukses')): ?>
      <div class="alert alert-success" style="margin-top:12px;"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    <?php if ($msg = get_flash('error')): ?>
      <div class="alert alert-danger" style="margin-top:12px;"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <div class="table-card mt-3">
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Lapangan</th>
              <th>Tanggal</th>
              <th>Jam</th>
              <th>Durasi</th>
              <th>Total</th>
              <th>Status Pesanan</th>
              <th>Status Bayar</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($pesanan)): ?>
              <tr>
                <td colspan="9" class="empty">Belum ada pesanan.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($pesanan as $row): ?>
                <tr>
                  <td>#<?= (int)$row['id'] ?></td>
                  <td>
                    <?= htmlspecialchars($row['nama_lapangan']) ?>
                    <?php if (!empty($row['jenis_lapangan'])): ?>
                      <br><span class="meta"><?= htmlspecialchars(ucfirst($row['jenis_lapangan'])) ?></span>
                    <?php endif; ?>
                  </td>
                  <td><?= format_tanggal_indonesia($row['tanggal']) ?></td>
                  <td>
                    <?= htmlspecialchars(substr($row['jam_mulai'],0,5)) ?>
                    -
                    <?= htmlspecialchars(substr($row['jam_selesai'],0,5)) ?>
                  </td>
                  <td><?= htmlspecialchars($row['durasi_jam']) ?> jam</td>
                  <td><?= format_rupiah($row['total_bayar']) ?></td>
                  <td><?= htmlspecialchars(ucfirst($row['status_pesanan'] ?? 'menunggu')) ?></td>
                  <td><?= htmlspecialchars($row['status_bayar'] ?? 'Belum Bayar') ?></td>
                  <td>
                    <a class="outline small" href="detail_pesanan.php?id=<?= (int)$row['id'] ?>">
                      Detail
                    </a>
                  </td>
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
