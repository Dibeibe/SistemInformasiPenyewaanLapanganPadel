<?php
// konfigurasi/koneksi.php
// File ini untuk koneksi ke database MySQL

$host = 'localhost';
$port = '3307';
$user = 'root';
$pass = ''; // sesuaikan dengan password MySQL kamu
$dbname = 'sewa_padel';

try {
    $koneksi = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass
    );
    $koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
