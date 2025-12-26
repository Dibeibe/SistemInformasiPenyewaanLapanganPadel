<?php
require_once "../konfigurasi/koneksi.php";
require_once "../konfigurasi/otentikasi.php";
cekRole(['admin', 'petugas']);

if (!isset($_GET['lapangan_id'])) {
    header("Location: data_lapangan.php");
    exit;
}

$lapangan_id = intval($_GET['lapangan_id']);

// Upload foto baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto'])) {
    $nama_file = time() . "_" . basename($_FILES['foto']['name']);
    $target_dir = "../aset/uploads/";
    $target_path = $target_dir . $nama_file;

    if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_path)) {
        $stmt = $koneksi->prepare("INSERT INTO foto_lapangan (lapangan_id, path_file) VALUES (?, ?)");
        $stmt->bind_param("is", $lapangan_id, $nama_file);
        $stmt->execute();
    }
}

if (isset($_GET['hapus'])) {
    $hapus = intval($_GET['hapus']);
    $stmt = $koneksi->prepare("DELETE FROM foto_lapangan WHERE id=? AND lapangan_id=?");
    $stmt->bind_param("ii", $hapus, $lapangan_id);
    $stmt->execute();
}

$result = $koneksi->query("SELECT * FROM foto_lapangan WHERE lapangan_id=$lapangan_id ORDER BY urutan ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Foto Lapangan</title>
    <link rel="stylesheet" href="../aset/style.css">
</head>
<body>
<h2>Foto Lapangan (ID: <?= $lapangan_id ?>)</h2>

<form method="POST" enctype="multipart/form-data">
    <label>Unggah Foto Baru:</label><br>
    <input type="file" name="foto" accept="image/*" required>
    <button type="submit">Upload</button>
</form>

<br>
<table border="1" cellpadding="8">
    <tr>
        <th>Foto</th>
        <th>Aksi</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><img src="../aset/uploads/<?= htmlspecialchars($row['path_file']) ?>" width="150"></td>
        <td><a href="?lapangan_id=<?= $lapangan_id ?>&hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus foto ini?')">Hapus</a></td>
    </tr>
    <?php endwhile; ?>
</table>

<br>
<a href="data_lapangan.php">Kembali</a>
</body>
</html>
