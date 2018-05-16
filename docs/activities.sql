-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 16, 2018 at 07:19 PM
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
  `info_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activity_type` tinyint(4) DEFAULT '0' COMMENT '0=not set, 1=trail, 2=tour, 3=attraction',
  `highlights` text COLLATE utf8mb4_unicode_ci,
  `wildlife` text COLLATE utf8mb4_unicode_ci,
  `facilities` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parking` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cost` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `distance` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `difficulty` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `elevation` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `season` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `public_transportation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trail_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'out and back, loop, etc',
  `user_id` int(10) UNSIGNED NOT NULL,
  `view_count` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `published_flag` tinyint(4) NOT NULL DEFAULT '0',
  `approved_flag` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_flag` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`id`, `title`, `description`, `map_link`, `info_link`, `activity_type`, `highlights`, `wildlife`, `facilities`, `parking`, `cost`, `distance`, `difficulty`, `elevation`, `season`, `public_transportation`, `trail_type`, `user_id`, `view_count`, `published_flag`, `approved_flag`, `deleted_flag`, `created_at`, `updated_at`) VALUES
(1, 'Seattle Waterfront to Lake Union via Ship Canal', 'This 8.5 mile Hike or Bike loop starts at the Olympic Sculpture Park at the entrance to Myrtle Edwards Park (Broad Street and Elliot Avenue). From the Elliot Bay Trail there is a protected bike trail that connects with the South Ship Canal Trail, then joins the Lake Union Loop Trail.', 'https://www.google.com/maps/d/embed?mid=1DCC6Grd1QN9n_vAdYMMJCgy5h8Hegfa5', NULL, 0, 'Beautiful views of Downtown Seattle, Elliot Bay, Puget Sound, the Olympic Mountains.', NULL, NULL, NULL, NULL, '8.5 miles / 13.7 kms', NULL, NULL, NULL, NULL, NULL, 1, 0, 0, 0, 0, '2018-05-16 06:55:54', '2018-05-17 01:00:00'),
(2, 'Lincoln Park HIke', 'This is the good hike.', NULL, NULL, NULL, NULL, NULL, NULL, 'Free parking in the lots off of Fauntleroy Ave.', NULL, '2 miles / 3.2 kms', 'Easy', 'Change: 200 ft', 'Year-round', NULL, 'Out and Back or Loop', 1, 0, 0, 0, 0, '2018-05-16 08:09:11', '2018-05-17 02:17:00'),
(3, 'Alki Beach Trail', 'Alki Trail and Alki Beach Park is a 3 mile long ocean front promenade that runs from Alki Point to Duwamish Head on Elliott Bay. It\'s a great spot for a walk or bike ride any time of year, and in the summer draws joggers, roller-bladers, volleyball players, beachcombers, sunbathers, bicyclists and strollers out to enjoy the sun.', 'https://www.google.com/maps/d/embed?mid=1n1bbV3bRJEL6KAl3H5Gbr8cpo6cv7qiJ', NULL, NULL, 'Great views of downtown Seattle, Puget Sound, and the San Juan Islands.', 'Waterfowl, Sea lions, Bald Eagles (rare)', 'Bathrooms, Park Benches, Picnic tables, Boat dock, Fire Pits, Volleyball courts (nets not included)', 'Free street parking', NULL, '3 miles / 4.8 kms', 'Easy', 'Sea level', 'Year round', 'Bus 56 connects to downtown Seattle.  Bus 50 connects to California Ave, West Seattle', 'Out and Back', 1, 0, 0, 0, 0, '2018-05-16 08:18:28', '2018-05-17 02:18:05');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
