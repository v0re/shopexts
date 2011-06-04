-- create tables
CREATE TABLE IF NOT EXISTS `[db_prefix]products` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `status` enum('approved','pending') NOT NULL default 'approved',
  `thumb` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `author_id` int(10) unsigned NOT NULL default '0',
  `tags` varchar(255) NOT NULL default '',
  `categories` text NOT NULL,
  `views` int(11) NOT NULL,
  `rate` float NOT NULL,
  `rate_count` int(11) NOT NULL,
  `comments_count` int(11) NOT NULL,
  `featured` tinyint(4) NOT NULL,
  `price_range` varchar(16) NOT NULL,
  `allow_view_product_to` int(11) NOT NULL,
  `allow_post_in_forum_to` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uri` (`uri`),
  KEY `author_id` (`author_id`),
  KEY `created` (`created`),
  FULLTEXT KEY `search` (`title`,`desc`,`tags`,`categories`),
  FULLTEXT KEY `tags` (`tags`),
  FULLTEXT KEY `categories` (`categories`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]customers` (
  `file_id` int(10) unsigned NOT NULL,
  `client_id` int(10) unsigned NOT NULL,
  `order_id` varchar(16) collate utf8_unicode_ci NOT NULL,
  `count` int(10) unsigned NOT NULL,
  `date` int(10) unsigned NOT NULL,
  KEY `file_id` (`file_id`,`client_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]product_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]product_videos` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]product_files` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `author_id` int(10) unsigned NOT NULL,
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  `price` decimal(11,2) unsigned NOT NULL,
  `allow_purchase_to` varchar(16) NOT NULL,
  `hidden` tinyint(4) NOT NULL default '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `[db_prefix]rating` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `[db_prefix]rating_track` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
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

-- create forum tables

CREATE TABLE `[db_prefix]forum` (
  `forum_id` int(10) unsigned NOT NULL auto_increment,
  `forum_uri` varchar(255) NOT NULL default '',
  `cat_id` int(11) NOT NULL default '0',
  `forum_title` varchar(255) default NULL,
  `forum_desc` varchar(255) NOT NULL default '',
  `forum_posts` int(11) NOT NULL default '0',
  `forum_topics` int(11) NOT NULL default '0',
  `forum_last` int(11) NOT NULL default '0',
  `forum_type` enum('public','private') NOT NULL default 'public',
  `forum_order` int(11) NOT NULL default '0',
  `entry_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`forum_id`),
  KEY `cat_id` (`cat_id`),
  KEY `forum_uri` (`forum_uri`),
  KEY `entry_id` (`entry_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]forum_cat` (
  `cat_id` int(10) unsigned NOT NULL auto_increment,
  `cat_uri` varchar(255) NOT NULL default '',
  `cat_name` varchar(255) default NULL,
  `cat_icon` varchar(32) NOT NULL default '',
  `cat_order` float NOT NULL default '0',
  `cat_expanded` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`cat_id`),
  KEY `cat_order` (`cat_order`),
  KEY `cat_uri` (`cat_uri`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `[db_prefix]forum_cat` (`cat_id`, `cat_uri`, `cat_name`, `cat_icon`, `cat_order`) VALUES 
(1, 'Store', 'Store', '', 64);

CREATE TABLE `[db_prefix]forum_flag` (
  `user` varchar(32) NOT NULL default '',
  `topic_id` int(11) NOT NULL default '0',
  `when` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user`,`topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]forum_post` (
  `post_id` int(10) unsigned NOT NULL auto_increment,
  `topic_id` int(11) NOT NULL default '0',
  `forum_id` int(11) NOT NULL default '0',
  `user` varchar(32) NOT NULL default '0',
  `post_text` mediumtext NOT NULL,
  `when` int(11) NOT NULL default '0',
  `votes` int(11) NOT NULL default '0',
  `reports` int(11) NOT NULL default '0',
  `hidden` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`post_id`),
  KEY `topic_id` (`topic_id`),
  KEY `forum_id` (`forum_id`),
  KEY `user` (`user`),
  KEY `when` (`when`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]forum_topic` (
  `topic_id` int(10) unsigned NOT NULL auto_increment,
  `topic_uri` varchar(255) NOT NULL default '',
  `forum_id` int(11) NOT NULL default '0',
  `topic_title` varchar(255) NOT NULL default '',
  `when` int(11) NOT NULL default '0',
  `topic_posts` int(11) NOT NULL default '0',
  `first_post_user` varchar(32) NOT NULL default '0',
  `first_post_when` int(11) NOT NULL default '0',
  `last_post_user` varchar(32) NOT NULL default '',
  `last_post_when` int(11) NOT NULL default '0',
  `topic_sticky` int(11) NOT NULL default '0',
  `topic_locked` tinyint(4) NOT NULL default '0',
  `topic_hidden` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`topic_id`),
  KEY `forum_id` (`forum_id`),
  KEY `forum_id_2` (`forum_id`,`when`),
  KEY `topic_uri` (`topic_uri`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]forum_user` (
  `user_name` varchar(32) NOT NULL default '',
  `user_pwd` varchar(32) NOT NULL default '',
  `user_email` varchar(128) NOT NULL default '',
  `user_join_date` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_name`),
  UNIQUE KEY `user_email` (`user_email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]forum_user_activity` (
  `user` varchar(32) NOT NULL default '',
  `act_current` int(11) NOT NULL default '0',
  `act_last` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]forum_user_stat` (
  `user` varchar(32) NOT NULL default '',
  `posts` int(11) NOT NULL default '0',
  `user_last_post` int(11) NOT NULL default '0',
  KEY `user` (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]forum_vote` (
  `user_name` varchar(32) NOT NULL default '',
  `post_id` int(11) NOT NULL default '0',
  `vote_when` int(11) NOT NULL default '0',
  `vote_point` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`user_name`,`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]forum_actions_log` (
  `user_name` varchar(32) NOT NULL default '',
  `id` int(11) NOT NULL default '0',
  `action_name` varchar(32) NOT NULL default '',
  `action_when` int(11) NOT NULL default '0',
  KEY `action_when` (`action_when`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]forum_attachments` (
  `att_hash` char(16) COLLATE utf8_unicode_ci NOT NULL,
  `post_id` int(10) unsigned NOT NULL,
  `att_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `att_type` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `att_when` int(11) NOT NULL,
  `att_size` int(11) NOT NULL,
  `att_downloads` int(11) NOT NULL,
  PRIMARY KEY (`att_hash`),
  KEY `post_id` (`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]forum_signatures` (
  `user` varchar(32) NOT NULL,
  `signature` varchar(255) NOT NULL,
  `when` int(11) NOT NULL,
  PRIMARY KEY (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- page compose pages
SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_store_view', 'Store Product View', @iMaxOrder+1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_store_celendar', 'Store Calendar', @iMaxOrder+2);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_store_main', 'Store Home', @iMaxOrder+3);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_store_my', 'Store My', @iMaxOrder+4);

-- page compose blocks
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    ('bx_store_view', '998px', 'Product''s actions block', '_bx_store_block_actions', '1', '0', 'Actions', '', '1', '34', 'non,memb', '0'),    
    ('bx_store_view', '998px', 'Product''s rate block', '_bx_store_block_rate', '1', '1', 'Rate', '', '1', '34', 'non,memb', '0'),    
    ('bx_store_view', '998px', 'Product''s info block', '_bx_store_block_info', '1', '2', 'Info', '', '1', '34', 'non,memb', '0'),
    ('bx_store_view', '998px', 'Product''s items block', '_bx_store_block_items', '1', '3', 'Files', '', '1', '34', 'non,memb', '0'),    
    ('bx_store_view', '998px', 'Product''s description block', '_bx_store_block_desc', '2', '0', 'Desc', '', '1', '66', 'non,memb', '0'),
    ('bx_store_view', '998px', 'Product''s photo block', '_bx_store_block_photo', '2', '1', 'Photo', '', '1', '66', 'non,memb', '0'),
    ('bx_store_view', '998px', 'Product''s videos block', '_bx_store_block_video', '2', '2', 'Video', '', '1', '66', 'non,memb', '0'),    
    ('bx_store_view', '998px', 'Product''s comments block', '_bx_store_block_comments', '2', '3', 'Comments', '', '1', '66', 'non,memb', '0'),
    ('bx_store_main', '998px', 'Latest Featured Product', '_bx_store_block_latest_featured_product', '2', '0', 'LatestFeaturedProduct', '', '1', '66', 'non,memb', '0'),
    ('bx_store_main', '998px', 'Recent products', '_bx_store_block_recent', '1', '0', 'Recent', '', '1', '34', 'non,memb', '0'),
    ('bx_store_my', '998px', 'Administration Owner', '_bx_store_block_administration_owner', '1', '0', 'Owner', '', '1', '100', 'non,memb', '0'),
    ('bx_store_my', '998px', 'User''s products', '_bx_store_block_users_products', '1', '1', 'Browse', '', '0', '100', 'non,memb', '0'),
    ('index', '998px', 'Store', '_bx_store_block_homepage', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''store'', ''homepage_block'');', 1, 66, 'non,memb', 0),
    ('profile', '998px', 'User Store', '_bx_store_block_my_products', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''store'', ''profile_block'', array($this->oProfileGen->_iProfileID));', 1, 34, 'non,memb', 0);
    

-- permalinkU
INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=store/', 'm/store/', 'bx_store_permalinks');

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Store', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('bx_store_permalinks', 'on', 26, 'Enable friendly permalinks in store', 'checkbox', '', '', '0', ''),
('bx_store_autoapproval', 'on', @iCategId, 'Activate all products after creation automatically', 'checkbox', '', '', '0', ''),
('bx_store_product_of_the_day_from_featured_only', '', @iCategId, 'Product of the day is from featured products only', 'checkbox', '', '', '0', ''),
('category_auto_app_bx_store', 'on', @iCategId, 'Activate all categories after creation automatically', 'checkbox', '', '', '0', ''),
('bx_store_perpage_main_recent', '10', @iCategId, 'Number of recently added products to show on store home', 'digit', '', '', '0', ''),
('bx_store_perpage_browse', '14', @iCategId, 'Number of products to show on browse pages', 'digit', '', '', '0', ''),
('bx_store_perpage_profile', '4', @iCategId, 'Number of products to show on profile page', 'digit', '', '', '0', ''),
('bx_store_perpage_homepage', '5', @iCategId, 'Number of products to show on homepage', 'digit', '', '', '0', ''),
('bx_store_homepage_default_tab', 'featured', @iCategId, 'Default store block tab on homepage', 'select', '', '', '0', 'featured,recent,top,popular,free'),
('bx_store_max_rss_num', '10', @iCategId, 'Max number of rss items to provide', 'digit', '', '', '0', '');

-- search objects
INSERT INTO `sys_objects_search` VALUES(NULL, 'bx_store', '_bx_store', 'BxStoreSearchResult', 'modules/boonex/store/classes/BxStoreSearchResult.php');

-- vote objects
INSERT INTO `sys_objects_vote` VALUES (NULL, 'bx_store', '[db_prefix]rating', '[db_prefix]rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', '[db_prefix]products', 'rate', 'rate_count', 'id', 'BxStoreVoting', 'modules/boonex/store/classes/BxStoreVoting.php');

-- comments objects
INSERT INTO `sys_objects_cmts` VALUES (NULL, 'bx_store', '[db_prefix]cmts', '[db_prefix]cmts_track', '0', '1', '90', '5', '1', '-3', 'slide', '2000', '1', '1', 'cmt', '[db_prefix]products', 'id', 'comments_count', 'BxStoreCmts', 'modules/boonex/store/classes/BxStoreCmts.php');

-- views objects
INSERT INTO `sys_objects_views` VALUES(NULL, 'bx_store', '[db_prefix]views_track', 86400, '[db_prefix]products', 'id', 'views', 1);

-- tag objects
INSERT INTO `sys_objects_tag` VALUES (NULL, 'bx_store', 'SELECT `Tags` FROM `[db_prefix]products` WHERE `id` = {iID} AND `status` = ''approved''', 'bx_store_permalinks', 'm/store/browse/tag/{tag}', 'modules/?r=store/browse/tag/{tag}', '_bx_store');

-- category objects
INSERT INTO `sys_objects_categories` VALUES (NULL, 'bx_store', 'SELECT `Categories` FROM `[db_prefix]products` WHERE `id` = {iID} AND `status` = ''approved''', 'bx_store_permalinks', 'm/store/browse/category/{tag}', 'modules/?r=store/browse/category/{tag}', '_bx_store');

INSERT INTO `sys_categories` (`Category`, `ID`, `Type`, `Owner`, `Status`) VALUES 
('Store', '0', 'bx_photos', '0', 'active'),
('Templates', '0', 'bx_store', '0', 'active'),
('Languages', '0', 'bx_store', '0', 'active'),
('GEO', '0', 'bx_store', '0', 'active'),
('Payments', '0', 'bx_store', '0', 'active'),
('SEO', '0', 'bx_store', '0', 'active'),
('Security', '0', 'bx_store', '0', 'active'),
('Entertainment', '0', 'bx_store', '0', 'active'),
('Media', '0', 'bx_store', '0', 'active'),
('Performance', '0', 'bx_store', '0', 'active');

-- users actions
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'modules/boonex/store/|edit.png', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxStoreModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''edit/{ID}'';', '0', 'bx_store'),
    ('{TitleDelete}', 'modules/boonex/store/|action_block.png', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxStoreModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''delete/{ID}'';', '1', 'bx_store'),
    ('{TitleShare}', 'modules/boonex/store/|action_share.png', '', 'showPopupAnyHtml (''{BaseUri}share_popup/{ID}'');', '', '4', 'bx_store'),
    ('{TitleBroadcast}', 'modules/boonex/store/|action_broadcast.png', '{BaseUri}broadcast/{ID}', '', '', '5', 'bx_store'),
    ('{AddToFeatured}', 'modules/boonex/store/|star__plus.png', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxStoreModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''mark_featured/{ID}'';', 6, 'bx_store'),
    ('{TitleSubscribe}', 'action_subscribe.png', '', '{ScriptSubscribe}', '', 7, 'bx_store'),
    ('{evalResult}', 'modules/boonex/store/|store.png', '{BaseUri}browse/my&bx_store_filter=add_product', '', 'return $GLOBALS[''logged''][''member''] || $GLOBALS[''logged''][''admin''] ? _t(''_bx_store_action_add_product'') : '''';', '1', 'bx_store_title'),
    ('{evalResult}', 'modules/boonex/store/|store.png', '{BaseUri}browse/my', '', 'return $GLOBALS[''logged''][''member''] || $GLOBALS[''logged''][''admin''] ? _t(''_bx_store_action_my_products'') : '''';', '2', 'bx_store_title'),
    ('{evalResult}', 'modules/boonex/store/|store.png', '{BaseUri}', '', 'return $GLOBALS[''logged''][''member''] || $GLOBALS[''logged''][''admin''] ? _t(''_bx_store_action_store_home'') : '''';', '3', 'bx_store_title');
    
-- top menu 
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, 0, 'Store', '_bx_store_menu_root', 'modules/?r=store/view/|modules/?r=store/broadcast/|modules/?r=store/edit/|forum/store/', '', 'non,memb', '', '', '', 1, 1, 1, 'system', 'modules/boonex/store/|bx_store.png', '', '0', '');
SET @iCatRoot := LAST_INSERT_ID();
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, @iCatRoot, 'Store View Product', '_bx_store_menu_view_product', 'modules/?r=store/view/{bx_store_view_uri}', 0, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/store/|bx_store.png', '', 0, ''),
(NULL, @iCatRoot, 'Store View Forum', '_bx_store_menu_view_forum', 'forum/store/forum/{bx_store_view_uri}-0.htm|forum/store/', 1, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/store/|bx_store.png', '', 0, ''),
(NULL, @iCatRoot, 'Store View Comments', '_bx_store_menu_view_comments', 'modules/?r=store/comments/{bx_store_view_uri}', 2, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/store/|bx_store.png', '', 0, '');


SET @iMaxMenuOrder := (SELECT `Order` + 1 FROM `sys_menu_top` WHERE `Parent` = 0 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, 0, 'Store', '_bx_store_menu_root', 'modules/?r=store/home/|modules/?r=store/', @iMaxMenuOrder, 'non,memb', '', '', '', 1, 1, 1, 'top', 'modules/boonex/store/|bx_store.png', '', 1, '');
SET @iCatRoot := LAST_INSERT_ID();
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, @iCatRoot, 'Store Main Page', '_bx_store_menu_main', 'modules/?r=store/home/', 0, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/store/|bx_store.png', '', 0, ''),
(NULL, @iCatRoot, 'Recent Products', '_bx_store_menu_recent', 'modules/?r=store/browse/recent', 2, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/store/|bx_store.png', '', 0, ''),
(NULL, @iCatRoot, 'Top Rated Products', '_bx_store_menu_top_rated', 'modules/?r=store/browse/top', 3, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/store/|bx_store.png', '', 0, ''),
(NULL, @iCatRoot, 'Popular Products', '_bx_store_menu_popular', 'modules/?r=store/browse/popular', 4, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/store/|bx_store.png', '', 0, ''),
(NULL, @iCatRoot, 'Featured Products', '_bx_store_menu_featured', 'modules/?r=store/browse/featured', 5, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/store/|bx_store.png', '', 0, ''),
(NULL, @iCatRoot, 'Free Products', '_bx_store_menu_free_products', 'modules/?r=store/browse/free', 6, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/store/|bx_store.png', '', 0, ''),
(NULL, @iCatRoot, 'Store Tags', '_bx_store_menu_tags', 'modules/?r=store/tags', 8, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/store/|bx_store.png', '', 0, 'bx_store'),
(NULL, @iCatRoot, 'Store Categories', '_bx_store_menu_categories', 'modules/?r=store/categories', 9, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/store/|bx_store.png', '', 0, 'bx_store'),
(NULL, @iCatRoot, 'Calendar', '_bx_store_menu_calendar', 'modules/?r=store/calendar', 10, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/store/|bx_store.png', '', 0, ''),
(NULL, @iCatRoot, 'Search', '_bx_store_menu_search', 'modules/?r=store/search', 11, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/store/|bx_store.png', '', 0, '');

--SET @iCatProfile := (SELECT `ID` FROM `sys_menu_top` WHERE `Parent` = 0 AND `Name` = 'View Profile' LIMIT 1);
SET @iCatProfileOrder := (SELECT MAX(`Order`)+1 FROM `sys_menu_top` WHERE `Parent` = 9 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, 9, 'Store', '_bx_store_menu_my_products_profile', 'modules/?r=store/browse/user/{profileNick}', @iCatProfileOrder, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
SET @iCatProfileOrder := (SELECT MAX(`Order`)+1 FROM `sys_menu_top` WHERE `Parent` = 4 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, 4, 'Store', '_bx_store_menu_my_products_profile', 'modules/?r=store/browse/my', @iCatProfileOrder, 'memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'bx_store', '_bx_store', '{siteUrl}modules/?r=store/administration/', 'Store module by BoonEx','modules/boonex/store/|store.png', @iMax+1);

-- site stats
INSERT INTO `sys_stat_site` VALUES(NULL, 'bx_store', 'bx_store', 'modules/?r=store/', 'SELECT COUNT(`id`) FROM `[db_prefix]products` WHERE `status` = ''approved''', '../modules/?r=store/administration', 'SELECT COUNT(`id`) FROM `[db_prefix]products` WHERE `status` != ''approved''', 'modules/boonex/store/|store.png', 0);

-- PQ statistics
INSERT INTO `sys_stat_member` VALUES ('bx_store', 'SELECT COUNT(*) FROM `[db_prefix]products` WHERE `author_id` = ''__member_id__'' AND `status`=''approved''');
INSERT INTO `sys_stat_member` VALUES ('bx_storep', 'SELECT COUNT(*) FROM `[db_prefix]products` WHERE `author_id` = ''__member_id__'' AND `Status`!=''approved''');
INSERT INTO `sys_account_custom_stat_elements` VALUES(NULL, '_bx_store', '__bx_store__ __l_created__ (<a href="modules/?r=store/browse/my&bx_store_filter=add_product">__l_add__</a>)');

-- email templates
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`)
VALUES ('bx_store_broadcast', '<BroadcastTitle>', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p>Hello <NickName>,</p> <p><a href="<EntryUrl>"><EntryTitle></a> product admin has sent the following broadcast message:</p> <pre><BroadcastMessage></pre> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'Store broadcast message', '0'),
('bx_store_sbs', 'Product was changed', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p>Hello <NickName>,</p> <p><a href="<ViewLink>"><EntryTitle></a> product was changed: <br /> <ActionName> </p> <p>You may cancel the subscription by clicking the following link: <a href="<UnsubscribeLink>"><UnsubscribeLink></a></p> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'Product subscription template', '0');

-- membership actions
SET @iLevelNonMember := 1;
SET @iLevelStandard := 2;
SET @iLevelPromotion := 3;

INSERT INTO `sys_acl_actions` VALUES (NULL, 'store view product', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'store browse', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'store search', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'store add product', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'store product comments delete and edit', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'store edit any product', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'store delete any product', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'store mark as featured', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'store approve product', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'store broadcast message', NULL);

-- alert handlers
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'bx_store_profile_delete', '', '', 'BxDolService::call(''store'', ''response_profile_delete'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'profile', 'delete', @iHandler);

INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'bx_store_media_delete', '', '', 'BxDolService::call(''store'', ''response_media_delete'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_photos', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_videos', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_files', 'delete', @iHandler);

-- member menu
INSERT INTO 
    `sys_menu_member` 
SET
    `Name` = 'bx_store',
    `Eval` = 'return BxDolService::call(''store'', ''get_member_menu_item'', array({ID}));',
    `Type` = 'linked_item',
    `Parent` = '1';

-- privacy
INSERT INTO `sys_privacy_actions` (`module_uri`, `name`, `title`, `default_group`) VALUES
('store', 'purchase', '_bx_store_privacy_purchase_file', '4'),
('store', 'post_in_forum', '_bx_store_privacy_post_in_forum_product', 'c'),
('store', 'view_product', '_bx_store_privacy_view_product', '3');

-- payments
INSERT INTO `bx_pmt_modules` (`uri`) VALUES ('store');

-- subscriptions
INSERT INTO `sys_sbs_types` (`unit`, `action`, `template`, `params`) VALUES
('bx_store', '', '', 'return BxDolService::call(''store'', ''get_subscription_params'', array($arg2, $arg3));'),
('bx_store', 'change', 'bx_store_sbs', 'return BxDolService::call(''store'', ''get_subscription_params'', array($arg2, $arg3));'),
('bx_store', 'commentPost', 'bx_store_sbs', 'return BxDolService::call(''store'', ''get_subscription_params'', array($arg2, $arg3));'),
('bx_store', 'rate', 'bx_store_sbs', 'return BxDolService::call(''store'', ''get_subscription_params'', array($arg2, $arg3));');

