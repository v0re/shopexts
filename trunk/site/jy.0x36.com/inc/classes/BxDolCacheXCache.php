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

class BxDolCacheXCache extends BxDolCache {
   
    var $iTTL = 3600;
 
	/**
	 * constructor
	 */
	function BxDolCacheXCache() {
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

		if (!xcache_isset($sKey))
			return null;

		return xcache_get($sKey);
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

        $bResult = xcache_set($sKey, $mixedData, false === $iTTL ? $this->iTTL : $iTTL);
        return $bResult;
	}

	/**
	 * Delete cache from shared memory
	 *
	 * @param string $sKey - file name
	 * @return result of the operation
	 */
    function delData($sKey) {

		if (!xcache_isset($sKey))
			return true;

		return xcache_unset($sKey);
    }

    /**
     * Check if eAccelerator is available
     * @return boolean
     */
    function isAvailable() {

        return extension_loaded('xcache');
    }

    /**
     * remove all data from cache by key prefix
     * @return true on success
     */
    function removeAllByPrefix ($s) {        

        return xcache_unset_by_prefix ($s);
    }
}

