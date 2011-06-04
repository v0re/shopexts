
-- create tables
CREATE TABLE IF NOT EXISTS `[db_prefix]main` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `url` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `entryUri` varchar(255) NOT NULL,
  `description` text NOT NULL default '',
  `status` enum('approved','pending') NOT NULL default 'approved',
  `photo` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `ownerid` int(10) unsigned NOT NULL default '0',
  `allowView` int(11) NOT NULL,
  `allowComments` int(11) NOT NULL default '0',
  `allowRate` int(11) NOT NULL default '0',
  `tags` varchar(255) NOT NULL default '',
  `categories` text NOT NULL,
  `views` int(11) NOT NULL default 0,
  `rate` float NOT NULL default 0,
  `rateCount` int(11) NOT NULL default 0,
  `commentsCount` int(11) NOT NULL,
  `featured` tinyint(4) NOT NULL default 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `entryUri` (`entryUri`),
  KEY `date` (`date`),
  FULLTEXT KEY `title` (`title`,`description`,`tags`,`categories`),
  FULLTEXT KEY `tags` (`tags`),
  FULLTEXT KEY `categories` (`categories`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]rating` (
  `sites_id` smallint( 6 ) NOT NULL default '0',
  `sites_rating_count` int( 11 ) NOT NULL default '0',
  `sites_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `sites_id` (`sites_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]rating_track` (
  `sites_id` smallint( 6 ) NOT NULL default '0',
  `sites_ip` varchar( 20 ) default NULL,
  `sites_date` datetime default NULL,
  KEY `sites_ip` (`sites_ip`, `sites_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]cmts` (
  `cmt_id` int( 11 ) NOT NULL AUTO_INCREMENT ,
  `cmt_parent_id` int( 11 ) NOT NULL default '0',
  `cmt_object_id` int( 12 ) NOT NULL default '0',
  `cmt_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_text` text NOT NULL ,
  `cmt_mood` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate` int( 11 ) NOT NULL default '0',
  `cmt_rate_count` int( 11 ) NOT NULL default '0',
  `cmt_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `cmt_replies` int( 11 ) NOT NULL default '0',
  PRIMARY KEY ( `cmt_id` ),
  KEY `cmt_object_id` (`cmt_object_id` , `cmt_parent_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]views_track` (
  `id` int(10) unsigned NOT NULL,
  `viewer` int(10) unsigned NOT NULL,
  `ip` int(10) unsigned NOT NULL,
  `ts` int(10) unsigned NOT NULL,
  KEY `id` (`id`,`viewer`,`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- top menu
SET @iMaxMenuOrder := (SELECT `Order` + 1 FROM `sys_menu_top` WHERE `Parent` = 0 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top`(`Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES 
(0, 'Sites', '_bx_sites', 'modules/?r=sites/view/', '', 'non,memb', '', '', '', 0, 0, 1, 'system', 'modules/boonex/sites/|bx_sites.png', '', 0, '');

INSERT INTO `sys_menu_top`(`Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(0, 'Sites', '_bx_sites', 'modules/?r=sites/home/|modules/?r=sites/', @iMaxMenuOrder, 'non,memb', '', '', '', 1, 1, 1, 'top', 'modules/boonex/sites/|bx_sites.png', '', 1, '');
SET @iCatRoot := LAST_INSERT_ID();

INSERT INTO `sys_menu_top`(`Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(@iCatRoot, 'SitesHome', '_bx_sites_home_top_menu_sitem', 'modules/?r=sites/home/', 0, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/sites/|bx_sites.png', '', 0, ''),
(@iCatRoot, 'SitesAll', '_bx_sites_all_top_menu_sitem', 'modules/?r=sites/browse/all', 1, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/sites/|bx_sites.png', '', 0, ''),
(@iCatRoot, 'SitesAdmin', '_bx_sites_admin_top_menu_sitem', 'modules/?r=sites/browse/admin', 2, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/sites/|bx_sites.png', '', 0, ''),
(@iCatRoot, 'SitesUsers', '_bx_sites_profile_top_menu_sitem', 'modules/?r=sites/browse/users', 3, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/sites/|bx_sites.png', '', 0, ''),
(@iCatRoot, 'SitesTop', '_bx_sites_top_top_menu_sitem', 'modules/?r=sites/browse/top', 4, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/sites/|bx_sites.png', '', 0, ''),
(@iCatRoot, 'SitesPopular', '_bx_sites_popular_top_menu_sitem', 'modules/?r=sites/browse/popular', 5, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/sites/|bx_sites.png', '', 0, ''),
(@iCatRoot, 'SitesFeatured', '_bx_sites_featured_top_menu_sitem', 'modules/?r=sites/browse/featured', 8, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/sites/|bx_sites.png', '', 0, 'bx_sites'),
(@iCatRoot, 'SitesTags', '_bx_sites_tags_top_menu_sitem', 'modules/?r=sites/tags', 9, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/sites/|bx_sites.png', '', 0, 'bx_sites'),
(@iCatRoot, 'SitesCategories', '_bx_sites_categories_top_menu_sitem', 'modules/?r=sites/categories', 10, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/sites/|bx_sites.png', '', 0, ''),
(@iCatRoot, 'SitesHoN', '_bx_sites_hon_top_menu_sitem', 'modules/?r=sites/hon', 11, 'memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/sites/|bx_sites.png', '', 0, ''),
(@iCatRoot, 'SitesCalendar', '_bx_sites_calendar_top_menu_sitem', 'modules/?r=sites/calendar', 12, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/sites/|bx_sites.png', '', 0, ''),
(@iCatRoot, 'SitesSearch', '_bx_sites_search_top_menu_sitem', 'modules/?r=sites/search', 13, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/sites/|bx_sites.png', '', 0, '');

SET @iCatProfileOrder := (SELECT MAX(`Order`)+1 FROM `sys_menu_top` WHERE `Parent` = 9 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top`(`Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(9, 'Sites', '_bx_sites_menu_my_sites_profile', 'modules/?r=sites/browse/user/{profileNick}', @iCatProfileOrder, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
SET @iCatProfileOrder := (SELECT MAX(`Order`)+1 FROM `sys_menu_top` WHERE `Parent` = 4 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top`(`Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(4, 'Sites', '_bx_sites_menu_my_sites_profile', 'modules/?r=sites/browse/my', @iCatProfileOrder, 'memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');

INSERT INTO `sys_permalinks`(`standard`, `permalink`, `check`) VALUES('modules/?r=sites/', 'm/sites/', 'bx_sites_permalinks');

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Sites', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('bx_sites_permalinks', 'on', 26, 'Enable friendly permalinks in sites', 'checkbox', '', '', 0, ''),
('bx_sites_autoapproval', 'on', @iCategId, 'Activate all sites after creation automatically', 'checkbox', '', '', 1, ''),
('bx_sites_comments', 'on', @iCategId, 'Allow comments for sites', 'checkbox', '', '', 2, ''),
('bx_sites_votes', 'on', @iCategId, 'Allow votes for sites', 'checkbox', '', '', 3, ''),
('bx_sites_per_page', '10', @iCategId, 'The number of items shown on the page', 'digit', '', '', 4, ''),
('bx_sites_max_rss_num', '10', @iCategId, 'Max number of rss items to provide', 'digit', '', '', 5, ''),
('category_auto_app_bx_sites', 'on', @iCategId, 'Activate all categories for all sites after creation automatically', 'checkbox', '', '', 6, ''),
('bx_sites_thumb_url', 'http://images.shrinktheweb.com/xino.php', @iCategId, 'Url', 'digit', '', '', 7, ''),
('bx_sites_thumb_service', 'ShrinkWebUrlThumbnail', @iCategId, 'Name of service', 'digit', '', '', 8, ''),
('bx_sites_thumb_action', 'Thumbnail', @iCategId, 'Name of action', 'digit', '', '', 9, ''),
('bx_sites_thumb_access_key', '', @iCategId, 'Access Key', 'digit', '', '', 10, ''),
('bx_sites_thumb_pswd', '', @iCategId, 'Secret Key', 'digit', '', '', 11, '');

-- page compose
SET @iPCPOrder := (SELECT MAX(`Order`) FROM `sys_page_compose_pages`);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES 
('bx_sites_main', 'Main Sites Page', @iPCPOrder + 1),
('bx_sites_profile', 'Profile Sites Page', @iPCPOrder + 2),
('bx_sites_view', 'Site View Page', @iPCPOrder + 3),
('bx_sites_hon', 'Site HoN Page', @iPCPOrder + 4);

-- index page
SET @iPCOrder := (SELECT MAX(`Order`) FROM `sys_page_compose` WHERE `Page`='index' AND `Column`='1');
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
('index', '998px', 'Show list of latest sites', '_bx_sites_bcaption_latest', 1, @iPCOrder + 1, 'PHP', 'return BxDolService::call(\'sites\', \'index_block\');', 1, 66, 'non,memb', 0);

-- profile page
SET @iPCOrder := (SELECT MAX(`Order`) FROM `sys_page_compose` WHERE `Page`='profile' AND `Column`='2');
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
('profile', '998px', 'Show list profile sites', '_bx_sites', 2, @iPCOrder + 1, 'PHP', 'return BxDolService::call(\'sites\', \'profile_block\', array($this->oProfileGen->_aProfile[\'NickName\']));', 1, 66, 'non,memb', 0);

INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
('bx_sites_main', '998px', 'View public feature', '_bx_sites_caption_public_feature', 1, 0, 'ViewFeature', '', 1, 34, 'non,memb', 0),
('bx_sites_main', '998px', 'View recently public site', '_bx_sites_caption_public_last_featured', 2, 0, 'ViewRecent', '', 1, 66, 'non,memb', 0),
('bx_sites_main', '998px', 'View latest public sites', '_bx_sites_caption_public_latest', 2, 1, 'ViewAll', '', 1, 66, 'non,memb', 0),
('bx_sites_profile', '998px', 'Administration', '_bx_sites_bcaption_administration', 1, 0, 'Administration', '', 1, 100, 'non,memb', 0),
('bx_sites_profile', '998px', 'Owner Sites', '_bx_sites_bcaption_owner_sites', 1, 1, 'Owner', '', 1, 100, 'non,memb', 0),

('bx_sites_view', '998px', 'Actions for Site', '_bx_sites_bcaption_actions', 1, 0, 'ViewActions', '', 1, 34, 'non,memb', 0),
('bx_sites_view', '998px', 'Information on Site', '_bx_sites_bcaption_information', 1, 1, 'ViewInformation', '', 1, 34, 'non,memb', 0),

('bx_sites_view', '998px', 'Image Site', '_bx_sites_bcaption_image', 2, 0, 'ViewImage', '', 1, 66, 'non,memb', 0),
('bx_sites_view', '998px', 'Description Site', '_bx_sites_bcaption_description', 2, 1, 'ViewDescription', '', 1, 66, 'non,memb', 0),
('bx_sites_view', '998px', 'Comments for Site', '_bx_sites_bcaption_comments', 2, 2, 'ViewComments', '', 1, 66, 'non,memb', 0),

('bx_sites_hon', '998px', 'Previously Rated', '_bx_sites_bcaption_previously', 1, 0, 'ViewPreviously', '', 1, 34, 'non,memb', 0),
('bx_sites_hon', '998px', 'Rate Site', '_bx_sites_bcaption_rate', 2, 0, 'ViewRate', '', 1, 66, 'non,memb', 0);

-- vote objects
INSERT INTO `sys_objects_vote` VALUES (NULL, 'bx_sites', 'bx_sites_rating', 'bx_sites_rating_track', 'sites_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', 'bx_sites_main', 'rate', 'rateCount', 'id', 'BxSitesVoting', 'modules/boonex/sites/classes/BxSitesVoting.php');

-- comments objects
INSERT INTO `sys_objects_cmts` VALUES (NULL, 'bx_sites', 'bx_sites_cmts', 'bx_sites_cmts_track', '0', '1', '90', '5', '1', '-3', 'slide', '2000', '1', '1', 'cmt', 'bx_sites_main', 'id', 'commentsCount', 'BxSitesCmts', 'modules/boonex/sites/classes/BxSitesCmts.php');

-- views objects
INSERT INTO `sys_objects_views` VALUES(NULL, 'bx_sites', 'bx_sites_views_track', 86400, 'bx_sites_main', 'id', 'views', 1);

-- search objects
INSERT INTO `sys_objects_search` (`ObjectName`, `Title`, `ClassName`, `ClassPath`)
VALUES ('bx_sites', '_bx_sites', 'BxSitesSearchResult', 'modules/boonex/sites/classes/BxSitesSearchResult.php');

-- tag objects
INSERT INTO `sys_objects_tag` VALUES (NULL, 'bx_sites', 'SELECT `tags` FROM `[db_prefix]main` WHERE `id` = {iID} AND `status` = ''approved''', 'bx_sites_permalinks', 'm/sites/browse/tag/{tag}', 'modules/?r=sites/browse/tag/{tag}', '_bx_sites');

-- category objects
INSERT INTO `sys_objects_categories` VALUES (NULL, 'bx_sites', 'SELECT `categories` FROM `[db_prefix]main` WHERE `id` = {iID} AND `status` = ''approved''', 'bx_sites_permalinks', 'm/sites/browse/category/{tag}', 'modules/?r=sites/browse/category/{tag}', '_bx_sites');

-- categories
INSERT INTO `sys_categories` (`Category`, `ID`, `Type`, `Owner`, `Status`) VALUES
    ('Sites', '0', 'bx_photos', '0', 'active'),
    ('Technology', '0', 'bx_sites', '0', 'active'),
    ('World & Business', '0', 'bx_sites', '0', 'active'),
    ('Science', '0', 'bx_sites', '0', 'active'),
    ('Gaming', '0', 'bx_sites', '0', 'active'),
    ('Lifestyle', '0', 'bx_sites', '0', 'active'),
    ('Entertainment', '0', 'bx_sites', '0', 'active'),
    ('Sports', '0', 'bx_sites', '0', 'active');

-- users actions
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'modules/boonex/sites/|edit.png', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxSitesModule'']->_oConfig; return  $oConfig->getBaseUri() . ''edit/{ID}'';', 0, 'bx_sites'),
    ('{TitleDelete}', 'modules/boonex/sites/|action_block.png', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'');return false;', '$oConfig = $GLOBALS[''oBxSitesModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''delete/{ID}'';', 1, 'bx_sites'),
    ('{TitleShare}', 'modules/boonex/sites/|action_share.png', '', 'bx_site_show_share_popup()', '', 2, 'bx_sites'),
    ('{AddToFeatured}', 'modules/boonex/sites/|calendar_add.png', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'');return false;', '$oConfig = $GLOBALS[''oBxSitesModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''featured/{ID}'';', 3, 'bx_sites'),
    ('{evalResult}', 'modules/boonex/sites/|site_add.png', '{BaseUri}browse/my/add', '', 'if (($GLOBALS[''logged''][''member''] || $GLOBALS[''logged''][''admin'']) && {isAllowedAdd} == 1) return _t(''_bx_sites_action_add_site''); return;', 1, 'bx_sites_title'),
    ('{evalResult}', 'modules/boonex/sites/|sites.png', '{BaseUri}browse/my', '', 'if ($GLOBALS[''logged''][''member''] || $GLOBALS[''logged''][''admin'']) return _t(''_bx_sites_action_my_sites''); return;', 2, 'bx_sites_title'),
    ('{evalResult}', 'modules/boonex/sites/|sites.png', '{BaseUri}home/', '', 'if ($GLOBALS[''logged''][''member''] || $GLOBALS[''logged''][''admin'']) return _t(''_bx_sites_action_home_sites''); return;', 3, 'bx_sites_title'),
    ('{sbs_sites_title}', 'action_subscribe.png', '', '{sbs_sites_script}', '', 6, 'bx_sites');

-- site stats
INSERT INTO `sys_stat_site` VALUES(NULL, 'sts', 'bx_sites', 'modules/?r=sites/home/', 'SELECT COUNT(`ID`) FROM `[db_prefix]main` WHERE `status` = ''approved''', '../modules/?r=sites/administration', 'SELECT COUNT(`ID`) FROM `[db_prefix]main` WHERE `status` != ''approved''', 'modules/boonex/sites/|sites.png', 0);

-- PQ statistics
INSERT INTO `sys_stat_member` VALUES ('bx_sites', 'SELECT COUNT(*) FROM `[db_prefix]main` WHERE `ownerid` = ''__member_id__'' AND `status`=''approved''');
INSERT INTO `sys_stat_member` VALUES ('bx_sitesp', 'SELECT COUNT(*) FROM `[db_prefix]main` WHERE `ownerid` = ''__member_id__'' AND `status`!=''approved''');
INSERT INTO `sys_account_custom_stat_elements` VALUES(NULL, '_bx_sites', '__bx_sites__ __l_created__ (<a href="modules/?r=sites/browse/my/add">__l_add__</a>)');

-- membership actions
SET @iLevelNonMember := 1;
SET @iLevelStandard := 2;
SET @iLevelPromotion := 3;

INSERT INTO `sys_acl_actions` VALUES (NULL, 'sites view', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'sites browse', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'sites search', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'sites add', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);
    
INSERT INTO `sys_acl_actions` VALUES (NULL, 'sites edit any site', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'sites delete any site', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'sites mark as featured', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'sites approve', NULL);
    
-- alert handlers
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'bx_sites', 'BxSitesProfileDeleteResponse', 'modules/boonex/sites/classes/BxSitesProfileDeleteResponse.php', '');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'profile', 'delete', @iHandler);

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'bx_sites', '_bx_sites', '{siteUrl}modules/?r=sites/administration', 'Sites module by BoonEx', 'modules/boonex/sites/|sites.png', @iMax+1);

-- privacy
INSERT INTO `sys_privacy_actions`(`module_uri`, `name`, `title`, `default_group`) VALUES
('bx_sites', 'view', '_bx_sites_privacy_view', '3'),
('bx_sites', 'comments', '_bx_sites_privacy_comment', '3'),
('bx_sites', 'rate', '_bx_sites_privacy_vote', '3');

-- subscriptions
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('t_sbsSitesComments', 'New site comments', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p><b>Dear <RealName></b>,</p><br /><p>The site you subscribed to has new comments!</p><br /> <p>Click <a href="<ViewLink>">here</a> to view them.</p><br /> <p><b>Thank you for using our services!</b></p> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'New site comments subscription.', '0'),
('t_sbsSitesRates', 'Site was rated', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p><b>Dear <RealName></b>,</p><br /><p>The site you subscribed to was rated!</p><br /> <p>Click <a href="<ViewLink>">here</a> to view it.</p><br /> <p><b>Thank you for using our services!</b></p> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'New site rates subscription.', '0');

INSERT INTO `sys_sbs_types`(`unit`, `action`, `template`, `params`) VALUES
('bx_sites', '', '', 'return array(''template'' => array(''Subscription'' => _t(''_bx_sites_sbs_main''), ''ViewLink'' => BX_DOL_URL_MODULES . ''?r=sites/view/'' . $arg3));'),
('bx_sites', 'commentPost', 't_sbsSitesComments', 'return array(''template'' => array(''Subscription'' => _t(''_bx_sites_sbs_comment''), ''ViewLink'' => BX_DOL_URL_MODULES . ''?r=sites/view/'' . $arg3));'),
('bx_sites', 'rate', 't_sbsSitesRates', 'return array(''template'' => array(''Subscription'' => _t(''_bx_sites_sbs_votes''), ''ViewLink'' => BX_DOL_URL_MODULES . ''?r=sites/view/'' . $arg3));');
