<?php
require_once "../konfigurasi/koneksi.php";
require_once "../konfigurasi/fungsi.php";
require_once "../konfigurasi/otentikasi.php";

wajib_login(); 
// Kalau punya fungsi wajib_admin() atau wajib_peran('admin'), bisa pakai itu

// ========== Aksi ubah status pembayaran ==========
if (isset($_GET['id'], $_GET['aksi'])) {
    $id   = (int)$_GET['id'];
    $aksi = $_GET['aksi'];

    $mapStatus = [
        'lunas'  => 'sudah_bayar',
        'gagal'  => 'gagal',
        'refund' => 'refund'
    ];

    if (isset($mapStatus[$aksi])) {
        $stmt = $koneksi->prepare("UPDATE pembayaran SET status_bayar = ? WHERE id = ?");
        $stmt->execute([$mapStatus[$aksi], $id]);
        set_flash('sukses', 'Status pembayaran berhasil diubah.');
    }

    redirect('pembayaran.php');
}

// ========== Ambil data pembayaran + relasi ==========
$sql = "
SELECT pb.*,
       p.tanggal,
       p.jam_mulai,
       p.jam_selesai,
       p.total_bayar,
       u.nama    AS nama_pengguna,
       l.nama    AS nama_lapangan
FROM pembayaran pb
JOIN pesanan p  ON p.id = pb.pesanan_id
JOIN pengguna u ON u.id = p.pengguna_id
JOIN lapangan l ON l.id = p.lapangan_id
ORDER BY pb.dibayar_pada DESC, p.tanggal DESC, p.jam_mulai DESC
";

$stmt = $koneksi->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$judul = "Data Pembayaran";
require_once "../tampilan/header.php";
require_once "../tampilan/navbar.php";

$BASE_URL_UPLOAD = "../aset/uploads/";
?>

<main class="konten">
  <section class="container">
    <div class="header-row" style="display:flex;justify-content:space-between;align-items:center;gap:12px;">
      <div>
        <h1>Data Pembayaran</h1>
        <p class="meta">Cek bukti pembayaran dan atur status pembayaran pesanan lapangan padel.</p>
      </div>
      <div class="hero-actions">
        <a class="outline" href="beranda.php">Kembali ke Dashboard</a>
      </div>
    </div>

    <?php if ($msg = get_flash('sukses')): ?>
      <div class="alert alert-success" style="margin-top:12px;"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <div class="table-card mt-3">
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Pengguna</th>
              <th>Lapangan</th>
              <th>Tanggal / Jam</th>
              <th>Jumlah</th>
              <th>Status</th>
              <th>Bukti</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($data)): ?>
              <tr>
                <td colspan="8" class="empty">Belum ada data pembayaran.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($data as $row): ?>
                <tr>
                  <td>#<?= (int)$row['id'] ?></td>
                  <td><?= htmlspecialchars($row['nama_pengguna']) ?></td>
                  <td><?= htmlspecialchars($row['nama_lapangan']) ?></td>
                  <td>
                    <?= format_tanggal_indonesia($row['tanggal']) ?><br>
                    <span class="meta">
                      <?= htmlspecialchars(substr($row['jam_mulai'],0,5)) ?>
                      -
                      <?= htmlspecialchars(substr($row['jam_selesai'],0,5)) ?>
                    </span>
                  </td>
                  <td><?= format_rupiah($row['jumlah']) ?></td>
                  <td><?= htmlspecialchars($row['status_bayar']) ?></td>
                  <td>
                    <?php if (!empty($row['bukti_path'])): ?>
                      <a class="outline small"
                         href="<?= $BASE_URL_UPLOAD . htmlspecialchars($row['bukti_path']) ?>"
                         target="_blank">
                        Lihat Bukti
                      </a>
                    <?php else: ?>
                      <span class="meta">Tidak ada bukti</span>
                    <?php endif; ?>
                  </td>
                  <td class="actions">
                    <a class="outline small"
                       href="pembayaran.php?id=<?= (int)$row['id'] ?>&aksi=lunas">
                      Tandai Lunas
                    </a>
                    <a class="outline small"
                       href="pembayaran.php?id=<?= (int)$row['id'] ?>&aksi=gagal">
                      Gagal
                    </a>
                    <a class="outline small"
                       href="pembayaran.php?id=<?= (int)$row['id'] ?>&aksi=refund">
                      Refund
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
