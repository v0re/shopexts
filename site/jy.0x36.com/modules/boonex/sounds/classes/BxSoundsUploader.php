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

//require_once( BX_DIRECTORY_PATH_MODULES . 'boonex/sounds/classes/BxShMusicMain.php' );
bx_import('BxDolFilesUploader');
bx_import('BxDolCategories');
bx_import('BxDolModule');
bx_import('BxDolAlbums');

global $sIncPath;
global $sModulesPath;
global $sModule;
global $sFilesPath;
global $oDb;
require_once($sIncPath . 'db.inc.php');

$sModule = "mp3";
$sModulePath = $sModulesPath . $sModule . '/inc/';

global $sFilesPathMp3;

global $sModulesPath;
$sModule = "mp3";
require_once($sModulesPath . $sModule . '/inc/header.inc.php');
require_once($sModulesPath . $sModule . '/inc/constants.inc.php');
require_once($sModulesPath . $sModule . '/inc/functions.inc.php');
require_once($sModulesPath . $sModule . '/inc/customFunctions.inc.php');

class BxSoundsUploader extends BxDolFilesUploader {

	// constructor
	function BxSoundsUploader() {
		parent::BxDolFilesUploader('Sound');

		$this->oModule = BxDolModule::getInstance('BxSoundsModule');
		$this->sWorkingFile = $this->oModule->_oConfig->getBaseUri() . 'albums/my/add_objects';
		$this->sMultiUploaderParams['accept_file'] = $this->sWorkingFile;
		$this->sMultiUploaderParams['form_caption'] = _t('_bx_sounds_add_objects');
		$this->sMultiUploaderParams['accept_format'] = $this->oModule->_oConfig->getAvailableFlashExts();
		$this->sMultiUploaderParams['accept_format_desc'] = 'Music Files';
	}

	/*
	* Service - generate sound upload main form
	*
	* params
	* $sPredefCategory - TODO remove
	* $aExtras - TODO predefined album and category should appear here with names: predef_album and predef_category
	*/
	function serviceGenMusicUploadForm($aExtras = array()) {
		return $this->GenMainAddMusicForm($aExtras);
	}

	function GenMainAddMusicForm($aExtras = array()) {
	    $aUploaders = array_keys($this->oModule->_oConfig->getUploaderList());
		return $this->_GenMainAddCommonForm($aExtras, $aUploaders);
	}

	function getEmbedFormFile() {
	    return $this->_getEmbedFormFile();
    }

    function getRecordFormFile() {
	    $sCustomRecorderObject = getApplicationContent('mp3', 'recorder', array('user' => $this->_getAuthorId(), 'password' => $this->_getAuthorPassword(), 'extra' => ''), true);
	    return $this->_getRecordFormFile($sCustomRecorderObject);
    }

    function getUploadFormFile() {
		return $this->_getUploadFormFile();
    }

	function GenSendFileInfoForm($iFileID, $aDefaultValues = array()) {

		$aPossibleDuration = array();
		$aPossibleDuration['duration'] = array(
			'type' => 'hidden',
			'name' => 'duration',
			'value' => isset($aDefaultValues['duration']) ? $aDefaultValues['duration'] : "0"
		);

		return $this->_GenSendFileInfoForm($iFileID, $aDefaultValues, array(), $aPossibleDuration);
	}

	function serviceCancelFileInfo() {
		$iFileID = (int)$_GET['file_id'];
		if ($iFileID) {
			if ($this->oModule->serviceRemoveObject($iFileID)) {
				//deleteMusic($iFileID);
				return 1;
			}
		}
		return 0;
	}

	function servicePerformMultiMusicUpload () {
		$this->_iOwnerId = (int)$_POST['oid'];

		if ($_FILES) {
            if ($_FILES['Filedata']['error'] || $_FILES['Filedata']['size'] > $this->iMaxFilesize)
                return;
            $sResult .= $this->_shareMusic($_FILES['Filedata']['tmp_name'], true, $_FILES['Filedata']['name']);
            return ($sResult!='') ? $this->GenJquieryInjection() . $sResult : '';
		}
	}

    function servicePerformMusicUpload($sFilePath, $aInfo, $isMoveUploadedFile = false) {
		global $sModule;
		$sModule = "mp3";
		global $sFilesPathMp3;

		if (!$this->oModule->_iProfileId)
			$this->oModule->_iProfileId = $this->_iOwnerId;
	    if (!$this->_iOwnerId || !$this->oModule->isAllowedAdd())
			return false;
		$sFilePath = process_db_input($sFilePath, BX_TAGS_STRIP, BX_SLASHES_NO_ACTION);
		$iOwnerID = $this->_getAuthorId();
		$iOwnerID = ($this->_iOwnerId > 0) ? $this->_iOwnerId : $iOwnerID;		
		$iPointPos = strrpos($sFilePath, '.');
        $sExt = substr($sFilePath, $iPointPos + 1);
        if (!$this->oModule->_oConfig->checkAllowedExts(strtolower($sExt)))
            return false;

        if (!($iId = uploadMusic($sFilePath, $iOwnerID, $sFilePath, $isMoveUploadedFile))) {
            return false;
        }

        if ($aInfo) {
            foreach (array('title', 'categories', 'tags', 'desc') as $sKey)
        		$aInfo[$sKey] = isset($aInfo[$sKey]) ? $aInfo[$sKey] : '';
        	$this->initMusicFile($iId, $aInfo['title'], $aInfo['categories'], $aInfo['tags'], $aInfo['desc']);

            $sAlbum = mb_strlen($_POST['extra_param_album']) > 0 ? $_POST['extra_param_album'] : getParam('sys_album_default_name');
    		$sAlbum = isset($aInfo['album']) ? $aInfo['album'] : $sAlbum;
    		$sAutoActive = false;
    		if (strtolower($sExt) == 'mp3' && getSettingValue($sModule, "autoApprove") == true)
    			$sAutoActive = true;
            $this->addObjectToAlbum($this->oModule->oAlbums, $sAlbum, $iId, $sAutoActive);
            $this->oModule->isAllowedAdd(true, true);
        }
        return $iId;
    }

	function serviceAcceptFile() {
		$sResult = '';
		if ($_FILES) {
			for ($i=0; $i<count($_FILES['file']['tmp_name']); $i++) {
				if ($_FILES['file']['error'][$i] || $_FILES['file']['size'][$i] > $this->iMaxFilesize)
					continue;
				$sResult .= $this->_shareMusic($_FILES['file']['tmp_name'][$i], true, $_FILES['file']['name'][$i]);
			}
		}
		return ($sResult!='') ? $this->GenJquieryInjection() . $sResult : '';
	}

	function serviceAcceptRecordFile() {
		$sResult = $this->_recordMusic();
		return ($sResult!='') ? $this->GenJquieryInjection() . $sResult : '';
	}

	function serviceAcceptFileInfo() {
		$iAuthorId = $this->_getAuthorId();
		$sJSMusicId = (int)$_POST['file_id'];
		switch($_POST['type'])
		{
			case 'record':
				global $sFilesPathMp3;
				$sFileName = $iAuthorId . TEMP_FILE_NAME . MP3_EXTENSION;
				$iMusicID = uploadMusic($sFilesPathMp3 . $sFileName, $iAuthorId, $sFileName, false);
                $this->addObjectToAlbum($this->oModule->oAlbums, $_POST['extra_param_album'], $iMusicID, false);
				break;
			case 'upload':
			default:
				$iMusicID = $sJSMusicId;
				break;
		}

		if ($iMusicID && $iAuthorId) {
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

			if ($this->initMusicFile($iMusicID, $sTitle, $sCategories, $sTags, $sDescription)) {
			    //--- BxSounds -> Upload unit for Alerts Engine ---//
                
                require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolAlerts.php');
                $oZ = new BxDolAlerts('bx_sounds', 'add', $iMusicID, $iAuthorId, $this->_getExtraParams($_POST));
                $oZ->alert();
                //--- BxSounds -> Upload unit for Alerts Engine ---//

				return '<script type="text/javascript">parent.' . $this->_sJsPostObject . '.onSuccessSendingFileInfo("' . $sJSMusicId . '");</script>';
			}
		}
		return '<script type="text/javascript">parent.' . $this->_sJsPostObject . '.showErrorMsg("sound_failed_message");</script>';
	}

	function _embedMusic($sMusicId, $sTitle, $sDesc, $sTags, $sImage, $iDuration) {
		$sAuthorCheck = $this->checkAuthorBeforeAdd();
		if(empty($sAuthorCheck)) {
			$sEmbedThumbUrl = getEmbedThumbnail($this->_getAuthorId(), $sImage);
			if($sEmbedThumbUrl)
			{
				$aDefault = array('music' => $sMusicId, 'title' => $sTitle, 'description' => $sDesc, 'tags' => $sTags, 'duration' => $iDuration, 'image' => $sEmbedThumbUrl, 'type' => "embed");
				return $this->GenSendFileInfoForm(1, $aDefault);
			}
			else 
				return $this->getMusicAddError();
		}
		else
			return $sAuthorCheck;
	}

	function OnSuccessMusicUpload() {
	}

	function _recordMusic() {
		$sAuthorCheck = $this->checkAuthorBeforeAdd();
		if(empty($sAuthorCheck)) {
			if(checkRecord($this->_getAuthorId()))
				return $this->GenSendFileInfoForm(1, array('type' => "record"));
			else 
				return $this->getMusicAddError();
		}
		else
			return $sAuthorCheck;
	}

	function _shareMusic($sFilePath, $isMoveUploadedFile = true , $sRealFilename = '') {
		global $sModule;
		$sAuthorCheck = $this->checkAuthorBeforeAdd();
		if (!$this->oModule->_iProfileId)
			$this->oModule->_iProfileId = $this->_iOwnerId;
		if (empty($sAuthorCheck) && $this->oModule->isAllowedAdd()) {
    		$bAutoActive = false;
			$sFilePath = process_db_input($sFilePath, BX_TAGS_STRIP, BX_SLASHES_NO_ACTION);
			if (mb_strlen($sRealFilename) > 0) {
				$sRealFilename = process_db_input($sRealFilename, BX_TAGS_STRIP);
			    $iPointPos = strrpos($sRealFilename, '.');
			    $sExt = substr($sRealFilename, $iPointPos + 1);
			    if (!$this->oModule->_oConfig->checkAllowedExts(strtolower($sExt)))
			        return $this->getMusicAddError();
		        $this->sTempFilename = substr($sRealFilename, 0, $iPointPos);
        		if (strtolower($sExt) == 'mp3' && getSettingValue($sModule, "autoApprove") == true)
					$bAutoActive = true;
			}
            $sROwnerID = ($this->_iOwnerId) ? $this->_iOwnerId : $this->_getAuthorId();
			$iMID = uploadMusic($sFilePath, $sROwnerID, $sRealFilename, $isMoveUploadedFile);
			if ($iMID > 0) {
				$this->addObjectToAlbum($this->oModule->oAlbums, $_POST['extra_param_album'], $iMID, $bAutoActive);
				$this->oModule->isAllowedAdd(true, true);
				$aDefault = array('title' => $this->sTempFilename, 'description' => $this->sTempFilename);
				return $this->GenSendFileInfoForm($iMID, $aDefault);
			} else
				return $this->getMusicAddError();
		}
		else 
			return $sAuthorCheck;
	}

	function getMusicAddError() {
		return '<script type="text/javascript">parent.' . $this->_sJsPostObject . '.showErrorMsg("sound_failed_file_message");</script>';
	}

	function initMusicFile($iMusicID, $sTitle, $sCategories, $sTags, $sDesc) {
		$sMedUri = uriGenerate($sTitle, $this->oModule->_oDb->sFileTable, $this->oModule->_oDb->aFileFields['medUri']);
		$bRes = $this->oModule->_oDb->updateData($iMusicID, array('Categories'=>$sCategories, 'medTitle'=>$sTitle, 'medTags'=>$sTags, 'medDesc'=>$sDesc, 'medUri'=>$sMedUri));

		$oTag = new BxDolTags();
        $oTag->reparseObjTags('bx_sounds', $iMusicID);
        $oCateg = new BxDolCategories();
        $oCateg->reparseObjTags('bx_sounds', $iMusicID);
		
		$bRes = true; //TODO chech why if false
		return $bRes;
	}

	function serviceGenAddMusicPage($aExtras = array()) {
		$sAddMusicC = _t('_bx_sounds_add');
		$sRecMusicC = _t('_bx_sounds_record');
		$sEmbMusicC = _t('_bx_sounds_embed');
		$sFlashMusicC = _t('_adm_admtools_Flash');

		$sMusicUploadForm = $this->GenMainAddMusicForm($aExtras);

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
		<span style="vertical-align:middle;"><a href="{$this->sWorkingFile}">{$sFlashMusicC}</a></span>
	</div>
	<div class="{$sUploadActStyle}" id="common_edit_blog">
		<span style="vertical-align:middle;"><a href="{$this->sWorkingFile}&mode=single">{$sAddMusicC}</a></span>
	</div>
	<div class="{$sRecordActStyle}" id="common_edit_blog">
		<span style="vertical-align:middle;"><a href="{$this->sWorkingFile}&mode=record">{$sRecMusicC}</a></span>
	</div>
</div>
EOF;

		return DesignBoxContent(_t('_bx_sounds_my'), '<div class="dbContentHtml">'.$sMusicUploadForm.'</div>', 1, $sActions);
	}

    function serviceGetUploaderForm($aExtras) {
        return $this->GenMainAddMusicForm($aExtras);
    }
}

?>