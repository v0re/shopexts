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

class BxBlogsCalendar extends BxTemplCalendar {

	var $oBlogsModule;

    function BxBlogsCalendar ($iYear, $iMonth, &$oModule) {
        parent::BxTemplCalendar($iYear, $iMonth);
		$this->oBlogsModule = &$oModule;
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
    	$sStatus = 'approval';
    	if($this -> oBlogsModule -> isAllowedApprove() 
    		|| $this -> oBlogsModule -> isAllowedPostEdit(-1) 
    		|| $this -> oBlogsModule -> isAllowedPostDelete(-1) ) {

			$sStatus = '';
    	}

        return $this->oBlogsModule->_oDb->getBlogPostsByMonth($this->iYear
        	, $this->iMonth, $this->iNextYear, $this->iNextMonth, $sStatus);
    }

    /**
     * return html for data unit for some day, it is:
     * - icon 32x32 with link if data have associated image, use $GLOBALS['oFunctions']->sysIcon() to return small icon
     * - data title with link if data have no associated image
     */ 
    function getUnit(&$aData) {
		$iPostID = (int)$aData['PostID'];
		$sPostUri = $aData['PostUri'];
		$sName = $aData['PostCaption'];
		$sUrl = $this->oBlogsModule->genUrl($iPostID, $sPostUri, 'entry');

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
        return $this->oBlogsModule->sHomeUrl . $this->oBlogsModule->_oConfig->sUserExFile . "?action=show_calendar&date=";
    }

    function getBrowseUri () {
        return $this->oBlogsModule->sHomeUrl . $this->oBlogsModule->_oConfig->sUserExFile . "?action=show_calendar_day&date=";
    }

    function getEntriesNames () {
        return array(_t('_bx_blog_single'), _t('_bx_blog_plural'));
    }
}

?>