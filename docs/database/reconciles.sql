-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 31, 2020 at 03:37 AM
-- Server version: 5.7.24
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
-- Database: `datalabc_travel`
--

-- --------------------------------------------------------

--
-- Table structure for table `reconciles`
--

CREATE TABLE `reconciles` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `account_id` int(10) UNSIGNED NOT NULL,
  `balance` decimal(10,2) NOT NULL,
  `reconcile_date` date DEFAULT NULL COMMENT 'date when account was reconciled',
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtotal1` decimal(10,0) DEFAULT NULL COMMENT 'for accounts with multiple balances',
  `subtotal2` decimal(10,0) DEFAULT NULL,
  `subtotal3` decimal(10,0) DEFAULT NULL,
  `subtotal4` decimal(10,0) DEFAULT NULL,
  `subtotal5` decimal(10,0) DEFAULT NULL,
  `subtotal_label1` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtotal_label2` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtotal_label3` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtotal_label4` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtotal_label5` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_flag` tinyint(4) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `reconciles`
--
ALTER TABLE `reconciles`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `reconciles`
--
ALTER TABLE `reconciles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
