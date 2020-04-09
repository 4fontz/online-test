-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 09, 2020 at 03:14 PM
-- Server version: 10.1.32-MariaDB
-- PHP Version: 7.1.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `online_test`
--

-- --------------------------------------------------------

--
-- Table structure for table `online_login`
--

CREATE TABLE `online_login` (
  `login_id` int(11) NOT NULL,
  `login_name` varchar(150) NOT NULL,
  `login_username` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `login_password` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `login_status` enum('Y','N') NOT NULL DEFAULT 'Y',
  `login_created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `online_login`
--

INSERT INTO `online_login` (`login_id`, `login_name`, `login_username`, `login_password`, `login_status`, `login_created_at`) VALUES
(1, 'Admin', 'admin', '99eb3c8cb9d42beeeb12c7e4ef857649', 'Y', '2020-04-09 16:58:55');

-- --------------------------------------------------------

--
-- Table structure for table `online_purchase`
--

CREATE TABLE `online_purchase` (
  `purchase_id` int(11) NOT NULL,
  `purchase_name` varchar(150) NOT NULL,
  `purchase_unit` enum('Kg','Nos') NOT NULL DEFAULT 'Kg',
  `purchase_quantity` int(11) NOT NULL,
  `purchase_net_purchase_rate` decimal(10,0) NOT NULL DEFAULT '0',
  `purchase_markup` varchar(150) NOT NULL,
  `purchase_per_kg_piece` decimal(10,0) NOT NULL DEFAULT '0',
  `purchase_sales_price` decimal(10,0) NOT NULL DEFAULT '0',
  `purchase_created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `purchase_updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `online_purchase`
--

INSERT INTO `online_purchase` (`purchase_id`, `purchase_name`, `purchase_unit`, `purchase_quantity`, `purchase_net_purchase_rate`, `purchase_markup`, `purchase_per_kg_piece`, `purchase_sales_price`, `purchase_created_at`, `purchase_updated_at`) VALUES
(1, 'Apple', 'Kg', 10, '1000', '60', '100', '160', '2020-04-09 05:38:40', '2020-04-09 06:05:14'),
(2, 'Mobile Phone', 'Nos', 10, '150000', '10', '15000', '16500', '2020-04-09 06:16:31', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `online_login`
--
ALTER TABLE `online_login`
  ADD PRIMARY KEY (`login_id`);

--
-- Indexes for table `online_purchase`
--
ALTER TABLE `online_purchase`
  ADD PRIMARY KEY (`purchase_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `online_login`
--
ALTER TABLE `online_login`
  MODIFY `login_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `online_purchase`
--
ALTER TABLE `online_purchase`
  MODIFY `purchase_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
