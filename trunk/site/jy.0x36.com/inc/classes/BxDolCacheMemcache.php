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

class BxDolCacheMemcache extends BxDolCache {
   
    var $iTTL = 3600; 
    var $iStoreFlag = 0; 
    var $oMemcache = null;

	/**
	 * constructor
	 */
	function BxDolCacheMemcache() {
	    parent::BxDolCache();
        if (class_exists('Memcache')) {
            $this->oMemcache = new Memcache();
            if (!$this->oMemcache->connect (getParam('sys_cache_memcache_host'), getParam('sys_cache_memcache_port'))) 
                $this->oMemcache = null;
        }
	}
	
	/**
	 * Get data from cache server
	 *
	 * @param string $sKey - file name
     * @param int $iTTL - time to live
	 * @return the data is got from cache.
	 */
	function getData($sKey, $iTTL = false) {
        $mixedData = $this->oMemcache->get($sKey);
		return false === $mixedData ? null : $mixedData;
	}

	/**
	 * Save data in cache server
	 *
	 * @param string $sKey - file name
	 * @param mixed $mixedData - the data to be cached in the file
     * @param int $iTTL - time to live
	 * @return boolean result of operation.
	 */
	function setData($sKey, $mixedData, $iTTL = false) {
        return $this->oMemcache->set($sKey, $mixedData, $this->iStoreFlag, false === $iTTL ? $this->iTTL : $iTTL);
	}

	/**
	 * Delete cache from cache server
	 *
	 * @param string $sKey - file name
	 * @return result of the operation
	 */
    function delData($sKey) {
        $this->oMemcache->delete($sKey);
        return true;
    }

    /**
     * Check if eAccelerator is available
     * @return boolean
     */
    function isAvailable() {
        return $this->oMemcache == null ? false : true;
    }

    /**
     * remove all data from cache by key prefix
     * @return true on success
     */
    function removeAllByPrefix ($s) {        
        // not implemented for current cache
        return false;
    }
}

