-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 06, 2018 at 08:44 PM
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
-- Table structure for table `sites`
--

CREATE TABLE `sites` (
  `id` int(10) UNSIGNED NOT NULL,
  `deleted_flag` tinyint(4) NOT NULL DEFAULT '0',
  `user_id` int(10) UNSIGNED NOT NULL,
  `site_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `site_url` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `site_title` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_filename` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `main_section_text` text COLLATE utf8mb4_unicode_ci,
  `main_section_subtext` text COLLATE utf8mb4_unicode_ci,
  `seo_text` text COLLATE utf8mb4_unicode_ci,
  `tour_photos_minimum` tinyint(4) NOT NULL DEFAULT '-1',
  `sections-show-blogs` tinyint(4) NOT NULL DEFAULT '0',
  `sections-show-tours` tinyint(4) NOT NULL DEFAULT '0',
  `sections-show-articles` tinyint(4) NOT NULL DEFAULT '0',
  `current_location_map_link` text COLLATE utf8mb4_unicode_ci,
  `current_location_photo` text COLLATE utf8mb4_unicode_ci,
  `previous_location_list` text COLLATE utf8mb4_unicode_ci,
  `affiliate_link_home_section1_1` text COLLATE utf8mb4_unicode_ci,
  `affiliate_link_home_section1_2` text COLLATE utf8mb4_unicode_ci,
  `affiliate_link_home_section1_3` text COLLATE utf8mb4_unicode_ci,
  `affiliate_link_footer1` text COLLATE utf8mb4_unicode_ci,
  `affiliate_link_footer2` text COLLATE utf8mb4_unicode_ci,
  `affiliate_link1` text COLLATE utf8mb4_unicode_ci,
  `affiliate_link2` text COLLATE utf8mb4_unicode_ci,
  `affiliate_link3` text COLLATE utf8mb4_unicode_ci,
  `affiliate_link4` text COLLATE utf8mb4_unicode_ci,
  `affiliate_link5` text COLLATE utf8mb4_unicode_ci,
  `affiliate_link6` text COLLATE utf8mb4_unicode_ci,
  `affiliate_link7` text COLLATE utf8mb4_unicode_ci,
  `affiliate_link8` text COLLATE utf8mb4_unicode_ci,
  `affiliate_link9` text COLLATE utf8mb4_unicode_ci,
  `affiliate_link10` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sites`
--

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sites`
--
ALTER TABLE `sites`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sites`
--
ALTER TABLE `sites`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
