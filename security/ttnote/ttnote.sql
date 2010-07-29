-- MySQL dump 10.13  Distrib 5.1.47, for pc-linux-gnu (i686)
--
-- Host: localhost    Database: ttnote
-- ------------------------------------------------------
-- Server version	5.1.47

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `sdb_base_app_content`
--

DROP TABLE IF EXISTS `sdb_base_app_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sdb_base_app_content` (
  `content_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `content_type` varchar(80) NOT NULL,
  `app_id` varchar(32) NOT NULL,
  `content_name` varchar(80) DEFAULT NULL,
  `content_title` varchar(100) DEFAULT NULL,
  `content_path` varchar(255) DEFAULT NULL,
  `disabled` enum('true','false') DEFAULT 'false',
  PRIMARY KEY (`content_id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sdb_base_apps`
--

DROP TABLE IF EXISTS `sdb_base_apps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sdb_base_apps` (
  `app_id` varchar(32) NOT NULL DEFAULT '',
  `app_name` varchar(50) DEFAULT NULL,
  `debug_mode` enum('true','false') DEFAULT 'false',
  `app_config` text,
  `status` enum('installed','resolved','starting','active','stopping','uninstalled','broken') DEFAULT 'uninstalled',
  `webpath` varchar(20) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `local_ver` varchar(20) DEFAULT NULL,
  `remote_ver` varchar(20) DEFAULT NULL,
  `author_name` varchar(100) DEFAULT NULL,
  `author_url` varchar(100) DEFAULT NULL,
  `author_email` varchar(100) DEFAULT NULL,
  `dbver` varchar(32) DEFAULT NULL,
  `remote_config` longtext,
  PRIMARY KEY (`app_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sdb_base_cache_expires`
--

DROP TABLE IF EXISTS `sdb_base_cache_expires`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sdb_base_cache_expires` (
  `type` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `expire` int(10) unsigned NOT NULL,
  PRIMARY KEY (`type`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sdb_base_files`
--

DROP TABLE IF EXISTS `sdb_base_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sdb_base_files` (
  `file_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `file_path` varchar(255) DEFAULT NULL,
  `file_type` enum('private','public') DEFAULT 'public',
  `last_change_time` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sdb_base_kvstore`
--

DROP TABLE IF EXISTS `sdb_base_kvstore`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sdb_base_kvstore` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `prefix` varchar(255) NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` longtext,
  `dateline` int(10) unsigned DEFAULT NULL,
  `ttl` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sdb_base_network`
--

DROP TABLE IF EXISTS `sdb_base_network`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sdb_base_network` (
  `node_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `node_name` varchar(255) NOT NULL,
  `node_url` varchar(100) NOT NULL,
  `node_api` varchar(100) NOT NULL,
  `link_status` enum('active','group','wait') NOT NULL DEFAULT 'wait',
  `node_detail` varchar(255) DEFAULT NULL,
  `token` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`node_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sdb_base_queue`
--

DROP TABLE IF EXISTS `sdb_base_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sdb_base_queue` (
  `queue_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `queue_title` varchar(50) NOT NULL,
  `status` enum('running','hibernate','paused') NOT NULL DEFAULT 'hibernate',
  `worker` varchar(200) NOT NULL,
  `start_time` int(10) unsigned NOT NULL,
  `worker_active` int(10) unsigned DEFAULT NULL,
  `total` mediumint(8) unsigned DEFAULT NULL,
  `remaining` mediumint(8) unsigned DEFAULT NULL,
  `cursor_id` varchar(255) NOT NULL DEFAULT '0',
  `runkey` char(32) DEFAULT NULL,
  `task_name` varchar(50) DEFAULT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`queue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sdb_base_rpcpoll`
--

DROP TABLE IF EXISTS `sdb_base_rpcpoll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sdb_base_rpcpoll` (
  `id` varchar(32) DEFAULT NULL,
  `process_id` varchar(32) DEFAULT NULL,
  `type` enum('request','response') DEFAULT NULL,
  `calltime` int(10) unsigned DEFAULT NULL,
  `network` mediumint(8) unsigned DEFAULT NULL,
  `method` varchar(100) DEFAULT NULL,
  `params` text,
  `callback` varchar(200) DEFAULT NULL,
  `callback_params` text,
  `result` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sdb_base_task`
--

DROP TABLE IF EXISTS `sdb_base_task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sdb_base_task` (
  `task` varchar(100) NOT NULL DEFAULT '',
  `minute` int(10) unsigned DEFAULT NULL,
  `hour` int(10) unsigned DEFAULT NULL,
  `day` int(10) unsigned DEFAULT NULL,
  `week` int(10) unsigned DEFAULT NULL,
  `month` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`task`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-07-29 18:54:06
