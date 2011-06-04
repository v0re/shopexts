
    --
    -- Table structure for table `bx_shoutbox_messages`
    --

    CREATE TABLE `[db_prefix]messages` (
      `ID` int(10) unsigned NOT NULL auto_increment,
      `OwnerID` int(11) NOT NULL,
      `Message` text NOT NULL,
      `Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `IP` int(10) unsigned NOT NULL,
      PRIMARY KEY (`ID`),
      KEY `IP` (`IP`)
    ) ENGINE=MyISAM;


    --
    -- Dumping data for table `sys_page_compose`
    --

    INSERT INTO 
        `sys_page_compose` 
    (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`)
        VALUES
    ('index', '960px', 'Shoutbox', '_bx_shoutbox', 2, 5, 'PHP', 'BxDolService::call(''shoutbox'', ''get_shoutbox'');', 1, 50, 'non,memb', 0);

    --
    -- Dumping data for table `sys_acl_actions`
    --

    SET @iLevelNonMember := 1;
    SET @iLevelStandard  := 2;
    SET @iLevelPromotion := 3;

    INSERT INTO `sys_acl_actions` VALUES (NULL, 'shoutbox use', NULL);
    SET @iAction := LAST_INSERT_ID();

    INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
        (@iLevelNonMember, @iAction), 
        (@iLevelStandard, @iAction), 
        (@iLevelPromotion, @iAction);

    INSERT INTO `sys_acl_actions` VALUES (NULL, 'shoutbox delete messages', NULL);
    INSERT INTO `sys_acl_actions` VALUES (NULL, 'shoutbox block by ip', NULL);

    --
    -- Admin menu ;
    --

    INSERT INTO 
        `sys_menu_admin` 
    SET
        `name`          = 'Shoutbox',
        `title`         = '_bx_shoutbox', 
        `url`           = '{siteUrl}modules/?r=shoutbox/administration/',
        `description`   = 'Some shoutbox''s settings',
        `icon`          = 'modules/boonex/shoutbox/|shoutbox.png',
        `parent_id`     = 2;
    
    --
    -- `sys_options_cats` ;
    --

    SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
    INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Shoutbox', @iMaxOrder);
    SET @iKategId = (SELECT LAST_INSERT_ID());

    --
    -- Dumping data for table `sys_options`;
    --

    INSERT INTO 
        `sys_options` 
    SET
        `Name` = 'shoutbox_update_time',
        `kateg` = @iKategId,
        `desc`  = 'Shoutbox update time (in milliseconds)',
        `Type`  = 'digit',
        `VALUE` = '7000',
        `check` = 'return is_numeric($arg0);',
        `order_in_kateg` = 1;

    INSERT INTO 
        `sys_options` 
    SET
        `Name` = 'shoutbox_allowed_messages',
        `kateg` = @iKategId,
        `desc`  = 'The number of the saved messages',
        `Type`  = 'digit',
        `VALUE` = '30',
        `check` = 'return is_numeric($arg0);',
        `order_in_kateg` = 2;
 
    INSERT INTO 
        `sys_options` 
    SET
        `Name` = 'shoutbox_process_smiles',
        `kateg` = @iKategId,
        `desc`  = 'Allow to procces smile''s codes',
        `Type`  = 'checkbox',
        `VALUE` = 'on',
        `order_in_kateg` = 3;
        
   INSERT INTO 
        `sys_options` 
    SET
        `Name` = 'shoutbox_clean_oldest',
        `kateg` = @iKategId,
        `desc`  = 'Clean messages older than (sec)',
        `Type`  = 'digit',
        `VALUE` = '172800',
        `check` = 'return is_numeric($arg0);',
        `order_in_kateg` = 4;

    INSERT INTO 
        `sys_options` 
    SET
        `Name` = 'shoutbox_block_sec',
        `kateg` = @iKategId,
        `desc`  = 'IP blocking time (sec)',
        `Type`  = 'digit',
        `VALUE` = '86400',
        `check` = 'return is_numeric($arg0);',
        `order_in_kateg` = 5;

    --
    -- Dumping data for table `sys_cron_jobs`
    --

    INSERT INTO 
        `sys_cron_jobs` 
    (`name`, `time`, `class`, `file`)
        VALUES
    ('BxShoutBox', '*/5 * * * *', 'BxShoutBoxCron', 'modules/boonex/shoutbox/classes/BxShoutBoxCron.php');
