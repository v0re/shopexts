
-- main
CREATE TABLE IF NOT EXISTS `[db_prefix]main` (
  `user_id` int(10) unsigned NOT NULL auto_increment,
  `css` text NOT NULL default '',
  `tmp` text NOT NULL default '',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- units
CREATE TABLE IF NOT EXISTS `[db_prefix]units` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `caption` varchar(100) NOT NULL,
  `css_name` varchar(500) NOT NULL,
  `type` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `bx_profile_custom_units` (`name`, `caption`, `css_name`, `type`) VALUES
	('body', 'Full page', 'body', 'background'),
    ('boxfull', 'Default box full', '#divUnderCustomization .disignBoxFirst', 'background'),
    ('boxcontent', 'Default box content', '#divUnderCustomization .disignBoxFirst .boxContent', 'background'),
    ('boxheader', 'Default box header', '#divUnderCustomization .disignBoxFirst .boxFirstHeader', 'background'),
    ('infoval', 'General info values', '#divUnderCustomization .input_wrapper_value', 'font'),
    ('infocaption', 'General info captions', '#divUnderCustomization .caption', 'font'),
    ('boxheader', 'Box header', '#divUnderCustomization .boxFirstHeader', 'font'),
    ('box', 'Default box', '#divUnderCustomization .disignBoxFirst', 'border'),
    ('form', 'Form', '#divUnderCustomization .form_advanced_table', 'border'),
    ('form_cell', 'Cell in form', '#divUnderCustomization .form_advanced_table th, #divUnderCustomization .form_advanced_table td', 'border'),
    ('form', 'Form', '#divUnderCustomization .form_advanced_table th, #divUnderCustomization .form_advanced_table td', 'background'),
    ('header_box', 'Header box', '#divUnderCustomization div.boxFirstHeader', 'border'),
    ('top_button_active', 'Active button on the top menu', '#divUnderCustomization div.dbTopMenu div.active', 'font'),
    ('top_button_notactive', 'Not active button on the top menu', '#divUnderCustomization div.dbTopMenu div.notActive a', 'font'),
    ('action_button', 'Action button', '#divUnderCustomization table div.button_wrapper input.form_input_submit', 'font');

-- themes
CREATE TABLE IF NOT EXISTS `[db_prefix]themes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `ownerid` int(10) NOT NULL,
  `css` text NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- images
CREATE TABLE IF NOT EXISTS `[db_prefix]images` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `ext` varchar(4) NOT NULL,
  `count` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- permalinks
INSERT INTO `sys_permalinks`(`standard`, `permalink`, `check`) VALUES('modules/?r=profile_customize/', 'm/profile_customize/', 'bx_profile_customize_permalinks');

-- settings
SET @iMaxOrder = (SELECT `order_in_kateg` + 1 FROM `sys_options` WHERE `kateg` = 1 ORDER BY `order_in_kateg` DESC LIMIT 1);
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('bx_profile_customize_enable', 'on', 1, 'Enable profile customization', 'checkbox', '', '', @iMaxOrder, '');

SET @iMaxOrder = (SELECT `order_in_kateg` + 1 FROM `sys_options` WHERE `kateg` = 26 ORDER BY `order_in_kateg` DESC LIMIT 1);
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('bx_profile_customize_permalinks', 'on', 26, 'Enable friendly permalinks in profile customizer', 'checkbox', '', '', @iMaxOrder, '');

-- action
SET @iMaxOrder = (SELECT `Order` + 1 FROM `sys_objects_actions` WHERE `Type` = 'Profile' ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES('{evalResult}', 'modules/boonex/profile_customize/|bx_action_customize.png', '', '$(''#profile_customize_page'').fadeIn(''slow'', function() {dbTopMenuLoad(''profile_customizer'');});', 'if (defined(''BX_PROFILE_PAGE'') && {ID} == {member_id} && getParam(''bx_profile_customize_enable'') == ''on'') return _t( ''_Customize'' ); else return null;', @iMaxOrder, 'Profile');

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'bx_profile_customize', '_bx_profile_customize', '{siteUrl}modules/?r=profile_customize/administration', 'Profile customizer module by BoonEx', 'modules/boonex/profile_customize/|bx_action_customize.png', @iMax+1);
