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

class BxAdsCalendar extends BxTemplCalendar {

	var $oAdsModule;

    function BxAdsCalendar ($iYear, $iMonth, &$oModule) {
        parent::BxTemplCalendar($iYear, $iMonth);
		$this->oAdsModule = &$oModule;
    }

	/**
	* return records for current month, there is mandatory field `Day` - a day for current row
	* use the following class variables to pass to your database query 
	* $this->iYear, $this->iMonth, $this->iNextYear, $this->iNextMonth
	*
	* for example:
	* 
	* return $db->getAll ("
	*  SELECT *, DAYOFMONTH(FROM_UNIXTIME(`DateTime`)) AS `Day`
	*  FROM `my_table`
	*  WHERE `Date` >= UNIX_TIMESTAMP('{$this->iYear}-{$this->iMonth}-1') AND `Date` < UNIX_TIMESTAMP('{$this->iNextYear}-{$this->iNextMonth}-1') AND `Status` = 'approved'");
	*
	*/ 
    function getData () {
        return $this->oAdsModule->_oDb->getAdsByMonth($this->iYear, $this->iMonth, $this->iNextYear, $this->iNextMonth);
    }

	/**
	* return html for data unit for some day, it is:
	* - icon 32x32 with link if data have associated image, use $GLOBALS['oFunctions']->sysIcon() to return small icon
	* - data title with link if data have no associated image
	*/ 
    // function getUnit(&$aData) {
    // }

	/**
	* return base calendar url
	* year and month will be added to this url automatically
	* so if your base url is /m/some_module/calendar/, it will be transormed to
	* /m/some_module/calendar/YEAR/MONTH, like /m/some_module/calendar/2009/3
	*/ 
    function getBaseUri () {
        return $this->oAdsModule->_oConfig->sCurrBrowsedFile . "?action=show_calendar&date=";
    }

    function getBrowseUri () {
		return $this->oAdsModule->_oConfig->sCurrBrowsedFile . "?action=show_calendar_ads&date=";
    }

    function getEntriesNames () {
        return array(_t('_bx_ads_Ad'), _t('_bx_ads_Ads'));
    }
}

?>