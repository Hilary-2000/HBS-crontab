-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 19, 2022 at 07:12 AM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.0.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `testimonytbl1`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_calendar`
--

CREATE TABLE `academic_calendar` (
  `term` varchar(10) NOT NULL,
  `start_time` date NOT NULL,
  `end_time` date NOT NULL,
  `closing_date` date NOT NULL,
  `id` int(11) NOT NULL,
  `academic_year` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `academic_calendar`
--

INSERT INTO `academic_calendar` (`term`, `start_time`, `end_time`, `closing_date`, `id`, `academic_year`) VALUES
('TERM_1', '2022-04-07', '2022-06-21', '2022-06-21', 1, '2022'),
('TERM_2', '2022-06-22', '2022-08-30', '2022-08-30', 2, '2022'),
('TERM_3', '2022-09-01', '2022-12-31', '2022-12-31', 3, '2022');

-- --------------------------------------------------------

--
-- Table structure for table `advance_pay`
--

CREATE TABLE `advance_pay` (
  `month_effect` varchar(200) NOT NULL,
  `amount` int(10) NOT NULL,
  `installments` int(10) NOT NULL,
  `date_taken` varchar(200) NOT NULL,
  `employees_id` int(10) NOT NULL,
  `balance_left` int(10) NOT NULL,
  `payment_breakdown` longtext DEFAULT NULL,
  `advance_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `advance_pay`
--

INSERT INTO `advance_pay` (`month_effect`, `amount`, `installments`, `date_taken`, `employees_id`, `balance_left`, `payment_breakdown`, `advance_id`) VALUES
('2022-12', 1000, 2, '2022-12-06', 3, 1000, NULL, 8),
('2023-04', 3000, 2, '2022-12-06', 1, 3000, NULL, 9),
('2023-02', 2000, 1, '2022-12-06', 9, 2000, NULL, 10),
('2023-12', 2000, 20, '2022-12-06', 1, 2000, NULL, 11),
('2023-02', 2300, 3, '2022-12-06', 9, 2300, NULL, 12),
('2025-07', 5000, 5, '2022-12-06', 20, 5000, NULL, 13),
('2023-07', 1000, 4, '2022-12-06', 3, 1000, NULL, 14),
('2024-06', 25000, 50, '2022-12-06', 20, 25000, NULL, 15),
('2024-06', 6000, 10, '2022-12-06', 11, 6000, NULL, 16),
('2024-07', 1200, 1, '2022-12-06', 20, 1200, NULL, 17),
('2023-07', 2000, 1, '2022-12-06', 20, 2000, NULL, 18),
('2023-10', 3000, 2, '2022-12-06', 20, 3000, NULL, 19),
('2023-07', 500, 1, '2022-12-06', 1, 500, NULL, 20),
('2024-07', 6500, 5, '2022-12-06', 2, 6500, NULL, 21),
('2024-07', 44, 2, '2022-12-06', 1, 44, NULL, 22),
('2023-12', 7500, 3, '2022-12-06', 9, 7500, NULL, 23),
('2025-06', 6000, 3, '2022-12-06', 8, 6000, NULL, 24),
('2024-07', 6600, 11, '2022-12-06', 9, 6600, NULL, 25),
('2023-03', 2500, 5, '2022-12-06', 3, 2500, NULL, 26),
('2025-06', 3600, 3, '2022-12-06', 2, 3600, NULL, 27),
('2023-06', 3200, 8, '2022-12-06', 11, 3200, NULL, 28),
('2023-08', 2200, 10, '2022-12-06', 1, 2200, NULL, 29),
('2023-01', 2000, 4, '2022-12-08', 2, 2000, NULL, 30),
('2022-11', 20000, 4, '2022-12-08', 12, 0, '[{\"paydate\":\"20221208133312\",\"payment_for\":\"Nov:2022\",\"amount_paid\":5000},{\"paydate\":\"20221208133509\",\"payment_for\":\"Dec:2022\",\"amount_paid\":5000},{\"paydate\":\"20221212075605\",\"payment_for\":\"Jan:2023\",\"amount_paid\":5000},{\"paydate\":\"20221212075605\",\"payment_for\":\"Feb:2023\",\"amount_paid\":5000}]', 31),
('2022-12', 10000, 2, '2022-12-14', 15, 10000, NULL, 32);

-- --------------------------------------------------------

--
-- Table structure for table `apply_leave`
--

CREATE TABLE `apply_leave` (
  `leave_category` int(20) DEFAULT NULL,
  `employee_id` varchar(200) DEFAULT NULL,
  `days_duration` int(20) DEFAULT NULL,
  `from` varchar(200) DEFAULT NULL,
  `to` varchar(200) DEFAULT NULL,
  `date_applied` varchar(200) DEFAULT NULL,
  `leave_description` mediumtext DEFAULT NULL,
  `status` int(10) DEFAULT NULL COMMENT '0 pending 1 accepted 2 declined',
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `apply_leave`
--

INSERT INTO `apply_leave` (`leave_category`, `employee_id`, `days_duration`, `from`, `to`, `date_applied`, `leave_description`, `status`, `id`) VALUES
(1, '1', 8, '2023-01-01', '2023-01-15', '2022-11-26', 'Going to ocha for my wedding.', 1, 1),
(1, '1', 5, '2022-12-08', '2022-12-15', '2022-11-28', '', 2, 9),
(1, '2', 5, '2022-12-10', '2022-12-17', '2022-11-28', 'I am feeling sick. Kindly I need this leave.', 1, 10),
(1, '42', 5, '2022-12-09', '2022-12-16', '2022-11-28', 'Union of teachers', 1, 11),
(3, '1', 90, '2022-12-07', '2023-03-27', '2022-11-29', 'I would like to go for the ninety day leave to do my delivery.I will be back after ninety days.', 2, 12),
(2, '1', 13, '2022-12-17', '2023-01-05', '2022-11-30', 'I am applying since I am expecting my firstborn Child soon.', 1, 13),
(1, '1', 10, '2022-12-22', '2023-01-06', '2022-11-30', '', 1, 14),
(1, '1', 1, '2022-12-23', '2022-12-27', '2022-12-09', '', 0, 15);

-- --------------------------------------------------------

--
-- Table structure for table `attendancetable`
--

CREATE TABLE `attendancetable` (
  `id` int(11) NOT NULL,
  `admission_no` varchar(10) NOT NULL,
  `class` varchar(10) NOT NULL,
  `date` date NOT NULL,
  `signedby` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `attendancetable`
--

INSERT INTO `attendancetable` (`id`, `admission_no`, `class`, `date`, `signedby`) VALUES
(1, '1', '8', '2021-09-20', 'HILARY'),
(2, '2', '8', '2021-09-20', 'HILARY'),
(3, '3', '7', '2021-09-20', 'HILARY'),
(4, '1', '8', '2021-09-21', 'IAN'),
(5, '2', '8', '2021-09-21', 'IAN'),
(6, '1', '8', '2021-09-22', 'HILARY'),
(7, '2', '8', '2021-09-22', 'HILARY'),
(8, '3', '7', '2021-09-22', 'HILARY'),
(9, '1', '8', '2021-09-23', 'HILARY'),
(10, '2', '8', '2021-09-23', 'HILARY'),
(11, '5', '8', '2021-09-23', 'HILARY'),
(12, '6', 'GRADE1', '2021-09-26', 'HILARY'),
(13, '1', '8', '2021-09-30', 'HILARY'),
(14, '2', '8', '2021-09-30', 'HILARY'),
(15, '5', '8', '2021-09-30', 'HILARY'),
(16, '1', '8', '2021-10-02', 'HILARY'),
(17, '2', '8', '2021-10-02', 'HILARY'),
(18, '5', '8', '2021-10-02', 'HILARY'),
(19, '1', '8', '2021-10-04', 'HILARY'),
(20, '2', '8', '2021-10-04', 'HILARY'),
(21, '5', '8', '2021-10-04', 'HILARY'),
(22, '7', '8', '2021-10-04', 'HILARY'),
(23, '3', '7', '2021-10-04', 'HILARY'),
(24, '4', '7', '2021-10-04', 'HILARY'),
(25, '6', 'GRADE1', '2021-10-04', 'HILARY'),
(26, '1', '8', '2022-02-10', 'HILARY'),
(27, '2', '8', '2022-02-10', 'HILARY'),
(28, '5', '8', '2022-02-10', 'HILARY'),
(29, '7', '8', '2022-02-10', 'HILARY'),
(30, '19', '8', '2022-02-10', 'HILARY'),
(31, '20', '8', '2022-02-10', 'HILARY'),
(32, '21', '8', '2022-02-10', 'HILARY'),
(33, '22', '8', '2022-02-10', 'HILARY'),
(34, '23', '8', '2022-02-10', 'HILARY'),
(35, '35', '8', '2022-02-10', 'HILARY'),
(36, '38', '8', '2022-02-10', 'HILARY'),
(37, '39', '8', '2022-02-10', 'HILARY'),
(38, '3', '7', '2022-02-10', 'HILARY'),
(39, '25', '7', '2022-02-10', 'HILARY'),
(40, '26', '7', '2022-02-10', 'HILARY'),
(41, '28', '7', '2022-02-10', 'HILARY'),
(42, '29', '7', '2022-02-10', 'HILARY'),
(43, '31', '7', '2022-02-10', 'HILARY'),
(44, '32', '7', '2022-02-10', 'HILARY'),
(45, '37', '7', '2022-02-10', 'HILARY'),
(46, '6', '7', '2022-05-26', 'HILARY'),
(47, '9', '7', '2022-05-26', 'HILARY'),
(48, '10', '7', '2022-05-26', 'HILARY'),
(49, '11', '7', '2022-05-26', 'HILARY'),
(50, '13', '7', '2022-05-26', 'HILARY'),
(51, '14', '7', '2022-05-26', 'HILARY'),
(52, '22', '7', '2022-05-26', 'HILARY'),
(53, 'HJHGJHG', '7', '2022-05-26', 'HILARY'),
(54, '17', '5', '2022-05-26', 'HILARY'),
(55, '1', '8', '2022-05-27', 'HILARY'),
(56, '20', '8', '2022-05-27', 'HILARY'),
(57, '23', '8', '2022-05-27', 'HILARY'),
(58, '25', '8', '2022-05-27', 'HILARY'),
(59, '32', '8', '2022-05-27', 'HILARY'),
(60, '41', '8', '2022-05-27', 'HILARY'),
(61, '40', '8', '2022-05-27', 'HILARY'),
(62, 'KJHKJHJ', '8', '2022-05-27', 'HILARY'),
(63, 'SDSF', '8', '2022-05-27', 'HILARY'),
(64, 'LBSMIS12', '8', '2022-05-27', 'HILARY'),
(65, 'FGF11', '8', '2022-05-27', 'HILARY'),
(66, '6', '7', '2022-05-27', 'HILARY'),
(67, '8', '7', '2022-05-27', 'HILARY'),
(68, '10', '7', '2022-05-27', 'HILARY'),
(69, '11', '7', '2022-05-27', 'HILARY'),
(70, '12', '7', '2022-05-27', 'HILARY'),
(71, '14', '7', '2022-05-27', 'HILARY'),
(72, '22', '7', '2022-05-27', 'HILARY'),
(73, '34', '7', '2022-05-27', 'HILARY'),
(74, '36', '7', '2022-05-27', 'HILARY'),
(75, 'HJHGJHG', '7', '2022-05-27', 'HILARY'),
(76, '15', '6', '2022-05-27', 'HILARY'),
(77, '18', '6', '2022-05-27', 'HILARY'),
(78, '17', '5', '2022-05-27', 'HILARY'),
(79, '6', '7', '2022-10-04', 'WESLEY'),
(80, '8', '7', '2022-10-04', 'WESLEY'),
(81, '1', '8', '2022-10-04', 'WESLEY'),
(82, '19', '8', '2022-10-04', 'WESLEY'),
(83, '1', '8', '2022-10-03', 'WESLEY'),
(84, '19', '8', '2022-10-03', 'WESLEY'),
(85, '20', '8', '2022-10-03', 'WESLEY'),
(86, '23', '8', '2022-10-03', 'WESLEY'),
(87, '24', '8', '2022-10-03', 'WESLEY'),
(88, '25', '8', '2022-10-03', 'WESLEY'),
(89, '37', '8', '2022-10-03', 'WESLEY'),
(90, 'HGJHGJH', '8', '2022-10-03', 'WESLEY'),
(91, 'KJHKJHJ', '8', '2022-10-03', 'WESLEY'),
(92, 'SDSF', '8', '2022-10-03', 'WESLEY'),
(93, '35', '8', '2022-10-03', 'WESLEY'),
(94, '1', '8', '2022-09-30', 'WESLEY'),
(95, '19', '8', '2022-09-30', 'WESLEY'),
(96, '20', '8', '2022-09-30', 'WESLEY'),
(97, '23', '8', '2022-09-30', 'WESLEY'),
(98, '24', '8', '2022-09-30', 'WESLEY'),
(99, '25', '8', '2022-09-30', 'WESLEY'),
(100, '37', '8', '2022-09-30', 'WESLEY'),
(101, 'HGJHGJH', '8', '2022-09-30', 'WESLEY'),
(102, 'KJHKJHJ', '8', '2022-09-30', 'WESLEY'),
(103, 'SDSF', '8', '2022-09-30', 'WESLEY'),
(104, '35', '8', '2022-09-30', 'WESLEY'),
(116, '6', '7', '2022-09-30', 'WESLEY'),
(117, '8', '7', '2022-09-30', 'WESLEY'),
(118, '9', '7', '2022-09-30', 'WESLEY'),
(119, '11', '7', '2022-09-30', 'WESLEY'),
(120, '12', '7', '2022-09-30', 'WESLEY'),
(121, '14', '7', '2022-09-30', 'WESLEY'),
(122, '22', '7', '2022-09-30', 'WESLEY'),
(123, '34', '7', '2022-09-30', 'WESLEY'),
(124, '36', '7', '2022-09-30', 'WESLEY'),
(125, 'LBS102', '7', '2022-09-30', 'WESLEY'),
(126, 'MGM101', '7', '2022-09-30', 'WESLEY'),
(127, 'MGM102', '7', '2022-09-30', 'WESLEY'),
(128, 'MGM103', '7', '2022-09-30', 'WESLEY'),
(142, '6', '7', '2022-09-29', 'WESLEY'),
(143, '8', '7', '2022-09-29', 'WESLEY'),
(144, '10', '7', '2022-09-29', 'WESLEY'),
(145, '11', '7', '2022-09-29', 'WESLEY'),
(146, '12', '7', '2022-09-29', 'WESLEY'),
(147, '14', '7', '2022-09-29', 'WESLEY'),
(148, '22', '7', '2022-09-29', 'WESLEY'),
(149, '36', '7', '2022-09-29', 'WESLEY'),
(150, 'HJHGJHG', '7', '2022-09-29', 'WESLEY'),
(151, 'LBS102', '7', '2022-09-29', 'WESLEY'),
(152, 'MGM102', '7', '2022-09-29', 'WESLEY'),
(153, 'MGM103', '7', '2022-09-29', 'WESLEY'),
(154, '15', '6', '2022-09-29', 'WESLEY'),
(155, '18', '6', '2022-09-29', 'WESLEY'),
(194, '1', '8', '2022-10-06', 'ROBERT'),
(195, '19', '8', '2022-10-06', 'ROBERT'),
(196, '20', '8', '2022-10-06', 'ROBERT'),
(197, '23', '8', '2022-10-06', 'ROBERT'),
(198, '24', '8', '2022-10-06', 'ROBERT'),
(199, '32', '8', '2022-10-06', 'ROBERT'),
(200, '41', '8', '2022-10-06', 'ROBERT'),
(201, '37', '8', '2022-10-06', 'ROBERT'),
(202, 'HGJHGJH', '8', '2022-10-06', 'ROBERT'),
(203, 'KJHKJHJ', '8', '2022-10-06', 'ROBERT'),
(204, 'LBSMIS12', '8', '2022-10-06', 'ROBERT'),
(205, 'FGF11', '8', '2022-10-06', 'ROBERT'),
(206, '35', '8', '2022-10-06', 'ROBERT'),
(207, '38', '8', '2022-10-06', 'ROBERT'),
(208, '1', '8', '2022-10-05', 'ROBERT'),
(209, '19', '8', '2022-10-05', 'ROBERT'),
(210, '20', '8', '2022-10-05', 'ROBERT'),
(211, '24', '8', '2022-10-05', 'ROBERT'),
(212, '25', '8', '2022-10-05', 'ROBERT'),
(213, '32', '8', '2022-10-05', 'ROBERT'),
(214, '37', '8', '2022-10-05', 'ROBERT'),
(215, 'HGJHGJH', '8', '2022-10-05', 'ROBERT'),
(216, 'KJHKJHJ', '8', '2022-10-05', 'ROBERT'),
(217, 'SDSF', '8', '2022-10-05', 'ROBERT'),
(218, 'FGF11', '8', '2022-10-05', 'ROBERT'),
(219, 'LBD343', '8', '2022-10-05', 'ROBERT'),
(220, '35', '8', '2022-10-05', 'ROBERT'),
(221, '38', '8', '2022-10-05', 'ROBERT'),
(222, '1', '8', '2022-11-19', 'HILARY'),
(223, '19', '8', '2022-11-19', 'HILARY'),
(224, '23', '8', '2022-11-19', 'HILARY'),
(225, '24', '8', '2022-11-19', 'HILARY'),
(226, '32', '8', '2022-11-19', 'HILARY'),
(227, '41', '8', '2022-11-19', 'HILARY'),
(228, '40', '8', '2022-11-19', 'HILARY'),
(229, 'HGJHGJH', '8', '2022-11-19', 'HILARY'),
(230, 'KJHKJHJ', '8', '2022-11-19', 'HILARY'),
(231, 'SDSF', '8', '2022-11-19', 'HILARY'),
(232, 'FGF11', '8', '2022-11-19', 'HILARY'),
(233, 'LBD343', '8', '2022-11-19', 'HILARY'),
(234, '35', '8', '2022-11-19', 'HILARY'),
(235, '38', '8', '2022-11-19', 'HILARY'),
(236, '1', '8', '2022-11-18', 'HILARY'),
(237, '19', '8', '2022-11-18', 'HILARY'),
(238, '20', '8', '2022-11-18', 'HILARY'),
(239, '24', '8', '2022-11-18', 'HILARY'),
(240, '32', '8', '2022-11-18', 'HILARY'),
(241, '41', '8', '2022-11-18', 'HILARY'),
(242, '40', '8', '2022-11-18', 'HILARY'),
(243, '37', '8', '2022-11-18', 'HILARY'),
(244, 'KJHKJHJ', '8', '2022-11-18', 'HILARY'),
(245, 'SDSF', '8', '2022-11-18', 'HILARY'),
(246, 'LBSMIS12', '8', '2022-11-18', 'HILARY'),
(247, 'LBD343', '8', '2022-11-18', 'HILARY'),
(248, '35', '8', '2022-11-18', 'HILARY'),
(249, 'MGM104', '8', '2022-11-18', 'HILARY'),
(251, '1', '8', '2022-11-17', 'HILARY'),
(252, '19', '8', '2022-11-17', 'HILARY'),
(253, '20', '8', '2022-11-17', 'HILARY'),
(254, '23', '8', '2022-11-17', 'HILARY'),
(255, '25', '8', '2022-11-17', 'HILARY'),
(256, '32', '8', '2022-11-17', 'HILARY'),
(257, '41', '8', '2022-11-17', 'HILARY'),
(258, 'HGJHGJH', '8', '2022-11-17', 'HILARY'),
(259, 'KJHKJHJ', '8', '2022-11-17', 'HILARY'),
(260, 'SDSF', '8', '2022-11-17', 'HILARY'),
(261, 'FGF11', '8', '2022-11-17', 'HILARY'),
(262, 'LBD343', '8', '2022-11-17', 'HILARY'),
(263, '35', '8', '2022-11-17', 'HILARY'),
(264, 'MGM104', '8', '2022-11-17', 'HILARY'),
(265, '17', '1', '2022-11-28', 'DISMAS');

-- --------------------------------------------------------

--
-- Table structure for table `boarding_list`
--

CREATE TABLE `boarding_list` (
  `id` int(10) NOT NULL,
  `student_id` varchar(200) NOT NULL,
  `dorm_id` int(10) NOT NULL,
  `date_of_enrollment` date NOT NULL,
  `deleted` int(1) NOT NULL,
  `activated` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `boarding_list`
--

INSERT INTO `boarding_list` (`id`, `student_id`, `dorm_id`, `date_of_enrollment`, `deleted`, `activated`) VALUES
(5, '5', 2, '2021-10-01', 0, 1),
(8, '11', 1, '2021-10-04', 0, 1),
(9, '12', 1, '2021-10-04', 0, 1),
(10, '13', 2, '2021-10-04', 0, 1),
(12, '16', 2, '2021-10-13', 0, 1),
(14, '14', 1, '2021-10-13', 0, 1),
(15, '17', 1, '2021-10-13', 0, 1),
(16, '18', 1, '2021-10-13', 0, 1),
(18, '4', 1, '2022-02-14', 0, 1),
(19, '6', 1, '2022-02-14', 0, 1),
(20, '21', 1, '2022-02-14', 0, 1),
(21, '41', 1, '2022-03-24', 0, 1),
(22, 'KJHKJHJ', 2, '2022-04-11', 0, 1),
(25, '10', 1, '2022-11-17', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `class_teacher_tbl`
--

CREATE TABLE `class_teacher_tbl` (
  `class_teacher_id` int(10) NOT NULL,
  `class_assigned` varchar(30) NOT NULL,
  `active` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `class_teacher_tbl`
--

INSERT INTO `class_teacher_tbl` (`class_teacher_id`, `class_assigned`, `active`) VALUES
(2, '1', 1),
(3, '2', 1),
(10, '7', 1),
(11, '4', 1),
(15, '6', 1),
(16, '3', 1),
(43, '8', 1);

-- --------------------------------------------------------

--
-- Table structure for table `dorm_list`
--

CREATE TABLE `dorm_list` (
  `dorm_id` int(30) NOT NULL,
  `dorm_name` varchar(200) NOT NULL,
  `dorm_capacity` int(30) NOT NULL,
  `dorm_captain` varchar(200) NOT NULL,
  `activated` int(1) NOT NULL,
  `deleted` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `dorm_list`
--

INSERT INTO `dorm_list` (`dorm_id`, `dorm_name`, `dorm_capacity`, `dorm_captain`, `activated`, `deleted`) VALUES
(1, 'Mt Sinai Dormitory', 16, '9', 1, 0),
(2, 'Mt Longonot', 40, '3', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `email_address`
--

CREATE TABLE `email_address` (
  `id` int(11) NOT NULL,
  `sender_from` varchar(500) DEFAULT NULL,
  `recipient_to` varchar(500) DEFAULT NULL,
  `bcc` varchar(500) DEFAULT NULL,
  `date_time` varchar(50) DEFAULT NULL,
  `message_subject` varchar(500) DEFAULT NULL,
  `message` longtext DEFAULT NULL,
  `cc` varchar(500) DEFAULT NULL,
  `attachments` mediumtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `email_address`
--

INSERT INTO `email_address` (`id`, `sender_from`, `recipient_to`, `bcc`, `date_time`, `message_subject`, `message`, `cc`, `attachments`) VALUES
(1, 'mail@ladybirdsmis.com', 'hilaryne@gmail.com', 'mail@ladybirdsmis.com', '20221031133257', 'Hillary Is Our New Director', '<p>Dear Hillary,</p>\n<p>I am writting to share with you that on <strong>Monday 14th Oct 2020&nbsp;</strong>we will have an event in school.</p>\n<p><em>Kind regards</em></p>\n<p><em>James</em></p>\n<p><em>Times two</em></p>', 'ladybirdsmis@gmail.com', NULL),
(2, 'mail@ladybirdsmis.com', 'hilaryme@gmail.com', 'mail@ladybirdsmis.com', '20221031133332', 'Hillary Is Our New Director', '<p>Dear Hillary,</p>\n<p>I am writting to share with you that on <strong>Monday 14th Oct 2020&nbsp;</strong>we will have an event in school.</p>\n<p><em>Kind regards</em></p>\n<p><em>James</em></p>\n<p><em>Times two</em></p>', 'ladybirdsmis@gmail.com', NULL),
(3, 'mail@ladybirdsmis.com', 'hilaryme@gmail.com', 'mail@ladybirdsmis.com', '20221031173153', 'Hillary Is Our New Director', '<p>Dear Hillary,</p>\n<p>I am writting to share with you that on <strong>Monday 14th Oct 2020&nbsp;</strong>we will have an event in school.</p>\n<p><em>Kind regards</em></p>\n<p><em>James</em></p>\n<p><em>Times two</em></p>', 'ladybirdsmis@gmail.com', NULL),
(4, 'mail@ladybirdsmis.com', 'hilaryme45@gmail.com', '', '20221031181039', 'Fees Reminder', '<p>Hello Friend,</p>\n<p>This is an email test,</p>\n<p>Kindly ignore it if recieved!</p>\n<p>Kind regards.</p>\n<p>Hillary.</p>', '', NULL),
(5, 'mail@ladybirdsmis.com', 'hilaryme45@gmail.com', '', '20221031181352', 'Check Out Our websites', '<p>Check out our website</p>\n<p><a title=\"System\" href=\"https://www.ladybirdsmis.com\" target=\"_blank\" rel=\"noopener\">Ladybird School Management System</a></p>\n<p>Hillary</p>', '', NULL),
(6, 'mail@ladybirdsmis.com', 'hilaryme45@gmail.com', '', '20221103200642', 'Test Message', '<p>I fee that everything is wrong.</p>', '', NULL),
(7, 'mail@ladybirdsmis.com', 'hilaryme45@gmail.com', 'mail@ladybirdsmis.com', '20221103223949', NULL, NULL, 'mail@ladybirdsmis.com', '../invoices/testimonytbl1/202211031939/202211031939_6.pdf'),
(8, 'mail@ladybirdsmis.com', 'hilaryme45@gmail.com,kairuian123@gmail.com', '', '20221103224431', NULL, NULL, '', '../invoices/testimonytbl1/202211031944/202211031944_6.pdf'),
(26, 'mail@ladybirdsmis.com', 'hilaryme45@gmail.com', '', '20221104145146', 'Fees Invoice', 'Dear Parent,\r\n\r\nWe hope you are doing fine and healthy, Please find the attached invoice for your child`s fees.\r\nKind regards,\r\nHeadteacher', '', '../invoices/testimonytbl1/202211041151/202211041151_1.pdf'),
(27, 'mail@ladybirdsmis.com', 'hilaryme45@gmail.com', '', '20221104145420', 'Fees Invoice', 'Dear Parent,\r\n\r\nWe hope you are doing fine and healthy, Please find the attached invoice for your child`s fees.\r\nKind regards,\r\nHeadteacher', '', '../invoices/testimonytbl1/202211041154/202211041154_1.pdf'),
(38, 'mail@ladybirdsmis.com', NULL, '', '20221105161443', 'Email from  Demo School', '<p>Dear Mr&nbsp;Kevin,</p>\n<p>Your son`s balance is Kes 87,500. Kindly clear before 10th Nov 2022</p>\n<p>Kind regards</p>\n<p>Hillary Ngige</p>', '', NULL),
(39, 'mail@ladybirdsmis.com', 'ladybirdsmis@gmail.com', '', '20221105161824', 'Hillary Ngige Fees Reminder', '<p>Dear Mr&nbsp;Kevin,</p>\n<p>Your son`s balance is Kes 87,500. Kindly clear before 10th Nov 2022</p>\n<p>Kind regards</p>\n<p>Hillary Ngige</p>', '', NULL),
(40, 'mail@ladybirdsmis.com', 'ladybirdsmis@gmail.com', '', '20221105162057', 'Hillary Ngige Fees Reminder', '<p>Dear Mrs&nbsp;Hillary,</p>\n<p>Your son`s balance is Kes 87,500. Kindly clear before 10th Nov 2022</p>\n<p>Kind regards</p>\n<p>Hillary Ngige</p>', '', NULL),
(41, 'mail@ladybirdsmis.com', 'ladybirdsmis@gmail.com', '', '20221105162146', 'Hillary Ngige Fees Reminder', '<p>Dear Mrs&nbsp;Maria,</p>\n<p>Your son`s balance is Kes 87,500. Kindly clear before 10th Nov 2022</p>\n<p>Kind regards</p>\n<p>Hillary Ngige</p>', '', NULL),
(42, 'mail@ladybirdsmis.com', 'hilaryme45@gmail.com', '', '20221105162526', 'Hillary Ngige Fees Reminder', '<p>Dear Mr&nbsp;Kevin,</p>\n<p>Your son`s balance is Kes 87,500. Kindly clear before 10th Nov 2022</p>\n<p>Kind regards</p>\n<p>Hillary Ngige</p>', '', NULL),
(43, 'mail@ladybirdsmis.com', 'ladybirdsmis@gmail.com', '', '20221105162531', 'Hillary Ngige Fees Reminder', '<p>Dear Mr&nbsp;Kevin,</p>\n<p>Your son`s balance is Kes 87,500. Kindly clear before 10th Nov 2022</p>\n<p>Kind regards</p>\n<p>Hillary Ngige</p>', '', NULL),
(44, 'mail@ladybirdsmis.com', 'hilaryme45@gmail.com', '', '20221105162659', 'Hillary (Grade 8) Fees Reminder', '<p>Dear Mr&nbsp;Kevin,</p>\n<p>Your son`s balance is Kes 87,500. Kindly clear before 10th Nov 2022</p>\n<p>Kind regards</p>\n<p>Hillary Ngige</p>', '', NULL),
(45, 'mail@ladybirdsmis.com', 'ladybirdsmis@gmail.com', '', '20221105162705', 'Hillary (Grade 8) Fees Reminder', '<p>Dear Mr&nbsp;Kevin,</p>\n<p>Your son`s balance is Kes 87,500. Kindly clear before 10th Nov 2022</p>\n<p>Kind regards</p>\n<p>Hillary Ngige</p>', '', NULL),
(46, 'mail@ladybirdsmis.com', 'hilaryme45@gmail.com', '', '20221105162914', 'Hillary (Grade 8) Fees Reminder', '<p>Dear Mr&nbsp;Kevin,</p>\n<p>Your son`s balance is Kes 87,500. Kindly clear before 10th Nov 2022</p>\n<p>Kind regards</p>\n<p>Hillary Ngige</p>', '', NULL),
(47, 'mail@ladybirdsmis.com', 'ladybirdsmis@gmail.com', '', '20221105162921', 'Hillary (Grade 8) Fees Reminder', '<p>Dear Mrs&nbsp;Maria,</p>\n<p>Your son`s balance is Kes 87,500. Kindly clear before 10th Nov 2022</p>\n<p>Kind regards</p>\n<p>Hillary Ngige</p>', '', NULL),
(48, 'mail@ladybirdsmis.com', 'hilaryme45@gmail.com', '', '20221105170640', 'Hillary (Grade 8) Fees Reminder', '<p>Dear Mr&nbsp;Kevin,</p>\n<p>Your son`s balance is Kes 87,500. Kindly clear before 10th Nov 2022</p>\n<p>Kind regards</p>\n<p>Hillary Ngige</p>', '', NULL),
(49, 'mail@ladybirdsmis.com', 'ladybirdsmis@gmail.com', '', '20221105170647', 'Hillary (Grade 8) Fees Reminder', '<p>Dear Mrs&nbsp;Maria,</p>\n<p>Your son`s balance is Kes 87,500. Kindly clear before 10th Nov 2022</p>\n<p>Kind regards</p>\n<p>Hillary Ngige</p>', '', NULL),
(50, 'mail@ladybirdsmis.com', 'hilaryme45@gmail.com', '', '20221105171016', 'Hillary (Grade 8) Fees Reminder', '<p>Dear Mr&nbsp;Kevin,</p>\n<p>Your son`s balance is Kes 87,500. Kindly clear before 10th Nov 2022</p>\n<p>Kind regards</p>\n<p>Hillary Ngige</p>', '', NULL),
(51, 'mail@ladybirdsmis.com', 'ladybirdsmis@gmail.com', '', '20221105171021', 'Hillary (Grade 8) Fees Reminder', '<p>Dear Mrs&nbsp;Maria,</p>\n<p>Your son`s balance is Kes 87,500. Kindly clear before 10th Nov 2022</p>\n<p>Kind regards</p>\n<p>Hillary Ngige</p>', '', NULL),
(52, 'mail@ladybirdsmis.com', 'hilaryme45@gmail.com', '', '20221105171106', 'Hillary (Grade 8) Fees Reminder', '<p>Dear Mr&nbsp;Kevin,</p>\n<p>Your son`s balance is Kes 87,500. Kindly clear before 10th Nov 2022</p>\n<p>Kind regards</p>\n<p>Hillary Ngige</p>', '', NULL),
(57, 'mail@ladybirdsmis.com', 'hilaryme45@gmail.com,ladybirdsmis@gmail.com', '', '20221107090958', ' Demo School Fees Invoice', 'Dear Parent,\r\n\r\nWe hope you are doing fine and healthy, Please find the attached invoice for your child`s fees.\r\nKind regards,\r\nHeadteacher', '', '../invoices/testimonytbl1/202211070609/202211070609_1.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `exams_tbl`
--

CREATE TABLE `exams_tbl` (
  `exams_id` int(11) NOT NULL,
  `exams_name` varchar(100) NOT NULL,
  `curriculum` varchar(300) NOT NULL,
  `class_sitting` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `subject_done` varchar(500) NOT NULL,
  `target_mean_score` varchar(500) DEFAULT NULL,
  `deleted` int(1) NOT NULL,
  `students_sitting` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `exams_tbl`
--

INSERT INTO `exams_tbl` (`exams_id`, `exams_name`, `curriculum`, `class_sitting`, `start_date`, `end_date`, `subject_done`, `target_mean_score`, `deleted`, `students_sitting`) VALUES
(1, 'MATH CONTEST', '844', '(3,4,5,6,7,8)', '2021-09-14', '2022-08-06', '(2)', '77', 0, NULL),
(2, 'TESO SOUTH MOCK', '844', '(5,6,7,8)', '2021-10-06', '2021-10-12', '(1,2,3,4,5,6)', '300', 0, NULL),
(3, 'KiGANJO 16', '844', '(1,2,3,4,5,6,7,8)', '2022-04-12', '2022-06-01', '(1,2,3,4,5,6)', '67', 0, NULL),
(7, 'ENG CONTEST', 'IGCSE', '(6,7,8)', '2022-08-07', '2022-08-11', '(1)', '', 0, NULL),
(8, 'SCIENCE CONTEST', 'IGCSE', '(6,7,8)', '2022-08-07', '2022-09-02', '(4)', '', 0, '[{\"classname\":\"6\",\"classlist\":[\"15\",\"16\",\"18\"]},{\"classname\":\"7\",\"classlist\":[\"6\",\"8\",\"9\",\"10\",\"11\",\"12\",\"13\",\"14\",\"22\",\"34\",\"36\",\"HJHGJHG\",\"LBS102\",\"MGM101\",\"MGM102\",\"MGM103\"]},{\"classname\":\"8\",\"classlist\":[\"1\",\"19\",\"20\",\"23\",\"24\",\"25\",\"32\",\"41\",\"40\",\"37\",\"HGJHGJH\",\"KJHKJHJ\",\"SDSF\",\"LBSMIS12\",\"FGF11\",\"35\",\"38\",\"MGM104\"]}]'),
(9, 'TEST EXAMS', '844', '(6,7,8)', '2022-09-15', '2022-09-30', '(1,2,3,4,5,6,11,12)', '100', 0, '[{\"classname\":\"6\",\"classlist\":[\"15\",\"16\",\"18\"]},{\"classname\":\"7\",\"classlist\":[\"6\",\"8\",\"9\",\"10\",\"11\",\"12\",\"13\",\"14\",\"22\",\"34\",\"36\",\"HJHGJHG\",\"LBS102\",\"MGM101\",\"MGM102\",\"MGM103\"]},{\"classname\":\"8\",\"classlist\":[\"1\",\"19\",\"20\",\"23\",\"24\",\"25\",\"32\",\"41\",\"40\",\"37\",\"HGJHGJH\",\"KJHKJHJ\",\"SDSF\",\"LBSMIS12\",\"FGF11\",\"LBD343\",\"35\",\"38\",\"MGM104\"]}]'),
(10, 'NEW  EXAMS', '844', '(6,7,8)', '2022-10-01', '2022-10-08', '(1,2,3,4,5,6)', '45', 0, '[{\"classname\":\"6\",\"classlist\":[\"15\",\"16\",\"18\"]},{\"classname\":\"7\",\"classlist\":[\"6\",\"8\",\"9\",\"10\",\"11\",\"12\",\"13\",\"14\",\"22\",\"34\",\"36\",\"HJHGJHG\",\"LBS102\",\"MGM101\",\"MGM102\",\"MGM103\"]},{\"classname\":\"8\",\"classlist\":[\"1\",\"19\",\"20\",\"23\",\"24\",\"25\",\"32\",\"41\",\"40\",\"37\",\"HGJHGJH\",\"KJHKJHJ\",\"SDSF\",\"LBSMIS12\",\"FGF11\",\"35\",\"38\",\"MGM104\"]}]');

-- --------------------------------------------------------

--
-- Table structure for table `exam_record_tbl`
--

CREATE TABLE `exam_record_tbl` (
  `result_id` int(30) NOT NULL,
  `exam_id` int(30) NOT NULL,
  `student_id` varchar(200) NOT NULL,
  `subject_id` int(30) NOT NULL,
  `exam_marks` int(30) NOT NULL,
  `exam_grade` varchar(10) NOT NULL,
  `filled_by` varchar(100) NOT NULL,
  `class name` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `exam_record_tbl`
--

INSERT INTO `exam_record_tbl` (`result_id`, `exam_id`, `student_id`, `subject_id`, `exam_marks`, `exam_grade`, `filled_by`, `class name`) VALUES
(1, 2, '15', 1, 25, '-', '1', '5'),
(2, 2, '14', 1, 20, '-', '1', '5'),
(3, 2, '13', 1, 32, '-', '1', '5'),
(4, 2, '12', 1, 36, '-', '1', '5'),
(6, 2, '10', 1, 33, '-', '1', '5'),
(9, 2, '15', 2, 45, '-', '1', '5'),
(11, 2, '13', 2, 66, '-', '1', '5'),
(13, 2, '11', 2, 33, '-', '1', '5'),
(14, 2, '10', 2, 43, '-', '1', '5'),
(15, 2, '9', 2, 65, '-', '1', '5'),
(17, 2, '15', 3, 20, '-', '1', '5'),
(18, 2, '14', 3, 30, '-', '1', '5'),
(19, 2, '13', 3, 15, '-', '1', '5'),
(20, 2, '12', 3, 20, '-', '1', '5'),
(21, 2, '11', 3, 30, '-', '1', '5'),
(22, 2, '10', 3, 48, '-', '1', '5'),
(23, 2, '9', 3, 40, '-', '1', '5'),
(24, 2, '8', 3, 45, '-', '1', '5'),
(25, 2, '8', 2, 80, '-', '1', '5'),
(26, 2, '8', 1, 44, '-', '1', '5'),
(27, 2, '11', 1, 25, '-', '1', '5'),
(28, 2, '9', 1, 36, '-', '1', '5'),
(29, 2, '12', 2, 54, '-', '1', '5'),
(30, 2, '14', 2, 68, '-', '1', '5'),
(31, 2, '15', 5, 3, 'M.E', '1', '5'),
(32, 2, '14', 5, 4, 'E.E', '1', '5'),
(33, 2, '13', 5, 2, 'A.E', '1', '5'),
(34, 2, '12', 5, 4, 'E.E', '1', '5'),
(35, 2, '11', 5, 3, 'M.E', '1', '5'),
(36, 2, '10', 5, 3, 'M.E', '1', '5'),
(37, 2, '9', 5, 3, 'M.E', '1', '5'),
(38, 2, '8', 5, 4, 'E.E', '1', '5'),
(39, 2, '8', 4, 78, '-', '1', '5'),
(40, 2, '15', 4, 78, '-', '1', '5'),
(41, 2, '14', 4, 50, '-', '1', '5'),
(42, 2, '13', 4, 66, '-', '1', '5'),
(43, 2, '12', 4, 70, '-', '1', '5'),
(44, 2, '11', 4, 88, '-', '1', '5'),
(45, 2, '10', 4, 65, '-', '1', '5'),
(46, 2, '9', 4, 84, '-', '1', '5'),
(47, 2, '15', 6, 50, '-', '1', '5'),
(48, 2, '14', 6, 46, '-', '1', '5'),
(49, 2, '13', 6, 40, '-', '1', '5'),
(50, 2, '12', 6, 45, '-', '1', '5'),
(51, 2, '11', 6, 58, '-', '1', '5'),
(52, 2, '10', 6, 63, '-', '1', '5'),
(53, 2, '9', 6, 45, '-', '1', '5'),
(54, 2, '8', 6, 33, '-', '1', '5'),
(55, 3, '17', 4, 4, 'E.E', '1', '5'),
(56, 3, '40', 4, 3, 'M.E', '1', '5'),
(58, 3, '20', 2, 85, '-', '1', '8'),
(59, 3, '23', 2, 45, '-', '1', '8'),
(60, 3, '24', 2, 55, '-', '1', '8'),
(61, 3, '25', 2, 66, '-', '1', '8'),
(62, 3, '32', 2, 42, '-', '1', '8'),
(63, 3, '41', 2, 53, '-', '1', '8'),
(64, 3, 'FGF11', 2, 52, '-', '1', '8'),
(65, 3, 'LBSMIS12', 2, 32, '-', '1', '8'),
(66, 3, 'SDSF', 2, 55, '-', '1', '8'),
(67, 3, 'KJHKJHJ', 2, 65, '-', '1', '8'),
(68, 3, 'HGJHGJH', 2, 45, '-', '1', '8'),
(69, 3, '37', 2, 66, '-', '1', '8'),
(70, 3, '40', 2, 75, '-', '1', '8'),
(71, 3, '1', 2, 60, '-', '1', '8'),
(72, 1, '6', 2, 77, 'B', '1', '7'),
(73, 1, '8', 2, 46, 'C', '1', '7'),
(74, 1, '9', 2, 86, 'A', '1', '7'),
(75, 1, '10', 2, 36, 'D', '1', '7'),
(76, 1, '11', 2, 47, 'C', '1', '7'),
(77, 1, '12', 2, 78, 'B', '1', '7'),
(78, 1, '13', 2, 85, 'A', '1', '7'),
(79, 1, '14', 2, 23, 'E', '1', '7'),
(80, 1, '22', 2, 32, 'D', '1', '7'),
(81, 1, '34', 2, 55, 'C', '1', '7'),
(82, 1, '36', 2, 74, 'B', '1', '7'),
(83, 1, 'HJHGJHG', 2, 55, 'C', '1', '7'),
(84, 1, 'LBS102', 2, 77, 'B', '1', '7'),
(85, 1, 'MGM101', 2, 41, 'D', '1', '7'),
(86, 1, 'MGM102', 2, 65, 'B', '1', '7'),
(87, 1, 'MGM103', 2, 33, 'D', '1', '7'),
(88, 7, '15', 1, 42, 'A', '1', '6'),
(89, 7, '16', 1, 33, 'B', '1', '6'),
(90, 7, '18', 1, 42, 'A', '1', '6'),
(91, 8, '6', 4, 56, 'C', '1', '7'),
(92, 8, '8', 4, 77, 'B', '1', '7'),
(93, 8, '9', 4, 55, 'C', '1', '7'),
(94, 8, '10', 4, 68, 'B', '1', '7'),
(95, 8, '11', 4, 77, 'B', '1', '7'),
(98, 8, '12', 4, 95, 'A', '1', '7'),
(99, 8, '13', 4, 45, 'C', '1', '7'),
(100, 8, '14', 4, 33, 'D', '1', '7'),
(102, 8, '22', 4, 88, 'A', '1', '7'),
(103, 8, '34', 4, 49, 'C', '1', '7'),
(104, 8, '34', 4, 49, 'C', '1', '7'),
(105, 8, '36', 4, 66, 'B', '1', '7'),
(106, 8, '36', 4, 66, 'B', '1', '7'),
(107, 8, 'HJHGJHG', 4, 72, 'B', '1', '7'),
(108, 8, 'LBS102', 4, 78, 'B', '1', '7'),
(109, 8, 'MGM101', 4, 75, 'B', '1', '7'),
(110, 8, 'MGM102', 4, 49, 'C', '1', '7'),
(111, 8, 'MGM103', 4, 56, 'C', '1', '7'),
(112, 8, '37', 4, 65, 'B', '1', '8'),
(113, 8, '20', 4, 88, 'A', '1', '8'),
(114, 8, '19', 4, 55, 'C', '1', '8'),
(115, 8, '23', 4, 75, 'B', '1', '8'),
(116, 8, '32', 4, 68, 'B', '1', '8'),
(117, 8, '41', 4, 54, 'C', '1', '8'),
(118, 8, '40', 4, 59, 'C', '1', '8'),
(119, 8, 'KJHKJHJ', 4, 67, 'B', '1', '8'),
(120, 8, '1', 4, 73, 'B', '1', '8'),
(121, 8, '24', 4, 45, 'C', '1', '8'),
(122, 8, '25', 4, 77, 'B', '1', '8'),
(123, 8, 'HGJHGJH', 4, 66, 'B', '1', '8'),
(125, 8, 'LBSMIS12', 4, 89, 'A', '1', '8'),
(126, 8, 'FGF11', 4, 44, 'D', '1', '8'),
(127, 8, '35', 4, 36, 'D', '1', '8'),
(128, 8, '38', 4, 68, 'B', '1', '8'),
(129, 8, 'MGM104', 4, 78, 'B', '1', '8'),
(130, 9, '18', 1, 26, 'C', '1', '6'),
(131, 9, '16', 1, 32, 'B', '1', '6'),
(132, 9, '15', 1, 44, 'A', '1', '6'),
(133, 9, '1', 1, 33, 'B', '1', '8'),
(134, 9, '19', 1, 44, 'A', '1', '8'),
(135, 9, '20', 1, 24, 'C', '1', '8'),
(136, 9, '23', 1, 25, 'C', '1', '8'),
(137, 9, '24', 1, 35, 'B', '1', '8'),
(138, 9, '25', 1, 45, 'A', '1', '8'),
(139, 9, '25', 1, 45, 'A', '1', '8'),
(140, 9, '25', 1, 45, 'A', '1', '8'),
(141, 9, '32', 1, 45, 'A', '1', '8'),
(142, 9, '41', 1, 22, 'C', '1', '8'),
(143, 9, '41', 1, 22, 'C', '1', '8'),
(144, 9, '41', 1, 22, 'C', '1', '8'),
(145, 9, '40', 1, 30, 'B', '1', '8'),
(146, 9, '37', 1, 42, 'A', '1', '8'),
(147, 9, 'HGJHGJH', 1, 26, 'C', '1', '8'),
(148, 9, 'KJHKJHJ', 1, 6, 'E', '1', '8'),
(149, 9, 'SDSF', 1, 47, 'A', '1', '8'),
(150, 9, 'LBSMIS12', 1, 48, 'A', '1', '8'),
(151, 9, 'FGF11', 1, 36, 'B', '1', '8'),
(152, 9, '35', 1, 34, 'B', '1', '8'),
(153, 9, '38', 1, 44, 'A', '1', '8'),
(154, 9, 'MGM104', 1, 33, 'B', '1', '8');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `expid` int(11) NOT NULL,
  `exp_name` varchar(100) NOT NULL,
  `exp_category` varchar(300) NOT NULL,
  `unit_name` varchar(30) DEFAULT NULL,
  `exp_quantity` int(30) NOT NULL,
  `exp_unit_cost` int(30) NOT NULL,
  `exp_amount` int(30) NOT NULL DEFAULT 0,
  `expense_date` date NOT NULL,
  `exp_time` varchar(10) NOT NULL,
  `exp_active` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`expid`, `exp_name`, `exp_category`, `unit_name`, `exp_quantity`, `exp_unit_cost`, `exp_amount`, `expense_date`, `exp_time`, `exp_active`) VALUES
(1, 'MAIZE', 'utility', '', 90, 66, 5940, '2021-09-13', '14:54:42', 0),
(2, 'KRA', 'taxes', '', 1, 15000, 15000, '2021-09-13', '14:58:46', 0),
(3, 'Nurse', 'Medical', '', 2, 2200, 4400, '2021-09-13', '16:18:46', 0),
(4, 'HONEY', 'utility', 'lts', 1, 600, 600, '2021-09-20', '09:10:13', 0),
(5, 'Unga', 'utility', 'Kgs', 100, 100, 10000, '2021-09-21', '17:15:24', 0),
(6, 'Rice', 'daily-expense', 'Kgs', 20, 130, 2600, '2021-09-22', '08:41:17', 0),
(7, 'Sugar', 'daily-expense', '', 1, 80, 80, '2021-10-04', '13:55:55', 0),
(8, 'RIce', 'daily-expense', 'Kg', 1, 80, 80, '2021-10-06', '10:07:46', 0),
(9, 'WATER', 'daily-expense', 'LITRES', 12, 10, 120, '2021-10-12', '11:59:52', 0),
(10, 'BREAD', 'labour', '', 100, 50, 5000, '2021-10-12', '12:00:23', 0),
(11, 'LICENCE', 'taxes', '', 1, 1000, 1000, '2021-10-12', '12:02:38', 0),
(12, 'SERVICE', 'labour', 'kgs', 100, 10, 1000, '2021-10-13', '11:47:44', 0),
(13, 'KODI', 'Rent', '', 1, 45000, 45000, '2021-10-16', '11:52:12', 0),
(14, 'sugar', 'labour', 'kgs', 20, 100, 2000, '2022-02-11', '16:41:48', 0),
(15, 'Diary Meal', 'utility', '', 1000, 1, 1000, '2022-02-14', '19:15:24', 0),
(16, 'Sugar', 'daily-expense', 'Kgs', 12, 100, 1200, '2022-06-28', '19:40:02', 0),
(17, 'Fuel Bus KDA 101P', 'daily-expense', 'Ltrs', 10, 159, 1590, '2022-09-26', '13:38:54', 0);

-- --------------------------------------------------------

--
-- Table structure for table `fees_structure`
--

CREATE TABLE `fees_structure` (
  `expenses` varchar(100) NOT NULL,
  `TERM_1` int(10) NOT NULL,
  `TERM_2` int(10) NOT NULL,
  `TERM_3` int(100) NOT NULL,
  `classes` varchar(100) NOT NULL DEFAULT '0-11',
  `ids` int(100) NOT NULL,
  `activated` int(1) NOT NULL DEFAULT 1,
  `roles` varchar(30) NOT NULL,
  `date_changed` varchar(11) DEFAULT NULL,
  `term_1_old` int(10) NOT NULL DEFAULT 0,
  `term_2_old` int(10) NOT NULL DEFAULT 0,
  `term_3_old` int(10) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fees_structure`
--

INSERT INTO `fees_structure` (`expenses`, `TERM_1`, `TERM_2`, `TERM_3`, `classes`, `ids`, `activated`, `roles`, `date_changed`, `term_1_old`, `term_2_old`, `term_3_old`) VALUES
('Kitale 1', 2000, 2000, 2000, '|8|,|7|', 9, 1, 'regular', '2022-04-25', 2000, 2000, 2000),
('Tution 3', 25000, 15000, 10000, '|8|,|7|,|6|,|5|,|4|,|1|', 10, 1, 'regular', '2022-09-26', 25000, 15000, 10000),
('borders', 5000, 5000, 5000, '|8|,|7|,|6|,|5|,|4|,|3|,|2|,|1|', 11, 1, 'boarding', '2022-05-31', 5000, 5000, 5000),
('TRIPS FEE', 2000, 4000, 6000, '|5|,|4|,|3|,|2|,|1|', 13, 1, 'provisional', '2022-09-25', 2000, 4000, 6000);

-- --------------------------------------------------------

--
-- Table structure for table `finance`
--

CREATE TABLE `finance` (
  `stud_admin` varchar(201) NOT NULL,
  `transaction_id` int(30) NOT NULL,
  `time_of_transaction` varchar(30) NOT NULL,
  `date_of_transaction` varchar(30) NOT NULL,
  `transaction_code` varchar(100) NOT NULL DEFAULT '0',
  `amount` int(10) NOT NULL DEFAULT 0,
  `balance` int(10) NOT NULL DEFAULT 0,
  `payment_for` varchar(100) NOT NULL,
  `payBy` varchar(100) NOT NULL DEFAULT 'sys',
  `mode_of_pay` varchar(50) NOT NULL,
  `status` int(1) NOT NULL DEFAULT 0 COMMENT 'status 1 shows is reversed',
  `idsd` varchar(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `finance`
--

INSERT INTO `finance` (`stud_admin`, `transaction_id`, `time_of_transaction`, `date_of_transaction`, `transaction_code`, `amount`, `balance`, `payment_for`, `payBy`, `mode_of_pay`, `status`, `idsd`) VALUES
('9', 262, '13:05:55', '2022-01-03', 'cash', 60000, 51500, 'BOARDERS', '1', 'cash', 0, '0'),
('6', 263, '13:12:15', '2022-11-03', 'cash', 0, 5000, 'BOARDERS', '1', 'cash', 0, '0'),
('1', 264, '15:36:28', '2022-11-03', 'cash', 50000, 87500, 'Kitale 1', '1', 'cash', 0, '0'),
('19', 265, '16:03:07', '2022-11-03', 'cash', 137500, 0, 'borders', '1', 'cash', 0, '0'),
('10', 269, '18:17:43', '2022-11-17', 'cash', 5000, 7000, 'Tution 3', '1', 'cash', 0, '0'),
('9', 270, '19:20:12', '2022-11-17', 'cash', 25000, 108500, 'Kitale 1', '1', 'cash', 0, '0'),
('9', 271, '11:10:58', '2022-11-21', 'cash', 1000, 107500, 'Kitale 1', '1', 'cash', 0, '0');

-- --------------------------------------------------------

--
-- Table structure for table `leave_categories`
--

CREATE TABLE `leave_categories` (
  `leave_title` varchar(300) DEFAULT NULL,
  `gender` varchar(100) DEFAULT NULL,
  `max_days` int(20) DEFAULT NULL,
  `leave_year_starts` varchar(500) DEFAULT NULL,
  `days_are_accrued` varchar(500) DEFAULT NULL,
  `period_accrued` varchar(500) DEFAULT NULL,
  `max_days_carry_forward` int(10) DEFAULT NULL,
  `employment_type` varchar(500) DEFAULT NULL,
  `active` int(20) NOT NULL DEFAULT 1,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `leave_categories`
--

INSERT INTO `leave_categories` (`leave_title`, `gender`, `max_days`, `leave_year_starts`, `days_are_accrued`, `period_accrued`, `max_days_carry_forward`, `employment_type`, `active`, `id`) VALUES
('Annual Leave', 'All', 21, 'Start Of Academic Yr', 'Monthly', 'Start Of Month', 10, NULL, 1, 1),
('Paternal Leave', 'Male', 14, 'Start Of Academic Yr', 'Yearly', 'Start Of Year', 0, NULL, 1, 2),
('Maternity Leave', 'All', 90, 'Start Of Academic Yr', 'Yearly', 'Start Of Year', 0, NULL, 1, 3),
('Compassionate Leave', 'All', 12, 'Start Of Academic Yr', 'Weekly', 'Start Of Week', 0, NULL, 1, 4);

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `login_time` varchar(10) NOT NULL,
  `active_time` varchar(10) NOT NULL,
  `date` date NOT NULL,
  `user_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `login_time`, `active_time`, `date`, `user_id`) VALUES
(1, '12:26:47', '23:10:19', '2021-09-13', 1),
(2, '10:58:43', '20:12:14', '2021-09-14', 1),
(3, '09:12:39', '19:21:09', '2021-09-15', 1),
(4, '12:13:00', '23:59:59', '2021-09-16', 1),
(5, '00:00:01', '00:17:23', '2021-09-17', 1),
(6, '11:19:35', '18:58:03', '2021-09-18', 1),
(7, '22:03:44', '23:59:59', '2021-09-19', 1),
(8, '00:00:01', '21:31:55', '2021-09-20', 1),
(9, '10:29:44', '23:59:32', '2021-09-21', 1),
(10, '13:41:28', '17:02:29', '2021-09-21', 9),
(11, '00:00:32', '23:59:09', '2021-09-22', 1),
(12, '09:04:32', '09:11:32', '2021-09-22', 15),
(13, '09:08:43', '09:12:22', '2021-09-22', 12),
(14, '09:13:09', '09:13:23', '2021-09-22', 17),
(15, '09:13:58', '09:57:02', '2021-09-22', 11),
(16, '15:37:58', '15:39:12', '2021-09-22', 16),
(17, '15:41:33', '15:47:05', '2021-09-22', 18),
(18, '16:04:49', '16:05:20', '2021-09-22', 8),
(19, '00:00:09', '20:46:05', '2021-09-23', 1),
(20, '09:45:55', '17:57:36', '2021-09-24', 1),
(21, '11:07:49', '11:09:37', '2021-09-25', 1),
(22, '10:17:15', '21:04:12', '2021-09-26', 1),
(23, '11:33:39', '11:44:11', '2021-09-26', 29),
(24, '11:38:35', '11:51:32', '2021-09-26', 19),
(25, '19:44:34', '23:59:03', '2021-09-27', 1),
(26, '19:55:17', '20:24:39', '2021-09-27', 7),
(27, '00:00:03', '18:12:15', '2021-09-28', 1),
(28, '10:35:20', '18:53:00', '2021-09-29', 1),
(29, '02:06:11', '22:44:22', '2021-09-30', 1),
(30, '15:14:57', '15:17:26', '2021-09-30', 7),
(31, '15:17:46', '15:20:52', '2021-09-30', 17),
(32, '10:19:24', '19:12:08', '2021-10-01', 1),
(33, '19:12:30', '23:45:53', '2021-10-01', 9),
(34, '10:02:47', '22:41:57', '2021-10-02', 1),
(35, '10:19:11', '19:26:43', '2021-10-03', 1),
(36, '11:04:40', '11:07:14', '2021-10-03', 2),
(37, '11:01:27', '23:59:31', '2021-10-04', 1),
(38, '00:00:31', '22:15:45', '2021-10-05', 1),
(39, '16:34:21', '16:46:00', '2021-10-05', 15),
(40, '09:21:07', '10:11:46', '2021-10-06', 1),
(41, '09:43:51', '10:06:34', '2021-10-06', 2),
(42, '22:12:56', '23:59:59', '2021-10-06', 7),
(43, '00:00:01', '00:29:47', '2021-10-07', 7),
(44, '15:00:20', '18:45:58', '2021-10-07', 1),
(45, '11:17:23', '23:07:15', '2021-10-09', 1),
(46, '12:50:14', '17:28:13', '2021-10-10', 1),
(47, '03:26:59', '23:59:59', '2021-10-12', 1),
(48, '00:00:01', '23:59:56', '2021-10-13', 1),
(49, '00:00:56', '23:59:52', '2021-10-14', 1),
(50, '00:00:52', '01:56:52', '2021-10-15', 1),
(51, '11:22:47', '21:59:27', '2021-10-16', 1),
(52, '14:18:28', '21:15:14', '2021-10-16', 2),
(53, '16:00:51', '16:01:53', '2021-10-18', 1),
(54, '12:27:02', '23:59:58', '2021-10-20', 1),
(55, '00:00:00', '03:01:57', '2021-10-21', 1),
(56, '22:16:19', '23:59:54', '2021-10-22', 1),
(57, '00:00:54', '23:59:27', '2021-10-23', 1),
(58, '00:00:27', '05:46:27', '2021-10-24', 1),
(59, '12:37:44', '23:46:50', '2021-10-25', 1),
(60, '00:41:50', '23:59:29', '2021-10-26', 1),
(61, '00:00:29', '23:59:20', '2021-10-27', 1),
(62, '00:00:20', '23:59:56', '2021-10-28', 1),
(63, '00:00:56', '12:52:24', '2021-10-29', 1),
(64, '14:12:27', '22:42:04', '2021-10-30', 1),
(65, '12:06:13', '23:59:59', '2021-10-31', 1),
(66, '00:00:01', '13:06:23', '2021-11-01', 1),
(67, '10:40:59', '15:59:36', '2021-11-02', 1),
(68, '14:42:22', '14:46:12', '2021-11-02', 18),
(69, '11:39:29', '22:34:28', '2021-11-23', 1),
(70, '08:45:28', '13:37:24', '2021-11-24', 1),
(71, '15:33:52', '17:10:48', '2021-11-26', 1),
(72, '10:31:50', '13:46:18', '2021-11-27', 1),
(73, '18:20:52', '19:47:17', '2021-11-28', 1),
(74, '14:04:42', '22:28:27', '2021-11-29', 1),
(75, '10:19:46', '22:02:11', '2021-11-30', 1),
(76, '19:37:12', '19:41:22', '2021-11-30', 2),
(77, '15:00:31', '15:46:47', '2021-12-01', 7),
(78, '17:30:06', '23:59:59', '2021-12-01', 1),
(79, '00:00:59', '01:02:39', '2021-12-02', 1),
(80, '15:42:36', '16:29:47', '2021-12-06', 1),
(81, '10:02:37', '12:29:48', '2021-12-07', 1),
(82, '18:49:06', '23:59:50', '2021-12-08', 1),
(83, '00:00:50', '12:48:15', '2021-12-09', 1),
(84, '00:58:17', '00:58:28', '2021-12-10', 1),
(85, '13:47:45', '17:46:29', '2021-12-14', 1),
(86, '13:36:32', '19:48:01', '2021-12-15', 1),
(87, '15:33:35', '16:35:14', '2021-12-16', 1),
(88, '18:08:03', '23:37:37', '2021-12-17', 1),
(89, '09:42:21', '09:51:44', '2021-12-22', 1),
(90, '15:57:18', '16:20:06', '2021-12-26', 1),
(91, '08:25:45', '08:29:13', '2021-12-31', 1),
(92, '09:54:26', '12:00:14', '2022-01-05', 1),
(93, '09:44:20', '09:51:22', '2022-01-07', 1),
(94, '16:08:43', '16:13:20', '2022-01-08', 1),
(95, '16:23:50', '23:55:35', '2022-01-13', 7),
(96, '15:48:57', '15:49:50', '2022-01-15', 1),
(97, '12:37:46', '18:45:44', '2022-01-17', 1),
(98, '00:26:38', '01:32:46', '2022-01-18', 1),
(99, '12:31:03', '12:41:59', '2022-01-19', 1),
(100, '12:54:25', '13:05:22', '2022-02-08', 1),
(101, '14:52:21', '20:52:24', '2022-02-10', 1),
(102, '10:43:56', '20:08:37', '2022-02-11', 1),
(103, '14:32:32', '23:59:35', '2022-02-13', 1),
(104, '00:00:35', '22:16:10', '2022-02-14', 1),
(105, '12:04:16', '15:18:40', '2022-02-15', 1),
(106, '11:08:52', '23:59:29', '2022-02-16', 1),
(107, '00:00:29', '21:16:23', '2022-02-17', 1),
(108, '14:04:15', '17:44:28', '2022-02-19', 1),
(109, '14:28:22', '17:02:03', '2022-03-06', 1),
(110, '15:13:16', '17:16:05', '2022-03-11', 7),
(111, '11:11:37', '21:11:51', '2022-03-23', 1),
(112, '11:59:48', '23:59:48', '2022-03-24', 1),
(113, '00:00:48', '23:59:43', '2022-03-25', 1),
(114, '00:00:42', '02:18:42', '2022-03-26', 1),
(115, '11:38:10', '22:29:43', '2022-03-28', 1),
(116, '12:07:44', '23:09:09', '2022-03-29', 1),
(117, '12:19:46', '17:16:02', '2022-04-01', 1),
(118, '19:54:53', '23:59:59', '2022-04-02', 1),
(119, '00:00:01', '23:59:31', '2022-04-03', 1),
(120, '00:00:31', '03:22:31', '2022-04-04', 1),
(121, '21:24:37', '23:59:42', '2022-04-07', 1),
(122, '00:00:42', '23:45:34', '2022-04-08', 1),
(123, '17:47:36', '22:20:54', '2022-04-09', 1),
(124, '13:01:11', '17:33:22', '2022-04-11', 1),
(125, '17:00:09', '23:59:28', '2022-04-15', 1),
(126, '00:00:28', '02:53:02', '2022-04-16', 1),
(127, '13:58:02', '21:53:49', '2022-04-19', 1),
(128, '18:59:06', '22:56:55', '2022-04-20', 1),
(129, '11:55:21', '19:02:28', '2022-04-21', 1),
(130, '12:32:12', '18:24:32', '2022-04-22', 1),
(131, '16:16:46', '16:18:45', '2022-04-22', 2),
(132, '12:07:41', '23:59:56', '2022-04-23', 1),
(133, '00:00:56', '23:59:50', '2022-04-24', 1),
(134, '00:00:50', '08:38:50', '2022-04-25', 1),
(135, '06:05:41', '07:28:13', '2022-04-25', 38),
(136, '18:18:34', '23:59:39', '2022-04-26', 1),
(137, '00:00:35', '01:10:35', '2022-04-27', 1),
(138, '17:20:07', '23:59:14', '2022-05-02', 1),
(139, '17:27:08', '17:39:28', '2022-05-02', 38),
(140, '00:00:14', '01:06:14', '2022-05-03', 1),
(141, '13:12:31', '23:59:19', '2022-05-12', 1),
(142, '00:00:20', '23:59:44', '2022-05-13', 1),
(143, '00:00:44', '23:59:38', '2022-05-14', 1),
(144, '00:00:38', '01:35:38', '2022-05-15', 1),
(145, '10:05:59', '17:02:31', '2022-05-16', 1),
(146, '20:51:32', '23:24:41', '2022-05-19', 1),
(147, '12:25:58', '23:59:11', '2022-05-23', 1),
(148, '00:00:11', '23:59:25', '2022-05-24', 1),
(149, '00:00:25', '23:59:47', '2022-05-25', 1),
(150, '15:02:26', '15:02:48', '2022-05-25', 5),
(151, '00:00:47', '23:59:46', '2022-05-26', 1),
(152, '16:06:55', '22:20:11', '2022-05-26', 5),
(153, '00:00:46', '18:26:46', '2022-05-27', 1),
(154, '16:18:13', '23:59:38', '2022-05-30', 1),
(155, '00:00:38', '23:59:00', '2022-05-31', 1),
(156, '00:00:00', '23:59:53', '2022-06-01', 1),
(157, '11:55:12', '12:01:08', '2022-06-01', 38),
(158, '00:00:53', '23:59:46', '2022-06-02', 1),
(159, '00:00:46', '23:59:38', '2022-06-03', 1),
(160, '00:00:38', '23:18:20', '2022-06-04', 1),
(161, '16:33:10', '19:39:18', '2022-06-06', 1),
(162, '15:15:52', '23:59:38', '2022-06-07', 1),
(163, '00:00:30', '00:06:23', '2022-06-08', 1),
(164, '10:09:56', '23:59:59', '2022-06-09', 1),
(165, '00:00:01', '23:59:32', '2022-06-10', 1),
(166, '20:43:08', '21:51:50', '2022-06-10', 12),
(167, '21:52:06', '22:01:38', '2022-06-10', 7),
(168, '22:01:53', '22:25:25', '2022-06-10', 39),
(169, '00:00:32', '04:14:32', '2022-06-11', 1),
(170, '11:00:01', '23:59:27', '2022-06-13', 1),
(171, '00:00:27', '02:58:27', '2022-06-14', 1),
(172, '13:16:39', '23:59:50', '2022-06-15', 1),
(173, '00:00:50', '02:20:50', '2022-06-16', 1),
(174, '15:16:06', '23:15:18', '2022-06-27', 1),
(175, '11:52:19', '23:59:41', '2022-06-28', 1),
(176, '00:00:41', '23:59:34', '2022-06-29', 1),
(177, '00:00:34', '19:13:49', '2022-06-30', 1),
(178, '20:13:18', '23:59:14', '2022-07-13', 1),
(179, '00:00:14', '02:04:14', '2022-07-14', 1),
(180, '13:03:05', '22:08:18', '2022-07-18', 1),
(181, '22:08:30', '23:59:08', '2022-07-18', 7),
(182, '00:00:08', '04:06:06', '2022-07-19', 7),
(183, '14:31:31', '23:59:52', '2022-07-23', 1),
(184, '00:00:16', '23:59:47', '2022-07-24', 1),
(185, '00:00:47', '19:27:53', '2022-07-25', 1),
(186, '01:57:25', '23:59:10', '2022-07-26', 1),
(187, '00:00:10', '22:26:21', '2022-07-27', 1),
(188, '22:26:37', '23:59:59', '2022-07-27', 40),
(189, '00:00:01', '01:09:33', '2022-07-28', 40),
(190, '01:09:51', '03:11:04', '2022-07-28', 1),
(191, '10:13:58', '19:46:37', '2022-08-04', 1),
(192, '15:18:43', '23:59:58', '2022-08-06', 1),
(193, '00:00:00', '15:17:33', '2022-08-07', 1),
(194, '15:17:54', '15:21:48', '2022-08-07', 2),
(195, '09:44:07', '22:01:40', '2022-08-08', 1),
(196, '20:33:45', '20:50:09', '2022-08-13', 1),
(197, '14:10:34', '23:59:08', '2022-08-22', 1),
(198, '00:00:08', '23:59:00', '2022-08-23', 1),
(199, '00:00:00', '23:59:52', '2022-08-24', 1),
(200, '00:00:52', '23:59:58', '2022-08-25', 1),
(201, '00:00:01', '23:59:39', '2022-08-26', 1),
(202, '00:00:39', '23:59:33', '2022-08-27', 1),
(203, '00:00:33', '23:59:26', '2022-08-28', 1),
(204, '00:00:26', '23:59:20', '2022-08-29', 1),
(205, '00:00:20', '23:59:12', '2022-08-30', 1),
(206, '00:00:12', '23:59:47', '2022-08-31', 1),
(207, '00:00:47', '23:59:54', '2022-09-01', 1),
(208, '00:00:54', '23:59:43', '2022-09-02', 1),
(209, '00:00:43', '18:43:47', '2022-09-03', 1),
(210, '16:28:55', '17:54:22', '2022-09-05', 1),
(211, '18:01:43', '23:22:07', '2022-09-13', 1),
(212, '11:47:54', '23:59:11', '2022-09-14', 1),
(213, '00:00:11', '19:29:10', '2022-09-15', 1),
(214, '12:17:06', '23:59:26', '2022-09-16', 1),
(215, '00:00:26', '23:59:25', '2022-09-17', 1),
(216, '00:00:25', '04:05:53', '2022-09-18', 1),
(217, '16:05:51', '23:59:08', '2022-09-19', 1),
(218, '00:00:08', '22:14:46', '2022-09-20', 1),
(219, '18:01:13', '20:15:45', '2022-09-21', 1),
(220, '20:23:53', '23:59:44', '2022-09-23', 1),
(221, '00:00:44', '01:16:33', '2022-09-24', 1),
(222, '21:29:05', '23:39:28', '2022-09-25', 1),
(223, '12:20:23', '19:32:57', '2022-09-26', 1),
(224, '17:46:04', '21:26:13', '2022-09-27', 1),
(225, '13:44:03', '23:59:48', '2022-09-30', 1),
(226, '00:00:48', '05:52:48', '2022-10-01', 1),
(227, '15:20:01', '22:08:45', '2022-10-03', 1),
(228, '15:41:32', '17:16:34', '2022-10-04', 1),
(229, '17:16:50', '23:59:59', '2022-10-04', 5),
(230, '00:00:59', '02:32:59', '2022-10-05', 5),
(231, '11:48:22', '23:59:44', '2022-10-06', 1),
(232, '13:13:29', '14:46:36', '2022-10-06', 43),
(233, '00:00:44', '00:30:26', '2022-10-07', 1),
(234, '17:47:24', '20:59:46', '2022-10-10', 1),
(235, '16:46:38', '19:19:54', '2022-10-12', 7),
(236, '17:50:17', '19:03:28', '2022-10-19', 1),
(237, '14:47:46', '23:59:17', '2022-10-22', 1),
(238, '00:00:17', '21:09:04', '2022-10-23', 1),
(239, '02:13:35', '02:50:04', '2022-10-24', 1),
(240, '15:28:05', '23:59:44', '2022-10-27', 1),
(241, '00:00:44', '21:32:42', '2022-10-28', 1),
(242, '12:24:39', '23:59:27', '2022-10-29', 1),
(243, '00:00:27', '23:59:20', '2022-10-30', 1),
(244, '00:00:20', '20:22:12', '2022-10-31', 1),
(245, '12:02:58', '23:07:09', '2022-11-01', 1),
(246, '10:44:29', '21:10:57', '2022-11-02', 1),
(247, '11:25:24', '23:59:55', '2022-11-03', 1),
(248, '00:00:55', '20:36:12', '2022-11-04', 1),
(249, '13:52:47', '23:16:50', '2022-11-05', 1),
(250, '19:02:08', '23:59:08', '2022-11-06', 1),
(251, '00:00:08', '14:44:20', '2022-11-07', 1),
(252, '11:43:51', '13:21:22', '2022-11-09', 1),
(253, '11:27:57', '21:05:13', '2022-11-10', 1),
(254, '13:10:01', '19:08:51', '2022-11-16', 1),
(255, '10:58:56', '23:59:26', '2022-11-17', 1),
(256, '00:00:26', '13:00:19', '2022-11-18', 1),
(257, '12:03:25', '20:41:57', '2022-11-19', 1),
(258, '17:03:37', '23:03:17', '2022-11-20', 1),
(259, '10:53:45', '23:59:32', '2022-11-21', 1),
(260, '20:12:23', '20:13:23', '2022-11-21', 42),
(261, '00:00:32', '23:15:51', '2022-11-22', 1),
(262, '12:27:39', '12:32:35', '2022-11-22', 5),
(263, '10:22:25', '20:44:37', '2022-11-23', 1),
(264, '08:11:54', '23:59:26', '2022-11-24', 1),
(265, '00:00:26', '23:59:19', '2022-11-25', 1),
(266, '00:00:19', '20:55:13', '2022-11-26', 1),
(267, '12:42:32', '23:59:06', '2022-11-27', 1),
(268, '00:00:06', '23:59:58', '2022-11-28', 1),
(269, '12:02:26', '20:56:33', '2022-11-28', 2),
(270, '12:03:48', '12:04:47', '2022-11-28', 5),
(271, '00:00:58', '09:53:13', '2022-11-29', 1),
(272, '00:26:45', '01:15:00', '2022-11-29', 42),
(273, '12:47:08', '23:59:07', '2022-11-30', 1),
(274, '00:00:07', '20:39:26', '2022-12-01', 1),
(275, '11:51:25', '23:32:29', '2022-12-03', 1),
(276, '13:05:58', '23:59:41', '2022-12-04', 1),
(277, '00:00:41', '20:31:50', '2022-12-05', 1),
(278, '10:16:01', '23:59:43', '2022-12-06', 1),
(279, '00:00:43', '23:59:36', '2022-12-07', 1),
(280, '00:00:36', '23:59:28', '2022-12-08', 1),
(281, '00:00:28', '18:20:21', '2022-12-09', 1),
(282, '10:01:49', '21:40:03', '2022-12-12', 1),
(283, '11:31:13', '23:59:09', '2022-12-13', 1),
(284, '00:00:09', '19:10:19', '2022-12-14', 1),
(285, '09:55:26', '10:11:20', '2022-12-19', 1);

-- --------------------------------------------------------

--
-- Table structure for table `mpesa_transactions`
--

CREATE TABLE `mpesa_transactions` (
  `transaction_id` int(11) NOT NULL,
  `mpesa_id` varchar(200) DEFAULT NULL,
  `amount` varchar(11) DEFAULT NULL,
  `std_adm` varchar(11) DEFAULT NULL,
  `transaction_time` varchar(30) DEFAULT NULL,
  `short_code` varchar(25) DEFAULT NULL,
  `payment_number` varchar(100) DEFAULT NULL,
  `fullname` varchar(300) DEFAULT NULL,
  `transaction_status` int(2) DEFAULT 0 COMMENT '1 for assigned , 0 for not assigned'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `mpesa_transactions`
--

INSERT INTO `mpesa_transactions` (`transaction_id`, `mpesa_id`, `amount`, `std_adm`, `transaction_time`, `short_code`, `payment_number`, `fullname`, `transaction_status`) VALUES
(1, 'PLR0QR0V56', '5.00', '33', '20220118121323', '4061913', '254713620727', 'OWEN MALINGU ADALA', 1),
(2, 'PLR0QR0V56', '500', '41', '20220118121323', '4061913', '254713620727', 'OWEN MALINGU ADALA', 1),
(3, 'PLR0QR0V56', '500', '22', '20220118121323', '4061913', '254713620727', 'OWEN MALINGU ADALA', 1),
(4, 'PLR0QR0V56', '500', '22', '20220118121323', '4061913', '254713620727', 'OWEN MALINGU ADALA', 1),
(5, 'PLR0QR0V56', '5000', '20', '20220118121323', '4061913', '254713620727', 'OWEN MALINGU ADALA', 1),
(6, 'PLR0QR0V56', '5000.00', '33', '20220118121323', '4061913', '254713620727', 'OWEN MALINGU ADALA', 1),
(7, 'PLR0QR0V56', '3000.00', '33', '20220118121323', '4061913', '254713620727', 'OWEN MALINGU ADALA', 1),
(8, 'PLR0QR0V56', '4000.00', '35', '20220118121323', '4061913', '254713620727', 'OWEN MALINGU ADALA', 1),
(9, 'PLR0QR0V56', '7000.00', '25', '20220118121323', '4061913', '254713620727', 'OWEN MALINGU ADALA', 1),
(10, 'PLR0QR0V56', '1000.00', '22', '20220118121323', '4061913', '254713620727', 'OWEN MALINGU ADALA', 1),
(11, 'PLR0QR0V56', '600.00', '33', '20220118121323', '4061913', '254713620727', 'OWEN MALINGU ADALA', 1);

-- --------------------------------------------------------

--
-- Table structure for table `payroll_information`
--

CREATE TABLE `payroll_information` (
  `staff_id` int(20) DEFAULT NULL,
  `payroll_id` int(20) NOT NULL,
  `current_balance` int(20) DEFAULT NULL,
  `current_balance_monNyear` varchar(300) DEFAULT NULL,
  `salary_amount` varchar(100) DEFAULT NULL,
  `effect_month` varchar(100) DEFAULT NULL,
  `salary_breakdown` longtext DEFAULT NULL,
  `type_of_payment` varchar(200) DEFAULT 'salary'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `payroll_information`
--

INSERT INTO `payroll_information` (`staff_id`, `payroll_id`, `current_balance`, `current_balance_monNyear`, `salary_amount`, `effect_month`, `salary_breakdown`, `type_of_payment`) VALUES
(3, 18, 6000, 'Sep:2021', '10000', 'May:2021', NULL, 'salary'),
(1, 19, 12096, 'Apr:2022', '33000,41196,47100,41096', 'Nov:2021,Jun:2022,Jun:2022,Jun:2022', '{\"gross_salary\":\"33000\",\"personal_relief\":\"yes\",\"nhif_relief\":\"yes\",\"deduct_paye\":\"yes\",\"deduct_nhif\":\"yes\",\"nssf_rates\":\"teir_old\",\"allowances\":[{\"name\":\"HOUSE ALLOWANCE\",\"value\":\"10000\"},{\"name\":\"TRANSPORT ALLOWANCE\",\"value\":\"5000\"}],\"year\":\"2022\"}', 'salary'),
(9, 25, 445, 'Oct:2022', '15000,34289,34179', 'Oct:2021,Jun:2022,Dec:2022', '{\"gross_salary\":\"15000\",\"personal_relief\":\"yes\",\"nhif_relief\":\"yes\",\"deduct_paye\":\"yes\",\"deduct_nhif\":\"yes\",\"nssf_rates\":\"teir_1_2\",\"allowances\":[{\"name\":\"TRANSPORT ALLOWANCE\",\"value\":\"15000\"},{\"name\":\"EDUCATION ALLOWANCE\",\"value\":\"12000\"}],\"year\":\"2022\"}', 'salary'),
(2, 27, 15695, 'Jun:2021', '33695', 'May:2021', '{\"gross_salary\":\"25000\",\"personal_relief\":\"no\",\"nhif_relief\":\"no\",\"deduct_paye\":\"yes\",\"deduct_nhif\":\"yes\",\"nssf_rates\":\"teir_1_2\",\"allowances\":[{\"name\":\"transport allowance\",\"value\":\"10000\"},{\"name\":\"house allowance\",\"value\":\"7000\"}],\"year\":\"2022\"}', 'salary'),
(20, 28, 25990, 'Nov:2021', '33895,29995,29908', 'Sep:2021,Jun:2022,Dec:2022', '{\"gross_salary\":\"20000\",\"personal_relief\":\"yes\",\"nhif_relief\":\"yes\",\"deduct_paye\":\"yes\",\"deduct_nhif\":\"yes\",\"nssf_rates\":\"teir_1_2\",\"allowances\":[{\"name\":\"PILOT ALLOWANCE\",\"value\":\"12000\"},{\"name\":\"HOUSE ALLOWANCE\",\"value\":\"5000\"}],\"year\":\"2022\"}', 'salary'),
(8, 30, 26090, 'May:2022', '33045,33745', 'Mar:2022,Sep:2022', '{\"gross_salary\":\"30000\",\"personal_relief\":\"yes\",\"nhif_relief\":\"no\",\"deduct_paye\":\"yes\",\"deduct_nhif\":\"no\",\"nssf_rates\":\"teir_1_2\",\"allowances\":[{\"name\":\"House allowance\",\"value\":\"6000\"},{\"name\":\"Medical allowance\",\"value\":\"2000\"}],\"year\":\"2022\"}', 'salary'),
(11, 31, 156416, 'Dec:2022', '166361,166416', 'Nov:2022,Dec:2022', '{\"gross_salary\":\"230000\",\"personal_relief\":\"yes\",\"nhif_relief\":\"yes\",\"deduct_paye\":\"yes\",\"deduct_nhif\":\"yes\",\"nssf_rates\":\"teir_1_2\",\"allowances\":\"\",\"year\":\"2021\"}', 'salary'),
(10, 32, 47120, 'Dec:2022', '48560', 'Oct:2022', '{\"gross_salary\":\"50000\",\"personal_relief\":\"yes\",\"nhif_relief\":\"yes\",\"deduct_paye\":\"yes\",\"deduct_nhif\":\"yes\",\"nssf_rates\":\"teir_1_2\",\"allowances\":[{\"name\":\"House allowance\",\"value\":\"10000\"}],\"year\":\"2022\"}', 'salary'),
(12, 35, 15000, 'Mar:2023', '100000', 'Sep:2022', '{\"gross_salary\":\"100000\",\"personal_relief\":\"no\",\"nhif_relief\":\"no\",\"deduct_paye\":\"no\",\"deduct_nhif\":\"no\",\"nssf_rates\":\"none\",\"allowances\":\"\",\"year\":\"2022\"}', 'salary'),
(15, 37, 30210, 'Oct:2022', '72105,71386', 'Aug:2022,Dec:2022', '{\"gross_salary\":\"80000\",\"personal_relief\":\"yes\",\"nhif_relief\":\"yes\",\"deduct_paye\":\"yes\",\"deduct_nhif\":\"yes\",\"nssf_rates\":\"teir_1_2\",\"allowances\":[{\"name\":\"House Allowances\",\"value\":\"10000\"},{\"name\":\"Tree Allowances\",\"value\":\"4000\"}],\"year\":\"2021\"}', 'salary');

-- --------------------------------------------------------

--
-- Table structure for table `salary_payment`
--

CREATE TABLE `salary_payment` (
  `pay_id` int(11) NOT NULL,
  `staff_paid` int(50) DEFAULT NULL,
  `amount_paid` int(50) DEFAULT NULL,
  `mode_of_payment` varchar(50) DEFAULT NULL,
  `payment_code` varchar(100) DEFAULT NULL,
  `date_paid` varchar(20) DEFAULT NULL,
  `time_paid` varchar(20) DEFAULT NULL,
  `type_of_payment` varchar(200) DEFAULT 'salary'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `salary_payment`
--

INSERT INTO `salary_payment` (`pay_id`, `staff_paid`, `amount_paid`, `mode_of_payment`, `payment_code`, `date_paid`, `time_paid`, `type_of_payment`) VALUES
(29, 29, 30000, 'cash', 'cash', '2021-09-26', '11:48:36', 'salary'),
(31, 3, 5000, 'bank', 'JKHKJH', '2021-10-12', '12:09:27', 'salary'),
(32, 3, 10000, 'bank', 'HGJHGJHJ', '2021-10-16', '11:53:35', 'salary'),
(33, 29, 30000, 'cash', 'cash', '2021-10-16', '14:07:10', 'salary'),
(34, 3, 20000, 'bank', 'gjhgjyghg', '2021-11-30', '19:44:21', 'salary'),
(35, 1, 12000, 'cash', 'cash', '2022-02-14', '20:00:07', 'salary'),
(53, 9, 10000, 'cash', 'cash', '2022-04-20', '19:32:32', 'salary'),
(54, 9, 22000, 'bank', 'FDGDFGDFG', '2022-04-22', '19:33:35', 'salary'),
(55, 1, 50000, 'cash', 'cash', '2022-04-21', '12:48:52', 'salary'),
(60, 9, 20000, 'm-pesa', 'IUGJHGJ', '2022-06-13', '17:21:14', 'salary'),
(61, 9, 13000, 'bank', 'FDGHFD', '2022-06-13', '17:46:54', 'salary'),
(64, 9, 12000, 'bank', 'fgdhdf', '2022-06-27', '15:19:19', 'salary'),
(65, 20, 1300, 'bank', 'IYUGJHYG', '2022-06-27', '15:21:53', 'salary'),
(66, 20, 1000, 'bank', 'JHGKJHJH', '2022-06-27', '19:16:36', 'salary'),
(72, 20, 40000, 'cash', 'cash', '2022-06-27', '20:18:07', 'salary'),
(73, 20, 1000, 'bank', 'KJHJGH', '2022-06-28', '19:54:08', 'salary'),
(74, 8, 50000, 'cash', 'cash', '2022-09-26', '14:11:50', 'salary'),
(75, 1, 100000, 'bank', 'HGJHGJHG', '2022-12-03', '14:12:34', 'salary'),
(76, 11, 150000, 'bank', 'FDFFFDFD', '2022-12-03', '14:46:23', 'salary'),
(77, 9, 100000, 'cash', 'cash', '2022-12-03', '20:58:02', 'salary'),
(82, 11, 16416, 'cash', 'cash', '2022-12-07', '13:10:40', 'salary'),
(100, 10, 50000, 'cash', 'cash', '2022-12-07', '14:41:49', 'salary'),
(103, 11, 10000, 'cash', 'cash', '2022-12-07', '14:53:31', 'salary'),
(124, 12, 100001, 'cash', 'cash', '2022-12-07', '20:38:53', 'salary'),
(125, 12, 25000, 'cash', 'cash', '2022-12-07', '20:41:31', 'salary'),
(126, 12, 10000, 'bank', 'JHGJHGJYH', '2022-12-08', '14:16:19', 'salary'),
(127, 12, 100000, 'cash', 'cash', '2022-12-08', '16:33:11', 'salary'),
(128, 12, 150000, 'cash', 'cash', '2022-12-08', '16:35:09', 'salary'),
(129, 12, 200000, 'cash', 'cash', '2022-12-12', '10:56:05', 'salary'),
(130, 9, 100000, 'cash', 'cash', '2022-12-12', '11:14:15', 'salary'),
(131, 15, 115000, 'cash', 'cash', '2022-12-14', '17:40:01', 'salary');

-- --------------------------------------------------------

--
-- Table structure for table `school_vans`
--

CREATE TABLE `school_vans` (
  `van_id` int(11) NOT NULL,
  `van_name` varchar(45) DEFAULT NULL,
  `van_reg_no` varchar(45) DEFAULT NULL,
  `model_name` varchar(300) DEFAULT NULL,
  `van_seater_size` int(11) DEFAULT NULL,
  `route_id` int(11) DEFAULT NULL,
  `insurance_expiration` varchar(40) DEFAULT NULL,
  `next_service_date` varchar(200) DEFAULT NULL,
  `driver_name` int(11) DEFAULT NULL,
  `status` int(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `school_vans`
--

INSERT INTO `school_vans` (`van_id`, `van_name`, `van_reg_no`, `model_name`, `van_seater_size`, `route_id`, `insurance_expiration`, `next_service_date`, `driver_name`, `status`) VALUES
(7, 'JAMES 1', 'KCD 001 H', 'SCANIA', 33, 1, '2022-12-18', '2022-10-23', 20, 1),
(8, 'Prodigal Son', 'LBS 101 V', 'Scania', 65, 2, '2022-10-08', '2022-10-08', 12, 1);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `sett` varchar(200) NOT NULL,
  `valued` mediumtext NOT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`sett`, `valued`, `id`) VALUES
('admissionessentials', 'BREAD,Golf bat', 1),
('class', '1,2,3,4,5,6,7,8', 2),
('lastadmgen', '34', 5),
('user_roles', '[{\"name\":\"HILLARY TESTER\",\"roles\":[{\"name\":\"admitbtn\",\"Status\":\"yes\"},{\"name\":\"findstudsbtn\",\"Status\":\"yes\"},{\"name\":\"callregister\",\"Status\":\"yes\"},{\"name\":\"regstaffs\",\"Status\":\"yes\"},{\"name\":\"managestaf\",\"Status\":\"yes\"},{\"name\":\"promoteStd\",\"Status\":\"yes\"},{\"name\":\"humanresource\",\"Status\":\"no\"},{\"name\":\"payfeess\",\"Status\":\"no\"},{\"name\":\"findtrans\",\"Status\":\"no\"},{\"name\":\"mpesaTrans\",\"Status\":\"no\"},{\"name\":\"feestruct\",\"Status\":\"no\"},{\"name\":\"expenses_btn\",\"Status\":\"no\"},{\"name\":\"finance_report_btn\",\"Status\":\"no\"},{\"name\":\"payroll_sys\",\"Status\":\"yes\"},{\"name\":\"routes_n_trans\",\"Status\":\"no\"},{\"name\":\"enroll_students\",\"Status\":\"no\"},{\"name\":\"regsub\",\"Status\":\"no\"},{\"name\":\"managesub\",\"Status\":\"no\"},{\"name\":\"managetrnsub\",\"Status\":\"no\"},{\"name\":\"generate_tt_btn\",\"Status\":\"no\"},{\"name\":\"examanagement\",\"Status\":\"no\"},{\"name\":\"exam_fill_btn\",\"Status\":\"no\"},{\"name\":\"enroll_boarding_btn\",\"Status\":\"yes\"},{\"name\":\"maanage_dorm\",\"Status\":\"no\"},{\"name\":\"sms_broadcast\",\"Status\":\"yes\"},{\"name\":\"update_school_profile\",\"Status\":\"yes\"},{\"name\":\"update_personal_profile\",\"Status\":\"yes\"},{\"name\":\"set_btns\",\"Status\":\"no\"},{\"name\":\"my_reports\",\"Status\":\"no\"}]},{\"name\":\"Spoke\",\"roles\":[{\"name\":\"admitbtn\",\"Status\":\"yes\"},{\"name\":\"findstudsbtn\",\"Status\":\"yes\"},{\"name\":\"callregister\",\"Status\":\"yes\"},{\"name\":\"regstaffs\",\"Status\":\"yes\"},{\"name\":\"managestaf\",\"Status\":\"yes\"},{\"name\":\"promoteStd\",\"Status\":\"yes\"},{\"name\":\"humanresource\",\"Status\":\"no\"},{\"name\":\"payfeess\",\"Status\":\"yes\"},{\"name\":\"findtrans\",\"Status\":\"yes\"},{\"name\":\"mpesaTrans\",\"Status\":\"yes\"},{\"name\":\"feestruct\",\"Status\":\"yes\"},{\"name\":\"expenses_btn\",\"Status\":\"yes\"},{\"name\":\"finance_report_btn\",\"Status\":\"yes\"},{\"name\":\"payroll_sys\",\"Status\":\"yes\"},{\"name\":\"routes_n_trans\",\"Status\":\"no\"},{\"name\":\"enroll_students\",\"Status\":\"no\"},{\"name\":\"regsub\",\"Status\":\"no\"},{\"name\":\"managesub\",\"Status\":\"no\"},{\"name\":\"managetrnsub\",\"Status\":\"no\"},{\"name\":\"generate_tt_btn\",\"Status\":\"no\"},{\"name\":\"examanagement\",\"Status\":\"no\"},{\"name\":\"exam_fill_btn\",\"Status\":\"no\"},{\"name\":\"enroll_boarding_btn\",\"Status\":\"no\"},{\"name\":\"maanage_dorm\",\"Status\":\"no\"},{\"name\":\"sms_broadcast\",\"Status\":\"no\"},{\"name\":\"update_school_profile\",\"Status\":\"yes\"},{\"name\":\"update_personal_profile\",\"Status\":\"yes\"},{\"name\":\"set_btns\",\"Status\":\"yes\"},{\"name\":\"my_reports\",\"Status\":\"no\"}]},{\"name\":\"HR\",\"roles\":[{\"name\":\"admitbtn\",\"Status\":\"yes\"},{\"name\":\"findstudsbtn\",\"Status\":\"yes\"},{\"name\":\"callregister\",\"Status\":\"yes\"},{\"name\":\"regstaffs\",\"Status\":\"yes\"},{\"name\":\"managestaf\",\"Status\":\"yes\"},{\"name\":\"promoteStd\",\"Status\":\"yes\"},{\"name\":\"humanresource\",\"Status\":\"no\"},{\"name\":\"payfeess\",\"Status\":\"no\"},{\"name\":\"findtrans\",\"Status\":\"no\"},{\"name\":\"mpesaTrans\",\"Status\":\"no\"},{\"name\":\"feestruct\",\"Status\":\"no\"},{\"name\":\"expenses_btn\",\"Status\":\"no\"},{\"name\":\"finance_report_btn\",\"Status\":\"no\"},{\"name\":\"payroll_sys\",\"Status\":\"no\"},{\"name\":\"routes_n_trans\",\"Status\":\"no\"},{\"name\":\"enroll_students\",\"Status\":\"no\"},{\"name\":\"regsub\",\"Status\":\"no\"},{\"name\":\"managesub\",\"Status\":\"no\"},{\"name\":\"managetrnsub\",\"Status\":\"no\"},{\"name\":\"generate_tt_btn\",\"Status\":\"no\"},{\"name\":\"examanagement\",\"Status\":\"no\"},{\"name\":\"exam_fill_btn\",\"Status\":\"no\"},{\"name\":\"enroll_boarding_btn\",\"Status\":\"no\"},{\"name\":\"maanage_dorm\",\"Status\":\"no\"},{\"name\":\"sms_broadcast\",\"Status\":\"no\"},{\"name\":\"update_school_profile\",\"Status\":\"no\"},{\"name\":\"update_personal_profile\",\"Status\":\"yes\"},{\"name\":\"set_btns\",\"Status\":\"no\"},{\"name\":\"my_reports\",\"Status\":\"no\"}]}]', 13),
('clubs/sports_house', '[{\"id\":\"1\",\"Name\":\"Salsa Dances\"},{\"id\":2,\"Name\":\"Dance Clubs\"},{\"id\":3,\"Name\":\"Horses Clubs\"},{\"id\":4,\"Name\":\"Names Clubs\"}]', 14),
('email_setup', '{\"sender_name\":\"Ladybird Softech Co.\",\"email_host_addr\":\"mail.privateemail.com\",\"email_username\":\"mail@ladybirdsmis.com\",\"email_password\":\"2000Hilary\",\"tester_mail\":\"hilaryme45@gmail.com\"}', 19),
('working_days', 'Mon,Tue,Wed,Thur,Fri,Sat', 20),
('last_acad_yr', '[{\"TERM_1\":{\"START_DATE\":\"2021-04-01\",\"END_DATE\":\"2021-06-21\"},\"TERM_2\":{\"START_DATE\":\"2021-06-22\",\"END_DATE\":\"2021-08-30\"},\"TERM_3\":{\"START_DATE\":\"2021-09-01\",\"END_DATE\":\"2022-03-31\"}}]', 24);

-- --------------------------------------------------------

--
-- Table structure for table `sms_api`
--

CREATE TABLE `sms_api` (
  `sms_api_key` varchar(2000) NOT NULL,
  `patner_id` varchar(2000) NOT NULL,
  `short_code` varchar(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sms_table`
--

CREATE TABLE `sms_table` (
  `send_id` int(11) NOT NULL,
  `message_count` int(11) NOT NULL,
  `message_sent_succesfully` int(10) NOT NULL,
  `message_undelivered` int(10) NOT NULL,
  `message_type` varchar(100) NOT NULL,
  `sender_no` int(11) NOT NULL,
  `message_description` varchar(100) NOT NULL,
  `message` varchar(1000) NOT NULL,
  `charged` int(11) NOT NULL DEFAULT 0,
  `date_sent` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sms_table`
--

INSERT INTO `sms_table` (`send_id`, `message_count`, `message_sent_succesfully`, `message_undelivered`, `message_type`, `sender_no`, `message_description`, `message`, `charged`, `date_sent`) VALUES
(93, 1, 1, 1, 'Multicast', 797730265, 'Test message,...', 'Test message,', 0, '2022-12-12');

-- --------------------------------------------------------

--
-- Table structure for table `student_data`
--

CREATE TABLE `student_data` (
  `surname` varchar(100) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `second_name` varchar(100) DEFAULT NULL,
  `index_no` varchar(300) DEFAULT '0',
  `D_O_B` date DEFAULT NULL,
  `gender` varchar(6) DEFAULT NULL,
  `stud_class` varchar(300) DEFAULT NULL,
  `adm_no` varchar(200) NOT NULL,
  `D_O_A` date DEFAULT NULL,
  `parentName` varchar(100) DEFAULT NULL,
  `parentContacts` varchar(100) DEFAULT NULL,
  `parent_relation` varchar(30) DEFAULT NULL,
  `parent_email` varchar(100) DEFAULT NULL,
  `parent_name2` varchar(100) DEFAULT NULL,
  `parent_contact2` varchar(200) DEFAULT NULL,
  `parent_relation2` varchar(200) DEFAULT NULL,
  `parent_email2` varchar(200) DEFAULT NULL,
  `address` varchar(100) DEFAULT 'N/A',
  `BCNo` varchar(30) DEFAULT '0',
  `student_upi` varchar(30) DEFAULT NULL,
  `admissionessentials` varchar(100) DEFAULT NULL,
  `dormitory` varchar(100) DEFAULT 'none',
  `boarding` varchar(10) DEFAULT 'none' COMMENT 'none, enroll, enrolled',
  `examInterview` varchar(10) DEFAULT 'NO',
  `disabled` varchar(5) DEFAULT 'No',
  `disable_describe` mediumtext DEFAULT NULL,
  `deleted` int(1) DEFAULT 0,
  `activated` int(1) DEFAULT 1,
  `ids` int(11) NOT NULL,
  `year_of_study` mediumtext DEFAULT NULL,
  `primary_parent_occupation` varchar(255) DEFAULT NULL,
  `secondary_parent_occupation` varchar(255) DEFAULT NULL,
  `prev_sch_attended` mediumtext DEFAULT NULL,
  `medical_history` mediumtext DEFAULT NULL,
  `source_funding` varchar(255) DEFAULT NULL,
  `clubs_id` varchar(500) DEFAULT NULL,
  `student_image` mediumtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `student_data`
--

INSERT INTO `student_data` (`surname`, `first_name`, `second_name`, `index_no`, `D_O_B`, `gender`, `stud_class`, `adm_no`, `D_O_A`, `parentName`, `parentContacts`, `parent_relation`, `parent_email`, `parent_name2`, `parent_contact2`, `parent_relation2`, `parent_email2`, `address`, `BCNo`, `student_upi`, `admissionessentials`, `dormitory`, `boarding`, `examInterview`, `disabled`, `disable_describe`, `deleted`, `activated`, `ids`, `year_of_study`, `primary_parent_occupation`, `secondary_parent_occupation`, `prev_sch_attended`, `medical_history`, `source_funding`, `clubs_id`, `student_image`) VALUES
('ADALA', 'HILLARY', 'NGIGE', '0', '2019-09-13', 'Male', '8', '1', '2021-09-13', 'Kevin Masilwa', '0743551250', 'Father', 'hilaryme45@gmail.com', 'Maria Wakio', '0704241905', 'num', 'ladybirdsmis@gmail.com', 'Thika', '2147483647', '234221', '', 'none', 'none', 'NO', 'No', 'none', 0, 1, 1, '2021:8|2022:8', 'TEACHER', 'DATA', '[{\"school_name\":\"Makupa Makupa Primary\",\"date_left\":\"2022-09-22\",\"marks_scored\":\"420\",\"leaving_cert\":\"true\",\"reason_for_leaving\":\"Invalid Curcumstances\"},{\"school_name\":\"Jumanji Karani\",\"date_left\":\"2022-08-11\",\"marks_scored\":\"410 \",\"leaving_cert\":\"true\",\"reason_for_leaving\":\"Majaja\"}]', '', NULL, '', 'images/students_profiles/testimonytbl1/1/-afjpvu.jpg'),
('BUKEKO', 'CHARLES', 'KHAMALA', '0', '2019-06-04', 'Male', '-1', '4', '2021-09-22', 'JAMES BUKEKO', '0714215455', 'Father', 'hilaryme45@gmail.com', '', '', '', '', 'Thika, KIambu', 'JKH9878', '878978', 'BREAD,Golf bat', '1', 'enrolled', 'NO', 'No', 'none', 0, 1, 4, '2021:7|2022:-1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('ACHIENG', 'SHARON', 'MANDAU', '0', '2017-01-31', 'Male', '-1', '5', '2021-09-22', 'THIKA MAIN', '0702023350', 'Father', 'esmond@gmail.com', '', NULL, NULL, '', 'Toll, Thika road', '0', '', 'BREAD,Golf bat', '2', 'enrolled', 'NO', 'No', 'none', 0, 1, 5, '2021:8', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('ONYANGO', 'LAURENCE', 'OPIYO', '0', '2012-01-31', 'Male', '7', '6', '2021-09-26', 'BREMI OPIYO', '0705211212', 'Father', 'hilaryme45@gmail.com', 'KAIRU IAN', '0743551250', 'Father', 'kairuian123@gmail.com', 'KAWANGWARE', '0', '78687687', 'BREAD,Golf bat', '1', 'enrolled', 'NO', 'No', '', 0, 1, 6, '2021:6|2022:7', '', 'Business man', '', '', NULL, '2', 'images/students_profiles/testimonytbl1/6/shoe.PNG'),
('ONYANGO', 'LAURENCE', 'OTIENO', '0', '2019-01-01', 'Female', '-2', '7', '2021-10-02', 'terence kinyua', '0714124154', 'Father', 'james@gmail.com', '', '', '', '', 'Industrial Area', '98798', '87687', 'BREAD,Golf bat', 'none', 'none', 'NO', 'No', 'none', 0, 1, 7, '2021:8|2022:-2', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('NJUGUNA', 'TIMOTHY', 'KAMAU', '0', '2016-01-04', 'Female', '7', '8', '2021-10-05', 'Jackline mwende', '0704241515', 'Mother', 'hilaryme45@gmail.com', '', '', '', '', 'Kiambu, Ke', '3243223', '323234', 'BREAD,Golf bat', 'none', 'none', 'NO', 'No', 'none', 0, 1, 8, '2021:5|2022:7', '', '', '', '', NULL, '', NULL),
('MBAPPE', 'KILIAN', 'CHRISTIAN', '0', '2019-10-04', 'Male', '7', '9', '2021-10-05', 'JAMES MUGO', '0714541415', 'Father', 'hilaryme45@gmail.com', '', '', '', '', 'Kitale, INC', '0', '353', 'BREAD,Golf bat', 'none', 'none', 'NO', 'No', 'none', 0, 1, 9, '2021:5|2022:7', '', '', '', '', NULL, '', NULL),
('AKOTH', 'TRACY', 'OUNDO', '0', '2019-10-01', 'Female', '7', '10', '2021-10-04', 'James Omondi', '0745414144', 'Father', 'hilaryme45@gmail.com', '', '', '', '', 'Busia Ke', '423423', '24324', 'BREAD,Golf bat', '1', 'enrolled', 'NO', 'No', 'none', 0, 1, 10, '2021:5|2022:7', '', '', '', '', NULL, '', NULL),
('TUMAINI', 'SHARON', 'ATIENO', '0', '2018-11-22', 'Female', '7', '11', '2022-07-01', '0704245167', '0721112145', 'Father', 'hilaryme45@gmail.com', '', '', '', '', 'NAIROBI, KE', '90809809', '09809809', 'BREAD,Golf bat', '1', 'enrolled', 'NO', 'No', 'none', 0, 1, 11, '2021:5|2022:7', '', '', '', '', NULL, '', NULL),
('KHAMALA', 'CHRISPINUS', 'ODHIAMBO', '0', '2013-01-29', 'Male', '7', '12', '2021-10-04', 'TRACY PANDE', '0799741017', 'Mother', 'hilaryme45@gmail.com', '', '', '', '', 'BUSIA KE', '232DFDSF3', '21321321', 'BREAD,Golf bat', '1', 'enrolled', 'NO', 'No', 'none', 0, 1, 12, '2021:5|2022:7', '', '', '', '', NULL, '', NULL),
('TSUMA', 'CEDRIQUE', 'MUSUNGU', '0', '2011-02-08', 'Male', '7', '13', '2022-11-01', 'JAMES OMONDI', '0754341451', 'Father', 'hilaryme45@gmail.com', '', '', '', '', 'BUSIA KE', 'KJHKJNMN', '78867687', 'BREAD,Golf bat', '2', 'enrolled', 'NO', 'No', 'none', 0, 1, 13, '2021:5|2022:7', '', '', '', '', NULL, '', NULL),
('OMONDI', 'TIMON', 'KHALWALE', '0', '2013-01-29', 'Male', '7', '14', '2021-10-04', 'SAMUEL ETOO', '0714541214', 'Father', 'hilaryme45@gmail.com', '', '', '', '', 'JUJA, KIAMBU', '9898798', '98978', 'BREAD,Golf bat', '1', 'enrolled', 'NO', 'No', 'none', 0, 1, 14, '2021:5|2022:7', '', '', '', '', NULL, '', NULL),
('KAMAU', 'DENZEL', 'PAULINE', '0', '2011-02-01', 'Male', '6', '15', '2021-10-04', 'JANICE OPIYO', '0714155454', 'Father', 'jamesoundo@gmail.com', '', '', '', '', 'KILIFI, MOMBASA', 'KJHKJH990', '8909809', 'BREAD,Golf bat', 'none', 'none', 'NO', 'No', 'none', 0, 1, 15, '2021:5|2022:6', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('JUMA', 'SHARON', 'OMONDI', '0', '2010-02-02', 'Female', '6', '16', '2021-10-04', 'KEVIN JUMA', '0701141545', 'Father', 'kevin@gmail.com', 'Test Parent', '0713620727', 'Mother', 'hilaryme45@gmail.com', 'Busia, Ke', '5413213', '51231213', 'BREAD,Golf bat', '2', 'enrolled', 'NO', 'No', 'none', 0, 1, 16, '2021:4|2022:6', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('OMONDI', 'SARAH', 'KAPULE', '0', '2016-02-09', 'Female', '1', '17', '2021-10-04', 'Janice Modeo', '0704241905', 'Mother', 'janice@gmail.com', '', '', '', '', 'Limuru, Ke', '2423432', '2321423', 'BREAD,Golf bat', '1', 'enrolled', 'NO', 'No', 'none', 0, 1, 17, '2021:4|2022:1', '', '', '', '', NULL, '', 'images/students_profiles/testimonytbl1/17/trade 1.PNG'),
('AMANDA', 'JULIUS', 'MUGANDA', '0', '2010-02-02', 'Male', '6', '18', '2021-10-04', 'THOMAS PATRICK', '0714512114', 'Father', '', '', '', '', '', 'Busia, Ke', '879879', '9879879', 'BREAD,Golf bat', '1', 'enrolled', 'NO', 'No', 'none', 0, 1, 18, '2021:4|2022:6', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('WAKIO', 'MARIA', 'NGIGE', '0', '2014-01-28', 'Female', '8', '19', '2021-10-05', 'JAMES OUNDO', '0704241305', 'Father', 'james@gmail.com', '', '', '', '', 'Thika kiambu', '0', '', 'BREAD,Golf bat', 'none', 'none', 'NO', 'No', 'none', 0, 1, 19, '2021:8|2022:8', NULL, NULL, NULL, NULL, NULL, NULL, 'images/students_profiles/testimonytbl1/19/20220923_134734.jpg'),
('THOMAS', 'PETER', 'EGHAN', '0', '2011-02-01', 'Female', '8', '20', '2021-10-05', 'JOEL ORONDA', '0714245141', 'Father', 'opuko@thaddeus.com', '', '', '', '', 'KENYATTA UNIVERSITY', '0', 'JHKJH98JHK', 'BREAD,Golf bat', 'none', 'enroll', 'NO', 'No', 'none', 0, 1, 20, '2021:8|2022:8', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('OTET', 'DESMOND', 'TUTU', '0', '2011-02-01', 'Male', '-1', '21', '2021-10-05', 'THOMAS ODEDE', '0715214145', 'Father', 'odude@gmail.com', '', '', '', '', 'KIGANJO THIKA', 'FDGDF4343', 'DFGDFGDF', 'BREAD,Golf bat', '1', 'enrolled', 'NO', 'No', 'none', 0, 1, 21, '2021:8', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('SUSAN', 'JOEL', 'KAMAU', '0', '2011-02-01', 'Male', '7', '22', '2021-10-05', 'JOSEPH OJIL', '0704124154', 'Father', 'hilaryme45@gmail.com', '', '', '', '', 'KIMANI WA MATANGI', '3FDSFSDF33', 'GFFGHGF32', 'BREAD,Golf bat', 'none', 'enroll', 'NO', 'No', 'none', 0, 1, 22, '2021:8|2022:7', '', '', '', '', NULL, '', NULL),
('KALE', 'MADUA', 'KHALE', '0', '2011-03-02', 'Female', '8', '23', '2021-10-05', 'JOPHIL AKEYO', '0714542145', 'Father', 'kale@gmail.com', 'JUMA SALIM KASAU', '0743551250', 'Father', 'hilaryme45@gmail.com', 'KITALE KE', '546546521', 'SDFGDSGDS3', 'BREAD,Golf bat', 'none', 'enroll', 'NO', 'No', 'none', 0, 1, 23, '2021:8|2022:8', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('OPUKO', 'THADDEUS', 'JUDE', '0', '2010-02-02', 'Male', '8', '24', '2021-10-05', 'JULIUS MUGANDA', '0704512451', 'Father', 'julius@gmail.com', '', '', '', '', 'KIAMBU', '0', '8798798B', 'BREAD,Golf bat', 'none', 'enroll', 'NO', 'No', 'none', 0, 1, 24, '2021:8|2022:8', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('TSINALE', 'HARRIET', 'OUKO', '0', '2014-01-28', 'Female', '8', '25', '2021-10-05', 'JAMAL KALIWA', '0704124514', 'Father', 'khalid@gmail.com', '', NULL, NULL, '', 'KITUI', '98798798', 'KLJLKJ909', 'BREAD,Golf bat', 'none', 'enroll', 'NO', 'No', 'none', 0, 1, 25, '2021:7|2022:8', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('AMANDA', 'JULIUS', 'KHALWALE', '0', '2009-02-10', 'Male', '-1', '26', '2021-10-05', 'KEVIN MASILWA', '0714215411', 'Father', 'jiji@gmail.com', '', '', '', '', 'JEVANJEE GARDENS', '89798NKN', '8787687KJ', 'BREAD,Golf bat', 'none', 'enroll', 'NO', 'No', 'none', 0, 1, 26, '2021:7|2022:-1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('MARLEY', 'DAMIAN', 'KIPCHUMBA', '0', '2006-03-01', 'Male', '-1', '27', '2021-10-05', 'JULIUS YEGO', '0714541454', 'Father', 'peter@gmail.com', '', '', '', '', '', '', '8798IOUKJ8', 'BREAD,Golf bat', 'none', 'enroll', 'NO', 'No', 'none', 0, 1, 27, '2021:7|2022:-1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BALE', 'GARETH', 'HUMAN', '0', '2006-01-31', 'Female', '-1', '29', '2021-10-05', 'JUMANJI', '0714541514', 'Father', '', '', '', '', '', '', '', '', NULL, 'none', 'enroll', 'NO', 'No', NULL, 0, 1, 29, '2021:7|2022:-1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('OMONDI', 'LEONARD', 'MASILWA', '0', '2000-06-06', 'Female', '-1', '30', '2021-10-05', 'JULIS DEZMORE', '0741214144', 'Father', '', '', '', '', '', '', '', '', 'BREAD,Golf bat', 'none', 'enroll', 'NO', 'No', 'none', 0, 1, 30, '2021:7|2022:-1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('HANNAH', 'PAULINE', 'PETERSON', '0', '2010-02-02', 'Female', '-1', '31', '2021-10-05', 'PETER KARANJA', '0714124544', 'Father', '', '', '', '', '', '', '', '', 'BREAD,Golf bat', 'none', '', 'NO', 'No', 'none', 0, 1, 31, '2021:7|2022:-1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('AKOTH', 'TRACY', 'MANDIWA', '0', '2001-02-06', 'Female', '8', '32', '2021-10-05', 'HILLARY NGIGE', '0715414147', 'Father', '', '', '', '', '', '', '', '', 'BREAD,Golf bat', 'none', 'enroll', 'NO', 'No', 'none', 0, 1, 32, '2021:7|2022:8', NULL, NULL, NULL, NULL, NULL, NULL, 'images/students_profiles/testimonytbl1/32/20220923_134750.jpg'),
('Opuko', 'Thaddeus', 'Jude', '0', '2008-10-01', 'Female', '7', '34', '2021-10-09', 'Hillary Ngige', '0743551250', 'Father', 'hilaryme45@gmail.com', '', '', '', '', 'Kiambu', 'GGFJGJDJH', '57857886', NULL, 'none', 'none', 'NO', 'No', '', 0, 1, 34, '2021:6|2022:7', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('PANDE', 'TRACY ', 'OKOTH', '0', '2019-11-05', 'Male', '7', '36', '2021-11-23', 'LEONARD DAVINCI', '0745457475', 'Mother', 'hilaryme45@gmail.com', '', '', '', '', '', '0', '', 'BREAD,Golf bat', 'none', 'none', 'NO', 'No', '', 0, 1, 36, '2021:7|2022:7', '', '', '', '', NULL, '', NULL),
('MAKINI', 'JUMA', 'JUX', '0', '2020-02-15', 'Male', '8', '41', '2022-02-15', 'SIMON MUGO', '0704241905', 'Mother', 'hilaryme45@gmail.com', 'none', 'none', 'none', 'none', '', '0', '', 'BREAD,Golf bat', '1', 'enrolled', 'NO', 'No', 'none', 0, 1, 40, ':7|2022:8', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('JAMES', 'MUGOH', 'KELLY', '0', '2002-01-29', 'Male', '8', '40', '2022-03-24', 'PETER THAIRU', '0741512115', 'Father', 'hilaryme45@gmail.com', 'HILLARY NGIGE', '0716151413', 'Mother', 'james@gmail.com', '', '0', '', 'BREAD,Golf bat', 'none', '', 'NO', 'No', 'none', 0, 1, 41, '2022:8', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('NEW', 'STUDENT', 'TEST', '0', '2016-02-02', 'Male', '8', '37', '2022-04-08', 'OWEN MALINGU', '0743551250', 'Mother', 'owen@gmail.com', 'MARIA WAKIO', '0713620727', 'Mother', 'hilaryme45@gmail.com', '', '0', '', '', 'none', 'enroll', 'NO', 'No', 'none', 0, 1, 42, ':8|2022:8', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('PATRICK', 'QUAKO', 'KIKA', '0', '2020-04-11', 'Male', '7', 'HJHGJHG', '2022-04-11', 'JAMES MUGOH', '0743551250', 'Father', 'hilaryme45@gmail.com', 'none', 'none', 'none', 'none', '', '0', '', NULL, 'none', 'none', 'NO', 'No', '', 0, 1, 43, '', '', '', '', '', NULL, '', NULL),
('TESTING', 'TEST', 'TEST', '0', '2020-04-11', 'Male', '8', 'HGJHGJH', '2022-04-11', 'KEVI JUNA', '0741512415', 'Father', '', 'none', 'none', 'none', 'none', '', '0', '', '', 'none', 'none', 'NO', 'No', 'none', 0, 1, 44, '', '', '', '', '', NULL, '', NULL),
('ABIGAIL', 'AMBANI', 'ABIGAIL', '0', '2018-02-06', 'Female', '8', 'KJHKJHJ', '2022-04-11', 'JAMES MUGOH', '0743551250', 'Father', '', 'none', 'none', 'none', 'none', '', '', '', '', '2', 'enrolled', 'NO', 'Yes', 'DISLOCATED LEG', 0, 1, 45, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'images/students_profiles/testimonytbl1/KJHKJHJ/20220923_134750.jpg'),
('KAMAU', 'MUGIH', 'OUJJ', '0', '2020-04-11', 'Female', '8', 'SDSF', '2022-04-11', 'LKJLKJ', '0714151617', 'Guardian', 'keik@gmail.com', 'none', 'none', 'none', 'none', '', '0', '', '', 'none', 'none', 'NO', 'No', 'none', 0, 1, 46, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BWIRE', 'ESMOND', 'ADALA', '0', '2016-02-09', 'Male', '8', 'LBSMIS12', '2022-05-12', 'KILIMANJARO', '0713620727', 'Mother', 'kilimajaro2gmail.com', 'none', 'none', 'none', 'none', '', '0', '', '', 'none', 'enroll', 'NO', 'No', 'none', 0, 1, 47, '', '', '', '', '', NULL, '2', NULL),
('NEW', 'STUDENT', 'NAH', '0', '2011-02-01', 'Male', '8', 'FGF11', '2022-05-26', 'JAMES MY', '0743551250', 'Father', 'james@gmail.com', 'none', 'none', 'none', 'none', '', '', 'FDSFD', 'BREAD,Golf bat', 'none', 'enroll', 'NO', 'No', 'none', 0, 1, 48, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('ADALA', 'HILLARY', 'NGIGE', '0', '2017-01-31', 'Male', '7', 'LBS102', '2022-07-23', 'Johann Rebman', '0713620727', 'Father', 'hilaryme45@gmail.com', 'Kijana wamalwa', '0743551250', 'Mother', 'hilaryme45@gmail.com', '', '0', '', NULL, 'none', 'none', 'NO', 'No', '', 0, 1, 49, '', 'Doctor', 'Doctor', '', '', NULL, '', NULL),
('Mandaiwe', 'Khamala', 'Boohle', '0', '2009-02-03', 'Female', '8', 'LBD343', '2022-07-23', 'MAMA OWEN', '0713620727', 'Mother', 'hilaryme45@gmail.com', 'Maria Wakio', '07435512150', 'Father', 'hilaryme45@gmail.com', 'Kijabe Ke', 'JHGJHG', 'JHGJH77', NULL, 'none', 'none', 'NO', 'No', '', 0, 1, 50, '', 'Kiganjo', 'Kiganjo', '', '', NULL, '', NULL),
('James', 'Mayweather', 'kiki', '0', '2020-07-23', 'Male', '8', '35', '2022-10-04', 'JUJA', '0713620727', 'Father', 'hilaryme45@gmail.com', 'Peter Cleavas', '0704241905', 'Guardian', 'james@gmail.com', 'KJH', 'KUHJ8', 'HGHG7', NULL, 'none', 'none', 'NO', 'No', '', 0, 1, 51, '', 'Nurse', 'Nurse', '', '', NULL, '', NULL),
('Karimi', 'Juma', 'Kimani', '0', '2020-07-31', 'Male', '8', '38', '2022-07-23', 'Maxine', '0713620727', 'Father', 'james@gmail.com', 'James Mugoh', '0743551250', 'Father', 'hilaryme45@gmail.com', 'Kitisuru Estate', 'dsdfd', '2252', '', 'none', 'none', 'NO', 'No', 'none', 0, 1, 52, '', 'Accountant At CBK', 'Engineer', '[{\"school_name\":\"Masjid Academy\",\"date_left\":\"2022-08-25\",\"marks_scored\":\"450 \",\"leaving_cert\":\"true\",\"reason_for_leaving\":\"My mum hated the headteacher\"}]', 'We have medical attendance', 'Sponsorship', '3', NULL),
('NGUGI', 'ROBERT', 'WAGITHOMO', '35601110101', '2007-01-30', 'Male', '7', 'MGM101', '2022-07-24', 'PETER THAIRU', '0713620727', 'Mother', 'hilaryme45@gmail.com', 'James May', '0704241905', 'Father', 'jamesmay@gmail.com', 'KIJABE', 'LKIJLKKJ', 'LKJLK', 'BREAD,Golf bat', 'none', 'none', 'NO', 'No', 'none', 0, 1, 53, '', 'Doctor', 'Doctor', '[{\"school_name\":\"St Juja Peters\",\"date_left\":\"2022-02-01\",\"marks_scored\":\"350 E (Plain)\",\"leaving_cert\":\"true\",\"reason_for_leaving\":\"Inadequate fees\"}]', 'I have no medical issues', 'Sponsorship', '', NULL),
('DIOR', 'CHRISTIAN', 'DIOR', '0', '2020-07-31', 'Female', '7', 'MGM102', '2022-07-27', 'OWEN MALINGU', '0713620727', 'Father', 'hilaryme45@gmail.com', 'SAMSON KIPCHUMBA', '0743551250', 'Guardian', 'N/A', 'Kijabe', 'GFDHHG54', 'KJHKJ99', 'BREAD,Golf bat', 'none', 'none', 'NO', 'Yes', 'Had a leg vrocken', 0, 1, 54, '', 'N/A', 'N/A', '', 'N/A', 'Reimbursment', '1', NULL),
('SAKAJA', 'JOHNSON', 'POAN', '0', '2020-07-16', 'Male', '7', 'MGM103', '2022-07-27', 'GEORGE OLAYO', '0713620727', 'Father', 'hilaryme45@gmail.com', 'Kevin Juma', '0713620727', 'Guardian', 'hilaryme45@gmail.com', 'KJHKJ', 'KJHKJ', 'KJHKJH', '', 'none', 'none', 'NO', 'No', 'none', 0, 1, 55, '', 'Staff', 'Staff', '', 'Its a done deal', 'Reimbursment', '4', NULL),
('JAMAL', 'DESMOND', 'BWIRE', '0', '2020-08-07', 'Female', '8', 'MGM104', '2022-08-04', 'Kimani wa Matangi', '0713620727', 'Father', 'kimani@gmail.com', 'Tsuma Cedrique', '0743551250', 'Father', 'none', '', '', '', 'BREAD,Golf bat', 'none', 'none', 'NO', 'No', 'none', 0, 1, 56, NULL, 'Doctor', 'Doctor', '[{\"school_name\":\"St caribean\",\"date_left\":\"2022-08-19\",\"marks_scored\":\"Pass\",\"leaving_cert\":\"true\",\"reason_for_leaving\":\"Invalid Reason\"},{\"school_name\":\"Kimani School\",\"date_left\":\"2022-02-01\",\"marks_scored\":\"400 A plain\",\"leaving_cert\":\"true\",\"reason_for_leaving\":\"Masculine Tuesday\"}]', 'He has no medical history', 'Self', '3', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `table_subject`
--

CREATE TABLE `table_subject` (
  `subject_id` int(10) NOT NULL,
  `subject_name` varchar(100) DEFAULT NULL,
  `timetable_id` varchar(10) DEFAULT NULL,
  `max_marks` int(4) DEFAULT NULL,
  `classes_taught` varchar(100) DEFAULT NULL,
  `teachers_id` varchar(100) DEFAULT NULL,
  `sub_activated` int(1) DEFAULT NULL,
  `grading` mediumtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `table_subject`
--

INSERT INTO `table_subject` (`subject_id`, `subject_name`, `timetable_id`, `max_marks`, `classes_taught`, `teachers_id`, `sub_activated`, `grading`) VALUES
(1, 'ENGLISH', 'ENG', 50, '1,2,3,4,5,6,7,8', '(2:6)|(3:7)|(2:8)|(3:5)|(11:1)', 1, '[{\"grade_name\": \"A\",\"max\":\"50\",\"min\":\"40\",\"grade_id\":\"1\"},{\"grade_name\": \"B\",\"max\":\"39\",\"min\":\"30\",\"grade_id\":\"1\"},{\"grade_name\": \"C\",\"max\":\"29\",\"min\":\"22\",\"grade_id\":\"1\"},{\"grade_name\": \"D\",\"max\":\"21\",\"min\":\"15\",\"grade_id\":\"1\"},{\"grade_name\": \"E\",\"max\":\"14\",\"min\":\"0\",\"grade_id\":\"1\"}]'),
(2, 'MATHEMATICS', 'MAT', 100, '1,2,3,4,5,6,7,8', '(1:6)|(1:8)|(29:1)|(29:2)|(29:3)|(29:4)|(5:5)|(5:7)', 1, '[{\"grade_name\": \"A\",\"max\":\"100\",\"min\":\"85\",\"grade_id\":\"1\"},{\"grade_name\": \"B\",\"max\":\"84\",\"min\":\"65\",\"grade_id\":\"1\"},{\"grade_name\": \"C\",\"max\":\"64\",\"min\":\"45\",\"grade_id\":\"1\"},{\"grade_name\": \"D\",\"max\":\"44\",\"min\":\"25\",\"grade_id\":\"1\"},{\"grade_name\": \"E\",\"max\":\"24\",\"min\":\"0\",\"grade_id\":\"1\"}]'),
(3, 'KISWAHILI', 'KIS', 100, '1,2,3,4,5,6,7,8', '(8:6)|(8:8)|(7:5)|(7:7)', 1, NULL),
(4, 'SCIENCE', 'SCI', 100, '5,6,7,8', '(1:5)|(1:7)|(5:6)|(5:8)', 1, '[{\"grade_name\": \"A\",\"max\":\"100\",\"min\":\"80\",\"grade_id\":\"1\"},{\"grade_name\": \"B\",\"max\":\"79\",\"min\":\"65\",\"grade_id\":\"1\"},{\"grade_name\": \"C\",\"max\":\"64\",\"min\":\"45\",\"grade_id\":\"1\"},{\"grade_name\": \"D\",\"max\":\"44\",\"min\":\"25\",\"grade_id\":\"1\"},{\"grade_name\": \"E\",\"max\":\"24\",\"min\":\"0\",\"grade_id\":\"1\"}]'),
(5, 'CRE', 'CRE', 30, '5,6,7,8', '(1:5)|(8:7)|(7:6)|(7:8)', 1, NULL),
(6, 'SOCIAL STUDIES', 'SST', 70, '5,6,7,8', '(2:5)|(3:6)|(2:7)|(3:8)', 1, NULL),
(11, 'Juma Jux', 'JMG', 70, '1,2,3,4,5,6,7,8', NULL, 1, '[{\"grade_name\": \"A\",\"max\":\"70\",\"min\":\"62\",\"grade_id\":\"1\"},{\"grade_name\": \"B\",\"max\":\"61\",\"min\":\"48\",\"grade_id\":\"1\"},{\"grade_name\": \"C\",\"max\":\"47\",\"min\":\"38\",\"grade_id\":\"1\"},{\"grade_name\": \"D\",\"max\":\"37\",\"min\":\"25\",\"grade_id\":\"1\"},{\"grade_name\": \"E\",\"max\":\"24\",\"min\":\"0\",\"grade_id\":\"1\"}]'),
(12, 'Kiswahili Lugha', 'KSL', 90, '1,2,3,4,5,6,7,8', NULL, 1, '[{\"grade_name\": \"A\",\"max\":\"90\",\"min\":\"75\",\"grade_id\":\"1\"},{\"grade_name\": \"B\",\"max\":\"74\",\"min\":\"65\",\"grade_id\":\"1\"},{\"grade_name\": \"C\",\"max\":\"64\",\"min\":\"45\",\"grade_id\":\"1\"},{\"grade_name\": \"D\",\"max\":\"44\",\"min\":\"25\",\"grade_id\":\"1\"},{\"grade_name\": \"E\",\"max\":\"24\",\"min\":\"0\",\"grade_id\":\"1\"}]'),
(13, 'FRENCH', 'FRE', 100, '6,7,8', NULL, 1, '[{\"grade_name\": \"A\",\"max\":\"100\",\"min\":\"75\",\"grade_id\":\"1\"},{\"grade_name\": \"B\",\"max\":\"74\",\"min\":\"60\",\"grade_id\":\"1\"},{\"grade_name\": \"C\",\"max\":\"59\",\"min\":\"40\",\"grade_id\":\"1\"},{\"grade_name\": \"D\",\"max\":\"39\",\"min\":\"20\",\"grade_id\":\"1\"},{\"grade_name\": \"E\",\"max\":\"19\",\"min\":\"0\",\"grade_id\":\"1\"}]');

-- --------------------------------------------------------

--
-- Table structure for table `tblnotification`
--

CREATE TABLE `tblnotification` (
  `notification_id` int(30) NOT NULL,
  `notification_name` varchar(1000) DEFAULT NULL,
  `Notification_content` mediumtext DEFAULT NULL,
  `sender_id` varchar(30) DEFAULT NULL,
  `notification_status` varchar(2) DEFAULT NULL,
  `notification_reciever_id` varchar(25) DEFAULT NULL,
  `notification_reciever_auth` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tblnotification`
--

INSERT INTO `tblnotification` (`notification_id`, `notification_name`, `Notification_content`, `sender_id`, `notification_status`, `notification_reciever_id`, `notification_reciever_auth`) VALUES
(1, 'Admission of <b>THOMAS PATRICK</b> in your class was successfull', '<b>THOMAS PATRICK</b> has been successfully admitted to class: <b>8</b>', 'Administration System', '1', '11', '5'),
(2, 'Admission of <b>ELIZABETH ATIENO</b> in your class was successfull', '<b>ELIZABETH ATIENO</b> has been successfully admitted to class: <b>7</b>', 'Administration System', '0', '10', '5'),
(3, 'Thanks for the feedback!', 'We really value your feedback, we`ll review it and use it to make your experience better as we go.<br><b>Thank you!</b>', 'Ladybird SMIS', '1', '1', '1'),
(6, 'New Message', 'Thayo man', '1', '1', '9', 'all'),
(7, 'Admission of <b>CHARLES KHAMALA</b> in your class was successfull', '<b>CHARLES KHAMALA</b> has been successfully admitted to class: <b>7</b>', 'Administration System', '0', '10', '5'),
(8, 'Admission of <b>SHARON MANDAU</b> in your class was successfull', '<b>SHARON MANDAU</b> has been successfully admitted to class: <b>8</b>', 'Administration System', '1', '11', '5'),
(9, 'Thanks for the feedback!', 'We really value your feedback, we`ll review it and use it to make your experience better as we go.<br><b>Thank you!</b>', 'Ladybird SMIS', '1', '18', '2'),
(10, 'Hello <b>David bremy</b>. Welcome!', 'Hello <b>David bremy</b>, Welcome to <b>             TESTIMONY GRAMMAR SCHOOL SMIS</b>. <br>You are assigned <b>Class teacher</b> by your administrator.<br>Use the menu on your left to navigate the system and the home button on the top to view your dashboard.', 'Administration system', '1', '29', '5'),
(11, 'Thanks for the feedback!', 'We really value your feedback, we`ll review it and use it to make your experience better as we go.<br><b>Thank you!</b>', 'Ladybird SMIS', '1', '17', '5'),
(12, 'Admission of <b>LAURENCE OTIENO</b> in your class was successfull', '<b>LAURENCE OTIENO</b> has been successfully admitted to class: <b>8</b>', 'Administration System', '1', '12', '5'),
(13, 'Admission of <b>TIMOTHY KAMAU</b> in your class was successfull', '<b>TIMOTHY KAMAU</b> has been successfully admitted to class: <b>5</b>', 'Administration System', '0', '6', '5'),
(14, 'Admission of <b>KILIAN CHRISTIAN</b> in your class was successfull', '<b>KILIAN CHRISTIAN</b> has been successfully admitted to class: <b>5</b>', 'Administration System', '0', '6', '5'),
(15, 'Admission of <b>TRACY OUNDO</b> in your class was successfull', '<b>TRACY OUNDO</b> has been successfully admitted to class: <b>5</b>', 'Administration System', '0', '6', '5'),
(16, 'Admission of <b>SHARON ATIENO</b> in your class was successfull', '<b>SHARON ATIENO</b> has been successfully admitted to class: <b>5</b>', 'Administration System', '0', '6', '5'),
(17, 'Admission of <b>CHRISPINUS ODHIAMBO</b> in your class was successfull', '<b>CHRISPINUS ODHIAMBO</b> has been successfully admitted to class: <b>5</b>', 'Administration System', '0', '6', '5'),
(18, 'Admission of <b>CEDRIQUE MUSUNGU</b> in your class was successfull', '<b>CEDRIQUE MUSUNGU</b> has been successfully admitted to class: <b>5</b>', 'Administration System', '0', '6', '5'),
(19, 'Admission of <b>TIMON KHALWALE</b> in your class was successfull', '<b>TIMON KHALWALE</b> has been successfully admitted to class: <b>5</b>', 'Administration System', '0', '6', '5'),
(20, 'Admission of <b>DENZEL PAULINE</b> in your class was successfull', '<b>DENZEL PAULINE</b> has been successfully admitted to class: <b>5</b>', 'Administration System', '0', '6', '5'),
(21, 'Admission of <b>SHARON OMONDI</b> in your class was successfull', '<b>SHARON OMONDI</b> has been successfully admitted to class: <b>4</b>', 'Administration System', '0', '26', '5'),
(22, 'Admission of <b>SARAH KAPULE</b> in your class was successfull', '<b>SARAH KAPULE</b> has been successfully admitted to class: <b>4</b>', 'Administration System', '0', '26', '5'),
(23, 'Admission of <b>JULIUS MUGANDA</b> in your class was successfull', '<b>JULIUS MUGANDA</b> has been successfully admitted to class: <b>4</b>', 'Administration System', '0', '26', '5'),
(24, 'Thanks for the feedback!', 'We really value your feedback, we`ll review it and use it to make your experience better as we go.<br><b>Thank you!</b>', 'Ladybird SMIS', '1', '1', '1'),
(25, 'Thanks for the feedback!', 'We really value your feedback, we`ll review it and use it to make your experience better as we go.<br><b>Thank you!</b>', 'Ladybird SMIS', '1', '15', '5'),
(26, 'Admission of <b>MARIA NGIGE</b> in your class was successfull', '<b>MARIA NGIGE</b> has been successfully admitted to class: <b>8</b>', 'Administration System', '1', '12', '5'),
(27, 'Admission of <b>THOMAS EGHAN</b> in your class was successfull', '<b>THOMAS EGHAN</b> has been successfully admitted to class: <b>8</b>', 'Administration System', '1', '12', '5'),
(28, 'Admission of <b>DESMOND TUTU</b> in your class was successfull', '<b>DESMOND TUTU</b> has been successfully admitted to class: <b>8</b>', 'Administration System', '1', '12', '5'),
(29, 'Admission of <b>JOEL KAMAU</b> in your class was successfull', '<b>JOEL KAMAU</b> has been successfully admitted to class: <b>8</b>', 'Administration System', '1', '12', '5'),
(30, 'Admission of <b>MADUA KHALE</b> in your class was successfull', '<b>MADUA KHALE</b> has been successfully admitted to class: <b>8</b>', 'Administration System', '1', '12', '5'),
(31, 'Admission of <b>THADDEUS JUDE</b> in your class was successfull', '<b>THADDEUS JUDE</b> has been successfully admitted to class: <b>8</b>', 'Administration System', '0', '12', '5'),
(32, 'Admission of <b>HARRIET OUKO</b> in your class was successfull', '<b>HARRIET OUKO</b> has been successfully admitted to class: <b>7</b>', 'Administration System', '0', '10', '5'),
(33, 'Admission of <b>JULIUS KHALWALE</b> in your class was successfull', '<b>JULIUS KHALWALE</b> has been successfully admitted to class: <b>7</b>', 'Administration System', '0', '10', '5'),
(34, 'Admission of <b>DAMIAN KIPCHUMBA</b> in your class was successfull', '<b>DAMIAN KIPCHUMBA</b> has been successfully admitted to class: <b>7</b>', 'Administration System', '0', '10', '5'),
(35, 'Admission of <b>PETER CLEVAS</b> in your class was successfull', '<b>PETER CLEVAS</b> has been successfully admitted to class: <b>7</b>', 'Administration System', '0', '10', '5'),
(36, 'Admission of <b>GARETH HUMAN</b> in your class was successfull', '<b>GARETH HUMAN</b> has been successfully admitted to class: <b>7</b>', 'Administration System', '0', '10', '5'),
(37, 'Admission of <b>LEONARD MASILWA</b> in your class was successfull', '<b>LEONARD MASILWA</b> has been successfully admitted to class: <b>7</b>', 'Administration System', '0', '10', '5'),
(38, 'Admission of <b>PAULINE PETERSON</b> in your class was successfull', '<b>PAULINE PETERSON</b> has been successfully admitted to class: <b>7</b>', 'Administration System', '0', '10', '5'),
(39, 'Admission of <b>TRACY MANDIWA</b> in your class was successfull', '<b>TRACY MANDIWA</b> has been successfully admitted to class: <b>7</b>', 'Administration System', '0', '10', '5'),
(40, 'Admission of <b>Caren Odhiambo</b> in your class was successfull', '<b>Caren Odhiambo</b> has been successfully admitted to class: <b>5</b>', 'Administration System', '0', '6', '5'),
(41, 'Admission of <b>Thaddeus Jude</b> in your class was successfull', '<b>Thaddeus Jude</b> has been successfully admitted to class: <b>8</b>', 'Administration System', '0', '12', '5'),
(42, 'New Message', 'MBUDAH FORM', '1', '0', '3', 'all'),
(43, 'Admission of <b>JAMES INDIANA</b> in your class was successfull', '<b>JAMES INDIANA</b> has been successfully admitted to class: <b>8</b>', 'Administration System', '0', '12', '5'),
(44, 'Admission of <b>TRACY  OKOTH</b> in your class was successfull', '<b>TRACY  OKOTH</b> has been successfully admitted to class: <b>7</b>', 'Administration System', '0', '10', '5'),
(45, 'Admission of <b>ESMOND BWIRE</b> in your class was successfull', '<b>ESMOND BWIRE</b> has been successfully admitted to class: <b>7</b>', 'Administration System', '0', '10', '5'),
(46, 'Admission of <b>KEVIN ASIPO</b> in your class was successfull', '<b>KEVIN ASIPO</b> has been successfully admitted to class: <b>8</b>', 'Administration System', '0', '12', '5'),
(47, 'Admission of <b>KIMATHI TSUMA</b> in your class was successfull', '<b>KIMATHI TSUMA</b> has been successfully admitted to class: <b>8</b>', 'Administration System', '0', '12', '5'),
(48, 'Admission of <b>JUMA JUX</b> in your class was successfull', '<b>JUMA JUX</b> has been successfully admitted to class: <b>6</b>', 'Administration System', '0', '8', '5'),
(49, 'Hello <b>OWEN MALINGU</b>. Welcome!', 'Hello <b>OWEN MALINGU</b>, Welcome to <b>   TESTIMONY GRAMMAR SCHOOL SMIS</b>. <br>You are assigned <b>N/A</b> by your administrator.<br>Use the menu on your left to navigate the system and the home button on the top to view your dashboard.', 'Administration system', '0', '37', '6'),
(50, 'Admission of <b>MUGOH KELLY</b> in your class was successfull', '<b>MUGOH KELLY</b> has been successfully admitted to class: <b>5</b>', 'Administration System', '0', '6', '5'),
(51, 'Admission of <b>STUDENT TEST</b> in your class was successfull', '<b>STUDENT TEST</b> has been successfully admitted to class: <b>8</b>', 'Administration System', '0', '12', '5'),
(52, 'Admission of <b>QUAKO KIKA</b> in your class was successfull', '<b>QUAKO KIKA</b> has been successfully admitted to class: <b>7</b>', 'Administration System', '0', '10', '5'),
(53, 'Admission of <b>TEST TEST</b> in your class was successfull', '<b>TEST TEST</b> has been successfully admitted to class: <b>8</b>', 'Administration System', '0', '12', '5'),
(54, 'Admission of <b>AMBANI ABIGAIL</b> in your class was successfull', '<b>AMBANI ABIGAIL</b> has been successfully admitted to class: <b>8</b>', 'Administration System', '0', '12', '5'),
(55, 'Admission of <b>MUGIH OUJJ</b> in your class was successfull', '<b>MUGIH OUJJ</b> has been successfully admitted to class: <b>7</b>', 'Administration System', '0', '10', '5'),
(56, 'Admission of <b>ESMOND ADALA</b> in your class was successfull', '<b>ESMOND ADALA</b> has been successfully admitted to class: <b>8</b>', 'Administration System', '0', '12', '5'),
(57, 'Admission of <b>STUDENT NAH</b> in your class was successfull', '<b>STUDENT NAH</b> has been successfully admitted to class: <b>8</b>', 'Administration System', '0', '12', '5'),
(58, 'Notice', 'Notice to delete user', 'My system', '1', '1', '1'),
(59, 'Confirmed payment for HILLARY NGIGE', 'Confirmed Ksh 100 has been recieved from HILLARY NGIGE Adm No: 1 for <b>BOARDERS</b>, on Jun-10-2022 at 21:24:33 hrs.<br>The payment mode used was <b>cash</b>', 'Payment system', '1', 'all', '1'),
(60, 'Reversal of payment for PETER EGHAN', 'Reversal of Ksh 10,000 for PETER EGHAN Adm No: 20 has been done successfully on Jun-10-2022 at 18:39:24 hrs', 'Payment system', '1', 'all', '1'),
(61, 'Reversal of payment for PETER EGHAN', 'Reversal of Ksh 400 for PETER EGHAN Adm No: 20 has been done successfully on Jun-10-2022 at 18:39:57 hrs', 'Payment system', '1', 'all', '1'),
(62, 'Registration of <b>LEVIS KIMANI</b> as a new staff was successfull', 'Registration of LEVIS KIMANI as <b>N/A</b> has been done successfully<br>The user is to use their username and password you assigned them to login', 'Administration system', '1', 'all', '1'),
(63, 'Hello <b>LEVIS KIMANI</b>. Welcome!', 'Hello <b>LEVIS KIMANI</b>, Welcome to <b>    TESTIMONY GRAMMAR SCHOOL SMIS</b>. <br>You are assigned <b>N/A</b> by your administrator.<br>Use the menu on your left to navigate the system and the home button on the top to view your dashboard.', 'Administration system', '1', '39', 'HILLARY TESTER'),
(64, 'Staff has been deleted', 'James Kamau has been deleted on 10Fri Jun 2022.', 'Administration system', '1', 'all', '1'),
(65, 'Staff has been deleted', 'Owen Malingus has been deleted on 10th Jun 2022.', 'Administration system', '1', 'all', '1'),
(66, 'Staff has been deleted', 'Thomas Tomas has been deleted on 10th Jun 2022 by HILARY NGIGE ADALA.', 'Administration system', '1', 'all', '1'),
(67, 'Staff has been deleted', 'Kevin Jum has been deleted on 10th Jun 2022 by HILARY NGIGE ADALA.', 'Administration system', '1', 'all', '1'),
(68, 'Staff has been deleted', 'Kimani Wamatangi has been deleted on 10th Jun 2022 by HILARY NGIGE ADALA.', 'Administration system', '1', 'all', '1'),
(69, 'Staff has been deleted', 'Joseph Gathure has been deleted on 10th Jun 2022 by HILARY NGIGE ADALA.', 'Administration system', '1', 'all', '1'),
(70, 'Staff has been deleted', 'David Bremmy has been deleted on 10th Jun 2022 by HILARY NGIGE ADALA.', 'Administration system', '1', 'all', '1'),
(71, 'Staff has been deleted', 'Kamau Wangendo  has been deleted on 10th Jun 2022 by HILARY NGIGE ADALA.', 'Administration system', '1', 'all', '1'),
(72, 'Confirmed payment for HILLARY NGIGE', 'Confirmed Ksh 100 has been recieved from HILLARY NGIGE Adm No: 1 for <b>BOARDERS</b>, on Jun-11-2022 at 02:19:09 hrs.<br>The payment mode used was <b>cash</b>', 'Payment system', '1', 'all', '1'),
(73, 'Confirmed payment for CHARLES KHAMALA', 'Confirmed Ksh 1,000 has been recieved from CHARLES KHAMALA Adm No: 4 for <b>balance</b>, on Jun-13-2022 at 11:07:59 hrs.<br>The payment mode used was <b>cash</b>', 'Payment system', '1', 'all', '1'),
(74, 'Confirmed payment for CHRISPINUS ODHIAMBO', 'Confirmed Ksh 1,000 has been recieved from CHRISPINUS ODHIAMBO Adm No: 12 for <b>BOARDERS</b>, on Jun-13-2022 at 14:21:23 hrs.<br>The payment mode used was <b>bank</b>', 'Payment system', '1', 'all', '1'),
(75, 'Confirmed payment for SHARON ATIENO', 'Confirmed Ksh 1,000 has been recieved from SHARON ATIENO Adm No: 11 for <b>BOARDERS</b>, on Jun-13-2022 at 14:23:03 hrs.<br>The payment mode used was <b>mpesa</b>', 'Payment system', '1', 'all', '1'),
(76, 'Confirmed payment for CEDRIQUE MUSUNGU', 'Confirmed Ksh 1,000 has been recieved from CEDRIQUE MUSUNGU Adm No: 13 for <b>BOARDERS</b>, on Jun-13-2022 at 14:23:28 hrs.<br>The payment mode used was <b>bank</b>', 'Payment system', '1', 'all', '1'),
(77, 'Thanks for the feedback!', 'We really value your feedback, we`ll review it and use it to make your experience better as we go.<br><b>Thank you!</b>', 'Ladybird SMIS', '1', '1', '1'),
(78, 'Confirmed payment for TRACY OUNDO', 'Confirmed Ksh 1,000 has been recieved from TRACY OUNDO Adm No: 10 for <b>BOARDERS</b>, on Jun-13-2022 at 14:29:56 hrs.<br>The payment mode used was <b>cash</b>', 'Payment system', '1', 'all', '1'),
(79, 'Confirmed payment for TRACY OUNDO', 'Confirmed Ksh 40,000 has been recieved from TRACY OUNDO Adm No: 10 for <b>BOARDERS</b>, on Jun-28-2022 at 21:46:02 hrs.<br>The payment mode used was <b>cash</b>', 'Payment system', '1', 'all', '1'),
(80, 'Confirmed payment for SHARON ATIENO', 'Confirmed Ksh 20,000 has been recieved from SHARON ATIENO Adm No: 11 for <b>BOARDERS</b>, on Jun-29-2022 at 02:51:55 hrs.<br>The payment mode used was <b>cash</b>', 'Payment system', '1', 'all', '1'),
(81, 'Thanks for the feedback!', 'We really value your feedback, we`ll review it and use it to make your experience better as we go.<br><b>Thank you!</b>', 'Ladybird SMIS', '1', '1', '1'),
(82, 'Confirmed payment for HILLARY NGIGE', 'Confirmed Ksh 1,000 has been recieved from HILLARY NGIGE Adm No: 1 for <b>KISWAHILI</b>, on Jul-18-2022 at 13:14:54 hrs.<br>The payment mode used was <b>cash</b>', 'Payment system', '1', 'all', '1'),
(83, 'Confirmed payment for CHRISPINUS ODHIAMBO', 'Confirmed Ksh 1,000 has been recieved from CHRISPINUS ODHIAMBO Adm No: 12 for <b>KISWAHILI</b>, on Jul-18-2022 at 13:33:31 hrs.<br>The payment mode used was <b>cash</b>', 'Payment system', '1', 'all', '1'),
(84, 'Confirmed payment for CEDRIQUE MUSUNGU', 'Confirmed Ksh 2,000 has been recieved from CEDRIQUE MUSUNGU Adm No: 13 for <b>borders</b>, on Jul-18-2022 at 13:41:19 hrs.<br>The payment mode used was <b>cash</b>', 'Payment system', '1', 'all', '1'),
(85, 'Confirmed payment for TIMON KHALWALE', 'Confirmed Ksh 2,500 has been recieved from TIMON KHALWALE Adm No: 14 for <b>borders</b>, on Jul-18-2022 at 13:55:40 hrs.<br>The payment mode used was <b>cash</b>', 'Payment system', '1', 'all', '1'),
(86, 'Confirmed payment for CHRISPINUS ODHIAMBO', 'Confirmed Ksh 1,000 has been recieved from CHRISPINUS ODHIAMBO Adm No: 12 for <b>borders</b>, on Jul-18-2022 at 14:03:42 hrs.<br>The payment mode used was <b>cash</b>', 'Payment system', '1', 'all', '1'),
(87, 'Confirmed payment for SHARON OMONDI', 'Confirmed Ksh 3,000 has been recieved from SHARON OMONDI Adm No: 16 for <b>BOARDERS</b>, on Jul-18-2022 at 14:39:53 hrs.<br>The payment mode used was <b>cash</b>', 'Payment system', '1', 'all', '1'),
(88, 'Confirmed payment for TRACY OUNDO', 'Confirmed Ksh 2,000 has been recieved from TRACY OUNDO Adm No: 10 for <b>Kitale 1</b>, on Jul-18-2022 at 16:44:06 hrs.<br>The payment mode used was <b>mpesa</b>', 'Payment system', '1', 'all', '1'),
(89, 'Confirmed payment for PETER EGHAN', 'Confirmed Ksh 23,000 has been recieved from PETER EGHAN Adm No: 20 for <b>KISWAHILI</b>, on Jul-18-2022 at 20:54:42 hrs.<br>The payment mode used was <b>bank</b>', 'Payment system', '1', 'all', '1'),
(90, 'Admission of <b>HILLARY NGIGE</b> in class: <b>7</b> was successfull', '<b>HILLARY NGIGE</b> has been successfully admitted to class: 7', 'Administration System', '1', 'all', '1'),
(91, 'Admission of <b>HILLARY NGIGE</b> in your class was successfull', '<b>HILLARY NGIGE</b> has been successfully admitted to class: <b>7</b>', 'Administration System', '0', '10', '5'),
(92, 'Admission of <b>Khamala Boohle</b> in class: <b>5</b> was successfull', '<b>Khamala Boohle</b> has been successfully admitted to class: 5', 'Administration System', '1', 'all', '1'),
(93, 'Admission of <b>Mayweather kiki</b> in class: <b>8</b> was successfull', '<b>Mayweather kiki</b> has been successfully admitted to class: 8', 'Administration System', '1', 'all', '1'),
(94, 'Admission of <b>Mayweather kiki</b> in your class was successfull', '<b>Mayweather kiki</b> has been successfully admitted to class: <b>8</b>', 'Administration System', '0', '12', '5'),
(95, 'Admission of <b>Juma Kimani</b> in class: <b>8</b> was successfull', '<b>Juma Kimani</b> has been successfully admitted to class: 8', 'Administration System', '1', 'all', '1'),
(96, 'Admission of <b>Juma Kimani</b> in your class was successfull', '<b>Juma Kimani</b> has been successfully admitted to class: <b>8</b>', 'Administration System', '0', '12', '5'),
(97, 'Admission of <b>ROBERT WAGITHOMO</b> in class: <b>7</b> was successfull', '<b>ROBERT WAGITHOMO</b> has been successfully admitted to class: 7', 'Administration System', '1', 'all', '1'),
(98, 'Admission of <b>ROBERT WAGITHOMO</b> in your class was successfull', '<b>ROBERT WAGITHOMO</b> has been successfully admitted to class: <b>7</b>', 'Administration System', '0', '10', '5'),
(99, 'Admission of <b>CHRISTIAN DIOR</b> in class: <b>7</b> was successfull', '<b>CHRISTIAN DIOR</b> has been successfully admitted to class: 7', 'Administration System', '1', 'all', '1'),
(100, 'Admission of <b>CHRISTIAN DIOR</b> in your class was successfull', '<b>CHRISTIAN DIOR</b> has been successfully admitted to class: <b>7</b>', 'Administration System', '0', '10', '5'),
(101, 'Admission of <b>JOHNSON POAN</b> in class: <b>7</b> was successfull', '<b>JOHNSON POAN</b> has been successfully admitted to class: 7', 'Administration System', '1', 'all', '1'),
(102, 'Admission of <b>JOHNSON POAN</b> in your class was successfull', '<b>JOHNSON POAN</b> has been successfully admitted to class: <b>7</b>', 'Administration System', '0', '10', '5'),
(103, 'Registration of <b>NELSON MANDELA</b> as a new staff was successfull', 'Registration of NELSON MANDELA as <b>HILLARY TESTER</b> has been done successfully<br>The user is to use their username and password you assigned them to login', 'Administration system', '1', 'all', '1'),
(104, 'Hello <b>NELSON MANDELA</b>. Welcome!', 'Hello <b>NELSON MANDELA</b>, Welcome to <b>     TESTIMONY GRAMMAR SCHOOL SMIS</b>. <br>You are assigned <b>HILLARY TESTER</b> by your administrator.<br>Use the menu on your left to navigate the system and the home button on the top to view your dashboard.', 'Administration system', '1', '40', 'HILLARY TESTER'),
(105, 'Admission of <b>DESMOND BWIRE</b> in class: <b>8</b> was successfull', '<b>DESMOND BWIRE</b> has been successfully admitted to class: 8', 'Administration System', '1', 'all', '1'),
(106, 'Registration of <b>JAMES MAY</b> as a new staff was successfull', 'Registration of JAMES MAY as <b>Class teacher</b> has been done successfully<br>The user is to use their username and password you assigned them to login', 'Administration system', '1', 'all', '1'),
(107, 'Hello <b>JAMES MAY</b>. Welcome!', 'Hello <b>JAMES MAY</b>, Welcome to <b> DEMO SCHOOL SMIS</b>. <br>You are assigned <b>Class teacher</b> by your administrator.<br>Use the menu on your left to navigate the system and the home button on the top to view your dashboard.', 'Administration system', '0', '41', '5'),
(108, 'Confirmed payment for JOEL KAMAU', 'Confirmed Ksh 1,000 has been recieved from JOEL KAMAU Adm No: 22 for <b>KISWAHILI</b>, on Sep-25-2022 at 22:06:28 hrs.<br>The payment mode used was <b>mpesa</b>', 'Payment system', '1', 'all', '1'),
(109, 'Confirmed payment for SARAH KAPULE', 'Confirmed Ksh 1,000 has been recieved from SARAH KAPULE Adm No: 17 for <b>TRIPS FEE</b>, on Sep-25-2022 at 22:24:48 hrs.<br>The payment mode used was <b>cash</b>', 'Payment system', '1', 'all', '1'),
(110, 'Confirmed payment for Mayweather kiki', 'Confirmed Ksh 4,000 has been recieved from Mayweather kiki Adm No: 35 for <b>Transport 876</b>, on Sep-26-2022 at 13:00:19 hrs.<br>The payment mode used was <b>mpesa</b>', 'Payment system', '1', 'all', '1'),
(111, 'Registration of <b>HILLARY NGIGE ADALA</b> as a new staff was successfull', 'Registration of HILLARY NGIGE ADALA as <b>HILLARY TESTER</b> has been done successfully<br>The user is to use their username and password you assigned them to login', 'Administration system', '1', 'all', '1'),
(112, 'Hello <b>HILLARY NGIGE ADALA</b>. Welcome!', 'Hello <b>HILLARY NGIGE ADALA</b>, Welcome to <b> DEMO SCHOOL SMIS</b>. <br>You are assigned <b>HILLARY TESTER</b> by your administrator.<br>Use the menu on your left to navigate the system and the home button on the top to view your dashboard.', 'Administration system', '0', '42', 'HILLARY TESTER'),
(113, 'Registration of <b>HILLARY NGIGE</b> as a new staff was successfull', 'Registration of HILLARY NGIGE as <b>Teacher</b> has been done successfully<br>The user is to use their username and password you assigned them to login', 'Administration system', '1', 'all', '1'),
(114, 'Hello <b>HILLARY NGIGE</b>. Welcome!', 'Hello <b>HILLARY NGIGE</b>, Welcome to <b> DEMO SCHOOL SMIS</b>. <br>You are assigned <b>Teacher</b> by your administrator.<br>Use the menu on your left to navigate the system and the home button on the top to view your dashboard.', 'Administration system', '0', '43', '2'),
(115, 'Staff has been deleted', 'Homer Simpson has been deleted on 26th Sep 2022 by HILARY NGIGE ADALA.', 'Administration system', '1', 'all', '1'),
(116, 'Confirmed payment for THADDEUS JUDE', 'Confirmed Ksh 140,000 has been recieved from THADDEUS JUDE Adm No: 24 for <b>Kitale 1</b>, on Oct-31-2022 at 19:23:55 hrs.<br>The payment mode used was <b>cash</b>', 'Payment system', '1', 'all', '1'),
(117, 'Confirmed payment for KILIAN CHRISTIAN', 'Confirmed Ksh 25,000 has been recieved from KILIAN CHRISTIAN Adm No: 9 for <b>BOARDERS</b>, on Nov-03-2022 at 12:31:47 hrs.<br>The payment mode used was <b>cash</b>', 'Payment system', '1', 'all', '1'),
(118, 'Confirmed payment for KILIAN CHRISTIAN', 'Confirmed Ksh 60,000 has been recieved from KILIAN CHRISTIAN Adm No: 9 for <b>BOARDERS</b>, on Nov-03-2022 at 13:05:55 hrs.<br>The payment mode used was <b>cash</b>', 'Payment system', '1', 'all', '1'),
(119, 'Confirmed payment for LAURENCE OPIYO', 'Confirmed Ksh 0 has been recieved from LAURENCE OPIYO Adm No: 6 for <b>BOARDERS</b>, on Nov-03-2022 at 13:12:15 hrs.<br>The payment mode used was <b>cash</b>', 'Payment system', '1', 'all', '1'),
(120, 'Confirmed payment for HILLARY NGIGE', 'Confirmed Ksh 50,000 has been recieved from HILLARY NGIGE Adm No: 1 for <b>Kitale 1</b>, on Nov-03-2022 at 15:36:28 hrs.<br>The payment mode used was <b>cash</b>', 'Payment system', '1', 'all', '1'),
(121, 'Confirmed payment for MARIA NGIGE', 'Confirmed Ksh 137,500 has been recieved from MARIA NGIGE Adm No: 19 for <b>borders</b>, on Nov-03-2022 at 16:03:07 hrs.<br>The payment mode used was <b>cash</b>', 'Payment system', '1', 'all', '1'),
(122, 'Confirmed payment for TRACY OUNDO', 'Confirmed Ksh 20,000 has been recieved from TRACY OUNDO Adm No: 10 for <b>Tution 3</b>, on Nov-16-2022 at 16:09:07 hrs.<br>The payment mode used was <b>cash</b>', 'Payment system', '1', 'all', '1'),
(123, 'Confirmed payment for TRACY OUNDO', 'Confirmed Ksh 10,000 has been recieved from TRACY OUNDO Adm No: 10 for <b>Kitale 1</b>, on Nov-16-2022 at 16:42:34 hrs.<br>The payment mode used was <b>cash</b>', 'Payment system', '0', 'all', '1'),
(124, 'Confirmed payment for TRACY OUNDO', 'Confirmed Ksh 5,000 has been recieved from TRACY OUNDO Adm No: 10 for <b>Kitale 1</b>, on Nov-17-2022 at 11:54:39 hrs.<br>The payment mode used was <b>cash</b>', 'Payment system', '0', 'all', '1'),
(125, 'Confirmed payment for TRACY OUNDO', 'Confirmed Ksh 5,000 has been recieved from TRACY OUNDO Adm No: 10 for <b>Tution 3</b>, on Nov-17-2022 at 18:17:43 hrs.<br>The payment mode used was <b>cash</b>', 'Payment system', '0', 'all', '1'),
(126, 'Confirmed payment for KILIAN CHRISTIAN', 'Confirmed Ksh 25,000 has been recieved from KILIAN CHRISTIAN Adm No: 9 for <b>Kitale 1</b>, on Nov-17-2022 at 19:20:12 hrs.<br>The payment mode used was <b>cash</b>', 'Payment system', '0', 'all', '1'),
(127, 'Confirmed payment for KILIAN CHRISTIAN', 'Confirmed Ksh 1,000 has been recieved from KILIAN CHRISTIAN Adm No: 9 for <b>Kitale 1</b>, on Nov-21-2022 at 11:10:58 hrs.<br>The payment mode used was <b>cash</b>', 'Payment system', '1', 'all', '1'),
(128, 'Registration of <b>JAMES SITITHWI</b> as a new staff was successfull', 'Registration of JAMES SITITHWI as <b>HILLARY TESTER</b> has been done successfully<br>The user is to use their username and password you assigned them to login', 'Administration system', '1', 'all', '1'),
(129, 'Hello <b>JAMES SITITHWI</b>. Welcome!', 'Hello <b>JAMES SITITHWI</b>, Welcome to <b> DEMO SCHOOL SMIS</b>. <br>You are assigned <b>HILLARY TESTER</b> by your administrator.<br>Use the menu on your left to navigate the system and the home button on the top to view your dashboard.', 'Administration system', '0', '44', 'HILLARY TESTER'),
(135, 'Leave Approved', 'Your Null has been successfully approved!', '1', '0', '2', '5'),
(136, 'Leave Approved', 'Your Annual Leave has been successfully approved!', '1', '1', '42', 'HR'),
(137, 'Leave Approved', 'Your Annual Leave has been successfully approved!', '1', '0', '2', '5'),
(138, 'Leave Approved', 'Your Annual Leave has been successfully approved!', '1', '0', '42', 'HR'),
(139, 'Leave Approved', 'Your Paternal Leave has been successfully approved!', '1', '1', '1', '1'),
(140, 'Leave Declined', 'Your Annual Leave has been declined approved<br>Kindly contact your administrator for more information.!', '1', '1', '1', '1'),
(141, 'Leave Declined', 'Your Annual Leave has been declined approved<br>Kindly contact your administrator for more information.!', '1', '1', '1', '1'),
(142, 'Leave Approved', 'Your Annual Leave has been successfully approved!', '1', '1', '1', '1');

-- --------------------------------------------------------

--
-- Table structure for table `transport_enrolled_students`
--

CREATE TABLE `transport_enrolled_students` (
  `id` int(11) NOT NULL,
  `student_id` varchar(255) DEFAULT NULL,
  `route_id` int(11) DEFAULT NULL,
  `stoppage` varchar(200) DEFAULT NULL,
  `date_of_reg` varchar(200) DEFAULT NULL,
  `status` int(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `transport_enrolled_students`
--

INSERT INTO `transport_enrolled_students` (`id`, `student_id`, `route_id`, `stoppage`, `date_of_reg`, `status`) VALUES
(5, '23', 1, 'Samaki Estate', '2022-03-29', 1),
(7, '40', 1, 'shayi mpempe', '2022-03-29', 1),
(8, '20', 1, 'KENYATTA UNIVERSITY', '2022-03-29', 1),
(9, '25', 1, 'Kasarani kenya', '2022-03-29', 1),
(10, '14', 1, 'Juja Corner Kenya', '2022-03-29', 1),
(11, '6', 1, 'utawala', '2022-03-29', 1),
(13, '12', 1, 'BUSIA KE', '2022-04-02', 1),
(14, '41', 1, 'Juja town', '2022-04-02', 1),
(16, 'LBSMIS12', 1, 'Kilimani', '2022-05-12', 1),
(17, '24', 1, 'KIAMBU', '2022-05-19', 1),
(18, '11', 1, 'NAIROBI, KE', '2022-05-19', 1),
(19, '37', 1, 'kiganjo', '2022-05-23', 1),
(23, '10', 2, 'Busia Ke', '2022-11-17', 1),
(24, '35', 1, 'Student stages', '2022-11-21', 1);

-- --------------------------------------------------------

--
-- Table structure for table `van_routes`
--

CREATE TABLE `van_routes` (
  `route_id` int(11) NOT NULL,
  `route_name` varchar(200) DEFAULT NULL,
  `route_price` int(11) DEFAULT NULL,
  `route_areas` varchar(2000) DEFAULT NULL,
  `route_vans` varchar(500) DEFAULT NULL,
  `route_status` int(1) DEFAULT NULL,
  `route_date_change` varchar(300) DEFAULT NULL,
  `route_prev_price` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `van_routes`
--

INSERT INTO `van_routes` (`route_id`, `route_name`, `route_price`, `route_areas`, `route_vans`, `route_status`, `route_date_change`, `route_prev_price`) VALUES
(1, 'route 3', 2000, 'MAKINDU', NULL, NULL, '2022-05-31', 3000),
(2, 'Zone A one way', 4500, 'Kiganjo, kitisuru, ruiru, busia, pokot', NULL, NULL, NULL, NULL),
(3, 'Zone A two way', 6000, 'Kiganjo, kitisuru, ruiru, busia, pokot', NULL, NULL, NULL, NULL),
(4, 'Route 12', 2500, 'kik', NULL, NULL, NULL, NULL),
(6, 'Router 22', 2000, 'kikik', NULL, NULL, NULL, NULL),
(7, 'Router 21', 2000, 'kikak', NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_calendar`
--
ALTER TABLE `academic_calendar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `advance_pay`
--
ALTER TABLE `advance_pay`
  ADD PRIMARY KEY (`advance_id`);

--
-- Indexes for table `apply_leave`
--
ALTER TABLE `apply_leave`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendancetable`
--
ALTER TABLE `attendancetable`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `boarding_list`
--
ALTER TABLE `boarding_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dorm_list`
--
ALTER TABLE `dorm_list`
  ADD PRIMARY KEY (`dorm_id`);

--
-- Indexes for table `email_address`
--
ALTER TABLE `email_address`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exams_tbl`
--
ALTER TABLE `exams_tbl`
  ADD PRIMARY KEY (`exams_id`);

--
-- Indexes for table `exam_record_tbl`
--
ALTER TABLE `exam_record_tbl`
  ADD PRIMARY KEY (`result_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`expid`);

--
-- Indexes for table `fees_structure`
--
ALTER TABLE `fees_structure`
  ADD PRIMARY KEY (`ids`);

--
-- Indexes for table `finance`
--
ALTER TABLE `finance`
  ADD PRIMARY KEY (`transaction_id`);

--
-- Indexes for table `leave_categories`
--
ALTER TABLE `leave_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mpesa_transactions`
--
ALTER TABLE `mpesa_transactions`
  ADD PRIMARY KEY (`transaction_id`);

--
-- Indexes for table `payroll_information`
--
ALTER TABLE `payroll_information`
  ADD PRIMARY KEY (`payroll_id`);

--
-- Indexes for table `salary_payment`
--
ALTER TABLE `salary_payment`
  ADD PRIMARY KEY (`pay_id`);

--
-- Indexes for table `school_vans`
--
ALTER TABLE `school_vans`
  ADD PRIMARY KEY (`van_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sms_api`
--
ALTER TABLE `sms_api`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `sms_table`
--
ALTER TABLE `sms_table`
  ADD PRIMARY KEY (`send_id`);

--
-- Indexes for table `student_data`
--
ALTER TABLE `student_data`
  ADD PRIMARY KEY (`ids`);

--
-- Indexes for table `table_subject`
--
ALTER TABLE `table_subject`
  ADD PRIMARY KEY (`subject_id`);

--
-- Indexes for table `tblnotification`
--
ALTER TABLE `tblnotification`
  ADD PRIMARY KEY (`notification_id`);

--
-- Indexes for table `transport_enrolled_students`
--
ALTER TABLE `transport_enrolled_students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `van_routes`
--
ALTER TABLE `van_routes`
  ADD PRIMARY KEY (`route_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_calendar`
--
ALTER TABLE `academic_calendar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `advance_pay`
--
ALTER TABLE `advance_pay`
  MODIFY `advance_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `apply_leave`
--
ALTER TABLE `apply_leave`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `attendancetable`
--
ALTER TABLE `attendancetable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=266;

--
-- AUTO_INCREMENT for table `boarding_list`
--
ALTER TABLE `boarding_list`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `dorm_list`
--
ALTER TABLE `dorm_list`
  MODIFY `dorm_id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `email_address`
--
ALTER TABLE `email_address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `exams_tbl`
--
ALTER TABLE `exams_tbl`
  MODIFY `exams_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `exam_record_tbl`
--
ALTER TABLE `exam_record_tbl`
  MODIFY `result_id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=155;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `expid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `fees_structure`
--
ALTER TABLE `fees_structure`
  MODIFY `ids` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `finance`
--
ALTER TABLE `finance`
  MODIFY `transaction_id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=272;

--
-- AUTO_INCREMENT for table `leave_categories`
--
ALTER TABLE `leave_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=286;

--
-- AUTO_INCREMENT for table `mpesa_transactions`
--
ALTER TABLE `mpesa_transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `payroll_information`
--
ALTER TABLE `payroll_information`
  MODIFY `payroll_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `salary_payment`
--
ALTER TABLE `salary_payment`
  MODIFY `pay_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=132;

--
-- AUTO_INCREMENT for table `school_vans`
--
ALTER TABLE `school_vans`
  MODIFY `van_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `sms_api`
--
ALTER TABLE `sms_api`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sms_table`
--
ALTER TABLE `sms_table`
  MODIFY `send_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT for table `student_data`
--
ALTER TABLE `student_data`
  MODIFY `ids` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `table_subject`
--
ALTER TABLE `table_subject`
  MODIFY `subject_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tblnotification`
--
ALTER TABLE `tblnotification`
  MODIFY `notification_id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=143;

--
-- AUTO_INCREMENT for table `transport_enrolled_students`
--
ALTER TABLE `transport_enrolled_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `van_routes`
--
ALTER TABLE `van_routes`
  MODIFY `route_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
