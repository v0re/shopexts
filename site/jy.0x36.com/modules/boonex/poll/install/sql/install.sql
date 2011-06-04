
    --
    -- Table structure for table `bx_poll_data`
    --

    CREATE TABLE `[db_prefix]data` (
      `id_poll` int(11) NOT NULL auto_increment,
      `id_profile` int(11) NOT NULL default '0',
      `poll_question` varchar(255) NOT NULL default '',
      `poll_answers` text NOT NULL,
      `poll_results` varchar(60) NOT NULL default '',
      `poll_total_votes` int(11) NOT NULL default '0',
      `poll_status` varchar(20) NOT NULL default '',
      `poll_approval` tinyint(4) NOT NULL default '0',
      `poll_date` int(10) NOT NULL,
      `poll_rate` int(11) NOT NULL,
      `poll_rate_count` int(11) NOT NULL,
      `poll_comments_count` int(11) NOT NULL,
      `poll_tags` varchar(255) NOT NULL,
      `poll_featured` tinyint(4) NOT NULL default '0',
      `allow_comment_to` int(11) NOT NULL default '3',
      `allow_vote_to` int(11) NOT NULL default '3',
      `allow_view_to` int(11) NOT NULL default '3',
      `poll_categories` varchar(255) NOT NULL,
      PRIMARY KEY  (`id_poll`),
      KEY `id_profile` (`id_profile`),
      KEY `poll_featured` (`poll_featured`),
      FULLTEXT KEY `ftMain` (`poll_question`,`poll_answers`,`poll_tags`,`poll_categories`),
      FULLTEXT KEY `poll_tags` (`poll_tags`),
      FULLTEXT KEY `poll_categories` (`poll_categories`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

    --
    -- `sys_options_cats` ;
    --

    SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
    INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Polls', @iMaxOrder);
    SET @iKatId = (SELECT LAST_INSERT_ID());

    --
    -- sys_options ;
    --

    INSERT INTO 
        `sys_options` 
    SET 
        `Name` = 'enable_poll', 
        `VALUE` = 'on', 
        `kateg` = @iKatId, 
        `desc` = 'Enable members polls', 
        `Type` = 'checkbox';

    INSERT INTO 
        `sys_options` 
    SET 
        `Name` = 'profile_poll_num', 
        `VALUE` = '4', 
        `kateg` = @iKatId, 
        `desc` = 'Number of polls that user can create', 
        `Type` = 'digit';

    INSERT INTO 
        `sys_options` 
    SET 
        `Name` = 'profile_page_polls', 
        `VALUE` = '2', 
        `kateg` = @iKatId, 
        `desc` = 'Number of polls visible on profile page', 
        `Type` = 'digit';

    INSERT INTO 
        `sys_options` 
    SET 
        `Name` = 'index_page_polls', 
        `VALUE` = '2', 
        `kateg` = @iKatId, 
        `desc` = 'Number of polls visible on index page', 
        `Type` = 'digit';

    INSERT INTO 
        `sys_options` 
    SET 
        `Name` = 'profile_poll_act', 
        `VALUE` = 'on', 
        `kateg` = @iKatId, 
        `desc` = 'Enable profile polls activation', 
        `Type` = 'checkbox';

    INSERT INTO 
        `sys_options` 
    SET 
        `Name` = 'category_auto_app_bx_poll', 
        `VALUE` = 'on', 
        `kateg` = @iKatId, 
        `desc` = 'Activate all categories for all polls after creation automatically', 
        `Type` = 'checkbox';

    --
    -- Site stats;
    --

    INSERT INTO 
        `sys_stat_site` 
    SET 
        `Name`       = 'pls', 
        `Title`      = 'bx_polls', 
        `UserLink`   = 'modules/?r=poll/&action=poll_home',
        `UserQuery`  = 'SELECT COUNT(`id_poll`) FROM `bx_poll_data` WHERE `poll_approval`=1 and `poll_status` = ''active'' ', 
        `AdminLink`  = '../modules/?r=poll/administration', 
        `AdminQuery` = 'SELECT COUNT(`id_poll`) FROM `bx_poll_data` WHERE `poll_approval`=0', 
        `IconName`   = 'modules/boonex/poll/|pls.png', 
        `StatOrder`  = 10;
        
    --
    -- Admin menu ;
    --

    INSERT INTO 
        `sys_menu_admin` 
    SET
        `name`          = 'Polls',
        `title`         = '_bx_polls', 
        `url`           = '{siteUrl}modules/?r=poll/administration/',
        `description`   = 'Members can create their own polls, and you can moderate them right here',
        `icon`          = 'modules/boonex/poll/|pls.png',
        `parent_id`     = 2;

    --     
    -- Top menu ;
    -- 

    INSERT INTO 
        `sys_menu_top` 
    SET
        `Parent`     = 0, 
        `Name`       = 'Poll unit', 
        `Caption`    = '',
        `Link`       = 'm/poll/?action=my&action=show_poll_info&id=|modules/?r=poll/&action=show_poll_info&id=', 
        `Order`      = 1, 
        `Visible`    = 'non,memb', 
        `Editable`   = 1, 
        `Deletable`  = 1, 
        `Active`     = 1, 
        `Type`       = 'system', 
        `Picture`    = 'modules/boonex/poll/|bx_polls.png', 
        `BQuickLink` = 0;

    SET @iMaxMenuOrder := (SELECT `Order` + 1 FROM `sys_menu_top` WHERE `Parent` = 0 ORDER BY `Order` DESC LIMIT 1);

    INSERT INTO 
        `sys_menu_top` 
    SET
        `Name`       = 'Polls', 
        `Caption`    = '_bx_polls', 
        `Link`       = 'modules/?r=poll/&action=poll_home', 
        `Order`      = @iMaxMenuOrder, 
        `Visible`    = 'non,memb', 
        `Editable`   = 1, 
        `Deletable`  = 1, 
        `Active`     = 1, 
        `Type`       = 'top', 
        `Picture`    = 'modules/boonex/poll/|bx_polls.png', 
        `BQuickLink` = 1;

    SET @iMenuParentId = (SELECT LAST_INSERT_ID());

    INSERT INTO 
        `sys_menu_top` 
    SET
        `Parent`     = @iMenuParentId, 
        `Name`       = 'Home polls', 
        `Caption`    = '_bx_poll_home',
        `Link`       = 'modules/?r=poll/&action=poll_home', 
        `Order`      = 1, 
        `Visible`    = 'non,memb', 
        `Editable`   = 1, 
        `Deletable`  = 1, 
        `Active`     = 1, 
        `Type`       = 'custom',
        `Picture`    = 'modules/boonex/poll/|bx_polls.png', 
        `BQuickLink` = 0;

    INSERT INTO 
        `sys_menu_top` 
    SET
        `Parent`     = @iMenuParentId, 
        `Name`       = 'All Polls', 
        `Caption`    = '_bx_poll_all',
        `Link`       = 'modules/?r=poll/', 
        `Order`      = 2, 
        `Visible`    = 'non,memb', 
        `Editable`   = 1, 
        `Deletable`  = 1, 
        `Active`     = 1, 
        `Type`       = 'custom',
        `Picture`    = 'modules/boonex/poll/|bx_polls.png', 
        `BQuickLink` = 0;

    INSERT INTO 
        `sys_menu_top` 
    SET
        `Parent`     = @iMenuParentId, 
        `Name`       = 'Popular', 
        `Caption`    = '_bx_poll_popular',
        `Link`       = 'modules/?r=poll/&action=popular', 
        `Order`      = 3, 
        `Visible`    = 'non,memb', 
        `Editable`   = 1, 
        `Deletable`  = 1, 
        `Active`     = 1, 
        `Type`       = 'custom',
        `Picture`    = 'modules/boonex/poll/|bx_polls.png', 
        `BQuickLink` = 0;

    INSERT INTO 
        `sys_menu_top` 
    SET
        `Parent`     = @iMenuParentId, 
        `Name`       = 'Featured', 
        `Caption`    = '_bx_poll_featured',
        `Link`       = 'modules/?r=poll/&action=featured', 
        `Order`      = 4, 
        `Visible`    = 'non,memb', 
        `Editable`   = 1, 
        `Deletable`  = 1, 
        `Active`     = 1, 
        `Type`       = 'custom',
        `Picture`    = 'modules/boonex/poll/|bx_polls.png', 
        `BQuickLink` = 0;

    INSERT INTO 
        `sys_menu_top` 
    SET
        `Parent`     = @iMenuParentId, 
        `Name`       = 'Calendar', 
        `Caption`    = '_bx_poll_calendar',
        `Link`       = 'modules/?r=poll/calendar', 
        `Order`      = 5, 
        `Visible`    = 'non,memb', 
        `Editable`   = 1, 
        `Deletable`  = 1, 
        `Active`     = 1, 
        `Type`       = 'custom',
        `Picture`    = 'modules/boonex/poll/|bx_polls.png', 
        `BQuickLink` = 0;

    INSERT INTO 
        `sys_menu_top` 
    SET
        `Parent`     = @iMenuParentId, 
        `Name`       = 'Search', 
        `Caption`    = '_bx_poll_search',
        `Link`       = 'searchKeyword.php?type=poll', 
        `Order`      = 6, 
        `Visible`    = 'non,memb', 
        `Editable`   = 1, 
        `Deletable`  = 1, 
        `Active`     = 1, 
        `Type`       = 'custom', 
        `Picture`    = 'modules/boonex/poll/|bx_polls.png', 
        `BQuickLink` = 0;

    INSERT INTO 
        `sys_menu_top` 
    SET
        `Parent`     = @iMenuParentId, 
        `Name`       = 'Tags', 
        `Caption`    = '_bx_poll_tags',
        `Link`       = 'modules/?r=poll/tags', 
        `Order`      = 7, 
        `Visible`    = 'non,memb', 
        `Editable`   = 1, 
        `Deletable`  = 1, 
        `Active`     = 1, 
        `Type`       = 'custom',
        `Picture`    = 'modules/boonex/poll/|bx_polls.png', 
        `BQuickLink` = 0;

    INSERT INTO 
        `sys_menu_top` 
    SET
        `Parent`     = @iMenuParentId, 
        `Name`       = 'Categories', 
        `Caption`    = '_bx_poll_categories',
        `Link`       = 'modules/?r=poll/categories', 
        `Order`      = 8, 
        `Visible`    = 'non,memb', 
        `Editable`   = 1, 
        `Deletable`  = 1, 
        `Active`     = 1, 
        `Type`       = 'custom',
        `Picture`    = 'modules/boonex/poll/|bx_polls.png', 
        `BQuickLink` = 0;

    INSERT INTO 
        `sys_menu_top` 
    SET
        `Parent`     = 4, 
        `Name`       = 'My Polls', 
        `Caption`    = '_bx_polls',
        `Link`       = 'modules/?r=poll/&action=my', 
        `Order`      = 4, 
        `Visible`    = 'memb', 
        `Editable`   = 1, 
        `Deletable`  = 1, 
        `Active`     = 1, 
        `Type`       = 'custom';

    INSERT INTO 
        `sys_menu_top` 
    SET
        `Parent`     = 9, 
        `Name`       = 'Polls', 
        `Caption`    = '_bx_poll',
        `Link`       = 'modules/?r=poll/&action=user&nickname={profileNick}', 
        `Order`      = 4, 
        `Visible`    = 'non,memb', 
        `Editable`   = 1, 
        `Deletable`  = 1, 
        `Active`     = 1, 
        `Type`       = 'custom';
        
    --
    -- sys_account_custom_stat_elements ;
    --

    INSERT INTO 
        `sys_account_custom_stat_elements` 
    SET
        `Label` = '_bx_polls', 
        `Value` = '__spo__ __l_bx_polls__ (<a href="modules/?r=poll&action=my">__l_bx_poll_create__</a>)';

    --
    -- sys_stat_member ;
    --

    INSERT INTO 
        `sys_stat_member` 
    SET 
        `Type` = 'spo', 
        `SQL`  = 'SELECT COUNT(*) FROM `[db_prefix]data` WHERE `id_profile` = ''__member_id__'' AND `poll_status`=''active'' AND `poll_approval` = 1';

    --
    -- sys_page_compose ;
    --

    INSERT INTO 
        `sys_page_compose` 
    SET
        `Page`      = 'profile', 
        `PageWidth` = '998px', 
        `Desc`      = 'Member''s polls block', 
        `Caption`   = '_bx_polls', 
        `Column`    = 1, 
        `Order`     = 5, 
        `Func`      = 'PHP',
        `Content`  = 'BxDolService::call(''poll'', ''get_polls'', array(''get_profile_polls'', $this->oProfileGen->_iProfileID));',    
        `DesignBox` = 0, 
        `ColWidth`  = 50, 
        `Visible`   = 'non,memb';

    INSERT INTO 
        `sys_page_compose` 
    SET
        `Page`      = 'index', 
        `PageWidth` = '998px', 
        `Desc`      = 'Member''s polls block', 
        `Caption`   = '_bx_polls', 
        `Column`    = 2, 
        `Order`     = 5, 
        `Func`      = 'PHP',
        `Content`  = 'BxDolService::call(''poll'', ''get_polls'', array(''get_polls''));',    
        `DesignBox` = 0, 
        `ColWidth`  = 50, 
        `Visible`   = 'non,memb';

    INSERT INTO 
        `sys_menu_member` 
    SET 
        `Name` = 'bx_poll',
        `Eval` = 'return BxDolService::call(''poll'', ''get_member_menu_link'', array(''{ID}''));',
        `Parent` = '1',
        `Type` = 'linked_item';

    --
    -- Dumping data for table `sys_page_compose`
    --

    INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES('show_poll_info', '998px', 'Poll''s owner information', '_bx_poll_owner', 1, 1, 'OwnerBlock', '', 1, 37, 'non,memb', 0);
    INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES('show_poll_info', '998px', 'Poll''s vote block', '_bx_poll_votings', 1, 3, 'VotingsBlock', '', 1, 37, 'non,memb', 0);
    INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES('show_poll_info', '998px', 'Poll''s comments block', '_bx_poll_comments', 2, 2, 'CommentsBlock', '', 1, 63, 'non,memb', 0);
    INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES('show_poll_info', '998px', 'Poll''s block', '_bx_poll', 2, 1, 'PoolBlock', '', 1, 63, 'non,memb', 0);
    INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES('show_poll_info', '998px', 'Poll''s action block', '_bx_poll_actions', 1, 0, 'ActionsBlock', '', 1, 37, 'non,memb', 0);

    INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES('poll_home', '998px', 'Featured polls', '_bx_poll_featured', 1, 0, 'FeaturedHome', '', 1, 34, 'non,memb', 0);
    INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES('poll_home', '998px', 'Latest polls', '_bx_poll_latest_public', 2, 0, 'LatestHome', '', 1, 66, 'non,memb', 0);

    --
    -- Dumping data for table `sys_page_compose_pages`
    --

    INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES('show_poll_info', 'View poll', 32);
    INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES('poll_home', 'Polls home', 33);

    --
    -- Table structure for table `poll_rating`
    --

    CREATE TABLE `[db_prefix]rating` (
      `id` bigint(8) NOT NULL default '0',
      `rating_count` int(11) NOT NULL default '0',
      `rating_sum` int(11) NOT NULL default '0',
      UNIQUE KEY `med_id` (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

    --
    -- Table structure for table `poll_voting_track`
    --

    CREATE TABLE `[db_prefix]voting_track` (
      `id` int(12) NOT NULL default '0',
      `ip` varchar(20) default NULL,
      `date` datetime default NULL,
      KEY `med_ip` (`ip`,`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

    --
    -- Dumping data for table `sys_objects_vote`
    --

    INSERT INTO 
        `sys_objects_vote` 
            (`ObjectName`, `TableRating`, `TableTrack`, `RowPrefix`, `MaxVotes`, 
                `PostName`, `IsDuplicate`, `IsOn`, `className`, `classFile`, `TriggerTable`, 
                `TriggerFieldRate`, `TriggerFieldRateCount`, `TriggerFieldId`, `OverrideClassName`, `OverrideClassFile`) 
    VALUES
            ('bx_poll', '[db_prefix]rating', '[db_prefix]voting_track', '', 5, 
                'vote_send_result', 'BX_PERIOD_PER_VOTE', 1, '', '', '[db_prefix]data', 
                    'poll_rate', 'poll_rate_count', 'id_poll', '', '');
                
    --
    -- Table structure for table `PollsCmtsTrack`
    --

    CREATE TABLE `[db_prefix]cmts_track` (
      `cmt_system_id` int(11) NOT NULL default '0',
      `cmt_id` int(11) NOT NULL default '0',
      `cmt_rate` tinyint(4) NOT NULL default '0',
      `cmt_rate_author_id` int(10) unsigned NOT NULL default '0',
      `cmt_rate_author_nip` int(11) unsigned NOT NULL default '0',
      `cmt_rate_ts` int(11) NOT NULL default '0',
      PRIMARY KEY  (`cmt_system_id`,`cmt_id`,`cmt_rate_author_nip`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

    --
    -- Table structure for table `PollsCmts`
    --

    CREATE TABLE `[db_prefix]cmts` (
      `cmt_id` int(11) NOT NULL auto_increment,
      `cmt_parent_id` int(11) NOT NULL default '0',
      `cmt_object_id` int(12) NOT NULL default '0',
      `cmt_author_id` int(10) unsigned NOT NULL default '0',
      `cmt_text` text NOT NULL,
      `cmt_mood` tinyint(4) NOT NULL default '0',
      `cmt_rate` int(11) NOT NULL default '0',
      `cmt_rate_count` int(11) NOT NULL default '0',
      `cmt_time` datetime NOT NULL default '0000-00-00 00:00:00',
      `cmt_replies` int(11) NOT NULL default '0',
      PRIMARY KEY  (`cmt_id`),
      KEY `cmt_object_id` (`cmt_object_id`,`cmt_parent_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

    --
    -- Dumping data for table `sys_objects_cmts`
    --

    INSERT INTO 
        `sys_objects_cmts` 
            (`ObjectName`, `TableCmts`, `TableTrack`, `AllowTags`, `Nl2br`, `SecToEdit`, `PerView`, `IsRatable`, `ViewingThreshold`, `AnimationEffect`, `AnimationSpeed`, `IsOn`, `IsMood`, `RootStylePrefix`, `TriggerTable`, `TriggerFieldId`, `TriggerFieldComments`, `ClassName`, `ClassFile`) 
    VALUES
            ('bx_poll', '[db_prefix]cmts', '[db_prefix]cmts_track', 0, 1, 90, 5, 1, -3, 'slide', 2000, 1, 1, 'cmt', '[db_prefix]data', 'id_poll', 'poll_comments_count', '', '');
 
    --
    -- Dumping data for table `sys_objects_actions`
    --

    INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`, `bDisplayInSubMenuHeader`) VALUES('_bx_poll_add', 'modules/boonex/poll/|pls.png', '{evalResult}', '', 'return isMember() ? ''{BaseUri}&action=my&mode=add'' : null;', 1, 'bx_poll', 1);
    INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`, `bDisplayInSubMenuHeader`) VALUES('_bx_poll_my', 'modules/boonex/poll/|pls.png', '{evalResult}', '', 'return isMember() ? ''{BaseUri}&action=my'' : null;', 1, 'bx_poll', 1);
    INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`, `bDisplayInSubMenuHeader`) VALUES('_bx_poll_edit', 'edit.gif', '{evalResult}', '', 'return isMember() ? BxDolService::call(''poll'', ''edit_action_button'', array({ID}, {PollId})) : null;', 2, 'bx_poll', 0);
    INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`, `bDisplayInSubMenuHeader`) VALUES('_bx_poll_delete', 'delete.gif', '{evalResult}', '', 'return isMember() ? BxDolService::call(''poll'', ''delete_action_button'', array({ID}, {PollId})) : null;', 3, 'bx_poll', 0);
    INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`, `bDisplayInSubMenuHeader`) VALUES('_bx_poll_share', 'action_share.png', '', 'showPopupAnyHtml (\'{BaseUri}share_popup/{PollId}\');', '', 1, 'bx_poll', 0);
    INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`, `bDisplayInSubMenuHeader`) VALUES ('{sbs_poll_title}', 'action_subscribe.png', '', '{sbs_poll_script}', '', 1, 'bx_poll', 0);
    INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`, `bDisplayInSubMenuHeader`) VALUES('_bx_poll_home', 'modules/boonex/poll/|pls.png', '{evalResult}', '', 'return isMember() ? ''{BaseUri}&action=poll_home'' : null;', 1, 'bx_poll', 1);

    --
    -- Dumping data for table `sys_objects_actions`
    --

    INSERT INTO `sys_sbs_types`(`unit`, `action`, `template`, `params`) VALUES
    ('bx_poll', '', '', 'return array(''template'' => array(''Subscription'' => _t(''_bx_poll_sbs_main''), ''ViewLink'' => BX_DOL_URL_MODULES . ''?r=poll/&action=show_poll_info&id='' . $arg3));'),
    ('bx_poll', 'commentPost', 't_sbsPollComments', 'return array(''template'' => array(''Subscription'' => _t(''_bx_poll_sbs_comment''), ''ViewLink'' => BX_DOL_URL_MODULES . ''?r=poll/&action=show_poll_info&id='' . $arg3));'),
    ('bx_poll', 'rate', 't_sbsPollRates', 'return array(''template'' => array(''Subscription'' => _t(''_bx_poll_sbs_votes''), ''ViewLink'' => BX_DOL_URL_MODULES . ''?r=poll/&action=show_poll_info&id='' . $arg3));');

    
    INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
    ('t_sbsPollComments', 'New polls comments', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p><b>Dear <RealName></b>,</p><br /><p>The polls you subscribed to has new comments!</p><br /> <p>Click <a href="<ViewLink>">here</a> to view them.</p><br /> <p><b>Thank you for using our services!</b></p> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'New polls comments subscription.', '0'),
    ('t_sbsPollRates', 'Polls was rated', '<html><head></head><body style="font: 12px Verdana; color:#000000"> <p><b>Dear <RealName></b>,</p><br /><p>The polls you subscribed to was rated!</p><br /> <p>Click <a href="<ViewLink>">here</a> to view it.</p><br /> <p><b>Thank you for using our services!</b></p> <p>--</p> <p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!! <br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'New polls rates subscription.', '0');

    -- permalink

    INSERT INTO 
        `sys_permalinks` 
    SET
        `standard`  = 'modules/?r=poll/', 
        `permalink` = 'm/poll/', 
        `check`     = 'bx_poll_permalinks';

    -- settings

    INSERT INTO 
        `sys_options` 
    (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) 
        VALUES
    ('bx_poll_permalinks', 'on', 26, 'Enable friendly permalinks in polls', 'checkbox', '', '', '0', '');

    --
    -- Dumping data for table `sys_objects_tag`
    --

    INSERT INTO 
        `sys_objects_tag` 
    (`ObjectName`, `Query`, `PermalinkParam`, `EnabledPermalink`, `DisabledPermalink`, `LangKey`) 
        VALUES
    ('bx_poll', 'SELECT `poll_tags` FROM `[db_prefix]data` WHERE `id_poll` = {iID} AND `poll_approval` = 1 AND `poll_status` = ''active'' ', 'bx_poll_permalinks', 'm/poll/tag/{tag}', 'modules/?r=poll/tag/{tag}', '_bx_polls');

    INSERT INTO 
        `sys_privacy_actions` 
            (`module_uri`, `name`, `title`, `default_group`) 
        VALUES
            ('poll', 'comment', '_bx_poll_allow_comment', 3),
            ('poll', 'view', '_bx_poll_allow_view', 3),
            ('poll', 'vote', '_bx_poll_allow_vote', 3);
        
    INSERT INTO 
        `sys_objects_categories` 
    VALUES 
        ('', 'bx_poll', 'SELECT `poll_categories` FROM `[db_prefix]data` WHERE `id_poll`  = {iID} AND `poll_approval` = 1 AND `poll_status` = ''active''', 'bx_poll_permalinks', 'm/poll/?action=category&category={tag}', 'modules/?r=poll/&action=category&category={tag}', '_bx_polls');

    INSERT INTO 
        `sys_categories` 
    (`Category`, `ID`, `Type`, `Owner`, `Status`) 
        VALUES 
    ('Best', 0, 'bx_poll', 0, 'active'),
    ('Fun', 0, 'bx_poll', 0, 'active'),
    ('Other', '0', 'bx_poll', '0', 'active');

    --
    -- `sys_alerts_handlers` ;
    --

    INSERT INTO
        `sys_alerts_handlers`
    SET
        `name`  = 'bx_poll',
        `class` = 'BxPollResponse',
        `file`  = 'modules/boonex/poll/classes/BxPollResponse.php';

    SET @iHandlerId = (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'bx_poll'  LIMIT 1);
   
    --
    -- `sys_alerts` ;
    --

    INSERT INTO
        `sys_alerts`
    SET
        `unit`       = 'profile',
        `action`     = 'delete',
        `handler_id` = @iHandlerId;

    --
    -- `sys_objects_search` ;
    --

    INSERT INTO
        `sys_objects_search`
    SET
        `ObjectName` = 'poll',
        `Title`      = '_bx_polls',
        `ClassName`  = 'BxPollSearch',
        `ClassPath`  = 'modules/boonex/poll/classes/BxPollSearch.php';
    
    --
    -- Dumping data for table `sys_acl_actions`
    --

    SET @iLevelStandard  := 2;
    SET @iLevelPromotion := 3;

    INSERT INTO `sys_acl_actions` VALUES (NULL, 'create polls', NULL);
    SET @iAction := LAST_INSERT_ID();

    INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
        (@iLevelStandard, @iAction), 
        (@iLevelPromotion, @iAction);