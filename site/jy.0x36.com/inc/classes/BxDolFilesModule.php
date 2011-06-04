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

bx_import('BxDolModule');
bx_import('BxDolAlbums');
bx_import("BxTempFormView");

class BxDolFilesModule extends BxDolModule {
	var $_iProfileId;
	var $aPageTmpl;
	var $oPrivacy;
	var $oAlbumPrivacy;
	
	var $oAlbums;
	
	var $_aMemActions = array('add', 'view', 'delete', 'approve', 'edit');
	
	var $aSectionsAdmin = array();
	
    /**
	 * Constructor
	 */
	function BxDolFilesModule (&$aModule) {
	    parent::BxDolModule($aModule);
	    $this->_iProfileId = $this->_oDb->iViewer;
        $this->_oTemplate->init($this->_oDb);
        $this->aPageTmpl = array(
            'name_index' => 1, 
            'header' => $GLOBALS['site']['title'],
            'header_text' => '',
        );
        $sClassName = $this->_oConfig->getClassPrefix() . 'Privacy';
        bx_import('Privacy', $aModule);
        $this->oPrivacy = new $sClassName();
        $this->oAlbumPrivacy = new $sClassName('sys_albums');
        $this->oAlbums = new BxDolAlbums($this->_oConfig->getMainPrefix(), $this->_iProfileId);
        
        $this->aSectionsAdmin = array(
        	'approved' => array(
        		'exclude_btns' => array('activate')
        	),
        	'disapproved' => array(
        		'exclude_btns' => array('deactivate', 'featured', 'unfeatured')
        	),
        	'pending' => array(
        		'exclude_btns' => array('activate', 'deactivate', 'featured', 'unfeatured')
        	),
        );
	}
	
	function _checkVisible ($aParam = array()) {
        $aVis = array(BX_DOL_PG_ALL);
        if ($this->_iProfileId > 0)
            $aVis[] = BX_DOL_PG_MEMBERS;
        return $aVis;
	}
	
	function _defineActionsArray () {
        $aNewActions = array();
		foreach ($this->_aMemActions as $sValue)
			$aNewActions[] = $this->_oConfig->getUri() . ' ' . $sValue;
		return $aNewActions;
	}
	
    function _defineActions () {
    	$aActionList = $this->_defineActionsArray();
    	defineMembershipActions($aActionList);
    }
    
    function _defineActionName ($sAction) {
        $sConstName = strtoupper(str_replace(' ', '_', $this->_oConfig->getMainPrefix()) . '_' . $sAction);
		return constant($sConstName);
    }
    
    function _deleteFile ($iFileId) {
        $aInfo = $this->serviceCheckDelete($iFileId);
        if (!$aInfo)
            return false;
        if ($this->_oDb->deleteData($iFileId)) {
            $aFilesPostfix = $this->_oConfig->aFilePostfix;
            //delete temp files
            $aFilesPostfix['temp'] = '';
            if (isset($aFilesPostfix['original']))
                $aFilesPostfix['original'] = $this->_getOriginalExt($aInfo, $aFilesPostfix['original']);
            foreach ($aFilesPostfix as $sValue) {
                $sFilePath = $this->_oConfig->getFilesPath() . $iFileId . $sValue;
                @unlink($sFilePath);
            }
            bx_import('BxDolVoting');
            $oVoting = new BxDolVoting($this->_oConfig->getMainPrefix(), 0, 0);
            $oVoting->deleteVotings($iFileId);
            bx_import('BxDolCmts');
            $oCmts = new BxDolCmts($this->_oConfig->getMainPrefix(), $iFileId);
            $oCmts->onObjectDelete();            
            
            bx_import('BxDolCategories');
            //tags & categories parsing
            $oTag = new BxDolTags();
            $oTag->reparseObjTags($this->_oConfig->getMainPrefix(), $iFileId);
            $oCateg = new BxDolCategories();
            $oCateg->reparseObjTags($this->_oConfig->getMainPrefix(), $iFileId);
            
            $bUpdateCounter = $aInfo['Approved'] == 'approved' ? true : false;
            $this->oAlbums->removeObjectTotal($iFileId, $bUpdateCounter);

            bx_import ('BxDolAlerts');
            $oAlert = new BxDolAlerts($this->_oConfig->getMainPrefix(), 'delete', $iFileId, $this->_iProfileId);
            $oAlert->alert();           
            $this->isAllowedDelete($aInfo, true);
        }
        else
            return false;
        return true;
    }
    
    function _deleteAlbumUnits ($iAlbumId) {
        $iAlbumId = (int)$iAlbumId;
        $aObjects = $this->oAlbums->getAlbumObjList($iAlbumId);
        $iCount = 0;
        foreach ($aObjects as $iValue) {
            $iObj = (int)$iValue;
            if (!$this->_deleteFile($iObj))
                $iCount++;
        }
        return $iCount;
    }
    
    function _getOriginalExt (&$aInfo, $sTmpl, $sKey = '{ext}') {
    	return str_replace($sKey, $aInfo['medExt'], $sTmpl);
    }
	
	function actionAdministration ($sParam = '', $sParam1 = '') {
	    if (!isAdmin($this->_iProfileId)) return;
	    $this->checkActions();
	    
    	if (isset($_GET['action']) && $_GET['action'] == 'findMembers') {
            echo $this->getMemberList();
            exit;
        }
        
        $aMenu = array(
            $this->_oConfig->getMainPrefix() . '_admin_main' => array('title' => _t('_' . $this->_oConfig->getMainPrefix() . '_admin_main'), 'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/home'), 
            $this->_oConfig->getMainPrefix() . '_admin_settings' => array('title' => _t('_' . $this->_oConfig->getMainPrefix() . '_admin_settings'), 'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/settings'),
        );
        
        switch ($sParam) {
            case 'settings':
                $aMenu[$this->_oConfig->getMainPrefix() . '_admin_settings']['active'] = 1;
                $sCode = $this->getAdminSettings($aMenu);
                break;
            default:
                $aMenu[$this->_oConfig->getMainPrefix() . '_admin_main']['active'] = 1;
                $sCode = $this->getAdminMainPage($aMenu, $sParam1);
                break;
        }
        $this->aPageTmpl['name_index'] = 9;
        $this->aPageTmpl['header'] = _t('_' . $this->_oConfig->getMainPrefix() . '_admin');
        $this->aPageTmpl['css_name'] = array('forms_adv.css', 'my.css', 'search.css', 'search_admin.css');
        $this->_oTemplate->pageCode($this->aPageTmpl, array('page_main_code' => $sCode), array(), array(), true);
	}
	
	function actionHome () {
        $sClassName = $this->_oConfig->getClassPrefix() . 'PageHome';
	    bx_import('PageHome', $this->_aModule);
        $oPage = new $sClassName($this);
        $sCode = $oPage->getCode();
        $this->aPageTmpl['css_name'] = array('browse.css');
        $this->aPageTmpl['header'] = _t('_' . $this->_oConfig->getMainPrefix() . '_top_menu_home');
        $this->_oTemplate->pageCode($this->aPageTmpl, array('page_main_code' => $sCode));
	}
	
	function actionCategories () {
	    bx_import('BxTemplCategoriesModule');
        $aParam = array(
            'type' => $this->_oConfig->getMainPrefix(),
        );
        $oCateg = new BxTemplCategoriesModule($aParam, _t('_' . $this->_oConfig->getMainPrefix() . '_categories_all'), BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'categories');
        $sCode = $oCateg->getCode();
        $this->aPageTmpl['header'] = _t('_' . $this->_oConfig->getMainPrefix() . '_top_menu_categories');
        $this->_oTemplate->pageCode($this->aPageTmpl, array('page_main_code' => $sCode));
	}
	
	function actionTags () {
	    bx_import('BxTemplTagsModule');
        $aParam = array(
            'type' => $this->_oConfig->getMainPrefix(),
            'orderby' => 'popular'
        );
        $oTags = new BxTemplTagsModule($aParam, _t('_' . $this->_oConfig->getMainPrefix() . '_bcaption_all'), BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'tags');
        $sCode = $oTags->getCode();
        $this->aPageTmpl['header'] = _t('_' . $this->_oConfig->getMainPrefix() . '_top_menu_tags');
        $this->_oTemplate->pageCode($this->aPageTmpl, array('page_main_code' => $sCode));
	}
	
	function actionView ($sUri) {
	    $aIdent = array(
           'fileUri' => $sUri,
        );
        $aInfo = $this->_oDb->getFileInfo($aIdent);
        if (!empty($aInfo)) {
           if (!$this->isAllowedView($aInfo)) {
               $sKey  = _t('_' . $this->_oConfig->getMainPrefix() . '_forbidden');
               $sCode = DesignBoxContent($sKey, MsgBox($sKey), 1);
           }
           else {
               $aInfo['medTitle'] = stripslashes($aInfo['medTitle']);
               $aInfo['medDesc']  = stripslashes($aInfo['medDesc']);
               $aInfo['NickName'] = getNickName($aInfo['medProfId']);
               // album data about prev and next files
               // calculation of un-approved files in album               
               $sClassName = $this->_oConfig->getClassPrefix() . 'Search';
			   bx_import('Search', $this->_aModule);
			   $oSearch = new $sClassName();
			   $oSearch->aCurrent['restriction']['albumId'] = array('value' => $aInfo['albumId'], 'field' => 'ID', 'operator' => '=',  'table'=>'sys_albums');
			   $oSearch->aCurrent['restriction']['activeStatus']['operator'] = '<>';
               
               $aIds = '';
               $aExcludeList = $oSearch->getSearchData();
               if (!empty($aExcludeList)) {
	               foreach ($aExcludeList as $aValue)
	               		$aIds[] = $aValue['id'];
           	   }
               $aInfo['prevItem'] = $this->oAlbums->getClosestObj($aInfo['albumId'], $aInfo['medID'], 'prev', $aInfo['obj_order'], $aIds);
               $aInfo['nextItem'] = $this->oAlbums->getClosestObj($aInfo['albumId'], $aInfo['medID'], 'next', $aInfo['obj_order'], $aIds);
               
               $aInfo['favorited'] = $this->_oDb->checkFavoritesIn($aInfo['medID']);
               
               bx_import('PageView', $this->_aModule);
               $sClassName = $this->_oConfig->getClassPrefix() . 'PageView';
			   $oPage = new $sClassName($this, $aInfo);
               $sCode = $oPage->getCode();
               $this->aPageTmpl['header'] = $sKey = $aInfo['medTitle'];
               
               if ($this->_iProfileId != $aInfo['medProfId'])
					$this->isAllowedView($aInfo, true);
           }    
        }
        else {
			header("HTTP/1.1 404 Not Found");
            $sKey  = _t('_' . $this->_oConfig->getMainPrefix() . '_not_found');
            $sCode = DesignBoxContent($sKey, MsgBox($sKey), 1);
        }
        $GLOBALS['oTopMenu']->setCustomSubHeader($sKey);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_' . $this->_oConfig->getMainPrefix()) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aInfo['albumCaption'] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/album/' . $aInfo['albumUri'] . '/owner/' . $aInfo['NickName'],
            $sKey => '',
        ));
        $this->_oTemplate->pageCode($this->aPageTmpl, array('page_main_code' => $sCode));
	}
	
	function actionBrowse ($sParamName = '', $sParamValue = '', $sParamValue1 = '', $sParamValue2 = '', $sParamValue3 = '') {
		$bAlbumView = false;
	    if ($sParamName == 'album' && $sParamValue1 == 'owner') {
            $bAlbumView = true;
            $aAlbumInfo = $this->oAlbums->getAlbumInfo(array('fileUri' => $sParamValue, 'owner' => getID($sParamValue2)), array('ID', 'Caption', 'Owner', 'AllowAlbumView'));
	        $GLOBALS['oTopMenu']->setCustomSubHeader($aAlbumInfo['Caption']);
	        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
	            _t('_' . $this->_oConfig->getMainPrefix()) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
	            $aAlbumInfo['Caption'] => '',
	        ));
            $sAlbumPreview = '';
			if ($this->_oConfig->getGlParam('album_slideshow_on') == 'on') {
		        $sAlbumPreview = $this->_oTemplate->getAlbumPreview('rss/album/' . $sParamValue . '/owner/' . $sParamValue2);
		        if (strlen($sAlbumPreview) > 0)
		        	$sAlbumPreview = DesignBoxContent($aAlbumInfo['Caption'], $sAlbumPreview, 1);
			}
        }

	    if (!empty($aAlbumInfo) && $aAlbumInfo['Owner'] == $this->_iProfileId && $sParamValue2 === getNickName($this->_iProfileId)) {
	    	$this->actionAlbumsViewMy('main_objects', $sParamValue, $sParamValue1, $sParamValue2, $sParamValue3);
            return;
        }
        
        if ($bAlbumView && !empty($aAlbumInfo['AllowAlbumView']) && !$this->oAlbumPrivacy->check('album_view', $aAlbumInfo['ID'], $this->_iProfileId)) {
            $sKey  = _t('_' . $this->_oConfig->getMainPrefix() . '_access_denied');
            $sCode = DesignBoxContent($sKey, MsgBox($sKey), 1);
            $this->aPageTmpl['header'] = $sKey;
            $this->_oTemplate->pageCode($this->aPageTmpl, array('page_main_code' => $sCode));
            return;
        }
	    $sClassName = $this->_oConfig->getClassPrefix() . 'Search';
        bx_import('Search', $this->_aModule);
        $oSearch = new $sClassName($sParamName, $sParamValue, $sParamValue1, $sParamValue2);
        $sRss = bx_get('rss');
        if ($sRss !== false && $sRss) {
            $oSearch->aCurrent['paginate']['perPage'] = 10;
            echo $oSearch->rss();
            exit;
        }
		
		$sTopPostfix = isset($oSearch->aCurrent['restriction'][$sParamName]) || $oSearch->aCurrent['sorting'] == $sParamName ? $sParamName : 'all';
		$sCaption = _t('_' . $this->_oConfig->getMainPrefix() . '_top_menu_' . $sTopPostfix);
        if (mb_strlen($sParamValue) > 0 && isset($oSearch->aCurrent['restriction'][$sParamName])) {
            $sParamValue = $this->getBrowseParam($sParamName, $sParamValue);
        	$oSearch->aCurrent['restriction'][$sParamName]['value'] = $sParamValue;
			$sCaption = _t('_' . $this->_oConfig->getMainPrefix() . '_browse_by_' . $sParamName, process_pass_data($sParamValue));
		}
        if ($bAlbumView) {
            $oSearch->aCurrent['restriction']['allow_view']['value'] = array($aAlbumInfo['AllowAlbumView']);
			$sCaption = _t('_' . $this->_oConfig->getMainPrefix() . '_browse_by_' . $sParamName, $aAlbumInfo['Caption']);
		}

        if($sParamName == 'calendar') {
		    $sCaption = _t('_' . $this->_oConfig->getMainPrefix() . '_caption_browse_by_day')
		        . ': ' . getLocaleDate( strtotime("{$sParamValue}-{$sParamValue1}-{$sParamValue2}")
		        , BX_DOL_LOCALE_DATE_SHORT);
		}

        $oSearch->aCurrent['paginate']['perPage'] = (int)$this->_oConfig->getGlParam('number_all');
        $sCode = $oSearch->displayResultBlock();
        if ($oSearch->aCurrent['paginate']['totalNum'] > 0) {
            $sCode = $GLOBALS['oFunctions']->centerContent($sCode, '.sys_file_search_unit');
            $aAdd = array($sParamName, $sParamValue, $sParamValue1, $sParamValue2, $sParamValue3);
            foreach ($aAdd as $sValue) {
                if (strlen($sValue) > 0)
                    $sArg .= '/' . rawurlencode($sValue);
                else
                    break;
            }
            $sLink  = $this->_oConfig->getBaseUri() . 'browse' . $sArg;
            $oPaginate = new BxDolPaginate(array(
                'page_url' => $sLink . '&page={page}&per_page={per_page}',
                'count' => $oSearch->aCurrent['paginate']['totalNum'],
                'per_page' => $oSearch->aCurrent['paginate']['perPage'],
                'page' => $oSearch->aCurrent['paginate']['page'],
                'per_page_changer' => true,
                'page_reloader' => true,
                'on_change_per_page' => 'document.location=\'' . BX_DOL_URL_ROOT . $sLink . '&page=1&per_page=\' + this.value;'
            ));
            $sPaginate = $oPaginate->getPaginate();
        }
        else
            $sCode = MsgBox(_t('_Empty'));
        $aMenu = array();
        $sCode = DesignBoxContent($sCaption, $sCode . $sPaginate, 1, $this->_oTemplate->getExtraTopMenu($aMenu, BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri()));
        $this->aPageTmpl['css_name'] = array('browse.css');
        $this->aPageTmpl['header'] = $sCaption;
        $this->_oTemplate->pageCode($this->aPageTmpl, array('page_main_code' => $sCode . $sAlbumPreview));
	}
	
	function actionEdit ($iFileId) {
        $iFileId = (int)$iFileId > 0 ? (int)$iFileId : (int)$_POST['fileId'];
        if ($iFileId == 0)
           exit;
        
        $this->aPageTmpl['name_index'] = 44;
        $sJsCode = '<script language="javascript">window.setTimeout(function () { window.parent.opener.location = window.parent.opener.location; window.parent.close(); }, 3000); </script>';
        $aManageArray = array('medTitle', 'medTags', 'medDesc', 'medProfId', 'Categories');
        $aInfo = $this->_oDb->getFileInfo(array('fileId'=>$iFileId), false, $aManageArray);
        if (!$this->isAllowedEdit($aInfo))
           $sCode = MsgBox(_t('_' . $this->_oConfig->getMainPrefix() . '_access_denied')) . $sJsCode;
        else {
            $oCategories = new BxDolCategories();
			$oCategories->getTagObjectConfig();            	
            $aCategories = $oCategories->getGroupChooser($this->_oConfig->getMainPrefix(), $this->_iProfileId, true);
            $aCategories['value'] = explode(CATEGORIES_DIVIDER, $aInfo['Categories']);
            $aForm = array(
                'form_attrs' => array(
                    'id' => $this->_oConfig->getMainPrefix() . '_upload_form',
                    'method' => 'post',
                    'action' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'edit/' . $iFileId
                ),
                'params' => array (
			        'db' => array(
			            'submit_name' => 'submit',
			        ),
			        'checker_helper' => 'BxSupportCheckerHelper',
			    ),
                'inputs' => array(
                    'header' => array(
                        'type' => 'block_header',
                        'caption' => _t('_Info'),
                    ),
                    'title' => array(
                        'type' => 'text',
                        'name' => 'medTitle',
                        'caption' => _t('_Title'),
                        'required' => true,
                        'value' => $aInfo['medTitle'],
                        'checker' => array (  
						    'func' => 'length',
						    'params' => array(3, 128),
						    'error' => _t('_td_err_incorrect_length'),
						),
                    ),
                    'tags' => array(
                        'type' => 'text',
                        'name' => 'medTags',
                        'caption' => _t('_Tags'),
                        'info' => _t('_Tags_desc'),
                        'value' => $aInfo['medTags']
                    ),
                    'description' => array(
                        'type' => 'textarea',
                        'name' => 'medDesc',
                        'caption' => _t('_Description'),
                        'required' => true,
                        'value' => $aInfo['medDesc'],
                        'checker' => array (  
						    'func' => 'length',
						    'params' => array(3, 65536),
						    'error' => _t('_td_err_incorrect_length'),
						),
                    ),
                    'categories' => $aCategories,
                    'fileId' => array(
                        'type' => 'hidden',
                        'name' => 'fileId',
                        'value' => $iFileId,
                    ),
                    'medProfId' => array(
                        'type' => 'hidden',
                        'name' => 'medProfId',
                        'value' => $aInfo['medProfId'],
                    ),
                    'submit' => array(
                        'type' => 'submit',
                        'name' => 'submit',
                        'value' => _t('_Submit'),
                        'colspan' => true,
                    ),
                ),
            );
            $oForm = new BxTemplFormView($aForm);
            $oForm->initChecker();        	
        	if ($oForm->isSubmittedAndValid()) {
                $aValues = array();
                foreach ($aManageArray as $sKey) {
                    if ($sKey != 'Categories')
                       $aValues[$sKey] = $_POST[$sKey];
                    else {
                       $aValues[$sKey] = implode(CATEGORIES_DIVIDER, $_POST[$sKey]);
                    }
                }
                if ($this->_oDb->updateData($iFileId, $aValues)) {
                    $sType = $this->_oConfig->getMainPrefix();
                    bx_import('BxDolCategories');
                    
                    $oTag = new BxDolTags();
                    $oTag->reparseObjTags($sType, $iFileId);
                    $oCateg = new BxDolCategories();
                    $oCateg->reparseObjTags($sType, $iFileId);
                    
                    $sCode = MsgBox(_t('_' . $this->_oConfig->getMainPrefix() . '_save_success')) . $sJsCode;
                }
            }    
            else {
                $sCode = $oForm->getCode();               
                $this->aPageTmpl['css_name'] = array('forms_adv.css', 'explanation.css');
            }    
        }	
        $this->aPageTmpl['header'] = _t('_Edit');
        $this->_oTemplate->pageCode($this->aPageTmpl, array('page_main_code' => $sCode));             
	}
	
    function actionRate () {
        $sClassPath = $this->_oConfig->getClassPath() . $this->_oConfig->getClassPrefix() . 'Rate.php';
        if (file_exists($sClassPath)) { 
            require_once($sClassPath);
            $sClassName = $this->_oConfig->getClassPrefix() . 'Rate';
            $oPage = new $sClassName($this->_oConfig->getMainPrefix());
            $sCode = $oPage->getCode();
            $this->aPageTmpl['header'] = _t('_' . $this->_oConfig->getMainPrefix() . '_top_menu_rate');
        }
        else {
            $sKey = _t('_sys_request_page_not_found_cpt');
            $sCode = DesignBoxContent($sKey, MsgBox($sKey), 1);
        }
        $this->aPageTmpl['css_name'] = array('search.css', 'browse.css');
        $this->_oTemplate->pageCode($this->aPageTmpl, array('page_main_code' => $sCode));
    }
	
	function actionRss ($sParamName, $sParamValue, $sParamValue1, $sParamValue2) {
		if ($this->_oConfig->getGlParam('rss_feed_on') == 'on') {
			switch ($sParamName) {
				case 'album':
					$aAlbumInfo = $this->oAlbums->getAlbumInfo(array('fileUri'=>$sParamValue, 'owner'=> getID($sParamValue2)), array('ID', 'Owner'));
					$aFileCopycat = array(
						'albumId' => $aAlbumInfo['ID'],
						'medProfId' => $aAlbumInfo['Owner'],
						'Approved' => 'approved',
					);
					$aUnits = array();
					if ($this->isAllowedView($aFileCopycat)) {
						$sClassName = $this->_oConfig->getClassPrefix() . 'Search';
						bx_import('Search', $this->_aModule);
						$oSearch = new $sClassName();
						$oSearch->aCurrent['paginate']['perPage'] = 1000;
						$aUnits = $oSearch->serviceGetFilesInAlbum($aAlbumInfo['ID']);
					}
					$sCode = $this->_oTemplate->getAlbumFeed($aUnits);
					break;
	        }
	        header('Content-Type: text/xml; charset=UTF-8');
			echo $sCode;
		}
	}
	
	function actionReport ($sFileUri) {
		$aForm = $this->getSubmitForm($sFileUri, 'report');
		$oForm = new BxTemplFormView($aForm);
		$oForm->initChecker();
		if ($oForm->isSubmittedAndValid()) {
            if ($this->sendFileInfo($_POST['email'], $_POST['messageText'], BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $sFileUri, $_POST['mediaAction']))
               $sCode = MsgBox(_t('_File info was sent')) . '<script language="javascript">window.setTimeout(function () { window.parent.close(); }, 3000); </script>';
        }
        else {
            $sCode = $oForm->getCode();
            $this->aPageTmpl['css_name'] = array('forms_adv.css', 'explanation.css');
        }    
        $this->aPageTmpl['name_index'] = 44;
        $this->aPageTmpl['header'] = _t('_' . $this->_oConfig->getMainPrefix() . '_action_report');
        $this->_oTemplate->pageCode($this->aPageTmpl, array('page_main_code' => $sCode));
    }
	
	function actionShare ($sFileUri) {
        $aForm = $this->getSubmitForm($sFileUri, 'share');
		$oForm = new BxTemplFormView($aForm);
		$oForm->initChecker();
		if ($oForm->isSubmittedAndValid()) {
            if ($this->sendFileInfo($_POST['email'], $_POST['messageText'], BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $sFileUri, $_POST['mediaAction']))
               $sCode = MsgBox(_t('_File info was sent')) . '<script language="javascript">window.setTimeout(function () { window.parent.close(); }, 3000); </script>';            
        }
        else {
            $sCode = $oForm->getCode();
            $this->aPageTmpl['css_name'] = array('forms_adv.css', 'explanation.css');
        }    
        $this->aPageTmpl['name_index'] = 44;
        $this->aPageTmpl['header'] = _t('_' . $this->_oConfig->getMainPrefix() . '_action_share');
        $this->_oTemplate->pageCode($this->aPageTmpl, array('page_main_code' => $sCode));
	}
	
	function actionFavorite ($iFileId) {
		if (!$this->_oDb->checkFavoritesIn($iFileId)) {
        	$sMessPost = 'add';
        	$this->_oDb->addToFavorites($iFileId);
        }
        else {
        	$sMessPost = 'remove';
        	$this->_oDb->removeFromFavorites($iFileId);
        }
	    $sJQueryJS = genAjaxyPopupJS($iFileId);
        echo MsgBox(_t('_' . $this->_oConfig->getMainPrefix() . '_fav_' . $sMessPost)) . $sJQueryJS;
        exit;
	}
	
	function actionDelete ($iFileId, $sAlbumUri = '', $sOwnerNick = '') {
	    $iFileId   = (int)$iFileId;
        $sJQueryJS = '';
        $sLangKey  = '_' . $this->_oConfig->getMainPrefix() . '_delete';
        if ($this->_deleteFile($iFileId)) {
        	$sRedirectMain = 'albums/my/main/';
        	if (mb_strlen($sAlbumUri) > 0) {
	    		$sAlbumUri = clear_xss($sAlbumUri);
	    		$sOwnerNick = clear_xss($sOwnerNick);
	        	$sRedirectMain =  'browse/album/' . $sAlbumUri . '/owner/' . $sOwnerNick;
        	}
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . $sRedirectMain;
            $sJQueryJS = genAjaxyPopupJS($iFileId, 'ajaxy_popup_result_div', $sRedirect);
        }
        else
        	$sLangKey .= '_error';
        
        echo MsgBox(_t($sLangKey)) . $sJQueryJS;
        exit;
	}
	
	function actionAlbums ($sParamName = '', $sParamValue = '', $sParamValue1 = '', $sParamValue2 = '', $sParamValue3 = '') {
	    switch ($sParamName) {
	        case 'my':
	            $this->actionAlbumsViewMy($sParamValue, $sParamValue1, $sParamValue2, $sParamValue3);	            	           
                break;
            case 'browse':
                if ('owner' == $sParamValue) {
                    $GLOBALS['oTopMenu']->setCurrentProfileID(getID($sParamValue1)); // select profile subtab, instead of module tab 
					$this->aPageTmpl['header'] = _t('_' . $this->_oConfig->getMainPrefix() . '_browse_by_owner', $sParamValue1);
				}
	        default:
        	    $sClassName = $this->_oConfig->getClassPrefix() . 'Search';
                bx_import('Search', $this->_aModule);
                $oSearch = new $sClassName($sParamValue, $sParamValue1, $sParamValue2, $sParamValue3);
                if (strlen($sParamValue) > 0 && strlen($sParamValue1) > 0 && isset($oSearch->aCurrent['restriction'][$sParamValue]))
                    $oSearch->aCurrent['restriction'][$sParamValue]['value'] = 'owner' == $sParamValue ? getID($sParamValue1) : $sParamValue1;
                
                $oSearch->aCurrent['paginate']['perPage'] = isset($_GET['per_page']) ? (int)$_GET['per_page'] : (int)$this->_oConfig->getGlParam('number_albums_browse');
                $oSearch->aCurrent['paginate']['page'] = isset($_GET['page']) ? (int)$_GET['page'] : $oSearch->aCurrent['paginate']['page']; 
                $sCode = $oSearch->getAlbumList($oSearch->aCurrent['paginate']['page'], $oSearch->aCurrent['paginate']['perPage']);
                if ($oSearch->aCurrent['paginate']['totalAlbumNum'] > 0) {
                    $sCode = $GLOBALS['oFunctions']->centerContent($sCode, '.sys_album_unit');
                    $aExclude = array($oSearch->aCurrent['name'] . '_mode', 'r');
                    $sMode = isset($_GET[$this->aCurrent['name'] . '_mode']) ? '&'.$this->aCurrent['name'] . '_mode='.$_GET[$this->aCurrent['name'] . '_mode'] : '';
                    $sLink  = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'albums/' . $sParamName;
                    $aLinkAddon = $oSearch->getLinkAddByPrams($aExclude);
                    $oPaginate = new BxDolPaginate(array(
                        'page_url' => $sLink . '&page={page}&per_page={per_page}',
                        'count' => $oSearch->aCurrent['paginate']['totalAlbumNum'],
                        'per_page' => $oSearch->aCurrent['paginate']['perPage'],
                        'page' => $oSearch->aCurrent['paginate']['page'],
                        'per_page_changer' => true,
                        'page_reloader' => true,
                        'on_change_per_page' => 'document.location=\'' . $sLink . $sMode . $aLinkAddon['params'] . '&page=1&per_page=\' + this.value;'
                    ));
                    $sPaginate = $oPaginate->getPaginate();
                }
                else
                    $sCode = MsgBox(_t('Empty'));
                $sCode = DesignBoxContent(_t('_' . $this->_oConfig->getMainPrefix()), $sCode . $sPaginate, 1);
                $this->_oTemplate->pageCode($this->aPageTmpl, array('page_main_code' => $sCode));
	    }
	}
	
	function actionAlbumsViewMy ($sParamValue = '', $sParamValue1 = '', $sParamValue2 = '', $sParamValue3 = '') {
	    if ($this->_iProfileId == 0 || !isLoggedActive()) {
	        $sKey  = _t('_' . $this->_oConfig->getMainPrefix() . '_access_denied');
            $sCode = DesignBoxContent($sKey, MsgBox($sKey), 1);
	    }
        else {
            //album actions check
    	    if (is_array($_POST['entry'])) {
    	        foreach ($_POST['entry'] as $iValue) {
    	            $iValue = (int)$iValue;
    	            switch (true) {
    	                case isset($_POST['action_delete']):
    	                    $iCount = $this->_deleteAlbumUnits($iValue);
                            if ($iCount == 0)
                                $this->oAlbums->removeAlbum($iValue);
    	                    break;
	                    case isset($_POST['action_move_to']):
	                        $this->oAlbums->moveObject((int)$_POST['album_id'], (int)$_POST['new_album'], $iValue);
                            break;
                        case isset($_POST['action_delete_object']):
                            $this->_deleteFile($iValue);
                            break;
    	            }
    	        }
    	    }
			$GLOBALS['oTopMenu']->setCurrentProfileID($this->_iProfileId);
	        $sClassName = $this->_oConfig->getClassPrefix() . 'PageAlbumsMy';
            bx_import('PageAlbumsMy', $this->_aModule);
            $oPage = new $sClassName($this, $this->_iProfileId, array($sParamValue, $sParamValue1, $sParamValue2, $sParamValue3));
            $sCode = $oPage->getCode();
        }
        $this->_oTemplate->pageCode($this->aPageTmpl, array('page_main_code' => $sCode), '', '', false);
	}
	
	function actionAlbumOrganize ($sAlbumUri) {
	    $aSort = $_POST['unit'];
	    $this->oAlbums->sortObjects($sAlbumUri, $aSort);
	}
	
	function actionAlbumReverse ($sAlbumUri) {
        $this->oAlbums->sortObjects($sAlbumUri);
        
        $sClassName = $this->_oConfig->getClassPrefix() . 'Search';
        bx_import('Search', $this->_aModule);
        $oSearch = new $sClassName('album', $sAlbumUri, 'owner', getNickName($this->_iProfileId));
        $oSearch->bAdminMode = false;
        $oSearch->aCurrent['view'] = 'short';
        $oSearch->aCurrent['restriction']['album']['value'] = $sAlbumUri;
        $oSearch->aCurrent['restriction']['albumType']['value'] = $oSearch->aCurrent['name'];
        $oSearch->aCurrent['paginate']['perPage'] = 1000;
        $aUnits = $oSearch->getSearchData();
		if (is_array($aUnits)) {
	        foreach ($aUnits as $aData)
	            $sCode .= $oSearch->displaySearchUnit($aData);
		}
        echo $sCode . '<div class="clear_both"></div>';
	}
	
    function actionAlbumDelete ($sAlbumUri) {
        $aAlbumInfo = $this->oAlbums->getAlbumInfo(array('fileUri'=>$sAlbumUri), array('ID', 'Owner'));
        if ((int)$aAlbumInfo['Owner'] != $this->_iProfileId)
            $sMessage =_t('_' . $this->_oConfig->getMainPrefix() . '_access_denied');
        else {
            $iCount = $this->_deleteAlbumUnits((int)$aAlbumInfo['ID']);
            if ($iCount > 0)
                $sMessage = _t('_' . $this->_oConfig->getMainPrefix() . '_album_delete_error', $iCount);
            else {
                $sMessage = _t('_' . $this->_oConfig->getMainPrefix() . '_album_delete_success');
                $this->oAlbums->removeAlbum((int)$aAlbumInfo['ID']); 
                $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'albums/my/';
                $sJQueryJS = genAjaxyPopupJS(1, 'ajaxy_popup_result_div', $sRedirect);
            }
        }
        echo MsgBox($sMessage, 1) . $sJQueryJS;
        exit;
    }
	
    function actionCalendar ($iYear = '', $iMonth = '') {
        $sClassName = $this->_oConfig->getClassPrefix() . 'Calendar';
        bx_import('Calendar', $this->_aModule);
        $oCalendar = new $sClassName($iYear, $iMonth, $this->_oDb, $this->_oTemplate, $this->_oConfig);
        $sTitle = _t('_' . $this->_oConfig->getMainPrefix() . '_top_menu_calendar');
        $sCode = DesignBoxContent($sTitle, $oCalendar->display(), 1);
        $this->aPageTmpl['header'] = $sTitle;
        $this->_oTemplate->pageCode($this->aPageTmpl, array('page_main_code' => $sCode));
    }
	
	function isAllowedAdd ($isPerformAction = false, $isDefineActions = false) {
		if ($this->isAdmin($this->_iProfileId)) return true;
        if (!isMember($this->_iProfileId)) return false;
        if (!$isDefineActions)
        	$this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, $this->_defineActionName('add'), $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
	}
	
    function isAllowedEdit (&$aFile, $isPerformAction = false) {
		if ($this->isAdmin($this->_iProfileId))
			return true;
        if ($aFile['medProfId'] == $this->_iProfileId)
            return true;
        else {
			if (!isMember($this->_iProfileId)) return false;
            $this->_defineActions();
            $aCheck = checkAction($this->_iProfileId, $this->_defineActionName('edit'), $isPerformAction);
            return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
        }
    }
    
    function isAllowedDelete (&$aFile, $isPerformAction = false) {
        if ($this->isAdmin($this->_iProfileId) || $aFile['medProfId'] == $this->_iProfileId) return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, $this->_defineActionName('delete'), $isPerformAction);
        if ($aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED) return true;
        return false;
    }
    
    function isAllowedView (&$aFile, $isPerformAction = false) {
    	$bAdmin = $this->isAdmin($this->_iProfileId);
    	if ($bAdmin || $aFile['medProfId'] == $this->_iProfileId) return true;    	
        if (!$bAdmin && $aFile['Approved'] != 'approved') return false;
        $aOwnerInfo = getProfileInfo($aFile['medProfId']);
        if ($aOwnerInfo['Status'] == 'Rejected' || $aOwnerInfo['Status'] == 'Suspended') return false;
        if (!$this->oAlbumPrivacy->check('album_view', $aFile['albumId'], $this->_iProfileId))
            return false;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, $this->_defineActionName('view'), $isPerformAction);
        if ($aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED)
            return false;
        return true;
    }
    
    function isAdmin ($iId = 0) {
        if (isAdmin($iId))
            return true;
        else
            return isModerator($iId);
    }
    
    function adminApproveFile ($iFileId) {
    	$iFileId = (int)$iFileId;
    	$aInfo = $this->_oDb->getFileInfo(array('fileId'=>$iFileId), true, array('Approved'));
        if ($aInfo['Approved'] != 'approved') {
	    	$this->_oDb->approveFile($iFileId);
	        $this->oAlbums->updateObjCounterById($iFileId);
	        bx_import('BxDolCategories');
            //tags & categories parsing
            $oTag = new BxDolTags();
            $oTag->reparseObjTags($this->_oConfig->getMainPrefix(), $iFileId);
            $oCateg = new BxDolCategories();
            $oCateg->reparseObjTags($this->_oConfig->getMainPrefix(), $iFileId);
        }
    }
    
    function adminDisapproveFile ($iFileId) {
    	$iFileId = (int)$iFileId;
    	$aInfo = $this->_oDb->getFileInfo(array('fileId'=>$iFileId), true, array('Approved'));
		$this->_oDb->disapproveFile($iFileId);
        if ($aInfo['Approved'] == 'approved') {
	        $this->oAlbums->updateObjCounterById($iFileId, false);
	        bx_import('BxDolCategories');
            //tags & categories parsing
            $oTag = new BxDolTags();
            $oTag->reparseObjTags($this->_oConfig->getMainPrefix(), $iFileId);
            $oCateg = new BxDolCategories();
            $oCateg->reparseObjTags($this->_oConfig->getMainPrefix(), $iFileId);
        }
    }
    
    function adminMakeFeatured ($iFileId) {
        $this->_oDb->makeFeatured($iFileId);
    }
    
    function adminMakeUnfeatured ($iFileId) {
        $this->_oDb->makeUnFeatured($iFileId);
    }
    
    function checkActions () {
        $aActionList = $this->_oConfig->getActionArray();
        foreach ($aActionList as $sKey => $aValue) {
            if (!is_array($_POST['entry']))
                return;
            if (isset($_POST[$sKey]) && method_exists($this, $aValue['method'])) {
                foreach ($_POST['entry'] as $iValue) {
                    $sComm = '$this->'.$aValue['method'].'('.(int)$iValue.');';
                    eval($sComm);
                }    
                break;
            }
        }
    }
    
    function getMemberList () {
        $sCode = '';
        if (isset($_GET['q'])) {
            $aMemList = $this->_oDb->getMemberList($_GET['q']);
            if (count($aMemList) > 0) {
                foreach ($aMemList as $aData)
                    $sCode .= $aData['NickName']." \n";
                }
            }
        return $sCode;
    }
	
    function getAdminMainPage (&$aMenu, $sParam = '') {
    	$GLOBALS['oAdmTemplate']->addLocation($this->_oConfig->getUri(), $this->_oConfig->getHomePath(), $this->_oConfig->getHomeUrl());
        $sModPref = $this->_oConfig->getMainPrefix();
        
        $sClassName = $this->_oConfig->getClassPrefix() . 'Search';
        bx_import('Search', $this->_aModule);
        $oSearch = new $sClassName();
        $oSearch->clearFilters();
        $oSearch->bAdminMode = true;
        $oSearch->id = 1;
        $oSearch->aCurrent['paginate']['perPage'] = (int)getParam($sModPref . '_all_count');
        
        $aSections = $this->aSectionsAdmin;
        $bAjaxMode = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') ? true : false;
        
        $sParam = clear_xss($sParam);
        if (mb_strlen($sParam) == 0 || !isset($aSections[$sParam]))
        	$sParam = 'approved';
    	
    	$oSearch->aCurrent['restriction']['activeStatus']['value'] = $sParam;
    	$aSections[$sParam]['active'] = 1;
        
		// array of buttons
        $aBtnsArray = $this->_oConfig->getActionArray();
        // making search result box menu
    	if ($aSections[$sParam]['exclude_btns'] == 'all')
    		$aBtnsArray = array();
    	elseif (is_array($aSections[$sParam]['exclude_btns'])) {
    		foreach ($aSections[$sParam]['exclude_btns'] as $sValue)
    			unset($aBtnsArray['action_' . $sValue]);
    	}
    	
        foreach ($aSections as $sKey => $aValue) {
        	$aSections[$sKey]['href'] = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/home/' . $sKey;
        	$aSections[$sKey]['title'] = _t('_' . $sModPref . '_' . $sKey);
        }
        if (!empty($aBtnsArray)) {
	        $aBtns = array();
	        foreach ($aBtnsArray as $sKey => $aValue)
	            $aBtns[$sKey] = _t($aValue['caption']);
	        $sManage = $oSearch->showAdminActionsPanel($oSearch->aCurrent['name'] . '_admin_form', $aBtns);
        }
        else {
        	$sManage = '';
        	$oSearch->bAdminMode = false;
        }
        
        if ($bAjaxMode) {
            $oSearch->aCurrent['restriction']['activeStatus']['value'] = process_db_input($sParam, BX_TAGS_STRIP);
            $sPostOwner = bx_get('owner');
            $sOwner = $sPostOwner !== false ? process_db_input($sPostOwner, BX_TAGS_STRIP) : '';
            if (strlen($sOwner) > 0)
                $oSearch->aCurrent['restriction']['owner']['value'] = getID($sOwner);
            $sCode = $oSearch->displayResultBlock();            
            $aCode = $this->getResultCodeArray($oSearch, $sCode);
            echo $this->_oTemplate->getFilesBox($aCode);
            exit;
        }
        $aInputs['status'] = array('type'=>'hidden');
        $aUnits = array(
        	'head' => $this->_oTemplate->getHeaderCode(),
        	'module_prefix' => $sModPref,
        	'search_form' => DesignBoxAdmin(_t('_' . $sModPref . '_admin'), $this->_oTemplate->getSearchForm($aInputs), $aMenu),
        );
        
        $sCode = $oSearch->displayResultBlock();
        $aCode = $this->getResultCodeArray($oSearch, $sCode);
        $sCode = $this->_oTemplate->getFilesBox($aCode, 'page_block_' . $oSearch->id);
        $aUnits['files'] = DesignBoxAdmin(_t('_' . $sModPref), $sCode, $aSections, $sManage);
        return $this->_oTemplate->parseHtmlByName('media_admin.html', $aUnits);
    }
        
    function getAdminSettings (&$aMenu) {
        $iId = $this->_oDb->getSettingsCategory();
        if(empty($iId))
           return MsgBox(_t('_' . $this->_oConfig->getMainPrefix() . '_msg_page_not_found'));
        bx_import('BxDolAdminSettings');

        $mixedResult = '';
        if(isset($_POST['save']) && isset($_POST['cat'])) {
            $oSettings = new BxDolAdminSettings($iId);
            $mixedResult = $oSettings->saveChanges($_POST);
        }
        
        $oSettings = new BxDolAdminSettings($iId);
        $sResult = $oSettings->getForm();
                   
        if($mixedResult !== true && !empty($mixedResult))
            $sResult = $mixedResult . $sResult;
        return DesignBoxAdmin(_t('_' . $this->_oConfig->getMainPrefix() . '_admin'), $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $sResult)), $aMenu);
    }
    
    function getBrowseParam ($sParamName, $sParamValue) {
    	$aPredef = array('tag', 'category');
    	return in_array($sParamName, $aPredef) ? uri2title($sParamValue) : $sParamValue;
    }
    
    function getResultCodeArray (&$oSearch, $sCode) {
    	$aCode = array(
    		'code' => MsgBox(_t('_Empty')),
    		'paginate' => ''
    	);
    	$iCount = $oSearch->aCurrent['paginate']['totalNum'];
    	if ($iCount > 0) {
            $aCode['code'] = $GLOBALS['oFunctions']->centerContent($sCode, '.sys_file_search_unit');
	        $sLink = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/home/' . $oSearch->aCurrent['restriction']['activeStatus']['value'];
	        $sKeyWord = bx_get('keyword');
	        if ($sKeyWord !== false)
	        	$sLink .= '&keyword=' . clear_xss($sKeyWord);
	        $aExclude = array('r');
	        $aLinkAddon = $oSearch->getLinkAddByPrams($aExclude);
	        $oPaginate = new BxDolPaginate(array(
	            'page_url' => $sLink,
	        	'count' => $iCount,
	            'per_page' => $oSearch->aCurrent['paginate']['perPage'],
	            'page' => $oSearch->aCurrent['paginate']['page'],
	            'per_page_changer' => true,
	            'page_reloader' => true,
	            'on_change_page' 	 => 'return !loadDynamicBlock(' . $oSearch->id . ', \'' . $sLink . $aLinkAddon['params'] . $aLinkAddon['paginate'] . '\');',
	            'on_change_per_page' => 'return !loadDynamicBlock(' . $oSearch->id . ', \'' . $sLink . $aLinkAddon['params'] . '&page=1&per_page=\' + this.value);'
	        ));
            $aCode['paginate'] = $oPaginate->getPaginate();
    	}
        return $aCode;
    }
    
    function getSubmitForm ($sFileUri, $sAction) {
    	$aEmails = array();
		$sFileLink = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $sFileUri; 
		switch ($sAction) {
		    case 'share': 
		        $aEmails = array(
		            'type' => 'text',
		            'name' => 'email',
		            'caption' => _t("_Enter email(s)"),
					'required' => true,
		            'checker' => array(
		                'func' => 'emailSet',
		                'error' => _t("_Incorrect Email")
					),
		        );
		        $aShareSites = array(
		            'type' => 'custom',
		            'content' => $this->_oTemplate->getSitesSetBox($sFileLink)
		        );                
		        break;
		    case 'report': 
		        $aEmails = array(
		            'type' => 'hidden',
		            'name' => 'email',
		            'value' => $GLOBALS['site']['email_notify']
		        );
		        $aShareSites = array(
		            'type' => 'custom',
		            'content' => ''
		        );
		        break;
		}
 		$aForm = array(
           'form_attrs' => array(
              'name' => 'submitAction',
              'action' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . $sAction . '/' . $sFileUri,
              'method' => 'post',
           ),
           'params' => array (
		        'db' => array(
		            'submit_name' => 'send',
		        ),
		        'checker_helper' => 'BxSupportCheckerHelper',
		    ),
           'inputs' => array(
               'sites' => $aShareSites,
               'email' => $aEmails,
               'message' => array(
					'type' => 'textarea',
					'name' => 'messageText',
					'caption' => _t('_Message text'),
					'value' => '',
					'required' => 1,
					'checker' => array (  
					    'func' => 'length',
					    'params' => array(3,65536),
					    'error' => _t('_td_err_incorrect_length'),
					),
               ),
               array(
                   'type' => 'input_set',
                   0 => array(
                       'type' => 'submit',
                       'name' => 'send',
                       'value' => _t('_Send')
                   ),
                   1 => array(
                       'type' => 'reset',
                       'name' => 'rest',
                       'value' => _t('_Reset')
                   ),
               ),
               'fileUri' => array(
                   'type' => 'hidden',
                   'name' => 'fileUri',
                   'value' => $sFileLink
               ),
               'mediaAction' => array(
                   'type' => 'hidden',
                   'name' => 'mediaAction',
                   'value' => $sAction
               )
           )
		);        
		return $aForm;
    }
    
    function sendFileInfo($sEmail, $sMessage, $sUrl, $sType = 'share') {  
        $aUser = getProfileInfo($this->_iProfileId);
        $sUrl  = urldecode($sUrl);
        $aPlus = array(
            'MediaType' => _t('_' . $this->_oConfig->getMainPrefix() . '_single'),
            'MediaUrl' => $sUrl,
            'SenderNickName' => $aUser ? $aUser['NickName'] : _t("_Visitor"),
            'UserExplanation' => $sMessage
        );
        bx_import('BxDolEmailTemplates');
        $rEmailTemplate = new BxDolEmailTemplates();
        $sSubject = 't_' . $this->_oConfig->getMainPrefix() . '_' . $sType;
        $aEmails = explode(",", $sEmail);
        foreach ($aEmails as $sMail) {
            $aTemplate = $rEmailTemplate->getTemplate($sSubject);
            $sMail = trim($sMail);
            if (sendMail($sMail, $aTemplate['Subject'], $aTemplate['Body'], '', $aPlus))
                return true;
        }
        return false;
    }
    
    function serviceRemoveObject ($iFileId) {
        $iFileId = (int)$iFileId;
        return $this->_deleteFile($iFileId);
    }
    
	function serviceGetFavoriteList ($iMember, $iFrom = 0, $iPerPage = 10) {
        return $this->_oDb->getFavorites($iMember, $iFrom, $iPerPage);
    }
    
	function serviceGetMemberMenuItem ($iUser) {
        $oMemberMenu = bx_instance('BxDolMemberMenu');
        // language keys;
        $aLanguageKeys = array(
            $this->_oConfig->getUri() => _t( '_' . $this->_oConfig->getMainPrefix()),
        );
        
        // fill all necessary data;
        $aLinkInfo = array(
            'item_img_src'  => $this->_oTemplate->getIconUrl('member_menu_sub.png'),
            'item_img_alt'  => $aLanguageKeys[$this->_oConfig->getUri()],
            'item_link'     => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'albums/my/',
            'item_onclick'  => null,
            'item_title'    => $aLanguageKeys[$this->_oConfig->getUri()],
            'extra_info'    => $this->_oDb->getFilesCountByAuthor($iUser),
        );
        return $oMemberMenu -> getGetExtraMenuLink($aLinkInfo);
    }
    
    function serviceGetSpyData () {
        return array(
            'handlers' => array(
                array('alert_unit' => $this->_oConfig->getMainPrefix(), 'alert_action' => 'add', 'module_uri' => $this->_oConfig->getUri(), 'module_class' => 'Module', 'module_method' => 'get_spy_post'),
                array('alert_unit' => $this->_oConfig->getMainPrefix(), 'alert_action' => 'commentPost', 'module_uri' => $this->_oConfig->getUri(), 'module_class' => 'Module', 'module_method' => 'get_spy_post'),
                array('alert_unit' => $this->_oConfig->getMainPrefix(), 'alert_action' => 'rate', 'module_uri' => $this->_oConfig->getUri(), 'module_class' => 'Module', 'module_method' => 'get_spy_post'),
            ),
            'alerts' => array(
                array('unit' => $this->_oConfig->getMainPrefix(), 'action' => 'add'),
                array('unit' => $this->_oConfig->getMainPrefix(), 'action' => 'commentPost'),
                array('unit' => $this->_oConfig->getMainPrefix(), 'action' => 'rate'),
            )
        );
    }
    
    function serviceGetSpyPost($sAction, $iObjectId = 0, $iSenderId = 0, $aExtraParams = array()) {
        $aRet = array();
        $aInfo = $this->_oDb->getFileInfo(array('fileId' => $iObjectId), true, array('medUri', 'medTitle', 'medProfId'));
        $aRet = array(
            'params'    => array(
                'profile_link'  => getProfileLink($iSenderId), 
                'profile_nick'  => getNickName($iSenderId),
                'entry_url'     => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aInfo['medUri'],
                'entry_caption' => $aInfo['medTitle'],
                'recipient_p_link' => getProfileLink($aInfo['medProfId']),
                'recipient_p_nick' => getNickName($aInfo['medProfId']),
            ),
            'recipient_id' => 0,
        );
        switch($sAction) {
            case 'add' :
                $aRet['lang_key']  = '_' . $this->_oConfig->getMainPrefix() . '_spy_added';
                break;
            case 'commentPost' :
                $aRet['lang_key']  = '_' . $this->_oConfig->getMainPrefix() . '_spy_comment_posted';
                $aRet['recipient_id'] = $aInfo['medProfId'];
                break;
            case 'rate' :
                $aRet['lang_key']  = '_' . $this->_oConfig->getMainPrefix() . '_spy_rated';
                $aRet['recipient_id'] = $aInfo['medProfId'];
                break;
        }
        return $aRet;
    }
    
    function serviceDeleteProfileData ($iProfileId) {
        if (!$iProfileId)
            return false;

        $aDataEntries = $this->_oDb->getFilesByAuthor($iProfileId);
        foreach ($aDataEntries as $iFileId) {
            $this->_deleteFile($iFileId);
        }
    }
    
    function serviceDeleteProfileAlbums ($iProfileId) {
		if (!$iProfileId)
            return false;
        $aDataEntries = $this->oAlbums->getAlbumList(array('owner'=>$iProfileId, 'status'=>'any', 'show_empty'=>true), 0, 0, true);
        foreach ($aDataEntries as $aValue)
             $this->oAlbums->removeAlbum($aValue['ID']);
    }

    function serviceResponseProfileDelete ($oAlert) {
        if (!($iProfileId = (int)$oAlert->iObject))
            return false;

        $this->serviceDeleteProfileData($iProfileId);
        $this->serviceDeleteProfileAlbums($iProfileId);
        return true;
    }
    
    // return array with info or false result
    function serviceCheckDelete ($iFileId) {
    	return $this->serviceCheckAction('delete', $iFileId);
    }
    
    function serviceCheckAction ($sAction, $iFileId) {
		$iFileId = (int)$iFileId;
		$sAction = ucfirst(strip_tags($sAction));
		if ($iFileId == 0 || strlen($sAction) == 0)
			return false;
		$aFileInfo = $this->_oDb->getFileInfo(array('fileId' => $iFileId), true, array('medID', 'medProfId', 'medExt', 'medDate', 'Approved'));
		if (empty($aFileInfo))
            return false;
        $sMethodName = 'isAllowed' . $sAction;
        if (!method_exists($this, $sMethodName))
        	return false;
        if (!$this->$sMethodName($aFileInfo))
            return false;
        return $aFileInfo;
    }
    
    function serviceGetSubscriptionParams ($sAction, $iEntryId) {
		$aDataEntry = $this->_oDb->getFileInfo(array('fileId' => $iEntryId), true, array('medUri', 'medTitle', 'Approved'));
    	if (empty($aDataEntry) || $aDataEntry['Approved'] != 'approved') {
            return array('skip' => true);
        }
        
        $aActionList = array(
        	'main' => '_sbs_main',
        	'rate' => '_sbs_comments',
        	'commentPost' => '_sbs_rates'
        );
        
        $sCurr = isset($aActionList[$sAction]) ? $aActionList[$sAction] : $aActionList['main'];
        return array (
            'skip' => false,
            'template' => array (
                'Subscription' => _t('_' . $this->_oConfig->getMainPrefix() . $sCurr),
                'ViewLink' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['medUri'],
            ),
        );
    }
    
    // info services
    function serviceGetAllAlbums ($iProfId, $sStatus = 'active') {
    	$aAlbumsArray = $this->oAlbums->getAlbumList(array('owner' => $iProfId, 'status' => $sStatus));
    	foreach ($aAlbumsArray as $aAlbum)
    		$aList[$aAlbum['ID']] = $aAlbum;
		return $aList;
    }
}

// support classes
class BxSupportCheckerHelper extends BxDolFormCheckerHelper {
	function checkEmailSet($sSet) {
        $aEmails = explode(',', $sSet);
        foreach ($aEmails as $sEmail) {
        	$sEmail = trim($sEmail);
			if (!preg_match('/^[a-z0-9_\-]+(\.[_a-z0-9\-]+)*@([_a-z0-9\-]+\.)+([a-z]{2}|aero|arpa|asia|biz|cat|com|coop|edu|gov|info|int|jobs|mil|mobi|museum|name|net|org|pro|tel|travel)$/i', $sEmail))
	            return false;
        }
        return true;
    }
}

?>
