-- tables
DROP TABLE IF EXISTS `[db_prefix]_posts`, `[db_prefix]_rating`, `[db_prefix]_voting_track`, `[db_prefix]_main`, `[db_prefix]_cmts`, `[db_prefix]_views_track`;

-- PQ statistics
DELETE FROM `sys_account_custom_stat_elements` WHERE `Label` = '_bx_blog_Blog';

-- settings
SET @iCategoryID := (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Blogs' LIMIT 1);
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategoryID;
DELETE FROM `sys_options` WHERE `kateg` = @iCategoryID;
DELETE FROM `sys_options` WHERE `Name` = 'permalinks_blogs';
-- DELETE FROM `sys_options` WHERE `Name` = 'permalinks_module_blogs';

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'Blogs';

-- categories
DELETE FROM `sys_categories` WHERE `Type`='[db_prefix]';

-- category objects
DELETE FROM `sys_objects_categories` WHERE `ObjectName`='[db_prefix]';

-- comments objects
DELETE FROM `sys_objects_cmts` WHERE `TableCmts`='[db_prefix]_cmts';

-- page compose pages
DELETE FROM `sys_page_compose_pages` WHERE `Name` IN ('bx_blogs', 'bx_blogs_home');

DELETE FROM `sys_page_compose` WHERE `Page`='index' AND `Desc`='Recently posted blogs' AND `Caption`='_bx_blog_Blogs' AND `Func`='PHP';
DELETE FROM `sys_page_compose` WHERE `Page`='profile' AND `Desc`='Member blog block' AND `Caption`='_bx_blog_Blog' AND `Func`='PHP';
DELETE FROM `sys_page_compose` WHERE `Page` IN ('bx_blogs', 'bx_blogs_home');

-- PQ statistics
DELETE FROM `sys_stat_member` WHERE `Type`='mbp';
DELETE FROM `sys_stat_member` WHERE `Type`='mbpc';

-- site stats
DELETE FROM `sys_stat_site` WHERE `Name`='blg';

-- search objects
DELETE FROM `sys_objects_search` WHERE `ClassName`='BxBlogsSearchUnit';

-- tag objects
DELETE FROM `sys_objects_tag` WHERE `ObjectName`='blog' AND `LangKey`='_bx_blog_Blogs';
DELETE FROM `sys_tags` WHERE `Type` = 'blog';

-- top menu
SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Blogs' LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `Parent` = 9 AND `Name` = 'Profile Blog';
DELETE FROM `sys_menu_top` WHERE `Parent` = 4 AND `Name` = 'Profile Blog';
DELETE FROM `sys_menu_top` WHERE `Parent` = 0 AND `Caption` = '_bx_blog_Posts';

-- vote objects
DELETE FROM `sys_objects_vote` WHERE `ObjectName`='blogposts' AND `TableRating`='[db_prefix]_rating';

-- permalinks
DELETE FROM `sys_permalinks` WHERE `check` = 'permalinks_blogs';

-- Alerts Handler and Events
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'bx_blogs_profile_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

-- DELETE FROM `sys_alerts` WHERE `unit` = '[db_prefix]';
-- DELETE FROM `sys_alerts_handlers` WHERE `name`='[db_prefix]' LIMIT 1;

-- views objects
DELETE FROM `sys_objects_views` WHERE `name` = 'blogposts';

-- Membership
DELETE `sys_acl_actions`, `sys_acl_matrix` FROM `sys_acl_actions`, `sys_acl_matrix` WHERE `sys_acl_matrix`.`IDAction` = `sys_acl_actions`.`ID` AND `sys_acl_actions`.`Name` IN('blog view', 'blog post view', 'blogs browse', 'blogs posts browse', 'blog post search', 'blog post add', 'blog posts edit any post', 'blog posts delete any post', 'blog posts approving', 'blog posts comments delete and edit');
DELETE FROM `sys_acl_actions` WHERE `Name` IN('blog view', 'blog post view', 'blogs browse', 'blogs posts browse', 'blog post search', 'blog post add', 'blog posts edit any post', 'blog posts delete any post', 'blog posts approving', 'blog posts comments delete and edit');

-- member menu
DELETE FROM `sys_menu_member` WHERE `Name` = '[db_prefix]' AND `Parent` = '1';

-- privacy
DELETE FROM `sys_privacy_actions` WHERE `module_uri`='blogs';

-- actions
DELETE FROM `sys_objects_actions` WHERE `Type`='[db_prefix]';
DELETE FROM `sys_objects_actions` WHERE `Type`='[db_prefix]_m';

-- subscriptions
DELETE FROM `sys_sbs_entries` USING `sys_sbs_types`, `sys_sbs_entries` WHERE `sys_sbs_types`.`id`=`sys_sbs_entries`.`subscription_id` AND `sys_sbs_types`.`unit`='[db_prefix]';
DELETE FROM `sys_sbs_types` WHERE `unit`='[db_prefix]';

-- email templates
DELETE FROM `sys_email_templates` WHERE `Name` IN ('t_sbsBlogpostsComments', 't_sbsBlogpostsRates');