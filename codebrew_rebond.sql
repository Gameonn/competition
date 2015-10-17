-- phpMyAdmin SQL Dump
-- version 4.3.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 14, 2015 at 07:46 AM
-- Server version: 5.5.42-37.1
-- PHP Version: 5.4.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `codebrew_rebond`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `category_name`, `created_on`) VALUES
(1, 'Music', '2015-07-14 05:55:36'),
(2, 'Fashion', '2015-07-14 05:55:36'),
(3, 'Sports', '2015-07-14 05:55:36'),
(4, 'Pets', '2015-07-14 05:55:36'),
(5, 'Activities', '2015-07-14 05:55:36'),
(6, 'Food', '2015-07-14 05:55:36'),
(7, 'Politics', '2015-07-14 07:54:58'),
(8, 'TV', '2015-07-14 07:54:58');

-- --------------------------------------------------------

--
-- Table structure for table `challenge`
--

CREATE TABLE IF NOT EXISTS `challenge` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer` varchar(60) NOT NULL,
  `answer_time` varchar(3) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `question_id`, `comment`, `created_on`) VALUES
(1, 1, 1, 'I know it.....', '2015-07-14 09:28:52');

-- --------------------------------------------------------

--
-- Table structure for table `follow`
--

CREATE TABLE IF NOT EXISTS `follow` (
  `id` int(11) NOT NULL,
  `user_id1` int(11) NOT NULL,
  `user_id2` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE IF NOT EXISTS `likes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(11) NOT NULL,
  `user_id_sender` int(11) NOT NULL,
  `user_id_reciever` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE IF NOT EXISTS `notification` (
  `id` int(11) NOT NULL,
  `user_id_sender` int(11) NOT NULL,
  `user_id_reciever` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `type` enum('comment','like','question','follow') NOT NULL,
  `is_read` tinyint(1) NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`id`, `user_id_sender`, `user_id_reciever`, `question_id`, `title`, `type`, `is_read`, `created_on`) VALUES
(1, 1, 8, 1, 'Ankit commented on your question', 'comment', 0, '2015-07-14 09:28:52');

-- --------------------------------------------------------

--
-- Table structure for table `question`
--

CREATE TABLE IF NOT EXISTS `question` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `answer` varchar(50) NOT NULL,
  `image` varchar(60) NOT NULL,
  `video` varchar(60) NOT NULL,
  `type` enum('video','image') NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `question`
--

INSERT INTO `question` (`id`, `user_id`, `category_id`, `title`, `answer`, `image`, `video`, `type`, `created_on`) VALUES
(1, 8, 3, 'What is my childhood best FIFA player', 'Lionel Messi', 'Img_55a4bc1803ac0115.jpg', '', 'image', '2015-07-14 07:36:56'),
(2, 8, 5, 'What is my childhood best friend''s name?', 'Robert', 'Img_55a4ad758d0c2564.jpg', '', 'image', '2015-07-14 06:35:05'),
(3, 8, 5, 'How old was sherry in this picture?', '9', 'Img_55a4be5101dff491.jpg', '', 'image', '2015-07-14 07:46:25');

-- --------------------------------------------------------

--
-- Table structure for table `scoring`
--

CREATE TABLE IF NOT EXISTS `scoring` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL,
  `apn_id` int(11) NOT NULL,
  `fbid` bigint(20) NOT NULL,
  `twitter_id` varchar(150) NOT NULL,
  `instagram_id` varchar(150) NOT NULL,
  `google_id` varchar(150) NOT NULL,
  `pinterest_id` varchar(150) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(80) NOT NULL,
  `password` varchar(150) NOT NULL,
  `bio` text NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `phone` bigint(13) NOT NULL,
  `profile_pic` varchar(60) NOT NULL,
  `token` varchar(150) NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `apn_id`, `fbid`, `twitter_id`, `instagram_id`, `google_id`, `pinterest_id`, `name`, `email`, `password`, `bio`, `gender`, `phone`, `profile_pic`, `token`, `created_on`) VALUES
(1, 0, 0, '', '', '', '', 'Ankit', 'ankit@gmail.com', '447d2c8dc25efbc493788a322f1a00e7', 'Compellingly synthesize multidisciplinary niche markets through backend functionalities.', 'male', 0, 'Img_55a3948d7aa65923.jpg', 'e1030616d867af4fe8a94d929f378872', '2015-07-14 11:38:38'),
(2, 0, 1005548785423232, 'iert9534504875', '1508021713.81822e9.8d060d0d9d964dfdb7f44c6379848c99', '8450940UIRWEs345', 'EJHE49830OEHX', 'Macbear', 'a@a.com', 'c4ca4238a0b923820dcc509a6f75849b', 'Compellingly synthesize multidisciplinary niche markets through backend functionalities.', 'male', 0, 'Img_55a3975c3d9a8594.jpg', '5560884b77b13c50ef17da3959308f63', '2015-07-13 13:11:32'),
(3, 0, 7834643271291903, '', '', '', '', 'ROWPQ', '', '', 'Compellingly synthesize multidisciplinary niche markets through backend functionalities.', 'male', 0, 'Img_55a39be6bdd1d270.jpg', '', '2015-07-14 05:09:37'),
(4, 0, 0, 'qwer323534654', '', '', '', 'Lehman', '', '', '', 'male', 0, 'no_image.png', '0f1d8124e8693c81282c49972db67c66', '2015-07-13 13:11:43'),
(5, 0, 0, '', '2028525090.81822e9.4b9289ebbab64cebb1efa38d81e2a5ee', '', '', 'Jack', '', '', '', 'male', 0, 'no_image.png', 'c9386217b0cfc71a14412c615370b4a0', '2015-07-13 12:57:02'),
(6, 0, 0, '', '', '5484IRUI934845', '', 'Jack', '', '', '', 'male', 0, 'no_image.png', '0bed13e75946cd5ebb02d76c34e161cb', '2015-07-13 13:09:52'),
(7, 0, 0, '', '', '', 'Pfhr49830OEHX', 'Creature', '', '', '', 'male', 0, 'no_image.png', '75c2c4cc2913ed3d882a65747f7e82f4', '2015-07-13 13:11:01'),
(8, 0, 0, '', '', '', '', 'Conver', 'abc@a.com', 'cdc3d28efdf9e6074cc89f95752e20c9', 'Continually integrate covalent schemas after worldwide initiatives.', 'female', 9876543210, 'Img_55a4b11c27226737.jpg', '1d08f485f6e35148d6fa77c6322ccaf4', '2015-07-14 07:23:01'),
(10, 0, 0, '', '', '', '', 'Cremo', 'lamp_save@gmail.com', 'c4ca4238a0b923820dcc509a6f75849b', 'Compellingly synthesize multidisciplinary niche markets through backend functionalities.', '', 9876543215, 'no_image.png', '3442e8a206bc1c093007a4f5223035d7', '2015-07-14 07:26:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `challenge`
--
ALTER TABLE `challenge`
  ADD PRIMARY KEY (`id`), ADD KEY `user_id` (`user_id`), ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`), ADD KEY `user_id` (`user_id`), ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `follow`
--
ALTER TABLE `follow`
  ADD PRIMARY KEY (`id`), ADD KEY `user_id1` (`user_id1`), ADD KEY `user_id2` (`user_id2`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`), ADD KEY `user_id` (`user_id`), ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`), ADD KEY `user_id_sender` (`user_id_sender`), ADD KEY `user_id_reciever` (`user_id_reciever`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`), ADD KEY `user_id_sender` (`user_id_sender`), ADD KEY `user_id_reciever` (`user_id_reciever`);

--
-- Indexes for table `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`id`), ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `scoring`
--
ALTER TABLE `scoring`
  ADD PRIMARY KEY (`id`), ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `challenge`
--
ALTER TABLE `challenge`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `follow`
--
ALTER TABLE `follow`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `question`
--
ALTER TABLE `question`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `scoring`
--
ALTER TABLE `scoring`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `challenge`
--
ALTER TABLE `challenge`
ADD CONSTRAINT `challenge_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `challenge_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `follow`
--
ALTER TABLE `follow`
ADD CONSTRAINT `follow_ibfk_2` FOREIGN KEY (`user_id2`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `follow_ibfk_1` FOREIGN KEY (`user_id1`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`user_id_reciever`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`user_id_sender`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
ADD CONSTRAINT `notification_ibfk_2` FOREIGN KEY (`user_id_reciever`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`user_id_sender`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `question`
--
ALTER TABLE `question`
ADD CONSTRAINT `question_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `scoring`
--
ALTER TABLE `scoring`
ADD CONSTRAINT `scoring_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
