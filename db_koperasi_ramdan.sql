-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 05, 2026 at 11:37 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_koperasi_ramdan`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_anggota_ramdan`
--

CREATE TABLE `tb_anggota_ramdan` (
  `id_anggota_ramdan` int(11) NOT NULL,
  `nama_ramdan` varchar(100) NOT NULL,
  `alamat_ramdan` text DEFAULT NULL,
  `no_hp_ramdan` varchar(20) DEFAULT NULL,
  `tanggal_daftar_ramdan` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_anggota_ramdan`
--

INSERT INTO `tb_anggota_ramdan` (`id_anggota_ramdan`, `nama_ramdan`, `alamat_ramdan`, `no_hp_ramdan`, `tanggal_daftar_ramdan`) VALUES
(1, 'batosano', 'sangkur', '0895321978200', '2026-04-03'),
(4, 'akbar', 'padasuka', '234234234234', '2026-05-04'),
(5, 'tipa', 'polsek', '6786545645', '2026-05-04'),
(6, 'rapasa', 'padalarang', '7890869678', '2026-05-04'),
(7, 'azka ', 'batujajar', '0235923578234', '2026-05-04');

-- --------------------------------------------------------

--
-- Table structure for table `tb_angsuran_ramdan`
--

CREATE TABLE `tb_angsuran_ramdan` (
  `id_angsuran_ramdan` int(11) NOT NULL,
  `id_pinjaman_ramdan` int(11) NOT NULL,
  `jumlah_bayar_ramdan` decimal(15,2) NOT NULL,
  `metode_pembayaran_ramdan` enum('Tunai','Transfer') DEFAULT 'Tunai',
  `sisa_pinjaman_ramdan` decimal(15,2) NOT NULL,
  `tanggal_bayar_ramdan` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_angsuran_ramdan`
--

INSERT INTO `tb_angsuran_ramdan` (`id_angsuran_ramdan`, `id_pinjaman_ramdan`, `jumlah_bayar_ramdan`, `metode_pembayaran_ramdan`, `sisa_pinjaman_ramdan`, `tanggal_bayar_ramdan`) VALUES
(8, 6, 250000.00, 'Transfer', 0.00, '2026-05-03'),
(9, 6, 850000.00, 'Tunai', 0.00, '2026-05-04'),
(10, 7, 250000.00, 'Tunai', 0.00, '2026-05-04'),
(11, 7, 200000.00, 'Tunai', 0.00, '2026-05-04');

-- --------------------------------------------------------

--
-- Table structure for table `tb_pinjaman_ramdan`
--

CREATE TABLE `tb_pinjaman_ramdan` (
  `id_pinjaman_ramdan` int(11) NOT NULL,
  `id_anggota_ramdan` int(11) NOT NULL,
  `jumlah_pinjaman_ramdan` decimal(15,2) NOT NULL,
  `bunga_ramdan` decimal(5,2) NOT NULL,
  `lama_pinjaman_ramdan` int(11) NOT NULL,
  `total_pinjaman_ramdan` decimal(15,2) NOT NULL,
  `sisa_pinjaman_ramdan` int(11) DEFAULT NULL,
  `status_pinjaman_ramdan` enum('Diajukan','Disetujui','Lunas') DEFAULT 'Diajukan',
  `tanggal_pinjaman_ramdan` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_pinjaman_ramdan`
--

INSERT INTO `tb_pinjaman_ramdan` (`id_pinjaman_ramdan`, `id_anggota_ramdan`, `jumlah_pinjaman_ramdan`, `bunga_ramdan`, `lama_pinjaman_ramdan`, `total_pinjaman_ramdan`, `sisa_pinjaman_ramdan`, `status_pinjaman_ramdan`, `tanggal_pinjaman_ramdan`) VALUES
(6, 1, 1000000.00, 10.00, 3, 1100000.00, 0, 'Lunas', '2026-05-03'),
(7, 1, 500000.00, 10.00, 3, 550000.00, 100000, 'Disetujui', '2026-05-04');

-- --------------------------------------------------------

--
-- Table structure for table `tb_role_ramdan`
--

CREATE TABLE `tb_role_ramdan` (
  `id_role_ramdan` int(11) NOT NULL,
  `nama_role_ramdan` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_role_ramdan`
--

INSERT INTO `tb_role_ramdan` (`id_role_ramdan`, `nama_role_ramdan`) VALUES
(1, 'Admin'),
(2, 'Petugas');

-- --------------------------------------------------------

--
-- Table structure for table `tb_simpanan_ramdan`
--

CREATE TABLE `tb_simpanan_ramdan` (
  `id_simpanan_ramdan` int(11) NOT NULL,
  `id_anggota_ramdan` int(11) NOT NULL,
  `jenis_simpanan_ramdan` enum('Pokok','Wajib','Sukarela') NOT NULL,
  `jumlah_ramdan` decimal(15,2) NOT NULL,
  `metode_pembayaran_ramdan` enum('Tunai','Transfer') DEFAULT 'Tunai',
  `tanggal_ramdan` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_simpanan_ramdan`
--

INSERT INTO `tb_simpanan_ramdan` (`id_simpanan_ramdan`, `id_anggota_ramdan`, `jenis_simpanan_ramdan`, `jumlah_ramdan`, `metode_pembayaran_ramdan`, `tanggal_ramdan`) VALUES
(1, 1, 'Pokok', 100000.00, 'Tunai', '2026-04-03'),
(2, 1, 'Sukarela', 5000000.00, 'Tunai', '2026-04-29'),
(3, 1, 'Sukarela', -100000.00, 'Tunai', '2026-04-29'),
(4, 1, 'Sukarela', 200000.00, 'Tunai', '2026-05-02'),
(5, 1, 'Sukarela', 200000.00, 'Tunai', '2026-05-02'),
(6, 1, 'Sukarela', 200000.00, 'Tunai', '2026-05-02'),
(7, 1, 'Sukarela', 200000.00, 'Tunai', '2026-05-02'),
(8, 1, 'Sukarela', 200000.00, 'Tunai', '2026-05-02'),
(9, 1, 'Sukarela', 250000.00, 'Transfer', '2026-05-03'),
(10, 1, 'Sukarela', -5250000.00, 'Tunai', '2026-05-04'),
(11, 1, 'Sukarela', 100000.00, 'Tunai', '2026-05-04');

-- --------------------------------------------------------

--
-- Table structure for table `tb_user_ramdan`
--

CREATE TABLE `tb_user_ramdan` (
  `id_user_ramdan` int(11) NOT NULL,
  `id_role_ramdan` int(11) NOT NULL,
  `username_ramdan` varchar(50) NOT NULL,
  `password_ramdan` varchar(255) NOT NULL,
  `nama_ramdan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_user_ramdan`
--

INSERT INTO `tb_user_ramdan` (`id_user_ramdan`, `id_role_ramdan`, `username_ramdan`, `password_ramdan`, `nama_ramdan`) VALUES
(1, 1, 'admin', '$2y$10$37ni.haMoCiI3CuckFM5D.MJ0qSRMQznUf.bTj08vnotBVPmQK/hO', 'Administrator');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_anggota_ramdan`
--
ALTER TABLE `tb_anggota_ramdan`
  ADD PRIMARY KEY (`id_anggota_ramdan`);

--
-- Indexes for table `tb_angsuran_ramdan`
--
ALTER TABLE `tb_angsuran_ramdan`
  ADD PRIMARY KEY (`id_angsuran_ramdan`),
  ADD KEY `id_pinjaman_ramdan` (`id_pinjaman_ramdan`);

--
-- Indexes for table `tb_pinjaman_ramdan`
--
ALTER TABLE `tb_pinjaman_ramdan`
  ADD PRIMARY KEY (`id_pinjaman_ramdan`),
  ADD KEY `id_anggota_ramdan` (`id_anggota_ramdan`);

--
-- Indexes for table `tb_role_ramdan`
--
ALTER TABLE `tb_role_ramdan`
  ADD PRIMARY KEY (`id_role_ramdan`);

--
-- Indexes for table `tb_simpanan_ramdan`
--
ALTER TABLE `tb_simpanan_ramdan`
  ADD PRIMARY KEY (`id_simpanan_ramdan`),
  ADD KEY `id_anggota_ramdan` (`id_anggota_ramdan`);

--
-- Indexes for table `tb_user_ramdan`
--
ALTER TABLE `tb_user_ramdan`
  ADD PRIMARY KEY (`id_user_ramdan`),
  ADD UNIQUE KEY `username_ramdan` (`username_ramdan`),
  ADD KEY `id_role_ramdan` (`id_role_ramdan`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_anggota_ramdan`
--
ALTER TABLE `tb_anggota_ramdan`
  MODIFY `id_anggota_ramdan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tb_angsuran_ramdan`
--
ALTER TABLE `tb_angsuran_ramdan`
  MODIFY `id_angsuran_ramdan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tb_pinjaman_ramdan`
--
ALTER TABLE `tb_pinjaman_ramdan`
  MODIFY `id_pinjaman_ramdan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tb_role_ramdan`
--
ALTER TABLE `tb_role_ramdan`
  MODIFY `id_role_ramdan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tb_simpanan_ramdan`
--
ALTER TABLE `tb_simpanan_ramdan`
  MODIFY `id_simpanan_ramdan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tb_user_ramdan`
--
ALTER TABLE `tb_user_ramdan`
  MODIFY `id_user_ramdan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_angsuran_ramdan`
--
ALTER TABLE `tb_angsuran_ramdan`
  ADD CONSTRAINT `tb_angsuran_ramdan_ibfk_1` FOREIGN KEY (`id_pinjaman_ramdan`) REFERENCES `tb_pinjaman_ramdan` (`id_pinjaman_ramdan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tb_pinjaman_ramdan`
--
ALTER TABLE `tb_pinjaman_ramdan`
  ADD CONSTRAINT `tb_pinjaman_ramdan_ibfk_1` FOREIGN KEY (`id_anggota_ramdan`) REFERENCES `tb_anggota_ramdan` (`id_anggota_ramdan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tb_simpanan_ramdan`
--
ALTER TABLE `tb_simpanan_ramdan`
  ADD CONSTRAINT `tb_simpanan_ramdan_ibfk_1` FOREIGN KEY (`id_anggota_ramdan`) REFERENCES `tb_anggota_ramdan` (`id_anggota_ramdan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tb_user_ramdan`
--
ALTER TABLE `tb_user_ramdan`
  ADD CONSTRAINT `tb_user_ramdan_ibfk_1` FOREIGN KEY (`id_role_ramdan`) REFERENCES `tb_role_ramdan` (`id_role_ramdan`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
