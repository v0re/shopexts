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

require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolFilesDb.php');

class BxPhotosDb extends BxDolFilesDb {	
	/*
	 * Constructor.
	 */
	function BxPhotosDb (&$oConfig) {
		parent::BxDolFilesDb($oConfig);
		$this->sFileTable = 'bx_photos_main';
		$this->sFavoriteTable = 'bx_photos_favorites';
		$this->aFileFields['medDesc'] = 'Desc';
		$this->aFileFields['medExt']  = 'Ext';
		$this->aFileFields['medSize'] = 'Size';
		$this->aFileFields['Hash'] = 'Hash';
	}
	
	function getSettingsCategory () {
        return (int)$this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Photos' LIMIT 1");
    }
    
    function getIdByHash ($sHash) {
    	$sHash = process_db_input($sHash, BX_TAGS_STRIP);
    	return (int)$this->fromMemory('bx_photos_' . $sHash, 'getOne', "
    	SELECT `{$this->aFileFields['medID']}`
    	FROM `{$this->sFileTable}`
    	WHERE `{$this->aFileFields['Hash']}` = '$sHash'");
    }
}

?>