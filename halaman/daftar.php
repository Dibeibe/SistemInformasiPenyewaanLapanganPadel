<?php
require_once "../konfigurasi/koneksi.php";  // $koneksi = PDO
require_once "../konfigurasi/fungsi.php";

$judul = "Daftar";
require_once "../tampilan/header.php";
require_once "../tampilan/navbar.php";

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama      = trim($_POST['nama'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $telepon   = trim($_POST['telepon'] ?? '');
    $password  = $_POST['kata_sandi'] ?? '';

    if ($nama === '' || $email === '' || $password === '') {
        $error = "Nama, email, dan kata sandi wajib diisi.";
    } else {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO pengguna (nama, email, telepon, kata_sandi_hash, peran)
                    VALUES (?, ?, ?, ?, 'pengguna')";
            $stmt = $koneksi->prepare($sql);
            $stmt->execute([$nama, $email, $telepon, $hash]);

            header("Location: masuk.php?daftar=berhasil");
            exit;
        } catch (PDOException $e) {
            // 23000 = integrity constraint violation (mis. UNIQUE email)
            if ($e->getCode() === '23000') {
                $error = "Gagal mendaftar: email sudah digunakan.";
            } else {
                $error = "Terjadi kesalahan. Coba lagi.";
            }
        }
    }
}
?>

<main class="konten">
  <section class="hero">
    <div class="container">
      <h1>Buat Akun Baru</h1>
      <p class="meta">Daftar untuk mulai memesan lapangan padel favoritmu.</p>
    </div>
  </section>

  <section class="container" style="max-width:560px; margin-top:24px;">
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="card-form">
      <label>Nama</label>
      <input class="input" type="text" name="nama" required>

      <label>Email</label>
      <input class="input" type="email" name="email" required>

      <label>Telepon <span class="meta">(opsional)</span></label>
      <input class="input" type="text" name="telepon" placeholder="08xxxxxxxxxx">

      <label>Kata Sandi</label>
      <input class="input" type="password" name="kata_sandi" required>

      <button class="btn mt-3" type="submit">Daftar</button>
    </form>

    <p class="meta" style="text-align:center; margin-top:14px;">
      Sudah punya akun?
      <a href="masuk.php" class="outline small">Masuk</a>
    </p>
  </section>
</main>

<?php require_once "../tampilan/footer.php"; ?>
