<?php
/**
*                            Orca Interactive Forum Script
*                              ---------------
*     Started             : Mon Mar 23 2006
*     Copyright           : (C) 2007 BoonEx Group
*     Website             : http://www.boonex.com
* This file is part of Orca - Interactive Forum Script
* Creative Commons Attribution 3.0 License
**/


/**
 *
 * redefine callback functions in Forum class
 *******************************************************************************/

require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolAlerts.php');
require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolProfile.php');

global $f;

$f->getUserInfo = 'getUserInfo';
$f->getUserPerm = 'getUserPerm';
$f->getLoginUser = 'getLoginUser';
$f->onPostReply = 'onPostReply';
$f->onPostEdit = 'onPostEdit'; // $arrayTopic, $intPostId, $stringPostText, $stringUser
$f->onPostDelete = 'onPostDelete'; // $arrayTopic, $intPostId, $stringUser
$f->onNewTopic = 'onNewTopic'; // $intForumId, $stringTopicSubject, $stringTopicText, $isTopicSticky, $stringUser, $stringTopicUri
$f->onVote = 'onVote'; // $intPostId, $stringUser, $intVote (1 or -1)
$f->onReport = 'onReport'; // $intPostId, $stringUser
$f->onFlag = 'onFlag'; // $intTopicId, $stringUser
$f->onUnflag = 'onUnflag'; // $intTopicId, $stringUser

function onPostReply ($aTopic, $sPostText, $sUser) {

    $oProfile = new BxDolProfile ($sUser);
    $aPlusOriginal = array (
        'PosterUrl' => $oProfile->_iProfileID ? getProfileLink($oProfile->_iProfileID) : 'javascript:void(0);' ,
        'PosterNickName' => $sUser,
        'TopicTitle' => $aTopic['topic_title'],
        'ReplyText' => $sPostText,
    );

    $oEmailTemplate = new BxDolEmailTemplates();
    $aTemplate = $oEmailTemplate->getTemplate('bx_forum_notifier');

    $fdb = new DbForum ();
    $a = $fdb->getSubscribersToTopic ($aTopic['topic_id']);
    foreach ($a as $r)
    {
        if ($r['user'] == $sUser) 
            continue;
        $oRecipient = new BxDolProfile ($r['user']);
        $aRecipient = getProfileInfo($oRecipient->_iProfileID);
        $aPlus = array_merge (array ('Recipient' => ' ' . $aRecipient['NickName']), $aPlusOriginal);
        sendMail(trim($aRecipient['Email']), $aTemplate['Subject'], $aTemplate['Body'], '', $aPlus);
    }

	$oAlert = new BxDolAlerts('bx_forum', 'reply', $aTopic['topic_id']);
	$oAlert->alert();    
}

function onPostEdit ($aTopic, $iPostId, $sPostText, $sUser) {
	$oAlert = new BxDolAlerts('bx_forum', 'post_edit', $iPostId);
	$oAlert->alert();    
}

function onPostDelete ($aTopic, $iPostId, $sUser) {
	$oAlert = new BxDolAlerts('bx_forum', 'edit_del', $iPostId);
	$oAlert->alert();    
}

function onNewTopic ($iForumId, $sTopicSubject, $sTopicText, $isTopicSticky, $sUser, $sTopicUri) {
    $a = array ($iForumId, $sTopicSubject, $sTopicText, $isTopicSticky, $sUser);
	$oAlert = new BxDolAlerts('bx_forum', 'new_topic', $sTopicUri, $a);
	$oAlert->alert();
}

function onVote ($iPostId, $sUser, $iVote) {
    $a = array ($sUser, $iVote);
	$oAlert = new BxDolAlerts('bx_forum', 'vote', $iPostId, $a);
	$oAlert->alert();
}

function onReport ($iPostId, $sUser) {
	$oAlert = new BxDolAlerts('bx_forum', 'post_report', $iPostId);
	$oAlert->alert();
}

function onFlag ($iTopicId, $sUser) {
	$oAlert = new BxDolAlerts('bx_forum', 'flag', $iTopicId);
	$oAlert->alert();
}

function onUnflag ($iTopicId, $sUser) {
	$oAlert = new BxDolAlerts('bx_forum', 'unflag', $iTopicId);
	$oAlert->alert();
}

function getUserInfo ($sUser) {

    $aRoles = array (
        '_bx_forum_role_admin' => BX_DOL_ROLE_ADMIN,        
        '_bx_forum_role_moderator' => BX_DOL_ROLE_MODERATOR,
        '_bx_forum_role_affiliate' => BX_DOL_ROLE_AFFILIATE,        
        '_bx_forum_role_member' => BX_DOL_ROLE_MEMBER,
        '_bx_forum_role_guest' => BX_DOL_ROLE_GUEST,
    );

    require_once( BX_DIRECTORY_PATH_ROOT . 'inc/utils.inc.php' );
    require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolProfile.php' );                    

    $aRet = array(); 

    $oProfile = new BxDolProfile ($sUser);

    $aRet['profile_onclick'] = '';
    $aRet['profile_url'] = getProfileLink($oProfile->_iProfileID);
    $aRet['admin'] = isAdmin($oProfile->_iProfileID);
    $aRet['special'] = false;    
    $aRet['join_date'] = '';

    $aRet['role'] = _t('_bx_forum_role_undefined');
    foreach ($aRoles as $sRolelangKey => $iRoleMask) {
        if (isRole($iRoleMask, $oProfile->_iProfileID)) {
            $aRet['role'] = _t($sRolelangKey);
            break;
        }
    }

    if ($oProfile->_iProfileID)
    {        
        $aProfileInfo = getProfileInfo($oProfile->_iProfileID);
        if ($aProfileInfo['Avatar']) {
		    include_once (BX_DIRECTORY_PATH_MODULES . 'boonex/avatar/include.php');
            $aRet['avatar'] = BX_AVA_URL_USER_AVATARS . $aProfileInfo['Avatar'] . 'i' . BX_AVA_EXT;
        } else {
            $aRet['avatar'] = $GLOBALS['oFunctions']->getSexPic($aProfileInfo['Sex'], 'small');
        }
    }

    // Ray integration [begin]
/*
    $iId = $oProfile->_iProfileID;
    $sPassword = md5(getPassword($iId));
    $bEnableRay = (getParam( 'enable_ray' ) == 'on');
    $check_res = checkAction ($iId, ACTION_ID_USE_RAY_IM);

    $aRay = '<ray_on>0</ray_on>';
    if ($bEnableRay && $check_res[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED) {
        $aRet['ray_on'] = 1;
        $aRet['ray_id'] = $iId;
        $aRet['ray_pwd'] = $sPassword;
    }
*/                  
    // Ray integration [ end ]
    
    return $aRet;
}

function getUserPerm ($sUser, $sType, $sAction, $iForumId) {

    global $logged;

    $iMemberId = $logged['member'] || $logged['admin'] ? $_COOKIE['memberID'] : 0;

    $isOrcaAdmin = $logged['admin'];

    $isLoggedIn = $iMemberId ? 1 : 0;

    defineMembershipActions (array ('forum public read', 'forum public post', 'forum private read', 'forum private post', 'forum search', 'forum edit all', 'forum delete all', 'forum make sticky', 'forum del topics', 'forum move topics', 'forum hide topics', 'forum unhide topics', 'forum hide posts', 'forum unhide posts', 'forum files download'));

    $isPublicForumReadAllowed  =                ($aCheck = checkAction($iMemberId, BX_FORUM_PUBLIC_READ, true)) && CHECK_ACTION_RESULT_ALLOWED == $aCheck[CHECK_ACTION_RESULT] ? 1 : 0;
    $isPublicForumPostAllowed  = $isLoggedIn && ($aCheck = checkAction($iMemberId, BX_FORUM_PUBLIC_POST, true)) && CHECK_ACTION_RESULT_ALLOWED == $aCheck[CHECK_ACTION_RESULT] ? 1 : 0;
    $isPrivateForumReadAllowed = $isLoggedIn && ($aCheck = checkAction($iMemberId, BX_FORUM_PRIVATE_READ, true)) && CHECK_ACTION_RESULT_ALLOWED == $aCheck[CHECK_ACTION_RESULT] ? 1 : 0;
    $isPrivateForumPostAllowed = $isLoggedIn && ($aCheck = checkAction($iMemberId, BX_FORUM_PRIVATE_POST, true)) && CHECK_ACTION_RESULT_ALLOWED == $aCheck[CHECK_ACTION_RESULT] ? 1 : 0;
    $isEditAllAllowed = ($aCheck = checkAction($iMemberId, BX_FORUM_EDIT_ALL, true)) && CHECK_ACTION_RESULT_ALLOWED == $aCheck[CHECK_ACTION_RESULT] ? 1 : 0;
    $isDelAllAllowed = ($aCheck = checkAction($iMemberId, BX_FORUM_DELETE_ALL, true)) && CHECK_ACTION_RESULT_ALLOWED == $aCheck[CHECK_ACTION_RESULT] ? 1 : 0;

    return array (
        'read_public' => $isOrcaAdmin || $isPublicForumReadAllowed,
        'post_public' => $isOrcaAdmin || $isPublicForumPostAllowed ? 1 : 0,
        'edit_public' => $isOrcaAdmin || $isEditAllAllowed ? 1 : 0,
        'del_public'  => $isOrcaAdmin || $isEditAllAllowed ? 1 : 0,

        'read_private' => $isOrcaAdmin || $isPrivateForumReadAllowed ? 1 : 0,
        'post_private' => $isOrcaAdmin || $isPrivateForumPostAllowed ? 1 : 0,
        'edit_private' => $isOrcaAdmin || $isEditAllAllowed ? 1 : 0,
        'del_private'  => $isOrcaAdmin || $isDelAllAllowed ? 1 : 0,

        'edit_own' => 1,
        'del_own' => 1,

        'download_' => $isOrcaAdmin  || (($aCheck = checkAction($iMemberId, BX_FORUM_FILES_DOWNLOAD, true)) && CHECK_ACTION_RESULT_ALLOWED == $aCheck[CHECK_ACTION_RESULT]) ? 1 : 0,
        'search_' => $isOrcaAdmin  || (($aCheck = checkAction($iMemberId, BX_FORUM_SEARCH, true)) && CHECK_ACTION_RESULT_ALLOWED == $aCheck[CHECK_ACTION_RESULT]) ? 1 : 0,
        'sticky_' => $isOrcaAdmin  || (($aCheck = checkAction($iMemberId, BX_FORUM_MAKE_STICKY, true)) && CHECK_ACTION_RESULT_ALLOWED == $aCheck[CHECK_ACTION_RESULT]) ? 1 : 0,

        'del_topics_' => $isOrcaAdmin  || (($aCheck = checkAction($iMemberId, BX_FORUM_DEL_TOPICS, true)) && CHECK_ACTION_RESULT_ALLOWED == $aCheck[CHECK_ACTION_RESULT]) ? 1 : 0,
        'move_topics_' => $isOrcaAdmin  || (($aCheck = checkAction($iMemberId, BX_FORUM_MOVE_TOPICS, true)) && CHECK_ACTION_RESULT_ALLOWED == $aCheck[CHECK_ACTION_RESULT]) ? 1 : 0,
        'hide_topics_' => $isOrcaAdmin  || (($aCheck = checkAction($iMemberId, BX_FORUM_HIDE_TOPICS, true)) && CHECK_ACTION_RESULT_ALLOWED == $aCheck[CHECK_ACTION_RESULT]) ? 1 : 0,
        'unhide_topics_' => $isOrcaAdmin  || (($aCheck = checkAction($iMemberId, BX_FORUM_HIDE_TOPICS, true)) && CHECK_ACTION_RESULT_ALLOWED == $aCheck[CHECK_ACTION_RESULT]) ? 1 : 0,
        'hide_posts_' => $isOrcaAdmin  || (($aCheck = checkAction($iMemberId, BX_FORUM_HIDE_TOPICS, true)) && CHECK_ACTION_RESULT_ALLOWED == $aCheck[CHECK_ACTION_RESULT]) ? 1 : 0,
        'unhide_posts_' => $isOrcaAdmin  || (($aCheck = checkAction($iMemberId, BX_FORUM_HIDE_TOPICS, true)) && CHECK_ACTION_RESULT_ALLOWED == $aCheck[CHECK_ACTION_RESULT]) ? 1 : 0,
    );
}

function getLoginUser () {
    global $logged;
    if ($logged['member'] || $logged['admin'])
        return getNickName(getID((int)$_COOKIE['memberID']));
    
    return '';
}

?>
