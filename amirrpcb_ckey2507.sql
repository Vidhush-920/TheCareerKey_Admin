-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 05, 2026 at 03:23 PM
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
-- Database: `amirrpcb_ckey2507`
--

-- --------------------------------------------------------

--
-- Table structure for table `ckey_results`
--

DROP TABLE IF EXISTS `ckey_results`;
CREATE TABLE IF NOT EXISTS `ckey_results` (
  `rec_id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nic` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `log_no` int NOT NULL DEFAULT '1',
  `score_r` int NOT NULL,
  `score_i` int NOT NULL,
  `score_a` int NOT NULL,
  `score_s` int NOT NULL,
  `score_e` int NOT NULL,
  `score_c` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`nic`,`log_no`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



-- --------------------------------------------------------

--
-- Table structure for table `ckey_roles`
--

DROP TABLE IF EXISTS `ckey_roles`;
CREATE TABLE IF NOT EXISTS `ckey_roles` (
  `role_id` int NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_description` text COLLATE utf8mb4_unicode_ci,
  `crud_staff` tinyint(1) NOT NULL DEFAULT 0,
  `crud_admins` tinyint(1) NOT NULL DEFAULT 0,
  `update_role` tinyint(1) NOT NULL DEFAULT 0,
  `view_results` tinyint(1) NOT NULL DEFAULT 1,
  `crud_results` tinyint(1) NOT NULL DEFAULT 0,
  `role_status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ckey_roles`
--

INSERT INTO `ckey_roles` (`role_id`, `role_name`, `role_description`, `crud_staff`, `crud_admins`, `update_role`, `view_results`, `crud_results`, `role_status`) VALUES
(1, 'superadmin', 'Full access to all features and settings', 1, 1, 1, 1, 1, 'active'),
(2, 'admin', 'Manage staff, view results, and add notes, but cannot manage superadmins', 1, 0, 0, 1, 1, 'active'),
(3, 'staff', 'View results only', 0, 0, 0, 1, 0, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `ckey_staffs`
--

DROP TABLE IF EXISTS `ckey_staffs`;
CREATE TABLE IF NOT EXISTS `ckey_staffs` (
  `staff_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fname` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lname` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nic` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  PRIMARY KEY (`staff_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `nic` (`nic`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ckey_staffs`
--

INSERT INTO `ckey_staffs` (`staff_id`, `username`, `fname`, `lname`, `nic`, `email`, `phone`, `role`, `password_hash`, `status`) VALUES
(1, 'sauser0152', 'SuperAd', 'User', '209912345679', 'sauser01@vcot.com', '', 'superadmin', '$2y$10$54VQ7lAuVaBkzvobLp0Ox.rtWWM7LmiMfjc9fi7Hb6HfF50iLFPQO', 'active'),
(2, 'aduser003', 'Admin', 'User', '209643218745', 'aduser03@vcot.com', '', 'admin', '$2y$10$rgE9iTVEO9JVz8kazNUkkeMPp3Fpx9BLcXV9fLg7zseldSbiYh0iO', 'active'),
(3, 'staffuser678', 'Staff', 'User', '209214235867', 'staffuser7@vcot.com', '', 'staff', '$2y$10$VFHgJdR/v1fRrIfdRPt1rO4iPPnIjuKyEMRioD8pt0mVxlRffCWx6', 'active');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
