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

bx_import('BxDolFilesModule');

class BxFilesModule extends BxDolFilesModule {
    /**
	 * Constructor
	 */
	function BxFilesModule($aModule) {
	    parent::BxDolFilesModule($aModule);
	    $this->_aMemActions[] = 'download';
		$this->aSectionsAdmin['pending'] = array(
			'exclude_btns' => array('deactivate', 'featured', 'unfeatured')
		);
	}
	
	function _getOriginalExt (&$aInfo, $sTmpl, $sKey = '{ext}') {
    	return str_replace($sKey, sha1($aInfo['medDate']), $sTmpl);
    }
	
	function actionDownload ($sFileUri) {
        $aInfo = $this->_oDb->getFileInfo(array('fileUri'=>$sFileUri), false, array('medID', 'medProfId', 'medExt', 'medDate', 'Type'));
        if (!$this->isAllowedDownload($aInfo)) {
			$sCode = MsgBox(_t('_Access denied'));
			$this->aPageTmpl['name_index'] = 44;
			$this->_oTemplate->pageCode($this->aPageTmpl, array('page_main_code' => $sCode));
        }
        else {
			$sPathFull = $this->_oConfig->getHomePath() . 'data/files/' . $aInfo['medID'] . '_' . sha1($aInfo['medDate']);
			if (file_exists($sPathFull)) {
				header('Connection: close');
				header('Content-Type: ' . $aInfo['Type']);
				header('Content-Length: ' . filesize($sPathFull));
				header('Last-Modified: ' . gmdate('r', filemtime($sPathFull)));
				header('Content-Disposition: attachment; filename="' . $sFileUri . '.' . $aInfo['medExt'] . '";');
				readfile($sPathFull);
				$this->_oDb->updateDownloadsCount($sFileUri);
				exit;
			}
			else {				
				header("HTTP/1.1 404 Not Found");
				$sCode = MsgBox(_t('_bx_files_not_found'));
				$this->aPageTmpl['name_index'] = 44;
				$this->_oTemplate->pageCode($this->aPageTmpl, array('page_main_code' => $sCode));
			}
        }
	}
	
    function isAllowedDownload (&$aFile, $isPerformAction = false) {
        if ($this->isAdmin($this->_iProfileId) || $aFile['medProfId'] == $this->_iProfileId) return true;
        if (!$this->oPrivacy->check('download', $aFile['medID'], $this->_iProfileId))
            return false;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_FILES_DOWNLOAD, $isPerformAction);
        if ($aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED)
            return false;
        return true;
    }

	function actionAlbumsViewMy ($sParamValue = '', $sParamValue1 = '', $sParamValue2 = '', $sParamValue3 = '') {
		$sAction = bx_get('action');
		if ($sAction !== false) {
			require_once('BxFilesUploader.php');
			$oUploader = new BxFilesUploader();

			switch($sAction) {
				case 'accept_upload':
					echo $oUploader->serviceAcceptFile(); exit;
					break;
				case 'accept_record':
					echo $oUploader->serviceAcceptRecordFile(); exit;
					break;
				case 'accept_embed':
					echo $oUploader->serviceAcceptEmbedFile(); exit;
					break;
				case 'cancel_file':
					echo $oUploader->serviceCancelFileInfo(); exit;
					break;
				case 'accept_file_info':
					echo $oUploader->serviceAcceptFileInfo(); exit;
					break;

				case 'accept_multi_files':
					echo $oUploader->servicePerformMultiFileUpload(); exit;
					break;

				default:
					break;
			}
		} else {
			parent::actionAlbumsViewMy($sParamValue, $sParamValue1, $sParamValue2, $sParamValue3);
		}
	}
	
    function actionBrowse ($sParamName = '', $sParamValue = '', $sParamValue1 = '', $sParamValue2 = '', $sParamValue3 = '') {
        $bAlbumView = false;
        if ($sParamName == 'album' && $sParamValue1 == 'owner') {
            $bAlbumView = true;
            $GLOBALS['oTopMenu']->setCustomSubHeader($sParamValue);
            $oAlbums = new BxDolAlbums($this->_oConfig->getMainPrefix());
            $aAlbumInfo = $oAlbums->getAlbumInfo(array('fileUri' => $sParamValue, 'owner' => getID($sParamValue2)), array('ID', 'Caption', 'Owner', 'AllowAlbumView'));
            $GLOBALS['oTopMenu']->setCustomSubHeader($aAlbumInfo['Caption']);
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
        require_once('BxFilesSearch.php');
        $oSearch = new BxFilesSearch($sParamName, $sParamValue, $sParamValue1, $sParamValue2);
        $sRss = bx_get('rss');
        if ($sRss !== false && $sRss) {
            $oSearch->aCurrent['paginate']['perPage'] = 10;
            echo $oSearch->rss();
            exit;
        }
		
		$sTopPostfix = isset($oSearch->aCurrent['restriction'][$sParamName]) || $oSearch->aCurrent['sorting'] == $sParamName ? $sParamName : 'all';
		$sCaption = _t('_' . $this->_oConfig->getMainPrefix() . '_top_menu_' . $sTopPostfix);
        if (strlen($sParamName) > 0 && strlen($sParamValue) > 0 && isset($oSearch->aCurrent['restriction'][$sParamName])) {
            $oSearch->aCurrent['restriction'][$sParamName]['value'] = $sParamValue;
			$sCaption = _t('_' . $this->_oConfig->getMainPrefix() . '_browse_by_' . $sParamName, $sParamValue);
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
                    $sArg .= '/' . $sValue;
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
                'on_change_per_page' => 'return !loadDynamicBlock(1, \'' . $sLink . '&page=1&per_page=\' + this.value);'
            ));
            $sPaginate = $oPaginate->getPaginate();
        }
        else
            $sCode = MsgBox(_t('_Empty'));
        $aMenu = array();
        $sCode = DesignBoxContent($sCaption, $sCode, 1);
        $this->aPageTmpl['css_name'] = array('browse.css');
        $this->aPageTmpl['header'] = $sCaption;
        $this->_oTemplate->pageCode($this->aPageTmpl, array('page_main_code' => $sCode));
    }

	function actionEdit ($iFileId) {
        $iFileId = (int)$iFileId > 0 ? (int)$iFileId : (int)$_POST['fileId'];
        if ($iFileId == 0)
           exit;
        
        $this->aPageTmpl['name_index'] = 44;
        $sJsCode = '<script language="javascript">window.setTimeout(function () { window.parent.opener.location = window.parent.opener.location; window.parent.close(); }, 3000); </script>';
        $aManageArray = array('medTitle', 'medTags', 'medDesc', 'medProfId', 'Categories', 'AllowDownload');
        $aInfo = $this->_oDb->getFileInfo(array('fileId'=>$iFileId), false, $aManageArray);
        if (!$this->isAllowedEdit($aInfo))
           $sCode = MsgBox(_t('_' . $this->_oConfig->getMainPrefix() . '_access_denied')) . $sJsCode;
        else {
			$oCategories = new BxDolCategories();
			$oCategories->getTagObjectConfig();            	
			$aCategories = $oCategories->getGroupChooser($this->_oConfig->getMainPrefix(), $this->_iProfileId, true);
			$aCategories['value'] = explode(CATEGORIES_DIVIDER, $aInfo['Categories']);
			
			$aAllowDownload = $this->oPrivacy->getGroupChooser($this->_iProfileId, $this->_oConfig->getUri(), 'download');
			$aAllowDownload['value'] = $aInfo['AllowDownload'];
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
					'AllowDownload' => $aAllowDownload,
					'fileId' => array(
						'type' => 'hidden',
						'name' => 'fileId',
						'value' => $iFileId,
					),
					'medProfId' => array(
						'type' => 'hidden',
						'name' => 'medProfId',
						'value' => $this->_iProfileId,
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
    
    function serviceGetWallData(){
        return array(
            'handlers' => array(
                array(
                    'alert_unit' => 'bx_files', 
                    'alert_action' => 'add', 
                    'module_uri' => 'files', 
                    'module_class' => 'Search', 
                    'module_method' => 'get_wall_post'
                )
            ),
            'alerts' => array(
                array('unit' => 'bx_files', 'action' => 'add')
            )
        );
    }
}
?>