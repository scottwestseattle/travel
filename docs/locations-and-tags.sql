-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 19, 2018 at 03:10 PM
-- Server version: 5.7.19
-- PHP Version: 7.1.9

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
-- Table structure for table `activity_location`
--

DROP TABLE IF EXISTS `activity_location`;
CREATE TABLE IF NOT EXISTS `activity_location` (
  `activity_id` int(10) UNSIGNED NOT NULL,
  `location_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `entries_tags_entry_id_tag_id_unique` (`activity_id`,`location_id`),
  KEY `entries_tags_tag_id_foreign` (`location_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_location`
--

INSERT INTO `activity_location` (`activity_id`, `location_id`, `created_at`, `updated_at`) VALUES
(14, 1, '2018-05-17 23:30:06', '2018-05-17 23:30:06'),
(1, 1, '2018-05-17 23:30:54', '2018-05-17 23:30:54'),
(1, 2, '2018-05-18 19:31:29', '2018-05-18 19:31:29'),
(1, 3, '2018-05-18 19:31:29', '2018-05-18 19:31:29'),
(14, 2, '2018-05-18 19:32:31', '2018-05-18 19:32:31'),
(14, 3, '2018-05-18 19:32:31', '2018-05-18 19:32:31'),
(6, 2, '2018-05-18 19:36:12', '2018-05-18 19:36:12'),
(6, 3, '2018-05-18 19:36:12', '2018-05-18 19:36:12'),
(6, 4, '2018-05-18 19:37:35', '2018-05-18 19:37:35'),
(6, 7, '2018-05-18 22:01:45', '2018-05-18 22:01:45'),
(4, 2, '2018-05-18 19:36:12', '2018-05-18 19:36:12'),
(4, 3, '2018-05-18 19:36:12', '2018-05-18 19:36:12'),
(4, 7, '2018-05-18 22:01:45', '2018-05-18 22:01:45'),
(4, 5, '2018-05-18 22:01:45', '2018-05-18 22:01:45');

-- --------------------------------------------------------

--
-- Table structure for table `entry_tag`
--

DROP TABLE IF EXISTS `entry_tag`;
CREATE TABLE IF NOT EXISTS `entry_tag` (
  `entry_id` int(10) UNSIGNED NOT NULL,
  `tag_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `entries_tags_entry_id_tag_id_unique` (`entry_id`,`tag_id`),
  KEY `entries_tags_tag_id_foreign` (`tag_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `entry_tag`
--

INSERT INTO `entry_tag` (`entry_id`, `tag_id`, `created_at`, `updated_at`) VALUES
(14, 1, '2018-05-17 23:30:06', '2018-05-17 23:30:06'),
(1, 1, '2018-05-17 23:30:54', '2018-05-17 23:30:54'),
(1, 2, '2018-05-18 19:31:29', '2018-05-18 19:31:29'),
(1, 3, '2018-05-18 19:31:29', '2018-05-18 19:31:29'),
(14, 2, '2018-05-18 19:32:31', '2018-05-18 19:32:31'),
(14, 3, '2018-05-18 19:32:31', '2018-05-18 19:32:31'),
(9, 2, '2018-05-18 19:36:12', '2018-05-18 19:36:12'),
(9, 3, '2018-05-18 19:36:12', '2018-05-18 19:36:12'),
(9, 5, '2018-05-18 19:37:35', '2018-05-18 19:37:35');

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

DROP TABLE IF EXISTS `locations`;
CREATE TABLE IF NOT EXISTS `locations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(11) NOT NULL,
  `location_type` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` smallint(4) DEFAULT '0',
  `breadcrumb_flag` tinyint(4) DEFAULT '1',
  `deleted_flag` tinyint(4) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `user_id`, `parent_id`, `location_type`, `name`, `level`, `breadcrumb_flag`, `deleted_flag`, `created_at`, `updated_at`) VALUES
(32, 1, 31, 500, 'Washington', 0, 1, 0, '2018-05-19 07:26:02', '2018-05-19 07:26:02'),
(31, 1, 30, 400, 'Pacific Northwest', 0, 1, 0, '2018-05-19 07:25:42', '2018-05-19 07:25:42'),
(30, 1, 27, 300, 'USA', 0, 1, 0, '2018-05-19 07:25:26', '2018-05-19 07:25:26'),
(28, 1, 0, 100, 'South America', 0, 1, 0, '2018-05-19 07:22:27', '2018-05-19 07:22:27'),
(27, 1, 0, 100, 'North America', 0, 1, 0, '2018-05-19 07:22:20', '2018-05-19 07:22:20'),
(26, 1, 0, 100, 'Oceana', 0, 1, 0, '2018-05-19 07:22:13', '2018-05-19 07:22:13'),
(25, 1, 0, 100, 'Europe', 0, 1, 0, '2018-05-19 07:22:05', '2018-05-19 07:22:05'),
(24, 1, 0, 100, 'Africa', 0, 1, 0, '2018-05-19 07:21:46', '2018-05-19 07:21:46'),
(23, 1, 0, 100, 'Asia', 0, 1, 0, '2018-05-19 07:21:21', '2018-05-19 07:21:21'),
(33, 1, 32, 700, 'Seattle', 0, 1, 0, '2018-05-19 07:26:19', '2018-05-19 07:26:19'),
(34, 1, 32, 600, 'Olympic Peninsula', 0, 1, 0, '2018-05-19 07:51:55', '2018-05-19 07:51:55'),
(35, 1, 34, 700, 'Olympic National Park', 0, 1, 0, '2018-05-19 07:52:16', '2018-05-19 07:52:16'),
(36, 1, 34, 700, 'Port Angeles', 0, 1, 0, '2018-05-19 07:52:36', '2018-05-19 07:52:36'),
(39, 1, 0, 100, 'Central America', 0, 1, 0, '2018-05-19 08:29:50', '2018-05-19 08:29:50'),
(38, 1, 33, 700, 'North Bend', 0, 1, 0, '2018-05-19 07:54:28', '2018-05-19 08:26:22'),
(40, 1, 39, 300, 'Costa Rica', 0, 1, 0, '2018-05-19 08:30:07', '2018-05-19 08:30:07');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` tinyint(4) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `user_id`, `name`, `level`, `created_at`, `updated_at`) VALUES
(1, 1, 'Seattle', 60, '2018-01-13 04:51:50', '2018-01-13 04:51:50'),
(2, 1, 'Washington', 30, '2018-01-13 04:52:06', '2018-01-13 04:52:06'),
(3, 1, 'USA', 10, '2018-01-13 04:52:19', '2018-01-13 04:52:19'),
(4, 1, 'Olympic National Park', 50, '2018-01-13 04:52:33', '2018-01-13 04:52:33'),
(5, 1, 'Port Angeles', 60, '2018-01-13 04:52:41', '2018-01-13 04:52:41'),
(6, 1, 'North Bend', 60, '2018-01-13 04:52:46', '2018-01-13 04:52:46'),
(7, 1, 'Olympic Peninsula', 40, '2018-05-18 19:41:31', '2018-05-18 19:41:31'),
(8, 1, 'Pacific Northwest', 20, '2018-05-18 19:42:09', '2018-05-18 19:42:09'),
(9, 1, 'West Seattle', 70, '2018-05-18 19:44:23', '2018-05-18 19:44:23');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
