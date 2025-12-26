<?php
require_once "../konfigurasi/koneksi.php";
require_once "../konfigurasi/fungsi.php";
require_once "../konfigurasi/otentikasi.php";

// Hanya admin & petugas
wajib_peran(['admin', 'petugas']);

$judul = "Data Lapangan";
require_once "../tampilan/header.php";
require_once "../tampilan/navbar.php";

// Base URL project lokal
$BASE_URL = "http://localhost/sistem-sewa-padel/";

/* Ambil data lapangan + foto utama */
$sql = "
  SELECT 
    l.id, 
    l.nama, 
    l.jenis, 
    l.harga_per_jam, 
    l.aktif,
    f.path_file AS foto_utama
  FROM lapangan l
  LEFT JOIN foto_lapangan f 
    ON f.lapangan_id = l.id AND f.urutan = 1
  ORDER BY l.id DESC
";
$stmt = $koneksi->query($sql);
$lapangan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="konten">
  <section class="container">
    <div class="header-row" style="display:flex;justify-content:space-between;align-items:center;gap:12px;">
      <div>
        <h1>Data Lapangan</h1>
        <p class="meta">Kelola daftar lapangan padel yang tersedia untuk disewa.</p>
      </div>
      <div>
        <a class="btn" href="form_lapangan.php">+ Tambah Lapangan</a>
      </div>
    </div>

    <div class="table-card mt-3">
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th style="width:80px;">Foto</th>
              <th>Nama</th>
              <th>Jenis</th>
              <th style="width:160px;">Harga/Jam</th>
              <th style="width:120px;">Status</th>
              <th style="width:220px; text-align:left;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($lapangan)): ?>
              <tr>
                <td colspan="6" class="empty">Belum ada lapangan. Klik “Tambah Lapangan”.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($lapangan as $row): ?>
                <tr>
                  <td style="vertical-align:middle;">
                    <?php
                    $foto_url = '';
                    if (!empty($row['foto_utama'])) {
                      // Hilangkan slash di awal biar URL gak dobel
                      $path = ltrim($row['foto_utama'], '/');
                      $foto_url = $BASE_URL . $path;
                    }
                    ?>

                    <?php if (!empty($foto_url) && file_exists("../" . $row['foto_utama'])): ?>
                      <img src="<?= htmlspecialchars($foto_url) ?>" alt="Foto Lapangan"
                           style="width:70px;height:50px;object-fit:cover;border-radius:6px;">
                    <?php else: ?>
                      <span class="text-muted">Tidak ada foto</span>
                    <?php endif; ?>
                  </td>

                  <td style="vertical-align:middle;"><?= htmlspecialchars($row['nama']) ?></td>
                  <td style="vertical-align:middle;"><?= htmlspecialchars(ucfirst($row['jenis'])) ?></td>
                  <td style="vertical-align:middle;"><?= format_rupiah($row['harga_per_jam']) ?></td>
                  <td style="vertical-align:middle;">
                    <?php if ((int)$row['aktif'] === 1): ?>
                      <span class="badge badge-success">Aktif</span>
                    <?php else: ?>
                      <span class="badge badge-warning">Nonaktif</span>
                    <?php endif; ?>
                  </td>
                  <td class="actions"
                      style="vertical-align:middle; text-align:left;
                             display:flex; gap:8px; align-items:center; justify-content:flex-start;
                             white-space:nowrap;">
                    <a class="outline small" href="form_lapangan.php?id=<?= (int)$row['id'] ?>">Edit</a>
                    <a class="outline small" href="foto_lapangan.php?lapangan_id=<?= (int)$row['id'] ?>">Foto</a>
                    <a class="danger small"
                       href="hapus_lapangan.php?id=<?= (int)$row['id'] ?>"
                       onclick="return confirm('Yakin hapus lapangan ini?')">Hapus</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="hero-actions mt-3">
      <a class="outline" href="beranda.php">Kembali ke Dashboard</a>
    </div>
  </section>
</main>

<?php require_once "../tampilan/footer.php"; ?>
