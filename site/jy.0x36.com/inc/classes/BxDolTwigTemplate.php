<?
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

bx_import ('BxDolModuleTemplate');

/**
 * Base template class for modules like events/groups/store
 */
class BxDolTwigTemplate extends BxDolModuleTemplate {
    var $_iPageIndex = 13;
    var $_oMain = null;
    var $_bObStarted = 0;

    function BxDolTwigTemplate(&$oConfig, &$oDb, $sRootPath = BX_DIRECTORY_PATH_ROOT, $sRootUrl = BX_DOL_URL_ROOT) {
        parent::BxDolModuleTemplate($oConfig, $oDb, $sRootPath, $sRootUrl);

        if (isset($GLOBALS['oAdmTemplate']))
            $GLOBALS['oAdmTemplate']->addDynamicLocation($this->_oConfig->getHomePath(), $this->_oConfig->getHomeUrl());
    }

    // ======================= common functions

    function addCssAdmin ($sName) {        
        if (empty($GLOBALS['oAdmTemplate'])) 
            return;        
        $GLOBALS['oAdmTemplate']->addCss ($sName);
    }

    function addJsAdmin ($sName) {        
        if (empty($GLOBALS['oAdmTemplate'])) 
            return;
        $GLOBALS['oAdmTemplate']->addJs ($sName);
    }

    function parseHtmlByName ($sName, &$aVars) {        
        return parent::parseHtmlByName ($sName.'.html', $aVars);
    }

    // ======================= page generation functions
    
    function pageCode ($sTitle, $isDesignBox = true, $isWrap = true) {

        global $_page;        
        global $_page_cont;

        $_page['name_index'] = $isDesignBox ? 0 : $this->_iPageIndex; 

        $_page['header'] = $sTitle ? $sTitle : $GLOBALS['site']['title'];
        $_page['header_text'] = $sTitle;

        $_page_cont[$_page['name_index']]['page_main_code'] = $this->pageEnd();
        if ($isWrap) {
            $aVars = array (
                'content' => $_page_cont[$_page['name_index']]['page_main_code'],
            );
            $_page_cont[$_page['name_index']]['page_main_code'] = $this->parseHtmlByName('default_padding', $aVars);
        }

        $GLOBALS['oSysTemplate']->addDynamicLocation($this->_oConfig->getHomePath(), $this->_oConfig->getHomeUrl());
        PageCode($GLOBALS['oSysTemplate']);
    }

    function adminBlock ($sContent, $sTitle, $aMenu = array()) {
        return DesignBoxAdmin($sTitle, $sContent, $aMenu);
    }

    function pageCodeAdmin ($sTitle) {

        global $_page;        
        global $_page_cont;

        $_page['name_index'] = 9; 

        $_page['header'] = $sTitle ? $sTitle : $GLOBALS['site']['title'];
        $_page['header_text'] = $sTitle;
        
        $_page_cont[$_page['name_index']]['page_main_code'] = $this->pageEnd();

        PageCodeAdmin();
    }

    function pageStart () {
        if (0 == $this->_bObStarted)  {            
            ob_start ();
            $this->_bObStarted = 1;
        }
    }

    function pageEnd ($isGetContent = true) {
        if (1 == $this->_bObStarted)  {            
            $sRet = '';
            if ($isGetContent)
                $sRet = ob_get_clean();
            else
                ob_end_clean();
            $this->_bObStarted = 0;
            return $sRet;
        }
    }

    // ======================= tags/cat parsing functions

    function parseTags ($s) {
        return $this->_parseAnything ($s, ',', BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/tag/');
    }

    function parseCategories ($s) {
        bx_import ('BxDolCategories');
        return $this->_parseAnything ($s, CATEGORIES_DIVIDER, BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/category/');
    }

    function _parseAnything ($s, $sDiv, $sLinkStart, $sClassName = '') {
        $sRet = '';
        $a = explode ($sDiv, $s);
        $sClass = $sClassName ? 'class="'.$sClassName.'"' : '';
        foreach ($a as $sName)
            $sRet .= '<a '.$sClass.' href="' . $sLinkStart . title2uri($sName) . '">'.$sName.'</a> ';
        return $sRet;
    }

    // ======================= display standard pages functions
    
    function displayAccessDenied () {
        $this->pageStart();
        echo MsgBox(_t('_Access denied'));
        $this->pageCode (_t('_Access denied'), true, false);
    }    

    function displayNoData () {
        $this->pageStart();
        echo MsgBox(_t('_Empty'));
        $this->pageCode (_t('_Empty'), true, false);
    }    

    function displayErrorOccured () {
        $this->pageStart();
        echo MsgBox(_t('_Error Occured'));
        $this->pageCode (_t('_Error Occured'), true, false);
    }    

    function displayPageNotFound () {        
        header("HTTP/1.0 404 Not Found");
        $this->pageStart();
        echo MsgBox(_t('_sys_request_page_not_found_cpt'));
        $this->pageCode (_t('_sys_request_page_not_found_cpt'), true, false);
    }

    function displayMsg ($s, $isTranslate = false) {
        $this->pageStart();
        echo MsgBox($isTranslate ? _t($s) : $s);
        $this->pageCode ($isTranslate ? _t($s) : $s, true);
    }        

}
