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

bx_import ('BxDolCalendar');

/**
 * @see BxDolCalendar
 */ 
class BxBaseCalendar extends BxDolCalendar {
    function BxBaseCalendar ($iYear, $iMonth) {
        parent::BxDolCalendar($iYear, $iMonth);
    }

    function display() {
        $aVars = array (
            'bx_repeat:week_names' => $this->_getWeekNames (),
            'bx_repeat:calendar_row' => $this->_getCalendar (),
            'month_prev_url' => $this->getBaseUri () . "{$this->iPrevYear}/{$this->iPrevMonth}",
            'month_prev_name' => _t('_month_prev'),
            'month_prev_icon' => getTemplateIcon('sys_back.png'),
            'month_next_url' => $this->getBaseUri () . "{$this->iNextYear}/{$this->iNextMonth}",
            'month_next_name' => _t('_month_next'),
            'month_next_icon' => getTemplateIcon('sys_next.png'),
            'month_current' => $this->getTitle(),
        );
        $sHtml = $GLOBALS['oSysTemplate']->parseHtmlByName('calendar.html', $aVars);
        $sHtml = preg_replace ('#<bx_repeat:events>.*?</bx_repeat:events>#s', '', $sHtml);
        $GLOBALS['oSysTemplate']->addCss('calendar.css');
        return $sHtml;
    }    
}

?>
