-- phpMyAdmin SQL Dump
-- version 5.2.1deb1+jammy2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 25, 2024 at 02:16 PM
-- Server version: 8.0.36-0ubuntu0.22.04.1
-- PHP Version: 8.1.2-1ubuntu2.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cei326omada5`
--

-- --------------------------------------------------------

--
-- Table structure for table `bills`
--

CREATE TABLE `bills` (
  `bill_id` int NOT NULL,
  `property_id` int DEFAULT NULL,
  `bill_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bill` blob,
  `receipt` blob,
  `status` enum('p','f') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'p',
  `comment` text COLLATE utf8mb4_general_ci,
  `CREATED` datetime DEFAULT CURRENT_TIMESTAMP,
  `CREATED_BY` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `UPDATED` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `UPDATED_BY` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `building_id` int DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `expire_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bills`
--

INSERT INTO `bills` (`bill_id`, `property_id`, `bill_title`, `bill`, `receipt`, `status`, `comment`, `CREATED`, `CREATED_BY`, `UPDATED`, `UPDATED_BY`, `building_id`, `cost`, `expire_date`) VALUES
(1, 1, 'test', NULL, NULL, 'p', 'test', '2024-04-21 20:04:18', NULL, NULL, NULL, 1, 3000.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `building`
--

CREATE TABLE `building` (
  `building_id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `postal_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `county` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `num_floors` int DEFAULT NULL,
  `num_properties` int DEFAULT NULL,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `CREATED` datetime DEFAULT CURRENT_TIMESTAMP,
  `CREATED_BY` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `UPDATED` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `UPDATED_BY` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `building_photo` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `building`
--

INSERT INTO `building` (`building_id`, `name`, `address`, `postal_code`, `city`, `county`, `num_floors`, `num_properties`, `comment`, `CREATED`, `CREATED_BY`, `UPDATED`, `UPDATED_BY`, `building_photo`) VALUES
(1, 'Fifty-One Washington', 'Christou  55, Emba ', '8250', 'Paphos', 'Cyprus', 6, 12, 'Year Built: 2009\r\nUnit Types: 1-bedroom, 2-bedroom, and 3-bedroom apartments\r\nAmenities: Swimming pool, fitness center, parking garage,\r\n24-hour security\r\nBuilding Materials: Reinforced concrete, glass, and stone', '2024-04-01 20:20:03', 'test', '2024-04-25 14:15:42', 'test', 'https://www.inquirer.com/resizer/5w_NQN1UlpYsdtP_3BSybYyCHBo=/0x149:5000x3485/760x507/filters:format(webp)/cloudfront-us-east-1.images.arcpublishing.com/pmn/MV6GXUJJW5HSJIEYQUU3P4GN6M.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `contracts`
--

CREATE TABLE `contracts` (
  `contract_id` int NOT NULL,
  `property_id` int NOT NULL,
  `contract` blob,
  `CREATED` datetime DEFAULT CURRENT_TIMESTAMP,
  `CREATED_BY` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `UPDATED` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `UPDATED_BY` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `expire_date` date DEFAULT NULL,
  `start_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guest`
--

CREATE TABLE `guest` (
  `guest_id` int NOT NULL,
  `property_id` int NOT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `comment` text COLLATE utf8mb4_general_ci,
  `CREATED` datetime DEFAULT CURRENT_TIMESTAMP,
  `CREATED_BY` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `UPDATED` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `UPDATED_BY` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `history_id` int NOT NULL,
  `action_type` enum('INSERT','UPDATE','DELETE') COLLATE utf8mb4_general_ci NOT NULL,
  `table_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `record_id` int NOT NULL,
  `changed_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `action_timestamp` datetime DEFAULT CURRENT_TIMESTAMP,
  `performed_by` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ;

--
-- Dumping data for table `history`
--

INSERT INTO `history` (`history_id`, `action_type`, `table_name`, `record_id`, `changed_data`, `action_timestamp`, `performed_by`) VALUES
(1, 'UPDATE', 'roles', 1, '{\"old_role_name\": \"test\", \"new_role_name\": \"nnnnn\"}', '2024-03-05 00:25:46', 'System'),
(2, 'UPDATE', 'roles', 1, '{\"old_role_name\": \"nnnnn\", \"new_role_name\": \"c\"}', '2024-03-05 14:23:16', 'System'),
(3, 'INSERT', 'users', 2, '{\"username\": \"rr\", \"email\": \"as@f.com\", \"full_name\": \"eee\"}', '2024-03-05 14:25:21', 'System'),
(4, 'INSERT', 'building', 1, '{\"name\": \"ft\", \"address\": \"tt\", \"city\": \"tt\", \"comment\": null}', '2024-03-05 15:26:22', 'System'),
(5, 'INSERT', 'bills', 1, '{\"bill_title\": \"r\", \"status\": \"p\", \"comment\": \"dd\"}', '2024-03-05 16:14:52', 'System'),
(6, 'INSERT', 'bills', 2, '{\"bill_title\": \"e\", \"status\": \"p\", \"comment\": \"ee\"}', '2024-03-05 17:21:49', 'System'),
(7, 'UPDATE', 'bills', 2, '{\"old_bill_title\": \"e\", \"new_bill_title\": \"e\", \"old_status\": \"p\", \"new_status\": \"f\", \"old_comment\": \"ee\", \"new_comment\": \"ee\"}', '2024-03-05 17:21:57', 'System'),
(8, 'INSERT', 'property', 1, '{\"building_id\": 1, \"status\": \"a\", \"pet\": \"n\", \"furnished\": \"n\", \"rooms\": null, \"bathrooms\": null, \"parking\": null, \"area\": null, \"details\": null}', '2024-03-05 18:22:15', NULL),
(9, 'INSERT', 'guest', 1, '{\"property_id\": 1, \"full_name\": \"dd\", \"phone\": null, \"email\": null, \"comment\": null}', '2024-03-05 18:22:30', 'CURRENT_USER_PLACEHOLDER'),
(10, 'DELETE', 'bills', 1, '{\"bill_title\": \"r\", \"status\": \"p\", \"comment\": \"dd\"}', '2024-03-05 19:06:21', 'System'),
(11, 'DELETE', 'bills', 2, '{\"bill_title\": \"e\", \"status\": \"f\", \"comment\": \"ee\"}', '2024-03-05 19:06:21', 'System'),
(12, 'DELETE', 'users', 2, '{\"username\": \"rr\", \"email\": \"as@f.com\"}', '2024-03-05 19:07:02', 'System'),
(13, 'DELETE', 'roles', 1, '{\"role_name\": \"c\"}', '2024-03-05 19:07:08', 'System'),
(15, 'DELETE', 'guest', 1, '{\"property_id\": 1, \"full_name\": \"dd\", \"phone\": null, \"email\": null, \"comment\": null}', '2024-03-05 19:07:29', 'CURRENT_USER_PLACEHOLDER'),
(16, 'DELETE', 'property', 1, '{\"building_id\": 1, \"status\": \"a\", \"pet\": \"n\", \"furnished\": \"n\", \"rooms\": null, \"bathrooms\": null, \"parking\": null, \"area\": null, \"details\": null}', '2024-03-05 19:08:05', NULL),
(17, 'DELETE', 'building', 1, '{\"name\": \"ft\", \"address\": \"tt\", \"comment\": null}', '2024-03-05 19:08:12', 'System'),
(18, 'INSERT', 'building', 2, '{\"name\": \"t\", \"address\": \"t\", \"city\": \"t\", \"comment\": null}', '2024-03-05 19:15:07', 'System'),
(19, 'DELETE', 'building', 2, '{\"name\": \"t\", \"address\": \"t\", \"comment\": null}', '2024-03-05 19:15:39', 'System'),
(20, 'INSERT', 'building', 3, '{\"name\": \"t\", \"address\": \"t\", \"city\": \"\", \"comment\": null}', '2024-03-05 19:26:16', 'System'),
(21, 'DELETE', 'building', 3, '{\"name\": \"t\", \"address\": \"t\", \"comment\": null}', '2024-03-05 19:26:21', 'System'),
(22, 'INSERT', 'building', 4, '{\"name\": \"2\", \"address\": \"\", \"city\": \"\", \"comment\": null}', '2024-03-06 22:45:40', 'System'),
(23, 'INSERT', 'property', 2, '{\"building_id\": 4, \"status\": \"a\", \"pet\": \"n\", \"furnished\": \"n\", \"rooms\": null, \"bathrooms\": null, \"parking\": null, \"area\": null, \"details\": null}', '2024-03-06 22:45:50', NULL),
(24, 'INSERT', 'property_photos', 1, NULL, '2024-03-06 22:46:02', NULL),
(25, 'DELETE', 'property_photos', 1, NULL, '2024-03-06 22:46:18', NULL),
(26, 'DELETE', 'property', 2, '{\"building_id\": 4, \"status\": \"a\", \"pet\": \"n\", \"furnished\": \"n\", \"rooms\": null, \"bathrooms\": null, \"parking\": null, \"area\": null, \"details\": null}', '2024-03-06 22:46:24', NULL),
(27, 'DELETE', 'building', 4, '{\"name\": \"2\", \"address\": \"\", \"comment\": null}', '2024-03-06 22:46:36', 'System');

-- --------------------------------------------------------

--
-- Table structure for table `property`
--

CREATE TABLE `property` (
  `property_id` int NOT NULL,
  `building_id` int NOT NULL,
  `owner_id` int DEFAULT NULL,
  `tenant_id` int DEFAULT NULL,
  `floor` int DEFAULT NULL,
  `number` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('a','r') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'a',
  `pet` enum('y','n') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'n',
  `furnished` enum('y','n') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'n',
  `rooms` int DEFAULT NULL,
  `bathrooms` int DEFAULT NULL,
  `parking` enum('c','u') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `area` decimal(10,2) DEFAULT NULL,
  `details` text COLLATE utf8mb4_general_ci,
  `property_videos` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `comment` text COLLATE utf8mb4_general_ci,
  `CREATED` datetime DEFAULT CURRENT_TIMESTAMP,
  `CREATED_BY` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `UPDATED` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `UPDATED_BY` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `property`
--

INSERT INTO `property` (`property_id`, `building_id`, `owner_id`, `tenant_id`, `floor`, `number`, `status`, `pet`, `furnished`, `rooms`, `bathrooms`, `parking`, `area`, `details`, `property_videos`, `comment`, `CREATED`, `CREATED_BY`, `UPDATED`, `UPDATED_BY`) VALUES
(18, 1, 20, 9, 1, '1', 'a', 'y', 'y', 2, 3, 'u', 87.00, 'This is my 1st Property in the Building ', NULL, 'I would like to thank Marios for uploading and showing the pictures', '2024-04-22 15:38:08', NULL, NULL, NULL),
(19, 1, 20, 7, 1, '2', 'a', 'y', 'y', 3, 2, 'u', 101.00, 'This is my 2nd Property in the Building', NULL, 'This page needs fixing but this is a test and something to show tomorrow Tuesday 23/04', '2024-04-22 15:39:12', NULL, NULL, NULL),
(20, 1, 20, 9, 1, '2', 'a', 'y', 'y', 3, 3, 'u', 4.00, 'This is my 2nd Property in the Building', NULL, 'This page needs fixing but this is a test and some...', '2024-04-22 15:40:03', NULL, NULL, NULL),
(21, 1, 20, 9, 1, '1', 'a', 'y', 'y', 4, 2, 'u', 99.00, 'This page needs fixing but this is a test and some...', NULL, 'This page needs fixing but this is a test and some...', '2024-04-22 15:40:41', NULL, NULL, NULL),
(22, 1, 20, 1, 1, '1', 'a', 'y', 'y', 1, 1, 'u', 1.00, 'The 3 photos above are to be displayed as Carousel', NULL, 'The 3 photos above are to be displayed as Carousel', '2024-04-22 15:41:18', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `property_photos`
--

CREATE TABLE `property_photos` (
  `id` int NOT NULL,
  `property_id` int DEFAULT NULL,
  `photo_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `property_photos`
--

INSERT INTO `property_photos` (`id`, `property_id`, `photo_path`) VALUES
(1, 18, 'https://bestdesignideas.com/wp-content/uploads/2015/11/Modern-Apartment-In-European-Style-In-Taiwan-From-Fertility-Design-Studio-1.jpg'),
(2, 19, 'https://archello.s3.eu-central-1.amazonaws.com/images/2018/11/16/Modern-apartment-in-Moscow-Diff-007.1542370629.5678.jpg'),
(3, 20, 'https://archello.s3.eu-central-1.amazonaws.com/images/2021/09/08/ab-partners-midtown-apartment--modern-interior-design-apartments-archello.1631083770.83.jpg'),
(4, 21, 'https://cdn.homedit.com/wp-content/uploads/2017/05/Taiwan-residence-decorated-with-a-cool-and-royal-color-palete.jpg'),
(7, 18, '18_66268460b21c0.jpg'),
(8, 20, '20_662684d3cbffa.jpg'),
(9, 21, '21_662684f9b9d16.png'),
(10, 21, '21_662684f9ba57c.png'),
(11, 21, '21_662684f9baccc.png'),
(12, 22, '22_6626851e4a6ff.png');

-- --------------------------------------------------------

--
-- Table structure for table `request`
--

CREATE TABLE `request` (
  `request_id` int NOT NULL,
  `property_id` int NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `details` text COLLATE utf8mb4_general_ci,
  `status` enum('p','f') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'p',
  `start_date` date DEFAULT NULL,
  `finish_date` date DEFAULT NULL,
  `CREATED` datetime DEFAULT CURRENT_TIMESTAMP,
  `CREATED_BY` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `UPDATED` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `UPDATED_BY` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int NOT NULL,
  `role_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `CREATED` datetime DEFAULT CURRENT_TIMESTAMP,
  `CREATED_BY` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `UPDATED` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `UPDATED_BY` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `CREATED`, `CREATED_BY`, `UPDATED`, `UPDATED_BY`) VALUES
(1, 'admin', '2024-03-24 23:10:12', NULL, NULL, NULL),
(2, 'owner', '2024-03-25 21:57:20', NULL, NULL, NULL),
(3, 'tenant', '2024-03-25 21:57:20', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `role_id` int NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone_1` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone_2` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nationality` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `CREATED` datetime DEFAULT CURRENT_TIMESTAMP,
  `CREATED_BY` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `UPDATED` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `UPDATED_BY` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `name` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `surname` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `role_id`, `password`, `email`, `phone_1`, `phone_2`, `nationality`, `CREATED`, `CREATED_BY`, `UPDATED`, `UPDATED_BY`, `name`, `surname`) VALUES
(7, 1, '$2y$10$eLw.8pMrYcrzxg9zuiTuleUqajCrx6AeXtHlW3DggEmuzFtQAyw6i', 'test1@gmail.com', NULL, NULL, NULL, '2024-03-25 19:00:58', NULL, '2024-04-17 15:11:11', NULL, 'admin', 'admin'),
(17, 3, '$2y$10$ZJIjMtjYmq8x2TJKGbmZ4OEy3rFTY9Fm78mQwkEptZzS4tuSrPO6O', 'test3@gmail.com', NULL, NULL, NULL, '2024-03-25 22:37:40', NULL, '2024-04-17 15:12:02', NULL, 'tenant', 'tenant'),
(19, 1, '$2y$10$yKCKOZuloZ.598yn3SPsVOBNdLU3gZ3c8sT1UJgGwGCFgjonG5Tp2', 'test4@gmail.com', NULL, NULL, NULL, '2024-04-07 17:09:10', NULL, NULL, NULL, NULL, NULL),
(20, 2, '$2y$10$bkeG3f8dTqyrGbTvOrZRIelNjlcbFxyIhllx0J1oNL1ygB3L7kGZi', 'test2@gmail.com', '99664477', '99998888', 'Cypriot', '2024-04-15 18:28:46', NULL, '2024-04-22 15:02:29', NULL, 'Marios', 'Stylianou'),
(23, 2, '$2y$10$.otvF/R9lBgGYQfw7/HTD.mGms8BKQN0EyeTjdGgIBMgUT77paUaq', 'mariosstylianou99@gmail.com', '99866211', '', 'Cypriot', '2024-04-16 05:00:03', NULL, '2024-04-21 06:41:47', NULL, 'Marios', 'Stylianou'),
(26, 2, NULL, 'antphil87@gmail.com', '99882462', '-', 'Cypriot', '2024-04-16 08:56:18', NULL, '2024-04-16 13:24:48', NULL, 'Antonis', 'Filippou'),
(27, 2, '$2y$10$LuVQSsNnG2erfJizUJgEV.8Z5J6Eb4IrPDxE5elpNUW.DZIZ2WKwm', 'sotirisfrost@gmail.com', '97824000', '', 'Greek', '2024-04-19 09:27:40', NULL, '2024-04-22 14:35:11', NULL, 'Sotiris', 'Gypsiotis'),
(29, 2, '$2y$10$b8AS5NTt7h0Lj7XdDrj1qeGpbN6tdrELgb6sXU.UXDfaHljWt4MXG', 'marios1992cy@hotmail.com', '99109071', '', 'Cypriot', '2024-04-22 03:27:17', NULL, NULL, NULL, 'Marios', 'Chris'),
(30, 2, NULL, 'johnysins@sirina.com', '33333333', NULL, 'American', '2024-04-22 03:30:01', NULL, NULL, NULL, 'Johny', 'Sins');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bills`
--
ALTER TABLE `bills`
  ADD PRIMARY KEY (`bill_id`),
  ADD KEY `fk_bills_property` (`property_id`);

--
-- Indexes for table `building`
--
ALTER TABLE `building`
  ADD PRIMARY KEY (`building_id`);

--
-- Indexes for table `contracts`
--
ALTER TABLE `contracts`
  ADD PRIMARY KEY (`contract_id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `guest`
--
ALTER TABLE `guest`
  ADD PRIMARY KEY (`guest_id`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `table_name` (`table_name`,`record_id`);

--
-- Indexes for table `property`
--
ALTER TABLE `property`
  ADD PRIMARY KEY (`property_id`),
  ADD KEY `fk_property_building` (`building_id`),
  ADD KEY `fk_property_owner_id` (`owner_id`);

--
-- Indexes for table `property_photos`
--
ALTER TABLE `property_photos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `request`
--
ALTER TABLE `request`
  ADD PRIMARY KEY (`request_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`,`phone_1`,`phone_2`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bills`
--
ALTER TABLE `bills`
  MODIFY `bill_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `building`
--
ALTER TABLE `building`
  MODIFY `building_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contracts`
--
ALTER TABLE `contracts`
  MODIFY `contract_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guest`
--
ALTER TABLE `guest`
  MODIFY `guest_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `history_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `property`
--
ALTER TABLE `property`
  MODIFY `property_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `property_photos`
--
ALTER TABLE `property_photos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `request`
--
ALTER TABLE `request`
  MODIFY `request_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
