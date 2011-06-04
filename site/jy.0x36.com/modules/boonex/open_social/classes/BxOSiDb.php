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

class BxOSiDb extends BxDolDb {	
	var $_oConfig;

	/*
	* Constructor.
	*/
	function BxOSiDb(&$oConfig) {
		parent::BxDolDb();

		$this->_oConfig = $oConfig;
	}

	function insertProfileApplication($_iProfileID, $sNewUrl, $sNewDesc) {
		if ($sNewUrl != '' && $sNewDesc != '') {
			$sStatus = (getParam('applications_auto_appr_bx_open_social') == 'on') ? 'active' : 'passive';

			$sOSiSQL = "
				INSERT INTO `bx_osi_main` SET
				`person_id`='{$_iProfileID}',
				`url`='{$sNewUrl}' /*,
				`description`='{$sNewDesc}',
				`status`='{$sStatus}'*/
			";
			return $this->query($sOSiSQL);
		}
	}

	function updateProfileApplication($_iProfileID, $sNewUrl, $iOldID) {
		if ($iOldID && $sNewUrl != '') {
			$sStatus = (getParam('applications_auto_appr_bx_open_social') == 'on') ? 'active' : 'passive';

			$sOSiSQL = "
				UPDATE `bx_osi_main` SET
				`url`='{$sNewUrl}',
				`status`='{$sStatus}'
				WHERE 
				`person_id`='{$_iProfileID}' AND `ID`='{$iOldID}'
			";
			return $this->query($sOSiSQL);
		}
	}

	function deleteProfileApplication($_iProfileID, $iOldID) {
		if ($iOldID) {
			$sOSiSQL = "
				DELETE FROM `bx_osi_main`
				WHERE `person_id`='{$_iProfileID}' AND `ID`='{$iOldID}'
			";
			return $this->query($sOSiSQL);
		}
	}
	function deleteApplication($iID) {
		if ($iID) {
			$sOSiSQL = "DELETE FROM `bx_osi_main` WHERE `ID`='{$iID}'";
			return $this->query($sOSiSQL);
		}
	}

	function getProfileApplications($_iProfileID) {
		$sMemberApplsSQL = "SELECT * FROM `bx_osi_main` WHERE `person_id`='{$_iProfileID}'";

		$aApplsInfo = array();

	    $aApplicationInfo = $this->getFirstRow($sMemberApplsSQL);
	    while ($aApplicationInfo) {
            $aApplsInfo[] = $aApplicationInfo;
            $aApplicationInfo = $this->getNextRow();
	    }

		return $aApplsInfo;
	}

	function getActiveProfileApplications($_iProfileID) {
		$sMemberApplsSQL = "SELECT * FROM `bx_osi_main` WHERE `person_id`='{$_iProfileID}' AND `status`='active'";

		$aApplsInfo = array();

	    $aApplicationInfo = $this->getFirstRow($sMemberApplsSQL);
	    while ($aApplicationInfo) {
            $aApplsInfo[] = $aApplicationInfo;
            $aApplicationInfo = $this->getNextRow();
	    }

		return $aApplsInfo;
	}

	function getAdminsApplications() {
		$aApplications = array();
		/*$aAdmins = array();

		$sAdminListSQL = "SELECT * FROM `Profiles` WHERE `Role`='3' AND `Status`='Active'";
		$aAdminInfo = $this->getFirstRow($sAdminListSQL);
		while ($aAdminInfo) {
			$iAdminID = (int)$aAdminInfo['ID'];
			$aAdmins[] = $iAdminID;
			$aAdminInfo = $this->getNextRow();
		}*/

		//foreach ($aAdmins as $iAdminID) {
			//$sMemberApplsSQL = "SELECT * FROM `bx_osi_main` WHERE `person_id`='{$iAdminID}' AND `status`='active'";
			$sMemberApplsSQL = "SELECT * FROM `bx_osi_main` WHERE `person_id`='0' AND `status`='active'";
			$aApplicationInfo = $this->getFirstRow($sMemberApplsSQL);
			while ($aApplicationInfo) {
				$sApplID = (int)$aApplicationInfo['ID'];
				$sApplTitle = $aApplicationInfo['title'];
				$aApplications[$sApplID] = $sApplTitle;
				$aApplicationInfo = $this->getNextRow();
			}
		//}

		return $aApplications;
	}

	function getApplicationInfo($iID) {
		$sApplsSQL = "SELECT * FROM `bx_osi_main` WHERE `ID`='{$iID}'";
		return db_arr($sApplsSQL);
	}

	function updateApplicationStatus($iID, $sStatus = 'passive') {
		if ($iID && $sStatus != '') {
			$sOSiSQL = "UPDATE `bx_osi_main` SET `status`='{$sStatus}' WHERE `ID`='{$iID}'";
			return $this->query($sOSiSQL);
		}
	}

	function copyApplication($iID, $iMemberID) {
		if ($iID && $iMemberID >= 0) {
			$sOSiSQL = "
				INSERT INTO `bx_osi_main` (`person_id`, `url`, `description`, `status`, `title`, `directory_title`, `author`, `author_email`, `settings`, `views`, `version`, `height`, `scrolling`, `modified`, `screenshot`, `thumbnail`)
				SELECT '{$iMemberID}', `url`, `description`, `status`, `title`, `directory_title`, `author`, `author_email`, `settings`, `views`, `version`, `height`, `scrolling`, `modified`, `screenshot`, `thumbnail`
				FROM `bx_osi_main` WHERE `ID` ='{$iID}' AND `person_id` != {$iMemberID}
			";
			return $this->query($sOSiSQL);
		}
	}
}

?>