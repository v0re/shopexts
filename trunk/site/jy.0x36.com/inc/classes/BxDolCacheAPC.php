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

class BxDolCacheAPC extends BxDolCache {
   
    var $iTTL = 3600;
 
	/**
	 * constructor
	 */
	function BxDolCacheAPC() {
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

        $isSucess = false;
        $mixedData = apc_fetch ($sKey, $isSucess);
        if (!$isSucess)
            return null;

		return $mixedData;
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

        return apc_store ($sKey, $mixedData, false === $iTTL ? $this->iTTL : $iTTL);
	}

	/**
	 * Delete cache from shared memory
	 *
	 * @param string $sKey - file name
	 * @return result of the operation
	 */
    function delData($sKey) {

        $isSucess = false;
        apc_fetch ($sKey, $isSucess);
        if (!$isSucess)
            return true;

        return apc_delete($sKey);
    }

    /**
     * Check if eAccelerator is available
     * @return boolean
     */
    function isAvailable()
    {
        return extension_loaded('apc');
    }

    /**
     * remove all data from cache by key prefix
     * @return true on success
     */
    function removeAllByPrefix ($s) {        
   
        $l = strlen($s);
        $aKeys = apc_cache_info('user'); 
        if (isset($aKeys['cache_list']) && is_array($aKeys['cache_list'])) {
            foreach ($aKeys['cache_list'] as $r) {
                $sKey = $r['info'];
                if (0 == strncmp($sKey, $s, $l))
                    $this->delData($sKey);
            }
        }
        return true;
    }
}

