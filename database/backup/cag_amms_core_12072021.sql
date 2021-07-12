-- phpMyAdmin SQL Dump
-- version 5.0.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 12, 2021 at 01:55 PM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cag_amms_core`
--

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2019_08_19_000000_create_failed_jobs_table', 1),
(3, '2021_07_07_060035_create_x_fiscal_years_table', 1),
(4, '2021_07_07_060603_create_x_strategic_plan_durations_table', 1),
(5, '2021_07_07_061257_create_x_strategic_plan_outcomes_table', 1),
(6, '2021_07_07_061309_create_x_strategic_plan_outputs_table', 1),
(7, '2021_07_07_061619_create_x_strategic_plan_required_capacities_table', 1),
(8, '2021_07_07_063250_create_op_activities_table', 1),
(10, '2021_07_07_063335_create_op_activity_responsibles_table', 1),
(11, '2021_07_07_063320_create_op_activity_milestones_table', 2),
(12, '2021_07_11_131602_create_op_yearly_audit_calendars_table', 3),
(14, '2021_07_12_061113_create_x_responsible_offices_table', 4),
(15, '2021_07_11_132615_create_op_yearly_audit_calendar_responsibles_table', 5);

-- --------------------------------------------------------

--
-- Table structure for table `op_activities`
--

CREATE TABLE `op_activities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `duration_id` int(11) NOT NULL,
  `fiscal_year_id` int(11) NOT NULL,
  `outcome_id` int(11) NOT NULL,
  `output_id` int(11) NOT NULL,
  `activity_no` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_bn` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activity_parent_id` int(11) NOT NULL DEFAULT 0,
  `is_parent` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'parent/child',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `op_activities`
--

INSERT INTO `op_activities` (`id`, `duration_id`, `fiscal_year_id`, `outcome_id`, `output_id`, `activity_no`, `title_en`, `title_bn`, `activity_parent_id`, `is_parent`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 'Activity 1.1', 'Preparation of Annual Audit  Plan.', 'বার্ষিক অডিট পরিকল্পনা প্রণয়ন', 0, 0, '2021-07-10 06:10:09', '2021-07-10 06:10:09'),
(2, 1, 1, 1, 1, 'Activity 1.2', 'Financial Audit', 'বাজেটারি সেন্ট্রাল গভর্ণমেন্ট প্রতিষ্ঠানের উপর ফাইন‍্যান্সিয়াল অডিট', 0, 0, '2021-07-10 06:11:21', '2021-07-10 06:11:21'),
(3, 1, 1, 1, 1, 'Activity 1.2.1', 'Financial Audit on Budgetary Central Government', 'বাজেটারি সেন্ট্রাল গভর্ণমেন্ট প্রতিষ্ঠানের উপর ফাইন‍্যান্সিয়াল অডিট', 2, 1, '2021-07-10 06:12:45', '2021-07-10 06:12:45'),
(4, 1, 1, 1, 1, 'Activity 1.2.2', 'Financial Audit on Extra  Budgetary Organisations', 'এক্সট্রা বাজেটারি সেন্ট্রাল গভর্ণমেন্ট প্রতিষ্ঠানের উপর ফাইন‍্যান্সিয়াল অডিট', 2, 1, '2021-07-10 06:12:45', '2021-07-10 06:12:45'),
(5, 1, 1, 1, 1, 'Activity 1.2.3', 'Audit on Special Purpose  Financial Statements', 'বিশেষ উদ্দেশ‍্যে প্রণীত ফাইন‍্যান্সিয়াল স্টেটমেন্ট অডিট', 2, 1, '2021-07-10 06:13:18', '2021-07-10 06:13:18'),
(6, 1, 1, 1, 1, 'Activity 1.3', 'Compliance Audit', 'কমপ্লায়েন্স অডিট', 0, 0, '2021-07-10 06:14:14', '2021-07-10 06:14:14'),
(7, 1, 1, 1, 1, 'Activity 1.3.1', 'Compliance Audit (First Half Yearly)', 'কমপ্লায়েন্স অডিট (১ম অর্ধ বার্ষিক)', 5, 1, '2021-07-10 06:15:32', '2021-07-10 06:15:32'),
(8, 1, 1, 1, 1, 'Activity 1.3.2', 'Compliance Audit (Second Half Yearly)', 'কমপ্লায়েন্স অডিট (২য় অর্ধ বার্ষিক)', 5, 1, '2021-07-10 06:15:52', '2021-07-10 06:15:52'),
(9, 1, 1, 1, 1, 'Activity 1.4', 'Performance Audits', 'পারফরমেন্স অডিট', 0, 0, '2021-07-10 06:16:53', '2021-07-10 06:16:53'),
(10, 1, 1, 1, 1, 'Activity 1.5:', 'Audits on Special Areas', 'বিশেষ ক্ষেত্রসমূহ নিরীক্ষা', 0, 0, '2021-07-10 06:22:01', '2021-07-10 06:22:01'),
(11, 1, 1, 1, 1, 'Activity 1.6', 'Updating Audit Code', 'Updating Audit Code', 0, 0, '2021-07-10 06:23:31', '2021-07-10 06:23:31'),
(12, 1, 1, 1, 1, 'Activity 1.7', 'Compliance Audit Guidelines', 'Compliance Audit Guidelines', 0, 0, '2021-07-10 06:23:43', '2021-07-10 06:23:43'),
(13, 1, 1, 1, 1, 'Activity 1.8', 'Financial Audit Guidelines', 'Financial Audit Guidelines', 0, 0, '2021-07-10 06:23:58', '2021-07-10 06:23:58'),
(14, 1, 1, 1, 1, 'Activity 1.9', 'Performance Audit Guidelines', 'Performance Audit Guidelines', 0, 0, '2021-07-10 06:24:11', '2021-07-10 06:24:11'),
(15, 1, 1, 1, 1, 'Activity 1.10', 'Updating Office Procedure  Manuals', 'Updating Office Procedure  Manuals', 0, 0, '2021-07-10 06:24:29', '2021-07-10 06:24:29'),
(16, 1, 1, 1, 1, 'Activity 1.11', 'Updating Subject Matter  Specific Manuals', 'Updating Subject Matter  Specific Manuals', 0, 0, '2021-07-10 06:24:41', '2021-07-10 06:24:41'),
(17, 1, 1, 1, 1, 'Activity 1.12', 'Using Data Analytics for  Preparing Audit Plan', 'Using Data Analytics for  Preparing Audit Plan', 0, 0, '2021-07-10 06:24:51', '2021-07-10 06:24:51'),
(18, 1, 1, 1, 1, 'Activity 1.13', 'Updating AMMS', 'Updating AMMS', 0, 0, '2021-07-10 06:25:18', '2021-07-10 06:25:18'),
(19, 1, 1, 1, 1, 'Activity 1.14', 'Developing Terms of Reference (TOR) for Audit Quality Assurance Cell', 'Developing Terms of Reference (TOR) for Audit Quality Assurance Cell', 0, 0, '2021-07-10 06:26:07', '2021-07-10 06:26:07'),
(20, 1, 2, 1, 2, 'Activity 2.1', 'Follow up Audit based on PAC Recommendations', 'পিএসি এর সুপারিশক্রমে ফলো আপ অডিট', 0, 0, '2021-07-10 06:26:59', '2021-07-10 06:26:59'),
(21, 1, 2, 1, 2, 'Activity 2.2', 'Follow up Audit on Implementation of Audit Recommendations', 'Follow up Audit on Implementation of Audit Recommendations', 0, 0, '2021-07-10 06:28:01', '2021-07-10 06:28:01'),
(22, 1, 1, 1, 2, 'Activity 2.3', 'Develop Archiving', 'Develop Archiving', 0, 0, '2021-07-10 06:28:15', '2021-07-10 06:28:15'),
(23, 1, 2, 1, 3, 'Activity 3.1', 'Formulate Government Accounting Standards and Procedure', 'Formulate Government Accounting Standards and Procedure', 0, 0, '2021-07-10 06:29:41', '2021-07-10 06:29:41'),
(24, 1, 2, 1, 3, 'Activity 3.2', 'Updating Finance Accounts Format', 'Updating Finance Accounts Format', 0, 0, '2021-07-10 06:30:03', '2021-07-10 06:30:03'),
(25, 1, 2, 1, 3, 'Activity 3.3', 'Updating Appropriation Accounts Format', 'Updating Appropriation Accounts Format', 0, 0, '2021-07-10 06:30:20', '2021-07-10 06:30:20'),
(26, 1, 1, 2, 4, 'Activity 4.1', 'Conducting Training Needs Assessment', 'Conducting Training Needs Assessment', 0, 0, '2021-07-10 06:31:24', '2021-07-10 06:31:24'),
(27, 1, 1, 2, 4, 'Activity 4.2', 'Develop Comprehensive Training Calendar', 'Develop Comprehensive Training Calendar', 0, 0, '2021-07-10 06:31:40', '2021-07-10 06:31:40'),
(28, 1, 1, 2, 4, 'Activity 4.3', 'Develop Core Groups in Specialized Areas for Knowledge Sharing', 'Develop Core Groups in Specialized Areas for Knowledge Sharing', 0, 0, '2021-07-10 06:32:01', '2021-07-10 06:32:01'),
(29, 1, 1, 2, 4, 'Activity 4.4', 'Arrange Short-term, Medium-term and Long-term Training in Home and Abroad', 'Arrange Short-term, Medium-term and Long-term Training in Home and Abroad', 0, 0, '2021-07-10 06:32:40', '2021-07-10 06:32:40'),
(30, 1, 1, 2, 4, 'Activity 4.5', 'Updating Communication Strategy', 'Updating Communication Strategy', 0, 0, '2021-07-10 06:32:54', '2021-07-10 06:32:54'),
(31, 1, 1, 2, 4, 'Activity 4.6', 'Developing Self-disclosure Policy', 'Developing Self-disclosure Policy', 0, 0, '2021-07-10 06:33:16', '2021-07-10 06:33:16'),
(32, 1, 1, 2, 4, 'Activity 4.7', 'Developing Terms of Reference (TOR) for Research and Development Wing', 'Developing Terms of Reference (TOR) for Research and Development Wing', 0, 0, '2021-07-10 06:33:58', '2021-07-10 06:33:58'),
(33, 1, 1, 2, 4, 'Activity 4.8', 'Develop HR Policy', 'Develop HR Policy', 0, 0, '2021-07-10 06:34:40', '2021-07-10 06:34:40'),
(34, 1, 1, 2, 4, 'Activity 4.9', 'Workshop/Seminar with Stakeholders', 'Workshop/Seminar with Stakeholders', 0, 0, '2021-07-10 06:34:57', '2021-07-10 06:34:57'),
(35, 1, 1, 2, 4, 'Activity 4.10', 'Training Module for continuous Professional Development', 'Training Module for continuous Professional Development', 0, 0, '2021-07-10 06:34:57', '2021-07-10 06:34:57');

-- --------------------------------------------------------

--
-- Table structure for table `op_activity_milestones`
--

CREATE TABLE `op_activity_milestones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fiscal_year_id` int(11) NOT NULL,
  `duration_id` int(11) NOT NULL,
  `outcome_id` int(11) NOT NULL,
  `output_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `title_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_bn` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `op_activity_milestones`
--

INSERT INTO `op_activity_milestones` (`id`, `fiscal_year_id`, `duration_id`, `outcome_id`, `output_id`, `activity_id`, `title_en`, `title_bn`, `target_date`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 1, 'Risk Assessment Completed', 'ঝুঁকি মুল‍্যায়ন', NULL, '2021-07-11 05:10:04', '2021-07-11 05:10:04'),
(2, 1, 1, 1, 1, 1, 'Analysis of Relevant Topics Completed', 'প্রাসঙ্গিক বিষয়সমূহ বিশ্লেষণ', NULL, '2021-07-11 05:11:40', '2021-07-11 05:11:40'),
(3, 1, 1, 1, 1, 1, 'Annual Audit Plan Finalized and Approved', 'বার্ষিক অডিট পরিকল্পনা চূড়ান্তকরণ ও অনুমোদন', NULL, '2021-07-11 05:12:28', '2021-07-11 05:12:28'),
(4, 1, 1, 1, 1, 3, 'Planning', 'অডিট পরিকল্পনা প্রণয়ন', NULL, '2021-07-11 05:12:53', '2021-07-11 05:12:53'),
(5, 1, 1, 1, 1, 3, 'Field Audit', 'মাঠ পর্যায়ে অডিট বাস্তবায়ন', NULL, '2021-07-11 05:13:13', '2021-07-11 05:13:13'),
(6, 1, 1, 1, 1, 3, 'Reporting', 'অডিট রিপোর্ট প্রস্তুত, চূড়ান্তকরণ ও সিএজি কার্যালয়ে প্রেরণ', NULL, '2021-07-11 05:14:07', '2021-07-11 05:14:07'),
(7, 1, 1, 1, 1, 4, 'Planning', 'Planning', NULL, '2021-07-11 05:14:31', '2021-07-11 05:14:31'),
(8, 1, 1, 1, 1, 4, 'Field Audit', 'মাঠ পর্যায়ে অডিট বাস্তবায়ন', NULL, '2021-07-11 05:13:13', '2021-07-11 05:13:13'),
(9, 1, 1, 1, 1, 4, 'Reporting', 'অডিট রিপোর্ট প্রস্তুত, চূড়ান্তকরণ ও সিএজি কার্যালয়ে প্রেরণ', NULL, '2021-07-11 05:14:07', '2021-07-11 05:14:07'),
(10, 1, 1, 1, 1, 5, 'Planning', 'Planning', NULL, '2021-07-11 05:14:31', '2021-07-11 05:14:31'),
(11, 1, 1, 1, 1, 5, 'Field Audit', 'মাঠ পর্যায়ে অডিট বাস্তবায়ন', NULL, '2021-07-11 05:13:13', '2021-07-11 05:13:13'),
(12, 1, 1, 1, 1, 5, 'Reporting', 'অডিট রিপোর্ট প্রস্তুত, চূড়ান্তকরণ ও সিএজি কার্যালয়ে প্রেরণ', NULL, '2021-07-11 05:14:07', '2021-07-11 05:14:07'),
(13, 1, 1, 1, 1, 7, 'Planning First (Half Yearly)', 'Planning First (Half Yearly)', NULL, '2021-07-11 05:16:27', '2021-07-11 05:16:27'),
(14, 1, 1, 1, 1, 7, 'Field Audit (First Half Yearly)', 'Field Audit (First Half Yearly)', NULL, '2021-07-11 05:16:36', '2021-07-11 05:16:36'),
(15, 1, 1, 1, 1, 7, 'Reporting (First Half Yearly)', 'Reporting (First Half Yearly)', NULL, '2021-07-11 05:16:48', '2021-07-11 05:16:48'),
(16, 1, 1, 1, 1, 8, 'Planning Second (Half Yearly)', 'Planning Second (Half Yearly)', NULL, '2021-07-11 05:16:27', '2021-07-11 05:16:27'),
(17, 1, 1, 1, 1, 8, 'Field Audit (Second Half Yearly)', 'Field Audit (Second Half Yearly)', NULL, '2021-07-11 05:16:36', '2021-07-11 05:16:36'),
(18, 1, 1, 1, 1, 8, 'Reporting (Second Half Yearly)', 'Reporting (Second Half Yearly)', NULL, '2021-07-11 05:16:48', '2021-07-11 05:16:48'),
(19, 1, 1, 1, 1, 9, 'Planning', 'Planning', NULL, '2021-07-11 05:18:12', '2021-07-11 05:18:12'),
(20, 1, 1, 1, 1, 9, 'Field Audit', 'মাঠ পর্যায়ে অডিট বাস্তবায়ন', NULL, '2021-07-11 05:13:13', '2021-07-11 05:13:13'),
(21, 1, 1, 1, 1, 9, 'Reporting', 'অডিট রিপোর্ট প্রস্তুত, চূড়ান্তকরণ ও সিএজি কার্যালয়ে প্রেরণ', NULL, '2021-07-11 05:14:07', '2021-07-11 05:14:07'),
(22, 1, 1, 1, 1, 10, 'Planning', 'Planning', NULL, '2021-07-11 05:18:12', '2021-07-11 05:18:12'),
(23, 1, 1, 1, 1, 10, 'Field Audit', 'মাঠ পর্যায়ে অডিট বাস্তবায়ন', NULL, '2021-07-11 05:13:13', '2021-07-11 05:13:13'),
(24, 1, 1, 1, 1, 10, 'Reporting', 'অডিট রিপোর্ট প্রস্তুত, চূড়ান্তকরণ ও সিএজি কার্যালয়ে প্রেরণ', NULL, '2021-07-11 05:14:07', '2021-07-11 05:14:07'),
(25, 1, 1, 1, 1, 11, 'Audit Code Updated', 'Audit Code Updated', NULL, '2021-07-11 05:19:08', '2021-07-11 05:19:08'),
(26, 1, 1, 1, 1, 12, 'Guidelines Updated', 'Guidelines Updated', NULL, '2021-07-11 05:19:17', '2021-07-11 05:19:17'),
(27, 1, 1, 1, 1, 13, 'Guidelines Updated', 'Guidelines Updated', NULL, '2021-07-11 05:19:28', '2021-07-11 05:19:28'),
(28, 1, 1, 1, 1, 14, 'Guidelines Updated', 'Guidelines Updated', NULL, '2021-07-11 05:19:33', '2021-07-11 05:19:33'),
(29, 1, 1, 1, 1, 15, 'Manuals Updated', 'Manuals Updated', NULL, '2021-07-11 05:19:47', '2021-07-11 05:19:47'),
(30, 1, 1, 1, 1, 16, 'Manuals Updated', 'Manuals Updated', NULL, '2021-07-11 05:20:22', '2021-07-11 05:20:22'),
(31, 1, 1, 1, 1, 17, 'Audit Plans Prepared Using Data Analytics', 'Audit Plans Prepared Using Data Analytics', NULL, '2021-07-11 05:20:46', '2021-07-11 05:20:46'),
(32, 1, 1, 1, 1, 18, 'AMMS Updated', 'AMMS Updated', NULL, '2021-07-11 05:20:59', '2021-07-11 05:20:59'),
(33, 1, 1, 1, 1, 19, 'Terms of Reference Developed', 'Terms of Reference Developed', NULL, '2021-07-11 05:21:13', '2021-07-11 05:21:13'),
(34, 2, 1, 1, 2, 20, 'Planning', 'Planning', NULL, '2021-07-11 05:21:38', '2021-07-11 05:21:38'),
(35, 2, 1, 1, 2, 20, 'Field Audit', 'মাঠ পর্যায়ে অডিট বাস্তবায়ন', NULL, '2021-07-11 05:13:13', '2021-07-11 05:13:13'),
(36, 2, 1, 1, 2, 20, 'Reporting', 'অডিট রিপোর্ট প্রস্তুত, চূড়ান্তকরণ ও সিএজি কার্যালয়ে প্রেরণ', NULL, '2021-07-11 05:14:07', '2021-07-11 05:14:07'),
(37, 2, 1, 1, 2, 21, 'Planning', 'Planning', NULL, '2021-07-11 05:21:38', '2021-07-11 05:21:38'),
(38, 2, 1, 1, 2, 21, 'Field Audit', 'মাঠ পর্যায়ে অডিট বাস্তবায়ন', NULL, '2021-07-11 05:13:13', '2021-07-11 05:13:13'),
(39, 2, 1, 1, 2, 21, 'Reporting', 'অডিট রিপোর্ট প্রস্তুত, চূড়ান্তকরণ ও সিএজি কার্যালয়ে প্রেরণ', NULL, '2021-07-11 05:14:07', '2021-07-11 05:14:07'),
(40, 1, 1, 1, 2, 22, 'Archiving Developed', 'Archiving Developed', NULL, '2021-07-11 05:22:32', '2021-07-11 05:22:32'),
(41, 2, 1, 1, 3, 23, 'Government Accounting Standards Formulated', 'Government Accounting Standards Formulated', NULL, '2021-07-11 05:22:48', '2021-07-11 05:22:48'),
(42, 2, 1, 1, 3, 24, 'Finance Accounts Format Updated', 'Finance Accounts Format Updated', NULL, '2021-07-11 05:23:00', '2021-07-11 05:23:00'),
(43, 2, 1, 1, 3, 25, 'Appropriation Accounts Format Updated', 'Appropriation Accounts Format Updated', NULL, '2021-07-11 05:23:11', '2021-07-11 05:23:11'),
(44, 1, 1, 2, 4, 26, 'Needs Assessment Completed', 'Needs Assessment Completed', NULL, '2021-07-11 05:23:34', '2021-07-11 05:23:34'),
(45, 1, 1, 2, 4, 27, 'Comprehensive Training Calendar Developed', 'Comprehensive Training Calendar Developed', NULL, '2021-07-11 05:23:45', '2021-07-11 05:23:45'),
(46, 1, 1, 2, 4, 28, 'Core Groups Developed', 'Core Groups Developed', NULL, '2021-07-11 05:23:59', '2021-07-11 05:23:59'),
(47, 1, 1, 2, 4, 29, 'Training Completed', 'Training Completed', NULL, '2021-07-11 05:24:07', '2021-07-11 05:24:07'),
(48, 1, 1, 2, 4, 30, 'Communication Strategies Updated', 'Communication Strategies Updated', NULL, '2021-07-11 05:24:20', '2021-07-11 05:24:20'),
(49, 1, 1, 2, 4, 31, 'Self-Disclosure Policy Developed', 'Self-Disclosure Policy Developed', NULL, '2021-07-11 05:24:34', '2021-07-11 05:24:34'),
(50, 1, 1, 2, 4, 32, 'Terms of Reference (TOR) of Research and Development Wing Developed', 'Terms of Reference (TOR) of Research and Development Wing Developed', NULL, '2021-07-11 05:24:49', '2021-07-11 05:24:49'),
(51, 1, 1, 2, 4, 33, 'HR Policy Developed', 'HR Policy Developed', NULL, '2021-07-11 05:26:29', '2021-07-11 05:26:29'),
(52, 1, 1, 2, 4, 34, 'Workshop/Seminar with Stakeholders Conducted', 'Workshop/Seminar with Stakeholders Conducted', NULL, '2021-07-11 05:26:46', '2021-07-11 05:26:46'),
(53, 1, 1, 2, 4, 35, 'Training Module Completed', 'Training Module Completed', NULL, '2021-07-11 05:29:47', '2021-07-11 05:29:47');

-- --------------------------------------------------------

--
-- Table structure for table `op_activity_responsibles`
--

CREATE TABLE `op_activity_responsibles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `activity_id` int(11) NOT NULL,
  `title_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_bn` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `op_yearly_audit_calendars`
--

CREATE TABLE `op_yearly_audit_calendars` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `duration_id` int(11) NOT NULL,
  `fiscal_year_id` int(11) NOT NULL,
  `outcome_id` int(11) NOT NULL,
  `output_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `milestone_id` int(11) NOT NULL,
  `target_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `op_yearly_audit_calendars`
--

INSERT INTO `op_yearly_audit_calendars` (`id`, `duration_id`, `fiscal_year_id`, `outcome_id`, `output_id`, `activity_id`, `milestone_id`, `target_date`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 1, 1, '2021-07-31', '2021-07-11 05:19:08', '2021-07-12 04:04:48'),
(2, 1, 1, 1, 1, 1, 2, '2021-07-31', '2021-07-11 05:19:08', '2021-07-11 05:19:08'),
(3, 1, 1, 1, 1, 1, 3, '2021-07-31', '2021-07-11 05:19:08', '2021-07-11 05:19:08'),
(4, 1, 1, 1, 1, 3, 4, NULL, '2021-07-11 05:19:08', '2021-07-11 05:19:08'),
(5, 1, 1, 1, 1, 3, 5, NULL, '2021-07-11 05:19:08', '2021-07-11 05:19:08'),
(6, 1, 1, 1, 1, 3, 6, NULL, '2021-07-11 05:19:08', '2021-07-11 05:19:08'),
(7, 1, 1, 1, 1, 4, 7, NULL, '2021-07-11 05:19:08', '2021-07-11 05:19:08'),
(8, 1, 1, 1, 1, 4, 8, NULL, '2021-07-11 05:19:08', '2021-07-11 05:19:08'),
(9, 1, 1, 1, 1, 4, 9, NULL, '2021-07-11 05:19:08', '2021-07-11 05:19:08'),
(10, 1, 1, 1, 1, 5, 10, NULL, '2021-07-11 05:19:08', '2021-07-11 05:19:08'),
(11, 1, 1, 1, 1, 5, 11, NULL, '2021-07-11 05:19:08', '2021-07-11 05:19:08'),
(12, 1, 1, 1, 1, 5, 12, NULL, '2021-07-11 05:19:08', '2021-07-11 05:19:08'),
(13, 1, 1, 1, 1, 7, 13, NULL, '2021-07-11 05:19:08', '2021-07-11 05:19:08'),
(14, 1, 1, 1, 1, 7, 14, NULL, '2021-07-11 05:19:08', '2021-07-11 05:19:08'),
(15, 1, 1, 1, 1, 7, 15, NULL, '2021-07-11 05:19:08', '2021-07-11 05:19:08'),
(16, 1, 1, 1, 1, 8, 16, NULL, '2021-07-11 05:19:08', '2021-07-11 05:19:08'),
(17, 1, 1, 1, 1, 8, 17, NULL, '2021-07-11 05:19:08', '2021-07-11 05:19:08'),
(18, 1, 1, 1, 1, 8, 18, NULL, '2021-07-11 05:19:08', '2021-07-11 05:19:08'),
(19, 1, 1, 1, 1, 9, 19, NULL, '2021-07-11 05:19:08', '2021-07-11 05:19:08'),
(20, 1, 1, 1, 1, 9, 20, NULL, '2021-07-11 05:19:08', '2021-07-11 05:19:08'),
(21, 1, 1, 1, 1, 9, 21, NULL, '2021-07-11 05:19:08', '2021-07-11 05:19:08'),
(22, 1, 1, 1, 1, 10, 22, NULL, '2021-07-11 05:19:08', '2021-07-11 05:19:08'),
(23, 1, 1, 1, 1, 10, 23, NULL, '2021-07-11 05:19:08', '2021-07-11 05:19:08'),
(24, 1, 1, 1, 1, 10, 24, NULL, '2021-07-11 05:19:08', '2021-07-11 05:19:08'),
(25, 1, 1, 1, 1, 11, 25, NULL, '2021-07-12 02:39:36', '2021-07-12 02:39:36'),
(26, 1, 1, 1, 1, 11, 25, NULL, '2021-07-12 02:39:36', '2021-07-12 02:39:36'),
(27, 1, 1, 1, 1, 12, 26, NULL, '2021-07-12 02:39:36', '2021-07-12 02:39:36'),
(28, 1, 1, 1, 1, 13, 27, NULL, '2021-07-12 02:39:36', '2021-07-12 02:39:36'),
(29, 1, 1, 1, 1, 14, 28, NULL, '2021-07-12 02:39:36', '2021-07-12 02:39:36'),
(30, 1, 1, 1, 1, 15, 29, NULL, '2021-07-12 02:39:36', '2021-07-12 02:39:36'),
(31, 1, 1, 1, 1, 16, 30, NULL, '2021-07-12 02:39:36', '2021-07-12 02:39:36'),
(32, 1, 1, 1, 1, 17, 31, NULL, '2021-07-12 02:39:36', '2021-07-12 02:39:36'),
(33, 1, 1, 1, 1, 18, 32, NULL, '2021-07-12 02:39:36', '2021-07-12 02:39:36'),
(34, 1, 1, 1, 1, 19, 33, NULL, '2021-07-12 02:39:36', '2021-07-12 02:39:36'),
(35, 1, 2, 1, 2, 20, 34, NULL, '2021-07-12 02:43:19', '2021-07-12 02:43:19'),
(36, 1, 2, 1, 2, 20, 35, NULL, '2021-07-12 02:43:19', '2021-07-12 02:43:19'),
(37, 1, 2, 1, 2, 20, 36, NULL, '2021-07-12 02:43:19', '2021-07-12 02:43:19'),
(38, 1, 2, 1, 2, 21, 37, NULL, '2021-07-12 02:43:19', '2021-07-12 02:43:19'),
(39, 1, 2, 1, 2, 21, 38, NULL, '2021-07-12 02:43:19', '2021-07-12 02:43:19'),
(40, 1, 2, 1, 2, 21, 39, NULL, '2021-07-12 02:43:19', '2021-07-12 02:43:19'),
(41, 1, 1, 1, 2, 22, 40, NULL, '2021-07-12 02:43:19', '2021-07-12 02:43:19'),
(42, 1, 2, 1, 3, 23, 41, NULL, '2021-07-12 02:43:19', '2021-07-12 02:43:19'),
(43, 1, 2, 1, 3, 24, 42, NULL, '2021-07-12 02:43:19', '2021-07-12 02:43:19'),
(44, 1, 2, 1, 3, 25, 43, NULL, '2021-07-12 02:43:19', '2021-07-12 02:43:19'),
(45, 1, 1, 2, 4, 26, 44, NULL, '2021-07-12 02:43:19', '2021-07-12 02:43:19'),
(46, 1, 1, 2, 4, 27, 45, NULL, '2021-07-12 02:43:19', '2021-07-12 02:43:19'),
(47, 1, 1, 2, 4, 28, 46, NULL, '2021-07-12 02:43:19', '2021-07-12 02:43:19'),
(48, 1, 1, 2, 4, 29, 47, NULL, '2021-07-12 02:43:19', '2021-07-12 02:43:19'),
(49, 1, 1, 2, 4, 30, 48, NULL, '2021-07-12 02:43:19', '2021-07-12 02:43:19'),
(50, 1, 1, 2, 4, 31, 49, NULL, '2021-07-12 02:43:19', '2021-07-12 02:43:19'),
(51, 1, 1, 2, 4, 32, 50, NULL, '2021-07-12 02:43:19', '2021-07-12 02:43:19'),
(52, 1, 1, 2, 4, 33, 51, NULL, '2021-07-12 02:43:19', '2021-07-12 02:43:19'),
(53, 1, 1, 2, 4, 34, 52, NULL, '2021-07-12 02:43:19', '2021-07-12 02:43:19'),
(54, 1, 1, 2, 4, 35, 53, NULL, '2021-07-12 02:43:19', '2021-07-12 02:43:19');

-- --------------------------------------------------------

--
-- Table structure for table `op_yearly_audit_calendar_responsibles`
--

CREATE TABLE `op_yearly_audit_calendar_responsibles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `duration_id` int(11) NOT NULL,
  `fiscal_year_id` int(11) NOT NULL,
  `outcome_id` int(11) NOT NULL,
  `output_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `op_yearly_audit_calendar_id` int(11) NOT NULL,
  `office_id` int(11) NOT NULL,
  `office_type` int(11) NOT NULL,
  `short_name_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_name_bn` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `office_name_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `office_name_bn` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `x_fiscal_years`
--

CREATE TABLE `x_fiscal_years` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `start` year(4) NOT NULL,
  `end` year(4) NOT NULL,
  `duration_id` int(11) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `x_fiscal_years`
--

INSERT INTO `x_fiscal_years` (`id`, `start`, `end`, `duration_id`, `description`, `created_at`, `updated_at`) VALUES
(1, 2021, 2022, 1, 'FY 2021-2022', '2021-07-09 23:54:27', '2021-07-10 00:21:33'),
(2, 2022, 2023, 1, 'FY 2022-2023', '2021-07-11 20:22:51', '2021-07-11 20:22:51');

-- --------------------------------------------------------

--
-- Table structure for table `x_responsible_offices`
--

CREATE TABLE `x_responsible_offices` (
  `id` int(11) NOT NULL,
  `office_id` int(11) NOT NULL,
  `office_name_en` varchar(255) NOT NULL,
  `office_name_bn` varchar(255) NOT NULL,
  `short_name_en` char(32) DEFAULT NULL,
  `short_name_bn` char(32) DEFAULT NULL,
  `office_sequence` int(11) NOT NULL,
  `office_layer` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `modified_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `x_responsible_offices`
--

INSERT INTO `x_responsible_offices` (`id`, `office_id`, `office_name_en`, `office_name_bn`, `short_name_en`, `short_name_bn`, `office_sequence`, `office_layer`, `created_at`, `modified_at`) VALUES
(1, 1, 'Office of the Comptroller and Auditor General of Bangladesh', 'মহা হিসাব নিরীক্ষক ও নিয়ন্ত্রকের কার্যালয়', 'OCAG', NULL, 1, 1, '2021-06-23 08:48:07', '2021-06-23 08:48:07'),
(2, 19, 'Directorate of Civil Audit', 'সিভিল অডিট অধিদপ্তর', 'DFAAA', NULL, 17, 2, '2021-06-24 16:41:52', '2021-06-24 16:41:52'),
(3, 20, 'Directorate of Local Government and Rural Development Audit', 'স্থানীয় সরকার ও পল্লী উন্নয়ন অডিট অধিদপ্তর', 'DHTRDLBA', NULL, 7, 2, '2021-06-24 16:43:17', '2021-06-24 16:43:17'),
(4, 21, 'Directorate of Health Audit', 'স্বাস্থ্য অডিট অধিদপ্তর', 'DHFWA', NULL, 13, 2, '2021-06-24 16:45:28', '2021-06-24 16:45:28'),
(5, 22, 'Directorate of Social Safety Security', 'সামাজিক নিরাপত্তা অডিট অধিদপ্তর', 'DSSWA', NULL, 16, 2, '2021-06-24 16:47:51', '2021-06-24 16:47:51'),
(6, 23, 'Directorate of Revenue Audit', 'রাজস্ব অডিট অধিদপ্তর', 'DRA', NULL, 9, 2, '2021-06-24 16:48:44', '2021-06-24 16:48:44'),
(7, 24, 'Directorate of Constitutional Bodies Audit', 'সাংবিধানিক প্রতিষ্ঠান অডিট অধিদপ্তর', 'DPACBA', NULL, 18, 2, '2021-06-24 16:49:37', '2021-06-24 16:49:37'),
(8, 25, 'Directorate of Power and Energy Audit', 'বিদ্যুৎ ও জ্বালানী অডিট অধিদপ্তর', 'DPENRA', NULL, 19, 2, '2021-06-24 16:50:28', '2021-06-24 16:50:28'),
(9, 26, 'Directorate of Mission Audit', 'মিশন অডিট অধিদপ্তর', 'DFEWA', NULL, 10, 2, '2021-06-24 16:51:10', '2021-06-24 16:51:10'),
(10, 27, 'Directorate of IT and Public Services Audit', 'আইটি ও জনসেবা অডিট অধিদপ্তর', 'DIA', NULL, 8, 2, '2021-06-24 16:52:18', '2021-06-24 16:52:18'),
(11, 28, 'Directorate of Agriculture and Environment Audit', 'কৃষি এবং পরিবেশ অডিট অধিদপ্তর', 'DAEA', NULL, 15, 2, '2021-06-24 16:53:16', '2021-06-24 16:53:16'),
(12, 29, 'Directorate of Commercial Audit', 'বাণিজ্যিক অডিট অধিদপ্তর', 'DICFIA', NULL, 4, 2, '2021-06-24 16:54:29', '2021-06-24 16:54:29'),
(13, 0, 'Directorate of Defense Audit', 'প্রতিরক্ষা অডিট অধিদপ্তর', 'DDA', NULL, 14, 2, '2021-06-24 16:58:18', '2021-06-24 16:58:18'),
(14, 31, 'Directorate of Foreign Aided Projects Audit', 'বৈদেশিক সাহায্যপুস্ট প্রকল্প অডিট অধিদপ্তর', 'DFAPA', NULL, 5, 2, '2021-06-24 16:59:03', '2021-06-24 16:59:03'),
(15, 32, 'Directorate of Works Audit', 'পূর্ত অডিট অধিদপ্তর', 'DHIA', NULL, 6, 2, '2021-06-24 17:00:03', '2021-06-24 17:00:03'),
(16, 33, 'Directorate of Postal, Telecommunication, Science and Technology Audit', 'ডাক, টেলিযোগাযোগ, বিজ্ঞান, তথ্য এবং প্রযুক্তি (পিটিএসটি) অডিট অধিদপ্তর', 'DSITA', NULL, 12, 2, '2021-06-24 17:01:19', '2021-06-24 17:01:19'),
(17, 34, 'Directorate of Transport Audit', 'পরিবহন অডিট অধিদপ্তর', 'DTA', NULL, 3, 2, '2021-06-24 17:02:04', '2021-06-24 17:02:04'),
(18, 35, 'Directorate of Education Audit', 'শিক্ষা অডিট অধিদপ্তর', 'DECRAAA', NULL, 11, 2, '2021-06-24 17:03:20', '2021-06-24 17:03:20'),
(19, 36, 'Financial Management Academy', 'ফাইন‍্যান্সিয়াল ম‍্যানেজমেন্ট একাডেমী', 'FIMA', 'ফিমা', 2, 99, '2021-07-12 06:25:03', '2021-07-12 06:25:03');

-- --------------------------------------------------------

--
-- Table structure for table `x_strategic_plan_durations`
--

CREATE TABLE `x_strategic_plan_durations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `start_year` year(4) NOT NULL,
  `end_year` year(4) NOT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `x_strategic_plan_durations`
--

INSERT INTO `x_strategic_plan_durations` (`id`, `start_year`, `end_year`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 2021, 2025, 'SP 21-25', '2021-07-09 23:32:10', '2021-07-09 23:32:10');

-- --------------------------------------------------------

--
-- Table structure for table `x_strategic_plan_outcomes`
--

CREATE TABLE `x_strategic_plan_outcomes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `duration_id` int(11) NOT NULL,
  `outcome_no` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `outcome_title_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `outcome_title_bn` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `x_strategic_plan_outcomes`
--

INSERT INTO `x_strategic_plan_outcomes` (`id`, `duration_id`, `outcome_no`, `outcome_title_en`, `outcome_title_bn`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 1, 'Outcome 1', 'Increase Credibility', 'Increase Credibility', 'Increased credibility to the SAI’s activities to \r\nthe parliament and other stakeholders will \r\nfacilitate the policymakers in taking \r\nappropriate measures for prudent management of scarce public resources.', '2021-07-10 00:28:19', '2021-07-10 00:28:19'),
(2, 1, 'Outcome 2', 'Improved public financial', 'Improved public financial', 'Improved public financial management resulting in beneficial change to the public sector', '2021-07-10 00:28:54', '2021-07-10 00:28:54');

-- --------------------------------------------------------

--
-- Table structure for table `x_strategic_plan_outputs`
--

CREATE TABLE `x_strategic_plan_outputs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fiscal_year_id` int(11) NOT NULL,
  `duration_id` int(11) NOT NULL,
  `outcome_id` int(11) NOT NULL,
  `output_no` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `output_title_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `output_title_bn` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `x_strategic_plan_outputs`
--

INSERT INTO `x_strategic_plan_outputs` (`id`, `fiscal_year_id`, `duration_id`, `outcome_id`, `output_no`, `output_title_en`, `output_title_bn`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 'Output 1', 'Quality Compliance', 'Quality Compliance', 'Quality Compliance, Financial and Performance audit reports including audit reports on special areas', '2021-07-10 00:29:40', '2021-07-10 00:29:40'),
(2, 1, 1, 1, 'Output 2', 'Increased Follow-up', 'Increased Follow-up', 'Increased Follow-up and reporting on implementation of audit recommendations', '2021-07-10 00:30:34', '2021-07-10 00:30:34'),
(3, 1, 1, 1, 'Output 3', 'Improved Government Accounting', 'Improved Government Accounting', 'Improved Government Accounting Standards and Procedures', '2021-07-10 00:33:51', '2021-07-10 00:33:51'),
(4, 1, 1, 2, 'Output 4', 'Training and Awareness building', 'Training and Awareness building', 'Training and Awareness building in consultation with key stakeholders on various PFM issues', '2021-07-10 00:34:23', '2021-07-10 00:34:23');

-- --------------------------------------------------------

--
-- Table structure for table `x_strategic_plan_required_capacities`
--

CREATE TABLE `x_strategic_plan_required_capacities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `duration_id` int(11) NOT NULL,
  `outcome_id` int(11) NOT NULL,
  `capacity_no` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_bn` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remarks` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `op_activities`
--
ALTER TABLE `op_activities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `op_activity_milestones`
--
ALTER TABLE `op_activity_milestones`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `op_activity_responsibles`
--
ALTER TABLE `op_activity_responsibles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `op_yearly_audit_calendars`
--
ALTER TABLE `op_yearly_audit_calendars`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `op_yearly_audit_calendar_responsibles`
--
ALTER TABLE `op_yearly_audit_calendar_responsibles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `x_fiscal_years`
--
ALTER TABLE `x_fiscal_years`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `x_responsible_offices`
--
ALTER TABLE `x_responsible_offices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `x_strategic_plan_durations`
--
ALTER TABLE `x_strategic_plan_durations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `x_strategic_plan_outcomes`
--
ALTER TABLE `x_strategic_plan_outcomes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `x_strategic_plan_outputs`
--
ALTER TABLE `x_strategic_plan_outputs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `x_strategic_plan_required_capacities`
--
ALTER TABLE `x_strategic_plan_required_capacities`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `op_activities`
--
ALTER TABLE `op_activities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `op_activity_milestones`
--
ALTER TABLE `op_activity_milestones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `op_activity_responsibles`
--
ALTER TABLE `op_activity_responsibles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `op_yearly_audit_calendars`
--
ALTER TABLE `op_yearly_audit_calendars`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `op_yearly_audit_calendar_responsibles`
--
ALTER TABLE `op_yearly_audit_calendar_responsibles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `x_fiscal_years`
--
ALTER TABLE `x_fiscal_years`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `x_responsible_offices`
--
ALTER TABLE `x_responsible_offices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `x_strategic_plan_durations`
--
ALTER TABLE `x_strategic_plan_durations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `x_strategic_plan_outcomes`
--
ALTER TABLE `x_strategic_plan_outcomes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `x_strategic_plan_outputs`
--
ALTER TABLE `x_strategic_plan_outputs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `x_strategic_plan_required_capacities`
--
ALTER TABLE `x_strategic_plan_required_capacities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
