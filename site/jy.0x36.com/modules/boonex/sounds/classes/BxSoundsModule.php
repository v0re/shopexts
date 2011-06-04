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
define('PROFILE_SOUND_CATEGORY', 'Profile sounds');

class BxSoundsModule extends BxDolFilesModule {
    function BxSoundsModule (&$aModule) {
        parent::BxDolFilesModule($aModule);
        
        // add more sections for administration
        $this->aSectionsAdmin['processing'] = array('exclude_btns' => 'all');
        $this->aSectionsAdmin['failed'] = array(
        	'exclude_btns' => array('activate', 'deactivate', 'featured', 'unfeatured')
    	);
    }
    
    function serviceGetProfileCat () {
        return PROFILE_SOUND_CATEGORY;
    }
    
	function actionAlbumsViewMy ($sParamValue = '', $sParamValue1 = '', $sParamValue2 = '', $sParamValue3 = '') {
		$sAction = bx_get('action');
		if ($sAction !== false) {
			require_once('BxSoundsUploader.php');
			$oUploader = new BxSoundsUploader();

			switch($sAction) {
				case 'accept_upload':
					echo $oUploader->serviceAcceptFile(); exit;
					break;
				case 'accept_record':
					echo $oUploader->serviceAcceptRecordFile(); exit;
					break;
				case 'cancel_file':
					echo $oUploader->serviceCancelFileInfo(); exit;
					break;
				case 'accept_file_info':
					echo $oUploader->serviceAcceptFileInfo(); exit;
					break;

				case 'accept_multi_files':
					echo $oUploader->servicePerformMultiMusicUpload(); exit;
					break;

				default:
					break;
			}
		} else {
			parent::actionAlbumsViewMy($sParamValue, $sParamValue1, $sParamValue2, $sParamValue3);
		}
	}
	
    function serviceGetWallData(){
        return array(
            'handlers' => array(
                array(
                    'alert_unit' => 'bx_sounds', 
                    'alert_action' => 'add', 
                    'module_uri' => 'sounds', 
                    'module_class' => 'Search', 
                    'module_method' => 'get_wall_post'
                )
            ),
            'alerts' => array(
                array('unit' => 'bx_sounds', 'action' => 'add')
            )
        );
    }
    
    function getEmbedCode ($iFileId) {
        return $this->_oTemplate->getEmbedCode($iFileId);
    }
}

?>