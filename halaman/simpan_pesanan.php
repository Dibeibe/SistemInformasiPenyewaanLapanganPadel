<?php
require_once '../konfigurasi/koneksi.php';
require_once '../konfigurasi/fungsi.php';
require_once '../konfigurasi/otentikasi.php';
require_once '../konfigurasi/csrf.php';

wajib_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('beranda.php');
}

if (!verifikasi_csrf($_POST['csrf_token'] ?? '')) {
    die('Token CSRF tidak valid.');
}

$lapangan_id = $_POST['lapangan_id'] ?? 0;
$tanggal     = $_POST['tanggal'] ?? '';
$jam_mulai   = $_POST['jam_mulai'] ?? '';
$durasi_jam  = floatval($_POST['durasi_jam'] ?? 1);
$catatan     = $_POST['catatan'] ?? '';

$jam_selesai = date('H:i:s', strtotime("$jam_mulai +$durasi_jam hours"));

// Ambil data lapangan
$stmtLap = $koneksi->prepare("SELECT harga_per_jam FROM lapangan WHERE id = ?");
$stmtLap->execute([$lapangan_id]);
$lap = $stmtLap->fetch(PDO::FETCH_ASSOC);

if (!$lap) {
    set_flash('error', 'Lapangan tidak ditemukan.');
    redirect('beranda.php');
}

$total_bayar = $lap['harga_per_jam'] * $durasi_jam;

// ================== CEK BENTROK JADWAL ==================
$sqlCek = "SELECT COUNT(*) FROM pesanan 
  WHERE lapangan_id=? AND tanggal=? 
  AND status_pesanan IN ('menunggu','terkonfirmasi') 
  AND NOT (jam_selesai <= ? OR jam_mulai >= ?)";
$stmtCek = $koneksi->prepare($sqlCek);
$stmtCek->execute([$lapangan_id, $tanggal, $jam_mulai, $jam_selesai]);
$adaBentrok = $stmtCek->fetchColumn();

if ($adaBentrok) {
    set_flash('error', 'Waktu tersebut sudah dipesan. Silakan pilih jam lain.');
    redirect("pesan_lapangan.php?id=$lapangan_id");
}

// ================== PROSES UPLOAD BUKTI (JIKA ADA) ==================
$namaFileBukti = null;

if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['bukti']['error'] !== UPLOAD_ERR_OK) {
        set_flash('error', 'Gagal mengunggah bukti pembayaran.');
        redirect('pesan_lapangan.php?id=' . (int)$lapangan_id);
    }

    $allowedTypes = ['image/jpeg','image/png','image/jpg','image/webp'];
    $tmpFile      = $_FILES['bukti']['tmp_name'];

    $mime = function_exists('mime_content_type') ? mime_content_type($tmpFile) : null;
    if ($mime && !in_array($mime, $allowedTypes, true)) {
        set_flash('error', 'Format bukti harus gambar (JPG/PNG/WEBP).');
        redirect('pesan_lapangan.php?id=' . (int)$lapangan_id);
    }

    $ext = strtolower(pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION));
    $namaFileBukti = 'bukti_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;

    $uploadDir = __DIR__ . '/../aset/uploads';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $tujuan = $uploadDir . '/' . $namaFileBukti;
    if (!move_uploaded_file($tmpFile, $tujuan)) {
        set_flash('error', 'Tidak dapat menyimpan bukti di server.');
        redirect('pesan_lapangan.php?id=' . (int)$lapangan_id);
    }
}

// ================== SIMPAN PESANAN ==================
$stmt = $koneksi->prepare("INSERT INTO pesanan 
(pengguna_id, lapangan_id, tanggal, jam_mulai, jam_selesai, durasi_jam, total_bayar, catatan) 
VALUES (?,?,?,?,?,?,?,?)");

$stmt->execute([
    $_SESSION['pengguna']['id'],
    $lapangan_id,
    $tanggal,
    $jam_mulai,
    $jam_selesai,
    $durasi_jam,
    $total_bayar,
    $catatan
]);

// Ambil id pesanan yang baru dibuat
$id_pesanan = $koneksi->lastInsertId();

// ================== SIMPAN DATA PEMBAYARAN ==================
if ($namaFileBukti) {
    // User mengupload bukti → langsung diberi status sudah_bayar
    $stmtPay = $koneksi->prepare("
        INSERT INTO pembayaran (pesanan_id, metode, jumlah, bukti_path, status_bayar, dibayar_pada)
        VALUES (?,?,?,?, 'sudah_bayar', NOW())
    ");
    $stmtPay->execute([
        $id_pesanan,
        'transfer',        // default metode
        $total_bayar,
        $namaFileBukti
    ]);
} else {
    // Belum upload bukti → status masih default (belum_bayar)
    $stmtPay = $koneksi->prepare("
        INSERT INTO pembayaran (pesanan_id, metode, jumlah)
        VALUES (?,?,?)
    ");
    $stmtPay->execute([
        $id_pesanan,
        'transfer',
        $total_bayar
    ]);
}

set_flash('sukses', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran atau upload bukti.');
redirect('riwayat_pesanan.php');
