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

require_once( 'inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'admin.inc.php' );

require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolModuleDb.php');
require_once( BX_DIRECTORY_PATH_MODULES . 'boonex/ads/classes/BxAdsModule.php');

check_logged();

function getMemberMenuAdsList($iID) {
	$oMemberMenu = bx_instance('BxDolMemberMenu');

	$oModuleDb = new BxDolModuleDb();
	$aModule = $oModuleDb->getModuleByUri('ads');

	$oAds = new BxAdsModule($aModule);

    $sAdsMainLink = ($oAds->bUseFriendlyLinks) ? BX_DOL_URL_ROOT . 'ads/my_page/' : "{$oAds->sCurrBrowsedFile}?action=my_page";
	$iMyAdsCnt = $oAds->_oDb->getMemberAdsCnt($iID);

	// language keys;
	$aLanguageKeys = array(
		'ads' => _t('_bx_ads_Ads'),
	);

	// fill all necessary data;
	$aLinkInfo = array(
		'item_img_src'  => $oAds -> _oTemplate -> getIconUrl ('ads.png'),
		'item_img_alt'  => $aLanguageKeys['ads'],
		'item_link'     => $sAdsMainLink,
		'item_onclick'  => null,
		'item_title'    => $aLanguageKeys['ads'],
		'extra_info'    => $iMyAdsCnt,
	);

	return $oMemberMenu -> getGetExtraMenuLink($aLinkInfo);
}

?>