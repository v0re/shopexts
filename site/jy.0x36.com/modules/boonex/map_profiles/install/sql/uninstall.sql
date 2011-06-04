
DROP TABLE IF EXISTS `[db_prefix]profiles`;
DROP TABLE IF EXISTS `[db_prefix]cities`;
DROP TABLE IF EXISTS `[db_prefix]countries`;

DELETE FROM `sys_page_compose_pages` WHERE `Name` IN('bx_map', 'bx_map_edit');
DELETE FROM `sys_page_compose` WHERE `Page` IN('bx_map', 'bx_map_edit');
DELETE FROM `sys_page_compose` WHERE `Page` = 'index' AND `Desc` = 'World Map';
DELETE FROM `sys_page_compose` WHERE `Page` = 'profile' AND `Desc` = 'Map';

DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=map_profiles/';

DELETE FROM `sys_menu_top` WHERE `Parent` = 6 AND `Name` = 'Profiles Map';

DELETE FROM `sys_menu_admin` WHERE `name` = 'bx_map';

SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Profiles Map General' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Profiles Map Homepage' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Profiles Map Separate' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Profiles Map Profile' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Profiles Map Hidden' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
DELETE FROM `sys_options` WHERE `Name` = 'bx_map_permalinks';

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'bx_map' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

-- privacy
DELETE FROM `sys_privacy_actions` WHERE `module_uri` = 'map_profiles';
