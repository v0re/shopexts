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

bx_import('BxDolDb');


/**
 * Permalinks for any content.
 *
 * An object of the class allows to check whether permalink is enabled 
 * and get it for specified standard URI.
 *
 *
 * Example of usage:
 * 1. Register permalink in database by adding necessary info in the `sys_permalinks` table.
 * 2. Create an object and process the URI
 * $oPermalinks = new BxDolPermalinks();
 * $oPermalinks->permalink('modules/?r=news/');
 *
 *
 * Memberships/ACL:
 * Doesn't depend on user's membership.
 *
 *
 * Alerts:
 * no alerts available
 *
 */
class BxDolPermalinks extends BxDolDb {
    var $sCacheFile;
    var $aLinks;
    
	function BxDolPermalinks() {
	    parent::BxDolDb();
	    

        $oCache = $GLOBALS['MySQL']->getDbCacheObject();
        $this->aLinks = $oCache->getData($GLOBALS['MySQL']->genDbCacheKey('sys_permalinks'));
        if (null === $this->aLinks)
	        if(!$this->cache())
	           $this->aLinks = array();
	}
	
	function cache() {
        $aLinks = $this->getAll("SELECT * FROM `sys_permalinks`");

        $aResult = array();
        foreach($aLinks as $aLink)
           $aResult[$aLink['standard']] = array(
                'permalink' => $aLink['permalink'],
                'check' => $aLink['check'],
                'enabled' => $this->getParam($aLink['check']) == 'on'
            );        
        

        $oCache = $GLOBALS['MySQL']->getDbCacheObject();
        if ($oCache->setData ($GLOBALS['MySQL']->genDbCacheKey('sys_permalinks'), $aResult)) {
            $this->aLinks = $aResult;
            return true;
        }
        
        return false;
	}

	function permalink($sLink) {
	    if(strpos($sLink, 'modules/?r=') === false && strpos($sLink, 'modules/index.php?r=') === false)
	       return $this->_isEnabled($sLink) ? $this->aLinks[$sLink]['permalink'] : $sLink;

	    $aMatch = array();
	    preg_match('/^.*(modules\/(index.php)?\?r=[A-Za-z0-9_-]+\/).*$/', $sLink, $aMatch);

	    if(!isset($aMatch[1]))
	       return $this->_isEnabled($sLink) ? $this->aLinks[$sLink]['permalink'] : $sLink;

	    $sBase = $aMatch[1];
	    if($this->_isEnabled($sBase))
	    	return str_replace($sBase, $this->aLinks[$sBase]['permalink'], $sLink);

	    $sBaseShort = str_replace('index.php', '', $sBase);
	    return $this->_isEnabled($sBaseShort) ? str_replace($sBase, $this->aLinks[$sBaseShort]['permalink'], $sLink) : $sLink;
	}

	function _isEnabled($sLink) {
	    return array_key_exists($sLink, $this->aLinks) && $this->aLinks[$sLink]['enabled'];
	}

    /**
     * redirect to the correct url after switching skin ot language
     * only correct modules urls are supported
     */ 
    function redirectIfNecessary ($aSkip = array()) {
                
        $sCurrentUrl = $_SERVER['PHP_SELF'] . '?' . bx_encode_url_params($_GET, $aSkip);

        if (!preg_match('/modules\/index.php\?r=(\w+)(.*)/', $sCurrentUrl, $m)) 
            return false;        

        $sStandardLink = 'modules/?r=' . $m[1] . '/';
        $sPermalink = $this->permalink ($sStandardLink);

        if (false !== strpos($sCurrentUrl, $sPermalink)) 
            return false;
        
        header("HTTP/1.1 301 Moved Permanently");
        header ('Location:' . BX_DOL_URL_ROOT . $sPermalink . rtrim(trim(urldecode($m[2]), '/'), '&'));

        return true;
    }
}
?>
