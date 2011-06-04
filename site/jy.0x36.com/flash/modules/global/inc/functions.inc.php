<?
/***************************************************************************
*
* IMPORTANT: This is a commercial product made by BoonEx Ltd. and cannot be modified for other than personal usage.
* This product cannot be redistributed for free or a fee without written permission from BoonEx Ltd.
* This notice may not be removed from the source code.
*
***************************************************************************/

function secureCheckWidgetName($sWidget)
{
	return ereg("^([0-9A-Za-z_]+)$", $sWidget);
}

/**
 * get error
 * @param sError - error template
 * @param ... - variable amount of incoming parameters with information to be used in the process of parsing.
 */
function getError($sError)
{
	$iNumArgs = func_num_args();

	for($i=1; $i<$iNumArgs; $i++)
	{
		$sValue = func_get_arg($i);
		$sError = str_replace("#" . $i. "#", $sValue, $sError);
	}
	return $sError;
}

/**--------------- Installation Functions ---------------**/
/**
 * Checks permissions of files and directories.
 * @param sFileName - file's name.
 */
function checkPermissions($sFileName)
{
	$sPermissions = "";
	$sFilePath = trim($sFileName);
	clearstatcache();
	if(!file_exists($sFilePath))
		$sResult = "";
	else
	{
		clearstatcache();
		$iPermissions = fileperms($sFilePath);
		for($i=0, $offset = 0; $i<3; $i++, $offset += 3)
		{
			$iPerm = 0;
			for($j=0; $j<3; $j++) ($iPermissions >> ($j+$offset)) & 1 ? $iPerm += pow(2, $j) : "";
			$sPermissions = $iPerm . $sPermissions;
		}
		$sResult = $sPermissions;
	}
	
	$sResult = "";
	$bDir = is_dir($sFilePath);
	if(is_readable($sFilePath)) $sResult = $bDir ? "755" : "644";
	if(is_writable($sFilePath)) $sResult = $bDir ? "777" : "666";
	if(!$bDir && is_executable($sFilePath)) $sResult = "777";
	
    return $sResult;
}

/**
 * Execute MySQL queries for specified widget.
 * @param sWidget - the name of the widget.
 */
function createDataBase($sWidget)
{
	global $sModulesPath;
	global $aErrorCodes;

	//--- Add info in the database ---//
	$sWidgetFile = $sWidget . "/install/install.sql";
	$sFileName = $sModulesPath . $sWidgetFile;
	$sModuleDBPrefix = DB_PREFIX . strtoupper(substr($sWidget, 0, 1)) . substr($sWidget, 1);

	if(!file_exists($sFileName)) return getError($aErrorCodes[1], $sWidgetFile);
	$rHandler = fopen($sFileName, "r");
	while(!feof($rHandler))
	{
		$str = fgets($rHandler);
		if($str[0]=="" || $str[0]=="#" || ($str[0] == "-" && $str[1] == "-")) continue;
		$str = str_replace("[module_db_prefix]", $sModuleDBPrefix, $str);
		if( (strlen($str) > 5 ? strpos($str, ";", strlen($str) - 4) : strpos($str, ";")) )
			$sQuery .= $str;
		else
		{
			$sQuery .= $str;
			continue;
		}
		if(!($res = getResult($sQuery))) return $aErrorCodes[5];
		$sQuery = "";
	}
	fclose($rHandler);
	return "";
}

/**
 * Recompile global/js/integration.js file - (re)define given widget
 * @param sWidget - the name of the widget.
 */
function recompileIntegrator($sWidget)
{
	global $sModulesPath;
	global $sGlobalDir;
	global $aModules;
	global $aErrorCodes;

	if(!isset($sWidget)) return;
	
	$sBegin = '//' . $sWidget . ' begin';
	$sAppsArray = "\n" . 'aRayApps["' . $sWidget . '"] = new Array();' . "\n";
	$sAppTmpl = 'aRayApps["' . $sWidget . '"]["#app#"] = {"params": new Array(#params#), "top": #top#, "left": #left#, "width": #width#, "height": #height#, "resizable": #resizable#};' . "\n";
	$sEnd = '//' . $sWidget . ' end';

	//getting integrator contents
	$sFile = $sGlobalDir . "data/integration.dat";
	$sFileName = $sModulesPath . $sFile;
	if(!file_exists($sFileName)) return parseXml($aXmlTemplates['result'], getError($aErrorCodes[1], $sFile), FAILED_VAL);
	$rHandle = fopen($sFileName, "rt");
	$sJSContents = fread($rHandle, filesize($sFileName)) ;
	fclose($rHandle);

	//creating insert string
	$sInsert = $sBegin . $sAppsArray;
	$sApps = "";
	foreach($aModules as $sAppName => $aModule)
	{
		if($aModule['inline']) continue;
			
		$sApp = $sAppTmpl;
		$sApp = str_replace("#app#", $sAppName, $sApp);
		$sParams = "'" . implode("', '", $aModule['parameters']) . "'";
		$sApp = str_replace("#params#", $sParams, $sApp);
		$sApp = str_replace("#top#", $aModule['layout']['top'], $sApp);
		$sApp = str_replace("#left#", $aModule['layout']['left'], $sApp);
			
		$iWidth = getSettingValue($sWidget, $sAppName . "_width");
		if(empty($iWidth)) $iWidth = $aModule['layout']['width'];
		if(!is_numeric($iWidth)) $iWidth = $aModule['minSize']['width'];
		$iHeight = getSettingValue($sWidget, $sAppName . "_height");
		if(empty($iHeight)) $iHeight = $aModule['layout']['height'];
		if(!is_numeric($iHeight)) $iHeight = $aModule['minSize']['height'];
			
		$sApp = str_replace("#width#", $iWidth, $sApp);
		$sApp = str_replace("#height#", $iHeight, $sApp);
		$sResizable = ($aModule['vResizable'] || $aModule['hResizable']) ? "1" : "0";
		$sApp = str_replace("#resizable#", $sResizable, $sApp);
		$sApps .= $sApp;
	}
	if(empty($sApps)) return array('value' => "", 'status' => SUCCESS_VAL);
	
	$sInsert .= $sApps . $sEnd;

	//inserting javascript code
	$iInsertBegin = strpos($sJSContents, $sBegin);
	$iInsertEnd = strpos($sJSContents, $sEnd) + strlen($sEnd);
	if($iInsertBegin === false) $sJSContents .= $sInsert . '\n';
	else $sJSContents = substr($sJSContents, 0, $iInsertBegin) . $sInsert . substr($sJSContents, $iInsertEnd);

	//--- Save changes to the file---//
	$bResult = true;
	if(($rHandle = @fopen($sFileName, "wt")) !== false)
	{
		$bResult = (fwrite($rHandle, $sJSContents) !== false);
		fclose($rHandle);
	}
	$sValue = $bResult && $rHandle ? "" : getError($aErrorCodes[2], $sFile);

	return array('value' => $sValue, 'status' => $bResult ? SUCCESS_VAL : FAILED_VAL);
}

/**
 * refresh extra file (skins.xml/langs.xml)
 * @param sWidget - the name of the widget.
 * @param sCase - skins/langs
 */
function refreshExtraFile($sWidget, $sCase, $bReset = false, $sDefaultFile = "", $aEnabledFiles = array())
{
	global $sModulesPath;
	global $aXmlTemplates;
	global $aErrorCodes;

	//--- Get folder contents ---//
	$sDir = $sWidget . "/" . $sCase;
	$sDirName = $sModulesPath . $sDir;
	if( !(file_exists($sDirName) && is_dir($sDirName)) ) return parseXml($aXmlTemplates['result'], getError($aErrorCodes[6], $sDir), FAILED_VAL);

	$aFiles = getExtraFiles($sWidget, $sCase, false);
	$iFilesCount = count($aFiles['files']);
	if($iFilesCount == 0) return array('value' => getError($aErrorCodes[7], $sDir), 'status' => FAILED_VAL, 'contents' => "");

	//--- Get XML file contents ---//
	$aFileContents = getFileContents($sWidget, "/xml/" . $sCase . ".xml", true);
	if($aFileContents['status'] == FAILED_VAL) return array('value' => $aFileContents['value'], 'status' => FAILED_VAL, 'contents' => "");
	$aContents = $aFileContents['contents'];

	//--- Merge folder and file contents ---//
	$sCurrent = isset($aContents[FILE_DEFAULT_KEY]) && in_array($aContents[FILE_DEFAULT_KEY], $aFiles["files"]) ? $aContents[FILE_DEFAULT_KEY] : $aFiles["current"];
	$sCurrent = $bReset && in_array($sDefaultFile, $aFiles["files"]) ? $sDefaultFile : $sCurrent;
	$aEnabledFiles[] = $sCurrent;
	$sContents = parseXml($aXmlTemplates["item"], FILE_DEFAULT_KEY, $sCurrent);

	for($i=0; $i<$iFilesCount; $i++)
	{
		$sEnabled = isset($aContents[$aFiles["files"][$i]]) ? $aContents[$aFiles["files"][$i]] : TRUE_VAL;
		if($bReset)
		   $sEnabled = in_array($aFiles["files"][$i], $aEnabledFiles) ? TRUE_VAL : FALSE_VAL;
		$sContents .= parseXml($aXmlTemplates["item"], $aFiles["files"][$i], $sEnabled);
	}
	$sContents = makeGroup($sContents, "items");

	//--- Save changes to the file---//
	$sFile = $sWidget . "/xml/" . $sCase . ".xml";
	$sFileName = $sModulesPath . $sFile;
	$bResult = false;
	if(($rHandle = @fopen($sFileName, "wt")) !== false)
	{
		$bResult = (fwrite($rHandle, $sContents) !== false);
		fclose($rHandle);
	}
	$bResult = $bResult && $rHandle;
	$sValue = $bResult ? "" : getError($aErrorCodes[2], $sFile);

	return array('value' => $sValue, 'status' => $bResult ? SUCCESS_VAL : FAILED_VAL, 'contents' => $sContents);
}

/**
 * creates widget's main config file
 * @param sWidget - the name of the widget.
 */
function createMainFile($sWidget)
{
	global $sModulesPath;
	global $aXmlTemplates;
	global $aErrorCodes;
	
	$bResult = false;
	if(secureCheckWidgetName($sWidget))
	{
		require($sModulesPath . $sWidget . "/inc/constants.inc.php");

		$sCode = $aInfo['code'];
		$sContents = parseXml($aXmlTemplates["item"], "status", WIDGET_STATUS_NOT_REGISTERED);
		$sContents .= parseXml($aXmlTemplates["item"], "license", "");
		$sContents .= parseXml($aXmlTemplates["item"], "code", $sCode);
		$sContents .= parseXml($aXmlTemplates["item"], "updated", TRUE_VAL);
		$sContents .= parseXml($aXmlTemplates["item"], "updateLast", "");
		$sContents .= parseXml($aXmlTemplates["item"], "updateUrl", "");
		$sContents = makeGroup($sContents, "items");

		//--- Save changes to the file---//
		$sFile = $sWidget . "/xml/main.xml";
		$sFileName = $sModulesPath . $sFile;
		if(($rHandle = @fopen($sFileName, "wt")) !== false)
		{
			$bResult = (fwrite($rHandle, $sContents) !== false);
			fclose($rHandle);
		}
		$bResult = $bResult && $rHandle;
		$sValue = $bResult ? "" : getError($aErrorCodes[2], $sFile);
	}
	else
	{
		$sValue = $aErrorCodes[8];
	}

	return array('value' => $sValue, 'status' => $bResult ? SUCCESS_VAL : FAILED_VAL);
}

/**
 * gets file contents
 * @param sWidget - the name of the widget.
 * @param sCase - skins/langs
 */
function getFileContents($sWidget, $sFile, $bArray = false)
{
	global $sModulesPath;
	global $aErrorCodes;

	$sFile = $sWidget . $sFile;
	$sFileName = $sModulesPath . $sFile;
	if(!file_exists($sFileName)) return array('value' => getError($aErrorCodes[1], $sFile), 'status' => FAILED_VAL, 'contents' => $bArray ? array() : "");
	$rHandle = fopen($sFileName, "rt");
	$iFileSize = filesize($sFileName);
	$sContents = $iFileSize > 0 ? fread($rHandle, filesize($sFileName)) : makeGroup("", "items");
	fclose($rHandle);

	$aContents = xmlGetValues($sContents, "item");
	return array('value' => "", 'status' => SUCCESS_VAL, 'contents' => $bArray ? $aContents : $sContents);
}

/**
 * gets file extension by given widget name and folder
 * @param sWidget - the name of the widget.
 * @param sFolder
 */
function getFileExtension($sWidget, $sFolder)
{
	global $sModulesPath;

	$aRightExtensions = array("swf", "xml");
	$sFolderPath = $sModulesPath . $sWidget . "/" . $sFolder . "/";
	if($rDirHandle = opendir($sFolderPath))
		while (false !== ($sFile = readdir($rDirHandle)))
		{
			$aPathInfo = pathinfo($sFolderPath . $sFile);
			if(is_file($sFolderPath . $sFile) && $sFile != "." && $sFile != ".." && in_array($aPathInfo['extension'], $aRightExtensions))
				return $aPathInfo['extension'];
		}
	return "";
}

function smartReadFile($location, $filename, $mimeType='application/octet-stream')
{
	if(!file_exists($location))
	{
		header ("HTTP/1.0 404 Not Found");
		return;
	}

	$size=filesize($location);
	$time=date('r',filemtime($location));
 
	$fm=@fopen($location,'rb');
	if(!$fm)
	{
		header ("HTTP/1.0 505 Internal server error");
		return;
	}
	$begin=0;
	$end=$size;

	if(isset($_SERVER['HTTP_RANGE']))
	{
		if(preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches))
		{
			$begin=intval($matches[1]);
			if(!empty($matches[2]))
				$end=intval($matches[2]);
		}
	}
 
	if($begin>0||$end<$size)
		header('HTTP/1.0 206 Partial Content');
	else
		header('HTTP/1.0 200 OK'); 

	header("Content-Type: $mimeType");
	header('Cache-Control: public, must-revalidate, max-age=0');
	header('Pragma: no-cache'); 
	header('Accept-Ranges: bytes');
	header('Content-Length:'.($end-$begin));
	header("Content-Range: bytes $begin-$end/$size");
	header("Content-Disposition: inline; filename=$filename");
	header("Content-Transfer-Encoding: binary\n");
	header("Last-Modified: $time");
	header('Connection: close'); 
	
	$cur=$begin;
	fseek($fm,$begin,0);

	while(!feof($fm)&&$cur<$end&&(connection_status()==0))
	{
		print fread($fm,min(1024*16,$end-$cur));
		$cur+=1024*16;
	}
}
?>