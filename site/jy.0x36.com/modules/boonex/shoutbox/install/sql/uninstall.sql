
    DROP TABLE IF EXISTS `[db_prefix]messages`;

    DELETE FROM 
        `sys_page_compose` 
    WHERE
        `Page` = 'index'
            AND
        `Caption` = '_bx_shoutbox';

    SET @iActionId := (SELECT `ID` FROM  `sys_acl_actions` WHERE `Name` =  'shoutbox use' LIMIT 1);
    DELETE FROM `sys_acl_actions` WHERE `ID` = @iActionId;
    DELETE FROM `sys_acl_matrix` WHERE `IDAction` = @iActionId;

    SET @iActionId := (SELECT `ID` FROM  `sys_acl_actions` WHERE `Name` =  'shoutbox delete messages' LIMIT 1);
    DELETE FROM `sys_acl_actions` WHERE `ID` = @iActionId;
    DELETE FROM `sys_acl_matrix` WHERE `IDAction` = @iActionId;

    SET @iActionId := (SELECT `ID` FROM  `sys_acl_actions` WHERE `Name` =  'shoutbox block by ip' LIMIT 1);
    DELETE FROM `sys_acl_actions` WHERE `ID` = @iActionId;
    DELETE FROM `sys_acl_matrix` WHERE `IDAction` = @iActionId;

    --
    -- Admin menu ;
    --

    DELETE FROM 
        `sys_menu_admin` 
    WHERE
        `name` = 'Shoutbox';

    --
    -- `sys_options_cats` ;
    --

    SET @iKategId = (SELECT `id` FROM `sys_options_cats` WHERE `name` = 'Shoutbox' LIMIT 1);
    DELETE FROM `sys_options_cats` WHERE `id` = @iKategId;

    --
    -- `sys_options` ;
    --

    DELETE FROM `sys_options` WHERE `kateg` = @iKategId;
 
    --
    -- `sys_cron_jobs`
    --

    DELETE FROM
        `sys_cron_jobs` 
    WHERE
   		`name` = 'BxShoutBox';