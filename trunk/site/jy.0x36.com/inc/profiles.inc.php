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
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'images.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'params.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'tags.inc.php' );

// user roles
define('BX_DOL_ROLE_GUEST',     0);
define('BX_DOL_ROLE_MEMBER',    1);
define('BX_DOL_ROLE_ADMIN',     2);
define('BX_DOL_ROLE_AFFILIATE', 4);
define('BX_DOL_ROLE_MODERATOR', 8);

/**
 * The following functions are needed to check whether user is logged in or not, active or not and get his ID.
 */
function isLogged() {
    return getLoggedId() != 0;
}
function isLoggedActive() {
    return isProfileActive();
}
function getLoggedId() {    
    return isset($_COOKIE['memberID']) && (!empty($GLOBALS['logged']['member']) || !empty($GLOBALS['logged']['admin'])) ? (int)$_COOKIE['memberID'] : 0;
}
function getLoggedPassword() {    
    return isset($_COOKIE['memberPassword']) && ($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) ? $_COOKIE['memberPassword'] : '';
}

/**
 * The following functions are needed to check the ROLE of user.
 */
function isMember($iId = 0) {
    return isRole(BX_DOL_ROLE_MEMBER, $iId);
}
if(!function_exists("isAdmin")) {
	function isAdmin($iId = 0) {
	    return isRole(BX_DOL_ROLE_ADMIN, $iId);
	}
}
function isAffiliate($iId = 0) {
    return isRole(BX_DOL_ROLE_AFFILIATE, $iId);
}
function isModerator($iId = 0) {
    return isRole(BX_DOL_ROLE_MODERATOR, $iId);
}
function isRole($iRole, $iId = 0) {
    $aProfile = getProfileInfo($iId);
    if($aProfile === false)
        return false;

    if(!((int)$aProfile['Role'] & $iRole))
        return false;

    return true;
}

$aUser = array(); //global cache array

function ShowZodiacSign( $date ) {
	global $site;

	if ( $date == "0000-00-00" )
		return "";

	if ( strlen($date) ) {
		$m = substr( $date, -5, 2 );
		$d = substr( $date, -2, 2 );

		switch ( $m ) {
			case '01': if ( $d <= 20 ) $sign = "capricorn"; else $sign = "aquarius";
				break;
			case '02': if ( $d <= 20 ) $sign = "aquarius"; else $sign = "pisces";
				break;
			case '03': if ( $d <= 20 ) $sign = "pisces"; else $sign = "aries";
				break;
			case '04': if ( $d <= 20 ) $sign = "aries"; else $sign = "taurus";
				break;
			case '05': if ( $d <= 20 ) $sign = "taurus"; else $sign = "gemini";
				break;
			case '06': if ( $d <= 21 ) $sign = "gemini"; else $sign = "cancer";
				break;
			case '07': if ( $d <= 22 ) $sign = "cancer"; else $sign = "leo";
				break;
			case '08': if ( $d <= 23 ) $sign = "leo"; else $sign = "virgo";
				break;
			case '09': if ( $d <= 23 ) $sign = "virgo"; else $sign = "libra";
				break;
			case '10': if ( $d <= 23 ) $sign = "libra"; else $sign = "scorpio";
				break;
			case '11': if ( $d <= 22 ) $sign = "scorpio"; else $sign = "sagittarius";
				break;
			case '12': if ( $d <= 21 ) $sign = "sagittarius"; else $sign = "capricorn";
	    }
		$sIcon = $sign . '.png';
		return '<img src="' . $site['zodiac'] . $sIcon . '" alt="' . $sign . '" title="' . $sign . '" />';
	} else {
		return "";
	}
}

function age( $birth_date ) {
	if ( $birth_date == "0000-00-00" )
		return _t("_uknown");

	$bd = explode( "-", $birth_date );
	$age = date("Y") - $bd[0] - 1;

	$arr[1] = "m";
	$arr[2] = "d";

	for ( $i = 1; $arr[$i]; $i++ ) {
		$n = date( $arr[$i] );
		if ( $n < $bd[$i] )
			break;
		if ( $n > $bd[$i] ) {
			++$age;
			break;
		}
	}

	return $age;
}

/**
 * Print code for membership status
 * $memberID - member ID
 * $offer_upgrade - will this code be printed at [c]ontrol [p]anel
 */
function GetMembershipStatus($memberID, $offer_upgrade = true) {
	$ret = '';

	$membership_info = getMemberMembershipInfo($memberID);

	$viewMembershipActions = "<br />(<a onclick=\"javascript:window.open('explanation.php?explain=membership&amp;type=".$membership_info['ID']."', '', 'width=660, height=500, menubar=no, status=no, resizable=no, scrollbars=yes, toolbar=no, location=no');\" href=\"javascript:void(0);\">"._t("_VIEW_MEMBERSHIP_ACTIONS")."</a>)<br />";

	// Show colored membership name
	if ( $membership_info['ID'] == MEMBERSHIP_ID_STANDARD ) {
		$ret .= _t( "_MEMBERSHIP_STANDARD" ). $viewMembershipActions;
		if ( $offer_upgrade )
			$ret .= " ". _t( "_MEMBERSHIP_UPGRADE_FROM_STANDARD" );
	} else {
		$ret .= "<font color=\"red\">{$membership_info['Name']}</font>$viewMembershipActions";

		$days_left = (int)( ($membership_info['DateExpires'] - time()) / (24 * 3600) );

		if(!is_null($membership_info['DateExpires'])) {
			$ret .= ( $days_left > 0 ) ? _t( "_MEMBERSHIP_EXPIRES_IN_DAYS", $days_left ) : _t( "_MEMBERSHIP_EXPIRES_TODAY", date( "H:i", $membership_info['DateExpires'] ), date( "H:i" ) );
		} else {
			$ret.= _t("_MEMBERSHIP_EXPIRES_NEVER");
		}
	}
	return $ret;
}

function isAutoApproval( $sAction ) {
	$autoApproval_ifPhoto   = ( 'on' == getParam("autoApproval_ifPhoto") );
	$autoApproval_ifSound   = ( 'on' == getParam("autoApproval_ifSound") );
	$autoApproval_ifVideo   = ( 'on' == getParam("autoApproval_ifVideo") );
	$autoApproval_ifProfile = ( 'on' == getParam("autoApproval_ifProfile") );
	$autoApproval_ifJoin    = ( 'on' == getParam("autoApproval_ifJoin") );

	switch ( $sAction ) {
		case 'photo':
			return $autoApproval_ifPhoto;
		case 'sound':
			return $autoApproval_ifSound;
		case 'video':
			return $autoApproval_ifVideo;
		case 'profile':
			return $autoApproval_ifProfile;
		case 'join':
			return $autoApproval_ifJoin;
		default:
			return false;
	}
}

function deleteUserDataFile( $userID ) {
	 global $aUser;

    $bUseCacheSystem = ( getParam('enable_cache_system') == 'on' ) ? true : false;
	if (!$bUseCacheSystem) return false;

	$userID = (int)$userID;
	$fileName = BX_DIRECTORY_PATH_CACHE . 'user' . $userID . '.php';
	if( file_exists($fileName) ) {
		unlink($fileName);
	}
}

function createUserDataFile( $userID ) {
    global $aUser;

    $bUseCacheSystem = ( getParam('enable_cache_system') == 'on' ) ? true : false;
	if (!$bUseCacheSystem) return false;

	$userID = (int)$userID;
	$fileName = BX_DIRECTORY_PATH_CACHE . 'user' . $userID . '.php';
	if( $userID > 0 ) {

		$aPreUser = getProfileInfoDirect ($userID);

		if( isset( $aPreUser ) and is_array( $aPreUser ) and $aPreUser) {
			$sUser = '<?';
			$sUser .= "\n\n";
			$sUser .= '$aUser[' . $userID . '] = array();';
			$sUser .= "\n";
			$sUser .= '$aUser[' . $userID . '][\'datafile\'] = true;';
			$sUser .= "\n";

			$replaceWhat = array( '\\',   '\''   );
			$replaceTo   = array( '\\\\', '\\\'' );

			foreach( $aPreUser as $key =>  $value )
				$sUser .= '$aUser[' . $userID . '][\'' . $key . '\']' . ' = ' . '\'' . str_replace( $replaceWhat, $replaceTo, $value )  . '\'' . ";\n";

			$sUser .= "\n" . '?>';

			if( $file = fopen( $fileName, "w" ) ) {
				fwrite( $file, $sUser );
				fclose( $file );
				@chmod ($fileName, 0666);

				@include( $fileName );
				return true;
			} else
				return false;
		}
	} else
		return false;
}

/**
 * Check whether the requested profile is active or not. 
 */
function isProfileActive($iId = 0) {
    $aProfile = getProfileInfo($iId);
    if($aProfile === false || empty($aProfile))
        return false;
        
    return $aProfile['Status'] == 'Active';
}
function getProfileInfoDirect ($iProfileID) {
    return $GLOBALS['MySQL']->getRow("SELECT * FROM `Profiles` WHERE `ID`='" . $iProfileID . "' LIMIT 1");
}

function getProfileInfo($iProfileID = 0, $checkActiveStatus = false, $forceCache = false) {
	global $aUser;

    $iProfileID = !empty($iProfileID) ? (int)$iProfileID : getLoggedId();    
	if(!$iProfileID)
		return false;    

	if(!isset( $aUser[$iProfileID]) || !is_array($aUser[$iProfileID]) || $forceCache) {
		$sCacheFile = BX_DIRECTORY_PATH_CACHE . 'user' . $iProfileID . '.php';        
        if( !file_exists( $sCacheFile ) || $forceCache ) {            
            if( !createUserDataFile( $iProfileID ) ) {
                return getProfileInfoDirect ($iProfileID);
            }
        }

		@include( $sCacheFile );
	}

	if( $checkActiveStatus and $aUser[$iProfileID]['Status'] != 'Active' )
		return false;

	return $aUser[$iProfileID];
}

/* osed only for xmlrpc */
function getNewLettersNum( $iID ) {
	$sqlQuery = 
	"
		SELECT 
			COUNT(`Recipient`) 
		FROM 
			`sys_messages` 
		WHERE 
			`Recipient`='$iID' 
				AND 
			`New`='1'
		 		 AND
        NOT FIND_IN_SET('Recipient', `Trash`)
	";
	return (int)db_value($sqlQuery);
}

/*function for inner using only 
    $ID - profile ID
    $iFrStatus - friend status (1 - approved, 0 - wait)
    $iOnline - filter for last nav moment (in minutes)
    $sqlWhere - add sql Conditions, should beginning from AND
*/
function getFriendNumber($iID, $iFrStatus = 1, $iOnline = 0, $sqlWhere = '') {
	$sqlAdd = '';

    if ($iOnline > 0) 
        $sqlAdd = " AND (p.`DateLastNav` > SUBDATE(NOW(), INTERVAL " . $iOnline . " MINUTE))";
    
    if (strlen($sqlWhere) > 0)
        $sqlAdd .= $sqlWhere;

	$sqlQuery = "SELECT COUNT(`f`.`ID`)
	FROM 
	(SELECT `ID` AS `ID` FROM `sys_friend_list` WHERE `Profile` = '{$iID}' AND `Check` = {$iFrStatus}
	UNION
	SELECT `Profile` AS `ID` FROM `sys_friend_list` WHERE `ID` = '{$iID}' AND `Check` = {$iFrStatus})
	AS `f` 
    INNER JOIN `Profiles` AS `p` ON `p`.`ID` = `f`.`ID`
    WHERE 1 {$sqlAdd}";

	return (int)db_value($sqlQuery);
}

function getMyFriendsEx($iID, $sWhereParam = '', $sSortParam = '', $sqlLimit = '') {
	$sJoin = $sOrderBy = '';

	switch($sSortParam) {
		
        case 'activity' : 
		case 'last_nav' : // DateLastNav
			$sOrderBy = 'ORDER BY p.`DateLastNav`';
			break;
        case 'activity_desc' : 
        case 'last_nav_desc' : // DateLastNav
			$sOrderBy = 'ORDER BY p.`DateLastNav` DESC';
			break;
		case 'date_reg' : // DateReg
			$sOrderBy = 'ORDER BY p.`DateReg`';
			break;
        case 'date_reg_desc' : // DateReg
			$sOrderBy = 'ORDER BY p.`DateReg` DESC';
			break;
		case 'image' : // Avatar
			$sOrderBy = 'ORDER BY p.`Avatar` DESC';
			break;	
		case 'rate' : // `sys_profile_rating`.`pr_rating_sum
			$sOrderBy = 'ORDER BY `sys_profile_rating`.`pr_rating_sum`';
			$sJoin = 'LEFT JOIN `sys_profile_rating` ON p.`ID` = `sys_profile_rating`.`pr_id`';
			break;
		default : // DateLastNav
			$sOrderBy = 'ORDER BY p.`DateLastNav` DESC';
			break;    	
	}

	$sLimit = ($sqlLimit == '') ? '' : /*"LIMIT 0, " .*/ $sqlLimit;
    $iOnlineTime = (int)getParam( "member_online_time" );
	$sqlQuery = "SELECT `p`.*, `f`.`ID`, 
				if(`DateLastNav` > SUBDATE(NOW( ), INTERVAL $iOnlineTime MINUTE ), 1, 0) AS `is_online`,
				UNIX_TIMESTAMP(p.`DateLastLogin`) AS 'TS_DateLastLogin', UNIX_TIMESTAMP(p.`DateReg`) AS 'TS_DateReg' 	FROM (
				SELECT `ID` AS `ID` FROM `sys_friend_list` WHERE `Profile` = '{$iID}' AND `Check` =1
				UNION
				SELECT `Profile` AS `ID` FROM `sys_friend_list` WHERE `ID` = '{$iID}' AND `Check` =1
			) AS `f`
			INNER JOIN `Profiles` AS `p` ON `p`.`ID` = `f`.`ID`
			{$sJoin}
            WHERE 1 {$sWhereParam}
			{$sOrderBy}
			{$sLimit}";

	$aFriends = array();

	$vProfiles = db_res($sqlQuery);
	while ($aProfiles = mysql_fetch_assoc($vProfiles)) {
		$aFriends[$aProfiles['ID']] = array($aProfiles['ID'], $aProfiles['TS_DateLastLogin'], $aProfiles['TS_DateReg'], $aProfiles['Rate'], $aProfiles['DateLastNav'], $aProfiles['is_online']);
	}

	return $aFriends;
}

/*
* The function returns NickName by given ID. If no ID specified, it tryes to get if from _COOKIE['memberID'];
*/
function getNickName( $ID = '' ) {
	if ( !$ID && !empty($_COOKIE['memberID']) )
		$ID = (int)$_COOKIE['memberID'];

    if ( !$ID )
		return '';

	$arr = getProfileInfo( $ID );
	return $arr['NickName'];
}

/*
 * The function returns Password by given ID.
 */
function getPassword( $ID = '' ) {
    if ( !(int)$ID )
		return '';

	$arr = getProfileInfo( $ID );
	return $arr['Password'];
}

function getProfileLink( $iID, $sLinkAdd = '' ) {
	$aProfInfo = getProfileInfo( $iID );
	$iID = ($aProfInfo['Couple'] > 0 && $aProfInfo['ID'] > $aProfInfo['Couple']) ? $aProfInfo['Couple'] : $iID;

	return (getParam('enable_modrewrite') == 'on') ? BX_DOL_URL_ROOT . getNickName($iID) . ( $sLinkAdd ? "?{$sLinkAdd}" : '' ) : BX_DOL_URL_ROOT . 'profile.php?ID='.$iID . ( $sLinkAdd ? "&{$sLinkAdd}" : '' );
}

function isLoggedBanned($iCurUserID = 0) {
	$iCCurUserID = ($iCurUserID>0) ? $iCurUserID : (int)$_COOKIE['memberID'];
	if ($iCCurUserID) {
		$CheckSQL = "
			SELECT * 
			FROM `sys_admin_ban_list` 
			WHERE `ProfID`='{$iCCurUserID}'
		";
		db_res($CheckSQL);
		if (db_affected_rows()>0) {
		    return true;
		}
	}
	return false;
}
function bx_login($iId, $bRememberMe = false) {
        
    $sPassword = getPassword($iId);
    
    $aUrl = parse_url($GLOBALS['site']['url']);
    $sPath = isset($aUrl['path']) && !empty($aUrl['path']) ? $aUrl['path'] : '/';
    $sHost = '';
    $iCookieTime = $bRememberMe ? time() + 24*60*60*30 : 0;
    setcookie("memberID", $iId, $iCookieTime, $sPath, $sHost);
	$_COOKIE['memberID'] = $iId;
    setcookie("memberPassword", $sPassword, $iCookieTime, $sPath, $sHost, false, true /* http only */);
	$_COOKIE['memberPassword'] = $sPassword;

    db_res("UPDATE `Profiles` SET `DateLastLogin`=NOW(), `DateLastNav`=NOW() WHERE `ID`='" . $iId . "'");
    createUserDataFile($iId);

    require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolAlerts.php');
    $oZ = new BxDolAlerts('profile', 'login',  $iId);
    $oZ->alert();
    
    return getProfileInfo($iId);
}
function bx_logout($bNotify = true) { 
    if($bNotify && isMember()) {
        require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolAlerts.php');
        $oZ = new BxDolAlerts('profile', 'logout', (int)$_COOKIE['memberID']);
        $oZ->alert();
    }
    
    $aUrl = parse_url($GLOBALS['site']['url']);
    $sPath = isset($aUrl['path']) && !empty($aUrl['path']) ? $aUrl['path'] : '/';

	setcookie('memberID', '', time() - 96 * 3600, $sPath);
    setcookie('memberPassword', '', time() - 96 * 3600, $sPath);

    unset($_COOKIE['memberID']);
    unset($_COOKIE['memberPassword']);
}

function setSearchStartAge($iMin) {
    if ($iMin <= 0)
        return false;
    
    $GLOBALS['MySQL']->query("update `sys_profile_fields` set `Min` = $iMin where `Name` = 'DateOfBirth'");
    
    require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolPFM.php');
    $oCacher = new BxDolPFMCacher();
	$oCacher -> createCache();
    
    return true;
}

function setSearchEndAge($iMax) {
    if ($iMax <= 0)
        return false;
    
    $GLOBALS['MySQL']->query("update `sys_profile_fields` set `Max` = $iMax where `Name` = 'DateOfBirth'");
    
    require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolPFM.php');
    $oCacher = new BxDolPFMCacher();
	$oCacher -> createCache();
    
    return true;
}

check_logged();
