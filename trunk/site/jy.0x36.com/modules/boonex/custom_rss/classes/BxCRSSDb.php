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

require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolDb.php' );

class BxCRSSDb extends BxDolDb {	
	var $_oConfig;

	/*
	* Constructor.
	*/
	function BxCRSSDb(&$oConfig) {
		parent::BxDolDb();

		$this->_oConfig = $oConfig;
	}

	function insertProfileRSS($_iProfileID, $sNewUrl, $sNewDesc, $iQuantity) {
		if ($sNewUrl != '' && $sNewDesc != '') {
			$sStatus = (getParam('crss_AutoApprove_RSS') == 'on') ? 'active' : 'passive';

			$sRSSSQL = "
				INSERT INTO `bx_crss_main` SET
				`ProfileID`='{$_iProfileID}',
				`RSSUrl`='{$sNewUrl}',
				`Quantity`='{$iQuantity}',
				`Description`='{$sNewDesc}',
				`Status`='{$sStatus}'
			";
			return $this->query($sRSSSQL);
		}
	}

	function updateProfileRSS($_iProfileID, $sNewUrl, $iOldID) {
		if ($iOldID != '' && $sNewUrl != '') {
			$sStatus = (getParam('crss_AutoApprove_RSS') == 'on') ? 'active' : 'passive';

			$sRSSSQL = "
				UPDATE `bx_crss_main` SET
				`RSSUrl`='{$sNewUrl}',
				`Status`='{$sStatus}'
				WHERE 
				`ProfileID`='{$_iProfileID}' AND `ID`='{$iOldID}'
			";
			return $this->query($sRSSSQL);
		}
	}

	function deleteProfileRSS($_iProfileID, $iOldID) {
		if ($iOldID != '') {
			$sRSSSQL = "
				DELETE FROM `bx_crss_main`
				WHERE `ProfileID`='{$_iProfileID}' AND `ID`='{$iOldID}'
			";
			return $this->query($sRSSSQL);
		}
	}

	function getProfileRSS($_iProfileID) {
		$sMemberRSSSQL = "SELECT * FROM `bx_crss_main` WHERE `ProfileID`='{$_iProfileID}'";

		$aRSSInfos = array();

	    $aRSSInfo = $this->getFirstRow($sMemberRSSSQL);
	    while($aRSSInfo) {
            $aRSSInfos[] = $aRSSInfo;
            $aRSSInfo = $this->getNextRow();
	    }

		return $aRSSInfos;
	}

	function getActiveProfileRSS($_iProfileID) {
		$sMemberRSSSQL = "SELECT * FROM `bx_crss_main` WHERE `ProfileID`='{$_iProfileID}' AND `Status`='active'";

		$aRSSInfos = array();

	    $aRSSInfo = $this->getFirstRow($sMemberRSSSQL);
	    while($aRSSInfo) {
            $aRSSInfos[] = $aRSSInfo;
            $aRSSInfo = $this->getNextRow();
	    }

		return $aRSSInfos;
	}
}

?>