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
	
	bx_import('BxDolModuleTemplate');
	
	class BxPollTemplate extends BxDolModuleTemplate {
		/**
		 * Constructor
		 */
		function BxPollTemplate(&$oConfig, &$oDb) 
	    {
		    parent::BxDolModuleTemplate($oConfig, $oDb);   
		}
	
	    // function of output
	    function pageCode ($aPage = array(), $aPageCont = array(), $aCss = array()) {
	
	        if (!empty($aPage)) {
	            foreach ($aPage as $sKey => $sValue)
	                $GLOBALS['_page'][$sKey] = $sValue;
	        }
	        if (!empty($aPageCont)) {
	            foreach ($aPageCont as $sKey => $sValue)
	                $GLOBALS['_page_cont'][$aPage['name_index']][$sKey] = $sValue;
	        }
	        if (!empty($aCss))
	            $this->addCss($aCss);
	
	        PageCode($this);
	    }
	
	    function adminBlock ($sContent, $sTitle, $aMenu = array()) {
	        return DesignBoxAdmin($sTitle, $sContent, $aMenu);
	    }
	
	    function pageCodeAdminStart()
	    {
	        ob_start();
	    }
	
	    function pageCodeAdmin ($sTitle) {
	
	        global $_page;        
	        global $_page_cont;
	
	        $_page['name_index'] = 9; 
	
	        $_page['header'] = $sTitle ? $sTitle : $GLOBALS['site']['title'];
	        $_page['header_text'] = $sTitle;
	        
	        $_page_cont[$_page['name_index']]['page_main_code'] = ob_get_clean();
	
	        PageCodeAdmin();
	    }
	
	    function defaultPage($sTitle, $sContent, $iPageIndex = 7) {
	
	        global $_page;        
	        global $_page_cont;
	
	        $_page['name_index'] = $iPageIndex; 
	
	        $_page['header'] = $sTitle ? $sTitle : $GLOBALS['site']['title'];
	        $_page['header_text'] = $sTitle;
	        
	        $_page_cont[$_page['name_index']]['page_main_code'] = $sContent;
	
	        PageCode();
	    }
	}