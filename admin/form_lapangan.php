<?php
require_once "../konfigurasi/koneksi.php";   // $koneksi = PDO
require_once "../konfigurasi/fungsi.php";
require_once "../konfigurasi/otentikasi.php";

wajib_peran(['admin','petugas']);

$id   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$edit = $id > 0;
$lap  = [
  'nama' => '',
  'jenis' => 'indoor',
  'lokasi' => '',
  'harga_per_jam' => '',
  'aktif' => 1,
  'foto_utama' => null
];

/* ===== Ambil data saat edit ===== */
if ($edit) {
  $st = $koneksi->prepare("
    SELECT l.id, l.nama, l.jenis, l.lokasi, l.harga_per_jam, l.aktif, f.path_file AS foto_utama
    FROM lapangan l
    LEFT JOIN foto_lapangan f ON f.lapangan_id = l.id AND f.urutan = 1
    WHERE l.id = ?
  ");
  $st->execute([$id]);
  $data = $st->fetch(PDO::FETCH_ASSOC);
  if ($data) { $lap = $data; } else { $edit = false; }
}

/* ===== Submit ===== */
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $lap['nama']          = trim($_POST['nama'] ?? '');
  $lap['jenis']         = $_POST['jenis'] ?? 'indoor';
  $lap['lokasi']        = trim($_POST['lokasi'] ?? '');
  $lap['harga_per_jam'] = (int)($_POST['harga_per_jam'] ?? 0);
  $lap['aktif']         = isset($_POST['aktif']) ? 1 : 0;

  // Validasi dasar
  if ($lap['nama'] === '' || $lap['harga_per_jam'] <= 0) {
    $error = "Nama dan harga per jam wajib diisi (harga > 0).";
  } else {
    // Simpan ke tabel lapangan
    if ($edit) {
      $st = $koneksi->prepare("
        UPDATE lapangan
           SET nama=?, jenis=?, lokasi=?, harga_per_jam=?, aktif=?
         WHERE id=?");
      $st->execute([$lap['nama'],$lap['jenis'],$lap['lokasi'],$lap['harga_per_jam'],$lap['aktif'],$id]);
    } else {
      $st = $koneksi->prepare("
        INSERT INTO lapangan (nama, jenis, lokasi, harga_per_jam, aktif)
        VALUES (?,?,?,?,?)");
      $st->execute([$lap['nama'],$lap['jenis'],$lap['lokasi'],$lap['harga_per_jam'],$lap['aktif']]);
      $id = $koneksi->lastInsertId();
    }

    /* ===== Upload Foto (jika ada) ===== */
    if (!empty($_FILES['foto']['name'])) {
      $folder = "../aset/foto_lapangan/";
      if (!is_dir($folder)) mkdir($folder, 0777, true);

      $namaFile = "lapangan_{$id}_" . time() . ".jpg";
      $path = $folder . $namaFile;

      // Validasi tipe file
      $tipe = mime_content_type($_FILES['foto']['tmp_name']);
      if (in_array($tipe, ['image/jpeg','image/png','image/webp'])) {
        move_uploaded_file($_FILES['foto']['tmp_name'], $path);

        // Simpan ke DB
        $cek = $koneksi->prepare("SELECT id FROM foto_lapangan WHERE lapangan_id=? AND urutan=1");
        $cek->execute([$id]);
        if ($cek->fetch()) {
          $up = $koneksi->prepare("UPDATE foto_lapangan SET path_file=?, urutan=1 WHERE lapangan_id=? AND urutan=1");
          $up->execute(["aset/foto_lapangan/".$namaFile, $id]);
        } else {
          $ins = $koneksi->prepare("INSERT INTO foto_lapangan (lapangan_id, path_file, urutan) VALUES (?,?,1)");
          $ins->execute([$id, "aset/foto_lapangan/".$namaFile]);
        }
      } else {
        $error = "Format foto tidak didukung. Gunakan JPG/PNG/WebP.";
      }
    }

    if (!$error) {
      set_flash('ok', $edit ? 'Lapangan berhasil diperbarui.' : 'Lapangan berhasil ditambahkan.');
      header("Location: data_lapangan.php");
      exit;
    }
  }
}

$judul = ($edit ? "Edit" : "Tambah") . " Lapangan";
require_once "../tampilan/header.php";
require_once "../tampilan/navbar.php";
?>

<main class="konten">
  <section class="container" style="max-width:720px;">
    <h1><?= $edit ? 'Edit' : 'Tambah' ?> Lapangan</h1>
    <p class="meta">Isi data lapangan padel. Pastikan harga per jam sesuai.</p>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="card-form" enctype="multipart/form-data" novalidate>
      <div style="display:grid; gap:12px;">
        <label>Nama</label>
        <input class="input" type="text" name="nama" value="<?= htmlspecialchars($lap['nama']) ?>" required>

        <label>Jenis</label>
        <select class="input" name="jenis">
          <option value="indoor"  <?= $lap['jenis']==='indoor'  ? 'selected':'' ?>>Indoor</option>
          <option value="outdoor" <?= $lap['jenis']==='outdoor' ? 'selected':'' ?>>Outdoor</option>
        </select>

        <label>Lokasi <span class="meta">(opsional)</span></label>
        <input class="input" type="text" name="lokasi" value="<?= htmlspecialchars($lap['lokasi']) ?>" placeholder="Nama GOR / alamat singkat">

        <label>Harga per Jam</label>
        <input class="input" type="number" min="0" step="1000" name="harga_per_jam" value="<?= htmlspecialchars($lap['harga_per_jam']) ?>" required>

        <label>Foto Utama <span class="meta">(opsional)</span></label>
        <?php if (!empty($lap['foto_utama'])): ?>
          <img src="../<?= htmlspecialchars($lap['foto_utama']) ?>" alt="Foto Lapangan" style="width:100%;max-width:300px;border-radius:6px;object-fit:cover;margin-bottom:8px;">
        <?php endif; ?>
        <input class="input" type="file" name="foto" accept="image/*">

        <label style="display:flex; gap:10px; align-items:center;">
          <input type="checkbox" name="aktif" <?= (int)$lap['aktif']===1 ? 'checked':'' ?>>
          <span>Aktif</span>
        </label>

        <div class="hero-actions" style="margin-top:6px;">
          <button class="btn" type="submit">Simpan</button>
          <a class="outline" href="data_lapangan.php">Batal</a>
        </div>
      </div>
    </form>
  </section>
</main>

<?php require_once "../tampilan/footer.php"; ?>
