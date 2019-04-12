-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 12-04-2019 a las 01:54:20
-- Versión del servidor: 10.3.14-MariaDB
-- Versión de PHP: 7.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `datalabc_cms`
--
--CREATE DATABASE IF NOT EXISTS `datalabc_cms` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `datalabc_cms`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accounts`
--

DROP TABLE IF EXISTS `accounts`;
CREATE TABLE IF NOT EXISTS `accounts` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_hint` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_type_flag` tinyint(4) NOT NULL,
  `linked_accounts` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `starting_balance` decimal(10,2) DEFAULT 0.00,
  `hidden_flag` tinyint(4) DEFAULT 0,
  `closed_flag` tinyint(4) DEFAULT 0,
  `deleted_flag` tinyint(4) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `activities`
--

DROP TABLE IF EXISTS `activities`;
CREATE TABLE IF NOT EXISTS `activities` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NOT NULL,
  `location_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL COMMENT 'link to entry',
  `title` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `map_link` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `map_label` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `map_labelalt` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `map_link2` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `map_label2` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `map_labelalt2` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `info_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activity_type` tinyint(4) DEFAULT 0 COMMENT '0=not set, 1=trail, 2=tour, 3=attraction',
  `highlights` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wildlife` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facilities` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parking` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cost` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `distance` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `difficulty` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `elevation` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `season` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `public_transportation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trail_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'out and back, loop, etc',
  `user_id` int(10) UNSIGNED NOT NULL,
  `view_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `published_flag` tinyint(4) NOT NULL DEFAULT 0,
  `approved_flag` tinyint(4) NOT NULL DEFAULT 0,
  `deleted_flag` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `activity_location`
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'only used for subcategories',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_flag` tinyint(4) DEFAULT 0 COMMENT 'not used: 1=Expense, 2=Income, 3=Transfer',
  `amount` decimal(10,2) DEFAULT 0.00 COMMENT 'used for split transaction categories only',
  `deleted_flag` tinyint(4) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `site_id` int(10) NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `parent_id` int(10) NOT NULL COMMENT 'Parent can be an Entry or a Photo',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_flag` tinyint(4) NOT NULL DEFAULT -1 COMMENT '-1=not, 99=other',
  `approved_flag` tinyint(4) NOT NULL DEFAULT 0,
  `deleted_flag` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entries`
--

DROP TABLE IF EXISTS `entries`;
CREATE TABLE IF NOT EXISTS `entries` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NOT NULL,
  `location_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL COMMENT 'Parent can also be an Entry which describes a blog',
  `photo_id` int(11) DEFAULT NULL,
  `title` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `permalink` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_short` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'slug line, highlights, etc',
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `type_flag` tinyint(4) NOT NULL DEFAULT -1 COMMENT '-1=not set, 1=tour/hike, 2=note, 3=blog, 4=blog entry, 5=other',
  `view_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `published_flag` tinyint(4) NOT NULL DEFAULT 0,
  `approved_flag` tinyint(4) NOT NULL DEFAULT 0,
  `deleted_flag` tinyint(4) NOT NULL DEFAULT 0,
  `display_date` date DEFAULT NULL,
  `color_foreground` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color_background` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entries_tags`
--

DROP TABLE IF EXISTS `entries_tags`;
CREATE TABLE IF NOT EXISTS `entries_tags` (
  `entry_id` int(10) UNSIGNED NOT NULL,
  `tag_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `entries_tags_entry_id_tag_id_unique` (`entry_id`,`tag_id`),
  KEY `entries_tags_tag_id_foreign` (`tag_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entries_v1`
--

DROP TABLE IF EXISTS `entries_v1`;
CREATE TABLE IF NOT EXISTS `entries_v1` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_language1` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `type_flag` tinyint(4) NOT NULL DEFAULT -1,
  `view_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `map_link` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_template_flag` tinyint(4) NOT NULL DEFAULT 0,
  `uses_template_flag` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entry_location`
--

DROP TABLE IF EXISTS `entry_location`;
CREATE TABLE IF NOT EXISTS `entry_location` (
  `entry_id` int(10) UNSIGNED NOT NULL,
  `location_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `entry_location_entry_id_location_id_unique` (`entry_id`,`location_id`),
  KEY `entry_location_location_id_foreign` (`location_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entry_photo`
--

DROP TABLE IF EXISTS `entry_photo`;
CREATE TABLE IF NOT EXISTS `entry_photo` (
  `entry_id` int(10) UNSIGNED NOT NULL,
  `photo_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `entry_photo_entry_id_photo_id_unique` (`entry_id`,`photo_id`),
  KEY `entry_photo_photo_id_foreign` (`photo_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE IF NOT EXISTS `events` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `deleted_flag` tinyint(4) NOT NULL DEFAULT 0,
  `site_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_flag` tinyint(4) NOT NULL COMMENT '1=Info, 2=Warning, 3=Error, 4=Exception, 99=other',
  `model_flag` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_flag` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `record_id` int(10) UNSIGNED DEFAULT NULL,
  `updates` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `error` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extraInfo` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `faqs`
--

DROP TABLE IF EXISTS `faqs`;
CREATE TABLE IF NOT EXISTS `faqs` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `use_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `locations`
--

DROP TABLE IF EXISTS `locations`;
CREATE TABLE IF NOT EXISTS `locations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(11) NOT NULL,
  `location_type` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` smallint(4) DEFAULT 0,
  `popular_flag` tinyint(4) DEFAULT 0 COMMENT ' 	include in quick links to popular locations 	',
  `breadcrumb_flag` tinyint(4) DEFAULT 1,
  `deleted_flag` tinyint(4) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `photos`
--

DROP TABLE IF EXISTS `photos`;
CREATE TABLE IF NOT EXISTS `photos` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `alt_text` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_flag` tinyint(4) NOT NULL DEFAULT -1,
  `view_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `photo_order` int(11) DEFAULT NULL,
  `type_flag` tinyint(4) NOT NULL DEFAULT -1 COMMENT '-1=not set, 0=slider, 1=entry, 2=receipt, 3=slider horizontal only, 4=slider vertical only, 99=other',
  `gallery_flag` tinyint(4) NOT NULL DEFAULT 1,
  `permalink` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_flag` tinyint(4) NOT NULL DEFAULT 0,
  `main_flag` tinyint(4) NOT NULL DEFAULT 0,
  `display_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `photos_user_id_index` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sites`
--

DROP TABLE IF EXISTS `sites`;
CREATE TABLE IF NOT EXISTS `sites` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `deleted_flag` tinyint(4) NOT NULL DEFAULT 0,
  `user_id` int(10) UNSIGNED NOT NULL,
  `site_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `site_url` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `site_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_filename` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `main_section_text` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `main_section_subtext` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram_link` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook_link` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter_link` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `other_link` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_text` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tour_photos_minimum` tinyint(4) NOT NULL DEFAULT -1,
  `sections-show-blogs` tinyint(4) NOT NULL DEFAULT 0,
  `sections-show-tours` tinyint(4) NOT NULL DEFAULT 0,
  `sections-show-articles` tinyint(4) NOT NULL DEFAULT 0,
  `current_location_title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_location` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_location_map_link` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_location_photo` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `previous_location_title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `previous_location_list` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `affiliate_link_home_section1_1` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `affiliate_link_home_section1_2` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `affiliate_link_home_section1_3` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `affiliate_link_footer1` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `affiliate_link_footer2` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `affiliate_link1` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `affiliate_link2` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `affiliate_link3` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `affiliate_link4` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `affiliate_link5` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `affiliate_link6` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `affiliate_link7` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `affiliate_link8` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `affiliate_link9` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `affiliate_link10` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tags`
--

DROP TABLE IF EXISTS `tags`;
CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tasks`
--

DROP TABLE IF EXISTS `tasks`;
CREATE TABLE IF NOT EXISTS `tasks` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `description` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tasks_user_id_index` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `templates`
--

DROP TABLE IF EXISTS `templates`;
CREATE TABLE IF NOT EXISTS `templates` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `site_id` int(10) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_flag` tinyint(4) NOT NULL DEFAULT -1 COMMENT '-1=not set, 99=other',
  `view_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `approved_flag` tinyint(4) NOT NULL DEFAULT 0,
  `published_flag` tinyint(4) NOT NULL DEFAULT 0,
  `deleted_flag` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transactions`
--

DROP TABLE IF EXISTS `transactions`;
CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `description` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(10) UNSIGNED NOT NULL,
  `type_flag` tinyint(4) DEFAULT 0,
  `category_id` int(10) UNSIGNED NOT NULL,
  `subcategory_id` int(10) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_date` date NOT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vendor_memo` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transfer_account_id` tinyint(4) DEFAULT NULL,
  `transfer_id` int(11) DEFAULT NULL COMMENT 'id of the first transfer transaction',
  `reconciled_flag` tinyint(4) DEFAULT 1,
  `deleted_flag` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `subcategory_id` (`subcategory_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `translations`
--

DROP TABLE IF EXISTS `translations`;
CREATE TABLE IF NOT EXISTS `translations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
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
  `large_col1` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `large_col2` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `large_col3` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `large_col4` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `large_col5` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `large_col6` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `large_col7` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `large_col8` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `large_col9` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `large_col10` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_flag` tinyint(4) NOT NULL DEFAULT 0,
  `deleted_flag` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `translations_parent_id_parent_table_language_unique` (`parent_id`,`parent_table`,`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_type` smallint(6) NOT NULL DEFAULT 0,
  `template_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `view_id` tinyint(4) NOT NULL DEFAULT -1,
  `blocked_flag` tinyint(4) DEFAULT 1,
  `search_whole_words_flag` tinyint(4) NOT NULL DEFAULT -1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `visitors`
--

DROP TABLE IF EXISTS `visitors`;
CREATE TABLE IF NOT EXISTS `visitors` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `site_id` int(10) UNSIGNED NOT NULL,
  `ip_address` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `host_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referrer` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `organization` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `continent` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state_region` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visit_count` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `model` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `deleted_flag` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
