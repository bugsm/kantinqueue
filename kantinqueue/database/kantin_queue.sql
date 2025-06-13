-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 11, 2025 at 12:58 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kantin_queue`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `SetEstimasiSelesai` (IN `pesanan_id` INT)   BEGIN
    DECLARE estimasi_menit INT;
    SET estimasi_menit = HitungEstimasiMenit();
    UPDATE pesanan
    SET estimasi_selesai = DATE_ADD(CURRENT_TIMESTAMP, INTERVAL estimasi_menit MINUTE)
    WHERE id_pesanan = pesanan_id;
END$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `HitungEstimasiMenit` () RETURNS INT DETERMINISTIC BEGIN
    DECLARE jumlah_antrian_aktif INT;
    DECLARE rata_rata_waktu_per_pesanan INT DEFAULT 5; -- Asumsi rata-rata 5 menit per pesanan
    SELECT COUNT(*) INTO jumlah_antrian_aktif FROM pesanan WHERE status = 'diproses' OR status = 'dipesan';
    RETURN jumlah_antrian_aktif * rata_rata_waktu_per_pesanan;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `detail_pesanan`
--

CREATE TABLE `detail_pesanan` (
  `id_detail` int NOT NULL,
  `id_pesanan` int DEFAULT NULL,
  `id_menu` int DEFAULT NULL,
  `jumlah` int NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `detail_pesanan`
--

INSERT INTO `detail_pesanan` (`id_detail`, `id_pesanan`, `id_menu`, `jumlah`, `subtotal`) VALUES
(1, 1, 1, 3, 45000.00),
(3, 3, 3, 2, 36000.00),
(5, 3, 4, 1, 3000.00);

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `id_mahasiswa` int NOT NULL,
  `nim` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `mahasiswa`
--

INSERT INTO `mahasiswa` (`id_mahasiswa`, `nim`, `nama`, `password`, `created_at`) VALUES
(1, '2317051112', 'Muhammad Nur Faadil', '$2y$10$cE2S7kYIZ6YxNuc3A0y/p.xHVuPEFSi9SbFU4KFieL.PPUHDV1Cxy', '2025-06-11 12:17:03');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id_menu` int NOT NULL,
  `nama_menu` varchar(100) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `stok` int NOT NULL,
  `estimasi_menit` int DEFAULT '5'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id_menu`, `nama_menu`, `harga`, `stok`, `estimasi_menit`) VALUES
(1, 'Nasi Goreng Spesial', 15000.00, 47, 5),
(2, 'Mie Ayam Bakso', 12000.00, 40, 4),
(3, 'Ayam Geprek Mozzarella', 18000.00, 28, 7),
(4, 'Es Teh Manis', 3000.00, 99, 1);

--
-- Triggers `menu`
--
DELIMITER $$
CREATE TRIGGER `cegah_harga_nol` BEFORE UPDATE ON `menu` FOR EACH ROW BEGIN
    IF NEW.harga < 1000 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error: Harga menu tidak boleh kurang dari Rp 1.000!';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` int NOT NULL,
  `id_mahasiswa` int DEFAULT NULL,
  `nomor_antrian` varchar(10) NOT NULL,
  `waktu_pesan` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `estimasi_selesai` timestamp NULL DEFAULT NULL,
  `status` enum('dipesan','diproses','selesai','dibatalkan','kadaluarsa') DEFAULT 'dipesan',
  `total_harga` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `id_mahasiswa`, `nomor_antrian`, `waktu_pesan`, `estimasi_selesai`, `status`, `total_harga`) VALUES
(1, 1, 'A-001', '2025-06-11 12:22:41', '2025-06-11 12:27:41', 'dipesan', 45000.00),
(3, 1, 'A-002', '2025-06-11 12:23:20', '2025-06-11 12:33:20', 'dipesan', 39000.00);

--
-- Triggers `pesanan`
--
DELIMITER $$
CREATE TRIGGER `update_statistik_pesanan` AFTER INSERT ON `pesanan` FOR EACH ROW BEGIN
    INSERT INTO statistik_harian (tanggal, total_pesanan_masuk)
    VALUES (CURDATE(), 1)
    ON DUPLICATE KEY UPDATE total_pesanan_masuk = total_pesanan_masuk + 1;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `statistik_harian`
--

CREATE TABLE `statistik_harian` (
  `tanggal` date NOT NULL,
  `total_pesanan_masuk` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_pesanan` (`id_pesanan`),
  ADD KEY `id_menu` (`id_menu`);

--
-- Indexes for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`id_mahasiswa`),
  ADD UNIQUE KEY `nim` (`nim`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id_menu`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`),
  ADD KEY `id_mahasiswa` (`id_mahasiswa`);

--
-- Indexes for table `statistik_harian`
--
ALTER TABLE `statistik_harian`
  ADD PRIMARY KEY (`tanggal`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  MODIFY `id_detail` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  MODIFY `id_mahasiswa` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id_menu` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD CONSTRAINT `detail_pesanan_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_pesanan_ibfk_2` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id_menu`);

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id_mahasiswa`) ON DELETE SET NULL;

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `ArsipkanPesananSelesai` ON SCHEDULE EVERY 1 DAY STARTS '2025-06-11 02:00:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    -- Contoh: mengubah status 'selesai' yang lebih dari 1 hari menjadi 'diarsip'
    -- UPDATE pesanan SET status = 'diarsip' WHERE status = 'selesai' AND waktu_pesan < DATE_SUB(NOW(), INTERVAL 1 DAY);
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
