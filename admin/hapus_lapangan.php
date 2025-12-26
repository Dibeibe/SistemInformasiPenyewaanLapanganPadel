<?php
require_once "../konfigurasi/koneksi.php";
require_once "../konfigurasi/otentikasi.php";
cekRole(['admin']);

if (!isset($_GET['id'])) {
    header("Location: data_lapangan.php");
    exit;
}

$id = intval($_GET['id']);
$stmt = $koneksi->prepare("DELETE FROM lapangan WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: data_lapangan.php");
exit;
