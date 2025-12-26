<?php
require_once '../konfigurasi/koneksi.php';
require_once '../konfigurasi/fungsi.php';
require_once '../konfigurasi/csrf.php';

$judul = 'Cari Jadwal';
require_once '../tampilan/header.php';
require_once '../tampilan/navbar.php';

$tanggal = $_GET['tanggal'] ?? date('Y-m-d');

/* Ambil semua lapangan aktif + foto pertamanya */
$sql = "
SELECT l.*,
  (SELECT f.path_file FROM foto_lapangan f
   WHERE f.lapangan_id = l.id
   ORDER BY f.urutan ASC, f.id ASC
   LIMIT 1) AS foto
FROM lapangan l
WHERE l.aktif = 1
ORDER BY l.nama";
$lapangan = $koneksi->query($sql)->fetchAll(PDO::FETCH_ASSOC);

/* Siapkan statement hitung pesanan pada tanggal tsb */
$stmtHitung = $koneksi->prepare("
  SELECT COUNT(*) 
  FROM pesanan 
  WHERE lapangan_id = ? 
    AND tanggal = ? 
    AND status_pesanan IN ('menunggu','terkonfirmasi','selesai')
");

$BASE = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$placeholder = $BASE . '/../aset/gambar/placeholder.jpg';
?>

<main class="konten">
  <section class="hero">
    <div class="container">
      <h1>Cari Jadwal Tersedia</h1>
      <p>Pilih tanggal untuk melihat ketersediaan tiap lapangan.</p>

      <form class="form-row" method="get" action="">
        <label for="tanggal">Tanggal</label>
        <input class="input" type="date" id="tanggal" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>" required>
        <button class="btn" type="submit">Cek Ketersediaan</button>
      </form>
    </div>
  </section>

  <section class="container">
    <?php if (!empty($_GET['tanggal'])): ?>
      <h2 class="mt-4">Hasil untuk: <?= htmlspecialchars(format_tanggal_indonesia($tanggal)) ?></h2>

      <div class="grid-lapangan">
        <?php foreach ($lapangan as $l): ?>
          <?php
            $stmtHitung->execute([$l['id'], $tanggal]);
            $terpakai = (int)$stmtHitung->fetchColumn();

            // Sederhana: jika ada pesanan di tanggal tsb, tandai "Ada Pesanan"
            // (Nanti bisa diupgrade ke grid jam-detail)
            $statusText  = $terpakai > 0 ? 'Ada Pesanan' : 'Banyak Slot Tersedia';
            $statusClass = $terpakai > 0 ? 'badge-warning' : 'badge-success';

            $foto = !empty($l['foto']) ? $BASE.'/../'.ltrim($l['foto'],'/') : $placeholder;
          ?>
          <article class="lapangan-card">
            <img class="lapangan-thumb" src="<?= htmlspecialchars($foto) ?>" alt="Foto <?= htmlspecialchars($l['nama']) ?>">
            <div class="lapangan-body">
              <h3><?= htmlspecialchars($l['nama']) ?></h3>
              <div class="meta">Jenis: <?= htmlspecialchars(ucfirst($l['jenis'])) ?></div>
              <div class="price"><?= format_rupiah($l['harga_per_jam']) ?>/jam</div>

              <div class="mt-2">
                <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
              </div>

              <div class="mt-3">
                <a class="outline" href="<?= $BASE ?>/pesan_lapangan.php?id=<?= (int)$l['id'] ?>&tanggal=<?= htmlspecialchars($tanggal) ?>">
                  Pesan Tanggal Ini
                </a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>

        <?php if (empty($lapangan)): ?>
          <article class="lapangan-card">
            <div class="lapangan-body">
              <h3 class="text-center">Belum ada lapangan aktif</h3>
              <p class="meta text-center">Silakan hubungi admin untuk menambahkan lapangan.</p>
            </div>
          </article>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <p class="meta mt-4">Pilih tanggal terlebih dahulu untuk melihat ketersediaan.</p>
    <?php endif; ?>
  </section>
</main>

<?php require_once '../tampilan/footer.php'; ?>
