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

bx_import('BxDolSubscription');

check_logged();
$oSubscription = new BxDolSubscription();

// --------------- page components
$iIndex = 0;

$_page = array(
    'css_name' => '',
    'header' => _t('_sys_pcpt_my_subscriptions'),
    'header_text' => _t('_sys_bcpt_my_subscriptions'),
    'name_index' => $iIndex
);
$_page_cont[$iIndex]['page_main_code'] = $oSubscription->getMySubscriptions();

// --------------- [END] page components

PageCode();
// --------------- page components functions

?>