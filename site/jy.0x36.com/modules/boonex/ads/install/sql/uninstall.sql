-- tables
DROP TABLE IF EXISTS `[db_prefix]_rating`, `[db_prefix]_voting_track`, `[db_prefix]_category`, `[db_prefix]_main`, `[db_prefix]_main_media`, `[db_prefix]_category_subs`, `[db_prefix]_cmts`, `[db_prefix]_views_track`;

-- PQ statistics
DELETE FROM `sys_account_custom_stat_elements` WHERE `Label` = '_bx_ads_Ads';

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'Ads';

-- comments objects
DELETE FROM `sys_objects_cmts` WHERE `TableCmts`='[db_prefix]_cmts';

-- settings
SET @iCategoryID := (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Ads' LIMIT 1);
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategoryID;
DELETE FROM `sys_options` WHERE `kateg` = @iCategoryID;
DELETE FROM `sys_options` WHERE `Name` = 'permalinks_module_ads';

-- page compose pages
DELETE FROM `sys_page_compose_pages` WHERE `Name`='ads' AND `Title`='Ads';

DELETE FROM `sys_page_compose` WHERE `Page`='index' AND `Desc`='Classifieds' AND `Caption`='_bx_ads_Ads' AND `Func`='PHP';
DELETE FROM `sys_page_compose` WHERE `Page`='member' AND `Desc`='Classifieds' AND `Caption`='_bx_ads_Ads' AND `Func`='PHP';
DELETE FROM `sys_page_compose` WHERE `Page`='profile' AND `Desc`='Classifieds' AND `Caption`='_bx_ads_Ads' AND `Func`='PHP';
DELETE FROM `sys_page_compose` WHERE `Page`='ads';

-- PQ statistics
DELETE FROM `sys_stat_member` WHERE `Type`='mad';

-- site stats
DELETE FROM `sys_stat_site` WHERE `Name`='cls';

-- search objects
DELETE FROM `sys_objects_search` WHERE `ClassName`='BxAdsSearchUnit';

-- tag objects
DELETE FROM `sys_objects_tag` WHERE `ObjectName`='ad' AND `LangKey`='_bx_ads_Ads';
DELETE FROM `sys_tags` WHERE `Type` = 'ad';

-- top menu
SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Ads' LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `Caption` = '_bx_ads_Ads';

-- vote objects
DELETE FROM `sys_objects_vote` WHERE `ObjectName`='ads' AND `TableRating`='[db_prefix]_rating';

-- permalinks
DELETE FROM `sys_permalinks` WHERE `check` = 'permalinks_module_ads';

-- Alerts Handler and Events
-- DELETE FROM `sys_alerts` WHERE `unit` = 'ads';
-- DELETE FROM `sys_alerts_handlers` WHERE `name`='ads' LIMIT 1;

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'bx_ads_profile_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

-- views objects
DELETE FROM `sys_objects_views` WHERE `name` = 'ads';

-- Membership
DELETE `sys_acl_actions`, `sys_acl_matrix` FROM `sys_acl_actions`, `sys_acl_matrix` WHERE `sys_acl_matrix`.`IDAction` = `sys_acl_actions`.`ID` AND `sys_acl_actions`.`Name` IN('ads view', 'ads browse', 'ads search', 'ads add', 'ads edit any ad', 'ads delete any ad', 'ads approving');
DELETE FROM `sys_acl_actions` WHERE `Name` IN('ads view', 'ads browse', 'ads search', 'ads add', 'ads edit any ad', 'ads delete any ad', 'ads approving');

-- member menu
DELETE FROM `sys_menu_member` WHERE `Name` = '[db_prefix]' AND `Parent` = '1' AND `Type` = 'linked_item';

-- privacy
DELETE FROM `sys_privacy_actions` WHERE `module_uri`='ads';

-- actions
DELETE FROM `sys_objects_actions` WHERE `Type`='[db_prefix]';

-- subscriptions
DELETE FROM `sys_sbs_entries` USING `sys_sbs_types`, `sys_sbs_entries` WHERE `sys_sbs_types`.`id`=`sys_sbs_entries`.`subscription_id` AND `sys_sbs_types`.`unit`='[db_prefix]';
DELETE FROM `sys_sbs_types` WHERE `unit`='[db_prefix]';

-- email templates
DELETE FROM `sys_email_templates` WHERE `Name` IN ('t_sbsAdsComments', 't_sbsAdsRates', 't_BuyNow', 't_BuyNowS');