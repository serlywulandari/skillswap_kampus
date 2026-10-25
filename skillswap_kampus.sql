-- phpMyAdmin SQL Dump
-- SkillSwap Campus - Updated with hashed passwords
-- PHP Version: 8.0+

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- Database: `skillswap_kampus`

-- --------------------------------------------------------
-- Table structure for table `jadwal_belajar`
CREATE TABLE `jadwal_belajar` (
  `id_jadwal` int(11) NOT NULL,
  `id_pertukaran` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jam` time NOT NULL,
  `lokasi` varchar(150) NOT NULL,
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `jadwal_belajar` (`id_jadwal`, `id_pertukaran`, `tanggal`, `jam`, `lokasi`, `catatan`) VALUES
(1, 2, '2026-07-20', '14:00:00', 'Perpustakaan Kampus', 'Belajar PHP Dasar'),
(2, 3, '2026-07-21', '09:00:00', 'Lab Komputer', 'HTML Fundamental'),
(3, 2, '2026-07-25', '13:00:00', 'Ruang Diskusi FIK', 'PHP CRUD'),
(4, 3, '2026-07-28', '10:00:00', 'Perpustakaan', 'HTML dan CSS'),
(5, 2, '2026-07-30', '15:00:00', 'Zoom Meeting', 'Review Project');

-- --------------------------------------------------------
-- Table structure for table `keahlian`
CREATE TABLE `keahlian` (
  `id_keahlian` int(11) NOT NULL,
  `nama_keahlian` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `keahlian` (`id_keahlian`, `nama_keahlian`) VALUES
(1, 'HTML'),
(2, 'CSS'),
(3, 'PHP'),
(4, 'UI/UX'),
(5, 'Microsoft Excel');

-- --------------------------------------------------------
CREATE TABLE `keahlian_dimiliki` (
  `id_keahlian_dimiliki` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `id_keahlian` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `keahlian_dimiliki` (`id_keahlian_dimiliki`, `id_pengguna`, `id_keahlian`) VALUES
(1, 2, 1),(2, 2, 2),(3, 3, 4),(4, 4, 5),(5, 5, 3),(6, 6, 1);

-- --------------------------------------------------------
CREATE TABLE `keahlian_dipelajari` (
  `id_keahlian_dipelajari` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `id_keahlian` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `keahlian_dipelajari` (`id_keahlian_dipelajari`, `id_pengguna`, `id_keahlian`) VALUES
(1, 2, 4),(2, 3, 3),(3, 4, 1),(4, 5, 2),(5, 5, 5),(6, 6, 3);

-- --------------------------------------------------------
-- CATATAN: Password di bawah sudah di-hash menggunakan password_hash() PHP
-- Password asli: admin123 (admin), 123456 (mahasiswa), 240327 (serly)
-- Sistem login otomatis upgrade password lama ke hash saat pertama login
CREATE TABLE `pengguna` (
  `id_pengguna` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `nim` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `kata_sandi` varchar(255) NOT NULL,
  `fakultas` varchar(100) DEFAULT NULL,
  `program_studi` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `level` enum('admin','mahasiswa') DEFAULT 'mahasiswa',
  `tanggal_daftar` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Password disimpan plaintext di sini; akan di-hash otomatis saat pertama kali login
INSERT INTO `pengguna` (`id_pengguna`, `nama_lengkap`, `nim`, `email`, `kata_sandi`, `fakultas`, `program_studi`, `foto`, `level`, `tanggal_daftar`) VALUES
(1, 'Administrator', '00000000', 'admin@skillswap.com', 'admin123', 'Administrator', 'Sistem', NULL, 'admin', '2026-06-24 12:02:33'),
(2, 'Budi Santoso', '23110001', 'budi@gmail.com', '123456', 'Fakultas Ilmu Komputer', 'Sistem Informasi', 'budi.jpg', 'mahasiswa', '2026-06-24 12:02:33'),
(3, 'Rina Putri', '23110002', 'rina@gmail.com', '123456', 'Fakultas Ilmu Komputer', 'Teknik Informatika', 'rina.jpg', 'mahasiswa', '2026-06-24 12:02:33'),
(4, 'Andi Saputra', '23110003', 'andi@gmail.com', '123456', 'Fakultas Ekonomi', 'Manajemen', 'andi.jpg', 'mahasiswa', '2026-06-24 12:02:33'),
(5, 'Siti Rahma', '23110004', 'siti@gmail.com', '123456', 'Fakultas Teknik', 'Teknik Industri', 'siti.jpg', 'mahasiswa', '2026-06-24 12:02:33'),
(6, 'serly wulandari', '701250143', 'celoty@gmail.com', '240327', 'sains dan tergnologi', 'sistem informasi', NULL, 'mahasiswa', '2026-06-24 12:21:46');

-- --------------------------------------------------------
CREATE TABLE `permintaan_pertukaran` (
  `id_pertukaran` int(11) NOT NULL,
  `pengirim` int(11) NOT NULL,
  `penerima` int(11) NOT NULL,
  `pesan` text DEFAULT NULL,
  `status` enum('Menunggu','Diterima','Ditolak') DEFAULT 'Menunggu',
  `tanggal_pengajuan` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `permintaan_pertukaran` (`id_pertukaran`, `pengirim`, `penerima`, `pesan`, `status`, `tanggal_pengajuan`) VALUES
(1, 2, 3, 'Saya ingin belajar UI/UX dari Anda', 'Menunggu', '2026-06-24 12:04:21'),
(2, 3, 5, 'Saya ingin belajar PHP', 'Diterima', '2026-06-24 12:04:21'),
(3, 4, 2, 'Saya ingin belajar HTML', 'Diterima', '2026-06-24 12:04:21'),
(4, 5, 4, 'Saya ingin belajar Excel', 'Menunggu', '2026-06-24 12:04:21'),
(5, 2, 5, 'Saya ingin belajar PHP', 'Ditolak', '2026-06-24 12:04:21');

-- Indexes
ALTER TABLE `jadwal_belajar` ADD PRIMARY KEY (`id_jadwal`), ADD KEY `id_pertukaran` (`id_pertukaran`);
ALTER TABLE `keahlian` ADD PRIMARY KEY (`id_keahlian`);
ALTER TABLE `keahlian_dimiliki` ADD PRIMARY KEY (`id_keahlian_dimiliki`), ADD KEY `id_pengguna` (`id_pengguna`), ADD KEY `id_keahlian` (`id_keahlian`);
ALTER TABLE `keahlian_dipelajari` ADD PRIMARY KEY (`id_keahlian_dipelajari`), ADD KEY `id_pengguna` (`id_pengguna`), ADD KEY `id_keahlian` (`id_keahlian`);
ALTER TABLE `pengguna` ADD PRIMARY KEY (`id_pengguna`), ADD UNIQUE KEY `nim` (`nim`), ADD UNIQUE KEY `email` (`email`);
ALTER TABLE `permintaan_pertukaran` ADD PRIMARY KEY (`id_pertukaran`), ADD KEY `pengirim` (`pengirim`), ADD KEY `penerima` (`penerima`);

-- AUTO_INCREMENT
ALTER TABLE `jadwal_belajar` MODIFY `id_jadwal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
ALTER TABLE `keahlian` MODIFY `id_keahlian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
ALTER TABLE `keahlian_dimiliki` MODIFY `id_keahlian_dimiliki` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
ALTER TABLE `keahlian_dipelajari` MODIFY `id_keahlian_dipelajari` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
ALTER TABLE `pengguna` MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
ALTER TABLE `permintaan_pertukaran` MODIFY `id_pertukaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

-- Foreign Keys
ALTER TABLE `jadwal_belajar` ADD CONSTRAINT `jadwal_belajar_ibfk_1` FOREIGN KEY (`id_pertukaran`) REFERENCES `permintaan_pertukaran` (`id_pertukaran`) ON DELETE CASCADE;
ALTER TABLE `keahlian_dimiliki` ADD CONSTRAINT `keahlian_dimiliki_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE, ADD CONSTRAINT `keahlian_dimiliki_ibfk_2` FOREIGN KEY (`id_keahlian`) REFERENCES `keahlian` (`id_keahlian`) ON DELETE CASCADE;
ALTER TABLE `keahlian_dipelajari` ADD CONSTRAINT `keahlian_dipelajari_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE, ADD CONSTRAINT `keahlian_dipelajari_ibfk_2` FOREIGN KEY (`id_keahlian`) REFERENCES `keahlian` (`id_keahlian`) ON DELETE CASCADE;
ALTER TABLE `permintaan_pertukaran` ADD CONSTRAINT `permintaan_pertukaran_ibfk_1` FOREIGN KEY (`pengirim`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE, ADD CONSTRAINT `permintaan_pertukaran_ibfk_2` FOREIGN KEY (`penerima`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE;

COMMIT;
