-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 26, 2017 at 08:00 PM
-- Server version: 5.6.33-0ubuntu0.14.04.1
-- PHP Version: 7.1.1-1+deb.sury.org~trusty+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wp_ayso1ref`
--
CREATE DATABASE IF NOT EXISTS `wp_ayso1ref` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `wp_ayso1ref`;

-- --------------------------------------------------------

--
-- Table structure for table `rs_events`
--

DROP TABLE IF EXISTS `rs_events`;
CREATE TABLE `rs_events` (
  `id` int(11) NOT NULL,
  `projectKey` varchar(45) NOT NULL,
  `name` varchar(255) NOT NULL,
  `dates` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `infoLink` varchar(255) DEFAULT NULL,
  `locked` tinyint(1) DEFAULT '1',
  `view` tinyint(1) DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT NULL,
  `show_medal_round` tinyint(1) DEFAULT '0',
  `label` varchar(255) DEFAULT NULL,
  `num_refs` int(11) DEFAULT '3',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `field_map` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `rs_games`
--

DROP TABLE IF EXISTS `rs_games`;
CREATE TABLE `rs_games` (
  `id` int(11) NOT NULL,
  `projectKey` varchar(45) NOT NULL,
  `game_number` int(11) NOT NULL,
  `date` date NOT NULL,
  `time` time DEFAULT NULL,
  `field` varchar(45) DEFAULT NULL,
  `division` varchar(45) DEFAULT NULL,
  `pool` varchar(3) DEFAULT NULL,
  `home` varchar(45) DEFAULT NULL,
  `home_team` varchar(45) DEFAULT NULL,
  `away` varchar(45) DEFAULT NULL,
  `away_team` varchar(45) DEFAULT NULL,
  `assignor` varchar(45) DEFAULT NULL,
  `cr` varchar(45) DEFAULT NULL,
  `ar1` varchar(45) DEFAULT NULL,
  `ar2` varchar(45) DEFAULT NULL,
  `r4th` varchar(45) DEFAULT NULL,
  `medalRound` tinyint(1) DEFAULT '0',
  `locked` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `rs_limits`
--

DROP TABLE IF EXISTS `rs_limits`;
CREATE TABLE `rs_limits` (
  `id` int(11) NOT NULL,
  `projectKey` varchar(45) NOT NULL,
  `division` varchar(10) NOT NULL,
  `limit` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `rs_log`
--

DROP TABLE IF EXISTS `rs_log`;
CREATE TABLE `rs_log` (
  `id` int(11) NOT NULL,
  `timestamp` datetime NOT NULL,
  `projectKey` varchar(45) CHARACTER SET utf8 NOT NULL,
  `note` varchar(1024) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `rs_messages`
--

DROP TABLE IF EXISTS `rs_messages`;
CREATE TABLE `rs_messages` (
  `id` int(11) NOT NULL,
  `message` varchar(4096) CHARACTER SET utf8 DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `rs_users`
--

DROP TABLE IF EXISTS `rs_users`;
CREATE TABLE `rs_users` (
  `id` int(11) NOT NULL,
  `name` char(255) NOT NULL,
  `enabled` tinyint(1) DEFAULT '1',
  `for_events` varchar(1024) DEFAULT NULL,
  `hash` varchar(255) DEFAULT NULL,
  `admin` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `rs_events`
--
ALTER TABLE `rs_events`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_UNIQUE` (`id`);

--
-- Indexes for table `rs_games`
--
ALTER TABLE `rs_games`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_UNIQUE` (`id`);

--
-- Indexes for table `rs_limits`
--
ALTER TABLE `rs_limits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_UNIQUE` (`id`);

--
-- Indexes for table `rs_log`
--
ALTER TABLE `rs_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rs_messages`
--
ALTER TABLE `rs_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rs_users`
--
ALTER TABLE `rs_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_UNIQUE` (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `rs_events`
--
ALTER TABLE `rs_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `rs_games`
--
ALTER TABLE `rs_games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=689;
--
-- AUTO_INCREMENT for table `rs_limits`
--
ALTER TABLE `rs_limits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;
--
-- AUTO_INCREMENT for table `rs_log`
--
ALTER TABLE `rs_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1461;
--
-- AUTO_INCREMENT for table `rs_messages`
--
ALTER TABLE `rs_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `rs_users`
--
ALTER TABLE `rs_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
