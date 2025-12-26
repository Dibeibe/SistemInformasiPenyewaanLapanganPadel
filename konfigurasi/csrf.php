<?php
// konfigurasi/csrf.php
// Proteksi form menggunakan token CSRF sederhana

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Buat token CSRF baru
function buat_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verifikasi token CSRF
function verifikasi_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Tambahkan input hidden di form
function input_csrf() {
    $token = buat_csrf_token();
    echo '<input type="hidden" name="csrf_token" value="' . $token . '">';
}
