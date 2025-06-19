-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 18, 2025 at 02:30 AM
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
-- Database: `barbershop_booking`
--

-- --------------------------------------------------------

--
-- Table structure for table `barbershops`
--

CREATE TABLE `barbershops` (
  `id` int NOT NULL,
  `nama_barbershop` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `alamat` text NOT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `jam_buka` time DEFAULT '08:00:00',
  `jam_tutup` time DEFAULT '21:00:00',
  `harga_potong` int DEFAULT '25000',
  `deskripsi` text,
  `photo_path` varchar(255) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `barbershops`
--

INSERT INTO `barbershops` (`id`, `nama_barbershop`, `email`, `password`, `alamat`, `no_hp`, `jam_buka`, `jam_tutup`, `harga_potong`, `deskripsi`, `photo_path`, `foto`, `status`, `created_at`) VALUES
(10, 'cutnco', 'cutnco@gmail.com', '$2y$10$SN92xX3TryZ6nrv8Blqspuvz4vXnq2y1Ylmy1PPEf29tgPv6Xtol2', 'Jl. Sultan Agung No.18 E, Sepang Jaya, Kec. Kedaton, Kota Bandar Lampung, Lampung 35132', '08111047304', '11:00:00', '21:00:00', 30000, 'barbershop terbaik dibandar lampung', 'uploads/6852223ec888f_2025-05-27.webp', NULL, 'aktif', '2025-06-18 01:37:53'),
(11, 'nobleman barbershop', 'nobleman@gmail.com', '$2y$10$NFa9MnHsb0vCaNm5pp3ES.Iy60dTCJ8y7HCdSaa/wRCwOXPcW3TEO', ' Jl. Gatot Subroto No.7, Pahoman, Enggal, Kota Bandar Lampung, Lampung 35227', '08111047304', '12:00:00', '21:00:00', 40000, 'barbershop dengan pelayanan terbaik di bandar lampung', 'uploads/685221b52aef2_2025-04-29.webp', NULL, 'aktif', '2025-06-18 02:17:25'),
(12, 'vanman barbershop', 'vanman@gmail.com', '$2y$10$zNaWFwI0HCInrRNcKoOkp.GXB9fDfRVc62.heefu.RVieN.o64dTe', 'Jl. Gajah Mada No.89 E, Tj. Agung Raya, Kec. Tanjungkarang Timur, Kota Bandar Lampung, Lampung 35125', '08111047304', '10:00:00', '22:00:00', 35000, 'barbershop dengan pelayanan murah tetapi dengan kualitas terbaik', 'uploads/6852246871280_unnamed.webp', NULL, 'aktif', '2025-06-18 02:28:56');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `barbershop_id` int NOT NULL,
  `tanggal_booking` date NOT NULL,
  `jam_booking` time NOT NULL,
  `layanan` varchar(100) DEFAULT 'Potong Rambut',
  `status` enum('pending','dikonfirmasi','selesai','dibatalkan') DEFAULT 'pending',
  `catatan` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `nomor_antrian` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `barbershop_id`, `tanggal_booking`, `jam_booking`, `layanan`, `status`, `catatan`, `created_at`, `nomor_antrian`) VALUES
(17, 11, 10, '2025-06-19', '12:30:00', 'cuci dan keramas', 'dibatalkan', 'gaya rambut cepak', '2025-06-18 02:10:18', NULL),
(18, 11, 10, '2025-06-19', '15:30:00', 'cuci dan keramas', 'pending', '-', '2025-06-18 02:23:38', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `layanan_barbershop`
--

CREATE TABLE `layanan_barbershop` (
  `id` int NOT NULL,
  `barbershop_id` int NOT NULL,
  `nama_layanan` varchar(100) NOT NULL,
  `harga` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `layanan_barbershop`
--

INSERT INTO `layanan_barbershop` (`id`, `barbershop_id`, `nama_layanan`, `harga`) VALUES
(34, 11, 'cuci dan keramas', 40000),
(35, 11, 'hair coloring', 200000),
(36, 11, 'perming', 150000),
(37, 10, 'cuci dan keramas', 30000),
(38, 10, 'perming', 100000),
(39, 10, 'coloring', 200000),
(42, 12, 'potong + keramas', 35000),
(43, 12, 'potong + keramas + pijat', 40000);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `alamat` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `no_hp`, `alamat`, `created_at`) VALUES
(1, 'John Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567893', 'Jl. Kebon Jeruk No. 321, Jakarta', '2025-06-01 11:59:02'),
(2, 'zidan rosyid', 'zidanrosyid22@gmail.com', '$2y$10$h5wx2v6TioNGeTu8EokuAeNX0/IpNKrsoY7erjpAwC4BXefLfCXXa', '081110473041', 'Jl. Raya Serang-Pontang, Linduk, Kec. Pontang, Kabupaten Serang, Banten 42192', '2025-06-01 12:15:02'),
(3, 'randi', 'randi@gmail.com', '$2y$10$abHJOsZ0Z8tqcUKBUsUZWez2wDhDtHe3aTCSDzKwAVXiyMO8cI38W', '123', 'awdawd', '2025-06-01 13:16:53'),
(4, 'Lutfi Harya', 'lutfi@gmail.com', '$2y$10$iv1AnHvoFmt9WwMK/iIJT.sfgsmQJSBskM3MyXC5e/Zpi0X4nCKWm', '08111047304112', '-', '2025-06-01 15:45:51'),
(5, 'zidan rosyid', 'zrosyid119@gmail.com', '$2y$10$9h9ncNOmBe1lDm6QmKgQ7.oU1r0Wv7T7WpGsvN3PBdgv5HUfTOc.K', '08111047304', 'serang', '2025-06-15 16:48:58'),
(7, 'zidan rosyid', 'zidanrosyid@gmail.com', '$2y$10$XMW/NR30LQfGQJ89Q8BHyuWk7Xo673FmV7rmhkcMGPv.JCITKEm6C', '08111047304', 'Jl. Raya Serang-Pontang, Linduk, Kec. Pontang, Kabupaten Serang, Banten 42192', '2025-06-17 01:30:17'),
(11, 'zidan rosyid', 'zrosyid118@gmail.com', '$2y$10$ew3Qn0i/RL/PuZweHaULC.rA9/p2IkuVXCHjaoDi9gv4mpMVbd04i', '08111047304', 'serang', '2025-06-18 01:39:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barbershops`
--
ALTER TABLE `barbershops`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `barbershop_id` (`barbershop_id`);

--
-- Indexes for table `layanan_barbershop`
--
ALTER TABLE `layanan_barbershop`
  ADD PRIMARY KEY (`id`),
  ADD KEY `barbershop_id` (`barbershop_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barbershops`
--
ALTER TABLE `barbershops`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `layanan_barbershop`
--
ALTER TABLE `layanan_barbershop`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`barbershop_id`) REFERENCES `barbershops` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `layanan_barbershop`
--
ALTER TABLE `layanan_barbershop`
  ADD CONSTRAINT `layanan_barbershop_ibfk_1` FOREIGN KEY (`barbershop_id`) REFERENCES `barbershops` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
