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

bx_import('BxDolMistake');

class BxDolAdminMenu extends BxDolMistake {

	/**
	 * constructor
	 */
	function BxDolAdminMenu() {
	    parent::BxDolMistake();
	}
    
	function getTopMenu() {
        $aItems = $GLOBALS['MySQL']->getAll("SELECT `caption`, `url`, `icon` FROM `sys_menu_admin_top` ORDER BY `Order`");
        
        $aItemsResult = array();
        foreach($aItems as $aItems)
        	$aItemsResult[] = array(
                'caption' => _t($aItems['caption']),
                'url' => str_replace(
                    array(
                        '{site_url}',
                        '{admin_url}'                    
                    ),
                    array(
                        $GLOBALS['site']['url'],
                        $GLOBALS['site']['url_admin'],
                    ),
                    $aItems['url']
                ),
                'icon' => $GLOBALS['oAdmTemplate']->getIconUrl($aItems['icon'])
        	);
        return $GLOBALS['oAdmTemplate']->parseHtmlByName('top_menu.html', array('bx_repeat:items' => $aItemsResult));
    }
    function getMainMenu() {
    	if(!isAdmin())
            return '';

        $sUri = rtrim(BX_DOL_URL_ROOT, "/") . $_SERVER['REQUEST_URI'];
    	$sFile = basename($_SERVER['PHP_SELF']);

    	$oPermalinks = new BxDolPermalinks();
    	$aMenu = $GLOBALS['MySQL']->getAll("SELECT `id`, `name`, `title`, `url`, `icon` FROM `sys_menu_admin` WHERE `parent_id`='0' ORDER BY `order`" );
    
    	$aItems = array();
    	foreach($aMenu as $aMenuItem) {
    	    $aMenuItem['url'] = str_replace(array('{siteUrl}', '{siteAdminUrl}'), array(BX_DOL_URL_ROOT, BX_DOL_URL_ADMIN), $aMenuItem['url']);

    	    $bActiveCateg = !defined('BX_DOL_ADMIN_INDEX') && $sFile == 'index.php' && (!empty($_GET['cat'])) && $_GET['cat'] == $aMenuItem['name'];
    		$aSubmenu = $GLOBALS['MySQL']->getAll("SELECT * FROM `sys_menu_admin` WHERE `parent_id`='" . $aMenuItem['id'] . "' ORDER BY `order`");
    
    		$aSubitems = array();
    		foreach($aSubmenu as $aSubmenuItem) {
    		    $aSubmenuItem['url'] = $oPermalinks->permalink($aSubmenuItem['url']);
    		    $aSubmenuItem['url'] = str_replace(array('{siteUrl}', '{siteAdminUrl}'), array(BX_DOL_URL_ROOT, BX_DOL_URL_ADMIN), $aSubmenuItem['url']);

    		    if(!defined('BX_DOL_ADMIN_INDEX') && $aSubmenuItem['url'] != '' && (strpos($sUri, $aSubmenuItem['url']) !== false || strpos($aSubmenuItem['url'], $sUri) !== false))
    		        $bActiveCateg = $bActiveItem = true;
                else 
                    $bActiveItem = false;
    		    
    			$aSubitems[] = BxDolAdminMenu::_getMainMenuSubitem($aSubmenuItem, $bActiveItem);			
    		}		
    		$aItems[] = BxDolAdminMenu::_getMainMenuItem($aMenuItem, $aSubitems, $bActiveCateg);
    	}
    	return $GLOBALS['oAdmTemplate']->parseHtmlByName('main_menu.html', array('bx_repeat:items' => $aItems));
    }
    function getMainMenuLink($sUrl) {
        if(substr($sUrl, 0, 11) == 'javascript:') {
            $sLink = 'javascript:void(0);';
            $sOnClick = 'onclick="' . $sUrl . '"';
    	}
    	else {
    	    $sLink = $sUrl;
            $sOnClick = '';
    	}
    
    	$aAdminProfile = getProfileInfo();
    	$aVariables = array(
            'adminLogin' => $aAdminProfile['NickName'],
            'adminPass' => $aAdminProfile['Password']
    	);
    	$sLink = $GLOBALS['oAdmTemplate']->parseHtmlByContent($sLink, $aVariables, array('{', '}'));
    	$sOnClick = $GLOBALS['oAdmTemplate']->parseHtmlByContent($sOnClick, $aVariables, array('{', '}'));    
    
    	return array($sLink, $sOnClick);
    }
    function _getMainMenuItem($aCateg, $aItems, $bActive) {    
    	global $oAdmTemplate;
    
    	$sAdminUrl = $GLOBALS['site']['url_admin'];
    	
    	$sClass = "";
    	if($bActive && !empty($aItems))
            $sClass = 'adm-mmh-opened';
        else if($bActive && empty($aItems))
            $sClass = 'adm-mmh-active';
            
        $sLink = "";
        if(!empty($aCateg['url']))
            $sLink = $aCateg['url'];
        else if($aCateg['id'])
            $sLink = $sAdminUrl . "index.php?cat=" . $aCateg['name'];
        else
            $sLink = $sAdminUrl . "index.php";
    
        return array(
            'class' => $sClass,
            'icon' => $oAdmTemplate->getIconUrl($aCateg['icon']),
            'bx_if:collapsible' => array(
                'condition' => !empty($aItems),
                'content' => array(
                    'class' => ($bActive && !empty($aItems) ? 'adm-mma-opened' : '')
                )
            ),
            'bx_if:item-text' => array(
                'condition' => $bActive,
                'content' => array(
                    'title' => _t($aCateg['title'])
                )
            ),
            'bx_if:item-link' => array(
                'condition' => !$bActive,
                'content' => array(
                    'link' => $sLink,
                    'title' => _t($aCateg['title'])
                )
            ),        
            'bx_if:submenu' => array(
                'condition' => !empty($aItems),
                'content' => array(
                    'id' => $aCateg['id'],
                    'class' => ($bActive && !empty($aItems) ? 'adm-mmi-opened' : ''),
                    'bx_repeat:subitems' => $aItems
                )
            )
    	);	
    }    
    
    function _getMainMenuSubitem($aItem, $bActive) {
    	global $oAdmTemplate;
    	
    	if(strlen($aItem['check']) > 0) {
    		$oFunction = create_function( '', $aItem['check'] );
    		if(!$oFunction())
    			return '';        
    	}
    	
    	if(!$bActive)
        	list($sLink, $sOnClick) = BxDolAdminMenu::getMainMenuLink($aItem['url']);
            
    	return array(
            'icon' => $GLOBALS['oAdmTemplate']->getIconUrl($aItem['icon']),
            'bx_if:subitem-text' => array(
                'condition' => $bActive,
                'content' => array(
                    'title' => _t($aItem['title'])
                )
            ),
            'bx_if:subitem-link' => array(
                'condition' => !$bActive,
                'content' => array(
                    'link' => empty($sLink) ? '' : $sLink,
                    'onclick' => empty($sOnClick) ? '' : $sOnClick,
                    'title' => _t($aItem['title'])
                )
            )
        );
    }
}

?>