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

bx_import('BxDolPageView');
bx_import('BxDolPrivacy');
require_once('BxFilesSearch.php');

class BxFilesPageHome extends BxDolPageView {
    var $oTemplate;
    var $oConfig;
    var $oDb;
    var $oSearch;
    var $aVisible = array();
    function BxFilesPageHome (&$oShared) {
        parent::BxDolPageView('bx_files_home');
        $this->oTemplate = $oShared->_oTemplate;
        $this->oConfig = $oShared->_oConfig;
        $this->oDb = $oShared->_oDb;
        $this->oSearch = new BxFilesSearch();
        $this->aVisible[] = BX_DOL_PG_ALL;
        if ($this->iMemberID > 0)
            $this->aVisible[] = BX_DOL_PG_MEMBERS;
        $this->oSearch->aCurrent['restriction']['allow_view']['value'] = $this->aVisible;
        $this->oSearch->aCurrent['restriction']['activeStatus']['value'] = 'approved';
        $this->oSearch->aCurrent['restriction']['album_status']['value'] = 'active';
    }
    
    function getBlockCode_All ($id) {
        $this->oSearch->clearFilters(array('activeStatus', 'allow_view', 'album_status', 'albumType', 'ownerStatus'), array('albumsObjects', 'albums', 'icon'));
        $this->oSearch->aCurrent['paginate']['perPage'] = (int)$this->oConfig->getGlParam('number_all');
        $this->oSearch->aCurrent['view'] = 'full';
        if (isset($this->oSearch->aCurrent['rss']))
            $this->oSearch->aCurrent['rss']['link'] = $this->oSearch->getCurrentUrl('browseAll', 0, '');

        $sCode = $this->oSearch->displayResultBlock();
        if ($this->oSearch->aCurrent['paginate']['totalNum'] > 0) {
            $aExclude = array($this->oSearch->aCurrent['name'] . '_mode', 'r');
            $sMode = isset($_GET[$this->aCurrent['name'] . '_mode']) ? '&' . $this->aCurrent['name'] . '_mode=' . $_GET[$this->aCurrent['name'].'_mode'] : '';
            $sLink  = $this->oConfig->getBaseUri() . 'home/';
            $aLinkAddon = $this->oSearch->getLinkAddByPrams($aExclude);
            $oPaginate = new BxDolPaginate(array(
                'page_url' => $sLink,
                'count' => $this->oSearch->aCurrent['paginate']['totalNum'],
                'per_page' => $this->oSearch->aCurrent['paginate']['perPage'],
                'page' => $this->oSearch->aCurrent['paginate']['page'],
                'per_page_changer' => true,
                'page_reloader' => true,
                'on_change_page' => 'return !loadDynamicBlock(' . $id . ', \'' . $sLink . $sMode . $aLinkAddon['params'] . '&page={page}&per_page={per_page}\');',
                'on_change_per_page' => 'return !loadDynamicBlock(' . $id . ', \'' . $sLink . $sMode . $aLinkAddon['params'] . '&page=1&per_page=\' + this.value);'
            ));
            $aTopMenu = $this->oSearch->getTopMenu(array($this->oSearch->aCurrent['name'] . '_mode'));
            $sPaginate = $oPaginate->getPaginate();
        }
        else {
            $sCode = MsgBox(_t("_Empty"));;
            $aTopMenu = array();
            $sPaginate = '';
        }
        return array($sCode, $aTopMenu, $sPaginate, '');
    }
        
    function getBlockCode_Featured () {
        $this->oSearch->clearFilters(array('activeStatus', 'allow_view', 'album_status', 'albumType', 'ownerStatus'), array('albumsObjects', 'albums', 'icon'));
        $this->oSearch->aCurrent['restriction']['featured'] = array(
            'field' => 'Featured',
            'value' => '1',
            'operator' => '=',
            'param' => 'featured'
        );
        $this->oSearch->aCurrent['paginate']['perPage'] = (int)$this->oConfig->getGlParam('number_top');
        $this->oSearch->aCurrent['view'] = 'short';        
        $sCode = $this->oSearch->displayResultBlock();
        if ($this->oSearch->aCurrent['paginate']['totalNum'] > 0) {
            return $sCode;
        }
    }
    
    function getBlockCode_Top () {
        $this->oSearch->clearFilters(array('activeStatus', 'allow_view', 'album_status', 'albumType', 'ownerStatus'), array('albumsObjects', 'albums', 'icon'));
        $this->oSearch->aCurrent['paginate']['perPage'] = (int)$this->oConfig->getGlParam('number_top');
        $this->oSearch->aCurrent['sorting'] = 'top';
        $this->oSearch->aCurrent['view'] = 'short';        
        $sCode = $this->oSearch->displayResultBlock();
        if ($this->oSearch->aCurrent['paginate']['totalNum'] > 0) {
            return $sCode;
        }
    }

    function getBlockCode_Albums () {
        $this->oSearch->clearFilters(array('activeStatus', 'allow_view', 'album_status', 'albumType', 'ownerStatus'), array('albumsObjects', 'albums', 'icon'));
        $aAlbumParams = array('allow_view' => $this->aVisible);
        $aCustom = array(
            'paginate_url' => BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'home',
            'simple_paginate_url' => BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'albums/browse',
        );
        $aCode = $this->oSearch->getAlbumsBlock(array(), $aAlbumParams, $aCustom);
        if ($this->oSearch->aCurrent['paginate']['totalAlbumNum'] > 0)
        	return $aCode;
    	else 
    		return MsgBox(_t('_Empty'));
    }
	
	function getBlockCode_Favorited () {
		$iUserId = getLoggedId();
		if ($iUserId == 0)
			return;
        $this->oSearch->clearFilters(array('activeStatus', 'allow_view', 'album_status', 'albumType', 'ownerStatus'), array('albumsObjects', 'albums', 'icon'));
    	if (isset($this->oSearch->aAddPartsConfig['favorite']) && !empty($this->oSearch->aAddPartsConfig['favorite'])) {
            $this->oSearch->aCurrent['join']['favorite'] = $this->oSearch->aAddPartsConfig['favorite']; 
            $this->oSearch->aCurrent['restriction']['fav'] = array(
                'value' => $iUserId,
                'field' => $this->oSearch->aAddPartsConfig['favorite']['userField'],
                'operator' => '=',
                'table' => $this->oSearch->aAddPartsConfig['favorite']['table']
            );
        }
        $this->oSearch->aCurrent['paginate']['perPage'] = (int)$this->oConfig->getGlParam('number_top');
        $sCode = $this->oSearch->displayResultBlock();
        if ($this->oSearch->aCurrent['paginate']['totalNum'] > 0) {
            $this->oSearch->aConstants['linksTempl']['favorited'] = 'browse/favorited';
            $sCode = $GLOBALS['oFunctions']->centerContent($sCode, '.sys_file_search_unit');
            $aTopMenu = array();
            $aBottomMenu = $this->oSearch->getBottomMenu('favorited', 0, '');
			return array($sCode, $aTopMenu, $aBottomMenu, '');
		}
    }
}

?>