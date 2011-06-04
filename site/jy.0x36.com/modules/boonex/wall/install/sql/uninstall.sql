DROP TABLE IF EXISTS `[db_prefix]events`;
DROP TABLE IF EXISTS `[db_prefix]handlers`;
DROP TABLE IF EXISTS `[db_prefix]comments`;
DROP TABLE IF EXISTS `[db_prefix]comments_track`;

DELETE FROM `sys_page_compose_pages` WHERE `Name`='wall';
DELETE FROM `sys_page_compose` WHERE `Caption` IN ('_wall_pc_post', '_wall_pc_view');

DELETE FROM `sys_menu_top` WHERE `Name` IN ('WallOwner', 'WallViewer');
DELETE FROM `sys_menu_admin` WHERE `name`='bx_wall';

DELETE FROM `sys_objects_cmts` WHERE `ObjectName`='bx_wall' LIMIT 1;

SET @iCategoryId = (SELECT `ID` FROM `sys_options_cats` WHERE `name`='Wall' LIMIT 1);
DELETE FROM `sys_options_cats` WHERE `name`='Wall' LIMIT 1;
DELETE FROM `sys_options` WHERE `kateg`=@iCategoryId OR `Name`='permalinks_module_wall';

DELETE FROM `sys_acl_actions` WHERE `Name` IN ('Wall Post Comment', 'Wall Delete Comment');

DELETE FROM `sys_categories` WHERE `Category`='wall';

DELETE FROM `sys_permalinks` WHERE `check`='permalinks_module_wall';

SELECT @iHandlerId:=`id` FROM `sys_alerts_handlers` WHERE `name`='bx_wall' LIMIT 1;
DELETE FROM `sys_alerts_handlers` WHERE `name`='bx_wall' LIMIT 1;
DELETE FROM `sys_alerts` WHERE `handler_id`=@iHandlerId;


DELETE FROM `sys_menu_member` WHERE `Name`='bx_wall' AND `Parent`='1';


DELETE FROM `sys_sbs_entries` USING `sys_sbs_types`, `sys_sbs_entries` WHERE `sys_sbs_types`.`id`=`sys_sbs_entries`.`subscription_id` AND `sys_sbs_types`.`unit`='bx_wall';
DELETE FROM `sys_sbs_types` WHERE `unit`='bx_wall';

DELETE FROM `sys_email_templates` WHERE `Name` IN ('t_sbsWallUpdates');
