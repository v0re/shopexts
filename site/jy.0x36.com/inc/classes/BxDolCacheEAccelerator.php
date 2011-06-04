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

bx_import('BxDolCache');

class BxDolCacheEAccelerator extends BxDolCache {
   
    var $iTTL = 3600;
 
	/**
	 * constructor
	 */
	function BxDolCacheEAccelerator() {
	    parent::BxDolCache();
	}
	
	/**
	 * Get data from shared memory cache
	 *
	 * @param string $sKey - file name
     * @param int $iTTL - time to live
	 * @return the data is got from cache.
	 */
	function getData($sKey, $iTTL = false) {
        $sData = eaccelerator_get($sKey);
		return null === $sData ? null : unserialize($sData);
	}
	/**
	 * Save data in shared memory cache
	 *
	 * @param string $sKey - file name
	 * @param mixed $mixedData - the data to be cached in the file
     * @param int $iTTL - time to live
	 * @return boolean result of operation.
	 */
	function setData($sKey, $mixedData, $iTTL = false) {
        $bResult = eaccelerator_put($sKey, serialize($mixedData), false === $iTTL ? $this->iTTL : $iTTL);
        return $bResult;
	}

	/**
	 * Delete cache from shared memory
	 *
	 * @param string $sKey - file name
	 * @return result of the operation
	 */
    function delData($sKey) {

        eaccelerator_lock($sKey);
        
        eaccelerator_rm($sKey);
        
        eaccelerator_unlock($sKey);
        
        return true;
    }

    /**
     * Check if eAccelerator is available
     * @return boolean
     */
    function isAvailable()
    {
        return function_exists('eaccelerator_put');
    }

    /**
     * remove all data from cache by key prefix
     * @return true on success
     */
    function removeAllByPrefix ($s) {        
   
        $l = strlen($s);
        $aKeys = eaccelerator_list_keys(); 
        foreach ($aKeys as $aKey) {
            $sKey = 0 === strpos($aKey['name'], ':') ? substr($aKey['name'], 1) : $aKey['name'];
            if (0 == strncmp($sKey, $s, $l))
                $this->delData($sKey);
        } 
        
        return true;
    }
}

