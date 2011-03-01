-- phpMyAdmin SQL Dump
-- version 2.6.4-pl3
-- http://www.phpmyadmin.net
-- 
-- Host: db1465.perfora.net
-- Generation Time: May 14, 2008 at 10:25 AM
-- Server version: 5.0.51
-- PHP Version: 4.3.10-200.schlund.1
-- 
-- Database: `db243453615`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `site_calls`
-- 

CREATE TABLE `site_calls` (
  `call_id` int(11) NOT NULL auto_increment,
  `call_first_name` varchar(100) collate latin1_general_ci NOT NULL,
  `call_last_name` varchar(100) collate latin1_general_ci NOT NULL,
  `call_phone` varchar(100) collate latin1_general_ci NOT NULL,
  `call_email` varchar(200) collate latin1_general_ci NOT NULL,
  `call_department` int(11) NOT NULL default '0',
  `call_request` int(11) NOT NULL default '0',
  `call_device` int(11) NOT NULL default '0',
  `call_details` text collate latin1_general_ci NOT NULL,
  `call_date` int(11) NOT NULL default '0',
  `call_date2` int(11) NOT NULL default '0',
  `call_status` int(11) NOT NULL default '0',
  `call_solution` text collate latin1_general_ci NOT NULL,
  `call_user` int(11) NOT NULL default '0',
  `call_staff` int(11) NOT NULL default '0',
  PRIMARY KEY  (`call_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Dumping data for table `site_calls`
-- 

INSERT INTO `site_calls` VALUES (4, 'Chris', '', '555-1313', 'chriss@example.com', 15, 8, 10, 'I opened a zip file, now my computer is running really slow.', 1210773480, -1, 0, '', 1008, 18);
INSERT INTO `site_calls` VALUES (5, 'Sally', '', '555-1414', 'sally@example.com', 17, 3, 11, 'I forgot my password to the network.', 1210593840, -1, 1, 'reset sally''s password.', 1007, 7);

-- --------------------------------------------------------

-- 
-- Table structure for table `site_notes`
-- 

CREATE TABLE `site_notes` (
  `note_id` int(11) NOT NULL auto_increment,
  `note_title` varchar(200) collate latin1_general_ci NOT NULL,
  `note_body` text collate latin1_general_ci NOT NULL,
  `note_relation` int(11) NOT NULL default '0',
  `note_type` int(1) NOT NULL default '0',
  `note_post_date` int(11) NOT NULL default '0',
  `note_post_ip` varchar(20) collate latin1_general_ci NOT NULL,
  `note_post_user` int(11) NOT NULL default '0',
  PRIMARY KEY  (`note_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Dumping data for table `site_notes`
-- 

INSERT INTO `site_notes` VALUES (9, 'Every Monday', 'This happens to Sally every Monday.', 5, 1, 1210773801, '127.0.0.1', 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `site_types`
-- 

CREATE TABLE `site_types` (
  `type_id` int(11) NOT NULL auto_increment,
  `type` int(1) NOT NULL default '0',
  `type_name` varchar(200) collate latin1_general_ci NOT NULL,
  `type_email` varchar(200) collate latin1_general_ci NOT NULL,
  `type_location` text collate latin1_general_ci NOT NULL,
  `type_phone` varchar(100) collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Dumping data for table `site_types`
-- 

INSERT INTO `site_types` VALUES (1, 1, 'Sales', '', '', '');
INSERT INTO `site_types` VALUES (2, 1, 'Marketing', '', '', '');
INSERT INTO `site_types` VALUES (3, 2, 'Urgent', '', '', '');
INSERT INTO `site_types` VALUES (4, 2, 'Question', '', '', '');
INSERT INTO `site_types` VALUES (5, 3, 'Monitor', '', '', '');
INSERT INTO `site_types` VALUES (6, 3, 'Keyboard', '', '', '');
INSERT INTO `site_types` VALUES (7, 0, 'Jon Techie', 'jon@example.com', 'Anytown, USA', '555-help');
INSERT INTO `site_types` VALUES (8, 2, 'Non-Urgent', '', '', '');
INSERT INTO `site_types` VALUES (9, 3, 'Mouse', '', '', '');
INSERT INTO `site_types` VALUES (10, 3, 'Network', '', '', '');
INSERT INTO `site_types` VALUES (11, 3, 'Other', '', '', '');
INSERT INTO `site_types` VALUES (12, 3, 'Computer Unit', '', '', '');
INSERT INTO `site_types` VALUES (13, 3, 'Printer', '', '', '');
INSERT INTO `site_types` VALUES (14, 3, 'Software', '', '', '');
INSERT INTO `site_types` VALUES (15, 1, 'Accounting', '', '', '');
INSERT INTO `site_types` VALUES (16, 1, 'Customer Service', '', '', '');
INSERT INTO `site_types` VALUES (17, 1, 'Design', '', '', '');
INSERT INTO `site_types` VALUES (18, 0, 'Sara Fixit', 'sara@example.com', 'Eastern branch', '555-work');

-- --------------------------------------------------------

-- 
-- Table structure for table `site_users`
-- 

CREATE TABLE `site_users` (
  `user_id` int(11) NOT NULL auto_increment,
  `user_login` varchar(30) collate latin1_general_ci NOT NULL,
  `user_password` varchar(30) collate latin1_general_ci NOT NULL,
  `user_name` varchar(200) collate latin1_general_ci NOT NULL,
  `user_address` varchar(200) collate latin1_general_ci NOT NULL,
  `user_city` varchar(100) collate latin1_general_ci NOT NULL,
  `user_state` char(3) collate latin1_general_ci NOT NULL,
  `user_zip` varchar(20) collate latin1_general_ci NOT NULL,
  `user_country` char(3) collate latin1_general_ci NOT NULL,
  `user_phone` varchar(39) collate latin1_general_ci NOT NULL,
  `user_email` varchar(200) collate latin1_general_ci NOT NULL,
  `user_email2` varchar(200) collate latin1_general_ci NOT NULL,
  `user_im_aol` varchar(100) collate latin1_general_ci NOT NULL,
  `user_im_icq` varchar(100) collate latin1_general_ci NOT NULL,
  `user_im_msn` varchar(100) collate latin1_general_ci NOT NULL,
  `user_im_yahoo` varchar(100) collate latin1_general_ci NOT NULL,
  `user_im_other` varchar(200) collate latin1_general_ci NOT NULL,
  `user_status` int(1) NOT NULL default '0',
  `user_level` int(1) NOT NULL default '0',
  `user_pending` int(11) NOT NULL default '0',
  `user_date` int(11) NOT NULL default '0',
  `last_login` int(11) NOT NULL default '0',
  `last_ip` varchar(20) collate latin1_general_ci NOT NULL,
  `user_msg_send` int(1) NOT NULL default '0',
  `user_msg_subject` varchar(200) collate latin1_general_ci NOT NULL,
  `user_protect_delete` int(1) default '0',
  `user_protect_edit` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1009 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci PACK_KEYS=0;

-- 
-- Dumping data for table `site_users`
-- 

INSERT INTO `site_users` VALUES (1, 'admin', 'test', 'Site Admin', '', '', '', '', '', '', 'admin@example.com', 'someone@example.com', '', '', '', '', '', 0, 0, 0, 0, 1117030100, '127.0.0.1', 1, 'New Message', 1, 0);
INSERT INTO `site_users` VALUES (1006, 'mark', 'test', 'Mark Johnson', '', '', '', '', '', '', 'markj@example.com', '', '', '', '', '', '', 0, 1, 0, 1117033601, 1117033624, '127.0.0.1', 0, '', 0, 0);
INSERT INTO `site_users` VALUES (1007, 'sally', 'test', 'Sally Lot', '', '', '', '', '', '', 'sallyl@example.com', '', '', '', '', '', '', 0, 1, 0, 1210772181, 0, '', 0, '', 0, 0);
INSERT INTO `site_users` VALUES (1008, 'chris', 'test', 'Chris Smith', '', '', '', '', '', '', 'chriss@example.com', '', '', '', '', '', '', 0, 1, 0, 1210772210, 0, '', 0, '', 0, 0);
