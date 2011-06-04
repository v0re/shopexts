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

bx_import('BxDolPermalinks');
bx_import('BxTemplFormView');
bx_import('BxDolUserStatusView');
bx_import('BxDolModule');

class BxBaseFunctions {

    var	$aSpecialKeys;
	var $iDhtmlPopupMenu;

    function BxBaseFunctions() {
        $this -> aSpecialKeys = array('rate' => '', 'rate_cnt' => '');
		$this -> iDhtmlPopupMenu = ( getParam('enable_new_dhtml_popups') == 'on' ) ? 0 : 1;
    }

    function getProfileMatch( $memberID, $profileID ) {
		$match_n = getProfilesMatch($memberID, $profileID); // impl
		return DesignProgressPos ( _t("_XX match", $match_n), $GLOBALS['oTemplConfig']->iProfileViewProgressBar, 100, $match_n );;
    }

    function getProfileZodiac( $profileDate ) {
		return ShowZodiacSign( $profileDate );
    }

    function TemplPageAddComponent($sKey) {
        switch( $sKey ) {
            case 'something':
                return false; // return here additional components
            default:
                return false; // if you have not such component, return false!
        }
    }

	/**
	* Function will generate object's action link ;
	*
	* @param  		: $aObjectParamaters (array) contain special markers ;
	* @param  		: $aRow (array) links's info ;
	* @param  		: $sCssClass (string) additional css style ;
	* @return 		: Html presentation data ;
	*/
    function genActionLink( &$aObjectParamaters, $aRow, $sCssClass = null ) {
		// ** init some needed variables ;
        $sOutputHtml = null;

		$aUsedTemplate = array ( 
			'action' => 'action_link.html'
		);

		// find and replace all special markers ;
		foreach( $aRow AS $sKeyName => $sKeyValue ) {
			if ( $sKeyName == 'Caption' ) {
				$aRow[$sKeyName] =  $this -> markerReplace($aObjectParamaters, $sKeyValue, $aRow['Eval'], true);
			} else {
				$aRow[$sKeyName] =  $this -> markerReplace($aObjectParamaters, $sKeyValue, $aRow['Eval']);
            }
        }

		$sKeyValue = trim($sKeyValue, '{}');

		if ( array_key_exists($sKeyValue, $this -> aSpecialKeys) ) {
			return $aRow['Eval'];
		} else {
			$sSiteUrl = (preg_match("/^(http|https|ftp|mailto)/", $aRow['Url'])) ? '' : BX_DOL_URL_ROOT;
			// build the link components ;
			//$sLinkSrc = (!$aRow['Script']) ? $aRow['Url'] : 'javascript:void(0)';

			$sScriptAction = ( $aRow['Script'] ) ? ' onclick="' . $aRow['Script'] . '"' : '';
			$sScriptAction = ($sScriptAction=='' && $aRow['Url']!='') ? " onclick=\"window.open ('{$sSiteUrl}{$aRow['Url']}','_self');\" " : $sScriptAction;

			$sIcon = getTemplateIcon($aRow['Icon']);

			if ( $aRow['Caption'] and ($aRow['Url'] or $aRow['Script'] ) ) {

				$sCssClass = ( $sCssClass ) ? 'class="' . $sCssClass . '"' :  null;

				$aTemplateKeys = array (
					'action_img_alt'	=> $aRow['Caption'],
					'action_img_src'	=> $sIcon,
					'action_caption'	=> $aRow['Caption'],
					'extended_css'		=> $sCssClass,
					'extended_action'	=> $sScriptAction,
				);

				$sOutputHtml .= $GLOBALS['oSysTemplate'] -> parseHtmlByName( $aUsedTemplate['action'], $aTemplateKeys );	
			}
		}

		return $sOutputHtml;
    }

	/**
	 * Function will parse and replace all special markers ;
	 *
     * @param $aMemberSettings (array) : all available member's information
	 * @param $sTransformText (text) : string that will to parse
	 * @param $bTranslate (boolean) : if isset this param - script will try to translate it used dolphin language file
	 * @return (string) : parsed string     
	*/
    function markerReplace( &$aMemberSettings, $sTransformText, $sExecuteCode = null, $bTranslate = false ) {
        $aMatches = array();
        preg_match_all( "/([a-z0-9\-\_ ]{1,})|({([^\}]+)\})/i", $sTransformText, $aMatches );
        if ( is_array($aMatches) and !empty($aMatches) ) {
            // replace all founded markers ;
            foreach( $aMatches[3] as $iMarker => $sMarkerValue ) {
                if ( is_array($aMemberSettings) and array_key_exists($sMarkerValue, $aMemberSettings) and !array_key_exists($sMarkerValue, $this -> aSpecialKeys) ){
                    $sTransformText = str_replace( '{' . $sMarkerValue . '}', $aMemberSettings[$sMarkerValue],  $sTransformText);
                } else if ( $sMarkerValue == 'evalResult' and $sExecuteCode ) {
                    //find all special markers into Execute code ;
                    $sExecuteCode = $this -> markerReplace( $aMemberSettings, $sExecuteCode );
                    $sTransformText =  str_replace( '{' . $sMarkerValue . '}', eval( $sExecuteCode ),  $sTransformText);
                } else {
                    //  if isset into special keys ;
                    if ( array_key_exists($sMarkerValue, $this -> aSpecialKeys) ) {
                        return $aMemberSettings[$sMarkerValue];
                    } else {
                        // undefined keys
                        switch ($sMarkerValue) {
                        }
                    }
                }
            }

            // try to translate item ;
			if ( $bTranslate ) {
				foreach( $aMatches[1] as $iMarker => $sMarkerValue ) if ( $sMarkerValue ) 
					$sTransformText = str_replace( $sMarkerValue , _t( trim($sMarkerValue) ),  $sTransformText);
			}
        }

        return $sTransformText;
    }
 
    function msgBox($sText, $iTimer = 0, $sOnClose = "") {
        $iId = mktime();
        
        return $GLOBALS['oSysTemplate']->parseHtmlByName('messageBox.html', array(
            'id' => $iId,
            'msgText' => $sText,
            'bx_if:timer' => array(
                'condition' => $iTimer > 0,
                'content' => array(
                    'id' => $iId,
                    'time' => 1000 * $iTimer,
                    'on_close' => $sOnClose,
                )
            )
        ));
    }
    
    function loadingBox($sName) {
        return $GLOBALS['oSysTemplate']->parseHtmlByName('loading.html', array(
            'name' => $sName,
        ));
    }
    
    /**
     * Get standard popup box.
     *
     * @param string $sTitle - translated title
     * @param string $sContent - content of the box
     * @param array $aActions - an array of actions. See an example below.
     * @return string HTML of Standard Popup Box
     * 
     * @see Example of actions 
     *      $aActions = array(
     *          'a1' => array('href' => 'javascript:void(0)', 'onclick' => 'javascript: changeType(this)', 'class' => 'wall-ptype-ctl', 'icon' => 'post_text.png', 'title' => _t('_title_a1'), 'active' => 1),
     *          'a2' => array('href' => 'javascript:void(0)', 'onclick' => 'javascript: changeType(this)', 'class' => 'wall-ptype-ctl', 'icon' => 'post_text.png', 'title' => _t('_title_a2'))
     *      );
     */
    function popupBox($sName, $sTitle, $sContent, $aActions = array()) {
        $iId = !empty($sName) ? $sName : mktime();
        
        $aButtons = array();
        foreach($aActions as $sId => $aAction)
            $aButtons[] = array(
                'id' => $sId,
                'title' => htmlspecialchars_adv(_t($aAction['title'])),
                'class' => isset($aAction['class']) ? ' class="' . $aAction['class'] . '"' : '',
                'icon' => isset($aAction['icon']) ? '<img src="' . $aAction['icon'] . '" />' : '',
                'href' => isset($aAction['href']) ? ' href="' . htmlspecialchars_adv($aAction['href']) . '"' : '',
                'target' => isset($aAction['target'])  ? ' target="' . $aAction['target'] . '"' : '',
                'on_click' => isset($aAction['onclick']) ? ' onclick="' . $aAction['onclick'] . '"' : '',
                'bx_if:hide_active' => array(
                    'condition' => !isset($aAction['active']) || $aAction['active'] != 1,
                    'content' => array()
                ),
                'bx_if:hide_inactive' => array(
                    'condition' => isset($aAction['active']) && $aAction['active'] == 1,
                    'content' => array()
                )                
            );

        return $GLOBALS['oSysTemplate']->parseHtmlByName('popup_box.html', array(
            'id' => $iId,
            'title' => $sTitle,
            'bx_repeat:actions' => $aButtons,
            'content' => $sContent
        ));
    }
    
    function transBox($content, $isPlaceInCenter = false) {
        return
            ($isPlaceInCenter ? '<div class="login_ajax_wrap">' : '') .
                $GLOBALS['oSysTemplate']->parseHtmlByName('transBox.html', array('content' => $content)) .
            ($isPlaceInCenter ? '</div>' : '');
    }

	/**
	* @description : function will generate the sex icon ;
	* @param 		: $sSex (string) - sex name ;
	* @return 		: (text) - path to image ;
	*/
	function genSexIcon($sSex) {
		switch( $sSex ) {
			case 'male'	: 
				return getTemplateIcon( 'male.png' );
			case 'female' :
				return getTemplateIcon( 'female.png' );
			case 'men'	: 
				return getTemplateIcon( 'male.png' );	
			default :
				return getTemplateIcon( 'tux.png' );
		}
    }

    function getSexPic($sSex, $sType = 'medium') {
        $aGenders = array (
            'female' => 'woman_',
            'Female' => 'woman_',
            'male' => 'man_',
            'Male' => 'man_',
        );        
        return getTemplateIcon(isset($aGenders[$sSex]) ? $aGenders[$sSex] . $sType . '.gif' : 'visitor_' . $sType . '.gif');
    }

    function getMemberAvatar($iId, $sType = 'medium') {
        $aProfile = getProfileInfo($iId);
        if (!$aProfile || !@include_once (BX_DIRECTORY_PATH_MODULES . 'boonex/avatar/include.php'))
            return false;
        return $aProfile['Avatar'] ? BX_AVA_URL_USER_AVATARS . $aProfile['Avatar'] . ($sType == 'small' ? 'i' : '') . BX_AVA_EXT : $this->getSexPic($aProfile['Sex'], $sType);
    }

    function getMemberThumbnail($iId, $sFloat = 'none', $bGenProfLink = false, $sForceSex = 'visitor', $isAutoCouple = true, $sType = 'medium', $aOnline = array()) {

        $aProfile = getProfileInfo($iId);
        if (!$aProfile)
            return '';

        $bCouple = ((int)$aProfile['Couple'] > 0) && $isAutoCouple ? true : false;
        
        $bOnline = 0;

        if (!@include_once (BX_DIRECTORY_PATH_MODULES . 'boonex/avatar/include.php'))
            return '';
        
        $sLink = ''; 
        $sNick = '';

        $oUserStatusView = bx_instance('BxDolUserStatusView'); //need singlton here
        $sStatusIcon = $oUserStatusView->getStatusIcon($iId, 'icon8');

		if ($iId > 0) {
		    $sLink = getProfileLink($iId); 
            $sNick = getNickname($iId);
			if (! empty($aOnline) && (int)$aOnline['is_online']==0) {
			} else {
				
				$bOnline = 1;
			}
		}
		if (!$bGenProfLink) {
			if ($sForceSex != 'visitor') {
				$sNick = _t('_Vacant');
				$sLink = 'javascript:void(0)';
			}
        }

        $w = $sType == 'medium' ? BX_AVA_W : BX_AVA_ICON_W;
        $h = $sType == 'medium' ? BX_AVA_H : BX_AVA_ICON_H;

        if ($bCouple)
            $sType = 'small';

        $aVariables = array(
            'iProfId' => $iId,
			'sys_thb_float' => $sFloat,
			'sys_thb_width' => $w + 6,
			'sys_thb_height' => $h + 6,
			'sys_img_width' => $w,
			'sys_img_height' => $h,
		    'sys_img_width1' => $w + 4,
            'sys_img_height1' => $h + 4,
			'sys_cpl_img_width' => BX_AVA_ICON_W,
			'sys_cpl_img_height' => BX_AVA_ICON_H,
			'sys_status_url' => getTemplateIcon($sStatusIcon),
			'sys_status_title' => $oUserStatusView->getStatus($iId),
            'usr_profile_url' => $sLink,
			'usr_thumb_url0' => $aProfile['Avatar'] ? BX_AVA_URL_USER_AVATARS . $aProfile['Avatar'] . ($sType == 'small' ? 'i' : '') . BX_AVA_EXT : $this->getSexPic($aProfile['Sex'], $sType),
			'usr_thumb_title0' => $sNick,
		    'bx_if:profileLink' => array(
		      'condition' => $bGenProfLink,
		      'content' => array(
		          'picWidth' => $w + 6,
		          'nickName' => $sNick,
		          'usr_profile_url' => $sLink
		      )
		     ),
            'sys_status_img_width'  => 12, 
            'sys_status_img_height' => 12, 
        );

        if ($bCouple) {
            $aProfileCouple = getProfileInfo($aProfile['Couple']);
            $sNickCouple = getNickname($aProfile['Couple']);
            $aVariables['usr_thumb_url1'] = $aProfileCouple['Avatar'] ? BX_AVA_URL_USER_AVATARS . $aProfileCouple['Avatar'] . 'i' . BX_AVA_EXT : $this->getSexPic($aProfileCouple['Sex'], 'small'); 
            $aVariables['usr_thumb_title1'] = $sNickCouple;
        }
        
        return $GLOBALS['oSysTemplate']->parseHtmlByName($bCouple ? "thumbnail_couple.html" : "thumbnail_single.html", $aVariables);
    }

    function getMemberIcon($iId, $sFloat = 'none', $bGenProfLink = false) {
        return $this->getMemberThumbnail($iId, $sFloat, $bGenProfLink, 'visitor', false, 'small');
	}

    /**
     * Get image of the specified type by image id 
     * @param $aImageInfo image info array with the following info
     *          $aImageInfo['Avatar'] - photo id, NOTE: it not relatyed to profiles avataras module at all
     * @param $sImgType image type 
     */     
	function _getImageShared($aImageInfo, $sType = 'thumb') {
	    return BxDolService::call('photos', 'get_image', array($aImageInfo, $sType), 'Search');
	}

    function getTemplateIcon($sName) {
        $sUrl = $GLOBALS['oSysTemplate']->getIconUrl($sName);
        return !empty($sUrl) ? $sUrl : $GLOBALS['oSysTemplate']->getIconUrl('spacer.gif');
    }

    function getTemplateImage($sName) {
        $sUrl = $GLOBALS['oSysTemplate']->getImageUrl($sName);
        return !empty($sUrl) ? $sUrl : $GLOBALS['oSysTemplate']->getImageUrl('spacer.gif');
    }

    /**
     * @description : function will generate object's action lists; 
     * @param :  $aKeys (array)  - array with all nedded keys;
     * @param :  $sActionsType (string) - type of actions; 
     * @param :  $iDivider (integer) - number of column; 
     * @return:  HTML presentation data; 
    */
    function genObjectsActions( &$aKeys,  $sActionsType, $bSubMenuMode = false ) {

		// ** init some needed variables ;		
		$sActionsList 	= null;
		$sResponceBlock = null;

		$aUsedTemplate	= array (
            'actions'     => 'member_actions_list.html',
            'ajaxy_popup' => 'ajaxy_popup_result.html',
		);

        $aKeys['display_type'] 	= $this -> iDhtmlPopupMenu ;

        // read data from cache file ;
        $oCache = $GLOBALS['MySQL']->getDbCacheObject();
        $aActions = $oCache->getData($GLOBALS['MySQL']->genDbCacheKey('sys_objects_actions'));
                
        // if cache file empty - will read from db ;
        if (null === $aActions || empty($aActions[$sActionsType]) ) {

			$sQuery  = 	"
				SELECT 
					`Caption`, `Icon`, `Url`, `Script`, `Eval`, `bDisplayInSubMenuHeader`
				FROM  
					`sys_objects_actions`
                WHERE 
					`Type` = '{$sActionsType}' 
				ORDER BY 
                    `Order`
            ";

			$rResult = db_res($sQuery);
			while ( $aRow = mysql_fetch_assoc($rResult) ) {
				$aActions[$sActionsType][] = $aRow;
			}

			// write data into cache file ;
			if ( is_array($aActions[$sActionsType]) and !empty($aActions[$sActionsType]) ) {
                $oCache->setData ($GLOBALS['MySQL']->genDbCacheKey('sys_objects_actions'), $aActions); 
			}
		}

        // ** generate actions block ;

        // contain all systems actions that will procces by self function ;
        $aCustomActions = array();
		if ( is_array($aActions[$sActionsType]) and !empty($aActions[$sActionsType]) ) {

            // need for table's divider ;
			$iDivider = $iIndex = 0; 
            foreach( $aActions[$sActionsType] as  $aRow ) {
				if ($bSubMenuMode && $aRow['bDisplayInSubMenuHeader']==0) continue;

                $sOpenTag = $sCloseTag = null;

                // generate action's link ;
                $sActionLink = $this -> genActionLink( $aKeys, $aRow, 'menuLink') ;

                if ( $sActionLink ) {
					$iDivider = $iIndex % 2; 

                    if ( !$iDivider ) {
						$sOpenTag = '<tr>';
                    }

                    if ( $iDivider ) {
                        $sCloseTag = '</tr>';
                    }

                    $aActionsItem[] = array (
                        'open_tag'    => $sOpenTag,  
                        'action_link' => $sActionLink,
                        'close_tag'   => $sCloseTag,
                    );

                    $iIndex++;
				}

                // it's system action ;
                if ( !$aRow['Url'] && !$aRow['Script'] ) {
                    $aCustomActions[] =  array (
                        'caption'   => $aRow['Caption'],
                        'code'      => $aRow['Eval'],
                    );
                }
            }
        }

		if ($iIndex % 2 == 1) { //fix for ODD menu elements count
			$aActionsItem[] = array (
				'open_tag'    => '',
				'action_link' => '',
				'close_tag'   => ''
			);
		}

        if ( !empty($aActionsItem) ) {

            // check what response window use ;
			// is there any value to having this template even if the ID is empty?
			if ( !$this -> iDhtmlPopupMenu && !empty($aKeys['ID'])) {
				$sResponceBlock = $GLOBALS['oSysTemplate'] -> parseHtmlByName( $aUsedTemplate['ajaxy_popup'], array('object_id' => $aKeys['ID']) );
            }

            $aTemplateKeys = array (
                'bx_repeat:actions' => $aActionsItem,
                'responce_block'    => $sResponceBlock,
            );

            $sActionsList = $GLOBALS['oSysTemplate'] -> parseHtmlByName( $aUsedTemplate['actions'], $aTemplateKeys );
        }

        //procces all the custom actions ;
        if ($aCustomActions) {
            foreach($aCustomActions as $iIndex => $sValue ) {
                $sActionsList .= eval( $this -> markerReplace($aKeys, $aCustomActions[$iIndex]['code']) );
            }
        }

        return $sActionsList;
    }

    /**
     * alternative to GenFormWrap
     * easy to use but javascript based
     * $s - content to be centered
     * $sBlockStyle - block's style, jquery selector
     *
     * see also bx_center_content javascript function, if you need to call this function from javascript
     */ 
    function centerContent ($s, $sBlockStyle, $isClearBoth = true) {
        $sId = 'id' . time() . rand();
        return  '<div id="'.$sId.'">' . $s . ($isClearBoth ? '<div class="clear_both"></div>' : '') . '</div><script>
            $(document).ready(function() {
                var eCenter = $("#'.$sId.'");
                var iAll = $("#'.$sId.' '.$sBlockStyle.'").size();
                var iWidthUnit = $("#'.$sId.' '.$sBlockStyle.':first").outerWidth({"margin":true});
                var iWidthContainer = eCenter.width();
                var iPerRow = parseInt(iWidthContainer/iWidthUnit);
                var iLeft = (iWidthContainer - (iAll > iPerRow ? iPerRow * iWidthUnit : iAll * iWidthUnit)) / 2;
                eCenter.css("padding-left", iLeft);
            });
        </script>';
    }

    /**
     * Function will generate site's bottom menu;
     *
     * @return : Html presentation data;
     */
    function genSiteBottomMenu() {

        if (isset($GLOBALS['bx_profiler'])) $GLOBALS['bx_profiler']->beginMenu('Bottom Menu');

        $sOutputHtml    = null;
        $aLinks         = array();  
        $oPermalinks    = new BxDolPermalinks();


        // read data from cache file
        $oCache = $GLOBALS['MySQL']->getDbCacheObject();
        $aLinks = $oCache->getData($GLOBALS['MySQL']->genDbCacheKey('sys_menu_bottom'));
        if (null === $aLinks) {

            $sQuery  = "SELECT * FROM `sys_menu_bottom` ORDER BY `Order`";
            $rResult = db_res($sQuery);
            while($aItems = mysql_fetch_assoc($rResult)) {
                $aLinks[] = array(
                    'menu_caption'		 => ($aItems['Caption']),
                    'menu_link'			 => ( $aItems['Script'] ) ? 'javascript:void(0)' : $oPermalinks -> permalink($aItems['Link']),
                    'extended_action'	 => ( $aItems['Script'] ) ? 'onclick="' . $aItems['Script'] . '"' : null,
                    'target'			 => ( $aItems['Target'] ) ? 'target="_blank"' : null,
                );
            }

            $oCache->setData ($GLOBALS['MySQL']->genDbCacheKey('sys_menu_bottom'), $aLinks);
        }

        foreach ($aLinks as $iID => $aItem) {
            $aLinks[$iID]['menu_caption'] = _t($aItem['menu_caption']);
        }
        $aTemplateKeys = array(
            'bx_repeat:items' => $aLinks,
        );

        $sOutputHtml = $GLOBALS['oSysTemplate'] -> parseHtmlByName( 'extra_bottom_menu.html', $aTemplateKeys );

        if (isset($GLOBALS['bx_profiler'])) $GLOBALS['bx_profiler']->endMenu('Bottom Menu');

        return $sOutputHtml;
    }

	function genNotifyMessage($sMessage, $sDirection = 'left', $isButton = false, $sScript = '') {
		$sDirStyle = ($sDirection == 'left') ? '' : 'notify_message_none';
		switch ($sDirection) {
			case 'none': break;
			case 'left': break;
		}

		$sPossibleID = ($isButton) ? ' id="isButton" ' : '';
        $sOnClick = $sScript ? ' onclick="' . $sScript . '"' : '';

		return <<<EOF
<div class="notify_message {$sDirStyle}" {$sPossibleID} {$sOnClick}>
	<table class="notify" cellpadding=0 cellspacing=0><tr><td>{$sMessage}</td></tr></table>
	<div class="notify_wrapper_close"> </div>
</div>
EOF;
	}

    function sysIcon ($sIcon, $sName, $sUrl = '', $iWidth = 0) {
        return '<div class="sys_icon">' . ($sUrl ? '<a title="'.$sName.'" href="'.$sUrl.'">' : '') . '<img alt="'.$sName.'" src="'.$sIcon.'" '.($iWidth ? 'width='.$iWidth : '').' />' . ($sUrl ? '</a>' : '') . '</div>';
    }
}

?>
