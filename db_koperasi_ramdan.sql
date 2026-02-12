-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 12, 2026 at 07:32 AM
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
  `id_anggota` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `tanggal_daftar` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_angsuran_ramdan`
--

CREATE TABLE `tb_angsuran_ramdan` (
  `id_angsuran` int(11) NOT NULL,
  `id_pinjaman` int(11) NOT NULL,
  `jumlah_bayar` decimal(15,2) NOT NULL,
  `sisa_pinjaman` decimal(15,2) NOT NULL,
  `tanggal_bayar` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_pinjaman_ramdan`
--

CREATE TABLE `tb_pinjaman_ramdan` (
  `id_pinjaman` int(11) NOT NULL,
  `id_anggota` int(11) NOT NULL,
  `jumlah_pinjaman` decimal(15,2) NOT NULL,
  `bunga` decimal(5,2) NOT NULL,
  `lama_pinjaman` int(11) NOT NULL,
  `total_pinjaman` decimal(15,2) NOT NULL,
  `status_pinjaman` enum('Diajukan','Disetujui','Lunas') DEFAULT 'Diajukan',
  `tanggal_pinjaman` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_role_ramdan`
--

CREATE TABLE `tb_role_ramdan` (
  `id_role` int(11) NOT NULL,
  `nama_role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_role_ramdan`
--

INSERT INTO `tb_role_ramdan` (`id_role`, `nama_role`) VALUES
(1, 'Admin'),
(2, 'Petugas');

-- --------------------------------------------------------

--
-- Table structure for table `tb_simpanan_ramdan`
--

CREATE TABLE `tb_simpanan_ramdan` (
  `id_simpanan` int(11) NOT NULL,
  `id_anggota` int(11) NOT NULL,
  `jenis_simpanan` enum('Pokok','Wajib','Sukarela') NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `tanggal` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_user_ramdan`
--

CREATE TABLE `tb_user_ramdan` (
  `id_user` int(11) NOT NULL,
  `id_role` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_user_ramdan`
--

INSERT INTO `tb_user_ramdan` (`id_user`, `id_role`, `username`, `password`, `nama`) VALUES
(1, 1, 'admin', 'admin123', 'Administrator'),
(2, 2, 'petugas', 'petugas123', 'Petugas Koperasi');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_anggota_ramdan`
--
ALTER TABLE `tb_anggota_ramdan`
  ADD PRIMARY KEY (`id_anggota`);

--
-- Indexes for table `tb_angsuran_ramdan`
--
ALTER TABLE `tb_angsuran_ramdan`
  ADD PRIMARY KEY (`id_angsuran`),
  ADD KEY `id_pinjaman` (`id_pinjaman`);

--
-- Indexes for table `tb_pinjaman_ramdan`
--
ALTER TABLE `tb_pinjaman_ramdan`
  ADD PRIMARY KEY (`id_pinjaman`),
  ADD KEY `id_anggota` (`id_anggota`);

--
-- Indexes for table `tb_role_ramdan`
--
ALTER TABLE `tb_role_ramdan`
  ADD PRIMARY KEY (`id_role`);

--
-- Indexes for table `tb_simpanan_ramdan`
--
ALTER TABLE `tb_simpanan_ramdan`
  ADD PRIMARY KEY (`id_simpanan`),
  ADD KEY `id_anggota` (`id_anggota`);

--
-- Indexes for table `tb_user_ramdan`
--
ALTER TABLE `tb_user_ramdan`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `id_role` (`id_role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_anggota_ramdan`
--
ALTER TABLE `tb_anggota_ramdan`
  MODIFY `id_anggota` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_angsuran_ramdan`
--
ALTER TABLE `tb_angsuran_ramdan`
  MODIFY `id_angsuran` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_pinjaman_ramdan`
--
ALTER TABLE `tb_pinjaman_ramdan`
  MODIFY `id_pinjaman` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_role_ramdan`
--
ALTER TABLE `tb_role_ramdan`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tb_simpanan_ramdan`
--
ALTER TABLE `tb_simpanan_ramdan`
  MODIFY `id_simpanan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_user_ramdan`
--
ALTER TABLE `tb_user_ramdan`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_angsuran_ramdan`
--
ALTER TABLE `tb_angsuran_ramdan`
  ADD CONSTRAINT `tb_angsuran_ramdan_ibfk_1` FOREIGN KEY (`id_pinjaman`) REFERENCES `tb_pinjaman_ramdan` (`id_pinjaman`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tb_pinjaman_ramdan`
--
ALTER TABLE `tb_pinjaman_ramdan`
  ADD CONSTRAINT `tb_pinjaman_ramdan_ibfk_1` FOREIGN KEY (`id_anggota`) REFERENCES `tb_anggota_ramdan` (`id_anggota`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tb_simpanan_ramdan`
--
ALTER TABLE `tb_simpanan_ramdan`
  ADD CONSTRAINT `tb_simpanan_ramdan_ibfk_1` FOREIGN KEY (`id_anggota`) REFERENCES `tb_anggota_ramdan` (`id_anggota`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tb_user_ramdan`
--
ALTER TABLE `tb_user_ramdan`
  ADD CONSTRAINT `tb_user_ramdan_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `tb_role_ramdan` (`id_role`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
