<?php
// admin/export_excel.php
require_once "../konfigurasi/koneksi.php";   // $koneksi = PDO
require_once "../konfigurasi/otentikasi.php";

wajib_peran(['admin','petugas']);

// ---- Ambil & validasi filter tanggal (opsional) ----
$mulai  = $_GET['mulai']  ?? null; // format: YYYY-MM-DD
$sampai = $_GET['sampai'] ?? null;

$validDate = function($d) {
  return is_string($d) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d);
};

$params = [];
$where  = "WHERE pb.status_bayar = 'sudah_bayar'";

if ($mulai && $validDate($mulai)) {
  $where .= " AND DATE(pb.dibayar_pada) >= ?";
  $params[] = $mulai;
}
if ($sampai && $validDate($sampai)) {
  $where .= " AND DATE(pb.dibayar_pada) <= ?";
  $params[] = $sampai;
}

// ---- Query rekap pendapatan per tanggal ----
$sql = "
SELECT DATE(pb.dibayar_pada) AS tanggal,
       SUM(pb.jumlah)        AS total
FROM pembayaran pb
{$where}
GROUP BY DATE(pb.dibayar_pada)
ORDER BY tanggal DESC
";
$stmt = $koneksi->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ---- Siapkan nama file ----
$rangePart = '';
if ($mulai && $validDate($mulai))  $rangePart .= "_dari_$mulai";
if ($sampai && $validDate($sampai)) $rangePart .= "_sampai_$sampai";
$filename = "laporan_pendapatan{$rangePart}_" . date('Ymd_His') . ".csv";

// ---- Header untuk unduhan CSV (Excel-friendly) ----
header('Content-Type: text/csv; charset=UTF-8');
header("Content-Disposition: attachment; filename=\"{$filename}\"");
header('Pragma: no-cache');
header('Expires: 0');

// Tulis BOM UTF-8 agar Excel Windows membaca dengan benar
echo "\xEF\xBB\xBF";

// ---- Tulis CSV ----
$out = fopen('php://output', 'w');

// Header kolom
fputcsv($out, ['Tanggal', 'Total Pendapatan (Rp)']);

// Baris data
foreach ($rows as $r) {
  // Format angka tetap sebagai angka (tanpa titik) agar mudah dijumlah di Excel
  // Jika ingin tampilan bertitik, ganti ke number_format
  fputcsv($out, [$r['tanggal'], $r['total']]);
}

fclose($out);
exit;
