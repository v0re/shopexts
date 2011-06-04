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

class BxVideosCalendar extends BxTemplCalendar {
    var $oDb, $oTemplate, $oConfig;

    function BxVideosCalendar ($iYear, $iMonth, &$oDb, &$oTemplate, &$oConfig) {
        parent::BxTemplCalendar($iYear, $iMonth);
        $this->oDb = &$oDb;
        $this->oTemplate = &$oTemplate;
        $this->oConfig = &$oConfig;
    }
    
    function getData () {
        return $this->oDb->getFilesByMonth ($this->iYear, $this->iMonth, $this->iNextYear, $this->iNextMonth);
    }
    
    function getUnit (&$aData) {
        
    }
    
    function getBaseUri () {
        return BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . "calendar/";
    }

    function getBrowseUri () {
        return BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . "browse/calendar/";
    }
    
    function getEntriesNames () {
        return array(_t('_bx_' . $this->oConfig->getUri() . '_single'), _t('_bx_' . $this->oConfig->getUri() . '_plural'));
    }    
}
?>