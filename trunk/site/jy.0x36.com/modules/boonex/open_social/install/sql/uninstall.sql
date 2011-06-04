-- tables
DROP TABLE IF EXISTS `[db_prefix]_main`;
DROP TABLE IF EXISTS `[db_prefix]_application_settings`;

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'Opensocial';

-- page compose pages
DELETE FROM `sys_page_compose` WHERE `Caption`='_osi_Custom_Feeds' AND `Func`='PHP';

-- special page compose page
DELETE FROM `sys_page_compose` WHERE `Content`='XML';

-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Opensocial integration' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;