-- create tables
CREATE TABLE IF NOT EXISTS `[db_prefix]units` (
  `ID` tinyint(4) unsigned NOT NULL auto_increment,
  `Text` mediumtext NOT NULL,
  `Author` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


INSERT INTO `[db_prefix]units` VALUES(1, 'We have all known the long loneliness and we have learned that the only solution is love and that love comes with community.', 'Dorothy Day');
INSERT INTO `[db_prefix]units` VALUES(2, 'For a community to be whole and healthy, it must be based on people''s love and concern for each other.', 'Millard Fuller');
INSERT INTO `[db_prefix]units` VALUES(3, 'We were born to unite with our fellow men, and to join in community with the human race.', 'Cicero');

-- injections
INSERT INTO `sys_injections` (`id`, `name`, `page_index`, `key`, `type`, `data`, `replace`, `active`) VALUES(NULL, 'quotes_injection', 0, 'injection_logo_after', 'php', 'return BxDolService::call("quotes", "get_quote_unit");', 0, 1);

-- admin menu
SET @iExtOrd = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT INTO `sys_menu_admin` (`id`, `parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) VALUES
(NULL, 2, 'Quotes', '_bx_Quotes', '{siteUrl}modules/?r=quotes/administration/', 'Quotes administration', 'mmi_host_tools.gif', '', '', @iExtOrd+1);