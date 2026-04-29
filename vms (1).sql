-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 29, 2026 at 08:51 AM
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
-- Database: `vms`
--

-- --------------------------------------------------------

--
-- Table structure for table `app_settings`
--

CREATE TABLE `app_settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `app_settings`
--

INSERT INTO `app_settings` (`setting_key`, `setting_value`) VALUES
('sidebar_color', '#050505');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `dept_id` int(11) NOT NULL,
  `dept_name` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `status` int(11) DEFAULT 1,
  `dept_color` varchar(20) DEFAULT '#4361ee'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`dept_id`, `dept_name`, `created_at`, `status`, `dept_color`) VALUES
(3, 'Digital marketing', '2026-04-24 17:39:04', 1, '#4361ee'),
(4, 'IT', '2026-04-25 09:26:39', 1, '#0d0d0d'),
(5, 'Employee', '2026-04-25 09:49:39', 1, '#c6d312'),
(6, 'HR', '2026-04-25 09:49:46', 1, '#24e54b'),
(7, 'Gate', '2026-04-25 10:00:11', 1, '#ee4444');

-- --------------------------------------------------------

--
-- Table structure for table `designation_master`
--

CREATE TABLE `designation_master` (
  `id` int(11) NOT NULL,
  `designation_name` varchar(100) NOT NULL,
  `status` int(11) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `designation_master`
--

INSERT INTO `designation_master` (`id`, `designation_name`, `status`, `created_at`) VALUES
(1, 'Manager', 1, '2026-04-25 19:52:56'),
(2, 'Software Engineer', 1, '2026-04-25 19:52:56'),
(3, 'HR Executive', 1, '2026-04-25 19:52:56'),
(4, 'Team Lead', 1, '2026-04-25 19:52:56'),
(5, 'Project Manager', 1, '2026-04-25 19:52:56'),
(6, 'QA Engineer', 1, '2026-04-25 19:52:56'),
(7, 'System Administrator', 1, '2026-04-25 19:52:56'),
(8, 'Business Analyst', 1, '2026-04-25 19:52:56'),
(9, 'UI/UX Designer', 1, '2026-04-25 19:52:56'),
(10, 'Security', 1, '2026-04-25 19:52:56'),
(11, 'Management', 1, '2026-04-25 19:52:56');

-- --------------------------------------------------------

--
-- Table structure for table `employee_master`
--

CREATE TABLE `employee_master` (
  `id` int(11) NOT NULL,
  `emp_name` varchar(100) NOT NULL,
  `designation` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `contact_no` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_master`
--

INSERT INTO `employee_master` (`id`, `emp_name`, `designation`, `department`, `email`, `contact_no`, `created_at`) VALUES
(42, 'Ramesh Kumar', 'Manager', 'Admin', 'suryapanneer@gmail.com', '9876543210', '2026-04-22 05:21:16'),
(43, 'Priya S', 'HR Executive', 'Digital marketing', 'suryapanneer04@gmail.com', '9876501234', '2026-04-22 05:21:16'),
(57, 'Janani', 'Software Engineer', 'IT', 'suryapanneer04@gmail.com', '9384178411', '2026-04-25 04:17:05'),
(58, 'jack', 'Office boy', 'Gate', 'jack@gmail.com', '9876501231', '2026-04-25 04:36:24');

-- --------------------------------------------------------

--
-- Table structure for table `mail_settings`
--

CREATE TABLE `mail_settings` (
  `id` int(11) NOT NULL,
  `smtp_host` varchar(255) DEFAULT NULL,
  `smtp_user` varchar(255) DEFAULT NULL,
  `smtp_pass` varchar(255) DEFAULT NULL,
  `smtp_port` int(11) DEFAULT NULL,
  `smtp_secure` varchar(10) DEFAULT NULL,
  `from_email` varchar(255) DEFAULT NULL,
  `from_name` varchar(255) DEFAULT NULL,
  `mail_footer` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mail_settings`
--

INSERT INTO `mail_settings` (`id`, `smtp_host`, `smtp_user`, `smtp_pass`, `smtp_port`, `smtp_secure`, `from_email`, `from_name`, `mail_footer`) VALUES
(1, 'smtp.gmail.com', 'suryapanneer04@gmail.com', 'mkmu yfqt tjsn fkpr', 587, 'tls', 'suryapanneer04@gmail.com', 'VMS', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `mail_templates`
--

CREATE TABLE `mail_templates` (
  `id` int(11) NOT NULL,
  `template_key` varchar(50) NOT NULL,
  `template_name` varchar(100) NOT NULL,
  `from_email` varchar(100) DEFAULT NULL,
  `to_email` varchar(100) DEFAULT NULL,
  `cc_email` varchar(255) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mail_footer` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mail_templates`
--

INSERT INTO `mail_templates` (`id`, `template_key`, `template_name`, `from_email`, `to_email`, `cc_email`, `subject`, `body`, `created_at`, `updated_at`, `mail_footer`) VALUES
(1, 'visitor_approval', 'Visitor Approval Notification', '', NULL, '', 'Visitor Request Submitted: {{pass_no}}', 'Dear {{emp_name}},<br><br>A new visitor request has been submitted and is awaiting your approval.<br><br><b>Visitor Details:</b><br><b>Visitor Name:</b> {{visitor_name}} <br><b>Contact Number:</b> {{contact_no}} <br><b>Pass Number:</b> {{pass_no}} <br><b>Purpose of Visit:</b> {{purpose}} <br><b>Company Name:</b> {{company_name}} <br><b>Check-in Time:</b> {{in_time}} <br><br>Kindly review and approve the visitor request at your earliest convenience.<br><br>Regards,<br><b>Visitor Management System</b>', '2026-04-27 05:03:53', '2026-04-27 07:11:37', NULL),
(2, 'user_credentials', 'User Account Credentials', NULL, NULL, NULL, 'Your VMS Account Credentials', '<h4>Welcome to VMS</h4><p>Dear {{full_name}},</p><p>Your account has been created successfully. Below are your login credentials:</p><p><b>Username:</b> {{email}}<br><b>Password:</b> {{password}}</p><p>Regards,<br>Admin Team</p>', '2026-04-27 05:03:53', '2026-04-27 05:03:53', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `plants`
--

CREATE TABLE `plants` (
  `plant_id` int(11) NOT NULL,
  `plant_location` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `plants`
--

INSERT INTO `plants` (`plant_id`, `plant_location`, `created_at`) VALUES
(1, 'chennai', '2026-04-24 17:44:58');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `contact_no` varchar(20) DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `role` varchar(50) DEFAULT 'employee',
  `reporting_manager` varchar(100) DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `contact_no`, `designation`, `department`, `location`, `role`, `reporting_manager`, `status`, `created_at`) VALUES
(1, 'Ramesh Kumar', 'suryapanneer@gmail.com', '$2y$10$vGEfSCCHgbGGRgyPtjyvnO4ccRJUiw7MJaqg/BX6G1ESh2zv/boXy', '9876543210', 'Management', 'admin', 'chennai', 'admin', 'admin', 'Active', '2026-04-25 09:31:15'),
(5, 'sundar', 'suryapanneer0@gmail.com', '$2y$10$u6PriGuF6MukY03d.qj28eAtl6Qj1UiDS/0qbUk/.dAIM4Ak1j9Gy', '9876501231', 'Manager', 'IT', 'chennai', 'employee', '1', 'Active', '2026-04-25 09:52:57'),
(11, 'Surya P', 'surya@gmail.com', '$2y$10$u6PriGuF6MukY03d.qj28eAtl6Qj1UiDS/0qbUk/.dAIM4Ak1j9Gy', '9384178442', 'Software Engineer', 'IT', 'chennai', 'employee', '5', 'Active', '2026-04-25 10:02:51'),
(12, 'Michael Scott', 'michael@example.com', '$2y$10$u6PriGuF6MukY03d.qj28eAtl6Qj1UiDS/0qbUk/.dAIM4Ak1j9Gy', '9259237304', 'Manager', 'Digital marketing', 'chennai', 'employee', '1', 'Active', '2026-04-26 01:41:19'),
(14, 'Jim Halpert', 'jim@example.com', '$2y$10$ZUORjHg1kMIuwblrbPxr4ebJR6VirF.DaRFgcuZPKg/oshnSMOxL$2y$10$u6PriGuF6MukY03d.qj28eAtl6Qj1UiDS/0qbUk/.dAIM4Ak1j9Gy', '9431673103', 'Sales Executive', 'Digital marketing', 'chennai', 'employee', '12', 'Active', '2026-04-26 01:41:19'),
(15, 'Pam Beesly', 'pam@example.com', '$2y$10$u6PriGuF6MukY03d.qj28eAtl6Qj1UiDS/0qbUk/.dAIM4Ak1j9Gy', '9911090482', 'Receptionist', 'Digital marketing', 'chennai', 'employee', '12', 'Active', '2026-04-26 01:41:19'),
(16, 'Angela Martin', 'angela@example.com', '$2y$10$u6PriGuF6MukY03d.qj28eAtl6Qj1UiDS/0qbUk/.dAIM4Ak1j9Gy', '9735472586', 'Accountant', 'IT', 'chennai', 'employee', '5', 'Active', '2026-04-26 01:41:19'),
(17, 'Kevin Malone', 'kevin@example.com', '$2y$10$u6PriGuF6MukY03d.qj28eAtl6Qj1UiDS/0qbUk/.dAIM4Ak1j9Gy', '9191266558', 'Assistent', 'IT', 'chennai', 'employee', '5', 'Active', '2026-04-26 01:41:19'),
(18, 'Oscar Martinez', 'oscar@example.com', '$2y$10$u6PriGuF6MukY03d.qj28eAtl6Qj1UiDS/0qbUk/.dAIM4Ak1j9Gy', '9940579593', 'Analyst', 'IT', 'chennai', 'employee', '5', 'Active', '2026-04-26 01:41:19'),
(19, 'Toby Flenderson', 'toby@example.com', '$2y$10$u6PriGuF6MukY03d.qj28eAtl6Qj1UiDS/0qbUk/.dAIM4Ak1j9Gy', '9833755328', 'HR Executive', 'HR', 'chennai', 'employee', '1', 'Active', '2026-04-26 01:41:19'),
(20, 'Stanley Hudson', 'stanley@example.com', '$2y$10$u6PriGuF6MukY03d.qj28eAtl6Qj1UiDS/0qbUk/.dAIM4Ak1j9Gy', '9308389883', 'Sales', 'Digital marketing', 'chennai', 'employee', '12', 'Active', '2026-04-26 01:41:19'),
(21, 'Kelly Kapoor', 'kelly@example.com', '$2y$10$u6PriGuF6MukY03d.qj28eAtl6Qj1UiDS/0qbUk/.dAIM4Ak1j9Gy', '9720617197', 'Customer Care', 'Digital marketing', 'chennai', 'employee', '12', 'Active', '2026-04-26 01:41:19'),
(22, 'Rony', 'rony@gmail.com', '$2y$10$u6PriGuF6MukY03d.qj28eAtl6Qj1UiDS/0qbUk/.dAIM4Ak1j9Gy', '9384178411', 'Security', 'Gate', 'chennai', 'timeoffice', '1', 'Active', '2026-04-26 05:37:15'),
(26, 'Janani', 'suryapanneer04@gmail.com', '$2y$10$u6PriGuF6MukY03d.qj28eAtl6Qj1UiDS/0qbUk/.dAIM4Ak1j9Gy', '9384178441', 'QA Engineer', 'IT', 'chennai', 'employee', '5', 'Active', '2026-04-27 10:06:28');

-- --------------------------------------------------------

--
-- Table structure for table `visitor_handoffs`
--

CREATE TABLE `visitor_handoffs` (
  `id` int(11) NOT NULL,
  `visitor_id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `assigned_by` int(11) NOT NULL,
  `check_in_time` datetime NOT NULL,
  `check_out_time` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visitor_handoffs`
--

INSERT INTO `visitor_handoffs` (`id`, `visitor_id`, `emp_id`, `assigned_by`, `check_in_time`, `check_out_time`, `notes`, `created_at`) VALUES
(17, 42, 5, 5, '2026-04-28 13:57:00', '2026-04-28 14:15:46', 'Handoff', '2026-04-28 08:45:46'),
(18, 42, 11, 5, '2026-04-28 14:15:46', '2026-04-28 14:16:00', '', '2026-04-28 08:45:46'),
(19, 43, 19, 19, '2026-04-28 14:23:00', '2026-04-28 14:25:57', 'Initial Meeting', '2026-04-28 08:54:19'),
(20, 43, 12, 19, '2026-04-28 14:25:57', '2026-04-28 14:28:00', '', '2026-04-28 08:55:57');

-- --------------------------------------------------------

--
-- Table structure for table `visitor_master`
--

CREATE TABLE `visitor_master` (
  `id` int(11) NOT NULL,
  `pass_no` varchar(50) DEFAULT NULL,
  `img_capture` longtext DEFAULT NULL,
  `person_to_meet` varchar(100) NOT NULL,
  `purpose` varchar(255) NOT NULL,
  `visitor_type` enum('Client','Vendor','Visitor') NOT NULL,
  `company_name` varchar(100) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `visitor_name` varchar(100) NOT NULL,
  `contact_no` varchar(15) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `devices` text DEFAULT NULL,
  `id_type` varchar(50) DEFAULT NULL,
  `id_upload` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `in_time` datetime DEFAULT NULL,
  `out_time` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `vehicle_type` varchar(50) DEFAULT NULL,
  `vehicle_number` varchar(50) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `mobile_count` int(11) DEFAULT NULL,
  `charger_count` int(11) DEFAULT NULL,
  `disc_count` int(11) DEFAULT NULL,
  `laptop_count` int(11) DEFAULT NULL,
  `approval_status` tinyint(4) DEFAULT 0 COMMENT '0=Pending, 1=Approved, 2=Rejected,\r\n3= Schedule',
  `meeting_out_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visitor_master`
--

INSERT INTO `visitor_master` (`id`, `pass_no`, `img_capture`, `person_to_meet`, `purpose`, `visitor_type`, `company_name`, `location`, `visitor_name`, `contact_no`, `email`, `devices`, `id_type`, `id_upload`, `status`, `in_time`, `out_time`, `created_at`, `vehicle_type`, `vehicle_number`, `employee_id`, `mobile_count`, `charger_count`, `disc_count`, `laptop_count`, `approval_status`, `meeting_out_time`) VALUES
(42, 'VMS_506963', 'img_20260428_135819_VMS_506963.jpeg', 'sundar', 'interview', 'Visitor', 'gentriq', 'chennai', 'Divya', '9384178400', 'suryapanneer04@gmail.com', '', 'Aadhar', NULL, 'Pending', '2026-04-28 13:57:00', '2026-04-28 14:21:00', '2026-04-28 08:28:19', '', '', 11, 0, 0, 0, 0, 1, '2026-04-28 14:16:00'),
(43, 'VMS_432758', 'img_20260428_142419_VMS_432758.jpeg', 'Toby Flenderson', 'addada', 'Visitor', 'gentriq', 'chennai', 'Meenatchi', '9384178421', 'suryapanneer04@gmail.com', '', 'Aadhar', NULL, 'Pending', '2026-04-28 14:23:00', '2026-04-28 14:31:00', '2026-04-28 08:54:19', '', '', 12, 0, 0, 0, 0, 1, '2026-04-28 14:28:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `app_settings`
--
ALTER TABLE `app_settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`dept_id`);

--
-- Indexes for table `designation_master`
--
ALTER TABLE `designation_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_master`
--
ALTER TABLE `employee_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mail_settings`
--
ALTER TABLE `mail_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mail_templates`
--
ALTER TABLE `mail_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `template_key` (`template_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `visitor_handoffs`
--
ALTER TABLE `visitor_handoffs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `visitor_master`
--
ALTER TABLE `visitor_master`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `mail_settings`
--
ALTER TABLE `mail_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mail_templates`
--
ALTER TABLE `mail_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `visitor_handoffs`
--
ALTER TABLE `visitor_handoffs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `visitor_master`
--
ALTER TABLE `visitor_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
