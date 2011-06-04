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

require_once( '../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'admin_design.inc.php' );
bx_import('BxDolFtp');
bx_import('BxDolInstallerUi');
bx_import('BxTemplFormView');

$logged['admin'] = member_auth( 1, true, true );

$oInstallerUi = new BxDolInstallerUi();

//--- Check actions ---//
$sResult = $sResultUpload = '';
$sResultDeleteModule = $sResultDeleteUpdate = '';
if(isset($_POST['modules-install']) && is_array($_POST['pathes']) && !empty($_POST['pathes']))
    $sResult = $oInstallerUi->actionInstall($_POST['pathes']);
if(isset($_POST['modules-delete']) && is_array($_POST['pathes']) && !empty($_POST['pathes']))
    $sResultDeleteModule = $oInstallerUi->actionDelete($_POST['pathes']);
else if(isset($_POST['modules-update']) && is_array($_POST['pathes']) && !empty($_POST['pathes']))
    $oInstallerUi->setCheckPathes($_POST['pathes']);
else if(isset($_POST['modules-uninstall']) && is_array($_POST['pathes']) && !empty($_POST['pathes']))
    $sResult = $oInstallerUi->actionUninstall($_POST['pathes']);
else if(isset($_POST['modules-recompile-languages']) && is_array($_POST['pathes']) && !empty($_POST['pathes']))
    $sResult = $oInstallerUi->actionRecompile($_POST['pathes']);
if(isset($_POST['updates-install']) && is_array($_POST['pathes']) && !empty($_POST['pathes']))
    $sResult = $oInstallerUi->actionUpdate($_POST['pathes']);
if(isset($_POST['updates-delete']) && is_array($_POST['pathes']) && !empty($_POST['pathes']))
    $sResultDeleteUpdate = $oInstallerUi->actionDelete($_POST['pathes']);
else if(isset($_POST['submit_upload']) && isset($_FILES['module']) && !empty($_FILES['module']['tmp_name']))
    $sResultUpload = $oInstallerUi->actionUpload('module', $_FILES['module'], $_POST);
else if(isset($_POST['submit_upload']) && isset($_FILES['update']) && !empty($_FILES['update']['tmp_name']))
    $sResultUpload = $oInstallerUi->actionUpload('update', $_FILES['update'], $_POST);

//--- Display cotent ---//
$iNameIndex = 7;

$_page['name_index'] = $iNameIndex;
$_page['css_name'] = array('forms_adv.css', 'modules.css');
$_page['header'] = "Modules";
$_page_cont[$iNameIndex] = array(
    'page_code_results' => !empty($sResult) ? DesignBoxAdmin(_t('_adm_box_cpt_operation_results'), $sResult) : '',
    'page_code_uploader' => DesignBoxAdmin(_t('_adm_box_cpt_upload'), $oInstallerUi->getUploader($sResultUpload), array (array('title' => _t('_adm_txt_get_new_modules'), 'href' => "http://www.boonex.com/unity/extensions/home/"))),
    'page_code_installed' => DesignBoxAdmin(_t('_adm_box_cpt_installed_modules'), $oInstallerUi->getInstalled()),
    'page_code_not_installed' => DesignBoxAdmin(_t('_adm_box_cpt_not_installed_modules'), $oInstallerUi->getNotInstalled($sResultDeleteModule)),
    'page_code_updates' => DesignBoxAdmin(_t('_adm_box_cpt_available_updates'), $oInstallerUi->getUpdates($sResultDeleteUpdate)),
    'page_code_market_feed' => 'on' == getParam('feeds_enable') ? DesignBoxAdmin(_t('_adm_box_cpt_featured_modules'), '
		<div class="RSSAggrCont" rssid="boonex_unity_market_featured" rssnum="5" member="0">
			<div class="loading_rss">
				<img src="' . getTemplateImage('loading.gif') . '" alt="'._t('_loading ...').'" />
			</div>
		 </div>') : '',
);

PageCodeAdmin();

?>
