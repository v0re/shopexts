--
-- Table structure for table `[db_prefix]events`
--
CREATE TABLE IF NOT EXISTS `[db_prefix]events` (
  `id` bigint(8) NOT NULL auto_increment,
  `owner_id` int(10) unsigned NOT NULL default '0',
  `object_id` int(10) unsigned NOT NULL,
  `type` varchar(255) collate utf8_unicode_ci NOT NULL,
  `action` varchar(255) collate utf8_unicode_ci NOT NULL,
  `content` text collate utf8_unicode_ci NOT NULL,
  `title` varchar(255) collate utf8_unicode_ci NOT NULL,
  `description` text collate utf8_unicode_ci NOT NULL,
  `date` int(8) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `owner_id` (`owner_id`),
  KEY `object_id` (`object_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `[db_prefix]handlers`
--
CREATE TABLE IF NOT EXISTS `[db_prefix]handlers` (
  `id` int(11) NOT NULL auto_increment,
  `alert_unit` varchar(64) NOT NULL default '',
  `alert_action` varchar(64) NOT NULL default '',
  `module_uri` varchar(64) NOT NULL default '',
  `module_class` varchar(64) NOT NULL default '',
  `module_method` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE `handler` (`alert_unit`, `alert_action`, `module_uri`, `module_class`, `module_method`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


SELECT @iPCPOrder:=MAX(`Order`) FROM `sys_page_compose_pages`;
INSERT INTO `sys_page_compose_pages`(`Name`, `Title`, `Order`) VALUES ('wall', 'Wall', @iPCPOrder+1);

SELECT @iPCOrder:=MAX(`Order`) FROM `sys_page_compose` WHERE `Page`='profile' AND `Column`='2';
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
('profile', '998px', 'Post event to a Wall', '_wall_pc_post', 2, @iPCOrder+1, 'PHP', 'return BxDolService::call(\'wall\', \'post_block\', array($this->oProfileGen->_iProfileID));', 1, 64, 'non,memb', 0),
('profile', '998px', 'View events on a Wall', '_wall_pc_view', 2, @iPCOrder+2, 'PHP', 'return BxDolService::call(\'wall\', \'view_block\', array($this->oProfileGen->_iProfileID));', 1, 64, 'non,memb', 0),
('wall', '998px', 'Post event to a Wall', '_wall_pc_post', 1, 0, 'Post', '', 1, 100, 'non,memb', 0),
('wall', '998px', 'View events on a Wall', '_wall_pc_view', 1, 1, 'View', '', 1, 100, 'non,memb', 0);


SELECT @iTMOrderOwner:=MAX(`Order`) FROM `sys_menu_top` WHERE `Parent`='4';
SELECT @iTMOrderViewer:=MAX(`Order`) FROM `sys_menu_top` WHERE `Parent`='9';
INSERT INTO `sys_menu_top` (`Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `BQuickLink`, `Statistics`) VALUES
(4, 'WallOwner', '_wall_top_smenu_item_my_wall', 'modules/?r=wall/', @iTMOrderOwner+1, 'memb', '', '', '', 1, 1, 1, 'custom', '', 0, ''),
(9, 'WallViewer', '_wall_top_smenu_item_wall', 'modules/?r=wall/index/{profileNick}', @iTMOrderViewer+1, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', 0, '');


SET @iOrder = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id`='2');
INSERT INTO `sys_menu_admin`(`parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) VALUES
(2, 'bx_wall', '_wall_admin_menu_sitem', '{siteUrl}modules/?r=wall/admin/', 'For managing wall', 'modules/boonex/wall/|admin_menu_icon.png', '', '', @iOrder+1);


INSERT INTO `sys_objects_cmts` (`ObjectName`, `TableCmts`, `TableTrack`, `AllowTags`, `Nl2br`, `SecToEdit`, `PerView`, `IsRatable`, `ViewingThreshold`, `AnimationEffect`, `AnimationSpeed`, `IsOn`, `IsMood`, `RootStylePrefix`, `TriggerTable`, `TriggerFieldId`, `TriggerFieldComments`, `ClassName`, `ClassFile`)
VALUES('bx_wall', '[db_prefix]comments', '[db_prefix]comments_track', 0, 1, 90, 9999, 1, -3, 'slide', 2000, 1, 1, 'wcmt', '', '', '', 'BxWallCmts', 'modules/boonex/wall/classes/BxWallCmts.php');



SET @iCategoryOrder = (SELECT MAX(`menu_order`) FROM `sys_options_cats`) + 1;
INSERT INTO `sys_options_cats` (`name` , `menu_order` ) VALUES ('Wall', @iCategoryOrder);
SET @iCategoryId = LAST_INSERT_ID();

INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`) VALUES
('permalinks_module_wall', 'on', 26, 'Enable friendly wall permalink', 'checkbox', '', '', 0),
('wall_enable_guest_comments', '', @iCategoryId, 'Allow non-members to post in wall', 'checkbox', '', '', 0),
('wall_enable_delete', 'on', @iCategoryId, 'Allow wall owner to remove events', 'checkbox', '', '', 0),
('wall_events_per_page', '5', @iCategoryId, 'Number of events are displayed on the page', 'digit', '', '', 0),
('wall_rss_length', '5', @iCategoryId, 'The length of RSS feed', 'digit', '', '', 0);


SET @iLevelNonMember := 1;
SET @iLevelStandard := 2;
SET @iLevelPromotion := 3;

INSERT INTO `sys_acl_actions`(`Name`, `AdditionalParamName`) VALUES ('Wall Post Comment', '');
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
(@iLevelStandard, @iAction), 
(@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions`(`Name`, `AdditionalParamName`) VALUES ('Wall Delete Comment', '');


INSERT INTO `sys_categories`(`Category`, `ID`, `Type`, `Owner`, `Status`) VALUES
('wall', 0, 'bx_photos', 0, 'active'),
('wall', 0, 'bx_sounds', 0, 'active'),
('wall', 0, 'bx_videos', 0, 'active');


INSERT INTO `sys_permalinks`(`standard`, `permalink`, `check`) VALUES('modules/?r=wall/', 'm/wall/', 'permalinks_module_wall');


INSERT INTO `sys_alerts_handlers`(`name`, `class`, `file`, `eval`) VALUES ('bx_wall', '', '', 'BxDolService::call(\'wall\', \'response\', array($this));');
SET @iHandlerId = LAST_INSERT_ID();

INSERT INTO `sys_alerts` (`unit`, `action`, `handler_id`) VALUES('profile', 'edit', @iHandlerId);
INSERT INTO `sys_alerts` (`unit`, `action`, `handler_id`) VALUES('profile', 'edit_status_message', @iHandlerId);
INSERT INTO `sys_alerts` (`unit`, `action`, `handler_id`) VALUES('profile', 'delete', @iHandlerId);
INSERT INTO `sys_alerts` (`unit`, `action`, `handler_id`) VALUES('friend', 'accept', @iHandlerId);

INSERT INTO `sys_menu_member` SET `Name`='bx_wall', `Eval`='return BxDolService::call(''wall'', ''get_member_menu_item'', array({ID}));', `Type`='linked_item', `Parent`='1';

INSERT INTO `sys_sbs_types`(`unit`, `action`, `template`, `params`) VALUES
('bx_wall', '', '', 'return BxDolService::call(\'wall\', \'get_subscription_params\', array($arg1, $arg2, $arg3));'),
('bx_wall', 'update', 't_sbsWallUpdates', 'return BxDolService::call(\'wall\', \'get_subscription_params\', array($arg1, $arg2, $arg3));');

INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('t_sbsWallUpdates', 'New wall event', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p><b>Dear <RealName></b>,</p><br /><p>The wall you subscribed to has new event!</p><br /> <p>Click <a href="<ViewLink>">here</a> to view it.</p><br /> <p><b>Thank you for using our services!</b></p> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'New wall events subscription.', '0'),
('t_sbsWallUpdates', 'New wall event', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p><b>Dear <RealName></b>,</p><br /><p>The wall you subscribed to has new event!</p><br /> <p>Click <a href="<ViewLink>">here</a> to view it.</p><br /> <p><b>Thank you for using our services!</b></p> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'New wall events subscription.', '1');
