<?php
require_once '../konfigurasi/koneksi.php';
require_once '../konfigurasi/fungsi.php';
require_once '../konfigurasi/otentikasi.php';
$judul = 'Beranda';
require_once '../tampilan/header.php';
require_once '../tampilan/navbar.php';

/* Ambil semua lapangan aktif + foto pertama (jika ada) */
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

$BASE = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$placeholder = $BASE . '/../aset/gambar/placeholder.jpg';
?>
<main class="konten">
  <section class="hero">
    <div class="container">
      <h1>Selamat Datang di Sistem Sewa Lapangan Padel</h1>
      <p>Pilih lapangan favoritmu, cek ketersediaan, dan pesan dalam beberapa klik.</p>
      <div class="hero-actions">
        <a class="btn" href="<?= $BASE ?>/cari_jadwal.php">Cari Jadwal</a>
        <a class="outline" href="<?= $BASE ?>/pesan_lapangan.php">Pesan Cepat</a>
      </div>
    </div>
  </section>

  <section class="container">
    <h2 class="mt-4">Lapangan Tersedia</h2>
    <div class="grid-lapangan">
      <?php foreach ($lapangan as $l): ?>
        <?php
          $foto = !empty($l['foto']) ? $BASE.'/../'.ltrim($l['foto'],'/') : $placeholder;
        ?>
        <article class="lapangan-card">
          <img class="lapangan-thumb" src="<?= htmlspecialchars($foto) ?>" alt="Foto <?= htmlspecialchars($l['nama']) ?>">
          <div class="lapangan-body">
            <h3><?= htmlspecialchars($l['nama']) ?></h3>
            <div class="meta">Jenis: <?= htmlspecialchars(ucfirst($l['jenis'])) ?></div>
            <div class="price"><?= format_rupiah($l['harga_per_jam']) ?>/jam</div>
            <a class="btn" href="pesan_lapangan.php?id=<?= (int)$l['id'] ?>">Pesan Sekarang</a>
          </div>
        </article>
      <?php endforeach; ?>

      <?php if (empty($lapangan)): ?>
        <div class="lapangan-card">
          <div class="lapangan-body">
            <h3 class="text-center">Belum ada lapangan aktif</h3>
            <p class="meta text-center">Silakan hubungi admin untuk menambahkan lapangan.</p>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </section>
</main>

<?php require_once '../tampilan/footer.php'; ?>
