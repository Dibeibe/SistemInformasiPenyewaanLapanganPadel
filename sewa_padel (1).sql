-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 08 Des 2025 pada 03.35
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sewa_padel`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `blok_jadwal`
--

CREATE TABLE `blok_jadwal` (
  `id` int(11) NOT NULL,
  `lapangan_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `alasan` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `foto_lapangan`
--

CREATE TABLE `foto_lapangan` (
  `id` int(11) NOT NULL,
  `lapangan_id` int(11) NOT NULL,
  `path_file` varchar(255) NOT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `foto_lapangan`
--

INSERT INTO `foto_lapangan` (`id`, `lapangan_id`, `path_file`, `urutan`) VALUES
(1, 1, 'aset/foto_lapangan/lapangan_1_1761475110.jpg', 1),
(2, 2, 'aset/foto_lapangan/lapangan_2_1761475575.jpg', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `lapangan`
--

CREATE TABLE `lapangan` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jenis` enum('indoor','outdoor') NOT NULL DEFAULT 'indoor',
  `lokasi` varchar(200) DEFAULT NULL,
  `harga_per_jam` int(11) NOT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `lapangan`
--

INSERT INTO `lapangan` (`id`, `nama`, `jenis`, `lokasi`, `harga_per_jam`, `aktif`) VALUES
(1, 'Lapangan Padel A', 'indoor', 'GELORA BUNG ADIB', 75000, 1),
(2, 'Lapangan Padel B', 'outdoor', 'GELORA BUNG ADIB 2', 100000, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id` bigint(20) NOT NULL,
  `pesanan_id` bigint(20) NOT NULL,
  `metode` enum('tunai','transfer','gateway') NOT NULL DEFAULT 'transfer',
  `jumlah` int(11) NOT NULL,
  `dibayar_pada` datetime DEFAULT NULL,
  `bukti_path` varchar(255) DEFAULT NULL,
  `status_bayar` enum('belum_bayar','sudah_bayar','gagal','refund') NOT NULL DEFAULT 'belum_bayar'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pembayaran`
--

INSERT INTO `pembayaran` (`id`, `pesanan_id`, `metode`, `jumlah`, `dibayar_pada`, `bukti_path`, `status_bayar`) VALUES
(1, 3, 'transfer', 75000, NULL, NULL, 'gagal'),
(2, 4, 'transfer', 100000, NULL, NULL, 'gagal'),
(3, 5, 'transfer', 75000, '2025-12-07 00:46:18', 'bukti_1765043178_6139.jpg', 'sudah_bayar'),
(4, 6, 'transfer', 75000, NULL, NULL, 'gagal'),
(5, 7, 'transfer', 100000, NULL, NULL, 'gagal'),
(6, 8, 'transfer', 100000, '2025-12-07 18:07:44', 'bukti_1765105664_8036.jpg', 'sudah_bayar');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengguna`
--

CREATE TABLE `pengguna` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `kata_sandi_hash` varchar(255) NOT NULL,
  `peran` enum('admin','petugas','pengguna') NOT NULL DEFAULT 'pengguna',
  `telepon` varchar(30) DEFAULT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengguna`
--

INSERT INTO `pengguna` (`id`, `nama`, `email`, `kata_sandi_hash`, `peran`, `telepon`, `dibuat_pada`) VALUES
(1, 'Administrator', 'admin@mail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '081234567890', '2025-10-26 06:28:55'),
(2, 'cindy', 'cindy@gmail.com', '$2y$10$CCNCfidG/OTO/ZZFn9MuFuZJSQ6zbkOBeetafdvV4qeSpzkgb9IfO', 'pengguna', '08991104539', '2025-10-26 10:50:22'),
(3, 'ramlan', 'ramlan123@gmail.com', '$2y$10$lB7wQop3tcI0ebSPvJxrfuZm9NDs/28ETqECFLjkXw1Le5BNxIFCC', 'pengguna', '087967568765', '2025-11-10 02:35:56'),
(4, 'fatur', 'fatur@email.com', '$2y$10$ePn.B8JWmX8Klyt7GdToBuHUJXqdBMxxs7krEToE2ys7EXmFFrj9m', 'pengguna', '08218312123', '2025-12-06 16:27:03'),
(5, 'adib', 'adib@mail.com', '$2y$10$Nt6EdQ8lk.LH/kC2V9/5IO9bpcCsrO.RPsZ/g0VZa1lSZ4wvufeI6', 'pengguna', '08412411428', '2025-12-06 17:48:29'),
(6, 'adel', 'adel@mail.com', '$2y$10$bhXSeXlnjcg2EuKzyMytXexOY/wdll9QQlEtu0rabLxw.HZBHezvu', 'pengguna', '0864234567', '2025-12-07 11:04:27');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pesanan`
--

CREATE TABLE `pesanan` (
  `id` bigint(20) NOT NULL,
  `pengguna_id` int(11) NOT NULL,
  `lapangan_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `durasi_jam` decimal(4,2) NOT NULL,
  `total_bayar` int(11) NOT NULL,
  `status_pesanan` enum('menunggu','terkonfirmasi','dibatalkan','selesai','refund') NOT NULL DEFAULT 'menunggu',
  `catatan` varchar(255) DEFAULT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pesanan`
--

INSERT INTO `pesanan` (`id`, `pengguna_id`, `lapangan_id`, `tanggal`, `jam_mulai`, `jam_selesai`, `durasi_jam`, `total_bayar`, `status_pesanan`, `catatan`, `dibuat_pada`) VALUES
(1, 2, 1, '2025-10-26', '20:56:00', '21:56:00', 1.00, 75000, 'selesai', '+ abangnya', '2025-10-26 10:53:55'),
(2, 4, 1, '2025-12-06', '23:27:00', '00:27:00', 1.00, 75000, 'selesai', '', '2025-12-06 16:27:28'),
(3, 4, 1, '2025-12-06', '23:34:00', '00:34:00', 1.00, 75000, 'selesai', '', '2025-12-06 16:34:47'),
(4, 4, 2, '2025-12-06', '00:39:00', '01:39:00', 1.00, 100000, 'selesai', '', '2025-12-06 17:39:38'),
(5, 4, 1, '2025-12-06', '00:46:00', '01:46:00', 1.00, 75000, 'selesai', '', '2025-12-06 17:46:18'),
(6, 5, 1, '2025-12-07', '03:00:00', '04:00:00', 1.00, 75000, 'selesai', '', '2025-12-06 17:50:33'),
(7, 6, 2, '2025-12-25', '18:04:00', '19:04:00', 1.00, 100000, 'selesai', '', '2025-12-07 11:05:07'),
(8, 6, 2, '2025-12-31', '18:07:00', '19:07:00', 1.00, 100000, 'selesai', '', '2025-12-07 11:07:44');

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `vw_pendapatan_harian`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `vw_pendapatan_harian` (
`tanggal` date
,`total_masuk` decimal(32,0)
);

-- --------------------------------------------------------

--
-- Struktur untuk view `vw_pendapatan_harian`
--
DROP TABLE IF EXISTS `vw_pendapatan_harian`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_pendapatan_harian`  AS SELECT `p`.`tanggal` AS `tanggal`, sum(case when `pb`.`status_bayar` = 'sudah_bayar' then `pb`.`jumlah` else 0 end) AS `total_masuk` FROM (`pesanan` `p` join `pembayaran` `pb` on(`pb`.`pesanan_id` = `p`.`id`)) GROUP BY `p`.`tanggal` ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `blok_jadwal`
--
ALTER TABLE `blok_jadwal`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_blok` (`lapangan_id`,`tanggal`,`jam_mulai`,`jam_selesai`),
  ADD KEY `lapangan_id` (`lapangan_id`,`tanggal`);

--
-- Indeks untuk tabel `foto_lapangan`
--
ALTER TABLE `foto_lapangan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lapangan_id` (`lapangan_id`),
  ADD KEY `urutan` (`urutan`);

--
-- Indeks untuk tabel `lapangan`
--
ALTER TABLE `lapangan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pesanan_id` (`pesanan_id`),
  ADD KEY `status_bayar` (`status_bayar`),
  ADD KEY `dibayar_pada` (`dibayar_pada`);

--
-- Indeks untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_slot` (`lapangan_id`,`tanggal`,`jam_mulai`,`jam_selesai`),
  ADD KEY `lapangan_id` (`lapangan_id`,`tanggal`,`jam_mulai`),
  ADD KEY `pengguna_id` (`pengguna_id`,`tanggal`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `blok_jadwal`
--
ALTER TABLE `blok_jadwal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `foto_lapangan`
--
ALTER TABLE `foto_lapangan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `lapangan`
--
ALTER TABLE `lapangan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `blok_jadwal`
--
ALTER TABLE `blok_jadwal`
  ADD CONSTRAINT `blok_jadwal_ibfk_1` FOREIGN KEY (`lapangan_id`) REFERENCES `lapangan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `foto_lapangan`
--
ALTER TABLE `foto_lapangan`
  ADD CONSTRAINT `foto_lapangan_ibfk_1` FOREIGN KEY (`lapangan_id`) REFERENCES `lapangan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`),
  ADD CONSTRAINT `pesanan_ibfk_2` FOREIGN KEY (`lapangan_id`) REFERENCES `lapangan` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
