-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 04, 2012 at 08:53 AM
-- Server version: 5.5.14
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `jharvard_pset7`
--
CREATE DATABASE `jharvard_pset7` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `jharvard_pset7`;

-- --------------------------------------------------------

--
-- Table structure for table `pset7_cache`
--

CREATE TABLE IF NOT EXISTS `pset7_cache` (
  `cacheid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sid` int(10) unsigned NOT NULL,
  `price` decimal(20,4) NOT NULL,
  `quote_epoch` int(11) unsigned NOT NULL COMMENT 'epoch is the lookup time not stock''s last trade time',
  PRIMARY KEY (`cacheid`),
  KEY `sid` (`sid`),
  KEY `sid_2` (`sid`,`quote_epoch`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1433 ;

-- --------------------------------------------------------

--
-- Table structure for table `pset7_history`
--

CREATE TABLE IF NOT EXISTS `pset7_history` (
  `hid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `sid` int(10) unsigned NOT NULL,
  `action` enum('sale','purchase') NOT NULL,
  `number_of_stocks` int(10) unsigned NOT NULL,
  `value` decimal(30,4) NOT NULL COMMENT 'total value of transaction (ie. not price per share)',
  `sale_tail` bigint(20) DEFAULT NULL COMMENT 'in the event of sale transactions: the profit (positive) or loss (negative) of the sale',
  `history_epoch` int(10) unsigned NOT NULL,
  PRIMARY KEY (`hid`),
  KEY `history_epoch` (`history_epoch`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Table structure for table `pset7_portfolios`
--

CREATE TABLE IF NOT EXISTS `pset7_portfolios` (
  `pid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `sid` int(10) unsigned NOT NULL,
  `number_of_stocks` int(10) unsigned NOT NULL,
  `purchase_price` decimal(20,4) NOT NULL,
  `purchase_time` int(10) unsigned NOT NULL COMMENT 'this is epoch time',
  PRIMARY KEY (`pid`),
  UNIQUE KEY `uid_2` (`uid`,`sid`,`purchase_price`),
  KEY `uid` (`uid`),
  KEY `sid` (`sid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=27 ;

--
-- Dumping data for table `pset7_portfolios`
--

-- --------------------------------------------------------

--
-- Table structure for table `pset7_stocks`
--

CREATE TABLE IF NOT EXISTS `pset7_stocks` (
  `sid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'unique stock id, to be used on stock_cache tbl',
  `symbol` varchar(64) NOT NULL,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`sid`),
  UNIQUE KEY `symbol` (`symbol`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `pset7_stocks`
--

INSERT INTO `pset7_stocks` (`sid`, `symbol`, `name`) VALUES
(1, 'MSFT', 'Microsoft Corpora'),
(2, 'AAPL', 'Apple Inc.'),
(3, 'OIL', 'Barclays Bank Plc'),
(4, 'NFLX', 'Netflix, Inc.'),
(5, 'MJNA.PK', 'MEDICAL MARIJUANA'),
(6, 'AIR', 'AAR Corp. Common'),
(7, 'GCX11.CMX', 'Gold Nov 11'),
(9, 'GRNB', 'Green Bankshares,'),
(10, 'GRPN', 'Groupon, Inc.'),
(11, 'BA', 'Boeing Company (T'),
(12, 'X', 'United States Ste'),
(13, 'XOM', 'Exxon Mobil Corpo'),
(14, 'IBM', 'International Bus');

-- --------------------------------------------------------

--
-- Table structure for table `pset7_users`
--

CREATE TABLE IF NOT EXISTS `pset7_users` (
  `uid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `displayname` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `cash` decimal(65,4) unsigned NOT NULL DEFAULT '0.0000',
  `timezone` varchar(64) NOT NULL DEFAULT 'UTC',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `displayname` (`displayname`),
  KEY `username_2` (`username`,`password`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------
