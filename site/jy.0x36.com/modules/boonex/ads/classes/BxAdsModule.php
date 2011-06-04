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

require_once(BX_DIRECTORY_PATH_INC . 'header.inc.php');
require_once(BX_DIRECTORY_PATH_INC . 'design.inc.php');
require_once(BX_DIRECTORY_PATH_INC . 'profiles.inc.php');
require_once(BX_DIRECTORY_PATH_INC . 'utils.inc.php');

bx_import('BxDolPageView');
bx_import('BxTemplCmtsView');
bx_import('BxDolPaginate');
bx_import('BxDolModule');
require_once('BxAdsPrivacy.php');

class BxDolAdsPageView extends BxDolPageView {
	var $oAds;

	function BxDolAdsPageView(&$oAd) {
		$this->oAds = &$oAd;
		parent::BxDolPageView('ads');
	}

	function getBlockCode_AdPhotos() {
		return $this->oAds->sTAPhotosContent;
	}

	function getBlockCode_ActionList() {
		return $this->oAds->sTAActionsContent;
	}

	function getBlockCode_ViewComments() {
		return $this->oAds->sTACommentsContent;
	}

	function getBlockCode_AdInfo() {
		return $this->oAds->sTAInfoContent;
	}

	function getBlockCode_UserOtherAds() {
		return $this->oAds->sTAOtherListingContent;
	}

	function getBlockCode_Rate() {
		return $this->oAds->sTARateContent;
	}

	function getBlockCode_AdDescription() {
		return $this->oAds->sTADescription;
	}

	function getBlockCode_AdCustomInfo() {
		return $this->oAds->sTAOtherInfo;
	}
}

/**
 * Ads module by BoonEx
 *
 * This module allow user to add ads, and even 'sell' its. 
 *
 * Example of using this module to get any Ad page:
 * 
 * bx_import('BxDolModuleDb');
 * require_once( BX_DIRECTORY_PATH_MODULES . 'boonex/ads/classes/BxAdsModule.php');
 * $oModuleDb = new BxDolModuleDb();
 * $aModule = $oModuleDb->getModuleByUri('ads');
 * $oAds = new BxAdsModule($aModule);
 * echo $oAds->ActionPrintAdvertisement($iID);
 * 
 *
 *
 * Profile's Wall:
 * 'create' and 'edit' events are displayed on profile's wall
 *
 *
 *
 * Spy:
 * 'create' event are displayed on profile's spy
 *
 *
 *
 * Memberships/ACL:
 * View ads - BX_ADS_VIEW
 * Browse ads - BX_ADS_BROWSE
 * Use search and view search results - BX_ADS_SEARCH
 * Add ads - BX_ADS_ADD
 * Edit any ad (as admin) - BX_ADS_EDIT_ANY_AD
 * Delete any ad (as admin) - BX_ADS_DELETE_ANY_AD
 * Approve any ad (as admin) - BX_ADS_APPROVING
 *
 * 
 *
 * Service methods:
 *
 * Ads block for index page (as PHP function)
 * @see BxAdsModule::serviceAdsIndexPage
 * BxDolService::call('bx_ads', 'ads_index_page', array());
 *
 * Ads block for profile page (as PHP function)
 * @see BxAdsModule::serviceAdsProfilePage
 * BxDolService::call('bx_ads', 'ads_profile_page', array($_iProfileID));
 *
 * Generation of member RSS feeds
 * @see BxAdsModule::serviceAdsRss
 * BxDolService::call('bx_ads', 'ads_rss', array());
 *
 * Get common css
 * @see BxAdsModule::serviceGetCommonCss
 * BxDolService::call('bx_ads', 'get_common_css', array());
 *
 * Get Spy data
 * @see BxAdsModule::serviceGetSpyData
 * BxDolService::call('bx_ads', 'get_spy_data', array());
 *
 * Get Spy ad units
 * @see BxAdsModule::serviceGetSpyPost
 * BxDolService::call('bx_ads', 'get_spy_post', array($sAction, $iObjectId, $iSenderId));
 *
 *
 *
 * Alerts:
 * Alerts type/unit - 'ads'
 * The following alerts are rised
 *
 *  view - view ad
 *      $iAdvertisementID - viewing ad id
 *      $this->_iVisitorID - visitor id
 *
 *  create - creating of new ad
 *      $iLastId - ad id (for new ad - 0)
 *      $this->_iVisitorID - ad owner id
 *
 *  edit - editing of existed ad
 *      $iLastId - ad id
 *      $this->_iVisitorID - ad owner id
 *
 *  delete - deleting of existed ad
 *      $iDeleteAdvertisementID - ad id
 *      $iDeleteAdvertisementID - ad owner id
 *
 *  buy - buy ad
 *      $iAdvertisementID - viewing ad id
 *      $this->_iVisitorID - visitor id
 *
 */
class BxAdsModule extends BxDolModule {

	//max sizes of pictures for resizing during upload
	var $iIconSize = 32;
	var $iThumbSize = 140;
	var $iBigThumbSize = 340;
	var $iImgSize = 600;

	//upload URL to dir
	var $sUploadDir = '';

	//max upload file size
	var $iMaxUplFileSize = 1048576; //1mb

	//path to image with Point
	var $sSpacerPath;

	//path to image pic_not_avail.gif
	var $sPicNotAvail = '';

	//admin mode, can All actions
	var $bAdminMode;

	//current file, for actions of forms and other
	var $sCurrBrowsedFile = '';

	var $iPerPageElements = 10;

	//use permalink
	var $bUseFriendlyLinks;

	//for page blocks
	var $sTAPhotosContent = '';
	var $sTAActionsContent = '';
	var $sTACommentsContent = '';
	var $sTAInfoContent = '';
	var $sTARateContent = '';
	var $sTAOtherListingContent = '';
	var $sTADescription = '';
	var $sTAOtherInfo = '';

	var $oCmtsView;
	var $oPrivacy;

	var $sHomeUrl;
	var $sHomePath;

	var $_iVisitorID;

	// Constructor
	function BxAdsModule($aModule) {
		global $site;

		parent::BxDolModule($aModule);

		$this->sHomeUrl = $this->_oConfig->getHomeUrl();
		$this->sHomePath = $this->_oConfig->getHomePath();

		$this->sUploadDir = 'media/images/classifieds/';

		$this->bUseFriendlyLinks = getParam('permalinks_module_ads') == 'on' ? true : false;
		$this->_oConfig->bUseFriendlyLinks = $this->bUseFriendlyLinks;

		$this->sPicNotAvail = $GLOBALS['oSysTemplate']->getIconUrl('no-photo-110.png');
		$this->sSpacerPath = getTemplateIcon('spacer.gif');

		$this->_iVisitorID = (isMember()) ? (int)$_COOKIE['memberID'] : 0;
		$this->bAdminMode = ($this->isAdmin()==true) ? true: false;
		$this->_oConfig->bAdminMode = ($this->isAdmin()==true) ? true: false;

		$this->sCurrBrowsedFile = $this->sHomeUrl . 'classifieds.php';
		$this->_oConfig->sCurrBrowsedFile = $this->sCurrBrowsedFile;

		$this->oPrivacy = new BxAdsPrivacy($this);

        $this->aPageTmpl['name_index'] = 71;
	}

	/**
	 * Generate array of filtered Advertisements
	 *
	 * @return HTML presentation of data
	 */
    function actionSearch() {
		global $aPreValues;

		$this->isAllowedSearch(true); // perform action

		$sCategory = (int)bx_get('FilterCat');
		$sSubCategory = (int)bx_get('FilterSubCat');
		$sCountry = process_db_input(bx_get('FilterCountry'), BX_TAGS_STRIP);
		$sCountry = (isset($aPreValues['Country'][$sCountry]) == true) ? $sCountry : '';
		$sKeywords = process_db_input(bx_get('FilterKeywords'), BX_TAGS_STRIP);

		$sSubCats = '';
		if ($sSubCategory<=0) {
			if ($sCategory>0) {
				$aSubCats = array();
				$vSubCats = $this->_oDb->getAllSubCatsInfo($sCategory);
				while( $aSubCat = mysql_fetch_assoc($vSubCats) ) {
					$aSubCats[] = (int)$aSubCat['ID'];
				}
				sort($aSubCats);
				if (count($aSubCats)>0) {
					$sSubCats = "`{$this->_oConfig->sSQLSubcatTable}`.`ID` IN (" . implode(",", $aSubCats) . ")";
				} else {
					return $oFunctions->MsgBox(_t('_SubCategory is required'));
				}
			} else {
				//return MsgBox(_t('_SubCategory is required'));
			}
		}

		$sCustomFieldCaption1 = process_db_input(bx_get('CustomFieldCaption1'), BX_TAGS_STRIP);
		$sCustomFieldCaption2 = process_db_input(bx_get('CustomFieldCaption2'), BX_TAGS_STRIP);

		require_once( $this->_oConfig->getClassPath() . 'BxAdsSearchUnit.php');
		$oTmpAdsSearch = new BxAdsSearchUnit();
		$oTmpAdsSearch->aCurrent['paginate']['perPage'] = 10;
		$oTmpAdsSearch->aCurrent['sorting'] = 'last';

		if ($sCategory > 0) {
			$oTmpAdsSearch->aCurrent['restriction']['categoryID']['value'] = $sCategory;
		}

		if (count($aSubCats)>0) {
			$oTmpAdsSearch->aCurrent['third_restr'] = "`{$this->_oConfig->sSQLSubcatTable}`.`ID` IN (" . implode(",", $aSubCats) . ")";
		} else {
			if ($sSubCategory > 0) {
				$oTmpAdsSearch->aCurrent['restriction']['subcategoryID']['value'] = $sSubCategory;
			}
		}

		if ($sCountry != '') {
			$oTmpAdsSearch->aCurrent['restriction']['country']['value'] = $sCountry;
		}

		if ($sKeywords != '') {
			$oTmpAdsSearch->aCurrent['restriction']['message_filter']['value'] = $sKeywords;
		}

		$oTmpAdsSearch->aCurrent['restriction']['categoryID']['value'] = $iSafeCatID;
		$sFilteredAds = $oTmpAdsSearch->displayResultBlock();
        if ($oTmpAdsSearch->aCurrent['paginate']['totalNum'] == 0) {
            $sFilteredAds = MsgBox(_t('_Empty'));
        } else {
    		// Prepare link to pagination
    		$sRequest = bx_html_attribute($_SERVER['PHP_SELF']) . '?';
    		foreach($_GET as $sKey => $sValue ) {
    			$sRequest .= '&' . $sKey . '=' . $sValue;
    		}
    		$sRequest .= '&page={page}&per_page={per_page}';
    		// End of prepare link to pagination
    		$oTmpAdsSearch->aCurrent['paginate']['page_url'] = $sRequest;
    		$sPagination = $oTmpAdsSearch->showPagination();

    		//$sFilteredAds = $GLOBALS['oFunctions']->centerContent($sFilteredAds, '.ads_Unit'); 
        }

		$sFilterForm = $this->PrintFilterForm();
		$sCode = DesignBoxContent(_t('_SEARCH_RESULT_H'), $sFilterForm . $sFilteredAds . $sPagination, 1);

		$sJS = <<<EOF
<script language="JavaScript" type="text/javascript">
	<!--
		var sAdsSiteUrl = "{$this->sHomeUrl}";
	-->
</script>
EOF;

        //--------------------------- output -------------------------------------------
        $this->aPageTmpl['header'] = _t('_bx_ads_Filter');
        $this->aPageTmpl['css_name'] = array('ads.css');
        $this->aPageTmpl['js_name'] = array('main.js');
        $this->_oTemplate->pageCode($this->aPageTmpl, array('page_main_code' => $sJS . $sCode));
    }

	/**
	 * Generate common forms and includes js
	 *
	 * @return HTML presentation of data
	 */
	function PrintCommandForms() {
        $sAdsLink = ($this->bUseFriendlyLinks) ? 'ads/' : $this->sCurrBrowsedFile;

        $this -> _oTemplate -> addJs('main.js');

		return <<<EOF
<script language="JavaScript" type="text/javascript">
	<!--
		var sAdsSiteUrl = "{$this->sHomeUrl}";
	-->
</script>
<form action="{$sAdsLink}" method="post" name="command_activate_advertisement">
	<input type="hidden" name="ActivateAdvertisementID" id="ActivateAdvertisementID" value="" />
</form>
<form action="{$sAdsLink}" method="post" name="command_delete_advertisement">
	<input type="hidden" name="DeleteAdvertisementID" id="DeleteAdvertisementID" value="" />
</form>
EOF;
	}

	/**
	 * Return string for Header, depends at POST params
	 *
	 * @return Textpresentation of data
	 */
	function GetHeaderString() {
		switch (bx_get('action')) {
			case 'show_featured':
				return _t('_bx_ads_Featured');
			case 'show_top_rated':
				return _t('_bx_ads_Top_Rated');
			case 'show_all_ads':
				return _t('_bx_ads_All_ads');
			case 'show_popular':
				return _t('_bx_ads_Popular');
			case 'tags':
				return _t('_Tags');
			case 'show_categories':
				return _t('_bx_ads_Categories');
			case 'show_calendar':
				return _t('_bx_ads_Calendar');
			case 'show_calendar_ads':
			    $sDate = bx_get('date');
				$aDate = explode('/', $sDate);
		
				$iValue1 = (int)$aDate[0];
				$iValue2 = (int)$aDate[1];
				$iValue3 = (int)$aDate[2];
                    
				return _t('_bx_ads_caption_browse_by_day') 
                        . getLocaleDate( strtotime("{$iValue1}-{$iValue2}-{$iValue3}")
                        , BX_DOL_LOCALE_DATE_SHORT);
			case 'show_featured':
				return _t('_bx_ads_Featured');
            case 'my_page':
                switch (bx_get('mode')) {
        			case 'add':
        				return _t('_bx_ads_Add');
                    case 'manage':
        				return _t('_bx_ads_Manage_ads');
                    case 'pending':
        				return _t('_bx_ads_pending_approval');
                    case 'disapproved':
        				return _t('_bx_ads_Disapproved');
                    default:
                        return _t('_bx_ads_My_Ads');
                }
                break;
		}

        if (false !== bx_get('action') && bx_get('action')=="3") {
			return _t('_bx_ads_Filtered_ads');;
		} elseif (false !== bx_get('ShowAdvertisementID')) {
			return $this->_oDb->getAdSubjectByID((int)bx_get('ShowAdvertisementID'));
		} elseif (false !== bx_get('entryUri')) {
			return $this->_oDb->getAdSubjectByUri(process_db_input(bx_get('entryUri'), BX_TAGS_STRIP));
		} elseif (false !== bx_get('UsersOtherListing') && (int)bx_get('IDProfile')>0) {
			return _t('_bx_ads_Users_other_listing');
		} elseif (false !== bx_get('bClassifiedID') || false !== bx_get('catUri')) {
			if (false !== bx_get('bClassifiedID') && (int)bx_get('bClassifiedID')>0) {
				return $this->_oDb->getCategoryNameByID((int)bx_get('bClassifiedID'));
			} elseif (false !== bx_get('catUri') && bx_get('catUri')!='') {
				return $this->_oDb->getCategoryNameByUri(process_db_input(bx_get('catUri'), BX_TAGS_STRIP));
			}
		} elseif (false !== bx_get('bSubClassifiedID') || false !== bx_get('scatUri')) {
			$aSubcatRes = null;
			if (false !== bx_get('bSubClassifiedID') && (int)bx_get('bSubClassifiedID')>0) {
				$aSubcatRes = $this->_oDb->getCatSubCatNameBySubCatID((int)bx_get('bSubClassifiedID'));
			} elseif (false !== bx_get('scatUri') && bx_get('scatUri')!='') {
				$aSubcatRes = $this->_oDb->getCatSubCatNameBySubCatUri(process_db_input(bx_get('scatUri'), BX_TAGS_STRIP));
			}

			if ($aSubcatRes) {
				return $aSubcatRes['Name'] . ' / ' . $aSubcatRes['NameSub'];
			}
		} else {
			return _t('_bx_ads_Ads_Home');
		}
	}

    // ================================== permissions
    function isAllowedComments (&$aAdPost) {
        if (($aAdPost['IDProfile'] == $this->_iVisitorID && isMember()) || $this->isAdmin ()) return true;
        return $this->oPrivacy->check('comment', $aAdPost['ID'], $this->_iVisitorID);
    }

	function isAllowedView($iOwnerID, $isPerformAction = false) {
		if ($this->isAdmin() || $iOwnerID == $this->_iVisitorID) return true;
		$this->_defineActions();
		$aCheck = checkAction($this->_iVisitorID, BX_ADS_VIEW, $isPerformAction);
		return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
	}

	function isAllowedBrowse($isPerformAction = false) {
		if ($this->isAdmin()) return true;
		$this->_defineActions();
		$aCheck = checkAction($this->_iVisitorID, BX_ADS_BROWSE, $isPerformAction);
		return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
	}

	function isAllowedSearch($isPerformAction = false) {
		if ($this->isAdmin()) return true;
		$this->_defineActions();
		$aCheck = checkAction($this->_iVisitorID, BX_ADS_SEARCH, $isPerformAction);
		return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
	}

	function isAllowedAdd($isPerformAction = false) {
		if ($this->isAdmin()) return true;
		if (isMember() == false) return false;
		$this->_defineActions();
		$aCheck = checkAction($this->_iVisitorID, BX_ADS_ADD, $isPerformAction);
		return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
	}

	function isAllowedEdit($iOwnerID, $isPerformAction = false) {
		if ($this->isAdmin() || (isMember() && $iOwnerID == $this->_iVisitorID)) return true;
		$this->_defineActions();
		$aCheck = checkAction($this->_iVisitorID, BX_ADS_EDIT_ANY_AD, $isPerformAction);
		return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
	}

	function isAllowedDelete($iOwnerID, $isPerformAction = false) {
		if ($this->isAdmin() || (isMember() && $iOwnerID == $this->_iVisitorID)) return true;
		$this->_defineActions();
		$aCheck = checkAction($this->_iVisitorID, BX_ADS_DELETE_ANY_AD, $isPerformAction);
		return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
	}

	function isAllowedApprove($isPerformAction = false) {
		if ($this->isAdmin()) return true;
		if (isMember() == false) return false;
		$this->_defineActions();
		$aCheck = checkAction($this->_iVisitorID, BX_ADS_APPROVING, $isPerformAction);
		return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
	}

	function isAdmin() {
		return isAdmin($this->_iVisitorID) || isModerator($this->_iVisitorID);
	}

	function _defineActions() {
		defineMembershipActions(array ('ads view', 'ads browse', 'ads search', 'ads add', 'ads edit any ad', 'ads delete any ad', 'ads approving'));
	}

	function CheckLogged() {
        $iProfileId = (isset($_COOKIE['memberID']) && ($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin'])) ? (int)$_COOKIE['memberID'] : 0;
		if (! $iProfileId) {
			member_auth(0);
		}
	}

	function getAdministrationSettings() {
		$iId = $this->_oDb->getSettingsCategory();
		if(empty($iId))
			return MsgBox(_t('_sys_request_page_not_found_cpt'));

		bx_import('BxDolAdminSettings');

		$mixedResult = '';
		if(isset($_POST['save']) && isset($_POST['cat'])) { 
			$oSettings = new BxDolAdminSettings($iId);
			$mixedResult = $oSettings->saveChanges($_POST);
		}

		$oSettings = new BxDolAdminSettings($iId);
		$sResult = $oSettings->getForm();

		if($mixedResult !== true && !empty($mixedResult))
			$sResult = $mixedResult . $sResult;

		return $sResult;
	}

	function GenAdminTabbedPage() {
		$sTitleC = _t('_bx_ads_Manage_ads');
		$sPendingC = _t('_bx_ads_pending_approval');
		$sManageCatsC = _t('_bx_ads_Manage_categories_form');
		$sSettingsC = _t('_Settings');

		$sPendingTab = $this->GenAdsAdminIndex();

        $sCatID = (int)bx_get('id');
        $iCatID = ($sCatID) ? $sCatID : 0;

		$sManageCatsTab = $this->getManageClassifiedsForm($iCatID);
		$sSettingsTab = $this->getAdministrationSettings();

        $iPage1Active = 1;
        $iPage2Active = 0;
        $sPage1Visible = 'visible';
        $sPage2Visible = 'hidden';
        if(isset($_POST['save']) && isset($_POST['cat'])) { 
            $iPage1Active = 0;
            $iPage2Active = 1;
            $sPage1Visible = 'hidden';
            $sPage2Visible = 'visible';
        }

		$sBoxContent = <<<EOF
<script type="text/javascript">
	<!--
	function switchAdmPage(iPageID, oLink) {
		//make all tabs - inactive
		//make selected tab - active
		//hide all pages
		//show selected page

        $(oLink).parent('.notActive').hide().siblings('.notActive:hidden').show().siblings('.active').hide().siblings('#' + $(oLink).attr('id') + '-act').show();

		$("#adm_pages").children().removeClass().toggleClass("hidden");
		$("#adm_pages #page" + iPageID).removeClass().toggleClass("visible");

		return false;
	}
	-->
</script>

<div class="boxContent" id="adm_pages">
	<div id="page1" class="{$sPage1Visible}">{$sPendingTab}<hr /><div class="clear_both"></div>{$sManageCatsTab}</div>
	<div id="page2" class="{$sPage2Visible}">{$sSettingsTab}</div>
</div>
<div style="clear: both;"></div>
EOF;

        $sActions = array(
            'ads_t1' => array('href' => 'javascript: void(0);', 'title' => $sPendingC, 'onclick' => 'switchAdmPage(1, this); return false;', 'active' => $iPage1Active),
            'ads_t2' => array('href' => 'javascript: void(0);', 'title' => $sSettingsC, 'onclick' => 'switchAdmPage(2, this); return false;', 'active' => $iPage2Active)
        );
		return DesignBoxAdmin($sTitleC, $sBoxContent, $sActions);
	}

	function GenMyPageAdmin($sForceMode = '') {
		$this->CheckLogged();

		$sAdministrationC = _t('_bx_ads_Administration');
		$sMyAdsC = _t('_bx_ads_My_Ads');
		$sPendApprC = _t('_bx_ads_pending_approval');
		$sDisApprC = _t('_bx_ads_Disapproved');
		$sAddC = _t('_bx_ads_Add');

		if (bx_get('action_delete') && is_array(bx_get('ads'))) {
			foreach (bx_get('ads') as $iAdID) {
				$this->ActionDeleteAdvertisement((int)$iAdID);
			}
		}

		require_once($this->_oConfig->getClassPath() . 'BxAdsSearchUnit.php');
		$oTmpAdsSearch = new BxAdsSearchUnit();
		$oTmpAdsSearch->bShowCheckboxes = false;
		$oTmpAdsSearch->aCurrent['paginate']['perPage'] = 10;
		$oTmpAdsSearch->aCurrent['restriction']['owner']['value'] = $this->_iVisitorID;

        $GLOBALS['oTopMenu']->setCurrentProfileID($this->_iVisitorID);

		$sMyAds = $oTmpAdsSearch->displayResultBlock();
        if ($oTmpAdsSearch->aCurrent['paginate']['totalNum'] == 0) {
            $sMyAds = MsgBox(_t('_Empty'));
        } else {
            //$sMyAds = $GLOBALS['oFunctions']->centerContent($sMyAds, '.ads_Unit'); 
        }
		$sMyAdsBox = DesignBoxContent ($sMyAdsC, $sMyAds, 1);

        $sAdsMainLink = ($this->bUseFriendlyLinks) ? 'ads/my_page/' : "{$this->sCurrBrowsedFile}?action=my_page";
        $sAdsAddLink = ($this->bUseFriendlyLinks) ? 'ads/my_page/add/' : "{$this->sCurrBrowsedFile}?action=my_page&mode=add";
        $sAdsManageLink = ($this->bUseFriendlyLinks) ? 'ads/my_page/manage/' : "{$this->sCurrBrowsedFile}?action=my_page&mode=manage";
        $sAdsPendingLink = ($this->bUseFriendlyLinks) ? 'ads/my_page/pending/' : "{$this->sCurrBrowsedFile}?action=my_page&mode=pending";
        $sAdsDisapprovedLink = ($this->bUseFriendlyLinks) ? 'ads/my_page/disapproved/' : "{$this->sCurrBrowsedFile}?action=my_page&mode=disapproved";

		$sAdmContent = '';
		$sCaption = '';
		$sMainTabClass = $sAddTabClass = $sManageTabClass = $sPendingTabClass = $sDisapprovedTabClass = 0;
		$sMode = ($sForceMode != '') ? $sForceMode : bx_get('mode');
		switch($sMode) {
			case 'add':
				$sAddTabClass = 1;
				$sNewPostForm = $this->AddNewPostForm((int)bx_get('EditPostID'), false);

				$sAdmContent = $sNewPostForm;
				$sCaption = $sAddC;
				break;
			case 'manage':
				$sManageTabClass = 1;

				$oTmpAdsSearch->bShowCheckboxes = true;
				$oTmpAdsSearch->aCurrent['restriction']['activeStatus']['value'] = 'active';
				$oTmpAdsSearch->aCurrent['second_restr'] = 'manage';
				$sAdsActiveUnitsVal = $oTmpAdsSearch->displayResultBlock();
                if ($oTmpAdsSearch->aCurrent['paginate']['totalNum'] == 0) {
                    $sAdsActiveUnitsVal = MsgBox(_t('_Empty'));
                } else {
            		//$sAdsActiveUnitsVal = $GLOBALS['oFunctions']->centerContent($sAdsActiveUnitsVal, '.ads_Unit'); 
                }

				$aButtons = array('action_delete' => '_Delete');
				$sActionsPanel = $oTmpAdsSearch->showAdminActionsPanel ('ads_box', $aButtons, 'ads');
				$sAdsActiveUnitsForm = <<<EOF
<form id="bx_ads_user_form" method="post">
	<div id="ads_box">
		{$sAdsActiveUnitsVal}
		<div class="clear_both"></div>
	</div>
	{$sActionsPanel}
</form>
EOF;
				$sCaption = $sMyAdsC;
				$sAdmContent = $sAdsActiveUnitsForm;

				break;
			case 'pending':
				$oTmpAdsSearch->aCurrent['restriction']['activeStatus']['value'] = 'new';
				$sAdsNewUnitsVal = $oTmpAdsSearch->displayResultBlock();
                if ($oTmpAdsSearch->aCurrent['paginate']['totalNum'] == 0) {
                    $sAdsNewUnitsVal = MsgBox(_t('_Empty'));
                } else {
            		//$sAdsNewUnitsVal = $GLOBALS['oFunctions']->centerContent($sAdsNewUnitsVal, '.ads_Unit'); 
                }

				$sAdmContent = $sAdsNewUnitsVal;
				$sCaption = $sPendApprC;
				$sPendingTabClass = 1;
				break;
			case 'disapproved':
				$sDisapprovedTabClass = 1;

				$oTmpAdsSearch->aCurrent['restriction']['activeStatus']['value'] = 'inactive';
				$sAdsDisUnitsVal = $oTmpAdsSearch->displayResultBlock();
                if ($oTmpAdsSearch->aCurrent['paginate']['totalNum'] == 0) {
                    $sAdsDisUnitsVal = MsgBox(_t('_Empty'));
                } else {
            		//$sAdsDisUnitsVal = $GLOBALS['oFunctions']->centerContent($sAdsDisUnitsVal, '.ads_Unit'); 
                }
				$sAdmContent = $sAdsDisUnitsVal;
				$sCaption = $sDisApprC;
				break;
			case 'main':
			default:
				$sMainTabClass = 1;

				$iMyAdsCnt = $this->_oDb->getMemberAdsCnt($this->_iVisitorID);
				$sAdministrationDescC = _t('_bx_ads_admin_box_desc', $iMyAdsCnt, $sAdsManageLink, $sAdsAddLink);
				$sAdmContent = $sAdministrationDescC;
				$sCaption = $sAdministrationC;
				break;
		}

        $sAdmPost = BxDolPageView::getBlockCaptionMenu(mktime(), array(
            'ads_main' => array('href' => $sAdsMainLink, 'title' => _t('_Main'), 'active' => $sMainTabClass),
            'ads_add' => array('href' => $sAdsAddLink, 'title' => _t('_bx_ads_Add'), 'active' => $sAddTabClass),
            'ads_manage' => array('href' => $sAdsManageLink, 'title' => _t('_bx_ads_Manage_ads'), 'active' => $sManageTabClass),
            'ads_pending' => array('href' => $sAdsPendingLink, 'title' => _t('_bx_ads_pending_approval'), 'active' => $sPendingTabClass),
            'ads_disapproved' => array('href' => $sAdsDisapprovedLink, 'title' => _t('_bx_ads_Disapproved'), 'active' => $sDisapprovedTabClass)
        ));

		$sAdministrationUnitsSect = DesignBoxContent ($sAdministrationC, $sAdmContent, 1, $sAdmPost);
		return $sAdministrationUnitsSect . $sMyAdsBox;
	}

	/**
	 * Generate Form for NewPost/EditPost for Ads
	 *
	 * @param $iPostID - Post ID
	 * @return HTML presentation of data
	 */
	function AddNewPostForm($iPostID=0, $bBox = true) {
        bx_import ('BxDolProfileFields');

		$this->CheckLogged();

		if ($iPostID==0) {
	        if (!$this->isAllowedAdd()) {
	            return $this->_oTemplate->displayAccessDenied ();
	        }
		} else {
			$aAdUnitInfo = $this->_oDb->getAdInfo($iPostID);

			$iOwnerID = (int)$aAdUnitInfo['OwnerID'];
	        if (!$this->isAllowedEdit($iOwnerID)) {
	            return $this->_oTemplate->displayAccessDenied();
	        }
		}

		$sNewAdC = _t('_Add Post');
		$sDaysC = _t('_days');
		$sMaxedString = _t('_bx_ads_Warn_max_live_days', 30);

        $sAdsAddLink = ($this->bUseFriendlyLinks) ? 'ads/my_page/add/' : "{$this->sCurrBrowsedFile}?action=my_page&mode=add";

		// Life time values
		$iMaxLt = (int)getParam('bx_ads_max_live_days');
        $aLifeTimeValues = array();
	    for ($i=5; $i<=$iMaxLt; $i+=5) {
            $aLifeTimeValues[] = array('key' => $i, 'value' => $i);
	    }

		// Categories and custom values
		$iCategoryID = (int)bx_get('Classified');
		$iSubCategoryID = (int)bx_get('IDClassifiedsSubs');
		$sCustomValues = $sScriptHandle = '';
		if (false !== bx_get('IDClassifiedsSubs')) {
			$sScriptHandle = <<<EOF
<script type="text/javascript">
	addEvent( window, 'load', function(){ $('#Classified').val('{$iCategoryID}'); } );
</script>
EOF;
		}

        $sCity = $sCountry = '';

		$aAllowView = $this->oPrivacy->getGroupChooser($this->_iVisitorID,
			'bx_ads', 'view', array(), _t('_bx_ads_privacy_view'));
		$aAllowRate = $this->oPrivacy->getGroupChooser($this->_iVisitorID,
			'bx_ads', 'rate', array(), _t('_bx_ads_privacy_rate'));
		$aAllowComment = $this->oPrivacy->getGroupChooser($this->_iVisitorID,
			'bx_ads', 'comment', array(), _t('_bx_ads_privacy_comment'));

		if ($iPostID > 0) {
			$this->ActionDeletePicture();

            $sAdsAddLink = ($this->bUseFriendlyLinks) ? 'ads/my_page/edit/' . $iPostID : "{$this->sCurrBrowsedFile}?action=my_page&mode=add&EditPostID={$iPostID}";
			$aAdUnitInfo = (is_array($aAdUnitInfo) && count($aAdUnitInfo)>0) ? $aAdUnitInfo : $this->_oDb->getAdInfo($iPostID);

			$iCategoryID = (int)$aAdUnitInfo['CatID'];
			$iSubCategoryID = (int)$aAdUnitInfo['SubID'];

			$sCity = $aAdUnitInfo['City'];
			$sCountry = $aAdUnitInfo['Country'];

			$sScriptHandle = <<<EOF
<script type="text/javascript">
	addEvent( window, 'load', function(){ $('#Classified').val('{$iCategoryID}'); } );
	addEvent( window, 'load', function(){ $('#SubClassified').val('{$iSubCategoryID}'); } );
</script>
EOF;

			$sSubsRows = '';
			$vSubs = $this->_oDb->getSubsNameIDCountAdsByAdID($iCategoryID);
			while( $aSub = mysql_fetch_assoc($vSubs) ) {
				$iSubID = (int)$aSub['ID'];
				$iSubName = $aSub['Name'];
				$sSubsRows .= '<option value="'.$iSubID.'">'.$iSubName.'</option>';
			}

			$sCustomValues = <<<EOF
{$aAdUnitInfo['CustomFieldName1']} {$aAdUnitInfo['Unit1']} <input type="text" name="CustomFieldValue1" value="{$aAdUnitInfo['CustomFieldValue1']}" size="20" maxlength="20" />
{$aAdUnitInfo['CustomFieldName2']} {$aAdUnitInfo['Unit2']} <input type="text" name="CustomFieldValue2" value="{$aAdUnitInfo['CustomFieldValue2']}" size="20" maxlength="20" />
EOF;

            $sNewAdC = _t('_Save Changes');
            $sMaxedString = _t('_bx_ads_Warn_max_live_days', (int)$aAdUnitInfo['LifeTime']);
		}

		//Main categories
		$vSqlRes = $this->_oDb->getAllCatsInfo();
		if (db_affected_rows()==-1)
			return _t('_Error Occured');
		$sCatOptions = '';
		while( $aSqlResStr = mysql_fetch_assoc($vSqlRes) ) {
			$sCatOptions .= "<option value=\"{$aSqlResStr['ID']}\">{$aSqlResStr['Name']}</option>\n";
		}

        $iSubCatID = (int)bx_get('IDClassifiedsSubs');
        $sExclamationIcon = $GLOBALS['oSysTemplate']->getIconUrl('exclamation.png');

        $aVars = array (
            'sCatOptions' => $sCatOptions,
            'sSubsRows' => $sSubsRows,
            'sScriptHandle' => $sScriptHandle,
            'sCustomValues' => $sCustomValues,
            'bx_if:cat_warning' => array(
                'condition' => (false !== bx_get('add_button') && ! $iSubCatID),
                'content' => array( )
            ),
        );
        $sCustomCategories = $this->_oTemplate->parseHtmlByName('ads_add_categ_form.html', $aVars);

        $oProfileFields = new BxDolProfileFields(0);
        $aCountries = $oProfileFields->convertValues4Input('#!Country');
        asort($aCountries);

		//adding form
		$aForm = array(
			'form_attrs' => array(
				'name' => 'CreateAdsForm',
				'action' => $sAdsAddLink,
				'method' => 'post',
				'enctype' => 'multipart/form-data',
			),
			'params' => array (
				'db' => array(
					'table' => $this->_oConfig->sSQLPostsTable,
					'key' => 'ID',
					'submit_name' => 'add_button',
				),
			),
			'inputs' => array(
                'CustomCategories' => array(
					'caption' => _t('_Category'),
					'type' => 'custom',
					'name' => 'CustomCategories',
					'content' => $sCustomCategories
				),
				'Subject' => array(
					'type' => 'text',
					'name' => 'Subject',
					'caption' => _t('_Caption'),
					'required' => true,
					'checker' => array (
						'func' => 'length',
						'params' => array(3,255),
						'error' => _t('_bx_ads_Caption_error'),
					),
					'db' => array (
						'pass' => 'Xss', 
					),
				),
				'LifeTime' => array(
					'type' => 'select',
					'name' => 'LifeTime',
					'caption' => _t('_bx_ads_Life_Time') . " ({$sDaysC})",
					'info' => $sMaxedString,
					'value' => 30,
					'values' => $aLifeTimeValues,
					'required' => true,
					'checker' => array (
						'func' => 'length',
						'params' => array(1,2),
						'error' => _t('_Error Occured'),
					),
					'db' => array (
						'pass' => 'Int', 
					),
				),
				'Tags' => array(
					'type' => 'text',
					'name' => 'Tags',
					'caption' => _t('_Tags'),
					'info' => _t('_sys_tags_note'),
					'required' => false,
					'db' => array (
						'pass' => 'Xss', 
					),
				),
				'Message' => array(
					'type' => 'textarea',
					'name' => 'Message',
					'caption' => _t('_bx_ads_post_text'),
					'required' => true,
					'html' => 2,
					'checker' => array (
						'func' => 'length',
						'params' => array(3,65535),
						'error' => _t('_bx_ads_Text_error'),
					),
					'db' => array (
						'pass' => 'XssHtml', 
					),
				),
                'Country' => array(
                    'type' => 'select',
                    'name' => 'Country',
                    'caption' => _t('_bx_ads_caption_country'),
                    'values' => $aCountries,
                    'value' => $sCountry,
                    'required' => true,
                    'checker' => array (
                        'func' => 'preg',
                        'params' => array('/^[a-zA-Z]{2}$/'),
                        'error' => _t ('_bx_ads_err_country'),
                    ),                                        
                    'db' => array (
                        'pass' => 'Preg', 
                        'params' => array('/([a-zA-Z]{2})/'),
                    ),                    
                ),
                'City' => array(
                    'type' => 'text',
                    'name' => 'City',
                    'caption' => _t('_bx_ads_caption_city'),
                    'required' => true,
                    'value' => $sCity,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,50),
                        'error' => _t ('_bx_ads_err_city'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),                
                ),
                'File' => array(
                    'type' => 'file',
                    'name' => 'userfile[]',
                    'caption' => _t('_associated_image'),
					'attrs' => array(
						'multiplyable' => 'true',
					)
                ),
                'ExistedImages' => array(
                    'type' => 'hidden',
                ),
                'allowView' => $aAllowView,
                'allowRate' => $aAllowRate,
                'allowComment' => $aAllowComment,
				'add_button' => array(
					'type' => 'submit',
					'name' => 'add_button',
					'value' => $sNewAdC,
				),
			),
		);

		$sExistedMedia = '';
		if ($iPostID>0) {
			$aAdUnitInfo = (is_array($aAdUnitInfo) && count($aAdUnitInfo)>0) ? $aAdUnitInfo : $this->_oDb->getAdInfo($iPostID);
			$sExistedMedia = $aAdUnitInfo['Media'];
			$sSubject = $aAdUnitInfo['Subject'];
			$sMessage = $aAdUnitInfo['Message'];
			$sPostTags = $aAdUnitInfo['Tags'];

			$sPostLifeTime = (int)$aAdUnitInfo['LifeTime'];

			$sPostPictureElements = $this->getImageManagingCode($aAdUnitInfo['Media'], $iPostID);
			if ($sPostPictureElements != '') {
				$aForm['inputs']['ExistedImages']['type'] = 'custom';
				$aForm['inputs']['ExistedImages']['content'] = $sPostPictureElements;
				$aForm['inputs']['ExistedImages']['caption'] = _t('_bx_ads_Existed_images');
			}

			$aForm['inputs']['Subject']['value'] = $sSubject;
			$aForm['inputs']['Message']['value'] = $sMessage;
			$aForm['inputs']['Tags']['value'] = $sPostTags;
			$aForm['inputs']['LifeTime']['value'] = $sPostLifeTime;

			$aForm['inputs']['allowView']['value'] = $aAdUnitInfo['AllowView'];
			$aForm['inputs']['allowRate']['value'] = $aAdUnitInfo['AllowRate'];
			$aForm['inputs']['allowComment']['value'] = $aAdUnitInfo['AllowComment'];

			$aForm['inputs']['hidden_postid'] = array(
				'type' => 'hidden',
				'name' => 'EditPostID',
				'value' => $iPostID,
			);
		}

        if (empty($aForm['inputs']['allowView']['value']) || !$aForm['inputs']['allowView']['value'])
            $aForm['inputs']['allowView']['value'] = BX_DOL_PG_ALL;
        if (empty($aForm['inputs']['allowRate']['value']) || !$aForm['inputs']['allowRate']['value'])
            $aForm['inputs']['allowRate']['value'] = BX_DOL_PG_ALL;
        if (empty($aForm['inputs']['allowComment']['value']) || !$aForm['inputs']['allowComment']['value'])
            $aForm['inputs']['allowComment']['value'] = BX_DOL_PG_ALL;

		$oForm = new BxTemplFormView($aForm);
		$oForm->initChecker();
		if ($oForm->isSubmittedAndValid() && $iSubCatID) {
			$this->CheckLogged();

			$sCurTime = time();
			$sPostUri = uriGenerate(bx_get('Subject'), $this->_oConfig->sSQLPostsTable, 'EntryUri');
			$sAutoApprovalVal = (getParam('bx_ads_auto_approving') == 'on') ? 'active' : 'new';

			$sCustomFieldValue1 = floatval(bx_get('CustomFieldValue1'));
			$sCustomFieldValue2 = floatval(bx_get('CustomFieldValue2'));

			$sMedIds = $this->parseUploadedFiles();

			$aValsAdd = array (
				'DateTime' => $sCurTime,
				'Status' => $sAutoApprovalVal,
				'IDClassifiedsSubs' => $iSubCatID,
				'CustomFieldValue1' => $sCustomFieldValue1,
				'CustomFieldValue2' => $sCustomFieldValue2
			);
			if ($sMedIds != '') {
				$sMedIds = ($sExistedMedia != '') ? $sExistedMedia . ',' . $sMedIds : $sMedIds;
				$aValsAdd['Media'] = $sMedIds;
			}

			$iLastId = -1;
			if ($iPostID>0) {
				$oForm->update($iPostID, $aValsAdd);
				$iLastId = $iPostID;
			} else {
                $aValsAdd['EntryUri'] = $sPostUri;
                $aValsAdd['IDProfile'] = $this->_iVisitorID;
				$iLastId = $oForm->insert($aValsAdd);
			}

			if ($iLastId > 0) {
				($iPostID>0) ? $this->isAllowedEdit($iAdvOwner, true) : $this->isAllowedAdd(true); // perform action

				//reparse tags
				bx_import('BxDolTags');
				$oTags = new BxDolTags();
				$oTags->reparseObjTags('ad', $iLastId);

				bx_import('BxDolAlerts');
				$sAlertAction = ($iPostID) ? 'edit' : 'create';
				$oZ = new BxDolAlerts('ads', $sAlertAction, $iLastId, $this->_iVisitorID);
				$oZ->alert();

				$sResult = ($iPostID>0) ? _t('_bx_ads_Ad_succ_updated') : _t('_bx_ads_Ad_succ_added');
				return  MsgBox($sResult) . $this->ActionPrintAdvertisement($iLastId);
			} else {
				return MsgBox(_t('_Error Occured'));
			}
		} else {
			$sNewAdFormVal = '<div class="blogs-view usualTextMarg">' . $oForm->getCode() . '</div>';
			return ($bBox) ? DesignBoxContent (_t('_bx_ads_Add_ad'), $sNewAdFormVal, 1) : $sNewAdFormVal;
		}
	}

	/**
	 * Parsing uploaded files, store its with temp names, fill data into SQL tables
	 *
	 * @param $iMemberID	current member ID
	 * @return Text presentation of data (enum ID`s)
	 */
	function parseUploadedFiles() {
		$sCurrentTime = time();

		if ( $_FILES) {
			$aIDs = array();

			for ($i=0; $i<count($_FILES['userfile']['tmp_name']); $i++) {
				if( $_FILES['userfile']['error'][$i] )
					continue;
				if( $_FILES['userfile']['size'][$i] > $this->iMaxUplFileSize ) {
					echo _t_err('_bx_ads_Warn_max_file_size', $_FILES['userfile']['name'][$i]);
					continue;
				}

				list( $width, $height, $type, $attr ) = getimagesize( $_FILES['userfile']['tmp_name'][$i] );

				if ( $type != 1 && $type != 2 && $type != 3 )
					continue;

				$sBaseName = $this->_iVisitorID .'_'. $sCurrentTime .'_'. ($i+1);
				$sExt = strrchr($_FILES['userfile']['name'][$i], '.');
				$sExt = strtolower(trim($sExt));

				$vResizeRes = imageResize( $_FILES['userfile']['tmp_name'][$i], BX_DIRECTORY_PATH_ROOT . "{$this->sUploadDir}img_{$sBaseName}{$sExt}", $this->iImgSize, $this->iImgSize );
				$vThumbResizeRes = imageResize( $_FILES['userfile']['tmp_name'][$i], BX_DIRECTORY_PATH_ROOT . "{$this->sUploadDir}thumb_{$sBaseName}{$sExt}", $this->iThumbSize, $this->iThumbSize );
				$vBigThumbResizeRes = imageResize( $_FILES['userfile']['tmp_name'][$i], BX_DIRECTORY_PATH_ROOT . "{$this->sUploadDir}big_thumb_{$sBaseName}{$sExt}", $this->iBigThumbSize, $this->iBigThumbSize );
				$vIconResizeRes = imageResize( $_FILES['userfile']['tmp_name'][$i], BX_DIRECTORY_PATH_ROOT . "{$this->sUploadDir}icon_{$sBaseName}{$sExt}", $this->iIconSize, $this->iIconSize );
				if ( $vResizeRes || $vThumbResizeRes || $vBigThumbResizeRes || $vIconResizeRes ) {
					echo _t_err("_ERROR_WHILE_PROCESSING"); 
					continue;
				}

				$aIDs[] = $this->_oDb->insertMedia($this->_iVisitorID, $sBaseName, $sExt);
			}
			return implode(',', $aIDs);
		}
	}

	/**
	 * Generate list of My Advertisements
	 *
	 * @return HTML presentation of data
	 */
	function getMemberAds($iOtherProfileID = 0, $iRandLim = 0, $iExceptUnit = 0) {
		$sBrowseAllAds = _t('_bx_ads_Browse_All_Ads');
		$sUserListC = _t('_bx_ads_Users_other_listing');

		$sHomeLink = ($this->bUseFriendlyLinks) ? BX_DOL_URL_ROOT . 'ads/' : "{$this->sCurrBrowsedFile}?Browse=1";
		$sMoreIcon = $this->_oTemplate->getIconUrl('more.png');
		$sSiteUrl = BX_DOL_URL_ROOT;

		$sBreadCrumbs = <<<EOF
<div class="paginate">
	<div class="view_all" style="background-image: url({$sMoreIcon});">
		<a href="{$sHomeLink}">{$sBrowseAllAds}</a> / {$sUserListC}
	</div>
</div>
EOF;

		require_once( $this->_oConfig->getClassPath() . 'BxAdsSearchUnit.php');
		$oTmpAdsSearch = new BxAdsSearchUnit();
		if ($iRandLim>0) {
			$oTmpAdsSearch->aCurrent['paginate']['perPage'] = (int)$iRandLim;
		} else {
			$oTmpAdsSearch->aCurrent['paginate']['perPage'] = 10;
		}
		$oTmpAdsSearch->aCurrent['sorting'] = 'last';
		$oTmpAdsSearch->aCurrent['restriction']['owner']['value'] = $iOtherProfileID;
		if ($iExceptUnit > 0) {
			$oTmpAdsSearch->aCurrent['restriction']['id']['value'] = $iExceptUnit;
			$oTmpAdsSearch->aCurrent['restriction']['id']['operator'] = '!=';
		}

		$sMemberAds = $oTmpAdsSearch->displayResultBlock();
        if ($oTmpAdsSearch->aCurrent['paginate']['totalNum'] == 0) {
            $sMemberAds = MsgBox(_t('_Empty'));
        } else {
            //$sMemberAds = $GLOBALS['oFunctions']->centerContent($sMemberAds, '.ads_Unit'); 
        }

		if ($iRandLim==0) {
			$GLOBALS['oTopMenu']->setCurrentProfileID($iOtherProfileID);
			return  DesignBoxContent ($sUserListC, $sMemberAds . $sBreadCrumbs, 1);
		}

		return $sMemberAds;
	}

	function DeleteProfileAdvertisement($iProfileID) {
		if ($this->bAdminMode==true) {
			$vDelSQL = $this->_oDb->getMemberAds((int)$iProfileID);
			while( $aAdv = mysql_fetch_assoc($vDelSQL) ) {
				$this->ActionDeleteAdvertisement($aAdv['ID']);
			}
		}
	}

	/**
	 * Deleting Advertisement from `bx_ads_main`
	 *
	 * @param $iID	ID of deleting Advertisement
	 * @return Text presentation of result
	 */
	function ActionDeleteAdvertisement($iID) {
		$iDeleteAdvertisementID = (int)$iID;

		$iAdvOwner = $this->_oDb->getOwnerOfAd($iDeleteAdvertisementID);

        if (!$this->isAllowedDelete($iAdvOwner)) {
            return $this->_oTemplate->displayAccessDenied();
        }

		if ($iDeleteAdvertisementID > 0) {
			$sSuccDel = _t("_bx_ads_Ad_succ_deleted");
			$sFailDel = _t("_bx_ads_Ad_fail_delete");

			$sRetHtml = '';
			$sMediaIDs = $this->_oDb->getMediaOfAd($iDeleteAdvertisementID);
			if ($sMediaIDs != '') {
                $aChunks = explode(',', $sMediaIDs);
				foreach ($aChunks as $sMedId) {
                    $iMedId = (int)$sMedId;
                    if ($iMedId) {
    					$sMediaFileName = $this->_oDb->getMediaFile($iMedId);
    					if ($sMediaFileName != '') {
    						@unlink (BX_DIRECTORY_PATH_ROOT.$this->sUploadDir . 'img_'.$sMediaFileName);
    						@unlink (BX_DIRECTORY_PATH_ROOT.$this->sUploadDir . 'thumb_'.$sMediaFileName);
    						@unlink (BX_DIRECTORY_PATH_ROOT.$this->sUploadDir . 'big_thumb_'.$sMediaFileName);
    						@unlink (BX_DIRECTORY_PATH_ROOT.$this->sUploadDir . 'icon_'.$sMediaFileName);
    					}
    					$this->_oDb->deleteMedia($iMedId);
                    }
				}
			}

			if ($this->_oDb->deleteAd($iDeleteAdvertisementID)) {
				$this->isAllowedDelete($iAdvOwner, true); // perform action

				$oCmts = new BxDolCmts('ads', $iDeleteAdvertisementID);
	    		$oCmts->onObjectDelete();

				//reparse tags
				bx_import('BxDolTags');
				$oTags = new BxDolTags();
				$oTags->reparseObjTags('ad', $iDeleteAdvertisementID);

				// delete views
				bx_import ('BxDolViews');
				$oViews = new BxDolViews('ads', $iDeleteAdvertisementID, false);
				$oViews->onObjectDelete();

				bx_import('BxDolAlerts');
				$oZ = new BxDolAlerts('ads', 'delete', $iDeleteAdvertisementID, $iDeleteAdvertisementID);
				$oZ->alert();

				$sRetHtml .= MsgBox(_t($sSuccDel));
			} else {
				$sRetHtml .= MsgBox(_t($sFailDel));
			}
			return $sRetHtml;
		} else {
			return MsgBox(_t('_Error Occured'));
		}
	}

	function getImageManagingCode($sMediaIDs, $iPostID) {
		$sDeleteC = _t('_Delete');

		if ($sMediaIDs != '') {
            $aChunks = explode(',', $sMediaIDs);
			foreach ($aChunks as $sMedId) {
                $iMedId = (int)$sMedId;
                if (is_numeric($iMedId) && $iMedId) {
    				$aSqlRes = $this->_oDb->getMediaInfo($iMedId);
    				if ($aSqlRes) {
    					$sFileName = BX_DOL_URL_ROOT . $this->sUploadDir.'thumb_'.$aSqlRes['MediaFile'];
                        $sAdsEditLink = ($this->bUseFriendlyLinks) ? "ads/my_page/edit/{$iPostID}/dimg/{$iMedId}" : "{$this->sCurrBrowsedFile}?action=my_page&mode=add&EditPostID={$iPostID}&dimg={$iMedId}";
    					$sImgTag .= <<<EOF
<div style="float:left;">
    <img class="photo1" src="{$this->sSpacerPath}" style="width:{$this->iThumbSize}px;height:{$this->iThumbSize}px;background-image:url({$sFileName});" alt="" />
    <br />
    <a href="{$sAdsEditLink}">{$sDeleteC}</a>
</div>
EOF;
                    }
                }
			}
			return <<<EOF
<div class="thumbnail_block">{$sImgTag}</div>
EOF;
		}
	}

	function getAdCover($sMediaIDs, $sType = 'thumb') {
        $sFileName = ($sType != 'icon') ? $this->sPicNotAvail : $GLOBALS['oSysTemplate']->getIconUrl('no-photo-64.png');

		if ($sMediaIDs != '') {
            $aChunks = explode(',', $sMediaIDs);
			$iMedId = (int)$aChunks[0];

            if (is_numeric($iMedId) && $iMedId) {
    			$aSqlRes = $this->_oDb->getMediaInfo($iMedId);
    			if ($aSqlRes) {
    				if (file_exists(BX_DIRECTORY_PATH_ROOT . $this->sUploadDir . $sType . '_' . $aSqlRes['MediaFile'])) {
    					$sFileName = BX_DOL_URL_ROOT . $this->sUploadDir . $sType . '_' . $aSqlRes['MediaFile'];
                    }
    			}
            }
		}
        return $sFileName;
	}

	/**
	 * Generate first paid page
	 *
	 * @param $iAdvertisementID	ID of Advertisement
	 * @return HTML presentation of data
	 */
	function ActionBuyAdvertisement($iAdvertisementID) {
		$sRetHtml = '';
		if ($this->_iVisitorID > 0) {
			$aSqlResStr = $this->_oDb->getAdInfo($iAdvertisementID);
			if ($aSqlResStr) {
				$sCustDetails = ($aSqlResStr['CustomFieldName1'] != NULL && $aSqlResStr['CustomFieldValue1'])?"{$aSqlResStr['Unit1']} {$aSqlResStr['CustomFieldValue1']}":'';
				$sCustDetails .= ($aSqlResStr['CustomFieldName2'] != NULL && $aSqlResStr['CustomFieldValue2'])?" - {$aSqlResStr['Unit2']} {$aSqlResStr['CustomFieldValue2']}":'';
				$sBuyMsg1 = _t('_bx_ads_BuyMsg1');
				$sBuyDet1 = _t('_bx_ads_BuyDetails1');
				$sContinue = _t('_Continue');

				$sBoxTag = <<<EOF
<div class="dbContent">
    <div>
    	<b>{$sBuyMsg1}</b>
    </div><br/>
    <div>
    	<b>{$sBuyDet1}</b>&nbsp;&nbsp;&nbsp;{$sCustDetails}
    </div><br/>
    <div>
    	<input class="button" type="submit" onclick="javascript:this.value='Wait...';this.disabled=true;document.bid_form.submit();" value="{$sContinue}" />
    </div>
</div>
EOF;

				$sRetHtml .= DesignBoxContent($aSqlResStr['Subject'], $sBoxTag, 1);
				$sRetHtml .= <<<EOF
<form action="{$this->sCurrBrowsedFile}" name="bid_form" method="post">
	<input type="hidden" name="BuySendNow" value="BuySendNow" />
	<input type="hidden" name="IDAdv" value="{$iAdvertisementID}" />
	<input type="hidden" name="IDSeller" value="{$aSqlResStr['IDProfile']}" />
</form>
EOF;
			}
		}
		return $sRetHtml;
	}

	/**
	 * Generate second paid page
	 *
	 * @param $iAdvertisementID	ID of Advertisement
	 * @return HTML presentation of data
	 */
	function ActionBuySendMailAdvertisement($iAdvertisementID) {
		global $site;

		$iSellerId = (int)bx_get('IDSeller');
		$sRetHtml = _t('_WARNING');
		if ($this->_iVisitorID > 0) {
			$aSqlResStr = $this->_oDb->getAdInfo($iAdvertisementID);
			$aSqlSellerRes = getProfileInfo($iSellerId);
			$aSqlMemberRes = getProfileInfo($this->_iVisitorID);
			if ($aSqlResStr) {
				$sCustDetails = ($aSqlResStr['CustomFieldName1'] != NULL && $aSqlResStr['CustomFieldValue1'])?"{$aSqlResStr['Unit1']} {$aSqlResStr['CustomFieldValue1']}":'';
				$sCustDetails .= ($aSqlResStr['CustomFieldName2'] != NULL && $aSqlResStr['CustomFieldValue2'])?" - {$aSqlResStr['Unit2']} {$aSqlResStr['CustomFieldValue2']}":'';

				$sPowDol = _t('_powered_by_Dolphin');
				$sBuyMsg2 = _t('_bx_ads_BuyMsg2');
				$sBuyDet1 = _t('_bx_ads_BuyDetails1');
				$sReturnBackC = _t('_bx_ads_Back');

                bx_import('BxDolEmailTemplates');
                $rEmailTemplate = new BxDolEmailTemplates();
                $aTemplate = $rEmailTemplate->getTemplate('t_BuyNow', $this->_iVisitorID) ;
                $aTemplateS = $rEmailTemplate->getTemplate('t_BuyNowS', $this->_iVisitorID) ;

				// Send email notification
                $sMessageB	= $aTemplate['Body'];
				$sMessageS	= $aTemplateS['Body'];
				$sSubject	= $aTemplate['Subject'];
				$sSubjectS	= $aTemplateS['Subject'];

				$aPlus = array();
				$aPlus['Subject'] = $aSqlResStr['Subject'];
				$aPlus['NickName'] = $aSqlSellerRes['NickName'];
				$aPlus['EmailS'] = $aSqlSellerRes['Email'];
				$aPlus['NickNameB'] = $aSqlMemberRes['NickName'];
				$aPlus['EmailB'] = $aSqlMemberRes['Email'];
				$aPlus['sCustDetails'] = $sCustDetails;

				$sGenUrl = $this->genUrl($iAdvertisementID, $aSqlResStr['EntryUri']);
				$aPlus['ShowAdvLnk'] = $sGenUrl;

				$aPlus['sPowDol'] = $sPowDol;
				$aPlus['site_email'] = $site['email'];

				$sRetHtml = '';
				$aPlus['Who'] = 'buyer';
				$aPlus['String1'] = _t('_bx_ads_you_have_purchased_an_item');
				sendMail( $aSqlMemberRes['Email'], $sSubject, $sMessageB, $aSqlSellerRes['ID'], $aPlus, 'html');

				$aPlus['Who'] = 'seller';
				$aPlus['String1'] = _t('_bx_ads_someone_wants_to_purchase');
				if (sendMail( $aSqlSellerRes['Email'], $sSubjectS, $sMessageS, $aSqlSellerRes['ID'], $aPlus, 'html')) {
					$sRetHtml .= MsgBox(_t('_Email was successfully sent'));
					bx_import('BxDolAlerts');
    				$oZ = new BxDolAlerts('ads', 'buy', $iAdvertisementID, $this->_iVisitorID);
    				$oZ->alert();
				}

				$sBoxContent = <<<EOF
<div class="dbContent">
    <div>
    	<b>{$sBuyMsg2}</b>
    </div><br/>
    <div>
    	<b>{$sBuyDet1}</b>&nbsp;&nbsp;&nbsp;{$sCustDetails}
    </div><br/>
    <div>
    	<a href="{$sGenUrl}">{$sReturnBackC}</a>
    </div>
</div>
EOF;
				$sRetHtml .= DesignBoxContent($aSqlResStr['Subject'], $sBoxContent, 1);
			}
		}
		return $sRetHtml;
	}

	/**
	 * Generate presentation Advertisement code with images and other
	 *
	 * @param $iID	ID of Advertisement
	 * @return HTML presentation of data
	 */
	function ActionPrintAdvertisement($iID) {
		global $site;
		global $aPreValues;

		$iAdvertisementID = (int)$iID;
		$sRetHtml = '';
		$sSiteUrl = BX_DOL_URL_ROOT;

		if ($this->bAdminMode && $iAdvertisementID>0) {
			$iFeaturedStatus = $this->_oDb->getFeaturedStatus($iAdvertisementID);
			$iNewStatus = ($iFeaturedStatus==1) ? 0 : 1;
			if (bx_get('do') == 'cfs') {
				$this->_oDb->UpdateFeatureStatus($iAdvertisementID, $iNewStatus);
			}
		}

		$aSqlResStr = $this->_oDb->getAdInfo($iAdvertisementID);
		if ($aSqlResStr) {
			$iOwnerID = (int)$aSqlResStr['IDProfile'];

			$bPossibleToView = $this->oPrivacy->check('view', $iAdvertisementID, $this->_iVisitorID);
			if ($this->isAllowedView($iOwnerID, true) == false || $bPossibleToView == false) {
				return $this->_oTemplate->displayAccessDenied();
			}

	        bx_import ('BxDolViews');
	        new BxDolViews('ads', $iAdvertisementID);

			$aNameRet = getProfileInfo($aSqlResStr['IDProfile']);
            $sCountryName = $aSqlResStr['Country'];
			$sCountryPic = ($sCountryName=='') ? '' : ' <img alt="'.$sCountryName.'" src="'.($site['flags'].strtolower($sCountryName)).'.gif"/>';
            $sCountryName = _t($aPreValues['Country'][ $sCountryName ]['LKey'] );

			$sPostedByC = _t('_bx_ads_Posted_by');
			$sPhoneC = _t('_Phone');
			$sDetailsC = _t('_bx_ads_Details');
			$sUserOtherListC = _t('_bx_ads_Users_other_listing');
			$sActionsC = _t('_Actions');
			$sSureC = _t('_Are you sure?');

			$sMoreIcon = $this->_oTemplate->getIconUrl('more.png');

            $sPostedBy .= '<div class="cls_res_info">';
            $sPostedBy .= $sPostedByC.': <span style="color:#333333;"><a href="'.getProfileLink($aNameRet['ID']).'">'.$aNameRet['NickName'].'</a></span>';
            $sPostedBy .= '</div>';
            if ($aNameRet['Phone'] != "") {
                $sPostedBy .= '<div class="cls_res_info">';
                $sPostedBy .= $sPhoneC.": <div class=\"clr3\">{$aNameRet['Phone']}</div>";
                $sPostedBy .= '</div>';
            }

			$sTimeAgo = _format_when($aSqlResStr['sec']);

			$aTags = array();
			$aTagsLinks = array();

			$aTags = preg_split("/[;,]/", $aSqlResStr['Tags']);
			foreach ( $aTags as $sTag ) {
				$sSubLink = ($this->bUseFriendlyLinks) ? "ads/tag/" : $this->sHomeUrl . "classifieds_tags.php?tag=";
                $sTagS = htmlspecialchars(title2uri($sTag));
				$aTagsLinks[] = '<a href="'."{$sSubLink}{$sTagS}".'">'.$sTag.'</a>';
			}
			$sTags .= implode(", ", $aTagsLinks);

			$sMemberActionForms = '';
			if ($this->_iVisitorID > 0 && $this->_iVisitorID != $aNameRet['ID']) {//print Send PM button and other actions
				if (getParam('bx_ads_enable_paid')=='on') {
					$sMemberActionForms .= <<<EOF
<form action="{$this->sCurrBrowsedFile}" name="BuyNowForm" method="post">
	<input type="hidden" name="BuyNow" value="BuyNow" />
	<input type="hidden" name="IDAdv" value="{$iAdvertisementID}" />
	<input type="hidden" name="IDSeller" value="{$aSqlResStr['IDProfile']}" />
</form>
EOF;
				}
				$sMemberActionForms .= <<<EOF
<form action="{$sSiteUrl}mail.php" name="post_pm" id="post_pm" method="get">
	<input type="hidden" name="mode" value="compose" />
	<input type="hidden" name="recipient_id" value="{$aSqlResStr['IDProfile']}" />
	<input type="hidden" name="subject" value="{$aSqlResStr['Subject']}" />
</form>
EOF;
			}

			$sEntryUrl = $this->genUrl($iAdvertisementID, $aSqlResStr['EntryUri'], 'entry');

			$sMediaIDs = $this->_oDb->getMediaOfAd($iAdvertisementID);
			if ($sMediaIDs != '') {
                $aReadyMedia = explode(',', $sMediaIDs);
				$sPictureSectContent = $this->_blockPhoto($aReadyMedia, $iOwnerID);
			}

			$sPictureSect = ($sPictureSectContent != '') ? DesignBoxContent ( _t('_bx_ads_Ad_photos'), $sPictureSectContent, 1) : '';
			$this->sTAPhotosContent = $sPictureSectContent;

			bx_import('BxDolSubscription');
			$oSubscription = new BxDolSubscription();
			$aButton = $oSubscription->getButton($this->_iVisitorID, 'bx_' . $this->_oConfig->getUri(), '', $iAdvertisementID);

			$aActionKeys = array(
				'visitor_id' => $this->_iVisitorID,
				'owner_id' => $aNameRet['ID'],
				'admin_mode' => "'" . $this->bAdminMode . "'",
				'ads_id' => $iAdvertisementID,
				'ads_status' => $aSqlResStr['Status'],
				'ads_featured' => (int)$aSqlResStr['Featured'],
				'sure_label' => $sSureC,
				'ads_entry_url' => $sEntryUrl,
				'only_menu' => 0,
				'sbs_ads_title' => $aButton['title'], 
				'sbs_ads_script' => $aButton['script']
			);
			$sActionsTable = $GLOBALS['oFunctions']->genObjectsActions($aActionKeys, 'bx_ads', false);
			$sSubsAddon = $oSubscription->getData();
			$sActionsSectContent = $sSubsAddon . $sMemberActionForms . $sActionsTable;
			$sActionsSect = ($this->_iVisitorID>0 || $this->bAdminMode) ? DesignBoxContent ( $sActionsC, $sActionsSectContent, 1) : '';
			$this->sTAActionsContent = ($this->_iVisitorID>0 || $this->bAdminMode) ? $sActionsSectContent : '';

            require_once($this->_oConfig->getClassPath() . 'BxAdsCmts.php');
            $this->oCmtsView = new BxAdsCmts ($this->_oConfig->getCommentSystemName(), $iAdvertisementID);
            $sCommentsSectContent = $this->oCmtsView->getExtraCss();
            $sCommentsSectContent .= $this->oCmtsView->getExtraJs();
            $sCommentsSectContent .= (!$this->oCmtsView->isEnabled()) ? '' : $this->oCmtsView->getCommentsFirst();
            $this->sTACommentsContent = $sCommentsSectContent;
            $sCommSect = DesignBoxContent ( $aCaptions['Comments'], $sCommentsSectContent, 1);

			$sUserOtherListing = $this->getMemberAds($aSqlResStr['IDProfile'], 2, $iAdvertisementID);

			$sDataTimeFormatted = getLocaleDate($aSqlResStr['DateTime_UTS']);
			$iViews = (int)$aSqlResStr['Views'];

			$sOwnerThumb = get_member_thumbnail($aSqlResStr['IDProfile'], 'none', true);
			$sAdsMessage = process_html_output($aSqlResStr['Message']);

			$sCategLink = ($this->bUseFriendlyLinks) ? BX_DOL_URL_ROOT . 'ads/cat/'.$aSqlResStr['CEntryUri'] : "{$this->sCurrBrowsedFile}?bClassifiedID={$aSqlResStr['CatID']}";
			$sSCategLink = ($this->bUseFriendlyLinks) ? BX_DOL_URL_ROOT . 'ads/subcat/'.$aSqlResStr['SEntryUri'] : "{$this->sCurrBrowsedFile}?bSubClassifiedID={$aSqlResStr['SubID']}";

			$aSubjVariables = array (
				'sOwnerThumb' => $sOwnerThumb,
				'sDataTimeFormatted' => $sDataTimeFormatted,
				'sTimeAgo' => $sTimeAgo,
				'categ_name' => $aSqlResStr['Name'],
				'subcateg_name' => $aSqlResStr['NameSub'],
				'sTags' => $sTags,
				'iViews' => $iViews,
				'sCategLink' => $sCategLink,
				'sSCategLink' => $sSCategLink,
			);
			$sSubjectSectContent = $this->_oTemplate->parseHtmlByTemplateName('unit_subject_block', $aSubjVariables);

			$sSubjectSect = DesignBoxContent(_t('_Info'), $sSubjectSectContent, 1);
			$this->sTAInfoContent = $sSubjectSectContent;

            $sDescriptionContent = '<div class="dbContent">'.$sAdsMessage.'</div>';        
            $sDescriptionSect = DesignBoxContent(_t('_Description'), $sDescriptionContent, 1);
            $this->sTADescription = $sDescriptionContent;

    		//adding form
    		$aForm = array(
    			'form_attrs' => array(
    				'name' => 'custom_values_form',
    				'action' => $oAds->sCurrBrowsedFile,
    			),
    			'inputs' => array(
    				'Country' => array(
    					'type' => 'value',
    					'name' => 'Country',
    					'caption' => _t('_Country'),
    					'value' => $sCountryName . $sCountryPic,
    				),
    				'City' => array(
    					'type' => 'value',
    					'name' => 'City',
    					'caption' => _t('_City'),
    					'value' => $aSqlResStr['City'],
    				),
                ),
            );

            if ($aSqlResStr['CustomFieldName1'] && $aSqlResStr['CustomFieldValue1']) {
                $aForm['inputs']['Custom1'] = array (
                    'type' => 'value',
                    'name' => 'Custom1',
                    'caption' => $aSqlResStr['CustomFieldName1'],
                    'value' => $aSqlResStr['Unit1'] . $aSqlResStr['CustomFieldValue1'],
                );
            }
            if ($aSqlResStr['CustomFieldName2'] && $aSqlResStr['CustomFieldValue2']) {
                $aForm['inputs']['Custom2'] = array (
                    'type' => 'value',
                    'name' => 'Custom2',
                    'caption' => $aSqlResStr['CustomFieldName2'],
                    'value' => $aSqlResStr['Unit2'] . $aSqlResStr['CustomFieldValue2'],
                );
            }

            $oForm = new BxTemplFormView($aForm);
            $sOtherInfoContent = $oForm->getCode();
            $sOtherInfoSect = DesignBoxContent(_t('_bx_ads_Custom_Values'), $sOtherInfoContent, 1);
            $this->sTAOtherInfo = $sOtherInfoContent;

			$bPossibleToRate = $this->oPrivacy->check('rate', $iAdvertisementID, $this->_iVisitorID);
			$oVotingView = new BxTemplVotingView ('ads', $iAdvertisementID);
			$iVote = ($oVotingView && $oVotingView->isEnabled() && $bPossibleToRate) ? 1 : 0;
            $sVotePostRating = $oVotingView->getBigVoting($iVote);

			$sRatingSect = DesignBoxContent (_t('_Rate'), $sVotePostRating, 1);
			$this->sTARateContent = $sVotePostRating;

			$sOtherListingContent = <<<EOF
<div class="dbContent">
	{$sUserOtherListing}
</div>
EOF;

			$sSPaginateActions = <<<EOF
<div class="paginate">
	<div class="view_all" style="background-image: url({$sMoreIcon});">
		<a href="{$this->sCurrBrowsedFile}" onclick="document.forms['UsersOtherListingForm'].submit(); return false;">{$sUserOtherListC}</a>
		<form action="{$this->sCurrBrowsedFile}" name="UsersOtherListingForm" method="post">
			<input type="hidden" name="UsersOtherListing" value="1" />
			<input type="hidden" name="IDProfile" value="{$aSqlResStr['IDProfile']}" />
		</form>
	</div>
	<div class="pages_section"></div>
</div>
EOF;

			$sOtherListingSect = DesignBoxContent ($sUserOtherListC, $sOtherListingContent . $sSPaginateActions, 1);
			$this->sTAOtherListingContent = $sOtherListingContent . $sSPaginateActions;

			$sHomeLink = ($this->bUseFriendlyLinks) ? BX_DOL_URL_ROOT . 'ads/' : "{$this->sCurrBrowsedFile}?Browse=1";

			$sBrowseAllAds = _t('_bx_ads_Browse_All_Ads');
			$sBreadCrumbs = <<<EOF
<div class="breadcrumbs">
<a href="{$sHomeLink}">{$sBrowseAllAds}</a>
/
<a href="{$sCategLink}">{$aSqlResStr['Name']}</a>
/
<a href="{$sSCategLink}">{$aSqlResStr['NameSub']}</a>
</div>
EOF;

			$aBlocks[1] .= $sActionsSect;
			$aBlocks[1] .= $sSubjectSect;
			$aBlocks[1] .= $sRatingSect;
			$aBlocks[1] .= $sOtherListingSect;
			$aBlocks[2] .= $sPictureSect;
			$aBlocks[2] .= $sCommSect;

			$sRetHtml = <<<EOF
{$sBreadCrumbs}
<div>
	<div class="clear_both"></div>
	<div class="cls_info_left">
		{$aBlocks['1']}
	</div>
	<div class="cls_info">
		{$sDescriptionSect}
		{$aBlocks['2']}
	</div>
	<div class="clear_both"></div>
</div>
<div class="clear_both"></div>
EOF;

			bx_import('BxDolAlerts');
			$oZ = new BxDolAlerts('ads', 'view', $iAdvertisementID, $this->_iVisitorID);
			$oZ->alert();

			$sAdCover = $this->getAdCover($aSqlResStr['Media'], 'icon');
			if ($sAdCover != '') {
				$GLOBALS['oTopMenu']->setCustomSubIconUrl($sAdCover);
			} else {
				$GLOBALS['oTopMenu']->setCustomSubIconUrl($this->_oTemplate->getIconUrl('bx_ads.png'));
			}
			$GLOBALS['oTopMenu']->setCustomSubHeader(htmlspecialchars($aSqlResStr['Subject']));

            $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
                _t('_bx_ads_Ads') => $sHomeLink,
                $aSqlResStr['Subject'] => '',
            ));

            $this->_oTemplate->setPageDescription(htmlspecialchars($aSqlResStr['Subject']));
            $this->_oTemplate->addPageKeywords(htmlspecialchars($aSqlResStr['Tags']));
		}
		return $sRetHtml;
	}

    function serviceGetSubscriptionParams ($sAction, $iEntryId) {
        $aPostInfo = $this->_oDb->getAdInfo($iEntryId);
        if($aPostInfo['IDProfile']) {
            $sEntryUrl = $this->genUrl($iEntryId, $aPostInfo['EntryUri']);
            $sEntryCaption = $aPostInfo['Subject'];
        } else {
            return array('skip' => true);
        }
        
        $aActionList = array(
            'main' => '_sbs_main',
            'rate' => '_sbs_rates',
            'commentPost' => '_sbs_comments'
        );

        $sCurr = isset($aActionList[$sAction]) ? $aActionList[$sAction] : $aActionList['main'];
        return array (
            'skip' => false,
            'template' => array (
                'Subscription' => _t('_bx_ads' . $sCurr, $sEntryCaption),
                'ViewLink' => $sEntryUrl,
            ),
        );
    }

	/**
	 * Generate array of Advertisements of some Classified
	 *
	 * @param $iClassifiedID	ID of Classified
	 * @return HTML presentation of data
	 */
	function PrintAllSubRecords($iClassifiedID) {
		$iSafeCatID = (int)$iClassifiedID;
		$sSiteUrl = BX_DOL_URL_ROOT;

		require_once( $this->_oConfig->getClassPath() . 'BxAdsSearchUnit.php');
		$oTmpAdsSearch = new BxAdsSearchUnit();
		$oTmpAdsSearch->aCurrent['paginate']['perPage'] = 10;
		$oTmpAdsSearch->aCurrent['sorting'] = 'last';
		$oTmpAdsSearch->aCurrent['restriction']['categoryID']['value'] = $iSafeCatID;
		$sCategoryAds = $oTmpAdsSearch->displayResultBlock();

        if ($oTmpAdsSearch->aCurrent['paginate']['totalNum'] == 0) {
            $sCategoryAds = MsgBox(_t('_Empty'));
        } else {
    		// Prepare link to pagination
    		if ($this->bUseFriendlyLinks==false) { //old variant
    			$sRequest = bx_html_attribute($_SERVER['PHP_SELF']) . '?bClassifiedID=' . $iSafeCatID . '&page={page}&per_page={per_page}';
    		} else {
    			$sRequest = BX_DOL_URL_ROOT . 'ads/all/cat/';
    			$sPaginAddon = '/' . process_db_input(bx_get('catUri'), BX_TAGS_STRIP);
    			$sRequest .= '{per_page}/{page}' . $sPaginAddon;
    		}
    		// End of prepare link to pagination

    		$oTmpAdsSearch->aCurrent['paginate']['page_url'] = $sRequest;
    		$sCategoryAds .= $oTmpAdsSearch->showPagination();

    		//$sCategoryAds = $GLOBALS['oFunctions']->centerContent($sCategoryAds, '.ads_Unit'); 
        }

		// Breadcrumb creating
		$sBrowseAllAds = _t('_bx_ads_Browse_All_Ads');
		$sHomeLink = ($this->bUseFriendlyLinks) ? BX_DOL_URL_ROOT . 'ads/' : "{$this->sCurrBrowsedFile}?Browse=1";
		$sNameCat = $this->_oDb->getCategoryNameByID($iSafeCatID);

		$sBreadCrumbs = <<<EOF
<div class="breadcrumbs">
<a href="{$sHomeLink}">{$sBrowseAllAds}</a>
/
<span class="active_link">{$sNameCat}</span>
</div>
EOF;
		// End of Breadcrumb creating

		$sFilter = $this->PrintFilterForm($iClassifiedID);

		$sCategoryAdsPageContent = DesignBoxContent ($sBreadCrumbs, $sFilter . $sCategoryAds, 1);
		return $sCategoryAdsPageContent;
	}

	/**
	 * Generate array of Advertisements of some SubClassified
	 *
	 * @param $iIDClassifiedsSubs	ID of SubClassified
	 * @return HTML presentation of data
	 */
	function PrintSubRecords($iIDClassifiedsSubs) {
		$iIDClassifiedsSubs = (int)$iIDClassifiedsSubs;
		$sSiteUrl = BX_DOL_URL_ROOT;

		require_once( $this->_oConfig->getClassPath() . 'BxAdsSearchUnit.php');
		$oTmpAdsSearch = new BxAdsSearchUnit();
		$oTmpAdsSearch->aCurrent['paginate']['perPage'] = 10;
		$oTmpAdsSearch->aCurrent['sorting'] = 'last';
		$oTmpAdsSearch->aCurrent['restriction']['subcategoryID']['value'] = $iIDClassifiedsSubs;
		$sSubAds = $oTmpAdsSearch->displayResultBlock();
        if ($oTmpAdsSearch->aCurrent['paginate']['totalNum'] == 0) {
            $sSubAds = MsgBox(_t('_Empty'));
        } else {
    		// Prepare link to pagination
    		if ($this->bUseFriendlyLinks==false) { //old variant
    			$sRequest = bx_html_attribute($_SERVER['PHP_SELF']) . '?bSubClassifiedID=' . $iIDClassifiedsSubs . '&page={page}&per_page={per_page}';
    		} else {
    			$sRequest = BX_DOL_URL_ROOT . 'ads/all/subcat/';
    			$sPaginAddon = '/' . process_db_input(bx_get('scatUri'), BX_TAGS_STRIP);
    			$sRequest .= '{per_page}/{page}' . $sPaginAddon;
    		}
    		// End of prepare link to pagination

    		$oTmpAdsSearch->aCurrent['paginate']['page_url'] = $sRequest;
    		$sSubAds .= $oTmpAdsSearch->showPagination();

    		//$sSubAds = $GLOBALS['oFunctions']->centerContent($sSubAds, '.ads_Unit'); 
        }

		// Breadcrumb creating
		$aSubcatRes = $this->_oDb->getCatAndSubInfoBySubID($iIDClassifiedsSubs);
		$sCaption = "<div class=\"fl\">{$aSubcatRes['Name']}->{$aSubcatRes['NameSub']}</div>\n";

		$sDesc = "<div class=\"cls_result_row\">{$aSubcatRes['Description']}</div>";

		$sHomeLink = ($this->bUseFriendlyLinks) ? BX_DOL_URL_ROOT . 'ads/' : "{$this->sCurrBrowsedFile}?Browse=1";
		$sCategLink = ($this->bUseFriendlyLinks) ? BX_DOL_URL_ROOT . 'ads/cat/'.$aSubcatRes['CEntryUri'] : "{$this->sCurrBrowsedFile}?bClassifiedID={$aSubcatRes['ClassifiedsID']}";

		$sBrowseAllAds = _t('_bx_ads_Browse_All_Ads');
		$sBreadCrumbs = <<<EOF
<div class="breadcrumbs">
<a href="{$sHomeLink}">{$sBrowseAllAds}</a>
/
<a href="{$sCategLink}">{$aSubcatRes['Name']}</a>
/
<span class="active_link">{$aSubcatRes['NameSub']}</span>
</div>
EOF;
		// End of Breadcrumb creating

		$sFilter = $this->PrintFilterForm(0, $iIDClassifiedsSubs);
		$sSubPageContent = DesignBoxContent ($sBreadCrumbs, $sFilter . $sSubAds, 1);
		return $sSubPageContent;
	}

	/**
	 * Generate a href to Back Link
	 *
	 * @return HTML presentation of data
	 */
	function PrintBackLink() {
		$sHomeLink = ($this->bUseFriendlyLinks && $this->bAdminMode == false) ? BX_DOL_URL_ROOT . 'ads/' : "{$this->sCurrBrowsedFile}?Browse=1";

		$sReturnBackC = _t('_bx_ads_Back');
		$sRetHtml = <<<EOF
<div>
	<b>
		<a href="{$sHomeLink}">{$sReturnBackC}</a>
	</b>
</div>
EOF;
		return $sRetHtml;
	}

	function GenAllAds($sType = 'last') {
		$sCaption = _t('_bx_ads_last_ads');
		$sDisplayMode = '';
		$sTypeMode = '';
		switch ($sType) {
			case 'last':
				$sCaption = _t('_bx_ads_All_ads');
				$sDisplayMode = 'last';
				break;
			case 'featured':
				$sCaption = _t('_bx_ads_Featured');
				$sTypeMode = 'featured';
				$sDisplayMode = 'last';
				break;
			case 'popular':
				$sCaption = _t('_bx_ads_Popular');
				$sDisplayMode = 'popular';
				break;
			case 'top':
			default:
				$sCaption = _t('_bx_ads_Top_Rated');
				$sDisplayMode = 'top';
				break;
		}

		$aParams = array();
		if (false !== bx_get('action')) {
			$aParams[] = 'action=' . bx_get('action');
		} else {
			$aParams[] = 'Browse=1';
		}
		$aParams[] = 'page={page}';
		$aParams[] = 'per_page={per_page}';
		$sParams = implode('&', $aParams);
		$sRequest = bx_html_attribute($_SERVER['PHP_SELF']) . '?' . $sParams;

		require_once( $this->_oConfig->getClassPath() . 'BxAdsSearchUnit.php');
		$oTmpAdsSearch = new BxAdsSearchUnit();
		$oTmpAdsSearch->aCurrent['paginate']['perPage'] = 10;
		$oTmpAdsSearch->aCurrent['sorting'] = $sDisplayMode;
		if ($sTypeMode!='' && $sTypeMode=='featured') {
			$oTmpAdsSearch->aCurrent['restriction']['featuredStatus']['value'] = 1;
		}

        //privacy changes
        if ($sType == 'last') {
            if ($this->_iVisitorID) {
                $oTmpAdsSearch->aCurrent['restriction']['allow_view']['value'] = array(BX_DOL_PG_ALL, BX_DOL_PG_MEMBERS);
            } else {
                $oTmpAdsSearch->aCurrent['restriction']['allow_view']['value'] = array(BX_DOL_PG_ALL);
            }
        }

		$sLastAds = $oTmpAdsSearch->displayResultBlock();
        if ($oTmpAdsSearch->aCurrent['paginate']['totalNum'] == 0) {
            $sLastAds = MsgBox(_t('_Empty'));
        } else {
    		$oTmpAdsSearch->aCurrent['paginate']['page_url'] = $sRequest;
    		$sLastAds .= $oTmpAdsSearch->showPagination();
    		//$sLastAds = $GLOBALS['oFunctions']->centerContent($sLastAds, '.ads_Unit'); 
        }

		$sLastAdsSection = DesignBoxContent($sCaption, $sLastAds, 1);
		return $sLastAdsSection;
	}

	function genCategoriesBlock() {
		$sCategoriesHtml = '';
		$iColumnsCnt = 2;

		$iColumnWidth = (100 - $iColumnsCnt*2) / $iColumnsCnt;

		$vSqlRes = $this->_oDb->getAllCatsInfo();
		$iCategoriesCnt = mysql_num_rows($vSqlRes);
		$iCategPerColumn = ceil($iCategoriesCnt / $iColumnsCnt);

		$iCounter = 0;
		while ($aSqlResStr = mysql_fetch_assoc($vSqlRes)) {
			$iID = $aSqlResStr['ID'];
			$sCatName = htmlspecialchars($aSqlResStr['Name']);
			$sCatUri = $aSqlResStr['CEntryUri'];

			$sCategLink = ($this->bUseFriendlyLinks) ? BX_DOL_URL_ROOT . 'ads/cat/'.$sCatUri : "{$this->sCurrBrowsedFile}?bClassifiedID={$iID}";

			$sqlResSubs = $this->_oDb->getAllSubCatsInfo($aSqlResStr['ID']);
			if (db_affected_rows()==-1) {
				return _t('_Error Occured');
			}
			$sSubsHtml = '';
			while ($aSqlResSubsStr = mysql_fetch_assoc($sqlResSubs)) {
				$iSubID = (int)$aSqlResSubsStr['ID'];

				$iAdsCnt = $this->_oDb->getCountOfAdsInSubCat($iSubID);
				$sCntSub =  ($iAdsCnt > 0) ? " ({$iAdsCnt})" : '';

				$sNameSubUp = htmlspecialchars(ucwords($aSqlResSubsStr['NameSub']));
				$sSCategLink = ($this->bUseFriendlyLinks) ? BX_DOL_URL_ROOT . 'ads/subcat/'.$aSqlResSubsStr['SEntryUri'] : "{$this->sCurrBrowsedFile}?bSubClassifiedID={$iSubID}";

				$sSubsHtml .= <<<EOF
<div>
	<a class="sub_l" href="{$sSCategLink}">
		{$sNameSubUp}
	</a>
	{$sCntSub}
</div>
EOF;
			}

			$sCaption = <<<EOF
<a href="{$sCategLink}">
	{$sCatName}
</a>
EOF;

			$sOpenColDiv = $sCloseColDiv = '';
			$iResidueOfDiv = $iCounter % $iCategPerColumn;

			if ($iResidueOfDiv == 0) {
				$sOpenColDiv = <<<EOF
<div style="width:{$iColumnWidth}%;float:left;position:relative;margin-left:1%;margin-right:1%;">
EOF;
			}
			if ($iResidueOfDiv == $iCategPerColumn-1) {
				$sCloseColDiv = <<<EOF
</div>
EOF;
			}

			$sCategoryBlock = DesignBoxContent($sCaption, $sSubsHtml, 1);

			$sCategoryCover = $this->_oTemplate->getIconUrl($aSqlResStr['Picture']);

			$aCategoryVariables = array (
				'category_cover_image' => $sCategoryCover,
				'category_url' => $sCategLink,
				'category_name' => $sCatName,
				'sub_categories_list' => $sSubsHtml,
				'onclick' => '',
				'target' => '',
				'unit_id' => $iID
			);
			$sCategoryBlock = $this->_oTemplate->parseHtmlByTemplateName('category_unit', $aCategoryVariables);

			$sCategoriesHtml .= $sOpenColDiv . $sCategoryBlock . $sCloseColDiv;
			$iCounter++;
		}

        if ($iCounter == 0) return MsgBox(_t('_Empty'));

		$iResidueOfDivLast = $iCounter % $iCategPerColumn;
		if ($iCounter > 0 && $iResidueOfDivLast>0 && $iResidueOfDivLast < $iCategPerColumn) {
			$sCategoriesHtml .= '</div>';
		}

		$sAddJS = <<<EOF
<script type="text/javascript">
	function ShowHideController() {
		this.ShowHideToggle = function(rObject) {
			var sChildID	= $(rObject).attr("bxchild");
			var sBlockState = $("#" + sChildID).css("display");

			if ( sBlockState == 'block' ){
				$("#" + sChildID).slideUp(300);
				$(rObject).css({ backgroundPosition : "0 -17px"});
			} else {
				$(rObject).css({ backgroundPosition : "0 0"});
				$("#" + sChildID).slideDown(300);
			}
		}
	}
</script>
EOF;

		$sCategoriesBlocks = <<<EOF
<div class="dbContent">
	{$sAddJS}
	{$sCategoriesHtml}
	<div class="clear_both"></div>
</div>
EOF;
		return DesignBoxContent(_t('_bx_ads_Categories'), $sCategoriesBlocks, 1);
	}

	/**
	 * Generate array of Classified in lists doubled form
	 *
	 * @return HTML presentation of data
	 */
	function getAdsMainPage() {
        if (!$this->isAllowedBrowse()) {
            return $this->_oTemplate->displayAccessDenied();
        }

		$sLastAdsSection = $this->GenAllAds();

		require_once( $this->_oConfig->getClassPath() . 'BxAdsSearchUnit.php');
		$oTmpAdsSearch = new BxAdsSearchUnit();
		$oTmpAdsSearch->sSelectedUnit = 'ad_of_day';
		$oTmpAdsSearch->aCurrent['paginate']['forcePage'] = 1;
		$oTmpAdsSearch->aCurrent['paginate']['perPage'] = 1;
        $oTmpAdsSearch->aCurrent['restriction']['featuredStatus']['value'] = 1;
		//$oTmpAdsSearch->aCurrent['sorting'] = 'top';
		//$oTmpAdsSearch->aCurrent['second_restr'] = 'toptoday';
		$sTopAdOfAllDayValue = $oTmpAdsSearch->displayResultBlock();
		$sTopAdOfAllDayValue = ($oTmpAdsSearch->aCurrent['paginate']['totalNum'] == 0) ? MsgBox(_t('_Empty')) : $sTopAdOfAllDayValue;

		$sTopAdOfAllDaySection = DesignBoxContent(_t('_bx_ads_last_featured'), $sTopAdOfAllDayValue, 1);

		$sCategoriesSection = $this->genCategoriesBlock();

		return <<<EOF
<div id="page_column_1" class="page_column page_column_first" style="width: 34%;">
	<div class="page_block_container">
		{$sLastAdsSection}
	</div>
</div>
<div id="page_column_2" class="page_column page_column_last" style="width: 66%;">
	<div class="page_block_container">
		{$sTopAdOfAllDaySection}
	</div>
	<div class="page_block_container">
		{$sCategoriesSection}
	</div>
</div>
<div class="clear_both"></div>
EOF;
	}

	/**
	 * Generate Filter form with ability of searching by Category, Country and keyword (in Subject and Message)
	 *
	 * @return HTML presentation of form
	 */
	function PrintFilterForm($iClassifiedID = 0, $iSubClassifiedID = 0) {
		global $aPreValues;

        if (!$this->isAllowedSearch()) {
            return ;
        }

		$sCategoriesC = _t('_bx_ads_Categories');
		$sViewAllC = _t('_View All');

		$iClassifiedID = (false !== bx_get('FilterCat') && (int)bx_get('FilterCat')>0) ? (int)bx_get('FilterCat') : (int)$iClassifiedID;
		$iSubClassifiedID = (false !== bx_get('FilterSubCat') && (int)bx_get('FilterSubCat')>0) ? (int)bx_get('FilterSubCat') : (int)$iSubClassifiedID;
		$sCountry = process_db_input(bx_get('FilterCountry'), BX_TAGS_STRIP);
		$sCountry = (isset($aPreValues['Country'][$sCountry]) == true) ? $sCountry : -1;

		$sKeywords = process_db_input(bx_get('FilterKeywords'), BX_TAGS_STRIP);

		$iFilterStyleHeight = 38;
		$sSubDspStyle = ($sCategorySub!="")?'':'none';

		$sClassifiedsOptions = '';
		$vSqlRes = $this->_oDb->getAllCatsInfo();
		if (db_affected_rows()==-1)
			return _t('_Error Occured');

		while( $aSqlResStr = mysql_fetch_assoc($vSqlRes) ) {
			$sClassifiedsOptions .= "<option value=\"{$aSqlResStr['ID']}\"".(($aSqlResStr['ID']==$iClassifiedID)?" selected":'').">{$aSqlResStr['Name']}</option>\n";
		}

		$sCountryOptions = '';
		$sSelCountry = $sCountry;
		foreach ( $aPreValues['Country'] as $key => $value ) {
			$sCountrySelected = ( $sSelCountry == $key ) ? 'selected="selected"' : '';
			$sCountryOptions .= "<option value=\"{$key}\" ". $sCountrySelected ." >". _t($value['LKey']) ."</option>";
		}

		$sKeywordsStr = ($sKeywords!='') ? $sKeywords : '';
		$sCateg = '';
		$sSubCateg = '';
		if ($iClassifiedID==0 && $iSubClassifiedID==0) {
			$iFilterStyleHeight = 70;

			$sOnChange = ($iClassifiedID>0) ? '' : <<<EOF
onchange="AjaxyAskForSubcatsWithInfo('FilterSubCat', this.value, 'custom_values');"
EOF;

			$sCateg = <<<EOF
<br />
{$sCategoriesC}:
<select name="FilterCat" id="FilterCat" {$sOnChange} style="width:250px;">
	<option value="-1">{$sViewAllC}</option>
	{$sClassifiedsOptions}
</select>
EOF;

			$sSubCateg = <<<EOF
<select name="FilterSubCat" id="FilterSubCat" style="display:{$sSubDspStyle};">
</select>
<input id="unit" type="text" value="" size="3" maxlength="8" style="display:none;" />
<br />
EOF;
		}

		if ($iClassifiedID > 0) {
			$sCateg .= '<input type="hidden" name="FilterCat" value="'.$iClassifiedID.'" />'; $sSubCateg='';
		}
		if ($iSubClassifiedID > 0) {
			$sCateg .= '<input type="hidden" name="FilterSubCat" value="'.$iSubClassifiedID.'" />'; $sSubCateg='';
		}

		$aVariables = array (
			'sCurrBrowsedFile' => $this->sCurrBrowsedFile,
			'iFilterStyleHeight' => $iFilterStyleHeight,
			'sCountryOptions' => $sCountryOptions,
			'sCateg' => $sCateg,
			'sSubCateg' => $sSubCateg,
			'sKeywordsStr' => $sKeywordsStr,
		);
		return $this->_oTemplate->parseHtmlByTemplateName('filter_form', $aVariables);
	}

	/**
	 * Compose Form to managing with Classifieds, subs, and custom fields
	 *
	 * @return HTML presentation of data
	 */
    function getManageClassifiedsForm($iCategoryID = 0, $bOnlyForm = false) {
        $sAction = 'add_main_category';

        $sTitle = $sDescription = '';
        $sCustomName1 = $sCustomName2 = $sUnit = $sUnit2 = $sPicture = '';
        //$sCustomAction1 = $sCustomAction2 = 4;

        if ($iCategoryID) {
            $aCatInfos = $this->_oDb->getCatInfo($iCategoryID);
            $sTitle = $aCatInfos[0]['Name'];
            $sDescription = $aCatInfos[0]['Description'];
            $sCustomName1 = $aCatInfos[0]['CustomFieldName1'];
            $sCustomName2 = $aCatInfos[0]['CustomFieldName2'];
            $sUnit = $aCatInfos[0]['Unit1'];
            $sUnit2 = $aCatInfos[0]['Unit2'];
            $sPicture = $aCatInfos[0]['Picture'];
        }

		//adding form
		$aForm = array(
			'form_attrs' => array(
				'name' => 'create_main_cats_form',
				'action' => $oAds->sCurrBrowsedFile,
				'method' => 'post',
			),
			'params' => array (
				'db' => array(
					'table' => $this->_oConfig->sSQLCatTable,
					'key' => 'ID',
					'submit_name' => 'add_button',
				),
			),
			'inputs' => array(
                'action' => array(
                    'type' => 'hidden',
                    'name' => 'action',
                    'value' => $sAction,
                ),
				'Name' => array(
					'type' => 'text',
					'name' => 'Name',
					'caption' => _t('_Title'),
					'required' => true,
					'value' => $sTitle,
					'checker' => array (
						'func' => 'length',
						'params' => array(3,64),
						'error' => _t('_bx_ads_title_error_desc', 64),
					),
					'db' => array (
						'pass' => 'Xss', 
					),
				),
				'Description' => array(
					'type' => 'text',
					'name' => 'Description',
					'caption' => _t('_Description'),
					'required' => true,
                    'value' => $sDescription,
					'checker' => array (
						'func' => 'length',
						'params' => array(3,128),
						'error' => _t('_bx_ads_description_error_desc', 128),
					),
					'db' => array (
						'pass' => 'Xss', 
					),
				),
				'CustomFieldName1' => array(
					'type' => 'text',
					'name' => 'CustomFieldName1',
					'caption' => _t('_bx_ads_customFieldName1'),
					'required' => false,
                    'value' => $sCustomName1,
					/*'checker' => array (
						'func' => 'length',
						'params' => array(3,50),
						'error' => _t('_bx_ads_custom_error_desc', 1, 50),
					),*/
					'db' => array (
						'pass' => 'Xss', 
					),
				),
				'Unit1' => array(
					'type' => 'text',
					'name' => 'Unit1',
					'caption' => _t('_bx_ads_Unit') . ' 1',
					'required' => false,
                    'value' => $sUnit,
					'db' => array (
						'pass' => 'Xss', 
					),
				),
				'CustomFieldName2' => array(
					'type' => 'text',
					'name' => 'CustomFieldName2',
					'caption' => _t('_bx_ads_customFieldName2'),
					'required' => false,
                    'value' => $sCustomName2,
					/*'checker' => array (
						'func' => 'length',
						'params' => array(3,50),
						'error' => _t('_bx_ads_custom_error_desc', 2, 50),
					),*/
					'db' => array (
						'pass' => 'Xss', 
					),
				),
				'Unit2' => array(
					'type' => 'text',
					'name' => 'Unit2',
					'caption' => _t('_bx_ads_Unit') . ' 2',
					'required' => false,
                    'value' => $sUnit2,
					'db' => array (
						'pass' => 'Xss', 
					),
				),
				'Picture' => array(
					'type' => 'text',
					'name' => 'Picture',
					'caption' => _t('_Picture'),
                    'info' => _t('_In') . ' \modules\boonex\ads\templates\base\images\icons\'',
                    'value' => $sPicture,
					'db' => array (
						'pass' => 'Xss', 
					),
				),
				'add_button' => array(
					'type' => 'submit',
					'name' => 'add_button',
					'value' => ($iCategoryID) ? _t('_Edit') : _t('_bx_ads_add_main_category'),
				),
            ),
        );

        if ($iCategoryID) {
			$aForm['inputs']['hidden_postid'] = array(
				'type' => 'hidden',
				'name' => 'id',
				'value' => $iCategoryID,
			);
        }

        $sCode = '';
		$oForm = new BxTemplFormView($aForm);
		$oForm->initChecker();
		if ($oForm->isSubmittedAndValid()) {
            $aValsAdd = array();
			if ($iCategoryID == 0) {
                $sCategUri = uriGenerate(bx_get('Name'), $this->_oConfig->sSQLCatTable, 'CEntryUri');
                $aValsAdd['CEntryUri'] = $sCategUri;
			}

			$iLastId = -1;
			if ($iCategoryID>0) {
				$oForm->update($iCategoryID, $aValsAdd);
				$iLastId = $iCategoryID;
			} else {
				$iLastId = $oForm->insert($aValsAdd);
			}

			if ($iLastId > 0) {
				$sCode = MsgBox(_t('_bx_ads_Main_category_successfully_added'), 1);
			} else {
				$sCode = MsgBox(_t('_bx_ads_Main_category_failed_add'), 1);
			}
		}

        if ($bOnlyForm) {
            return $sCode . $oForm->getCode();
        }

        $sActions = array(
            'add_subcat' => array('href' => 'javascript: void(0);', 'title' => _t('_bx_ads_add_subcategory'), 'onclick' => 'showPopupAnyHtml(\'modules/boonex/ads/post_mod_ads.php?action=add_sub_category\');', 'active' => 0),
            'manager' => array('href' => 'javascript: void(0);', 'title' => _t('_bx_ads_category_manager'), 'onclick' => 'showPopupAnyHtml(\'modules/boonex/ads/post_mod_ads.php?action=category_manager\');', 'active' => 0)
        );
        return DesignBoxAdmin(_t('_bx_ads_Manage_categories_form'), $sCode . $oForm->getCode(), $sActions);
    }

    function getAddSubcatForm($iSubCategoryID = 0, $bOnlyForm = false) { //admin side only
        $sSubmitC = _t('_bx_ads_add_subcategory');
        $sAction = 'add_sub_category';

        $aParentCategories = array();
		$vParentValues = $this->_oDb->getAllCatsInfo();
		while ($aCategInfo = mysql_fetch_assoc($vParentValues)) {
			$iID = $aCategInfo['ID'];
			$sName = $aCategInfo['Name'];
            $aParentCategories[$iID] = $sName;
		}

        $sTitle = $sDescription = '';
        $iParentID = 0;
        if ($iSubCategoryID) {
            $aSubcatInfos = $this->_oDb->getSubcatInfo($iSubCategoryID);
            $sTitle = $aSubcatInfos[0]['NameSub'];
            $sDescription = $aSubcatInfos[0]['Description'];
            $iParentID = (int)$aSubcatInfos[0]['IDClassified'];
        }

		//adding form
		$aForm = array(
			'form_attrs' => array(
				'name' => 'create_sub_cats_form',
				'action' => 'javascript: void(0)',
				'method' => 'post',
			),
			'params' => array (
				'db' => array(
					'table' => $this->_oConfig->sSQLSubcatTable,
					'key' => 'ID',
					'submit_name' => 'add_button',
				),
			),
			'inputs' => array(
                'action' => array(
                    'type' => 'hidden',
                    'name' => 'action',
                    'value' => $sAction,
                ),
				'IDClassified' => array(
					'type' => 'select',
					'name' => 'IDClassified',
					'caption' => _t('_bx_ads_parent_category'),
					'values' => $aParentCategories,
					'value' => $iParentID,
					'db' => array (
						'pass' => 'Int', 
					),
				),
				'NameSub' => array(
					'type' => 'text',
					'name' => 'NameSub',
					'caption' => _t('_Title'),
					'required' => true,
                    'value' => $sTitle,
					'checker' => array (
						'func' => 'length',
						'params' => array(3,128),
						'error' => _t('_bx_ads_title_error_desc', 128),
					),
					'db' => array (
						'pass' => 'Xss', 
					),
				),
				'Description' => array(
					'type' => 'text',
					'name' => 'Description',
					'caption' => _t('_Description'),
                    'value' => $sDescription,
					'db' => array (
						'pass' => 'Xss', 
					),
				),
				'add_button' => array(
					'type' => 'submit',
					'name' => 'add_button',
					'value' => $sSubmitC,
                    'attrs' => array(
                        'onClick' => "AdmCreateSubcategory('{$this->sHomeUrl}{$this->sCurrBrowsedFile}'); return false;"
                    )
				),
            ),
        );

        if ($iSubCategoryID) {
			$aForm['inputs']['hidden_postid'] = array(
				'type' => 'hidden',
				'name' => 'id',
				'value' => $iSubCategoryID,
			);
        }

        $sCode = '';

        $sJS = $this -> _oTemplate -> addJs('main.js', true);

		$oForm = new BxTemplFormView($aForm);
		$oForm->initChecker();
		if ($oForm->isSubmittedAndValid()) {

			$aValsAdd = array ();
			if ($iSubCategoryID == 0) {
                $sCategUri = uriGenerate(bx_get('NameSub'), $this->_oConfig->sSQLSubcatTable, 'SEntryUri');
                $aValsAdd['SEntryUri'] = $sCategUri;
			}

			$iLastId = -1;
			if ($iSubCategoryID>0) {
				$oForm->update($iSubCategoryID, $aValsAdd);
				$iLastId = $iSubCategoryID;
			} else {
				$iLastId = $oForm->insert($aValsAdd);
			}

			if ($iLastId > 0) {
				$sCode = MsgBox(_t('_bx_ads_Sub_category_successfully_added'), 1);
			} else {
				$sCode = MsgBox(_t('_bx_ads_Sub_category_failed_add'), 1);
			}
		}

        if ($bOnlyForm) {
            return $sCode . $oForm->getCode();
        }

        $sResult = $sJS . $sCode . $oForm->getCode();

        if (bx_get('mode') == 'json') {
            require_once( BX_DIRECTORY_PATH_PLUGINS . 'Services_JSON.php' );
            $oJson = new Services_JSON();
            return $oJson->encode($sResult);
            exit;
        }

        // display as popup
        $aVars = array (
            'title' => _t('_bx_ads_add_subcategory'),
            'icon_close_url' => getTemplateImage('close.gif'),            
            'ads_form' => $sResult,
        );
        return $GLOBALS['oFunctions']->transBox($this->_oTemplate->parseHtmlByName('add_manage_popup.html', $aVars), true);
    }

    function getCategoryManager() {
        $sCatID = (int)bx_get('id');
        if ($sCatID && false !== bx_get('sa')) {
            switch (bx_get('sa')) {
                case 'editcat':
                    $sResult = $this->getManageClassifiedsForm($sCatID, true);
                    break;
                case 'editscat':
                    $sResult = $this->getAddSubcatForm($sCatID, true);
                    break;
                case 'delcat':
                    $sResult = MsgBox(_t('_bx_ads_Main_category_failed_delete'), 1);
                    if ($this->_oDb->deleteCat($sCatID)) {
                        $sResult = MsgBox(_t('_bx_ads_Main_category_successfully_deleted'), 1);
                    }

    				$vSubCats = $this->_oDb->getAllSubCatsInfo($sCategory);
    				while( $aSubCat = mysql_fetch_assoc($vSubCats) ) {
    					$iSubcat = (int)$aSubCat['ID'];
                        $this->_oDb->deleteSubCat($iSubcat);
    				}

                    break;
                case 'delscat':
                    $sResult = MsgBox(_t('_bx_ads_Sub_category_failed_deleted'), 1);
                    if ($this->_oDb->deleteSubCat($sCatID)) {
                        $sResult = MsgBox(_t('_bx_ads_Sub_category_successfully_delete'), 1);
                    }
                    break;            
            }
        }

        $sJS = $this -> _oTemplate -> addJs('main.js', true);
        $sRootCaptionC = _t('_bx_ads_root');
        $sViewC = _t('_View');
        $sEditC = _t('_Edit');
        $sDeleteC = _t('_Delete');

		$sFolder2Icon = $this->_oTemplate->getImageUrl('folder_s.gif');
		$vSqlRes = $this->_oDb->getAllCatsInfo();
		if (db_affected_rows()==-1) {
			return _t('_Error Occured');
		}
		$sTreeRows = '';
		while( $aSqlResCls = mysql_fetch_assoc($vSqlRes) ) {
			$iID = (int)$aSqlResCls['ID'];
			$sName = $aSqlResCls['Name'];
			$sCUri = $aSqlResCls['CEntryUri'];
			// $iCnt = $this->_oDb->getCountOfAdsInCat($iID);
			// $sCnt = ($iCnt > 0) ? "({$iCnt})" : "(0)";
			$sCategLink = $this->genUrl($iID, $sCUri, 'cat');
            $sCategDelLink = $this->sCurrBrowsedFile . '?action=category_manager&sa=delcat&id=' . $iID;
            $sCategEditLink = $this->sCurrBrowsedFile . '?action=category_manager&sa=editcat&id=' . $iID;

			$vSubs = $this->_oDb->getSubsNameIDCountAdsByAdID($iID);
			$sSubsRows = '';
			while( $aSub = mysql_fetch_assoc($vSubs) ) {
				$iSubID = (int)$aSub['ID'];
				//$iSubAdsCnt = (int)$aSub['Count'];
				$iSubName = $aSub['Name'];
                $sSUri = $aSub['SEntryUri'];
                $sSubCategLink = $this->genUrl($iSubID, $sSUri, 'subcat');
                $sSubCategDelLink = $this->sCurrBrowsedFile . '?action=category_manager&sa=delscat&id=' . $iSubID;
                $sSubCategEditLink = $this->sCurrBrowsedFile . '?action=category_manager&sa=editscat&id=' . $iSubID;

				$sSubsRows .= <<<EOF
<li id='{$iSubID}'><span>{$iSubName} </span><a href="{$sSubCategLink}">({$sViewC})</a><a href="javascript: void(0)" onclick="AdmAction2Category('{$this->sCurrBrowsedFile}?action=category_manager', 'editscat', {$iSubID}); return false;">({$sEditC})</a><a href="javascript: void(0)" onclick="AdmAction2Category('{$this->sCurrBrowsedFile}?action=category_manager', 'delscat', {$iSubID}); return false;">({$sDeleteC})</a></li>
EOF;
			}

			$sTreeRows .= <<<EOF
<li id='{$iID}'><span>{$sName}</span><a href="{$sCategLink}">({$sViewC})</a><a href="javascript: void(0)" onclick="AdmAction2Category('{$this->sCurrBrowsedFile}?action=category_manager', 'editcat', {$iID}); return false;">({$sEditC})</a><a href="javascript: void(0)" onclick="AdmAction2Category('{$this->sCurrBrowsedFile}?action=category_manager', 'delcat', {$iID}); return false;">({$sDeleteC})</a>
	<ul>
		{$sSubsRows}
	</ul>
</li>
EOF;
		}

		$sResult .= <<<EOF
<script type="text/javascript">
var simpleTreeCollection;
$(document).ready(function(){
	simpleTreeCollection = $('.simpleTree').simpleTree({
		autoclose: true,
		afterClick:function(node){
			//alert("text-"+$('span:first',node).text());
		},
		afterDblClick:function(node){
			//alert("text-"+$('span:first',node).text());
		},
		afterMove:function(destination, source, pos){
			//alert("destination-"+destination.attr('id')+" source-"+source.attr('id')+" pos-"+pos);
		},
		afterAjax:function()
		{
			//alert('Loaded');
		},
		animate:true,
		drag:false
		//,docToFolderConvert:true
	});
});
</script>

<ul class="simpleTree">
	<li class="root" id='1'><span>{$sRootCaptionC}</span>
		<ul>
			{$sTreeRows}
		</ul>
	</li>
</ul>
<div class="clear_both"></div>
<!-- <div id="cat_manage_div"></div>
<div class="clear_both"></div> -->
EOF;

        if (bx_get('mode') == 'json') {
            require_once( BX_DIRECTORY_PATH_PLUGINS . 'Services_JSON.php' );
            $oJson = new Services_JSON();
            return $oJson->encode($sResult);
        }

        // display as popup
        $aVars = array (
            'title' => _t('_bx_ads_category_manager'),
            'icon_close_url' => getTemplateImage('close.gif'),            
            'ads_form' => $sJS . $sResult,
        );
        return $GLOBALS['oFunctions']->transBox($this->_oTemplate->parseHtmlByName('add_manage_popup.html', $aVars), true);
    }

	function GenAdsAdminIndex() {
		if ($this->bAdminMode) {
			//actions
			if (bx_get('action_approve') && is_array(bx_get('ads'))) {
				foreach (bx_get('ads') as $iAdID) {
					if ($this->_oDb->setPostStatus((int)$iAdID, 'active')) {
						//$this->onAdChanged ($iAdID);
					}
				}
			} elseif (bx_get('action_disapprove') && is_array(bx_get('ads'))) {
				foreach (bx_get('ads') as $iAdID) {
					if ($this->_oDb->setPostStatus((int)$iAdID)) {
						//$this->onAdChanged ($iAdID);
					}
				}
			} elseif (bx_get('action_delete') && is_array(bx_get('ads'))) {
				foreach (bx_get('ads') as $iAdID) {
					$this->ActionDeleteAdvertisement((int)$iAdID);
					//$this->onAdDeleted ($iAdID);
				}
			}

			$sCap2C = _t('_bx_ads_Moderating');
			$sAct = _t("_bx_ads_Activate");
			$sWholesaleC = _t("_bx_ads_wholesale");

			$sActivateAdvWholesale = <<<EOF
<h2>{$sCap2C} ( <a href=\"{$this->sCurrBrowsedFile}?ActivateAdvWholesale=1\">{$sAct} {$sWholesaleC}</a> )</h2>
EOF;

			require_once( $this->_oConfig->getClassPath() . 'BxAdsSearchUnit.php');
			$oTmpAdsSearch = new BxAdsSearchUnit();
			$oTmpAdsSearch->aCurrent['paginate']['perPage'] = 10;
			$oTmpAdsSearch->aCurrent['sorting'] = 'last';
            $oTmpAdsSearch->aCurrent['restriction']['activeStatus']['value'] = 'new';
			$oTmpAdsSearch->bShowCheckboxes = true;
			$sAdminSideAds = $oTmpAdsSearch->displayResultBlock();
			$sAdminSideAds = ($oTmpAdsSearch->aCurrent['paginate']['totalNum'] == 0) ? MsgBox(_t('_Empty')) : $sAdminSideAds;

			$sAdmPanel = $oTmpAdsSearch->showAdminActionsPanel('ads_box', array('action_approve' => '_Approve', 'action_disapprove' => '_Disapprove', 'action_delete' => '_Delete'), 'ads');

			// Prepare link to pagination
			$sRequest = $this->sCurrBrowsedFile . '?page={page}&per_page={per_page}';
			// End of prepare link to pagination
			$oTmpAdsSearch->aCurrent['paginate']['page_url'] = $sRequest;
			$sPostPagination = $oTmpAdsSearch->showPagination();

			$sPostsBox = $sAdminSideAds . '<div class="clear_both"></div>' . $sPostPagination;

			return <<<EOF
<form action="{$this->sCurrBrowsedFile}" method="post" name="ads_moderation">
	<div id="ads_box">
		{$sPostsBox}
	</div>
	{$sAdmPanel}
</form>
EOF;
		}
	}

	function GenReportSubmitForm($iCommentID) {
		if ($iCommentID ) {
			$iClsID = (int)bx_get('clsID');
			$sMessageC = _t("_Message text");

            $sCurrBrowsedFile = $this->sHomeUrl . 'classifieds.php';
			return <<<EOF
<div class="mediaInfo">
	<form name="submitAction" method="post" action="{$sCurrBrowsedFile}">
		<input type="hidden" name="commentID" value="{$iCommentID}" />
		<input type="hidden" name="clsID" value="{$iClsID}" />
		<input type="hidden" name="action" value="post_report" />
		<div>{$sMessageC}</div>
		<div><textarea cols="30" rows="10" name="messageText"></textarea></div>
		<div>
			<input type="submit" size="15" name="send" value="Send" />
			<input type="reset" size="15" name="send" value="Reset" />
		</div>
	</form>
</div>
EOF;
		}
	}

	function ActionReportSubmit() {
		global $site;

		$iClsID = (int)bx_get('clsID');
		$iCommID = (int)bx_get('commentID');
		$aUser = getProfileInfo($this->_iVisitorID);

		$sMailHeader		= "From: {$site['title']} <{$site['email_notify']}>";
		$sMailParameters	= "-f{$site['email_notify']}";

		$sMessage = process_db_input(bx_get('messageText'), BX_TAGS_VALIDATE);

		$sMailHeader = "MIME-Version: 1.0\r\n" . "Content-type: text/html; charset=UTF-8\r\n" . $sMailHeader;
		$sMailSubject = $aUser['NickName'].' bad comment report';

		$sGenUrl = $this->genUrl($iClsID, '', 'entry', true);

		 $sMailBody    = "Hello,\n
					{$aUser['NickName']} bad classified comment (comm num {$iCommID}): <a href=\"{$sGenUrl}\">See it</a>\n
					{$sMessage}\n
					Regards";

		$sMail = $site['email_notify'];

		if (sendMail( $sMail, sMailSubject, nl2br($sMailBody), '', '', 'html')) {
			$sCode = '<div class="mediaInfo">'._t("_File info was sent").'</div>';
			return MsgBox($sCode);
		}
	}

	/**
	 * Compose result of searching Advertisements by Tag
	 *
	 * @param $sTag	selected tag string
	 * @return HTML result
	 */
	function PrintAdvertisementsByTag($sTag) {
		$sSiteUrl = BX_DOL_URL_ROOT;

		$sSafeTag = addslashes(trim(strtolower($sTag)));
		$sTagResultC = _t('_bx_ads_search_results_by_tag');
		$sBrowseAllAds = _t('_bx_ads_Browse_All_Ads');

		$sHomeLink = ($this->bUseFriendlyLinks) ? BX_DOL_URL_ROOT . 'ads/' : "{$this->sCurrBrowsedFile}?Browse=1";

		$sBreadCrumbs = <<<EOF
<a href="{$sHomeLink}">{$sBrowseAllAds}</a> / {$sTagResultC} - {$sSafeTag}
EOF;

		require_once( $this->_oConfig->getClassPath() . 'BxAdsSearchUnit.php');
		$oTmpAdsSearch = new BxAdsSearchUnit();
		if ($iRandLim>0) {
			$oTmpAdsSearch->aCurrent['paginate']['perPage'] = (int)$iRandLim;
		} else {
			$oTmpAdsSearch->aCurrent['paginate']['perPage'] = 10;
		}
		$oTmpAdsSearch->aCurrent['sorting'] = 'last';
		$oTmpAdsSearch->aCurrent['restriction']['tag']['value'] = $sSafeTag;

        $sAdsByTags = $oTmpAdsSearch->displayResultBlock();
        if ($oTmpAdsSearch->aCurrent['paginate']['totalNum'] == 0) {
            $sAdsByTags = MsgBox(_t('_Empty'));
        } else {
    		// Prepare link to pagination
            $sSafeTagS = title2uri($sSafeTag);
    		if ($this->bUseFriendlyLinks==false) {
    			$sRequest = $this->sHomeUrl . "classifieds_tags.php?tag={$sSafeTagS}&page={page}&per_page={per_page}";
    		} else {
    			$sRequest = BX_DOL_URL_ROOT . "ads/tag/{$sSafeTagS}/{per_page}/{page}";
    		}
    		// End of prepare link to pagination
    		$oTmpAdsSearch->aCurrent['paginate']['page_url'] = $sRequest;
    		$sAdsByTags .= $oTmpAdsSearch->showPagination();
    		//$sAdsByTags = $GLOBALS['oFunctions']->centerContent ($sAdsByTags, '.ads_Unit');
        }

		return DesignBoxContent($sBreadCrumbs, $sAdsByTags, 1);
	}

	function GenAdsCalendar() {
		$aDateParams = array();
		$sDate = bx_get('date');
		if ($sDate) {
			$aDateParams = explode('/', $sDate);
		}

		require_once( $this->_oConfig->getClassPath() . 'BxAdsCalendar.php');
        $oCalendar = new BxAdsCalendar((int)$aDateParams[0], (int)$aDateParams[1], $this);
        $sAdsCalendar = $oCalendar->display();

		return DesignBoxContent(_t('_bx_ads_Calendar'), $sAdsCalendar, 1);
	}

	function GenAdsByDate() {
		$sCode = MsgBox(_t('_Empty'));

		$sDate = bx_get('date');
		$aDate = explode('/', $sDate);

		$iValue1 = (int)$aDate[0];
		$iValue2 = (int)$aDate[1];
		$iValue3 = (int)$aDate[2];

		if ($iValue1 > 0 && $iValue2 > 0 && $iValue3 > 0) {

			$sCaption = _t('_bx_ads_caption_browse_by_day') 
			    . getLocaleDate( strtotime("{$iValue1}-{$iValue2}-{$iValue3}"), BX_DOL_LOCALE_DATE_SHORT);

			require_once( $this->_oConfig->getClassPath() . 'BxAdsSearchUnit.php');
			$oTmpAdsSearch = new BxAdsSearchUnit();
			$oTmpAdsSearch->aCurrent['sorting'] = 'last';
			$oTmpAdsSearch->aCurrent['restriction']['calendar-min'] = array('value' => "UNIX_TIMESTAMP('{$iValue1}-{$iValue2}-{$iValue3} 00:00:00')", 'field' => 'DateTime', 'operator' => '>=', 'no_quote_value' => true);
			$oTmpAdsSearch->aCurrent['restriction']['calendar-max'] = array('value' => "UNIX_TIMESTAMP('{$iValue1}-{$iValue2}-{$iValue3} 23:59:59')", 'field' => 'DateTime', 'operator' => '<=', 'no_quote_value' => true);
			$sLastAds = $oTmpAdsSearch->displayResultBlock();
            if ($oTmpAdsSearch->aCurrent['paginate']['totalNum'] == 0) {
                $sLastAds = MsgBox(_t('_Empty'));
            } else {
        		$oTmpAdsSearch->aCurrent['paginate']['page_url'] = $sRequest;
        		$sLastAds .= $oTmpAdsSearch->showPagination();
        		//$sLastAds = $GLOBALS['oFunctions']->centerContent($sLastAds, '.ads_Unit'); 
            }

			$sRetHtmlVal = <<<EOF
<div class="dbContent">
	{$sLastAds}
</div>
EOF;
			return DesignBoxContent($sCaption, $sRetHtmlVal, 1);
		}
	}

	function ActionDeletePicture() {
		$iMediaID = (int)bx_get('dimg');
		$iEditAdvertisementID = (int)bx_get('EditPostID');

		$iAdvOwner = $this->_oDb->getOwnerOfAd($iEditAdvertisementID);

        if (!$this->isAllowedEdit($iAdvOwner)) {
            return $this->_oTemplate->displayAccessDenied();
        }

		if ($iEditAdvertisementID > 0) {
			//1. get media array
			$aAdvData = $this->_oDb->getAdInfo($iEditAdvertisementID);
			$sMediaIDs = $aAdvData['Media'];

			if ($sMediaIDs != '') {
                $aChunks = explode(',', $sMediaIDs);

				//2. don`t get deleted element
				$aNewMediaIDs = array();
				foreach ( $aChunks as $iMedId ) {
					if ($iMedId != $iMediaID) $aNewMediaIDs[] = $iMedId;
				}

				//3. collect new array of media
				$sNewMedia = implode(",", $aNewMediaIDs);

				//4. update field Media in classifieds with new array of media
				if ($this->_oDb->updatePostMedia($iEditAdvertisementID, $sNewMedia)) {
					//5. physycally delete file
					$sMediaFileName = $this->_oDb->getMediaFile($iMediaID);
					if ($sMediaFileName != '') {
						@unlink (BX_DIRECTORY_PATH_ROOT.$this->sUploadDir . 'img_'.$sMediaFileName );
						@unlink (BX_DIRECTORY_PATH_ROOT.$this->sUploadDir . 'thumb_'.$sMediaFileName );
						@unlink (BX_DIRECTORY_PATH_ROOT.$this->sUploadDir . 'big_thumb_'.$sMediaFileName );
						@unlink (BX_DIRECTORY_PATH_ROOT.$this->sUploadDir . 'icon_'.$sMediaFileName );
					}
					//6. delete record from table with media of Classifieds about deleted object
					if ($this->_oDb->deleteMedia($iMediaID)) {//continue
						return MsgBox(_t('_Photo successfully deleted'));
					}
				} else return MsgBox(_t('_Error Occured'));
			}
		} else {
			return MsgBox(_t('_Error Occured'));
		}
	}

    function _blockPhoto(&$aReadyMedia, $iAuthorId, $sPrefix = false) {
        if (!$aReadyMedia)
            return '';

        $aVars = array (
            'image_url' => false,
            'title' => false,
            'prefix' => $sPrefix ? $sPrefix : 'id'.time().'_'.rand(1, 999999), 
            'bx_repeat:images_icons' => array (),
        );

        foreach ($aReadyMedia as $sMedId) {
            $iMedId = (int)$sMedId;
            if (is_numeric($iMedId) && $iMedId) {
                $a = array ('ID' => $iAuthorId, 'Avatar' => $iMedId);

                $aSqlRes = $this->_oDb->getMediaInfo($iMedId);
                if ($aSqlRes) {
                    $aImageIcon = array(
                        'title' => $aSqlRes['MediaFile'],
                        'file' => BX_DOL_URL_ROOT . $this->sUploadDir.'icon_'.$aSqlRes['MediaFile']
                    );
                    $aImageFile = array(
                        'title' => $aSqlRes['MediaFile'],
                        'file' => BX_DOL_URL_ROOT . $this->sUploadDir.'img_'.$aSqlRes['MediaFile']
                    );
                }

                if (!$aVars['image_url']) {
                    $aVars['image_url'] = $aImageFile['file'];
                    $aVars['title'] = $aImageFile['title'];
                }

                $aVars['bx_repeat:images_icons'][] = array (
                    'icon_url' => $aImageIcon['file'],
                    'image_url' => $aImageFile['file'],
                    'title' => $aImageIcon['title'],
                );
            }
        }

        if (!$aVars['bx_repeat:images_icons'])
            return '';

        return $this->_oTemplate->parseHtmlByName('entry_view_block_images.html', $aVars);
    }

	/**
	* New implementation of Tags page
	*
	* @return html
	*/ 
	function GenTagsPage() {
        bx_import('BxTemplTagsModule');
        $aParam = array(
            'type' => 'ad',
            'orderby' => 'popular'
        );
        $oTags = new BxTemplTagsModule($aParam, _t('_all'), BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'tags');
        return $oTags->getCode();
	}

	function genUrl($iEntryId, $sEntryUri, $sType='entry', $bForce = false) {
		if ($bForce) {
			$sEntryUri = $this->_oDb->getAdUriByID($iEntryId);
		}

		$sMainUrl = BX_DOL_URL_ROOT;
		if ($this->bUseFriendlyLinks) {
			$sUrl = $sMainUrl."ads/{$sType}/{$sEntryUri}";
		} else {
			$sUrl = '';
			switch ($sType) {
				case 'entry':
					$sUrl = "{$this->sCurrBrowsedFile}?ShowAdvertisementID={$iEntryId}";
					break;
			}
		}
		return $sUrl;
	}

	/**
	* Ads block for index page (as PHP function). List of latest ads.
	*
	* @return html of last ads units
	*/ 
	function serviceAdsIndexPage() {
		require_once( $this->_oConfig->getClassPath() . 'BxAdsSearchUnit.php');
		$oTmpAdsSearch = new BxAdsSearchUnit();
		$oTmpAdsSearch->aCurrent['paginate']['perPage'] = 4;
		$oTmpAdsSearch->aCurrent['sorting'] = 'last';

	       //privacy changes
        if ($this->_iVisitorID) {
            $oTmpAdsSearch->aCurrent['restriction']['allow_view']['value'] = array(BX_DOL_PG_ALL, BX_DOL_PG_MEMBERS);
        } else {
            $oTmpAdsSearch->aCurrent['restriction']['allow_view']['value'] = array(BX_DOL_PG_ALL);
        }

        $sPostPagination = '';
		$sAllAds = $oTmpAdsSearch->displayResultBlock();
        if ($oTmpAdsSearch->aCurrent['paginate']['totalNum'] > 0) {
            $sPostPagination = $oTmpAdsSearch->showPagination2();
    		//$sAllAds = $GLOBALS['oFunctions']->centerContent($sAllAds, '.ads_Unit'); 

    		$aMenu = $oTmpAdsSearch->displayMenu();
    		return array($sAllAds, $aMenu[0], $sPostPagination);
        }
	}

	/**
	* Ads block for profile page (as PHP function). List of latest ads of member.
	*
	* @param $_iProfileID - member id
	*
	* @return html of last ads units
	*/
	function serviceAdsProfilePage($_iProfileID) {
        $GLOBALS['oTopMenu']->setCurrentProfileID($_iProfileID);

		require_once( $this->_oConfig->getClassPath() . 'BxAdsSearchUnit.php');
		$oTmpAdsSearch = new BxAdsSearchUnit();
		$oTmpAdsSearch->aCurrent['paginate']['perPage'] = 10;
		$oTmpAdsSearch->aCurrent['sorting'] = 'last';
		$oTmpAdsSearch->aCurrent['restriction']['owner']['value'] = $_iProfileID;
		$sMemberAds = $oTmpAdsSearch->displayResultBlock();

        if ($oTmpAdsSearch->aCurrent['paginate']['totalNum']) {
    		//$sMemberAds = $GLOBALS['oFunctions']->centerContent($sMemberAds, '.ads_Unit'); 
            return <<<EOF
<div class="clear_both"></div>
<div class="dbContent">
	{$sMemberAds}
	<div class="clear_both"></div>
</div>
EOF;
        }
	}

	/*
	* will need prepare array for BxDolRssFactory with params is:
	* 1. $aRssData, fields:
		'UnitID'
		'OwnerID'
		'UnitTitle'
		'UnitUri'
		'UnitDesc'
		'UnitDateTimeUTS'
		'UnitIcon'
	*/
	/**
	* Printing of member`s ads rss feeds
	*
	* @param bx_get('pid') - member id
	*
	* @return html of ads units of member
	*/
	function serviceAdsRss() {
		$iPID = (int)bx_get('pid');
		$aRssUnits = $this->_oDb->getMemberAdsRSS($iPID);
		if (is_array($aRssUnits) && count($aRssUnits)>0) {

			foreach ($aRssUnits as $iUnitID => $aUnitInfo) {
				$sPostLink = '';
				$iPostID = (int)$aUnitInfo['UnitID'];
				$sGenUrl = $this->genUrl($iPostID, $aUnitInfo['UnitUri']);

				$aRssUnits[$iUnitID]['UnitLink'] = $sPostLink;
			}

			$sUnitTitleC = _t('_bx_ads_Ads');
			$sMainLink = 'modules/boonex/ads/classifieds.php';

			bx_import('BxDolRssFactory');
			$oRssFactory = new BxDolRssFactory();

			//header('Content-Type: text/xml; charset=utf-8');
			echo $oRssFactory->GenRssByData($aRssUnits, $sUnitTitleC, $sMainLink);
		}
	}

	/**
	* Get common ads css
	*
	* @return html with css link
	*/
	function serviceGetCommonCss($bText = false) {
		return $this -> _oTemplate -> addCss('ads.css', $bText);
	}

    /*
    * Service - response profile delete
    */
    function serviceResponseProfileDelete ($oAlert) {
        if (!($iProfileId = (int)$oAlert->iObject))
            return false;
        $this->bAdminMode = true;
        $this->DeleteProfileAdvertisement($iProfileId);
        return true;
    }

	/**
	* Get Spy data
	*
	* @returm array of necessary parameters
	*/
	function serviceGetSpyData() {
		return array(
			'handlers' => array(
				array('alert_unit' => 'ads', 'alert_action' => 'create', 'module_uri' => 'ads', 'module_class' => 'Module', 'module_method' => 'get_spy_post'),
                array('alert_unit' => 'ads', 'alert_action' => 'rate', 'module_uri' => 'ads', 'module_class' => 'Module', 'module_method' => 'get_spy_post')
			),
			'alerts' => array(
				array('unit' => 'ads', 'action' => 'create'),
				array('unit' => 'ads', 'action' => 'rate')
			)
		);
	}

	/**
	* Get Spy ad unit
	*
	* $sAction - name of accepted action
	* $iObjectId - object id
	* $iSenderId - sender id
	*
	* @returm array of necessary parameters
	*/
    function serviceGetSpyPost($sAction, $iObjectId = 0, $iSenderId = 0, $aExtraParams = array()) {
        $aRet = array();

        switch($sAction) {
            case 'create' :
                $aPostInfo = $this->_oDb->getAdInfo($iObjectId);
                if($aPostInfo['IDProfile']) {
                    $sRecipientNickName     = getNickName($aPostInfo['IDProfile']);
                    $sRecipientProfileLink  = getProfileLink($aPostInfo['IDProfile']);
                    $sSenderNickName        = $iSenderId ? getNickName($iSenderId) : _t('_Guest');
                    $sSenderProfileLink     = $iSenderId ? getProfileLink($iSenderId) : 'javascript:void(0)';
                    $sCaption = $aPostInfo['Subject'];
                    $sEntryUrl = $this->genUrl($iObjectId, $aPostInfo['EntryUri'], 'entry');

                    $aRet = array(
                        'lang_key'  => '_bx_ads_added_spy',
                        'params'    => array(
                            'recipient_p_link' => $sRecipientProfileLink,
                            'recipient_p_nick' => $sRecipientNickName,
                            'profile_nick'     => $sSenderNickName,
                            'profile_link'     => $sSenderProfileLink,
                            'ads_url'          => $sEntryUrl,
                            'ads_caption'      => $sCaption,
                        ),
                        'recipient_id'     => 0,
                        'spy_type'         => 'content_activity', 
                    );
                }
                break;

            case 'rate' :
                $aPostInfo = $this->_oDb->getAdInfo($iObjectId);
                if($aPostInfo['IDProfile']) {
                    $sRecipientNickName     = getNickName($aPostInfo['IDProfile']);
                    $sRecipientProfileLink  = getProfileLink($aPostInfo['IDProfile']);
                    $sSenderNickName        = $iSenderId ? getNickName($iSenderId) : _t('_Guest');
                    $sSenderProfileLink     = $iSenderId ? getProfileLink($iSenderId) : 'javascript:void(0)';
                    $sCaption = $aPostInfo['Subject'];
                    $sEntryUrl = $this->genUrl($iObjectId, $aPostInfo['EntryUri'], 'entry');

                    $aRet = array(
                        'lang_key'  => '_bx_ads_rated_spy',
                        'params'    => array(
                            'recipient_p_link' => $sRecipientProfileLink,
                            'recipient_p_nick' => $sRecipientNickName,
                            'profile_nick'     => $sSenderNickName,
                            'profile_link'     => $sSenderProfileLink,
                            'ads_url'          => $sEntryUrl,
                            'ads_caption'      => $sCaption,
                        ),
                        'recipient_id'     => $aPostInfo['OwnerID'],
                        'spy_type'         => 'content_activity', 
                    );
                }
                break;
        }

        return $aRet;
    }

    function serviceGetWallData(){
        return array(
            'handlers' => array(
                array(
                    'alert_unit' => 'ads', 
                    'alert_action' => 'create', 
                    'module_uri' => 'ads', 
                    'module_class' => 'Module', 
                    'module_method' => 'get_wall_post'
                )
            ),
            'alerts' => array(
                array('unit' => 'ads', 'action' => 'create')
            )
        );
    }

    function serviceGetWallPost($aEvent) {
        $sAddedNewTxt = _t('_bx_ads_wall_added_new');
        $sPostTxt = _t('_bx_ads_wall_photo');

        $iObjectID = (int)$aEvent['object_id'];

        $aOwner = db_assoc_arr("SELECT `ID` AS `id`, `NickName` AS `username` FROM `Profiles` WHERE `ID`='" . (int)$aEvent['owner_id'] . "' LIMIT 1");

        $sTitle = $aOwner['username'] . ' ' . $sAddedNewTxt . ' ' . $sPostTxt;
        $aPostInfo = $this->_oDb->getAdInfo($iObjectID);
        $sCaption = $aPostInfo['Subject'];
        $sEntryUrl = $this->genUrl($iObjectID, $aPostInfo['EntryUri'], 'entry');

        $sCss = '';        
        if($aEvent['js_mode'])
            $sCss = $this->_oTemplate->addCss('wall_post.css', true);
        else 
            $this->_oTemplate->addCss('wall_post.css');

        $aVars = array(
                'cpt_user_name' => $aOwner['username'],
                'cpt_added_new' => $sAddedNewTxt,
                'cpt_object' => $sPostTxt,
                'cpt_item_url' => $sEntryUrl,
                'post_id' => $aEvent['id'],
        );
        return array(
            'title' => $sTitle,
            'description' => $sTitle,
            'content' => $sCss . $this->_oTemplate->parseHtmlByName('wall_post.html', $aVars)
        );
    }

}

?>
