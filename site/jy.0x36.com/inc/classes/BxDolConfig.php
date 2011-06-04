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

require_once(BX_DIRECTORY_PATH_INC . 'utils.inc.php');

bx_import('BxDolMistake');
bx_import('BxDolPermalinks');

/**
 * Base class for Config classes in modules engine.
 * 
 * The object of the class contains different basic configuration settings which are necessary for all modules.
 * 
 * 
 * Example of usage:
 * @see any module included in the default Dolphin's package.
 * 
 * 
 * Static Methods:
 * 
 * Get an instance of a module's class.
 * @see BxDolModule::getInstance($sClassName)
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

class BxDolConfig extends BxDolMistake {
    var $_iId;
    
    var $_sVendor;
    
    var $_sClassPrefix;
    
    var $_sDbPrefix;
    
    var $_sDirectory;
    
    var $_sUri;
    
	var $_sHomePath;
	
	var $_sClassPath;
	
	var $_sHomeUrl;

	/**
	 * constructor
	 */
	function BxDolConfig($aModule) {
	    parent::BxDolMistake();

	    $this->_iId = empty($aModule['id']) ? 0 : (int)$aModule['id'];
	    $this->_sVendor = $aModule['vendor'];
	    $this->_sClassPrefix = $aModule['class_prefix'];	    
	    $this->_sDbPrefix = $aModule['db_prefix'];
	    
	    $this->_sDirectory = $aModule['path'];
	    $this->_sHomePath = BX_DIRECTORY_PATH_MODULES . $this->_sDirectory;
	    $this->_sClassPath = $this->_sHomePath . 'classes/';	    
	    
	    $this->_sUri = $aModule['uri'];
	    $this->_sHomeUrl = BX_DOL_URL_MODULES . $this->_sDirectory;
	}
	function getId() {
	    return $this->_iId;
	}
	function getClassPrefix() {
	    return $this->_sClassPrefix;
	}
	function getDbPrefix() {
	    return $this->_sDbPrefix;
	}
	function getHomePath() {
	    return $this->_sHomePath;
	}
	function getClassPath() {
	    return $this->_sClassPath;
	}
	/**
	 * Get unique URI.
	 *
	 * @return string with unique URI.
	 */
	function getUri() {
	    return $this->_sUri;
	}
	/**
	 * Get base URI which depends on the Permalinks mechanism.
	 * 
	 * example /modules/?r=module_uri or /m/module_uri
	 * @return string with base URI.
	 */
	function getBaseUri() {
	    $oPermalinks = new BxDolPermalinks();
        return $oPermalinks->permalink('modules/?r=' . $this->_sUri . '/');
	}
	/**
	 * Get full URL.
	 *
	 * @return string with full URL.
	 */
	function getHomeUrl() {
	    return $this->_sHomeUrl;
	}
}
?>
