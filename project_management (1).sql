-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 14, 2025 at 06:24 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int NOT NULL,
  `text` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `duration` int NOT NULL,
  `progress` float DEFAULT '0',
  `parent` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `text`, `start_date`, `duration`, `progress`, `parent`) VALUES
(44, 'New task', '2025-04-07', 1, 0, 43),
(45, 'New task', '2025-04-07', 1, 0, 44),
(46, 'New task', '2025-04-07', 1, 0.785714, 45),
(47, 'FLUTTER', '2025-04-13', 60, 0.0671429, 0),
(48, 'BIKIN SPLASH SCRENN', '2025-04-13', 3, 0.752381, 47),
(49, 'AUTENTICATION PAKAI FIREBASE', '2025-04-16', 12, 0, 47),
(50, 'WEB', '2025-04-13', 15, 0.659048, 0),
(51, 'gant chart', '2025-04-13', 12, 1, 50),
(56, 'gant_chart + gambar', '2025-04-14', 5, 0.517143, 50);

-- --------------------------------------------------------

--
-- Table structure for table `tasks_json`
--

CREATE TABLE `tasks_json` (
  `id` int NOT NULL,
  `task_data` json NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tasks_json`
--

INSERT INTO `tasks_json` (`id`, `task_data`) VALUES
(1, '[{\"id\": \"67fc5f954ad70\", \"text\": \"TES 1\", \"action\": \"update\", \"parent\": 0, \"duration\": 3, \"progress\": 0, \"start_date\": \"2025-04-14\"}, {\"id\": \"67fc769bac848\", \"text\": \"New task\", \"action\": \"update\", \"parent\": 0, \"duration\": 1, \"progress\": 0, \"start_date\": \"2025-04-16\"}, {\"id\": \"67fc76f6043bf\", \"text\": \"ngasih surat ke pak erlan\", \"parent\": 0, \"duration\": 1, \"progress\": 1, \"start_date\": \"2025-04-14\"}, {\"id\": \"67fc838b2c6a8\", \"text\": \"ngasih surat ke pak wahyu spv produksi\", \"action\": \"update\", \"parent\": 0, \"duration\": 1, \"progress\": 1, \"start_date\": \"2025-04-14\"}]');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tasks_json`
--
ALTER TABLE `tasks_json`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `tasks_json`
--
ALTER TABLE `tasks_json`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
