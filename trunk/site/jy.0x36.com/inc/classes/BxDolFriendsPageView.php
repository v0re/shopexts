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

bx_import( 'BxDolPageView' );

class BxDolFriendsPageView extends BxDolPageView 
{
    // consit all necessary data for display members list ;
    var $aDisplayParameters;

    var $iProfileID;

    // link on search profile ;
    var $oSearchProfileTmpl;

    // contains the path to the current page ;
    var $sCurrentPage ;

    var $iMemberOnlineTime;

    /**
     * @description : class constructor ;
     * @param		: $sPageName (string) - name of build page ;	
     * @param 		: $aDisplayParameters (array) ;
                        per_page (integer) - number of elements for per page ;
                        page (integer) - current page ;
                        mode (string)  - will swith member view mode ;
                        sort (string)		- sorting parameters ;	
     * @param		: $iProfileID (integer) - member ID ;					
    */
    function BxDolFriendsPageView($sPageName, &$aDisplayParameters, $iProfileID)
    {
        parent::BxDolPageView($sPageName);
        $this -> aDisplayParameters = &$aDisplayParameters;
        $this -> oSearchProfileTmpl = new BxTemplSearchProfile();
        $this -> sCurrentPage = 'viewFriends.php';	

        // check member on line time ;

        $this -> iMemberOnlineTime = getParam('member_online_time');
        $this -> iProfileID = $iProfileID;
    }

    /**
    * @description : function will generate friends list ;
    * @return		: array ;
    */
    function getBlockCode_Friends() {
        // init some variables ;
        $sOutputHtml 	= '';
        $sEmpty 	    = '';
        $iIndex 		= '';

        $aUsedTemplates = array
        (
            'browse_searched_block.html'
        );

        // lang keys ;
        $sPhotoCaption  = _t( '_With photos only' );
        $sOnlineCaption = _t( '_online only' );

        // collect the SQL parameters ;

        $aWhereParam = array();
        if ( $this -> aDisplayParameters['photos'] ) 
            $aWhereParam[] = 'p.`Avatar` <> 0';

        if ( $this -> aDisplayParameters['online'] )
            $aWhereParam[] = "(p.`DateLastNav` > SUBDATE(NOW(), INTERVAL " . $this -> iMemberOnlineTime . " MINUTE)) ";

        $sWhereParam = null;
        foreach( $aWhereParam AS $sValue )
            if ( $sValue )
                $sWhereParam .= ' AND ' . $sValue;

        $iTotalNum = getFriendNumber($this->iProfileID, 1, 0, $sWhereParam);

        if( !$iTotalNum ) {
            $sEmpty = MsgBox( _t('_Empty') );
        }

        $iPerPage = $this -> aDisplayParameters['per_page'];
        $iCurPage = $this -> aDisplayParameters['page'];

        $sLimitFrom = ( $iCurPage - 1 ) * $iPerPage;
        $sqlLimit = "LIMIT {$sLimitFrom}, {$iPerPage}";

        // switch member's template ;

        $sTemplateName = ($this->aDisplayParameters['mode'] == 'extended') ? 'search_profiles_ext.html' : 'search_profiles_sim.html';

        // select the sorting parameters ;
        $sSortParam = 'activity';
        if ( isset($this -> aDisplayParameters['sort']) ) {
            switch($this -> aDisplayParameters['sort']) {
                case 'activity' :
                    $sSortParam = 'activity';
                break;	
                case 'date_reg' :	
                    $sSortParam = 'date_reg';
                break;	
                case 'rate' :	
                    $sSortParam = 'rate';
                break;	
                default :
                    $this -> aDisplayParameters['sort'] = 'activity';
                break;    	
            }
        }
        else
            $this -> aDisplayParameters['sort'] = 'activity';

        $aAllFriends = getMyFriendsEx($this->iProfileID, $sWhereParam, $sSortParam, $sqlLimit);

        $aExtendedCss = array( 'ext_css_class' => 'search_filled_block');

        foreach ($aAllFriends as $iFriendID => $aFriendsPrm) {
            $aMemberInfo = getProfileInfo($iFriendID);
            if ( $aMemberInfo['Couple']) {
                $aCoupleInfo = getProfileInfo( $aMemberInfo['Couple'] );
                if ( !($iIndex % 2)  ) {
                    $sOutputHtml .= $this -> oSearchProfileTmpl -> PrintSearhResult($aMemberInfo, $aCoupleInfo, null, $sTemplateName);
                } else {
                    // generate filled block ;
                    $sOutputHtml .= $this -> oSearchProfileTmpl -> PrintSearhResult($aMemberInfo, $aCoupleInfo, $aExtendedCss, $sTemplateName);
                }
            } else {
                if ( !($iIndex % 2)  ) {
                    $sOutputHtml .= $this -> oSearchProfileTmpl -> PrintSearhResult($aMemberInfo, '', null, $sTemplateName);
                } else {
                    // generate filled block ;
                    $sOutputHtml .= $this -> oSearchProfileTmpl -> PrintSearhResult($aMemberInfo, null, $aExtendedCss, $sTemplateName);
                }
            }
            $iIndex++;
        }

        $sOutputHtml .= '<div class="clear_both"></div>';

        // work with link pagination ;
        $aRequest = array();
        $sRequest = BX_DOL_URL_ROOT . 'viewFriends.php?';
        $aGetParams = array('mode', 'iUser', 'photos_only', 'online_only');
        if ( is_array($aGetParams) and !empty($aGetParams) )
            foreach($aGetParams AS $sValue ) 
                if ( isset($_GET[$sValue]) ) 
                {
                    $aRequest[] = $sValue . '=' . $_GET[$sValue];
                }

        $sRequest .= implode("&", $aRequest) . '&page={page}&per_page={per_page}&sort={sorting}';
        
        // gen pagination block ;
        $oPaginate = new BxDolPaginate
        (
            array
            (
                'page_url'	 => $sRequest,
                'count'		 => $iTotalNum,
                'per_page'	 => $iPerPage,
                'page'		 => $iCurPage,
                'sorting'    =>  $this -> aDisplayParameters['sort'],

                'per_page_changer'	 => true,
                'page_reloader'		 => true,
                'on_change_page'	 => null,
                'on_change_per_page' => null,
            )
        );

        $sPagination = $oPaginate -> getPaginate();

        // ** GENERATE HEADER PART ;

        // gen per page block ;

        $sPerPageBlock = $oPaginate -> getPages( $iPerPage );  

        // fill array with sorting params ;

        $aSortingParam = array
        (
            'activity' 	=> _t( '_Latest activity' ),
            'date_reg' 	=> _t( '_FieldCaption_DateReg_View' ),
            'rate' 		=> _t( '_Rate' ),
        );

        // gen sorting block ( type of : drop down ) ;

        $sSortBlock = $oPaginate -> getSorting( $aSortingParam ); 

        $sRequest = str_replace('{page}', '1', $sRequest);
        $sRequest = str_replace('{per_page}', $iPerPage, $sRequest);
        $sRequest = str_replace('{sorting}', $this -> aDisplayParameters['sort'], $sRequest);

        // init some visible parameters ;

        $sPhotosChecked = ($this -> aDisplayParameters['photos'])
            ? 'checked="checked"'
            : null;

        $sOnlineChecked = ($this -> aDisplayParameters['online'])
            ? 'checked="checked"'
            : null;

        // link for photos section ;

        $sPhotoLocation = $this -> getCutParam( 'photos_only',  $sRequest);

        // link for online section ;

        $sOnlineLocation = $this -> getCutParam( 'online_only',  $sRequest);

        // link for `mode switcher` ;

        $sModeLocation = $this -> getCutParam( 'mode',  $sRequest);
        $sModeLocation = $this -> getCutParam( 'per_page',  $sModeLocation);

        // fill array with template's keys ;
        $aTemplateKeys = array
        (
            'sort_block'      => $sSortBlock,
            'photo_checked'   => $sPhotosChecked,
            'photo_location'  => $sPhotoLocation,
            'photo_caption'   => $sPhotoCaption,
            'online_checked'  => $sOnlineChecked,
            'online_location' => $sOnlineLocation,
            'online_caption'  => $sOnlineCaption,
            'per_page_block'  => $sPerPageBlock,
            'searched_data'   => $sOutputHtml,
            'pagination'	  => $sPagination,
        );	

        // build template ;
        $sOutputHtml = $GLOBALS['oSysTemplate'] -> parseHtmlByName( $aUsedTemplates[0], $aTemplateKeys );

        // build the toggle block ;
        $aToggleItems = array
        (
            '' 			=>  _t( '_Simple' ),
            'extended' 	=>	_t( '_Extended' ),
        );

        foreach( $aToggleItems AS $sKey => $sValue )
        {
            $aToggleEllements[$sValue] = array
            (
                'href' => $sModeLocation . '&mode=' . $sKey,
                'dynamic' => true,
                'active' => ($this -> aDisplayParameters['mode'] == $sKey ),
            );
        }

        return array(   $sOutputHtml . $sEmpty, $aToggleEllements );
    }

    /**
    * @description : function will cute the parameter from received string;
    * @param		: $aExceptNames (string) - name of unnecessary paremeter;
    * @return		: cleared string;
    */
    function getCutParam( $sExceptParam, $sString ) 
    { 
        return preg_replace( "/(&amp;|&){$sExceptParam}=([a-z0-9\_\-]{1,})/i",'', $sString);
    }

    /**
     * Function will send count of online member's friends;
     *
     * @param  : $iMemberId (integer) - logged member's Id;
     * @param  : $iOldCount (integer) - received old count of messages (if will difference will generate message)
     * @return : (array) 
                [count]     - (integer) number of new messages;
                [message]   - (string) text message ( if will have a new messages );
     */
    function get_member_menu_bubble_online_friends($iMemberId, $iOldCount = 0)
    {
        global $oSysTemplate, $oFunctions, $site;

        $iMemberId 		  = (int) $iMemberId;
        $iOldCount		  = (int) $iOldCount;
        $iOnlineTime      = (int) getParam( "member_online_time" );
        $iOnlineFriends   = 0;

        $aNotifyMessages  = array();
        $aFriends         = array();

        if ( $iMemberId ) {
            $sWhereCondition = " AND (p.`DateLastNav` > SUBDATE(NOW(), INTERVAL " . $iOnlineTime . " MINUTE))";
            if( null != $aFoundFriends = getMyFriendsEx($iMemberId, $sWhereCondition) ) {
                foreach($aFoundFriends as $iFriendId => $aInfo)
                {
                    $aFriends[] = array($iFriendId);
                }
            }

            $iOnlineFriends  = count($aFriends);
           // $aFriends = array_reverse($aFriends);

            // if have some difference;
            if ( $iOnlineFriends > $iOldCount) {
                // generate notify messages;
                for( $i = $iOldCount; $i < $iOnlineFriends; $i++)
                {
                    $sFriendNickName  = getNickName($aFriends[$i][0]);
                    $sProfileLink     = getProfileLink($aFriends[$i][0]);

                    $aKeys = array (
                        'sender_thumb'    => $oFunctions -> getMemberIcon($aFriends[$i][0], 'left'),
                        'profile_link'    => $sProfileLink,
                        'friend_nickname' => $sFriendNickName,
                        'key_on_line'     => _t( '_Now online' ),
                    );
                    $sMessage = $oSysTemplate -> parseHtmlByName('view_friends_member_menu_notify_window.html', $aKeys);

                    $aNotifyMessages[] = array(
                        'message' => $oSysTemplate -> parseHtmlByName('member_menu_notify_window.html', array('message' => $sMessage))
                    );
                }
            }
        }

        $aRetEval = array(
           'count'     => $iOnlineFriends,
           'messages'  => $aNotifyMessages,
        );

        return $aRetEval;
    }

    /**
    * Function will generate list of member's friends ;
    *
    * @param  : $iMemberId (integer) - member's Id;
    * @return : Html presentation data;
    */
    function get_member_menu_friends_list($iMemberId = 0) 
    {
        global $oFunctions;

        $iMemberId 	 = (int) $iMemberId;
        $iOnlineTime = getParam('member_online_time');

        // define the member's menu position ;
        $sExtraMenuPosition = ( isset($_COOKIE['menu_position']) ) 
            ? $_COOKIE['menu_position']
            : getParam( 'ext_nav_menu_top_position' );

        $aLanguageKeys = array (
            'requests'    => _t( '_Friend Requests' ),
            'online'      => _t( '_Online Friends' ),
        );

        // get all friends requests ;
        $iFriendsRequests = getFriendNumber($iMemberId, 0) ;
        $iOnlineFriends   = getFriendNumber($iMemberId, 1, $iOnlineTime) ;

        // try to generate member's messages list ;

        $sWhereParam = "AND p.`DateLastNav` > SUBDATE(NOW(), INTERVAL " . $iOnlineTime . " MINUTE)";
        $aAllFriends = getMyFriendsEx($iMemberId, $sWhereParam, 'last_nav_desc', "LIMIT 5");
        $oModuleDb   = new BxDolModuleDb();

        $sVideoMessengerImgPath  = $GLOBALS['oSysTemplate'] -> getIconUrl('video.png');
        $sMessengerTitle = _t('_Chat');

        foreach ($aAllFriends as $iFriendID => $aFriendsPrm) 
        {
            $aMemberInfo = getProfileInfo($iFriendID);
            $sThumb = $oFunctions -> getMemberIcon($aMemberInfo['ID'], 'none');

            $sHeadline = ( mb_strlen($aMemberInfo['UserStatusMessage']) > 40 )
                ? mb_substr($aMemberInfo['UserStatusMessage'], 0, 40) . '...'
                : $aMemberInfo['UserStatusMessage'];

            $aFriends[] = array(
                'profile_link' => getProfileLink($iFriendID),
                'profile_nick' => $aMemberInfo['NickName'],
                'profile_id'   => $iFriendID,
                'thumbnail'    => $sThumb,
                'head_line'    => $sHeadline,

                'bx_if:video_messenger' => array (
                        'condition' =>  ( $oModuleDb -> isModule('messenger') ),
                        'content'   => array(
                            'sender_id'       => $iMemberId,
                            'sender_passw'    => getPassword($iMemberId),
                            'recipient_id'    => $iFriendID,
                            'video_img_src'   => $sVideoMessengerImgPath,
                            'messenger_title' => $sMessengerTitle,
                        ),
                ),
            );
        }

         $aExtraSection = array(
            'friends_request' => $aLanguageKeys['requests'],
            'request_count'   => $iFriendsRequests,

            'ID'              => $iMemberId,
            'online_friends'  => $aLanguageKeys['online'],
            'online_count'    => $iOnlineFriends,
        );

        // fill array with needed keys ;
        $aTemplateKeys = array (
            'bx_if:menu_position_bottom' => array (
                'condition' =>  ( $sExtraMenuPosition  == 'bottom' ),
                'content'   =>  $aExtraSection,
            ),

            'bx_if:menu_position_top' => array (
                'condition' =>  ( $sExtraMenuPosition  == 'top' || $sExtraMenuPosition  == 'static' ),
                'content'   =>  $aExtraSection,
            ),

            'bx_repeat:friend_list' => $aFriends,
        );

        $sOutputCode = $GLOBALS['oSysTemplate'] -> parseHtmlByName( 'view_friends_member_menu_friends_list.html', $aTemplateKeys );
        return $sOutputCode;
    }
}

?>