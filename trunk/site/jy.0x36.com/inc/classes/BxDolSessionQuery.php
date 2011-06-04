<?php
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

require_once (BX_DIRECTORY_PATH_CLASSES . 'BxDolDb.php');

/**
 * @see BxDolSession
 */ 
class BxDolSessionQuery extends BxDolDb {
	var $sTable;

	function BxDolSessionQuery() {
		parent::BxDolDb();

        $this->sTable = 'sys_sessions';
	}
    function getTableName() {
        return $this->sTable;
    }
    function exists($sId) {
    	$aSession = $this->getRow("SELECT `id`, `user_id`, `data` FROM `" . $this->sTable . "` WHERE `id`='" . $sId . "' LIMIT 1");    	
		return !empty($aSession) ? $aSession : false;
    }
    function save($sId, $aSet) {
    	$sSetClause = "`id`='" . $sId . "'";
    	foreach($aSet as $sKey => $sValue)
    		$sSetClause .= ", `" . $sKey . "`='" . $sValue . "'";
    	$sSetClause .= ", `date`=UNIX_TIMESTAMP()";

    	return (int)$this->query("REPLACE INTO `" . $this->sTable . "` SET " . $sSetClause) > 0;
    }
    function delete($sId) {
    	return (int)$this->query("DELETE FROM `" . $this->sTable . "` WHERE `id`='" . $sId . "' LIMIT 1") > 0;
    }
    function deleteExpired() {
    	$iRet = (int)$this->query("DELETE FROM `" . $this->sTable . "` WHERE `date`<(UNIX_TIMESTAMP()-" . BX_DOL_SESSION_LIFETIME . ")");
        $this->query("OPTIMIZE TABLE `" . $this->sTable . "`");
        return $iRet;
    }
}
?>
