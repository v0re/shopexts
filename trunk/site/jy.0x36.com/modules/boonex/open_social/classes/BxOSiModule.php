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

bx_import('BxDolPageView');
bx_import('BxDolModule');

//Boonex Opensocial applications container module
class BxOSiModule extends BxDolModule {

	// Variables
	var $_iProfileID;
	var $_bAdminMode;

	// Constructor
	function BxOSiModule($aModule) {
		parent::BxDolModule($aModule);

		$this->_bAdminMode = (isAdmin()) ? true : false;
	}

	function serviceGenCustomOSiBlock($iID) {
		return $this->GenCustomOSiBlock($iID);
	}

	function GenCustomOSiBlock($iID) {
		if ($iID < 1) return;

		$this->_iProfileID = $iID;
		$iVisitorID = (int)$_COOKIE['memberID'];

		$bAjaxMode = (false !== bx_get('mode') && bx_get('mode') == 'ajax') ? true : false;
		$bAjaxMode2 = (false !== bx_get('mode') && bx_get('mode') == 'ajax2') ? true : false;

		$sLoadingC = _t('_loading ...');

		$sHomeUrl = $this->_oConfig->getHomeUrl();
		$sLoadingIcon = getTemplateImage('loading.gif');
		$sManagingForm = $sApplsContent = $sAddingForm = $sCopyForm = $sAddingApplicationsBox = '';
		$sActiveApplList = $sApplAggr = '';

		//Generation of managing form
		if ($iVisitorID>0 && $iVisitorID == $this->_iProfileID) { // is owner
			$sAddC = _t('_Submit');
			$sEditC = _t('_Edit');
			$sAddNewURLC = _t('_Enter new URL');
			$sURLC = _t('_URL');
			$sDescriptionC = _t('_Description');

			$sAction = bx_get('action');

			if (isset($sAction) && $sAction != '') {
				$sNewUrl = process_db_input(bx_get('application_url'), 1);
				$iOldID = (int)bx_get('application_id');

				switch ($sAction) {
					case 'add_application':
						$sSubAction = ($this->_bAdminMode && ! defined('BX_PROFILE_PAGE')) ? 'adminaddapp' : 'addapp';
						bx_file_get_contents(BX_DOL_URL_ROOT . "modules/boonex/open_social/integration/html/profile/{$sSubAction}/{$iVisitorID}?appUrl={$sNewUrl}");

						if ($this->_bAdminMode && ! defined('BX_PROFILE_PAGE')) {
							header("Location: " . BX_DOL_URL_ROOT . 'modules/boonex/open_social/post_mod_os.php');
						} else {
                            $sProfileLink = getProfileLink($iVisitorID);
							header("Location: " . $sProfileLink);
						}
						break;
					case 'copy_admin_application':
							$sOSiSQL = "
								INSERT INTO `bx_osi_main` (`person_id`, `url`, `description`, `status`, `title`, `directory_title`, `author`, `author_email`, `settings`, `views`, `version`, `height`, `scrolling`, `modified`, `screenshot`, `thumbnail`)
								SELECT '{$iVisitorID}', `url`, `description`, `status`, `title`, `directory_title`, `author`, `author_email`, `settings`, `views`, `version`, `height`, `scrolling`, `modified`, `screenshot`, `thumbnail`
								FROM `bx_osi_main` WHERE `ID` ='{$iOldID}'
							";
							db_res($sOSiSQL);

                            $sProfileLink = getProfileLink($iVisitorID);
							header("Location: " . $sProfileLink);
						break;
				}
			}

			$sWorkingFile = ($this->_bAdminMode) ? 'post_mod_os' : 'os';

			if ($this->_bAdminMode || getParam('users_can_upload_bx_open_social') == 'on') {
				$sFormOnsubmitCode = <<<EOF
sApplUrl = $('form#adding_custom_appl_form input[name=application_url]').val();
sAddAppLink = '{$sHomeUrl}{$sWorkingFile}.php?ID={$this->_iProfileID}&action=add_application&application_url=' + sApplUrl + '&mode=ajax';
getHtmlData('custom_appl_lists_div', sAddAppLink, '', 'post');
return false;
EOF;

				//adding form
				$aForm = array(
					'form_attrs' => array(
						'name' => 'adding_custom_appl_form',
						'action' => bx_html_attribute($_SERVER['PHP_SELF']),
						'method' => 'post',
						/*'onsubmit' => $sFormOnsubmitCode,*/
					),
					'inputs' => array(
						'application_url' => array(
							'type' => 'text',
							'name' => 'application_url',
							'maxlength' => 255,
							'caption' => $sURLC,
							'info' => _t('_osi_applucation_url_desc'), //'http://www.matt.org/modules/GoogleClock.xml'
						),
						'hidden_action' => array(
							'type' => 'hidden',
							'name' => 'action',
							'value' => 'add_application',
						),
		                'hidden_visitor' => array(
		                    'type' => 'hidden',
		                    'name' => 'ID',
		                    'value' => $iVisitorID,
		                ),
						'add_button' => array(
							'type' => 'submit',
							'name' => 'submit',
							'caption' => '',
							'value' => $sAddC,
						),
					),
				);
				$oForm = new BxTemplFormView($aForm);
				$sAddingForm = $oForm->getCode();
			}

			if (defined('BX_PROFILE_PAGE')) {
				$aExistingApplications = $this->_oDb->getAdminsApplications();
				//adding admin applications form
				$aExForm = array(
		            'form_attrs' => array(
		                'name' => 'adding_custom_appl_form',
		                /*'action' => $sHomeUrl . $sWorkingFile . '.php',*/
		                'action' => bx_html_attribute($_SERVER['PHP_SELF']),
		                'method' => 'post',
		            ),
		            'inputs' => array(
						'header' => array(
							'type' => 'block_header',
							'caption' => _t('_osi_Existed_applications'),
						),
						'application_id' => array(
							'type' => 'select',
							'name' => 'application_id',
							'caption' => _t('_osi_Existed_applications'),
							'values' => $aExistingApplications,
						),
		                'hidden_action' => array(
		                    'type' => 'hidden',
		                    'name' => 'action',
		                    'value' => 'copy_admin_application',
		                ),
		                'hidden_action2' => array(
		                    'type' => 'hidden',
		                    'name' => 'ID',
		                    'value' => $iVisitorID,
		                ),
		                'add_ex_button' => array(
							'type' => 'submit',
							'name' => 'submit',
							'caption' => '',
							'value' => $sAddC,
		                ),
		            ),
		        );
		        $oExForm = new BxTemplFormView($aExForm);
		        $sCopyForm = $oExForm->getCode();
			}

			// adding applications form
			$sEmptySettingsDiv = <<<EOF
<div class="dbContent">
    <script language="javascript" type="text/javascript" src="{$sHomeUrl}js/main.js"></script>
    <div id="manage_applications_block" class="OsApplCont_0" >
    	{$sAddingForm}
    	{$sCopyForm}
    </div>
</div>
EOF;
            if (defined('BX_OS_ADMIN')) {
                $sAddingApplicationsBox = DesignBoxAdmin(_t('_osi_Settings'), $sEmptySettingsDiv);
            } else {
                $sAddingApplicationsBox = DesignBoxContent(_t('_osi_Settings'), $sEmptySettingsDiv, 1);
            }
		}

		$sPreferSpeed = $this->_oConfig->getAnimationSpeed();

		//if ($this->_bAdminMode == false) {
			// Collect all applications with settings in designbox modes
			$aMemberApplications = ($iVisitorID == $this->_iProfileID) ? $this->_oDb->getProfileApplications($this->_iProfileID) : $this->_oDb->getActiveProfileApplications($this->_iProfileID);
			foreach($aMemberApplications as $sKey => $aApplInfo) {
				$iAppliID = (int)$aApplInfo['ID'];
				$iPersonID = (int)$aApplInfo['person_id'];
                if ($iAppliID>0 && $iPersonID>0) {
    				$sOSiUrl = $aApplInfo['url'];
    				$sOSiTitle = $aApplInfo['title'];

    				$sApplFormDiv = $sAction = '';
    				if ($iVisitorID>0 && $iVisitorID == $this->_iProfileID) {
                        $sAction = BxDolPageView::getBlockCaptionMenu(mktime(), array(
                            'crss_t1' => array('href' => bx_html_attribute($_SERVER['PHP_SELF']), 'title' => $sEditC, 'onclick' => "ToggleAppSettings('{$sPreferSpeed}', {$iAppliID}); return false;")
                        ));

    					$sApplForm = bx_file_get_contents(BX_DOL_URL_ROOT . "modules/boonex/open_social/integration/html/profile/member_app_sett_by_id/{$iVisitorID}/{$iAppliID}");
    					$sApplFormDiv = <<<EOF
<div id="manage_applications_block" class="OsApplSettCont_{$iAppliID}" style="display:none;" >
	{$sApplForm}
</div>
EOF;
    				}

    				$sOSiUrlValue = bx_file_get_contents(BX_DOL_URL_ROOT . "modules/boonex/open_social/integration/html/appl/show/{$this->_iProfileID}/{$iAppliID}/1");

    				$sOSiUrlDiv = <<<EOF
<div class="OsApplCont_{$iAppliID}" style="" >
	{$sOSiUrlValue}
</div>
EOF;
    				$sOSiUrlValue = DesignBoxContent($sOSiTitle, $sOSiUrlDiv . $sApplFormDiv, 1, $sAction);

    				$sActiveApplList .= <<<EOF
<div class="OsApplAggrCont_{$iAppliID}" >
	<div class="clear_both"></div>
	{$sOSiUrlValue}
	<div class="clear_both"></div>
</div>
EOF;
                }
            }
			$sApplsContent = $sActiveApplList;

			$aFormVariables = array (
				'member_appl_list' => $sApplsContent,
				'member_appl_js_aggr' => $sApplAggr,
			);
			$sReadyApplContent = $this->_oTemplate->parseHtmlByTemplateName('member_osi_list_loaded', $aFormVariables);

			if ($bAjaxMode2) {
				echo $sReadyApplContent;
				exit;
			}
		//}

		// get common style-js header
		$sOSiHeaderOnce = bx_file_get_contents(BX_DOL_URL_ROOT . "modules/boonex/open_social/integration/html/appl/header_once/{$this->_iProfileID}");

		$aFormVariables = array (
			'view_css' => $this->_oTemplate->getCssUrl('view.css'),
			'main_js_url' => $sHomeUrl . 'js/main.js',
			'member_osi_list_loaded' => $sReadyApplContent
		);
		$sBlockContent = $this->_oTemplate->parseHtmlByTemplateName('view', $aFormVariables);

		return $sAddingApplicationsBox . $sOSiHeaderOnce . $sBlockContent;
	}

	// first usage  - for admin panel (moderation)
	function getApplicationUnits() {
		require_once( $this->_oConfig->getClassPath() . 'BxOSiSearchUnit.php');
		$oTmpAppSearch = new BxOSiSearchUnit($this->_oConfig, $this->_oTemplate);
		$oTmpAppSearch->aCurrent['paginate']['perPage'] = 10;
		$oTmpAppSearch->aCurrent['sorting'] = 'last';
		$sAppList = $oTmpAppSearch->displayResultBlock();
		if ($oTmpAppSearch->aCurrent['paginate']['totalNum'] == 0) return '';

		// Prepare link to pagination
		$sRequest = bx_html_attribute($_SERVER['PHP_SELF']) . '?page={page}&per_page={per_page}';
		// End of prepare link to pagination

		$oTmpAppSearch->aCurrent['paginate']['page_url'] = $sRequest;
		$sAppList .= $oTmpAppSearch->showPagination();

		return $sAppList;
	}

	// return admin applications in array
	function serviceGetAdminApplications() {
		return $this->_oDb->getAdminsApplications();
	}

	function serviceGenApplication($iAppID) {
		$iVisitorID = (int)$_COOKIE['memberID'];

		$aApplInfo = $this->_oDb->getApplicationInfo($iAppID);
		if ($aApplInfo['status'] == 'active') {
            $sJs = $this->_oTemplate->addJs('main.js', true);

			$sOSiUrlValue = bx_file_get_contents(BX_DOL_URL_ROOT . "modules/boonex/open_social/integration/html/appl/show/0/{$iAppID}/1");
			return $sJs . $sOSiUrlValue;
		}
	}
}

?>