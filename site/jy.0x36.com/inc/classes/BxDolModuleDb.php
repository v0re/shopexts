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

require_once('BxDolDb.php');
require_once('BxDolConfig.php');

class BxDolModuleDb extends BxDolDb {	    
    var $_sPrefix;
	/*
	 * Constructor.
	 */
	function BxDolModuleDb($oConfig = null) {
		parent::BxDolDb();

		if(is_a($oConfig,'BxDolConfig'))
            $this->_sPrefix = $oConfig->getDbPrefix();
	}
	function getPrefix() {
	    return $this->_sPrefix;
	}
	function getModuleById($iId) {
	    $sSql = "SELECT `id`, `title`, `vendor`, `version`, `update_url`, `path`, `uri`, `class_prefix`, `db_prefix`, `date` FROM `sys_modules` WHERE `id`='" . $iId . "' LIMIT 1";
	    return $this->fromMemory('sys_modules_' . $iId, 'getRow', $sSql);
	}
	function getModuleByUri($sUri) {
	    $sSql = "SELECT `id`, `title`, `vendor`, `version`, `update_url`, `path`, `uri`, `class_prefix`, `db_prefix`, `date` FROM `sys_modules` WHERE `uri`='" . $sUri . "' LIMIT 1";
	    return $this->fromMemory('sys_modules_' . $sUri, 'getRow', $sSql);
	}
	function isModule($sUri) {
	    $sSql = "SELECT `id` FROM `sys_modules` WHERE `uri`='" . $sUri . "' LIMIT 1";
	    return (int)$this->getOne($sSql) > 0;
	}
	function getModules() {
	    $sSql = "SELECT `id`, `title`, `vendor`, `version`, `update_url`, `path`, `uri`, `class_prefix`, `db_prefix`, `date` FROM `sys_modules` ORDER BY `title`";
	    return $this->fromMemory(`sys_modules`, 'getAll', $sSql);
	}
	function getDependent($sUri) {
	    $sSql = "SELECT `id`, `title` FROM `sys_modules` WHERE `dependencies` LIKE '%" . $sUri . "%'";
	    return $this->getAll($sSql);
	}
}
?>
