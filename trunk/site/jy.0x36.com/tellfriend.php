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

require_once('inc/header.inc.php');
require_once(BX_DIRECTORY_PATH_INC . 'design.inc.php');
require_once(BX_DIRECTORY_PATH_INC . 'profiles.inc.php');
require_once(BX_DIRECTORY_PATH_INC . 'utils.inc.php');
require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolEmailTemplates.php');

bx_import('BxTemplFormView');

// --------------- page variables and login

$_page['name_index'] 	= 29;
$_page['css_name']		= array('general.css', 'tellfriend.css', 'forms_adv.css');

$_page['header'] = _t("_Tell a friend");
$_page['header_text'] = _t("_Tell a friend");

$profileID = 0;
if( isset($_GET['ID']) ) {
    $profileID = (int) $_GET['ID'];
}
else if( isset($_POST['ID']) ) {
	$profileID = (int) $_POST['ID'];
}

$iSenderID = getLoggedId();
$aSenderInfo = getProfileInfo($iSenderID);

// --------------- GET/POST actions
$tell_friend_text = '';
if($_POST['submit_send']) {
	$tell_friend_text = SendTellFriend($iSenderID) ? "_Email was successfully sent" : "_Email sent failed";
	$tell_friend_text = MsgBox(_t($tell_friend_text));
}

// --------------- page components
$sYEmlNotValidC = _t('_Incorrect Email');
$sFEmlNotValidC = $sYEmlNotValidC . ' (' . _t('_Friend email') . ')';

$sCaption = ($profileID) ? _t('_TELLAFRIEND2', $site['title']) : _t('_TELLAFRIEND', $site['title']);

$aForm = array(
    'form_attrs' => array(
        'id' => 'invite_friend',
        'name' => 'invite_friend',
        'action' => BX_DOL_URL_ROOT . 'tellfriend.php',
        'method' => 'post',
        'enctype' => 'multipart/form-data',
        'onsubmit' => "var feml = document.forms['invite_friend'].friends_emails; var yeml = document.forms['invite_friend'].email; var bRet = true; if(emailCheck(yeml.value)==false) { alert('{$sYEmlNotValidC}'); bRet = false; } if (emailCheck(feml.value)==false) { alert('{$sFEmlNotValidC}'); bRet = false; } return bRet; "
    ),
    'inputs' => array (
        'header1' => array(
            'type' => 'block_header',
            'caption' => $sCaption,
        ),
        'id' => array(
            'type' => 'hidden',
            'name' => 'ID',                
            'value' => $profileID
        ),                                    
        'name' => array(
            'type' => 'text',
            'name' => 'name',
            'caption' => _t("_Your name"),
            'value' => $aSenderInfo['NickName']
        ),
        'email' => array(
            'type' => 'text',
            'name' => 'email',
            'caption' => _t("_Your email"),
            'value' => $aSenderInfo['Email']
        ),
        'friends_emails' => array(
            'type' => 'text',
            'name' => 'friends_emails',
            'caption' => _t("_Friend email"),
            'value' => ''
        ),            
        'submit_send' => array(
            'type' => 'submit',
            'name' => 'submit_send',
            'value' => _t("_Send Letter"),
        ),                
    )
);

$oForm = new BxTemplFormView($aForm);

$_ni = $_page['name_index'];
$_page_cont[$_ni]['page_code'] = $tell_friend_text . $oForm->getCode();
// --------------- [END] page components

PageCode();
// --------------- page components functions

/**
 * send "tell a friend" email
 */

function SendTellFriend($iSenderID = 0) {
    global $profileID;

    $sRecipient   = clear_xss($_POST['friends_emails']);
    $sSenderName  = clear_xss($_POST['name']);
    $sSenderEmail = clear_xss($_POST['email']);
    if ( strlen( trim($sRecipient) ) <= 0 )
        return 0;
    if ( strlen( trim($sSenderEmail) ) <= 0 )
        return 0;

    $sLinkAdd = $iSenderID > 0 ? 'idFriend=' . $iSenderID : '';
    $rEmailTemplate = new BxDolEmailTemplates();
    if ( $profileID )
    {
        $aTemplate = $rEmailTemplate -> getTemplate( 't_TellFriendProfile', $profileID ) ;
        $Link = getProfileLink($profileID, $sLinkAdd);
    }
    else
    {
        $aTemplate = $rEmailTemplate -> getTemplate( 't_TellFriend' ) ;
        $Link = BX_DOL_URL_ROOT;
        if (strlen($sLinkAdd) > 0)
        	$Link .= '?' . $sLinkAdd;
    }
    $aPlus = array(
		'Link' => $Link,
		'FromName' => $sSenderName
	);
	return sendMail($sRecipient, $aTemplate['Subject'], $aTemplate['Body'], '', $aPlus);
}

?>