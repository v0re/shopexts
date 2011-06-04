<?php

/***************************************************************************
*							Dolphin Smart Community Builder
*							  -------------------
*	 begin				: Mon Mar 23 2006
*	 copyright			: (C) 2007 BoonEx Group
*	 website			  : http://www.boonex.com
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

bx_import('BxBaseSearchResult');
bx_import('BxDolAlbums');
bx_import('BxTemplVotingView');

class BxBaseSearchResultSharedMedia extends BxBaseSearchResult {
    var $bAdminMode = false;
	
	var $sTemplUnit;
	var $aConstants = array();
	
	var $aPermalinks = array();
	
    var $aGlParamsSettings = array();
    var $sProfileCatType;
    
    // additional tables parameters (rate, favorite ...)
    var $aAddPartsConfig = array();
    
    var $oTemplate;
    var $sCurrentAlbum;

    var $sModuleClass;
    var $oModule;
    
    var $oPrivacy;
    
	function BxBaseSearchResultSharedMedia ($sModuleClass = '') {
		/* main settings for shared modules
		   ownFields - fields which will be got from main table ($this->aCurrent['table'])
           searchFields - fields which using for full text key search
           join - array of join tables
                join array (
                    'type' - type of join
                    'table' - join table
                    'mainField' - field from main table for 'on' condition
                    'onField' - field from joining table for 'on' condition
                    'joinFields' - array of fields from joining table 
                ) 
		*/
		
		$this->aCurrent = array(
		    'ownFields' => array('ID', 'Title', 'Uri', 'Date', 'Time', 'Rate', 'RateCount'),
            'searchFields' => array('Title', 'Tags', 'Description', 'Categories'),
            'join' => array(
                'profile' => array(
                    'type' => 'left',
                    'table' => 'Profiles',
                    'mainField' => 'Owner',
                    'onField' => 'ID',
                    'joinFields' => array('NickName')
                ),
                'albumsObjects' => array(
                    'type' => 'left',
                    'table' => 'sys_albums_objects',
                    'mainField' => 'ID',
                    'onField' => 'id_object',
                    'joinFields' => ''
                ),
                'albums' => array(
                    'type' => 'left',
                    'table' => 'sys_albums',
                    'mainField' => 'id_album',
                    'onField' => 'ID',
                    'joinFields' => array('AllowAlbumView'),
                    'mainTable' => 'sys_albums_objects'
                )
            ),
            'restriction' => array(
                'activeStatus' => array('value'=>'approved', 'field'=>'Status', 'operator'=>'=', 'paramName'=>'status'),
                'owner' => array('value'=>'', 'field'=>'Owner', 'operator'=>'=', 'paramName'=>'userID'),
                'ownerStatus' => array('value'=>array('Rejected', 'Suspended'), 'operator'=>'not in', 'paramName'=>'ownerStatus', 'table'=>'Profiles', 'field'=>'Status'),
                'tag' => array('value'=>'', 'field'=>'Tags', 'operator'=>'against', 'paramName'=>'tag'),
                'category'=> array('value'=>'', 'field'=>'Categories', 'operator'=>'against', 'paramName'=>'categoryUri'),
                'id' => array('value'=>'', 'field'=>'ID', 'operator'=>'in'),
                'allow_view' => array('value'=>'', 'field'=>'AllowAlbumView', 'operator'=>'in', 'table'=> 'sys_albums'),
                'album_status' => array('value'=>'active', 'field'=>'Status', 'operator'=>'=', 'table'=> 'sys_albums'),
                'albumType' => array('value'=>'', 'field'=>'Type', 'operator'=>'=', 'paramName'=>'albumType', 'table'=>'sys_albums'),
            ),
            'paginate' => array('perPage' => 10, 'page' => 1, 'totalNum' => 10, 'totalPages' => 1),
            'sorting' => 'last',
            'view' => 'full',
            'rss' => array(
				'title' => '',
	            'link' => '',
	            'image' => '',
	            'profile' => 0,
	            'fields' => array (
	                'Link' => '',
	                'Title' => 'title',
	                'DateTimeUTS' => 'date',
	                'Desc' => 'desc',
	                'Photo' => '',
	            ),
	        ),
		);
		// favorite config, basic for all media modules
        $this->aAddPartsConfig['favorite'] = array(
            'type' => 'inner',
            'table' => '',
            'mainField' => 'ID',
            'onField' => 'ID',
            'userField' => 'Profile',
            'joinFields' => ''
        );
        $this->aPseud = $this->_getPseud();
		parent::BxBaseSearchResult();
		
		$this->sModuleClass = $sModuleClass;
		$this->oModule = BxDolModule::getInstance($this->sModuleClass);
        $this->oTemplate = $GLOBALS['oSysTemplate'];
        
        $sClassName = $this->oModule->_oConfig->getClassPrefix() . 'Privacy';
        bx_import('Privacy', $this->oModule->_aModule);
        $this->oPrivacy = new $sClassName('sys_albums', 'ID', 'Owner');
		
	}
	
    function getCurrentUrl ($sType, $iId = 0, $sUri = '') {
        $sLink = $this->aConstants['linksTempl'][$sType];
        return BX_DOL_URL_ROOT . $this->oModule->_oConfig->getBaseUri() . str_replace('{uri}', $sUri, $sLink);
    }
	
	function displaySearchUnit ($aData) {
	    $bShort = isset($this->aCurrent['view']) && $this->aCurrent['view'] == 'short' ? true : false;
	    if ($this->oModule->isAdmin($this->oModule->_iProfileId) || is_array($this->aCurrent['restriction']['allow_view']['value']))
            $bVis = true;
        elseif ($this->oPrivacy->check('album_view', $aData['id_album'], $this->oModule->_iProfileId))
            $bVis = true;
        else
            $bVis = false;
	    
        if (!$bVis) {
            $aUnit = array(
               'img_url' => $this->oTemplate->getIconUrl('lock.png'),
               'bx_if:show_title' => array(
                   'condition' => !$bShort,
                   'content' => array(1)
               )
            );
            $sCode = $this->oTemplate->parseHtmlByName('browse_unit_private.html', $aUnit);
        }
        else
            $sCode = $bShort ? $this->getSearchUnitShort($aData) : $this->getSearchUnit($aData); 
		return $sCode;
	}
	
	function getSearchUnitShort ($aData) {
		$sCode = '
			<div class="sys_file_search_unit_short" id="unit_{id}">
				{pic}
			</div>
		';
		$aUnit['id'] = $aData['id'];
		$sFileLink = $this->getCurrentUrl('file', $aData['id'], $aData['uri']);
		// pic
		$aUnit['pic'] = $this->_getSharedThumb($aData['id'], $sFileLink, $aData['Hash']);
		return $this->_transformData($aUnit, $sCode, $this->sCssPref);
	}
	
	function getSearchUnit ($aData) {
		$aUnit = array();
        $aUnit['id'] = $aData['id'];
        $sFileLink = $this->getCurrentUrl('file', $aData['id'], $aData['uri']);
        // pic
        $aUnit['pic'] = $this->_getSharedThumb($aData['id'], $sFileLink, $aData['Hash']);
        // rate
        if (!is_null($this->oRate) && $this->oRate->isEnabled())
        	$aUnit['rate'] = $this->oRate->getJustVotingElement(0, 0, $aData['Rate']);
        
        // size
        $aUnit['size'] = isset($aData['size']) ? $this->getLength($aData['size']) : '';        
        // title
        $aUnit['titleLink'] = $sFileLink;
        $aUnit['title'] = stripslashes($aData['title']);
        
        // when
        $aUnit['when'] = defineTimeInterval($aData['date']);
        
        // from
        $aUnit['fromLink'] = getProfileLink($aData['ownerId']);
        $aUnit['from'] = $aData['ownerName'];
        
        // view
        $aUnit['view'] = $aData['view'];
        return $this->oTemplate->parseHtmlByName('browse_unit.html', $aUnit, array('{', '}'));
	}
	
	function getCurrentAlbum ($sAlbumUri) {
	    $this->sCurrentAlbum = strip_tags($sAlbumUri);
	}

    function getLength ($iTime) {
        $iTime = (int)round($iTime/1000);
    	if ($iTime < 60) {
    		$aLength[1] = '0';
    		$aLength[0] = $iTime;
    	}
    	elseif ($iTime < 3600) {
    		$aLength[1] = (int)($iTime/60);
    		$aLength[0] = $iTime%60;
    	}
    	$sCode = '';
    	for ($i = count($aLength)-1; $i >= 0; $i--) {
    		$sCode .= strlen($aLength[$i]) < 2 ? '0' . $aLength[$i] : $aLength[$i];
    		$sCode .= ':';
    	}
    	return	trim($sCode, ':');
	}
	
    function displayMenu () {
        $aDBTopMenu = $this->getTopMenu(array($this->aCurrent['name'] . '_mode'));
        $aDBBottomMenu = $this->getBottomMenu();
        return array( $aDBTopMenu, $aDBBottomMenu );
    }
    
    function getAlterOrder() {        
        $aSql = array();
        switch ($this->aCurrent['sorting']) {
            case 'popular':
                $aSql['order'] = " ORDER BY `{$this->aCurrent['table']}`.`Views` DESC";
                break;
            case 'album_order':
                $aSql['order'] = " ORDER BY `obj_order` ASC";
                break;
            default:
        }
        return $aSql;
    }
    
	function getTopMenu ($aExclude = array()) {
		$aDBTopMenu = array();
	    $aLinkAddon = $this->getLinkAddByPrams($aExclude);
	    foreach (array('last', 'top') as $sMyMode) {
			  switch ($sMyMode) {
				   case 'last':
				     	$sModeTitle = '_Latest';
				   	 	break;
				   case 'top':
				    	$sModeTitle = '_Top';
				    	break;
			  }
		
		 if (basename( $_SERVER['PHP_SELF'] ) == 'rewrite_name.php' || basename( $_SERVER['PHP_SELF'] ) == 'profile.php')
				$sLink = BX_DOL_URL_ROOT . "profile.php?ID={$this->aCurrent['restriction']['owner']['value']}&";
			else
				$sLink = bx_html_attribute($_SERVER['PHP_SELF']) . "?";
			$sLink .= $this->aCurrent['name'] . "_mode=$sMyMode" . $aLinkAddon['params'] . $aLinkAddon['paginate'] . $aLinkAddon['type'];
			
		  	$aDBTopMenu[$sModeTitle] = array('href' => $sLink, 'dynamic' => true, 'active' => ($sMyMode == $this->aCurrent['sorting']));
		}
		return $aDBTopMenu;
	}
	
    function getLatestFile () {
        $aWhere[] = "1";
        foreach( $this->aCurrent['restriction'] as $sKey => $aValue ) {
            if (isset($aValue['value'])) {
                switch ($sKey) {
                    case 'featured':
                    case 'owner':
                        if ((int)$aValue['value'] != 0) 
                            $aWhere[] = "`{$this->aCurrent['table']}`.`{$aValue['field']}` = '" . (int)$aValue['value'] . "'";
                        break;
                    case 'category':
                    case 'tag':
                        if (strlen($aValue['value']) > 0)
                            $aWhere[] = "MATCH(`{$this->aCurrent['table']}`.`{$aValue['field']}`) AGAINST ('" . trim(process_db_input($aValue['value'], BX_TAGS_STRIP)) . "')";
                        break;
                    case 'allow_view':
                        if (is_array($aValue['value'])) {
                            $sqlJoin = "LEFT JOIN `sys_albums_objects` ON `sys_albums_objects`.`id_object`=`{$this->aCurrent['table']}`.`{$this->aCurrent['ident']}`
                                        LEFT JOIN `sys_albums` ON `sys_albums_objects`.`id_album`=`sys_albums`.`ID`
                            ";
                            $sqlCode = "`AllowAlbumView` IN(";
                            foreach ($aValue['value'] as $sValue)
                                $sqlCode .= "$sValue, ";
                            $aWhere[] = rtrim($sqlCode, ", ") . ')';
                        }
                        break;
                }
            }
        }
        $sqlWhere = "WHERE " . implode( ' AND ', $aWhere ) . " AND `{$this->aCurrent['table']}`.`Status`= 'approved'";
        $sqlQuery = "SELECT `{$this->aCurrent['table']}`.`{$this->aCurrent['ident']}` as `{$this->aCurrent['ident']}` FROM `{$this->aCurrent['table']}` $sqlJoin $sqlWhere ORDER BY `{$this->aCurrent['ident']}` DESC LIMIT 1"; 
        $iFileId = db_value($sqlQuery);
        $sCode = '';
        if ($iFileId != 0) {
            $this->oTemplate->addCss('view.css');
            $aInfo = $this->oModule->_oDb->getFileInfo(array('fileId' => $iFileId));
            $aInfo['favCount'] = $this->oModule->_oDb->getFavoritesCount($iFileId);
            $sCode = $this->oTemplate->getViewFile($aInfo);
        }
        return $sCode;
    }
    
    function _getSharedThumb ($iId, $sFileLink, $sHash = '') {
        $sIdent = strlen($sHash) > 0 ? $sHash : $iId;
    	$aUnit = array(
            'imgUrl' => $this->getImgUrl($sIdent),
            'spacer' => getTemplateIcon('spacer.gif'),
            'fileLink' => $sFileLink,
            'bx_if:admin' => array(
                'condition' => $this->bAdminMode,
                'content' => array('id' => $iId)
            )
        );
        return $this->oModule->_oTemplate->parseHtmlByName('thumb.html', $aUnit);
    }
		
	function displaySearchBox ($sCode, $sPaginate = '', $bAdminBox = false) {
        $sCode = $GLOBALS['oFunctions']->centerContent($sCode, '.sys_file_search_unit') . '<div class="clear_both"></div>';
		$sTitle = _t($this->aCurrent['title']);
	    $sFunc = !$bAdminBox ? 'Content' : 'Admin';
		$sCode = call_user_func('DesignBox' . $sFunc, $sTitle, '<div class="searchContentBlock">' . $sCode . '</div>' .$sPaginate, 1);
	    if (!isset($_GET['searchMode']))
	       $sCode = '<div id="page_block_' . $this->id . '">' . $sCode . '</div>';
	    return $sCode;
	}
	
	function getImgUrl ($iId, $sImgType = 'browse') {
	    $iId = (int)$iId;
	    $sPostFix = isset($this->aConstants['picPostfix'][$sImgType]) ? $this->aConstants['picPostfix'][$sImgType] : $this->aConstants['picPostfix']['browse']; 
	    return $this->aConstants['filesUrl'] . $iId . $sPostFix; 
	}
	
	function getFilesInCatArray ($iId, $sCategory = '') {
	    $this->clearFilters();
	    $this->aCurrent['restriction']['owner']['value'] = $iId;  
        $this->aCurrent['paginate']['perPage'] = 1000;
        $this->aCurrent['join']['category'] = array(
            'type' => 'left',
            'table' => 'sys_categories',
            'mainField' => $this->aCurrent['ident'],
            'onField' => 'ID',
            'joinFields' => array('Category')
        );
        $this->aCurrent['restriction']['category'] = array(
            'value' => $sCategory,
            'field' => 'Category',
            'operator' => '=',
            'table' => 'sys_categories' 
        );
        
        $this->aCurrent['restriction']['type'] = array(
            'value' => $this->aCurrent['name'],
            'field' => 'Type',
            'operator' => '=',
            'table' => 'sys_categories'
        );

        $aFiles = $this->getSearchData(); 
        if (!$aFiles)
            $aFiles = array();
        return $aFiles;
	}
	
	function getFilesInAlbumArray ($iAlbumId, $aLimits = array()) {
	    $this->clearFilters(array('activeStatus'));
	    $this->aCurrent['join']['albumsObjects'] = array(
            'type' => 'left',
            'table' => 'sys_albums_objects',
            'mainField' => 'ID',
            'onField' => 'id_object',
            'joinFields' => array('obj_order')
        );
        $this->aCurrent['sorting'] = 'album_order';
		if ($aLimits['page'])
			$this->aCurrent['paginate']['page'] = (int)$aLimits['page'];
		if (isset($aLimits['per_page']) && $aLimits['per_page'] !== false)
			$this->aCurrent['paginate']['perPage'] = (int)$aLimits['per_page'];
        $this->aCurrent['restriction']['album'] = array(
            'value'=>(int)$iAlbumId, 'field'=>'id_album', 'operator'=>'=', 'paramName'=>'albumId', 'table'=>'sys_albums_objects'
        );
        $aFiles = $this->getSearchData(); 
        if (!$aFiles)
            $aFiles = array();
        return $aFiles;
	}
	
	function getProfileFiles ($iProfId, $aLimits = array()) {
		$this->clearFilters();
		if ($aLimits['page'])
			$this->aCurrent['paginate']['page'] = (int)$aLimits['page'];
		if ($aLimits['per_page'])
			$this->aCurrent['paginate']['perPage'] = $aLimits['per_page'];
	    $this->aCurrent['restriction']['activeStatus']['value'] = 'approved';
		$this->aCurrent['restriction']['owner']['value'] = (int)$iProfId;
        $aFiles = $this->getSearchData(); 
        if (!$aFiles)
            $aFiles = array();
        return $aFiles;
	}
	
	// browse functions    
    function addCustomParts () {
        if (!$this->bCustomParts) {
            $this->bCustomParts = true;
            $sModulePart = $this->getModuleFolder() . '/';
            $this->oTemplate->addLocation($this->aCurrent['name'], BX_DIRECTORY_PATH_MODULES . $sModulePart, BX_DOL_URL_MODULES . $sModulePart);
            $this->oTemplate->addCss(array('search.css'));
            //$this->oTemplate->removeLocation($this->aCurrent['name']);
            return '';
        }
    }
    
    function getModuleFolder () {
        return 'boonex/'.$this->aCurrent['name'];
    }
    
    function getAlbumList ($iPage = 1, $iPerPage = 10, $aCond = array()) {
        $oSet = new BxDolAlbums($this->aCurrent['name']);
        foreach ($this->aCurrent['restriction'] as $sKey => $aParam) {
            if (!empty($aParam['value'])) 
                $aData[$sKey] = $aParam['value'];
        }
        $aData = array_merge($aData, $aCond);
        $iAlbumCount = $oSet->getAlbumCount($aData);
        $this->aCurrent['paginate']['totalAlbumNum'] = $iAlbumCount;
        if ($iAlbumCount > 0) {
            $sCode = $this->addCustomParts();
            $aList = $oSet->getAlbumList($aData, (int)$iPage, (int)$iPerPage);
            $bCheckPrivacy = isset($aData['allow_view']) ? false : true;
            foreach ($aList as $aData)
                $sCode .= $this->displayAlbumUnit($aData, $bCheckPrivacy);
        }
        else
            $sCode = MsgBox(_t('_Empty')); 
        return $sCode;
    }
    
    function getAlbumsBlock ($aSectionParams = array(), $aAlbumParams = array(), $aCustom = array()) {
        $aCustomTmpl = array(
            'caption' => _t('_' . $this->oModule->_oConfig->getMainPrefix() .'_albums'),
            'enable_center' => true,
            'unit_css_class' => '.sys_album_unit',
            'page' => isset($_GET['page']) ? (int)$_GET['page'] : 1,
            'per_page' => isset($_GET['per_page']) ? (int)$_GET['per_page']: (int)$this->oModule->_oConfig->getGlParam('number_albums_home'),
            'simple_paginate' => true,
            'menu_top' => '',
            'menu_bottom' => '',
            'paginate_url' => '',
            'simple_paginate_url' => BX_DOL_URL_ROOT . $this->oModule->_oConfig->getUri() . '/albums/browse'
        );
        $aCustom = array_merge($aCustomTmpl, $aCustom);
        $this->aCurrent['paginate']['perPage'] = $aCustom['per_page'];
        $this->aCurrent['paginate']['page'] = $aCustom['page'];
        
        $this->fillFilters($aSectionParams);
        $sCode = $this->getAlbumList($this->aCurrent['paginate']['page'], $this->aCurrent['paginate']['perPage'], $aAlbumParams);
        if ($this->aCurrent['paginate']['totalAlbumNum'] > 0) {
            if ($aCustom['enable_center'])
                $sCode = $GLOBALS['oFunctions']->centerContent($sCode, $aCustom['unit_css_class']);
            if (empty($aCustom['menu_bottom'])) {
                $aLinkAddon = $this->getLinkAddByPrams(array('r'));
                $oPaginate = new BxDolPaginate(array(
                    'page_url' => $aCustom['paginate_url'],
                    'count' => $this->aCurrent['paginate']['totalAlbumNum'],
                    'per_page' => $this->aCurrent['paginate']['perPage'],
                    'page' => $this->aCurrent['paginate']['page'],
                    'per_page_changer' => true,
                    'page_reloader' => true,
                    'on_change_page' => 'return !loadDynamicBlock({id}, \'' . $aCustom['paginate_url'] . $aLinkAddon['params'] .'&page={page}&per_page={per_page}\');',
                ));
                $aCode['menu_bottom'] = $aCustom['simple_paginate'] ? $oPaginate->getSimplePaginate($aCustom['simple_paginate_url']) : $oPaginate->getPaginate();
            }
            else
                $aCode['menu_bottom'] = $aCustom['menu_bottom'];
        	$aCode['code'] = DesignBoxContent($aCustom['caption'], $sCode);
        }
        $aCode['menu_top'] = $aCustom['menu_top'];
        return array($aCode['code'], $aCode['menu_top'], $aCode['menu_bottom'], '');
    }
    
    function getAlbumCovers ($iAlbumId, $aParams = array()) {
        $iAlbumId = (int)$iAlbumId;
    	return $this->oModule->oAlbums->getAlbumCoverFiles($iAlbumId, array('table'=>$this->aCurrent['table'], 'field'=> 'ID'), array(array('field'=>'Status', 'value'=>'approved')));
    }
    
    function getAlbumCoverUrl (&$aIdent) {
    	return $this->getImgUrl($aIdent['id_object'], 'thumb');
    }
    
    function displayAlbumUnit ($aData, $bCheckPrivacy = true) {
    	if (!$this->bAdminMode && $bCheckPrivacy) {
            if (!$this->oPrivacy->check('album_view', $aData['ID'], $this->oModule->_iProfileId)) {
                $aUnit = array(
                   'img_url' => $this->oTemplate->getIconUrl('lock.png'),
                );
                return $this->oTemplate->parseHtmlByName('album_unit_private.html', $aUnit);
            }
        }
            
        $aUnit['bx_if:editMode'] = array(
            'condition' => $this->bAdminMode,
            'content' => array(
                'id' => $aData['ID'],
                'checked' => $this->sCurrentAlbum == $aData['Uri'] ? 'checked="checked"' : ''
            )
        );
        
        // from
        $aUnit['fromLink'] = getProfileLink($aData['Owner']);
        $aUnit['from'] = getNickName($aData['Owner']);
        
        $aUnit['albumUrl'] = $this->getCurrentUrl('album', $aData['ID'], $aData['Uri']) . '/owner/' . $aUnit['from'];
        // pics
        $aUnit['bx_repeat:units'] = MsgBox(_t('_Empty'));
        if ($aData['ObjCount'] > 0) {
        	$aPics = $this->getAlbumCovers($aData['ID']);
        	if (count($aPics) > 0) {
	            $sSpacer = $this->oTemplate->getIconUrl('spacer.gif'); 
	            foreach ($aPics as $aValue) {
	                $aUnits[] = array(
	                    'bx_if:exist' => array(
	                        'condition' => (int)$aValue['id_object'] > 0,
	                        'content' => array(
	                            'unit' => $this->getAlbumCoverUrl($aValue),
	                            'spacer' => $sSpacer
	                        )
	                    ),
	                );
	            }
	            $aUnit['bx_repeat:units'] = $aUnits;
        	}
	        elseif ($this->oModule->_iProfileId != $aData['Owner']) {
	        	$this->aCurrent['paginate']['totalAlbumNum']--;
            	return '';
	        }
        }
        
        // title
        $aUnit['titleLink'] = stripcslashes($aUnit['albumUrl']);
        $aUnit['title'] = $aData['Caption'];
        
        // when
        $aUnit['when'] = defineTimeInterval($aData['Date']);
        
        // view
        $aUnit['view'] = isset($aData['ObjCount']) ? $aData['ObjCount'] . ' ' . _t($this->aCurrent['title']): '';
        return $this->oTemplate->parseHtmlByName('album_unit.html', $aUnit, array('{','}'));
    }
    
    function serviceGetLength ($iTime) {
        return $this->getLength ($iTime);
    }
    
    function serviceGetFilesInCat ($iId, $sCategory = '') {
        
    }
    
    function serviceGetFilesInAlbum ($iAlbum) {
        
    }
    
    function serviceGetAllProfilePhotos ($iProfileId) {
    	
    }
	
	function serviceGetAlbumPrivacy ($iAlbumId, $iViewer = 0) {
		if (!$iViewer)
			$iViewer = $this->oModule->_iProfileId;
		return $this->oModule->oAlbumPrivacy->check('album_view', (int)$iAlbumId, $iViewer);
	}
    
    function serviceGetProfileAlbumsBlock ($iProfileId, $sSpecUrl = '') {
        $iProfileId   = (int)$iProfileId;
        $sNickName    = getNickName($iProfileId);
        $sSimpleUrl   = BX_DOL_URL_ROOT . $this->oModule->_oConfig->getBaseUri() . 'albums/browse/owner/' . $sNickName; 
        $sPaginateUrl = mb_strlen($sSpecUrl) > 0 ? strip_tags($sSpecUrl) : getProfileLink($iProfileId);
        return $this->getAlbumsBlock(array(), array('owner' => $iProfileId), array('paginate_url' => $sPaginateUrl, 'simple_paginate_url' => $sSimpleUrl));
    }
    
    function serviceGetProfileAlbumFiles ($iProfileId) {
        $iProfileId = (int)$iProfileId;
		$sNickKey = '{nickname}';
        $sNickName = getNickName($iProfileId);
		$sDefAlbumName = $this->oModule->_oConfig->getGlParam('profile_album_name');
		if (strpos($sDefAlbumName, $sNickKey) !== false)
			$sCaption = str_replace($sNickKey, $sNickName, $sDefAlbumName);
		else {
			$sCaption = $sDefAlbumName;
			$this->aCurrent['restriction']['album_owner'] = array(
				'value'=>$iProfileId, 'field'=>'Owner', 'operator'=>'=', 'paramName'=>'albumOwner', 'table'=>'sys_albums'
			);
		}
        $sUri = uriFilter($sCaption);
        $this->aCurrent['sorting'] = 'album_order';
        $this->aCurrent['restriction']['album'] = array(
            'value'=>$sUri, 'field'=>'Uri', 'operator'=>'=', 'paramName'=>'albumUri', 'table'=>'sys_albums'
        );
        $aFiles = $this->getSearchData();
        if (is_array($aFiles)) {
            foreach ($aFiles as $iKey => $aData)
                $aFiles[$iKey]['file'] = $this->getImgUrl($aData['id'], 'icon');
        }
        else
            $aFiles = array();
        return $aFiles;
    }
    
    function checkMemAction ($iFileOwner, $sAction = 'view') {
    	$iFileOwner = (int)$iFileOwner;
    	$sAction = clear_xss($sAction);
    	if ($this->oModule->isAdmin($this->oModule->_iProfileId) || $iFileOwner == $this->oModule->_iProfileId) return true;
        $this->oModule->_defineActions();
        $aCheck = checkAction($this->oModule->_iProfileId, $this->oModule->_defineActionName($sAction));
        if ($aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED)
            return false;
        return true;
    }
    
    function getRssUnitLink (&$a) {
        return BX_DOL_URL_ROOT . $this->oModule->_oConfig->getBaseUri() . 'view/' . $a['uri'];
    }
}

?>