<?php
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

//require_once('BxDolInstallerUtils.php' );
require_once('BxDolIO.php' );
//require_once('BxDolInstallerUtils.php' );

class BxDolAdminTools extends BxDolIO {

	/*var $sUnit;
	var $sAction;
	var $iObject;
	var $iSender;

	var $aUnit;*/

	//var $aInstallFiles;
	var $sTroubledElements;

	var $aInstallDirs;
	var $aInstallFiles;
	var $aFlashDirs;
	var $aFlashFiles;
	var $aPostInstallPermDirs;
	var $aPostInstallPermFiles;

	//constructor
	function BxDolAdminTools() {
	    parent::BxDolIO();
	    
		$this->sTroubledElements = '';

		$this->aInstallDirs = array(
			'backup',
			'cache',
			'cache_public',
			'langs',
			'media/app',
			'media/images',
			'media/images/banners',
			'media/images/blog',
			'media/images/classifieds',
			'media/images/membership',
			'media/images/profile',
			'media/images/profile_bg',
			'media/images/promo',
			'media/images/promo/original',
			'tmp',
            'plugins/htmlpurifier/standalone/HTMLPurifier/DefinitionCache/Serializer',
            'plugins/htmlpurifier/standalone/HTMLPurifier/DefinitionCache/Serializer/HTML',
            'plugins/htmlpurifier/standalone/HTMLPurifier/DefinitionCache/Serializer/CSS',
            'plugins/htmlpurifier/standalone/HTMLPurifier/DefinitionCache/Serializer/Test',
            'plugins/htmlpurifier/standalone/HTMLPurifier/DefinitionCache/Serializer/URI',
		);

		$this->aInstallFiles = array(
			
		);

		$this->aFlashDirs = array(
			'flash/modules/board/files',
			'flash/modules/chat/files',
			'flash/modules/photo/files',
			'flash/modules/im/files',
			'flash/modules/mp3/files',
			'flash/modules/video/files',
			'flash/modules/video_comments/files'
		);

		$this->aFlashFiles = array(
			'flash/modules/global/data/integration.dat',
			'flash/modules/board/xml/config.xml',
			'flash/modules/board/xml/langs.xml',
			'flash/modules/board/xml/main.xml',
			'flash/modules/board/xml/skins.xml',
			'flash/modules/chat/xml/config.xml',
			'flash/modules/chat/xml/langs.xml',
			'flash/modules/chat/xml/main.xml',
			'flash/modules/chat/xml/skins.xml',
			'flash/modules/desktop/xml/config.xml',
			'flash/modules/desktop/xml/langs.xml',
			'flash/modules/desktop/xml/main.xml',
			'flash/modules/desktop/xml/skins.xml',
			'flash/modules/global/app/ffmpeg.exe',
			'flash/modules/global/xml/config.xml',
			'flash/modules/global/xml/main.xml',
			'flash/modules/im/xml/config.xml',
			'flash/modules/im/xml/langs.xml',
			'flash/modules/im/xml/main.xml',
			'flash/modules/im/xml/skins.xml',
			'flash/modules/mp3/xml/config.xml',
			'flash/modules/mp3/xml/langs.xml',
			'flash/modules/mp3/xml/main.xml',
			'flash/modules/mp3/xml/skins.xml',
			'flash/modules/photo/xml/config.xml',
			'flash/modules/photo/xml/langs.xml',
			'flash/modules/photo/xml/main.xml',
			'flash/modules/photo/xml/skins.xml',
			'flash/modules/video/xml/config.xml',
			'flash/modules/video/xml/langs.xml',
			'flash/modules/video/xml/main.xml',
			'flash/modules/video/xml/skins.xml',
			'flash/modules/video_comments/xml/config.xml',
			'flash/modules/video_comments/xml/langs.xml',
			'flash/modules/video_comments/xml/main.xml',
			'flash/modules/video_comments/xml/skins.xml'
		);
	
		$this->aPostInstallPermDirs = array(
		);

		$this->aPostInstallPermFiles = array(
		);
	}

	function GenCommonCode() {
		$sAdditionDir = (isAdmin()==true) ? BX_DOL_URL_ROOT : '../';
		$sMasterSwPic = $sAdditionDir . 'media/images/master_nav.gif';

		$sRet = <<<EOF
<style type="text/css">

	div.hidden {
		display:none;
	}

	.left_side_sw_caption {
		float:left;
		text-align:justify;
		width:515px;
	}

	.right_side_sw_caption {
		float:right;
		width:60px;
	}

	tr.head td {
		background-color:#FFFFFF;
		font-weight:bold;
		height:17px;
		padding:5px;
		text-align:center;
		font-size:13px;
		border-color:silver;
	}
	tr.cont td {
		height:15px;
		padding:2px 5px;
		font-size:13px;
		border-color:silver;
	}

	.install_table {
		background-color: silver;
		border-width:0px;
	}

	span.unwritable {
		color:red;
		font-weight:bold;
		margin-right:5px;
	}

	span.writable {
		color:green;
		font-weight:bold;
		margin-right:5px;
	}

	span.desired {
		font-weight:bold;
		margin-right:5px;
	}

	.install_table tr.even {
		background-color:#EDE9E9;
	}

	.install_table tr.odd {
		background-color:#FFF;
	}

	.install_table tr:hover{
		background-color:#DDD;
	}

	#btn-alls-on {
		background:transparent url({$sMasterSwPic}) no-repeat scroll -0px -0px;
		width:25px;
		height:21px;
	}
	#btn-alls-off {
		background:transparent url({$sMasterSwPic}) no-repeat scroll -0px -20px;
		width:25px;
		height:21px;
	}
	#btn-troubled-on {
		background:transparent url({$sMasterSwPic}) no-repeat scroll -24px -20px;
		width:25px;
		height:21px;
	}

	#btn-troubled-off {
		background:transparent url({$sMasterSwPic}) no-repeat scroll -24px -0px;
		width:25px;
		height:21px;
	}

	tr.head td.left_aligned {
		text-align:left;
		font-weight:bold;
	}
</style>
EOF;
		return $sRet;
	}

	function GenPermTable() {
		$sDirsC = function_exists('_t') ? _t('_adm_admtools_Directories') : 'Directories';
		$sFilesC = function_exists('_t') ? _t('_adm_admtools_Files') : 'Files';
		$sElementsC = function_exists('_t') ? _t('_adm_admtools_Elements') : 'Elements';
		$sFlashC = function_exists('_t') ? _t('_adm_admtools_Flash') : 'Flash';
		$sCurrentLevelC = function_exists('_t') ? _t('_adm_admtools_Current_level') : 'Current level';
		$sDesiredLevelC = function_exists('_t') ? _t('_adm_admtools_Desired_level') : 'Desired level';
		$sBadFilesC = function_exists('_t') ? _t('_adm_admtools_Bad_files') : 'Next files and directories have inappropriate permissions';
		$sShowOnlyBadC = function_exists('_t') ? _t('_adm_admtools_Only_bad_files') : 'Show only troubled files and directories with inappropriate permissions';
		$sShowAllC = function_exists('_t') ? _t('_adm_admtools_Show_all_files') : 'Show all files and directories';
		$sDescriptionC = function_exists('_t') ? _t('_adm_admtools_Perm_description') : 'Dolphin needs special access for certain files and directories. Please, change permissions as specified in the chart below. Helpful info about permissions is <a href="http://www.boonex.com/trac/dolphin/wiki/DetailedInstall#Permissions" target="_blank">available here</a>.';

		$this->sTroubledElements = '';

		$sInstallDirs = $this->GenArrElemPerm($this->aInstallDirs, 1);
		$sFlashDirs = $this->GenArrElemPerm($this->aFlashDirs, 1);
		$sInstallFiles = $this->GenArrElemPerm($this->aInstallFiles, 2);
		$sFlashFiles = $this->GenArrElemPerm($this->aFlashFiles, 2);

		$sAdditionDir = (isAdmin()==true) ? BX_DOL_URL_ROOT : '../';
		$sLeftAddEl = (isAdmin()==true) ? '<div class="left_side_sw_caption">'.$sDescriptionC.'</div>' : '';

		$sSpacerPic = $sAdditionDir . 'media/images/spacer.gif';

		$sRet = <<<EOF
<script type="text/javascript">
	<!--
	function callSwitcher(){
		$('table.install_table tr:not(.troubled)').toggle();
	}

	function switchToTroubled(viewType) {
		if (viewType == 'A') {
			$("#btn-alls-off").attr("id", "btn-alls-on");
			$("#btn-troubled-on").attr("id", "btn-troubled-off");
			$('table.install_table tr:not(.troubled)').show();
		} else if (viewType == 'T') {
			$("#btn-alls-on").attr("id", "btn-alls-off");
			$("#btn-troubled-off").attr("id", "btn-troubled-on");
			$('table.install_table tr:not(.troubled)').hide();
		}
		return false;
	}
	-->
</script>

<table width="100%" cellspacing="1" cellpadding="0" class="install_table">
	<tr class="head troubled">
		<td colspan="3" style="text-align:center;">
		{$sLeftAddEl}
		<div class="right_side_sw_caption">
			<a onclick="return switchToTroubled('A')" href="#"><img id="btn-alls-on" src="{$sSpacerPic}" alt="{$sShowAllC}" title="{$sShowAllC}" /></a>
			<a onclick="return switchToTroubled('T')" href="#"><img id="btn-troubled-off" src="{$sSpacerPic}" alt="{$sShowOnlyBadC}" title="{$sShowOnlyBadC}" /></a>
		</div>
		<div class="clear_both"></div>
		</td>
	</tr>
	<tr class="head">
		<td colspan="3" style="text-align:center;" class="normal_td">{$sDirsC}</td>
	</tr>
	<tr class="head">
		<td>{$sDirsC}</td>
		<td>{$sCurrentLevelC}</td>
		<td>{$sDesiredLevelC}</td>
	</tr>
	{$sInstallDirs}
	<tr class="head">
		<td>{$sFlashC} {$sDirsC}</td>
		<td>{$sCurrentLevelC}</td>
		<td>{$sDesiredLevelC}</td>
	</tr>
	{$sFlashDirs}
	<tr class="head">
		<td colspan="3" style="text-align:center;">{$sFilesC}</td>
	</tr>
	<tr class="head">
		<td>{$sFilesC}</td>
		<td>{$sCurrentLevelC}</td>
		<td>{$sDesiredLevelC}</td>
	</tr>
	{$sInstallFiles}
	<tr class="head">
		<td>{$sFlashC} {$sFilesC}</td>
		<td>{$sCurrentLevelC}</td>
		<td>{$sDesiredLevelC}</td>
	</tr>
	{$sFlashFiles}
	<tr class="head troubled">
		<td colspan="3" style="text-align:center;">{$sBadFilesC}</td>
	</tr>
	<tr class="head troubled">
		<td>{$sElementsC}</td>
		<td>{$sCurrentLevelC}</td>
		<td>{$sDesiredLevelC}</td>
	</tr>
	{$this->sTroubledElements}
</table>
EOF;
		return $sRet;
	}

	function GenArrElemPerm($aElements, $iType) { //$iType: 1 - folder, 2 - file
		if (!is_array($aElements) || empty($aElements))
			return '';
		$sWritableC = function_exists('_t') ? _t('_adm_admtools_Writable') : 'Writable';
		$sNonWritableC = function_exists('_t') ? _t('_adm_admtools_Non_Writable') : 'Non-Writable';
		$sNotExistsC = function_exists('_t') ? _t('_adm_admtools_Not_Exists') : 'Not Exists';
		$sExecutableC = function_exists('_t') ? _t('_adm_admtools_Executable') : 'Executable';
		$sNonExecutableC = function_exists('_t') ? _t('_adm_admtools_Non_Executable') : 'Non-Executable';

		$iType = ($iType==1) ? 1 : 2;

		$sElements = '';
		$i = 0;
		foreach ($aElements as $sCurElement) {
			$iCurType = $iType;

			$sAwaitedPerm = ($iCurType==1) ? $sWritableC : $sWritableC;

			$sElemCntStyle = ($i%2==0) ? 'even' : 'odd' ;
			$bAccessible = ($iCurType==1) ? $this->isWritable($sCurElement) : $this->isWritable($sCurElement);

			if ($sCurElement == 'flash/modules/global/app/ffmpeg.exe') {
				$sAwaitedPerm = $sExecutableC;
				$bAccessible = $this->isExecutable($sCurElement);
			}

			if ($bAccessible) {
				$sResultPerm = ($iCurType==1) ? $sWritableC : $sWritableC;

				if ($sCurElement == 'flash/modules/global/app/ffmpeg.exe') {
					$sResultPerm = $sExecutableC;
				}

				$sElements .= <<<EOF
<tr class="cont {$sElemCntStyle}">
	<td>{$sCurElement}</td>
	<td class="span">
		<span class="writable">{$sResultPerm}</span>
	</td>
	<td class="span">
		<span class="desired">{$sAwaitedPerm}</span>
	</td>
</tr>
EOF;
			} else {
				$sPerm = $this->getPermissions($sCurElement);
				$sResultPerm = '';
				if ($sPerm==false) {
					$sResultPerm = $sNotExistsC;
				} else {
					$sResultPerm = ($iCurType==1) ? $sNonWritableC : $sNonWritableC;
				}

				if ($sCurElement == 'flash/modules/global/app/ffmpeg.exe') {
					$sResultPerm = $sNonExecutableC;
				}

				$sPerm = '';

				$sElements .= <<<EOF
<tr class="cont {$sElemCntStyle}">
	<td>{$sCurElement}</td>
	<td class="span">
		<span class="unwritable">{$sPerm} {$sResultPerm}</span>
	</td>
	<td class="span">
		<span class="desired">{$sAwaitedPerm}</span>
	</td>
</tr>
EOF;

				$this->sTroubledElements .= <<<EOF
<tr class="cont {$sElemCntStyle} troubled">
	<td>{$sCurElement}</td>
	<td class="span">
		<span class="unwritable">{$sPerm} {$sResultPerm}</span>
	</td>
	<td class="span">
		<span class="desired">{$sAwaitedPerm}</span>
	</td>
</tr>
EOF;

			}
			$i++;
		}
		return $sElements;
	}

	function performInstalCheck() { //check requirements
		$aErrors = array();

		$aErrors[] = (ini_get('register_globals') == 0) ? '' : '<font color="red">register_globals is On (warning, you should have this param in Off state, or your site will unsafe)</font>';
		$aErrors[] = (ini_get('safe_mode') == 0) ? '' : '<font color="red">safe_mode is On, disable it</font>';
		//$aErrors[] = (ini_get('allow_url_fopen') == 0) ? 'Off (warning, better keep this parameter in On to able register Dolphin' : '';
		$aErrors[] = (((int)phpversion()) < 4) ? '<font color="red">PHP version too old, update server please</font>' : '';
		$aErrors[] = (! extension_loaded( 'mbstring')) ? '<font color="red">mbstring extension not installed. <b>Warning!</b> Dolphin cannot work without <b>mbstring</b> extension.</font>' : '';

		if (version_compare(phpversion(), "5.2", ">") == 1) {
			$aErrors[] = (ini_get('allow_url_include') == 0) ? '' : '<font color="red">allow_url_include is On (warning, you should have this param in Off state, or your site will unsafe)</font>';
		};

		$aErrors = array_diff($aErrors, array('')); //delete empty
		if (count($aErrors)) {
			$sErrors = implode(" <br /> ", $aErrors);
			echo <<<EOF
{$sErrors} <br />
Please go to the <br />
<a href="http://www.boonex.com/trac/dolphin/wiki/GenDolTShooter">Dolphin Troubleshooter</a> <br />
and solve the problem.
EOF;
			exit;
		}
	}

    function GenCacheEnginesTable() {
        
        $sRet = '<table width="100%" cellspacing="1" cellpadding="0" class="install_table">';

        $aEngines = array ('File', 'EAccelerator', 'Memcache', 'APC', 'XCache');
        foreach ($aEngines as $sEngine) {
            $oCacheObject = @bx_instance ('BxDolCache' . $sEngine);
            $sRet .= '
<tr class="head troubled">
    <td>' . $sEngine . '</td>
    <td class="left_aligned">' . (@$oCacheObject->isAvailable() ? '<font color="green">' . _t('_Yes') . '</font>' : '<font color="red">' . _t('_No') . '</font>') . '</td>
</tr>';
        }

		$sRet .= '</table>';
        return $sRet;
    }

	function GenMainParamsTable() {
		$sNameC = _t('_adm_admtools_Name');
		$sValueC = _t('_adm_admtools_Value');
		$sRecommendedC = _t('_adm_admtools_Recommended');
		$sDifferentSettingsC = _t('_adm_admtools_Different_settings');
		$sInstalledApacheModulesC = _t('_adm_admtools_Installed_apache_modules');

		$sRegGlobal = ini_get('register_globals');
		$sRegGlobal = ($sRegGlobal==0) ? '<font color="green">Off</font>' : '<font color="red">On (warning, you should have this param in Off state, or your site will unsafe)</font>';
		$sSafeMode = ini_get('safe_mode');
		$sSafeMode = ($sSafeMode==0) ? '<font color="green">Off</font>' : '<font color="red">On (warning)</font>';
		$sDisabledFunc = ini_get('disable_functions');
		$sMemLimit = ini_get('memory_limit');
		$sMaxExecTime = ini_get('max_execution_time');
		$sPostMaxSize = ini_get('post_max_size');
		$sUploadMaxSize = ini_get('upload_max_filesize');
		$sAllowUrlFopen = ini_get('allow_url_fopen');
		$sAllowUrlFopen = ($sAllowUrlFopen==0) ? 'Off (warning, better keep this parameter in On to able register Dolphin' : '<font color="green">On</font>';

		$sSQLClientInfo = mysql_get_client_info();
		$sPhpVersion = phpversion();

		$sMbstring = '<font color="green">mbstring extension installed</font>';
		//check mbstring
		if( !extension_loaded( 'mbstring' ) ) {
			$sMbstring = '<font color="red">mbstring extension not installed. <b>Warning!</b> Dolphin cannot work without <b>mbstring</b> extension.</font>';
		}

		$sAllowUrlInclude = ($this->isAllowUrlInclude() == true) ? '<font color="red">On (warning, disable it, or your site will unsafe)</font>' : '<font color="green">Off</font>';

		$sInstalledModules = '';
		if (function_exists('apache_get_modules')) {
			$aInstalledModules = apache_get_modules();
			ob_start();
			echoDbg($aInstalledModules);
			$sInstalledModules = ob_get_contents();
			ob_end_clean();
		} else {
			$sInstalledModules = 'Can`t evaluate installed modules';
		}

		$sOperationSystem = @php_uname();

		$sFfmpeg = realpath(BX_DIRECTORY_PATH_ROOT . "flash/modules/global/app/ffmpeg.exe");
		$sFfmpegOutput  = `$sFfmpeg -version 2>&1`;

		$sRet = <<<EOF
<table width="100%" cellspacing="1" cellpadding="0" class="install_table">
	<tr class="head troubled">
		<td>{$sNameC}</td>
		<td class="left_aligned">{$sValueC}</td>
		<td class="left_aligned">{$sRecommendedC}</td>
	</tr>
	<tr class="head troubled">
		<td colspan="3" style="text-align:center;">{$sDifferentSettingsC}</td>
	</tr>
	<tr class="head troubled">
		<td>register globals</td>
		<td class="left_aligned">{$sRegGlobal}</td>
		<td class="left_aligned">Off</td>
	</tr>
	<tr class="head troubled">
		<td>safe mode</td>
		<td class="left_aligned">{$sSafeMode}</td>
		<td class="left_aligned">Off</td>
	</tr>
	<tr class="head troubled">
		<td>disabled functions</td>
		<td class="left_aligned">{$sDisabledFunc}</td>
		<td class="left_aligned"></td>
	</tr>
	<tr class="head troubled">
		<td>memory limit</td>
		<td class="left_aligned">{$sMemLimit}</td>
		<td class="left_aligned">128M</td>
	</tr>
	<tr class="head troubled">
		<td>max execution time</td>
		<td class="left_aligned">{$sMaxExecTime}</td>
		<td class="left_aligned">300</td>
	</tr>
	<tr class="head troubled">
		<td>post_max_size</td>
		<td class="left_aligned">{$sPostMaxSize}</td>
		<td class="left_aligned">128M or more</td>
	</tr>
	<tr class="head troubled">
		<td>upload max filesize</td>
		<td class="left_aligned">{$sUploadMaxSize}</td>
		<td class="left_aligned">128M or more</td>
	</tr>
	<tr class="head troubled">
		<td>allow_url_fopen</td>
		<td class="left_aligned">{$sAllowUrlFopen}</td>
		<td class="left_aligned">On</td>
	</tr>
	<tr class="head troubled">
		<td>allow_url_include (for php > 5.2)</td>
		<td class="left_aligned">{$sAllowUrlInclude}</td>
		<td class="left_aligned">Off</td>
	</tr>
	<tr class="head troubled">
		<td>mbstring installation</td>
		<td class="left_aligned">{$sMbstring}</td>
		<td class="left_aligned">Installed</td>
	</tr>
	<tr class="head troubled">
		<td>PHP version</td>
		<td class="left_aligned">{$sPhpVersion}</td>
		<td class="left_aligned">4.4.0/5.1.0 and higher</td>
	</tr>
	<tr class="head troubled">
		<td>SQL Client library version</td>
		<td class="left_aligned">{$sSQLClientInfo}</td>
		<td class="left_aligned">4.1.2 and higher</td>
	</tr>
	<tr class="head troubled">
		<td>{$sInstalledApacheModulesC}</td>
		<td class="left_aligned">{$sInstalledModules}</td>
		<td class="left_aligned"></td>
	</tr>
	<tr class="head troubled">
		<td>Operating system</td>
		<td class="left_aligned">{$sOperationSystem}</td>
		<td class="left_aligned">Unix / Linux / Windows</td>
	</tr>
	<tr class="head troubled">
		<td colspan="3" style="text-align:center;">Flash ffmpeg settings</td>
	</tr>
	<tr class="head troubled">
		<td>ffmpeg output</td>
		<td class="left_aligned">{$sFfmpegOutput}</td>
		<td class="left_aligned"></td>
	</tr>
</table>
EOF;
		return $sRet;
	}

	function GenTabbedPage() {
		$sTitleC = _t('_adm_admtools_title');
		$sPermissionsC = _t('_adm_admtools_Permissions');
		$sHostParamsC = _t('_adm_admtools_Host_Params');
        $sCacheEnginesC = _t('_adm_admtools_cache_engines');

		$sPermissionsTab = $this->GenPermTable();
		$sSettingsTab = $this->GenMainParamsTable();
        $sCacheEnginesTab = $this->GenCacheEnginesTable();

		$sBoxContent = <<<EOF
<script type="text/javascript">
	<!--
	function switchAdmPage(iPageID) {
		//make all tabs - inactive
		//mace selected tab - active
		//hide all pages
		//show selected page

		$(".dbTopMenu").children().removeClass().toggleClass("notActive");
		$("#main_menu" + iPageID).removeClass().toggleClass("active");

		$("#adm_pages").children().removeClass().toggleClass("hidden");
		$("#adm_pages #page" + iPageID).removeClass().toggleClass("visible");

		return false;
	}
	-->
</script>

<div class="boxContent" id="adm_pages">
	<div id="page1" class="visible">{$sPermissionsTab}</div>
	<div id="page2" class="hidden">{$sSettingsTab}</div>
	<div id="page3" class="hidden">
        <iframe frameborder="0" width="100%" height="800" scrolling="auto" src="host_tools.php?get_phpinfo=true"></iframe>
    </div>
    <div id="page4" class="hidden">{$sCacheEnginesTab}</div>
</div>
EOF;

		$sActions = <<<EOF
<div class="dbTopMenu">
	<div class="active" id="main_menu1"><span><a href="#" class="top_members_menu" onclick="switchAdmPage(1); return false;">{$sPermissionsC}</a></span></div>
	<div class="notActive" id="main_menu2"><span><a href="#" class="top_members_menu" onclick="switchAdmPage(2); return false;">{$sHostParamsC}</a></span></div>
	<div class="notActive" id="main_menu3"><span><a href="#" class="top_members_menu" onclick="switchAdmPage(3); return false;">phpinfo</a></span></div>
    <div class="notActive" id="main_menu4"><span><a href="#" class="top_members_menu" onclick="switchAdmPage(4); return false;">{$sCacheEnginesC}</a></span></div>
</div>
EOF;

		$sWrappedBox = $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $sBoxContent));
		return DesignBoxContent($sTitleC, $sWrappedBox, 1, $sActions);
	}

	//************
	function isFolderReadWrite($filename) {
		clearstatcache();

		$aPathInfo = pathinfo(__FILE__);
		$filename = $aPathInfo['dirname'] . '/../../' . $filename;

		return (@file_exists($filename . '/.') && is_readable( $filename ) && is_writable( $filename ) ) ? true : false;
	}

	function isFileReadWrite($filename) {
		clearstatcache();

		$aPathInfo = pathinfo(__FILE__);
		$filename = $aPathInfo['dirname'] . '/../../' . $filename;

	    return (is_file($filename) && is_readable( $filename ) && is_writable( $filename ) ) ? true : false;
	}

	function isFileExecutable($filename) {
		clearstatcache();

		$aPathInfo = pathinfo(__FILE__);
		$filename = $aPathInfo['dirname'] . '/../../' . $filename;

		return (is_file($filename) && is_executable( $filename ) ) ? true : false;
	}

	//************

	function isAllowUrlInclude() {
		if (version_compare(phpversion(), "5.2", ">") == 1) {
			$sAllowUrlInclude = ini_get('allow_url_include');
			return !($sAllowUrlInclude == 0);
		};
		return false;
	}

}

?>
