<?php
require_once '../konfigurasi/koneksi.php';
require_once '../konfigurasi/fungsi.php';
require_once '../konfigurasi/otentikasi.php';
require_once '../konfigurasi/csrf.php';

wajib_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$prefillTanggal = $_GET['tanggal'] ?? date('Y-m-d');

// Ambil data lapangan + foto pertama (jika ada)
$sql = "
SELECT l.*,
  (SELECT f.path_file FROM foto_lapangan f
   WHERE f.lapangan_id = l.id
   ORDER BY f.urutan ASC, f.id ASC
   LIMIT 1) AS foto
FROM lapangan l
WHERE l.id = ?
LIMIT 1";
$stmt = $koneksi->prepare($sql);
$stmt->execute([$id]);
$lap = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$lap) {
  set_flash('error', 'Lapangan tidak ditemukan.');
  redirect('beranda.php');
}

$judul = "Pesan Lapangan";
require_once '../tampilan/header.php';
require_once '../tampilan/navbar.php';

$BASE = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$foto  = !empty($lap['foto']) ? $BASE.'/../'.ltrim($lap['foto'],'/') : ($BASE . '/../aset/gambar/placeholder.jpg');
$harga = (int)$lap['harga_per_jam'];
?>

<main class="konten">
  <section class="hero">
    <div class="container">
      <h1>Pesan: <?= htmlspecialchars($lap['nama']) ?></h1>
      <p class="meta">
        Jenis: <?= htmlspecialchars(ucfirst($lap['jenis'])) ?> ·
        Harga: <strong><?= format_rupiah($harga) ?>/jam</strong>
        <?= $lap['lokasi'] ? ' · Lokasi: '.htmlspecialchars($lap['lokasi']) : '' ?>
      </p>
    </div>
  </section>

  <section class="container" style="max-width:900px; margin-top:22px;">
    <div style="display:grid; grid-template-columns: 360px 1fr; gap:18px;">
      <!-- Preview foto -->
      <article class="lapangan-card" style="overflow:hidden;">
        <img class="lapangan-thumb" src="<?= htmlspecialchars($foto) ?>" alt="Foto <?= htmlspecialchars($lap['nama']) ?>">
        <div class="lapangan-body">
          <div class="meta">Pastikan tanggal & jam sesuai. Estimasi biaya dihitung otomatis.</div>
        </div>
      </article>

      <!-- Form pemesanan -->
      <form method="post"
            action="simpan_pesanan.php"
            class="card-form"
            id="formPesan"
            enctype="multipart/form-data">
        <?php input_csrf(); ?>
        <input type="hidden" name="lapangan_id" value="<?= (int)$lap['id'] ?>">
        <input type="hidden" id="harga_per_jam" value="<?= $harga ?>">

        <label>Tanggal</label>
        <input class="input" type="date" name="tanggal" value="<?= htmlspecialchars($prefillTanggal) ?>" required min="<?= date('Y-m-d') ?>">

        <label>Jam Mulai</label>
        <input class="input" type="time" name="jam_mulai" required>

        <label>Durasi (jam)</label>
        <select class="input" name="durasi_jam" id="durasi" required>
          <?php for ($i=1; $i<=5; $i++): ?>
            <option value="<?= $i ?>"><?= $i ?> jam</option>
          <?php endfor; ?>
        </select>

        <label>Catatan <span class="meta">(opsional)</span></label>
        <textarea class="input" name="catatan" rows="3" placeholder="Contoh: bawa bola sendiri, latihan tim, dll."></textarea>

        <!-- Tambahan: Upload Bukti Pembayaran -->
        <label>Upload Bukti Pembayaran <span class="meta"></span></label>
        <input class="input" type="file" name="bukti" accept="image/*">

        <div class="info-estimasi">
          <div class="meta">Estimasi Total</div>
          <div id="estimasi_total" style="font-weight:700; font-size:20px;"><?= format_rupiah($harga) ?></div>
        </div>

        <div class="hero-actions" style="margin-top:6px;">
          <button class="btn" type="submit">Kirim Pesanan</button>
          <a class="outline" href="beranda.php">Batal</a>
        </div>
      </form>
    </div>

    <p class="meta mt-3">* Estimasi belum termasuk biaya tambahan (jika ada). Slot akan dicek bentrok saat disimpan.</p>
  </section>
</main>

<script>
// Hitung estimasi total sederhana = durasi * harga_per_jam
document.addEventListener('DOMContentLoaded', () => {
  const harga = parseInt(document.getElementById('harga_per_jam').value || '0', 10);
  const durasiEl = document.getElementById('durasi');
  const out = document.getElementById('estimasi_total');

  const formatRupiah = (n) => {
    try {
      return new Intl.NumberFormat('id-ID', { style:'currency', currency:'IDR', maximumFractionDigits:0 }).format(n);
    } catch(e) { // fallback
      return 'Rp ' + (n||0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
  };

  const update = () => {
    const dur = parseInt(durasiEl.value || '1', 10);
    out.textContent = formatRupiah(dur * harga);
  };

  durasiEl.addEventListener('change', update);
  update();
});
</script>

<?php require_once '../tampilan/footer.php'; ?>
