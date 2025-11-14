-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 14, 2025 at 02:54 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `grading_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `student_id`, `section_id`, `subject_id`) VALUES
(201, 51, 6, 0),
(202, 52, 6, 0),
(203, 53, 6, 0),
(204, 54, 6, 0),
(205, 55, 6, 0),
(206, 56, 6, 0),
(207, 57, 6, 0),
(208, 58, 6, 0),
(209, 59, 6, 0),
(210, 60, 6, 0),
(211, 61, 6, 0),
(212, 62, 6, 0),
(213, 63, 6, 0),
(214, 64, 6, 0),
(215, 65, 6, 0),
(216, 66, 6, 0),
(217, 67, 6, 0),
(218, 68, 6, 0),
(219, 69, 6, 0),
(220, 70, 6, 0),
(221, 71, 6, 0),
(222, 72, 6, 0),
(223, 73, 6, 0),
(224, 74, 6, 0),
(225, 75, 6, 0),
(226, 76, 6, 0),
(227, 77, 6, 0),
(228, 78, 6, 0),
(229, 79, 6, 0),
(230, 80, 6, 0),
(231, 81, 6, 0),
(232, 82, 6, 0),
(233, 83, 6, 0),
(234, 84, 6, 0),
(235, 85, 6, 0),
(236, 86, 6, 0),
(237, 87, 6, 0),
(238, 88, 6, 0),
(239, 89, 6, 0),
(240, 90, 6, 0),
(241, 91, 6, 0),
(242, 92, 6, 0),
(243, 93, 6, 0),
(244, 94, 6, 0),
(245, 95, 6, 0),
(246, 96, 6, 0),
(247, 97, 6, 0),
(248, 98, 6, 0),
(249, 99, 6, 0),
(301, 51, 8, 0),
(302, 52, 8, 0),
(303, 53, 8, 0),
(304, 54, 8, 0),
(305, 55, 8, 0),
(306, 56, 8, 0),
(307, 57, 8, 0),
(308, 58, 8, 0),
(309, 59, 8, 0),
(310, 60, 8, 0),
(311, 61, 8, 0),
(312, 62, 8, 0),
(313, 63, 8, 0),
(314, 64, 8, 0),
(315, 65, 8, 0),
(316, 66, 8, 0),
(317, 67, 8, 0),
(318, 68, 8, 0),
(319, 69, 8, 0),
(320, 70, 8, 0),
(321, 71, 8, 0),
(322, 72, 8, 0),
(323, 73, 8, 0),
(324, 74, 8, 0),
(325, 75, 8, 0),
(326, 76, 8, 0),
(327, 77, 8, 0),
(328, 78, 8, 0),
(329, 79, 8, 0),
(330, 80, 8, 0),
(331, 81, 8, 0),
(332, 82, 8, 0),
(333, 83, 8, 0),
(334, 84, 8, 0),
(335, 85, 8, 0),
(336, 86, 8, 0),
(337, 87, 8, 0),
(338, 88, 8, 0),
(339, 89, 8, 0),
(340, 90, 8, 0),
(341, 91, 8, 0),
(342, 92, 8, 0),
(343, 93, 8, 0),
(344, 94, 8, 0),
(345, 95, 8, 0),
(346, 96, 8, 0),
(347, 97, 8, 0),
(348, 98, 8, 0),
(349, 99, 8, 0);

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `grade_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `component_id` int(11) NOT NULL,
  `score` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`grade_id`, `student_id`, `component_id`, `score`) VALUES
(11, 91, 4, 100.00),
(12, 66, 4, 100.00),
(13, 87, 4, 100.00),
(14, 53, 4, 100.00),
(15, 97, 4, 100.00),
(16, 89, 4, 100.00),
(17, 84, 4, 100.00),
(18, 62, 4, 100.00),
(19, 64, 4, 100.00),
(20, 98, 4, 100.00),
(21, 55, 4, 100.00),
(22, 76, 4, 100.00),
(23, 79, 4, 100.00),
(24, 51, 4, 100.00),
(26, 85, 4, 100.00),
(27, 88, 4, 100.00),
(28, 82, 4, 100.00),
(29, 59, 4, 100.00),
(30, 92, 4, 100.00),
(31, 56, 4, 100.00),
(32, 75, 4, 100.00),
(33, 60, 4, 100.00),
(34, 70, 4, 100.00),
(35, 99, 4, 100.00),
(36, 72, 4, 100.00),
(37, 95, 4, 100.00),
(38, 80, 4, 100.00),
(39, 81, 4, 100.00),
(40, 57, 4, 100.00),
(41, 69, 4, 100.00),
(42, 61, 4, 100.00),
(43, 94, 4, 100.00),
(44, 86, 4, 100.00),
(45, 68, 4, 100.00),
(46, 93, 4, 100.00),
(47, 58, 4, 100.00),
(48, 96, 4, 100.00),
(49, 52, 4, 100.00),
(50, 63, 4, 100.00),
(51, 83, 4, 100.00),
(52, 77, 4, 100.00),
(53, 71, 4, 100.00),
(54, 54, 4, 100.00),
(55, 78, 4, 100.00),
(56, 73, 4, 100.00),
(57, 67, 4, 100.00),
(58, 74, 4, 100.00),
(59, 90, 4, 100.00),
(60, 65, 4, 100.00),
(61, 91, 5, 100.00),
(62, 66, 5, 100.00),
(63, 87, 5, 100.00),
(64, 53, 5, 100.00),
(65, 97, 5, 100.00),
(66, 89, 5, 100.00),
(67, 84, 5, 100.00),
(68, 62, 5, 100.00),
(69, 64, 5, 100.00),
(70, 98, 5, 1.00),
(71, 91, 7, 150.00),
(72, 66, 7, 150.00),
(73, 87, 7, 150.00),
(74, 53, 7, 150.00),
(75, 97, 7, 150.00),
(76, 89, 7, 150.00),
(77, 84, 7, 150.00),
(78, 62, 7, 150.00),
(79, 64, 7, 150.00),
(80, 98, 7, 150.00),
(81, 91, 6, 100.00),
(82, 66, 6, 100.00),
(83, 87, 6, 100.00),
(84, 53, 6, 100.00),
(85, 97, 6, 100.00),
(86, 89, 6, 100.00),
(87, 84, 6, 100.00),
(88, 62, 6, 100.00),
(89, 64, 6, 100.00),
(90, 98, 6, 100.00),
(91, 91, 2, 30.00),
(92, 66, 2, 30.00),
(93, 87, 2, 30.00),
(94, 53, 2, 30.00),
(95, 97, 2, 30.00),
(96, 89, 2, 30.00),
(97, 84, 2, 30.00),
(98, 62, 2, 30.00),
(99, 64, 2, 30.00),
(100, 98, 2, 30.00),
(101, 91, 3, 50.00),
(102, 66, 3, 50.00),
(103, 87, 3, 50.00),
(104, 53, 3, 50.00),
(105, 97, 3, 50.00),
(106, 89, 3, 50.00),
(107, 84, 3, 50.00),
(108, 62, 3, 50.00),
(109, 64, 3, 50.00),
(110, 98, 3, 50.00),
(111, 91, 10, 100.00),
(112, 66, 10, 100.00),
(113, 87, 10, 100.00),
(114, 53, 10, 100.00),
(115, 97, 10, 100.00),
(116, 89, 10, 100.00),
(117, 84, 10, 100.00),
(118, 62, 10, 100.00),
(119, 64, 10, 100.00),
(120, 98, 10, 100.00),
(121, 91, 11, 100.00),
(122, 66, 11, 100.00),
(123, 87, 11, 100.00),
(124, 53, 11, 100.00),
(125, 97, 11, 100.00),
(126, 89, 11, 100.00),
(127, 84, 11, 100.00),
(128, 62, 11, 100.00),
(129, 64, 11, 100.00),
(130, 98, 11, 100.00),
(131, 91, 13, 100.00),
(132, 66, 13, 100.00),
(133, 87, 13, 100.00),
(134, 53, 13, 100.00),
(135, 97, 13, 100.00),
(136, 89, 13, 100.00),
(137, 84, 13, 100.00),
(138, 62, 13, 100.00),
(139, 64, 13, 100.00),
(140, 98, 13, 100.00),
(141, 91, 12, 100.00),
(142, 66, 12, 100.00),
(143, 87, 12, 100.00),
(144, 53, 12, 100.00),
(145, 97, 12, 100.00),
(146, 89, 12, 100.00),
(147, 84, 12, 100.00),
(148, 62, 12, 100.00),
(149, 64, 12, 100.00),
(150, 98, 12, 100.00),
(151, 91, 8, 50.00),
(152, 66, 8, 50.00),
(153, 87, 8, 50.00),
(154, 53, 8, 50.00),
(155, 97, 8, 50.00),
(156, 89, 8, 50.00),
(157, 84, 8, 50.00),
(158, 62, 8, 50.00),
(159, 64, 8, 50.00),
(160, 98, 8, 50.00),
(161, 91, 9, 100.00),
(162, 66, 9, 100.00),
(163, 87, 9, 100.00),
(164, 53, 9, 100.00),
(165, 97, 9, 100.00),
(166, 89, 9, 100.00),
(167, 84, 9, 100.00),
(168, 62, 9, 100.00),
(169, 64, 9, 100.00),
(170, 98, 9, 100.00),
(171, 91, 14, 50.00),
(172, 66, 14, 50.00),
(173, 87, 14, 50.00),
(174, 53, 14, 50.00),
(175, 97, 14, 50.00),
(176, 89, 14, 50.00),
(177, 84, 14, 50.00),
(178, 62, 14, 50.00),
(179, 64, 14, 50.00),
(180, 98, 14, 50.00),
(181, 91, 15, 100.00),
(182, 66, 15, 100.00),
(183, 87, 15, 100.00),
(184, 53, 15, 100.00),
(185, 97, 15, 100.00),
(186, 89, 15, 100.00),
(187, 84, 15, 100.00),
(188, 62, 15, 100.00),
(189, 64, 15, 100.00),
(190, 98, 15, 100.00),
(191, 91, 17, 150.00),
(192, 66, 17, 150.00),
(193, 87, 17, 150.00),
(194, 53, 17, 150.00),
(195, 97, 17, 150.00),
(196, 89, 17, 150.00),
(197, 84, 17, 150.00),
(198, 62, 17, 150.00),
(199, 64, 17, 150.00),
(200, 98, 17, 150.00),
(201, 91, 16, 100.00),
(202, 66, 16, 100.00),
(203, 87, 16, 100.00),
(204, 53, 16, 100.00),
(205, 97, 16, 100.00),
(206, 89, 16, 100.00),
(207, 84, 16, 100.00),
(208, 62, 16, 100.00),
(209, 64, 16, 100.00),
(210, 98, 16, 100.00),
(211, 91, 18, 100.00),
(212, 66, 18, 100.00),
(213, 87, 18, 100.00),
(214, 53, 18, 100.00),
(215, 97, 18, 100.00),
(216, 89, 18, 100.00),
(217, 84, 18, 100.00),
(218, 62, 18, 100.00),
(219, 64, 18, 100.00),
(220, 98, 18, 100.00),
(221, 91, 20, 100.00),
(222, 66, 20, 100.00),
(223, 87, 20, 100.00),
(224, 53, 20, 100.00),
(225, 97, 20, 100.00),
(226, 89, 20, 100.00),
(227, 84, 20, 100.00),
(228, 62, 20, 100.00),
(229, 64, 20, 100.00),
(230, 98, 20, 100.00),
(231, 91, 19, 100.00),
(232, 66, 19, 100.00),
(233, 87, 19, 100.00),
(234, 53, 19, 100.00),
(235, 97, 19, 100.00),
(236, 89, 19, 100.00),
(237, 84, 19, 100.00),
(238, 62, 19, 100.00),
(239, 64, 19, 100.00),
(240, 98, 19, 100.00);

-- --------------------------------------------------------

--
-- Table structure for table `grading_components`
--

CREATE TABLE `grading_components` (
  `component_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `term` enum('PRE-LIM','MIDTERM','PRE-FINALS','FINALS') NOT NULL,
  `component_name` varchar(255) NOT NULL,
  `weight` decimal(5,2) NOT NULL,
  `max_score` int(11) NOT NULL DEFAULT 100
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grading_components`
--

INSERT INTO `grading_components` (`component_id`, `subject_id`, `term`, `component_name`, `weight`, `max_score`) VALUES
(2, 9, 'PRE-LIM', 'Quiz', 10.00, 30),
(3, 9, 'PRE-LIM', 'Quiz 2', 20.00, 50),
(4, 9, 'PRE-LIM', 'Activity 1', 30.00, 100),
(5, 9, 'PRE-LIM', 'Activity 2', 15.00, 100),
(6, 9, 'PRE-LIM', 'Project', 15.00, 100),
(7, 9, 'PRE-LIM', 'Exam', 10.00, 150),
(8, 9, 'MIDTERM', 'Quiz 1', 15.00, 50),
(9, 9, 'MIDTERM', 'Quiz 2', 15.00, 100),
(10, 9, 'MIDTERM', 'Activity 1', 20.00, 100),
(11, 9, 'MIDTERM', 'Activity 2', 20.00, 100),
(12, 9, 'MIDTERM', 'Project', 15.00, 100),
(13, 9, 'MIDTERM', 'Exam', 15.00, 100),
(14, 9, 'PRE-FINALS', 'Activity 1', 30.00, 50),
(15, 9, 'PRE-FINALS', 'Activity 2', 30.00, 100),
(16, 9, 'PRE-FINALS', 'Mock Def', 20.00, 100),
(17, 9, 'PRE-FINALS', 'Exam', 20.00, 150),
(18, 9, 'FINALS', 'Activity 1', 50.00, 100),
(19, 9, 'FINALS', 'Final Defense', 30.00, 100),
(20, 9, 'FINALS', 'Exam', 20.00, 100);

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `section_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `school_year` varchar(9) NOT NULL COMMENT 'e.g., 2024-2025',
  `semester` enum('1st Sem','2nd Sem','Summer') NOT NULL,
  `section_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`section_id`, `subject_id`, `school_year`, `semester`, `section_name`) VALUES
(6, 9, '2025-2026', '1st Sem', 'BSIT-2A'),
(8, 14, '2025-2026', '1st Sem', '2A'),
(9, 16, '2026-2027', '1st Sem', '2A');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `image_path` varchar(255) DEFAULT 'default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `first_name`, `middle_name`, `last_name`, `image_path`) VALUES
(51, 'Juan', 'Santos', 'Dela Cruz', 'default.png'),
(52, 'Maria', 'Lopez', 'Reyes', 'default.png'),
(53, 'Jose', 'Ramirez', 'Bautista', 'default.png'),
(54, 'Ana', 'Dizon', 'Santos', 'default.png'),
(55, 'Mark', 'Villanueva', 'Cruz', 'default.png'),
(56, 'John', 'Mendoza', 'Garcia', 'default.png'),
(57, 'Carla', 'Reyes', 'Mendoza', 'default.png'),
(58, 'Paolo', 'Fernandez', 'Ramos', 'default.png'),
(59, 'Angela', 'Bautista', 'Flores', 'default.png'),
(60, 'Carlo', 'Morales', 'Gonzales', 'default.png'),
(61, 'Kristine', 'Tolentino', 'Navarro', 'default.png'),
(62, 'Joshua', 'Mercado', 'Castillo', 'default.png'),
(63, 'Janelle', 'Aquino', 'Rivera', 'default.png'),
(64, 'Miguel', 'Castillo', 'Chavez', 'default.png'),
(65, 'Patricia', 'Gomez', 'Villanueva', 'default.png'),
(66, 'Jerome', 'Bautista', 'Aguilar', 'default.png'),
(67, 'Beatriz', 'Cruz', 'Torres', 'default.png'),
(68, 'Nathaniel', 'Santos', 'Perez', 'default.png'),
(69, 'Sophia', 'Ramos', 'Morales', 'default.png'),
(70, 'Francis', 'Dela Peña', 'Gutierrez', 'default.png'),
(71, 'Camille', 'Garcia', 'Sandoval', 'default.png'),
(72, 'Victor', 'Chua', 'Lim', 'default.png'),
(73, 'Melissa', 'Ong', 'Tan', 'default.png'),
(74, 'Christian', 'Tan', 'Uy', 'default.png'),
(75, 'Andrea', 'Lim', 'Go', 'default.png'),
(76, 'Patrick', 'Morales', 'David', 'default.png'),
(77, 'Clarisse', 'Dizon', 'Salazar', 'default.png'),
(78, 'Gabriel', 'Navarro', 'Soriano', 'default.png'),
(79, 'Hannah', 'Cruz', 'De Leon', 'default.png'),
(80, 'Lawrence', 'Pascual', 'Macaraeg', 'default.png'),
(81, 'Justine', 'Flores', 'Manalo', 'default.png'),
(82, 'Nicole', 'Gutierrez', 'Evangelista', 'default.png'),
(83, 'Tristan', 'Cortez', 'Robles', 'default.png'),
(84, 'Denise', 'Manalo', 'Castillo', 'default.png'),
(85, 'Adrian', 'Rivera', 'Domingo', 'default.png'),
(86, 'Mikaela', 'Santos', 'Peralta', 'default.png'),
(87, 'Raymond', 'Javier', 'Alcantara', 'default.png'),
(88, 'Lara', 'Gonzales', 'Enriquez', 'default.png'),
(89, 'Vincent', 'Ramirez', 'Cabrera', 'default.png'),
(90, 'Alyssa', 'Cruz', 'Vergara', 'default.png'),
(91, 'Julian', 'Torres', 'Abad', 'default.png'),
(92, 'Erika', 'Bautista', 'Francisco', 'default.png'),
(93, 'Leonard', 'Reyes', 'Pineda', 'default.png'),
(94, 'Bianca', 'Dela Cruz', 'Ocampo', 'default.png'),
(95, 'Eric', 'Mendoza', 'Lozano', 'default.png'),
(96, 'Thea', 'Navarro', 'Ramos', 'default.png'),
(97, 'Kevin', 'Morales', 'Bautista', 'default.png'),
(98, 'Kimberly', 'Tan', 'Chua', 'default.png'),
(99, 'Jordan', 'Go', 'Lim', 'default.png');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL,
  `subject_name` varchar(255) NOT NULL,
  `class_code` varchar(50) DEFAULT NULL,
  `prelim_weight` decimal(5,2) NOT NULL DEFAULT 25.00,
  `midterm_weight` decimal(5,2) NOT NULL DEFAULT 25.00,
  `prefinals_weight` decimal(5,2) NOT NULL DEFAULT 25.00,
  `finals_weight` decimal(5,2) NOT NULL DEFAULT 25.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subject_id`, `subject_name`, `class_code`, `prelim_weight`, `midterm_weight`, `prefinals_weight`, `finals_weight`) VALUES
(9, 'Object Oriented Programming', 'Major', 25.00, 25.00, 25.00, 25.00),
(14, 'Introduction to Programming', 'Major', 25.00, 25.00, 25.00, 25.00),
(15, 'awdwad', 'awd', 25.00, 25.00, 25.00, 25.00),
(16, 'Rizal', 'MAWewa', 25.00, 25.00, 25.00, 25.00),
(17, 'awdwad', 'awdwa', 25.00, 25.00, 25.00, 25.00),
(18, 'awdwa', 'awdaw', 25.00, 25.00, 25.00, 25.00),
(19, 'ewa', 'wae', 25.00, 25.00, 25.00, 25.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(6, 'admin', '$2y$10$sW8iYn3nhaOkqEaAX3/qW.zZLOtEzj8M7wx02WfJllnB.0zmGecam');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `fk_enrollment_section` (`section_id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`grade_id`),
  ADD UNIQUE KEY `unique_grade` (`student_id`,`component_id`),
  ADD KEY `component_id` (`component_id`);

--
-- Indexes for table `grading_components`
--
ALTER TABLE `grading_components`
  ADD PRIMARY KEY (`component_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`section_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`subject_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=351;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `grade_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=333;

--
-- AUTO_INCREMENT for table `grading_components`
--
ALTER TABLE `grading_components`
  MODIFY `component_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `section_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_enrollment_section` FOREIGN KEY (`section_id`) REFERENCES `sections` (`section_id`) ON DELETE CASCADE;

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grades_ibfk_2` FOREIGN KEY (`component_id`) REFERENCES `grading_components` (`component_id`) ON DELETE CASCADE;

--
-- Constraints for table `grading_components`
--
ALTER TABLE `grading_components`
  ADD CONSTRAINT `grading_components_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`) ON DELETE CASCADE;

--
-- Constraints for table `sections`
--
ALTER TABLE `sections`
  ADD CONSTRAINT `sections_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
