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

bx_import('BxDolMenu');

class BxBaseMenu extends BxDolMenu {
	var $iElementsCntInLine;

	var $sSiteUrl;

	var $iJumpedMenuID;

	var $sCustomSubIconUrl;
	var $sCustomSubHeader;
	var $sCustomActions;

	var $sBreadCrumb;

	var $bDebugMode;
	
	var $sWidth;

	function BxBaseMenu() {
		BxDolMenu::BxDolMenu();
		$this->iElementsCntInLine = (int)getParam('nav_menu_elements_on_line_' . (isLogged() ? 'usr' : 'gst'));
		
		$this->sSiteUrl = BX_DOL_URL_ROOT;
		$this->iJumpedMenuID = 0;
		$this->sCustomSubIconUrl = '';
		$this->sCustomSubHeader = '';
		$this->sCustomActions = '';

		$this->sBreadCrumb = '';

		$this->bDebugMode = false;
		
		$this->sWidth = $GLOBALS['oSysTemplate']->getPageWidth();
	}

	function setCustomSubIconUrl($sCustomSubIconUrl) {
		$this->sCustomSubIconUrl = $sCustomSubIconUrl;
	}

	function setCustomSubHeader($sCustomSubHeader) {
		$this->sCustomSubHeader = $sCustomSubHeader;
	}

	function setCustomSubActions2($aCustomActions) {
		if (is_array($aCustomActions) && count($aCustomActions) > 0) {
			$sActions = '';
			foreach ($aCustomActions as $iID => $aCustomAction) {
				$sTitle = $sLink = $sIcon = '';
				$sTitle = $aCustomAction['title'];
				$sLink = $aCustomAction['url'];
				$sIcon = $aCustomAction['icon'];

				$sActions .= <<<EOF
<div class="button_wrapper" style="width:48%;margin-right:1%;margin-left:1%;" onclick="window.open ('{$sLink}','_self');">
	<img alt="{$sTitle}" src="{$sIcon}" style="float:left;" />
	<input class="form_input_submit" type="submit" value="{$sTitle}" class="menuLink" />
	<div class="button_wrapper_close"></div>
</div>
EOF;
			}

			$sCustomActions = <<<EOF
<div class="menu_user_actions">
	<div class="actionsBlock">
		{$sActions}
	</div>
</div>
EOF;

			$this->sCustomActions = $sCustomActions;
		}
	}

	/*
	* Generate actions in submenu place at right.
	*/
    function setCustomSubActions(&$aKeys, $sActionsType, $bSubMenuMode = true) {
        
        if (!$sActionsType) {
            $this->sCustomActions = '';
            return;
        }

        // prepare all needed keys
        $aKeys['url']  			= $this->sSiteUrl;
		$aKeys['window_width'] 	= $this->oTemplConfig->popUpWindowWidth;
		$aKeys['window_height']	= $this->oTemplConfig->popUpWindowHeight;
		$aKeys['anonym_mode']	= $this->oTemplConfig->bAnonymousMode;

		// $aKeys['member_id']		= $iMemberID;
		// $aKeys['member_pass']	= getPassword($iMemberID);

		//$GLOBALS['oFunctions']->iDhtmlPopupMenu = 1;
        $sActions = $GLOBALS['oFunctions']->genObjectsActions($aKeys, $sActionsType, $bSubMenuMode);
		$this->sCustomActions = '<div class="menu_user_actions">' . $sActions . '</div>';
	}

	/*
	* Generate navigation menu source
	*/
	function getCode() {

        if (isset($GLOBALS['bx_profiler'])) $GLOBALS['bx_profiler']->beginMenu('Main Menu');

        $this->getMenuInfo();

		$this->genTopHeader();
		$this->genTopItems();
		$this->genTopFooter();

		if (!defined('BX_INDEX_PAGE')) {
			$this->genSubContHeader();

			$this->genSubMenus();
			$this->genSubContFooter();
		}

        if (isset($GLOBALS['bx_profiler'])) $GLOBALS['bx_profiler']->endMenu('Main Menu');

		return $this->sCode;
	}

	/*
	* Generate top header part
	*/
	function genTopHeader() {
		$iCurrent = $this->checkShowCurSub() ? 0 : $this->aMenuInfo['currentTop'];
		$this->sCode .= '<table class="topMenu" cellpadding="0" cellspacing="0" style="width:' . $this->sWidth . '"><tr>';
	}

	/*
	* Generate top menu elements
	*/
	function genTopItems() {
		$iCounter = 0;
		foreach( $this->aTopMenu as $iItemID => $aItem ) {
			if( $aItem['Type'] != 'top' )
				continue;
			if( !$this->checkToShow( $aItem ) )
				continue;
			if ($aItem['Caption'] == "{profileNick}" && $this->aMenuInfo['profileNick']=='') continue;

			$bActive = ( $iItemID == $this->aMenuInfo['currentTop'] );

			if ($bActive && $iCounter >= $this->iElementsCntInLine) {
				$this->iJumpedMenuID = $iItemID;
				break;
			}
			$iCounter++;
		}

		$iCounter = 0;
		foreach( $this->aTopMenu as $iItemID => $aItem ) {
			if( $aItem['Type'] != 'top' )
				continue;
			
			if( !$this->checkToShow( $aItem ) )
				continue;
			
            //generate
			list( $aItem['Link'] ) = explode( '|', $aItem['Link'] );

			$aItem['Link']    = $this->replaceMetas( $aItem['Link'] );
			$aItem['Onclick'] = $this->replaceMetas( $aItem['Onclick'] );

			$bActive = ( $iItemID == $this->aMenuInfo['currentTop'] );
			$bActive = ($aItem['Link']=='index.php' && $this->aMenuInfo['currentTop']==0) ? true : $bActive;

			if ($this->bDebugMode) print $iItemID . $aItem['Caption'] . '__' . $aItem['Link'] . '__' . $bActive . '<br />';

			$isBold = ($aItem['Icon'] != '') ? true : false;
			$sImage = ($aItem['Icon'] != '') ? $aItem['Icon'] : $aItem['Picture'];

			//Draw jumped element
            if ($this->iJumpedMenuID>0 && $this->iElementsCntInLine == $iCounter) {                
				$aItemJmp = $this->aTopMenu[$this->iJumpedMenuID];
			    list( $aItemJmp['Link'] ) = explode( '|', $aItemJmp['Link'] );
			    $aItemJmp['Link']    = $this->replaceMetas( $aItemJmp['Link'] );
                $aItemJmp['Onclick'] = $this->replaceMetas( $aItemJmp['Onclick'] );

				$bJumpActive = ( $this->iJumpedMenuID == $this->aMenuInfo['currentTop'] );
				$bJumpActive = ($aItemJmp['Link']=='index.php' && $this->aMenuInfo['currentTop']==0) ? true : $bJumpActive;

				$this->genTopItem(_t($aItemJmp['Caption']), $aItemJmp['Link'], $aItemJmp['Target'], $aItemJmp['Onclick'], $bJumpActive, $this->iJumpedMenuID, $isBold);

				if ($this->bDebugMode) print '<br />pre_pop: ' . $this->iJumpedMenuID . $aItemJmp['Caption'] . '__' . $aItemJmp['Link'] . '__' . $bJumpActive . '<br /><br />';
			}

			if ($this->iElementsCntInLine == $iCounter) {
				$this->GenMoreElementBegin();

				if ($this->bDebugMode) print '<br />more begin here ' . '<br /><br />';
			}

			if ($this->iJumpedMenuID>0 && $iItemID == $this->iJumpedMenuID) {
				//continue;
				if ($this->bDebugMode) print '<br />was jump out here ' . '<br /><br />';
			} else {
				if ($this->iElementsCntInLine > $iCounter) {
					$this->genTopItem(_t($aItem['Caption']), $aItem['Link'], $aItem['Target'], $aItem['Onclick'], $bActive, $iItemID, $isBold, $sImage);
				} else {
					$this->genTopItemMore(_t($aItem['Caption']), $aItem['Link'], $aItem['Target'], $aItem['Onclick'], $bActive, $iItemID);
				}
			}
			
			$iCounter++;
		}

		if ($this->iElementsCntInLine < $iCounter) {
			$this->GenMoreElementEnd();
		}
	}

	/*
	* Generate top footer part
	*/
	function genTopFooter() {
	    global $oSysTemplate;

		$sResult = '';
		$aFunctions = array('genMoreLanguagesElement', 'genMoreTemplatesElement', 'genSearchElement');
		foreach($aFunctions as $sFunction) {
    	    $aVariables = $this->$sFunction();
    		if(!empty($aVariables)) {
    		    $aVariables['right'] = empty($sResult) ? 'right' : '';
                $sResult = $oSysTemplate->parseHtmlByName('navigation_menu_item.html', $aVariables) . $sResult;
    		}
		}		

		$this->sCode .= $sResult . "</tr></table>";
	}

	/*
	* Generate search element
	*/
	function genSearchElement() {
        $sSearchC = process_line_output(_t('_Search'));
        ob_start();
?>
<script language="javascript">
$(document).ready( function() {
	$('#keyword').blur(function() {
			$('#keyword').removeClass();
			$('#keyword').addClass('input_main');
			if ('' == $('#keyword').val())
				$('#keyword').val('<?= $sSearchC ?>');
		}
	);
	$('#keyword').focus(function() {
			$('#keyword').removeClass();
			$('#keyword').addClass('input_focus');
			if ('<?= $sSearchC ?>' == $('#keyword').val())
				$('#keyword').val('');
		}
	);
});
</script>
<li>
	<div id="gse_search">
		<form action="searchKeyword.php" method="get" name="SearchForm">
			<input type="text" name="keyword" id="keyword" value="<?= $sSearchC ?>" class="input_main"/>
		</form>
	</div>
	<div class="clear_both"></div>
</li>
<?
        $sSearchElement = ob_get_clean();

		return array (
		    'icon_url' => getTemplateIcon('tm_item_search.png'),
		    'element_content' => $sSearchElement
		);
    }	

	/*
	* Generate templates element
	*/
	function genMoreTemplatesElement(){
		if(!getParam("enable_template")) 
            return;

		$aExistedTemplates = get_templates_array();
		if(count($aExistedTemplates) <= 1) 
            return;

		$sCurTemplate = strlen($_GET['skin']) ? $_GET['skin'] : $_COOKIE['skin'];

		$sTemplateElement = '';
		foreach ($aExistedTemplates as $sTemplateID => $sTemplateVal) {
			$sIActiveClass = ($sCurTemplate == $sTemplateID) ? ' active' : '';

			$sTemplateUrl = '';
			if ($sCurTemplate == $sTemplateID) {
				$sTemplateUrl = 'javascript: void(0)';
			} else {
                if (defined('BX_PROFILE_PAGE')) {
                    global $profileID;
                    $sTemplateUrl = getProfileLink($profileID) . '&skin='. $sTemplateID;
                } else {
                    $sGetTransfer = bx_encode_url_params($_GET, array('skin'));
    				$sTemplateUrl = bx_html_attribute($_SERVER['PHP_SELF']) . '?' . $sGetTransfer . 'skin='. $sTemplateID;
                }
			}

			//$sIOnclick = "window.open('{$sTemplateUrl}','_self');"; // old version
			$sTemplateElement .= '<li><a href="' . $sTemplateUrl . '" class="button more_ntop_element' . $sIActiveClass . '">'.$sTemplateVal.'</a>';
		}

		if($sTemplateElement == '')
            return;

		return array(
            'icon_url' => getTemplateIcon('tm_item_templates.png'),
            'element_content' => $sTemplateElement
		);
	}

	/*
	* Generate languages element
	*/
	function genMoreLanguagesElement(){
		$aExistedLanguages = getLangsArr();
		if(count($aExistedLanguages) <= 1) return;

		$sCurLanguage = strlen($_GET['lang']) ? $_GET['lang'] : $_COOKIE['lang'];

		$sLanguageElement = '';
		foreach ($aExistedLanguages as $sLanguageID => $sLanguageVal) {
            $sIActiveClass = ($sCurLanguage == $sLanguageID) ? ' active' : '';
			$sLanguageUrl = '';
			if ($sCurLanguage == $sLanguageID) {
				$sLanguageUrl = 'javascript: void(0)';
			} else {
				
				$sGetTransfer = bx_encode_url_params($_GET, array('lang'));
				$sLanguageUrl = bx_html_attribute($_SERVER['PHP_SELF']) . '?' . $sGetTransfer . 'lang='. $sLanguageID;
			}

			$sLanguageElement .= '<li><a href="' . $sLanguageUrl . '" value="' . $sLanguageVal . '" class="button more_ntop_element' . $sIActiveClass . '">'.$sLanguageVal.'</a>';
		}

		if ($sLanguageElement == '') return;

		return array(
            'icon_url' => getTemplateIcon('tm_item_languages.png'),
            'element_content' => $sLanguageElement
		);
	}

	/*
	* Generate sub container header
	*/
	function genSubContHeader() {
			$this->sCode .= '
				<div class="clear_both"></div>
				<div class="subMenusContainer" style="width:' . $this->sWidth . ';">';
	}

	/*
	* Generate sub container footer
	*/
	function genSubContFooter() {
			$this->sCode .= '
				</div>';
	}

	/*
	* Generate sub menu elements
	*/
	function genSubMenus() {
		foreach( $this->aTopMenu as $iTItemID => $aTItem ) {
			if( $aTItem['Type'] != 'top' && $aTItem['Type'] !='system')
				continue;

			if( !$this->checkToShow( $aTItem ) )
				continue;

			if( $this->aMenuInfo['currentTop'] == $iTItemID && $this->checkShowCurSub() )
				$sDisplay = 'block';
			else {
				$sDisplay = 'none';
				if ($aTItem['Caption']=='_Home' && $this->aMenuInfo['currentTop']==0)
					$sDisplay = 'block';
			}

			$sCaption = _t( $aTItem['Caption'] );
			$sCaption = $this->replaceMetas($sCaption);

			//generate
			if ($sDisplay == 'block') {
				$sPicture = $aTItem['Picture'];

				$iFirstID = $this->genSubFirstItem( $iTItemID );

				$this->genSubHeader( $iTItemID, $iFirstID, $sCaption, $sDisplay, $sPicture );
				$this->genSubItems( $iTItemID );
				$this->genSubFooter();
			}
		}
	}

	/*
	* Generate sub items of sub menu elements
	*/
    function genSubItems( $iTItemID = 0 ) {
        if( !$iTItemID )
            $iTItemID = $this->aMenuInfo['currentTop'];

		$sSubItems = '';
        foreach( $this->aTopMenu as $iItemID => $aItem ) {
            if( $aItem['Type'] != 'custom' )
                continue;
            if( $aItem['Parent'] != $iTItemID )
                continue;
            if( !$this->checkToShow( $aItem ) )
                continue;

            //generate
            list( $aItem['Link'] ) = explode( '|', $aItem['Link'] );

            $aItem['Link']    = $this->replaceMetas( $aItem['Link'] );
            $aItem['Onclick'] = $this->replaceMetas( $aItem['Onclick'] );

            $bActive = ( $iItemID == $this->aMenuInfo['currentCustom'] );

            $sSubItems .= $this->genSubItem( _t( $aItem['Caption'] ), $aItem['Link'], $aItem['Target'], $aItem['Onclick'], $bActive );
        }

		$this->sCode .= ($sSubItems=='') ? '<div class="subMenuContainerEmpty"></div>' : <<<EOF
			<div class="subMenuContainer">
				<table cellspacing="0" cellpadding="0"><tr>
					{$sSubItems}
				</tr></table>
			</div>
EOF;
    }

	/*
	* Generate top menu elements
	*/
    function genTopItem($sText, $sLink, $sTarget, $sOnclick, $bActive, $iItemID, $isBold = false, $sPicture = '') {
        
		$sActiveStyle = ($bActive) ? ' id="tm_active"' : '';

		if (!$bActive) {
			$sOnclick = $sOnclick ? ( ' onclick="' . $sOnclick . '"' ) : '';
			$sTarget  = $sTarget  ? ( ' target="'  . $sTarget  . '"' ) : '';
		}

		$sLink = (strpos($sLink, 'http://') === false && !strlen($sOnclick)) ? $this->sSiteUrl . $sLink : $sLink;

		$sMoreIcon = getTemplateIcon('tm_sitem_down.gif');

		$sSubMenu = $this->getAllSubMenus($iItemID);

		$sBoldStyle = ($isBold) ? 'style="font-weight:bold;"' : '';

		$sImgTabStyle = $sPictureRep = '';
		if ($isBold && $sPicture != '') {
			$sPicturePath = getTemplateIcon($sPicture);
			$sPictureRep = '<img src="' . $sPicturePath . '" />';

			$sText = '';
			$sImgTabStyle = 'style="width:38px;"';
		}

		$sMainSubs = ($sSubMenu=='') ? '' : <<<EOF
	<!--[if lte IE 6]><table id="mmm"><tr><td><![endif]-->
	<ul class="sub main_elements">
		{$sSubMenu}
		<li class="li_last_round">&nbsp;</li>
	</ul>
	<!--[if lte IE 6]></td></tr></table></a><![endif]-->
EOF;

		$this->sCode .= <<<EOF
<td class="top" {$sActiveStyle} {$sImgTabStyle}>
	<a href="{$sLink}" {$sOnclick} {$sTarget} class="top_link"><span class="down" {$sBoldStyle}>{$sPictureRep}{$sText}</span>
	<!--[if gte IE 7]><!--></a><!--<![endif]-->
	<div style="position:relative;display:block;">{$sMainSubs}</div>
</td>
EOF;
	}

	/*
	* Get parent of submenu element
	*/
	function genSubFirstItem( $iTItemID = 0 ) {
		if( !$iTItemID )
			$iTItemID = $this->aMenuInfo['currentTop'];

		foreach( $this->aTopMenu as $iItemID => $aItem ) {
			if( $aItem['Type'] != 'custom' )
				continue;
			
			if( $aItem['Parent'] != $iTItemID )
				continue;
			
			if( !$this->checkToShow( $aItem ) )
				continue;
			
			return $iItemID;
		}
	}

	/*
	* Generate header for sub items of sub menu elements
	*/
	function genSubHeader( $iTItemID, $iFirstID, $sCaption, $sDisplay, $sPicture = '' ) {
		$sLoginSection = $sSubElementCaption = $sProfStatusMessage = $sProfStatusMessageWhen = $sProfileActions = '';
		$sCaptionWL = $sProfStatusMessageEl = $sMiddleImg = '';

		if ($this->aMenuInfo['currentCustom'] == 0 && $iFirstID > 0) $this->aMenuInfo['currentCustom'] = $iFirstID;
		//comment need when take header for profile page
		if ($this->sCustomSubHeader == '' && $this->aMenuInfo['currentCustom'] > 0) {
			$sSubCapIcon = getTemplateIcon('_submenu_capt_right.gif');
			$sSubElementCaption = _t($this->aTopMenu[$this->aMenuInfo['currentCustom']]['Caption']);

			$sCustomPic = $this->aTopMenu[$this->aMenuInfo['currentCustom']]['Picture'];
			$sPicture = ($sCustomPic != '') ? $sCustomPic : $sPicture;

			$sMiddleImg = '<img src="'.$sSubCapIcon.'" />';
			$sSubElementCaption = <<<EOF
<font style="font-weight:normal;">{$sSubElementCaption}</font>
EOF;
		}

		if(!isMember())
			$sLoginSection = $GLOBALS['oSysTemplate']->ParseHtmlByName('login_join.html', array());

		/////Picture////////
		if ($this->sCustomSubHeader == '' && !empty($this->aMenuInfo['profileID'])) {
			$sPictureEl = get_member_icon($this->aMenuInfo['profileID'], 'left');

			$sSubCapIcon = getTemplateIcon('_submenu_capt_right.gif');
			$aProfInfo = getProfileInfo($this->aMenuInfo['profileID']);
			$sProfStatusMessage = process_line_output($aProfInfo['UserStatusMessage']);
			$sRealWhen = ($aProfInfo['UserStatusMessageWhen'] != 0) ? $aProfInfo['UserStatusMessageWhen'] : time();
			$sProfStatusMessageWhen = defineTimeInterval($sRealWhen);

			$isButtonNotify = false;
			if (defined('BX_PROFILE_PAGE') && $this->aMenuInfo['memberID'] == $this->aMenuInfo['profileID']) {
				$isButtonNotify = true;

				$sProfStatusMessage = ($sProfStatusMessage=='') ? _t('_Click_here_to_update_your_status') : $sProfStatusMessage . '<font style="color:#999;font-size:9px;margin-left:5px;">'.$sProfStatusMessageWhen.'</font>';
				$sProfStatusMessage = <<<EOF
<script language="JavaScript" type="text/javascript">
	function InloadStatusMessageEl() {
		$('#inloadedStatusMess').load("change_status.php?action=get_prof_status_mess");
		$('#StatusMessage').hide();
	}
</script>

<div id="StatusMessage" onclick="InloadStatusMessageEl(); return false;">{$sProfStatusMessage}</div>
<div id="inloadedStatusMess" style="float:left;"></div>
EOF;
			} elseif ($sProfStatusMessage != '') {

				$sProfStatusMessage = <<<EOF
<div id="StatusMessage">{$sProfStatusMessage}</div>
<div id="inloadedStatusMess" style="float:left;"></div>
EOF;

				$sProfileActions = $this->getProfileActions($aProfInfo, $this->aMenuInfo['memberID']);
			}

			if ($sProfStatusMessageWhen != '' && $sProfStatusMessage != '') {
				$sProfStatusMessageEl = $GLOBALS['oFunctions']->genNotifyMessage($sProfStatusMessage, 'left', $isButtonNotify);
			}
		} else {
			$sPicturePath = (isset($sPicture) && $sPicture!='') ? getTemplateIcon($sPicture) : '';
			$sPictureEl = ($sPicturePath == '') ? '' : <<<EOF
<img class="img_submenu" src="{$sPicturePath}" alt="" />
EOF;
		}

		$sPictureEl = ($this->sCustomSubIconUrl == '') ? $sPictureEl : <<<EOF
<img class="img_submenu" src="{$this->sCustomSubIconUrl}" alt="" />
EOF;
		/////Picture end////////

        if (true) { // $sSubElementCaption != '') {
            $aAllSubMainLinks = array();
            $sSubMainLinks = '';
            if (!empty($this->aTopMenu[$iFirstID]['Link'])) {
                list($aAllSubMainLinks) = explode('|', $this->aTopMenu[$iFirstID]['Link']);
                $sSubMainLinks = $this->replaceMetas($aAllSubMainLinks);

                if (empty($sSubMainLinks)) {
                    //try define the parent menu's item url
                    $sSubMainLinks = $this -> sSiteUrl . $this -> aTopMenu[ $this -> aMenuInfo['currentTop'] ]['Link'];
                }

                $sSubMainOnclick = $this->replaceMetas($this->aTopMenu[$iFirstID]['Onclick']);
            }

            $sSubMainOnclick = !empty($sSubMainOnclick) ? ' onclick="' . $sSubMainOnclick . '"' : '';

            $sCaption = <<<EOF
<a href="{$sSubMainLinks}" {$sSubMainOnclick}>{$sCaption}</a>
EOF;
            $sCaptionWL = $sCaption;
        }

		if ($this->sCustomSubHeader != '') {
			$sCaptionWL = $this->sCustomSubHeader;
		}

		if ($this->sCustomActions != '') {
			$sProfileActions = $this->sCustomActions;
		}

		// array of keys
		$aTemplateKeys = array (
			'submenu_id'      => $iTItemID,
			'display_value'   => $sDisplay,
			'picture'         => $sPictureEl,
			'sub_caption'     => $sCaptionWL,
			'profile_status'  => $sProfStatusMessageEl,
			'login_section'   => $sLoginSection,
			'profile_actions' => $sProfileActions,
			'injection_title_zone' => $sProfileActions,
		);
		$this->sCode .= $GLOBALS['oSysTemplate']->parseHtmlByName('navigation_menu_sub_header.html', $aTemplateKeys);

		$aBreadcrumb = array();

		if($iFirstID > 0 && $sCaption != '')
			$aBreadcrumb[] = $sCaption;		
		if($sSubElementCaption != '')
			$aBreadcrumb[] = $sSubElementCaption;

		$this->sBreadCrumb = $this->genBreadcrumb($aBreadcrumb);
	}

	function getProfileActions($p_arr, $iMemberID) {
        $iViewedMemberID = (int)$p_arr['ID'];

        if( (!$iMemberID  or !$iViewedMemberID) or ($iMemberID == $iViewedMemberID) )
			return null;

        // prepare all needed keys
        $p_arr['url']  			= $this->sSiteUrl;
		$p_arr['window_width'] 	= $this->oTemplConfig->popUpWindowWidth;
		$p_arr['window_height']	= $this->oTemplConfig->popUpWindowHeight;
		$p_arr['anonym_mode']	= $this->oTemplConfig->bAnonymousMode;

		$p_arr['member_id']		= $iMemberID;
		$p_arr['member_pass']	= getPassword( $iMemberID );

		$GLOBALS['oFunctions']->iDhtmlPopupMenu = 1;
        $sActions = $GLOBALS['oFunctions']->genObjectsActions($p_arr, 'Profile', true);
		return '<div class="menu_user_actions">' . $sActions . '</div>';
	}

	function genSubItem( $sCaption, $sLink, $sTarget, $sOnclick, $bActive ) {
		$sIcon1_a_l = getTemplateImage("tm_cm_item_left_act.png");		
		$sIcon1_a_c = getTemplateImage("tm_cm_item_center_act.png");
		$sIcon1_a_r = getTemplateImage("tm_cm_item_right_act.png");

		$sSubItems = '';
		if( !$bActive ) {
			$sOnclick = $sOnclick ? ( ' onclick="' . $sOnclick . '"' ) : '';
			$sTarget  = $sTarget  ? ( ' target="'  . $sTarget  . '"' ) : '';

			if ( strpos( $sLink, 'http://' ) === false && !strlen($sOnclick) )
				$sLink = $this->sSiteUrl . $sLink;

				$sSubItems .= <<<EOF
<td class="usual">
	<div><a class="sublinks" href="{$sLink}" {$sTarget} {$sOnclick}>{$sCaption}</a></div>
</td>
EOF;
			} else {
				$sSubItems .= <<<EOF
<td class="tabbed">
	<table cellspacing="0" cellpadding="0"><tr>
	<td><div style="background:url({$sIcon1_a_l}) no-repeat top left;width:9px;"></div></td>
	<td><div style="background:url({$sIcon1_a_c}) repeat-x top center;">{$sCaption}</div></td>
	<td><div style="background:url({$sIcon1_a_r}) no-repeat top right;width:9px;"></div></td>
	</tr></table>
</td>
EOF;
		}
		return $sSubItems;
	}

	/*
	* Generate footer for sub items of sub menu elements
	*/
	function genSubFooter() {
			$this->sCode .= <<<EOF
	</div>
</div>
EOF;
	}

	function getAllSubMenus($iItemID, $bActive = false) {
		$aMenuInfo = $this->aMenuInfo;

		$ret = '';
		
		$aTTopMenu = $this->aTopMenu;

		foreach( $aTTopMenu as $iTItemID => $aTItem ) {

			if( !$this->checkToShow( $aTItem ) )
				continue;

			if ($iItemID == $aTItem['Parent']) {
				//generate
				list( $aTItem['Link'] ) = explode( '|', $aTItem['Link'] );

				$aTItem['Link'] = str_replace( "{memberID}",    isset($aMenuInfo['memberID']) ? $aMenuInfo['memberID'] : '',    $aTItem['Link'] );
				$aTItem['Link'] = str_replace( "{memberNick}",  isset($aMenuInfo['memberNick']) ? $aMenuInfo['memberNick'] : '',  $aTItem['Link'] );
				$aTItem['Link'] = str_replace( "{memberLink}",  isset($aMenuInfo['memberLink']) ? $aMenuInfo['memberLink'] : '',  $aTItem['Link'] );

				$aTItem['Link'] = str_replace( "{profileID}",   isset($aMenuInfo['profileID']) ? $aMenuInfo['profileID'] : '',   $aTItem['Link'] );
				$aTItem['Onclick'] = str_replace( "{profileID}", isset($aMenuInfo['profileID']) ? $aMenuInfo['profileID'] : '',   $aTItem['Onclick'] );

				$aTItem['Link'] = str_replace( "{profileNick}", isset($aMenuInfo['profileNick']) ? $aMenuInfo['profileNick'] : '', $aTItem['Link'] );
				$aTItem['Onclick'] = str_replace( "{profileNick}", isset($aMenuInfo['profileNick']) ? $aMenuInfo['profileNick'] : '', $aTItem['Onclick'] );
									
				$aTItem['Link'] = str_replace( "{profileLink}", isset($aMenuInfo['profileLink']) ? $aMenuInfo['profileLink'] : '', $aTItem['Link'] );

				$aTItem['Onclick'] = str_replace( "{memberID}", isset($aMenuInfo['memberID']) ? $aMenuInfo['memberID'] : '',    $aTItem['Onclick'] );
				$aTItem['Onclick'] = str_replace( "{memberNick}",  isset($aMenuInfo['memberNick']) ? $aMenuInfo['memberNick'] : '',  $aTItem['Onclick'] );
				$aTItem['Onclick'] = str_replace( "{memberPass}",  getPassword( isset($aMenuInfo['memberID']) ? $aMenuInfo['memberID'] : ''),  $aTItem['Onclick'] );

				$sElement = $this->getCustomMenuItem( _t( $aTItem['Caption'] ), $aTItem['Link'], $aTItem['Target'], $aTItem['Onclick'], ( $iTItemID == $aMenuInfo['currentCustom'] ) );

				$ret .= $sElement;
			}
		}

		return $ret;
	}

	function getCustomMenuItem($sText, $sLink, $sTarget, $sOnclick, $bActive, $bSub = false) {
		//$sIActiveStyles = ($bActive) ? 'color:#333;font-weight:bold;' : '';
        $sIActiveClass = ($bActive) ? ' active' : '';
		$sITarget = (strlen($sTarget)) ? $sTarget : '_self';
		$sILink = (strpos($sLink, 'http://') === false && !strlen($sOnclick)) ? $this->sSiteUrl . $sLink : $sLink;
		//$sIOnclick = (strlen($sOnclick)) ? $sOnclick : "window.open('{$sILink}','{$sITarget}');"; //old version
		$sIOnclick = (strlen($sOnclick)) ? 'onclick="'.$sOnclick.'"' : '';

		return <<<EOF
<li>
    <a href="{$sILink}" {$sIOnclick} class="button more_ntop_element{$sIActiveClass}">{$sText}</a>
</li>
EOF;
	}

	function GenMoreElementBegin() {
		$sMoreIcon = getTemplateIcon("tm_sitem_down.gif");

		$sMoreMainIcon = getTemplateIcon('tm_item_more.png');

		$this->sCode .= <<<EOF
<td class="top" style="width:38px;">
	<a href="javascript: void(0);" onclick="void(0);" class="top_link">
		<span class="down"><img src="{$sMoreMainIcon}"/></span>
		<!--[if gte IE 7]><!--></a><!--<![endif]-->
		<!--[if lte IE 6]><table id="mmm"><tr><td><![endif]-->
		<div style="position:relative;display:block;">
        <ul class="sub">
EOF;
	}

	function genTopItemMore($sText, $sLink, $sTarget, $sOnclick, $bActive, $iItemID) {
		//$sIActiveStyles = ($bActive) ? 'color:#333;font-weight:bold;' : '';
        $sIActiveClass = ($bActive) ? ' active' : '';
		$sITarget = (strlen($sTarget)) ? $sTarget : '_self';
		$sILink = (strpos($sLink, 'http://') === false && !strlen($sOnclick)) ? $this->sSiteUrl . $sLink : $sLink;
		//$sIOnclick = (strlen($sOnclick)) ? $sOnclick : "window.open('{$sILink}','{$sITarget}');"; //old version
        $sIOnclick = (strlen($sOnclick)) ? 'onclick="'.$sOnclick.'"' : '';

		$sSubMenu = $this->getAllSubMenus($iItemID);

		$sActiveStyle = ($bActive) ? 'active' : '';
		$sActiveVisibleStyle = ($bActive) ? 'display:block;' : 'display:none;';
		$sSpacerIcon = getTemplateIcon( 'spacer.gif' );

		$this->iJSTempCnt++;

		if ($sSubMenu == '') {
			$this->sCode .= <<<EOF
<li class="{$sActiveStyle}">
	<a href="{$sILink}" {$sIOnclick} value="{$sText}" class="button more_ntop_element{$sIActiveClass}">{$sText}</a>
</li>
EOF;
		} else {
			$this->sCode .= <<<EOF
<li class="{$sActiveStyle}">
	<span class="more_down_tab_noimg" onclick="ChangeMoreMenu({$this->iJSTempCnt});return false;">
		<img class="more_right" src="{$sSpacerIcon}" id="more_img_{$this->iJSTempCnt}" />
	</span>
	<a href="{$sILink}" {$sIOnclick} value="{$sText}" class="button more_top_element{$sIActiveClass}" style="margin-left:0px;">{$sText}</a>
    <div class="clear_both"></div>
    	
	<ul style="{$sActiveVisibleStyle}" id="ul{$this->iJSTempCnt}" class="more_sub">
		{$sSubMenu}
	</ul>    
</li>
EOF;
		}
		$this->iJSTempCnt++;
	}

	function GenMoreElementEnd() {
		$this->sCode .= <<<EOF
    		<li class="li_last_round">&nbsp;</li>
    	</ul>
	</div>
	<div class="clear_both"></div>
	<!--[if lte IE 6]></td></tr></table></a><![endif]-->
</td>
EOF;
	}
	
    /*
	 * param is array of Path like
	 * $aPath[0] = '<a href="">XXX</a>'
	 * $aPath[1] = '<a href="">XXX1</a>'
	 * $aPath[2] = 'XXX2'
	 */
	function genBreadcrumb($aPath = array()) {
	    $sRootItem = '<a href="' . $this->sSiteUrl . '">' . _t('_Home') . '</a>';

        if (!empty($this->aCustomBreadcrumbs)) {
            $a = array();
            foreach ($this->aCustomBreadcrumbs as $sTitle => $sLink)
                if ($sTitle)
                    $a[] = $sLink ? '<a href="' . $sLink . '">' . $sTitle . '</a>' : $sTitle;
            $aPath = array_merge(array($sRootItem), $a);
        } elseif(!is_array($aPath) || empty($aPath)) {
            $aPath = array($sRootItem);
        } else {
            $aPath = array_merge(array($sRootItem), $aPath);
        }

        //define current url for single page (not contain any child pages)
        if( $this -> aMenuInfo['currentTop'] != -1 && count($aPath) == 1) {
        	$aPath[] =  _t($this -> aTopMenu[ $this -> aMenuInfo['currentTop'] ]['Caption']);
        }

        //--- Get breadcrumb path(left side) ---//
		$sDivider = '<img class="bc_divider" src="' . getTemplateImage('bc_divider.png') . '" />';
		$aPathLinks = array();
		foreach($aPath as $sLink)
			$aPathLinks[] = '<div class="bc_unit">' . $sLink . '</div>';
		$sPathLinks = implode($sDivider, $aPathLinks);
		
		//--- Get additional links(right side) ---//
		$sAddons = "";
		if(isMember()) {
		    $aProfile = getProfileInfo();		    

		    $sAddons = _t('_Hello member', $aProfile['NickName']);
		    $sAddons .= ' <a href="' . $this->sSiteUrl . 'member.php">' . _t('_sys_breadcrumb_account') . '</a>';
		    $sAddons .= ' <a href="' . $this->sSiteUrl . 'logout.php">' . _t('_sys_breadcrumb_logout') . '</a>';
		}
		else {
		    $sAddons = _t('_Hello member', _t('_sys_breadcrumb_guest'));
		    $sAddons .= ' <a href="' . $this->sSiteUrl . 'join.php">' . _t('_sys_breadcrumb_join') . '</a>';
		    $sAddons .= ' <a href="javascript:void(0)" onclick="showPopupLoginForm(); return false;">' . _t('_sys_breadcrumb_login') . '</a>';
		}

		return '<div class="breadcrumb"><div class="bc_open">&nbsp;</div>' . $sPathLinks . '<div class="bc_addons">' . $sAddons . '</div><div class="bc_close">&nbsp;</div></div>';
	}
}
?>
