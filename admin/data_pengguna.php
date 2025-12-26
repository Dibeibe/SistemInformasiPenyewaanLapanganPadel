<?php
include '../konfigurasi/koneksi.php';
session_start();

// Tambah pengguna
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $peran = $_POST['peran'];

    mysqli_query($koneksi, "INSERT INTO pengguna (nama, email, password, peran) VALUES
                            ('$nama','$email','$password','$peran')");
    header('Location: data_pengguna.php');
}

// Hapus pengguna
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM pengguna WHERE id_pengguna='$id'");
    header('Location: data_pengguna.php');
}

$data = mysqli_query($koneksi, "SELECT * FROM pengguna");
?>

<h2>Data Pengguna</h2>
<form method="POST">
    <input type="text" name="nama" placeholder="Nama" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <select name="peran">
        <option value="user">User</option>
        <option value="admin">Admin</option>
        <option value="petugas">Petugas</option>
    </select>
    <button type="submit" name="tambah">Tambah</button>
</form>

<table border="1" cellpadding="5">
<tr><th>Nama</th><th>Email</th><th>Peran</th><th>Aksi</th></tr>
<?php while ($row = mysqli_fetch_assoc($data)) { ?>
<tr>
    <td><?= $row['nama']; ?></td>
    <td><?= $row['email']; ?></td>
    <td><?= ucfirst($row['peran']); ?></td>
    <td><a href="?hapus=<?= $row['id_pengguna']; ?>" onclick="return confirm('Hapus user?')">Hapus</a></td>
</tr>
<?php } ?>
</table>
