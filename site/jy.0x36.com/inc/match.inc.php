<?php

require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolProfileFields.php' );
require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolEmailTemplates.php' );

function getMatchFields()
{
    $oDb = new BxDolDb();
    return $oDb->fromCache('sys_profile_match_fields', 'getAllWithKey', 
        'SELECT `ID`, `Name`, `MatchField`, `MatchPercent` FROM `sys_profile_fields` WHERE `MatchPercent` > 0', 'ID');
}

function getMatchProfiles($iProfileId, $bForce = false, $sSort = 'none')
{
    $aResult = array();

    if (!getParam('enable_match'))
        return $aResult;
    
    $oDb = new BxDolDb();
    
    if (!(int)$iProfileId)
        return $aResult;
        
    if (!$bForce)
    {
        $aMatch = $oDb->getRow("SELECT `profiles_match` FROM `sys_profiles_match` WHERE `profile_id` = $iProfileId AND `sort` = '$sSort'");
        if (!empty($aMatch))
            return unserialize($aMatch['profiles_match']);
    }
    else
        $oDb->query("DELETE FROM `sys_profiles_match` WHERE `profile_id` = $iProfileId");
        
    $aProf = getProfileInfo($iProfileId);
    
    if (empty($aProf))
        return $aResult;
        
    $aMathFields = getMatchFields();
    $iAge = (int)$oDb->getOne("SELECT DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), '{$aProf['DateOfBirth']}')), '%Y') + 0 AS age");
    
    foreach ($aMathFields as $sKey => $aFields)
    {
        $aMathFields[$sKey]['profiles'] = array();
        
        if ($aProf[$aFields['Name']])
        {
            if ($aMathFields[$aFields['MatchField']]['Name'] == 'DateOfBirth')
            {
                if ($iAge)
                    $sCond = "(DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), `DateOfBirth`)), '%Y') + 0) = $iAge";
            }
            else if($aMathFields[$aFields['MatchField']]['Name']) {
                $sCond = "`{$aMathFields[$aFields['MatchField']]['Name']}` = '" . 
                    process_db_input($aProf[$aFields['Name']], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION) . "'";

            	$aMathFields[$sKey]['profiles'] = $oDb->getAllWithKey("SELECT `ID` FROM `Profiles` WHERE `Status` = 'Active' AND `ID` != $iProfileId AND $sCond", 'ID');
            }
		}
    }
    
    $sCondSort = '';
    if ($sSort == 'activity')
        $sCondSort = 'ORDER BY `DateLastNav` DESC';
    else if ($sSort == 'date_reg')
        $sCondSort = 'ORDER BY `DateReg` DESC';

	$iPercentThreshold = getParam('match_percent');
    $aProfiles = $oDb->getColumn("SELECT `ID` FROM `Profiles` WHERE `Status` = 'Active' AND `ID` != $iProfileId $sCondSort");
    foreach ($aProfiles as $iProfId)
    {
        $iPercent = 0;
        
        foreach ($aMathFields as $sKey => $aFields)
        {
            if (isset($aFields['profiles'][$iProfId])) 
                $iPercent += (int)$aFields['MatchPercent'];
        }
        
        if ($iPercent >= $iPercentThreshold)
            $aResult[] = $iProfId;
    }
    
    $oDb->query("INSERT INTO `sys_profiles_match`(`profile_id`, `sort`, `profiles_match`) VALUES($iProfileId, '$sSort', '" . 
        serialize($aResult) . "')");
    
    return $aResult;
}

function getProfilesMatch( $iPID1 = 0, $iPID2 = 0 ) 
{
    $iPID1 = (int)$iPID1;
	$iPID2 = (int)$iPID2;
	
	if( !$iPID1 or !$iPID2 )
		return 0;

	if( $iPID1 == $iPID2 )
		return 0;
		
	$aProf1 = getProfileInfo($iPID1);
	$aProf2 = getProfileInfo($iPID2);

	if(empty($aProf1) || empty($aProf2))
		return 0;
		
	$iMatch = 0;
	$aMathFields = getMatchFields();
	
	foreach ($aMathFields as $sKey => $aFields)
	{
	    $bRes = false;
	    
	    if ($aProf1[$aFields['Name']])
	    {
    	    if ($aMathFields[$aFields['MatchField']]['Name'] == 'DateOfBirth')
    	        $bRes = age($aProf1['DateOfBirth']) == age($aProf2['DateOfBirth']);
    	    else
               $bRes = $aProf1[$aFields['Name']] == $aProf2[$aMathFields[$aFields['MatchField']]['Name']];
	    }
	    
	    if ($bRes)
	       $iMatch += (int)$aFields['MatchPercent'];
	}

	return $iMatch;
}

?>
