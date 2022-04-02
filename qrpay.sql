-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 27, 2022 at 10:18 PM
-- Server version: 10.3.16-MariaDB
-- PHP Version: 7.3.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `qrpay`
--

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `senderId` int(11) DEFAULT NULL COMMENT 'User that''s sending',
  `transId` varchar(10) DEFAULT NULL,
  `type` varchar(30) DEFAULT NULL,
  `amount` decimal(10,1) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `transType` enum('debit','credit') NOT NULL DEFAULT 'debit',
  `receiverId` int(11) DEFAULT NULL COMMENT 'User that''s receiving',
  `date` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `senderId`, `transId`, `type`, `amount`, `description`, `transType`, `receiverId`, `date`) VALUES
(1, 1, '2203246402', 'transfer', '100.0', 'Transfer from LOAD22YAMS to TEME226ERE // Thanks for yesterday', 'debit', 2, '1648135398'),
(2, 1, '2203248609', 'transfer', '100.0', 'Transfer from LOAD22YAMS to TEME226ERE // Test', 'debit', 2, '1648137773'),
(3, 1, '2203271971', 'transfer', '500.0', 'Transfer from LOAD22YAMS to TEME226ERE // Test Qr Method', 'debit', 2, '1648411152');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `uid` varchar(20) DEFAULT NULL,
  `balance` decimal(10,1) DEFAULT 0.0,
  `date` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `uid`, `balance`, `date`) VALUES
(1, 'Logad', 'LOAD22YAMS', '49300.0', '1648011074'),
(2, 'Test Name', 'TEME226ERE', '50700.0', '1648129150'),
(3, 'Lystun', 'LYUN22V4SB', '50000.0', '1648412195');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
