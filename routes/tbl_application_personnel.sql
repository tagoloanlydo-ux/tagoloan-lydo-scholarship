-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 29, 2025 at 01:20 PM
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
-- Database: `lydoscholar`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_application_personnel`
--

CREATE TABLE `tbl_application_personnel` (
  `application_personnel_id` bigint(20) UNSIGNED NOT NULL,
  `application_id` bigint(20) UNSIGNED NOT NULL,
  `lydopers_id` bigint(20) UNSIGNED NOT NULL,
  `initial_screening` varchar(50) NOT NULL DEFAULT 'Pending',
  `rejection_reason` text DEFAULT NULL,
  `reviewer_comment` text DEFAULT NULL,
  `is_bad` tinyint(1) NOT NULL DEFAULT 0,
  `remarks` text NOT NULL DEFAULT 'Pending',
  `status` varchar(50) NOT NULL DEFAULT 'Waiting',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `intake_sheet_token` varchar(64) DEFAULT NULL,
  `update_token` varchar(255) DEFAULT NULL,
  `intake_sheet_submitted` tinyint(1) NOT NULL DEFAULT 0,
  `application_letter_status` varchar(255) NOT NULL DEFAULT 'pending',
  `cert_of_reg_status` varchar(255) NOT NULL DEFAULT 'pending',
  `grade_slip_status` varchar(255) NOT NULL DEFAULT 'pending',
  `brgy_indigency_status` varchar(255) NOT NULL DEFAULT 'pending',
  `student_id_status` varchar(255) NOT NULL DEFAULT 'pending',
  `intake_sheet_token_expires_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_application_personnel`
--

INSERT INTO `tbl_application_personnel` (`application_personnel_id`, `application_id`, `lydopers_id`, `initial_screening`, `rejection_reason`, `reviewer_comment`, `is_bad`, `remarks`, `status`, `created_at`, `updated_at`, `intake_sheet_token`, `update_token`, `intake_sheet_submitted`, `application_letter_status`, `cert_of_reg_status`, `grade_slip_status`, `brgy_indigency_status`, `student_id_status`, `intake_sheet_token_expires_at`) VALUES
(1, 1, 2, 'Reviewed', 'sfsfs', NULL, 0, 'Poor', 'waiting', '2025-10-25 06:11:02', '2025-10-28 23:24:04', 'y08sqJ7t64ZvrhpUvPsgFXPDd1tT50KGJjpP77hvAtnZyuBybmv9HMBYnSrKYmfJ', '57FpDn7dLwTVgRLR4YWcESzusfwRG4nx0GL9QyYUo7JiAPohh396qeALkrqPqu1U', 0, 'good', 'good', 'good', 'good', 'good', NULL),
(2, 2, 2, 'Approved', NULL, NULL, 0, 'Pending', 'Waitng', '2025-10-28 00:14:27', '2025-10-28 23:02:55', '8xl4yLa0OtuyUgocAB4VQAoBENRIlBw2pbFqNbGfE0v5UX2CnDRHDirKRxDenoNY', NULL, 0, 'good', 'good', 'good', 'good', 'good', NULL),
(3, 3, 2, 'Reviewed', NULL, NULL, 0, 'Poor', 'Waiting', '2025-10-28 22:33:28', '2025-10-29 00:45:37', 'ETpXiZ06GiHSPGGtyTTF7RagyIhZC0flUETUwvarp5bqrNr9vtTzUn4cxIyqnhg2', NULL, 0, 'good', 'good', 'good', 'good', 'good', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_application_personnel`
--
ALTER TABLE `tbl_application_personnel`
  ADD PRIMARY KEY (`application_personnel_id`),
  ADD UNIQUE KEY `tbl_application_personnel_intake_sheet_token_unique` (`intake_sheet_token`),
  ADD KEY `tbl_application_personnel_application_id_foreign` (`application_id`),
  ADD KEY `tbl_application_personnel_lydopers_id_foreign` (`lydopers_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_application_personnel`
--
ALTER TABLE `tbl_application_personnel`
  MODIFY `application_personnel_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_application_personnel`
--
ALTER TABLE `tbl_application_personnel`
  ADD CONSTRAINT `tbl_application_personnel_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `tbl_application` (`application_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_application_personnel_lydopers_id_foreign` FOREIGN KEY (`lydopers_id`) REFERENCES `tbl_lydopers` (`lydopers_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
