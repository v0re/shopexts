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

define('BX_PROFILE_PAGE', 1);

require_once( 'inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );

bx_import('BxTemplProfileView');
bx_import('BxDolInstallerUtils');

$_page['name_index']	= 5;

$profileID = getID( $_GET['ID'] );
$memberID = getLoggedId();

if ( !$profileID ) {
	header("HTTP/1.1 404 Not Found");
	$_page['header'] = "{$site['title']} ". _t("_Member Profile");
	$_page['header_text'] = _t("_View profile");
	$_page['name_index'] = 0;
	$_page_cont[0]['page_main_code'] = MsgBox( _t("_Profile NA") );
	PageCode();
	exit;
}

// Check if member can view profile
$check_res = checkAction( $memberID, ACTION_ID_VIEW_PROFILES, true, $profileID );

if ( $check_res[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED
	&& !$logged['admin'] && !$logged['moderator'] && $memberID != $profileID)
{
	$_page['header'] = "{$site['title']} "._t("_Member Profile");
	$_page['header_text'] = "{$site['title']} "._t("_Member Profile");
	$_page['name_index'] = 0;
	$_page_cont[0]['page_main_code'] = MsgBox($check_res[CHECK_ACTION_MESSAGE]);
	PageCode();
	exit;
}

$oProfile = new BxBaseProfileGenerator( $profileID );

if (!$logged['admin'] && !$logged['moderator'] && $memberID != $profileID) {
	//Check privacy
	$oPrivacy = new BxDolPrivacy('Profiles', 'ID', 'ID');
	if(!$oPrivacy->check('view', $profileID, $memberID)) {
		$_page['name_index'] = 0;
		$_page['header'] = "{$site['title']} " . _t("_Member Profile");
		$_page['header_text'] = "{$site['title']} " . _t("_Member Profile");
		$_page_cont[0]['page_main_code'] = MsgBox(_t('_INVALID_ROLE'));
		PageCode();
		exit;
	}
}

$oProfile->oCmtsView->getExtraCss();
$oProfile->oCmtsView->getExtraJs();
$oProfile->oVotingView->getExtraJs();

$p_arr  = $oProfile -> _aProfile;

if (!($p_arr['ID'] && ($logged['admin'] || $logged['moderator'] || $oProfile->owner || $p_arr['Status'] == 'Active')))
{
	header("HTTP/1.1 404 Not Found");
	$_page['header'] = "{$site['title']} ". _t("_Member Profile");
	$_page['header_text'] = "{$site['title']} ". _t("_Member Profile");
	$_page['name_index'] = 0;
	$_page_cont[0]['page_main_code'] = MsgBox( _t("_Profile NA") );
	PageCode();
	exit;
}

$_page['header']      = process_line_output( $p_arr['NickName'] ) . ": ". htmlspecialchars_adv( $p_arr['Headline'] );
$_ni = $_page['name_index'];

$oPPV = new BxTemplProfileView($oProfile, $site, $dir);
if (BxDolInstallerUtils::isModuleInstalled("profile_customize"))
{
    $_page_cont[$_ni]['custom_block'] = '<div id="profile_customize_page" style="display: none;">' .
        BxDolService::call('profile_customize', 'get_customize_block', array()) . '</div>';
    $_page_cont[$_ni]['page_main_css'] = '<style type="text/css">' . 
        BxDolService::call('profile_customize', 'get_profile_style', array($profileID)) . '</style>';
}
else
{
    $_page_cont[$_ni]['custom_block'] = '';
    $_page_cont[$_ni]['page_main_css'] = '';
}

$_page_cont[$_ni]['page_main_code'] = $oPPV->getCode();

//--- Profile -> View unit for Alerts Engine ---//
if($profileID != $memberID) {
    require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolAlerts.php');
    $oAlert = new BxDolAlerts('profile', 'view', $profileID, $memberID);
    $oAlert->alert();

	bx_import ('BxDolViews');
	new BxDolViews('profiles', $profileID);
}
//--- Profile -> View unit for Alerts Engine ---//

$oSysTemplate->addJs('view_edit.js');
PageCode();

?>