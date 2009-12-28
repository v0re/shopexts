-- phpMyAdmin SQL Dump
-- version 3.0.0
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2009 年 10 月 14 日 10:58
-- 服务器版本: 5.0.27
-- PHP 版本: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `shop484`
--

-- --------------------------------------------------------

--
-- 表的结构 `sdb_member_pric`
--

CREATE TABLE IF NOT EXISTS `sdb_member_pric` (
  `ask_id` mediumint(8) unsigned NOT NULL auto_increment,
  `member_id` mediumint(8) NOT NULL,
  `goods_id` mediumint(8) NOT NULL,
  `addon` longtext,
  `asktime` int(10) default NULL,
  `ask_status` tinyint(3) unsigned NOT NULL default '0',
  `disabled` enum('true','false') default 'false',
  PRIMARY KEY  (`ask_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- 导出表中的数据 `sdb_member_pric`
--

INSERT INTO `sdb_member_pric` (`ask_id`, `member_id`, `goods_id`, `addon`, `asktime`, `ask_status`, `disabled`) VALUES
(1, 5, 75, NULL, 1255159861, 0, 'false'),
(2, 5, 75, NULL, 1255160262, 0, 'false'),
(3, 5, 74, NULL, 1255160279, 0, 'false'),
(4, 4, 75, NULL, 1255162065, 0, 'false'),
(5, 4, 74, NULL, 1255429598, 0, 'false'),
(6, 4, 72, NULL, 1255162111, 0, 'false'),
(19, 4, 3, NULL, 1255413804, 1, 'false'),
(9, 4, 33, NULL, 1255419240, 0, 'false'),
(10, 4, 73, NULL, 1255164183, 0, 'false'),
(11, 4, 70, NULL, 1255164208, 0, 'false'),
(12, 4, 69, NULL, 1255164227, 0, 'false'),
(13, 4, 66, NULL, 1255164238, 0, 'false'),
(14, 4, 63, NULL, 1255164251, 0, 'false'),
(15, 4, 59, NULL, 1255164264, 1, 'false'),
(16, 4, 71, NULL, 1255164307, 0, 'false'),
(17, 4, 68, NULL, 1255413675, 1, 'false'),
(18, 4, 65, NULL, 1255413853, 1, 'false');
