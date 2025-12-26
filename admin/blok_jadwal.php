<?php
include '../konfigurasi/koneksi.php';
session_start();

// Tambah blok jadwal
if (isset($_POST['tambah'])) {
    $id_lapangan = $_POST['id_lapangan'];
    $tanggal = $_POST['tanggal'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    $keterangan = $_POST['keterangan'];

    $query = "INSERT INTO blok_jadwal (id_lapangan, tanggal, jam_mulai, jam_selesai, keterangan)
              VALUES ('$id_lapangan', '$tanggal', '$jam_mulai', '$jam_selesai', '$keterangan')";
    mysqli_query($koneksi, $query);
    header('Location: blok_jadwal.php');
}

// Hapus blok jadwal
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM blok_jadwal WHERE id_blok = '$id'");
    header('Location: blok_jadwal.php');
}

// Ambil data blok jadwal
$blok = mysqli_query($koneksi, "SELECT b.*, l.nama_lapangan 
                                FROM blok_jadwal b
                                JOIN lapangan l ON b.id_lapangan = l.id_lapangan");
?>

<h2>Blok Jadwal Lapangan</h2>
<form method="POST">
    <select name="id_lapangan" required>
        <option value="">--Pilih Lapangan--</option>
        <?php
        $lapangan = mysqli_query($koneksi, "SELECT * FROM lapangan");
        while ($row = mysqli_fetch_assoc($lapangan)) {
            echo "<option value='{$row['id_lapangan']}'>{$row['nama_lapangan']}</option>";
        }
        ?>
    </select>
    <input type="date" name="tanggal" required>
    <input type="time" name="jam_mulai" required>
    <input type="time" name="jam_selesai" required>
    <input type="text" name="keterangan" placeholder="Keterangan">
    <button type="submit" name="tambah">Tambah</button>
</form>

<table border="1" cellpadding="5">
<tr>
    <th>Lapangan</th><th>Tanggal</th><th>Jam</th><th>Keterangan</th><th>Aksi</th>
</tr>
<?php while ($row = mysqli_fetch_assoc($blok)) { ?>
<tr>
    <td><?= $row['nama_lapangan']; ?></td>
    <td><?= $row['tanggal']; ?></td>
    <td><?= $row['jam_mulai']; ?> - <?= $row['jam_selesai']; ?></td>
    <td><?= $row['keterangan']; ?></td>
    <td><a href="?hapus=<?= $row['id_blok']; ?>" onclick="return confirm('Hapus?')">Hapus</a></td>
</tr>
<?php } ?>
</table>
