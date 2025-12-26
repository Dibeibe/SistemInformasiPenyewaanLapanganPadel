<?php
require_once "../konfigurasi/koneksi.php";
require_once "../konfigurasi/fungsi.php";
require_once "../konfigurasi/otentikasi.php";

$error = null;

/* ====== Proses LOGIN hanya jika POST ====== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email      = trim($_POST['email'] ?? '');
    $kata_sandi = $_POST['kata_sandi'] ?? '';

    if ($email === '' || $kata_sandi === '') {
        $error = "Email dan kata sandi wajib diisi.";
    } else {
        if (login($email, $kata_sandi)) {
            $user = pengguna();
            if ($user['peran'] === 'admin' || $user['peran'] === 'petugas') {
                header("Location: ../admin/beranda.php");
                exit;
            } else {
                header("Location: beranda.php");
                exit;
            }
        } else {
            $error = "Email atau kata sandi salah.";
        }
    }
}

/* ====== Tampilkan halaman ====== */
$judul = "Masuk";
require_once "../tampilan/header.php";
require_once "../tampilan/navbar.php";

/* Flash pesan setelah logout/daftar */
$flashOk = get_flash('ok');
$daftarBerhasil = !empty($_GET['daftar']) && $_GET['daftar'] === 'berhasil';
$baruLogout = !empty($_GET['logout']);
?>

<main class="konten">
  <section class="hero">
    <div class="container">
      <h1>Masuk ke Akunmu</h1>
      <p class="meta">Silakan login untuk melanjutkan pemesanan lapangan.</p>
    </div>
  </section>

  <section class="container" style="max-width:480px; margin-top:24px;">
    <?php if ($flashOk): ?>
      <div class="alert alert-success"><?= htmlspecialchars($flashOk) ?></div>
    <?php endif; ?>

    <?php if ($daftarBerhasil): ?>
      <div class="alert alert-success">Pendaftaran berhasil. Silakan masuk.</div>
    <?php endif; ?>

    <?php if ($baruLogout): ?>
      <div class="alert alert-success">Kamu sudah keluar.</div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="card-form">
      <label>Email</label>
      <input class="input" type="email" name="email" required>

      <label>Kata Sandi</label>
      <input class="input" type="password" name="kata_sandi" required>

      <button class="btn mt-3" type="submit">Masuk</button>
    </form>

    <p class="meta" style="text-align:center; margin-top:14px;">
      Belum punya akun?
      <a href="daftar.php" class="outline small">Daftar</a>
    </p>
  </section>
</main>

<?php require_once "../tampilan/footer.php"; ?>
