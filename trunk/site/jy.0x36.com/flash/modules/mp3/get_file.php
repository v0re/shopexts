<?
require_once("../../../inc/header.inc.php");
require_once($sIncPath . "customFunctions.inc.php");

$bResult = false;
$sId = (int)$_GET["id"];
$sToken = process_db_input($_GET["token"]);
$sFile = "files/" . $sId . ".mp3";

if(!empty($sId) && !empty($sToken) && file_exists($sFile))
{
	require_once($sIncPath . "db.inc.php");
	$sId = getValue("SELECT `ID` FROM `RayMp3Tokens` WHERE `ID`='" . $sId . "' AND `Token`='" . $sToken . "' LIMIT 1");
	$bResult = !empty($sId);
}

if($bResult)
{
	require_once($sIncPath . "functions.inc.php");
	smartReadFile($sFile, $sFile, "audio/mpeg");
}
else
	readfile($sFileErrorPath);
?>