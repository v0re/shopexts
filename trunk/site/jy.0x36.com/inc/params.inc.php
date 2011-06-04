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

if(isLogged()) {
    $iLoggedId = (int)getLoggedId();
    if(file_exists(BX_DIRECTORY_PATH_ROOT . 'user' . $iLoggedId . '.php') && is_file(BX_DIRECTORY_PATH_ROOT . 'user' . $iLoggedId . '.php'))
        require_once( BX_DIRECTORY_PATH_CACHE . 'user' . $iLoggedId . '.php');
}

$GLOBALS['BxDolTemplateJsOptions'] = array();
$GLOBALS['BxDolTemplateJsTranslations'] = array();       
$GLOBALS['BxDolTemplateJsImages'] = array();

//--- Initialize template's engine ---//
require_once(BX_DIRECTORY_PATH_INC . 'languages.inc.php');
require_once(BX_DIRECTORY_PATH_CLASSES . "BxDolTemplate.php");

$oSysTemplate = new BxDolTemplate();
$oSysTemplate->init();
//--- Add default CSS ---//
$oSysTemplate->addCss(array(
	'common.css',
    'general.css',
    'anchor.css',
	'forms_adv.css',
	'login_form.css',
    'top_menu.css',
    'BxCustomProfileMenu.css'
));
//--- Add default JS ---//
$oSysTemplate->addJs(array(
    'jquery.js',
    'jquery.jfeed.js',
    'jquery.dimensions.js',
    'functions.js',
    'jquery.dolTopMenu.js',
    'jquery.dolRSSFeed.js',
    'jquery.float_info.js',
    'jquery.webForms.js',
    'jquery.form.js',
    'jquery.dolPopup.js',
    'common_anim.js',
    'login.js',
    'ie7_flash_fix.js',
    'BxDolVoting.js',
    'user_status.js',
));
//--- Add default options in JS output ---//
$oSysTemplate->addJsOption(array(
	'sys_user_info_timeout'
));
//--- Add default language keys in JS output ---// 
$oSysTemplate->addJsTranslation(array(
	'_Counter', 
	'_PROFILE_ERR'
));
//--- Add default icons in JS output ---// 
$oSysTemplate->addJsIcon(array(
	'clock' => 'clock.png',
	'wf_plus' => 'action_fave.png',
	'wf_minus' => 'action_block.png',
	'wf_other' => 'folder_add.png',
	'more' => 'more.png',
	'collapse_open' => 'toggle_down.png',
	'collapse_closed' => 'toggle_right.png'
));
//--- Add default images in JS output ---// 
$oSysTemplate->addJsImage(array(
	'loading'   => 'loading.gif'
));

/**
 * Backward compatibility.
 * @deprecated
 */
$tmpl = $oSysTemplate->getCode();

require_once( BX_DIRECTORY_PATH_ROOT . "templates/tmpl_" . $tmpl . "/scripts/BxTemplConfig.php" );
$oTemplConfig = new BxTemplConfig($site);
//--- Initialize template's engine ---//

if (defined('BX_PROFILER') && BX_PROFILER) require_once(BX_DIRECTORY_PATH_MODULES . 'boonex/profiler/classes/BxProfiler.php');


// if IP is banned - total block
if ((int)getParam('ipBlacklistMode') == 1 && bx_is_ip_blocked()) { 
    echo _t('_Sorry, your IP been banned'); 
    exit;
} 

?>
