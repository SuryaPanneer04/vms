-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 24, 2026 at 12:48 PM
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
-- Table structure for table `employee_master`
--

CREATE TABLE `employee_master` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `emp_name` varchar(100) NOT NULL,
  `designation` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `role` varchar(50) DEFAULT 'employee',
  `contact_no` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_master`
--

INSERT INTO `employee_master` (`id`, `username`, `emp_name`, `designation`, `department`, `email`, `role`, `contact_no`, `created_at`) VALUES
(42, 'rameshkumar', 'Ramesh Kumar', 'Manager', 'Admin', 'suryapanneer04@gmail.com', 'admin', '9876543210', '2026-04-22 05:21:16'),
(43, 'priyas', 'Priya S', 'HR Executive', 'HR', 'suryapanneer04@gmail.com', 'employee', '9876501234', '2026-04-22 05:21:16'),
(44, 'arunv', 'Arun V', 'Software Engineer', 'IT', 'suryapanneer04@gmail.com', 'employee', '9988776655', '2026-04-22 05:21:16'),
(45, 'karthikr', 'Karthik R', 'Accountant', 'Finance', 'suryapanneer04@gmail.com', 'employee', '9876512345', '2026-04-22 05:21:16'),
(46, 'divyam', 'Divya M', 'Receptionist', 'Front Office', 'suryapanneer04@gmail.com', 'employee', '9876523456', '2026-04-22 05:21:16'),
(47, 'vignesht', 'Vignesh T', 'Team Lead', 'IT', 'suryapanneer04@gmail.com', 'employee', '9876534567', '2026-04-22 05:21:16'),
(48, 'meenak', 'Meena K', 'HR Manager', 'HR', 'suryapanneer04@gmail.com', 'employee', '9876545678', '2026-04-22 05:21:16'),
(49, 'sathishp', 'Sathish P', 'Security Officer', 'Security', 'suryapanneer04@gmail.com', 'employee', '9876556789', '2026-04-22 05:21:16'),
(50, 'lakshmin', 'Lakshmi N', 'Admin Assistant', 'Admin', 'suryapanneer04@gmail.com', 'employee', '9876567890', '2026-04-22 05:21:16'),
(51, 'prabhud', 'Prabhu D', 'System Analyst', 'IT', 'suryapanneer04@gmail.com', 'employee', '9876578901', '2026-04-22 05:21:16');

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
(22, 'VMS_299924', 'img_20260422_145854_VMS_299924.jpeg', 'Ramesh Kumar', 'interview', 'Visitor', 'BB', 'chennai maduravoyal', 'suryapanneer', '9384178442', 'suryapanneer04@gmail.com', 'Mobile,Disc', 'pan', '', 'Pending', '2026-04-22 14:57:00', '2026-04-22 15:04:00', '2026-04-22 09:28:54', 'Bike', 'TN 67 BK 9626', 42, 2, 0, 3, 0, 1, '2026-04-22 15:04:00'),
(23, 'VMS_637996', 'img_20260422_151655_VMS_637996.jpeg', 'Ramesh Kumar', 'interview', 'Visitor', 'BB', 'chennai maduravoyal', 'priya', '9384178443', 'suryapanneer04@gmail.com', 'Mobile', 'Aadhar', NULL, 'Pending', '2026-04-22 15:04:00', '2026-04-22 15:19:00', '2026-04-22 09:35:29', '', '', 42, 2, 0, 0, 0, 1, '2026-04-22 15:19:00'),
(25, 'VMS_779835', 'img_20260422_152638_VMS_178983.jpeg', 'Ramesh Kumar', 'interview', 'Visitor', 'BB', 'chennai maduravoyal', 'priya', '9384178441', 'suryapanneer04@gmail.com', 'Mobile', 'Aadhar', NULL, 'Pending', '2026-04-22 15:24:00', '2026-04-24 10:04:00', '2026-04-22 09:55:06', 'Bike', 'TN 67 BK 9626', 42, 2, 0, 0, 0, 1, '2026-04-24 10:04:00'),
(27, 'VMS_829347', 'img_20260424_161623_VMS_829347.jpeg', 'Ramesh Kumar', 'in', 'Visitor', 'gentriq', 'chennai maduravoyal', 'sundar', '9384178440', 'suryapanneer04@gmail.com', '', 'Driving License', NULL, 'Pending', '2026-04-24 16:12:00', '2026-04-24 16:16:00', '2026-04-24 10:45:46', '', 'TN 67 BK 9626', 42, 0, 0, 0, 0, 1, '2026-04-24 16:16:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `employee_master`
--
ALTER TABLE `employee_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `visitor_master`
--
ALTER TABLE `visitor_master`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pass_no` (`pass_no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `employee_master`
--
ALTER TABLE `employee_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `visitor_master`
--
ALTER TABLE `visitor_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
