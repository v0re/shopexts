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
bx_import('BxDolSessionQuery');

define('BX_DOL_SESSION_LIFETIME', 3600);
define('BX_DOL_SESSION_COOKIE', 'memberSession');

class BxDolSession extends BxDolMistake {
	var $oDb;
	var $sId;
	var $iUserId;
	var $aData;

	private function BxDolSession() {
		parent::BxDolMistake();

		$this->oDb = new BxDolSessionQuery();
		$this->sId = '';
		$this->iUserId = 0;
		$this->aData = array();
	}

	function getInstance() {
	    if(!isset($GLOBALS['bxDolClasses']['BxDolSession']))
	    	$GLOBALS['bxDolClasses']['BxDolSession'] = new BxDolSession();

	    if(!$GLOBALS['bxDolClasses']['BxDolSession']->getId())	    
        	$GLOBALS['bxDolClasses']['BxDolSession']->start();

		return $GLOBALS['bxDolClasses']['BxDolSession'];
	}

	function start(){
        if (defined('BX_DOL_CRON_EXECUTE'))
            return true;

		if($this->exists($this->sId))
            return true;

		$this->sId = genRndPwd(32, true);

		$aUrl = parse_url($GLOBALS['site']['url']);
	    $sPath = isset($aUrl['path']) && !empty($aUrl['path']) ? $aUrl['path'] : '/';
		setcookie(BX_DOL_SESSION_COOKIE, $this->sId, 0, $sPath, '', false, true);

		$this->save();
		return true;
	}

	function destroy() {
		$aUrl = parse_url($GLOBALS['site']['url']);
	    $sPath = isset($aUrl['path']) && !empty($aUrl['path']) ? $aUrl['path'] : '/';
		setcookie(BX_DOL_SESSION_COOKIE, '', time() - 86400, $sPath, '', false, true);
		unset($_COOKIE[BX_DOL_SESSION_COOKIE]);

		$this->oDb->delete($this->sId);

		$this->sId = '';
		$this->iUserId = 0;
		$this->aData = array();
	}

	function exists($sId = '') {
		if(empty($sId) && isset($_COOKIE[BX_DOL_SESSION_COOKIE]))
			$sId = process_db_input($_COOKIE[BX_DOL_SESSION_COOKIE], BX_TAGS_STRIP);

		$mixedSession = array();
		if(($mixedSession = $this->oDb->exists($sId)) !== false) {
			$this->sId = $mixedSession['id'];
			$this->iUserId = (int)$mixedSession['user_id']; 
			$this->aData = unserialize($mixedSession['data']);
			return true;
		}
		else
			return false;
	}

	function getId() {
		return $this->sId;
	}

	function setValue($sKey, $mixedValue) {
		if(empty($this->sId))
			$this->start();

		$this->aData[$sKey] = $mixedValue;
		$this->save();
	}

	function unsetValue($sKey) {
		if(empty($this->sId))
			$this->start();

		unset($this->aData[$sKey]);

		if(!empty($this->aData))
			$this->save();
		else 
			$this->destroy();
	}

	function getValue($sKey) {
		if(empty($this->sId))
			$this->start();

		return isset($this->aData[$sKey]) ? $this->aData[$sKey] : false;
	}

	private function save() {
		if($this->iUserId == 0)
			$this->iUserId = getLoggedId();

		$this->oDb->save($this->sId, array(
			'user_id' => $this->iUserId,
			'data' => serialize($this->aData)
		));
	} 
}
?>
