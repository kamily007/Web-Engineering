-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 06, 2025 at 03:43 PM
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
-- Database: `marriage_biodata_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `biodata`
--

CREATE TABLE `biodata` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `date_of_birth` date NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `height` varchar(50) DEFAULT NULL,
  `religion` enum('Islam','Hinduism','Christianity','Other') NOT NULL,
  `nationality` varchar(100) DEFAULT 'Bangladeshi',
  `marital_status` enum('Never Married','Married') NOT NULL,
  `education` text DEFAULT NULL,
  `present_address` text DEFAULT NULL,
  `permanent_address` text DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `profile_photo` varchar(500) DEFAULT NULL,
  `father_name` varchar(255) DEFAULT NULL,
  `father_occupation` varchar(255) DEFAULT NULL,
  `mother_name` varchar(255) DEFAULT NULL,
  `mother_occupation` varchar(255) DEFAULT NULL,
  `siblings` text DEFAULT NULL,
  `family_type` enum('Joint','Nuclear') DEFAULT NULL,
  `hobbies` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `biodata`
--

INSERT INTO `biodata` (`id`, `full_name`, `date_of_birth`, `gender`, `height`, `religion`, `nationality`, `marital_status`, `education`, `present_address`, `permanent_address`, `contact_number`, `email`, `profile_photo`, `father_name`, `father_occupation`, `mother_name`, `mother_occupation`, `siblings`, `family_type`, `hobbies`, `created_at`, `updated_at`) VALUES
(1, 'Sarah Ahmed', '1995-03-15', 'Female', '5\'4\"', 'Islam', 'Bangladeshi', 'Never Married', 'Masters in Computer Science', 'Dhaka, Bangladesh', 'Chittagong, Bangladesh', '+8801712345678', 'sarah@example.com', NULL, 'Ahmed Ali', 'Business', 'Fatima Ahmed', 'Teacher', '1 brother, 1 sister', 'Nuclear', 'Reading, Cooking, Travelling', '2025-08-06 13:39:20', '2025-08-06 13:39:20'),
(2, 'Mohammad Rahman', '1992-08-22', 'Male', '5\'8\"', 'Islam', 'Bangladeshi', 'Never Married', 'Bachelor in Engineering', 'Sylhet, Bangladesh', 'Sylhet, Bangladesh', '+8801987654321', 'mohammad@example.com', NULL, 'Abdul Rahman', 'Engineer', 'Rashida Rahman', 'Housewife', '2 sisters', 'Joint', 'Sports, Music, Movies', '2025-08-06 13:39:20', '2025-08-06 13:39:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `biodata`
--
ALTER TABLE `biodata`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `biodata`
--
ALTER TABLE `biodata`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
