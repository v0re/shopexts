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

bx_import('BxTemplSearchResult');

/**
 * Base data search class for modules like events/groups/store
 */
class BxDolTwigSearchResult extends BxTemplSearchResult  {

    var $oVotingView = null;
    var $iRate = 1;
    var $sBrowseUrl;
    var $isError;        
    var $aCurrent = array ();
    var $aGlParamsSettings = array();
    var $sProfileCatType;    
    var $sUnitTemplate = 'unit';
    var $sFilterName = 'unit';
    
    function BxDolTwigSearchResult() {
        parent::BxTemplSearchResult();
    }

    function getMain() {
        // override this to return main module class
    }

    function displaySearchUnit ($aData) {
        $oMain = $this->getMain();
		return $oMain->_oTemplate->unit($aData, $this->sUnitTemplate, $this->oVotingView);
    }

    function showPagination($bAdmin = false) {

        $oMain = $this->getMain();
        $oConfig = $oMain->_oConfig;
        bx_import('BxDolPaginate');
        $sUrlStart = BX_DOL_URL_ROOT . $oConfig->getBaseUri() . $this->sBrowseUrl;
        $sUrlStart .= (false === strpos($sUrlStart, '?') ? '?' : '&');

        $oPaginate = new BxDolPaginate(array(
            'page_url' => $sUrlStart . 'page={page}&per_page={per_page}' . (false !== bx_get($this->sFilterName) ? '&' . $this->sFilterName . '=' . bx_get($this->sFilterName) : ''),
            'count' => $this->aCurrent['paginate']['totalNum'],
            'per_page' => $this->aCurrent['paginate']['perPage'],
            'page' => $this->aCurrent['paginate']['page'],
            'per_page_changer' => true,
            'page_reloader' => true,
            'on_change_page' => '',
            'on_change_per_page' => "document.location='" . $sUrlStart . "page=1&per_page=' + this.value + '" . (false !== bx_get($this->sFilterName) ? '&' . $this->sFilterName . '=' . bx_get($this->sFilterName) ."';": "';"),
        ));

        return '<div class="clear_both"></div>'.$oPaginate->getPaginate();
    }

    function setPublicUnitsOnly($isPublic) {
        $this->aCurrent['restriction']['public']['value'] = $isPublic ? BX_DOL_PG_ALL : false;
    }

    function showPaginationAjax($sBlockId) {

        $oMain = $this->getMain();
        $oConfig = $oMain->_oConfig;
        bx_import('BxDolPaginate');
        $sUrlStart = BX_DOL_URL_ROOT . $oConfig->getBaseUri() . $this->sBrowseUrl;
        $sUrlStart .= (false === strpos($sUrlStart, '?') ? '?' : '&');

        $oPaginate = new BxDolPaginate(array(
            'page_url' => 'javascript:void(0);',
            'count' => $this->aCurrent['paginate']['totalNum'],
            'per_page' => $this->aCurrent['paginate']['perPage'],
            'page' => $this->aCurrent['paginate']['page'],
            'on_change_page' => "getHtmlData('{$sBlockId}', '{$sUrlStart}page={page}&per_page={per_page}&block={$sBlockId}" . (false !== bx_get($this->sFilterName) ? '&' . $this->sFilterName . '=' . bx_get($this->sFilterName) : '') . "');",
        ));

        return $oPaginate->getSimplePaginate(false, -1, -1, false);
    }

    function rss () {
        $this->setPublicUnitsOnly(true);
        return parent::rss();
    }
}

?>
