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

// Pastikan pesanan milik user
$sql = "
SELECT p.id, p.total_bayar,
       pb.id     AS pembayaran_id,
       pb.bukti_path,
       pb.status_bayar
FROM pesanan p
LEFT JOIN pembayaran pb ON pb.pesanan_id = p.id
WHERE p.id = ? AND p.pengguna_id = ?
LIMIT 1
";
$stmt = $koneksi->prepare($sql);
$stmt->execute([$idPesanan, $idPengguna]);
$pesanan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pesanan) {
    set_flash('error', 'Pesanan tidak ditemukan atau bukan milik Anda.');
    redirect('riwayat_pesanan.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['bukti'])) {
    if ($_FILES['bukti']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Gagal mengunggah file.';
    } else {
        $allowedTypes = ['image/jpeg','image/png','image/jpg','image/webp'];
        $mime = mime_content_type($_FILES['bukti']['tmp_name']) ?: '';

        if (!in_array($mime, $allowedTypes, true)) {
            $error = 'Format file harus JPG/PNG/WEBP.';
        } else {
            $ext = strtolower(pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION));
            $namaFile = 'bukti_' . $idPesanan . '_' . time() . '.' . $ext;
            $targetDir = "../aset/uploads/";

            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $targetPath = $targetDir . $namaFile;

            if (!move_uploaded_file($_FILES['bukti']['tmp_name'], $targetPath)) {
                $error = 'Tidak bisa menyimpan file di server.';
            } else {
                // Simpan ke tabel pembayaran
                if (!empty($pesanan['pembayaran_id'])) {
                    // update
                    $stmt2 = $koneksi->prepare("
                      UPDATE pembayaran
                      SET bukti_path = ?, status_bayar = 'sudah_bayar', dibayar_pada = NOW()
                      WHERE id = ?
                    ");
                    $stmt2->execute([$namaFile, $pesanan['pembayaran_id']]);
                } else {
                    // insert baru (fallback, harusnya sudah dibuat di simpan_pesanan)
                    $stmt2 = $koneksi->prepare("
                      INSERT INTO pembayaran (pesanan_id, metode, jumlah, bukti_path, status_bayar, dibayar_pada)
                      VALUES (?,?,?,?, 'sudah_bayar', NOW())
                    ");
                    $stmt2->execute([
                        $idPesanan, 'transfer', (int)$pesanan['total_bayar'], $namaFile
                    ]);
                }

                set_flash('sukses', 'Bukti pembayaran berhasil diunggah.');
                redirect("detail_pesanan.php?id=" . $idPesanan);
            }
        }
    }
}

$judul = "Unggah Bukti Pembayaran";
require_once "../tampilan/header.php";
require_once "../tampilan/navbar.php";
?>

<main class="konten">
  <section class="container" style="max-width:600px;">
    <h1>Unggah Bukti Pembayaran</h1>
    <p class="meta">Pesanan #<?= (int)$pesanan['id'] ?> · Total: <?= format_rupiah($pesanan['total_bayar']) ?></p>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="card" style="margin-top:16px;padding:16px;">
      <label for="bukti">Pilih File Bukti (gambar):</label>
      <input type="file" name="bukti" id="bukti" accept="image/*" required style="margin:8px 0;">
      <p class="meta">Format yang diizinkan: JPG, PNG, WEBP. Maksimal beberapa MB (sesuaikan di PHP.ini jika perlu).</p>
      <button class="btn" type="submit">Upload</button>
      <a class="outline" href="detail_pesanan.php?id=<?= (int)$pesanan['id'] ?>" style="margin-left:8px;">Batal</a>
    </form>
  </section>
</main>

<?php require_once "../tampilan/footer.php"; ?>
