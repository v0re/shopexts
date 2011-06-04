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
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'admin_design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
bx_import('BxDolAdminSettings');

$logged['admin'] = member_auth( 1, true, true );

$mixedCategory = 0;
if(bx_get('cat') !== false)
	$mixedCategory = bx_get('cat');

$oSettings = new BxDolAdminSettings($mixedCategory);

//--- Process submit ---//
$sResult = '';
if(isset($_POST['save']) && isset($_POST['cat'])) {
    $sResult = $oSettings->saveChanges($_POST);
}

$iNameIndex = 3;
$_page = array(
    'name_index' => $iNameIndex,
    'css_name' => array('forms_adv.css', 'settings.css'),
    'header' => _t('_adm_page_cpt_settings'),
    'header_text' => $oSettings->getTitle()
);
$_page_cont[$iNameIndex]['page_result_code'] = $sResult;
$_page_cont[$iNameIndex]['page_main_code'] = $oSettings->getForm();
if (26 == $mixedCategory) {
	define('BX_PROMO_CODE', adm_hosting_promo());
}

PageCodeAdmin();

?>
