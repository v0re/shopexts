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

define('BX_SECURITY_EXCEPTIONS', true);
define('BX_OS_ADMIN', 1);
$aBxSecurityExceptions = array(
    'POST.request',
    'GET.request',
    'REQUEST.request',
);

require_once('../../../inc/header.inc.php');
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'admin_design.inc.php' );
bx_import('BxDolPaginate');
bx_import('BxDolModuleDb');

require_once( BX_DIRECTORY_PATH_MODULES . 'boonex/open_social/classes/BxOSiModule.php');
$oModuleDb = new BxDolModuleDb();
$aModule = $oModuleDb->getModuleByUri('open_social');
$oBxOSiModule = new BxOSiModule($aModule);

$logged['admin'] = member_auth( 1, true, true );
$iAdminID = isAdmin() ? (int)$_COOKIE['memberID'] : 0;

if ($_REQUEST['action'] == 'get_preview') { // preview
	$iApplID = (int)$_REQUEST['appl_id'];

	$sApplicationSrc = $oBxOSiModule->serviceGenApplication($iApplID);
	$sRes = PopupBox('preview_box', _t('_Preview'), $sApplicationSrc);

	require_once( BX_DIRECTORY_PATH_PLUGINS . 'Services_JSON.php' );
    $oJson = new Services_JSON();
    echo $oJson->encode(array('code' => $sRes));
	exit;
}

if (isset($_POST['os_appls'])  && is_array($_POST['os_appls'])) { // manage subactions
	foreach($_POST['os_appls'] as $iApplID) {
		$iOldID = (int)$iApplID;
 		switch (true) {
			case isset($_POST['action_delete']):
				$oBxOSiModule->_oDb->deleteApplication($iOldID);
				break;
			case isset($_POST['action_approve']):
				$oBxOSiModule->_oDb->updateApplicationStatus($iOldID, 'active');
				break;
			case isset($_POST['action_disapprove']):
				$oBxOSiModule->_oDb->updateApplicationStatus($iOldID);
				break;
			case isset($_POST['action_copy']):
				$oBxOSiModule->_oDb->copyApplication($iOldID, 0);
				break;
		}
 	}
}

$sHomeUrl = $oBxOSiModule->_oConfig->getHomeUrl();

$sAdmPanel = $sApplications = '';
$sApplications = $oBxOSiModule->getApplicationUnits();
if ($sApplications != '') {
	bx_import('BxTemplSearchResult');
	$oSearchResult = new BxTemplSearchResult();
	$sAdmPanel = $oSearchResult->showAdminActionsPanel('application_container', array('action_approve' => '_Approve', 'action_disapprove' => '_Disapprove', 'action_delete' => '_Delete', 'action_copy' => '_osi_Copy_to_admin_applications'), 'os_appls');
} else {
	$sApplications = MsgBox(_t('_Empty'));
}

$sHeaderValue = _t('_osi_Opensocial_moderation');
$sForm = $oBxOSiModule->GenCustomOSiBlock($iAdminID);

$aFormVariables = array (
	'admin_url' => $sHomeUrl,
	'applications' => $sApplications,
	'admin_panel' => $sAdmPanel,
);
$sAdminCode = $oBxOSiModule->_oTemplate->parseHtmlByTemplateName('admin', $aFormVariables);

$iNameIndex = 9;
$_page = array(
    'name_index' => $iNameIndex,
    'css_name' => array('common.css', 'forms_adv.css', 'browse.css'),
    'header' => $sHeaderValue,
    'header_text' => $sHeaderValue
);
$_page_cont[$iNameIndex]['page_main_code'] = DesignBoxAdmin($sHeaderValue, $sAdminCode) . $sForm;
PageCodeAdmin();

?>
