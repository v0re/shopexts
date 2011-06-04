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

require_once( '../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'admin_design.inc.php' );

$logged['admin'] = member_auth( 1, true, true );

$aProfile = getProfileInfo();

$iNameIndex = 0;
$_page = array(
    'name_index' => $iNameIndex,
    'css_name' => array(),
    'js_name' => array(),
    'header' => _t('_adm_page_cpt_fapps'),
    'header_text' => _t('_adm_box_cpt_fapps')
);
$_page_cont[$iNameIndex]['page_main_code'] = $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => getApplicationContent(GLOBAL_MODULE, "admin", array("nick" => $aProfile['NickName'], "password" => $aProfile['Password']), true)));

PageCodeAdmin();

?>