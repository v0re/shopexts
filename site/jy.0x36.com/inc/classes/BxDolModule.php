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

bx_import('BxDolMistake');

/**
 * Base class for Module classes in modules engine.
 * 
 * The object of the class contains major objects of the whole module. They are:
 * a. An object of config class
 * @see BxDolConfig
 * 
 * b. An object of database class.
 * @see BxDolModuleDb
 * 
 * c. An object of template class.
 * @see BxDolTemplate
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
class BxDolModule extends BxDolMistake {
    var $_aModule;
    
	var $_oDb;
	
	var $_oTemplate;
	
	var $_oConfig;

	/**
	 * constructor
	 */
	function BxDolModule($aModule) {
	    parent::BxDolMistake();
	    
	    $this->_aModule = $aModule;
	    
	    $sClassPrefix = $aModule['class_prefix'];
	    $sClassPath = BX_DIRECTORY_PATH_MODULES . $aModule['path'] . 'classes/';	    

	    $sClassName = $sClassPrefix . 'Config';
	    require_once($sClassPath . $sClassName . '.php');
	    $this->_oConfig = new $sClassName($aModule);
	    
	    $sClassName = $sClassPrefix . 'Db';
	    require_once($sClassPath . $sClassName . '.php');
	    $this->_oDb = new $sClassName($this->_oConfig);

	    $sClassName = $sClassPrefix . 'Template';
	    require_once($sClassPath . $sClassName . '.php');
	    $this->_oTemplate = new $sClassName($this->_oConfig, $this->_oDb);
	    $this->_oTemplate->loadTemplates();
	}
	
	/**
	 * Static method to get an instance of a module's class.
	 * 
	 * NOTE. The prefered usage is to get an instance of [ClassPrefix]Module class. 
	 * But if it's needed an instance of class which has constructor without parameters 
	 * or with one parameter(an array with module's info) it can be retrieved.
	 * 
	 * @param $sClassName module's class name.
	 */
	function getInstance($sClassName) {
	    if(empty($sClassName))
            return null;

	    if(isset($GLOBALS['bxDolClasses'][$sClassName]))
           return $GLOBALS['bxDolClasses'][$sClassName];
        else {
            $aModule = db_arr("SELECT * FROM `sys_modules` WHERE INSTR('" . $sClassName . "', `class_prefix`)=1 LIMIT 1");           
            if(empty($aModule) || !is_array($aModule)) return null;

            $sClassPath = BX_DIRECTORY_PATH_MODULES . $aModule['path'] . '/classes/' . $sClassName . '.php';            
            if(!file_exists($sClassPath)) return null;
            
            require_once($sClassPath);            
            $GLOBALS['bxDolClasses'][$sClassName] = new $sClassName($aModule);
            return $GLOBALS['bxDolClasses'][$sClassName];
        }
	}
	/**
	 * Check whether user logged in or not.
	 * 
	 * @return boolean result of operation.
	 */
	function isLogged() {
        return isLogged();
	}
	/**
	 * Get currently logged in user ID.
	 *
	 * @return integer user ID.
	 */
	function getUserId() {
        return getLoggedId();
	}
	/**
	 * Get currently logged in user password.
	 *
	 * @return string user password.
	 */
	function getUserPassword () {
		return getLoggedPassword();
	}
	
	function getTitle($sUri) {
		return _t(BxDolModule::getTitleKey($sUri));
	}
	function getTitleKey($sUri) {
		return '_sys_module_' . strtolower(str_replace(' ', '_', $sUri));
	}
}
?>