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
require_once( BX_DIRECTORY_PATH_INC . 'tags.inc.php' );

check_logged();
$iLoggedID = getLoggedId();

if (isset($_GET['action']) && $_GET['action']=='get_prof_status_mess') {
	if ($iLoggedID) {
		bx_import( 'BxDolUserStatusView' );
		echo BxDolUserStatusView::getStatusPageLight($iLoggedID);
	}
	exit;
}
$sAction = bx_get('action');
if ($sAction!== false && $sAction=='get_prof_comment_block') {
	$iProfileID = (int)bx_get('id');
	if ($iProfileID) {
		$sCloseC = _t('_Close');
		bx_import( 'BxTemplCmtsView' );
		$oCmtsView = new BxTemplCmtsView ('profile', $iProfileID);
		if (!$oCmtsView->isEnabled()) exit;

		$sCloseImg = getTemplateImage('close.gif');
		$sCaptionItem = <<<BLAH
<div class="dbTopMenu">
	<img src="{$sCloseImg}" class="login_ajx_close" />
</div>
BLAH;
		$sCommentsBlock = $GLOBALS['oFunctions']->transBox(
			DesignBoxContent(_t('_Comments'), $oCmtsView->_getPostReplyBox(), 1, $sCaptionItem), false
		);

		echo <<<EOF
<style>
    div.cmt-post-reply {
        position: relative;
    }
</style>
{$sCommentsBlock}
EOF;
	}
	exit;
}

// --------------- page variables and login

$_page['name_index']	= 36;
$_page['css_name']		= 'change_status.css';

$logged['member'] = member_auth(0);

$_page['header'] = _t( "_CHANGE_STATUS_H" );
$_page['header_text'] = _t( "_CHANGE_STATUS_H1", $site['title'] );

// --------------- page components

$_ni = $_page['name_index'];
$_page_cont[$_ni]['page_main_code'] = PageCompPageMainCode($iLoggedID);

// --------------- [END] page components
$GLOBALS['oTopMenu']->setCustomSubHeader(_t( "_CHANGE_STATUS_H" ));
PageCode();

// --------------- page components functions

/**
 * page code function
 */
function PageCompPageMainCode($iLoggedID)
{
	$member['ID'] = (int)$iLoggedID;
	$p_arr = getProfileInfo( $member['ID'] );
	
	if ( $_POST['CHANGE_STATUS'] )
	{
		$sStatus = "";
		switch( $_POST['CHANGE_STATUS'] )
		{
			case 'SUSPEND':
				if ( $p_arr['Status'] == 'Active' )
					$sStatus = "Suspended";
			break;

			case 'ACTIVATE':
				if ( $p_arr['Status'] == 'Suspended' )
					$sStatus = "Active";
			break;
		}
		
		if (mb_strlen($sStatus) > 0)
			db_res("UPDATE `Profiles` SET `Status` = '$sStatus' WHERE `ID` = {$member['ID']}");

		createUserDataFile( $p_arr['ID'] );
		reparseObjTags( 'profile', $member['ID'] );
		
		$p_arr = getProfileInfo( $member['ID'] );
	}
	
	$aData = array(
		'profile_status_caption' => _t("_Profile status"),
		'status' => $p_arr['Status'],
		'status_lang_key' => _t('__' . $p_arr['Status']),
	);
	$aForm = array(
		'form_attrs' => array (
            'action' =>  BX_DOL_URL_ROOT . 'change_status.php',
            'method' => 'post',
            'name' => 'form_change_status'
        ),

        'inputs' => array(
            'status' => array (
                'type'     => 'hidden',
                'name'     => 'CHANGE_STATUS',
                'value'    => '',
            ),
            'subscribe' => array (
                'type'     => 'submit',
                'name'     => 'subscribe',
                'value'    => '',
            ),
        ),
	);
	switch ($p_arr['Status']) {
		case 'Active':
			$aForm['inputs']['status']['value'] = 'SUSPEND';
			$aForm['inputs']['subscribe']['value'] = _t('_Suspend account');
			$oForm = new BxTemplFormView($aForm);
			$aData['form'] = $oForm->getCode();
			$aData['message'] = _t("_PROFILE_CAN_SUSPEND");
			break;
		case 'Suspended':
			$aForm['inputs']['status']['value'] = 'ACTIVATE';
			$aForm['inputs']['subscribe']['value'] = _t('_Activate account');
			$oForm = new BxTemplFormView($aForm);
			$aData['form'] = $oForm->getCode();
			$aData['message'] = _t("_PROFILE_CAN_ACTIVATE");
			break;
		default:
			$aData['message'] = _t("_PROFILE_CANT_ACTIVATE/SUSPEND");
			$aData['form'] = '';
			break;
	}
	return $GLOBALS['oSysTemplate']->parseHtmlByName('change_status.html', $aData);
}

?>