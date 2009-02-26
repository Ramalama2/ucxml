-- phpMyAdmin SQL Dump
-- version 3.1.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 13, 2009 at 05:29 PM
-- Server version: 5.1.30
-- PHP Version: 5.2.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `ucxml`
--

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE IF NOT EXISTS `contacts` (
  `id_contact` varchar(36) NOT NULL DEFAULT '0',
  `display_name` varchar(30) NOT NULL DEFAULT '',
  `member_of` varchar(32) NOT NULL DEFAULT '',
  `lname` varchar(30) NOT NULL DEFAULT '',
  `fname` varchar(30) NOT NULL DEFAULT '',
  `nick` varchar(30) NOT NULL DEFAULT '',
  `title` varchar(30) NOT NULL DEFAULT '',
  `office_phone` varchar(30) DEFAULT '',
  `home_phone` varchar(30) DEFAULT '',
  `cell_phone` varchar(30) DEFAULT '',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `owner` varchar(36) NOT NULL DEFAULT '',
  `custom_phone` varchar(30) DEFAULT '',
  `custom_number` varchar(30) DEFAULT '',
  `other_phone` varchar(30) DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id_contact`, `display_name`, `member_of`, `lname`, `fname`, `nick`, `title`, `office_phone`, `home_phone`, `cell_phone`, `date`, `owner`, `custom_phone`, `custom_number`, `other_phone`) VALUES
('f0538239-592a7-64f48-158b-4991859e6a', 'VDJ, DJ - Skola', 'B521', 'VDJ', 'DJ', 'Skola', 'Ang.', '3444', '', '', '2009-02-10 15:47:03', '0', 'Create Custom', '', ''),
('a7d8d7a2-c722f-4caf7-a103-4991853a8d', 'Onovy, On - Skola', 'B512', 'Onovy', 'On', 'Skola', 'Doc', '', '', '', '2009-02-10 14:48:44', '0', 'Create Custom', '', '');
-- --------------------------------------------------------

--
-- Table structure for table `memos`
--

CREATE TABLE IF NOT EXISTS `memos` (
  `id_memo` varchar(36) NOT NULL DEFAULT '',
  `msg` blob NOT NULL,
  `date` int(11) NOT NULL DEFAULT '0',
  `access` varchar(20) NOT NULL DEFAULT '',
  `sender` varchar(20) NOT NULL DEFAULT '',
  `receiver` varchar(20) NOT NULL DEFAULT '',
  `title` varchar(30) NOT NULL DEFAULT '',
  `preference` varchar(11) NOT NULL DEFAULT 'primary',
  `memo_ob` varchar(6) NOT NULL DEFAULT 'date'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `memos`
--

INSERT INTO `memos` (`id_memo`, `msg`, `date`, `access`, `sender`, `receiver`, `title`, `preference`, `memo_ob`) VALUES
('1ab418cf-c9af9-82e24-4a1b-4990988748', '', 1234213051, '', 'admin', '', '', 'primary', 'Date');

-- --------------------------------------------------------

--
-- Table structure for table `phone`
--

CREATE TABLE IF NOT EXISTS `phone` (
  `id_phone` varchar(36) NOT NULL DEFAULT '0',
  `MAC` varchar(16) NOT NULL DEFAULT '',
  `date` varchar(12) NOT NULL DEFAULT '',
  `number` varchar(20) NOT NULL DEFAULT '',
  `away_msg` varchar(100) NOT NULL DEFAULT '',
  `fname` varchar(20) NOT NULL DEFAULT '',
  `lname` varchar(20) NOT NULL DEFAULT '',
  `status` int(1) NOT NULL DEFAULT '0',
  `access_lvl` varchar(12) DEFAULT '',
  `preference` varchar(11) NOT NULL DEFAULT 'primary',
  `ph_sec` char(3) NOT NULL DEFAULT 'yes',
  `status_view` varchar(2) NOT NULL DEFAULT '-1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `phone`
--

INSERT INTO `phone` (`id_phone`, `MAC`, `date`, `number`, `away_msg`, `fname`, `lname`, `status`, `access_lvl`, `preference`, `ph_sec`, `status_view`) VALUES
('38ceaef5-376ed-098c2-4236-43b563060c', 'SEP00131A6FD5E5', '', '', '', 'User', 'Sample', 0, 'Unrestricted', 'primary', 'Yes', '-1'),
('501bea4d-b0a5c-c69ef-d6c3-4991927a77', 'X3S00131A6FD5E5', '', '', '', 'User2', 'Userovy', 0, 'Restricted', '', '', '-1');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id_user` varchar(36) NOT NULL DEFAULT '',
  `username` varchar(50) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `email` varchar(50) NOT NULL DEFAULT '',
  `account_type` varchar(5) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `email`, `account_type`) VALUES
('0', 'admin', 'a74ad8dfacd4f985eb3977517615ce25', '', 'Admin'),
('639e8ade-13225-cad00-ddeb-43b55e6719', 'joe', 'ee11cbb19052e40b07aac0ca060c23ee', 'joe1234@hostXYZ.com', 'User');