-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 16, 2018 at 02:52 PM
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
-- Table structure for table `activities`
--

DROP TABLE IF EXISTS `activities`;
CREATE TABLE IF NOT EXISTS `activities` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `map_link` text COLLATE utf8mb4_unicode_ci,
  `highlights` text COLLATE utf8mb4_unicode_ci,
  `wildlife` text COLLATE utf8mb4_unicode_ci,
  `facilities` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parking` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entry_fee` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `distance` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `difficulty` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `elevation_change` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `season` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `public_transportation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `view_count` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `published_flag` tinyint(4) NOT NULL DEFAULT '0',
  `approved_flag` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`id`, `title`, `description`, `map_link`, `highlights`, `wildlife`, `facilities`, `parking`, `entry_fee`, `distance`, `difficulty`, `elevation_change`, `season`, `public_transportation`, `user_id`, `view_count`, `published_flag`, `approved_flag`, `created_at`, `updated_at`) VALUES
(1, 'First Activity', 'This is the first activity.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 0, 0, '2018-05-16 06:55:54', '2018-05-16 06:55:54'),
(2, 'Lincoln Park HIke', 'This is the good hike.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 0, 0, '2018-05-16 08:09:11', '2018-05-16 08:09:45'),
(3, 'Alki Beach Trail', 'Alki Trail and Alki Beach Park is a 3 mile long ocean front promenade that runs from Alki Point to Duwamish Head on Elliott Bay. It\'s a great spot for a walk or bike ride any time of year, and in the summer draws joggers, roller-bladers, volleyball players, beachcombers, sunbathers, bicyclists and strollers out to enjoy the sun.', 'https://www.google.com/maps/d/embed?mid=1n1bbV3bRJEL6KAl3H5Gbr8cpo6cv7qiJ', 'Great views of downtown Seattle, Puget Sound, and the San Juan Islands.', 'Waterfowl, Sea lions, Bald Eagles (rare)', 'Bathrooms, Park Benches, Picnic tables, Boat dock, Fire Pits, Volleyball courts (nets not included)', 'Free street parking', 'None', '3 miles', 'Easy', 'None', 'Year round', 'Bus 56 connects to downtown Seattle.  Bus 50 connects to California Ave, West Seattle', 1, 0, 0, 0, '2018-05-16 08:18:28', '2018-05-16 10:29:00');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
