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

require_once('../../../inc/header.inc.php');
require_once(BX_DIRECTORY_PATH_INC . 'design.inc.php');
require_once(BX_DIRECTORY_PATH_INC . 'profiles.inc.php');
require_once(BX_DIRECTORY_PATH_INC . 'utils.inc.php');
require_once(BX_DIRECTORY_PATH_INC . 'admin_design.inc.php');
require_once(BX_DIRECTORY_PATH_INC . 'admin.inc.php');

//require_once( BX_DIRECTORY_PATH_MODULES . $aModule['path'] . '/classes/' . $aModule['class_prefix'] . 'Module.php');
bx_import('BxDolModuleDb');
require_once( BX_DIRECTORY_PATH_MODULES . 'boonex/ads/classes/BxAdsModule.php');

$logged['admin'] = member_auth( 1, true, true );

$sResult = PageCompAds();
$sResult = $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $sResult));

$iNameIndex = 9;
$_page = array(
    'name_index' => $iNameIndex,
    'css_name' => array('common.css', 'forms_adv.css'),
    'js_name' => array('jquery.simple.tree.js'),
    'header' => _t('_bx_ads_Manage_ads'),
    'header_text' => _t('_bx_ads_Manage_ads')
);
$_page_cont[$iNameIndex]['page_main_code'] = $sResult;
PageCodeAdmin();

function PageCompAds() {
	$oModuleDb = new BxDolModuleDb();
	$aModule = $oModuleDb->getModuleByUri('ads');

	$oAds = new BxAdsModule($aModule);
	$oAds->sCurrBrowsedFile = 'post_mod_ads.php';
	$oAds->bAdminMode = true;

	$sCss = $oAds->_oTemplate->addCss(array('ads.css'), true);
	$sResult = $sCss . $oAds->PrintCommandForms();

	if ($_REQUEST) {
		if (false !== bx_get('action')) {
			if ((int)bx_get('action')==3) {
				$sResult .= $oAds->PrintFilterForm();
				$sResult .= $oAds->actionSearch();
				return $sResult;
			} elseif ((int)bx_get('action')==2) {
				$iClassifiedSubID = (int)bx_get('FilterSubCat');
				$sResult .= $oAds->PrintSubRecords($iClassifiedSubID);
				return $sResult;
			} elseif ((int)bx_get('action')==1) {
				$iClassifiedID = (int)bx_get('FilterCat');
				$sResult .= $oAds->PrintAllSubRecords($iClassifiedID);
				return $sResult;
			} elseif (bx_get('action')=='add_sub_category') {
                $sCatID = (int)bx_get('id');
                $iCatID = ($sCatID) ? $sCatID : 0;
				echo $oAds->getAddSubcatForm($iCatID); exit;
			} elseif (bx_get('action')=='category_manager') {
				echo $oAds->getCategoryManager(); exit;
			}
		} elseif (false !== bx_get('bClassifiedID')) {
			$iClassifiedID = (int)bx_get('bClassifiedID');
			if ($iClassifiedID > 0) {
				$sResult .= $oAds->PrintAllSubRecords($iClassifiedID);
				$sResult .= $oAds->PrintBackLink();
				return $sResult;
			}
		} elseif (false !== bx_get('bSubClassifiedID')) {
			$iSubClassifiedID = (int)bx_get('bSubClassifiedID');
			if ($iSubClassifiedID > 0) {
				$sResult .= $oAds->PrintSubRecords($iSubClassifiedID);
				$sResult .= $oAds->PrintBackLink();
				return $sResult;
			}
		} elseif (false !== bx_get('DeleteAdvertisementID')) {
			$id = (int)bx_get('DeleteAdvertisementID');
			if ($id > 0) {
				$sResult .= $oAds->ActionDeleteAdvertisement($id);
			}
		} elseif (false !== bx_get('ActivateAdvertisementID')) {
			$iAdID = (int)bx_get('ActivateAdvertisementID');
			if ($iAdID > 0) {
				$oAds->_oDb->setPostStatus($iAdID, 'active');
			}
		}
		if (false !== bx_get('UpdatedAdvertisementID')) {
			$id = (int)bx_get('UpdatedAdvertisementID');
			if ($id > 0) {
				if (false !== bx_get('DeletedPictureID') && (int)bx_get('DeletedPictureID')>0) {
					//delete a pic
					$sResult .= $oAds->ActionDeletePicture();
					$sResult .= $oAds->PrintEditForm($id);
				} else {
					$sResult .= $oAds->ActionUpdateAdvertisementID($id);
				}
			}
			return;
		} elseif (false !== bx_get('EditAdvertisementID')) {
			if (((int)bx_get('EditAdvertisementID')) > 0) {
				$sResult .= $oAds->PrintEditForm((int)bx_get('EditAdvertisementID'));
				$sResult .= $oAds->PrintBackLink();
				return $sResult;
			}
		} elseif (false !== bx_get('ShowAdvertisementID')) {
			if (bx_get('ShowAdvertisementID') > 0) {
				$sResult .= $oAds->ActionPrintAdvertisement((int)bx_get('ShowAdvertisementID'));
				$sResult .= $oAds->PrintBackLink();
				return $sResult;
			}
		} elseif (false !== bx_get('BuyNow')) {
			$iAdID = (int)bx_get('IDAdv');
			if ($iAdID > 0) {
				$sResult .= $oAds->ActionBuyAdvertisement($iAdID);
				return $sResult;
			}
		} elseif (false !== bx_get('BuySendNow')) {
			$iAdID = (int)bx_get('IDAdv');
			if ($iAdID > 0) {
				$sResult .= $oAds->ActionBuySendMailAdvertisement($iAdID);
				return $sResult;
			}
		} elseif (false !== bx_get('UsersOtherListing')) {
			$iProfileID = (int)bx_get('IDProfile');
			if ($iProfileID > -1) {
				$sResult .= $oAds->PrintMyAds($iProfileID);
				return $sResult;
			}
		}
	}

	$sResult .= $oAds->GenAdminTabbedPage();
	return $sResult;
}

?>