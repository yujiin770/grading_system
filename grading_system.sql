-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 11, 2025 at 09:51 AM
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
(6, 4, 2, 0),
(8, 5, 2, 0),
(9, 6, 2, 0),
(10, 11, 2, 0),
(11, 14, 2, 0),
(12, 13, 2, 0),
(13, 12, 2, 0),
(14, 9, 2, 0),
(15, 10, 2, 0),
(16, 7, 2, 0),
(17, 8, 2, 0),
(18, 7, 3, 0),
(19, 16, 2, 0),
(20, 17, 2, 0),
(21, 18, 2, 0),
(22, 19, 2, 0),
(23, 20, 2, 0),
(24, 16, 5, 0),
(25, 17, 5, 0),
(26, 18, 5, 0),
(27, 19, 5, 0),
(28, 20, 5, 0),
(29, 5, 5, 0),
(30, 4, 5, 0),
(31, 21, 5, 0),
(32, 22, 5, 0),
(33, 23, 5, 0),
(34, 11, 5, 0),
(35, 6, 5, 0),
(36, 14, 5, 0),
(37, 13, 5, 0),
(38, 12, 5, 0),
(39, 9, 5, 0),
(40, 15, 5, 0),
(41, 10, 5, 0),
(42, 7, 5, 0),
(43, 8, 5, 0);

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
(1, 4, 14, 50.00),
(2, 4, 15, 50.00),
(3, 4, 16, 150.00),
(4, 4, 17, 100.00),
(5, 4, 13, 30.00),
(6, 4, 12, 20.00),
(10, 5, 14, 50.00),
(12, 5, 15, 50.00),
(16, 11, 15, 50.00),
(18, 6, 15, 50.00),
(19, 14, 15, 50.00),
(20, 13, 15, 50.00),
(21, 12, 15, 50.00),
(22, 9, 15, 50.00),
(23, 10, 15, 50.00),
(24, 7, 15, 50.00),
(26, 11, 14, 50.00),
(28, 6, 14, 50.00),
(29, 14, 14, 50.00),
(30, 13, 14, 50.00),
(31, 12, 14, 50.00),
(32, 9, 14, 50.00),
(33, 10, 14, 50.00),
(34, 7, 14, 50.00),
(35, 8, 14, 50.00),
(36, 8, 15, 50.00),
(38, 11, 16, 150.00),
(39, 5, 16, 150.00),
(40, 6, 16, 150.00),
(41, 14, 16, 150.00),
(42, 13, 16, 150.00),
(43, 12, 16, 150.00),
(44, 9, 16, 150.00),
(45, 10, 16, 150.00),
(46, 7, 16, 150.00),
(47, 8, 16, 150.00),
(49, 11, 17, 100.00),
(50, 5, 17, 100.00),
(51, 6, 17, 100.00),
(52, 14, 17, 100.00),
(53, 13, 17, 100.00),
(54, 12, 17, 100.00),
(55, 9, 17, 100.00),
(56, 10, 17, 100.00),
(57, 7, 17, 100.00),
(58, 8, 17, 100.00),
(60, 11, 13, 30.00),
(61, 5, 13, 30.00),
(62, 6, 13, 30.00),
(63, 14, 13, 30.00),
(64, 13, 13, 30.00),
(65, 12, 13, 30.00),
(66, 9, 13, 30.00),
(67, 10, 13, 30.00),
(68, 7, 13, 30.00),
(69, 8, 13, 30.00),
(71, 11, 12, 20.00),
(72, 5, 12, 20.00),
(73, 6, 12, 20.00),
(74, 14, 12, 20.00),
(75, 13, 12, 20.00),
(76, 12, 12, 20.00),
(77, 9, 12, 20.00),
(78, 10, 12, 20.00),
(79, 7, 12, 20.00),
(80, 8, 12, 20.00),
(88, 4, 21, 120.00),
(89, 17, 21, 120.00),
(90, 11, 21, 120.00),
(91, 5, 21, 120.00),
(92, 6, 21, 120.00),
(93, 14, 21, 120.00),
(94, 13, 21, 120.00),
(95, 12, 21, 120.00),
(96, 16, 21, 120.00),
(97, 19, 21, 120.00),
(98, 20, 21, 120.00),
(99, 9, 21, 120.00),
(100, 10, 21, 120.00),
(101, 18, 21, 120.00),
(102, 7, 21, 120.00),
(103, 8, 21, 120.00),
(104, 4, 22, 100.00),
(105, 17, 22, 100.00),
(106, 11, 22, 100.00),
(107, 5, 22, 100.00),
(108, 6, 22, 100.00),
(109, 14, 22, 100.00),
(110, 13, 22, 100.00),
(111, 12, 22, 100.00),
(112, 16, 22, 100.00),
(113, 19, 22, 100.00),
(114, 4, 19, 50.00),
(115, 17, 19, 50.00),
(116, 11, 19, 50.00),
(117, 5, 19, 50.00),
(118, 6, 19, 50.00),
(119, 14, 19, 50.00),
(120, 13, 19, 50.00),
(121, 12, 19, 50.00),
(122, 16, 19, 50.00),
(123, 19, 19, 50.00),
(124, 4, 20, 30.00),
(125, 17, 20, 30.00),
(126, 11, 20, 30.00),
(127, 5, 20, 30.00),
(128, 6, 20, 30.00),
(129, 14, 20, 30.00),
(130, 13, 20, 30.00),
(131, 12, 20, 30.00),
(132, 16, 20, 30.00),
(133, 19, 20, 30.00),
(134, 20, 20, 30.00),
(135, 9, 20, 30.00),
(136, 10, 20, 30.00),
(137, 18, 20, 30.00),
(138, 7, 20, 30.00),
(139, 8, 20, 30.00);

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
(12, 1, 'PRE-LIM', 'Quiz1', 10.00, 20),
(13, 1, 'PRE-LIM', 'Quiz 2', 10.00, 30),
(14, 1, 'PRE-LIM', 'Activity 1', 20.00, 50),
(15, 1, 'PRE-LIM', 'Activity 2', 20.00, 50),
(16, 1, 'PRE-LIM', 'Exam', 25.00, 150),
(17, 1, 'PRE-LIM', 'Project', 15.00, 100),
(19, 1, 'MIDTERM', 'Quiz 1', 20.00, 50),
(20, 1, 'MIDTERM', 'Quiz 2', 20.00, 30),
(21, 1, 'MIDTERM', 'Exam', 30.00, 120),
(22, 1, 'MIDTERM', 'Project', 30.00, 100),
(23, 1, 'PRE-FINALS', 'Quiz 1', 30.00, 100),
(24, 1, 'PRE-FINALS', 'Activity 1', 30.00, 100),
(25, 1, 'PRE-FINALS', 'Exam', 20.00, 150),
(26, 1, 'PRE-FINALS', 'Mock defense', 20.00, 100),
(27, 1, 'FINALS', 'Activity 1', 40.00, 100),
(28, 1, 'FINALS', 'Activity 2', 30.00, 100),
(29, 1, 'FINALS', 'Final Defense', 30.00, 100);

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `section_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `section_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`section_id`, `subject_id`, `section_name`) VALUES
(2, 1, '2B'),
(3, 1, '2A'),
(5, 6, '2A');

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
(4, 'Eugene', 'Loyola', 'Almira', 'default.png'),
(5, 'Ivan', '', 'Bidayan', 'default.png'),
(6, 'Mark Lawrence', '', 'Catubay', 'default.png'),
(7, 'awe', 'wae', 'wae', 'default.png'),
(8, 'wae', 'wae', 'wae', 'default.png'),
(9, 'awe', 'wae', 'ewsd', 'default.png'),
(10, 'ewadsa', 'wae', 'sdawa', 'default.png'),
(11, 'waeae', 'dsadsa', 'asdas', 'default.png'),
(12, 'wadwad', 'wdsaxzcz', 'cxzcxz', 'default.png'),
(13, 'cxzsad', 'cxzasd', 'cxzcasd', 'default.png'),
(14, 'cxzsad', 'cxzasd', 'cxzasd', 'default.png'),
(15, 'kllkkl', 'klk', 'llkl', 'default.png'),
(16, 'wew', 'Edric', 'DaugDaug', 'default.png'),
(17, 'aw', 'Adrian', 'Angeles', 'default.png'),
(18, 'NJAWDNJK', 'AJKWDN', 'vnjsdksdvnjk', 'default.png'),
(19, 'MFDSL', 'MKLAW', 'DLSAA', 'default.png'),
(20, 'KLJAS', 'LKWJAE', 'DMKSFL', 'default.png'),
(21, 'John', 'Michael L', 'Doe', 'default.png'),
(22, 'Jane', 'K', 'Smith', 'default.png'),
(23, 'Juan', 'Dela J', 'Cruz', 'default.png');

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
(1, 'Object Oriented Programming', 'Major', 15.00, 25.00, 30.00, 30.00),
(6, 'Introduction to Programming', 'Major', 25.00, 25.00, 25.00, 25.00);

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `grade_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=140;

--
-- AUTO_INCREMENT for table `grading_components`
--
ALTER TABLE `grading_components`
  MODIFY `component_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `section_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
