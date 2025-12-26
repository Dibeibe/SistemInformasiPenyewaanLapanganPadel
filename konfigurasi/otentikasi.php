<?php
/**
 * Otentikasi & Otorisasi
 * - Gunakan bersama koneksi PDO ($koneksi) dari konfigurasi/koneksi.php
 * - Kompatibel dengan style project (tanpa framework)
 */

/* ==== START SESSION (aman, tanpa double-start) ==== */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Ambil data pengguna yang login dari session
 * @return array|null {id, nama, email, peran} atau null jika belum login
 */
function pengguna() {
    return $_SESSION['pengguna'] ?? null;
}

/* ==== HELPER ROLE SEDERHANA ==== */
function is_admin()   { $u = pengguna(); return $u && $u['peran'] === 'admin'; }
function is_petugas() { $u = pengguna(); return $u && $u['peran'] === 'petugas'; }
function is_pengguna(){ $u = pengguna(); return $u && $u['peran'] === 'pengguna'; }

/**
 * Login: verifikasi email & password (PDO)
 * @param string $email
 * @param string $password
 * @return bool true jika sukses
 */
function login($email, $password) {
    global $koneksi; // PDO dari koneksi.php

    $email = trim((string)$email);
    $password = (string)$password;

    if ($email === '' || $password === '') {
        return false;
    }

    $stmt = $koneksi->prepare("
        SELECT id, nama, email, kata_sandi_hash, peran
        FROM pengguna
        WHERE email = ?
        LIMIT 1
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) return false;
    if (!password_verify($password, $user['kata_sandi_hash'])) return false;

    // Set session minimal
    $_SESSION['pengguna'] = [
        'id'    => (int)$user['id'],
        'nama'  => $user['nama'],
        'email' => $user['email'],
        'peran' => $user['peran'],
    ];

    // Regenerasi ID session untuk keamanan
    session_regenerate_id(true);
    return true;
}

/**
 * Logout: hapus data session dan cookie sesi
 */
function logout() {
    // Hapus data user
    unset($_SESSION['pengguna']);

    // Optional: hapus seluruh session
    // $_SESSION = [];

    // Hapus cookie sesi (jika ada)
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Regenerate id agar tidak bisa direuse
    session_regenerate_id(true);
}

/**
 * Wajib login sebelum akses halaman
 * @param string $redirect path tujuan jika belum login (default: ../halaman/masuk.php)
 */
function wajib_login($redirect = '../halaman/masuk.php') {
    if (empty($_SESSION['pengguna'])) {
        header("Location: $redirect");
        exit;
    }
}

/**
 * Wajib punya salah satu peran tertentu
 * @param array|string $roles  contoh: ['admin','petugas'] atau 'admin'
 * @param string $onFail       redirect jika tidak berhak (default: ../halaman/403.php)
 */
function wajib_peran($roles, $onFail = '../halaman/403.php') {
    // Pastikan sudah login
    if (empty($_SESSION['pengguna'])) {
        wajib_login(); // akan redirect ke halaman login default
        return;
    }

    $roles = is_array($roles) ? $roles : [$roles];
    $u = $_SESSION['pengguna'];

    if (!in_array($u['peran'], $roles, true)) {
        header("Location: $onFail");
        exit;
    }
}

/* ===== Alias kompatibilitas lama =====
 * Beberapa file lama mungkin memanggil cekRole([...])
 * Kita mapping ke wajib_peran agar tidak error undefined function.
 */
function cekRole($roles, $onFail = '../halaman/403.php') {
    wajib_peran($roles, $onFail);
}
