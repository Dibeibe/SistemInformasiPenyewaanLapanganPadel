<?php
require_once "../konfigurasi/koneksi.php";   // $koneksi = PDO
require_once "../konfigurasi/fungsi.php";
require_once "../konfigurasi/otentikasi.php";

wajib_peran(['admin','petugas']); // hanya admin/petugas

/* ===================== UPDATE STATUS (via GET, dengan whitelist) ===================== */
if (isset($_GET['id'], $_GET['aksi'])) {
    $id  = (int)$_GET['id'];
    $aksi = $_GET['aksi'];

    // Map aksi -> status_pesanan sesuai skema
    $allowed = [
        'setuju'  => 'terkonfirmasi',
        'tolak'   => 'dibatalkan',
        'selesai' => 'selesai',
        'pending' => 'menunggu',
    ];

    if ($id > 0 && isset($allowed[$aksi])) {
        $st = $koneksi->prepare("UPDATE pesanan SET status_pesanan=? WHERE id=?");
        $st->execute([$allowed[$aksi], $id]);
        set_flash('ok', "Status pesanan #$id diubah menjadi {$allowed[$aksi]}.");
    }
    header("Location: data_pesanan.php");
    exit;
}

/* ===================== AMBIL DATA PESANAN ===================== */
$sql = "
SELECT 
  p.id,
  p.tanggal,
  p.jam_mulai,
  p.jam_selesai,
  p.status_pesanan,
  p.total_bayar,
  u.nama     AS nama_pengguna,
  l.nama     AS nama_lapangan
FROM pesanan p
JOIN pengguna u ON u.id = p.pengguna_id
JOIN lapangan l ON l.id = p.lapangan_id
ORDER BY p.tanggal DESC, p.jam_mulai DESC";
$pesanan = $koneksi->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$judul = "Data Pesanan";
require_once "../tampilan/header.php";
require_once "../tampilan/navbar.php";
$flashOk = get_flash('ok');

/* helper kecil untuk badge status */
function badge_status($status) {
    $map = [
        'menunggu'      => 'badge-warning',
        'terkonfirmasi' => 'badge-success',
        'selesai'       => 'badge-success',
        'dibatalkan'    => 'badge',
        'refund'        => 'badge'
    ];
    $cls = $map[$status] ?? 'badge';
    return "<span class=\"badge $cls\">".htmlspecialchars(ucfirst($status))."</span>";
}
?>

<main class="konten">
  <section class="container">
    <div class="header-row" style="display:flex;justify-content:space-between;align-items:center;gap:12px;">
      <div>
        <h1>Data Pesanan</h1>
        <p class="meta">Kelola pemesanan lapangan. Klik aksi untuk mengubah status.</p>
      </div>
      <div class="hero-actions">
        <a class="outline" href="beranda.php">Kembali ke Dashboard</a>
      </div>
    </div>

    <?php if ($flashOk): ?>
      <div class="alert alert-success" style="margin-top:12px;"><?= htmlspecialchars($flashOk) ?></div>
    <?php endif; ?>

    <div class="table-card mt-3">
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th style="width:80px">ID</th>
              <th>Pemesan</th>
              <th>Lapangan</th>
              <th style="width:160px">Tanggal</th>
              <th style="width:160px">Jam</th>
              <th style="width:140px">Total</th>
              <th style="width:140px">Status</th>
              <th style="width:260px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($pesanan)): ?>
              <tr><td colspan="8" class="empty">Belum ada pesanan.</td></tr>
            <?php else: ?>
              <?php foreach ($pesanan as $row): ?>
                <tr>
                  <td>#<?= (int)$row['id'] ?></td>
                  <td><?= htmlspecialchars($row['nama_pengguna']) ?></td>
                  <td><?= htmlspecialchars($row['nama_lapangan']) ?></td>
                  <td><?= htmlspecialchars(format_tanggal_indonesia($row['tanggal'])) ?></td>
                  <td><?= htmlspecialchars(substr($row['jam_mulai'],0,5)) ?>–<?= htmlspecialchars(substr($row['jam_selesai'],0,5)) ?></td>
                  <td><?= format_rupiah($row['total_bayar']) ?></td>
                  <td><?= badge_status($row['status_pesanan']) ?></td>
                  <td class="actions">
                    <a class="outline small" href="data_pesanan.php?id=<?= (int)$row['id'] ?>&aksi=setuju"
                       onclick="return confirm('Setujui pesanan #<?= (int)$row['id'] ?>?')">Setujui</a>
                    <a class="danger small" href="data_pesanan.php?id=<?= (int)$row['id'] ?>&aksi=tolak"
                       onclick="return confirm('Tolak pesanan #<?= (int)$row['id'] ?>?')">Tolak</a>
                    <a class="outline small" href="data_pesanan.php?id=<?= (int)$row['id'] ?>&aksi=selesai"
                       onclick="return confirm('Tandai selesai pesanan #<?= (int)$row['id'] ?>?')">Selesai</a>
                    <a class="outline small" href="detail_pesanan.php?id=<?= (int)$row['id'] ?>">Detail</a>
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
