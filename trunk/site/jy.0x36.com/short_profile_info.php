<?php

require_once('inc/header.inc.php');
require_once(BX_DIRECTORY_PATH_INC . 'design.inc.php');
require_once(BX_DIRECTORY_PATH_INC . 'params.inc.php');
require_once(BX_DIRECTORY_PATH_INC . 'utils.inc.php');

bx_import('BxDolUserStatusView');
bx_import('BxDolSubscription');

check_logged();

$iMemberId  = ( isset($_COOKIE['memberID']) && isMember() ) ? (int)$_COOKIE['memberID'] : 0;

$sTemplate = 'short_profile_info.html';

if (isset($_GET['ID']) && (int)$_GET['ID'] > 0) {
    $iProfId = (int)$_GET['ID'];
    $aProfileInfo = getProfileInfo($iProfId);
    $aMemberInfo = getProfileInfo($iMemberId);

    $aProfileInfo['window_width']    = $oTemplConfig->popUpWindowWidth;
    $aProfileInfo['window_height']   = $oTemplConfig->popUpWindowHeight;
    $aProfileInfo['anonym_mode']     = $oTemplConfig->bAnonymousMode;
    $aProfileInfo['member_pass']     = $aMemberInfo['Password'];
    $aProfileInfo['member_id']		= $iMemberId;
    $bDisplayType = ( getParam('enable_new_dhtml_popups')=='on' ) ? 0 : 1;
    $aProfileInfo['display_type'] = $bDisplayType;
    $aProfileInfo['url'] = BX_DOL_URL_ROOT;
    $aProfileInfo['status_message'] = process_line_output($aProfileInfo['UserStatusMessage']);

    //--- Subscription integration ---//		
	$oSubscription = new BxDolSubscription();
    $aButton = $oSubscription->getButton($iMemberId, 'profile', '', $iProfId);
    
    $aProfileInfo['sbs_profile_title'] = $aButton['title'];
	$aProfileInfo['sbs_profile_script'] = $aButton['script'];
    //--- Subscription integration ---//
    
    //--- Check for member/non-member ---//
    if(isMember()) {
    	$aProfileInfo['cpt_edit'] = _t('_EditProfile');
        $aProfileInfo['cpt_send_letter'] = _t('_SendLetter');
        $aProfileInfo['cpt_fave'] = _t('_Fave');
        $aProfileInfo['cpt_befriend'] = _t('_Befriend');
        $aProfileInfo['cpt_remove_friend'] = _t('_Remove friend');
        $aProfileInfo['cpt_greet'] = _t('_Greet');
        $aProfileInfo['cpt_get_mail'] = _t('_Get E-mail');
        $aProfileInfo['cpt_share'] = _t('_Share');
        $aProfileInfo['cpt_report'] = _t('_Report Spam');
        $aProfileInfo['cpt_block'] = _t('_Block');
        $aProfileInfo['cpt_unblock'] = _t('_Unblock');
    }
    else {
    	$aProfileInfo['cpt_edit'] = '';
        $aProfileInfo['cpt_send_letter'] = '';
        $aProfileInfo['cpt_fave'] = '';
        $aProfileInfo['cpt_befriend'] = '';
        $aProfileInfo['cpt_remove_friend'] = '';
        $aProfileInfo['cpt_greet'] = '';
        $aProfileInfo['cpt_get_mail'] = '';
        $aProfileInfo['cpt_share'] = '';
        $aProfileInfo['cpt_report'] = '';
        $aProfileInfo['cpt_block'] = '';
        $aProfileInfo['cpt_unblock'] = '';
    }
    
    $sProfLink = '<a href="'.getProfileLink($iProfId).'">'.$aProfileInfo['NickName'].'</a> ';

	$oUserStatus = new BxDolUserStatusView();
	$sUserIcon = $oUserStatus->getStatusIcon($iProfId, $aProfileInfo['UserStatus']);
	$sUserStatus = $oUserStatus->getStatus($iProfId);

    $aUnit = array(
        'status_pic' => getTemplateIcon($sUserIcon),
        'profile_status' => _t('_prof_status', $sProfLink, $sUserStatus),
        'profile_status_message' => $aProfileInfo['status_message'],
        'profile_actions' => $oFunctions->genObjectsActions( $aProfileInfo, 'Profile'),
        'bx_if:profile_status_cond' => array(
              'condition' => $aProfileInfo['status'] == 'online',
              'content' => array('chat_invite' => $sChat)
             )
    );

	$sCloseIcon = getTemplateIcon('reduce.png');
    ob_start();
    ?>
        <div class="reduce">
            <img src="<?= $sCloseIcon ?>" class="login_ajx_close" />
        </div>
    <?
    $sClose = ob_get_clean();
    $sClose = str_replace('__site_images__', $site['images'], $sClose);
    echo $oFunctions->transBox(
        $sClose . $oSysTemplate->parseHtmlByName($sTemplate, $aUnit)
    );
}

?>