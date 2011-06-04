<?
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
*     website              : http://www.boonex.com
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the
* License, or  any later version.
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Dolphin,
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

bx_import('BxDolModuleDb');

/*
 * Map module Data
 */
class BxMapDb extends BxDolModuleDb {

	/*
	 * Constructor.
	 */
	function BxMapDb(&$oConfig) {
		parent::BxDolModuleDb();
        $this->_sPrefix = $oConfig->getDbPrefix();
	}

    function updateLocation ($iId, $fLat, $fLng, $iZoom, $iType) {
        return $this->query ("INSERT INTO `" . $this->_sPrefix . "profiles` SET `id` = '$iId', `ts` = UNIX_TIMESTAMP(), `lat` = '$fLat', `lng` = '$fLng', `zoom` = '$iZoom', `type` = '$iType' ON DUPLICATE KEY UPDATE `ts` = UNIX_TIMESTAMP(), `lat` = '$fLat', `lng` = '$fLng', `zoom` = '$iZoom', `type` = '$iType'");
    }

    function deleteLocation ($iId) {
        return $this->query ("DELETE FROM `" . $this->_sPrefix . "profiles` WHERE `id` = '$iId'");
    }

    function insertCountryLocation ($sCountryCode, $fLat, $fLng, $isFailed = 0) {
        return $this->query ("INSERT INTO `" . $this->_sPrefix . "countries` SET `country` = '$sCountryCode', `lat` = '$fLat', `lng` = '$fLng', `failed` = '$isFailed' ON DUPLICATE KEY UPDATE `lat` = '$fLat', `lng` = '$fLng', `failed` = '$isFailed'");
    }

    function insertCityLocation ($sCountryCode, $sCity, $fLat, $fLng, $isFailed = 0) {
        return $this->query ("INSERT INTO `" . $this->_sPrefix . "cities` SET `country` = '$sCountryCode', `city` = '$sCity', `lat` = '$fLat', `lng` = '$fLng', `failed` = '$isFailed' ON DUPLICATE KEY UPDATE `lat` = '$fLat', `lng` = '$fLng', `failed` = '$isFailed'");
    }

    function insertProfileLocation ($iId, $fLat, $fLng, $iMapZoom, $sMapType, $sAddress, $sCountry, $iPrivacy = 0, $isFailed = 0) {
        $sPrivacyUpdate = '';
        $sPrivacyInsert = "`allow_view_location_to` = '" . BX_MAP_DEFAULT_PRIVACY . "',";
        if ($iPrivacy) {
            $sPrivacyInsert = $sPrivacyUpdate = "`allow_view_location_to` = '$iPrivacy',";
        }
        return $this->query ("INSERT INTO `" . $this->_sPrefix . "profiles` SET `id` = '$iId', `lat` = '$fLat', `lng` = '$fLng', `zoom` = '$iMapZoom', `type` = '$sMapType', `address` = '$sAddress', `country`= '$sCountry', $sPrivacyInsert `ts` = UNIX_TIMESTAMP(), `failed` = '$isFailed' ON DUPLICATE KEY UPDATE  `lat` = '$fLat', `lng` = '$fLng', `zoom` = '$iMapZoom', `type` = '$sMapType', `address` = '$sAddress', `country`= '$sCountry', $sPrivacyUpdate `ts` = UNIX_TIMESTAMP(), `failed` = '$isFailed'");
    }

    function getUndefinedCountries ($iLimit) {
        return $this->getPairs ("SELECT `c`.`ISO2`, `c`.`Country` FROM `sys_countries` AS `c` LEFT JOIN `" . $this->_sPrefix . "countries` AS `m` ON (`m`.`country` = `c`.`ISO2`) WHERE ISNULL(`m`.`country`) LIMIT $iLimit", 'ISO2', 'Country');
    }

    function getUndefinedCities ($iLimit) {
        return $this->getPairs ("SELECT `p`.`City`, `p`.`Country` FROM `Profiles` AS `p` LEFT JOIN `" . $this->_sPrefix . "cities` AS `m` ON (`m`.`country` = `p`.`Country` AND `m`.`city` = `p`.`City`) WHERE ISNULL(`m`.`country`) LIMIT $iLimit", 'Country', 'City');
    }    

    function getUndefinedProfiles ($iLimit) {
        return $this->getAllWithKey ("SELECT `p`.* FROM `Profiles` AS `p` LEFT JOIN `" . $this->_sPrefix . "profiles` AS `m` ON (`m`.`id` = `p`.`ID`) WHERE ISNULL(`m`.`id`) LIMIT $iLimit", 'ID');
    }    

    function clearProfiles ($isClearFailedOnly) {
        return $this->_clearTable ($isClearFailedOnly, 'profiles');
    }

    function clearCountries ($isClearFailedOnly) {
        return $this->_clearTable ($isClearFailedOnly, 'countries');
    }

    function clearCities ($isClearFailedOnly) {
        return $this->_clearTable ($isClearFailedOnly, 'cities');
    }

    function _clearTable ($isClearFailedOnly, $sTable) {
        if ($isClearFailedOnly) {
            $ret = $this->query ("DELETE FROM `" . $this->_sPrefix . "$sTable` WHERE `failed` != 0");
            $this->query ("OPTIMIZE TABLE `" . $this->_sPrefix . "$sTable`");
            return $ret;
        } else {
            return $this->query ("TRUNCATE TABLE `" . $this->_sPrefix . "$sTable`");
        }
    }    

    function getProfileById($iProfileId) { 
        return $this->getRow("SELECT `m`.`id`, `p`.`Avatar`, `m`.`lat`, `m`.`lng`, `m`.`zoom`, `m`.`type`, `m`.`address`, `m`.`country`, `m`.`allow_view_location_to` FROM `" . $this->_sPrefix . "profiles` AS `m` INNER JOIN `Profiles` AS `p` ON (`p`.`ID` = `m`.`id`) WHERE `m`.`failed` = 0 AND `p`.`Status` = 'Active' AND `m`.`id` = '$iProfileId' LIMIT 1");
    } 

    function getProfilesByBounds($fLatMin, $fLatMax, $fLngMin, $fLngMax) {
        $sWhere = $this->_getLatLngWhere ($fLatMin, $fLatMax, $fLngMin, $fLngMax);
        return $this->getAll("SELECT `m`.`id`, `p`.`Avatar`, `p`.`NickName`, `m`.`lat`, `m`.`lng` FROM `" . $this->_sPrefix . "profiles` AS `m` INNER JOIN `Profiles` AS `p` ON (`p`.`ID` = `m`.`id`) WHERE `m`.`failed` = 0 AND `p`.`Status` = 'Active' AND `m`.`allow_view_location_to` = '" . BX_DOL_PG_ALL . "' $sWhere LIMIT 250");
    } 

    function getCountryByCode($sCountryCode, $isScrict = true) {

        $sJoin = $isScrict ? 'INNER' : 'LEFT';

        return $this->getRow("SELECT `m`.`country`, `m`.`lat`, `m`.`lng`, COUNT(`p`.`ID`) AS `num`
            FROM `" . $this->_sPrefix . "countries` AS `m` 
            $sJoin JOIN `Profiles` AS `p` ON (`p`.`Country` = `m`.`country` AND `p`.`Status` = 'Active') 
            $sJoin JOIN `" . $this->_sPrefix . "profiles` AS `pm` ON (`pm`.`id` = `p`.`ID` AND `pm`.`failed` = 0 AND `pm`.`allow_view_location_to` = '" . BX_DOL_PG_ALL . "')
            WHERE `m`.`failed` = 0 AND `m`.`country` = '$sCountryCode'
            GROUP BY `p`.`Country`
            LIMIT 1"); 
    } 

    function getCountriesByBounds($fLatMin, $fLatMax, $fLngMin, $fLngMax) {
        $sWhere = $this->_getLatLngWhere ($fLatMin, $fLatMax, $fLngMin, $fLngMax);
        return $this->getAll("SELECT `m`.`country`, `m`.`lat`, `m`.`lng`, COUNT(`p`.`ID`) AS `num`
            FROM `" . $this->_sPrefix . "countries` AS `m` 
            INNER JOIN `Profiles` AS `p` ON (`p`.`Country` = `m`.`country` AND `p`.`Status` = 'Active') 
            INNER JOIN `" . $this->_sPrefix . "profiles` AS `pm` ON (`pm`.`id` = `p`.`ID` AND `pm`.`failed` = 0 AND `pm`.`allow_view_location_to` = '" . BX_DOL_PG_ALL . "')
            WHERE `m`.`failed` = 0 $sWhere 
            GROUP BY `p`.`Country`
            LIMIT 250"); 
    } 

    function getCitiesByBounds($fLatMin, $fLatMax, $fLngMin, $fLngMax) {
        $sWhere = $this->_getLatLngWhere ($fLatMin, $fLatMax, $fLngMin, $fLngMax);
        return $this->getAll("SELECT `m`.`country`, `m`.`city`, `m`.`lat`, `m`.`lng`, COUNT(`p`.`ID`) AS `num`
            FROM `" . $this->_sPrefix . "cities` AS `m`
            INNER JOIN `Profiles` AS `p` ON (`p`.`Country` = `m`.`country` AND `p`.`City` = `m`.`city` AND `p`.`Status` = 'Active')
            INNER JOIN `" . $this->_sPrefix . "profiles` AS `pm` ON (`pm`.`id` = `p`.`ID` AND `pm`.`failed` = 0 AND `pm`.`allow_view_location_to` = '" . BX_DOL_PG_ALL . "')
            WHERE `m`.`failed` = 0 $sWhere 
            GROUP BY `m`.`city`
            LIMIT 250"); 
    } 

    function _getLatLngWhere ($fLatMin, $fLatMax, $fLngMin, $fLngMax) {

        $sWhere = " AND `m`.`lat` < $fLatMax AND `m`.`lat` > $fLatMin ";
			
		if ($fLngMin < 0 && $fLngMax < 0 && $fLngMax < $fLngMin) {
			$f = $fLngMin;
			$fLngMin = $fLngMax;
			$fLngMax = $f;
		}

        if ($fLngMin < $fLngMax)
            $sWhere .= " AND `m`.`lng` < $fLngMax AND `m`.`lng` > $fLngMin ";
        else
            $sWhere .= " AND (`m`.`lng` < $fLngMax OR `m`.`lng` > $fLngMin) ";

        return $sWhere;
    }    

    function isCityLocationExists($sCountryCode, $sCity) {
        return $this->getOne("SELECT `m`.`country` FROM `" . $this->_sPrefix . "cities` AS `m` WHERE `m`.`country` = '$sCountryCode' AND `m`.`city` = '$sCity' AND `m`.`failed` = 0 LIMIT 1") ? true : false;
    }

    function getSettingsCategory($s) {
        return $this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = '$s' LIMIT 1");
    }    
}

?>
