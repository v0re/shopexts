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

class BxFilesSearch extends BxTemplSearchResult {
    var $oModule;
    var $oTemplate;
    var $bAdminMode = false;
    function BxFilesSearch ($sParamName = '', $sParamValue = '', $sParamValue1 = '', $sParamValue2 = '') {
        parent::BxTemplSearchResult();
        // main settings
        $this->aCurrent = array(
            'name' => 'bx_files',
            'title' => '_bx_files',
            'table' => 'bx_files_main',
            'ownFields' => array('ID', 'Title', 'Uri', 'Desc', 'Date', 'Size', 'Ext', 'Views', 'Rate', 'RateCount', 'Type'),
            'searchFields' => array('Title', 'Tags', 'Desc', 'Categories'),
            'join' => array(
                'profile' => array(
                    'type' => 'left',
                    'table' => 'Profiles',
                    'mainField' => 'Owner',
                    'onField' => 'ID',
                    'joinFields' => array('NickName')
                ),
                'icon' => array(
                    'type' => 'left',
                    'table' => 'bx_files_types',
                    'mainField' => 'Type',
                    'onField' => 'Type',
                    'joinFields' => array('Icon')
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
                'activeStatus' => array('value'=>'approved', 'field'=>'Status', 'operator'=>'=', 'paramName' => 'status'),
                'owner' => array('value'=>'', 'field'=>'NickName', 'operator'=>'=', 'paramName'=>'ownerName', 'table'=>'Profiles'),
                'ownerStatus' => array('value'=>array('Rejected', 'Suspended'), 'operator'=>'not in', 'paramName'=>'ownerStatus', 'table'=>'Profiles', 'field'=>'Status'),
                'tag' => array('value'=>'', 'field'=>'Tags', 'operator'=>'against', 'paramName'=>'tag'),
                'category' => array('value'=>'', 'field'=>'Categories', 'operator'=>'against', 'paramName'=>'categoryUri'),
                'id' => array('value' => '', 'field' => 'ID', 'operator' => 'in'),
                'allow_view' => array('value'=>'', 'field'=>'AllowAlbumView', 'operator'=>'in', 'table'=> 'sys_albums'),
                'album_status' => array('value'=>'active', 'field'=>'Status', 'operator'=>'=', 'table'=> 'sys_albums'),
                'albumType' => array('value'=>'', 'field'=>'Type', 'operator'=>'=', 'paramName'=>'albumType', 'table'=>'sys_albums'),
            ),
            'paginate' => array('perPage' => 10, 'page' => 1, 'totalNum' => 10, 'totalPages' => 1),
            'sorting' => 'last',
            'view' => 'full',
            'ident' => 'ID',
            'rss' => array( 
                'title' => _t('_bx_files'),
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
        
        // redeclaration some unique fav fields
		$this->aAddPartsConfig['favorite'] = array(
            'type' => 'inner',
            'table' => 'bx_files_favorites',
            'mainField' => 'ID',
            'onField' => 'ID',
            'userField' => 'Profile',
            'joinFields' => ''
        );
                
        $this->oModule = BxDolModule::getInstance('BxFilesModule');
        $this->oTemplate = $this->oModule->_oTemplate;
        $this->oModule->_oTemplate->addCss('search.css');
        $this->aConstants['filesUrl'] = $this->oModule->_oConfig->getFilesUrl();
        $this->aConstants['filesDir'] = $this->oModule->_oConfig->getFilesPath();
        
        //permalinks generation
        $this->aConstants['linksTempl'] = array(
            'home' => 'home',
            'file' => 'view/{uri}',
            'category' => 'browse/category/{uri}',
            'browseAll' => 'browse/',
            'browseUserAll' => 'albums/browse/owner/{uri}',
            'browseAllTop' => 'browse/top',
            'tag' => 'browse/tag/{uri}',
            'album' => 'browse/album/{uri}',
            'add' => 'browse/my/add'
        );
        
        $this->aCurrent['restriction']['albumType']['value'] = $this->aCurrent['name'];
        
        //additional modes for browse
        switch ($sParamName) {
            case 'calendar':
                $this->aCurrent['restriction']['calendar-min'] = array('value' => "UNIX_TIMESTAMP('{$sParamValue}-{$sParamValue1}-{$sParamValue2} 00:00:00')", 'field' => 'Date', 'operator' => '>=', 'no_quote_value' => true);
                $this->aCurrent['restriction']['calendar-max'] = array('value' => "UNIX_TIMESTAMP('{$sParamValue}-{$sParamValue1}-{$sParamValue2} 23:59:59')", 'field' => 'Date', 'operator' => '<=', 'no_quote_value' => true);
                break;
            case 'top':
                $this->aCurrent['sorting'] = 'top';
                break;
            case 'popular':
                $this->aCurrent['sorting'] = 'popular';
                break;
            case 'featured':
                $this->aCurrent['restriction']['featured'] = array(
                    'value'=>'1', 'field'=>'Featured', 'operator'=>'=', 'paramName'=>'bx_files_mode'
                ); 
                break;
			case 'favorited':
				if (isset($this->aAddPartsConfig['favorite']) && !empty($this->aAddPartsConfig['favorite']) && getLoggedId() != 0) {
                    $this->aCurrent['join']['favorite'] = $this->aAddPartsConfig['favorite']; 
                    $this->aCurrent['restriction']['fav'] = array(
                        'value' => getLoggedId(),
                        'field' => $this->aAddPartsConfig['favorite']['userField'],
                        'operator' => '=',
                        'table' => $this->aAddPartsConfig['favorite']['table']
                    );
                }
                break;
            case 'album':
                $this->aCurrent['sorting'] = 'album_order';
                $this->aCurrent['restriction']['album'] = array(
                    'value'=>'', 'field'=>'Uri', 'operator'=>'=', 'paramName'=>'albumUri', 'table'=>'sys_albums'
                );
                if ($sParamValue1 == 'owner' && strlen($sParamValue2) > 0)
                    $this->aCurrent['restriction']['owner']['value'] = $sParamValue2;
                break;    
        }
    }
    
    function getLength ($sSize) {
        return $sSize;
    }
    
    function _getPseud () {
        return array(    
            'id' => 'ID',
            'title' => 'Title',
            'date' => 'Date',
            'size' => 'Size',
            'uri' => 'Uri',
            'view' => 'Views',
            'ownerId' => 'Owner',
            'ownerName' => 'NickName',
            'desc' => 'Desc'
        );
    }
        
    function getAlterOrder () {        
        if ($this->aCurrent['sorting'] == 'popular') {
            $aSql = array();
            $aSql['order'] = " ORDER BY `DownloadsCount` DESC";
            return $aSql;
        }
        return array();
    }
    
    function displaySearchUnit ($aData) {
        $bShort = isset($this->aCurrent['view']) && $this->aCurrent['view'] == 'short' ? true : false;
        if ($this->oModule->isAdmin($this->oModule->_iProfileId) || is_array($this->aCurrent['restriction']['allow_view']['value']))
            $bVis = true;
        elseif ($this->oModule->oAlbumPrivacy->check('album_view', $aData['id_album'], $this->oModule->_iProfileId))
            $bVis = true;
        else
            $bVis = false;
        
        if (!$bVis) {
            $aUnit = array(
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
    
    function getSearchUnit ($aData) {
        $aUnit['unitClass'] = $this->aCurrent['name'];
        $aUnit['bx_if:admin'] = array(
            'condition' => $this->bAdminMode,
            'content' => array('id' => $aData['id'])
        );
        // pic
        $sPicName = empty($aData['Icon']) ? 'default.png': $aData['Icon'];
        $aUnit['pic'] = $this->oModule->_oTemplate->getIconUrl($sPicName);
        $aUnit['spacer'] = $this->oModule->_oTemplate->getIconUrl('spacer.gif');
        // rate
        if (!is_null($this->oRate) && $this->oRate->isEnabled())
            $aUnit['rate'] = $this->oRate->getJustVotingElement(0, 0, $aData['Rate']);

        // size
        //$aUnit['size'] = isset($aData['size']) ? $this->getLength($aData['size']) : '';
        $aUnit['size'] = '';
        
        // title
        $aUnit['titleLink'] = $this->getCurrentUrl('file', $aData['id'], $aData['uri']);
        $aUnit['title'] = stripslashes($aData['title']);        
        
        // from
        $aUnit['fromLink'] = getProfileLink($aData['ownerId']);
        $aUnit['from'] = $aData['ownerName'];
        
        // when
        $aUnit['when'] = defineTimeInterval($aData['date']);
        // view
        $aUnit['view'] = $aData['view'];
        // desc
        $aUnit['desc'] = stripslashes($aData['desc']);
        return $this->oModule->_oTemplate->parseHtmlByName('browse_unit.html', $aUnit, array('{','}'));
    }
    
    function getSearchUnitShort ($aData) {
        $aUnit = array();
        $aUnit['unitClass'] = $this->aCurrent['name'];
        // title
        $aUnit['titleLink'] = $this->getCurrentUrl('file', $aData['id'], $aData['uri']);;
        $aUnit['title'] = $aData['title'];
        
        // from
        $aUnit['fromLink'] = getProfileLink($aData['ownerId']);
        $aUnit['from'] = $aData['ownerName'];
        
        // when
        $aUnit['when'] = defineTimeInterval($aData['date']);
        
        // pic
        $sPicName = is_null($aData['Icon']) ? 'default.png': $aData['Icon'];
        $aUnit['pic'] = $this->oModule->_oTemplate->getIconUrl($sPicName);
        
        $aUnit['id'] = $aData['id'];
        return $this->oModule->_oTemplate->parseHtmlByName('browse_unit_short.html', $aUnit, array('{','}'));;
    }
    
    function setSorting () {
        $this->aCurrent['sorting'] = isset($_GET[$this->aCurrent['name'].'_mode']) ? $_GET[$this->aCurrent['name'].'_mode'] : $this->aCurrent['sorting'];
    }
    
    function getTopMenu ($aExclude = array()) {        
        $aDBTopMenu = array();
        $aLinkAddon = $this->getLinkAddByPrams($aExclude);
        foreach (array( 'last', 'popular' ) as $sMyMode) {
            switch ($sMyMode) {
               case 'last':
                    $sModeTitle = '_Latest';
                    break;
               case 'popular':
                    $sModeTitle = '_Popular';
                    break;
              }
              $sLink = bx_html_attribute($_SERVER['PHP_SELF']) . "?bx_files_mode=" . $sMyMode;
              $aDBTopMenu[_t($sModeTitle)] = array('href' => $sLink, 'dynamic' => true, 'active' => ($sMyMode == $this->aCurrent['sorting']));
        }
        return $aDBTopMenu;
    }
        
    function getCurrentUrl ($sType, $iId = 0, $sUri = '') {
        $sLink = $this->aConstants['linksTempl'][$sType];
        return BX_DOL_URL_ROOT . $this->oModule->_oConfig->getBaseUri() . str_replace('{uri}', $sUri, $sLink);
    }

    function getAlbumList ($iPage = 1, $iPerPage = 10, $aCond = array()) {
        $oSet = new BxDolAlbums($this->aCurrent['name']);
        foreach ($this->aCurrent['restriction'] as $sKey => $aParam)
            $aData[$sKey] = $aParam['value'];
        $aData = array_merge($aData, $aCond);
        $iAlbumCount = $oSet->getAlbumCount($aData);
        if ($iAlbumCount > 0) {
            $this->aCurrent['paginate']['totalAlbumNum'] = $iAlbumCount;
            $sCode = $this->addCustomParts();
            $aList = $oSet->getAlbumList($aData, (int)$iPage, (int)$iPerPage);
            foreach ($aList as $iKey => $aData)
                $sCode .= $this->displayAlbumUnit($aData);
        }
        else
            $sCode = MsgBox(_t('_Empty')); 
        return $sCode;
    }
    
    function displayAlbumUnit ($aData, $bCheckPrivacy = true) {
        if (!$this->bAdminMode && $bCheckPrivacy) {
            if (!$this->oModule->oAlbumPrivacy->check('album_view', $aData['ID'], $this->oModule->_iProfileId)) {
                $aUnit = array(
                   'img_url' => $this->oTemplate->getIconUrl('lock.png'),
                );
                return $this->oTemplate->parseHtmlByName('album_unit_private.html', $aUnit);
            }
        }
        if (!$this->bAdminMode && $bCheckPrivacy) {
            if (!$this->oModule->oAlbumPrivacy->check('album_view', $aData['ID'], $this->oModule->_iProfileId)) {
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
        
        // pic
        $aUnit['spacer'] = $this->oTemplate->getIconUrl('spacer.gif'); 
        
        // cover
        $aUnit['pic'] = $this->oTemplate->getIconUrl('folder.png');
        
        // title
        $aUnit['titleLink'] = $aUnit['albumUrl'];
        $aUnit['title'] = $aData['Caption'];
        
        // when
        $aUnit['when'] = defineTimeInterval($aData['Date']);
        
        // view
        $aUnit['view'] = isset($aData['ObjCount']) ? $aData['ObjCount'] . ' ' . _t($this->aCurrent['title']): '';
        return $this->oTemplate->parseHtmlByName('album_unit.html', $aUnit, array('{','}'));
    }
    
    function getImgUrl ($iId, $sImgType = 'browse') {
        $iId = (int)$iId;
        $sPostFix = isset($this->aConstants['picPostfix'][$sImgType]) ? $this->aConstants['picPostfix'][$sImgType] : $this->aConstants['picPostfix']['browse']; 
        return $this->aConstants['filesUrl'] . $iId . $sPostFix; 
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
        		$aCode['code'] = DesignBoxContent($aCustom['caption'], $sCode);
            }
            else
                $aCode['menu_bottom'] = $aCustom['menu_bottom'];
        }
        $aCode['menu_top'] = $aCustom['menu_top'];
        return array($aCode['code'], $aCode['menu_top'], $aCode['menu_bottom'], '');
    }
    
    //services
    function serviceGetFilesInCat ($iId, $sCategory = '') {
        $this->aCurrent['paginate']['perPage'] = 1000;
        $this->aCurrent['join']['category'] = array(
            'type' => 'left',
            'table' => 'sys_categories',
            'mainField' => 'ID',
            'onField' => 'ID',
            'joinFields' => array('Category')
        );
        
        $this->aCurrent['restriction']['ownerId'] = array(
            'value' => $iId ? $iId : '',
            'field' => 'Owner',
            'operator' => '=',
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
        foreach ($aFiles as $k => $aRow) {
            $sIcon = !empty($aRow['Icon']) ? $aRow['Icon'] : 'default.png';
            $aFiles[$k]['icon'] = $this->oModule->_oTemplate->getIconUrl($sIcon);
            $aFiles[$k]['url'] = $this->getCurrentUrl('file', $aRow['ID'], $aRow['uri']);
        }        
        return $aFiles;
    }
   
	function serviceGetAlbumPrivacy ($iAlbumId, $iViewer = 0) {
		if (!$iViewer)
			$iViewer = $this->oModule->_iProfileId;
		return $this->oModule->oAlbumPrivacy->check('album_view', (int)$iAlbumId, $iViewer);
	}
 
    function serviceGetFilesInAlbum ($iAlbumId, $isCheckPrivacy = false, $iViewer = 0) {
		if (!$iViewer)
			$iViewer = $this->oModule->_iProfileId;
		if ($isCheckPrivacy && !$this->oModule->oAlbumPrivacy->check('album_view', (int)$iAlbumId, $iViewer))
			return array();

        $this->aCurrent['paginate']['perPage'] = 1000;
        $this->aCurrent['join']['albumsObjects'] = array(
            'type' => 'left',
            'table' => 'sys_albums_objects',
            'mainField' => 'ID',
            'onField' => 'id_object',
            'joinFields' => array('obj_order')
        );
        $this->aCurrent['sorting'] = 'album_order';
        $this->aCurrent['restriction']['album'] = array(
            'value'=>(int)$iAlbumId, 'field'=>'id_album', 'operator'=>'=', 'paramName'=>'albumId', 'table'=>'sys_albums_objects'
        );
        $aFiles = $this->getSearchData(); 
        if (!$aFiles)
            $aFiles = array();
        foreach ($aFiles as $k => $aRow) {
            $sIcon = !empty($aRow['Icon']) ? $aRow['Icon'] : 'default.png';
            $aFiles[$k]['icon'] = $this->oModule->_oTemplate->getIconUrl($sIcon);
            $aFiles[$k]['url'] = $this->getCurrentUrl('file', $aRow['ID'], $aRow['uri']);
            $aFiles[$k]['mime_type'] = $aRow['Type'];
            $aFiles[$k]['path'] = $this->aConstants['filesDir'] . $aRow['ID'] . '_' . sha1($aRow['Date']);
        }
        return $aFiles;
    }
    
    function serviceGetFileArray ($iId) {
    	$iId = (int)$iId;
    	$sqlQuery = "SELECT a.`ID` as `id`,
							a.`Title` as `title`,
							a.`Desc` as `desc`,
							a.`Uri` as `uri`,
							a.`Owner` as `owner`,
							a.`Date` as `date`,
							a.`Ext`,
							a.`Type`,
							a.`Rate`,
							b.`id_album`,
							d.`Icon`
						FROM `{$this->aCurrent['table']}` as a
						LEFT JOIN `sys_albums_objects` as b ON b.`id_object` = a.`ID`
						LEFT JOIN `sys_albums` as c ON c.`ID` = b.`id_album`
						LEFT JOIN `bx_files_types` as d ON d.`Type` = a.`Type`
						WHERE a.`ID`='$iId' AND a.`Status`='approved' AND c.`Type`='{$this->aCurrent['name']}'";
    	$aData = db_arr($sqlQuery);
    	if (!$aData)
            return array();
        $sIcon = !empty($aData['Icon']) ? $aData['Icon'] : 'default.png';
        $sFile = $aData['id'];
		if (strlen($aData['Ext']) > 0)
			$sFile .= '_' . sha1($aData['date']);
        $aInfo = array(
        	'file' => $this->oModule->_oTemplate->getIconUrl($sIcon),
            'title' => $aData['title'],
            'owner' => $aData['owner'], 
            'description' => $aData['desc'],
            'url' => $this->getCurrentUrl('file', $iId, $aData['uri']),
            'date' => $aData['date'],
            'rate' => $aData['Rate'],
            'path' => $this->aConstants['filesDir'] . $sFile,
            'extension' => $aData['Ext'],
            'mime_type' => $aData['Type'],
            'album_id' => $aData['id_album']
        );
        return empty($aInfo['file']) ? array() : $aInfo;
    }
    
    function serviceGetFilesBlock ($aParams = array(), $aCustom = array(), $sLink = '') {
        $aCode = $this->getBrowseBlock($aParams, $aCustom, $sLink, false);
        if ($this->aCurrent['paginate']['totalNum'] > 0)
            return array($aCode['code'], $aCode['menu_top'], $aCode['menu_bottom'], $aCode['wrapper']);
    }
    
    function serviceGetFilePath ($iFile) {
        $iFile = (int)$iFile;
        $aInfo = $this->oModule->_oDb->getFileInfo(array('fileId'=>$iFile), true, array('medID', 'medExt'));
        return $this->aConstants['filesDir'] . $aInfo['medID'] . '.' . $aInfo['medExt']; 
    }
    
    function serviceGetProfileAlbumsBlock ($iProfileId, $sSpecUrl = '') {
        $iProfileId   = (int)$iProfileId;
        $sNickName    = getNickName($iProfileId);
        $sSimpleUrl   = BX_DOL_URL_ROOT . $this->oModule->_oConfig->getBaseUri() . 'albums/browse/owner/' . $sNickName; 
        $sPaginateUrl = mb_strlen($sSpecUrl) > 0 ? strip_tags($sSpecUrl) : getProfileLink($iProfileId);
        return $this->getAlbumsBlock(array('owner' => $iProfileId), array(), array('paginate_url' => $sPaginateUrl, 'simple_paginate_url' => $sSimpleUrl));
    }
    
    function serviceGetWallPost($aEvent) {
        $aOwner = db_assoc_arr("SELECT `ID` AS `id`, `NickName` AS `username` FROM `Profiles` WHERE `ID`='" . (int)$aEvent['owner_id'] . "' LIMIT 1");
	    $aFile = $this->serviceGetFileArray($aEvent['object_id'], 'browse');
	    if(empty($aOwner) || empty($aFile))
            return "";

        $sCss = "";
        if($aEvent['js_mode'])
            $sCss = $this->oTemplate->addCss('wall_post.css', true);
        else 
            $this->oTemplate->addCss('wall_post.css');
        
        $sAddedNewTxt = _t('_bx_files_wall_added_new');
        if(!$this->oModule->oAlbumPrivacy->check('album_view', $aFile['album_id'], $this->oModule->_iProfileId)) {
        	$sTxt = _t('_bx_files_wall_files_private');
        	$aOut = array(
        		'title' => $aOwner['username'] . ' ' . $sAddedNewTxt . ' ' . $sTxt,
        		'content' => $sCss . $this->oTemplate->parseHtmlByName('wall_post_private.html', array(
	                'cpt_user_name' => $aOwner['username'],
	                'cpt_added_new' => $sAddedNewTxt . ' ' . $sTxt,
	                'post_id' => $aEvent['id']
	            ))
        	);
        }
        else {
	        $sTxt = _t('_bx_files_wall_file');
	        $iSize = (int)$this->oModule->_oConfig->getGlParam('browse_width');
	        $aOut = array(
	            'title' => $aOwner['username'] . ' ' . $sAddedNewTxt . ' ' . $sTxt,
	            'description' => $aFile['description'],
	            'content' => $sCss . $this->oTemplate->parseHtmlByName('wall_post.html', array(
	                'cpt_user_name' => $aOwner['username'],
	                'cpt_added_new' => $sAddedNewTxt,
	                'cpt_file_url' => $aFile['url'],
	                'cpt_file' => $sTxt,
	                'cnt_file_width' => $iSize + 4,
	                'cnt_file_height' => $iSize + 4,
	                'cnt_file_url' => $aFile['file'],
	                'cnt_file_title' => $aFile['title'],
	                'post_id' => $aEvent['id']
	            ))
        	);
        }
        return $aOut;
    }
    
    function getRssUnitLink (&$a) {
        return BX_DOL_URL_ROOT . $this->oModule->_oConfig->getBaseUri() . 'view/' . $a['uri'];
    }
}

?>