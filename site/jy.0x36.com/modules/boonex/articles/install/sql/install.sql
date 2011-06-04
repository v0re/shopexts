--
-- Table structure for table `[db_prefix]entries`
--

CREATE TABLE IF NOT EXISTS `[db_prefix]entries` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `author_id` int(11) unsigned NOT NULL default '0',  
  `caption` varchar(100) NOT NULL default '',
  `snippet` text NOT NULL,
  `content` mediumtext NOT NULL,
  `when` int(11) NOT NULL default '0',
  `uri` varchar(100) NOT NULL default '',
  `tags` varchar(255) NOT NULL default '',
  `categories` varchar(255) NOT NULL default '',
  `comment` tinyint(0) NOT NULL default '0',
  `vote` tinyint(0) NOT NULL default '0',
  `date` int(11) NOT NULL default '0',
  `status` tinyint(4) NOT NULL default '0',
  `featured` tinyint(4) NOT NULL default '0',
  `rate` int(11) NOT NULL default '0',
  `rate_count` int(11) NOT NULL default '0',
  `view_count` int(11) NOT NULL default '0',
  `cmts_count` int(11) NOT NULL default '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uri` (`uri`),
  FULLTEXT KEY `search_group` (`caption`, `content`, `tags`, `categories`),
  FULLTEXT KEY `search_caption` (`caption`),
  FULLTEXT KEY `search_content` (`content`),
  FULLTEXT KEY `search_tags` (`tags`),
  FULLTEXT KEY `search_categories` (`categories`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Table structure for table `[db_prefix]comments`
--

CREATE TABLE IF NOT EXISTS `[db_prefix]comments` (
  `cmt_id` int(11) NOT NULL auto_increment,
  `cmt_parent_id` int(11) NOT NULL default '0',
  `cmt_object_id` int(11) NOT NULL default '0',
  `cmt_author_id` int(10) unsigned NOT NULL default '0',
  `cmt_text` text NOT NULL,
  `cmt_mood` tinyint(4) NOT NULL default '0',
  `cmt_rate` int(11) NOT NULL default '0',
  `cmt_rate_count` int(11) NOT NULL default '0',
  `cmt_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `cmt_replies` int(11) NOT NULL default '0',
  PRIMARY KEY  (`cmt_id`),
  KEY `cmt_object_id` (`cmt_object_id`,`cmt_parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Table structure for table `[db_prefix]comments_track`
--

CREATE TABLE IF NOT EXISTS `[db_prefix]comments_track` (
  `cmt_system_id` int(11) NOT NULL default '0',
  `cmt_id` int(11) NOT NULL default '0',
  `cmt_rate` tinyint(4) NOT NULL default '0',
  `cmt_rate_author_id` int(10) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int(11) unsigned NOT NULL default '0',
  `cmt_rate_ts` int(11) NOT NULL default '0',
  PRIMARY KEY  (`cmt_system_id`,`cmt_id`,`cmt_rate_author_nip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `[db_prefix]voting`
--
CREATE TABLE `[db_prefix]voting` (
  `arl_id` bigint(8) NOT NULL default '0',
  `arl_rating_count` int(11) NOT NULL default '0',
  `arl_rating_sum` int(11) NOT NULL default '0',
  UNIQUE KEY `arl_id` (`arl_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `[db_prefix]voting_track`
--
CREATE TABLE `[db_prefix]voting_track` (
  `arl_id` bigint(8) NOT NULL default '0',
  `arl_ip` varchar(20) default NULL,
  `arl_date` datetime default NULL,
  KEY `arl_ip` (`arl_ip`,`arl_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `[db_prefix]views_track`
--
CREATE TABLE IF NOT EXISTS `[db_prefix]views_track` (
  `id` int(10) unsigned NOT NULL,
  `viewer` int(10) unsigned NOT NULL,
  `ip` int(10) unsigned NOT NULL,
  `ts` int(10) unsigned NOT NULL,
  KEY `id` (`id`,`viewer`,`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


SET @iTMOrder = (SELECT MAX(`Order`) FROM `sys_menu_top` WHERE `Parent`='0');
INSERT INTO `sys_menu_top` (`Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `BQuickLink`, `Statistics`) VALUES
(0, 'Articles', '_articles_top_menu_item', 'modules/?r=articles/index/|modules/?r=articles/', @iTMOrder+1, 'non,memb', '', '', '', 1, 1, 1, 'top', 'modules/boonex/articles/|top_menu_icon.png', 0, '');

SET @iTMParentId = LAST_INSERT_ID( );
INSERT INTO `sys_menu_top` (`Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `BQuickLink`, `Statistics`) VALUES
(@iTMParentId, 'ArticlesHome', '_articles_home_top_menu_sitem', 'modules/?r=articles/index/', 0, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', 0, ''),
(@iTMParentId, 'ArticlesArchive', '_articles_archive_top_menu_sitem', 'modules/?r=articles/archive/', 1, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', 0, ''),
(@iTMParentId, 'ArticlesTop', '_articles_top_top_menu_sitem', 'modules/?r=articles/top/', 2, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', 0, ''),
(@iTMParentId, 'ArticlesPopular', '_articles_popular_top_menu_sitem', 'modules/?r=articles/popular/', 3, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', 0, ''),
(@iTMParentId, 'ArticlesFeatured', '_articles_featured_top_menu_sitem', 'modules/?r=articles/featured/', 4, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', 0, ''),
(@iTMParentId, 'ArticlesTags', '_articles_tags_top_menu_sitem', 'modules/?r=articles/tags/', 5, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', 0, ''),
(@iTMParentId, 'ArticlesCategories', '_articles_categories_top_menu_sitem', 'modules/?r=articles/categories/', 6, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', 0, ''),
(@iTMParentId, 'ArticlesCalendar', '_articles_calendar_top_menu_sitem', 'modules/?r=articles/calendar/', 7, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', 0, ''),
(@iTMParentId, 'ArticlesSearch', '_articles_search_top_menu_sitem', 'searchKeyword.php?type=bx_articles', 8, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', 0, ''),
(0, '[db_prefix]_view', '_articles_view_top_menu_sitem', 'modules/?r=articles/view/', 0, 'non,memb', '', '', '', 1, 1, 1, 'system', 'modules/boonex/articles/|top_menu_icon.png', 0, '');

INSERT INTO `sys_menu_member`(`Caption`, `Name`, `Icon`, `Link`, `Script`, `Eval`, `Order`, `Active`, `Editable`, `Deletable`, `Target`, `Position`, `Type`) VALUES
('_articles_ext_menu_item', 'Articles', '', 'modules/?r=articles/', '', '', 6, '1', 0, 0, '', 'bottom', 'link');

SET @iOrder = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id`='2');
INSERT INTO `sys_menu_admin`(`parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) VALUES
(2, 'bx_articles', '_articles_admin_menu_sitem', '{siteUrl}modules/?r=articles/admin/', 'For managing articles', 'modules/boonex/articles/|site_articles.png', '', '', @iOrder+1);


INSERT INTO `sys_permalinks`(`standard`, `permalink`, `check`) VALUES('modules/?r=articles/', 'm/articles/', 'permalinks_module_articles');


SET @iCategoryOrder = (SELECT MAX(`menu_order`) FROM `sys_options_cats`) + 1;
INSERT INTO `sys_options_cats` (`name` , `menu_order` ) VALUES ('Articles', @iCategoryOrder);
SET @iCategoryId = LAST_INSERT_ID();

INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`) VALUES
('permalinks_module_articles', 'on', 26, 'Enable friendly articles permalink', 'checkbox', '', '', 0),
('category_auto_app_bx_articles', 'on', 0, 'Autoapprove for categories', 'checkbox', '', '', 0),
('articles_autoapprove', 'on', @iCategoryId, 'Publish articles automatically', 'checkbox', '', '', 1),
('articles_comments', 'on', @iCategoryId, 'Allow comments for articles', 'checkbox', '', '', 2),
('articles_votes', 'on', @iCategoryId, 'Allow votes for articles', 'checkbox', '', '', 3),
('articles_index_number', '10', @iCategoryId, 'The number of articles on home page', 'digit', '', '', 4),
('articles_member_number', '10', @iCategoryId, 'The number of articles on account page', 'digit', '', '', 5),
('articles_snippet_length', '200', @iCategoryId, 'The length of article snippet for home and account pages', 'digit', '', '', 6),
('articles_per_page', '10', @iCategoryId, 'The number of items shown on the page', 'digit', '', '', 7),
('articles_rss_length', '10', @iCategoryId, 'The number of items shown in the RSS feed', 'digit', '', '', 8);

INSERT INTO `sys_objects_cmts` (`ObjectName`, `TableCmts`, `TableTrack`, `AllowTags`, `Nl2br`, `SecToEdit`, `PerView`, `IsRatable`, `ViewingThreshold`, `AnimationEffect`, `AnimationSpeed`, `IsOn`, `IsMood`, `RootStylePrefix`, `TriggerTable`, `TriggerFieldId`, `TriggerFieldComments`, `ClassName`, `ClassFile`) VALUES
('bx_articles', '[db_prefix]comments', '[db_prefix]comments_track', 0, 1, 90, 10, 1, -3, 'slide', 2000, 1, 0, 'cmt', '[db_prefix]entries', 'id', 'cmts_count', 'BxArlCmts', 'modules/boonex/articles/classes/BxArlCmts.php');

INSERT INTO `sys_objects_vote` (`ObjectName`, `TableRating`, `TableTrack`, `RowPrefix`, `MaxVotes`, `PostName`, `IsDuplicate`, `IsOn`, `className`, `classFile`, `TriggerTable`, `TriggerFieldRate`, `TriggerFieldRateCount`, `TriggerFieldId`, `OverrideClassName`, `OverrideClassFile`) VALUES
('bx_articles', '[db_prefix]voting', '[db_prefix]voting_track', 'arl_', 5, 'vote_send_result', 'BX_PERIOD_PER_VOTE', 1, '', '', '[db_prefix]entries', 'rate', 'rate_count', 'id', 'BxArlVoting', 'modules/boonex/articles/classes/BxArlVoting.php');

INSERT INTO `sys_objects_tag` (`ObjectName`, `Query`, `PermalinkParam`, `EnabledPermalink`, `DisabledPermalink`, `LangKey`) VALUES
('bx_articles', 'SELECT `tags` FROM `[db_prefix]entries` WHERE `id`={iID} AND `status`=0', 'permalinks_module_articles', 'm/articles/tag/{tag}', 'modules/?r=articles/tag/{tag}', '_articles_lcaption_tags');

INSERT INTO `sys_objects_categories` (`ObjectName`, `Query`, `PermalinkParam`, `EnabledPermalink`, `DisabledPermalink`, `LangKey`) 
VALUES ('bx_articles', 'SELECT `categories` FROM `[db_prefix]entries` WHERE `id`=''{iID}'' AND `status`=''0''', 'permalinks_module_articles', 'm/articles/category/{tag}', 'modules/?r=articles/category/{tag}', '_articles_lcaption_categories');

INSERT INTO `sys_categories` (`Category`, `ID`, `Type`, `Owner`, `Status`) VALUES 
('Default', '0', 'bx_articles', '0', 'active'),
('BoonEx Products', '0', 'bx_articles', '0', 'active'),
('Some Useful Info', '0', 'bx_articles', '0', 'active');

INSERT INTO `sys_objects_search` (`ObjectName`, `Title`, `ClassName`, `ClassPath`) VALUES
('bx_articles', '_articles_lcaption_search_object', 'BxArlSearchResult', 'modules/boonex/articles/classes/BxArlSearchResult.php');

INSERT INTO `sys_objects_views`(`name`, `table_track`, `period`, `trigger_table`, `trigger_field_id`, `trigger_field_views`, `is_on`) VALUES
('bx_articles', '[db_prefix]views_track', 86400, '[db_prefix]entries', 'id', 'view_count', 1);


SET @iPCPOrder = (SELECT MAX(`Order`) FROM `sys_page_compose_pages`);
INSERT INTO `sys_page_compose_pages`(`Name`, `Title`, `Order`) VALUES ('articles_single', 'Single Article', @iPCPOrder+1);

SET @iPCPOrder = (SELECT MAX(`Order`) FROM `sys_page_compose_pages`);
INSERT INTO `sys_page_compose_pages`(`Name`, `Title`, `Order`) VALUES ('articles_home', 'Articles Home', @iPCPOrder+1);

SET @iPCOrder = (SELECT MAX(`Order`) FROM `sys_page_compose` WHERE `Page`='index' AND `Column`='1');
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
('index', '998px', 'Show list of featured articles', '_articles_bcaption_featured', 1, @iPCOrder+1, 'PHP', 'return BxDolService::call(\'articles\', \'featured_block_index\', array(0, 0, false));', 1, 66, 'non,memb', 0),
('index', '998px', 'Show list of latest articles', '_articles_bcaption_latest', 1, @iPCOrder+2, 'PHP', 'return BxDolService::call(\'articles\', \'archive_block_index\', array(0, 0, false));', 1, 66, 'non,memb', 0),
('member', '998px', 'Show list of featured articles', '_articles_bcaption_featured', 2, 3, 'PHP', 'return BxDolService::call(\'articles\', \'featured_block_member\', array(0, 0, false));', 1, 66, 'memb', 0),
('member', '998px', 'Show list of latest articles', '_articles_bcaption_latest', 2, 4, 'PHP', 'return BxDolService::call(\'articles\', \'archive_block_member\', array(0, 0, false));', 1, 66, 'memb', 0),
('articles_single', '998px', 'Articles main content', '_articles_bcaption_view_main', 1, 0, 'Content', '', 1, 66, 'non,memb', 0),
('articles_single', '998px', 'Articles comments', '_articles_bcaption_view_comment', 1, 1, 'Comment', '', 1, 66, 'non,memb', 0),
('articles_single', '998px', 'Articles actions', '_articles_bcaption_view_action', 2, 0, 'Action', '', 1, 34, 'non,memb', 0),
('articles_single', '998px', 'Articles rating', '_articles_bcaption_view_vote', 2, 1, 'Vote', '', 1, 34, 'non,memb', 0),
('articles_home', '998px', 'Articles featured', '_articles_bcaption_featured', 1, 0, 'Featured', '', 1, 34, 'non,memb', 0),
('articles_home', '998px', 'Articles Latest', '_articles_bcaption_latest', 2, 0, 'Latest', '', 1, 66, 'non,memb', 0);

INSERT INTO `sys_objects_actions`(`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`, `bDisplayInSubMenuHeader`) VALUES
('{sbs_articles_title}', 'action_subscribe.png', '', '{sbs_articles_script}', '', 1, 'bx_articles', 0),
('{del_articles_title}', 'action_block.png', '', '{del_articles_script}', '', 2, 'bx_articles', 0);

INSERT INTO `sys_sbs_types`(`unit`, `action`, `template`, `params`) VALUES
('bx_articles', '', '', 'return BxDolService::call(\'articles\', \'get_subscription_params\', array($arg1, $arg2, $arg3));'),
('bx_articles', 'commentPost', 't_sbsArticlesComments', 'return BxDolService::call(\'articles\', \'get_subscription_params\', array($arg1, $arg2, $arg3));'),
('bx_articles', 'rate', 't_sbsArticlesRates', 'return BxDolService::call(\'articles\', \'get_subscription_params\', array($arg1, $arg2, $arg3));');

INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('t_sbsArticlesComments', 'New article comments', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p><b>Dear <RealName></b>,</p><br /><p>The article you subscribed to has new comments!</p><br /> <p>Click <a href="<ViewLink>">here</a> to view them.</p><br /> <p><b>Thank you for using our services!</b></p> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'New article comments subscription.', '0'),
('t_sbsArticlesComments', 'New article comments', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p><b>Dear <RealName></b>,</p><br /><p>The article you subscribed to has new comments!</p><br /> <p>Click <a href="<ViewLink>">here</a> to view them.</p><br /> <p><b>Thank you for using our services!</b></p> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'New article comments subscription.', '1'),
('t_sbsArticlesRates', 'Article was rated', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p><b>Dear <RealName></b>,</p><br /><p>The article you subscribed to was rated!</p><br /> <p>Click <a href="<ViewLink>">here</a> to view it.</p><br /> <p><b>Thank you for using our services!</b></p> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'New article rates subscription.', '0'),
('t_sbsArticlesRates', 'Article was rated', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p><b>Dear <RealName></b>,</p><br /><p>The article you subscribed to was rated!</p><br /> <p>Click <a href="<ViewLink>">here</a> to view it.</p><br /> <p><b>Thank you for using our services!</b></p> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'New article rates subscription.', '1');

INSERT INTO `sys_acl_actions`(`Name`, `AdditionalParamName`) VALUES ('Articles Delete', '');

INSERT INTO `sys_cron_jobs` (`name`, `time`, `class`, `file`, `eval`) VALUES
('bx_articles', '*/5 * * * *', 'BxArlCron', 'modules/boonex/articles/classes/BxArlCron.php', '');
