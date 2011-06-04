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
define('PROFILE_VIDEO_CATEGORY', 'Profile videos');

class BxVideosModule extends BxDolFilesModule {
    function BxVideosModule (&$aModule) {
        parent::BxDolFilesModule($aModule);
        
        // add more sections for administration
        $this->aSectionsAdmin['processing'] = array('exclude_btns' => 'all');
        $this->aSectionsAdmin['failed'] = array(
        	'exclude_btns' => array('activate', 'deactivate', 'featured', 'unfeatured')
    	);
    }
    
    function serviceGetProfileCat () {
        return PROFILE_VIDEO_CATEGORY;
    }
    
	function actionAlbumsViewMy ($sParamValue = '', $sParamValue1 = '', $sParamValue2 = '', $sParamValue3 = '') {
		$sAction = bx_get('action');
		if ($sAction !== false) {
			require_once('BxVideosUploader.php');
			$oUploader = new BxVideosUploader();

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
					echo $oUploader->servicePerformMultiVideoUpload(); exit;
					break;

				default:
					break;
			}
		} else {
			parent::actionAlbumsViewMy($sParamValue, $sParamValue1, $sParamValue2, $sParamValue3);
		}
	}
	
    function getEmbedCode ($iFileId, $aExtra = array()) {
        return $this->_oTemplate->getEmbedCode($iFileId, $aExtra);
    }
            
    function serviceGetWallData(){
        return array(
            'handlers' => array(
                array(
                    'alert_unit' => 'bx_videos', 
                    'alert_action' => 'add', 
                    'module_uri' => 'videos', 
                    'module_class' => 'Search', 
                    'module_method' => 'get_wall_post'
                )
            ),
            'alerts' => array(
                array('unit' => 'bx_videos', 'action' => 'add')
            )
        );
    }
}

?>