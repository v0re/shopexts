<?
function post($sTable, $sId, $sAuthor, $sParent, $sMood, $sFileId)
{
	global $sIncPath;
	global $sModule;
	global $sHomeUrl;
		
	$sText = getEmbedCode($sModule, "player", array('id' => $sFileId));
	$sText = str_replace($sHomeUrl, "[ray_url]", $sText);
	$sSql = "INSERT INTO `" . $sTable . "`(`cmt_parent_id`, `cmt_object_id`, `cmt_author_id`, `cmt_text`, `cmt_mood`, `cmt_time`) VALUES('" . $sParent . "', '" . $sId . "', '" . $sAuthor . "', '" . $sText . "', '" . $sMood . "', NOW())";
	getResult($sSql);
	$iCommentId = getLastInsertId();
	getResult("UPDATE `" . MODULE_DB_PREFIX . "Files` SET `Description`='" . $iCommentId . "' WHERE `ID`='" . $sFileId . "'");
	return $iCommentId;
}

function deleteFileByCommentId($iCommentId)
{
	global $sModule;
	$sDBModule = DB_PREFIX . ucfirst($sModule);
	
	$iId = (int)getValue("SELECT `ID` FROM `" . $sDBModule . "Files` WHERE `Description`='" . $iCommentId . "' LIMIT 1");
	if($iId > 0)
		_deleteFile($iId);
}
?>