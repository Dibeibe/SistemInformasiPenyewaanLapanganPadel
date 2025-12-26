<?php
require_once "../konfigurasi/koneksi.php";
require_once "../konfigurasi/fungsi.php";
require_once "../konfigurasi/otentikasi.php";

wajib_login(); 
// kalau kamu punya wajib_admin(), kamu bisa ganti wajib_login() → wajib_admin()

if (!isset($_GET['id'])) {
    redirect("pembayaran.php");
}

$idPesanan = (int)$_GET['id'];

// Ambil detail pesanan + pengguna + lapangan + pembayaran
$sql = "
SELECT 
    p.*,
    u.nama           AS nama_pengguna,
    u.email          AS email_pengguna,
    l.nama           AS nama_lapangan,
    l.jenis          AS jenis_lapangan,
    pb.status_bayar,
    pb.jumlah        AS jumlah_bayar,
    pb.bukti_path,
    pb.dibayar_pada
FROM pesanan p
JOIN pengguna u   ON u.id = p.pengguna_id
JOIN lapangan l   ON l.id = p.lapangan_id
LEFT JOIN pembayaran pb ON pb.pesanan_id = p.id
WHERE p.id = ?
LIMIT 1
";

$stmt = $koneksi->prepare($sql);
$stmt->execute([$idPesanan]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    set_flash("error", "Pesanan tidak ditemukan.");
    redirect("pembayaran.php");
}

$judul = "Detail Pesanan";
require_once "../tampilan/header.php";
require_once "../tampilan/navbar.php";

$UPLOAD_DIR = "../aset/uploads/";
?>

<main class="konten">
    <section class="container" style="max-width:900px;">

        <h1>Detail Pesanan #<?= htmlspecialchars($data['id']) ?></h1>
        <p class="meta">Informasi lengkap mengenai pesanan dan pembayaran.</p>

        <div class="table-card mt-3">
            <table class="table">
                <tbody>

                    <tr>
                        <th style="width:250px;">Nama Pemesan</th>
                        <td><?= htmlspecialchars($data['nama_pengguna']) ?></td>
                    </tr>

                    <tr>
                        <th>Lapangan</th>
                        <td>
                            <?= htmlspecialchars($data['nama_lapangan']) ?>
                            <?php if (!empty($data['jenis_lapangan'])): ?>
                            <span class="meta"> (<?= htmlspecialchars(ucfirst($data['jenis_lapangan'])) ?>)</span>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <tr>
                        <th>Tanggal</th>
                        <td><?= format_tanggal_indonesia($data['tanggal']) ?></td>
                    </tr>

                    <tr>
                        <th>Jam</th>
                        <td>
                            <?= htmlspecialchars(substr($data['jam_mulai'], 0, 5)) ?>
                             - 
                            <?= htmlspecialchars(substr($data['jam_selesai'], 0, 5)) ?>
                        </td>
                    </tr>

                    <tr>
                        <th>Total Bayar</th>
                        <td><?= format_rupiah($data['total_bayar']) ?></td>
                    </tr>

                    <tr>
                        <th>Status Pembayaran</th>
                        <td><?= htmlspecialchars(ucfirst($data['status_bayar'] ?? 'belum_bayar')) ?></td>
                    </tr>

                    <tr>
                        <th>Bukti Pembayaran</th>
                        <td>
                            <?php if (!empty($data['bukti_path'])): ?>
                                <img src="<?= $UPLOAD_DIR . htmlspecialchars($data['bukti_path']) ?>"
                                     alt="Bukti Pembayaran"
                                     style="max-width:350px;border-radius:10px;border:1px solid #e2e8f0;object-fit:contain;">
                                <br><br>
                                <a class="outline small" target="_blank"
                                   href="<?= $UPLOAD_DIR . htmlspecialchars($data['bukti_path']) ?>">
                                   Lihat ukuran penuh
                                </a>
                            <?php else: ?>
                                <span class="meta">Belum ada bukti pembayaran.</span>
                            <?php endif; ?>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

        <div class="hero-actions mt-4">
            <a class="outline" href="pembayaran.php">Kembali ke Data Pembayaran</a>
        </div>

    </section>
</main>

<?php require_once "../tampilan/footer.php"; ?>
