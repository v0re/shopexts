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

bx_import ('BxTemplCalendar');

class BxDolProfilesCalendar extends BxTemplCalendar {

	var $sMode = 'dor';

    function BxDolProfilesCalendar ($iYear, $iMonth) {
        parent::BxTemplCalendar($iYear, $iMonth);
    }

	function setMode($sMode) {
		$this->sMode = $sMode;
	}

    /**
     * return records for current month, there is mandatory field `Day` - a day for current row
     * use the following class variables to pass to your database query 
     * $this->iYear, $this->iMonth, $this->iNextYear, $this->iNextMonth
     *
     * for example:
     * 
     * return $db->getAll ("
     *  SELECT *, DAYOFMONTH(FROM_UNIXTIME(`Blogstart`)) AS `Day`
     *  FROM `my_table`
     *  WHERE `Date` >= UNIX_TIMESTAMP('{$this->iYear}-{$this->iMonth}-1') AND `Date` < UNIX_TIMESTAMP('{$this->iNextYear}-{$this->iNextMonth}-1') AND `Status` = 'approved'");
     *
     */ 
    function getData () {
		switch($this->sMode) {
			case 'dor':
		        return db_res_assoc_arr ("
					SELECT `Profiles`.*, DAYOFMONTH(`Profiles`.`DateReg`) AS `Day`
		            FROM `Profiles`
		            WHERE
						UNIX_TIMESTAMP(`Profiles`.`DateReg`) >= UNIX_TIMESTAMP('{$this->iYear}-{$this->iMonth}-1')
						AND UNIX_TIMESTAMP(`Profiles`.`DateReg`) < UNIX_TIMESTAMP('{$this->iNextYear}-{$this->iNextMonth}-1')
						AND `Profiles`.`Status` = 'Active'
				");
			case 'dob':
				$aWhere[] = "MONTH(`DateOfBirth`) = MONTH(CURDATE()) AND DAY(`DateOfBirth`) = DAY(CURDATE())";
		        return db_res_assoc_arr ("
					SELECT `Profiles`.*, DAYOFMONTH(`DateOfBirth`) AS `Day`
		            FROM `Profiles`
		            WHERE
						MONTH(`DateOfBirth`) = MONTH('{$this->iYear}-{$this->iMonth}-1') AND
						`Profiles`.`Status` = 'Active'
				");
		}

    }

    /**
     * return html for data unit for some day, it is:
     * - icon 32x32 with link if data have associated image, use $GLOBALS['oFunctions']->sysIcon() to return small icon
     * - data title with link if data have no associated image
     */ 
    function getUnit(&$aData) {
		//global $oFunctions;

		$iProfileID = (int)$aData['ID'];

		$sName = getNickName($iProfileID);
		$sUrl = getProfileLink($iProfileID);
		//$sIcon = get_member_icon($iProfileID, 'none', true);

		//return $sIcon;
		//return $oFunctions->sysIcon($sIcon, $sName, $sUrl);
        return <<<EOF
<div style="width:95%;">
	<a title="{$sName}" href="{$sUrl}">{$sName}</a>
</div>
EOF;
    }

    /**
     * return base calendar url
     * year and month will be added to this url automatically
     * so if your base url is /m/some_module/calendar/, it will be transormed to
     * /m/some_module/calendar/YEAR/MONTH, like /m/some_module/calendar/2009/3
     */ 
    function getBaseUri () {
		$sPossibleMode = (isset($_REQUEST['mode']) && $_REQUEST['mode']!='') ? '&mode=' . $_REQUEST['mode'] : '';
        return BX_DOL_URL_ROOT . "calendar.php?{$sPossibleMode}&date=";
    }
    
    function getBrowseUri () {
        return BX_DOL_URL_ROOT .  "calendar.php?action=browse&date=";
    }
    
    function getEntriesNames () {
        return array(_t('_sys_profile'), _t('_sys_profiles'));
    }
}

?>