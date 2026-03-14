-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 27, 2023 at 01:16 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bus`
--

-- --------------------------------------------------------

--
-- Table structure for table `bus_number_detection`
--

CREATE TABLE `bus_number_detection` (
  `id` int(6) NOT NULL,
  `bus_number` varchar(3) NOT NULL,
  `licence_plate_number` varchar(20) NOT NULL,
  `In_time` time NOT NULL,
  `In_date` date NOT NULL,
  `Out_time` time NOT NULL,
  `Out_Date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bus_number_detection`
--

INSERT INTO `bus_number_detection` (`id`, `bus_number`, `licence_plate_number`, `In_time`, `In_date`, `Out_time`, `Out_Date`) VALUES
(1, '9', 'TN 84 A55709', '16:38:23', '2023-07-27', '16:41:23', '2023-07-27'),
(2, '19', 'TN 84 c35619', '16:40:34', '2023-07-27', '00:00:00', '0000-00-00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bus_number_detection`
--
ALTER TABLE `bus_number_detection`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bus_number_detection`
--
ALTER TABLE `bus_number_detection`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
