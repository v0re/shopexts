-- create tables
CREATE TABLE IF NOT EXISTS `[db_prefix]main` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `country` varchar(2) NOT NULL,
  `city` varchar(64) NOT NULL,
  `zip` varchar(16) NOT NULL,
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
  `fans_count` int(11) NOT NULL,
  `featured` tinyint(4) NOT NULL,
  `allow_view_group_to` int(11) NOT NULL,
  `allow_view_fans_to` varchar(16) NOT NULL,
  `allow_comment_to` varchar(16) NOT NULL,
  `allow_rate_to` varchar(16) NOT NULL,  
  `allow_post_in_forum_to` varchar(16) NOT NULL,
  `allow_join_to` int(11) NOT NULL,
  `join_confirmation` tinyint(4) NOT NULL default '0',
  `allow_upload_photos_to` varchar(16) NOT NULL,
  `allow_upload_videos_to` varchar(16) NOT NULL,
  `allow_upload_sounds_to` varchar(16) NOT NULL,
  `allow_upload_files_to` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uri` (`uri`),
  KEY `author_id` (`author_id`),
  KEY `created` (`created`),
  FULLTEXT KEY `search` (`title`,`desc`,`tags`,`categories`),
  FULLTEXT KEY `tags` (`tags`),
  FULLTEXT KEY `categories` (`categories`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]fans` (
  `id_entry` int(10) unsigned NOT NULL,
  `id_profile` int(10) unsigned NOT NULL,
  `when` int(10) unsigned NOT NULL,
  `confirmed` tinyint(4) UNSIGNED NOT NULL default '0',
  PRIMARY KEY (`id_entry`, `id_profile`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]admins` (
  `id_entry` int(10) unsigned NOT NULL,
  `id_profile` int(10) unsigned NOT NULL,
  `when` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_entry`, `id_profile`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]videos` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]sounds` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]files` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
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
(1, 'Groups', 'Groups', '', 64);

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
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_groups_view', 'Group View', @iMaxOrder+1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_groups_celendar', 'Groups Calendar', @iMaxOrder+2);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_groups_main', 'Groups Home', @iMaxOrder+3);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_groups_my', 'Groups My', @iMaxOrder+4);

-- page compose blocks
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    ('bx_groups_view', '998px', 'Group''s actions block', '_bx_groups_block_actions', '1', '0', 'Actions', '', '1', '34', 'non,memb', '0'),    
    ('bx_groups_view', '998px', 'Group''s rate block', '_bx_groups_block_rate', '1', '1', 'Rate', '', '1', '34', 'non,memb', '0'),    
    ('bx_groups_view', '998px', 'Group''s info block', '_bx_groups_block_info', '1', '2', 'Info', '', '1', '34', 'non,memb', '0'),
    ('bx_groups_view', '998px', 'Group''s fans block', '_bx_groups_block_fans', '1', '3', 'Fans', '', '1', '34', 'non,memb', '0'),    
    ('bx_groups_view', '998px', 'Group''s unconfirmed fans block', '_bx_groups_block_fans_unconfirmed', '1', '5', 'FansUnconfirmed', '', '1', '34', 'memb', '0'),
    ('bx_groups_view', '998px', 'Group''s description block', '_bx_groups_block_desc', '2', '0', 'Desc', '', '1', '66', 'non,memb', '0'),
    ('bx_groups_view', '998px', 'Group''s photo block', '_bx_groups_block_photo', '2', '1', 'Photo', '', '1', '66', 'non,memb', '0'),
    ('bx_groups_view', '998px', 'Group''s videos block', '_bx_groups_block_video', '2', '2', 'Video', '', '1', '66', 'non,memb', '0'),    
    ('bx_groups_view', '998px', 'Group''s sounds block', '_bx_groups_block_sound', '2', '3', 'Sound', '', '1', '66', 'non,memb', '0'),    
    ('bx_groups_view', '998px', 'Group''s files block', '_bx_groups_block_files', '2', '4', 'Files', '', '1', '66', 'non,memb', '0'),    
    ('bx_groups_view', '998px', 'Group''s comments block', '_bx_groups_block_comments', '2', '5', 'Comments', '', '1', '66', 'non,memb', '0'),
    ('bx_groups_main', '998px', 'Latest Featured Group', '_bx_groups_block_latest_featured_group', '2', '0', 'LatestFeaturedGroup', '', '1', '66', 'non,memb', '0'),
    ('bx_groups_main', '998px', 'Recent Groups', '_bx_groups_block_recent', '1', '0', 'Recent', '', '1', '34', 'non,memb', '0'),
    ('bx_groups_my', '998px', 'Administration Owner', '_bx_groups_block_administration_owner', '1', '0', 'Owner', '', '1', '100', 'non,memb', '0'),
    ('bx_groups_my', '998px', 'User''s groups', '_bx_groups_block_users_groups', '1', '1', 'Browse', '', '0', '100', 'non,memb', '0'),
    ('index', '998px', 'Groups', '_bx_groups_block_homepage', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''groups'', ''homepage_block'');', 1, 66, 'non,memb', 0),
	('profile', '998px', 'Joined Groups', '_bx_groups_block_my_groups_joined', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''groups'', ''profile_block_joined'', array($this->oProfileGen->_iProfileID));', 1, 34, 'non,memb', 0),
    ('profile', '998px', 'User Groups', '_bx_groups_block_my_groups', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''groups'', ''profile_block'', array($this->oProfileGen->_iProfileID));', 1, 34, 'non,memb', 0);

-- permalinkU
INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=groups/', 'm/groups/', 'bx_groups_permalinks');

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Groups', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('bx_groups_permalinks', 'on', 26, 'Enable friendly permalinks in groups', 'checkbox', '', '', '0', ''),
('bx_groups_autoapproval', 'on', @iCategId, 'Activate all groups after creation automatically', 'checkbox', '', '', '0', ''),
('bx_groups_author_comments_admin', 'on', @iCategId, 'Allow group admin to edit and delete any comment', 'checkbox', '', '', '0', ''),
('bx_groups_max_email_invitations', '10', @iCategId, 'Max number of email invitation to send per one invite', 'digit', '', '', '0', ''),
('category_auto_app_bx_groups', 'on', @iCategId, 'Activate all categories after creation automatically', 'checkbox', '', '', '0', ''),
('bx_groups_perpage_view_fans', '6', @iCategId, 'Number of fans to show on group view page', 'digit', '', '', '0', ''),
('bx_groups_perpage_browse_fans', '30', @iCategId, 'Number of fans to show on browse fans page', 'digit', '', '', '0', ''),
('bx_groups_perpage_main_recent', '10', @iCategId, 'Number of recently added GROUPS to show on groups home', 'digit', '', '', '0', ''),
('bx_groups_perpage_browse', '14', @iCategId, 'Number of groups to show on browse pages', 'digit', '', '', '0', ''),
('bx_groups_perpage_profile', '4', @iCategId, 'Number of groups to show on profile page', 'digit', '', '', '0', ''),
('bx_groups_perpage_homepage', '5', @iCategId, 'Number of groups to show on homepage', 'digit', '', '', '0', ''),
('bx_groups_homepage_default_tab', 'featured', @iCategId, 'Default groups block tab on homepage', 'select', '', '', '0', 'featured,recent,top,popular'),
('bx_groups_max_rss_num', '10', @iCategId, 'Max number of rss items to provide', 'digit', '', '', '0', '');

-- search objects
INSERT INTO `sys_objects_search` VALUES(NULL, 'bx_groups', '_bx_groups', 'BxGroupsSearchResult', 'modules/boonex/groups/classes/BxGroupsSearchResult.php');

-- vote objects
INSERT INTO `sys_objects_vote` VALUES (NULL, 'bx_groups', '[db_prefix]rating', '[db_prefix]rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', '[db_prefix]main', 'rate', 'rate_count', 'id', 'BxGroupsVoting', 'modules/boonex/groups/classes/BxGroupsVoting.php');

-- comments objects
INSERT INTO `sys_objects_cmts` VALUES (NULL, 'bx_groups', '[db_prefix]cmts', '[db_prefix]cmts_track', '0', '1', '90', '5', '1', '-3', 'slide', '2000', '1', '1', 'cmt', '[db_prefix]main', 'id', 'comments_count', 'BxGroupsCmts', 'modules/boonex/groups/classes/BxGroupsCmts.php');

-- views objects
INSERT INTO `sys_objects_views` VALUES(NULL, 'bx_groups', '[db_prefix]views_track', 86400, '[db_prefix]main', 'id', 'views', 1);

-- tag objects
INSERT INTO `sys_objects_tag` VALUES (NULL, 'bx_groups', 'SELECT `Tags` FROM `[db_prefix]main` WHERE `id` = {iID} AND `status` = ''approved''', 'bx_groups_permalinks', 'm/groups/browse/tag/{tag}', 'modules/?r=groups/browse/tag/{tag}', '_bx_groups');

-- category objects
INSERT INTO `sys_objects_categories` VALUES (NULL, 'bx_groups', 'SELECT `Categories` FROM `[db_prefix]main` WHERE `id` = {iID} AND `status` = ''approved''', 'bx_groups_permalinks', 'm/groups/browse/category/{tag}', 'modules/?r=groups/browse/category/{tag}', '_bx_groups');

INSERT INTO `sys_categories` (`Category`, `ID`, `Type`, `Owner`, `Status`) VALUES 
('Groups', '0', 'bx_photos', '0', 'active'),
('Arts & Literature', '0', 'bx_groups', '0', 'active'),
('Animals & Pets', '0', 'bx_groups', '0', 'active'),
('Activities', '0', 'bx_groups', '0', 'active'),
('Automotive', '0', 'bx_groups', '0', 'active'),
('Business & Money', '0', 'bx_groups', '0', 'active'),
('Companies & Co-workers', '0', 'bx_groups', '0', 'active'),
('Cultures & Nations', '0', 'bx_groups', '0', 'active'),
('Dolphin Community', '0', 'bx_groups', '0', 'active'),
('Family & Friends', '0', 'bx_groups', '0', 'active'),
('Fan Clubs', '0', 'bx_groups', '0', 'active'),
('Fashion & Style', '0', 'bx_groups', '0', 'active'),
('Fitness & Body Building', '0', 'bx_groups', '0', 'active'),
('Food & Drink', '0', 'bx_groups', '0', 'active'),
('Gay, Lesbian & Bi', '0', 'bx_groups', '0', 'active'),
('Health & Wellness', '0', 'bx_groups', '0', 'active'),
('Hobbies & Entertainment', '0', 'bx_groups', '0', 'active'),
('Internet & Computers', '0', 'bx_groups', '0', 'active'),
('Love & Relationships', '0', 'bx_groups', '0', 'active'),
('Mass Media', '0', 'bx_groups', '0', 'active'),
('Music & Cinema', '0', 'bx_groups', '0', 'active'),
('Places & Travel', '0', 'bx_groups', '0', 'active'),
('Politics', '0', 'bx_groups', '0', 'active'),
('Recreation & Sports', '0', 'bx_groups', '0', 'active'),
('Religion', '0', 'bx_groups', '0', 'active'),
('Science & Innovations', '0', 'bx_groups', '0', 'active'),
('Sex', '0', 'bx_groups', '0', 'active'),
('Teens & Schools', '0', 'bx_groups', '0', 'active'),
('Other', '0', 'bx_groups', '0', 'active');

-- users actions
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'modules/boonex/groups/|edit.png', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxGroupsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''edit/{ID}'';', '0', 'bx_groups'),
    ('{TitleDelete}', 'modules/boonex/groups/|action_block.png', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxGroupsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''delete/{ID}'';', '1', 'bx_groups'),
    ('{TitleShare}', 'modules/boonex/groups/|action_share.png', '', 'showPopupAnyHtml (''{BaseUri}share_popup/{ID}'');', '', '2', 'bx_groups'),
    ('{TitleBroadcast}', 'modules/boonex/groups/|action_broadcast.png', '{BaseUri}broadcast/{ID}', '', '', '3', 'bx_groups'),
    ('{TitleJoin}', 'modules/boonex/groups/|user_add.png', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxGroupsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''join/{ID}/{iViewer}'';', '4', 'bx_groups'),
    ('{TitleInvite}', 'modules/boonex/groups/|group_add.png', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxGroupsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''invite/{ID}'';', '5', 'bx_groups'),
    ('{AddToFeatured}', 'modules/boonex/groups/|star__plus.png', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxGroupsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''mark_featured/{ID}'';', '6', 'bx_groups'),
    ('{TitleManageFans}', 'modules/boonex/groups/|action_manage_fans.png', '', 'showPopupAnyHtml (''{BaseUri}manage_fans_popup/{ID}'');', '', '8', 'bx_groups'),
    ('{TitleUploadPhotos}', 'modules/boonex/groups/|action_upload_photos.png', '{BaseUri}upload_photos/{URI}', '', '', '9', 'bx_groups'),
    ('{TitleUploadVideos}', 'modules/boonex/groups/|action_upload_videos.png', '{BaseUri}upload_videos/{URI}', '', '', '10', 'bx_groups'),
    ('{TitleUploadSounds}', 'modules/boonex/groups/|action_upload_sounds.png', '{BaseUri}upload_sounds/{URI}', '', '', '11', 'bx_groups'),
    ('{TitleUploadFiles}', 'modules/boonex/groups/|action_upload_files.png', '{BaseUri}upload_files/{URI}', '', '', '12', 'bx_groups'),
    ('{TitleSubscribe}', 'action_subscribe.png', '', '{ScriptSubscribe}', '', '13', 'bx_groups'),
    ('{evalResult}', 'modules/boonex/groups/|group_create.png', '{BaseUri}browse/my&bx_groups_filter=add_group', '', 'return $GLOBALS[''logged''][''member''] || $GLOBALS[''logged''][''admin''] ? _t(''_bx_groups_action_add_group'') : '''';', '1', 'bx_groups_title'),
    ('{evalResult}', 'modules/boonex/groups/|groups.png', '{BaseUri}browse/my', '', 'return $GLOBALS[''logged''][''member''] || $GLOBALS[''logged''][''admin''] ? _t(''_bx_groups_action_my_groups'') : '''';', '2', 'bx_groups_title'),
    ('{evalResult}', 'modules/boonex/groups/|groups.png', '{BaseUri}', '', 'return $GLOBALS[''logged''][''member''] || $GLOBALS[''logged''][''admin''] ? _t(''_bx_groups_action_groups_home'') : '''';', '2', 'bx_groups_title');
    
-- top menu 
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, 0, 'Groups', '_bx_groups_menu_root', 'modules/?r=groups/view/|modules/?r=groups/broadcast/|modules/?r=groups/invite/|modules/?r=groups/edit/|forum/groups/', '', 'non,memb', '', '', '', 1, 1, 1, 'system', 'modules/boonex/groups/|bx_groups.png', '', '0', '');
SET @iCatRoot := LAST_INSERT_ID();
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, @iCatRoot, 'Group View', '_bx_groups_menu_view_group', 'modules/?r=groups/view/{bx_groups_view_uri}', 0, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/groups/|bx_groups.png', '', 0, ''),
(NULL, @iCatRoot, 'Group View Forum', '_bx_groups_menu_view_forum', 'forum/groups/forum/{bx_groups_view_uri}-0.htm|forum/groups/', 1, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/groups/|bx_groups.png', '', 0, ''),
(NULL, @iCatRoot, 'Group View Fans', '_bx_groups_menu_view_fans', 'modules/?r=groups/browse_fans/{bx_groups_view_uri}', 2, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/groups/|bx_groups.png', '', 0, ''),
(NULL, @iCatRoot, 'Group View Comments', '_bx_groups_menu_view_comments', 'modules/?r=groups/comments/{bx_groups_view_uri}', 3, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/groups/|bx_groups.png', '', 0, '');


SET @iMaxMenuOrder := (SELECT `Order` + 1 FROM `sys_menu_top` WHERE `Parent` = 0 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, 0, 'Groups', '_bx_groups_menu_root', 'modules/?r=groups/home/|modules/?r=groups/', @iMaxMenuOrder, 'non,memb', '', '', '', 1, 1, 1, 'top', 'modules/boonex/groups/|bx_groups.png', '', 1, '');
SET @iCatRoot := LAST_INSERT_ID();
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, @iCatRoot, 'Groups Main Page', '_bx_groups_menu_main', 'modules/?r=groups/home/', 0, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/groups/|bx_groups.png', '', 0, ''),
(NULL, @iCatRoot, 'Recent Groups', '_bx_groups_menu_recent', 'modules/?r=groups/browse/recent', 2, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/groups/|bx_groups.png', '', 0, ''),
(NULL, @iCatRoot, 'Top Rated Groups', '_bx_groups_menu_top_rated', 'modules/?r=groups/browse/top', 3, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/groups/|bx_groups.png', '', 0, ''),
(NULL, @iCatRoot, 'Popular Groups', '_bx_groups_menu_popular', 'modules/?r=groups/browse/popular', 4, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/groups/|bx_groups.png', '', 0, ''),
(NULL, @iCatRoot, 'Featured Groups', '_bx_groups_menu_featured', 'modules/?r=groups/browse/featured', 5, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/groups/|bx_groups.png', '', 0, ''),
(NULL, @iCatRoot, 'Groups Tags', '_bx_groups_menu_tags', 'modules/?r=groups/tags', 8, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/groups/|bx_groups.png', '', 0, 'bx_groups'),
(NULL, @iCatRoot, 'Groups Categories', '_bx_groups_menu_categories', 'modules/?r=groups/categories', 9, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/groups/|bx_groups.png', '', 0, 'bx_groups'),
(NULL, @iCatRoot, 'Calendar', '_bx_groups_menu_calendar', 'modules/?r=groups/calendar', 10, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/groups/|bx_groups.png', '', 0, ''),
(NULL, @iCatRoot, 'Search', '_bx_groups_menu_search', 'modules/?r=groups/search', 11, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/boonex/groups/|bx_groups.png', '', 0, '');

--SET @iCatProfile := (SELECT `ID` FROM `sys_menu_top` WHERE `Parent` = 0 AND `Name` = 'View Profile' LIMIT 1);
SET @iCatProfileOrder := (SELECT MAX(`Order`)+1 FROM `sys_menu_top` WHERE `Parent` = 9 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, 9, 'Groups', '_bx_groups_menu_my_groups_profile', 'modules/?r=groups/browse/user/{profileNick}|modules/?r=groups/browse/joined/{profileNick}', @iCatProfileOrder, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
SET @iCatProfileOrder := (SELECT MAX(`Order`)+1 FROM `sys_menu_top` WHERE `Parent` = 4 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, 4, 'Groups', '_bx_groups_menu_my_groups_profile', 'modules/?r=groups/browse/my', @iCatProfileOrder, 'memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'bx_groups', '_bx_groups', '{siteUrl}modules/?r=groups/administration/', 'Groups module by BoonEx','modules/boonex/groups/|groups.png', @iMax+1);

-- site stats
INSERT INTO `sys_stat_site` VALUES(NULL, 'bx_groups', 'bx_groups', 'modules/?r=groups/', 'SELECT COUNT(`id`) FROM `[db_prefix]main` WHERE `status` = ''approved''', '../modules/?r=groups/administration', 'SELECT COUNT(`id`) FROM `[db_prefix]main` WHERE `status` != ''approved''', 'modules/boonex/groups/|groups.png', 0);

-- PQ statistics
INSERT INTO `sys_stat_member` VALUES ('bx_groups', 'SELECT COUNT(*) FROM `[db_prefix]main` WHERE `author_id` = ''__member_id__'' AND `status`=''approved''');
INSERT INTO `sys_stat_member` VALUES ('bx_groupsp', 'SELECT COUNT(*) FROM `[db_prefix]main` WHERE `author_id` = ''__member_id__'' AND `Status`!=''approved''');
INSERT INTO `sys_account_custom_stat_elements` VALUES(NULL, '_bx_groups', '__bx_groups__ __l_created__ (<a href="modules/?r=groups/browse/my&bx_groups_filter=add_group">__l_add__</a>)');

-- email templates
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES 
('bx_groups_broadcast', '<BroadcastTitle>', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p>Hello <NickName>,</p> <p><a href="<EntryUrl>"><EntryTitle></a> group admin has sent the following broadcast message:</p> <pre><BroadcastMessage></pre> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'Groups broadcast message', '0'),
('bx_groups_join_request', 'New join request to your group', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p>Hello <NickName>,</p> <p>New join request in your group <a href="<EntryUrl>"><EntryTitle></a>. Please review this request and reject or confirm it.</p> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'New join request to a group notification message', '0'),
('bx_groups_join_reject', 'Your join request to a group was rejected', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p>Hello <NickName>,</p> <p>Sorry, but your request to join <a href="<EntryUrl>"><EntryTitle></a> group was rejected by group admin(s).</p> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'Join request to a group was rejected notification message', '0'),
('bx_groups_join_confirm', 'Your join request to a group was confirmed', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p>Hello <NickName>,</p> <p>Congratulations! Your request to join <a href="<EntryUrl>"><EntryTitle></a> group was confirmed by group admin(s).</p> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'Join request to a group was confirmed notification message', '0'),
('bx_groups_fan_remove', 'You was removed from fans of a group', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p>Hello <NickName>,</p> <p>You was removed from fans of <a href="<EntryUrl>"><EntryTitle></a> group by group admin(s).</p> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'User was removed from fans of group notification message', '0'),
('bx_groups_fan_become_admin', 'You become admin of a group', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p>Hello <NickName>,</p> <p>Congratulations! You become admin of <a href="<EntryUrl>"><EntryTitle></a> group.</p> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'User become admin of a group notification message', '0'),
('bx_groups_admin_become_fan', 'You group admin status was removed', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p>Hello <NickName>,</p> <p>Your admin status was removed from <a href="<EntryUrl>"><EntryTitle></a> group by group admin(s).</p> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'User group admin status was removed notification message', '0'),
('bx_groups_invitation', 'Invitation to group: <GroupName>', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p>Hello <NickName>,</p> <p><a href="<InviterUrl>"><InviterNickName></a> has invited you to this group:</p> <pre><InvitationText></pre> <p> <b>Group Information:</b><br /> Name: <GroupName><br /> Location: <GroupLocation><br /> <a href="<GroupUrl>">More details</a> </p> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'Group invitation template', '0'),
('bx_groups_sbs', 'Group was changed', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p>Hello <NickName>,</p> <p><a href="<ViewLink>"><EntryTitle></a> group was changed: <br /> <ActionName> </p> <p>You may cancel the subscription by clicking the following link: <a href="<UnsubscribeLink>"><UnsubscribeLink></a></p> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'Group subscription template', '0');

-- membership actions
SET @iLevelNonMember := 1;
SET @iLevelStandard := 2;
SET @iLevelPromotion := 3;

INSERT INTO `sys_acl_actions` VALUES (NULL, 'groups view group', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'groups browse', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'groups search', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'groups add group', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'groups comments delete and edit', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'groups edit any group', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'groups delete any group', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'groups mark as featured', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'groups approve groups', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'groups broadcast message', NULL);

-- alert handlers
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'bx_groups_profile_delete', '', '', 'BxDolService::call(''groups'', ''response_profile_delete'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'profile', 'delete', @iHandler);

INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'bx_groups_media_delete', '', '', 'BxDolService::call(''groups'', ''response_media_delete'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_photos', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_videos', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_sounds', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_files', 'delete', @iHandler);

-- member menu
INSERT INTO 
    `sys_menu_member` 
SET
    `Name` = 'bx_groups',
    `Eval` = 'return BxDolService::call(''groups'', ''get_member_menu_item'', array({ID}));',
    `Type` = 'linked_item',
    `Parent` = '1';

-- privacy
INSERT INTO `sys_privacy_actions` (`module_uri`, `name`, `title`, `default_group`) VALUES
('groups', 'view_group', '_bx_groups_privacy_view_group', '3'),
('groups', 'view_fans', '_bx_groups_privacy_view_fans', '3'),
('groups', 'comment', '_bx_groups_privacy_comment', 'f'),
('groups', 'rate', '_bx_groups_privacy_rate', 'f'),
('groups', 'post_in_forum', '_bx_groups_privacy_post_in_forum', 'f'),
('groups', 'join', '_bx_groups_privacy_join', '3'),
('groups', 'upload_photos', '_bx_groups_privacy_upload_photos', 'a'),
('groups', 'upload_videos', '_bx_groups_privacy_upload_videos', 'a'),
('groups', 'upload_sounds', '_bx_groups_privacy_upload_sounds', 'a'),
('groups', 'upload_files', '_bx_groups_privacy_upload_files', 'a');

-- subscriptions
INSERT INTO `sys_sbs_types` (`unit`, `action`, `template`, `params`) VALUES
('bx_groups', '', '', 'return BxDolService::call(''groups'', ''get_subscription_params'', array($arg2, $arg3));'),
('bx_groups', 'change', 'bx_groups_sbs', 'return BxDolService::call(''groups'', ''get_subscription_params'', array($arg2, $arg3));'),
('bx_groups', 'commentPost', 'bx_groups_sbs', 'return BxDolService::call(''groups'', ''get_subscription_params'', array($arg2, $arg3));'),
('bx_groups', 'rate', 'bx_groups_sbs', 'return BxDolService::call(''groups'', ''get_subscription_params'', array($arg2, $arg3));'),
('bx_groups', 'join', 'bx_groups_sbs', 'return BxDolService::call(''groups'', ''get_subscription_params'', array($arg2, $arg3));');

