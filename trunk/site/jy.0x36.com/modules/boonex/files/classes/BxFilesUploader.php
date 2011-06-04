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

bx_import('BxDolFilesUploader');
bx_import('BxDolCategories');
bx_import('BxDolAlbums');
bx_import('BxDolModule');

class BxFilesUploader extends BxDolFilesUploader {
	// constructor
	function BxFilesUploader() {
		parent::BxDolFilesUploader('File');

		$this->oModule = BxDolModule::getInstance('BxFilesModule');
		$this->sWorkingFile = $this->oModule->_oConfig->getBaseUri() . 'albums/my/add_objects';
		$this->sMultiUploaderParams['accept_file'] = $this->sWorkingFile;
		$this->sMultiUploaderParams['form_caption'] = _t('_bx_files_add_objects');
		$this->sMultiUploaderParams['accept_format'] = $this->oModule->_oConfig->getAvailableFlashExts();
		$this->sMultiUploaderParams['accept_format_desc'] = 'Any Files';
	}

	/*
	* Service - generate files upload main form
	*
	* params
	* $sPredefCategory - TODO remove
	* $aExtras - TODO predefined album and category should appear here with names: predef_album and predef_category
	*/
	function serviceGenFileUploadForm($aExtras = array()) {
		return $this->GenMainAddFilesForm($aExtras);
	}

	function GenMainAddFilesForm($aExtras = array()) {
    	$aUploaders = array_keys($this->oModule->_oConfig->getUploaderList());
		return $this->_GenMainAddCommonForm($aExtras, $aUploaders);
	}
	
    function getUploadFormFile() {
		return $this->_getUploadFormFile();
    }

	function GenSendFileInfoForm($iFileID, $aDefaultValues = array()) {

		$sFileUrl = "";
		if(isset($aDefaultValues['image']))
			$sFileUrl = $aDefaultValues['image'];
		else if(!empty($iFileID)) {
			$aFileInfo = BxDolService::call('files', 'get_file_array', array($iFileID), 'Search');
			$sFileUrl = $aFileInfo['file'];
		}
		$sProtoEl = '<img src="' . $sFileUrl . '" />';

		$aPossibleImage = array();
		$aPossibleImage['preview_image'] = array(
			'type' => 'custom',
			'content' => $sProtoEl,
			'caption' => _t('_bx_files_preview'),
		);

		return $this->_GenSendFileInfoForm($iFileID, $aDefaultValues, $aPossibleImage, array());
	}

	function serviceCancelFileInfo() {
		$iFileID = (int)$_GET['file_id'];
		if ($iFileID) {
			
			if ($this->oModule->serviceRemoveObject($iFileID)) {
				//deleteFile($iFileID);
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
				$sResult .= $this->_shareFile($_FILES['file']['tmp_name'][$i], true, $_FILES['file']['name'][$i], $_FILES['file']['type'][$i]);
			}
		}
		return ($sResult!='') ? $this->GenJquieryInjection() . $sResult : '';
	}

	function serviceAcceptFileInfo() {
        $iAuthorId = $this->_getAuthorId();
		$sJSFileId = (int)$_POST['file_id'];
		switch($_POST['type']) {
			case 'upload':
			default:
				$iFileID = $sJSFileId;
				break;
		}

		if ($iFileID && $iAuthorId) {
			$sTitle = process_db_input($_POST['title'], BX_TAGS_STRIP);
			$sTags = process_db_input($_POST['tags'], BX_TAGS_STRIP);
			$sDescription = process_db_input($_POST['description'], BX_TAGS_VALIDATE);
			$iAllowDownload = (int)$_POST['AllowDownload'];

			$aCategories = array();
			foreach ($_POST['Categories'] as $sVal) {
				if ($sVal != '') {
					$aCategories[] = process_db_input($sVal, BX_TAGS_STRIP);
				}
			}
			$sCategories = implode(CATEGORIES_DIVIDER, $aCategories);

            if ($this->initFileFile($iFileID, $sTitle, $sCategories, $sTags, $sDescription, $iAllowDownload)) {
                //--- BxFile -> Upload unit for Alerts Engine ---//

                require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolAlerts.php');
                $oZ = new BxDolAlerts('bx_files', 'add', $iFileID, $iAuthorId, $this->_getExtraParams($_POST));
                $oZ->alert();
				
                return '<script type="text/javascript">parent.' . $this->_sJsPostObject . '.onSuccessSendingFileInfo("' . $sJSFileId . '");</script>';
			}
		}
		return '<script type="text/javascript">parent.' . $this->_sJsPostObject . '.showErrorMsg("file_failed_message");</script>';
	}

	function servicePerformMultiFileUpload () {
		$this->_iOwnerId = (int)$_POST['oid'];

		if ($_FILES) {
			if (!$_FILES['Filedata']['error'] && $_FILES['Filedata']['size'] <= $this->iMaxFilesize) {
	            $sResult .= $this->_shareFile($_FILES['Filedata']['tmp_name'], true, $_FILES['Filedata']['name'], $_FILES['Filedata']['type']);
	            return ($sResult!='') ? $this->GenJquieryInjection() . $sResult : '';
			}
		}
	}

	function servicePerformFileUpload ($sTmpFilename, $aFileInfo, $isUpdateThumb = '') {
	    return $this->performFileUpload($sTmpFilename, $aFileInfo, false, $sTmpFilename);
	}

	function _shareFile($sFilePath, $isMoveUploadedFile = true, $sRealFilename = '', $sFileType = '') {
		$sAuthorCheck = $this->checkAuthorBeforeAdd();
		if(empty($sAuthorCheck)) {
			//$this->sTempFilename = substr($sRealFilename, 0, strrpos($sRealFilename, '.'));

		    $iPointPos = strrpos($sRealFilename, '.');
            $sExt = substr($sRealFilename, $iPointPos + 1);
            if (!$this->oModule->_oConfig->checkAllowedExts($sExt))
                return $this->getFileAddError();
			$this->sTempFilename = substr($sRealFilename, 0, $iPointPos);

			$iMID = $this->performFileUpload($sFilePath, array(), $isMoveUploadedFile, $sRealFilename, $sFileType);
			if ($iMID>0) { //upload success
				$aDefault = array('title' => $this->sTempFilename, 'description' => $this->sTempFilename);
				return $this->GenSendFileInfoForm($iMID, $aDefault);
			} else
				return $this->getFileAddError();
		} else
			return $sAuthorCheck;
	}
	
	function getFileAddError() {
		return '<script type="text/javascript">alert("' . htmlspecialchars(addslashes(_t('_bx_files_upl_file_err'))) . '");</script>';
	}

	function initFileFile($iFileID, $sTitle, $sCategories, $sTags, $sDesc, $iAllowDownload) {
		$sMedUri = uriGenerate($sTitle, $this->oModule->_oDb->sFileTable, $this->oModule->_oDb->aFileFields['medUri']);
		$sStatus = getParam('bx_files_activation') == 'on' ? 'approved' : 'pending';

		$bRes = $this->oModule->_oDb->updateData($iFileID, array('Categories'=>$sCategories, 'medTitle'=>$sTitle, 'medTags'=>$sTags, 'medDesc'=>$sDesc, 'Approved'=>$sStatus, 'medUri'=>$sMedUri, 'AllowDownload'=>$iAllowDownload));

		if ($bRes) {
            $oTag = new BxDolTags();
            $oTag->reparseObjTags('bx_files', $iFileID);
            $oCateg = new BxDolCategories();
            $oCateg->reparseObjTags('bx_files', $iFileID);
		}
		return $bRes;
	}

	// simple upload
	function performFileUpload($sTmpFile, $aFileInfo, $isMoveUploadedFile = true, $sOriginalFilename = '', $sFileType = '') {
	    $iLastID = -1;

		// checker for flash uploader
		if (!$this->oModule->_iProfileId)
			$this->oModule->_iProfileId = $this->_iOwnerId;
	    if (! $this->_iOwnerId || file_exists($sTmpFile) == false || !$this->oModule->isAllowedAdd())
			return false;

		$sMediaDir = $this->oModule->_oConfig->getFilesPath();

		if (! $sMediaDir) {
			@unlink($sTmpFile);
			return false;
		}

		$sTempFileName = $sMediaDir . $this->_iOwnerId . '_temp';
        @unlink($sTempFileName);        

        if (($isMoveUploadedFile && is_uploaded_file($sTmpFile)) || !$isMoveUploadedFile) {

            if ($isMoveUploadedFile) {
                move_uploaded_file($sTmpFile, $sTempFileName);
                @unlink($sTmpFile);
            } else {
                $sTempFileName = $sTmpFile;
            }

			@chmod($sTempFileName, 0666);
			if (file_exists($sTempFileName) && filesize($sTempFileName) > 0) {
				$sOriginalFilenameSafe = process_db_input($sOriginalFilename, BX_TAGS_STRIP);
				$sExtension = strrchr($sOriginalFilename, '.');
				$iFileSize = filesize($sTempFileName);
				$sFileSize = sprintf("%u", $iFileSize / 1024);
				$sCurTime = time();
				
				if (is_array($aFileInfo) && count($aFileInfo) > 0) {
				    $aFileInfo['medSize'] = $iFileSize;
					$iLastID = $this->insertSharedMediaToDb($sExtension, $aFileInfo);
				}
				else {
					$aPassArray = array(
						'medProfId' => $this->_iOwnerId,
						'medTitle' => $sOriginalFilenameSafe,
						'medDesc' => $sOriginalFilenameSafe, 
						'medExt' => trim($sExtension, '.'),
						'medDate' => $sCurTime,
						'medUri' => $sCurTime,
						'medSize' => $iFileSize
					);

                    if (getParam('bx_files_activation') == 'on') {
                    	$bAutoActivate = true;
                    	$aPassArray['Approved'] = 'approved';
                    }
                    else {
                    	$bAutoActivate = false;
                    	$aPassArray['Approved'] = 'pending';
                    }
                    
                    if (mb_strlen($sFileType) > 0)
    					$aPassArray['Type'] = process_db_input($sFileType, BX_TAGS_STRIP);
					$iLastID = $this->oModule->_oDb->insertData($aPassArray);
                    $this->addObjectToAlbum($this->oModule->oAlbums, $_POST['extra_param_album'], $iLastID, $bAutoActivate);
                    $this->oModule->isAllowedAdd(true, true);
				}

                $sFunc = ($isMoveUploadedFile) ? 'rename' : 'copy';
				$sFilePostfix = '_' . sha1($sCurTime);
				if (! $sFunc($sTempFileName, $sMediaDir . $iLastID . $sFilePostfix)) {
					@unlink($sTempFileName);
					return false;
				}

				$this->sSendFileInfoFormCaption = $iLastID . $sExtension . " ({$sFileSize}kb)";

				$sFile = $sMediaDir . $iLastID . $sExtension;
			}
		}

		return $iLastID;
	}

	function insertSharedMediaToDb($sExt, $aFileInfo) {
		if (getParam('bx_files_activation') == 'on') {
        	$bAutoActivate = true;
        	$sStatus = 'approved';
        }
        else {
        	$bAutoActivate = false;
        	$sStatus = 'pending';
        }
		$sFileTitle = addslashes($aFileInfo['medTitle']);
		$sFileDesc = addslashes($aFileInfo['medDesc']);
		$sFileTags = addslashes($aFileInfo['medTags']);
		$iAllowDownload = (int)$aFileInfo['AllowDownload'];
		$sCategory = implode(CATEGORIES_DIVIDER, $aFileInfo['Categories']);
		$sAlbum = isset($aFileInfo['album']) ? $aFileInfo['album'] : getParam('sys_album_default_name');
		$sMedUri = uriGenerate($sFileTitle, $this->oModule->_oDb->sFileTable, $this->oModule->_oDb->aFileFields['medUri']);
		$sDimension = (int)$aFileInfo['medSize'];
		$sExtDb = trim($sExt, '.');
		$sCurTime = time();

		$iInsertedID = $this->oModule->_oDb->insertData(array('medProfId'=>$this->_iOwnerId, 'medExt'=>$sExtDb, 'medTitle'=>$sFileTitle, 'medUri'=>$sMedUri, 'medDesc'=>$sFileDesc, 'medTags'=>$sFileTags, 'Categories'=>$sCategory, 'medSize'=>$sDimension, 'Approved'=>$sStatus, 'medDate'=>$sCurTime, 'AllowDownload'=>$iAllowDownload));

		if (0 < $iInsertedID) {
            $oTag = new BxDolTags();
            $oTag->reparseObjTags('bx_files', $iInsertedID);
            $oCateg = new BxDolCategories();
            $oCateg->reparseObjTags('bx_files', $iInsertedID);
		    $this->addObjectToAlbum($this->oModule->oAlbums, $sAlbum, $iInsertedID, $bAutoActivate);
            return $iInsertedID;
		} else {
			return 0;
		}
	}

	function updateMediaShared($iMediaID, $aFileInfo) {
		$sFileTitle = addslashes($aFileInfo['medTitle']);
		$sFileDesc = addslashes($aFileInfo['medDesc']);
		$sMedUri = uriGenerate($sFileTitle, $this->oModule->_oDb->sFileTable, $this->oModule->_oDb->aFileFields['medUri']);
		$sCurTime = time();

		return $this->oModule->_oDb->updateData($iMediaID, array('medTitle'=>$sFileTitle, 'medUri'=>$sMedUri, 'medDesc'=>$sFileDesc, 'medDate'=>$sCurTime));
	}

	function serviceGenAddFilePage($aExtras = array()) {
		$sAddFileC = _t('_bx_files_add');
		$sFlashFileC = _t('_adm_admtools_Flash');
		$sFileUploadForm = $this->GenMainAddFilesForm($aExtras);
		$sUploadActStyle = $sFlashActStyle = 'notActive';
		switch ($_GET['mode']) {
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
		<span style="vertical-align:middle;"><a href="{$this->sWorkingFile}">{$sFlashFileC}</a></span>
	</div>
	<div class="{$sUploadActStyle}" id="common_edit_blog">
		<span style="vertical-align:middle;"><a href="{$this->sWorkingFile}&mode=single">{$sAddFileC}</a></span>
	</div>
</div>
EOF;

		return  DesignBoxContent(_t('_bx_files_my'), '<div class="dbContentHtml">'.$sFileUploadForm.'</div>', 1, $sActions);
	}
	
    function serviceGetUploaderForm($aExtras) {
        return $this->GenMainAddFilesForm($aExtras);
    }
    
	function getUploadFormArray (&$aForm, $aAddObjects = array()) {
		$aForm = parent::getUploadFormArray($aForm, $aAddObjects);		
		$aForm['AllowView'] = $this->oModule->oPrivacy->getGroupChooser($this->_iOwnerId, $this->oModule->_oConfig->getUri(), 'download');
		return $aForm;
	}
}
?>