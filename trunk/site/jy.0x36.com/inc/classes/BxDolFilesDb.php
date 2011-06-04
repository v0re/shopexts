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

require_once('BxDolModuleDb.php');

class BxDolFilesDb extends BxDolModuleDb {	
	var $_oConfig;
    
    var $iViever;
    // main files table
    var $sFileTable;
    // table of favorites
    var $sFavoriteTable;
    
    // array of files table's fields
    var $aFileFields;
    // array of favorite files table's fields
    var $aFavoriteFields;
    
	/*
	 * Constructor.
	 */
	function BxDolFilesDb (&$oConfig) {
		parent::BxDolModuleDb($oConfig);
		$this->_oConfig = &$oConfig;
		$this->iViewer = getLoggedId();
		$this->aFileFields = array(
            'medID'    => 'ID',
            'Categories'=> 'Categories',
            'medProfId'=> 'Owner',
            'medTitle' => 'Title',
            'medUri'   => 'Uri',
            'medDesc'  => 'Description',
            'medTags'  => 'Tags',
            'medDate'  => 'Date',
            'medViews' => 'Views',
            'Approved' => 'Status',
            'Featured' => 'Featured',
            'Rate' => 'Rate',
            'RateCount' => 'RateCount',
        );
        
        $this->aFavoriteFields = array(
            'fileId' => 'ID',
            'ownerId' => 'Profile',
            'favDate' => 'Date'
        );
	}
	
    function _changeFileCondition ($iFile, $sField, $sValue) {
        $iFile = (int)$iFile;
        $sqlQuery = "UPDATE `{$this->sFileTable}` SET `$sField` = '$sValue' WHERE `{$this->aFileFields['medID']}`='$iFile'";
        return $this->query($sqlQuery);
    }
	
	function approveFile ($iFile) {
	    return $this->_changeFileCondition($iFile, $this->aFileFields['Approved'], 'approved');
	}
	
	function disapproveFile ($iFile) {
	    return $this->_changeFileCondition($iFile, $this->aFileFields['Approved'], 'disapproved');
	}
	
	function makeFeatured ($iFile) {
	    return $this->_changeFileCondition($iFile, $this->aFileFields['Featured'], '1');
	}
	
    function makeUnFeatured ($iFile) {
        return $this->_changeFileCondition($iFile, $this->aFileFields['Featured'], '0');
    }
	
	function addToFavorites ($iFile) {
	    $iFile = (int)$iFile;
	    $bRes = false;
	    if ($iFile > 0) {
            $sqlQuery = "INSERT INTO `{$this->sFavoriteTable}` (`{$this->aFavoriteFields['fileId']}`, `{$this->aFavoriteFields['ownerId']}`, `{$this->aFavoriteFields['favDate']}`)
                         VALUES('$iFile', '{$this->iViewer}', '" . time() . "')";
            $iRes = (int)$this->query($sqlQuery);
            if ($iRes > 0)
        		$bRes = true;
	    }
	    return $bRes;
	}
	
	function removeFromFavorites ($iFile) {
		$iFile = (int)$iFile;
		$bRes = false;
		if ($iFile > 0) {
			$sqlQuery = "DELETE FROM `{$this->sFavoriteTable}`
						 WHERE `{$this->aFavoriteFields['fileId']}`='$iFile' 
						 AND `{$this->aFavoriteFields['ownerId']}`='{$this->iViewer}'";
			$iRes = (int)$this->query($sqlQuery);
            if ($iRes != 1)
        		$bRes = true;
		}
		return $bRes;
	}
	
	function checkFavoritesIn ($iFile) {
		$iFile = (int)$iFile;
		$sqlCheck = "SELECT COUNT(*) FROM `{$this->sFavoriteTable}` 
                     WHERE `{$this->aFavoriteFields['fileId']}`='$iFile' 
					 AND `{$this->aFavoriteFields['ownerId']}`='{$this->iViewer}'";
        $iCheck = (int)$this->getOne($sqlCheck);
        return $iCheck == 0 ? false : true;
	}
	
	function getFavorites ($iMember, $iFrom = 0, $iPerPage = 10) {
	    $iMember = (int)$iMember;
        $iFrom = (int)$iFrom;
        $iPerPage = (int)$iPerPage;
        $sqlQuery = "SELECT `{$this->aFavoriteFields['fileId']}` FROM `{$this->sFavoriteTable}` WHERE `{$this->aFavoriteFields['ownerId']}`=$iMember LIMIT $iFrom, $iPerPage";
        return $this->getAll($sqlQuery);
	}
	
	function getFavoritesCount ($iFile) {
	    $iFile = (int)$iFile;
	    $sqlQuery = "SELECT COUNT(*) FROM `{$this->sFavoriteTable}` WHERE `{$this->aFavoriteFields['fileId']}`='$iFile'";
	    return (int)$this->getOne($sqlQuery);
	}
	
    function getFilesByMonth ($iYear, $iMonth, $iNextYear, $iNextMonth, $sStatus = 'approved') {
        $aFields = array('medID', 'Categories', 'medProfId', 'medTitle', 'medUri', 'Rate');
        $sStatus = process_db_input($sStatus, BX_TAGS_STRIP);
        foreach ($aFields as $sValue) {
            if (isset($this->aFileFields[$sValue]))
                $sqlFields .= "`{$this->aFileFields[$sValue]}`, ";
        } 
        return $this->getAll ("SELECT $sqlFields DAYOFMONTH(FROM_UNIXTIME(`{$this->aFileFields['medDate']}`)) AS `Day`
            FROM `{$this->sFileTable}`
            WHERE `{$this->aFileFields['medDate']}` >= UNIX_TIMESTAMP('$iYear-$iMonth-1') AND `{$this->aFileFields['medDate']}` < UNIX_TIMESTAMP('$iNextYear-$iNextMonth-1') AND `{$this->aFileFields['Approved']}` = '$sStatus'");
    }
	
	function insertData ($aData) {
	   if ($this->_setData($aData))
	       return $this->lastId();
	}

	//update db action
	function updateData ($iFileId, $aData) {
	    if ($this->_setData($aData, $iFileId))
	       return true;
	    else
	       return false; 
	}
	
	// update/insert function
	function _setData ($aData, $iFileId = 0) {
		$sqlCond = "";
	    $iFileId = (int)$iFileId;
	    if ($iFileId > 0) {
	        $sqlQuery = "UPDATE";
	        $sqlCond = " WHERE `{$this->aFileFields['medID']}`='$iFileId'";
	    }
	    else {
	        $sqlQuery = "INSERT INTO ";
	        // spec key field
	        $aData['Hash'] = md5(microtime());
	    }
	    $sqlQuery .= "`{$this->sFileTable}` SET ";
        foreach ($aData as $sKey => $sValue) {
            if (array_key_exists($sKey, $this->aFileFields))
               $sqlQuery .= "`{$this->aFileFields[$sKey]}`='" . process_db_input($sValue, BX_TAGS_STRIP) . "', ";
        }
        return $this->query(trim($sqlQuery, ', ') . $sqlCond);
	}
	
	function deleteData ($iFile) {
	    $iFile = (int)$iFile;
        //delete from favorites
        $sqlQuery = "DELETE FROM `{$this->sFavoriteTable}` WHERE `{$this->aFavoriteFields['fileId']}`='$iFile'";
        $this->query($sqlQuery);
	    //delete from main table
	    $sqlQuery = "DELETE FROM `{$this->sFileTable}` WHERE `{$this->aFileFields['medID']}`='$iFile'";
	    return $this->query($sqlQuery);
	}
	
	function getFilesCountByParams (&$aParams) {
		$sqlWhere = "1";
		$sqlJoin  = "";
		foreach ($aParams as $sField => $mixedParam) {
			$sParam = process_db_input($sParam, BX_TAGS_STRIP);
			switch ($sField) {
				case 'albumID':
					$oAlbum = new BxDolAlbums($this->_oConfig->getMainPrefix(), $this->iViever);
					$sqlJoin = "LEFT JOIN `{$oAlbum->sAlbumObjectsTable}` ON `{$oAlbum->sAlbumObjectsTable}`.`id_object`=`{$this->sFileTable}`.`{$this->aFileFields['medID']}`";
					$sqlWhere .= " AND `{$oAlbum->sAlbumObjectsTable}`.`id_album` = " . (int)$mixedParam;
					break;
				case 'albumUri':
				case 'albumCaption':
					$oAlbum = new BxDolAlbums($this->_oConfig->getMainPrefix(), $this->iViever);
					$sqlJoin = "LEFT JOIN `{$oAlbum->sAlbumObjectsTable}` ON `{$oAlbum->sAlbumObjectsTable}`.`id_object`=`{$this->sFileTable}`.`{$this->aFileFields['medID']}`
								LEFT JOIN `{$oAlbum->sAlbumTable}` ON `{$oAlbum->sAlbumTable}`.`ID`=`{$oAlbum->sAlbumObjectsTable}`.`id_album`";
					$sFieldName = str_replace('album', '', $sField);
					$sqlWhere .= " AND `{$oAlbum->sAlbumTable}`.`Type` = '{$this->_oConfig->getMainPrefix()}' ";
					if (in_array($sFieldName, $oAlbum->aAlbumFields))
						$sqlWhere .= "AND `{$oAlbum->sAlbumTable}`.`$sFieldName` = '$mixedParam'";
					break;
				default:
					if (isset($this->aFileFields[$sField])) {
						$mixedParam = is_array($mixedParam) ? $mixedParam : array($mixedParam);
						$sqlList = "";
						foreach ($mixedParam as $sParam)
							$sqlList .= "'$sParam', ";
						$sqlList = trim($sqlList, ', ');
						$sqlWhere .= " AND `{$this->sFileTable}`.`{$this->aFileFields[$sField]}` IN($sqlList)";
					}
					break;
			}
		}
		$sqlQuery = "SELECT COUNT(`{$this->sFileTable}`.`{$this->aFileFields['medID']}`) FROM `{$this->sFileTable}` $sqlJoin WHERE $sqlWhere";
		return $this->getOne($sqlQuery);
	}
	
	function getFilesCountByAuthor ($iProfileId, $sStatus = 'approved') {
	    $iProfileId = (int)$iProfileId;
        $sStatus = process_db_input($sStatus, BX_TAGS_STRIP);
        if ($iProfileId > 0) {
            $sqlQuery = "SELECT COUNT(`{$this->aFileFields['medID']}`) FROM `{$this->sFileTable}` WHERE `{$this->aFileFields['medProfId']}`='$iProfileId' AND `{$this->aFileFields['Approved']}`='$sStatus'";
            return $this->getOne($sqlQuery);
        }
	}
	
	function getFilesByAuthor ($iProfileId) {
	    $iProfileId = (int)$iProfileId;
        if ($iProfileId > 0) {
            $sqlQuery = "SELECT `{$this->aFileFields['medID']}` FROM `{$this->sFileTable}` WHERE `{$this->aFileFields['medProfId']}`='$iProfileId'";
            return $this->getPairs($sqlQuery, $this->aFileFields['medID'], $this->aFileFields['medID']);
        }
	}
	
	function getFileInfo ($aIdent, $bSimple = false, $aFields = array()) {
	    if (isset($aIdent['fileUri']))
	        $sqlCondition = "`{$this->sFileTable}`.`{$this->aFileFields['medUri']}`='" . process_db_input($aIdent['fileUri'], BX_TAGS_STRIP) . "'";
	    elseif (isset($aIdent['fileId']))
	        $sqlCondition = "`{$this->sFileTable}`.`{$this->aFileFields['medID']}`='" . (int)$aIdent['fileId'] . "'";
	    else
	        return;
	    
        if (count($aFields) == 0)
	       $aFields = array_keys($this->aFileFields);
	    $sqlFields = "";
	    foreach ($aFields as $sValue) {
	        if (isset($this->aFileFields[$sValue]))
	           $sqlFields .= "`{$this->sFileTable}`.`{$this->aFileFields[$sValue]}` as `$sValue`, ";
	    }
        if (!$bSimple) {
            // album joins
            $oAlbum = new BxDolAlbums($this->_oConfig->getMainPrefix());
            $sqlAlbumJoin = "
                INNER JOIN `{$oAlbum->sAlbumObjectsTable}` ON `{$oAlbum->sAlbumObjectsTable}`.`id_object`=`{$this->sFileTable}`.`{$this->aFileFields['medID']}`
                INNER JOIN `{$oAlbum->sAlbumTable}` ON (`{$oAlbum->sAlbumTable}`.`ID`=`{$oAlbum->sAlbumObjectsTable}`.`id_album` AND `{$oAlbum->sAlbumTable}`.`Type`='" . $this->_oConfig->getMainPrefix() . "')
            ";
            $sqlAlbumFields = "`{$oAlbum->sAlbumTable}`.`ID` as `albumId`, `{$oAlbum->sAlbumTable}`.`Caption` as `albumCaption`, `{$oAlbum->sAlbumTable}`.`Uri` as `albumUri`, `{$oAlbum->sAlbumTable}`.`AllowAlbumView`, `{$oAlbum->sAlbumObjectsTable}`.`obj_order`";
            
            $sqlCount = "COUNT(`share1`.`{$this->aFileFields['medID']}`) as `Count`, ";
            $sqlCountJoin = "LEFT JOIN `{$this->sFileTable}` as `share1` USING (`{$this->aFileFields['medProfId']}`)";
            $sqlGroup = "GROUP BY `share1`.`{$this->aFileFields['medProfId']}`";
        }
        else
            $sqlFields = rtrim($sqlFields, ', ');
        $sqlQuery = "SELECT $sqlFields $sqlCount $sqlAlbumFields
                     FROM `{$this->sFileTable}`
                     $sqlCountJoin
                     $sqlAlbumJoin
                     WHERE $sqlCondition $sqlGroup LIMIT 1";
        return $this->getRow($sqlQuery);
	}
	
    function getSettingsCategory () {
        
    }
    
    function getMemberList ($sArg) {
        $sArg = process_db_input($sArg, BX_TAGS_STRIP);
        $sqlQuery = "SELECT `ID`, `NickName` FROM `Profiles` WHERE `NickName` LIKE '$sArg%'";
        return $this->getAll($sqlQuery);
    }
}

?>
