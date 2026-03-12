-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 11, 2026 at 02:41 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pediatric_clinic`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `details`, `ip_address`, `user_agent`, `timestamp`) VALUES
(1, 2, 'User registered', NULL, NULL, NULL, '2025-10-23 06:22:20'),
(2, 2, 'LOGIN', NULL, '::1', NULL, '2025-10-23 06:22:49'),
(3, 3, 'User registered', NULL, NULL, NULL, '2025-10-23 09:59:39'),
(4, 3, 'LOGIN', NULL, '::1', NULL, '2025-10-23 09:59:44'),
(5, 5, 'LOGIN', NULL, '::1', NULL, '2025-10-23 10:34:51'),
(6, 6, 'LOGIN', NULL, '::1', NULL, '2025-10-23 11:00:17'),
(7, 10, 'PATIENT_REGISTERED', 'Registered new patient: Emma Smith', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2025-10-23 11:36:59'),
(8, 7, 'MEDICAL_RECORD_CREATED', 'Created medical record for Emma Smith', '192.168.1.50', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', '2025-10-23 11:36:59'),
(9, 11, 'APPOINTMENT_BOOKED', 'Booked appointment for Sophia Johnson', '192.168.1.101', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15', '2025-10-23 11:36:59'),
(10, 3, 'LOGIN', NULL, '::1', NULL, '2025-10-28 13:12:39'),
(11, 5, 'LOGIN', NULL, '::1', NULL, '2025-10-29 14:09:32'),
(12, 5, 'LOGIN', NULL, '::1', NULL, '2025-10-29 14:29:39'),
(13, 5, 'LOGIN', NULL, '::1', NULL, '2025-10-29 14:30:12'),
(14, 3, 'LOGIN', NULL, '::1', NULL, '2025-10-29 14:39:18'),
(15, 5, 'LOGIN', NULL, '::1', NULL, '2025-10-29 14:52:30'),
(16, 6, 'LOGIN', NULL, '::1', NULL, '2025-10-30 14:31:03'),
(17, 6, 'LOGIN', NULL, '::1', NULL, '2025-11-02 08:27:59'),
(18, 5, 'LOGIN', NULL, '::1', NULL, '2025-11-02 08:28:22'),
(19, 6, 'LOGIN', NULL, '::1', NULL, '2025-11-02 12:39:59'),
(20, 6, 'LOGIN', NULL, '::1', NULL, '2025-11-02 13:09:18'),
(21, 5, 'LOGIN', NULL, '::1', NULL, '2025-11-06 01:44:17'),
(22, 6, 'LOGIN', NULL, '::1', NULL, '2025-11-06 01:46:27'),
(23, 5, 'LOGIN', NULL, '::1', NULL, '2025-11-06 01:50:50'),
(24, 6, 'LOGIN', NULL, '::1', NULL, '2025-11-06 03:26:40'),
(25, 3, 'LOGIN', NULL, '::1', NULL, '2025-11-06 06:53:38'),
(26, 5, 'LOGIN', NULL, '::1', NULL, '2025-11-06 06:54:23'),
(27, 6, 'LOGIN', NULL, '::1', NULL, '2025-11-06 06:59:06'),
(28, 6, 'LOGIN', NULL, '::1', NULL, '2025-11-06 12:26:06'),
(29, 6, 'LOGIN', NULL, '::1', NULL, '2025-11-06 13:10:19'),
(30, 3, 'LOGIN', NULL, '::1', NULL, '2025-11-15 16:04:27'),
(31, 6, 'LOGIN', NULL, '::1', NULL, '2025-11-15 16:20:07'),
(32, 6, 'LOGIN', NULL, '::1', NULL, '2025-11-16 01:17:48'),
(33, 5, 'LOGIN', NULL, '::1', NULL, '2025-11-17 14:55:05'),
(34, 5, 'LOGIN', NULL, '::1', NULL, '2025-11-23 05:18:20'),
(35, 6, 'LOGIN', NULL, '::1', NULL, '2025-11-23 05:19:58'),
(36, 3, 'LOGIN', NULL, '::1', NULL, '2025-11-23 05:20:52'),
(37, 3, 'LOGIN', NULL, '::1', NULL, '2025-11-23 06:24:12'),
(38, 3, 'LOGIN', NULL, '::1', NULL, '2025-11-24 10:43:48'),
(39, 6, 'LOGIN', NULL, '::1', NULL, '2025-12-05 23:16:45'),
(40, 6, 'LOGIN', NULL, '::1', NULL, '2025-12-06 00:52:57'),
(41, 5, 'LOGIN', NULL, '::1', NULL, '2025-12-06 00:53:25'),
(42, 3, 'LOGIN', NULL, '::1', NULL, '2025-12-06 01:00:13'),
(43, 3, 'LOGIN', NULL, '::1', NULL, '2026-01-16 08:11:46'),
(44, 3, 'LOGIN', NULL, '::1', NULL, '2026-01-18 11:05:19'),
(45, 3, 'LOGIN', NULL, '::1', NULL, '2026-01-19 01:56:15'),
(46, 3, 'LOGIN', NULL, '::1', NULL, '2026-01-19 02:04:04'),
(47, 3, 'LOGIN', NULL, '::1', NULL, '2026-01-19 02:14:55'),
(48, 3, 'LOGIN', NULL, '::1', NULL, '2026-01-19 02:15:26'),
(49, 7, 'APPOINTMENT_COMPLETED', 'Completed appointment for Sophia Geremillo', '192.168.1.55', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2023-10-25 02:30:00'),
(50, 8, 'MEDICATION_PRESCRIBED', 'Prescribed hydrocortisone cream for Lucas Geremillo', '192.168.1.56', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', '2023-10-26 01:50:00'),
(51, 6, 'VACCINE_ADMINISTERED', 'Administered MMR vaccine to Mia Tolosa', '192.168.1.57', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2023-10-27 03:20:00'),
(52, 3, 'PATIENT_PROFILE_UPDATED', 'Updated medical history for Sophia Geremillo', '192.168.1.100', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15', '2023-10-28 06:20:00'),
(53, 2, 'APPOINTMENT_BOOKED', 'Booked vaccination appointment for Noah Tolosa', '192.168.1.101', 'Mozilla/5.0 (Android 13; Mobile) AppleWebKit/537.36', '2023-10-29 01:15:00'),
(54, 10, 'MEDICAL_RECORD_VIEWED', 'Viewed medical records for Emma Smith', '192.168.1.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2023-10-30 08:30:00'),
(55, 7, 'PRESCRIPTION_PRINTED', 'Printed prescription for asthma medication', '192.168.1.55', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2023-10-31 03:45:00'),
(56, 9, 'MEDICAL_CERTIFICATE_ISSUED', 'Issued medical certificate for school enrollment', '192.168.1.58', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', '2023-11-01 07:20:00'),
(57, 3, 'LOGIN', NULL, '::1', NULL, '2026-01-19 03:38:25'),
(58, 3, 'LOGIN', NULL, '::1', NULL, '2026-01-19 03:51:07'),
(59, 5, 'LOGIN', NULL, '::1', NULL, '2026-01-19 03:53:04'),
(60, 6, 'LOGIN', NULL, '::1', NULL, '2026-01-19 03:54:00'),
(61, 3, 'LOGIN', NULL, '::1', NULL, '2026-01-21 00:47:44'),
(62, 5, 'LOGIN', NULL, '::1', NULL, '2026-01-21 01:02:42'),
(63, 5, 'LOGIN', NULL, '::1', NULL, '2026-01-21 01:45:18'),
(64, 6, 'LOGIN', NULL, '::1', NULL, '2026-01-21 01:46:07'),
(65, 3, 'LOGIN', NULL, '::1', NULL, '2026-01-21 01:59:05'),
(66, 6, 'LOGIN', NULL, '::1', NULL, '2026-01-21 02:33:49'),
(67, 6, 'LOGIN', NULL, '::1', NULL, '2026-01-21 03:39:03'),
(68, 5, 'LOGIN', NULL, '::1', NULL, '2026-01-21 13:04:37'),
(69, 6, 'LOGIN', NULL, '::1', NULL, '2026-01-21 13:09:31'),
(70, 6, 'LOGIN', NULL, '::1', NULL, '2026-01-22 12:49:37'),
(71, 3, 'LOGIN', NULL, '::1', NULL, '2026-01-22 13:58:52'),
(72, 3, 'LOGOUT', 'User logged out', '::1', NULL, '2026-01-22 13:58:54'),
(73, 5, 'LOGIN', NULL, '::1', NULL, '2026-01-22 13:59:01'),
(74, 5, 'LOGOUT', 'User logged out', '::1', NULL, '2026-01-22 14:10:38'),
(75, 6, 'LOGIN', NULL, '::1', NULL, '2026-01-22 14:11:19'),
(76, 6, 'LOGIN', NULL, '::1', NULL, '2026-01-22 14:46:01'),
(77, 3, 'LOGIN', NULL, '::1', NULL, '2026-01-23 06:16:27'),
(78, 3, 'LOGOUT', 'User logged out', '::1', NULL, '2026-01-23 06:19:26'),
(79, 6, 'LOGIN', NULL, '::1', NULL, '2026-01-23 06:19:32'),
(80, 6, 'LOGOUT', 'User logged out', '::1', NULL, '2026-01-23 06:19:51'),
(81, 3, 'LOGIN', NULL, '::1', NULL, '2026-01-23 06:20:20'),
(82, 6, 'LOGIN', NULL, '::1', NULL, '2026-01-24 02:15:47'),
(83, 6, 'LOGOUT', 'User logged out', '::1', NULL, '2026-01-24 02:16:40'),
(84, 5, 'LOGIN', NULL, '::1', NULL, '2026-01-24 02:16:45'),
(85, 5, 'LOGOUT', 'User logged out', '::1', NULL, '2026-01-24 02:17:46'),
(86, 3, 'LOGIN', NULL, '::1', NULL, '2026-01-24 02:17:57'),
(87, 3, 'LOGOUT', 'User logged out', '::1', NULL, '2026-01-24 02:25:37'),
(88, 3, 'LOGIN', NULL, '::1', NULL, '2026-01-25 11:07:45'),
(89, 3, 'LOGIN', NULL, '::1', NULL, '2026-01-25 12:42:34'),
(90, 3, 'LOGIN', NULL, '127.0.0.1', NULL, '2026-01-27 01:53:11'),
(91, 3, 'LOGIN', NULL, '::1', NULL, '2026-01-27 02:13:24'),
(92, 3, 'LOGOUT', 'User logged out', '::1', NULL, '2026-01-27 02:34:40'),
(93, 6, 'LOGIN', NULL, '::1', NULL, '2026-01-27 02:34:44'),
(94, 6, 'LOGOUT', 'User logged out', '::1', NULL, '2026-01-27 02:35:15'),
(95, 3, 'LOGIN', NULL, '::1', NULL, '2026-01-27 02:35:19'),
(96, 3, 'LOGOUT', 'User logged out', '::1', NULL, '2026-01-27 02:47:15'),
(97, 6, 'LOGIN', NULL, '::1', NULL, '2026-01-27 02:47:21'),
(98, 6, 'LOGOUT', 'User logged out', '::1', NULL, '2026-01-27 03:09:54'),
(99, 3, 'LOGIN', NULL, '::1', NULL, '2026-01-27 03:10:00'),
(100, 3, 'LOGOUT', 'User logged out', '::1', NULL, '2026-01-27 03:33:02'),
(101, 6, 'LOGIN', NULL, '::1', NULL, '2026-01-27 03:33:08'),
(102, 6, 'LOGOUT', 'User logged out', '::1', NULL, '2026-01-27 03:33:46'),
(103, 3, 'LOGIN', NULL, '::1', NULL, '2026-01-27 03:33:50'),
(104, 3, 'LOGOUT', 'User logged out', '::1', NULL, '2026-01-27 03:34:13'),
(105, 6, 'LOGIN', NULL, '::1', NULL, '2026-01-27 03:47:22'),
(106, 6, 'LOGOUT', 'User logged out', '::1', NULL, '2026-01-27 04:02:25'),
(107, 3, 'LOGIN', NULL, '::1', NULL, '2026-01-27 04:02:29'),
(108, 3, 'LOGIN', NULL, '::1', NULL, '2026-01-28 13:59:09'),
(109, 3, 'LOGIN', NULL, '::1', NULL, '2026-01-28 14:00:39'),
(110, 3, 'LOGOUT', 'User logged out', '::1', NULL, '2026-01-28 15:36:45'),
(111, 6, 'LOGIN', NULL, '::1', NULL, '2026-01-28 15:37:01'),
(112, 6, 'LOGOUT', 'User logged out', '::1', NULL, '2026-01-28 15:38:17'),
(113, 3, 'LOGIN', NULL, '::1', NULL, '2026-01-28 15:38:22'),
(114, 3, 'LOGOUT', 'User logged out', '::1', NULL, '2026-01-28 15:38:43'),
(115, 6, 'LOGIN', NULL, '::1', NULL, '2026-01-28 15:38:47'),
(116, 6, 'LOGOUT', 'User logged out', '::1', NULL, '2026-01-28 15:42:19'),
(117, 6, 'LOGIN', NULL, '::1', NULL, '2026-01-28 16:00:52'),
(118, 3, 'LOGIN', NULL, '::1', NULL, '2026-01-28 16:01:02'),
(119, 3, 'LOGOUT', 'User logged out', '::1', NULL, '2026-01-28 16:01:06'),
(120, 3, 'LOGIN', NULL, '::1', NULL, '2026-01-28 16:01:10'),
(121, 3, 'LOGOUT', 'User logged out', '::1', NULL, '2026-01-28 16:01:21'),
(122, 3, 'LOGIN', NULL, '::1', NULL, '2026-01-28 16:01:23'),
(123, 3, 'LOGIN', NULL, '::1', NULL, '2026-01-29 17:11:13'),
(124, 3, 'LOGOUT', 'User logged out', '::1', NULL, '2026-01-29 19:37:23'),
(125, 3, 'LOGIN', NULL, '::1', NULL, '2026-01-29 19:37:26'),
(126, 3, 'LOGIN', NULL, '::1', NULL, '2026-01-30 05:47:13'),
(127, 3, 'LOGIN', NULL, '::1', NULL, '2026-01-30 05:56:23'),
(128, 5, 'LOGIN', NULL, '::1', NULL, '2026-02-04 07:09:14'),
(129, 5, 'LOGOUT', 'User logged out', '::1', NULL, '2026-02-04 07:11:56'),
(130, 6, 'LOGIN', NULL, '::1', NULL, '2026-02-04 07:12:02'),
(131, 6, 'LOGOUT', 'User logged out', '::1', NULL, '2026-02-04 08:42:20'),
(132, 3, 'LOGIN', NULL, '::1', NULL, '2026-02-04 08:44:10'),
(133, 3, 'LOGOUT', 'User logged out', '::1', NULL, '2026-02-04 08:45:14'),
(134, 5, 'LOGIN', NULL, '::1', NULL, '2026-02-04 08:53:23'),
(135, 5, 'LOGOUT', 'User logged out', '::1', NULL, '2026-02-04 09:02:31'),
(136, 3, 'LOGIN', NULL, '::1', NULL, '2026-02-04 09:09:05'),
(137, 3, 'LOGIN', NULL, '::1', NULL, '2026-02-04 09:28:04'),
(138, 3, 'LOGOUT', 'User logged out', '::1', NULL, '2026-02-04 10:36:48'),
(139, 6, 'LOGIN', NULL, '::1', NULL, '2026-02-04 10:36:53'),
(140, 3, 'LOGIN', NULL, '::1', NULL, '2026-02-04 10:37:35'),
(141, 3, 'LOGIN', NULL, '::1', NULL, '2026-02-04 10:51:14'),
(142, 3, 'LOGIN', NULL, '::1', NULL, '2026-02-04 10:58:31'),
(143, 3, 'LOGOUT', 'User logged out', '::1', NULL, '2026-02-04 11:27:17'),
(144, 6, 'LOGIN', NULL, '::1', NULL, '2026-02-04 11:27:22'),
(145, 6, 'LOGIN', NULL, '::1', NULL, '2026-02-24 02:35:33'),
(146, 6, 'LOGIN', NULL, '::1', NULL, '2026-02-24 02:35:56'),
(147, 6, 'LOGIN', NULL, '::1', NULL, '2026-02-24 02:36:05'),
(148, 6, 'LOGOUT', 'User logged out', '::1', NULL, '2026-02-24 02:36:07'),
(149, 6, 'LOGIN', NULL, '::1', NULL, '2026-02-24 02:36:17'),
(150, 6, 'LOGOUT', 'User logged out', '::1', NULL, '2026-02-24 02:36:18'),
(151, 6, 'LOGIN', NULL, '::1', NULL, '2026-02-24 04:27:19'),
(152, 6, 'LOGOUT', 'User logged out', '::1', NULL, '2026-02-24 04:28:44'),
(153, 3, 'LOGIN', NULL, '::1', NULL, '2026-03-11 01:28:04'),
(154, 3, 'LOGOUT', 'User logged out', '::1', NULL, '2026-03-11 01:28:23'),
(155, 6, 'LOGIN', NULL, '::1', NULL, '2026-03-11 01:28:27'),
(156, 6, 'LOGOUT', 'User logged out', '::1', NULL, '2026-03-11 01:28:57'),
(157, 6, 'LOGIN', NULL, '::1', NULL, '2026-03-11 01:29:05'),
(158, 6, 'LOGOUT', 'User logged out', '::1', NULL, '2026-03-11 01:33:42'),
(159, 5, 'LOGIN', NULL, '::1', NULL, '2026-03-11 01:33:48');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `category` varchar(100) NOT NULL,
  `author` varchar(100) NOT NULL,
  `date_posted` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `content`, `category`, `author`, `date_posted`, `is_active`) VALUES
(1, 'System Maintenance Scheduled', 'Our system will undergo maintenance on Saturday from 2:00 AM to 6:00 AM. Services may be temporarily unavailable.', 'Maintenance', 'IT Department', '2023-06-10 10:00:00', 1),
(2, 'New Feature Release', 'We\'re excited to announce the release of our new dashboard feature with improved analytics.', 'Update', 'Product Team', '2023-06-08 09:30:00', 1),
(3, 'Company Event Next Month', 'Join us for our annual company picnic on July 22nd at Riverside Park.', 'Event', 'HR Department', '2023-06-05 14:15:00', 1),
(4, 'Security Update Required', 'All employees must update their passwords by the end of this week.', 'Security', 'Security Team', '2023-06-03 11:45:00', 1),
(5, 'Quarterly Results Published', 'We\'re pleased to announce a 15% growth in revenue compared to last year.', 'Finance', 'Finance Department', '2023-05-30 16:20:00', 1),
(6, 'New Clinic Hours Starting Next Month', 'Beginning November 1st, our clinic hours will be extended. Weekdays: 8AM-6PM, Saturdays: 9AM-2PM. Emergency services remain available 24/7.', 'UPDATE', 'Clinic Management', '2023-10-15 09:00:00', 1),
(7, 'Free Vaccination Week', 'Join us for Free Vaccination Week from October 23-27, 2023. All routine childhood vaccines will be provided at no cost. Appointments required.', 'EVENT', 'Public Health Department', '2023-10-10 14:30:00', 1),
(8, 'Electronic Health Records System Update', 'Our EHR system will be upgraded on October 20th from 10PM-2AM. Some features may be temporarily unavailable. We apologize for any inconvenience.', 'MAINTENANCE', 'IT Department', '2023-10-05 16:45:00', 1),
(9, 'New Pediatric Specialist Joining Clinic', 'We are pleased to announce that Dr. Amanda Rodriguez, a pediatric cardiology specialist, will be joining our team starting November 1st.', 'UPDATE', 'Medical Director', '2023-09-28 11:20:00', 1),
(10, 'Holiday Schedule Announcement', 'The clinic will be closed on December 25th and January 1st for the holidays. Emergency services will be available through our on-call system.', 'EVENT', 'Clinic Administration', '2023-09-20 10:15:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `type` enum('CONSULTATION','VACCINATION','CHECKUP','FOLLOW_UP','OTHER') NOT NULL,
  `status` enum('SCHEDULED','CONFIRMED','IN_PROGRESS','COMPLETED','CANCELLED','NO_SHOW') DEFAULT 'SCHEDULED',
  `reason` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `duration` int(11) DEFAULT 30,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `doctor_id`, `appointment_date`, `appointment_time`, `type`, `status`, `reason`, `notes`, `duration`, `created_by`, `created_at`, `updated_at`) VALUES
(9, 144, 8, '2025-10-29', '12:39:00', 'CONSULTATION', 'SCHEDULED', '', NULL, 30, NULL, '2025-10-28 13:36:04', '2025-10-28 13:36:04'),
(10, 144, 6, '2025-11-05', '12:58:00', 'VACCINATION', 'CONFIRMED', '', NULL, 30, NULL, '2025-10-28 13:55:24', '2025-11-06 07:00:20'),
(11, 145, 7, '2023-10-25', '10:00:00', 'CONSULTATION', 'COMPLETED', 'Annual checkup and asthma review', 'Patient doing well, asthma under control', 30, 7, '2023-10-20 01:00:00', '2023-10-25 02:30:00'),
(12, 145, 7, '2023-11-15', '14:30:00', 'FOLLOW_UP', 'SCHEDULED', 'Follow-up for asthma management', 'Bring inhaler and peak flow meter', 30, 7, '2023-10-25 02:35:00', '2023-10-25 02:35:00'),
(13, 146, 8, '2023-10-26', '09:15:00', 'CONSULTATION', 'COMPLETED', 'Eczema flare-up', 'Prescribed topical steroid cream', 30, 8, '2023-10-22 07:20:00', '2023-10-26 01:45:00'),
(14, 147, 6, '2023-10-27', '11:00:00', 'VACCINATION', 'COMPLETED', 'MMR booster shot', 'Vaccine administered, no adverse reactions', 15, 6, '2023-10-23 00:30:00', '2023-10-27 03:15:00'),
(15, 148, 9, '2023-10-28', '13:45:00', 'CHECKUP', 'CONFIRMED', '18-month well baby checkup', 'Regular developmental assessment', 30, 9, '2023-10-24 02:15:00', '2023-10-24 02:15:00'),
(16, 149, 7, '2023-10-30', '15:30:00', 'CONSULTATION', 'SCHEDULED', 'ADHD medication review', 'Bring current medication and school reports', 45, 7, '2023-10-25 06:20:00', '2023-10-25 06:20:00'),
(17, 150, 8, '2023-10-31', '10:45:00', 'FOLLOW_UP', 'CONFIRMED', 'Post-premature follow-up', 'Monitor growth and development', 30, 8, '2023-10-26 01:50:00', '2023-10-26 01:50:00'),
(18, 151, 9, '2023-11-01', '14:00:00', 'CONSULTATION', 'SCHEDULED', 'Diabetes management review', 'Check blood glucose logs', 45, 9, '2023-10-27 03:20:00', '2023-10-27 03:20:00'),
(19, 152, 6, '2023-11-02', '16:15:00', 'VACCINATION', 'CONFIRMED', 'DTaP and Polio vaccines', 'Routine immunization schedule', 20, 6, '2023-10-28 05:50:00', '2026-01-19 03:54:07'),
(20, 153, 7, '2023-11-03', '09:30:00', 'CHECKUP', 'SCHEDULED', 'Annual physical exam', 'Include asthma and allergy review', 30, 7, '2023-10-29 08:40:00', '2023-10-29 08:40:00'),
(21, 145, 6, '2026-01-29', '08:51:00', 'VACCINATION', 'COMPLETED', '', NULL, 30, NULL, '2026-01-21 00:48:26', '2026-01-21 02:52:02'),
(22, 145, 6, '2026-02-04', '10:00:00', 'CONSULTATION', 'CONFIRMED', '', NULL, 30, NULL, '2026-01-28 15:28:15', '2026-02-04 08:12:25'),
(23, 144, 7, '2026-03-11', '11:30:00', 'CONSULTATION', 'SCHEDULED', '', NULL, 30, NULL, '2026-01-28 15:36:33', '2026-01-28 15:36:33'),
(24, 146, 7, '2026-01-30', '11:30:00', 'CONSULTATION', 'SCHEDULED', '', NULL, 30, NULL, '2026-01-30 08:40:57', '2026-01-30 08:40:57');

-- --------------------------------------------------------

--
-- Table structure for table `clinic_settings`
--

CREATE TABLE `clinic_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('STRING','INTEGER','BOOLEAN','JSON') DEFAULT 'STRING',
  `description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clinic_settings`
--

INSERT INTO `clinic_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `updated_at`) VALUES
(1, 'clinic_name', 'PediCare Clinic', 'STRING', 'Name of the clinic', '2025-10-23 06:17:16'),
(2, 'clinic_phone', '0931 709 3056', 'STRING', 'Clinic contact number', '2026-01-21 13:05:05'),
(3, 'clinic_email', 'info@pedicare.com', 'STRING', 'Clinic email address', '2025-10-23 06:17:16'),
(4, 'clinic_address', '123 Healthcare Ave, Medical City, MC 12345', 'STRING', 'Clinic physical address', '2025-10-23 06:17:16'),
(5, 'business_hours', '{\"monday\": {\"open\": \"08:00\", \"close\": \"17:00\"}, \"tuesday\": {\"open\": \"08:00\", \"close\": \"17:00\"}, \"wednesday\": {\"open\": \"08:00\", \"close\": \"17:00\"}, \"thursday\": {\"open\": \"08:00\", \"close\": \"17:00\"}, \"friday\": {\"open\": \"08:00\", \"close\": \"17:00\"}, \"saturday\": {\"open\": \"09:00\", \"close\": \"13:00\"}, \"sunday\": {\"open\": null, \"close\": null}}', 'JSON', 'Clinic operating hours', '2025-10-23 06:17:16'),
(6, 'appointment_reminder_hours', '24', 'INTEGER', 'Hours before appointment to send reminder', '2025-10-23 06:17:16'),
(7, 'clinic_logo', '/assets/images/clinic-logo.png', 'STRING', 'Path to clinic logo for printing', '2025-11-06 12:24:04'),
(8, 'doctor_signature_path', '/assets/signatures/', 'STRING', 'Path to doctor signature images', '2025-11-06 12:24:04'),
(9, 'prescription_prefix', 'RX', 'STRING', 'Prefix for prescription numbers', '2025-11-06 12:24:04'),
(10, 'certificate_prefix', 'MC', 'STRING', 'Prefix for medical certificate numbers', '2025-11-06 12:24:04'),
(11, 'print_header', 'PediCare Clinic - Quality Pediatric Care', 'STRING', 'Header text for printed documents', '2025-11-06 12:24:04'),
(12, 'print_footer', 'This document is computer-generated and requires official signature', 'STRING', 'Footer text for printed documents', '2025-11-06 12:24:04');

-- --------------------------------------------------------

--
-- Table structure for table `consultation_notes`
--

CREATE TABLE `consultation_notes` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `diagnosis` text DEFAULT NULL,
  `notes` text NOT NULL,
  `treatment_plan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `developmental_milestones`
--

CREATE TABLE `developmental_milestones` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `milestone_type` enum('MOTOR','LANGUAGE','SOCIAL','COGNITIVE') NOT NULL,
  `milestone_description` varchar(255) NOT NULL,
  `achieved_date` date NOT NULL,
  `expected_age_months` int(11) NOT NULL,
  `achieved_age_months` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `recorded_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `doctor_availability`
--

CREATE TABLE `doctor_availability` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `availability_type` enum('AVAILABLE','UNAVAILABLE') DEFAULT 'AVAILABLE',
  `reason` varchar(255) DEFAULT NULL,
  `is_all_day` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor_availability`
--

INSERT INTO `doctor_availability` (`id`, `doctor_id`, `date`, `start_time`, `end_time`, `created_at`, `updated_at`, `availability_type`, `reason`, `is_all_day`) VALUES
(1, 6, '2026-01-25', '00:00:00', '00:00:00', '2026-01-25 11:31:40', '2026-01-25 11:31:40', 'UNAVAILABLE', 'Conference', 1),
(2, 7, '2026-02-14', '00:00:00', '00:00:00', '2026-01-25 11:31:40', '2026-01-25 11:31:40', 'UNAVAILABLE', 'Vacation', 1),
(3, 8, '2026-01-30', '00:00:00', '00:00:00', '2026-01-25 11:31:40', '2026-01-25 11:31:40', 'UNAVAILABLE', 'Training', 1),
(4, 6, '2026-02-25', '08:00:00', '22:35:00', '2026-01-27 02:35:13', '2026-01-27 02:35:13', 'AVAILABLE', NULL, 0),
(5, 6, '2026-02-04', '00:33:00', '12:33:00', '2026-01-27 03:33:41', '2026-01-27 03:33:41', 'AVAILABLE', NULL, 0),
(6, 6, '2026-02-03', '00:00:00', '00:00:00', '2026-01-27 04:02:15', '2026-01-27 04:02:15', 'UNAVAILABLE', 'Training', 1);

-- --------------------------------------------------------

--
-- Table structure for table `doctor_schedules`
--

CREATE TABLE `doctor_schedules` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `day_of_week` enum('MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `slot_duration` int(11) DEFAULT 30,
  `max_patients` int(11) DEFAULT 10,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor_schedules`
--

INSERT INTO `doctor_schedules` (`id`, `doctor_id`, `day_of_week`, `start_time`, `end_time`, `slot_duration`, `max_patients`, `active`, `created_at`) VALUES
(1, 7, 'MONDAY', '09:00:00', '17:00:00', 30, 15, 1, '2025-10-23 11:36:59'),
(2, 7, 'WEDNESDAY', '09:00:00', '17:00:00', 30, 15, 1, '2025-10-23 11:36:59'),
(3, 7, 'FRIDAY', '09:00:00', '17:00:00', 30, 15, 1, '2025-10-23 11:36:59'),
(4, 8, 'TUESDAY', '08:00:00', '16:00:00', 30, 12, 1, '2025-10-23 11:36:59'),
(5, 8, 'THURSDAY', '08:00:00', '16:00:00', 30, 12, 1, '2025-10-23 11:36:59'),
(6, 9, 'MONDAY', '10:00:00', '18:00:00', 45, 10, 1, '2025-10-23 11:36:59'),
(7, 9, 'WEDNESDAY', '10:00:00', '18:00:00', 45, 10, 1, '2025-10-23 11:36:59');

-- --------------------------------------------------------

--
-- Table structure for table `growth_records`
--

CREATE TABLE `growth_records` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `record_date` date NOT NULL,
  `height` decimal(5,2) NOT NULL,
  `weight` decimal(5,2) NOT NULL,
  `head_circumference` decimal(4,2) DEFAULT NULL,
  `bmi` decimal(4,2) DEFAULT NULL,
  `height_percentile` decimal(5,2) DEFAULT NULL,
  `weight_percentile` decimal(5,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `recorded_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medical_certificates`
--

CREATE TABLE `medical_certificates` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `certificate_date` date NOT NULL,
  `certificate_number` varchar(50) DEFAULT NULL,
  `purpose` enum('SCHOOL','SPORTS','TRAVEL','WORK','OTHER') NOT NULL DEFAULT 'SCHOOL',
  `diagnosis` text DEFAULT NULL,
  `recommendations` text DEFAULT NULL,
  `valid_from` date NOT NULL,
  `valid_until` date NOT NULL,
  `clinic_stamp` varchar(255) DEFAULT NULL,
  `doctor_signature` varchar(255) DEFAULT NULL,
  `is_printed` tinyint(1) DEFAULT 0,
  `printed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medical_records`
--

CREATE TABLE `medical_records` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `record_date` date NOT NULL,
  `record_type` enum('CONSULTATION','CHECKUP','FOLLOW_UP','EMERGENCY','OTHER') NOT NULL,
  `diagnosis` text DEFAULT NULL,
  `symptoms` text DEFAULT NULL,
  `temperature` decimal(4,2) DEFAULT NULL,
  `blood_pressure` varchar(20) DEFAULT NULL,
  `heart_rate` int(11) DEFAULT NULL,
  `respiratory_rate` int(11) DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `treatment_plan` text DEFAULT NULL,
  `prescriptions` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `follow_up_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medical_records`
--

INSERT INTO `medical_records` (`id`, `patient_id`, `doctor_id`, `record_date`, `record_type`, `diagnosis`, `symptoms`, `temperature`, `blood_pressure`, `heart_rate`, `respiratory_rate`, `height`, `weight`, `treatment_plan`, `prescriptions`, `notes`, `follow_up_date`, `created_at`) VALUES
(1, 145, 7, '2023-10-25', 'CONSULTATION', 'Asthma, well controlled', 'Mild wheezing, no shortness of breath', 36.80, '110/70', 85, 20, 95.50, 14.20, 'Continue current inhaler regimen, monitor peak flow', 'Albuterol inhaler PRN', 'Patient doing well, asthma under control. No changes needed to current treatment.', '2023-11-15', '2023-10-25 02:30:00'),
(2, 146, 8, '2023-10-26', 'CONSULTATION', 'Atopic dermatitis (eczema)', 'Dry, itchy patches on arms and legs', 37.10, '115/75', 92, 22, 78.30, 10.50, 'Topical steroids, moisturizer regimen', 'Hydrocortisone 1% cream apply BID x7 days', 'Eczema flare-up likely due to weather changes. Advised to use fragrance-free products.', '2023-11-09', '2023-10-26 01:45:00'),
(3, 147, 6, '2023-10-27', '', 'Routine immunization', 'No current symptoms', 36.90, '108/68', 88, 18, 102.30, 16.80, 'MMR booster vaccine administered', NULL, 'Vaccine administered in left arm. No immediate adverse reactions observed.', NULL, '2023-10-27 03:15:00'),
(4, 149, 7, '2023-09-15', 'CONSULTATION', 'ADHD, combined type', 'Difficulty focusing, hyperactivity at school', 37.00, '112/74', 90, 19, 115.20, 20.10, 'Behavioral therapy + medication management', 'Methylphenidate 10mg BID', 'Parent reports improvement in school performance with current dosage.', '2023-10-30', '2023-09-15 06:30:00'),
(5, 151, 9, '2023-09-28', 'CONSULTATION', 'Type 1 Diabetes Mellitus', 'Blood glucose fluctuations', 36.70, '118/76', 95, 20, 120.50, 22.40, 'Insulin adjustment, carb counting education', 'Insulin glargine 15 units HS, insulin lispro 1:10 carb ratio', 'HbA1c improved from 7.8% to 7.2%. Continue current regimen.', '2023-11-01', '2023-09-28 07:45:00');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `type` enum('APPOINTMENT','VACCINATION','SYSTEM','REMINDER') DEFAULT 'SYSTEM',
  `related_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `related_id`, `is_read`, `created_at`) VALUES
(1, 10, 'Appointment Reminder', 'Reminder: Emma Smith has an appointment on October 25, 2025 at 10:00 AM with Dr. Chen', 'APPOINTMENT', 1, 0, '2025-10-23 11:36:59'),
(2, 10, 'Vaccination Due', 'Noah Smith is due for MMR booster shot on October 26, 2025', 'VACCINATION', 2, 0, '2025-10-23 11:36:59'),
(3, 11, 'Appointment Confirmation', 'Sophia Johnson\'s appointment with Dr. Kim is confirmed for October 27, 2025 at 2:00 PM', 'APPOINTMENT', 3, 1, '2025-10-23 11:36:59'),
(4, 12, 'Follow-up Required', 'Liam Davis has a follow-up appointment scheduled for October 28, 2025', 'REMINDER', 4, 0, '2025-10-23 11:36:59'),
(5, 3, 'Appointment Reminder', 'Reminder: Sophia Geremillo has an appointment tomorrow at 10:00 AM with Dr. Chen', 'APPOINTMENT', 11, 1, '2023-10-24 01:00:00'),
(6, 3, 'Appointment Confirmation', 'Lucas Geremillo\'s appointment with Dr. Garcia has been confirmed for October 26 at 9:15 AM', 'APPOINTMENT', 13, 1, '2023-10-22 07:25:00'),
(7, 2, 'Vaccination Due', 'Mia Tolosa is due for Hepatitis A vaccine on November 15, 2023', 'VACCINATION', NULL, 0, '2023-10-25 06:30:00'),
(8, 10, 'Lab Results Available', 'Emma Smith\'s blood test results are now available for review', 'SYSTEM', NULL, 0, '2023-10-28 03:20:00'),
(9, 11, 'Medication Refill', 'Sophia Johnson\'s insulin prescription is due for refill', 'REMINDER', NULL, 0, '2023-10-29 08:45:00'),
(10, 12, 'Follow-up Required', 'Ava Davis has a follow-up appointment scheduled for November 3rd', 'REMINDER', 20, 0, '2023-10-30 02:15:00'),
(11, 3, 'New Message', 'You have a new message from Dr. Chen regarding Sophia\'s treatment plan', 'SYSTEM', NULL, 0, '2023-11-01 00:30:00'),
(12, 6, 'Schedule Change', 'Your appointment schedule has been updated for tomorrow', 'SYSTEM', NULL, 1, '2023-11-02 09:20:00');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `date_of_birth` date NOT NULL,
  `gender` enum('MALE','FEMALE','OTHER') NOT NULL,
  `blood_type` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `medical_conditions` text DEFAULT NULL,
  `special_notes` text DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `parent_id`, `first_name`, `last_name`, `date_of_birth`, `gender`, `blood_type`, `height`, `weight`, `allergies`, `medical_conditions`, `special_notes`, `profile_picture`, `created_at`, `updated_at`) VALUES
(144, 3, 'TEST', 'TEST', '2025-10-28', 'OTHER', 'O-', 0.30, 0.30, 'Nuts', 'SDA', 'SADAS', NULL, '2025-10-28 13:35:45', '2026-01-21 01:01:28'),
(145, 3, 'Sophia', 'Geremillo', '2020-03-15', 'FEMALE', 'A+', 95.50, 14.20, 'Penicillin, Peanuts', 'Asthma (mild)', 'Uses inhaler as needed, allergic to peanuts - carry EpiPen', NULL, '2023-06-10 00:30:00', '2023-10-20 01:15:00'),
(146, 3, 'Lucas', 'Geremillo', '2022-07-22', 'MALE', 'O+', 78.30, 10.50, 'None known', 'Eczema', 'Uses special moisturizer for eczema', NULL, '2023-06-10 00:30:00', '2023-10-20 01:15:00'),
(147, 2, 'Mia', 'Tolosa', '2019-05-18', 'FEMALE', 'B+', 102.30, 16.80, 'Dairy, Eggs', 'None', 'Lactose intolerant - dairy-free diet required', NULL, '2023-05-22 02:45:00', '2023-10-18 06:30:00'),
(148, 2, 'Noah', 'Tolosa', '2021-09-30', 'MALE', 'AB+', 85.60, 12.30, 'Bee stings', 'None', 'Carry EpiPen for bee sting allergy', NULL, '2023-05-22 02:45:00', '2023-10-18 06:30:00'),
(149, 10, 'Emma', 'Smith', '2018-11-05', 'FEMALE', 'A-', 115.20, 20.10, 'Sulfa drugs', 'ADHD', 'On medication, requires regular follow-up', NULL, '2023-04-15 01:20:00', '2023-10-22 03:00:00'),
(150, 10, 'Oliver', 'Smith', '2023-01-12', 'MALE', 'A-', 62.50, 7.80, 'None', 'None', 'Premature birth at 35 weeks', NULL, '2023-04-15 01:20:00', '2023-10-22 03:00:00'),
(151, 11, 'Sophia', 'Johnson', '2017-08-23', 'FEMALE', 'O+', 120.50, 22.40, 'Latex', 'Type 1 Diabetes', 'Requires insulin injections 3x daily', NULL, '2023-03-10 06:15:00', '2023-10-21 08:45:00'),
(152, 11, 'Liam', 'Johnson', '2020-12-08', 'MALE', 'O+', 92.30, 14.90, 'None', 'None', 'Regular checkups for developmental milestones', NULL, '2023-03-10 06:15:00', '2023-10-21 08:45:00'),
(153, 12, 'Ava', 'Davis', '2019-02-14', 'FEMALE', 'B-', 105.80, 18.20, 'Shellfish, Dust mites', 'Asthma', 'Uses inhaler daily, allergy to shellfish', NULL, '2023-02-28 03:30:00', '2023-10-19 02:20:00'),
(154, 12, 'Ethan', 'Davis', '2022-04-30', 'MALE', 'B-', 76.40, 11.10, 'None', 'None', 'Healthy, regular vaccinations up to date', NULL, '2023-02-28 03:30:00', '2023-10-19 02:20:00');

-- --------------------------------------------------------

--
-- Table structure for table `patient_files`
--

CREATE TABLE `patient_files` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `original_filename` varchar(255) NOT NULL,
  `stored_filename` varchar(255) NOT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_files`
--

INSERT INTO `patient_files` (`id`, `patient_id`, `uploaded_by`, `original_filename`, `stored_filename`, `mime_type`, `file_size`, `notes`, `created_at`) VALUES
(1, 144, 3, '620552369_738109055768856_5558849461148084878_n.jpg', 'ee7fd89c3690549335b0170e8beaf480.jpg', 'image/jpeg', 270878, '', '2026-02-04 09:37:48');

-- --------------------------------------------------------

--
-- Table structure for table `patient_vaccine_needs`
--

CREATE TABLE `patient_vaccine_needs` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `vaccine_name` varchar(255) NOT NULL,
  `recommended_date` date DEFAULT NULL,
  `status` enum('RECOMMENDED','SCHEDULED','GIVEN','NOT_NEEDED') DEFAULT 'RECOMMENDED',
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_vaccine_needs`
--

INSERT INTO `patient_vaccine_needs` (`id`, `patient_id`, `vaccine_name`, `recommended_date`, `status`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 152, 'IPV', '2026-02-26', 'RECOMMENDED', '0', 6, '2026-02-04 08:28:56', NULL),
(2, 144, 'MMR', '2026-02-25', 'RECOMMENDED', '0', 6, '2026-02-04 10:37:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `id` int(11) NOT NULL,
  `prescription_number` varchar(50) DEFAULT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `prescription_date` date NOT NULL DEFAULT curdate(),
  `medication_name` varchar(255) NOT NULL,
  `dosage` varchar(100) NOT NULL,
  `frequency` varchar(100) NOT NULL,
  `duration` varchar(100) NOT NULL,
  `refills_allowed` int(11) DEFAULT 0,
  `refill_instructions` text DEFAULT NULL,
  `pharmacy_notes` text DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `clinic_address` text DEFAULT NULL,
  `doctor_signature` varchar(255) DEFAULT NULL,
  `is_printed` tinyint(1) DEFAULT 0,
  `printed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescriptions`
--

INSERT INTO `prescriptions` (`id`, `prescription_number`, `patient_id`, `doctor_id`, `prescription_date`, `medication_name`, `dosage`, `frequency`, `duration`, `refills_allowed`, `refill_instructions`, `pharmacy_notes`, `instructions`, `clinic_address`, `doctor_signature`, `is_printed`, `printed_at`, `created_at`) VALUES
(1, 'RX2023102501', 145, 7, '2023-10-25', 'Albuterol Inhaler', '90 mcg', '2 puffs every 4-6 hours as needed', '1 year', 5, 'Refill as needed for asthma symptoms', 'Dispense with spacer device', 'Use at first sign of wheezing or shortness of breath. Rinse mouth after use.', '123 Healthcare Ave, Medical City, MC 12345', 'digital_signature_dr_chen.png', 1, '2023-10-25 02:40:00', '2023-10-25 02:35:00'),
(2, 'RX2023102601', 146, 8, '2023-10-26', 'Hydrocortisone Cream 1%', 'Apply thin layer', 'Twice daily', '7 days', 2, 'May refill once if needed', 'For external use only', 'Apply to affected areas only. Avoid face and genital areas. Discontinue if irritation occurs.', '123 Healthcare Ave, Medical City, MC 12345', 'digital_signature_dr_garcia.png', 1, '2023-10-26 01:50:00', '2023-10-26 01:46:00'),
(3, 'RX2023091501', 149, 7, '2023-09-15', 'Methylphenidate ER', '10 mg', 'Once daily in morning', '30 days', 5, 'Monthly refills with follow-up appointments', 'C-II controlled substance', 'Take with breakfast. May cause decreased appetite. Avoid in evening.', '123 Healthcare Ave, Medical City, MC 12345', 'digital_signature_dr_chen.png', 1, '2023-09-15 07:00:00', '2023-09-15 06:40:00'),
(4, 'RX2023092801', 151, 9, '2023-09-28', 'Insulin Glargine', '15 units', 'Once daily at bedtime', '30 days', 5, 'Monthly refills with glucose log review', 'Keep refrigerated', 'Inject subcutaneously in abdomen. Rotate injection sites. Monitor blood glucose regularly.', '123 Healthcare Ave, Medical City, MC 12345', 'digital_signature_dr_kim.png', 1, '2023-09-28 08:00:00', '2023-09-28 07:50:00'),
(5, 'RX2023102502', 145, 7, '2023-10-25', 'Fluticasone Inhaler', '110 mcg', '2 puffs daily', '90 days', 3, 'Refill every 3 months', 'Maintenance inhaler for asthma', 'Use daily for asthma control, even when feeling well. Rinse mouth after use.', '123 Healthcare Ave, Medical City, MC 12345', 'digital_signature_dr_chen.png', 0, NULL, '2023-10-25 02:38:00'),
(6, NULL, 147, 6, '2026-01-22', 'adsa', 'sad', 'dsa', 'das', 0, NULL, NULL, 'das', NULL, NULL, 0, NULL, '2026-01-22 13:38:17');

-- --------------------------------------------------------

--
-- Table structure for table `print_templates`
--

CREATE TABLE `print_templates` (
  `id` int(11) NOT NULL,
  `template_name` varchar(100) NOT NULL,
  `template_type` enum('PRESCRIPTION','MEDICAL_CERTIFICATE','LAB_REPORT','OTHER') NOT NULL,
  `template_content` text NOT NULL,
  `header_html` text DEFAULT NULL,
  `footer_html` text DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `print_templates`
--

INSERT INTO `print_templates` (`id`, `template_name`, `template_type`, `template_content`, `header_html`, `footer_html`, `is_default`, `active`, `created_at`) VALUES
(1, 'Default Prescription', 'PRESCRIPTION', '<div class=\"prescription\">\n    <h2>PRESCRIPTION</h2>\n    <p><strong>Patient:</strong> {{patient_name}}</p>\n    <p><strong>Date:</strong> {{prescription_date}}</p>\n    <hr>\n    <p><strong>Medication:</strong> {{medication_name}}</p>\n    <p><strong>Dosage:</strong> {{dosage}}</p>\n    <p><strong>Frequency:</strong> {{frequency}}</p>\n    <p><strong>Duration:</strong> {{duration}}</p>\n    <p><strong>Instructions:</strong> {{instructions}}</p>\n    <hr>\n    <p><strong>Doctor:</strong> {{doctor_name}}</p>\n    <p><strong>License:</strong> {{license_number}}</p>\n</div>', '<div class=\"header\"><h1>{{clinic_name}}</h1><p>{{clinic_address}}</p></div>', '<div class=\"footer\"><p>This is a computer-generated document</p></div>', 1, 1, '2025-11-06 12:23:54'),
(2, 'Default Medical Certificate', 'MEDICAL_CERTIFICATE', '<div class=\"certificate\">\n    <h2>MEDICAL CERTIFICATE</h2>\n    <p>This is to certify that <strong>{{patient_name}}</strong> was examined on <strong>{{certificate_date}}</strong>.</p>\n    <p><strong>Diagnosis:</strong> {{diagnosis}}</p>\n    <p><strong>Recommendations:</strong> {{recommendations}}</p>\n    <p>This certificate is valid from <strong>{{valid_from}}</strong> to <strong>{{valid_until}}</strong>.</p>\n    <br><br>\n    <p>_________________________</p>\n    <p><strong>{{doctor_name}}</strong></p>\n    <p>{{doctor_qualifications}}</p>\n</div>', '<div class=\"header\"><h1>{{clinic_name}}</h1><p>Medical Certificate</p></div>', '<div class=\"footer\"><p>Certificate #: {{certificate_number}}</p></div>', 1, 1, '2025-11-06 12:23:54');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `duration` int(11) DEFAULT 30,
  `cost` decimal(10,2) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `description`, `duration`, `cost`, `active`, `created_at`) VALUES
(1, 'Consultation', 'Comprehensive health assessment and medical consultation', 30, 50.00, 1, '2025-10-23 06:17:16'),
(2, 'Vaccination', 'Immunization services with proper documentation', 15, 25.00, 1, '2025-10-23 06:17:16'),
(3, 'Well Baby Checkup', 'Regular developmental assessment and health monitoring', 30, 40.00, 1, '2025-10-23 06:17:16'),
(4, 'Medical Certificate & Clearance', 'Official medical clearance certificate for school, sports, and activities', 20, 30.00, 1, '2025-10-23 06:17:16'),
(5, 'Referral Services', 'Coordinated care with specialists', 15, 20.00, 1, '2025-10-23 06:17:16'),
(6, 'Ear Piercing', 'Safe and professional ear piercing service', 30, 35.00, 1, '2025-10-23 06:17:16'),
(7, 'Medical Certificate', 'Official medical certificate for school, sports, or work', 15, 25.00, 1, '2025-11-06 12:23:44'),
(8, 'Prescription Service', 'Medication prescription and management', 10, 15.00, 1, '2025-11-06 12:23:44');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('PARENT','DOCTOR','DOCTOR_OWNER','ADMIN') NOT NULL DEFAULT 'PARENT',
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('MALE','FEMALE','OTHER') DEFAULT NULL,
  `address` text DEFAULT NULL,
  `emergency_contact_name` varchar(100) DEFAULT NULL,
  `emergency_contact_phone` varchar(20) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `specialization` varchar(100) DEFAULT NULL,
  `license_number` varchar(50) DEFAULT NULL,
  `years_of_experience` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `phone`, `password`, `user_type`, `status`, `date_of_birth`, `gender`, `address`, `emergency_contact_name`, `emergency_contact_phone`, `profile_picture`, `specialization`, `license_number`, `years_of_experience`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'User', 'admin@pedicare.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ADMIN', 'active', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-23 06:17:16', '2025-10-23 06:17:16'),
(2, 'jansen', 'tolosa', 'jstolosa@gmail.com', '09661907753', '$2y$10$b0UhKehEg9hwl14XaSPhhOnK62tcZleKbtuMrPTEteFnA/DIyKiCq', 'PARENT', 'active', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-23 06:22:20', '2025-10-23 06:22:20'),
(3, 'Nowel', 'GEREMILLO', 'nowelgeremillo94@gmail.com', '+1 (202) 555-0143', '$2y$10$vse1uWHWtIyit7UJJ20CUePFCfYrDgzoK3j/KXTg9FMcq0rbAVMdi', 'PARENT', 'active', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-23 09:59:39', '2026-01-21 00:59:36'),
(5, 'Admin', 'User', 'admin2@pedicare.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ADMIN', 'active', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-23 10:34:00', '2025-10-23 10:34:00'),
(6, 'Sarah', 'Johnson', 'dr.sarah@pedicare.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'DOCTOR', 'active', NULL, NULL, NULL, NULL, NULL, NULL, 'Pediatrician', NULL, 15, '2025-10-23 11:00:03', '2025-10-23 11:00:03'),
(7, 'Michael', 'Chen', 'dr.chen@pedicare.com', '+1 (555) 234-5678', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'DOCTOR', 'active', NULL, NULL, NULL, NULL, NULL, NULL, 'Pediatric Cardiology', 'MED123456', 12, '2025-10-23 11:36:58', '2025-10-23 11:36:58'),
(8, 'Maria', 'Garcia', 'dr.garcia@pedicare.com', '+1 (555) 345-6789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'DOCTOR', 'active', NULL, NULL, NULL, NULL, NULL, NULL, 'General Pediatrics', 'MED234567', 8, '2025-10-23 11:36:58', '2025-10-23 11:36:58'),
(9, 'David', 'Kim', 'dr.kim@pedicare.com', '+1 (555) 456-7890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'DOCTOR_OWNER', 'active', NULL, NULL, NULL, NULL, NULL, NULL, 'Pediatric Neurology', 'MED345678', 20, '2025-10-23 11:36:58', '2025-10-23 11:36:58'),
(10, 'Jennifer', 'Smith', 'jennifer.smith@email.com', '+1 (555) 111-2222', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'PARENT', 'active', NULL, NULL, '123 Oak Street, Springfield, SP 12345', 'John Smith', '+1 (555) 111-3333', NULL, NULL, NULL, NULL, '2025-10-23 11:36:59', '2025-10-23 11:36:59'),
(11, 'Robert', 'Johnson', 'robert.johnson@email.com', '+1 (555) 222-3333', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'PARENT', 'active', NULL, NULL, '456 Maple Avenue, Springfield, SP 12346', 'Lisa Johnson', '+1 (555) 222-4444', NULL, NULL, NULL, NULL, '2025-10-23 11:36:59', '2025-10-23 11:36:59'),
(12, 'Emily', 'Davis', 'emily.davis@email.com', '+1 (555) 333-4444', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'PARENT', 'active', NULL, NULL, '789 Pine Road, Springfield, SP 12347', 'Mike Davis', '+1 (555) 333-5555', NULL, NULL, NULL, NULL, '2025-10-23 11:36:59', '2025-10-23 11:36:59');

-- --------------------------------------------------------

--
-- Table structure for table `vaccination_records`
--

CREATE TABLE `vaccination_records` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `vaccine_name` varchar(100) NOT NULL,
  `vaccine_type` enum('ROUTINE','OPTIONAL','SPECIAL') DEFAULT 'ROUTINE',
  `dose_number` int(11) DEFAULT NULL,
  `total_doses` int(11) DEFAULT NULL,
  `administration_date` date NOT NULL,
  `next_due_date` date DEFAULT NULL,
  `administered_by` int(11) DEFAULT NULL,
  `lot_number` varchar(50) DEFAULT NULL,
  `manufacturer` varchar(100) DEFAULT NULL,
  `site` enum('LEFT_ARM','RIGHT_ARM','LEFT_THIGH','RIGHT_THIGH','ORAL') DEFAULT 'LEFT_ARM',
  `notes` text DEFAULT NULL,
  `status` enum('COMPLETED','SCHEDULED','MISSED','OVERDUE') DEFAULT 'COMPLETED',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vaccination_records`
--

INSERT INTO `vaccination_records` (`id`, `patient_id`, `vaccine_name`, `vaccine_type`, `dose_number`, `total_doses`, `administration_date`, `next_due_date`, `administered_by`, `lot_number`, `manufacturer`, `site`, `notes`, `status`, `created_at`) VALUES
(1, 145, 'MMR', 'ROUTINE', 2, 2, '2023-10-27', '2033-10-27', 6, 'MMR202310A', 'Merck', 'LEFT_ARM', 'Booster dose administered', 'COMPLETED', '2023-10-27 03:15:00'),
(2, 145, 'Varicella', 'ROUTINE', 2, 2, '2023-04-15', NULL, 7, 'VAR202304B', 'GlaxoSmithKline', 'RIGHT_ARM', 'Second dose completed', 'COMPLETED', '2023-04-15 02:30:00'),
(3, 146, 'DTaP', 'ROUTINE', 4, 5, '2023-09-10', '2026-09-10', 8, 'DTAP202309C', 'Sanofi', 'LEFT_THIGH', 'Fourth dose administered', 'COMPLETED', '2023-09-10 06:20:00'),
(4, 147, 'Hepatitis A', 'ROUTINE', 1, 2, '2023-03-22', '2023-09-22', 6, 'HEPA202303D', 'GlaxoSmithKline', 'RIGHT_ARM', 'First dose administered', 'COMPLETED', '2023-03-22 01:45:00'),
(5, 148, 'PCV13', 'ROUTINE', 3, 4, '2023-08-05', '2024-02-05', 9, 'PCV202308E', 'Pfizer', 'LEFT_THIGH', 'Pneumococcal vaccine', 'COMPLETED', '2023-08-05 03:30:00'),
(6, 149, 'Tdap', 'ROUTINE', 1, 1, '2023-08-15', '2033-08-15', 7, 'TDAP202308F', 'Sanofi', 'LEFT_ARM', 'Tetanus booster', 'COMPLETED', '2023-08-15 08:15:00'),
(7, 150, 'Hepatitis B', 'ROUTINE', 3, 3, '2023-07-20', NULL, 8, 'HEPB202307G', 'Merck', 'RIGHT_THIGH', 'Birth dose series completed', 'COMPLETED', '2023-07-20 02:00:00'),
(8, 151, 'HPV', 'ROUTINE', 1, 2, '2023-06-10', '2024-06-10', 9, 'HPV202306H', 'Merck', 'LEFT_ARM', 'First dose of HPV series', 'COMPLETED', '2023-06-10 05:45:00'),
(9, 152, 'IPV', 'ROUTINE', 3, 4, '2023-05-18', '2024-05-18', 6, 'IPV202305I', 'Sanofi', 'RIGHT_THIGH', 'Third polio vaccine', 'COMPLETED', '2023-05-18 07:30:00'),
(10, 153, 'Influenza', 'ROUTINE', 1, 1, '2023-10-10', '2024-10-10', 7, 'FLU202310J', 'Seqirus', 'LEFT_ARM', 'Annual flu vaccine', 'COMPLETED', '2023-10-10 01:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `vaccines`
--

CREATE TABLE `vaccines` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `disease_protected` varchar(255) DEFAULT NULL,
  `recommended_age_months` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vaccines`
--

INSERT INTO `vaccines` (`id`, `name`, `disease_protected`, `recommended_age_months`, `created_at`) VALUES
(1, 'BCG', 'Tuberculosis', 0, '2025-11-06 05:43:46'),
(2, 'Hepatitis B', 'Hepatitis B', 0, '2025-11-06 05:43:46'),
(3, 'DTaP', 'Diphtheria, Tetanus, Pertussis', 2, '2025-11-06 05:43:46'),
(4, 'IPV', 'Polio', 2, '2025-11-06 05:43:46'),
(5, 'Hib', 'Haemophilus influenzae type b', 2, '2025-11-06 05:43:46'),
(6, 'PCV', 'Pneumococcal disease', 2, '2025-11-06 05:43:46'),
(7, 'Rotavirus', 'Rotavirus', 2, '2025-11-06 05:43:46'),
(8, 'MMR', 'Measles, Mumps, Rubella', 12, '2025-11-06 05:43:46'),
(9, 'Varicella', 'Chickenpox', 12, '2025-11-06 05:43:46'),
(10, 'Hepatitis A', 'Hepatitis A', 12, '2025-11-06 05:43:46'),
(11, 'BCG', 'Tuberculosis', 0, '2025-11-06 05:59:16'),
(12, 'Hepatitis B', 'Hepatitis B', 0, '2025-11-06 05:59:16'),
(13, 'DTaP', 'Diphtheria, Tetanus, Pertussis', 2, '2025-11-06 05:59:16'),
(14, 'IPV', 'Polio', 2, '2025-11-06 05:59:16'),
(15, 'Hib', 'Haemophilus influenzae type b', 2, '2025-11-06 05:59:16'),
(16, 'PCV', 'Pneumococcal disease', 2, '2025-11-06 05:59:16'),
(17, 'Rotavirus', 'Rotavirus', 2, '2025-11-06 05:59:16'),
(18, 'MMR', 'Measles, Mumps, Rubella', 12, '2025-11-06 05:59:16'),
(19, 'Varicella', 'Chickenpox', 12, '2025-11-06 05:59:16'),
(20, 'Hepatitis A', 'Hepatitis A', 12, '2025-11-06 05:59:16');

-- --------------------------------------------------------

--
-- Table structure for table `vaccine_schedule`
--

CREATE TABLE `vaccine_schedule` (
  `id` int(11) NOT NULL,
  `vaccine_name` varchar(100) NOT NULL,
  `recommended_age_months` int(11) NOT NULL,
  `dose_number` int(11) NOT NULL,
  `total_doses` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `is_mandatory` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vaccine_schedule`
--

INSERT INTO `vaccine_schedule` (`id`, `vaccine_name`, `recommended_age_months`, `dose_number`, `total_doses`, `description`, `is_mandatory`, `created_at`) VALUES
(1, 'BCG', 0, 1, 1, 'Bacillus Calmette-Guérin vaccine for tuberculosis', 1, '2026-01-19 03:00:27'),
(2, 'Hepatitis B', 0, 1, 3, 'First dose of Hepatitis B vaccine', 1, '2026-01-19 03:00:27'),
(3, 'DPT', 2, 1, 5, 'Diphtheria, Pertussis, Tetanus - first dose', 1, '2026-01-19 03:00:27'),
(4, 'Polio', 2, 1, 4, 'Inactivated polio vaccine - first dose', 1, '2026-01-19 03:00:27'),
(5, 'Hib', 2, 1, 4, 'Haemophilus influenzae type b - first dose', 1, '2026-01-19 03:00:27'),
(6, 'PCV', 2, 1, 4, 'Pneumococcal conjugate vaccine - first dose', 1, '2026-01-19 03:00:27'),
(7, 'Rotavirus', 2, 1, 3, 'Rotavirus vaccine - first dose', 1, '2026-01-19 03:00:27'),
(8, 'DPT', 4, 2, 5, 'Diphtheria, Pertussis, Tetanus - second dose', 1, '2026-01-19 03:00:27'),
(9, 'Polio', 4, 2, 4, 'Inactivated polio vaccine - second dose', 1, '2026-01-19 03:00:27'),
(10, 'Hib', 4, 2, 4, 'Haemophilus influenzae type b - second dose', 1, '2026-01-19 03:00:27'),
(11, 'PCV', 4, 2, 4, 'Pneumococcal conjugate vaccine - second dose', 1, '2026-01-19 03:00:27'),
(12, 'Rotavirus', 4, 2, 3, 'Rotavirus vaccine - second dose', 1, '2026-01-19 03:00:27'),
(13, 'DPT', 6, 3, 5, 'Diphtheria, Pertussis, Tetanus - third dose', 1, '2026-01-19 03:00:27'),
(14, 'Polio', 6, 3, 4, 'Inactivated polio vaccine - third dose', 1, '2026-01-19 03:00:27'),
(15, 'Hib', 6, 3, 4, 'Haemophilus influenzae type b - third dose', 1, '2026-01-19 03:00:27'),
(16, 'PCV', 6, 3, 4, 'Pneumococcal conjugate vaccine - third dose', 1, '2026-01-19 03:00:27'),
(17, 'Rotavirus', 6, 3, 3, 'Rotavirus vaccine - third dose', 1, '2026-01-19 03:00:27'),
(18, 'MMR', 12, 1, 2, 'Measles, Mumps, Rubella - first dose', 1, '2026-01-19 03:00:27'),
(19, 'Varicella', 12, 1, 2, 'Chickenpox vaccine - first dose', 1, '2026-01-19 03:00:27'),
(20, 'Hepatitis A', 12, 1, 2, 'Hepatitis A vaccine - first dose', 1, '2026-01-19 03:00:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_activity_logs_user_id` (`user_id`),
  ADD KEY `idx_activity_logs_timestamp` (`timestamp`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_appointments_patient_id` (`patient_id`),
  ADD KEY `idx_appointments_doctor_id` (`doctor_id`),
  ADD KEY `idx_appointments_date_time` (`appointment_date`,`appointment_time`);

--
-- Indexes for table `clinic_settings`
--
ALTER TABLE `clinic_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `consultation_notes`
--
ALTER TABLE `consultation_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `developmental_milestones`
--
ALTER TABLE `developmental_milestones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `milestone_type` (`milestone_type`),
  ADD KEY `developmental_milestones_ibfk_2` (`recorded_by`);

--
-- Indexes for table `doctor_availability`
--
ALTER TABLE `doctor_availability`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `doctor_schedules`
--
ALTER TABLE `doctor_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `growth_records`
--
ALTER TABLE `growth_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `record_date` (`record_date`),
  ADD KEY `growth_records_ibfk_2` (`recorded_by`);

--
-- Indexes for table `medical_certificates`
--
ALTER TABLE `medical_certificates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `certificate_number` (`certificate_number`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `idx_medical_records_patient_id` (`patient_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notifications_user_id` (`user_id`),
  ADD KEY `idx_notifications_is_read` (`is_read`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_patients_parent_id` (`parent_id`);

--
-- Indexes for table `patient_files`
--
ALTER TABLE `patient_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_patient_files_patient_id` (`patient_id`),
  ADD KEY `idx_patient_files_uploaded_by` (`uploaded_by`);

--
-- Indexes for table `patient_vaccine_needs`
--
ALTER TABLE `patient_vaccine_needs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_patient_vaccine_patient_id` (`patient_id`),
  ADD KEY `idx_patient_vaccine_created_by` (`created_by`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_prescription_number` (`prescription_number`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `print_templates`
--
ALTER TABLE `print_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_email` (`email`),
  ADD KEY `idx_users_user_type` (`user_type`);

--
-- Indexes for table `vaccination_records`
--
ALTER TABLE `vaccination_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `administered_by` (`administered_by`),
  ADD KEY `idx_vaccination_records_patient_id` (`patient_id`);

--
-- Indexes for table `vaccines`
--
ALTER TABLE `vaccines`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vaccine_schedule`
--
ALTER TABLE `vaccine_schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recommended_age_months` (`recommended_age_months`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=160;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `clinic_settings`
--
ALTER TABLE `clinic_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `consultation_notes`
--
ALTER TABLE `consultation_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `developmental_milestones`
--
ALTER TABLE `developmental_milestones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `doctor_availability`
--
ALTER TABLE `doctor_availability`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `doctor_schedules`
--
ALTER TABLE `doctor_schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `growth_records`
--
ALTER TABLE `growth_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medical_certificates`
--
ALTER TABLE `medical_certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `medical_records`
--
ALTER TABLE `medical_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=155;

--
-- AUTO_INCREMENT for table `patient_files`
--
ALTER TABLE `patient_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `patient_vaccine_needs`
--
ALTER TABLE `patient_vaccine_needs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `print_templates`
--
ALTER TABLE `print_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `vaccination_records`
--
ALTER TABLE `vaccination_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `vaccines`
--
ALTER TABLE `vaccines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `vaccine_schedule`
--
ALTER TABLE `vaccine_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `consultation_notes`
--
ALTER TABLE `consultation_notes`
  ADD CONSTRAINT `consultation_notes_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `consultation_notes_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `developmental_milestones`
--
ALTER TABLE `developmental_milestones`
  ADD CONSTRAINT `developmental_milestones_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `developmental_milestones_ibfk_2` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `doctor_availability`
--
ALTER TABLE `doctor_availability`
  ADD CONSTRAINT `doctor_availability_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `doctor_schedules`
--
ALTER TABLE `doctor_schedules`
  ADD CONSTRAINT `doctor_schedules_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `growth_records`
--
ALTER TABLE `growth_records`
  ADD CONSTRAINT `growth_records_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `growth_records_ibfk_2` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `medical_certificates`
--
ALTER TABLE `medical_certificates`
  ADD CONSTRAINT `medical_certificates_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medical_certificates_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medical_certificates_ibfk_3` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD CONSTRAINT `medical_records_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medical_records_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `patients_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patient_files`
--
ALTER TABLE `patient_files`
  ADD CONSTRAINT `patient_files_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_files_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `patient_vaccine_needs`
--
ALTER TABLE `patient_vaccine_needs`
  ADD CONSTRAINT `patient_vaccine_needs_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_vaccine_needs_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD CONSTRAINT `prescriptions_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `prescriptions_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `vaccination_records`
--
ALTER TABLE `vaccination_records`
  ADD CONSTRAINT `vaccination_records_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vaccination_records_ibfk_2` FOREIGN KEY (`administered_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
