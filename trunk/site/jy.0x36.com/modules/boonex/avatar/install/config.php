<?
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
*     website              : http://www.boonex.com
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the
* License, or  any later version.
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Dolphin,
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

$aConfig = array(
	/**
	 * Main Section.
	 */
	'title' => 'Avatar',
	'version' => '1.0.6',
	'vendor' => 'Boonex',
	'update_url' => '',
	
	'compatible_with' => array(
        '7.0.6'
    ),
	

    /**
	 * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
	 */
	'home_dir' => 'boonex/avatar/',
	'home_uri' => 'avatar',
	
	'db_prefix' => 'bx_avatar_',
	'class_prefix' => 'BxAva',
	/**
	 * Installation/Uninstallation Section.
	 */
	'install' => array(
		'show_introduction' => 0,
		'change_permissions' => 1,
		'execute_sql' => 1,
		'update_languages' => 1,
		'recompile_main_menu' => 1,
		'recompile_member_menu' => 0,
        'recompile_site_stats' => 0,		
		'recompile_page_builder' => 1,
		'recompile_profile_fields' => 0,
		'recompile_comments' => 0,
		'recompile_member_actions' => 0,
		'recompile_tags' => 0,
		'recompile_votes' => 0,
		'recompile_categories' => 0,
		'recompile_search' => 0,
		'recompile_injections' => 0,
		'recompile_permalinks' => 1,
		'recompile_alerts' => 1,
		'show_conclusion' => 0,
	),
	'uninstall' => array (
		'show_introduction' => 0,
		'change_permissions' => 0,
		'execute_sql' => 1,
		'update_languages' => 1,
		'recompile_main_menu' => 1,
		'recompile_member_menu' => 0,
		'recompile_site_stats' => 0,
		'recompile_page_builder' => 1,
		'recompile_profile_fields' => 0,
		'recompile_comments' => 0,
		'recompile_member_actions' => 0,
		'recompile_tags' => 0,
		'recompile_votes' => 0,
		'recompile_categories' => 0,
		'recompile_search' => 0,
		'recompile_injections' => 0,
		'recompile_permalinks' => 1,
		'recompile_alerts' => 1,
		'show_conclusion' => 0,
    ),
    /**
	 * Dependencies Section
	 */
	'dependencies' => array(),

	/**
	 * Category for language keys.
	 */
	'language_category' => 'Boonex Avatars',

	/**
	 * Permissions Section
	 */
    'install_permissions' => array(
        'writable' => array(
            'data/images/',
            'data/tmp/',
        ),
    ),
    'uninstall_permissions' => array(),

	/**
	 * Introduction and Conclusion Section.
	 */
	'install_info' => array(
		'introduction' => '',
		'conclusion' => ''
	),
	'uninstall_info' => array(
		'introduction' => '',
		'conclusion' => ''
	)
);
?>
