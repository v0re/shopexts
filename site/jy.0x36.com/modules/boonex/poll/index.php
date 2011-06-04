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

    require_once( BX_DIRECTORY_PATH_MODULES . $aModule['path'] . 'classes/' . $aModule['class_prefix'] . 'View.php');
    require_once( BX_DIRECTORY_PATH_MODULES . $aModule['path'] . 'classes/' . $aModule['class_prefix'] . 'Module.php');

    // ** init some needed variables ;

    global $_page;
    global $_page_cont;

    $iProfileId = getLoggedId();

    $iIndex = 57;

    $iPollId = ( isset($_GET['id']) )  ? (int) $_GET['id'] : 0;

    // define all needed poll's settings ;
    $aPollSettings = array
    (
        // check admin mode ;
        'admin_mode' => isAdmin() ? true : false,

        // logged member's id ;
        'member_id'  =>  $iProfileId,

        // number of poll's columns for per page ;    
        'page_columns' => 2,

        // number of poll's elements for per page ;
        'per_page' => ( isset($_GET['per_page']) )
            ? (int) $_GET['per_page']
            : 6,

        // current page ;
        'page' => ( isset($_GET['page']) )
            ? (int) $_GET['page']
            : 1,

       'featured_page' => ( isset($_GET['featured_page']) )
            ? (int) $_GET['featured_page']
            : 1,

       'featured_per_page' => ( isset($_GET['featured_per_page']) )
            ? (int) $_GET['featured_per_page']
            : 3,

        // contain some specific actions for polls ;
        'action' => ( isset($_GET['action']) )
            ? $_GET['action']
            : null,

        // contain number of needed pool id ;
        'edit_poll_id' => ( isset($_GET['edit_poll_id']) )
            ? (int) $_GET['edit_poll_id']
            : 0,

        'mode'  => ( isset($_GET['mode']) )
            ? addslashes($_GET['mode'])
            : null,

        'tag'   =>  ( isset($_GET['tag']) )
            ? addslashes($_GET['tag'])
            : null
    );

    $oPoll = & new BxPollModule($aModule, $aPollSettings);

    $_page['name_index']	= $iIndex;

    $sPageCaption = _t('_bx_poll_all');

    $_page['header']        = $sPageCaption ;
    $_page['header_text']   = $sPageCaption ;
    $_page['css_name']      = 'main.css';

    $oPoll -> _oTemplate -> setPageDescription( _t('_bx_poll_PH') );
    $oPoll -> _oTemplate -> addPageKeywords( _t('_bx_poll_keyword') );

    // get custom actions button;
    $oPoll -> getCustomActionButton();

    if(!$aPollSettings['action']) {
        $_page_cont[$iIndex]['page_main_code'] = $oPoll -> searchAll();
    }
    else {
        switch($aPollSettings['action']) {
            case 'user' :
                $sUserName = ( isset($_GET['nickname']) )
                    ? $_GET['nickname']
                    : null;

                // define profile's Id;
                $iProfileId = getId($sUserName);

                if($iProfileId) {
                    $GLOBALS['oTopMenu']->setCurrentProfileID($iProfileId);
                    $_page_cont[$iIndex]['page_main_code'] = $oPoll -> searchAllProfilePolls($iProfileId);
                }
                else {
                    // if profile's Id not defined will draw all polls list;
                    $_page_cont[$iIndex]['page_main_code'] = $oPoll -> searchAll();
                }
            break;

            case 'tag' : 
                $sPageCaption = _t('_bx_poll_tags');

                $_page['header']        = $sPageCaption ;
                $_page['header_text']   = $sPageCaption ;

                $sTag = ( isset($_GET['tag']) )
                    ? uri2title($_GET['tag'])
                    : null;

                $_page_cont[$iIndex]['page_main_code'] = $oPoll -> searchTags($sTag);
            break;

            case 'category' : 
                $sPageCaption = _t('_bx_poll_view_category');

                $_page['header']        = $sPageCaption ;
                $_page['header_text']   = $sPageCaption ;

                $sCategory = ( isset($_GET['category']) )
                    ? uri2title($_GET['category'])
                    : null;

                $_page_cont[$iIndex]['page_main_code'] = $oPoll -> searchCategories($sCategory);
            break;

            case 'featured' : 
                $sPageCaption = _t('_bx_poll_featured_polls');

                $_page['header']        = $sPageCaption ;
                $_page['header_text']   = $sPageCaption ;

                $_page_cont[$iIndex]['page_main_code'] = $oPoll -> searchFeatured();
            break;

            case 'popular' : 
                $sPageCaption = _t('_bx_poll_popular_polls');

                $_page['header']        = $sPageCaption ;
                $_page['header_text']   = $sPageCaption ;
    
                $_page_cont[$iIndex]['page_main_code'] = $oPoll -> searchPopular();
            break;

            case 'my' : 
                $sPageCaption = _t('_bx_poll_my');

                $_page['header']        = $sPageCaption ;
                $_page['header_text']   = $sPageCaption ;

                if($iProfileId) {
                    $GLOBALS['oTopMenu']->setCurrentProfileID($iProfileId);
                    $_page_cont[$iIndex]['page_main_code'] = $oPoll -> searchMy();
                }
                else {
                    member_auth(0);
                }                
            break;

            case 'show_poll_info' : 
            case 'poll_home' :

                // draw polls question on menu's panel;
                $aPollInfo = $oPoll -> _oDb -> getPollInfo($iPollId);
                if($aPollSettings['action'] == 'show_poll_info' && $aPollInfo) {
                    $sPageTitle = $aPollInfo[0]['poll_question'];

                    $sPageCaption = _t('_bx_poll_view');
                    $_page['header']        = $sPageCaption ;
                    $_page['header_text']   = $sPageCaption ; 

                    $oPoll -> _oTemplate -> setPageDescription($aPollInfo[0]['poll_question']);
                    $oPoll -> _oTemplate -> addPageKeywords($aPollInfo[0]['poll_answers'], '<delim>');
    
                    if( mb_strlen($sPageTitle) > $oPoll -> sPollHomeTitleLenght) {
                        $sPageTitle = mb_substr($sPageTitle, 0, $oPoll -> sPollHomeTitleLenght) . '...';
                    }

                    $GLOBALS['oTopMenu'] -> setCustomSubHeader($sPageTitle); 
                }
                else {
                    $sPageCaption = _t('_bx_poll_home');
                    $_page['header']        = $sPageCaption ;
                    $_page['header_text']   = $sPageCaption ;                    
                }

                $oViewPoll = bx_instance($aModule['class_prefix'] . 'View', array($aPollSettings['action']
                        , $aModule, $oPoll, $iPollId), $aModule);

                $sInitPart   = $oPoll -> getInitPollPage();

                if($aPollSettings['action'] == 'show_poll_info') {
                    if(!$aPollInfo) {
                        $_page_cont[$iIndex]['page_main_code'] = MsgBox( _t('_Empty') );
                    }
                    else {
                        $_page_cont[$iIndex]['page_main_code'] = $sInitPart . $oViewPoll -> getCode();
                    }
                }
                else {
                    $_page_cont[$iIndex]['page_main_code'] = $sInitPart . $oViewPoll -> getCode();
                }
            break;

            case 'delete_poll' :
                if($aPollSettings['member_id'] && $iPollId) {
                    if( isAdmin() ) {
                        $oPoll -> _oDb -> deletePoll( (int) $iPollId);
                    }
                    else {
                        $aPollInfo = $oPoll -> _oDb -> getPollInfo( (int) $iPollId);
                        if($aPollInfo && $aPollInfo[0]['id_profile'] == $aPollSettings['member_id']) {
                            $oPoll -> _oDb -> deletePoll( (int) $iPollId);
                        }
                    }    
                }

                $_page_cont[$iIndex]['page_main_code'] = $oPoll -> searchAll();
            break;

            default :
                $_page_cont[$iIndex]['page_main_code'] = $oPoll -> searchAll();
        }
    }

    PageCode($oPoll -> _oTemplate);