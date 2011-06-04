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

define('BX_INDEX_PAGE', 1);

if ( !file_exists( "inc/header.inc.php" ) )
{
	// this is dynamic page -  send headers to do not cache this page
	$now = gmdate('D, d M Y H:i:s') . ' GMT';
	header("Expires: $now");
	header("Last-Modified: $now");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");

	echo "It seems to be script is <b>not</b> installed.<br />\n";
	if ( file_exists( "install/index.php" ) ) {
		echo "Please, wait. Redirecting you to installation form...<br />\n";
		echo "<script language=\"Javascript\">location.href = 'install/index.php';</script>\n";
	}
	exit;
}

require_once( 'inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'admin.inc.php' );

require_once( BX_DIRECTORY_PATH_INC . 'db.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'prof.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'membership_levels.inc.php' );
require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolPageView.php' );
require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolProfileFields.php' );
require_once( BX_DIRECTORY_PATH_ROOT . "templates/tmpl_{$tmpl}/scripts/BxTemplFormView.php" );
require_once( BX_DIRECTORY_PATH_ROOT . "templates/tmpl_{$tmpl}/scripts/BxTemplVotingView.php" );

bx_import("BxTemplIndexPageView");

//-- registration by invitation only --//;
if( !empty($_GET['idFriend']) && (int)$_GET['idFriend'] && getParam('reg_by_inv_only') == 'on' ) {
    setcookie('idFriend', (int)$_GET['idFriend'], 0, '/');
}

check_logged();

$_page['name_index'] 	= 1;

$oSysTemplate->setPageTitle($site['title']);
$oSysTemplate->setPageDescription(getParam("MetaDescription"));
$oSysTemplate->setPageMainBoxTitle($site['title']);
$oSysTemplate->addPageKeywords(getParam("MetaKeyWords"));
$oSysTemplate->addCss(array('index.css'));

$oIPV = new BxTemplIndexPageView();

$_ni = $_page['name_index'];
$_page_cont[$_ni]['promo_code'] = getPromoCode();
$_page_cont[$_ni]['page_main_code'] = $oIPV -> getCode();

PageCode();

?>
