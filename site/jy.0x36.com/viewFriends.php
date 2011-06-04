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
    require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
    require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolPageView.php' );

    require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolFriendsPageView.php');
    require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolPaginate.php');
	
	bx_import('BxTemplProfileView');
	bx_import('BxTemplSearchProfile');

    $_page['name_index'] = 7;
    $_page['css_name'] = array('browse.css');
    $_page['js_name']  = 'browse_members.js';

    $iProfileId = isset($_GET['iUser']) ? (int) $_GET['iUser'] : getLoggedId();

    if (!$iProfileId) 
    {
    	$_page['header'] = _t('_View friends');
    	$_page['header_text'] = _t('_View friends');
    	$_page['name_index'] = 0;
    	$_page_cont[0]['page_main_code'] = MsgBox( _t('_Profile NA') );
    	PageCode();
    	exit;
    }

    $sPageCaption = _t( '_Friends of', getNickName($iProfileId) );

    $_page['header'] 		= $sPageCaption;
    $_page['header_text'] 	= $sPageCaption;
    $_ni = $_page['name_index'];



    // generate page

    // init some needed varibales ;

    if ( isset($_GET['per_page']) ) 
    {
        $iPerPage = (int) $_GET['per_page'];
    }
    else 
    {
        $iPerPage  = ( isset($_GET['mode']) and  $_GET['mode'] == 'extended' )
            ? $iPerPage = 5
            : $iPerPage = 32;
    }

    if ( $iPerPage	<= 0 )
        $iPerPage = 32;

    if ( $iPerPage > 100 )
            $iPerPage = 100;

    $iPage = ( isset($_GET['page']) )	
        ? (int) $_GET['page']
        : 1;

    if ( $iPage	<= 0 )
        $iPage = 1;

    $aDisplayParameters = array
    (
        'per_page' 	=> $iPerPage,
        'page' 		=> $iPage,
        'mode' 		=> isset($_GET['mode']) ? $_GET['mode'] : null,
        'photos'	=> isset($_GET['photos_only']) ? true : false,
        'online'	=> isset($_GET['online_only']) ? true : false,
        'sort'		=> isset($_GET['sort']) ? $_GET['sort'] : null,
    );

    $sPageName = ( isLogged() ) ? 'my_friends' : 'friends';


    $oFriendsPage = new BxDolFriendsPageView( $sPageName, $aDisplayParameters, $iProfileId);
    $sOutputHtml  = $oFriendsPage -> getCode();

    $_page_cont[$_ni]['page_main_code'] = $sOutputHtml;

    PageCode();

?>
