<?php
// Pastikan TIDAK double session_start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ===========================
   Helper umum
   =========================== */

// Redirect sederhana
function redirect($url) {
    header("Location: $url");
    exit;
}

// Flash message (satu kali tampil)
function set_flash($key, $msg) {
    $_SESSION['flash'][$key] = $msg;
}
function get_flash($key) {
    if (!empty($_SESSION['flash'][$key])) {
        $m = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $m;
    }
    return null;
}

// Sanitasi string ringan
function str_clean($s) {
    return trim(filter_var($s, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));
}

// Format angka rupiah
function format_rupiah($angka) {
    return 'Rp ' . number_format((float)$angka, 0, ',', '.');
}

// Format tanggal ke Indonesia (YYYY-mm-dd -> 26 Oktober 2025)
function format_tanggal_indonesia($date) {
    if (!$date) return '';
    $bulan = [
        1=>'Januari','Februari','Maret','April','Mei','Juni',
        'Juli','Agustus','September','Oktober','November','Desember'
    ];
    [$y,$m,$d] = explode('-', $date);
    return ltrim($d,'0') . ' ' . $bulan[(int)$m] . ' ' . $y;
}
