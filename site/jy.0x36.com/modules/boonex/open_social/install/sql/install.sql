-- create tables
CREATE TABLE `[db_prefix]_main` (
  `ID` int(11) unsigned NOT NULL auto_increment,
  `person_id` int(11) unsigned NOT NULL,
  `url` varchar(255) NOT NULL,
  `description` mediumtext NOT NULL,
  `status` enum('active','passive') NOT NULL default 'active',
  `title` varchar(128) NOT NULL,
  `directory_title` varchar(128) NOT NULL,
  `author` varchar(128) NOT NULL,
  `author_email` varchar(128) NOT NULL,
  `settings` mediumtext NOT NULL,
  `views` mediumtext NOT NULL,
  `version` varchar(64) NOT NULL,
  `height` int(11) NOT NULL default '0',
  `scrolling` int(11) NOT NULL default '0',
  `modified` int(11) NOT NULL,
  `screenshot` varchar(128) NOT NULL,
  `thumbnail` varchar(128) NOT NULL,
  PRIMARY KEY  (`ID`),
  KEY `ProfileID` (`person_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


SET @iAnyAdmin = (SELECT `ID` FROM `Profiles` WHERE `Role`='3' LIMIT 1);
INSERT INTO `bx_osi_main` (`ID`, `person_id`, `url`, `description`, `status`, `title`, `directory_title`, `author`, `author_email`, `settings`, `views`, `version`, `height`, `scrolling`, `modified`, `screenshot`, `thumbnail`) VALUES
(NULL, 0, 'http://www.matt.org/modules/GoogleClock.xml', 'Add a Clock to your page', 'active', 'Google Clock', '', 'Leonel Corona', 'leonel@matt.org', 'O:8:"stdClass":1:{s:5:"vsize";O:8:"stdClass":5:{s:11:"displayName";s:10:"Clock Size";s:4:"type";s:4:"ENUM";s:7:"default";s:5:"240px";s:10:"enumValues";O:8:"stdClass":4:{s:5:"100px";s:5:"100px";s:5:"150px";s:5:"150px";s:5:"200px";s:5:"200px";s:5:"240px";s:5:"240px";}s:8:"required";s:5:"false";}}', 'O:8:"stdClass":1:{s:4:"home";O:8:"stdClass":8:{s:4:"name";s:4:"home";s:4:"type";s:4:"HTML";s:4:"href";N;s:6:"quirks";b:1;s:4:"view";s:0:"";s:14:"preferedHeight";s:0:"";s:13:"preferedWidth";s:0:"";s:16:"rewrittenContent";N;}}', 'e62ab22d744e91b1e9a1f5b3d752716a', 0, 0, 1246430802, 'http://www.matt.org/modules/images/clockscreenshoot.jpg', 'http://www.matt.org/modules/images/googleclocktn.png'),
(NULL, 0, 'http://www.canbuffi.de/gadgets/clock/clock.xml', 'Adds a nice digital clock to your iGoogle. You can choose between different time formats (12/24h), different date formats (English, German and French date format), timezones and enable daylight savings. Until now it is translated into German, French, Croatian and Serbian; more translations will follow. Tags: Clock, Time, Date, Uhr, Uhrzeit, Datum', 'active', 'Clock & Date', 'Clock & Date', 'Sebastian Majstorovic', 'google@canbuffi.de', 'O:8:"stdClass":10:{s:5:"title";O:8:"stdClass":5:{s:11:"displayName";s:11:"Clock title";s:4:"type";s:6:"STRING";s:7:"default";s:12:"Clock & Date";s:10:"enumValues";N;s:8:"required";s:5:"false";}s:11:"time_format";O:8:"stdClass":5:{s:11:"displayName";s:11:"Time format";s:4:"type";s:4:"ENUM";s:7:"default";s:1:"0";s:10:"enumValues";a:2:{i:0;s:7:"1:15 PM";i:1;s:5:"13:15";}s:8:"required";s:5:"false";}s:7:"seconds";O:8:"stdClass":5:{s:11:"displayName";s:16:"Display seconds?";s:4:"type";s:4:"BOOL";s:7:"default";s:4:"true";s:10:"enumValues";N;s:8:"required";s:5:"false";}s:11:"date_format";O:8:"stdClass":5:{s:11:"displayName";s:11:"Date format";s:4:"type";s:4:"ENUM";s:7:"default";s:1:"0";s:10:"enumValues";a:4:{i:0;s:15:"January 1, 2000";i:1;s:15:"1. January 2000";i:2;s:15:"2000, January 1";i:3;s:14:"1 January 2000";}s:8:"required";s:5:"false";}s:9:"dayofweek";O:8:"stdClass":5:{s:11:"displayName";s:24:"Display day of the week?";s:4:"type";s:4:"BOOL";s:7:"default";s:4:"true";s:10:"enumValues";N;s:8:"required";s:5:"false";}s:12:"offset_hours";O:8:"stdClass":5:{s:11:"displayName";s:16:"Timezone (hours)";s:4:"type";s:4:"ENUM";s:7:"default";s:0:"";s:10:"enumValues";O:8:"stdClass":26:{s:3:"-12";s:6:"UTC-12";s:3:"-11";s:6:"UTC-11";s:3:"-10";s:6:"UTC-10";s:2:"-9";s:5:"UTC-9";s:2:"-8";s:5:"UTC-8";s:2:"-7";s:5:"UTC-7";s:2:"-6";s:5:"UTC-6";s:2:"-5";s:5:"UTC-5";s:2:"-4";s:5:"UTC-4";s:2:"-3";s:5:"UTC-3";s:2:"-2";s:5:"UTC-2";s:2:"-1";s:5:"UTC-1";s:7:"_empty_";s:3:"UTC";s:2:"+1";s:5:"UTC+1";s:2:"+2";s:5:"UTC+2";s:2:"+3";s:5:"UTC+3";s:2:"+4";s:5:"UTC+4";s:2:"+5";s:5:"UTC+5";s:2:"+6";s:5:"UTC+6";s:2:"+7";s:5:"UTC+7";s:2:"+8";s:5:"UTC+8";s:2:"+9";s:5:"UTC+9";s:3:"+10";s:6:"UTC+10";s:3:"+11";s:6:"UTC+11";s:3:"+12";s:6:"UTC+12";s:3:"+13";s:6:"UTC+13";}s:8:"required";s:5:"false";}s:14:"offset_minutes";O:8:"stdClass":5:{s:11:"displayName";s:18:"Timezone (minutes)";s:4:"type";s:4:"ENUM";s:7:"default";s:0:"";s:10:"enumValues";O:8:"stdClass":4:{s:7:"_empty_";s:0:"";s:2:"15";s:2:"15";s:2:"30";s:2:"30";s:2:"45";s:2:"45";}s:8:"required";s:5:"false";}s:8:"daylight";O:8:"stdClass":5:{s:11:"displayName";s:16:"Daylight savings";s:4:"type";s:4:"BOOL";s:7:"default";s:5:"false";s:10:"enumValues";N;s:8:"required";s:5:"false";}s:5:"color";O:8:"stdClass":5:{s:11:"displayName";s:5:"Color";s:4:"type";s:4:"ENUM";s:7:"default";s:3:"red";s:10:"enumValues";O:8:"stdClass":6:{s:3:"red";s:3:"Red";s:4:"blue";s:4:"Blue";s:5:"green";s:5:"Green";s:5:"brown";s:5:"Brown";s:6:"orange";s:6:"Orange";s:6:"purple";s:6:"Purple";}s:8:"required";s:5:"false";}s:6:"amazon";O:8:"stdClass":5:{s:11:"displayName";s:6:"Amazon";s:4:"type";s:6:"HIDDEN";s:7:"default";s:1:"1";s:10:"enumValues";N;s:8:"required";s:5:"false";}}', 'O:8:"stdClass":1:{s:4:"home";O:8:"stdClass":8:{s:4:"name";s:4:"home";s:4:"type";s:4:"HTML";s:4:"href";N;s:6:"quirks";b:1;s:4:"view";s:0:"";s:14:"preferedHeight";s:0:"";s:13:"preferedWidth";s:0:"";s:16:"rewrittenContent";N;}}', 'fe14b130b59f5333572a487b8029c9e7', 170, 0, 1246446031, 'http://www.canbuffi.de/gadgets/clock_date/images/screen.png', 'http://www.canbuffi.de/gadgets/clock_date/images/thumb.png');

CREATE TABLE `[db_prefix]_application_settings` (
  `application_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  `name` char(128) NOT NULL,
  `value` char(255) NOT NULL,
  UNIQUE KEY `application_id` (`application_id`,`person_id`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- admin menu
SET @iExtCat = (SELECT `id` FROM `sys_menu_admin` WHERE `name`='extensions');
SET @iExtOrd = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT INTO `sys_menu_admin` (`id`, `parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) VALUES
(NULL, 2, 'Opensocial', '_osi_Opensocial_moderation', '{siteUrl}modules/boonex/open_social/post_mod_os.php', 'Opensocial applications moderation', 'mmi_host_tools.gif', '', '', @iExtOrd+1);

-- page compose pages
SET @PageKey1 = (SELECT MAX(`order`) FROM `sys_page_compose` WHERE `Page` = 'profile' AND `Column`=2);
INSERT INTO `sys_page_compose` (`ID`, `Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES(NULL, 'profile', '998px', 'Custom Opensocial Feed block', '_osi_Custom_Feeds', 2, @PageKey1+1, 'PHP', 'return BxDolService::call(''open_social'', ''gen_custom_osi_block'', array($this->oProfileGen->_iProfileID));', 0, 66, 'non,memb', 0);

-- special page compose page
INSERT INTO `sys_page_compose` (`ID`, `Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
(NULL, '', '998px', 'Opensocial Block', '_osi_Opensocial_Block', 0, 0, 'Sample', 'XML', 1, 0, 'non,memb', 0);

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Opensocial integration', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('applications_auto_appr_bx_open_social', 'on', @iCategId, 'Activate all new applications from members after adding automatically', 'checkbox', '', '', '0', ''),
('users_can_upload_bx_open_social', 'on', @iCategId, 'Members able to upload new Opensocial applications', 'checkbox', '', '', '0', '');
