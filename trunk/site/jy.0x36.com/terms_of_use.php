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

require_once( 'inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );

// --------------- page variables and login
$_page['name_index'] 	= 18;
$_page['css_name']		= 'terms_of_use.css';

check_logged();

$_page['header'] = _t( "_TERMS_OF_USE_H" );
$_page['header_text'] = _t( "_TERMS_OF_USE_H1" );
// --------------- page components

$_ni = $_page['name_index'];
$_page_cont[$_ni]['page_main_code'] = PageCompPageMainCode();
// --------------- [END] page components

PageCode();

// --------------- page components functions

/**
 * page code function
 */
function PageCompPageMainCode() {
	global $oTemplConfig;
	global $site;

	$sRet = _t( "_TERMS_OF_USE" );
	$sRet = '<div class="dbContent">' . str_replace( '<site>', $site['title'], $sRet ) . '</div>';

    //return DesignBoxContent( _t( "_TERMS_OF_USE_H1" ), $ret, $oTemplConfig -> PageCompThird_db_num);
    return $sRet;
}

?>