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

require_once(BX_DIRECTORY_PATH_CLASSES . "BxDolInstaller.php");

class BxDskInstaller extends BxDolInstaller {
    var $sGetDesktopUrl = "http://air.boonex.com/desktop/";
    var $sDesktopFile = "file/desktop.air";
	
	function BxDskInstaller($aConfig) {
        parent::BxDolInstaller($aConfig);
		$this->_aActions['get_desktop'] = array('title' => 'Getting Desktop downloadable from boonex.com:');
		$this->_aActions['remove_desktop'] = array('title' => 'Removing Desktop downloadable:');
    }
	
	function actionGetDesktop($bInstall = true) {
		global $sHomeUrl;
		
		$sTempFile = BX_DIRECTORY_PATH_MODULES . $this->_aConfig['home_dir'] . $this->sDesktopFile;
		
		$sData = $this->readUrl($this->sGetDesktopUrl . "index.php", array('url' => $sHomeUrl . 'XML.php'));
		if(empty($sData)) return BX_DOL_INSTALLER_FAILED;
		
		$fp = @fopen($sTempFile, "w");
		@fwrite($fp, $this->readUrl($this->sGetDesktopUrl . $sData));
		@fclose($fp);

		$this->readUrl($this->sGetDesktopUrl . "index.php", array("delete" => $sData));
	
		if(!file_exists($sTempFile) || filesize($sTempFile) == 0) return BX_DOL_INSTALLER_FAILED;
        return BX_DOL_INSTALLER_SUCCESS;
	}
	
	function actionRemoveDesktop($bInstall = true) {
		@unlink(BX_DIRECTORY_PATH_MODULES . $this->_aConfig['home_dir'] . $this->sDesktopFile);
        return BX_DOL_INSTALLER_SUCCESS;
	}
	
	function readUrl($sUrl, $aParams = array())
	{
		return bx_file_get_contents($sUrl, $aParams);
	}
}
?>