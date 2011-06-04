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

require_once( './inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'admin.inc.php' );

require_once( BX_DIRECTORY_PATH_INC . 'db.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolPageView.php' );

check_logged();

$_page['name_index'] 	= 81;

$sPageName = process_pass_data( $_GET['ID'] );

$oIPV = new BxDolPageView($sPageName);
if ($oIPV->isLoaded()) {
    $sPageTitle = htmlspecialchars($oIPV->getPageTitle());
    $_page['header'] 		= $sPageTitle;
    $_page['header_text'] 	= $sPageTitle;

    $_ni = $_page['name_index'];
    $_page_cont[$_ni]['page_main_code'] = $oIPV -> getCode();

    PageCode();
} else {
    $oSysTemplate->displayPageNotFound();
}

?>
