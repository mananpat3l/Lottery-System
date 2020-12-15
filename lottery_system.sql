-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 11, 2020 at 09:12 PM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.4.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lottery_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `lotteries`
--

CREATE TABLE `lotteries` (
  `lotto_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `prize` decimal(10,0) NOT NULL,
  `date` date NOT NULL,
  `win_1` int(11) DEFAULT NULL,
  `win_2` int(11) DEFAULT NULL,
  `win_3` int(11) DEFAULT NULL,
  `win_4` int(11) DEFAULT NULL,
  `win_5` int(11) DEFAULT NULL,
  `win_6` int(11) DEFAULT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lotteries`
--

INSERT INTO `lotteries` (`lotto_id`, `name`, `prize`, `date`, `win_1`, `win_2`, `win_3`, `win_4`, `win_5`, `win_6`, `image`) VALUES
(1, 'Jackpot', '300000', '2020-12-26', NULL, NULL, NULL, NULL, NULL, NULL, '4.png'),
(2, 'Lotto', '10000', '2021-01-01', NULL, NULL, NULL, NULL, NULL, NULL, '3.png'),
(3, 'Mega Lotto', '100000', '2021-04-01', NULL, NULL, NULL, NULL, NULL, NULL, '2.jpg'),
(4, 'Lightning Lotto', '5500', '2020-12-25', 1, 8, 15, 20, 29, 52, '1.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `member_id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `usertype` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`member_id`, `first_name`, `last_name`, `dob`, `email`, `password`, `usertype`) VALUES
(1, 'Test', 'T', '1985-03-22', 'test123@luckylotto.com', '$2y$10$DE007a7P6hER48O3ivH8uudQGqChFTxglBQ3pkGbXLeK5qGx1uvzC', 'member'),
(3, 'Manan', 'Patel', '2020-06-05', 'mananpatel.98@gmail.com', '$2y$10$y7nUnv6cqGQqEwho.8pJB.th.QtAP24X6G/5VUqfhi7G06okBEm/.', 'member'),
(14, 'Jack', 'ma', '1989-10-25', 'jackma12@alibaba.com', '$2y$10$vn6W2XOW8MvmaPn8zvn3dud2gx4H9tUzoL6Lb4GIO2n3ops986b0a', 'member');

-- --------------------------------------------------------

--
-- Table structure for table `payment_method`
--

CREATE TABLE `payment_method` (
  `payment_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `number` varchar(16) NOT NULL,
  `expiry` date NOT NULL,
  `security_code` int(3) NOT NULL,
  `member_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `ticket_id` int(11) NOT NULL,
  `lotto_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `num_1` int(11) NOT NULL,
  `num_2` int(11) NOT NULL,
  `num_3` int(11) NOT NULL,
  `num_4` int(11) NOT NULL,
  `num_5` int(11) NOT NULL,
  `num_6` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`ticket_id`, `lotto_id`, `user_id`, `num_1`, `num_2`, `num_3`, `num_4`, `num_5`, `num_6`) VALUES
(44, 1, 3, 5, 19, 30, 42, 45, 51),
(46, 1, 3, 14, 16, 27, 29, 40, 53);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lotteries`
--
ALTER TABLE `lotteries`
  ADD PRIMARY KEY (`lotto_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`member_id`);

--
-- Indexes for table `payment_method`
--
ALTER TABLE `payment_method`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `member_payment_FK` (`member_id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`ticket_id`),
  ADD KEY `lotto_id_FK` (`lotto_id`),
  ADD KEY `user_id_FK` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lotteries`
--
ALTER TABLE `lotteries`
  MODIFY `lotto_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `payment_method`
--
ALTER TABLE `payment_method`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `payment_method`
--
ALTER TABLE `payment_method`
  ADD CONSTRAINT `member_payment_FK` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `lotto_id_FK` FOREIGN KEY (`lotto_id`) REFERENCES `lotteries` (`lotto_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_id_FK` FOREIGN KEY (`user_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
