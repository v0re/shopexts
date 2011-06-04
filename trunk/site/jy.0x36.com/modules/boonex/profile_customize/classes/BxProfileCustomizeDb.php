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

define('BX_PROFILE_CUSTOM_TABLE_PREFIX', 'bx_profile_custom');

class BxProfileCustomizeDb extends BxDolModuleDb {  
    var $_oConfig;
    /*
     * Constructor.
     */
    function BxProfileCustomizeDb(&$oConfig) {
        parent::BxDolModuleDb();
        
        $this->_oConfig = $oConfig;
    }
    
    function getProfileByUserId($iUserId) 
    {
        return $this->getRow("SELECT * FROM `" . BX_PROFILE_CUSTOM_TABLE_PREFIX . "_main` WHERE `user_id` = $iUserId LIMIT 1");
    }
    
    function getProfileTmpByUserId($iUserId)
    {
        $aStyle = $this->getProfileByUserId($iUserId);
        
        if (!empty($aStyle))
            return unserialize($aStyle['tmp']);
        
        return array();
    }
    
    function getProfileCssByUserId($iUserId)
    {
        $aStyle = $this->getProfileByUserId($iUserId);
        
        if (!empty($aStyle))
            return unserialize($aStyle['css']);
        
        return '';
    }
    
    function updateProfileByUserId($iUserId, $sStyle, $sType)
    {
        // check exist user
        $aRow = $this->getProfileByUserId($iUserId);
        if (empty($aRow))
            return $this->query("INSERT INTO `" . BX_PROFILE_CUSTOM_TABLE_PREFIX . "_main` (`user_id`, `$sType`) VALUES($iUserId, '$sStyle')");
        else
            return $this->query("UPDATE `" . BX_PROFILE_CUSTOM_TABLE_PREFIX . "_main` SET `$sType` = '$sStyle' WHERE `user_id` = $iUserId LIMIT 1");
    }
    
    function saveProfileByUserId($iUserId)
    {
        return $this->query("UPDATE `" . BX_PROFILE_CUSTOM_TABLE_PREFIX . "_main` SET `css` = `tmp` WHERE `user_id` = $iUserId LIMIT 1");
    }
    
    function updateProfileTmpByUserId($iUserId, $aTmp)
    {
        return $this->updateProfileByUserId($iUserId, serialize($aTmp), 'tmp');
    }
    
    function updateProfileCssByUserId($iUserId, $aCss)
    {
        return $this->updateProfileByUserId($iUserId, serialize($aCss), 'css');
    }
    
    function resetProfileStyleByUserId($iUserId)
    {
        return $this->query("DELETE FROM `" . BX_PROFILE_CUSTOM_TABLE_PREFIX . "_main` WHERE `user_id` = $iUserId"); 
    }
    
    function getUnits()
    {
        $aResult = array();
        $aRows = $this->getAll("SELECT `name`, `caption`, `css_name`, `type` FROM `" . BX_PROFILE_CUSTOM_TABLE_PREFIX . "_units`");
        
        foreach ($aRows as $aValue)
        {
            $aResult[$aValue['type']][$aValue['name']] = array(
                'name' => $aValue['caption'],
                'css_name' => $aValue['css_name']
            );
        }

        return $aResult;
    }
    
    function getUnitById($iUnitId)
    {
        return $this->getRow("SELECT * FROM `" . BX_PROFILE_CUSTOM_TABLE_PREFIX . "_units` WHERE `id` = $iUnitId LIMIT 1");
    }
    
    function deleteUnit($iUnitId)
    {
        return $this->query("DELETE FROM `" . BX_PROFILE_CUSTOM_TABLE_PREFIX . "_units` WHERE `id` = $iUnitId");
    }
    
    function getAllThemesByUserId($iUserId)
    {
        return $this->getAll("SELECT * FROM `" . BX_PROFILE_CUSTOM_TABLE_PREFIX . "_themes` WHERE `ownerid` = $iUserId ORDER BY `id`");
    }
    
    function getSharedThemes()
    {
        return $this->getAll("SELECT * FROM `" . BX_PROFILE_CUSTOM_TABLE_PREFIX . "_themes` WHERE `ownerid` = 0 ORDER BY `id`");
    }
    
    function getThemeByName($sName)
    {
        return $this->getRow("SELECT * FROM `" . BX_PROFILE_CUSTOM_TABLE_PREFIX . "_themes` WHERE `name` = '$sName' LIMIT 1");
    }
    
    function getThemeById($iThemeId)
    {
        return $this->getRow("SELECT * FROM `" . BX_PROFILE_CUSTOM_TABLE_PREFIX . "_themes` WHERE `id` = '$iThemeId' LIMIT 1");
    }
    
    function getThemeStyle($iThemeId)
    {
        if ((int)$iThemeId)
        {
            $aTheme = $this->getRow("SELECT * FROM `" . BX_PROFILE_CUSTOM_TABLE_PREFIX . "_themes` WHERE `id` = $iThemeId LIMIT 1");
            
            if (!empty($aTheme))
                return unserialize($aTheme['css']);
        }
        
        return array();
    }
    
    function addTheme($sName, $iOwnerId, $sCss)
    {
        if ($this->query("INSERT INTO `" . BX_PROFILE_CUSTOM_TABLE_PREFIX . 
                "_themes` (`name`, `ownerid`, `css`) VALUES('$sName', $iOwnerId, '$sCss')"))
            return $this->lastId();
            
        return -1;
    }
    
    function deleteTheme($iThemeId)
    {
        return $this->query("DELETE FROM `" . BX_PROFILE_CUSTOM_TABLE_PREFIX . "_themes` WHERE `id` = $iThemeId");
    }
    
    function addImage($sExt)
    {
        if (strlen($sExt) > 0 && $this->query("INSERT INTO `" . BX_PROFILE_CUSTOM_TABLE_PREFIX . "_images` (`ext`, `count`) VALUES('$sExt', 1)"))
            return $this->lastId() . '.' . $sExt;
            
        return '';
    }
    
    function copyImage($sFileName)
    {
        if (strlen($sFileName) > 0)
        {
            $sId = basename($sFileName, '.' . pathinfo($sFileName, PATHINFO_EXTENSION));
            return strlen($sId) > 0 ? $this->query("UPDATE `" . BX_PROFILE_CUSTOM_TABLE_PREFIX . "_images` SET `count` = `count` +  1 WHERE `id` = $sId") : 0;
        }
        
        return 0;
    }
    
    function deleteImage($sFileName)
    {
        $sResult = true;
        
        if (strlen($sFileName) > 0)
        {
            $sId = basename($sFileName, '.' . pathinfo($sFileName, PATHINFO_EXTENSION));
            if (strlen($sId) > 0 && $this->query("UPDATE `" . BX_PROFILE_CUSTOM_TABLE_PREFIX . "_images` SET `count` = `count` -  1 WHERE `id` = $sId"))
            {
                $aRow = $this->getRow("SELECT * FROM `" . BX_PROFILE_CUSTOM_TABLE_PREFIX . "_images` WHERE `id` = $sId LIMIT 1");
                if ($aRow['count'] < 1)
                    $this->query("DELETE FROM `" . BX_PROFILE_CUSTOM_TABLE_PREFIX . "_images` WHERE `id` = $sId");
                else
                    $sResult = false;
            }
        }
        
        return $sResult;
    }
    
    function getSettingsCategory() 
    {
        return $this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Profile Customizer' LIMIT 1");
    }
}
?>