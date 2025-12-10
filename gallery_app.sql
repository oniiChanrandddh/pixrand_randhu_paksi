-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 10, 2025 at 05:53 PM
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
-- Database: `gallery_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `albums`
--

CREATE TABLE `albums` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `visibility` enum('public','private') NOT NULL DEFAULT 'private',
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `albums`
--

INSERT INTO `albums` (`id`, `user_id`, `visibility`, `title`, `description`, `created_at`) VALUES
(6, 1, 'public', 'rand_collection_1', 'lorem ipsum', '2025-12-01 22:59:58'),
(8, 1, 'public', 'rand_collection', 'It\'s my life', '2025-12-02 13:12:58'),
(9, 6, 'private', 'priv', '123', '2025-12-04 15:23:31');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `photo_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `photo_id`, `user_id`, `comment`, `created_at`) VALUES
(3, 6, 6, 'TEST', '2025-12-02 00:17:52'),
(7, 16, 17, 'do', '2025-12-02 13:16:39'),
(11, 16, 6, 'keren sekali', '2025-12-06 16:38:33'),
(12, 24, 19, 'aaowkowko', '2025-12-09 13:43:08');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `photo_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`id`, `photo_id`, `user_id`, `created_at`) VALUES
(2, 6, 6, '2025-12-02 00:00:38'),
(3, 9, 6, '2025-12-02 07:52:04'),
(4, 16, 17, '2025-12-02 13:15:17'),
(11, 16, 6, '2025-12-06 16:38:25'),
(12, 24, 6, '2025-12-06 18:20:59'),
(13, 25, 6, '2025-12-07 01:38:42'),
(14, 14, 6, '2025-12-07 01:38:52'),
(15, 27, 19, '2025-12-09 13:42:04'),
(16, 24, 19, '2025-12-09 13:42:59');

-- --------------------------------------------------------

--
-- Table structure for table `photos`
--

CREATE TABLE `photos` (
  `id` int(11) NOT NULL,
  `album_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `caption` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `photos`
--

INSERT INTO `photos` (`id`, `album_id`, `user_id`, `file_path`, `title`, `caption`, `created_at`) VALUES
(6, 6, 1, '692dbbe4132f6_Screenshot 2025-04-28 142843.png', 'SleepCoffeeeeee', 'Dolor Sit Amet', '2025-12-01 23:01:40'),
(9, 6, 1, '1764606294_692dc1565abc1.png', 'Your Name', 'Kimi No na Wa', '2025-12-01 23:24:54'),
(10, 6, 6, '1764610603_692dd22b1c4b5.png', 'A', 'B', '2025-12-02 00:36:43'),
(14, 6, 6, '1764611682_692dd66236004.png', 'a', 'a', '2025-12-02 00:54:42'),
(16, 8, 1, '1764656034_692e83a2e3c0c.png', '1', '1', '2025-12-02 13:13:54'),
(24, 8, 6, '1764853119_6931857fa4c15.png', 'INI PUNYA RANDHU', 'ASWD', '2025-12-04 19:58:39'),
(25, 9, 6, '1765046278_69347806de585.png', 'test', 'a', '2025-12-07 01:37:58'),
(27, 8, 6, '1765113254_69357da6c433e.png', 'qw', 'keren', '2025-12-07 20:14:14'),
(28, 9, 6, '1765113864_69358008b8396.png', 'q', 'rb', '2025-12-07 20:24:24');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$0iuDJv35VZoFOvxTSjjB0.ihk34bkjNxudjYeHofkjIiN5SbuVN0G', 'Administrator', 'admin', '2025-11-27 22:26:41'),
(6, 'randh', '$2y$10$b6ptZLVPmi2kftfgDfjBeOPAjwwf7B7rqDS1u43aiD72.CRP5OWJC', 'randhu paksi', 'user', '2025-12-01 20:16:48'),
(17, 'azw', '$2y$10$9z7L4mvoUVLs41iNQmkOjuL2n2htkqZyzJzhKcv59mCCaXOoxxUNq', 'Arista_Wilantara', 'user', '2025-12-02 13:14:45'),
(19, 'randh_', '$2y$10$CTXEPJ7kQ5s4hbhS2DnEGuhMajZgpL7M9YN10jNh4FrcP6JtItSz6', 'randhu', 'user', '2025-12-09 13:41:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `albums`
--
ALTER TABLE `albums`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `photo_id` (`photo_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `photo_id` (`photo_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `photos`
--
ALTER TABLE `photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `album_id` (`album_id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `albums`
--
ALTER TABLE `albums`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `photos`
--
ALTER TABLE `photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `albums`
--
ALTER TABLE `albums`
  ADD CONSTRAINT `albums_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`photo_id`) REFERENCES `photos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`photo_id`) REFERENCES `photos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `photos`
--
ALTER TABLE `photos`
  ADD CONSTRAINT `photos_ibfk_1` FOREIGN KEY (`album_id`) REFERENCES `albums` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `photos_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
