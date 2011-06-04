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

require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolModuleDb.php' );

define('BX_SITES_TABLE_PREFIX', 'bx_sites');

class BxSitesDb extends BxDolModuleDb {  
    var $_oConfig;
    /*
     * Constructor.
     */
    function BxSitesDb(&$oConfig) {
        parent::BxDolModuleDb();
        
        $this->_oConfig = $oConfig;
    }
    
    function getMembershipActions() 
    {
        $sSql = "SELECT `ID` AS `id`, `Name` AS `name` FROM `sys_acl_actions` WHERE `Name`='use sites'";
        return $this->getAll($sSql);
    }
    
    function getSiteById($iSiteId) 
    {
        return $this->getRow ("SELECT * FROM `" . BX_SITES_TABLE_PREFIX . "_main` WHERE `id` = $iSiteId LIMIT 1");
    }
    
    function getSiteByEntryUri($sEntryUri) 
    {
        return $this->getRow ("SELECT * FROM `" . BX_SITES_TABLE_PREFIX . "_main` WHERE `entryUri` = '$sEntryUri' LIMIT 1");
    }
    
    function getSiteLatest()
    {
        return $this->getRow ("SELECT * FROM `" . BX_SITES_TABLE_PREFIX . "_main` ORDER BY `date` DESC LIMIT 1");
    }
    
    function getSiteByUrl($sUrl)
    {
        return $this->getRow ("SELECT * FROM `" . BX_SITES_TABLE_PREFIX . "_main` WHERE `url` = '$sUrl' LIMIT 1");
    }
    
    function getSites() 
    {
        return $this->getAll("SELECT * FROM `" . BX_SITES_TABLE_PREFIX . "_main`");
    }
    
    function getSitesByAuthor($iProfileId)
    {
        return $this->getAll("SELECT * FROM `" . BX_SITES_TABLE_PREFIX . "_main` WHERE `ownerid` = $iProfileId");
    }
    
    function markFeatured($iSiteId) 
    {
        return $this->query ("UPDATE `" . BX_SITES_TABLE_PREFIX . "_main` SET `featured` = (`featured` - 1)*(`featured` - 1) WHERE `id` = $iSiteId LIMIT 1");
    }
    
    function deleteSiteById($iSiteId) 
    {
        return $this->query("DELETE FROM `" . BX_SITES_TABLE_PREFIX . "_main` WHERE `id` = $iSiteId");
    }
    
    function getProfileIdByNickName($sNick)
    {
        return $this->getOne ("SELECT `ID` FROM `Profiles` WHERE `NickName` = '$sNick' LIMIT 1");
    }
    
    function getSitesByMonth($iYear, $iMonth, $iNextYear, $iNextMonth) 
    {
        return $this->getAll("SELECT *, DAYOFMONTH(FROM_UNIXTIME(`date`)) AS `Day`
            FROM `" . BX_SITES_TABLE_PREFIX . "_main`
            WHERE `date` >= UNIX_TIMESTAMP('$iYear-$iMonth-1') AND `date` < UNIX_TIMESTAMP('$iNextYear-$iNextMonth-1') AND `status` = 'approved'");
    }
    
    function getSettingsCategory() {
        return $this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Sites' LIMIT 1");
    }
    
    function setStatusSite($iSiteId, $sStatus)
    {
        $this->query("UPDATE `" . BX_SITES_TABLE_PREFIX . "_main` SET `status` = '$sStatus' WHERE `id` = $iSiteId");
    }
    
    function getCountByOwnerAndStatus($iOwnerId, $sStatus)
    {
        return $this->getOne ("SELECT count(*) FROM `" . BX_SITES_TABLE_PREFIX . "_main` WHERE `status` = '$sStatus' AND `ownerid` = $iOwnerId");
    }
}
?>