<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -----------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2006 BoonEx Group
*     website              : http://www.boonex.com/
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software. This work is licensed under a Creative Commons Attribution 3.0 License. 
* http://creativecommons.org/licenses/by/3.0/
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the Creative Commons Attribution 3.0 License for more details. 
* You should have received a copy of the Creative Commons Attribution 3.0 License along with Dolphin, 
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

bx_import('BxDolPageView');

class BxSitesPageHon extends BxDolPageView {
        
    var $_oSites;
    var $_oTemplate;
    var $_oDb;
    
    function BxSitesPageHon(&$oSites) 
    {
        parent::BxDolPageView('bx_sites_hon');
                
        $this->_oSites = &$oSites;
        $this->_oTemplate = $oSites->_oTemplate;
        $this->_oDb = $oSites->_oDb;
    }

    function getBlockCode_ViewPreviously() 
    {
        bx_sites_import('SearchResult');
        $oSearchResult = new BxSitesSearchResult('hon_prev_rate');
        $oSearchResult->sUnitTemplate = 'block_prev_hon';
            
        if ($s = $oSearchResult->displayResultBlock())
            return $s;
        else
            return MsgBox(_t('_Empty'));
    }
    
    function getBlockCode_ViewRate() 
    {
        bx_sites_import('SearchResult');
        $oSearchResult = new BxSitesSearchResult('hon_rate');
        $oSearchResult->sUnitName = 'hon';
        $oSearchResult->sUnitTemplate = 'block_hon';
            
        if ($s = $oSearchResult->displayResultBlock())
            return $s;
        else
            return MsgBox(_t('_Empty'));
    }
}
?>