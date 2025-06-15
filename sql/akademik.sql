-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 01, 2025 at 05:06 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `akademik`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `daftar_mahasiswa_per_dosen` (IN `nipDosen` VARCHAR(10))   BEGIN
    SELECT DISTINCT M.NIM, M.Nama
    FROM Mahasiswa M
    JOIN Nilai N ON M.NIM = N.NIM
    JOIN matakuliah MK ON N.Kode_Matkul = MK.Kode_Matkul
    WHERE MK.NIP = nipDosen;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `daftar_mahasiswa_per_mk` (IN `kodeMK` VARCHAR(10))   BEGIN
    SELECT M.NIM, M.Nama
    FROM Mahasiswa M
    JOIN Nilai N ON M.NIM = N.NIM
    WHERE N.Kode_Matkul = kodeMK;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `dosen`
--

CREATE TABLE `dosen` (
  `NIP` varchar(10) NOT NULL,
  `Nama_Dosen` varchar(100) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dosen`
--

INSERT INTO `dosen` (`NIP`, `Nama_Dosen`, `Email`) VALUES
('555555555', 'Asep Dedy', '555555@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `ipk`
--

CREATE TABLE `ipk` (
  `NIM` varchar(10) NOT NULL,
  `Total_SKS` int(11) DEFAULT 0,
  `Total_Bobot` decimal(10,2) DEFAULT 0.00,
  `IPK` decimal(4,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ipk`
--

INSERT INTO `ipk` (`NIM`, `Total_SKS`, `Total_Bobot`, `IPK`) VALUES
('2306041', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `NIM` varchar(10) NOT NULL,
  `Nama` varchar(100) DEFAULT NULL,
  `Alamat` text DEFAULT NULL,
  `Tanggal_Lahir` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mahasiswa`
--

INSERT INTO `mahasiswa` (`NIM`, `Nama`, `Alamat`, `Tanggal_Lahir`) VALUES
('2306041', 'Faiz', 'Cibatu', '2025-05-28');

-- --------------------------------------------------------

--
-- Table structure for table `matakuliah`
--

CREATE TABLE `matakuliah` (
  `kode_matkul` varchar(20) NOT NULL,
  `nama_matkul` varchar(100) NOT NULL,
  `sks` int(11) NOT NULL,
  `nip_dosen` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `matakuliah`
--

INSERT INTO `matakuliah` (`kode_matkul`, `nama_matkul`, `sks`, `nip_dosen`) VALUES
('MN10', 'Metode Numerik', 3, '555555555');

-- --------------------------------------------------------

--
-- Table structure for table `nilai`
--

CREATE TABLE `nilai` (
  `ID_Nilai` int(11) NOT NULL,
  `NIM` varchar(10) DEFAULT NULL,
  `Kode_Matkul` varchar(10) DEFAULT NULL,
  `Nilai` decimal(5,2) DEFAULT NULL,
  `Semester` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nilai`
--

INSERT INTO `nilai` (`ID_Nilai`, `NIM`, `Kode_Matkul`, `Nilai`, `Semester`) VALUES
(9, '2306041', 'MN10', 2.00, NULL);

--
-- Triggers `nilai`
--
DELIMITER $$
CREATE TRIGGER `hitung_ipk_setelah_nilai` AFTER INSERT ON `nilai` FOR EACH ROW BEGIN
    DECLARE bobot DECIMAL(5,2);
    DECLARE sks INT;

    SELECT SKS INTO sks FROM matakuliah WHERE kode_matkul = NEW.kode_matkul;
    SET bobot = NEW.Nilai * sks;

    INSERT INTO IPK (NIM, Total_SKS, Total_Bobot, IPK)
    VALUES (NEW.NIM, sks, bobot, bobot / sks)
    ON DUPLICATE KEY UPDATE
        Total_SKS = Total_SKS + sks,
        Total_Bobot = Total_Bobot + bobot,
        IPK = (Total_Bobot + bobot) / (Total_SKS + sks);
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dosen`
--
ALTER TABLE `dosen`
  ADD PRIMARY KEY (`NIP`);

--
-- Indexes for table `ipk`
--
ALTER TABLE `ipk`
  ADD PRIMARY KEY (`NIM`);

--
-- Indexes for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`NIM`);

--
-- Indexes for table `matakuliah`
--
ALTER TABLE `matakuliah`
  ADD PRIMARY KEY (`kode_matkul`),
  ADD KEY `nip_dosen` (`nip_dosen`);

--
-- Indexes for table `nilai`
--
ALTER TABLE `nilai`
  ADD PRIMARY KEY (`ID_Nilai`),
  ADD KEY `NIM` (`NIM`),
  ADD KEY `Kode_Matkul` (`Kode_Matkul`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `nilai`
--
ALTER TABLE `nilai`
  MODIFY `ID_Nilai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ipk`
--
ALTER TABLE `ipk`
  ADD CONSTRAINT `ipk_ibfk_1` FOREIGN KEY (`NIM`) REFERENCES `mahasiswa` (`NIM`);

--
-- Constraints for table `matakuliah`
--
ALTER TABLE `matakuliah`
  ADD CONSTRAINT `matakuliah_ibfk_1` FOREIGN KEY (`nip_dosen`) REFERENCES `dosen` (`NIP`);

--
-- Constraints for table `nilai`
--
ALTER TABLE `nilai`
  ADD CONSTRAINT `nilai_ibfk_1` FOREIGN KEY (`NIM`) REFERENCES `mahasiswa` (`NIM`),
  ADD CONSTRAINT `nilai_ibfk_2` FOREIGN KEY (`Kode_Matkul`) REFERENCES `matakuliah` (`kode_matkul`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
