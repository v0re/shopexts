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

define('BX_INDEX_PAGE', 1);

require_once( './inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'admin.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'db.inc.php' );

bx_import('BxTemplJoinPageView');

check_logged();

if (isLogged()) {
    header ('Location:' . BX_DOL_URL_ROOT . 'member.php');
    exit;
}

$_page['header'] = _t( '_JOIN_H' );
$_page['header_text'] = _t( '_JOIN_H' );


if(getParam('reg_by_inv_only') == 'on' && getID($_COOKIE['idFriend']) == 0){
    $_page['name_index'] = 0;
    $_page_cont[0]['page_main_code'] = MsgBox(_t('_registration by invitation only'));
    PageCode();
    exit;
}

$_page['name_index'] = 81;
$_ni = $_page['name_index'];

$oJoinView = new BxTemplJoinPageView();
$_page_cont[$_ni]['page_main_code'] = $oJoinView->getCode();

$GLOBALS['oSysTemplate']->addJsTranslation('_Errors in join form');
$GLOBALS['oSysTemplate']->addJs(array('join.js', 'jquery.form.js'));
$GLOBALS['oSysTemplate']->addCss(array('join.css'));
PageCode();
?>
