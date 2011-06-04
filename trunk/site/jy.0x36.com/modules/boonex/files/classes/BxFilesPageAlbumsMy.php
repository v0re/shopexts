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

require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolPageView.php');
require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolAlbums.php');
require_once('BxFilesPrivacy.php');
require_once('BxFilesSearch.php');

class BxFilesPageAlbumsMy extends BxDolPageView {
    var $oTemplate;
    var $oConfig;
    var $oDb;
    var $oSearch;
    var $iOwnerId;
    var $aAddParams;
    
    var $oAlbum;
    
    var $aCurrentBlocks;
    var $aSystemBlocks = array(
        'main' => array(
            'blocks' => array('adminShort', 'my'),
            'level' => 0
        ),
        'add' => array(
            'blocks' => array('add', 'my'),
            'level' => 0
        ),
        'manage' => array(
            'blocks' => array('adminFull', 'my'),
            'level' => 0
        ),
        'disapproved' => array(
            'blocks' => array('adminFullDisapproved', 'my'),
            'level' => 0
        ),
        'main_objects' => array(
            'blocks' => array('adminAlbumShort', 'albumObjects'),
            'level' => 1,
            'link' => 'browse/album/'
        ),
        'edit' => array(
            'blocks' => array('edit', 'albumObjects'),
            'level' => 1
        ),
        'organize' => array(
            'blocks' => array('organize', 'albumObjects'),
            'level' => 1
        ),
        'add_objects' => array(
            'blocks' => array('addObjects', 'albumObjects'),
            'level' => 1
        ),
        'manage_objects' => array(
            'blocks' => array('manageObjects', 'albumObjects'),
            'level' => 1
        ),
        'manage_objects_disapproved' => array(
            'blocks' => array('manageObjectsDisapproved', 'albumObjects'),
            'level' => 1
        ),
        'manage_objects_pending' => array(
            'blocks' => array('manageObjectsPending', 'albumObjects'),
            'level' => 1
        ),
    );
    
    function BxFilesPageAlbumsMy (&$oShared, $iOwnerId, $aParams = array()) {
        parent::BxDolPageView('bx_files_albums_my');
        $this->oTemplate = $oShared->_oTemplate;
        $this->oConfig = $oShared->_oConfig;
        $this->oDb = $oShared->_oDb;
        $this->iOwnerId = $iOwnerId;
        $this->aAddParams = $aParams;
        $this->oSearch = new BxFilesSearch('album', $this->aAddParams[1], 'owner', getNickName($this->iOwnerId));
        $this->oAlbum = new BxDolAlbums('bx_files', $this->iOwnerId);
        
        if (isset($this->aSystemBlocks[$this->aAddParams[0]]))
           $this->aCurrentBlocks = $this->aSystemBlocks[$this->aAddParams[0]];
        else
           $this->aCurrentBlocks = $this->aSystemBlocks['main'];
        $this->oTemplate->addCss('my.css');
        
        $this->oSearch->aCurrent['restriction']['ownerId'] = array(
            'value' => $this->iOwnerId,
            'field' => 'Owner',
            'operator' => '=',
            'paramName' => 'ownerId'
        );
        $sCaption = str_replace('{nickname}', getNickName($this->iOwnerId), $this->oConfig->getGlParam('profile_album_name'));
        $aOwnerDefAlbumInfo = $this->oAlbum->getAlbumInfo(array('fileUri'=>uriFilter($sCaption), 'owner'=>$this->iOwnerId));
        if (!is_array($aOwnerDefAlbumInfo) || empty($aOwnerDefAlbumInfo)) {
            $aData = array(
                'caption' => $sCaption,
                'location' => _t('_bx_' . $this->oConfig->getUri() . '_undefined'),
                'owner' => $this->iOwnerId,
                'AllowAlbumView' => BX_DOL_PG_ALL,
            );
            $this->oAlbum->addAlbum($aData, false);
        }
    }
    
    // constant block
    function getBlockCode_my ($iBoxId) {
        if (!in_array('my', $this->aCurrentBlocks['blocks']))
            return '';
        $this->oSearch->clearFilters(array('activeStatus', 'allow_view', 'album_status'), array('albumsObjects', 'albums'));
        $this->oSearch->bAdminMode = false;
        $this->oSearch->aCurrent['view'] = 'full';
        
        $this->oSearch->aCurrent['restriction']['activeStatus']['value'] = 'approved';
        $iPerPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : (int)$this->oConfig->getGlParam('number_albums_home');
        $iPage = isset($_GET['page']) ? (int)$_GET['page'] : $this->oSearch->aCurrent['paginate']['page'];
        $sCode = $this->oSearch->getAlbumList($iPage, $iPerPage, array('owner'=>$this->iOwnerId, 'show_empty' => true));
        if ($this->oSearch->aCurrent['paginate']['totalAlbumNum'] > 0) {
	        $aLinkAddon = $this->oSearch->getLinkAddByPrams();
            $sParamsDevider = $this -> oConfig -> isPermalinkEnabled
	            ? '?'
	            : '';

	        $sLink  = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'albums/my' . $sParamsDevider;
	        $oPaginate = new BxDolPaginate(array(
	            'page_url' => $sLink,
	            'count' => $this->oSearch->aCurrent['paginate']['totalAlbumNum'],
	            'per_page' => $iPerPage,
	            'page' => $iPage,
	            'per_page_changer' => true,
	            'page_reloader' => true,
	            'on_change_page' => 'return !loadDynamicBlock(' . $iBoxId . ', \'' . $sLink . $aLinkAddon['params'] . '&page={page}&per_page={per_page}\');',
                'on_change_per_page' => 'return !loadDynamicBlock(' . $iBoxId . ', \'' . $sLink . $aLinkAddon['params'] . '&page=1&per_page=\' + this.value);'
	        ));
	        $sPaginate = $oPaginate->getSimplePaginate(BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'albums/browse/owner/' . getNickName($this->iOwnerId));
        	$sCode = $GLOBALS['oFunctions']->centerContent($sCode, '.sys_album_unit') . $sPaginate;
        }
        return $sCode;
    }
    
    function getBlockCode_adminShort () {
        if (in_array('adminShort', $this->aCurrentBlocks['blocks'])) {
            $iNumber = $this->oAlbum->getAlbumCount(array('owner' => $this->iOwnerId, 'show_empty' => true));
            return array($this->oTemplate->getAdminAlbumShort($iNumber), $this->getTopMenu('main'), array());
        }    
    }
    
    function getBlockCode_adminAlbumShort () {
        if (in_array('adminAlbumShort', $this->aCurrentBlocks['blocks'])) {
            $iNumber = $this->oAlbum->getObjCount(array('fileUri' => $this->aAddParams[1], 'owner' => $this->iOwnerId));
            return array($this->oTemplate->getAdminShort($iNumber, $this->aAddParams[1], $this->aAddParams[3]), $this->getTopMenu('main_objects'), array());
        }
    }
    
    function getBlockCode_adminFull ($iBoxId) {
        if (in_array('adminFull', $this->aCurrentBlocks['blocks'])) {
            return array($this->getAdminPart(array(), array('section'=>'manage', 'page_block_id' => $iBoxId)), $this->getTopMenu('manage'), array(), '');
        }
    }
    
    function getBlockCode_adminFullDisapproved ($iBoxId) {
        if (in_array('adminFullDisapproved', $this->aCurrentBlocks['blocks']))
            return array($this->getAdminPart(array('status' => 'passive'), array('section'=>'manage', 'page_block_id' => $iBoxId)), $this->getTopMenu('disapproved'), array(), '');
    }
    
    function getBlockCode_add () {
        if (!in_array('add', $this->aCurrentBlocks['blocks']))
            return '';
                    
        $oAlbumPrivacy = new BxFilesPrivacy('sys_albums', 'ID', 'Owner');
        $aPrivFieldView = $oAlbumPrivacy->getGroupChooser($this->iOwnerId, $this->oConfig->getUri(), 'album_view');
        $aForm = $this->oTemplate->getAlbumFormAddArray(array('allow_view' => $aPrivFieldView));
        $oForm = new BxTemplFormView($aForm);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid()) {
            $aFields = array('caption', 'location', 'description', 'AllowAlbumView', 'owner');
            $aData = array();
            foreach ($aFields as $sValue) {
                if (isset($_POST[$sValue]))
                    $aData[$sValue] = $_POST[$sValue];
            }
            if ($this->oAlbum->addAlbum($aData))
                $sCode = MsgBox(_t('_bx_' . $this->oConfig->getUri() . '_album_save_success'));
        }    
        else
            $sCode = $oForm->getCode();
        return array($sCode, $this->getTopMenu('add'));
    }
    
    function getBlockCode_addObjects ($iBoxId) {
        if (!in_array('addObjects', $this->aCurrentBlocks['blocks']))
            return '';
        if (!$this->oSearch->oModule->isAllowedAdd()) {
            $sCode = MsgBox(_t('_' . $this->oConfig->getMainPrefix() . '_access_denied'));
            $sSubMenu = '';
        }
        else {  
            require_once('BxFilesUploader.php');
            $sLink = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'albums/my/add_objects/' . $this->aAddParams[1] . '/'. $this->aAddParams[2] . '/' . $this->aAddParams[3];
            $aMenu = $this->oConfig->getUploaderSwitcher($sLink);
            $sSubMenu = $this->oTemplate->getExtraSwitcher($aMenu, '_' . $this->oConfig->getMainPrefix() . '_choose_uploader', $iBoxId); 
            $oUploader = new BxFilesUploader();
            $sCode = '<div class="bx_sys_default_padding">' . $oUploader->GenMainAddFilesForm(array('album' => $this->aAddParams[1])) . '</div>';
        }
        return array($sSubMenu . $sCode, $this->getTopMenu('add_objects'), '', '');
    }
    
    function getBlockCode_manageObjects ($iBoxId) {
        if (in_array('manageObjects', $this->aCurrentBlocks['blocks'])) {
            return array($this->getAdminObjectPart(array('activeStatus'=>'approved'), $iBoxId, true), $this->getTopMenu('manage_objects'), array(), '');
        }
    }
    
    function getBlockCode_manageObjectsDisapproved ($iBoxId) {
        if (in_array('manageObjectsDisapproved', $this->aCurrentBlocks['blocks'])) {
            return array($this->getAdminObjectPart(array('activeStatus'=>'disapproved'), $iBoxId), $this->getTopMenu('manage_objects_disapproved'), array(), '');
        }
    }
    
    function getBlockCode_manageObjectsPending ($iBoxId) {
        if (in_array('manageObjectsPending', $this->aCurrentBlocks['blocks'])) {
            return array($this->getAdminObjectPart(array('activeStatus'=>'pending'), $iBoxId), $this->getTopMenu('manage_objects_pending'), array(), '');
        }
    }
    
    function getBlockCode_edit () {
        if (!in_array('edit', $this->aCurrentBlocks['blocks']))
            return '';
        
        $aInfo = $this->oAlbum->getAlbumInfo(array('fileUri' => $this->aAddParams[1], 'owner' => $this->iOwnerId));
        if ($aInfo['Owner'] != $this->iOwnerId)
            $sCode = MsgBox(_t('_Access denied'));

        $oAlbumPrivacy = new BxFilesPrivacy('sys_albums', 'ID', 'Owner');
        $aPrivFieldView = $oAlbumPrivacy->getGroupChooser($this->iOwnerId, $this->oConfig->getUri(), 'album_view');
        $aPrivFieldView['value'] = $aInfo['AllowAlbumView'];
        
        $aReInputs = array(
        	'title' => array(
                'name' => 'Caption',
                'value' => $aInfo['Caption']
            ),
            'location' => array(
                'name' => 'Location',
                'value' => $aInfo['Location']
            ),
            'description' => array(
                'name' => 'Description',
                'value' => $aInfo['Description']
            ),
            'allow_view' => $aPrivFieldView,
            'uri' => array(
                'type' => 'hidden',
                'name' => 'Uri',
                'value' => $this->aAddParams[1],
            ),
        );
        
        $aReForm = array(
        	'id' => $this->oConfig->getMainPrefix() . '_upload_form',
            'method' => 'post',
            'action' => $this->oConfig->getBaseUri().'albums/my/edit/' . strip_tags($this->aAddParams[1] . '/' . strip_tags($this->aAddParams[2]) . '/' . strip_tags($this->aAddParams[3]))
        );
        
        $aForm = $this->oTemplate->getAlbumFormEditArray($aReInputs, $aReForm);
        $oForm = new BxTemplFormView($aForm);
        $oForm->initChecker();
        if ($oForm->isSubmittedAndValid()) {
            $aFields = array('Caption', 'Location', 'Description', 'AllowAlbumView');
            $aData = array();
            foreach ($aFields as $sValue) {
                if (isset($_POST[$sValue]))
                    $aData[$sValue] = $_POST[$sValue];
            }
            if ($this->oAlbum->updateAlbum($_POST['Uri'], $aData))
                $sCode = MsgBox(_t('_bx_' . $this->oConfig->getUri() . '_album_save_success'));
        }    
        else
            $sCode = $oForm->getCode();
        return array($sCode, $this->getTopMenu('edit'));
    }
    
    function getBlockCode_organize () {
        if (!in_array('organize', $this->aCurrentBlocks['blocks']))
            return '';
        $this->oSearch->clearFilters(array('activeStatus', 'allow_view', 'album_status'), array('albumsObjects', 'albums', 'icon'));
        $this->oSearch->bAdminMode = false;
        $this->oSearch->aCurrent['view'] = 'short';
        
        $this->oSearch->aCurrent['restriction']['album']['value'] = $this->aAddParams[1];
        $this->oSearch->aCurrent['restriction']['albumType']['value'] = $this->oSearch->aCurrent['name'];
        $this->oSearch->aCurrent['restriction']['ownerId']['value'] = $this->iOwnerId;
        $this->oSearch->aCurrent['sorting'] = 'album_order';
        $this->oSearch->aCurrent['paginate']['perPage'] = 1000;
        $aUnits = $this->oSearch->getSearchData();
        if ($this->oSearch->aCurrent['paginate']['totalNum'] > 0) {			
            $sMainUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri();
			foreach ($aUnits as $aData)
				$sCode .= $this->oSearch->displaySearchUnit($aData);
	        $aBtns = array(
	            'action_reverse' => array(
					'value' => _t('_' . $this->oConfig->getMainPrefix() . '_album_organize_reverse'),
					'type' => 'submit',
					'name' => 'reverse',
					'onclick' => 'onclick=\'getHtmlData("unit_area", "' . $sMainUrl . 'album_reverse/' . $this->aAddParams[1] . '"); return false;\''
				)
	        );
            $sAreaId = 'unit_area';
	        $sCode = $GLOBALS['oFunctions']->centerContent('<div id="' . $sAreaId . '">' . $sCode . '<div class="clear_both"></div></div>', '.sys_file_search_unit_short');
            $sManage = $this->oSearch->showAdminActionsPanel('', $aBtns, 'entry', false, false);
            $aUnit = array(
	            'main_code' => $sCode . $sManage,
	            'bx_if:hidden' => array(
	                'condition' => (int)$aAlbumInfo['ID'] != 0,
	                'content' => array()
	            )
	        );
            $sJsCode = $this->oTemplate->parseHtmlByName('js_organize.html', array(
            	'url' => $sMainUrl,
            	'unit_area_id' => $sAreaId,
            	'album_name' => $this->aAddParams[1],
            	'add_params' => $this->aAddParams[2] . '/' . $this->aAddParams[3]
            ));
            $this->oTemplate->addJs(array('jquery-ui.js', 'ui.sortable.js'));
	        $sCode = $sJsCode . $this->oTemplate->parseHtmlByName('manage_form.html', $aUnit);
        }
        else
            $sCode = MsgBox(_t('_Empty'));
        return array($sCode, $this->getTopMenu('organize'), array(), '');
    }
    
    function getBlockCode_delete () {
        if (!in_array('delete', $this->aCurrentBlocks['blocks']))
            return '';
        $aForm = array(
            'form_attrs' => array(
            ),
            'inputs' => array(
                'info' => array(
                    'type' => 'custom',
                    'content' => _t('_bx_' . $this->oConfig->getUri() . '_album_delete_warning')
                ),
                'submit' => array(
                    'type' => 'submit',
                    'name' => 'submit',
                    'value' => _t('_bx_' . $this->oConfig->getUri() . '_album_delete_confirm'),
                    'attrs' => array(
                        'onclick' => "getHtmlData('ajaxy_popup_result_div', '" . BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . "album_delete/" . $this->aAddParams[1] . "'); return false;"
                    )
                ),
            ),
        );
        bx_import("BxTempFormView");
        $oForm = new BxTemplFormView($aForm);
        $sCode = $oForm->getCode() . '<div id="ajaxy_popup_result_div"></div>';
        return array($sCode, $this->getTopMenu('delete'));
    }
    
    function getBlockCode_albumObjects () {
        if (!in_array('albumObjects', $this->aCurrentBlocks['blocks']))
            return '';
        $this->oSearch->clearFilters(array('allow_view', 'album_status'), array('albumsObjects', 'albums', 'icon'));
        $this->oSearch->aCurrent['sorting'] = 'album_order';
        $this->oSearch->bAdminMode = false;
        $this->oSearch->aCurrent['view'] = 'full';
        
        $this->oSearch->aCurrent['restriction']['activeStatus']['value'] = 'approved';
        $this->oSearch->aCurrent['restriction']['album']['value'] = $this->aAddParams[1];
        $this->oSearch->aCurrent['restriction']['albumType']['value'] = $this->oSearch->aCurrent['name'];
        $this->oSearch->aCurrent['restriction']['ownerId']['value'] = $this->iOwnerId;
        $this->oSearch->aCurrent['paginate']['perPage'] = $this->oConfig->getGlParam('number_all');
        $sCode = $this->oSearch->displayResultBlock();
        if ($this->oSearch->aCurrent['paginate']['totalNum'] > 0) {
            $sUrlBody = '';
        	for ($i = 1; $i < 4; $i++)
            	$sUrlBody .= '/' . $this->aAddParams[$i];
            $sUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'browse/album' . $sUrlBody . '&per_page=' . $this->oSearch->aCurrent['paginate']['totalNum'];
            $aBottomMenu = $this->oSearch->getBottomMenu($sUrl, $this->iOwnerId, getNickName($this->iOwnerId));
        }
        else {
            $sCode = $this->oSearch->addCustomParts() . MsgBox(_t("_Empty"));
            $aBottomMenu = array();
        }
        $aSections = array(
			'disapproved' => NULL,
			'pending' => NULL,
		);
		$sLangKey = '_' . $this->oConfig->getMainPrefix() . '_count_status_info';
		$sUnitKey = '_' . $this->oConfig->getMainPrefix() . '_album_manage_objects_';
		$sHref = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'albums/my/manage_objects_{section}/' . $this->aAddParams[1] . '/owner/' . $this->aAddParams[3];
        $sInfo = '';
		$sMessage = '';
        foreach ($aSections as $sSection => $mixedStatus) {
			$mixedStatus = is_null($mixedStatus) ? $sSection : $mixedStatus;
        	$aParams = array('albumUri' => $this->aAddParams[1], 'Approved' => $mixedStatus);
        	$iCount = $this->oDb->getFilesCountByParams($aParams);
        	if ($iCount > 0) {
        		$sLangUnitKey = _t($sUnitKey . $sSection);
				$sMessage .= _t($sLangKey, $iCount, str_replace('{section}', $sSection, $sHref), $sLangUnitKey) . ' ';
        	}
        }
		$aVars = array ('msg' => $sMessage);
		$sInfo = $this->oTemplate->parseHtmlByName ('pending_approval_plank.html', $aVars);
        return array($sInfo . $sCode, array(), $aBottomMenu, '');
    }
    
    // support functions
    function getTopMenu ($sMode = 'main') {
        $aTopMenu = array();
        if (strlen($this->aAddParams[1]) > 0) {
            $iCheck = 1;
            $sName = $this->aAddParams[1] . '/' . strip_tags($this->aAddParams[2]) . '/' . strip_tags($this->aAddParams[3]); 
        }
        else
            $iCheck = 0;
        $sHrefPref = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri();
        foreach ($this->aSystemBlocks as $sKey => $aValue) {
            $sAdd  = $aValue['const'] == true ? '' : $sName;
            $sOwnLink = isset($aValue['link']) ? $aValue['link'] : 'albums/my/' . $sKey . '/';   
            if ($aValue['level'] == $iCheck)
                $aTopMenu[_t('_bx_' . $this->oConfig->getUri() . '_album_' . $sKey)] = array(
                    'href' => $sHrefPref . $sOwnLink . $sAdd,
                    'active' => ($sMode == $sKey )
                );
        }
        return $aTopMenu;
    }
    
    function getAdminPart ($aCondition = array(), $aCustom = array()) {
        $this->oSearch->bAdminMode = true;
        $iPerPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : (int)$this->oConfig->getGlParam('number_albums_home');
        $iPage = isset($_GET['page']) ? (int)$_GET['page'] : $this->oSearch->aCurrent['paginate']['page'];
        $this->oSearch->aCurrent['restriction']['owner']['value'] = $this->iOwnerId;
        $aCondition['show_empty'] = true;
        $sCode = $this->oSearch->getAlbumList($iPage, $iPerPage, $aCondition);
        $aBtns = array(
            'action_delete' => _t('_Delete')
        );
        
        $sSection = isset($aCustom['section']) ? strip_tags($aCustom['section']) : '';
        $iId = isset($aCustom['page_block_id']) ? (int)$aCustom['page_block_id'] : 1;
        $aLinkAddon = $this->oSearch->getLinkAddByPrams();
        $sLink  = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'albums/my/' . $sSection;
        $this->oSearch->aCurrent['paginate']['perPage'] = 2;
        $oPaginate = new BxDolPaginate(array(
            'page_url' => $sLink,
            'count' => $this->oSearch->aCurrent['paginate']['totalAlbumNum'],
            'per_page' => $iPerPage,
            'page' => $iPage,
            'per_page_changer' => true,
            'page_reloader' => true,
            'on_change_page' => 'return !loadDynamicBlock(' . $iId . ', \'' . $sLink . $aLinkAddon['params'] . '&page={page}&per_page={per_page}\');',
            'on_change_per_page' => 'return !loadDynamicBlock(' . $iId . ', \'' . $sLink . $aLinkAddon['params'] . '&page=1&per_page=\' + this.value);'
        ));
        $sPaginate = $oPaginate->getSimplePaginate(BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'albums/browse/owner/' . getNickName($this->iOwnerId));
        $sManage = $this->oSearch->showAdminActionsPanel($this->oSearch->aCurrent['name'] . '_admin_form', $aBtns);         
        $aUnit = array(
            'main_code' => $GLOBALS['oFunctions']->centerContent($sCode, '.sys_album_unit') . $sPaginate . $sManage,
            'bx_if:hidden' => ''
        ); 
        return $this->oTemplate->parseHtmlByName('manage_form.html', $aUnit);
    }
    
    function getAdminObjectPart ($aCondition = array(), $iBoxId = 0, $bShowMove = false) {
        $this->oSearch->clearFilters(array('activeStatus', 'allow_view', 'album_status'), array('albumsObjects', 'albums', 'icon'));
        $this->oSearch->bAdminMode = true;
        
        $this->oSearch->aCurrent['sorting'] = 'album_order';
        $this->oSearch->aCurrent['view'] = 'full';
        
        $this->oSearch->aCurrent['restriction']['album']['value'] = $this->aAddParams[1];
        $this->oSearch->aCurrent['restriction']['albumType']['value'] = $this->oSearch->aCurrent['name'];
        $this->oSearch->aCurrent['restriction']['ownerId']['value'] = $this->iOwnerId;
        
        if (is_array($aCondition)) {
            foreach ($aCondition as $sKey => $sValue) {
                if (isset($this->oSearch->aCurrent['restriction'][$sKey]))
                    $this->oSearch->aCurrent['restriction'][$sKey]['value'] = $sValue;
            }
        }
        
        $aUserAlbums = $this->oAlbum->getAlbumList(array('owner' => $this->iOwnerId, 'show_empty' => true), 0, 0, true);
        foreach ($aUserAlbums as $aValue) {
            if ($aValue['Uri'] != $this->aAddParams[1]) {
                $aAlbums[] = array(
                    'album_id' => $aValue['ID'],
                    'album_caption' => $aValue['Caption']
                );
            }
            else {
                $aAlbumInfo = array('ID' => $aValue['ID']);
                $this->oSearch->aCurrent['restriction']['allow_view']['value'] = array($aValue['AllowAlbumView']); 
            }
        }
        $aBtns = array(
            'action_delete_object' => _t('_Delete'),
        );
        $sMoveToAlbum = '';
        if (count($aUserAlbums) > 1 && $bShowMove) {
            $aBtns['action_move_to'] = _t('_sys_album_move_to_another');
            $sMoveToAlbum = $this->oTemplate->parseHtmlByName('albums_select.html', array('bx_repeat:choose' => $aAlbums));
        }
        $sCode = $this->oSearch->displayResultBlock();
        if ($this->oSearch->aCurrent['paginate']['totalNum'] == 0)
            $sCode = MsgBox(_t('_Empty'));
        else {
            $sCode = $GLOBALS['oFunctions']->centerContent($sCode, '.sys_file_search_unit');
			$sPaginate = $this->oSearch->getBottomMenu('browseUserAll', $this->iOwnerId, getNickName($this->iOwnerId));
            if ($iBoxId > 0)
            	$sPaginate = str_replace('{id}', $iBoxId, $sPaginate);
        }
        $sManage = $sPaginate . $this->oSearch->showAdminActionsPanel($this->oSearch->aCurrent['name'] . '_admin_form', $aBtns, 'entry', true, false, $sMoveToAlbum);        
        $aAlbumInfo = $this->oAlbum->getAlbumInfo(array('fileUri' => $this->aAddParams[1]), array('ID'));
        $aUnit = array(
            'main_code' => $sCode . $sManage,
            'bx_if:hidden' => array(
                'condition' => (int)$aAlbumInfo['ID'] != 0,
                'content' => array(
                    'hidden_name' =>  'album_id',
                    'hidden_value' => (int)$aAlbumInfo['ID']
                )
            )
        );
        return $this->oTemplate->parseHtmlByName('manage_form.html', $aUnit);
    }
}

?>