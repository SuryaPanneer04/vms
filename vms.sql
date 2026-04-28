-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 27, 2026 at 01:42 PM
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
(3, 41, 19, 19, '2026-04-27 12:35:00', '2026-04-27 12:37:33', 'Handoff', '2026-04-27 07:07:33'),
(4, 41, 5, 19, '2026-04-27 12:37:33', '2026-04-27 12:38:00', 'final round', '2026-04-27 07:07:33'),
(11, 22, 19, 19, '2026-04-27 12:37:33', '2026-04-27 12:35:00', 'Initial Meeting', '2026-04-27 07:24:50'),
(12, 23, 19, 19, '0000-00-00 00:00:00', '2026-04-27 12:35:00', 'Initial Meeting', '2026-04-27 07:24:50'),
(13, 25, 19, 19, '2026-04-27 12:35:00', '2026-04-27 12:35:00', 'Initial Meeting', '2026-04-27 07:24:50'),
(14, 27, 19, 19, '2026-04-27 12:35:00', '2026-04-27 12:35:00', 'Initial Meeting', '2026-04-27 07:24:50'),
(15, 30, 19, 19, '2026-04-27 12:35:00', '2026-04-27 12:35:00', 'Initial Meeting', '2026-04-27 07:24:50'),
(16, 31, 11, 11, '2026-04-27 12:35:00', '2026-04-27 12:35:00', 'Initial Meeting', '2026-04-27 07:24:50');

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
(22, 'VMS_299924', 'img_20260422_145854_VMS_299924.jpeg', 'Ramesh Kumar', 'interview', 'Visitor', 'BB', 'chennai maduravoyal', 'suryapanneer', '9384178442', 'suryapanneer04@gmail.com', 'Mobile,Disc', 'pan', '', 'Pending', '2026-04-22 14:57:00', '2026-04-22 15:04:00', '2026-04-22 03:58:54', 'Bike', 'TN 67 BK 9626', 19, 2, 0, 3, 0, 1, '2026-04-22 15:04:00'),
(23, 'VMS_637996', 'img_20260422_151655_VMS_637996.jpeg', 'Ramesh Kumar', 'interview', 'Visitor', 'BB', 'chennai maduravoyal', 'priya', '9384178443', 'suryapanneer04@gmail.com', 'Mobile', 'Aadhar', NULL, 'Pending', '2026-04-22 15:04:00', '2026-04-22 15:19:00', '2026-04-22 04:05:29', '', '', 19, 2, 0, 0, 0, 1, '2026-04-22 15:19:00'),
(25, 'VMS_779835', 'img_20260422_152638_VMS_178983.jpeg', 'Ramesh Kumar', 'interview', 'Visitor', 'BB', 'chennai maduravoyal', 'priya', '9384178441', 'suryapanneer04@gmail.com', 'Mobile', 'Aadhar', NULL, 'Pending', '2026-04-22 15:24:00', '2026-04-24 10:04:00', '2026-04-22 04:25:06', 'Bike', 'TN 67 BK 9626', 19, 2, 0, 0, 0, 1, '2026-04-24 10:04:00'),
(27, 'VMS_829347', 'img_20260424_161623_VMS_829347.jpeg', 'Ramesh Kumar', 'in', 'Visitor', 'gentriq', 'chennai maduravoyal', 'sundar', '9384178440', 'suryapanneer04@gmail.com', '', 'Driving License', NULL, 'Pending', '2026-04-24 16:12:00', '2026-04-24 16:16:00', '2026-04-24 05:15:46', '', 'TN 67 BK 9626', 19, 0, 0, 0, 0, 1, '2026-04-24 16:16:00'),
(30, 'VMS_417334', 'img_20260425_102308_VMS_417334.jpeg', 'Priya S', 'interview', 'Client', 'gentriq', 'chennai maduravoyal', 'Naveen', '9384178422', 'suryapanneer04@gmail.com', 'Mobile', 'Aadhar', NULL, 'Pending', '2026-04-25 10:16:00', '2026-04-25 10:24:00', '2026-04-24 23:23:08', 'Bike', 'TN 67 BK 9626', 19, 2, 0, 0, 0, 1, '2026-04-25 10:24:00'),
(31, 'VMS_619959', 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/4gHYSUNDX1BST0ZJTEUAAQEAAAHIAAAAAAQwAABtbnRyUkdCIFhZWiAH4AABAAEAAAAAAABhY3NwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAQAA9tYAAQAAAADTLQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAlkZXNjAAAA8AAAACRyWFlaAAABFAAAABRnWFlaAAABKAAAABRiWFlaAAABPAAAABR3dHB0AAABUAAAABRyVFJDAAABZAAAAChnVFJDAAABZAAAAChiVFJDAAABZAAAAChjcHJ0AAABjAAAADxtbHVjAAAAAAAAAAEAAAAMZW5VUwAAAAgAAAAcAHMAUgBHAEJYWVogAAAAAAAAb6IAADj1AAADkFhZWiAAAAAAAABimQAAt4UAABjaWFlaIAAAAAAAACSgAAAPhAAAts9YWVogAAAAAAAA9tYAAQAAAADTLXBhcmEAAAAAAAQAAAACZmYAAPKnAAANWQAAE9AAAApbAAAAAAAAAABtbHVjAAAAAAAAAAEAAAAMZW5VUwAAACAAAAAcAEcAbwBvAGcAbABlACAASQBuAGMALgAgADIAMAAxADb/2wBDAAMCAgMCAgMDAwMEAwMEBQgFBQQEBQoHBwYIDAoMDAsKCwsNDhIQDQ4RDgsLEBYQERMUFRUVDA8XGBYUGBIUFRT/2wBDAQMEBAUEBQkFBQkUDQsNFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBT/wAARCADwAUADASIAAhEBAxEB/8QAHQAAAgIDAQEBAAAAAAAAAAAABAUDBgIHCAEACf/EAD0QAAIBAwMDAwIDBgUDAwUAAAECAwAEEQUSIQYxQRMiUQdhMnGBCBQjQpGhFVJiscEWJDPR4fAlQ4Ki8f/EABsBAAMBAQEBAQAAAAAAAAAAAAIDBAEABQYH/8QAJhEAAgICAgICAwEAAwAAAAAAAAECEQMhEjEEQRNRBRQiYRUyQv/aAAwDAQACEQMRAD8A4SjClgNrYHkjzRVtImzLJnBwc8VDEcyc8A/epNhjyG9pY+K9XvR8/wAqJSFaMtwwByM0bZMhAzgEc8ULb5cFDgL+VGxARoMYx3ximR07JW92epbJKxlbLEn3AUSwL7BFwvbBPavI2IYZXCtn2gc0QIFIXavA75NMT9CZq0ERW4T3Ow3JzwMnFG2qFlbOcEknjvWNoVcZAOR4Izmio9zSDjYmck9q5KxVqJKpCj2fyjuamhSMIjMuSDnI715Gnu96rIvgZoy0Q87lRRnHB8USFylbs+tw8TgqfYTzjvTGIkJkEncexGTUEEZLMWG5B2phbR7RswoB8Z7UwW6MIYCGDZznuDxRYXc6Yf2p8HsaySFYgQ7DOOOaziTjKplh3yKNIXdKiYO7RsoXJb2+6sojttjGxAX/ACnsK+S2O5gQQ/w1TxWXqgBgQwHaiURLlTsGCRpFtikLL54819CEeMhuFHB3URDZvL7n3BAedvzXjLh2zHvX+hpvoXya6A7m2VgoVj6a8gd8/rS64ib1fcSVAGP8tOjAio2yMtxz8rX00ULwKCmcrjPbHxWM3lfYnhjVnjVcbACcAd6ju4E9Pf8A5f8Aemb2gEYKcDGORQslq0kRGdqj+9c2FGTErIZGYnOwjBJqFrY+mD+J6cvbHAXwO/kGhHQBjyQB3Ncm0DSYuSEwqHI3D4PcflQdzEhGF43nnvTiRA7BB585qCaNUKgjleMDxQv7YxNVQmliV4iF7jihRAQpjU9/ink1qANy/hPfHGaGlgjjkzyC3z4pUhsVoSSxm3UqHYE8du9B7N2GJK44AxTt4CWO0e3OcihJY9zjaBjzntS6Q60JwknqErwPAxzQ80bTkq2XIPP2pyYWlAcNhQe3ag5YxHKxUcg9vmgaTdjoOhSYPcdoIwfyNC3SF2PLZAwabSoAxbaefFCyxKiliWIpUv8ASqHQrMSeixwx2+D5oXCSncDjIyO9MpIgC3+XwDQv7sJF9v65pdaKk1VC+ZMMASduMkVhNIHZmIBbjbREkfpo2TuHbmoblVUjnjjJNYtDE2BSsWj9o7VBMx9MHGMfNHGEIhIGePmhW/BhOcH+lDIcnotfpHexz2qeIbVUcqM98dqwRfVcAj2nyKIUeiqITknxjvTUqPPkyaCDLe0tt+cUVHGDICVPBx+dfW7rHCH2jfnk0ZApcE7TnIyxPf8AKjQoyt1WR18N2yeaKEQVwDggHGFqMKsZO5QDnv8ANGW7JIyrgbM8ijSQicq0SjdHtRTweM4HNEx25kUZOcdwDipIYgspAwV+aliLIWRBnnJHejWlombs8WAlwrHIHzR9rahSysB3GB8isQAxUfzNztFGRQ+pIxYgg+BRJNim1E+UYYYUsP8ATRaRqTlQVI75OaihtmQ8AA0aqgPg7TnxinKKFNt7PLeRMldmFJ74zTGDkbOCfFRxWxMyjGD4HzRcVsN7N6ZYDjB4/vRqIpza0YLE0LEAEs/Zu9TrGwkAZT+a156RYqmfb3JHepkU5XnC9wxPNEkJcmeTMZEGM7B2BHmhwIgQTtDZzye9HtAGOOTnwOK8NqqbnC8/l2oqOc7AU9wLMufyzzWQgWc524Px5olkkZR6ZL89gOazSBok3M+VbkqByP1raBjktiuSIMrZUrzjJNRtaosO53YoOQCO1Mp4g5CjbsHbihp4yF28cdzmsqzebXYrNuyPlTuB7A8UPcQDdkr38YptPEX25G0DsCKglhdDuLE/YVtUGp30JLm3GVKoQo8jz+dRSRYY7VDZHPGabzRmVwChZTgChJrciUheeDx8UDQyLFUbbB2xg4yR2oOVY3ky+B4zmm8toQvtB91CSWu47ZCSQcYHxQNBJsUXURChEb3fahWtnPHbAptcQ+kwBQqO4bvQpiI3OFyccZ4pbQ5MUSRMoJxxwT8/lQ03/cHJUqAOABgmms9rt5Ye6hngLjsFAHnvSGURf0KHtxtLYOW42ihXhDZDK2M4wPNNnJCsqx5xwWxzQ3pMxYsMAfI70LWiyDS0JrlEQBRgNnzQsxDk7FKgDnjimU4Z+SoXHbIoNo3MuTgE9/vSaHXsX3EbOqsFBXyRQk0fuJKgr9+1MZ1LkjGAp/DQc0e/IIPah/wemAsCvcA7uRxUPp5JAzk+MdqOMYeJsFgRxg0Opwx3fH83zQsqii0+kNgKcD4FMbaJJIVIA3DO7J5NBFDuIKqnxzg0dbQiJAd3B4p6VnnydBSorxjacAnkY7UUFKBFDA/GKit4zlONpJz80TFESwBwMdjiiivRNKTXRNDCXwWBY/lRlpEzHDMo58VDFG6th/bxnIPajooyhBU+4HjmjSJm72SrCj7xuIOP60ZDbBQGygC+COagSF5MNgq/50fbqxLBgDjwD/zTkmieTXZnCN5Kke0c5xzUkEUpAKtlSc4+1TKmwqQR81Pbq7S4ZsIexwactk0pNGVrGJWKkcn7c0fHCyr24HGQK9SyKEFMNxwfOaKjjYcsf0x2oqoVydGCAAk43EeKKgjaTGSGQdwOKyjtyCRsBz+lFR26/gAIz3oqFOd9GCW6yRtgkYGBXqWiKuSCVXuM0UsLQrtwBmpVQFfdnZj8QPetoFzS7IbeJXcbUBVuB9qkeNCjbky6nuD2rONXXcq7tpxgDiiIkOw4wcHJz3o60KU7YrNu4kDIhIxyc9qyu7UGPjPIyMHFHSZl5VTlu5AxWEylgo2kjtj/AJzWpfYHNRsWCM7MseCMhh4/OhhEzqWY5X8PNOHs2CDDDeBxjv8ArUax7A28ZTviiZqbE0ls9wmVBJHAPOKFlLwyKjEN+XzTqZQq7duAx7/FCyQBSGQgkecUHsO9UhWVkckHOR3Ge9B3kQMo25LHH5CnLw4kMh445Pagpo1OAvI8k1j2Gp0uxbLC6jb2BPk0POgRSckDz96ZSwSKd/c9sk0PLGWDK4JwQcDjj4pbHRn6Yqu4SwQ4IjHA3d6BmA9Tgg/AA7U5mXeBuxgdgO9ByxjJUJtJH9aU4spjJMUSo7ElicJz7qEe3BQkckc5804e2Kv3JBHIIoGWIkkhT9wTS2rKYukJ5lwoUEA4yc8UNJF7PxZ8hc96byIqKfU9zkcDtigXkDBU2kf6sUpooTtCScAHvgH5oRwrKPxMaa3dopOcnvz5zQUkCgkkHPYYpTVlUXoWyKCSAcDHJoOeADG9iQBgYpnNEhHtGG85oRvczZXPjilNUUxlQtYhJANpC/OO9QOjOzefjPejJiGPPB5/Oh5UOF52rjBOeaApjssoBck5yAMjI7UxhiiCRFwSDzgHtQ6qU9uAgPceKLg4iwpwpPPHanrTIZOgu2BikxubAyQG4o9D6gHBx8UvCBnyJMAUzhw685AxyOxNMRBOW9BMYDqDhSAcY7nFGAAjaCQP9I8VFDAVC7fcvfb2olUAYMuE5xg+acok82wm0GOFJGD3PORR0NtsfO8EtnKngihUcx8JGCR3IptbKSu8j39z+VMqiZyPYbctk9/v2ouCEl1ztGO5+KwjQM+FPf8AzeKOhdDsT8DHyexpsSecgi3tsox/CzDgeKlihcL+IEH5FZBWUDAJUHHaiYrZllQqhIzng0dE0m30YhUZ14KnzijYEMeW3sFJ7EdqwgHukQqSF8kd6JtwHk28kfHxTEvoXdbMhDG5L/iAH9akigUDBQ/OKnggDREOhUZ7/NExgYOADkYAxzXNGd9gQgIYyZKluOR2FZxIFO0YXIwR5/SiY4RJuPO1fDV5sd2Yhgg7YB4pgi62DTxL6BA3Jg/hBqH0ym3cxbA4xR0turshJVeeSK9khCYCYf4rmqAUrYE6hkBAIPYnzQckDO+CfaeCD8UykQtGNre7see9Yi3K5ckEnjC1g3k3oUyIH3qEyF45HFYPEI8bl2gDHBphKm1eQxC/bvUUsazorgbQOwOKBhddCuZBIQuGBb/MKguIQgKnGPkc0xa38/jyOT3qGaIEFSCh8eK5oZGX2I5kB77+ORx3oV7bLg7VJJyTmnxg2IwfA8/NAtbgybThh3B+9ZQ1b2KbqMM+3KgnzjtQk6OpwgBwOeM5pzNAqPz7/nDDJoM2wd3VcjPYilMoixLcBwp7KT5POKXyqVJyc7qb3cZEwQ7gRxnHegrpmJP8wBxxzQNFSYpuINjHd7u3JPag7lUCgLnOMfnmm8uJCcqMn5FL7hViJCgkH7cikSVFMWI54tsijdjwBQkijLIQoyO5OOKazW7MCG787SR2pdJFlwuCSB5FJZTFtMVyKChG0jxu/wCKFlcAkqvOP/nNNJ45HIQrgDwKEkQqwXG5PmkNUWwdix0Mh4zk/FDXBZQq8Njk4pgiM28e0Dwe1BTK3jutAVRbrRZ40YgbSCPvRlrMjhVOcHjg8VFbqJZFAIUgYIo6K3jTAUZYcEYqlNezzZ7QTb2yyuMcYPAHmmltblduUCY5zmgIJMHcQFA7AUwgUuylWwp7/FGtkcml2FW6bhkg5btyaOgQkAFCB2yRUFsFjiJJBUnj7UwhTeqhuMnvmmr6ESkTW9moaM4JA8H5o4xOANjbQTzgUNHv3DjkHjafFMbceopG33dz9qeokbmZQxgJmTBB/m+KYQ2+IwV9zdxiobdNm1jk5Pk0fa2wLZYNljkc/NOWuiWTVkluG24lwCfBPemC/wAKIsMNtxnFQqAHUMGP8uRzRqRlTsOCD3ycEUxCOdaR5aBXkZiD+vz9qLhjctypyexPNSQQJtAIA589v1qdlaGQYQBs+OeK1IS5s+iDA5HC/Lf7VLHCJW3Z+44r5YhI3qBtp888Vk6NDhgcgDAxRNC+bQHclVkOGChzg581C7YfBIVRziiLiSPaG2njjceM0j1e8S3Jwz7gcnB4NHdIS/6dhtzfB0wnOMkjwaVvrgC98N9jzVa1LqBVDMZR/Wqre9XojNlwSO+KVKfHsZHFLI9I2bJ1AsafhCt8E5qMa+uCGILnknPatQydbxYw0v8AfFex9aKOVfnvzSfmgV/qZX6NwrrEbHjLcYBPFSC+iKCNWAHnca1Nb9ZrKeXGQOQKaWnVm0g7v1zXfLBmPx8ke0bIJQLwTntgdjWEsak5Pt2jAPfNVC06oVnAcmTzjPFOotZgmiVdxck52/FGpX0Dxkuw+WICN2ZPHG6lzIxYJIVGOAFNMheieIqcoe3PxUM0a7VIG5s9vNd7HLaFlyBHlAhCjzQLAKrsqfmTTiYB3x3wOCRS6WN2kyxDRnuUPNKfY1OhVc25duwPxgZwaCYIVJYFMf3pyyKZeOFxigZY/TkYjDfkewoXotxtVYpe243YHPyecUvuIijtgDAGQTTe4fazLtyW4H2oKaIemcD3ZxkmksqiImQzSlGO0fOaX3EIVSpbcuc5BpzLtAxjJyQDSu6jwBsG3JxgnNIZTGVKxc6YUBSPuTQTtGQBwxz2HimPp8MBgjvzQTLGWkPGQewpbRVB2rFshG4nd+hoOYjG5skE8HsKYzxhSSMcnuaDfLL7ecnHFKaK0yyRkbW2bFbnk96PgydjNyAOTQMMDbyXUAEds5IpjasirtOceDTzz5BCQkSjYVyV4z/LTGDcuATkYwQo70PHwwdF48DFMLYhmyMJ5yeRRIlmthttFkKuQoAyS3FH2yPuOCQR3x2xQMSYfgZJOSKZ2rMkRGBk+Bz+lPimSSdOmHW6BQGOG+9GIPT3ZdVB7kck/ahUYYQMCue9HRou4Ln8vNORNJoJjtCI0Kl33cimdvFsVAQSAfn+1BRTqmEw3P6HFM7WL1DuyCoGAD801E02mSWtsq4zHjnsTjmmcSBm2hfuDUEShVTcmfvntTGEO4ZcKvhcijI3o9gIeUBie2M47UXEpDEr7hjnHxUSQYZCMDBwcc5qaOMxyyvnI77T4plfQrlR6qlGYchT2H2rGX+DEFJCg8c0SZFMRLEMV7KBzSTV7gnIyQAeR80aAk1VoC1eT+LgDK+dh4rVvXvXFt0xbyFo2nnf2RxZ885J+1WrXtdECOoJAHZvmtB/UXWUe59SQrLJzhQe1T558Y6Zd4GH5sitaEOpfVC8u5GEkIjXJHtzSG+6seZZGDncRxzSfU75ZdzKoUnvjmq/cXIkbAYjivClmk+2foGHwsaWo0NZupLiUcSENmiYOqLlYwfVPfuTVZbA47/cVLHC7LlOeM8VMps9L9eFdFvg6tmD5YlT3796a2vW0kWCZP71rmaR4m55/WvI7hsZ7VqytMTLxIS9G6dI67Z2QNIBzjvV90bqpSAQ4OeDnvXMEN9LEw2nAH3qz6F1fPZFVkf1E7YJ5qrH5DR5fkfjYyVxOp9P1vfGFR85/mY5NP7S6Eh2yEbe+4djWmumuoBc28TxtjOMA1sLSdSJhPkngA969WGTkfJ5sDxyotUsgZgoX2kcmg5bdolGwKFPjvWFvKZYu+1j8HNZyD0/cZiVPFMaE2mAyhi4TYfzoK5hRSQMlh2x4o6WTCuM5zQU2N+DxleNp7UA6M6FsoVgUAJccZHahLqMmMDOWHcUdNCsRLbmL/IOKDkQq5cnJYcY5pMl9FUJCbYscjFyeB+E0Ddxeoc4Gfgim8i7gVbIJ5pbcsp4OAvbJ+aQy2L+hbcBYwoA2nttFB3EShQQNpP60xcENkYGO4AzS+4yX44+3waVIrxvQvmjLuSzYx220snIiJJxgHgnjNNmIAYsVNL5pGdhtRRg9j5pRVGmiywxBSQcKT5PgUwtI1A3bw2O4oFHAU5QMDx3pharFGAE9/HJpyPObDIXQAe0sc9+2KZWxVl9p2seM0AiLnIPJ7CjbVCXGRtH3pqRLNtsOAdZEAAYg/rTNduQv4W8YGMfnQESkEAAd8hs5o4uqsp2hm85p8SXIwyOJlKgMHXyaaQRGJ8rkgcn5FCxsjqpBAwvGD5qe1IRznODwSOaetEkmkrGkWx23sACw8jOKYWgO5cEn7DzQcKIrKN2V8k80XAnozsv4wezKPFGtkk2MrVnLkswI+DTO1R87iMEHzS2Lb6TL2bjkUwto2aPJYlgMnB701IQ/wDA+JkwMx8E5Hfv81LLbxqwG4tkZIHg1hHKkQ75f/UOBXs0jCNy+Ec9sHiiSF2C385hjyOQoAOPNUjXtX9FZPUOCfk8061y92wlB3ByxzjitFfVvraXTS0FqQXKbmdv5AewoJT4K2Pw4ZZ5KEQXr3rKO3tJm9bfGp2nB5rQuu9QG7uZJEJ2kcAmo9W124vY5A8rOjHOM1XGdmPJrwc+d5D9D8D8fHBHfZJLcSOWyeD/AGqEozOAAT+VZpG7k4J/WmtnYhAryDfnkKPmvM5H0UYpIWw2UsoG1T+VFmzlgjBQEH705EVw0YaCHYAMHdWP+G3jHlAfOM0N7sOr0KH01pe4yx5zQ66fJvwoJAHfFPptPuwDtKr/AL0E63FsdrDg/Fc5fR1MXXMHpIM5z96iilMeD3NNJB+/e18Kw7E0suoGtn25GPnFEpUC4pl36R6nezZEdh6ee4PNbr0LVBcxxMrZUgHOa5es7oxuo7AVuD6f68Y7VRNJ7RwOa9Hx8taZ8z+R8RNc0b20zUN78KBxjLcmmancCpIYHnPete2nUltEqlpc7vimsPVloqkCQjHYk16qmn7PkJ4pJ9FiuJNg2qoJJyTQ13JvdWZcAdtopDc9cWfyCQMZ8ik9z13bKWX1VX75rnKPtnQxz9IsN07B/cML3x3IoK5IdRtIGOxFVa468tsEbw/yT8UOnW1qxC7wMdsUtzjXZZDDk7aLKzGSDOSQD2B5pddMpUtnb8A0AnVNrIOXG4DwayXVYJSCh34+TnFTtosjGS9ELSs8m7wBjig5icb++T3NMpLn1BtQgHuQB2oGaIL/AEJGT/vS5Mqx1dgEmFZlYZx48mgrpW2jC7R4U8mjZv8AyYxwfI8UvmkPr7Q2/HkClFaZaYocTBt24/PimUacIQobByMdqASGNZBmUEDxg80dbPGwAycNxwKciGemHwJnksMfGKM3bQnu3c8/FBKy/gIIdfAouGdUQY2gnz3NPjsjm6GaKPTACgjuCD2oqNY2H4gxzjk8il6TkYIG5R247VRNd+od9YapJFCixrE2PcMmmOSgIjjlmfGKNtwEghOCRRlpIruFQ4Kt7s9h+Vc9X31O1WefZFJhzySOKDX6manag5uSx+e9C88Uxi/HZpI6j9RQuU2Hb2Oe9FWV4kpRcrvHdt3auTLn6m6qQAJ2APNRRfUXV3Y7LuUEeQcZrv2YxBl+JzSOyUvFQcurc8HOaaW1/GsS7iuFPG7vXMf0o1DqP6gdQrYHUXt7SNDJPcbM7B4A+5OBz9zzjFb1h+mVpH/5+prnuO7qD/QCi/dxx7BX4XyHtNFygvRIXZiMeGB7ilt7q4RyqncD+XFJZOjtCtgwPU95hR/9txyfPBFVXVLO1tnkW01y7xj2mYAjP34of+QxLsB/hvIek0NuotSMK4ZyeMkZrlr6udVHU9auFgXEafwmb5IJycf2q/8AW3Weq6Imxp4rxAPc4XaRWhNX1A6jfTSvn3ksTSPI8mMo/wAs9X8X+NyYct5EL5JztwRkfIr6zRZZgvfP9qxdQT55+O1FabEizKCd2fjuK8eUj7eMUkHW2n4lA/Ex8Yq06VogY7iOQOPFRaNphyHIwT8irRaptGAMHtmppSodGNsHTRd0Y9uPJ+9TjQw+CAVwKYxpM8OFGcV9G1wo9NoyD3wKRzZUsYju9NA4HJxmk91pKlfdj8x3FXCe2uCCVTvxzS240e4YEnCj7iuU2ZLGa+1DTJbfLhd3xg0vlCzRMJSN/jB4FXS/tmUgYyPvVa1LSA5LAgN5Ip8ZX2TyjRX2Qq42gceae6HczySCJZCinsS2BSpo9o9PduI+BRWlWrT3UcbZXJAyBT4y4k2SCmtl2tYbj/Eo7RnMzuM/w5uKfabp9vqmpyWSNdwTrkFCc1FrnRcfTOj2epRGVppHATc2cnvzTj6Y6rHD1NKuoxr+/TDHruwAUd8frTFmlWjz348foYH6aiSPcxnfH3P+1ItUi0HRJXhm9syD3LsJYmt83EJMbbfdu8qORWrPqb9PILjT7vVIlka7GDgfHH/FLeVy7YK8deka6fqDQYGJ9B3J7DaKgj1PQb2Uho5ID4I7Uli6dmubZrl/TSFW2bN2GP6VLrGn6YLVWsC6SrwyOc5NGpv7Hx8eI/bQYWgMttcSYbttbINCFb3T2BWcsPIPFB9IajPYTFZt5icfgA5qzHTU1Znmglb088Ka55XFi5YL1RBpmvu7YLH1Bwc+aa/vjSEoWO3vVP13TptNk3Rk5Hb70d05drq0Yj3BXBwQzY5qmOXkiLJ4tPQ8kEfqqwUnjtnvUDrGuGxjz3zTuw6ZM4PqSAEHxzijE6VtDFmQuD96JzQMcDPIdryAnGB35xmmyqsUSMgIBPilsEe9RnsOOaZQRKYVUFt2e4OarTPLyaDQqlgVyD/ejYSke0hO/lu9DwRlGGAcqKJjJYoAvY8gnvTk1WiKfR7NcrbxNIcg/wBK1R1fqEd7qUzrGMLkZA7mtj6/ei00+c5wQpxWktY1SRi+ZCQMnaPJNKyySWy3wccnO0LLm49IEjmTOGNBCZpfaQT8D4rESl2IycHua+yzSHaM/rXlym2z6mONEjPk47uP6YqaFstgfiFBMpmfgBvHtNXj6XdOrr3WOnWzw+tH6gLq3baOT/YUMptbDWM2b+z9Z6ri7nSEw2D4zMykeoRngHzjn8s1u4W0l4R7l++DTTTNDjsdPihhi9KBeNiDAxTK20iFOY0PHbJxikOduwXCnRWZ+mnlT+GOfJPBpNqPR+yN3kdjxn2iths0QnAYkJjHArDqGw/etLJhflQcHFKc0+jFj/w4Q+qer3MvUV1bAskMblApP96pKQs6MxPHI5FWr6llpusr/a27MpB/PNV+7RYlSNCSSMlvNOi3RUoqArJLPtGSB2p3oVv/AB0JTg/1NBQW244RcsTjNXvprRQCksnJXvkcCsbpDUrH9hZLHbCZxgAdu2KjGs25YrEjOo70+03p64126Ks4S3Ue/wACtvdCdNdN6GVe79Nyce4gHP8A6VJKSb2XQxtrRpyw1+5SD+FZN6fblSRVz6VhGvIqS2jRuQcso4rcPU+vdP2VuqraW8kZHtAAziqfqmu6Zp1ml5ZskSnJ2DuD8Ypc+PSGQUgH/p2yik2SqBtHHHIrV/VumXb380NvuCjt6dONZ62urhvVh3Z75/DxT76b9R6VdJczakR68WCsXGXJPn7ClwvkHkao1dJ9MdbuLFZwkyI3Z381VNX6a1DQspdAyIedwB4rp7rnr1G0qOZdn7vnYowBz8VquXULPXkeGVVViCRntVLnxdUTyx8o3Zou6tik4Ycg8kmrr9MtHg1fXII5YwHDgp8UF1Ho7WruuzaoOQR5FQ9F6wLPXrdgSGEgxzx3809u42iJ2nR1hqXSNpruhJYXSKyAZEgGCCPIqgdKfRL/AA/qa5vb26MtpG2bYIcljn+bjx9vNbh0e1bVNItZFkC7lBwRjOaOg0xolywxg4AHcmp/kcXVHcE0IY9K9M7y5C9h81jewLGuX948AeKd3CRxSAyMA58fFC3VsJomJUS47EcEUKaXRna2am6s+nFlqcUkun2kcV854bO0E+Tx3Na51foi20jfDcajbrdqPcgQ8H4zW+bmRl3hVMa8gHFVi+6QtrmX1ZrdGYjuw5o45l0w4xpmo9I6KWSUSfv0bpnG1G92KtmldNtYQukTtKG7ZHanun/TK2tL5Lr95kcZ3elgKDVkis4ocpEoXArZScugZtRZpLrK7it3ktSrLKBg4HANa1t7u503UfWjLfiyxroPrPpWHXgGj/hTJkE981qPWulLzT5mjeBmz2ZASDVGN8TuEckbLd031NDfqrb8ORyAcVaF1lZDsGW4529q090zEdNuJTIGU9sn4q76bdiSMiNwB8mqOVkMoOLLVZ2N68fpraSyFs+4oePypxadPagFCrazBm/mOcH/ANK2lbfu7YKoEA545FN7e5AC4QD5FVfLs8ufhp9s1Xb9G6tcABYthHGSf+ac2/081L0VLPFEO3LE5+a2MQjvuDBTjtSzqrWz0101f6gsazm2haQKexIGcZo1lfoX+lD2aI+r2j3fTulxtJMrxvKBtTxwTWj9SLSKhJ7ncQPNXPq76o6n1tbCK8VFjWTKKiDg/nVK1OQCcqMgAeanyScuz0PHwRxr+QV1yS3K47rXsEu2J2x7hxnGaxEoJAbJ/LzWaL6WFwAoqVvZ6KR6jBcEcDP4fmt9fssaU151hdzCNisVttZsZ27mXH+xrQajd+ELn5zXRP7KeoDStT1WaSZFUxorB22gkk4/2P8AWulpWdTZ1PcBLGMAANk8nGMUbpUUOqIzKyOwP405U/0rmj6y/XCXX5bvpvQY/wDtZcRPMoJklbIyF+FPA7c/ka2Z9F31jpLpKz0+/UG7nYzBH5aJTjCt/c/rUkpOLTvQShaNov09amckkB/8ijio9asootJkjbaQRwq9/wBaDs9cebUoleSJQ5xtXv8AnQPU31X6J0+KWC71qAXMchheOI7yG85x8c0lSjNuUEFUov8Aro4B+pOlvp/XGoxTjAEzsCBgEbj2qtf+Zy5XIrov9qT6aRaZfWWv2svqWt2uzkYIY5YfpjNc5pMYJSnGfJNXQ/qJklsK0qEidWwO+cgdq2v0rZLcemGxjI5+a1lZSx71ABckgcVtrpu22W8ezIYj8PkGgydDcSuRdm1fTdDtnhypkYcoF5ql3us6nqWoEWdunpA+3L7cf3q1ab0WuqyCWeTJ/wBQpwv02syoEZJB4O2vNuns9am1op93davLa6fJfXFpdMqMv7natlyfBbbxx/8A2lk2k36R7rhyFblYSeFzW4+n/p9a2MG5Y15ONx5NDdWdDyX2qWVnZne5T1Zm7hRngZ+eDTnNSqjFgkttmtE0GW8skK8Hb4Gaps63Ol6yhhco6N3zzmuv/pF9O9H1iS5tLq+ijEEbMzgbjuBA29/vWpfqf9K7Rup7iPTJwsyEssZPLD7iuxy3ZmXC+JqzXn/xa0t0uBdboXZ0LTF4wzY3FV7LnaufnA+KVPYvbKklt6kj+SfB/Kt06P0aLnSImnhyw4YAeRQd901HYxEiFVUnsw5rZ5FJ7AhgcUaR1IT6g2J1KuRjiqHEktnrQXJRo3xit2dR6fG75iQKy+V81qfX4JIteyFHuIPFPxtNEWSNS2dw/TQtfdJaXK5WQyQIdg7/AIatgs3ZWbPvx+EdxVF+iQZugbMod0ioRuH5nirtYJPHNlkZR5Y1PkkoOmLguWxZPonqSb4zgnkh+5qJLWGMOqY2gfxHOM/lVrntlmQhV9q8l8c/pVQFpsunHvYhjuQH8Q/KgtJ8gqtUgWSO1LuoBGftQt5o8cqqcbec7gKaxWgWfJTYn+ViM4qLUry4jXK2yiJf8x71kZO3ZzVdCGaFY4mJxgHATHNJZJh6hyBn4xVn9eO7UsVxz3Hiob2xheJWYcDs+Ko5WtCmr7Kfe229GLJjHYnxVfvLaPcQm2UjztwBV11O1C27SlxGmMg571rrrLV5dGsP3i22TxqcMAa29oBWtIreudNwASXDJ/FJJOKpy6gLGUrsfOewFXPSdcXX4GeNi5P4oj3H6VVeqLD0rjIbDj+U96pjJrQPZ07HqCROIsFjjmnGmzB13kBU7YPmgbbT4w4UqPn2im9tEkQUentHjirkrIUndtn0tzHE4cKQM8c1VPqTfi/6R1W3VyuYHPI7gAmrXeqigFwDGPPmqp1Gpi0rUJmXdGsTttfjIwaOqM03RyDEjNMFxtw3JFCXspllbBycnINMYCq3lw+eVycUslYK2eGYnnxU8pWyuCRC26SLnjBwKziHLA/rXjIFbOQvz5qa2h3ux4xSnQ6voyUArtTG4/NXn6eadrerzzQaTaS3ARd8pj4VR8sew+1UdEIkYkAL4xXXf7GMqxaHrkD224PNGTJjudp45+P+aGTXHZsbUtCDpL6aawttba7aRwxXRJbbOMOpyR2I+x70yu/qfrGmapLbXXpRzRZR4yMbj4574rekkETyPIkRX1ZMkbfv3J+a5n+uPSl3o3XNvqEk0cVrqshaJMncirtBJHxzkfNeepfJPg+j01kioXJEOgza71J1VMs2pyWhmD/xnkwqAjwP7ACrT0H+zxb3vU0U/UOpi705X3i2t+PW/wBLHwPnH9R3q6690to1j0VPe+jGJre3JFxwpJA4yfJJx+ZNV3oXoTXOruix1Do9+3qLK0L27MRtK/B5+Qe3mth7+P0dPFjlpvsuf7VmkRXv0tKI4hNnNHNEOMMc7Mf0cn9K4S03p+fXNdjsYwVkcksfOBzW9vqtc6/FposdRe5Dod2y43D+xrWXQeorpXXttPKAzICOe2SCP+asjqNiMmHhNRbDtR+nr9NBJGkYlGG5SwJq7dNKzCIZA47/ADVh64ji6nsNV1mErBCGQBBjvgA4FVjpqWSKRDuJGOM9qllNyjsZ8ahLRsrSvURlAc7ce4YzWx+ldIMixs4YIwyeKo+hKCiM4yvAIB71svR9VjsoRswABjHeoLt2evFUNb6zgsrZpH2gJjAIxmtW9QddQ2FxcL+7kxyKUJRsMB9qsPWvUc9+iWtsGDEdh2NUK46Rf1DJeTCMHknPaiVt6BlOj3oK7stP1SSayje3MpAcFtxYffNINZ6kh6U6uv8AUJLVrq5mYkkv57c/pVis9O0wHZuUbeN/qYYffiqrrWkadNcMHl3MxIzuzVCjeyXnei6dEfUu11O3a3ugqbyWU47E+KY9Ry2TBUDli4wp+OPNafk0CXS2E1pIQFbIHzVh0/XJ7iALcD3KMEnvSJRcRscmqYi6mttk7EAbB3Na21+0Mmp2mwAM52KT45rYvUc3rSPuG1ft5qga7MiSWrM+CJAQ1UYWyDOkzsX6T6bFpvTtjZR8vFCFZufc3k/qcmryYnMjR+mA3ZnFVX6M6xp3UWgQvpr+o6qFmJ/Epx2q4XeppbXQjj5CZyRRyqKuRF/6qIq1yzuAAsIOCMll71XbfR7ySQuImypzl2wDWwIH/f2DJlDjkeKDIa7mCe1Ixy3+r8qncVPdjVLj6KWLGdg5WRVIGdpXPNe29pFqVqxmdt/ZmBzg/H2pvrlnNHJviyikYJ8Ur0m0miklfIEbDLljgGigoxdMCfKStAY0mGANyHQHI57ChLtQiFpcKnZUzj9afNbpIXaPCp4QnvSnVoPXAXB357Z8U/io9AOV9la1S0g1CzeHAZG4ZGPBrm7qG+u+lddu7eWNjp8jnbG7ZXb9jXSN5o8qyyOoJbb+H5qhdT9K2nUVnJHOnLD2lx+E0v5EnUkGoqtGpdKt20bV47+wYfudwOUBJx9qW9X6jJNrIlDDgc8d6a6xGOnLafR51aWRWLRSqcYXx+tLNDtLe5kea5nBlwAqyHOfvVkfv0ZGJ0GvWl8CAkEY4/m55r2XqnVZ4lEeyMg5G6qykpWVR+L70et9wqnv/vXop70eZ/gbc61qjNmS7Ypn+QVVut9euINDuzPdTEOjLtzwcjGDTh7lnDYDL4GewqjfVS5mt+nFRhgzSAZIx965gRSvRqe2kaWG4kICUvdlYKQACfvRltII7OVnHJOBQcqqw7ckeKQ+y6K0ZbQyjjJHipFcq2VGD2+1YZ2IPGeCamiChMns3FA+6CokjOJhgeMkV2n+xPpcVz05rM0jEhbheD2HtHP/AM+K4u3FWGATiuz/ANhe7STS+pINoaSOWB/Tb4IfB/saCSTjsKLadnTtxY289qFkVYbf4I5atNdefs9Tdd/Uiy1m9uxJoUUKoIOzjaDhB9ixyT9yPvW6NVtJpF3qxIPOMVno3qPlGQlO3IqByUZ1Q6Kbj2aC+vGiJ0x9P3s3hMrXEi29vHGCfeckE/opx98VfP2fOhb3or6XaVbX8At9QlLzywMuGBZiVDD5C7c57Yx4raNzp4/eFV0WU/6sEL+ZqZ1js4y5lMj4xu8fkKbjisaaZs5uVGiv2m+krXV/p5qOp/uoF/YKGQouNoLAMTj7Zr87tQllsNQE6nBDZzX616zptr1Lo93p8owZ4WiPOAQRjn+tfmT9Wui73o7q3UdKvrYwz28hAyPa69wwPkEEGnQmpKkDJy1ZatIsNb6g6UudStlMekxEF2YgbicdvJ7jtRHTpZCiYOe241XLL6zfuXRttoEVuUjXCvzwT84pxoOooJU95BPINSTXZdrTs29oUqpEFLABRnGe9XGyaOS0YqTuPPPita6HqCupBPfjP/pVx03VUtl2FiSBgA1BK0z0oSGRlhsEnvJ1DOoIRMZzitXuNb6hvZZZS0au+Qo54z2rYs9/HqEixlUC+d1NrDVdD0T3XRHpkfhVR3qzFJUS5FUrK9ZdMaXp2mr+/TbLtgCEwKrvUOi6UbdjbyEy85BwcU+65+o/TMnustPmuZVOA0hC4478VT7H6h6M+5Lm1ZGPIY9hVL0rEqVuioRXN/p9y+7dPAxxlh+GmUkiyFpE/AV520ZqmoWF6XkhdGRvA5pDLN6YcKpAPYCp5y5BKPFg2oXOVZW2nnuaQFbW9uDFcRiSMr3+9G6lepAMt3wTVD1zVWR19HcST2HFbjTFZGb9/Z46nl6ObqMPdLBp9rAJsyN7c5xj8zkfniupOnY7Xq/Q7LVID6YnQS+mRjOfzrjL6UfQXqv6im1uby5XTNCuCsje/fJIowRhRwP1OR8Guz9S1LS/p30qVdvRtdPtshY8blRFwFGSMk4AA+cUU0smuyJXGVjcaeLeTbFkAL7iBxj4pVf6jBbu2/bFGqk7t2BgdzWmtP8A2rbXU1uYP8HuEVVYQsJFO4jON3HH6Z/5rX/UH1a1nW2kjaURxSAgxgAYHxkc1soTikoopw4/lbOlIdU0/VImaCdLuEeQ34agkWKcN6GAijnA4zWjfpH1JqiXcel2EHqxzS7rmZwSI0x8+O1b6haEMEttkZHdW7VrjTTYvJHhaK1qUhDARqcZ8cCoTC8sYDIVBGc+as9zZI7guig+AKW3CJJmOIEKo9zeM/ag4y536E/zxKhrIkisbgSSOsIUj1gcEDHzXMdp9QdQ0m7v7eW6F0rOwR3OSvPeuoOvZLCHp2//AHr1I7MRH1NvfGPH3riq70+W/vb2TTIbie3gy+8jJVf9RFP+OM07Bi6pjq2li1/W0F9fJCjklnY9/tTbW/pmkjCeyu3WQDIJOQ1VTprpHVNeM11BavLBB72wCNw8gHyftW6+nOlll0C3uY3cIV9qyNkj7UbTSqJROUaqJ5BaK0W6SXPc4BxR9u8TooVWfHHPzS2GNWlHqZYgcccUe0GxwVO35AFenSR4Nv2yaaQAEKoGBWrPq1ctJZWkZYvucnnkKK2XKN0bgthiePvWq/q5chLqxtk2g7WZvt/85rmrRsHcqKA0Srp6f59x4oX09iDJ5+BRUylLGIF+dxOM0M43/hP/ALVM9HoxRgp2nkgknOKIDHb+IFM5xUCB1I3AB/kiplA3g4zng580L+wuiZA5yw5GPyrpT9jPXbnTus72FMsJrbe4z32sMD/9j/SubI9vpMN3IPAHat0/sra1PpH1X0gRDKzl4pQfKlScfnkCsmv5Z0e0fo9oGsQ6wHRYylwq52MO4p1Bp4niK2wVWGNzN2Gaok+oATRXFmDE4BBZO/5Cr/09cK+lQs5RJyg9RAw/t81Fjkpvj7Q+UeKsDkkgtn/d24DcE+WNYS2MZRk2hkI9rE8ivdasGunMtvluSM45FTaVHJaWjvdyjaByT4rG3KTg1oBqlyTALfQDCDI/thAyWz2HwK03+019H7TrrpGa7t7OOLV7Zd1tKV2uwHJDfOe3963FJqnpe+Zd1uc7QTg/Y1RPqX13+72folkK5/Fuye1D/Pjwbo1N5Wkflhqmh3VjfSRvG0ckTsrxsMFSDzkVd+n9TP7pE5OSV/2qL62XEcXXd5dWx2W1xI0gC9uTk/3pN0/eeva7d2MMSBmnWpxUkGm4vibT0XV8mMKe/erRN1QmmQl3UyELk47VqiDUmtAAG2g+R4r7U9UlurUYnz+YqSWK2XQy0jYf/XIeIylhEc9iaUap1PcXls/pSFmXkgtwK1y9+8agbyzecnvUjai/pqcgc5IJxzTVhqqFSzXpm1OltD/x27SC5uUj9m9znOOM4ofrrpm00qNRDKWxnv3PFUmz6qe1A9FikoGCQcZrPqDq661OKJJ33CNQCc8mqPjEfLQFaarPZiUbgEX5NEv1YSY1Ycv25qpz3X75OUifJPceKjnkZmWMHBj8/P5UHxbD+W0P9b1gOFXhj8VV571f8RtA+MBwSv2r24ugEy7EMPJpaLiKS6EjjkHg0yMKBk+So7o6B+vPT/TnQMYW3/8AqVtEqQ2viQkcH7Aea1H1P1lqnV9zcy6nevdNL3B4H5ADgD8q1j0frmkSXDJqlzJbRKh2mP5+9O9U6qWx0KKHTXRridz6k4I37R2GT2FMqMFaRRjePHuey4dH/RvUtcBkivP8Oh3cGZTls+QBjit26J9Gem+lLeKTUpk16627i0o2oCPOwH+xJrmTQfqtqPTSAXGqteHGPQMjMF//AC8fpXl19edZu5/a6JGpyMZz+pqGWXJya9CnJRdxdI7V05dNtdDi/wAMt4rWJxuMSgIfvkDsaDgxJdnZIEJHC965Ftfr1qcMo3EH/NtbGaf2X7RJjYPtdSTtyRzWTxTnTTE/Krdo64nmE1r6O8Fl7txxS6X/ALZVAYOnYsO4rQdt9fItgUShiwyTjmrr0v8AVOz1RlBkJB8Cnv8AmSQm7ei0a/oEGpwS28zCW2mG1h3BqsaL9P8AROk4pYbO2RVl/GpJJYffNWm41K3Lh45GcPyAPH3pZd3HoNLMx3b/AOZuaKMKkDKetildBtrNXMVusERz/DVcKtJ760isY1SBQsI52rTSbWtxKyOvIxgUFezJtBUgKx53eaO09JgNt7KBbIFG5+efbUgddxZ2Bx3+9CMrmHJIG0cjHehrRxJJukJSAZx5BNeoeZd6HLTw7BleTnBFae+qbCfW0YFW2xgEEd+9bOkuw5UJGwXHtzxWkuvL15epLnJyFIXHOKJ/R2P/ALCm+k/gQjbxyaCJ/i4GOTRF12jAHAAB5odtqkEYQfJNSM9NdWZs21lBUZHms1QzADGMng14h38vg4Pc1kxDcjIIPYUDCJowFYqBz5Jq8fS/qFOnettHvnlFukNzGzSH+Ubhk/0zVAedt5CruY4GKLtIpVnDyttx2QV1m9H6ldR/UXpTRLewY30PrFlLBG/k8k/+9UrqX9qHo/TdwspZHkQe1UOQf1rga41+9mXEszAYCqS3IAGAKFF9I3tBYsOxzSHji9+zJZJJpejtW6/bhEJkRNOUtj8TPjdx5qt6h+27qN1IVjsIFCD2oxOM/J+a5Je5n9TY5yQePvULyMGYttweMDvRpUJbdnSup/tfdU6gxSEWyB/Cpgf78VrXWPq/1NrV6ILm+3xNySwAH9q1mL7933NgnjHJrJdVRlBIyCPNZKPNbDi6LJq1iutnfcyLKR5J7Uj/AOlLjT2c20gAPOCe1LDc+/MU7JznGaJXU75R6vqsyjx81iioxpBc5N2MYru5tjsngMgx3Wi5dUtjCQoKkjGCeTVaGtXMvChlbPc8kU30aWAy+rdI0zA8DFDxsdHK09gM94wk3BXBA4wuaGW7e4YZcjnlW71dLow3SrNDD6RBwABQj2Wm6w5WaL0p8YBHmjWgJTTYLYWDTXKKibwV7/FLtaWW2umWQZ8HB5qwDoqUJmK9kjA8AHtRVv0MoBkupfXA8En+9bf2cpRKRaqZH/hRk+expuNGuXG+RhFGfOeaf3d9p2hoSgTdnGByarWp9Rm5JEScdsVr2Cp7Br6ztLYMWPqtjGGNLoNPEzZO2JTyABXsG+eQu/Jo19ojI3YYcmsDUmDzpFEm1cByO1AMS0gUs4A854r2cbpwVGPjmsdm+QKSQT+lYzeTfYSoTO4/hHmpY0AkJGCCO/xQ6IQmMjj48V6JiAQCDnuRQqKMbClQSMSx7V7GfTTPcg4NQibYmCQT5xUeQ6Hex58VoIebyWMqVz+lO+nerrnTLgSJIVAOc5qrF2TahAwP1rwzGE4ABU8965xV2d6N4331wu9O0kC0KvcOMeo4zivejPrHrEt40utXYl0sjawRB/DPg4A7VpVLxoyoJyPgHzXsPUVzaiSPko/Bx8UaroFwtHUGmdf6DqMzpHdbxjO91Kg/qcVnqHVGlxcvfwiM44WQH+mK5Yg1yeUrAHJUHjLYq46B01Jc31nNKQkIIZtjFg1A4pbRqTqmzbcNm87Frl/ZjlV4oxILeKMbANo+TmvTchgAAVGO+azS2/d4QU5VsnGe1euqb2eLf0A39yBkR4VsY344xWhOqQG6imBYSMXNbO+pHUQ0PTNkDBZpSVA+PvWnY3aUSXEoJYjjd3yaVkdaKsEbdkVzseeQbtp7Y+KiEW4qBx96jwzyc5Iz5qfavprk5/5qRumegZK6IoVh4x+dDs7mQKoIYHwfFfPLlwBjJpjp1phdzAM3yaxujn2TWtttCnbuJ7mjkhSOTKFjkf0ryGN4WwwIzWYIQ5PAPApdmNmDkE7XB75FekqPkse9eXCEkBTgHyPNfQ2zZ3Anbn+taBdmA90jZXt2+ajS3ZwcrjB80eFG/BGMjuajkO1sHgGhNA5YExgDI8ipIRBIAWTCjjBqWWHcA4PA74oWSLZkqeSP5qKzkyT92hckRoAT5oqC0XYE2E+eOagtxsXcMMfIoqC6xJ7hjI+cVlHWkH2+mxyRllUZ8gipP3BI1yFG0eBxihE1f9yzgA47YrI6wHh3kEf6RXIGyxaZapLYyDgJnIzVY1QfulzuiOMEkFaa6Vrcc0RtpV2K381CS6c148gjfO3ux+K6rMIbbq+SE7T7uPw5qG76nvLrKRybVbvilF/AbO52lcqe5+KhE6b9ozjwe1dVmcbJpIY5gWkJdjQV1GZMLu47YAoiZvTG4naMVHbRm5wxU48c961aGpUZ2SIFKjuPmvSCjE5xnjHzUrRekAFXax/ShZkklf25yOM0LNImMe9cfiz3rxvc5Jxwe9ZsrKoUj8ziso1eUHauNo7Hsa0I+X3yYwpwO9erAkZwMHd3rxD6ZOFAP3r1pCyglPOMYrEd2YPGGZgh4Ar5UJUHapxzk14CwduCi+alUrJn25P3ojiF2DuQBg+a8ADuB34xUjr78Y7DvUJZiG4K7eeKz2YiQoFYMDj7ZrFAg3nHJPmvSwbaSDyMcV84JAwv65ras2xbt9O49Ro9wzyBxWwulus4IljgLentwFD9qpahXJz8ck1G1sqkFTg9hiuOez//2Q==', 'Toby Flenderson', 'interview', 'Visitor', 'BB', 'Chennai', 'suryapanneer', '9384178411', 'suryapanneer04@gmail.com', '', 'Aadhar', NULL, 'Pending', '2026-04-26 16:37:00', '2026-04-26 16:49:00', '2026-04-26 05:39:07', '', '', 11, 0, 0, 0, 0, 1, '2026-04-26 16:47:00'),
(41, 'VMS_867832', 'img_20260427_123550_VMS_867832.jpeg', 'Toby Flenderson', 'interview', 'Visitor', 'gentriq', 'chennai', 'Divya', '9384178400', 'suryapanneer04@gmail.com', 'Mobile', 'Aadhar', '1777273550_69ef0ace6a627.png', 'Pending', '2026-04-27 12:35:00', '2026-04-27 12:39:00', '2026-04-27 07:05:50', '', '', 5, 1, 0, 0, 0, 1, '2026-04-27 12:38:00');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `visitor_master`
--
ALTER TABLE `visitor_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
