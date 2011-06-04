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
require_once( BX_DIRECTORY_PATH_INC . 'db.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'languages.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );

//MESSAGE CONSTANTS PASSED TO _t_ext() FUNCTION BY checkAction()
//NOTE: checkAction() RETURNS LANGUAGE DEPENDANT MESSAGES

define('CHECK_ACTION_MESSAGE_NOT_ALLOWED',			"_ACTION_NOT_ALLOWED");
define('CHECK_ACTION_MESSAGE_NOT_ACTIVE',			"_ACTION_NOT_ACTIVE");
define('CHECK_ACTION_MESSAGE_LIMIT_REACHED',		"_ACTION_LIMIT_REACHED");
define('CHECK_ACTION_MESSAGE_MESSAGE_EVERY_PERIOD',	"_ACTION_EVERY_PERIOD");
define('CHECK_ACTION_MESSAGE_NOT_ALLOWED_BEFORE',	"_ACTION_NOT_ALLOWED_BEFORE");
define('CHECK_ACTION_MESSAGE_NOT_ALLOWED_AFTER',	"_ACTION_NOT_ALLOWED_AFTER");

//NODES OF THE $args ARRAY THAT IS PASSED TO THE _t_ext() FUNCTION BY checkAction()

define('CHECK_ACTION_LANG_FILE_ACTION',		1);
define('CHECK_ACTION_LANG_FILE_MEMBERSHIP',	2);
define('CHECK_ACTION_LANG_FILE_LIMIT',		3);
define('CHECK_ACTION_LANG_FILE_PERIOD',		4);
define('CHECK_ACTION_LANG_FILE_AFTER',		5);
define('CHECK_ACTION_LANG_FILE_BEFORE',		6);
define('CHECK_ACTION_LANG_FILE_SITE_EMAIL',	7);

//ACTION ID's

define('ACTION_ID_SEND_VKISS',		 1);
define('ACTION_ID_VIEW_PROFILES',	 2);
define('ACTION_ID_VOTE',			 3);
define('ACTION_ID_SEND_MESSAGE',	 4);
define('ACTION_ID_GET_EMAIL',		 5);
define('ACTION_ID_COMMENTS_POST', 6);
define('ACTION_ID_COMMENTS_VOTE', 7);
define('ACTION_ID_COMMENTS_EDIT_OWN', 8);
define('ACTION_ID_COMMENTS_REMOVE_OWN', 9);

//PREDEFINED MEMBERSHIP ID's

define('MEMBERSHIP_ID_NON_MEMBER', 1);
define('MEMBERSHIP_ID_STANDARD', 2);
define('MEMBERSHIP_ID_PROMOTION', 3);

//INDICES FOR checkAction() RESULT ARRAY

define('CHECK_ACTION_RESULT', 0);
define('CHECK_ACTION_MESSAGE', 1);
define('CHECK_ACTION_PARAMETER', 3);

//CHECK_ACTION_RESULT NODE VALUES

define('CHECK_ACTION_RESULT_ALLOWED',				0);
define('CHECK_ACTION_RESULT_NOT_ALLOWED',			1);
define('CHECK_ACTION_RESULT_NOT_ACTIVE',			2);
define('CHECK_ACTION_RESULT_LIMIT_REACHED',			3);
define('CHECK_ACTION_RESULT_NOT_ALLOWED_BEFORE',	4);
define('CHECK_ACTION_RESULT_NOT_ALLOWED_AFTER',		5);

/**
 * Returns number of members with a given membership at a given time
 *
 * @param int $membershipID		- members of what membership should be counted.
 * 								  if 0, then all members are counted ($except is not considered);
 * 								  if MEMBERSHIP_ID_NON_MEMBER is specified, function returns -1
 *
 * @param unix_timestamp $time	- date/time to use when counting members.
 * 								  if not specified, uses the present moment
 * @param boolean $except		- if true, counts all members that DON'T have specified membership
 *
 *
 */
/*function getMembersCount($membershipID = 0, $time = '', $except = false)  //'A' just old unused function
{
	$membershipID = (int)$membershipID;
	$time = ($time == '') ? time() : (int)$time;
	$except = $except ? true : false;

	if($membershipID == MEMBERSHIP_ID_NON_MEMBER || $membershipID < 0) return -1;

	$resProfiles = db_res("SELECT COUNT(*) FROM Profiles");

	$totalProfiles = mysql_fetch_row($resProfiles);
	$totalProfiles = (int)$totalProfiles[0];

	if($membershipID == 0) return $totalProfiles;

	$queryWhereMembership = '';

	if($membershipID != MEMBERSHIP_ID_STANDARD) $queryWhereMembership = "IDLevel = $membershipID AND";

	$query = "
		SELECT	COUNT(DISTINCT IDMember)
		FROM	`sys_acl_levels_members`
		WHERE	$queryWhereMembership
				(DateExpires IS NULL OR UNIX_TIMESTAMP(DateExpires) > $time) AND
				(DateStarts IS NULL OR UNIX_TIMESTAMP(DateStarts) <= $time)";

	$resProfileMemLevels = db_res($query);

	$membershipProfiles = mysql_fetch_row($resProfileMemLevels);
	$membershipProfiles = (int)$membershipProfiles[0];

	if($membershipID == MEMBERSHIP_ID_STANDARD) $membershipProfiles = $totalProfiles - $membershipProfiles;

	if($except) $membershipProfiles = $totalProfiles - $membershipProfiles;

	return $membershipProfiles;
}*/

/**
 * This is an internal function - do NOT use it outside of membership_levels.inc.php!
 */
function getMemberMembershipInfo_current($memberID, $time = '')
{
    $sCacheName = 'arrMemLevel'.$memberID.$time;
	$memberID = (int)$memberID;
	$time = ($time == '') ? time() : (int)$time;

	//fetch the last purchased/assigned membership
	//that is still active for the given member

    $arrMemLevel =& $GLOBALS['MySQL']->fromMemory($sCacheName, 'getRow', "
		SELECT	`sys_acl_levels_members`.IDLevel as ID,
				`sys_acl_levels`.Name as Name,
				UNIX_TIMESTAMP(`sys_acl_levels_members`.DateStarts) as DateStarts,
				UNIX_TIMESTAMP(`sys_acl_levels_members`.DateExpires) as DateExpires,
                `sys_acl_levels_members`.`TransactionID` AS `TransactionID`
		FROM	`sys_acl_levels_members`
				RIGHT JOIN Profiles
				ON `sys_acl_levels_members`.IDMember = Profiles.ID
					AND	(`sys_acl_levels_members`.DateStarts IS NULL
						OR `sys_acl_levels_members`.DateStarts <= FROM_UNIXTIME($time))
					AND	(`sys_acl_levels_members`.DateExpires IS NULL
						OR `sys_acl_levels_members`.DateExpires > FROM_UNIXTIME($time))
				LEFT JOIN `sys_acl_levels`
				ON `sys_acl_levels_members`.IDLevel = `sys_acl_levels`.ID

		WHERE	Profiles.ID = $memberID

		ORDER BY `sys_acl_levels_members`.DateStarts DESC

		LIMIT 0, 1");

	//no such member found

    if (!$arrMemLevel || !count($arrMemLevel))
    {
		//fetch info about Non-member membership
        $arrMemLevel =& $GLOBALS['MySQL']->fromCache('sys_acl_levels'.MEMBERSHIP_ID_NON_MEMBER, 'getRow', "SELECT ID, Name FROM `sys_acl_levels` WHERE ID = ".MEMBERSHIP_ID_NON_MEMBER);
        if (!$arrMemLevel || !count($arrMemLevel))
		{

			//this should never happen, but just in case
			echo "<br /><b>getMemberMembershipInfo()</b> fatal error: <b>Non-Member</b> membership not found.";
			exit();
		}

		return $arrMemLevel;
	}


	//no purchased/assigned memberships for the member or all of them
	//have expired -- the member is assumed to have Standard membership

	if(is_null($arrMemLevel['ID']))
	{
        $arrMemLevel =& $GLOBALS['MySQL']->fromCache('sys_acl_levels'.MEMBERSHIP_ID_STANDARD, 'getRow', "SELECT ID, Name FROM `sys_acl_levels` WHERE ID = ".MEMBERSHIP_ID_STANDARD);
        if (!$arrMemLevel || !count($arrMemLevel))
		{
			//again, this should never happen, but just in case
			echo "<br /><b>getMemberMembershipInfo()</b> fatal error: <b>Standard</b> membership not found.";
			exit();
		}
	}

	return $arrMemLevel;
}

/**
 * Retrieves information about membership for a given member at a given moment.
 *
 * If there are no memberships purchased/assigned to the
 * given member or all of them have expired at the given point,
 * the member is assumed to be a standard member, and the function
 * returns	information about the Standard membership. This will
 * also happen if a member wasnt actually registered in the database
 * at that point - the function will still return info about Standard
 * membership, not the Non-member one.
 *
 * If there is no profile with the given $memberID,
 * the function returns information about the Non-member
 * predefined membership.
 *
 * The Standard and Non-member memberships have their
 * DateStarts and DateExpires attributes set to NULL.
 *
 * @param int $memberID	- ID of a member to get info about
 * @param int $time		- specifies the time to use when determining membership;
 * 						  if not specified, the function takes the current time
 *
 * @return array(	'ID'			=> membership id,
 * 					'Name'			=> membership name,
 * 					'DateStarts'	=> (UNIX timestamp) date/time purchased,
 * 					'DateExpires'	=> (UNIX timestamp) date/time expires )
 *
 */
function getMemberMembershipInfo($memberID, $time = '')
{
	$time = ($time == '') ? time() : (int)$time;

	$originalMembership = getMemberMembershipInfo_current($memberID, $time);

	if(	$originalMembership['ID'] == MEMBERSHIP_ID_STANDARD ||
		$originalMembership['ID'] == MEMBERSHIP_ID_NON_MEMBER )
	{
		return $originalMembership;
	}

	$arrMembership = $originalMembership;

	do
	{
		$dateStarts = $arrMembership['DateStarts'];
		$arrMembership = getMemberMembershipInfo_current($memberID, ((int)$dateStarts < 1 ? 0 : $dateStarts - 1));
	}
	while($arrMembership['ID'] == $originalMembership['ID'] && (int)$arrMembership['DateStarts']);

	$arrMembership = $originalMembership;

	do
	{
		$dateExpires = $arrMembership['DateExpires'];
		$arrMembership = getMemberMembershipInfo_current($memberID, $dateExpires);
	}
	while($arrMembership['ID'] == $originalMembership['ID'] && (int)$arrMembership['DateExpires']);

	$originalMembership['DateStarts'] = $dateStarts;
	$originalMembership['DateExpires'] = $dateExpires;

	return $originalMembership;
}

/**
 * This is an internal function - do NOT use it outside of membership_levels.inc.php!
 */
function getMemberMembershipInfo_latest($memberID, $time = '')
{
	$time = ($time == '') ? time() : (int)$time;

	$originalMembership = getMemberMembershipInfo_current($memberID, $time);

	if(	$originalMembership['ID'] == MEMBERSHIP_ID_STANDARD ||
		$originalMembership['ID'] == MEMBERSHIP_ID_NON_MEMBER )
	{
		return $originalMembership;
	}

	$arrMembership = $originalMembership;
	$lastMembership = $originalMembership;

	do
	{
		$dateExpires = $arrMembership['DateExpires'];
		$arrMembership = getMemberMembershipInfo_current($memberID, $dateExpires);
		if(!is_null($dateExpires))
			$lastMembership = $arrMembership;
	}
	while($arrMembership['ID'] != MEMBERSHIP_ID_STANDARD && (int)$arrMembership['DateExpires']);

	$arrMembership = $lastMembership;

	do
	{
		$dateStarts = $arrMembership['DateStarts'];
		$arrMembership = getMemberMembershipInfo_current($memberID, ((int)$dateStarts < 1 ? 0 : $dateStarts - 1));
	}
	while($arrMembership['ID'] == $lastMembership['ID'] && (int)$arrMembership['DateStarts']);

	$originalMembership['DateStarts'] = $dateStarts;
	$originalMembership['DateExpires'] = $dateExpires;

	return $originalMembership;
}

/**
 * Checks if a given action is allowed for a given member and updates action information if the
 * action is performed.
 *
 * @param int $memberID			- ID of a member that is going to perform an action
 * @param int $actionID			- ID of the action itself
 * @param boolean $performAction	- if true, then action information is updated, i.e. action
 * 								  is 'performed'
 *
 * @return array(	CHECK_ACTION_RESULT => CHECK_ACTION_RESULT_ constant,
 * 					CHECK_ACTION_MESSAGE => CHECK_ACTION_MESSAGE_ constant,
 * 					CHECK_ACTION_PARAMETER => additional action parameter (string) )
 *
 *
 * NOTES:
 *
 * $result[CHECK_ACTION_MESSAGE] contains a message with detailed information about the result,
 * already processed by the language file
 *
 * if $result[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED then this node contains
 * an empty string
 *
 * The error messages themselves are stored in the language file. Additional variables are
 * passed to the languages.inc.php function _t_ext() as an array and can be used there in the form of
 * {0}, {1}, {2} ...
 *
 * Additional variables passed to the lang. file on errors (can be used in error messages):
 *
 * 	For all errors:
 *
 * 		$arg0[CHECK_ACTION_LANG_FILE_ACTION]	= name of the action
 * 		$arg0[CHECK_ACTION_LANG_FILE_MEMBERSHIP]= name of the current membership
 *
 * 	CHECK_ACTION_RESULT_LIMIT_REACHED:
 *
 * 		$arg0[CHECK_ACTION_LANG_FILE_LIMIT]		= limit on number of actions allowed for the member
 * 		$arg0[CHECK_ACTION_LANG_FILE_PERIOD]	= period that the limit is set for (in hours, 0 if unlimited)
 *
 * 	CHECK_ACTION_RESULT_NOT_ALLOWED_BEFORE:
 *
 * 		$arg0[CHECK_ACTION_LANG_FILE_BEFORE]	= date/time since when the action is allowed
 *
 * 	CHECK_ACTION_RESULT_NOT_ALLOWED_AFTER:
 *
 * 		$arg0[CHECK_ACTION_LANG_FILE_AFTER]		= date/time since when the action is not allowed
 *
 * $result[CHECK_ACTION_PARAMETER] contains an additional parameter that can be considered
 * when performing the action (like the number of profiles to show in search result)
*/
function checkAction($memberID, $actionID, $performAction = false, $iForcedProfID = 0, $isCheckMemberStatus = true)
{
	global $logged;
	global $site;

	//output array initialization

	$result = array();
	$arrLangFileParams = array();

	$dateFormat = "F j, Y, g:i a";	//used when displaying error messages

	//input validation

	$memberID = (int)$memberID;
	$actionID = (int)$actionID;
	$performAction = $performAction ? true : false;

	//get current member's membership information

	$arrMembership = getMemberMembershipInfo($memberID);

	$arrLangFileParams[CHECK_ACTION_LANG_FILE_MEMBERSHIP] = $arrMembership['Name'];
	$arrLangFileParams[CHECK_ACTION_LANG_FILE_SITE_EMAIL] = $site['email'];

	//profile active check

	if($arrMembership['ID'] != MEMBERSHIP_ID_NON_MEMBER || $logged['admin'] || $logged['moderator'])
	{
		$iDestID = $memberID;
        if ( (isAdmin() || isModerator()) && $iForcedProfID>0) {		
			$iDestID = $iForcedProfID;
			$performAction = false;
		}

        if ($isCheckMemberStatus) {
            $active = getProfileInfo( $iDestID );
            if ($active['Status'] != 'Active') {
                $result[CHECK_ACTION_RESULT] = CHECK_ACTION_RESULT_NOT_ACTIVE;
                $result[CHECK_ACTION_MESSAGE] = _t_ext(CHECK_ACTION_MESSAGE_NOT_ACTIVE, $arrLangFileParams);
                return $result;
            }
        }
	}

	//get permissions for the current action

	$resMembershipAction = db_res("
		SELECT	Name,
				IDAction,
				AllowedCount,
				AllowedPeriodLen,
				UNIX_TIMESTAMP(AllowedPeriodStart) as AllowedPeriodStart,
				UNIX_TIMESTAMP(AllowedPeriodEnd) as AllowedPeriodEnd,
				AdditionalParamValue
		FROM	`sys_acl_actions`
				LEFT JOIN `sys_acl_matrix`
				ON	`sys_acl_matrix`.IDAction = `sys_acl_actions`.ID
					AND `sys_acl_matrix`.IDLevel = {$arrMembership['ID']}
		WHERE	`sys_acl_actions`.ID = $actionID");

	//no such action

	if(mysql_num_rows($resMembershipAction) < 1)
	{
		echo "<br /><b>checkAction()</b> fatal error. Unknown action ID: $actionID<br />";
		exit();
	}

	$arrAction = mysql_fetch_assoc($resMembershipAction);

	$result[CHECK_ACTION_PARAMETER]	= $arrAction['AdditionalParamValue'];
	$arrLangFileParams[CHECK_ACTION_LANG_FILE_ACTION] = _t('_'.$arrAction['Name']);

	//action is not allowed for the current membership

	if(is_null($arrAction['IDAction']))
	{
		$result[CHECK_ACTION_RESULT] = CHECK_ACTION_RESULT_NOT_ALLOWED;
		$result[CHECK_ACTION_MESSAGE] = _t_ext(CHECK_ACTION_MESSAGE_NOT_ALLOWED, $arrLangFileParams);
		return $result;
	}

	//Check fixed period limitations if present (also for non-members)

	if($arrAction['AllowedPeriodStart'] && time() < $arrAction['AllowedPeriodStart'])
	{

		$arrLangFileParams[CHECK_ACTION_LANG_FILE_BEFORE] = date($dateFormat, $arrAction['AllowedPeriodStart']);

		$result[CHECK_ACTION_RESULT] = CHECK_ACTION_RESULT_NOT_ALLOWED_BEFORE;
		$result[CHECK_ACTION_MESSAGE] = _t_ext(CHECK_ACTION_MESSAGE_NOT_ALLOWED_BEFORE, $arrLangFileParams);

		return $result;
	}

	if($arrAction['AllowedPeriodEnd'] && time() > $arrAction['AllowedPeriodEnd'])
	{

		$arrLangFileParams[CHECK_ACTION_LANG_FILE_AFTER] = date($dateFormat, $arrAction['AllowedPeriodEnd']);

		$result[CHECK_ACTION_RESULT] = CHECK_ACTION_RESULT_NOT_ALLOWED_AFTER;
		$result[CHECK_ACTION_MESSAGE] = _t_ext(CHECK_ACTION_MESSAGE_NOT_ALLOWED_AFTER, $arrLangFileParams);

		return $result;
	}

	//if non-member, allow action without performing further checks

	if ($arrMembership['ID'] == MEMBERSHIP_ID_NON_MEMBER)
	{
		$result[CHECK_ACTION_RESULT] = CHECK_ACTION_RESULT_ALLOWED;
		return $result;
	}

	//check other limitations (for members only)

	$allowedCnt = (int)$arrAction['AllowedCount'];		//number of allowed actions
														//if not specified or 0, number of
														//actions is unlimited

	$periodLen = (int)$arrAction['AllowedPeriodLen'];	//period for AllowedCount in hours
														//if not specified, AllowedCount is
														//treated as total number of actions
														//permitted

	//number of actions is limited

	if($allowedCnt > 0)
	{
		//get current action info for the member

		$actionTrack = db_res("SELECT ActionsLeft,
									  UNIX_TIMESTAMP(ValidSince) as ValidSince
							   FROM `sys_acl_actions_track`
							   WHERE IDAction = $actionID AND IDMember = $memberID");

		$actionsLeft = $performAction ? $allowedCnt - 1 : $allowedCnt;
		$validSince = time();

		//member is requesting/performing this action for the first time,
		//and there is no corresponding record in sys_acl_actions_track table

		if(mysql_num_rows($actionTrack) <= 0)
		{
			//add action to sys_acl_actions_track table

			db_res("
				INSERT INTO `sys_acl_actions_track` (IDAction, IDMember, ActionsLeft, ValidSince)
				VALUES ($actionID, $memberID, $actionsLeft, FROM_UNIXTIME($validSince))");

			$result[CHECK_ACTION_RESULT] = CHECK_ACTION_RESULT_ALLOWED;
			return $result;
		}

		//action has been requested/performed at least once at this point
		//and there is a corresponding record in sys_acl_actions_track table

		$actionTrack = mysql_fetch_assoc($actionTrack);

		//action record in sys_acl_actions_track table is out of date

		$periodEnd = (int)$actionTrack['ValidSince'] + $periodLen * 3600; //ValidSince is in seconds, PeriodLen is in hours

		if($periodLen > 0 && $periodEnd < time())
		{
			db_res("
				UPDATE	`sys_acl_actions_track`
				SET		ActionsLeft = $actionsLeft, ValidSince = FROM_UNIXTIME($validSince)
				WHERE	IDAction = $actionID AND IDMember = $memberID");

			$result[CHECK_ACTION_RESULT] = CHECK_ACTION_RESULT_ALLOWED;
			return $result;
		}

		//action record is up to date

		$actionsLeft = (int)$actionTrack['ActionsLeft'];

		//action limit reached for now

		if($actionsLeft <= 0 )
		{
			$arrLangFileParams[CHECK_ACTION_LANG_FILE_LIMIT] = $allowedCnt;
			$arrLangFileParams[CHECK_ACTION_LANG_FILE_PERIOD] = $periodLen;

			$result[CHECK_ACTION_RESULT] = CHECK_ACTION_RESULT_LIMIT_REACHED;
			$result[CHECK_ACTION_MESSAGE] = '<div style="width: 80%">' .
				_t_ext(CHECK_ACTION_MESSAGE_LIMIT_REACHED, $arrLangFileParams) .
				($periodLen > 0 ? _t_ext(CHECK_ACTION_MESSAGE_MESSAGE_EVERY_PERIOD, $arrLangFileParams) : '') .
				'.</div>';

			return $result;
		}

		if($performAction)
		{
			$actionsLeft--;

			db_res("
				UPDATE `sys_acl_actions_track`
				SET ActionsLeft = $actionsLeft
				WHERE IDAction = $actionID AND IDMember = $memberID");
		}
	}

	$result[CHECK_ACTION_RESULT] = CHECK_ACTION_RESULT_ALLOWED;
	return $result;
}

/**
 * Buy a membership for a member
 *
 * @param int $memberID			- member that is going to get the membership
 * @param int $membershipID		- bought membership
 * @param int $transactionID	- internal key of the transaction (ID from Transactions table)
 * @param boolean $startsNow	- if true, the membership will start immediately;
 *								  if false, the membership will start after the current
 *								  membership expires
 *
 * @return boolean				- true in case of success, false in case of failure
 *
 *
 */
function buyMembership($memberID, $membershipID, $transactionID, $startsNow=false) {
	//input validation
	$memberID = (int)$memberID;
	$membershipID = (int)$membershipID;
	$price = (float)$price;

	$arrMembership = db_arr("SELECT 
            `tl`.`ID` AS `ID`, 
            `tlp`.`Days` AS `Days`,
            `tl`.`Active` AS `Active`, 
            `tl`.`Purchasable` AS `Purchasable` 
        FROM `sys_acl_levels` AS `tl` 
        LEFT JOIN `sys_acl_level_prices` AS `tlp` ON `tl`.`ID`=`tlp`.`IDLevel` 
        WHERE `tlp`.`id`='" . $membershipID . "'"
    );
	if(!is_array($arrMembership) || empty($arrMembership))
        return false;

	$membershipID = (int)$arrMembership['ID'];

	//check for predefined non-purchasable memberships
	if(in_array($membershipID, array(MEMBERSHIP_ID_NON_MEMBER, MEMBERSHIP_ID_STANDARD, MEMBERSHIP_ID_PROMOTION)))
		return false;

	//check if membership is active and purchasable
	if($arrMembership['Active'] != 'yes' || $arrMembership['Purchasable'] != 'yes') 
        return false;

	return setMembership($memberID, $membershipID, $arrMembership['Days'], $startsNow, $transactionID);
}

/**
 * Set a membership for a member
 *
 * @param int $memberID			- member that is going to get the membership
 * @param int $membershipID		- membership that is going to be assigned to the member
 * 								  if $membershipID == MEMBERSHIP_ID_STANDARD then $days
 *								  and $startsNow parameters are not used, so Standard
 *								  membership is always set immediately and `forever`
 *
 * @param int $days				- number of days to set membership for
 *								  if 0, then the membership is set forever
 *
 * @param boolean $startsNow	- if true, the membership will start immediately;
 *								  if false, the membership will start after the current
 *								  membership expires
 *
 * @return boolean				- true in case of success, false in case of failure
 *
 *
 */
function setMembership($memberID, $membershipID, $days = 0, $startsNow = false, $transactionID = '')
{
	$memberID = (int)$memberID;
	$membershipID = (int)$membershipID;
	$days = (int)$days;
	$startsNow = $startsNow ? true : false;
	$SECONDS_IN_DAY = 86400;
	
	if(!$memberID) {
		$memberID = -1;
	}

	if(empty($transactionID)) 
        $transactionID = 'NULL';

	//check if member exists
	$aProfileInfo = getProfileInfo($memberID);
	if(!$aProfileInfo) return false;

	//check if membership exists
	$res = db_res("SELECT COUNT(ID) FROM `sys_acl_levels` WHERE ID = $membershipID");
	$res = mysql_fetch_row($res);
	if($res[0]!=1) return false;

	if($membershipID == MEMBERSHIP_ID_NON_MEMBER) return false;

	$currentMembership = getMemberMembershipInfo($memberID);
	$latestMembership = getMemberMembershipInfo_latest($memberID);

	if($membershipID == MEMBERSHIP_ID_STANDARD)
	{
		//return if already Standard

		if($currentMembership['ID'] == MEMBERSHIP_ID_STANDARD) return true;

		//delete any present and future memberships

		db_res("
			DELETE	FROM `sys_acl_levels_members`
			WHERE	IDMember = $memberID
					AND	(DateExpires IS NULL OR DateExpires > NOW())");

		if(db_affected_rows() > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	if($days < 0) return false;

	$dateStarts = time();

	if(!$startsNow)
	{
		//make the membership start after the current membership expires

		if(!is_null($latestMembership['DateExpires']))
		{
			$dateStarts = $latestMembership['DateExpires'];

			// if membership already exists then it's unlimited - just shift or delete it
			$res = db_res("
				SELECT	IDMember
				FROM	`sys_acl_levels_members`
				WHERE	IDMember = $memberID
						AND UNIX_TIMESTAMP(DateStarts) = $dateStarts
						AND IDLevel = $membershipID");
			$res = mysql_fetch_row($res);
			if($res[0])
			{
				if($days == 0)
				{
					db_res("DELETE	FROM `sys_acl_levels_members`
							WHERE	IDMember = $memberID
									AND UNIX_TIMESTAMP(DateStarts) = $dateStarts
									AND IDLevel = $membershipID");
				}
				else
				{
					db_res("UPDATE	`sys_acl_levels_members`
							SET		DateStarts = FROM_UNIXTIME(". ((int)$dateStarts + $days * $SECONDS_IN_DAY) .")
							WHERE	IDMember = $memberID
									AND UNIX_TIMESTAMP(DateStarts) = $dateStarts
									AND IDLevel = $membershipID");
				}
			}
		}
	}
	else {
		//delete previous profile's membership level
		db_res("DELETE FROM `sys_acl_levels_members` WHERE `IDMember` = {$memberID}");	
	}

	if($days == 0)
	{
		//if days==0 then set the membership forever
		$dateExpires = 'NULL';
	}
	else
	{
		$dateExpires = (int)$dateStarts + $days * $SECONDS_IN_DAY;
	}

	//insert corresponding record into sys_acl_levels_members
	db_res("
		INSERT `sys_acl_levels_members` (IDMember, IDLevel, DateStarts, DateExpires, TransactionID)
		VALUES ($memberID, $membershipID, FROM_UNIXTIME($dateStarts), FROM_UNIXTIME($dateExpires), '$transactionID')");

	if(db_affected_rows() <= 0) return false;

	//Set Membership Alert
	bx_import('BxDolAlerts');
	$oZ = new BxDolAlerts('profile', 'set_membership', '', $memberID, array('mlevel'=> $membershipID, 'days' => $days, 'starts_now' => $startsNow, 'txn_id' => $transactionID));
	$oZ->alert();

	return true;
}

/**
 * Get the list of existing memberships
 *
 * @param bool $purchasableOnly	- if true, fetches only purchasable memberships;
 * 								  'purchasable' here means that:
 *								  1. MemLevels.Purchasable = 'yes'
 *								  2. MemLevels.Active = 'yes'
 * 								  3. there is at least one pricing option for the membership
 *
 * @return array( membershipID_1 => membershipName_1,  membershipID_2 => membershipName_2, ...) - if no such memberships, then just array()
 *
 *
 */
function getMemberships($purchasableOnly = false)
{
	$result = array();

	$queryPurchasable = '';

	if($purchasableOnly)
	{
		$queryPurchasable = "INNER JOIN `sys_acl_level_prices` ON `sys_acl_level_prices`.IDLevel = `sys_acl_levels`.ID WHERE Purchasable = 'yes' AND Active = 'yes'";
	}

	$resMemLevels = db_res("SELECT DISTINCT `sys_acl_levels`.ID, `sys_acl_levels`.Name FROM `sys_acl_levels` $queryPurchasable");

	while(list($id, $name) = mysql_fetch_row($resMemLevels))
	{
		$result[(int)$id] = $name;
	}

	return $result;
}

/**
 * Get pricing options for the given membership
 *
 * @param int $membershipID	- membership to get prices for
 *
 * @return array( days1 => price1, days2 => price2, ...) - if no prices set, then just array()
 *
 *
 */
function getMembershipPrices($membershipID)
{
	$membershipID = (int)$membershipID;
	$result = array();

	$resMemLevelPrices = db_res("SELECT Days, Price FROM `sys_acl_level_prices` WHERE IDLevel = $membershipID ORDER BY Days ASC");

	while(list($days, $price) = mysql_fetch_row($resMemLevelPrices))
	{
		$result[(int)$days] = (float)$price;
	}

	return $result;
}

/**
 * Get info about a given membership
 *
 * @param int $membershipID	- membership to get info about
 *
 * @return array(	'Name' => name,
 * 					'Active' => active,
 *					'Purchasable' => purchasable,
 *					'Removable' => removable)
 *
 *
 */
function getMembershipInfo($membershipID)
{
	$membershipID = (int)$membershipID;
	$result = array();

	$resMemLevels = db_res("SELECT Name, Active, Purchasable, Removable FROM `sys_acl_levels` WHERE ID = $membershipID");

	if(mysql_num_rows($resMemLevels) > 0)
	{
		$result = mysql_fetch_assoc($resMemLevels);
	}

	return $result;
}


/**
 * Define action, dirine defining all names are transl;ated the following way:
 *  my action => BX_MY_ACTION
 *
 * @param $aActions array of actions from sys_acl_actions table, with default array keys (starting from 0) and text values
 */
function defineMembershipActions ($aActionsAll, $sPrefix = 'BX_')
{
    $aActions = array ();
    foreach ($aActionsAll as $sName)
        if (!defined($sPrefix . strtoupper(str_replace(' ', '_', $sName))))
            $aActions[] = $sName;
    if (!$aActions)
        return;

    $sActions = implode("','", $aActions);
    $res = db_res("SELECT `ID`, `Name` FROM `sys_acl_actions` WHERE `Name` IN('$sActions')");
    while ($r = mysql_fetch_array($res)) {
        define ($sPrefix . strtoupper(str_replace(' ', '_', $r['Name'])), $r['ID']);
    }
}		

?>
