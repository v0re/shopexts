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
require_once(BX_DIRECTORY_PATH_PLUGINS . 'Services_JSON.php');
define('PROFILE_PHOTO_CATEGORY', 'Profile photos');

class BxPhotosModule extends BxDolFilesModule {
    function BxPhotosModule (&$aModule) {
        parent::BxDolFilesModule($aModule);
		$this->aSectionsAdmin['pending'] = array(
			'exclude_btns' => array('deactivate', 'featured', 'unfeatured')
		);
    }
    
    function actionGetCurrentImage ($iPicId) {
        $iPicId = (int)$iPicId;
        if ($iPicId > 0) {
            require_once('BxPhotosSearch.php');
            $oMedia = new BxPhotosSearch();
            $aInfo = $oMedia->serviceGetPhotoArray($iPicId, 'file');
            $aInfo['ownerUrl'] = getProfileLink($aInfo['owner']);
            $aInfo['ownerName'] = getNickName($aInfo['owner']);
            $aInfo['date'] = defineTimeInterval($aInfo['date']);
            $oMedia->getRatePart();
            $aInfo['rate'] = $oMedia->oRate->getJustVotingElement(0, 0, $aInfo['rate']);
            $aLinkAddon = $oMedia->getLinkAddByPrams();
            $oPaginate = new BxDolPaginate(array(
                'count' => (int)$_GET['total'],
                'per_page' => 1,
                'page' => (int)$_GET['page'],
                'info' => false,
                'per_page_changer' => false,
                'page_reloader' => false,
                'on_change_page' => 'getCurrentImage({page})',
            ));
            $aInfo['paginate'] = $oPaginate->getPaginate();
            header('Content-Type:text/javascript');
            $oJSON = new Services_JSON();
            echo $oJSON->encode($aInfo);
        }
    }
        
    function actionGetImage ($sParamValue, $sParamValue1) {
        $sParamValue  = clear_xss($sParamValue);
        $sParamValue1 = clear_xss($sParamValue1);
		$iPointPos    = strrpos($sParamValue1, '.');
		$sKey = substr($sParamValue1, 0, $iPointPos);
        $iId = $this->_oDb->getIdByHash($sKey);
		if ($iId > 0) {
			$sExt = substr($sParamValue1, $iPointPos + 1);
			switch ($sExt) {
				case 'png':
					$sCntType = 'image/x-png';
					break;
				case 'gif':
					$sCntType = 'image/gif';
					break;
				default:
					$sCntType = 'image/jpeg';
			}
	        $sPath = $this->_oConfig->getFilesPath() . $iId . str_replace('{ext}', $sExt, $this->_oConfig->aFilePostfix[$sParamValue]);
	        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	        header("Content-Type:" . $sCntType);
	        header("Content-Length: " . filesize($sPath));
	        readfile($sPath);
		}
		else {
			header("HTTP/1.0 404 Not Found");
			echo _t('_sys_request_page_not_found_cpt');
		}
        exit;
    }
        
	function actionAlbumsViewMy ($sParamValue = '', $sParamValue1 = '', $sParamValue2 = '', $sParamValue3 = '') {
		$sAction = bx_get('action');
		if ($sAction !== false) {
			require_once('BxPhotosUploader.php');
			$oUploader = new BxPhotosUploader();

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
					echo $oUploader->servicePerformMultiPhotoUpload(); exit;
					break;

				default:
					break;
			}
		} else {
			parent::actionAlbumsViewMy($sParamValue, $sParamValue1, $sParamValue2, $sParamValue3);
		}
	}
	
    function serviceGetProfileCat () {
        return PROFILE_PHOTO_CATEGORY;
    }
    
    function serviceGetWallData(){
        return array(
            'handlers' => array(
                array(
                    'alert_unit' => 'bx_photos', 
                    'alert_action' => 'add', 
                    'module_uri' => 'photos', 
                    'module_class' => 'Search', 
                    'module_method' => 'get_wall_post'
                )
            ),
            'alerts' => array(
                array('unit' => 'bx_photos', 'action' => 'add')
            )
        );
    }
	
}

?>