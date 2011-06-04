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

require_once('BxDolTemplate.php');

class BxDolModuleTemplate extends BxDolTemplate {
	var $_oDb;
    var $_oConfig; 

	/*
	 * Constructor.
	 */
	function BxDolModuleTemplate(&$oConfig, &$oDb, $sRootPath = BX_DIRECTORY_PATH_ROOT, $sRootUrl = BX_DOL_URL_ROOT) {
		parent::BxDolTemplate($sRootPath, $sRootUrl);

		$this->_oDb = &$oDb;
	    $this->_oConfig = &$oConfig;
	    
	    $sClassPrefix = $oConfig->getClassPrefix();
	    $sHomePath = $oConfig->getHomePath();
	    $sHomeUrl = $oConfig->getHomeUrl();

	    $this->addLocation($sClassPrefix, $sHomePath, $sHomeUrl);
	    $this->addLocationJs($sClassPrefix, $sHomePath . 'js/', $sHomeUrl . 'js/');
	}
	function addAdminCss($mixedFiles, $bDynamic = false) {
		global $oAdmTemplate;
		
		$sLocationKey = $oAdmTemplate->addDynamicLocation($this->_oConfig->getHomePath(), $this->_oConfig->getHomeUrl());
		$mixedResult = $oAdmTemplate->addCss($mixedFiles, $bDynamic);
		$oAdmTemplate->removeLocation($sLocationKey);

		return $mixedResult;
	}
	function addAdminJs($mixedFiles, $bDynamic = false) {
		global $oAdmTemplate;
		
		$sLocationKey = $oAdmTemplate->addDynamicLocationJs($this->_oConfig->getHomePath() . 'js/', $this->_oConfig->getHomeUrl() . 'js/');
		$mixedResult = $oAdmTemplate->addJs($mixedFiles, $bDynamic);
		$oAdmTemplate->removeLocationJs($sLocationKey);

		return $mixedResult;
	}
}
?>