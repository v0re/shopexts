<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -----------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2006 BoonEx Group
*     website              : http://www.boonex.com/
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software. This work is licensed under a Creative Commons Attribution 3.0 License. 
* http://creativecommons.org/licenses/by/3.0/
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the Creative Commons Attribution 3.0 License for more details. 
* You should have received a copy of the Creative Commons Attribution 3.0 License along with Dolphin, 
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

require_once( BX_DIRECTORY_PATH_MODULES . $aModule['path'] . '/classes/' . $aModule['class_prefix'] . 'Module.php');

global $_page;
global $_page_cont;

$iId = isset($_COOKIE['memberID']) ? (int)$_COOKIE['memberID'] : 0;
$_page['name_index']	= 57;
$_page['css_name']		= 'main.css';

// --------------- page variables and login


check_logged();

$_page['header'] = _t( "_chat_page_rules_caption" );
$_page['header_text'] = _t( "_chat_page_rules_caption" );

// --------------- page components

$_ni = $_page['name_index'];
$_page_cont[$_ni]['page_main_code'] = PageCompMainCode();

// --------------- [END] page components

PageCode();

// --------------- page components functions


/**
 * page code function
 */
function PageCompMainCode()
{
    return DesignBoxContent( _t( "_chat_page_rules_caption" ), '<div class="dbContent">' . _t( "_chat_rules" ) . '</div>', $GLOBALS['oTemplConfig'] -> PageCompThird_db_num);
}
?>