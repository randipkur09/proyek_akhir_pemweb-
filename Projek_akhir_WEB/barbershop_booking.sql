-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 01, 2025 at 02:00 PM
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
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `barbershops`
--

INSERT INTO `barbershops` (`id`, `nama_barbershop`, `email`, `password`, `alamat`, `no_hp`, `jam_buka`, `jam_tutup`, `harga_potong`, `deskripsi`, `status`, `created_at`) VALUES
(1, 'Barbershop Keren', 'keren@barbershop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jl. Merdeka No. 123, Jakarta', '081234567890', '08:00:00', '21:00:00', 30000, 'Barbershop modern dengan pelayanan terbaik', 'aktif', '2025-06-01 11:59:02'),
(2, 'Classic Barber', 'classic@barbershop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jl. Sudirman No. 456, Jakarta', '081234567891', '08:00:00', '21:00:00', 25000, 'Barbershop klasik dengan gaya vintage', 'aktif', '2025-06-01 11:59:02'),
(3, 'Modern Cut', 'modern@barbershop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jl. Thamrin No. 789, Jakarta', '081234567892', '08:00:00', '21:00:00', 35000, 'Barbershop trendy untuk gaya rambut modern', 'aktif', '2025-06-01 11:59:02'),
(4, 'ceria barbershop', 'barbershop@gmail.com', '$2y$10$JlVRTn5.UsrrZnqRvkvyg.15MKxMA.02JWea/k6H7bOCgupcwi7au', 'Jl. Raya Serang-Pontang, Linduk, Kec. Pontang, Kabupaten Serang, Banten 42192', '08111047304', '08:00:00', '21:00:00', 40000, '-', 'aktif', '2025-06-01 12:13:43'),
(5, 'Randi BARBER', 'randipkur@gmail.com', '$2y$10$OtyS03dxZLYh9SYFhv0nOegP.C/PqZu6ImO9j/41yI5C5bpAThet2', 'awdawd', '087748183268', '08:00:00', '21:00:00', 25000, 'mantap', 'aktif', '2025-06-01 12:32:42'),
(7, 'randi barerw', 'randii@gmail.com', '$2y$10$ciHuYkRmV7Cv0o0NuDKz8.99n7j9xmzXw64qlYaZDxoPGkWSJhz1u', 'dawdwa', '123', '08:00:00', '21:00:00', 25000, 'adwad', 'aktif', '2025-06-01 13:19:13');

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
(1, 2, 1, '2025-06-05', '14:30:00', 'Potong Rambut', 'pending', '-', '2025-06-01 12:23:56', NULL),
(2, 3, 1, '2025-06-19', '11:30:00', 'Potong + Cuci', 'dibatalkan', 'dawdwa', '2025-06-01 13:17:25', NULL),
(3, 3, 7, '2025-06-19', '10:30:00', 'Potong + Cuci', 'selesai', 'dawadwa', '2025-06-01 13:20:11', NULL),
(4, 3, 7, '2025-06-13', '10:00:00', 'Potong + Styling', 'selesai', 'dwadw', '2025-06-01 13:34:40', 1),
(6, 3, 7, '2025-07-02', '13:00:00', 'Potong + Styling', 'selesai', 'adawd', '2025-06-01 13:50:42', 1),
(7, 3, 7, '2025-06-06', '13:00:00', 'Potong + Styling', 'dikonfirmasi', 'adwawd', '2025-06-01 13:54:08', 2),
(8, 3, 7, '2025-06-17', '13:00:00', 'Potong + Styling', 'dikonfirmasi', 'dwadaw', '2025-06-01 13:54:38', 3),
(9, 3, 7, '2025-06-12', '14:30:00', 'Potong + Styling', 'dikonfirmasi', 'adwawd', '2025-06-01 13:57:23', 50);

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
(3, 'randi', 'randi@gmail.com', '$2y$10$abHJOsZ0Z8tqcUKBUsUZWez2wDhDtHe3aTCSDzKwAVXiyMO8cI38W', '123', 'awdawd', '2025-06-01 13:16:53');

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`barbershop_id`) REFERENCES `barbershops` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
