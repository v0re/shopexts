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

$GLOBALS['iAdminPage'] = 1;

require_once(BX_DIRECTORY_PATH_INC . 'admin_design.inc.php');

bx_import('Module', $aModule);

global $_page;
global $_page_cont;
global $logged;

check_logged();

$iIndex = 9;
$_page['name_index'] = $iIndex;
$_page['header'] = _t('_wall_pc_admin');
$_page['css_name'] = array('forms_adv.css');

if(!@isAdmin()) {
    send_headers_page_changed();
	login_form("", 1);
	exit;
}

$oWall = new BxWallModule($aModule);

//--- Process actions ---//
$mixedResultSettings = '';
if(isset($_POST['save']) && isset($_POST['cat']))
    $mixedResultSettings = $oWall->setSettings($_POST);
//--- Process actions ---//

$_page_cont[$iIndex]['page_main_code'] = DesignBoxAdmin(_t('_wall_bc_settings'), $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $oWall->getSettingsForm($mixedResultSettings))));

PageCodeAdmin();
?>