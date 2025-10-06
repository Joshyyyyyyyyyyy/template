-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 06, 2025 at 05:51 PM
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
-- Database: `pms`
--

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `fee_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_status` varchar(50) NOT NULL,
  `paymongo_payment_id` varchar(255) DEFAULT NULL,
  `paymongo_source_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `student_id`, `fee_id`, `amount`, `payment_method`, `payment_status`, `paymongo_payment_id`, `paymongo_source_id`, `created_at`) VALUES
(26, 9, 3, 25.00, 'maya', 'completed', NULL, 'src_o8yDZnQsL3BV5qkwbBZri6WH', '2025-10-04 06:59:01'),
(29, 19, 11, 75.00, 'gcash', 'completed', NULL, 'src_tsRfTAKQZyWwBHpTSrGeV6Fo', '2025-10-06 04:50:22'),
(30, 19, 11, 700.00, 'gcash', 'completed', NULL, 'src_qs5MtBCisaqg2As9MJiktBJQ', '2025-10-06 06:45:04');

-- --------------------------------------------------------

--
-- Table structure for table `scholarships`
--

CREATE TABLE `scholarships` (
  `scholarship_id` int(11) NOT NULL,
  `scholarship_name` varchar(100) NOT NULL,
  `scholarship_code` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `coverage_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scholarships`
--

INSERT INTO `scholarships` (`scholarship_id`, `scholarship_name`, `scholarship_code`, `description`, `coverage_percentage`, `is_active`, `created_at`) VALUES
(1, 'ACCAEX Scholarship', 'ACCAEX', 'Academic Excellence Scholarship for students with outstanding academic performance', 50.00, 1, '2025-10-02 14:28:33'),
(2, 'PWD Scholarship', 'PWD', 'Scholarship for Persons with Disabilities - Automatic 100% coverage with complete requirements', 100.00, 1, '2025-10-02 14:28:33'),
(3, 'Sports Scholarship', 'SPORTS', 'Athletic Scholarship for student athletes - Automatic 100% coverage with complete requirements', 100.00, 1, '2025-10-02 14:28:33');

-- --------------------------------------------------------

--
-- Table structure for table `scholarship_applications`
--

CREATE TABLE `scholarship_applications` (
  `application_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `scholarship_id` int(11) NOT NULL,
  `scholarship_amount` decimal(10,2) DEFAULT 0.00 COMMENT 'Approved scholarship discount amount',
  `scholarship_percentage` decimal(5,2) DEFAULT 0.00 COMMENT 'Scholarship percentage granted',
  `application_status` enum('pending','under_review','approved','rejected','archived') NOT NULL DEFAULT 'pending',
  `application_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `review_date` timestamp NULL DEFAULT NULL,
  `reviewed_by` varchar(255) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `academic_year` varchar(50) NOT NULL,
  `semester` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scholarship_applications`
--

INSERT INTO `scholarship_applications` (`application_id`, `student_id`, `scholarship_id`, `scholarship_amount`, `scholarship_percentage`, `application_status`, `application_date`, `review_date`, `reviewed_by`, `approved_by`, `remarks`, `academic_year`, `semester`) VALUES
(4, 9, 3, 6025.00, 100.00, 'archived', '2025-10-04 07:52:38', '2025-10-04 07:53:06', 'Scholarship Coordinator', NULL, '', 'AY 2025-2026', '1st Semester'),
(12, 19, 3, 1250.00, 25.00, 'approved', '2025-10-05 12:18:05', '2025-10-05 12:18:36', 'Ms. Angela Mae F. Santos', NULL, '', 'AY 2025-2026', '1st Semester');

-- --------------------------------------------------------

--
-- Table structure for table `scholarship_requirements`
--

CREATE TABLE `scholarship_requirements` (
  `requirement_id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `requirement_name` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_type` varchar(100) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scholarship_requirements`
--

INSERT INTO `scholarship_requirements` (`requirement_id`, `application_id`, `requirement_name`, `file_name`, `file_path`, `file_size`, `file_type`, `uploaded_at`) VALUES
(7, 4, 'coach_letter', 'ASSIGNMENT2-Temlates.docx', 'C:\\XAMMP\\htdocs\\PMS/uploads/scholarships/9/4/coach_letter_1759564358_68e0d2462bde6.docx', 49287, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', '2025-10-04 07:52:38'),
(8, 4, 'cor', 'ASSIGNMENT2-Temlates.docx', 'C:\\XAMMP\\htdocs\\PMS/uploads/scholarships/9/4/cor_1759564358_68e0d24635236.docx', 49287, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', '2025-10-04 07:52:38'),
(23, 12, 'coach_letter', 'red.jpg', 'C:\\XAMMP\\htdocs\\PMS/uploads/scholarships/19/12/coach_letter_1759666685_68e261fd8d88c.jpg', 202501, 'image/jpeg', '2025-10-05 12:18:05'),
(24, 12, 'cor', 'paymongo.jpg', 'C:\\XAMMP\\htdocs\\PMS/uploads/scholarships/19/12/cor_1759666685_68e261fd8e81e.jpg', 2495, 'image/jpeg', '2025-10-05 12:18:05');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `program` varchar(255) NOT NULL,
  `year_level` varchar(50) NOT NULL,
  `gpa` decimal(3,2) DEFAULT NULL COMMENT 'Student GPA for scholarship calculation (e.g., 1.00, 1.25, 1.50, 1.75)',
  `student_status` enum('regular','irregular') NOT NULL DEFAULT 'regular',
  `college` varchar(255) NOT NULL,
  `campus` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `user_id`, `name`, `email`, `program`, `year_level`, `gpa`, `student_status`, `college`, `campus`, `created_at`) VALUES
(9, 9, 'Red Gin Bilog', 'riverojosh19@gmail.com', 'BS Information Technology', '3rd Year', NULL, 'regular', 'College of Information Technology', 'Quezon City Campus', '2025-10-04 05:13:10'),
(16, 18, 'Red Gin Bilog', 'Joshua@gmail.com', 'BS Information Technology', '2nd Year', NULL, 'regular', 'College of Nursing', 'Quezon City Campus', '2025-10-04 17:09:40'),
(19, 21, 'Joshua Andrie R. Suruiz', 'suruizandrie@gmail.com', 'BS Information Technology', '2nd Year', NULL, 'regular', 'College of Information Technology', 'Quezon City Campus', '2025-10-05 12:17:20'),
(21, 23, 'Red Gin Bilog', 'redginhero@gmail.com', 'BS Information Technology', '3rd Year', NULL, 'regular', 'College of Business Administration', 'Quezon City Campus', '2025-10-06 05:56:52');

-- --------------------------------------------------------

--
-- Table structure for table `tuition_fees`
--

CREATE TABLE `tuition_fees` (
  `fee_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `semester` varchar(50) NOT NULL,
  `academic_year` varchar(50) NOT NULL,
  `tuition_fee` decimal(10,2) NOT NULL,
  `tuition_sponsored` tinyint(1) NOT NULL DEFAULT 0,
  `misc_fees` decimal(10,2) NOT NULL,
  `enrollment_fees` decimal(10,2) NOT NULL,
  `less_payment` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `paid_amount` decimal(10,2) DEFAULT 0.00,
  `balance` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tuition_fees`
--

INSERT INTO `tuition_fees` (`fee_id`, `student_id`, `semester`, `academic_year`, `tuition_fee`, `tuition_sponsored`, `misc_fees`, `enrollment_fees`, `less_payment`, `total_amount`, `paid_amount`, `balance`, `created_at`) VALUES
(3, 9, '1st Semester', 'AY 2025-2026', 9000.00, 1, 5175.00, 850.00, 6025.00, 6025.00, 25.00, 0.00, '2025-10-04 05:13:10'),
(9, 16, '1st Semester', 'AY 2025-2026', 9000.00, 1, 5175.00, 850.00, 9037.50, 6025.00, 0.00, 0.00, '2025-10-04 17:09:40'),
(11, 19, '1st Semester', 'AY 2025-2026', 9000.00, 1, 5175.00, 850.00, 1250.00, 6025.00, 2025.00, 4000.00, '2025-10-05 12:17:20'),
(13, 21, '1st Semester', 'AY 2025-2026', 9000.00, 1, 5175.00, 850.00, 0.00, 6025.00, 0.00, 6025.00, '2025-10-06 05:56:52');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL COMMENT 'Hashed password',
  `name` varchar(255) DEFAULT NULL COMMENT 'Full name of the user',
  `position` varchar(100) DEFAULT NULL COMMENT 'Position/role title',
  `user_type` enum('student','scholarship_coordinator','financial_controller','admin') NOT NULL,
  `profile_picture` varchar(500) DEFAULT NULL COMMENT 'Path to profile picture',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password`, `name`, `position`, `user_type`, `profile_picture`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(9, 'riverojosh19@gmail.com', '$2y$10$DBJrxE9ugedNzGTpMYv78ui6FyZe9PAIoXBj9piy8CKWuJ.ffOFGS', NULL, NULL, 'student', '/uploads/profiles/students/student_1759554789_68e0ace5c2d4d.jpg', 1, '2025-10-04 07:52:14', '2025-10-04 05:13:09', '2025-10-04 07:52:14'),
(12, 'andrie@gmail.com', '$2y$10$NCi8JsSb86uXkN244MpLvekcwuHUd/D0cJyVPAXNPh6B8XBfrYrvC', 'Ms. Angela Mae F. Santos', 'Scholarship Coordinator', 'scholarship_coordinator', NULL, 1, '2025-10-06 05:57:46', '2025-10-04 08:52:38', '2025-10-06 05:57:46'),
(13, 'suruiz@gmail.com', '$2y$10$u6M2mrcBCHZloG.Ar.09..q0cqu/jZFpIWnyVtIksJfBQPSAviJES', 'Ms. Liza D. Ramos', 'admin', 'admin', NULL, 1, '2025-10-05 16:37:54', '2025-10-04 09:52:42', '2025-10-05 16:37:54'),
(18, 'Joshua@gmail.com', '$2y$10$L2I7xlVYWRLGVtfI5fmsxu8knbfdroY7KPhEOA1e7rD6vl6Aya91a', NULL, NULL, 'student', '/uploads/profiles/students/student_1759597780_68e154d46d7a5.jpg', 1, '2025-10-04 17:24:54', '2025-10-04 17:09:40', '2025-10-04 17:24:54'),
(21, 'suruizandrie@gmail.com', '$2y$10$dn50OckaqokOq/MUfLUTjud/lN8Y7USynPY2lew.ZQLzZxM6AW79C', NULL, NULL, 'student', '/uploads/profiles/students/student_1759666640_68e261d0245c2.jpg', 1, '2025-10-06 06:44:13', '2025-10-05 12:17:20', '2025-10-06 06:44:13'),
(22, 'rivero@gmail.com', '$2y$10$Gd6fW6kUsMNnoYkMyjTAtO8/cx/rZXpJI7TFn.Q4CYkrtgLoC55h6', 'Joshua Suruiz', 'Financial Controller', 'financial_controller', NULL, 1, '2025-10-06 05:02:57', '2025-10-05 17:04:13', '2025-10-06 05:02:57'),
(23, 'redginhero@gmail.com', '$2y$10$FIXz10Xcen1LAn3sF29QV.9GRHWSFg0S.SMAI4nFuxNL8U5Teapc2', NULL, NULL, 'student', '/uploads/profiles/students/student_1759730212_68e35a2481f4a.jpg', 1, '2025-10-06 05:57:10', '2025-10-06 05:56:52', '2025-10-06 05:57:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `fee_id` (`fee_id`);

--
-- Indexes for table `scholarships`
--
ALTER TABLE `scholarships`
  ADD PRIMARY KEY (`scholarship_id`),
  ADD UNIQUE KEY `scholarship_code` (`scholarship_code`);

--
-- Indexes for table `scholarship_applications`
--
ALTER TABLE `scholarship_applications`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `scholarship_id` (`scholarship_id`),
  ADD KEY `idx_application_status` (`application_status`),
  ADD KEY `idx_application_date` (`application_date`),
  ADD KEY `idx_student_scholarship` (`student_id`,`scholarship_id`),
  ADD KEY `fk_approved_by_user` (`approved_by`);

--
-- Indexes for table `scholarship_requirements`
--
ALTER TABLE `scholarship_requirements`
  ADD PRIMARY KEY (`requirement_id`),
  ADD KEY `application_id` (`application_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_student_user` (`user_id`);

--
-- Indexes for table `tuition_fees`
--
ALTER TABLE `tuition_fees`
  ADD PRIMARY KEY (`fee_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_user_type` (`user_type`),
  ADD KEY `idx_email_password` (`email`,`password`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `scholarships`
--
ALTER TABLE `scholarships`
  MODIFY `scholarship_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `scholarship_applications`
--
ALTER TABLE `scholarship_applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `scholarship_requirements`
--
ALTER TABLE `scholarship_requirements`
  MODIFY `requirement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `tuition_fees`
--
ALTER TABLE `tuition_fees`
  MODIFY `fee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`fee_id`) REFERENCES `tuition_fees` (`fee_id`) ON DELETE CASCADE;

--
-- Constraints for table `scholarship_applications`
--
ALTER TABLE `scholarship_applications`
  ADD CONSTRAINT `fk_approved_by_user` FOREIGN KEY (`approved_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `scholarship_applications_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `scholarship_applications_ibfk_2` FOREIGN KEY (`scholarship_id`) REFERENCES `scholarships` (`scholarship_id`) ON DELETE CASCADE;

--
-- Constraints for table `scholarship_requirements`
--
ALTER TABLE `scholarship_requirements`
  ADD CONSTRAINT `scholarship_requirements_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `scholarship_applications` (`application_id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `fk_student_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_students_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `tuition_fees`
--
ALTER TABLE `tuition_fees`
  ADD CONSTRAINT `tuition_fees_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
