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

require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolMistake.php');
require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolCacheFile.php');

class BxDolParams extends BxDolMistake {
    var $_oDb;
    var $_oCache;
    var $_sCacheFile;
    var $_aParams;

	/**
	 * constructor
	 */
	function BxDolParams($oDb) {
	    parent::BxDolMistake();

        global $site;

	    $this->_oDb = $oDb;
	    $this->_sCacheFile = 'sys_options_' . md5($site['ver'] . $site['build'] . $site['url']) . '.php';

	    $this->_oCache = new BxDolCacheFile(); // feel free to change to another cache system if you are sure that it is available
	    $this->_aParams = $this->_oCache->getData($this->_sCacheFile);

	    if (empty($this->_aParams) && $this->_oDb != null)
	        $this->cache();
	}

    function isInCache($sKey) {
        return isset($this->_aParams[$sKey]);
    }

    function get($sKey, $bFromCache = true) {
        if (!$sKey) 
            return false;
	    if ($bFromCache && $this->isInCache($sKey)) 
	       return $this->_aParams[$sKey];
	    else 
	       return $this->_oDb->getOne("SELECT `VALUE` FROM `sys_options` WHERE `Name`='" . $sKey . "' LIMIT 1");
	}

	function set($sKey, $mixedValue) {	    
	    //--- Update Database ---//
	    $this->_oDb->query("UPDATE `sys_options` SET `VALUE`='" . $mixedValue . "' WHERE `Name`='" . $sKey . "' LIMIT 1");

	    //--- Update Cache ---//
	    $this->cache();
	}

	function cache() {
        $this->_aParams = $this->_oDb->getPairs("SELECT `Name`, `VALUE` FROM `sys_options`", "Name", "VALUE");
        if (empty($this->_aParams)) {
            $this->_aParams = array ();
            return false;
        }

        return $this->_oCache->setData($this->_sCacheFile, $this->_aParams);
	}

    function clearCache() {
        $this->_oCache->delData($this->_sCacheFile);
    }
}