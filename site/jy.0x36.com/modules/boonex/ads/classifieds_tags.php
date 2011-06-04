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

require_once( '../../../inc/header.inc.php' );
require_once(BX_DIRECTORY_PATH_INC . 'design.inc.php');
require_once(BX_DIRECTORY_PATH_INC . 'admin.inc.php');

//require_once( BX_DIRECTORY_PATH_MODULES . $aModule['path'] . '/classes/' . $aModule['class_prefix'] . 'Module.php');
bx_import('BxDolModuleDb');
require_once( BX_DIRECTORY_PATH_MODULES . 'boonex/ads/classes/BxAdsModule.php');

// --------------- page variables and login
$_page['name_index'] 	= 151;

$oModuleDb = new BxDolModuleDb();
$aModule = $oModuleDb->getModuleByUri('ads');

$oAds = new BxAdsModule($aModule);
$oAds->sCurrBrowsedFile = $oAds->sHomeUrl . 'classifieds.php';
$_page['header'] = $oAds->GetHeaderString();
$_page['header_text'] = $oAds->GetHeaderString();

$_ni = $_page['name_index'];
$_page_cont[$_ni]['page_main_code'] = PageCompAds($oAds);

$oAds->_oTemplate->addCss(array('ads.css', 'categories.css'));

function PageCompAds($oAds) {
	$sRetHtml = '';

	$sRetHtml .= $oAds->PrintCommandForms();

	if ($_REQUEST) {
		if (false !== bx_get('tag')) {
			$sTag = uri2title(process_db_input(bx_get('tag'), BX_TAGS_STRIP));
			$sRetHtml .= $oAds->PrintAdvertisementsByTag($sTag);
		}
	}

	return $sRetHtml;
}

PageCode($oAds->_oTemplate);

?>