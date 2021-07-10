-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 10, 2021 at 05:49 AM
-- Server version: 5.7.31
-- PHP Version: 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cs204_final_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `chat_rooms`
--

DROP TABLE IF EXISTS `chat_rooms`;
CREATE TABLE IF NOT EXISTS `chat_rooms` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  `room_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chat_rooms`
--

INSERT INTO `chat_rooms` (`ID`, `type`, `room_name`, `thumbnail`, `date_created`) VALUES
(6, 1, '', NULL, '2021-06-19 22:35:30'),
(10, 2, 'Quizzy', 'assets/images/chat_room_thumbnails/60ce10f430c272.06291532.png', '2021-06-19 22:44:52'),
(12, 1, '', NULL, '2021-07-10 12:39:43'),
(13, 2, 'Movie Discuss Every Weekend', 'assets/images/chat_room_thumbnails/60e9345625dc39.87675934.jpeg', '2021-07-10 12:47:02');

-- --------------------------------------------------------

--
-- Table structure for table `chat_room_participate`
--

DROP TABLE IF EXISTS `chat_room_participate`;
CREATE TABLE IF NOT EXISTS `chat_room_participate` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `room` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `fk_chat_participate_room` (`room`),
  KEY `fk_chat_participate_user` (`user`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chat_room_participate`
--

INSERT INTO `chat_room_participate` (`ID`, `room`, `user`, `date_created`) VALUES
(42, 6, 3, '2021-06-19 22:35:30'),
(43, 6, 4, '2021-06-19 22:35:30'),
(55, 10, 3, '2021-06-19 22:44:52'),
(56, 10, 4, '2021-06-19 22:45:28'),
(62, 12, 3, '2021-07-10 12:39:43'),
(63, 12, 19, '2021-07-10 12:39:43'),
(64, 13, 19, '2021-07-10 12:47:02'),
(65, 13, 4, '2021-07-10 12:47:02'),
(66, 13, 3, '2021-07-10 12:47:02');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `post` int(11) NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `fk_comment_user` (`user`),
  KEY `fk_comment_post` (`post`),
  KEY `fk_comment_parent` (`parent`)
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`ID`, `user`, `post`, `content`, `parent`, `date_created`) VALUES
(20, 3, 4, '🤩 I\'m writing a comment with emoji', NULL, '2021-06-26 21:59:52'),
(25, 4, 4, '🤩 I\'m writing a subcomment with emoji', 20, '2021-06-26 22:38:41'),
(32, 3, 4, 'Freaking hard 😣', 20, '2021-06-26 22:57:24'),
(49, 3, 4, 'OMG my new comment here 🤩', NULL, '2021-06-28 21:38:18'),
(53, 3, 4, 'Test subcomment', 32, '2021-06-28 22:57:49'),
(81, 3, 4, 'Add a new comment', NULL, '2021-07-04 16:53:04'),
(82, 3, 4, 'Replying', 49, '2021-07-04 16:53:40'),
(83, 3, 4, '<a href=\"profile.php?id=4\" class=\"mention-a-display\">Lê Trần Minh Trung</a> Hello', 25, '2021-07-04 16:53:53'),
(87, 3, 4, '<a href=\"profile.php?id=4\" class=\"mention-a-display\">Lê Trần Minh Trung</a> Aloha', 25, '2021-07-07 00:23:30'),
(89, 3, 4, 'Newly added comment', NULL, '2021-07-07 11:28:44'),
(90, 3, 4, '<a href=\"profile.php?id=4\" class=\"mention-a-display\">Lê Trần Minh Trung</a> Try reply', 25, '2021-07-07 11:42:03'),
(92, 3, 4, '<a href=\"profile.php?id=4\" class=\"mention-a-display\">Lê Trần Minh Trung</a> Hello Trung', 25, '2021-07-08 00:08:52'),
(95, 3, 13, 'Draco Malfoy\'s wand is simple but good-looking 🤗', NULL, '2021-07-10 12:22:43');

-- --------------------------------------------------------

--
-- Table structure for table `comment_reactions`
--

DROP TABLE IF EXISTS `comment_reactions`;
CREATE TABLE IF NOT EXISTS `comment_reactions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `comment` int(11) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `FK_comment_reaction_user` (`user`),
  KEY `FK_comment_reaction_comment` (`comment`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `comment_reactions`
--

INSERT INTO `comment_reactions` (`ID`, `user`, `comment`, `date_created`) VALUES
(12, 4, 20, '2021-06-26 23:50:55'),
(27, 3, 25, '2021-06-27 00:16:30'),
(31, 3, 20, '2021-07-02 23:25:11'),
(32, 3, 32, '2021-07-02 23:25:23'),
(34, 3, 81, '2021-07-07 00:28:52'),
(35, 3, 49, '2021-07-07 00:28:54'),
(39, 3, 89, '2021-07-07 11:28:47'),
(41, 3, 83, '2021-07-08 00:10:09'),
(42, 3, 90, '2021-07-08 00:10:10');

-- --------------------------------------------------------

--
-- Table structure for table `job_education_info`
--

DROP TABLE IF EXISTS `job_education_info`;
CREATE TABLE IF NOT EXISTS `job_education_info` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `user_profile` int(11) NOT NULL,
  `info` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` int(11) NOT NULL,
  `start_year` int(11) NOT NULL,
  `end_year` int(11) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `FK_info_profile` (`user_profile`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_education_info`
--

INSERT INTO `job_education_info` (`ID`, `user_profile`, `info`, `type`, `start_year`, `end_year`, `date_created`) VALUES
(1, 3, 'Study at HCMUS', 1, 2019, 2023, '2021-07-08 13:56:52'),
(2, 3, 'Study at PTNK', 1, 2016, 2019, '2021-07-08 14:00:05'),
(3, 3, 'Studied at TĐN', 1, 2012, 2016, '2021-07-08 14:04:25'),
(7, 19, 'Work at IMDB', 0, 2012, NULL, '2021-07-10 12:36:15');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
CREATE TABLE IF NOT EXISTS `posts` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `movie_id` int(11) DEFAULT NULL,
  `movie_type` tinyint(1) DEFAULT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `media` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mode` int(11) NOT NULL,
  `share_from` int(11) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `fk_post_user` (`user`),
  KEY `fk_post_share` (`share_from`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`ID`, `user`, `movie_id`, `movie_type`, `content`, `media`, `mode`, `share_from`, `date_created`) VALUES
(4, 4, 772, 1, 'Now I\'m aware that Home Alone 2: Lost in New York is the exact same plot as the first Home Alone, and not to mention how silly it seems that these folks would leave their kid behind, but come on, this movie was all in good fun. It still has me constantly laughing \'till this day, I think if you loved the first Home Alone, I\'m sure that you\'ll just naturally love this one as well. Not too many folks are just giving it a good chance. I mean, the Wet Bandants are back and they are just as funny as ever! I think this house that Kevin set up was actually in some ways a little more fun than the first because of the traps he was able to set. Not to mention the Plaza Hotel plot was so great and fun to watch.\r\n\r\nKevin and his family are heading down to Florida for Christmas this year, but when Kevin gets mixed up at the air port and gets on the wrong flight, he ends up in New York. Instead of complaining or panicking, he just plain enjoys it. He goes on the ultimate tour with his father\'s bag of money and credit cards and cons himself into the Plaza Hotel claiming he\'s there with his dad. But the Wet Bandants who have now re-named themselves as the Sticky Bandants are in New York as well and are planning on stealing all the money from a toy store that is going to be given to the children\'s hospital. But Kevin is going to make sure that they don\'t mess around with the kids and has set his uncle\'s abandoned apartment up for a fun house of traps.\r\n\r\nHome Alone 2: Lost in New York was an absolute blast. The reason I\'m giving it a ten is because I think the rating should be higher. People really need to give this movie a shot. My favorite scene is without a doubt when Marv gets hit in the face with the bricks, also, the scene where the Plaza Hotel managers are asked by Kevin\'s mom \"What kind of idiots do you have working here?!\"... the lady just smiles and says \"The finest in New York!\", it was just too funny! I love this movie and I\'m always going to recommend it for a good watch, I think you\'ll enjoy it if you give it a chance.\r\n\r\n10/10', 'assets/images/posts/60d4cb4eed07a1.64747005.jpeg', 1, NULL, '2021-06-25 01:13:34'),
(11, 3, NULL, NULL, '', NULL, 1, 4, '2021-07-05 22:50:26'),
(13, 3, 12445, 1, '😍🤩🤩It\'s truly a masterpiece <3 💓💓💓', 'assets/images/posts/60e92b4b92c63.jpeg', 2, NULL, '2021-07-06 23:40:52'),
(27, 19, 496243, 1, 'What can you expect from a movie awarded the Best Picture at Oscars? Well firstly you need to take in mind that this movie has nothing related to the scientific \"parasite\". The words \"parasite\" in the movie title is used to symbolize the way poor people earn money to live by \"parasitizing\" on rich people in the society, just like the way \"parasite\" live.\nYou should watch this movie and try to find its own meaning by yourself\nRating: 9.5/10', 'assets/images/posts/60e930f0b5658.jpeg', 2, NULL, '2021-07-10 12:32:32');

-- --------------------------------------------------------

--
-- Table structure for table `post_reactions`
--

DROP TABLE IF EXISTS `post_reactions`;
CREATE TABLE IF NOT EXISTS `post_reactions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `post` int(11) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `fk_post_reaction_user` (`user`),
  KEY `fk_post_reaction_post` (`post`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `post_reactions`
--

INSERT INTO `post_reactions` (`ID`, `user`, `post`, `date_created`) VALUES
(45, 3, 4, '2021-07-05 22:44:29'),
(55, 3, 13, '2021-07-10 12:17:26'),
(57, 3, 27, '2021-07-10 12:32:39');

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

DROP TABLE IF EXISTS `profiles`;
CREATE TABLE IF NOT EXISTS `profiles` (
  `ID` int(11) NOT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_of_birth` date NOT NULL,
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `profile_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'https://firebasestorage.googleapis.com/v0/b/cs204finalproj.appspot.com/o/istockphoto-1223671392-612x612.jpg?alt=media&token=e9312c19-c34e-4a87-9a72-552532766cde',
  `profile_cover` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'https://firebasestorage.googleapis.com/v0/b/cs204finalproj.appspot.com/o/fbdefault.png?alt=media&token=7248bbab-cc3e-420e-8a94-4c3b23a5f0f6',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `profiles`
--

INSERT INTO `profiles` (`ID`, `display_name`, `email`, `date_of_birth`, `gender`, `description`, `profile_image`, `profile_cover`) VALUES
(3, 'Nguyễn Cao Nhân', 'ncn@gmail.com', '2001-12-27', 'male', 'Fresh Corns is gonna be great, I hope <3', 'assets/images/profile/60cb9610839bf0.34903423.jpeg', 'assets/images/cover/60cceb87b7e790.09449367.jpeg'),
(4, 'Lê Trần Minh Trung', 'trungle@gmail.com', '2001-11-16', 'male', NULL, 'https://firebasestorage.googleapis.com/v0/b/cs204finalproj.appspot.com/o/istockphoto-1223671392-612x612.jpg?alt=media&token=e9312c19-c34e-4a87-9a72-552532766cde', 'https://firebasestorage.googleapis.com/v0/b/cs204finalproj.appspot.com/o/fbdefault.png?alt=media&token=7248bbab-cc3e-420e-8a94-4c3b23a5f0f6'),
(19, 'Movie Expert', 'me@gmail.com', '1993-07-15', 'male', 'I\'m a movie expert', 'assets/images/profile/60e931bf48a1b9.95607340.jpeg', 'assets/images/cover/60e931bf48c216.05008665.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `relationships`
--

DROP TABLE IF EXISTS `relationships`;
CREATE TABLE IF NOT EXISTS `relationships` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `user1` int(11) NOT NULL,
  `user2` int(11) NOT NULL,
  `approved` int(11) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `fk_relationship_user_1` (`user1`),
  KEY `fk_relationship_user_2` (`user2`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `relationships`
--

INSERT INTO `relationships` (`ID`, `user1`, `user2`, `approved`, `date_created`) VALUES
(11, 3, 4, 0, '2021-07-10 12:01:42'),
(12, 19, 3, 0, '2021-07-10 12:39:26');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`ID`, `username`, `password`, `date_created`) VALUES
(3, 'ncn2001', '$2y$10$FAOV5dHejRXN5nkpSKvFB.9WdrGIwfoiq3gioz.60nkJIQ8Ywut0u', '2021-06-18 00:02:04'),
(4, 'trungle', '$2y$10$Qn7VjvR.2Y.I1Re2ocoeouzGEWlLZ546CGGiGelIqDN7CGu8YAoCq', '2021-06-18 00:16:13'),
(19, 'movie_expert', '$2y$10$3dlyA5ZrN/xBunZEbz0ms.j1e4Z7i9SpavCx..ZEQD48udvGyxSOG', '2021-07-10 12:33:26');

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
