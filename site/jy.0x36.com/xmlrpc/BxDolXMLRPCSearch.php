<?php

class BxDolXMLRPCSearch
{
    function getSearchResultsLocation($sUser, $sPwd, $sLang, $sCountryCode, $sCity, $isOnlineOnly, $isWithPhotosOnly, $iStart, $iPP)
    {
        if (!($iId = BxDolXMLRPCUtil::checkLogin ($sUser, $sPwd)))
            return new xmlrpcresp(new xmlrpcval(array('error' => new xmlrpcval(1,"int")), "struct"));

        BxDolXMLRPCUtil::setLanguage ($sLang); 

        $sCountryCode = process_db_input ($sCountryCode, BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION);
        $sCity = process_db_input ($sCity, BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION);

        $sWhere = '';
        if ($sCountryCode)
            $sWhere .= " AND `Country`= '$sCountryCode' ";
        if ($sCity)
            $sWhere .= " AND `City`LIKE '$sCity' ";
        if ($isWithPhotosOnly)
            $sWhere .= " AND `Avatar` ";
        if ($isOnlineOnly) 
        {
            $iOnlineTime = (int)getParam( 'member_online_time' );
            $sWhere .= " AND `DateLastNav` >= DATE_SUB(NOW(), INTERVAL $iOnlineTime MINUTE)";
        }
        $iStart = (int)$iStart;
        if (!$iStart || $iStart < 0)
            $iStart = 0;
        $iPP = (int)$iPP;
        if (!$iPP || $iPP < 1)
            $iPP = 1;

        $r = db_res ("
            SELECT * FROM `Profiles`
            WHERE 1 $sWhere
            ORDER BY `DateLastNav` DESC
            LIMIT $iStart, $iPP");

        while ($aRow = mysql_fetch_array ($r))
            $aProfiles[] = new xmlrpcval(BxDolXMLRPCUtil::fillProfileArray($aRow, 'thumb'), 'struct');

        return new xmlrpcval ($aProfiles, "array");
    }

    function getSearchResultsNearMe($sUser, $sPwd, $sLang, $sLat, $sLng, $isOnlineOnly, $isWithPhotosOnly, $iStart, $iPP)
    {
        if (!($iId = BxDolXMLRPCUtil::checkLogin ($sUser, $sPwd)))
            return new xmlrpcresp(new xmlrpcval(array('error' => new xmlrpcval(1,"int")), "struct"));

        BxDolXMLRPCUtil::setLanguage ($sLang); 

        $sLat = (float)$sLat;
        $sLng = (float)$sLng;

        $sWhere = '';
        $sJoin = '';
        $sLocation = '';
        if ($sLat && $sLng)
        {
            $sDistance = ", (POW($sLat-`loc`.`lat`, 2)+POW($sLng-`loc`.`lng`, 2)) AS `distance`";
            $sJoin .= " INNER JOIN `bx_map_profiles` AS `loc` ON  (`loc`.`id` = `Profiles`.`ID` AND `loc`.`failed` = 0) ";
        }
        if ($isWithPhotosOnly)
            $sWhere .= " AND `Avatar` ";
        if ($isOnlineOnly) 
        {
            $iOnlineTime = getParam( 'member_online_time' );
            $sWhere .= " AND `DateLastNav` >= DATE_SUB(NOW(), INTERVAL $iOnlineTime MINUTE)";
        }
        $iStart = (int)$iStart;
        if (!$iStart || $iStart < 0)
            $iStart = 0;
        $iPP = (int)$iPP;
        if (!$iPP || $iPP < 1)
            $iPP = 1;

        $r = db_res ("
            SELECT * " . $sDistance  . " FROM `Profiles`
            $sJoin
            WHERE 1 $sWhere
            ORDER BY `distance` ASC
            LIMIT $iStart, $iPP");

        while ($aRow = mysql_fetch_array ($r))
            $aProfiles[] = new xmlrpcval(BxDolXMLRPCUtil::fillProfileArray($aRow, 'thumb'), 'struct');

        return new xmlrpcval ($aProfiles, "array");
    }

    function getSearchResultsKeyword($sUser, $sPwd, $sLang, $sKeyword, $isOnlineOnly, $isWithPhotosOnly, $iStart, $iPP)
    {
        if (!($iId = BxDolXMLRPCUtil::checkLogin ($sUser, $sPwd)))
            return new xmlrpcresp(new xmlrpcval(array('error' => new xmlrpcval(1,"int")), "struct"));

        BxDolXMLRPCUtil::setLanguage ($sLang); 

        $sKeyword = process_db_input ($sKeyword, BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION);

        $sMatch = '';
        if ($sKeyword && strlen($sKeyword) > 2)
        {
            $sMatch .= " MATCH (`NickName`, `City`, `Headline`, `DescriptionMe`, `Tags`) AGAINST ('$sKeyword') ";
            $sWhere .= " AND $sMatch  ";
        }
        if ($isWithPhotosOnly)
            $sWhere .= " AND `Avatar` ";
        if ($isOnlineOnly) 
        {
            $iOnlineTime = getParam( 'member_online_time' );
            $sWhere .= " AND `DateLastNav` >= DATE_SUB(NOW(), INTERVAL $iOnlineTime MINUTE)";
        }
        $iStart = (int)$iStart;
        if (!$iStart || $iStart < 0)
            $iStart = 0;
        $iPP = (int)$iPP;
        if (!$iPP || $iPP < 1)
            $iPP = 1;

        $r = db_res ("
            SELECT * " . ( $sMatch ? ", $sMatch" : '') . " FROM `Profiles`
            WHERE 1 $sWhere
            ORDER BY `DateLastNav` DESC
            LIMIT $iStart, $iPP");

        while ($aRow = mysql_fetch_array ($r))
            $aProfiles[] = new xmlrpcval(BxDolXMLRPCUtil::fillProfileArray($aRow, 'thumb'), 'struct');

        return new xmlrpcval ($aProfiles, "array");
    }
}

?>
