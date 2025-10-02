-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 02, 2025 at 08:41 AM
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
(8, 1, 1, 500.00, 'gcash', 'completed', NULL, 'src_x1so5jHL7zLyDxzznsrYZ8Aj', '2025-10-01 17:24:18'),
(9, 1, 1, 1000.00, 'gcash', 'completed', NULL, 'src_hDq8rBeWeJHbRunBu2KnNvZP', '2025-10-01 17:46:10'),
(10, 1, 1, 525.00, 'maya', 'completed', NULL, 'src_mb1fZJ5ECX9A8eWGxM7DnftE', '2025-10-01 18:05:34'),
(11, 1, 1, 500.00, 'gcash', 'completed', NULL, 'src_NVCpQ7cva8nHhTjHjSwzwXLu', '2025-10-02 01:59:31'),
(12, 1, 1, 200.00, 'gcash', 'pending', NULL, 'src_Y2oLJNWd47AV3YApFjfZeRfd', '2025-10-02 02:37:08'),
(13, 1, 1, 200.00, 'gcash', 'pending', NULL, 'src_CMTKhWvd3keKMSs4xcap5RAT', '2025-10-02 02:37:23'),
(14, 1, 1, 200.00, 'gcash', 'pending', NULL, 'src_btAC7antKVSa91mwTGpsiYQq', '2025-10-02 02:48:08'),
(15, 1, 1, 200.00, 'gcash', 'completed', NULL, 'src_iJr4eGbaBjcA62rajedMWTut', '2025-10-02 03:03:23'),
(16, 1, 1, 200.00, 'gcash', 'completed', NULL, 'src_b3GsjB38MpamjevBXyn2qV1o', '2025-10-02 03:15:19'),
(17, 1, 1, 100.00, 'gcash', 'completed', NULL, 'src_D58AX199ku4bHKBsAtm77wHQ', '2025-10-02 03:30:59'),
(18, 1, 1, 100.00, 'gcash', 'completed', NULL, 'src_7uJrnU7FW25A177n2CjFG5Ey', '2025-10-02 03:37:31'),
(19, 1, 1, 50.00, 'gcash', 'completed', NULL, 'src_TfDAfp2WZBuTYAqRa8gvEbpS', '2025-10-02 03:42:14'),
(20, 1, 1, 50.00, 'gcash', 'completed', NULL, 'src_t9h7BoV85SYrdQctsTG9hotW', '2025-10-02 03:50:14');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `program` varchar(255) NOT NULL,
  `year_level` varchar(50) NOT NULL,
  `student_status` enum('regular','irregular') NOT NULL DEFAULT 'regular',
  `college` varchar(255) NOT NULL,
  `campus` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `name`, `email`, `program`, `year_level`, `student_status`, `college`, `campus`, `created_at`) VALUES
(1, 'Joshua Garcia', 'suruizandrie@gmail.com', 'BS Information Technology', '3rd Year', 'regular', 'College of Information Technology', 'Quezon City Campus', '2025-10-01 15:41:36');

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
(1, 1, '1st Semester', 'AY 2025-2026', 9000.00, 1, 5175.00, 850.00, 1000.00, 6025.00, 5025.00, 1000.00, '2025-10-01 15:41:36');

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
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `tuition_fees`
--
ALTER TABLE `tuition_fees`
  ADD PRIMARY KEY (`fee_id`),
  ADD KEY `student_id` (`student_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tuition_fees`
--
ALTER TABLE `tuition_fees`
  MODIFY `fee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- Constraints for table `tuition_fees`
--
ALTER TABLE `tuition_fees`
  ADD CONSTRAINT `tuition_fees_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
