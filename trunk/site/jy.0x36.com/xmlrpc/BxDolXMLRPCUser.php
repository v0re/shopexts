<?php

class BxDolXMLRPCUser
{
    function login($sUser, $sPwd)
    {
        $iId = BxDolXMLRPCUtil::checkLogin ($sUser, $sPwd);
        return new xmlrpcresp(new xmlrpcval($iId, "int"));
    }
    
    function updateUserLocation ($sUser, $sPwd, $sLat, $sLng, $sZoom, $sMapType) 
    {	
        if (!($iId = BxDolXMLRPCUtil::checkLogin ($sUser, $sPwd)) || !preg_match('/^[A-Za-z0-9]*$/', $sMapType))
            return new xmlrpcresp(new xmlrpcval(array('error' => new xmlrpcval(1,"int")), "struct"));

        $iRet = BxDolService::call('map_profiles', 'update_profile_location_manually', array ($iId, (float)$sLat, (float)$sLng, (int)$sZoom, $sMapType, '', '')) ? '1' : '0';

        return new xmlrpcresp(new xmlrpcval($iRet));
    }

    function getUserLocation ($sUser, $sPwd, $sNick) 
    {
        if (!($iId = BxDolXMLRPCUtil::checkLogin ($sUser, $sPwd)))
            return new xmlrpcresp(new xmlrpcval(array('error' => new xmlrpcval(1,"int")), "struct"));
       
        $iProfileId = getID($sNick, false);
        $aLocation = BxDolService::call('map_profiles', 'get_location', array($iProfileId, $iId, true));
        if (-1 == $aLocation) // access denied
            return new xmlrpcval("-1");
        if (!is_array($aLocation)) // location is undefined
            return new xmlrpcval("0");

        return new xmlrpcval(array(
            'lat' => new xmlrpcval($aLocation['lat']),
            'lng' => new xmlrpcval($aLocation['lng']),
            'zoom' => new xmlrpcval($aLocation['zoom']),
            'type' => new xmlrpcval($aLocation['type']),
            'address' => new xmlrpcval($aLocation['address']),
            'country' => new xmlrpcval($aLocation['country']),
        ), 'struct');
    }

    function getHomepageInfo($sUser, $sPwd)
    {
        if (!($iId = BxDolXMLRPCUtil::checkLogin ($sUser, $sPwd)))
            return new xmlrpcresp(new xmlrpcval(array('error' => new xmlrpcval(1,"int")), "struct"));

        $aRet = BxDolXMLRPCUtil::getUserInfo($iId);

        $aRet['unreadLetters'] = new xmlrpcval(getNewLettersNum($iId));
        $aFriendReq =  db_arr( "SELECT count(*) AS `num` FROM `sys_friend_list` WHERE `Profile` = {$iId} AND  `Check` = '0'" );
        $aRet['friendRequests'] = new xmlrpcval($aFriendReq['num']);

        return new xmlrpcval ($aRet, "struct");
    }

    function getUserInfo($sUser, $sPwd, $sNick, $sLang)
    {
        $iIdProfile = BxDolXMLRPCUtil::getIdByNickname ($sNick);
        if (!$iIdProfile || !($iId = BxDolXMLRPCUtil::checkLogin ($sUser, $sPwd)))
            return new xmlrpcresp(new xmlrpcval(array('error' => new xmlrpcval(1,"int")), "struct"));

        BxDolXMLRPCUtil::setLanguage ($sLang); 
		
		if ($iIdProfile != $iId)
		{
			// privacy
			bx_import('BxDolPrivacy');
			$oPrivacy = new BxDolPrivacy('Profiles', 'ID', 'ID');
			if ($iIdProfile != $iId && !$oPrivacy->check('view', $iIdProfile, $iId))
				return new xmlrpcval ('-1');

			// membership
			$aCheckRes = checkAction($iId, ACTION_ID_VIEW_PROFILES, true, $iIdProfile);
			if ($aCheckRes[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED)
				return new xmlrpcval ($aCheckRes[CHECK_ACTION_MESSAGE]);
		}

        $aRet = BxDolXMLRPCUtil::getUserInfo($iIdProfile);
        return new xmlrpcval ($aRet, "struct");
    }

    function getUserInfoExtra($sUser, $sPwd, $sNick, $sLang)
    {
        $iIdProfile = BxDolXMLRPCUtil::getIdByNickname ($sNick);
        if (!$iIdProfile || !($iId = BxDolXMLRPCUtil::checkLogin ($sUser, $sPwd)))
            return new xmlrpcresp(new xmlrpcval(array('error' => new xmlrpcval(1,"int")), "struct"));

        BxDolXMLRPCUtil::setLanguage ($sLang); 

        $o = new BxDolXMLRPCProfileView ($iIdProfile);
        return $o->getProfileInfoExtra();
    }

    function updateStatusMessage ($sUser, $sPwd, $sStatusMsg) 
    {
        if (!($iId = BxDolXMLRPCUtil::checkLogin ($sUser, $sPwd)))
            return new xmlrpcresp(new xmlrpcval(array('error' => new xmlrpcval(1,"int")), "struct"));

        ob_start();
        $_GET['action'] = '1';
        require_once( BX_DIRECTORY_PATH_ROOT . 'list_pop.php' );
        ob_end_clean();

        $_POST['status_message'] = $sStatusMsg;
        ActionChangeStatusMessage ($iId);

        return new xmlrpcresp(new xmlrpcval($iRet, "int"));
    }

}

?>
