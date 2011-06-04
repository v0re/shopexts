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

class BxFilesDb extends BxDolFilesDb {	
	/*
	 * Constructor.
	 */
	function BxFilesDb(&$oConfig) {
		parent::BxDolFilesDb($oConfig);
		
		$this->sFileTable = 'bx_files_main';
        $this->sFavoriteTable = 'bx_files_favorites';
        $this->sMimeTypeTable = 'bx_files_types';
        
        $aAddFields = array(
            'medExt'   => 'Ext',
            'medDesc'  => 'Desc',
            'medSize'  => 'Size',
            'Type'     => 'Type',
            'DownloadsCount' => 'DownloadsCount',
            'AllowDownload'  => 'AllowDownload'
        );
        $this->aFileFields = array_merge($this->aFileFields, $aAddFields);
        
        $this->aFavoriteFields = array(
            'fileId'  => 'ID',
            'ownerId' => 'Profile',
            'favDate' => 'Date'
        );
	}
	
	function getTypeIcon ($sType) {
		$sType = process_db_input($sType, BX_TAGS_STRIP);
		$sqlQuery = "SELECT `Icon` FROM `{$this->sMimeTypeTable}` WHERE `{$this->aFileFields['Type']}`='$sType' LIMIT 1";
		return $this->getOne($sqlQuery);
	}
		
	function getDownloadsCount ($iFile) {
	    $iFile = (int)$iFile;
		return $this->query("SELECT `{$this->aFileFields['DownloadsCount']}` FROM `{$this->sFileTable}` WHERE `{$this->aFileFields['medID']}` = '$iFile'");
	}
	
	function updateDownloadsCount ($sFileUri) {
		$sFileUri = process_db_input($sFileUri, BX_TAGS_STRIP);
	    $this->query("UPDATE `{$this->sFileTable}` SET `{$this->aFileFields['DownloadsCount']}` = `{$this->aFileFields['DownloadsCount']}` + 1 WHERE `{$this->aFileFields['medUri']}`='$sFileUri'");
	}
	
	function insertMimeType ($sMimeType) {
		$sMimeType = process_db_input($sMimeType, BX_TAGS_STRIP);
	    $sqlQuery = "INSERT INTO `{$this->sMimeTypeTable}` SET `Type`='$sMimeType'";
	    $this->res($sqlQuery);
	}
	
	function updateMimeTypePic ($mixedMimeTypes, $sPic) {
	    $mixedMimeTypes = process_db_input($mixedMimeTypes, BX_TAGS_STRIP);
		if (is_array($mixedMimeTypes))
	        $sqlCond = "IN('" . implode("', '", $mixedMimeTypes) . "')";
	    else
	       $sqlCond = "= '$mixedMimeTypes'";
	       
        $sqlQuery = "UPDATE `{$this->sMimeTypeTable}` SET `Icon` = '$sPic' WHERE `Type` $sqlCond";
        $this->res($sqlQuery);
	}
	
	function checkMimeTypeExist ($sMimeType) {
		$sMimeType = process_db_input($sMimeType, BX_TAGS_STRIP);
	    $sqlQuery = "SELECT COUNT(*) FROM `{$this->sMimeTypeTable}` WHERE `Type`='$sMimeType'";
	    return (int)$this->getOne($sqlQuery);
	}
	
    function getSettingsCategory () {
        return (int)$this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Files' LIMIT 1");
    }
}

?>