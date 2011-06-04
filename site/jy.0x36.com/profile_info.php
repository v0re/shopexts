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
    require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
    require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolPageView.php' );
    
	bx_import( 'BxTemplProfileView' );

    $sPageCaption = _t( '_Profile info' );

    $_page['name_index'] 	= 7;
    $_page['header'] 		= $sPageCaption;
    $_page['header_text'] 	= $sPageCaption;
    $_page['css_name']		= 'profile_view.css';

    bx_import ('BxTemplProfileView');

    class BxDolProfileInfoPageView extends BxTemplProfileView
    {
    	// contain informaion about viewed profile ;
    	var $aMemberInfo = array();
        // logged member ID ;
    	var $iMemberID;
    	var $oProfilePV;
    	
    	/**
    	 * Class constructor ;
    	 */
    	function BxDolProfileInfoPageView( $sPageName, &$aMemberInfo ) 
    	{
    		global $site, $dir;

	    	$this->oProfileGen = new BxBaseProfileGenerator( $aMemberInfo['ID'] );
    		$this->aConfSite = $site;
            $this->aConfDir  = $dir;
    		parent::BxDolPageView($sPageName);

    		$this->iMemberID  = getLoggedId();
    		$this->aMemberInfo = &$aMemberInfo;    		
    	}


    	/**
    	 * Function will generate profile's  general information ;
    	 *
    	 * @return : (text) - html presentation data;
         */
    	function getBlockCode_GeneralInfo($iBlockID)
    	{
    		return $this -> getBlockCode_PFBlock($iBlockID, 17);
    	}

    	/**
    	 * Function will generate profile's additional information ;
    	 *
    	 * @return : (text) - html presentation data;
         */
    	function getBlockCode_AdditionalInfo($iBlockID)
    	{
    		return $this -> getBlockCode_PFBlock($iBlockID, 20);
    	}
    
       	/**
    	 * Function will generate profile's additional information ;
    	 *
    	 * @return : (text) - html presentation data;
         */
    	function getBlockCode_Description() {
            if(!$this->aMemberInfo['DescriptionMe'])
                return;

            return array($this->aMemberInfo['DescriptionMe']);
    	}
    }

    //-- init some needed variables --//;

    $iViewedID   = false != bx_get('ID') ?  (int) bx_get('ID') : 0;
	if(!$iViewedID) {
		$iViewedID = getLoggedId();
	}

    $sOutputHtml = MsgBox ( _t( '_Profile NA' ) );
    $GLOBALS['oTopMenu'] -> setCurrentProfileID($iViewedID);

    // fill array with all profile informaion
    $aMemberInfo  = getProfileInfo($iViewedID);

    // build page;
    $_ni = $_page['name_index'];

    if ( is_array($aMemberInfo) and !empty($aMemberInfo) ) 
    {
    	// prepare all needed keys ;
    	$aMemberInfo['window_width']  	= $oTemplConfig -> popUpWindowWidth;
    	$aMemberInfo['window_height'] 	= $oTemplConfig -> popUpWindowHeight;
    	$aMemberInfo['anonym_mode'] 	= $oTemplConfig -> bAnonymousMode;
    	$aMemberInfo['member_pass'] 	= $aMemberInfo['Password'];
    	$aMemberInfo['member_id'] 		= $aMemberInfo['ID'];

    	$bDisplayType = ( getParam('enable_new_dhtml_popups')=='on' ) ? 0 : 1;
    	$aMemberInfo['display_type'] = $bDisplayType;
    	$aMemberInfo['url'] = BX_DOL_URL_ROOT;

    	$oProfileInfo = new BxDolProfileInfoPageView('profile_info', $aMemberInfo);
    	$sOutputHtml  = $oProfileInfo->getCode();
    }

    $_page_cont[$_ni]['page_main_code'] = $sOutputHtml;

    PageCode();
