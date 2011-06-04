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

require_once( 'header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'admin_design.inc.php' );
require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolEmailTemplates.php' );

function login_form($text = "", $member = 0, $bbAjaxMode = false) 
{
	global $site;
	global $_page_cont;
	global $_page;
	global $admin_dir;

	if($member == 1) {
	    LoginFormAdmin();
        exit;
	}

    $sFormCode = getMemberLoginFormCode('login_box_form');

    $sCloseImg = getTemplateImage('close.gif');

   $sCaptionItem = <<<BLAH
    <div class="dbTopMenu">
    <img src="{$sCloseImg}" class="login_ajx_close" />
    </div>
BLAH;

	if($bbAjaxMode) {

        $sMemberLoginFormAjx = $GLOBALS['oFunctions']->transBox(
            DesignBoxContent(_t('_Member Login'), $sFormCode, 1, $sCaptionItem), true
        );

		echo $sMemberLoginFormAjx;
		exit;
	}

	$sMemberLoginForm = <<<EOF
<div class="controlsDiv">
{$sFormCode}
</div>
EOF;


	$_page['header'] = $site['title'] . ' ' . _t("_Member Login");
	$_page['header_text'] = _t("_Member Login");

	if ($bbAjaxMode && $member==1)
		$template = BX_DIRECTORY_PATH_ROOT . "templates/base/login_form_ajax_a.html";

	if ($bbAjaxMode==false && $member==0) {
		$_page_cont[0]['page_main_code'] = $sMemberLoginForm;
	} else {
		$_page_cont[0]['page_main_code'] = PageCompLoginForm($text,$member,$mem,$login_page,$join_page,$forgot_page,$template);
	}

	if ($bbAjaxMode) {
        echo <<<EOF
<div class="login_ajax_wrap">
	{$_page_cont[0]['page_main_code']}
</div>
EOF;
		exit;
	}
	
    $_page['name_index'] = 0;
    PageCode();
    exit;	
}

function PageCompLoginForm($text, $member, $mem, $login_page, $join_page, $forgot_page, $template = '') {
	global $site;

	$aFormReplace = array();
	
	if ($member == 1)
		$name_label = _t("_Log In");
	else
		$name_label = ($member == 2) ? _t("_ID") : _t("_E-mail or ID");
	
	$aFormReplace['header_text']    = $site['title'] . ' ' . $mem . ' Login';
	$aFormReplace['warning_text']   = $text;
	$aFormReplace['action_url']     = $login_page;

	$sRelocate = bx_get('relocate');
	if (!$sUrlRelocate = $sRelocate or basename($sRelocate) == 'index.php' or basename($sRelocate) == 'join.php')
		$sUrlRelocate = '';

	$aFormReplace['site_a_url']     = BX_DOL_URL_ROOT;
	$aFormReplace['relocate_url']   = htmlspecialchars($sUrlRelocate);
	$aFormReplace['images']			= $site['images'];
	$aFormReplace['name_label']     = $name_label;
	$aFormReplace['password_label'] = _t("_Password");
	$aFormReplace['submit_label']   = _t("_Log In");
	$aFormReplace['member_label']   = _t('_Member Login');
	$aFormReplace['remeber_label']	= _t("_Remember password");
	$aFormReplace['form_onsubmit']  = "validateLoginForm( this, '" . BX_DOL_URL_ROOT . "', '" . addslashes( htmlspecialchars( _t('_PROFILE_ERR') ) ) . "'); return false;";
	
	if ($forgot_page) {
		$aFormReplace['forgot_page_url'] = $forgot_page;
		$aFormReplace['forgot_label']    = _t("_forgot_your_password") . '?';
		$aFormReplace['clickhere_label'] = _t("_Click here");
	} else {
		$aFormReplace['forgot_page_url'] = '';
		$aFormReplace['forgot_label']    = '';
	}
	
	if ($join_page) {
		$aFormReplace['not_a_member']  = _t('_not_a_member');
		$aFormReplace['or']            = _t( '_or' );
		$aFormReplace['join_label']    = _t( '_Join now' );
		$aFormReplace['join_page_url'] = $join_page;
	} else {
		$aFormReplace['or']  = '';
		$aFormReplace['not_a_member']  = '';
		$aFormReplace['join_label']    = '';
		$aFormReplace['join_page_url'] = '';
	}

	$sTemplateFilename = basename($template);
	return $GLOBALS['oSysTemplate']->parseHtmlByName($sTemplateFilename, $aFormReplace);
}

function activation_mail( $ID, $text = 1 ) {
	global $ret;

	$ID = (int)$ID;
	$p_arr = db_arr( "SELECT `Email` FROM `Profiles` WHERE `ID` = '$ID'" );
	if ( !$p_arr ) {
		$ret['ErrorCode'] = 7;
	    return false;
	}

	$rEmailTemplate = new BxDolEmailTemplates();
	$aTemplate = $rEmailTemplate -> getTemplate( 't_Confirmation' ) ;
	$recipient  = $p_arr['Email'];

	$sConfirmationCode	= base64_encode( base64_encode( crypt( $recipient, CRYPT_EXT_DES ? "secret_co" : "se" ) ) );
	$sConfirmationLink	= BX_DOL_URL_ROOT . "profile_activate.php?ConfID={$ID}&ConfCode=" . urlencode( $sConfirmationCode );

	$aPlus = array();
	$aPlus['ConfCode'] = $sConfirmationCode;
	$aPlus['ConfirmationLink'] = $sConfirmationLink;

	$mail_ret = sendMail( $recipient, $aTemplate['Subject'], $aTemplate['Body'], $ID, $aPlus );

	if ( $mail_ret ) {
		if ( $text ) {
			$page_text .= '<div class="Notice">' . _t("_EMAIL_CONF_SENT") . "</div>";
			
			$page_text .= "<center><form method=get action=\"" . BX_DOL_URL_ROOT . "profile_activate.php\">";
			$page_text .= "<table class=text2 cellspacing=0 cellpadding=0><td><b>"._t("_ENTER_CONF_CODE").":</b>&nbsp;</td><td><input type=hidden name=\"ConfID\" value=\"{$ID}\">";
			$page_text .= '<input class=no type="text" name="ConfCode" size=30></td><td>&nbsp;</td>';
			$page_text .= '<td><input class=no type="submit" value="'._t("_Submit").'"></td></table>';
			$page_text .= '</form></center><br />';
	    } else
			return true;
	} else {
	    if ( $text )
			$page_text .= "<br /><br />"._t("_EMAIL_CONF_NOT_SENT");
	    else {
			$ret['ErrorCode'] = 10;
			return false;
		}
	}
	return ($text) ? $page_text : true;
}

function mem_expiration_letter( $ID, $membership_name, $expire_days ) {
	$ID = (int)$ID;

	if ( !$ID )
		return false;

	$p_arr = db_arr( "SELECT `Email` FROM `Profiles` WHERE `ID` = $ID", 0 );
	if ( !$p_arr )
		return false;

	$rEmailTemplate = new BxDolEmailTemplates();
	$aTemplate = $rEmailTemplate -> getTemplate( 't_MemExpiration', $ID ) ;

	$recipient  = $p_arr['Email'];

	$aPlus = array();
	$aPlus['MembershipName'] = $membership_name;
	$aPlus['ExpireDays'] = $expire_days;

	$mail_ret = sendMail( $recipient, $aTemplate['Subject'], $aTemplate['Body'], $ID, $aPlus  );

	if ($mail_ret)
		return true;
	else
		return false;
}

function getID( $str, $with_email = 1 ) {
	if ( $with_email ) {
		if ( eregi("^[_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,4}$", $str) ) {
			$str = process_db_input($str);
	    	$mail_arr = db_arr( "SELECT `ID` FROM `Profiles` WHERE `Email` = '$str'" );
			if ( (int)$mail_arr['ID'] ) {
				return (int)$mail_arr['ID'];
			}
		}
	}

    $str = process_db_input($str);
    $iID = (int)db_value( "SELECT `ID` FROM `Profiles` WHERE `NickName` = '$str'" );
	
    if(!$iID) {
        $aProfile = getProfileInfo($str);
        $iID = isset($aProfile['ID']) ? $aProfile['ID'] : 0;
    }
	return $iID;
}

// check encrypted password (ex., from Cookie)
function check_login($ID, $passwd, $iRole = BX_DOL_ROLE_MEMBER, $error_handle = true) {
	$ID = (int)$ID;
    
    if (!$ID) {
		if ($error_handle)
			login_form(_t("_PROFILE_ERR"), $member);
		return false;
	}

	switch ($iRole) {
		case BX_DOL_ROLE_MEMBER: $member = 0; break;
		case BX_DOL_ROLE_ADMIN:  $member = 1; break;
	}

    $aProfile = getProfileInfo($ID);
    
	// If no such members
	if (!$aProfile) {
		if ($error_handle)
			login_form(_t("_PROFILE_ERR"), $member);
		return false;
	}

	// If password is incorrect
	if (strcmp($aProfile['Password'], $passwd) != 0) {
		if ($error_handle)
			login_form(_t("_INVALID_PASSWD"), $member);
		return false;
	}
    
    if (!((int)$aProfile['Role'] & $iRole)) {
		if ($error_handle)
		  login_form(_t("_INVALID_ROLE"), $member);
		return false;
    }

    if(((int)$aProfile['Role'] & BX_DOL_ROLE_ADMIN) || ((int)$aProfile['Role'] & BX_DOL_ROLE_MODERATOR)) {
        if( 'on' != getParam('ext_nav_menu_enabled') ) {
            update_date_lastnav($ID);
        }

        return true;
    }

    // if IP is banned
	if ((2 == getParam('ipBlacklistMode') && bx_is_ip_blocked()) || ('on' == getParam('sys_dnsbl_enable') && bx_is_ip_dns_blacklisted('', 'login'))) {
        if ($error_handle) {
				$GLOBALS['_page']['name_index'] = 55;
				$GLOBALS['_page']['css_name'] = '';
				$GLOBALS['_ni'] = $GLOBALS['_page']['name_index'];
				$GLOBALS['_page_cont'][$GLOBALS['_ni']]['page_main_code'] = MsgBox(_t('_Sorry, your IP been banned'));
				PageCode();
        }
        return false;
	}

    // if profile is banned
    if (isLoggedBanned($aProfile['ID'])) {
        if ($error_handle) {
			$GLOBALS['_page']['name_index'] = 55;
			$GLOBALS['_page']['css_name'] = '';
			$GLOBALS['_ni'] = $GLOBALS['_page']['name_index'];
			$GLOBALS['_page_cont'][$GLOBALS['_ni']]['page_main_code'] = MsgBox(_t('_member_banned'));
			PageCode();
        }
        return false;
	}

    if( 'on' != getParam('ext_nav_menu_enabled') ) {
        update_date_lastnav($ID);
    }

	return true;
}

function check_logged(){
	$aAccTypes = array(
	   1 => 'admin', 
	   0 => 'member'
    );

	$bLogged = false;
	foreach($aAccTypes as $iKey => $sValue)
		if($GLOBALS['logged'][$sValue] = member_auth($iKey, false)) {
		    $bLogged = true;
            break;
		}
		  
	if((isset($_COOKIE['memberID']) || isset($_COOKIE['memberPassword'])) && !$bLogged)
	    bx_logout(false);
}

// 0 - member, 1 - admin
function member_auth($member = 0, $error_handle = true, $bAjx = false) {
   	global $site;

   	switch ($member) {
	    case 0:
	   		$mem	    = 'member';
	   		$login_page = BX_DOL_URL_ROOT . "member.php";
            $iRole      = BX_DOL_ROLE_MEMBER;
	    break;
	    case 1:
	   		$mem	    = 'admin';
	   		$login_page = BX_DOL_URL_ADMIN . "index.php";
            $iRole      = BX_DOL_ROLE_ADMIN;
	    break;
    }

    if (empty($_COOKIE['memberID']) || !isset($_COOKIE['memberPassword'])) {
        if ($error_handle) {
            $text = _t("_LOGIN_REQUIRED_AE1");
            if ($member == 0)
               $text .= "<br />"._t("_LOGIN_REQUIRED_AE2", $site['images'], BX_DOL_URL_ROOT, $site['title']);
            
			$bAjxMode = (isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) and $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') ? true : false;
			if ($member=1 && $bAjx==true) $bAjxMode = true;
            login_form($text, $member, $bAjxMode);
        }
        return false;
    }

    return check_login(process_pass_data($_COOKIE['memberID']), process_pass_data($_COOKIE['memberPassword' ]), $iRole, $error_handle);
}

// check unencrypted password
function check_password($sUsername, $sPassword, $iRole = BX_DOL_ROLE_MEMBER, $error_handle = true) {
    $iId = getID($sUsername);
    if (!$iId) return false;
    
    $aUser = getProfileInfo($iId);
    $sPassCheck = encryptUserPwd($sPassword, $aUser['Salt']);
    
    return check_login($iId, $sPassCheck, $iRole, $error_handle);
}

function update_date_lastnav($iId)
{
    $iId = (int) $iId;

    // update the date of last navigate;
    $sQuery = "UPDATE `Profiles` SET `DateLastNav` = NOW() WHERE `ID` = '{$iId}'";
    db_res($sQuery);
}

function profile_delete($ID) {
	//global $MySQL;
	global $dir;

	//recompile global profiles cache
    $GLOBALS['MySQL']->cleanCache('sys_browse_people');

	$ID = (int)$ID;
	
	if ( !$ID )
	    return false;
	
	if ( !getProfileInfo( $ID ) )
	    return false;	

    db_res( "DELETE FROM `sys_admin_ban_list` WHERE `ProfID`='". $ID . "' LIMIT 1");
	db_res( "DELETE FROM `sys_greetings` WHERE `ID` = '{$ID}' OR `Profile` = '{$ID}'" );
    db_res( "DELETE FROM `sys_block_list` WHERE `ID` = '{$ID}' OR `Profile` = '{$ID}'" );
	db_res( "DELETE FROM `sys_messages` WHERE Recipient = {$ID} " );
    db_res( "DELETE FROM `sys_fave_list` WHERE ID = {$ID} OR Profile = {$ID}" );
	db_res( "DELETE FROM `sys_friend_list` WHERE ID = {$ID} OR Profile = {$ID}" );
	db_res( "DELETE FROM `sys_acl_levels_members` WHERE `IDMember` = {$ID}" );
	db_res( "DELETE FROM `sys_tags` WHERE `ObjID` = {$ID} AND `Type` = 'profile'" );

    // delete profile votings
    require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolVoting.php' ); 
    $oVotingProfile = new BxDolVoting ('profile', 0, 0);
    $oVotingProfile->deleteVotings ($ID);

    // delete profile comments 
    require_once (BX_DIRECTORY_PATH_CLASSES . 'BxDolCmts.php'); 
    $oCmts = new BxDolCmts('profile', $ID);
    $oCmts->onObjectDelete();
    // delete all comments in all comments' systems, this user posted
    $oCmts->onAuthorDelete($ID);

	$iPossibleCoupleID = (int)db_value( "SELECT `ID` FROM `Profiles` WHERE `Couple` = '{$ID}'" );
	if ($iPossibleCoupleID) {
		db_res( "DELETE FROM `Profiles` WHERE `ID` = '{$iPossibleCoupleID}'" );
		//delete cache file
		deleteUserDataFile( $iPossibleCoupleID );
	}

	db_res( "DELETE FROM `Profiles` WHERE `ID` = '{$ID}'" );

	// create system event
	require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolAlerts.php');
	$oZ = new BxDolAlerts('profile', 'delete',  $ID);
	$oZ->alert();

	//delete cache file
	deleteUserDataFile( $ID );
}

function get_user_online_status ($ID) {
    $iOnline = 0;

    if($ID && is_numeric($ID) ) {
        $aMemberInfo  = getProfileInfo($ID);
        // check user status;
        if($aMemberInfo['UserStatus'] != 'offline') {
            $min     = getParam( "member_online_time" );
            $iOnline = $GLOBALS['MySQL']->fromMemory ("member_online_status.$ID.$min", 'getOne', "SELECT count(ID) as count_id FROM Profiles WHERE DateLastNav > SUBDATE(NOW(), INTERVAL {$min} MINUTE) AND ID={$ID}");
        }
    }

    return  $iOnline;
}

?>
