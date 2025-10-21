-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 27, 2025 at 07:22 PM
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
-- Database: `db_lydoscholarship`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2025_08_19_040613_create_tbl_applicant_table', 1),
(2, '2025_08_19_040613_create_tbl_application_table', 1),
(3, '2025_08_19_040614_create_tbl_lydopers_table', 1),
(4, '2025_08_19_040720_create_tbl_announce_table', 1),
(5, '2025_08_19_040756_create_tbl_application_personnel_table', 1),
(6, '2025_08_19_040823_create_tbl_scholar_table', 1),
(7, '2025_08_19_040906_create_tbl_disburse_table', 1),
(8, '2025_08_19_040946_create_renewal_table', 1),
(9, '2025_08_19_041009_create_password_resets_table', 1),
(10, '2025_08_19_041623_create_sessions_table', 1),
(11, '2025_08_19_041751_create_cache_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`email`, `token`, `created_at`) VALUES
('cagatanmark21@gmail.com', 'dfbDAuhUJ73YTnfScxxIXgz0CFU9pTkT95oZ649GGUsRnf1bSZ3Q7ckm1n7xwCVR', '2025-08-19 01:31:52'),
('cagataknmark17@gmail.com', 'kBcM1jJpjFDkQ91E0Z7rY4JOXwUznIXFjmzuhowDKhVFl9zswaMKhNBwhYaEyuHo', '2025-08-20 21:07:44'),
('cagatanm23@gmail.com', 'xwY8MjgpA4CKTLH6yrLDtaXas2HTklmp0JrKR87nRPra3si2EaRuXt7AyDtsKErV', '2025-08-25 22:54:20');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_announce`
--

CREATE TABLE `tbl_announce` (
  `announce_id` bigint(20) UNSIGNED NOT NULL,
  `lydopers_id` bigint(20) UNSIGNED NOT NULL,
  `announce_title` varchar(255) NOT NULL,
  `announce_content` text NOT NULL,
  `announce_type` varchar(255) NOT NULL,
  `date_posted` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_applicant`
--

CREATE TABLE `tbl_applicant` (
  `applicant_id` bigint(20) UNSIGNED NOT NULL,
  `applicant_fname` varchar(50) NOT NULL,
  `applicant_mname` varchar(50) DEFAULT NULL,
  `applicant_lname` varchar(50) NOT NULL,
  `applicant_suffix` varchar(10) DEFAULT NULL,
  `applicant_gender` varchar(10) NOT NULL,
  `applicant_bdate` date NOT NULL,
  `applicant_civil_status` varchar(20) NOT NULL,
  `applicant_brgy` varchar(100) NOT NULL,
  `applicant_email` varchar(100) NOT NULL,
  `applicant_contact_number` varchar(20) NOT NULL,
  `applicant_school_name` varchar(100) NOT NULL,
  `applicant_year_level` varchar(20) NOT NULL,
  `applicant_course` varchar(100) NOT NULL,
  `applicant_acad_year` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_applicant`
--

INSERT INTO `tbl_applicant` (`applicant_id`, `applicant_fname`, `applicant_mname`, `applicant_lname`, `applicant_suffix`, `applicant_gender`, `applicant_bdate`, `applicant_civil_status`, `applicant_brgy`, `applicant_email`, `applicant_contact_number`, `applicant_school_name`, `applicant_year_level`, `applicant_course`, `applicant_acad_year`, `created_at`, `updated_at`) VALUES
(1, 'Juan', 'Santos', 'Dela Cruz', NULL, 'Male', '2002-05-12', 'Single', 'Natumolan, Tagoloan', 'juan.delacruz@example.com', '09123456781', 'Tagoloan Community College', '3rd Year', 'BSIT', '2025-2026', '2025-08-21 12:12:37', '2025-08-21 12:12:37'),
(2, 'Maria', 'Lopez', 'Reyes', NULL, 'Female', '2003-08-20', 'Single', 'Baluarte, Tagoloan', 'maria.reyes@example.com', '09123456782', 'USTP Tagoloan', '2nd Year', 'BSED English', '2025-2026', '2025-08-21 12:12:37', '2025-08-21 12:12:37'),
(3, 'Jose', 'Cruz', 'Manalo', NULL, 'Male', '2001-01-15', 'Single', 'Sta. Ana, Tagoloan', 'cagatanm23@gmail.com', '09123456783', 'PHINMA COC', '4th Year', 'BSBA', '2025-2026', '2025-08-21 12:12:37', '2025-08-21 12:12:37'),
(4, 'Ana', 'Garcia', 'Torres', NULL, 'Female', '2004-03-18', 'Single', 'Mohon, Tagoloan', 'ana.torres@example.com', '09123456784', 'Xavier University', '1st Year', 'BS Accountancy', '2025-2026', '2025-08-21 12:12:37', '2025-08-21 12:12:37'),
(5, 'Mark', 'Villanueva', 'Serrano', NULL, 'Male', '2002-11-05', 'Single', 'Gracia', 'mark.serrano@example.com', '09123456785', 'USTP Tagoloan', '3rd Year', 'BSECE', '2025-2026', '2025-08-21 12:12:37', '2025-08-22 20:07:52'),
(6, 'Louise', 'Domingo', 'Fernandez', NULL, 'Female', '2003-06-22', 'Single', 'Baluarte', 'louise.fernandez@example.com', '09123456786', 'Tagoloan Community College', '2nd Year', 'BSHRM', '2025-2026', '2025-08-21 12:12:37', '2025-08-22 21:03:44');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_application`
--

CREATE TABLE `tbl_application` (
  `application_id` bigint(20) UNSIGNED NOT NULL,
  `applicant_id` bigint(20) UNSIGNED NOT NULL,
  `application_letter` text NOT NULL,
  `cert_of_reg` text NOT NULL,
  `grade_slip` text NOT NULL,
  `brgy_indigency` text NOT NULL,
  `date_submitted` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_application`
--

INSERT INTO `tbl_application` (`application_id`, `applicant_id`, `application_letter`, `cert_of_reg`, `grade_slip`, `brgy_indigency`, `date_submitted`, `created_at`, `updated_at`) VALUES
(1, 1, 'letter_juan.pdf', 'cor_juan.pdf', 'grades_juan.pdf', 'indigency_juan.pdf', '2025-08-21', '2025-08-21 12:13:04', '2025-08-21 12:13:04'),
(2, 2, 'letter_maria.pdf', 'cor_maria.pdf', 'grades_maria.pdf', 'indigency_maria.pdf', '2025-08-21', '2025-08-21 12:13:04', '2025-08-21 12:13:04'),
(3, 3, 'letter_jose.pdf', 'cor_jose.pdf', 'grades_jose.pdf', 'indigency_jose.pdf', '2025-08-21', '2025-08-21 12:13:04', '2025-08-21 12:13:04'),
(4, 4, 'letter_ana.pdf', 'cor_ana.pdf', 'grades_ana.pdf', 'indigency_ana.pdf', '2025-08-21', '2025-08-21 12:13:04', '2025-08-21 12:13:04'),
(5, 5, 'letter_mark.pdf', 'cor_mark.pdf', 'grades_mark.pdf', 'indigency_mark.pdf', '2025-08-21', '2025-08-21 12:13:04', '2025-08-21 12:13:04'),
(6, 6, 'letter_louise.pdf', 'cor_louise.pdf', 'grades_louise.pdf', 'indigency_louise.pdf', '2025-08-21', '2025-08-21 12:13:04', '2025-08-21 12:13:04');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_application_personnel`
--

CREATE TABLE `tbl_application_personnel` (
  `application_personnel_id` bigint(20) UNSIGNED NOT NULL,
  `application_id` bigint(20) UNSIGNED NOT NULL,
  `lydopers_id` bigint(20) UNSIGNED NOT NULL,
  `initial_screening` varchar(50) NOT NULL DEFAULT 'Pending',
  `remarks` text NOT NULL DEFAULT 'Pending',
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_application_personnel`
--

INSERT INTO `tbl_application_personnel` (`application_personnel_id`, `application_id`, `lydopers_id`, `initial_screening`, `remarks`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 6, 'Approved', 'Poor', 'pending', '2025-08-21 12:13:31', '2025-08-24 20:36:20'),
(2, 2, 6, 'Approved', 'Non Indigenous', 'pending', '2025-08-21 12:13:31', '2025-08-24 20:36:22'),
(3, 3, 6, 'Approved', 'Poor', 'pending', '2025-08-21 12:13:31', '2025-08-24 20:36:27'),
(4, 4, 6, 'Rejected', 'Ultra Poor', 'Pending', '2025-08-21 12:13:31', '2025-08-24 20:36:25'),
(5, 5, 6, 'Approved', 'Non Poor', 'pending', '2025-08-21 12:13:31', '2025-08-25 23:10:55'),
(27, 5, 1, 'Reviewed', 'Non Poor', 'Rejected', '2025-08-24 19:50:13', '2025-08-24 19:50:13'),
(28, 1, 1, 'Reviewed', 'Poor', 'pending', '2025-08-24 20:36:20', '2025-08-24 20:36:20'),
(29, 2, 1, 'Reviewed', 'Non Indigenous', 'pending', '2025-08-24 20:36:22', '2025-08-24 20:36:22'),
(30, 4, 1, 'Reviewed', 'Ultra Poor', 'Pending', '2025-08-24 20:36:25', '2025-08-24 20:36:25'),
(31, 3, 1, 'Reviewed', 'Poor', 'pending', '2025-08-24 20:36:27', '2025-08-24 20:36:27');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_disburse`
--

CREATE TABLE `tbl_disburse` (
  `disburse_id` bigint(20) UNSIGNED NOT NULL,
  `scholar_id` bigint(20) UNSIGNED NOT NULL,
  `lydopers_id` bigint(20) UNSIGNED NOT NULL,
  `disburse_semester` varchar(20) NOT NULL,
  `disburse_acad_year` varchar(20) NOT NULL,
  `dirburse_amount` decimal(10,2) NOT NULL,
  `disburse_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_lydopers`
--

CREATE TABLE `tbl_lydopers` (
  `lydopers_id` bigint(20) UNSIGNED NOT NULL,
  `lydopers_fname` varchar(50) NOT NULL,
  `lydopers_mname` varchar(50) DEFAULT NULL,
  `lydopers_lname` varchar(50) NOT NULL,
  `lydopers_suffix` varchar(10) DEFAULT NULL,
  `lydopers_address` varchar(255) DEFAULT NULL,
  `lydopers_bdate` date DEFAULT NULL,
  `lydopers_email` varchar(100) NOT NULL,
  `lydopers_contact_number` bigint(20) UNSIGNED NOT NULL,
  `lydopers_username` varchar(50) NOT NULL,
  `lydopers_pass` varchar(255) NOT NULL,
  `lydopers_role` varchar(50) NOT NULL,
  `lydopers_status` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_lydopers`
--

INSERT INTO `tbl_lydopers` (`lydopers_id`, `lydopers_fname`, `lydopers_mname`, `lydopers_lname`, `lydopers_suffix`, `lydopers_address`, `lydopers_bdate`, `lydopers_email`, `lydopers_contact_number`, `lydopers_username`, `lydopers_pass`, `lydopers_role`, `lydopers_status`, `created_at`, `updated_at`) VALUES
(1, 'Mark', 'samillano', 'cagatan', NULL, 'P-9 SanMartin Villanueva Mis. Or.', '2003-10-10', 'samillanomark21@gmail.com', 9194519238, 'cagatan', '$2y$12$8sUTf5auL9In..Za04flceZRv1ZhQIeXuzfMTKyVMOStwYLGp2Sd6', 'lydo_staff', 'active', '2025-08-19 00:49:21', '2025-08-19 02:09:33'),
(2, 'Joanna', NULL, 'Arias', NULL, 'P=San Martin', '2003-10-10', 'Cagatanm23@gmail.com', 9194519238, 'Arias@gmail.com', '$2y$12$13NHH9dXilKLsQKcwDZcPO73UELh79YG2sNngfvwSOqxGc3qMJtd.', 'lydo_staff', 'active', '2025-08-19 03:48:04', '2025-08-19 03:48:04'),
(4, 'Mark', 'samillano', 'cagatan', NULL, 'P-9 SanMartin Villanueva Mis. Or.', '2003-10-10', 'cagatanmark17@gmail.com', 9194519238, 'Arias23', '$2y$12$WTJ6k1LS84Q3fPXOQ7Omye88Y5nijghvuxACowhvu7t.7tQczsh8O', 'lydo_staff', 'inactive', '2025-08-20 19:53:59', '2025-08-20 19:53:59'),
(5, 'Mark', 'samillano', 'cagatan', NULL, 'P-9 SanMartin Villanueva Mis. Or.', '2003-10-10', 'cagataknmark17@gmail.com', 9194519238, 'Arias@gmail.comk', '$2y$12$60fVDyQrnKC7/N2mQae2geISHrFA8TY8JotusR0CDbcE.G5tYdjkq', 'lydo_staff', 'Active', '2025-08-20 20:06:54', '2025-08-20 20:06:54'),
(6, 'Joanna', NULL, 'Arias', NULL, 'tagoloan', '2000-11-11', 'Joanna@gmail.com', 9123456789, 'joanna', '$2y$12$7BZLNdAhlKRPAnbg7hxfAuZBDT5PEZNBedEokze1EQgX3mPcHdJse', 'mayor_staff', 'active', '2025-08-22 22:59:50', '2025-08-22 22:59:50'),
(7, 'Monica', NULL, 'Rabino', NULL, 'tagoloan', '2003-11-11', 'monica@gmail.com', 9123456789, 'monica', '$2y$12$YA0h/NajMBFRncpxFQhE5OTfhQsLxT0KSbxd3lK7Tac21585Z3FES', 'lydo_admin', 'active', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_renewal`
--

CREATE TABLE `tbl_renewal` (
  `renewal_id` bigint(20) UNSIGNED NOT NULL,
  `scholar_id` bigint(20) UNSIGNED NOT NULL,
  `renewal_cert_of_reg` text NOT NULL,
  `renewal_grade_slip` text NOT NULL,
  `renewal_brgy_indigency` text NOT NULL,
  `renewal_semester` varchar(20) NOT NULL,
  `renewal_acad_year` varchar(20) NOT NULL,
  `date_submitted` date NOT NULL,
  `renewal_status` varchar(50) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_renewal`
--

INSERT INTO `tbl_renewal` (`renewal_id`, `scholar_id`, `renewal_cert_of_reg`, `renewal_grade_slip`, `renewal_brgy_indigency`, `renewal_semester`, `renewal_acad_year`, `date_submitted`, `renewal_status`, `created_at`, `updated_at`) VALUES
(1, 1, 'renewal_cor_juan.pdf', 'renewal_grades_juan.pdf', 'renewal_indigency_juan.pdf', '1st Semester', '2025-2026', '2025-08-23', 'Approved', '2025-08-23 05:33:04', '2025-08-27 07:34:07');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_scholar`
--

CREATE TABLE `tbl_scholar` (
  `scholar_id` bigint(20) UNSIGNED NOT NULL,
  `application_id` bigint(20) UNSIGNED NOT NULL,
  `scholar_username` varchar(50) NOT NULL,
  `scholar_pass` varchar(255) NOT NULL,
  `date_activated` date NOT NULL,
  `scholar_status` varchar(50) NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_scholar`
--

INSERT INTO `tbl_scholar` (`scholar_id`, `application_id`, `scholar_username`, `scholar_pass`, `date_activated`, `scholar_status`, `created_at`, `updated_at`) VALUES
(1, 1, 'juan_scholar', 'f6ccb3e8d609012238c0b39e60b2c9632b3cdede91e035dad1de43469768f4cc', '2025-08-23', 'Inactive', '2025-08-23 05:32:51', '2025-08-24 20:41:39'),
(20221216, 3, 'jose_scholar', '1cd763f4482ed8c2f58fe7608542b975c0b158c81aae7aaade5d58b0164b4a37', '2025-08-23', 'Active', '2025-08-23 05:32:51', '2025-08-23 05:32:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

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
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `tbl_announce`
--
ALTER TABLE `tbl_announce`
  ADD PRIMARY KEY (`announce_id`),
  ADD KEY `tbl_announce_lydopers_id_foreign` (`lydopers_id`);

--
-- Indexes for table `tbl_applicant`
--
ALTER TABLE `tbl_applicant`
  ADD PRIMARY KEY (`applicant_id`),
  ADD UNIQUE KEY `tbl_applicant_applicant_email_unique` (`applicant_email`);

--
-- Indexes for table `tbl_application`
--
ALTER TABLE `tbl_application`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `tbl_application_applicant_id_foreign` (`applicant_id`);

--
-- Indexes for table `tbl_application_personnel`
--
ALTER TABLE `tbl_application_personnel`
  ADD PRIMARY KEY (`application_personnel_id`),
  ADD KEY `tbl_application_personnel_application_id_foreign` (`application_id`),
  ADD KEY `tbl_application_personnel_lydopers_id_foreign` (`lydopers_id`);

--
-- Indexes for table `tbl_disburse`
--
ALTER TABLE `tbl_disburse`
  ADD PRIMARY KEY (`disburse_id`),
  ADD KEY `tbl_disburse_scholar_id_foreign` (`scholar_id`),
  ADD KEY `tbl_disburse_lydopers_id_foreign` (`lydopers_id`);

--
-- Indexes for table `tbl_lydopers`
--
ALTER TABLE `tbl_lydopers`
  ADD PRIMARY KEY (`lydopers_id`),
  ADD UNIQUE KEY `tbl_lydopers_lydopers_email_unique` (`lydopers_email`),
  ADD UNIQUE KEY `tbl_lydopers_lydopers_username_unique` (`lydopers_username`);

--
-- Indexes for table `tbl_renewal`
--
ALTER TABLE `tbl_renewal`
  ADD PRIMARY KEY (`renewal_id`),
  ADD KEY `tbl_renewal_scholar_id_foreign` (`scholar_id`);

--
-- Indexes for table `tbl_scholar`
--
ALTER TABLE `tbl_scholar`
  ADD PRIMARY KEY (`scholar_id`),
  ADD UNIQUE KEY `tbl_scholar_scholar_username_unique` (`scholar_username`),
  ADD KEY `tbl_scholar_application_id_foreign` (`application_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tbl_announce`
--
ALTER TABLE `tbl_announce`
  MODIFY `announce_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_applicant`
--
ALTER TABLE `tbl_applicant`
  MODIFY `applicant_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_application`
--
ALTER TABLE `tbl_application`
  MODIFY `application_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_application_personnel`
--
ALTER TABLE `tbl_application_personnel`
  MODIFY `application_personnel_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `tbl_disburse`
--
ALTER TABLE `tbl_disburse`
  MODIFY `disburse_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_lydopers`
--
ALTER TABLE `tbl_lydopers`
  MODIFY `lydopers_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_renewal`
--
ALTER TABLE `tbl_renewal`
  MODIFY `renewal_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_scholar`
--
ALTER TABLE `tbl_scholar`
  MODIFY `scholar_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20221217;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_announce`
--
ALTER TABLE `tbl_announce`
  ADD CONSTRAINT `tbl_announce_lydopers_id_foreign` FOREIGN KEY (`lydopers_id`) REFERENCES `tbl_lydopers` (`lydopers_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_application`
--
ALTER TABLE `tbl_application`
  ADD CONSTRAINT `tbl_application_applicant_id_foreign` FOREIGN KEY (`applicant_id`) REFERENCES `tbl_applicant` (`applicant_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_application_personnel`
--
ALTER TABLE `tbl_application_personnel`
  ADD CONSTRAINT `tbl_application_personnel_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `tbl_application` (`application_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_application_personnel_lydopers_id_foreign` FOREIGN KEY (`lydopers_id`) REFERENCES `tbl_lydopers` (`lydopers_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_disburse`
--
ALTER TABLE `tbl_disburse`
  ADD CONSTRAINT `tbl_disburse_lydopers_id_foreign` FOREIGN KEY (`lydopers_id`) REFERENCES `tbl_lydopers` (`lydopers_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_disburse_scholar_id_foreign` FOREIGN KEY (`scholar_id`) REFERENCES `tbl_scholar` (`scholar_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_renewal`
--
ALTER TABLE `tbl_renewal`
  ADD CONSTRAINT `tbl_renewal_scholar_id_foreign` FOREIGN KEY (`scholar_id`) REFERENCES `tbl_scholar` (`scholar_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_scholar`
--
ALTER TABLE `tbl_scholar`
  ADD CONSTRAINT `tbl_scholar_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `tbl_application` (`application_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
