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

bx_import('BxDolTemplate');

class BxDolTemplateAdmin extends BxDolTemplate {
	/**
	 * Constructor
	 */
	function BxDolTemplateAdmin($sHomeFolder) {
	    parent::BxDolTemplate(BX_DIRECTORY_PATH_ROOT . $sHomeFolder . DIRECTORY_SEPARATOR, BX_DOL_URL_ROOT . $sHomeFolder . '/');
	    
	    $this->_sPrefix = 'BxDolTemplateAdmin';
	    $this->_sInjectionsTable = 'sys_injections_admin';
	    $this->_sInjectionsCache = 'sys_injections_admin.inc';

	    $this->_sCodeKey = 'askin';
	    $this->_sCode = isset($_COOKIE[$this->_sCodeKey]) && preg_match('/^[A-Za-z0-9_-]+$/', $_COOKIE[$this->_sCodeKey]) ? $_COOKIE[$this->_sCodeKey] : BX_DOL_TEMPLATE_DEFAULT_CODE;
	    $this->_sCode = isset($_GET[$this->_sCodeKey]) && preg_match('/^[A-Za-z0-9_-]+$/', $_GET[$this->_sCodeKey]) ? $_GET[$this->_sCodeKey] : $this->_sCode;

	    $this->addLocationJs('system_admin_js', $this->_sRootPath . 'js/' , $this->_sRootUrl . 'js/');
	}
	
	/**
     * Parse system keys.
     *
     * @param string $sKey key
     * @return string value associated with the key.
     */
	function parseSystemKey($sKey, $mixedKeyWrapperHtml = null) {
		global $logged;

		$aKeyWrappers = $this->_getKeyWrappers($mixedKeyWrapperHtml);

		$sRet = '';
		switch( $sKey ) {
		    case 'version':
		        $sRet = $GLOBALS['site']['ver'];
		        break;
            case 'page_charset':
                $sRet = 'UTF-8'; 
                break;
            case 'page_keywords':
			    if(!empty($GLOBALS[$this->_sPrefix . 'PageKeywords']) && is_array($GLOBALS[$this->_sPrefix . 'PageKeywords']))
					$sRet = '<meta name="keywords" content="' . process_line_output(implode(',', $GLOBALS[$this->_sPrefix . 'PageKeywords'])) . '" />';
                break;
			case 'page_description':				
                if(!empty($GLOBALS[$this->_sPrefix . 'PageDescription']) && is_string($GLOBALS[$this->_sPrefix . 'PageDescription']))
                    $sRet = '<meta name="description" content="' . process_line_output($GLOBALS[$this->_sPrefix . 'PageDescription']) . '" />';
		        break;
            case 'page_header':
                if(!empty($GLOBALS[$this->_sPrefix . 'PageTitle']))
                    $sRet = $GLOBALS[$this->_sPrefix . 'PageTitle'];
                else if(isset($GLOBALS['_page']['header']))
                    $sRet = $GLOBALS['_page']['header'];

                $sRet = process_line_output($sRet);
			    break;
			case 'page_header_text':
			    if(!empty($GLOBALS[$this->_sPrefix . 'PageMainBoxTitle']))
                    $sRet = process_line_output($GLOBALS[$this->_sPrefix . 'PageMainBoxTitle']);
                else if(isset($GLOBALS['_page']['header_text']))
                    $sRet = $GLOBALS['_page']['header_text'];

                $sRet = process_line_output($sRet);
			    break;
            case 'main_div_width':                
			    if(!empty($GLOBALS[$this->_sPrefix . 'PageWidth']))
                    $sRet = process_line_output($GLOBALS[$this->_sPrefix . 'PageWidth']);
			    break;
            case 'top_menu':
                $sRet = BxDolAdminMenu::getTopMenu();
                break;
            case 'main_menu':
				$sRet = BxDolAdminMenu::getMainMenu();
                break;
            case 'dol_images':
                $sRet = $this->_processJsImages();
                break;
            case 'dol_lang':
                $sRet = $this->_processJsTranslations();
                break;
            case 'dol_options':
                $sRet = $this->_processJsOptions(); 
                break;
			case 'boonex_promo':
				if (getParam('enable_dolphin_footer'))
					$sRet = $this->parseHtmlByName('boonex_promo.html', array());
				break;
			case 'promo_code':
				if (defined('BX_PROMO_CODE'))
					$sRet = BX_PROMO_CODE;
				else
					$sRet = ' ';
				break;
			case 'copyright':
			    $sRet = _t( '_copyright',   date('Y') ) . getVersionComment(); 
			    break;
    		}

		$sRet = BxDolTemplate::processInjection($GLOBALS['_page']['name_index'], $sKey, $sRet);
		return $sRet;
	}
}
?>
