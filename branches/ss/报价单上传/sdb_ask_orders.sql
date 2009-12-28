-- phpMyAdmin SQL Dump
-- version 3.0.0
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2009 年 12 月 14 日 14:40
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
-- 表的结构 `sdb_ask_orders`
--

CREATE TABLE IF NOT EXISTS `sdb_ask_orders` (
  `ask_id` mediumint(8) unsigned NOT NULL auto_increment,
  `order_url` varchar(100) default NULL,
  `order_name` varchar(100) default NULL,
  `order_type` varchar(10) default NULL,
  `add_time` int(10) unsigned NOT NULL default '0',
  `disabled` enum('true','false') default 'false',
  PRIMARY KEY  (`ask_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `sdb_ask_orders`
--

