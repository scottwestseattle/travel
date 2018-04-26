-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 26, 2018 at 01:55 AM
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
-- Table structure for table `entries`
--

CREATE TABLE `entries` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `description_language1` text COLLATE utf8mb4_unicode_ci,
  `user_id` int(10) UNSIGNED NOT NULL,
  `type_flag` tinyint(4) NOT NULL DEFAULT '-1',
  `view_count` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `map_link` text COLLATE utf8mb4_unicode_ci,
  `main_photo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_template_flag` tinyint(4) NOT NULL DEFAULT '0',
  `uses_template_flag` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `entries`
--

INSERT INTO `entries` (`id`, `title`, `description`, `description_language1`, `user_id`, `type_flag`, `view_count`, `map_link`, `main_photo`, `is_template_flag`, `uses_template_flag`, `created_at`, `updated_at`) VALUES
(2, 'Epic Places: Santorini Island', 'Santorini Island, known as Thira to the Greeks, was once home to a wealthy maritime society. A violent volcanic eruption destroyed the community, but left behind the dramatic landscapes the island is now famous for. The coast is lined with black-sand beaches, scarred hills and steep cliffs. Inland, plant life flourishes in the soil that was enriched by the lava. But there is more to Santorini than natural beauty. Tourists can also explore the island’s cultural offerings at vineyards, archaeological museums and ruins.', NULL, 1, -1, 14, '', '', 0, 1, '2018-01-02 04:47:19', '2018-04-15 03:14:21'),
(3, 'West Seattle: California Ave', 'Alki Trail and Alki Beach Park is a long beach strip that runs from Alki Point to Duwamish Head on Elliott Bay. It\'s a great spot for a 3 mile walk any time of year, and in the summer draws joggers, roller-bladers, volleyball players, beachcombers, sunbathers, bicyclists and strollers out to enjoy the sun.', NULL, 1, -1, 0, 'https://www.google.com/maps/d/embed?mid=1n1bbV3bRJEL6KAl3H5Gbr8cpo6cv7qiJ', '', 1, 0, '2018-04-18 02:03:10', '2018-04-23 21:55:30'),
(4, 'West Seattle: Lincoln Park Hike', 'With views of the snow-capped Olympic Mountains and the San Juan Islands, this 2 mile partially paved, out and back or loop trail follows the Pacific coastline. To make it a loop, follow the ocean front trail and return by going up to the bluff through the old growth forest with more ocean views.', NULL, 1, -1, 0, 'https://www.google.com/maps/d/embed?mid=1DCC6Grd1QN9n_vAdYMMJCgy5h8Hegfa5', '', 1, 0, '2018-04-17 10:35:20', '2018-04-23 10:48:23'),
(5, 'West Seattle: Alki Beach Trail', 'Alki Trail and Alki Beach Park is a long beach strip that runs from Alki Point to Duwamish Head on Elliott Bay. It\'s a great spot for a 3 mile walk any time of year, and in the summer draws joggers, roller-bladers, volleyball players, beachcombers, sunbathers, bicyclists and strollers out to enjoy the sun.', NULL, 1, -1, 0, NULL, '', 1, 0, '2018-04-19 02:39:25', '2018-04-23 22:00:57'),
(6, 'Burke-Gilman Trail', NULL, NULL, 1, -1, 0, '', '', 1, 0, '2018-04-20 22:46:54', '2018-04-20 22:46:54'),
(7, 'West Seattle Water Taxi', 'description', NULL, 1, -1, 2, NULL, '', 1, 0, '2018-04-18 02:03:50', '2018-04-23 22:05:24'),
(8, 'Best Gear for Traveling Light', 'This is the best gear for traveling as light as possible.', NULL, 1, -1, 0, '', '', 0, 0, '2018-04-14 21:47:29', '2018-04-14 21:47:29'),
(1, 'Seattle: Downtown Waterfront to Ship Canal', 'This 8.5 mile Hike or Bike loop starts at the Olympic Sculpture Park at the entrance to Myrtle Edwards Park (Broad Street and Elliot Avenue).  From the Elliot Bay Trail there is a protected bike trail that connects with the South Ship Canal Trail, then joins the Lake Union Loop Trail.', NULL, 1, -1, 0, '', '', 1, 0, '2018-04-17 06:54:15', '2018-04-17 10:53:42'),
(9, 'Test New Entry', 'test new entry', NULL, 1, -1, 0, NULL, NULL, 0, 0, '2018-04-23 10:56:35', '2018-04-23 21:59:38'),
(14, 'Downtown Tourist Attractions', 'Walking tour of Downtown Tourist Attractions', NULL, 1, -1, 0, NULL, NULL, 1, 0, '2018-04-23 21:59:17', '2018-04-23 21:59:17');

-- --------------------------------------------------------

--
-- Table structure for table `entries_tags`
--

CREATE TABLE `entries_tags` (
  `entry_id` int(10) UNSIGNED NOT NULL,
  `tag_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `link` text COLLATE utf8mb4_unicode_ci,
  `use_count` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faqs`
--

INSERT INTO `faqs` (`id`, `user_id`, `title`, `description`, `link`, `use_count`, `created_at`, `updated_at`) VALUES
(3, 1, 'Development Quick Links', '[Localhost PhpMyAdmin](http://localhost/phpmyadmin/)\r\n\r\n[Google Docs - My Projects](https://docs.google.com/spreadsheets/d/1HtVjeFZpJMftnX9sfQRJ-gJSWE5ju4UJZfaYsLMBrA0/edit#gid=1878467571)\r\n\r\n[Seattle Weather](https://www.google.com/search?q=seattle+weather&rlz=1C1CHBF_enUS771US771&oq=seattle+we&aqs=chrome.0.69i59j69i60l2j69i61j0l2.2023j0j4&sourceid=chrome&ie=UTF-8)\r\n\r\n[Laravel Eloquent Relationships](https://laravel.com/docs/5.5/eloquent-relationships)\r\n\r\n[SQL Best Practices](https://github.com/michaljuhas/SQL-training-advanced/blob/master/S02-Coding-style/S02-L03-Best-practices.md)', NULL, 0, '2018-01-15 09:36:26', '2018-01-18 01:29:37'),
(4, 1, 'Guest Review Award 2017', 'Workplace: \r\n[Latest update](https://booking.facebook.com/groups/749688881836009/permalink/995134950624733/)\r\n\r\nGuru: \r\n[Summary of Guest Review Award](https://docs.google.com/spreadsheets/d/1224XI7lrj89atJVNug9dTS01fOYQDkHAYVX91DUSuV8/edit#gid=15)\r\n\r\nGuru: \r\n[Delivery Status](https://docs.google.com/spreadsheets/d/1HtVjeFZpJMftnX9sfQRJ-gJSWE5ju4UJZfaYsLMBrA0/edit#gid=1878467571)\r\n\r\nPartner Help: \r\n[When will I receive my Guest Review Award?](https://partnerhelp.booking.com/hc/en-us/articles/115000696305)', NULL, 0, '2018-01-15 09:47:24', '2018-01-18 01:50:50'),
(7, 1, 'Risk Free Reservations Opt-out Tool', '[Opt-out Tool: Replace Property ID](https://hoffice.booking.com/availadmin/fflex.html?hotel_id=304967)', NULL, 0, '2018-01-18 01:26:58', '2018-01-18 01:26:58'),
(5, 1, 'Stack Overflow Sample Page', NULL, 'https://stackoverflow.com/search?tab=relevance&q=cakephp%203%20session%20timeout', 0, '2018-01-18 01:00:55', '2018-01-18 01:29:53'),
(6, 1, 'Booking Quick Links', '[Booking.com Offices Worldwide](https://www.booking.com/content/offices.es.html?label=gen173nr-1FCAEoggJCAlhYSDNiBW5vcmVmcgV1c192YYgBAZgBCsIBA2FibsgBDNgBAegBAfgBC5ICAXmoAgQ;sid=3164d56c0c484443eb5cc481c6d10b36)\r\n\r\n[Booking.com - All Property Types](https://www.booking.com/accommodations.en-gb.html)\r\n\r\n[Channel Managers / XML Providers: full list](https://office.booking.com/xmladmin/provider/list.html)\r\n\r\n[Guest Profile Search](https://office.booking.com/users/index.html?ses=off;hotel_id=)', NULL, 0, '2018-01-18 01:26:05', '2018-01-18 01:26:05'),
(8, 1, 'Payments by Booking.com / PBB', '[Exclusive opt-out](https://globalguru.booking.com/hc/en-gb/articles/206822250-Exclusive-Opt-out-procedure-pay-out-?utf8=%E2%9C%93&query=payments+by+booking+opt-out&commit=search)', NULL, 0, '2018-01-18 01:27:17', '2018-01-18 01:27:17'),
(9, 1, 'Autoclosed (content)', '[Global Guru - Auto-closed Content](https://globalguru.booking.com/hc/en-gb/articles/206300664-AutoClosed-Content-Status)', NULL, 0, '2018-01-18 01:27:36', '2018-01-18 01:27:36'),
(10, 1, 'Booking Glossary - Property Types, Room Types, etc', NULL, 'https://docs.google.com/document/d/137t8jX9qpYo_iTg4Kfh-eWnxVvy76WDFkUK_URdzvmM/edit', 0, '2018-01-18 01:28:09', '2018-01-18 01:28:09'),
(11, 1, 'Change Property Name/Address', '[CSC Procedure](https://customerservice.booking.com/new-as-procedures/as-procedure-change-property-name/)', NULL, 0, '2018-01-18 01:28:29', '2018-01-18 01:28:29');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2017_12_28_191317_create_entries_tags_table', 1),
(4, '2017_12_28_191317_create_tags_table', 1),
(5, '2017_12_30_151557_create_tasks_table', 1),
(6, '2017_12_30_191318_create_entries_table', 1),
(9, '2018_01_02_150314_add_flags_to_entries_table', 2),
(13, '2018_01_08_132301_add_settings_to_users', 3),
(14, '2018_01_13_201852_create_faqs_table', 4),
(15, '2018_04_21_155143_add_map_link_to_entries_table', 5);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `user_id`, `name`, `created_at`, `updated_at`) VALUES
(1, 1, 'Channel Manager', '2018-01-13 04:51:50', '2018-01-13 04:51:50'),
(2, 1, 'Account', '2018-01-13 04:52:06', '2018-01-13 04:52:06'),
(3, 1, 'Bugs', '2018-01-13 04:52:19', '2018-01-13 04:52:19'),
(4, 1, 'Configuration', '2018-01-13 04:52:33', '2018-01-13 04:52:33'),
(5, 1, 'Content', '2018-01-13 04:52:41', '2018-01-13 04:52:41'),
(6, 1, 'Forward', '2018-01-13 04:52:46', '2018-01-13 04:52:46');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(10) UNSIGNED NOT NULL,
  `description` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` text COLLATE utf8mb4_unicode_ci,
  `user_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `description`, `link`, `user_id`, `created_at`, `updated_at`) VALUES
(6, 'Check this property for autogen', 'https://www.booking.com/hotel/cr/finca-don-juan.en-gb.html?label=gen173nr-1FCAEoggJCAlhYSDNYBHIFdXNfd2GIAQGYAS7CAQp3aW5kb3dzIDEwyAEM2AEB6AEB-AELkgIBeagCAw;sid=77b84a77e7de1f101c37b045b20d4a61;dest_id=-1107397;dest_type=city;dist=0;hapos=2;hpos=2;room1=A%2CA;sb_price_type=total;srepoch=1515685745;srfid=55eb0474b4c41575262a8ff0ee1dfbad0b10ea10X2;srpvid=4f416f37ef770ccb;type=total;ucfs=1&#hotelTmpl', 1, '2018-01-11 23:50:14', '2018-01-11 23:50:14'),
(7, 'Check Autogen>> Arenal Paraiso', 'https://www.booking.com/hotel/cr/arenal-paraiso-resort-amp-spa.en-gb.html?label=gen173nr-1FCAEoggJCAlhYSDNYBHIFdXNfd2GIAQGYAS7CAQp3aW5kb3dzIDEwyAEM2AEB6AEB-AELkgIBeagCAw;sid=aa35b7eaffb81a0693a99d17282cfab2;fs=2;shid=2883530&', 1, '2018-01-11 23:51:16', '2018-01-11 23:51:16');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `template_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `view_id` tinyint(4) NOT NULL DEFAULT '-1',
  `search_title_only_flag` tinyint(4) NOT NULL DEFAULT '-1',
  `search_whole_words_flag` tinyint(4) NOT NULL DEFAULT '-1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `remember_token`, `template_id`, `view_id`, `search_title_only_flag`, `search_whole_words_flag`, `created_at`, `updated_at`) VALUES
(1, 'Scott', 'scott@datalab1.com', '$2y$10$cOzpwmCcSbthkKaOv0gm4uNXfX8uZkvIxn7L4/7GG5gWxnWROJL9C', 'TRUTHGq33kz614T7WKLEBUY0CJdDZIBFPel98AbzqSiggTuywdjQ2qxYFQl6', 243, -1, -1, -1, '2018-01-02 04:47:08', '2018-01-21 00:28:55'),
(2, 'Tester', 'sbwilkinson@yahoo.com', '$2y$10$5H0RoVdAh/eHONrQbNOu2.aakPp11QJt6OOrWtI0SQX9SFB6Mx5Ty', 'Bm6J1joJ0d80RdGR1Qo93t5NO99GLsjyzfpONwvQ47fnT0vJdIkopBnyOcAo', 0, -1, -1, -1, '2018-01-19 00:57:37', '2018-01-19 00:57:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `entries`
--
ALTER TABLE `entries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `entries_tags`
--
ALTER TABLE `entries_tags`
  ADD UNIQUE KEY `entries_tags_entry_id_tag_id_unique` (`entry_id`,`tag_id`),
  ADD KEY `entries_tags_tag_id_foreign` (`tag_id`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tasks_user_id_index` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `entries`
--
ALTER TABLE `entries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
