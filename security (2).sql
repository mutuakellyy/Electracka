-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 31, 2025 at 10:21 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `security`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
CREATE TABLE IF NOT EXISTS `attendance` (
  `id` int NOT NULL AUTO_INCREMENT,
  `institution_id` int NOT NULL,
  `guard_id` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `present` tinyint(1) NOT NULL,
  `timein` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `timeout` timestamp NULL DEFAULT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `institution_id`, `guard_id`, `present`, `timein`, `timeout`, `date`) VALUES
(1, 1, '2', 0, '2025-07-30 12:09:48', NULL, '2025-07-30 00:00:00'),
(2, 1, '5', 1, '2025-07-30 12:11:11', NULL, '2025-07-30 00:00:00'),
(3, 1, '9', 1, '2025-07-30 12:11:17', NULL, '2025-07-30 00:00:00'),
(4, 1, '10', 1, '2025-07-30 13:07:41', NULL, '2025-07-30 00:00:00'),
(5, 1, '5', 1, '2025-07-31 08:16:03', NULL, '2025-07-31 00:00:00'),
(6, 1, '9', 1, '2025-07-31 08:16:07', NULL, '2025-07-31 00:00:00'),
(7, 1, '10', 1, '2025-07-31 08:16:09', NULL, '2025-07-31 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `guard_reports`
--

DROP TABLE IF EXISTS `guard_reports`;
CREATE TABLE IF NOT EXISTS `guard_reports` (
  `id` int NOT NULL AUTO_INCREMENT,
  `guard_email` varchar(255) DEFAULT NULL,
  `institution_id` int DEFAULT NULL,
  `location_id` int DEFAULT NULL,
  `details` text,
  `submitted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `guard_reports`
--

INSERT INTO `guard_reports` (`id`, `guard_email`, `institution_id`, `location_id`, `details`, `submitted_at`) VALUES
(1, 'joshiiikms@gmail.com', 1, 1, 'there has been a student nabbed with some narcotics', '2025-07-28 11:53:31'),
(2, 'joshiiikms@gmail.com', 1, 3, 'everything was well', '2025-07-28 16:37:50');

-- --------------------------------------------------------

--
-- Table structure for table `institution`
--

DROP TABLE IF EXISTS `institution`;
CREATE TABLE IF NOT EXISTS `institution` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `emailaddress` varchar(250) NOT NULL,
  `phonenumber` int NOT NULL,
  `location` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `emailaddress` (`emailaddress`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `institution`
--

INSERT INTO `institution` (`id`, `name`, `emailaddress`, `phonenumber`, `location`) VALUES
(1, 'Multimedia University', 'multimedia@gmail.com', 752365412, 'Nairobi'),
(2, 'Masaai Mara university', 'masaimara@gmail.com', 752365412, 'Narok'),
(3, 'University of Nairobi', 'uon@gmail.com', 774445236, 'Nairobi'),
(4, 'Mekaela Academies', 'mekaela@gmail.com', 121315468, 'Diani');

-- --------------------------------------------------------

--
-- Table structure for table `institution_locations`
--

DROP TABLE IF EXISTS `institution_locations`;
CREATE TABLE IF NOT EXISTS `institution_locations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `institution_id` int NOT NULL,
  `name` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `institution_locations`
--

INSERT INTO `institution_locations` (`id`, `institution_id`, `name`) VALUES
(1, 1, 'Main Gate'),
(2, 1, 'Administration block'),
(3, 1, 'Hostels'),
(4, 1, 'Club House'),
(5, 1, 'Stadium Gate'),
(6, 1, 'Dining Area'),
(7, 1, 'Hotel'),
(8, 2, 'Main Gate'),
(9, 2, 'Administration block'),
(10, 2, 'Hostels'),
(11, 2, 'Club House'),
(12, 2, 'Gate C'),
(13, 2, 'Dining Area'),
(14, 3, 'Main Gate'),
(15, 3, 'Hostels'),
(16, 3, 'Library'),
(17, 3, 'Kitchen Area'),
(18, 3, 'Playground'),
(19, 3, 'Administration Block'),
(20, 3, 'Main Parkins'),
(21, 4, 'Dining Room'),
(22, 1, 'sewage plant'),
(23, 1, 'lower prreferbs');

-- --------------------------------------------------------

--
-- Table structure for table `location_scans`
--

DROP TABLE IF EXISTS `location_scans`;
CREATE TABLE IF NOT EXISTS `location_scans` (
  `id` int NOT NULL AUTO_INCREMENT,
  `institution_id` int DEFAULT NULL,
  `location_id` int DEFAULT NULL,
  `location_name` varchar(100) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `scanned_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `emailaddress` varchar(250) NOT NULL,
  `review` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

DROP TABLE IF EXISTS `schedules`;
CREATE TABLE IF NOT EXISTS `schedules` (
  `id` int NOT NULL AUTO_INCREMENT,
  `guard_email` varchar(255) DEFAULT NULL,
  `institution_id` int DEFAULT NULL,
  `shift_start` datetime DEFAULT NULL,
  `shift_end` datetime DEFAULT NULL,
  `location_id` int DEFAULT NULL,
  `scan_interval` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `guard_email`, `institution_id`, `shift_start`, `shift_end`, `location_id`, `scan_interval`) VALUES
(1, 'joshiiikms@gmail.com', 1, '2025-07-30 17:00:00', '2025-07-31 06:00:00', 1, 180);

-- --------------------------------------------------------

--
-- Table structure for table `supervisor_report`
--

DROP TABLE IF EXISTS `supervisor_report`;
CREATE TABLE IF NOT EXISTS `supervisor_report` (
  `id` int NOT NULL AUTO_INCREMENT,
  `supervisor_email` varchar(250) NOT NULL,
  `institution_id` int NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `details` text,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `supervisor_report`
--

INSERT INTO `supervisor_report` (`id`, `supervisor_email`, `institution_id`, `title`, `details`, `date_created`) VALUES
(1, 'sterph@gmail.com', 1, 'Theft', 'dfdfdfdfdfdfhtf', '2025-07-30 17:25:21'),
(2, 'sterph@gmail.com', 1, 'manicure', 'a girl was caught carrying some suspicious manicure', '2025-07-31 11:35:45');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `firstname` varchar(15) NOT NULL,
  `surname` varchar(30) NOT NULL,
  `securitynumber` varchar(10) NOT NULL,
  `contact` int NOT NULL,
  `emailaddress` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `institution` int NOT NULL,
  `role` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `emailaddress` (`emailaddress`),
  UNIQUE KEY `contact` (`contact`),
  KEY `securitynumber` (`securitynumber`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `firstname`, `surname`, `securitynumber`, `contact`, `emailaddress`, `password`, `institution`, `role`, `created`, `active`) VALUES
(1, 'Belinda', 'Inesco', '', 752369854, 'belinda@gmail.com', 'vo..fwM3iOvz.', 1, 'manager', '2025-06-19 17:59:06', 1),
(2, 'Joshua', 'Maitha', '0001', 745395810, 'joshiiikms@gmail.com', 'vo..fwM3iOvz.', 1, 'guard', '2025-06-19 18:02:03', 1),
(3, 'Lamech', 'Mutua', '', 745396746, 'lamech@gmail.com', 'vo..fwM3iOvz.', 2, 'manager', '2025-07-09 09:15:12', 1),
(4, 'Kariuki', 'Steph', '', 745396576, 'sterph@gmail.com', 'vo..fwM3iOvz.', 1, 'supervisor', '2025-07-09 09:58:10', 1),
(5, 'Mutinda', 'Maitha', '75645', 723541269, 'mutindamaitha@gmail.com', 'vo..fwM3iOvz.', 1, 'guard', '2025-07-09 13:07:06', 1),
(6, 'Mutua', 'Mathew', '59865', 745213955, 'mut@gmair.com', 'vo..fwM3iOvz.', 1, 'guard', '2025-07-14 11:30:43', 0),
(7, 'Ahmed', 'Hussein', '', 112362545, 'ahmed@yahoo.com', 'vo..fwM3iOvz.', 3, 'supervisor', '2025-07-15 20:23:53', 1),
(8, 'Yusuf ', 'Suleiman', '76567', 756982354, 'yusuleiman@gmail.com', 'vo..fwM3iOvz.', 3, 'guard', '2025-07-15 20:23:53', 1),
(9, 'Monica', 'Athuman', '36521', 112365892, 'monica@yahoo.com', 'vo..fwM3iOvz.', 1, 'guard', '2025-07-15 20:23:53', 1),
(10, 'Anthony', 'Maria', '85689', 756235784, 'maria@gmail.com', 'vo..fwM3iOvz.', 1, 'guard', '2025-07-15 20:23:53', 1),
(11, 'Abednego', 'Yassin', '', 755323651, 'yassin@gmail.com', 'vo..fwM3iOvz.', 3, 'manager', '2025-07-15 21:17:40', 1),
(12, 'Mavoko', 'Yonah', '', 755366651, 'mavoka@gmail.com', 'vo..fwM3iOvz.', 2, 'supervisor', '2025-07-15 21:17:40', 1),
(13, 'Abel', 'Yakuza', '55332', 758623651, 'yakuzan@gmail.com', 'vo..fwM3iOvz.', 2, 'guard', '2025-07-15 21:17:40', 1),
(14, 'Ishmael', 'Malombe', '55472', 758623555, 'ishmael@gmail.com', 'vo..fwM3iOvz.', 2, 'guard', '2025-07-15 21:17:40', 1),
(15, 'Hamza', 'Hamisi', '33124', 115323651, 'hamza@gmail.com', 'vo..fwM3iOvz.', 2, 'guard', '2025-07-15 21:27:09', 1),
(16, 'Binti', 'Hamisi', '33624', 115327751, 'binti@gmail.com', 'vo..fwM3iOvz.', 2, 'guard', '2025-07-15 21:27:09', 1),
(17, 'Hassan', 'Ali', '35624', 116753651, 'hassan@gmail.com', 'vo..fwM3iOvz.', 2, 'guard', '2025-07-15 21:27:09', 1),
(18, 'Halima', 'Mihsi', '36745', 765743651, 'mishi@gmail.com', 'vo..fwM3iOvz.', 3, 'guard', '2025-07-15 21:27:09', 1),
(19, 'Michelle', 'Kombo', '76454', 746666551, 'michelle@gmail.com', 'vo..fwM3iOvz.', 3, 'guard', '2025-07-15 21:27:09', 1),
(20, 'Juma', 'Abubakar', '65987', 115596655, 'abubakar@gmail.com', 'vo..fwM3iOvz.', 3, 'guard', '2025-07-17 09:42:41', 1),
(21, 'Mwanasiti', 'Juma', '123654', 112544872, 'mwanasiti@gmail.com', 'vo..fwM3iOvz.', 3, 'guard', '2025-07-23 10:35:36', 1),
(22, 'Mwanasiti', 'Mwajuma', '123654', 789564456, 'mwajuma@gmail.com', 'vo..fwM3iOvz.', 4, 'manager', '2025-07-23 10:51:18', 1),
(23, 'Mwijaku', 'Limisula', '123654', 112546979, 'mwilisua@gmail.com', 'vo..fwM3iOvz.', 4, 'supervisor', '2025-07-23 10:54:28', 1),
(24, 'Mwakulosa', 'Armathea', '764445', 751123555, 'mwarmathea@yahoo.com', 'vo..fwM3iOvz.', 4, 'guard', '2025-07-29 09:29:42', 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
