-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 17, 2014 at 03:35 PM
-- Server version: 5.5.34
-- PHP Version: 5.3.10-1ubuntu3.8

-- --------------------------------------------------------
--
-- NOTE: You MUST change the location ID in the 2 INSERT statements before running the script
-- 
-- IT NEEDS TO BE THE SAME LOCATION ID FOR BOTH VALUES - REMEMBER THIS VALUE AS IT WILL BE NEEDED TO 
-- AUTHENTICATE USING THE ADMIN ACCOUNT THE FIRST TIME
-- 
-- 
-- 
-- 
-- 
-- --------------------------------------------------------

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `api`
--

-- --------------------------------------------------------

--
-- Table structure for table `authKeys`
--

CREATE TABLE IF NOT EXISTS `authKeys` (
  `authKey` varchar(250) NOT NULL DEFAULT '',
  `locationID` varchar(250) DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `timestampExpire` int(11) DEFAULT NULL,
  `securityRole` int(11) DEFAULT NULL,
  `notes` varchar(500) DEFAULT NULL,
  `createdStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`authKey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE IF NOT EXISTS `location` (
  `uid` varchar(250) NOT NULL DEFAULT '',
  `locationName` varchar(250) DEFAULT NULL,
  `recordStatus` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`uid`),
  KEY `recordStatus` (`recordStatus`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `location` (`uid`, `locationName`, `recordStatus`) VALUES
('DEFAULT_LOCATION_ID_CHANGE_THIS', 'Default Location ', 1);

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE IF NOT EXISTS `staff` (
  `uid` varchar(250) NOT NULL DEFAULT '',
  `locationID` varchar(100) DEFAULT NULL,
  `employeeName` varchar(100) DEFAULT NULL,
  `userName` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `securityRole` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `recordStatus` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `userName` (`userName`),
  KEY `recordStatus` (`recordStatus`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
INSERT INTO `staff` (`uid`, `locationID`, `employeeName`, `userName`, `password`, `securityRole`, `status`, `recordStatus`) VALUES
('4bfV5s77j8UkQETTyDH6H', 'DEFAULT_LOCATION_ID_CHANGE_THIS', 'Owner Account', 'admin', 'password', 1, 1, 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
