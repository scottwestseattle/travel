-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 23, 2018 at 12:13 AM
-- Server version: 5.7.14
-- PHP Version: 7.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
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
-- Table structure for table `translations`
--

CREATE TABLE `translations` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_table` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` int(10) UNSIGNED NOT NULL,
  `language` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `small_col1` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `small_col2` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `small_col3` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `small_col4` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `small_col5` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `small_col6` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `small_col7` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `small_col8` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `small_col9` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `small_col10` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `medium_col1` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `medium_col2` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `medium_col3` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `medium_col4` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `medium_col5` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `medium_col6` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `medium_col7` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `medium_col8` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `medium_col9` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `medium_col10` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `large_col1` text COLLATE utf8mb4_unicode_ci,
  `large_col2` text COLLATE utf8mb4_unicode_ci,
  `large_col3` text COLLATE utf8mb4_unicode_ci,
  `large_col4` text COLLATE utf8mb4_unicode_ci,
  `large_col5` text COLLATE utf8mb4_unicode_ci,
  `large_col6` text COLLATE utf8mb4_unicode_ci,
  `large_col7` text COLLATE utf8mb4_unicode_ci,
  `large_col8` text COLLATE utf8mb4_unicode_ci,
  `large_col9` text COLLATE utf8mb4_unicode_ci,
  `large_col10` text COLLATE utf8mb4_unicode_ci,
  `approved_flag` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_flag` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `translations`
--
ALTER TABLE `translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `translations_parent_id_parent_table_language_unique` (`parent_id`,`parent_table`,`language`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `translations`
--
ALTER TABLE `translations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
