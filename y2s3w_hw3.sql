-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 03, 2021 at 09:54 AM
-- Server version: 10.4.18-MariaDB
-- PHP Version: 8.0.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `y2s3w_hw3`
--

-- --------------------------------------------------------

--
-- Table structure for table `chat_rooms`
--

CREATE TABLE `chat_rooms` (
  `ID` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `room_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_room_participate`
--

CREATE TABLE `chat_room_participate` (
  `ID` int(11) NOT NULL,
  `room` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `ID` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `post` int(11) NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comment_reactions`
--

CREATE TABLE `comment_reactions` (
  `ID` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `comment` int(11) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_education_info`
--

CREATE TABLE `job_education_info` (
  `ID` int(11) NOT NULL,
  `user_profile` int(11) NOT NULL,
  `info` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_year` int(11) NOT NULL,
  `end_year` int(11) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `ID` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `movie_type` tinyint(1) NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `media` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mode` int(11) NOT NULL,
  `share_from` int(11) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`ID`, `user`, `movie_id`, `movie_type`, `content`, `media`, `mode`, `share_from`, `date_created`) VALUES
(13, 1, 84958, 0, 'Good', NULL, 1, NULL, '2021-06-28 14:19:12'),
(14, 1, 85271, 0, 'đầu voi đuôi chuột', NULL, 1, NULL, '2021-06-28 14:20:26'),
(16, 1, 84958, 0, 'okay', NULL, 1, NULL, '2021-06-28 15:27:43'),
(19, 1, 299534, 1, 'almsot 3 billions', NULL, 1, NULL, '2021-06-28 15:33:44'),
(22, 1, 475557, 1, 'You cut out a piece of me & Now I bleed internally', NULL, 1, NULL, '2021-06-29 13:10:56'),
(23, 1, 49521, 1, 'Sup', NULL, 1, NULL, '2021-06-29 13:12:24'),
(24, 1, 467909, 1, 'Meh', NULL, 1, NULL, '2021-06-29 13:51:54'),
(25, 1, 1399, 0, 'Haven\'t even watched it', NULL, 1, NULL, '2021-06-29 13:55:29'),
(26, 1, 844198, 1, 'she is the moment', NULL, 1, NULL, '2021-06-30 13:22:48'),
(27, 1, 844198, 1, 'test', 'imgs/posts/60dc116781d08.jpeg', 1, NULL, '2021-06-30 13:38:31'),
(28, 1, 495764, 1, 'Cheap', 'imgs/posts/60dc29e1be602.jpeg', 1, NULL, '2021-06-30 15:22:57'),
(31, 1, 833829, 1, 'I\'m on the next, level yuh', 'imgs/posts/60dc33e03f502.jpeg', 1, NULL, '2021-06-30 16:05:36'),
(34, 1, 216015, 1, 'This movie rendition of Beyonce\'s Crazy In Love is a certified BOP', 'imgs/posts/60dc7cc893dd0.jpeg', 1, NULL, '2021-06-30 21:16:40');

-- --------------------------------------------------------

--
-- Table structure for table `post_reactions`
--

CREATE TABLE `post_reactions` (
  `ID` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `post` int(11) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `post_reactions`
--

INSERT INTO `post_reactions` (`ID`, `user`, `post`, `date_created`) VALUES
(40, 1, 27, '2021-07-02 20:29:55'),
(42, 1, 34, '2021-07-03 12:55:27'),
(43, 1, 31, '2021-07-03 13:41:07');

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE `profiles` (
  `ID` int(11) NOT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_of_birth` datetime NOT NULL,
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'https://firebasestorage.googleapis.com/v0/b/cs204finalproj.appspot.com/o/istockphoto-1223671392-612x612.jpg?alt=media&token=e9312c19-c34e-4a87-9a72-552532766cde',
  `profile_cover` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'https://firebasestorage.googleapis.com/v0/b/cs204finalproj.appspot.com/o/fbdefault.png?alt=media&token=7248bbab-cc3e-420e-8a94-4c3b23a5f0f6'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `profiles`
--

INSERT INTO `profiles` (`ID`, `display_name`, `email`, `date_of_birth`, `gender`, `description`, `profile_image`, `profile_cover`) VALUES
(1, 'Dua Lipa', '', '2021-07-01 10:28:38', '', NULL, 'https://firebasestorage.googleapis.com/v0/b/cs204finalproj.appspot.com/o/istockphoto-1223671392-612x612.jpg?alt=media&token=e9312c19-c34e-4a87-9a72-552532766cde', 'https://firebasestorage.googleapis.com/v0/b/cs204finalproj.appspot.com/o/fbdefault.png?alt=media&token=7248bbab-cc3e-420e-8a94-4c3b23a5f0f6');

-- --------------------------------------------------------

--
-- Table structure for table `relationships`
--

CREATE TABLE `relationships` (
  `ID` int(11) NOT NULL,
  `user1` int(11) NOT NULL,
  `user2` int(11) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`ID`, `username`, `password`, `date_created`) VALUES
(1, 'admin', 'admin', '2021-06-28 13:47:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chat_rooms`
--
ALTER TABLE `chat_rooms`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `chat_room_participate`
--
ALTER TABLE `chat_room_participate`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_chat_participate_room` (`room`),
  ADD KEY `fk_chat_participate_user` (`user`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_comment_user` (`user`),
  ADD KEY `fk_comment_post` (`post`),
  ADD KEY `fk_comment_parent` (`parent`);

--
-- Indexes for table `comment_reactions`
--
ALTER TABLE `comment_reactions`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `FK_comment_reaction_user` (`user`),
  ADD KEY `FK_comment_reaction_comment` (`comment`);

--
-- Indexes for table `job_education_info`
--
ALTER TABLE `job_education_info`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `FK_info_profile` (`user_profile`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_post_user` (`user`),
  ADD KEY `fk_post_share` (`share_from`);

--
-- Indexes for table `post_reactions`
--
ALTER TABLE `post_reactions`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_post_reaction_user` (`user`),
  ADD KEY `fk_post_reaction_post` (`post`);

--
-- Indexes for table `profiles`
--
ALTER TABLE `profiles`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `relationships`
--
ALTER TABLE `relationships`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_relationship_user_1` (`user1`),
  ADD KEY `fk_relationship_user_2` (`user2`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chat_rooms`
--
ALTER TABLE `chat_rooms`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_room_participate`
--
ALTER TABLE `chat_room_participate`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comment_reactions`
--
ALTER TABLE `comment_reactions`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_education_info`
--
ALTER TABLE `job_education_info`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `post_reactions`
--
ALTER TABLE `post_reactions`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `relationships`
--
ALTER TABLE `relationships`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chat_room_participate`
--
ALTER TABLE `chat_room_participate`
  ADD CONSTRAINT `fk_chat_participate_room` FOREIGN KEY (`room`) REFERENCES `chat_rooms` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_chat_participate_user` FOREIGN KEY (`user`) REFERENCES `users` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `fk_comment_parent` FOREIGN KEY (`parent`) REFERENCES `comments` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_comment_post` FOREIGN KEY (`post`) REFERENCES `posts` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_comment_user` FOREIGN KEY (`user`) REFERENCES `users` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `comment_reactions`
--
ALTER TABLE `comment_reactions`
  ADD CONSTRAINT `FK_comment_reaction_comment` FOREIGN KEY (`comment`) REFERENCES `comments` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_comment_reaction_user` FOREIGN KEY (`user`) REFERENCES `users` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `job_education_info`
--
ALTER TABLE `job_education_info`
  ADD CONSTRAINT `FK_info_profile` FOREIGN KEY (`user_profile`) REFERENCES `profiles` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `fk_post_share` FOREIGN KEY (`share_from`) REFERENCES `posts` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_post_user` FOREIGN KEY (`user`) REFERENCES `users` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `post_reactions`
--
ALTER TABLE `post_reactions`
  ADD CONSTRAINT `fk_post_reaction_post` FOREIGN KEY (`post`) REFERENCES `posts` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_post_reaction_user` FOREIGN KEY (`user`) REFERENCES `users` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `profiles`
--
ALTER TABLE `profiles`
  ADD CONSTRAINT `fk_profile_user` FOREIGN KEY (`ID`) REFERENCES `users` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `relationships`
--
ALTER TABLE `relationships`
  ADD CONSTRAINT `fk_relationship_user_1` FOREIGN KEY (`user1`) REFERENCES `users` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_relationship_user_2` FOREIGN KEY (`user2`) REFERENCES `users` (`ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
