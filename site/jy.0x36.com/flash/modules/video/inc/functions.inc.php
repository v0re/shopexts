<?
/***************************************************************************
*
* IMPORTANT: This is a commercial product made by BoonEx Ltd. and cannot be modified for other than personal usage.
* This product cannot be redistributed for free or a fee without written permission from BoonEx Ltd.
* This notice may not be removed from the source code.
*
***************************************************************************/

function prepareCommand($sTemplate, $aOptions)
{
	foreach($aOptions as $sKey => $sValue)
		$sTemplate = str_replace("#" . $sKey . "#", $sValue, $sTemplate);
	return $sTemplate;
}

function usex264()
{
	global $sModule;
	return getSettingValue($sModule, "usex264") == TRUE_VAL;
}

function getEmbedThumbnail($sUserId, $sImageUrl)
{
	global $sFilesPath;
	global $sFilesUrl;
	
	$sFileName = $sUserId . TEMP_FILE_NAME . THUMB_FILE_NAME . IMAGE_EXTENSION;
	$sFilePath = $sFilesPath . $sFileName;
	copy($sImageUrl, $sFilePath);
	@chmod($sFilePath, 0666);
	if(convertVideoFile($sFilePath, getGrabImageTmpl($sFilePath, $sFilePath, "-s " . THUMB_SIZE)))
		return $sFilesUrl . $sFileName;
	else
		return false;
}

function getRecordThumbnail($sUserId)
{
	global $sFilesPath;
	global $sFilesUrl;
	
	$sFileName = $sUserId . TEMP_FILE_NAME . THUMB_FILE_NAME . IMAGE_EXTENSION;
	if(file_exists($sFilesPath . $sFileName))
		return $sFilesUrl . $sFileName;
	else
		return false;
}

function embedVideo($sUserId, $sVideoId, $iDuration)
{
	global $sFilesPath;
	global $sModule;
	$sDBModule = DB_PREFIX . ucfirst($sModule);
	$sStatus = getSettingValue($sModule, "autoApprove") == TRUE_VAL ? STATUS_APPROVED : STATUS_DISAPPROVED;
	getResult("INSERT INTO `" . $sDBModule . "Files` SET `Date`='" . time() . "', `Owner`='" . $sUserId . "', `Status`='" . $sStatus . "', `Source`='youtube', `Video`='" . $sVideoId . "', `Time`='" . ($iDuration * 1000) . "'");
	
	$sFileId = getLastInsertId();
	@rename($sFilesPath . $sUserId . TEMP_FILE_NAME . THUMB_FILE_NAME . IMAGE_EXTENSION, $sFilesPath . $sFileId . THUMB_FILE_NAME . IMAGE_EXTENSION);
	return $sFileId;
}

function recordVideo($sUserId)
{
	global $sFilesPath;
	global $sModule;
	$sDBModule = DB_PREFIX . ucfirst($sModule);
	getResult("INSERT INTO `" . $sDBModule . "Files` SET `Date`='" . time() . "', `Owner`='" . $sUserId . "', `Status`='" . STATUS_PENDING . "'");
	
	$sFileId = getLastInsertId();
	@rename($sFilesPath . $sUserId . TEMP_FILE_NAME . FLV_EXTENSION, $sFilesPath . $sFileId . FLV_EXTENSION);
	@rename($sFilesPath . $sUserId . TEMP_FILE_NAME . IMAGE_EXTENSION, $sFilesPath . $sFileId . IMAGE_EXTENSION);
	@rename($sFilesPath . $sUserId . TEMP_FILE_NAME . THUMB_FILE_NAME . IMAGE_EXTENSION, $sFilesPath . $sFileId . THUMB_FILE_NAME . IMAGE_EXTENSION);
	return $sFileId;
}

function uploadVideo($sFilePath, $sUserId, $isMoveUploadedFile = false, $sImageFilePath = '', $sFileName = '')
{
	global $sModule;
	global $sFilesPath;
	
	$sTempFileName = $sFilesPath . $sUserId . TEMP_FILE_NAME;
	@unlink($sTempFileName);
	if(file_exists($sFilePath) && filesize($sFilePath) > 0)
	{
		if(is_uploaded_file($sFilePath)) move_uploaded_file($sFilePath, $sTempFileName);
		else {
			@rename($sFilePath, $sTempFileName);
		}
		@chmod($sTempFileName, 0666);
		if(file_exists($sTempFileName) && filesize($sTempFileName)>0)
		{
			if(!grabImages($sTempFileName, $sTempFileName))
				return false;

			$sImageFilePath = "1";
			$sTempImgFileName = $sFilesPath . $sUserId . TEMP_FILE_NAME . IMAGE_EXTENSION;
			$sTempThumbFileName = $sFilesPath . $sUserId . TEMP_FILE_NAME . THUMB_FILE_NAME . IMAGE_EXTENSION;
		
			$sDBModule = DB_PREFIX . ucfirst($sModule);
            $sUri = video_genUri($sFileName);
            $sUriPart = empty($sUri) ? "" : "`Uri`='" . $sUri . "', ";

			getResult("INSERT INTO `" . $sDBModule . "Files` SET `Title`='" . $sFileName . "', " . $sUriPart .  "`Description`='" . $sFileName . "', `Date`='" . time() . "', `Owner`='" . $sUserId . "', `Status`='" . STATUS_PENDING . "'");
			$sFileId = getLastInsertId();
			rename($sTempFileName, $sFilesPath . $sFileId);
			if($sImageFilePath != '') {
				@rename($sTempImgFileName, $sFilesPath . $sFileId . IMAGE_EXTENSION);
				@rename($sTempThumbFileName, $sFilesPath . $sFileId . THUMB_FILE_NAME . IMAGE_EXTENSION);
			}
			return $sFileId;
		}
	}
	return false;
}

function publishRecordedVideo($sUserId, $sTitle, $sCategory, $sTags, $sDesc)
{
	global $sModule;
	global $sFilesPath;
	
	$sPlayFile = $sFilesPath . $sUserId . TEMP_FILE_NAME . FLV_EXTENSION;
	if(file_exists($sPlayFile) && filesize($sPlayFile)>0)
	{
		$sDBModule = DB_PREFIX . ucfirst($sModule);
		$sUri = video_genUri($sTitle);
		$sUriPart = empty($sUri) ? "" : "`Uri`='" . $sUri . "', ";
		getResult("INSERT INTO `" . $sDBModule . "Files` SET `Categories`='" . $sCategory . "', `Title`='" . $sTitle . "', " . $sUriPart . "`Tags`='" . $sTags . "', `Description`='" . $sDesc . "', `Date`='" . time() . "', `Owner`='" . $sUserId . "', `Status`='" . STATUS_PENDING . "'");
		$sFileId = getLastInsertId();
		rename($sPlayFile, $sFilesPath . $sFileId);
		@rename($sFilesPath . $sUserId . TEMP_FILE_NAME . IMAGE_EXTENSION, $sFilesPath . $sFileId . IMAGE_EXTENSION);
		@rename($sFilesPath . $sUserId . TEMP_FILE_NAME . THUMB_FILE_NAME . IMAGE_EXTENSION, $sFilesPath . $sFileId . THUMB_FILE_NAME . IMAGE_EXTENSION);
		return $sFileId;
	}
	else return false;
}

function initVideo($sId, $sTitle, $sCategory, $sTags, $sDesc)
{
    global $oDb;
	global $sModule;
	global $oDb;

	$sUri = video_genUri($sTitle);
	$sUriPart = empty($sUri) ? "" : "`Uri`='" . $sUri . "', ";

	$sDBModule = DB_PREFIX . ucfirst($sModule);

	getResult("UPDATE `" . $sDBModule . "Files` SET `Categories`='" . $sCategory . "', `Title`='" . $sTitle . "', " . $sUriPart . "`Tags`='" . $sTags . "', `Description`='" . $sDesc . "' WHERE `ID`='" . $sId . "'");
	return mysql_affected_rows($oDb->rLink) > 0 ? true : false;
}

function getVideoSize($sInputFile)
{
	global $sFilesPath;
	
	if(!file_exists($sInputFile) || filesize($sInputFile)==0)
	{
		if(strpos($sInputFile, $sFilesPath) === FALSE) return $sInputFile;
		else return VIDEO_SIZE_16_9;
	}
	
	$sFile = $sFilesPath . time() . IMAGE_EXTENSION;
	$sTmpl = prepareCommand($GLOBALS['aConvertTmpls']['image'], array("input" => $sInputFile, "size" => "", "second" => 0, "output" => $sFile));
	if(convertVideoFile($sFile, $sTmpl))
	{
		$aSize = getimagesize($sFile);
		@unlink($sFile);
		$iRelation = $aSize[0]/$aSize[1];
		$i169Dif = abs($iRelation - 16/9);
		$i43Dif = abs($iRelation - 4/3);
		
		if($i169Dif > $i43Dif) return VIDEO_SIZE_4_3;
		else return VIDEO_SIZE_16_9;
	}
	return VIDEO_SIZE_16_9;
}

function getConverterTmpl($sInputFile, $sSize, $bSound = true, $bRecorded = false)
{
	global $sModule;
	$bUsex264 = usex264();
	if($bSound)
		$sSound = $bUsex264 ? " -acodec libfaac -ab 128kb -ac 2 -ar 44100 " : " -acodec libmp3lame -ar 44100 ";
	else
		$sSound = " -an ";
	
	return prepareCommand($GLOBALS['aConvertTmpls'][$bUsex264 ? 'playX264' : 'play'], array("input" => $sInputFile, "bitrate" => getVideoBitrate(), "size" => getVideoSize($sSize), "audio_options" => $sSound));
}

function getVideoBitrate()
{
	global $sModule;
	
	$iBitrate = getSettingValue($sModule, "bitrate");
	if(empty($iBitrate) || !is_int($iBitrate))
		$iBitrate = 512;
	return $iBitrate;
}

function convertVideoFile($sFile, $sCommand)
{
	popen($sCommand, "r");
	if(file_exists($sFile))
		@chmod($sFile, 0666);
	return file_exists($sFile) && filesize($sFile) > 0;
}

function convertMainVideo($sId, $sTmpl = "", $bRecorded = false)
{
	global $sFilesPath;
	global $sModule;
	
	$sTempFile = $sFilesPath . $sId;
	$sResultFile = $sTempFile . (usex264() ? M4V_EXTENSION : FLV_EXTENSION);
		
	$bResult = true;
	if(!file_exists($sResultFile) || filesize($sResultFile)==0)
	{
		if(empty($sTmpl))
			$sTmpl = getConverterTmpl($sTempFile, $sTempFile, true, $bRecorded);
		$sTmpl = prepareCommand($sTmpl, array("output" => $sResultFile));
		$bResult = convertVideoFile($sResultFile, $sTmpl);
		if(!$bResult)
		{
			$sTmpl = getConverterTmpl($sTempFile, $sTempFile, false);
			$sTmpl = prepareCommand($sTmpl, array("output" => $sResultFile));
			$bResult = convertVideoFile($sResultFile, $sTmpl);
		}
	}
	if($bResult && usex264())
		$bResult = moveMp4Meta($sResultFile);
	return $bResult && grabImages($sResultFile, $sTempFile);
}

function convertVideo($sId)
{
	global $sModule;
	global $sFilesPath;
	
	$sTempFile = $sFilesPath . $sId;
	$sSourceFile = $sTempFile;
	
	$bUseX264 = usex264();
	$sTmpl = prepareCommand($GLOBALS['aConvertTmpls'][$bUseX264 ? "playX264" : "play"], array("bitrate" => getVideoBitrate(), "audio_options" => $bUseX264 ? "-acodec libfaac -ab 128kb -ac 2 -ar 44100" : "-acodec libmp3lame -ar 44100"));
	
	if(file_exists($sTempFile) && filesize($sTempFile)>0)
		$sTmpl = prepareCommand($sTmpl, array("input" => $sTempFile, "size" => getVideoSize($sTempFile)));
	else
	{
		$sSourceFile .= FLV_EXTENSION;
		if(file_exists($sSourceFile) && filesize($sSourceFile)>0)
			$sTmpl = prepareCommand($sTmpl, array("input" => $sSourceFile, "size" => getVideoSize($sSourceFile)));
	}
	if(empty($sTmpl)) return false;
	
	$sDBModule = DB_PREFIX . ucfirst($sModule);
	getResult("UPDATE `" . $sDBModule . "Files` SET `Date`='" . time() . "', `Status`='" . STATUS_PROCESSING . "' WHERE `ID`='" . $sId . "'");

	$bResult = convertMainVideo($sId, $sTmpl);
	if(!$bResult) return false;	
	
	$sMobileFile = $sTempFile . MOBILE_EXTENSION;
	$sMobileCommand = prepareCommand($GLOBALS['aConvertTmpls']['mobile'], array("input" => $sSourceFile, "size" => "qcif", "output" => $sMobileFile));
	$bResult = convertVideoFile($sMobileFile, $sMobileCommand);
	
	if($bResult)
	{
		$sAutoApprove = getSettingValue($sModule, "autoApprove") == TRUE_VAL ? STATUS_APPROVED : STATUS_DISAPPROVED;
		getResult("UPDATE `" . $sDBModule . "Files` SET `Date`='" . time() . "', `Status`='" . $sAutoApprove . "' WHERE `ID`='" . $sId . "'");
	}
	else
	{
		getResult("UPDATE `" . $sDBModule . "Files` SET `Status`='" . STATUS_FAILED . "' WHERE `ID`='" . $sId . "'");
	}
	deleteTempFiles($sId);
	return $bResult;
}

function grabImages($sInputFile, $sOutputFile, $iSecond = 0, $bForse = false)
{
	$sImageFile = $sOutputFile . IMAGE_EXTENSION;
	$sThumbFile = $sOutputFile . THUMB_FILE_NAME . IMAGE_EXTENSION;
	
	if(!$bForse && file_exists($sImageFile) && filesize($sImageFile)>0) $bResult = true;
	else $bResult = convertVideoFile($sImageFile, getGrabImageTmpl($sInputFile, $sImageFile, "", $iSecond));
	if(!$bResult) return false;
	
	if(!$bForse && file_exists($sThumbFile) && filesize($sThumbFile)>0) $bResult = true;
	else $bResult = convertVideoFile($sThumbFile, getGrabImageTmpl($sInputFile, $sThumbFile, "-s " . THUMB_SIZE, $iSecond));
	return $bResult;
}

function getGrabImageTmpl($sInputFile, $sOutputFile, $sSize = "", $iSecond = 0)
{
	global $aConvertTmpls;
	
	return prepareCommand($aConvertTmpls["image"], array("input" => $sInputFile, "second" => $iSecond, "size" => (empty($sSize) ? "" : $sSize), "output" => $sOutputFile));
}

function deleteTempFiles($sUserId, $bSourceOnly = false)
{
	global $sFilesPath;
	
	$sTempFile = $sUserId . TEMP_FILE_NAME;
	@unlink($sFilesPath . $sUserId);
	@unlink($sFilesPath . $sTempFile);
	if($bSourceOnly) return;
	@unlink($sFilesPath . $sTempFile . IMAGE_EXTENSION);
	@unlink($sFilesPath . $sTempFile . THUMB_FILE_NAME . IMAGE_EXTENSION);
	@unlink($sFilesPath . $sTempFile . FLV_EXTENSION);
	@unlink($sFilesPath . $sTempFile . M4V_EXTENSION);
}

/**
* Delete file
* @param $sFile - file identificator
* @return $bResult - result of operation (true/false)
*/
function deleteVideo($sFile)
{
	global $sFilesPath;
	global $oDb;
	global $sModule;

	$sDBModule = DB_PREFIX . ucfirst($sModule);
	getResult("DELETE FROM `" . $sDBModule . "Files` WHERE `ID`='" . $sFile . "'");
	if(mysql_affected_rows($oDb->rLink))
		video_parseTags($sFile);
	$sFileName = $sFilesPath . $sFile;
	@unlink($sFileName);
	$bResult =  (@unlink($sFileName . FLV_EXTENSION) || @unlink($sFileName . M4V_EXTENSION)) &&
				@unlink($sFileName . IMAGE_EXTENSION) &&
				@unlink($sFileName . THUMB_FILE_NAME . IMAGE_EXTENSION);
	return $bResult;
}

function getToken($sId)
{
	global $sFilesPath;
	global $sModule;
	$sDBModule = DB_PREFIX . ucfirst($sModule);
	
	if(file_exists($sFilesPath . $sId . FLV_EXTENSION) || file_exists($sFilesPath . $sId . M4V_EXTENSION))
	{
		$iCurrentTime = time();
		$sToken = md5($iCurrentTime);
		getResult("INSERT INTO `" . $sDBModule . "Tokens`(`ID`, `Token`, `Date`) VALUES('" . $sId . "', '" . $sToken . "', '" . $iCurrentTime . "')");
		return $sToken;
	}
	return "";
}
?>