<?php

/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -----------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2006 BoonEx Group
*     website              : http://www.boonex.com/
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software. This work is licensed under a Creative Commons Attribution 3.0 License. 
* http://creativecommons.org/licenses/by/3.0/
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the Creative Commons Attribution 3.0 License for more details. 
* You should have received a copy of the Creative Commons Attribution 3.0 License along with Dolphin, 
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

//require_once(BX_DIRECTORY_PATH_MODULES . 'boonex/photos/classes/BxPhotosModule.php' );

bx_import('BxDolFilesUploader');
bx_import('BxDolCategories');
bx_import('BxDolAlbums');
bx_import('BxDolModule');

global $sModulesPath;
global $sModulesUrl;
$sModule = "photo";
global $sFilesPath;
global $sFilesUrl;
require_once($sModulesPath . $sModule . '/inc/header.inc.php');
require_once($sModulesPath . $sModule . '/inc/constants.inc.php');
require_once($sModulesPath . $sModule . '/inc/functions.inc.php');

class BxPhotosUploader extends BxDolFilesUploader {
	// constructor
	function BxPhotosUploader() {
		parent::BxDolFilesUploader('Photo');

		$this->oModule = BxDolModule::getInstance('BxPhotosModule');
		$this->sWorkingFile = $this->oModule->_oConfig->getBaseUri() . 'albums/my/add_objects';
		$this->sMultiUploaderParams['accept_format'] = $this->oModule->_oConfig->getAvailableFlashExts();
		$this->sMultiUploaderParams['accept_file'] = $this->sWorkingFile;
		$this->sMultiUploaderParams['form_caption'] = _t('_bx_photos_add_objects');

		/*$this->sMultiUploaderParams['post_params'] = '{action: "accept_multi_files", oid: "'.$this->_iOwnerId.'", "extra_param_album": "__extra_param_album__"}';*/
	}

	/*
	* Service - generate photo upload main form
	*
	* params
	* $sPredefCategory - TODO remove
	* $aExtras - TODO predefined album and category should appear here with names: predef_album and predef_category
	*/
	function serviceGenPhotoUploadForm($aExtras = array()) {
		return $this->GenMainAddPhotosForm($aExtras);
	}

	function GenMainAddPhotosForm($aExtras = array()) {
	    $aUploaders = array_keys($this->oModule->_oConfig->getUploaderList());
		return $this->_GenMainAddCommonForm($aExtras, $aUploaders);
	}

	function getEmbedFormFile() {
        $sKey = $this->oModule->_oConfig->getGlParam('flickr_photo_api');
	    return ($sKey != '') ? $this->_getEmbedFormFile() : MsgBox(_t('_bx_photos_flickr_key_not_exist'));
    }

    function getRecordFormFile() {
	    $sCustomRecorderObject = getApplicationContent('photo', 'shooter', array('id' => $this->_getAuthorId(), 'password' => $this->_getAuthorPassword(), 'extra' => ''), true);
	    return $this->_getRecordFormFile($sCustomRecorderObject);
    }

    function getUploadFormFile() {
		return $this->_getUploadFormFile();
    }

	function GenSendFileInfoForm($iFileID, $aDefaultValues = array()) {
		$sPhotoUrl = "";
		if(isset($aDefaultValues['image']))
			$sPhotoUrl = $aDefaultValues['image'];
		else if(!empty($iFileID)) {
			$aPhotoInfo = BxDolService::call('photos', 'get_photo_array', array($iFileID), 'Search');
			$sPhotoUrl = $aPhotoInfo['file'];
		}
		$sProtoEl = '<img src="' . $sPhotoUrl . '" />';

		$aPossibleImage = array();
		$aPossibleImage['preview_image'] = array(
			'type' => 'custom',
			'content' => $sProtoEl,
			'caption' => _t('_bx_photos_preview'),
		);

		return $this->_GenSendFileInfoForm($iFileID, $aDefaultValues, $aPossibleImage, array());
	}

	function serviceCancelFileInfo() {
		$iFileID = (int)$_GET['file_id'];
		if ($iFileID) {
			if ($this->oModule->serviceRemoveObject($iFileID)) {
				//deletePhoto($iFileID);
				return 1;
			}
		}
		return 0;
	}

	function serviceAcceptFile() {
		$sResult = '';
		if ($_FILES) {
			for ($i=0; $i<count($_FILES['file']['tmp_name']); $i++) {
				if ($_FILES['file']['error'][$i] || $_FILES['file']['size'][$i] > $this->iMaxFilesize)
					continue;
				$sResult .= $this->_sharePhoto($_FILES['file']['tmp_name'][$i], true, $_FILES['file']['name'][$i]);
			}
		}
		return ($sResult!='') ? $this->GenJquieryInjection() . $sResult : '';
	}

	function serviceAcceptRecordFile() {
		$sResult = $this->_recordPhoto();
		return ($sResult!='') ? $this->GenJquieryInjection() . $sResult : '';
	}

	function serviceAcceptEmbedFile() {
		$sErrorReturn = '<script type="text/javascript">parent.' . $this->_sJsPostObject . '.showErrorMsg("photo_embed_failed_message");parent.' . $this->_sJsPostObject . '.resetEmbed();</script>';
		$sPhotoId = substr(trim($_POST['embed']), -11, 10);
		if(empty($sPhotoId)) return $sErrorReturn;

        $sApiKey = $this->oModule->_oConfig->getGlParam('flickr_photo_api');
        $sPhotoUrl = str_replace("#api_key#", $sApiKey, FLICKR_PHOTO_RSS);
        $sPhotoUrl = str_replace("#photo#", $sPhotoId, $sPhotoUrl);
		$sPhotoDataOriginal = $this->embedReadUrl($sPhotoUrl);
        
        $aResult = $this->embedGetTagAttributes($sPhotoDataOriginal, "rsp");
        if($aResult["stat"] == "fail")
        {
            $aResult = $this->embedGetTagAttributes($sPhotoDataOriginal, "err");
            $sNewError = $aResult["msg"];
            $sErrorReturn = '<script type="text/javascript">parent.' . $this->_sJsPostObject . '.changeErrorMsgBoxMsg("photo_embed_failed_message", "'.$sNewError.'"); parent.' . $this->_sJsPostObject . '.showErrorMsg("photo_embed_failed_message");parent.' . $this->_sJsPostObject . '.resetEmbed();</script>';
            return $sErrorReturn;
        }
        
		$sPhotoData = $this->embedGetTagContents($sPhotoDataOriginal, "photo");
		if(empty($sPhotoData)) return $sErrorReturn;
		
		$sTitle = $this->embedGetTagContents($sPhotoData, "title");
		$sDesc = $this->embedGetTagContents($sPhotoData, "description");
		$sTags = strip_tags($this->embedGetTagContents($sPhotoData, "tags"));
		$sTags = trim(str_replace("\n", " ", $sTags));
		$sTags = trim(str_replace("\t", "", $sTags));
		
		$aPhoto = $this->embedGetTagAttributes($sPhotoDataOriginal, "photo");
		$sImage = str_replace("#id#", $sPhotoId, FLICKR_PHOTO_URL);
		$sImage = str_replace("#farm#", $aPhoto['farm'], $sImage);
		$sImage = str_replace("#server#", $aPhoto['server'], $sImage);
		$sExt = "jpg";
		$sMode = "";
		if(isset($aPhoto['originalsecret']))
		{
			$aPhoto['secret'] = $aPhoto['originalsecret'];
			$sExt = $aPhoto['originalformat'];
			$sMode = "_o";
		}
		$sImage = str_replace("#secret#", $aPhoto['secret'], $sImage);
		$sImage = str_replace("#mode#", $sMode, $sImage);
		$sImage = str_replace("#ext#", $sExt, $sImage);
		if(empty($sTitle)) return $sErrorReturn;

		$sResult = $this->_embedPhoto($sPhotoId, $sTitle, $sDesc, $sTags, $sImage);
		return ($sResult!='') ? $this->GenJquieryInjection() . $sResult : '';
	}

	function serviceAcceptFileInfo() {
        $iAuthorId = $this->_getAuthorId();
		$sJSPhotoId = (int)$_POST['file_id'];
		switch($_POST['type'])
		{
			case 'embed':
			case 'record':
				global $sFilesPath;
				$iPhotoID = (int)$this->performPhotoUpload($sFilesPath . $iAuthorId . IMAGE_EXTENSION, array(), false, false);
				removeFiles($iAuthorId);
				break;
			case 'upload':
			default:
				$iPhotoID = $sJSPhotoId;
				break;
		}

		if ($iPhotoID && $iAuthorId) {
			$sTitle = $_POST['title'];
			$sTags = $_POST['tags'];
			$sDescription = $_POST['description'];

			$aCategories = array();
			foreach ($_POST['Categories'] as $sKey => $sVal) {
				if ($sVal != '') {
					$aCategories[] = $sVal;
				}
			}
			$sCategories = implode(CATEGORIES_DIVIDER, $aCategories);
            if ($this->initPhotoFile($iPhotoID, $sTitle, $sCategories, $sTags, $sDescription)) {
                require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolAlerts.php');
                $oZ = new BxDolAlerts('bx_photos', 'add', $iPhotoID, $iAuthorId, $this->_getExtraParams($_POST));
                $oZ->alert();
                //--- BxPhoto -> Upload unit for Alerts Engine ---//
				return '<script type="text/javascript">parent.' . $this->_sJsPostObject . '.onSuccessSendingFileInfo("' . $sJSPhotoId . '");</script>';
			}
		}
		return '<script type="text/javascript">parent.' . $this->_sJsPostObject . '.showErrorMsg("photo_failed_message");</script>';
	}

	function _embedPhoto($sPhotoId, $sTitle, $sDesc, $sTags, $sImage) {
		$sAuthorCheck = $this->checkAuthorBeforeAdd();
		if(empty($sAuthorCheck)) {
			$sEmbedThumbUrl = photo_getEmbedThumbnail($this->_getAuthorId(), $sImage);
			if($sEmbedThumbUrl)
			{
				$aDefault = array('photo' => $sPhotoId, 'title' => $sTitle, 'description' => $sDesc, 'tags' => $sTags, 'image' => $sEmbedThumbUrl, 'type' => "embed");
				return $this->GenSendFileInfoForm(1, $aDefault);
			}
			else 
				return $this->getPhotoAddError();
		}
		else
			return $sAuthorCheck;
	}

	// function checkAuthorBeforeAdd() {
		// return $this->_getAuthorId() ? "" : '<script type="text/javascript">alert("' . htmlspecialchars(addslashes(_t('_LOGIN_REQUIRED_AE1'))) . '");</script>';
	// }

	function servicePerformMultiPhotoUpload () {
		$this->_iOwnerId = (int)$_POST['oid'];
		if ($_FILES) {
            if ($_FILES['Filedata']['error'] || $_FILES['Filedata']['size'] > $this->iMaxFilesize)
                return;
            $sResult .= $this->_sharePhoto($_FILES['Filedata']['tmp_name'], true, $_FILES['Filedata']['name']);
            return ($sResult!='') ? $this->GenJquieryInjection() . $sResult : '';
		}

	}

	function servicePerformPhotoUpload ($sTmpFilename, $aFileInfo, $isUpdateThumb, $iAuthorId = 0) {
		if (!$iAuthorId)
			$iAuthorId = $this->_iOwnerId;
	    return $this->performPhotoUpload($sTmpFilename, $aFileInfo, $isUpdateThumb, false, 0, $iAuthorId);
	}

	function servicePerformPhotoReplace($sTmpFilename, $aFileInfo, $isUpdateThumb, $iPhotoID) {
	    return $this->performPhotoUpload($sTmpFilename, $aFileInfo, $isUpdateThumb, false, $iPhotoID);
	}

	function _sharePhoto($sFilePath, $isMoveUploadedFile = true, $sRealFilename = '') {
		$sAuthorCheck = $this->checkAuthorBeforeAdd();
		if(empty($sAuthorCheck)) {
		    $iPointPos = strrpos($sRealFilename, '.');
            $sExt = substr($sRealFilename, $iPointPos + 1);
            if (!$this->oModule->_oConfig->checkAllowedExts(strtolower($sExt)))
                return $this->getPhotoAddError();
			$this->sTempFilename = substr($sRealFilename, 0, $iPointPos);
			$iMID = $this->performPhotoUpload($sFilePath, array(), false, $isMoveUploadedFile);
			if ($iMID>0) { //upload success
				$aDefault = array('title' => $this->sTempFilename, 'description' => $this->sTempFilename);
				return $this->GenSendFileInfoForm($iMID, $aDefault);
			} else
				return $this->getPhotoAddError();
		} else
			return $sAuthorCheck;
	}
	
	function _recordPhoto() {
		$sAuthorCheck = $this->checkAuthorBeforeAdd();
		if(empty($sAuthorCheck)) {
            global $sFilesPath;
			$sRecordThumbUrl = photo_getRecordThumbnail($this->_getAuthorId());
			if($sRecordThumbUrl) {
				$aDefault = array('image' => $sRecordThumbUrl, 'type' => "record");
				return $this->GenSendFileInfoForm(1, $aDefault);
			} else
				return $this->getPhotoAddError();
		}
		else
			return $sAuthorCheck;
	}
	
	function getPhotoAddError() {
		return '<script type="text/javascript">parent.' . $this->_sJsPostObject . '.showErrorMsg("photo_failed_file_message");</script>';
	}

	function initPhotoFile($iPhotoID, $sTitle, $sCategories, $sTags, $sDesc) {
		$sStatus = getParam('bx_photos_activation') == 'on' ? 'approved' : 'pending';
		$sUri = uriGenerate($sTitle, $this->oModule->_oDb->sFileTable, $this->oModule->_oDb->aFileFields['medUri']);
		$bRes = $this->oModule->_oDb->updateData($iPhotoID, array('Categories'=>$sCategories, 'medTitle'=>$sTitle, 'medUri'=>$sUri, 'medTags'=>$sTags, 'medDesc'=>$sDesc, 'Approved'=>$sStatus));
        
		if ($bRes) {
	        $oTag = new BxDolTags();
            $oTag->reparseObjTags('bx_photos', $iPhotoID);
            $oCateg = new BxDolCategories();
            $oCateg->reparseObjTags('bx_photos', $iPhotoID);                                               
		}
		return $bRes;
	}

	// simple upload
	function performPhotoUpload($sTmpFile, $aFileInfo, $bAutoAssign2Profile = false, $isMoveUploadedFile = true, $iChangingPhotoID = 0, $iAuthorId = 0) {
		global $dir;
	    
		$iLastID = -1;

		if (!$iAuthorId)
			$iAuthorId = $this->_iOwnerId;
		$this->oModule = BxDolModule::getInstance('BxPhotosModule');
		// checker for flash uploader
		if (!$this->oModule->_iProfileId)
			$this->oModule->_iProfileId = $this->_iOwnerId;
		if (!$iAuthorId || file_exists($sTmpFile) == false || !$this->oModule->isAllowedAdd())
			return false;

		$sMediaDir = $this->oModule->_oConfig->getFilesPath();

		if (! $sMediaDir) {
			@unlink($sTmpFile);
			return false;
		}

		$sTempFileName = $sMediaDir . $iAuthorId . '_temp';
        @unlink($sTempFileName);        

        if (($isMoveUploadedFile && is_uploaded_file($sTmpFile)) || !$isMoveUploadedFile) {

            if ($isMoveUploadedFile) {
                move_uploaded_file($sTmpFile, $sTempFileName);
                @unlink($sTmpFile);
            } else {
                $sTempFileName = $sTmpFile;
            }

			@chmod($sTempFileName, 0644);
			if(file_exists($sTempFileName) && filesize($sTempFileName)>0) {
				$aSize = getimagesize($sTempFileName);
				if (!$aSize) {
					@unlink($sTempFileName);
					return false;
				}

				switch($aSize[2]) {
					case IMAGETYPE_JPEG: $sExtension = '.jpg'; break;
					case IMAGETYPE_GIF:  $sExtension = '.gif'; break;
					case IMAGETYPE_PNG:  $sExtension = '.png'; break;
					default:
						@unlink($sTempFileName);
						return false;
				}

				$sStatus = 'processing';
				$iImgWidth = (int)$aSize[0];
				$iImgHeight = (int)$aSize[1];
				$sDimension = $iImgWidth.'x'.$iImgHeight;
				$sFileSize = sprintf("%u", filesize($sTempFileName) / 1024);

				if ($iChangingPhotoID==0) {
	                if (is_array($aFileInfo) && count($aFileInfo)>0) {
						$aFileInfo['dimension'] = $sDimension;
	                    $iLastID = $this->insertSharedMediaToDb($sExtension, $aFileInfo, $iAuthorId);
					} else {
						$sExtDb = trim($sExtension, '.');
						$sMedUri = $sCurTime = time();

						$sTitleDescTemp = ($this->sTempFilename != '') ? $this->sTempFilename : $iAuthorId . '_temp';
                        if (getParam('bx_photos_activation') == 'on') {
				        	$bAutoActivate = true;
				        	$sStatus = 'approved';
				        }
				        else {
				        	$bAutoActivate = false;
				        	$sStatus = 'pending';
				        }
                        $sAlbum = $_POST['extra_param_album'];

						$iLastID = $this->oModule->_oDb->insertData(array('medProfId'=>$iAuthorId, 'medExt'=>$sExtDb, 'medTitle'=>$sTitleDescTemp, 'medUri'=>$sMedUri, 'medDesc'=>$sTitleDescTemp, 'medTags'=>'', 'Categories'=>PROFILE_PHOTO_CATEGORY, 'medSize'=>$sDimension, 'Approved'=>$sStatus, 'medDate'=>$sCurTime));
                        $this->addObjectToAlbum($this->oModule->oAlbums, $sAlbum, $iLastID, $bAutoActivate, $iAuthorId);
    	                $this->oModule->isAllowedAdd(true, true);
					}
				} else {
					$iLastID = $iChangingPhotoID;
					$this->updateMediaShared($iLastID, $aFileInfo);
				}

                $sFunc = ($isMoveUploadedFile) ? 'rename' : 'copy';
				if (! $sFunc($sTempFileName, $sMediaDir . $iLastID . $sExtension)) {
					@unlink($sTempFileName);
					return false;
				}

				$this->sSendFileInfoFormCaption = $iLastID . $sExtension . " ({$sDimension}) ({$sFileSize}kb)";

				$sFile = $sMediaDir . $iLastID . $sExtension;

				// watermark postprocessing
				if (getParam('enable_watermark') == 'on') {
					$iTransparent = getParam('transparent1');
					$sWaterMark = $dir['profileImage'] . getParam('Water_Mark');

					if (strlen(getParam('Water_Mark')) && file_exists($sWaterMark))
						applyWatermark($sFile, $sFile, $sWaterMark, $iTransparent);
				}
				
				$aFileTypes = array(
					'icon'  => array('postfix' => 'ri', 'size_def' => 32),
					'thumb' => array('postfix' => 'rt', 'size_def' => 64),
					'browse'=> array('postfix' => 't', 'size_def' => 140),
					'file'  => array('postfix' => 'm', 'size_def' => 600)
				);
				
				// force into JPG
				$sExtension = '.jpg';
				
				// generate present pics
				foreach ($aFileTypes as $sKey => $aValue) {
					$iWidth  = (int)$this->oModule->_oConfig->getGlParam($sKey . '_width');
					$iHeight = (int)$this->oModule->_oConfig->getGlParam($sKey . '_height');
					if ($iWidth == 0)
						$iWidth = $aValue['size_def'];
					if ($iHeight == 0)
						$iHeight = $aValue['size_def'];
					$sNewFilePath = $sMediaDir . $iLastID . '_' . $aValue['postfix'] . $sExtension;
					$iRes = imageResize($sFile, $sNewFilePath, $iWidth, $iHeight, true);
					if ($iRes != 0)
						return false; //resizing was failed					
					@chmod($sNewFilePath, 0644);
				}

				$aOwnerInfo = getProfileInfo($iAuthorId);
				$bAutoAssign2Profile = ($aOwnerInfo['Avatar']==0) ? true : $bAutoAssign2Profile;
				if ($bAutoAssign2Profile && $iLastID > 0) {
					$this->setPrimarySharedPhoto($iLastID, $iAuthorId);
					createUserDataFile($iAuthorId);
				}

				if (is_array($aFileInfo) && count($aFileInfo)>0) {
				    //--- BxPhoto -> Upload unit for Alerts Engine ---//
	                require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolAlerts.php');
	                $oZ = new BxDolAlerts('bx_photos', 'add', $iLastID, $iAuthorId, $this->_getExtraParams($_POST));
	                $oZ->alert();
	                //--- BxPhoto -> Upload unit for Alerts Engine ---//
				}
			}
		}

		return $iLastID;
	}

	function insertSharedMediaToDb($sExt, $aFileInfo, $iAuthorId = 0) {
		if (!$iAuthorId)
			$iAuthorId = $this->_iOwnerId;
		if (getParam('bx_photos_activation') == 'on') {
        	$bAutoActivate = true;
        	$sStatus = 'approved';
        }
        else {
        	$bAutoActivate = false;
        	$sStatus = 'pending';
        }
		$sFileTitle= $aFileInfo['medTitle'];
		$sFileDesc = $aFileInfo['medDesc'];
		$sFileTags = $aFileInfo['medTags'];
		$sCategory = implode(CATEGORIES_DIVIDER, $aFileInfo['Categories']);
		$sDimension = $aFileInfo['dimension'];

        $sAlbum = mb_strlen($_POST['extra_param_album']) > 0 ? $_POST['extra_param_album'] : getParam('sys_album_default_name');
		$sAlbum = isset($aFileInfo['album']) ? $aFileInfo['album'] : $sAlbum;

		$sMedUri = uriGenerate($sFileTitle, $this->oModule->_oDb->sFileTable, $this->oModule->_oDb->aFileFields['medUri']);
		$sExtDb = trim($sExt, '.');
		$sCurTime = time();

		$iInsertedID = $this->oModule->_oDb->insertData(array('medProfId'=>$iAuthorId, 'medExt'=>$sExtDb, 'medTitle'=>$sFileTitle, 'medUri'=>$sMedUri, 'medDesc'=>$sFileDesc, 'medTags'=>$sFileTags, 'Categories'=>$sCategory, 'medSize'=>$sDimension, 'Approved'=>$sStatus, 'medDate'=>$sCurTime));

		if (0 < $iInsertedID) {
		    $oTag = new BxDolTags();
            $oTag->reparseObjTags('bx_photos', $iInsertedID);
            $oCateg = new BxDolCategories();
            $oCateg->reparseObjTags('bx_photos', $iInsertedID);
            $this->addObjectToAlbum($this->oModule->oAlbums, $sAlbum, $iInsertedID, $bAutoActivate, $iAuthorId); 
            return $iInsertedID;
		} else {
			return 0;
		}
	}

	function setPrimarySharedPhoto($iPhotoID, $iAuthorId = 0) {
		//Possible TODO
		/*$sUpdateSQL = "
			UPDATE `Profiles`
			SET `PrimPhoto` = '{$iPhotoID}', `Picture` = '1'
			WHERE `ID` = '{$this->_iOwnerId}'
			LIMIT 1
		";
		db_res($sUpdateSQL);*/
	}

	function updateMediaShared($iMediaID, $aFileInfo) {
		$sFileTitle = addslashes($aFileInfo['medTitle']);
		$sFileDesc = addslashes($aFileInfo['medDesc']);
		$sMedUri = uriGenerate($sFileTitle, $this->oModule->_oDb->sFileTable, $this->oModule->_oDb->aFileFields['medUri']);
		$sCurTime = time();

		return $this->oModule->_oDb->updateData($iMediaID, array('medTitle'=>$sFileTitle, 'medUri'=>$sMedUri, 'medDesc'=>$sFileDesc, 'medDate'=>$sCurTime));
	}

	function serviceGenAddPhotoPage($aExtras = array()) {
		$sAddPhotoC = _t('_bx_photos_add');
		$sRecPhotoC = _t('_bx_photos_record');
		$sEmbPhotoC = _t('_bx_photos_embed');
		$sFlashPhotoC = _t('_adm_admtools_Flash');
		$sPhotoUploadForm = $this->GenMainAddPhotosForm($aExtras);
		$sUploadActStyle = $sRecordActStyle = $sEmbedActStyle = $sFlashActStyle = 'notActive';
		switch ($_GET['mode']) {
			case 'record':
				$sRecordActStyle = 'active';
				break;
			case 'embed':
				$sEmbedActStyle = 'active';
				break;
			case 'single':
				$sUploadActStyle = 'active';
				break;
			default:
				$sFlashActStyle = 'active';
				break;
		}

		$sActions = <<<EOF
<div class="dbTopMenu">
	<div class="{$sFlashActStyle}" id="common_edit_blog">
		<span style="vertical-align:middle;"><a href="{$this->sWorkingFile}">{$sFlashPhotoC}</a></span>
	</div>
	<div class="{$sUploadActStyle}" id="common_edit_blog">
		<span style="vertical-align:middle;"><a href="{$this->sWorkingFile}&mode=single">{$sAddPhotoC}</a></span>
	</div>
	<div class="{$sRecordActStyle}" id="common_edit_blog">
		<span style="vertical-align:middle;"><a href="{$this->sWorkingFile}&mode=record">{$sRecPhotoC}</a></span>
	</div>
	<div class="{$sEmbedActStyle}" id="common_edit_blog">
		<span style="vertical-align:middle;"><a href="{$this->sWorkingFile}&mode=embed">{$sEmbPhotoC}</a></span>
	</div>
</div>
EOF;

		return  DesignBoxContent(_t('_bx_photos_my'), '<div class="dbContentHtml">'.$sPhotoUploadForm.'</div>', 1, $sActions);
	}
	
    function serviceGetUploaderForm($aExtras) {
        return $this->GenMainAddPhotosForm($aExtras);
    }
}
?>
