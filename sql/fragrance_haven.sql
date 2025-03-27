-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 13, 2025 at 01:14 PM
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
-- Database: `fragrance_haven`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT 'na',
  `website` varchar(255) DEFAULT 'na',
  `image` varchar(255) DEFAULT 'na'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `description`, `website`, `image`) VALUES
(1, 'Parfums Astraux', 'Enhancing your beauty is a wonderful way to feel confident and empowered. With the simple act of wear', 'https://Parfumsastraux.com', '../uploads/brands/67d2013764095_2024-07-16.jpg'),
(3, 'The Boar Grooming Co.', 'Helping you become the best version of yourself with our grooming essential products.', 'https://shopee.ph/boargrooming', '../uploads/brands/67d200154c5bc_451640499_919618329971014_3665569765189569783_n.jpg'),
(4, 'Dot Anon', 'An artisan perfume by an anonymous creator, blending rare essences into a captivating narrative of sophistication and intrigue. Crafted with passion and mystery, it invites wearers into a world of subtle elegance and untold stories.', 'https://dotanon.com/', '../uploads/brands/67d2012da1fb0_452226180_122096267042437434_2824801443922656726_n.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `position` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `description`, `created_at`, `created_by`, `position`) VALUES
(1, 'Fragrance Discussions', 'Talk about perfumes and scents', '2025-03-12 19:23:55', 1, 0),
(2, 'Parfums Astraux', 'sample category', '2025-03-12 20:19:07', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `perfume_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fragrance_families`
--

CREATE TABLE `fragrance_families` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fragrance_families`
--

INSERT INTO `fragrance_families` (`id`, `name`) VALUES
(1, 'Floral'),
(2, 'Woody'),
(3, 'Oriental'),
(4, 'Floral'),
(5, 'Woody'),
(6, 'Citrus'),
(7, 'Fruity'),
(8, 'Oriental'),
(9, 'Soft floral'),
(10, 'Woody fragrances'),
(11, 'Fougère'),
(12, 'Fresh'),
(13, 'Green'),
(14, 'Amber'),
(15, 'Aromatics'),
(16, 'Floral fragrances'),
(17, 'Floral oriental'),
(18, 'Mossy Woods'),
(19, 'Water'),
(20, 'Ambery'),
(21, 'Chypre'),
(22, 'Citrus fragrances'),
(23, 'Dry woods'),
(24, 'Gourmand'),
(25, 'Fresh fragrance family'),
(26, 'Oriental fragrances'),
(27, 'Spices');

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `perfumes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`perfumes`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `perfumers`
--

CREATE TABLE `perfumers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `bio` text DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `nationality` varchar(50) DEFAULT NULL,
  `most_loved_perfume_id` int(11) DEFAULT NULL,
  `tagline` varchar(255) DEFAULT NULL,
  `expertise` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `perfumers`
--

INSERT INTO `perfumers` (`id`, `name`, `bio`, `birthdate`, `nationality`, `most_loved_perfume_id`, `tagline`, `expertise`, `image`) VALUES
(4, 'Edriel Basinang', NULL, NULL, NULL, NULL, 'ning ning', 'eating', '../uploads/perfumers/67d20ca3aacdd_453050075_4123711007855919_317748554791334451_n.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `perfumes`
--

CREATE TABLE `perfumes` (
  `id` int(11) NOT NULL,
  `perfume_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `accords` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `fashion_styles` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `brand_id` int(11) NOT NULL,
  `perfumer_id` int(11) DEFAULT NULL,
  `launch_year` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `perfumes`
--

INSERT INTO `perfumes` (`id`, `perfume_name`, `description`, `accords`, `notes`, `fashion_styles`, `image`, `created_at`, `brand_id`, `perfumer_id`, `launch_year`) VALUES
(12, 'Yosi Break', 'Yosi Break by Parfums Astraux is a warm and inviting fragrance that evokes the comforting atmosphere of a relaxing pause. The rich, smoky essence of tobacco intertwines with sweet oud, creating a deep and luxurious foundation. Earthy moss adds a touch of natural elegance, while warm amber envelops the scent in a cozy glow. This harmonious blend captures the essence of moments spent unwinding, making it a perfect companion for leisurely breaks and intimate gatherings. Yosi Break was launched in 2023. The nose behind this fragrance is Edriel Basinang', 'amber, tobacco, oud, mossy, sweet, earthy', 'Amber, Tobacco, Oud, Moss', 'gengeng', '67d203112a62c.jpg', '2025-03-12 21:56:33', 1, 4, 2023);

-- --------------------------------------------------------

--
-- Table structure for table `perfume_families`
--

CREATE TABLE `perfume_families` (
  `id` int(11) NOT NULL,
  `perfume_id` int(11) NOT NULL,
  `family_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `perfume_families`
--

INSERT INTO `perfume_families` (`id`, `perfume_id`, `family_id`) VALUES
(23, 12, 14);

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `post_id` int(11) NOT NULL,
  `thread_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`post_id`, `thread_id`, `user_id`, `content`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'Citrus-based scents like Acqua di Gio work well in summer!', '2025-03-12 19:23:55', '2025-03-12 19:23:55'),
(2, 1, 1, 'sample', '2025-03-12 19:31:24', '2025-03-12 19:31:24'),
(3, 1, 1, 'sample', '2025-03-12 19:31:32', '2025-03-12 19:31:32'),
(4, 1, 1, 'sample', '2025-03-12 19:31:35', '2025-03-12 19:31:35'),
(5, 1, 1, 'test', '2025-03-12 19:32:32', '2025-03-12 19:32:32'),
(6, 1, 1, 'test', '2025-03-12 19:33:43', '2025-03-12 19:33:43'),
(7, 1, 1, 'test123', '2025-03-12 19:34:22', '2025-03-12 19:34:22'),
(8, 1, 1, 'test', '2025-03-12 19:35:19', '2025-03-12 19:35:19'),
(9, 1, 1, '12345678654', '2025-03-12 19:35:26', '2025-03-12 19:35:26'),
(10, 2, 1, 'test reply 102', '2025-03-12 19:58:40', '2025-03-12 19:58:40');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `perfume_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `review_text` text DEFAULT NULL,
  `scent_impression` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `threads`
--

CREATE TABLE `threads` (
  `thread_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `view_count` int(11) DEFAULT 0,
  `status` enum('open','closed','sticky') DEFAULT 'open',
  `locked_by` int(11) DEFAULT NULL,
  `locked_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `threads`
--

INSERT INTO `threads` (`thread_id`, `category_id`, `user_id`, `title`, `content`, `created_at`, `updated_at`, `view_count`, `status`, `locked_by`, `locked_at`) VALUES
(1, 1, 1, 'Best summer fragrances?', 'What are your recommendations for summer scents?', '2025-03-12 19:23:55', '2025-03-13 12:09:01', 23, 'open', NULL, NULL),
(2, 1, 1, 'sample thread 101', 'sample thread 101', '2025-03-12 19:50:37', '2025-03-13 12:09:29', 10, 'open', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('user','moderator','admin') DEFAULT 'user',
  `profile_picture` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `profile_picture`, `bio`, `created_at`) VALUES
(1, 'harmony_red', 'basinangedriel3@gmail.com', '$2y$10$SiwKQfhB.amyufpH3hJaJ.ldlhJgCGgBeXOIr6grfSFRGaIZdICd6', 'admin', 'uploads/Screenshot 2025-02-25 154708.png', '', '2025-03-11 09:03:09'),
(2, 'rflubao@gmail.com', 'rflubao@gmail.com', '$2y$10$fMhqQXMu1oKI2Db38ITZkuM7Fvedm.0NyPWDnWXL94zoQYldgQaU.', 'moderator', '../uploads/user/67cffcb84295d_Screenshot 2025-02-25 154543.png', 'rflubao@gmail.comrflubao@gmail.com', '2025-03-11 09:04:56'),
(5, 'basinangedriel3@gmail.com12', 'basinangedriel3@gmail.com12', '$2y$10$8X4G/hGi.IVhnhHjdwVTUegFca9vttmTl.qv1ZWLMI/tCwQaCtLp2', 'moderator', '../uploads/user/67d01dc648520_2024-07-16.jpg', 'basinangedriel3@gmail.com11basinangedriel3@gmail.com11basinangedriel3@gmail.com12', '2025-03-11 11:25:58'),
(6, 'basinangedriel3@gmail.com11', 'basinangedriel3@gmail.com11', '$2y$10$zMeUaMqvE6V.ABvIh5a2XO28zTnK141hAGrqb2li2Xl4DRpPcN8Ve', 'user', 'uploads/453050075_4123711007855919_317748554791334451_n.jpg', NULL, '2025-03-13 12:10:04');

-- --------------------------------------------------------

--
-- Table structure for table `user_follow`
--

CREATE TABLE `user_follow` (
  `id` int(11) NOT NULL,
  `follower_id` int(11) NOT NULL,
  `following_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `vote_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `value` tinyint(4) DEFAULT NULL CHECK (`value` in (-1,1)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_activity_log_user` (`user_id`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_favorites_user` (`user_id`),
  ADD KEY `fk_favorites_perfume` (`perfume_id`);

--
-- Indexes for table `fragrance_families`
--
ALTER TABLE `fragrance_families`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `perfumers`
--
ALTER TABLE `perfumers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `perfumes`
--
ALTER TABLE `perfumes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_perfumes_brand` (`brand_id`),
  ADD KEY `fk_perfumes_perfumer` (`perfumer_id`);

--
-- Indexes for table `perfume_families`
--
ALTER TABLE `perfume_families`
  ADD PRIMARY KEY (`id`),
  ADD KEY `perfume_id` (`perfume_id`),
  ADD KEY `family_id` (`family_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `idx_posts_thread` (`thread_id`),
  ADD KEY `idx_posts_user` (`user_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reviews_perfume` (`perfume_id`),
  ADD KEY `fk_reviews_user` (`user_id`);

--
-- Indexes for table `threads`
--
ALTER TABLE `threads`
  ADD PRIMARY KEY (`thread_id`),
  ADD KEY `idx_threads_category` (`category_id`),
  ADD KEY `idx_threads_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_follow`
--
ALTER TABLE `user_follow`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_follow_follower` (`follower_id`),
  ADD KEY `fk_user_follow_following` (`following_id`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`vote_id`),
  ADD UNIQUE KEY `unique_vote` (`user_id`,`post_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `idx_votes_user_post` (`user_id`,`post_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fragrance_families`
--
ALTER TABLE `fragrance_families`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `perfumers`
--
ALTER TABLE `perfumers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `perfumes`
--
ALTER TABLE `perfumes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `perfume_families`
--
ALTER TABLE `perfume_families`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `threads`
--
ALTER TABLE `threads`
  MODIFY `thread_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_follow`
--
ALTER TABLE `user_follow`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `vote_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `fk_activity_log_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `fk_favorites_perfume` FOREIGN KEY (`perfume_id`) REFERENCES `perfumes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_favorites_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `perfumes`
--
ALTER TABLE `perfumes`
  ADD CONSTRAINT `fk_perfumes_brand` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_perfumes_perfumer` FOREIGN KEY (`perfumer_id`) REFERENCES `perfumers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `perfume_families`
--
ALTER TABLE `perfume_families`
  ADD CONSTRAINT `perfume_families_ibfk_1` FOREIGN KEY (`perfume_id`) REFERENCES `perfumes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `perfume_families_ibfk_2` FOREIGN KEY (`family_id`) REFERENCES `fragrance_families` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`thread_id`) REFERENCES `threads` (`thread_id`),
  ADD CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_perfume` FOREIGN KEY (`perfume_id`) REFERENCES `perfumes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `threads`
--
ALTER TABLE `threads`
  ADD CONSTRAINT `threads_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`),
  ADD CONSTRAINT `threads_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `user_follow`
--
ALTER TABLE `user_follow`
  ADD CONSTRAINT `fk_user_follow_follower` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_follow_following` FOREIGN KEY (`following_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `votes_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
