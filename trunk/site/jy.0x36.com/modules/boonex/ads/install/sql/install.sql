-- create tables
CREATE TABLE `[db_prefix]_rating` (
  `ads_id` int(12) NOT NULL default '0',
  `ads_rating_count` int(11) NOT NULL default '0',
  `ads_rating_sum` int(11) NOT NULL default '0',
  UNIQUE KEY `med_id` (`ads_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]_voting_track` (
  `ads_id` int(12) NOT NULL default '0',
  `ads_ip` varchar(20) default NULL,
  `ads_date` datetime default NULL,
  KEY `med_ip` (`ads_ip`,`ads_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]_category` (
  `ID` int(11) unsigned NOT NULL auto_increment,
  `Name` varchar(64) NOT NULL default '',
  `CEntryUri` varchar(64) NOT NULL default '',
  `Description` varchar(128) default NULL,
  `CustomFieldName1` varchar(50) default NULL,
  `CustomFieldName2` varchar(50) default NULL,
  `Unit1` varchar(8) NOT NULL default '$',
  `Unit2` varchar(8) NOT NULL,
  `Picture` varchar(255) NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `CEntryUri` (`CEntryUri`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `[db_prefix]_category` (`ID`, `Name`, `CEntryUri`, `Description`, `CustomFieldName1`, `CustomFieldName2`, `Unit1`, `Unit2`, `Picture`) VALUES 
(1, 'Jobs', 'Jobs', 'There is Jobs description', 'salary', 'age', '$', 'y/o', 'bx_aboutus.png'),
(2, 'Music Exchange', 'Music-Exchange', 'music exchange desc', 'price', 'year', '$', '', 'bx_music.png'),
(4, 'Housing & Rentals', 'Housing-_-Rentals', 'Housing & Rentals desc', 'rental', '', '$', '', 'bx_news.png'),
(5, 'Services', 'Services', 'Services desc', 'price', '', '$', '', 'bx_privacy.png'),
(7, 'Casting Calls', 'Casting-Calls', 'Casting Calls desc', 'price', '', '$', '', 'bx_contactus.png'),
(8, 'Personals', 'Personals', 'Personals desc', 'payment', 'age', '$', 'y/o', 'bx_groups.png'),
(9, 'For Sale', 'For-Sale', 'For Sale desc', 'price', 'year', '$', '', 'bx_ads.png'),
(10, 'Cars For Sale', 'Cars-For-Sale', 'Cars For Sale desc', 'price', 'year', '$', '', 'bx_search.png');


CREATE TABLE `[db_prefix]_main` (
  `ID` int(11) unsigned NOT NULL auto_increment,
  `IDProfile` int(11) unsigned NOT NULL default '0',
  `IDClassifiedsSubs` int(11) unsigned NOT NULL default '0',
  `DateTime` int(10) default NULL,
  `Subject` varchar(50) NOT NULL default '',
  `EntryUri` varchar(255) NOT NULL default '',
  `Message` text NOT NULL,
  `Status` enum('new','active','inactive') NOT NULL default 'new',
  `CustomFieldValue1` varchar(50) default NULL,
  `CustomFieldValue2` varchar(50) default NULL,
  `LifeTime` int(3) NOT NULL default '30',
  `Media` varchar(50) default NULL,
  `Tags` text NOT NULL,
  `Country` varchar(2) default NULL,
  `City` varchar(128) default NULL,
  `Featured` int(1) NOT NULL default '0',
  `Views` int(11) NOT NULL,
  `Rate` float NOT NULL,
  `RateCount` int(11) NOT NULL,
  `CommentsCount` int(11) NOT NULL,
  `AllowView` int(11) NOT NULL,
  `AllowRate` int(11) NOT NULL,
  `AllowComment` int(11) NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `EntryUri` (`EntryUri`),
  KEY `IDProfile` (`IDProfile`),
  KEY `IDClassifiedsSubs` (`IDClassifiedsSubs`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- FULLTEXT search
ALTER TABLE `[db_prefix]_main` ADD FULLTEXT KEY `ftMain` (`Subject`, `Tags`, `Message`, `City`);
ALTER TABLE `[db_prefix]_main` ADD FULLTEXT KEY `ftTags` (`Tags`);

CREATE TABLE `[db_prefix]_main_media` (
  `MediaID` int(11) unsigned NOT NULL auto_increment,
  `MediaProfileID` int(11) unsigned NOT NULL default '0',
  `MediaType` enum('photo','other') NOT NULL default 'photo',
  `MediaFile` varchar(50) NOT NULL default '',
  `MediaDate` int(10) default NULL,
  PRIMARY KEY  (`MediaID`),
  KEY `med_prof_id` (`MediaProfileID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]_category_subs` (
  `ID` int(11) unsigned NOT NULL auto_increment,
  `IDClassified` int(11) unsigned default NULL,
  `NameSub` varchar(128) NOT NULL default '',
  `SEntryUri` varchar(128) NOT NULL default '',
  `Description` varchar(150) default 'No description',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `SEntryUri` (`SEntryUri`),
  KEY `IDClassified` (`IDClassified`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `[db_prefix]_category_subs` VALUES(4, 2, 'positions and openings', 'positions-and-openings', 'positions and openings desc');
INSERT INTO `[db_prefix]_category_subs` VALUES(5, 2, 'instruments for sale', 'instruments-for-sale', 'instruments for sale desc');
INSERT INTO `[db_prefix]_category_subs` VALUES(6, 2, 'instruments wanted', 'instruments-wanted', 'instruments wanted desc');
INSERT INTO `[db_prefix]_category_subs` VALUES(7, 3, 'activities', 'activities', 'activities desc');
INSERT INTO `[db_prefix]_category_subs` VALUES(8, 3, 'artists', 'artists', 'artists desc');
INSERT INTO `[db_prefix]_category_subs` VALUES(9, 3, 'childcare', 'childcare', 'childcare desc');
INSERT INTO `[db_prefix]_category_subs` VALUES(10, 4, 'apartments / housing', 'apartments-housing', 'apartments / housing description');
INSERT INTO `[db_prefix]_category_subs` VALUES(11, 4, 'real estate for sale', 'real-estate-for-sale', 'real estate for sale description');
INSERT INTO `[db_prefix]_category_subs` VALUES(12, 4, 'roommates', 'roommates', 'roommates description');
INSERT INTO `[db_prefix]_category_subs` VALUES(38, 1, 'accounting / finance', 'accounting-finance', 'accounting / finance desc');
INSERT INTO `[db_prefix]_category_subs` VALUES(36, 5, 'automotive', 'automotive', 'automotive desc');
INSERT INTO `[db_prefix]_category_subs` VALUES(43, 1, 'education / nonprofit sec', 'education-nonprofit-sec', 'education / nonprofit sector desc');
INSERT INTO `[db_prefix]_category_subs` VALUES(47, 1, 'government / legal', 'government-legal', 'government/legal desc');
INSERT INTO `[db_prefix]_category_subs` VALUES(84, 1, 'programming / web design', 'programming-web-design', 'programming / web design desc');
INSERT INTO `[db_prefix]_category_subs` VALUES(54, 1, 'other', 'other', 'other desc');
INSERT INTO `[db_prefix]_category_subs` VALUES(55, 4, 'temporary vacation rental', 'temporary-vacation-rental', 'temporary vacation rentals desc');
INSERT INTO `[db_prefix]_category_subs` VALUES(56, 4, 'office / commercial', 'office-commercial', 'office / commercial  desc');
INSERT INTO `[db_prefix]_category_subs` VALUES(58, 5, 'financial', 'financial', 'financial');
INSERT INTO `[db_prefix]_category_subs` VALUES(60, 5, 'labor / move', 'labor-move', 'labor/move desc');
INSERT INTO `[db_prefix]_category_subs` VALUES(61, 5, 'legal', 'legal', 'legal desc');
INSERT INTO `[db_prefix]_category_subs` VALUES(62, 5, 'educational', 'educational', 'educational desc');
INSERT INTO `[db_prefix]_category_subs` VALUES(64, 7, 'acting', 'acting', 'acting desc');
INSERT INTO `[db_prefix]_category_subs` VALUES(65, 7, 'dance', 'dance', 'dance desc');
INSERT INTO `[db_prefix]_category_subs` VALUES(83, 7, 'musicians', 'musicians', 'musicians desc');
INSERT INTO `[db_prefix]_category_subs` VALUES(67, 7, 'modeling', 'modeling', 'modeling desc');
INSERT INTO `[db_prefix]_category_subs` VALUES(68, 7, 'reality shows', 'reality-shows', 'reality shows  desc');
INSERT INTO `[db_prefix]_category_subs` VALUES(69, 8, 'men seeking women', 'men-seeking-women', 'men seeking women desc');
INSERT INTO `[db_prefix]_category_subs` VALUES(70, 8, 'women seeking men', 'women-seeking-men', 'women seeking men desc');
INSERT INTO `[db_prefix]_category_subs` VALUES(71, 8, 'women seeking women', 'women-seeking-women', 'women seeking women desc');
INSERT INTO `[db_prefix]_category_subs` VALUES(72, 8, 'men seeking men', 'men-seeking-men', 'men seeking men desc');
INSERT INTO `[db_prefix]_category_subs` VALUES(73, 8, 'missed connections', 'missed-connections', 'missed connections desc');
INSERT INTO `[db_prefix]_category_subs` VALUES(74, 9, 'barter', 'barter', 'barter desc');
INSERT INTO `[db_prefix]_category_subs` VALUES(77, 9, 'clothing', 'clothing', 'clothing desc');
INSERT INTO `[db_prefix]_category_subs` VALUES(78, 9, 'collectibles', 'collectibles', 'collectibles desc');
INSERT INTO `[db_prefix]_category_subs` VALUES(79, 9, 'miscellaneous', 'miscellaneous', 'miscellaneous desc');
INSERT INTO `[db_prefix]_category_subs` VALUES(80, 10, 'autos / trucks', 'autos-trucks', 'autos / trucks desc');
INSERT INTO `[db_prefix]_category_subs` VALUES(81, 10, 'motorcycles', 'motorcycles', 'motorcycles desc');
INSERT INTO `[db_prefix]_category_subs` VALUES(82, 10, 'auto parts', 'auto-parts', 'auto parts desc');

CREATE TABLE `[db_prefix]_cmts` (
  `cmt_id` int(11) NOT NULL auto_increment,
  `cmt_parent_id` int(11) NOT NULL default '0',
  `cmt_object_id` int(11) NOT NULL default '0',
  `cmt_author_id` int(10) unsigned NOT NULL default '0',
  `cmt_text` text NOT NULL,
  `cmt_mood` tinyint NOT NULL default '0',
  `cmt_rate` int(11) NOT NULL default '0',
  `cmt_rate_count` int(11) NOT NULL default '0',
  `cmt_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `cmt_replies` int(11) NOT NULL default '0',
  PRIMARY KEY  (`cmt_id`),
  KEY `cmt_object_id` (`cmt_object_id`,`cmt_parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]_views_track` (
  `id` int(10) unsigned NOT NULL,
  `viewer` int(10) unsigned NOT NULL,
  `ip` int(10) unsigned NOT NULL,
  `ts` int(10) unsigned NOT NULL,
  KEY `id` (`id`,`viewer`,`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- PQ statistics
INSERT INTO `sys_account_custom_stat_elements` (`ID`, `Label`, `Value`) VALUES(NULL, '_bx_ads_Ads', '__mad__ __l_bx_ads_Ads__ (<a href="__site_url__ads/my_page/add/">__l_Post__</a>)');

-- admin menu
SET @iExtOrd = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT INTO `sys_menu_admin` (`id`, `parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) VALUES (NULL, 2, 'Ads', 'Ads', '{siteUrl}modules/boonex/ads/post_mod_ads.php', 'Administrator can manage ads categories, subcategories, etc.', 'modules/boonex/ads/|ads.png', '', '', @iExtOrd+1);

-- comments objects
INSERT INTO `sys_objects_cmts` (`ID`, `ObjectName`, `TableCmts`, `TableTrack`, `AllowTags`, `Nl2br`, `SecToEdit`, `PerView`, `IsRatable`, `ViewingThreshold`, `AnimationEffect`, `AnimationSpeed`, `IsOn`, `IsMood`, `RootStylePrefix`, `TriggerTable`, `TriggerFieldId`, `TriggerFieldComments`, `ClassName`, `ClassFile`) VALUES(NULL, 'ads', '[db_prefix]_cmts', 'sys_cmts_track', 0, 1, 90, 5, 1, -3, 'slide', 2000, 1, 1, 'cmt', '[db_prefix]_main', 'ID', 'CommentsCount', '', '');

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` VALUES(NULL, 'Ads', @iMaxOrder);
SET @iGlCategID = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`) VALUES
('bx_ads_max_live_days', '30', @iGlCategID, 'How long can Classifieds live (days)', 'digit', '', '', NULL),
('bx_ads_enable_paid', 'on', @iGlCategID, 'Enable Ability to work with Buy Now button in Ads', 'checkbox', '', '', NULL),
('bx_ads_auto_approving', 'on', @iGlCategID, 'Automatic advertisements activation after adding', 'checkbox', '', '', NULL),
('permalinks_module_ads', 'on', 26, 'Enable friendly ads permalink', 'checkbox', '', '', 0);

-- page compose pages
SET @iPCPOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES('ads', 'Ads', @iPCPOrder+1);

INSERT INTO `sys_page_compose` (`ID`, `Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
(NULL, 'index', '998px', 'Classifieds', '_bx_ads_Ads', 2, 5, 'PHP', 'return BxDolService::call(''ads'', ''ads_index_page'');', 1, 34, 'non,memb', 0),
(NULL, 'member', '998px', 'Classifieds', '_bx_ads_Ads', 0, 0, 'PHP', 'return BxDolService::call(''ads'', ''ads_profile_page'', array($this->iMember));', 1, 34, 'non,memb', 0),
(NULL, 'profile', '998px', 'Classifieds', '_bx_ads_Ads', 1, 2, 'PHP', 'return BxDolService::call(''ads'', ''ads_profile_page'', array($this->oProfileGen->_iProfileID));', 1, 34, 'non,memb', 0),
(NULL, 'ads', '998px', '', '_Description', 2, 0, 'AdDescription', '', 1, 66, 'non,memb', 0),
(NULL, 'ads', '998px', '', '_bx_ads_Custom_Values', 2, 1, 'AdCustomInfo', '', 1, 66, 'non,memb', 0),
(NULL, 'ads', '998px', '', '_bx_ads_Ad_photos', 2, 2, 'AdPhotos', '', 1, 66, 'non,memb', 0),
(NULL, 'ads', '998px', '', '_Comments', 2, 3, 'ViewComments', '', 1, 66, 'non,memb', 0),
(NULL, 'ads', '998px', '', '_Actions', 1, 0, 'ActionList', '', 1, 34, 'non,memb', 0),
(NULL, 'ads', '998px', '', '_Info', 1, 1, 'AdInfo', '', 1, 34, 'non,memb', 0),
(NULL, 'ads', '998px', '', '_Rate', 1, 2, 'Rate', '', 1, 34, 'non,memb', 0),
(NULL, 'ads', '998px', '', '_bx_ads_Users_other_listing', 1, 3, 'UserOtherAds', '', 1, 34, 'non,memb', 0);

-- PQ statistics
INSERT INTO `sys_stat_member` (`Type`, `SQL`) VALUES('mad', 'SELECT COUNT(*) FROM `[db_prefix]_main` WHERE `IDProfile` = ''__member_id__'' AND `Status`=''active''');

-- site stats
INSERT INTO `sys_stat_site` (`ID`, `Name`, `Title`, `UserLink`, `UserQuery`, `AdminLink`, `AdminQuery`, `IconName`, `StatOrder`) VALUES(NULL, 'cls', 'bx_ads_Ads', 'modules/boonex/ads/classifieds.php?Browse=1', 'SELECT COUNT(`ID`) FROM `[db_prefix]_main` WHERE `Status`=''active'' AND UNIX_TIMESTAMP() - `[db_prefix]_main`.`LifeTime`*24*60*60 < `[db_prefix]_main`.`DateTime`', '', '', 'modules/boonex/ads/|ads.png', 0);

-- search objects
INSERT INTO `sys_objects_search` (`ID`, `ObjectName`, `Title`, `ClassName`, `ClassPath`) VALUES(NULL, 'bx_ads', '_bx_ads_Ads', 'BxAdsSearchUnit', 'modules/boonex/ads/classes/BxAdsSearchUnit.php');

-- tag objects
INSERT INTO `sys_objects_tag` (`ID`, `ObjectName`, `Query`, `PermalinkParam`, `EnabledPermalink`, `DisabledPermalink`, `LangKey`) VALUES(NULL, 'ad', 'SELECT `Tags` FROM `[db_prefix]_main` WHERE `ID` = {iID} AND `status` = ''active''', 'permalinks_module_ads', 'ads/tag/{tag}', 'modules/boonex/ads/classifieds_tags.php?tag={tag}', '_bx_ads_Ads');

-- top menu
SET @iTopMenuLastOrder := (SELECT `Order` + 1 FROM `sys_menu_top` WHERE `Parent` = 0 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `BQuickLink`, `Statistics`) VALUES(NULL, 0, 'Ads', '_bx_ads_Ads', 'modules/boonex/ads/classifieds.php?Browse=1|modules/boonex/ads/classifieds.php?action=show_calendar_ads&date=|modules/boonex/ads/classifieds_tags.php?tag=|ads/tag/|modules/boonex/ads/classifieds.php?bClassifiedID=|ads/cat/|modules/boonex/ads/classifieds.php?bSubClassifiedID=|ads/subcat/', @iTopMenuLastOrder, 'non,memb', '', '', '', 1, 1, 1, 'top', 'modules/boonex/ads/|bx_ads.png', 1, '');
SET @menu_id = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `BQuickLink`, `Statistics`) VALUES
(NULL, 9, 'Profile Ads', '_bx_ads_Ads', 'modules/boonex/ads/classifieds.php?UsersOtherListing=1&IDProfile={profileID}', 4, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/ads/|bx_ads.png', 0, ''),
(NULL, 4, 'Profile Ads', '_bx_ads_Ads', 'modules/boonex/ads/classifieds.php?action=my_page|ads/my_page/', 4, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/ads/|bx_ads.png', 0, ''),
(NULL, @menu_id, 'Ads Calendar', '_bx_ads_Calendar', 'modules/boonex/ads/classifieds.php?action=show_calendar', 6, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/ads/|bx_ads.png', 0, ''),
(NULL, @menu_id, 'Ads Categories', '_bx_ads_Categories', 'modules/boonex/ads/classifieds.php?action=show_categories', 5, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/ads/|bx_ads.png', 0, ''),
(NULL, @menu_id, 'Popular Ads', '_bx_ads_Popular', 'modules/boonex/ads/classifieds.php?action=show_popular', 3, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/ads/|bx_ads.png', 0, ''),
(NULL, @menu_id, 'Ads Tags', '_Tags', 'modules/boonex/ads/classifieds.php?action=tags', 4, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', 0, ''),
(NULL, @menu_id, 'Top Rated Ads', '_bx_ads_Top_Rated', 'modules/boonex/ads/classifieds.php?action=show_top_rated', 2, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/ads/|bx_ads.png', 0, ''),
(NULL, @menu_id, 'Ads Search', '_Search', 'searchKeyword.php?type=bx_ads|modules/boonex/ads/classifieds.php?FilterCountry=', 8, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/ads/|bx_ads.png', 0, ''),
(NULL, @menu_id, 'Ads Featured', '_bx_ads_Featured', 'modules/boonex/ads/classifieds.php?action=show_featured', 7, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/ads/|bx_ads.png', 0, ''),
(NULL, @menu_id, 'All Ads', '_bx_ads_All_ads', 'modules/boonex/ads/classifieds.php?action=show_all_ads', 1, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/ads/|bx_ads.png', 0, ''),
(NULL, @menu_id, 'Ads Home', '_bx_ads_Ads_Home', 'modules/boonex/ads/classifieds.php?Browse=1', 0, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/ads/|bx_ads.png', 0, ''),
(NULL, 0, 'bx_ads_view', '_bx_ads_Ads', 'modules/boonex/ads/classifieds.php?ShowAdvertisementID=|ads/entry/', 0, 'non,memb', '', '', '', 1, 1, 1, 'system', '', 0, '');

-- vote objects
INSERT INTO `sys_objects_vote` (`ID`, `ObjectName`, `TableRating`, `TableTrack`, `RowPrefix`, `MaxVotes`, `PostName`, `IsDuplicate`, `IsOn`, `className`, `classFile`, `TriggerTable`, `TriggerFieldRate`, `TriggerFieldRateCount`, `TriggerFieldId`, `OverrideClassName`, `OverrideClassFile`) VALUES(NULL, 'ads', '[db_prefix]_rating', '[db_prefix]_voting_track', 'ads_', 5, 'vote_send_result', 'BX_PERIOD_PER_VOTE', 1, '', '', '[db_prefix]_main', 'Rate', 'RateCount', 'ID', '', '');

-- permalinks
INSERT INTO `sys_permalinks`(`standard`, `permalink`, `check`) VALUES
('modules/boonex/ads/classifieds.php?Browse=1', 'ads/', 'permalinks_module_ads'),
('modules/boonex/ads/classifieds.php?action=show_calendar', 'ads/calendar/', 'permalinks_module_ads'),
('modules/boonex/ads/classifieds.php?action=show_categories', 'ads/categories/', 'permalinks_module_ads'),
('modules/boonex/ads/classifieds.php?action=tags', 'ads/tags/', 'permalinks_module_ads'),
('modules/boonex/ads/classifieds.php?action=show_all_ads', 'ads/all_ads/', 'permalinks_module_ads'),
('modules/boonex/ads/classifieds.php?action=show_top_rated', 'ads/top_ads/', 'permalinks_module_ads'),
('modules/boonex/ads/classifieds.php?action=show_popular', 'ads/popular_ads/', 'permalinks_module_ads'),
('modules/boonex/ads/classifieds.php?action=show_featured', 'ads/featured_ads/', 'permalinks_module_ads'),
('modules/boonex/ads/classifieds.php?action=my_page', 'ads/my_page/', 'permalinks_module_ads');

INSERT INTO `sys_permalinks`(`standard`, `permalink`, `check`) VALUES
('modules/?r=ads/search/', 'm/ads/search/', 'permalinks_module_ads');

-- Alerts Handler and Events
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'bx_ads_profile_delete', '', '', 'BxDolService::call(''ads'', ''response_profile_delete'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'profile', 'delete', @iHandler);

-- views objects
INSERT INTO `sys_objects_views` VALUES(NULL, 'ads', '[db_prefix]_views_track', 86400, '[db_prefix]_main', 'ID', 'Views', 1);

-- Membership
SET @iLevelNonMember := 1;
SET @iLevelStandard := 2;
SET @iLevelPromotion := 3;

INSERT INTO `sys_acl_actions` VALUES (NULL, 'ads view', NULL);
SET @iAction = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'ads browse', NULL);
SET @iAction = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'ads search', NULL);
SET @iAction = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'ads add', NULL);
SET @iAction = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'ads edit any ad', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'ads delete any ad', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'ads approving', NULL);

-- member menu
INSERT INTO `sys_menu_member` SET `Name` = '[db_prefix]', `Eval` = 'require_once( BX_DIRECTORY_PATH_ROOT . ''modules/boonex/ads/ads_get_member_menu_list.php'' );\r\nreturn getMemberMenuAdsList({ID});', `Parent` = '1', `Type` = 'linked_item';

-- privacy
INSERT INTO `sys_privacy_actions` (`module_uri`, `name`, `title`, `default_group`) VALUES
('ads', 'comment', '_bx_ads_privacy_comment', '3'),
('ads', 'rate', '_bx_ads_privacy_rate', '3'),
('ads', 'view', '_bx_ads_privacy_view', '3');

-- actions
INSERT INTO `sys_objects_actions` (`ID`, `Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`, `bDisplayInSubMenuHeader`) VALUES
(NULL, '_bx_ads_Add', 'action_fave.png', '{evalResult}', '', 'if ({only_menu} == 1)\r\n    if (getParam(''permalinks_module_ads'') == ''on'') return ''ads/my_page/add/'';\r\n    else return ''modules/boonex/ads/classifieds.php?action=my_page&mode=add'';\r\nelse\r\n    return null;\r\n', 1, '[db_prefix]', 1),
(NULL, '_bx_ads_My_Ads', 'tux.png', '{evalResult}', '', 'if ({only_menu} == 1)\r\n    if (getParam(''permalinks_module_ads'') == ''on'') return ''ads/my_page/'';\r\n    else return ''modules/boonex/ads/classifieds.php?action=my_page'';\r\nelse\r\n    return null;\r\n', 2, '[db_prefix]', 1),
(NULL, '_bx_ads_Ads_Home', 'modules/boonex/ads/|ads.png', '{evalResult}', '', 'if ({only_menu} == 1)\r\n    if (getParam(''permalinks_module_ads'') == ''on'') return ''ads/'';\r\n    else return ''modules/boonex/ads/classifieds.php?Browse=1'';\r\nelse\r\n    return null;\r\n', 3, '[db_prefix]', 1),
(NULL, '{evalResult}', 'cool.gif', '', 'document.forms[''BuyNowForm''].submit();', '$bBnp = getParam(''bx_ads_enable_paid'');\r\nif ({visitor_id} > 0 && {visitor_id} != {owner_id} && $bBnp==''on'') {\r\nreturn _t(''_bx_ads_Buy_Now'');\r\n}\r\nelse\r\nreturn null;', 4, '[db_prefix]', 0),
(NULL, '{evalResult}', 'action_send.png', '', 'document.forms[''post_pm''].submit();', 'if ({visitor_id} > 0 && {visitor_id} != {owner_id}) {\r\nreturn _t(''_Send Message'');\r\n}\r\nelse\r\nreturn null;', 5, '[db_prefix]', 0),
(NULL, '_Edit', 'edit.png', '{evalResult}', '', 'if (({visitor_id} > 0 && {visitor_id} == {owner_id}) || ({admin_mode}==true)) {\r\n    return (getParam(''permalinks_module_ads'') == ''on'') ? ''ads/my_page/edit/{ads_id}'' : ''modules/boonex/ads/classifieds.php?action=my_page&mode=add&EditPostID={ads_id}'';\r\n} else return null;', 6, '[db_prefix]', 0),
(NULL, '{evalResult}', 'action_block.png', '', 'iDelAdID = {ads_id}; if (confirm(''{sure_label}'')) { $(''#DeleteAdvertisementID'').val(iDelAdID);document.forms.command_delete_advertisement.submit(); }', '$oModule = BxDolModule::getInstance(''BxAdsModule'');\r\n if (({visitor_id} > 0 && {visitor_id} == {owner_id}) || ({admin_mode}==true) || $oModule->isAllowedDelete({owner_id})) {\r\nreturn _t(''_Delete'');\r\n}\r\nelse\r\nreturn null;', 7, '[db_prefix]', 0),
(NULL, '_bx_ads_RSS', 'rss.png', 'rss_factory.php?action=ads&pid={owner_id}', '', '', 8, '[db_prefix]', 0),
(NULL, '{evalResult}', 'action_greet.png', '', '$(''#ActivateAdvertisementID'').val(''{ads_id}'');document.forms.command_activate_advertisement.submit(); return false;', 'if ({admin_mode}==true && ''{ads_status}''!=''active'') {\r\nreturn _t(''_bx_ads_Activate'');\r\n}\r\nelse\r\nreturn null;', 9, '[db_prefix]', 0),
(NULL, '{evalResult}', 'action_fave.png', '{ads_entry_url}&do=cfs', '', '$iAdsFeature = (int)''{ads_featured}'';\r\nif ({admin_mode}==true) {\r\nreturn ($iAdsFeature==1) ? _t(''_bx_ads_De-Feature_it'') : _t(''_bx_ads_Feature_it'');\r\n}\r\nelse\r\nreturn null;', 10, '[db_prefix]', 0),
(NULL, '{sbs_ads_title}', 'action_subscribe.png', '', '{sbs_ads_script}', '', 11, '[db_prefix]', 0);

-- subscriptions
INSERT INTO `sys_sbs_types`(`unit`, `action`, `template`, `params`) VALUES
('bx_ads', '', '', 'return BxDolService::call(''ads'', ''get_subscription_params'', array($arg2, $arg3));'),
('bx_ads', 'commentPost', 't_sbsAdsComments', 'return BxDolService::call(''ads'', ''get_subscription_params'', array($arg2, $arg3));'),
('bx_ads', 'rate', 't_sbsAdsRates', 'return BxDolService::call(''ads'', ''get_subscription_params'', array($arg2, $arg3));');

-- email templates
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('t_sbsAdsComments', 'New Ad comments', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p><b>Dear <RealName></b>,</p><br /><p>The ad you subscribed to has new comments!</p><br /> <p>Click <a href="<ViewLink>">here</a> to view them.</p><br /> <p><b>Thank you for using our services!</b></p> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'New ad comments subscription.', '0'),
('t_sbsAdsRates', 'Ad was rated', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p><b>Dear <RealName></b>,</p><br /><p>The ad you subscribed to was rated!</p><br /> <p>Click <a href="<ViewLink>">here</a> to view it.</p><br /> <p><b>Thank you for using our services!</b></p> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'New ad rates subscription.', '0');

INSERT INTO `sys_email_templates` VALUES(NULL, 't_BuyNow', 'You have purchased an item', '<html><head></head><body style="font: 12px Verdana; color:#000000">\r\n<div style="border:1px solid #CCCCCC;">\r\n<div style="color:#666666; font-weight:bold; height:23px; padding:3px 0px 0px 6px; text-transform:uppercase;">\r\nCongratulations! <String1>.\r\n</div>\r\n<div style="padding:3px 3px 10px;">\r\nItem title: <Subject><br/>\r\nSeller`s Name: <NickName><br/>\r\nSeller`s email: <EmailS><br/><br/>\r\nBuyer Name: <NickNameB><br/>\r\nBuyer email: <EmailB><br/><br/>\r\nPrice details: <sCustDetails><br/><br/>\r\nLink to Item<br/>\r\n<a href="<ShowAdvLnk>"><ShowAdvLnk></a><br/><br/>\r\nContact the <Who> directly to arrange payment and delivery. To avoid fraud, we recommend dealing locally, avoiding Western Union and wire transfers.<br/><br/>\r\nThank you for using our site,<br/>\r\nP.S. If you ever need support or have comments for us, e-mail us at <site_email></div></div>\r\n</body></html>', 'BuyNow notification letter template for Buyer', 0);
INSERT INTO `sys_email_templates` VALUES(NULL, 't_BuyNow', 'You have purchased an item', '<html><head></head><body style="font: 12px Verdana; color:#000000">\r\n<div style="border:1px solid #CCCCCC;">\r\n<div style="color:#666666; font-weight:bold; height:23px; padding:3px 0px 0px 6px; text-transform:uppercase;">\r\nCongratulations! <String1>.\r\n</div>\r\n<div style="padding:3px 3px 10px;">\r\nItem title: <Subject><br/>\r\nSeller`s Name: <NickName><br/>\r\nSeller`s email: <EmailS><br/><br/>\r\nBuyer Name: <NickNameB><br/>\r\nBuyer email: <EmailB><br/><br/>\r\nPrice details: <sCustDetails><br/><br/>\r\nLink to Item<br/>\r\n<a href="<ShowAdvLnk>"><ShowAdvLnk></a><br/><br/>\r\nContact the <Who> directly to arrange payment and delivery. To avoid fraud, we recommend dealing locally, avoiding Western Union and wire transfers.<br/><br/>\r\nThank you for using our site,<br/>\r\nP.S. If you ever need support or have comments for us, e-mail us at <site_email></div></div>\r\n</body></html>', 'BuyNow notification letter template for Buyer', 1);
INSERT INTO `sys_email_templates` VALUES(NULL, 't_BuyNowS', 'An item offered by you  has been purchased', '<html><head></head><body style="font: 12px Verdana; color:#000000">\r\n<div style="border:1px solid #CCCCCC;">\r\n<div style="color:#666666; font-weight:bold; height:23px; padding:3px 0px 0px 6px; text-transform:uppercase;">\r\nCongratulations! <String1>.\r\n</div>\r\n<div style="padding:3px 3px 10px;">\r\nItem title: <Subject><br/>\r\nSeller`s Name: <NickName><br/>\r\nSeller`s email: <EmailS><br/><br/>\r\nBuyer Name: <NickNameB><br/>\r\nBuyer email: <EmailB><br/><br/>\r\nPrice details: <sCustDetails><br/><br/>\r\nLink to Item<br/>\r\n<a href="<ShowAdvLnk>"><ShowAdvLnk></a><br/><br/>\r\nContact the <Who> directly to arrange payment and delivery. To avoid fraud, we recommend dealing locally, avoiding Western Union and wire transfers.<br/><br/>\r\nThank you for using our site,<br/>\r\nP.S. If you ever need support or have comments for us, e-mail us at <site_email></div></div>\r\n</body></html>', 'BuyNow notification letter template for Seller', 0);
INSERT INTO `sys_email_templates` VALUES(NULL, 't_BuyNowS', 'An item offered by you  has been purchased', '<html><head></head><body style="font: 12px Verdana; color:#000000">\r\n<div style="border:1px solid #CCCCCC;">\r\n<div style="color:#666666; font-weight:bold; height:23px; padding:3px 0px 0px 6px; text-transform:uppercase;">\r\nCongratulations! <String1>.\r\n</div>\r\n<div style="padding:3px 3px 10px;">\r\nItem title: <Subject><br/>\r\nSeller`s Name: <NickName><br/>\r\nSeller`s email: <EmailS><br/><br/>\r\nBuyer Name: <NickNameB><br/>\r\nBuyer email: <EmailB><br/><br/>\r\nPrice details: <sCustDetails><br/><br/>\r\nLink to Item<br/>\r\n<a href="<ShowAdvLnk>"><ShowAdvLnk></a><br/><br/>\r\nContact the <Who> directly to arrange payment and delivery. To avoid fraud, we recommend dealing locally, avoiding Western Union and wire transfers.<br/><br/>\r\nThank you for using our site,<br/>\r\nP.S. If you ever need support or have comments for us, e-mail us at <site_email></div></div>\r\n</body></html>', 'BuyNow notification letter template for Seller', 1);

