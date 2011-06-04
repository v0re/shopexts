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

require_once(BX_DIRECTORY_PATH_INC . 'design.inc.php');
require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolSearch.php');
require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolPaginate.php');

class BxBaseSearchResult extends BxDolSearchResult {
	var $aPermalinks;
	var $aConstants;

    function BxBaseSearchResult() {
        parent::BxDolSearchResult();
    }

	function isPermalinkEnabled() {
       return isset($this->_isPermalinkEnabled) ? $this->_isPermalinkEnabled : ($this->_isPermalinkEnabled = (getParam($this->aPermalinks['param']) == 'on'));
    }
    
   	function getCurrentUrl ($sType, $iId, $sUri, $aOwner = '') {
   	    $sLink = $this->aConstants['linksTempl'][$sType];
		$sLink = str_replace('{id}', $iId, $sLink);
		$sLink = str_replace('{uri}', $sUri, $sLink);
		if (is_array($aOwner) && !empty($aOwner)) {
			$sLink = str_replace('{ownerName}', $aOwner['ownerName'], $sLink);
			$sLink = str_replace('{ownerId}', $aOwner['ownerId'], $sLink);
		}
		return $GLOBALS['site']['url'] . $sLink;
	}
	
    function displayResultBlock () {
        $sCode = '';
        $aData = $this->getSearchData();
        if ($this->aCurrent['paginate']['totalNum'] > 0) {
            $sCode .= $this->addCustomParts();
            foreach ($aData as $aValue) {
                $sCode .= $this->displaySearchUnit($aValue);
            }
            $sCode = '<div class="result_block">' . $sCode . '<div class="clear_both"></div></div>';
        }
        return $sCode;
    }
	
    function displaySearchBox ($sCode, $sPaginate = '', $bAdminBox = false) {
        $sMenu = '';
        if (isset($this->aCurrent['rss']) && $this->aCurrent['rss']['link']) {
            bx_import('BxDolPageView');
            $sMenu = BxDolPageView::getBlockCaptionItemCode(time(), array(_t('RSS') => array('href' => $this->aCurrent['rss']['link'] . (false === strpos($this->aCurrent['rss']['link'], '?') ? '?' : '&') . 'rss=1', 'icon' => getTemplateIcon('rss.png'))));
        }
		$sTitle = _t($this->aCurrent['title']);
		if (!$bAdminBox) {
			$sCode = DesignBoxContent($sTitle, $sCode. $sPaginate, 1, $sMenu);			
		}
		else {
			$sCode = DesignBoxAdmin($sTitle, $sCode, '', $sPaginate, 1);
		}
	    if (!isset($_GET['searchMode']))
            $sCode = '<div id="page_block_'.$this->id.'">'.$sCode.'<div class="clear_both"></div></div>';
		return $sCode;
	}
	
	function _transformData ($aUnit, $sTempl, $sCssHeader = '') {
	    foreach ($aUnit as $sKey => $sValue)
			$sTempl = str_replace('{'.$sKey.'}', $sValue, $sTempl);
		
		$sCssHeader = strlen($sCssHeader) > 0 ?  $sCssHeader : 'text_Unit';
		$sTempl =  str_replace('{unitClass}', $sCssHeader, $sTempl);
		return $sTempl;
	}

    function showAdminActionsPanel($sWrapperId, $aButtons, $sCheckboxName = 'entry', $bSelectAll = true, $bSelectAllChecked = false, $sCustomHtml = '') {
        $aBtns = array();
        foreach ($aButtons as $k => $v) {
            if(is_array($v)) {
                $aBtns[] = $v;
                continue;
            }
            $aBtns[] = array(
                'type' => 'submit',
                'name' => $k,
                'value' => '_' == $v[0] ? _t($v) : $v,
                'onclick' => '',
            );            
        }
        $aUnit = array(
            'bx_repeat:buttons' => $aBtns,
            'bx_if:customHTML' => array(
                'condition' => strlen($sCustomHtml) > 0,
                'content' => array(
                    'custom_HTML' => $sCustomHtml,
                )
            ),
            'bx_if:selectAll' => array(
                'condition' => $bSelectAll,
                'content' => array(
                    'wrapperId' => $sWrapperId,
                    'checkboxName' => $sCheckboxName,
                    'checked' => ($bSelectAll && $bSelectAllChecked ? 'checked="checked"' : '')
                )
            ),
        );
        return $GLOBALS['oSysTemplate']->parseHtmlByName('adminActionsPanel.html', $aUnit, array('{','}'));
    }

    function showAdminFilterPanel($sFilterValue, $sInputId = 'filter_input_id', $sCheckboxId = 'filter_checkbox_id', $sFilterName = 'filter', $sOnApply = '') {

        $sFilter = _t('_sys_admin_filter');
        $sApply = _t('_sys_admin_apply');

        $sFilterValue = bx_html_attribute($sFilterValue);
        $isChecked = $sFilterValue ? ' checked="checked" ' : '';

        $sJsContent = "";
        if(empty($sOnApply)) {
            $sOnApply = 'on_filter_apply(this)';
            ob_start();
?>
    <script type="text/javascript">
        function on_filter_apply (e) {
            var s = ('' + document.location).replace (/[&]{0,1}<?=$sFilterName;?>=.*/, ''); // remove filter
            s = s.replace(/page=\d+/, 'page=1'); // goto 1st page
            if (e.checked && $('#<?=$sInputId;?>').val().length > 2)
                s += (-1 == s.indexOf('?') ? '?' : '&') + '<?=$sFilterName;?>=' + $('#<?=$sInputId;?>').val(); // append filter
            document.location = s;
        }
    </script>
<?
            $sJsContent = ob_get_clean();
        }
        
        return <<<EOF
    {$sJsContent}
    <div class="admin_filter_panel">
        <table>
            <tr>
                <td>{$sFilter}</td>
                <td>
                    <div class="input_wrapper input_wrapper_text">
                        <input type="text" id="{$sInputId}" value="{$sFilterValue}" class="form_input_text" onkeyup="$('#{$sCheckboxId}').attr('checked', '')" />
                        <div class="input_close input_close_text">&nbsp;</div>
                    </div>
                </td>
                <td><input type="checkbox" id="{$sCheckboxId}" $isChecked onclick="{$sOnApply}" /></td>
                <td><label for="{$sCheckboxId}">{$sApply}</label></td>
            </tr>
        </table>
    </div>
EOF;
    }

	function showPagination($bAdmin = false, $bChangePage = true, $bPageReload = true) {
        $sPageLink = $this->getCurrentUrl('browseAll', 0, '');
        $aLinkAddon = $this->getLinkAddByPrams();

        if ($aLinkAddon) {
           foreach($aLinkAddon as $sValue)
                $sPageLink .= $sValue;
        }
 
        if(!$this->id)
            $this->id = 0;
        
        $sLoadDynamicUrl = $this->id .', \'searchKeywordContent.php?searchMode=ajax&section[]=' . $this->aCurrent['name'] . $aLinkAddon['params'];
        $sKeyword = bx_get('keyword');
        if ($sKeyword !== false && mb_strlen($sKeyword) > 0)
        	$sLoadDynamicUrl .= '&keyword=' . rawurlencode($sKeyword);
        $oPaginate = new BxDolPaginate(array(
            'page_url' => $sPageLink,
            'count' => $this->aCurrent['paginate']['totalNum'],
            'per_page' => $this->aCurrent['paginate']['perPage'],
            'page' => $this->aCurrent['paginate']['page'],
            'per_page_changer' => true,
            'page_reloader' => true,
            'on_change_page' => !$bPageReload ? '' : 'return !loadDynamicBlock(' . $sLoadDynamicUrl . $aLinkAddon['paginate'].'\');',
            'on_change_per_page' => !$bChangePage ? '' : 'return !loadDynamicBlock(' . $sLoadDynamicUrl .'&page=1&per_page=\' + this.value);',
        ));
        return '<div class="clear_both"></div>' . $oPaginate->getPaginate();
    }
    
    function getLinkAddByPrams ($aExclude = array()) {
        $aExclude[] = '_r';
        $aExclude[] = 'pageBlock';
        $aExclude[] = 'searchMode';
        $aExclude[] = 'section';
        $aExclude[] = 'keyword';
        $aLinks = array();
        $aCurrParams = array();
        $aParams = array();
        
        foreach ($this->aCurrent['restriction'] as $sKey => $aValue) {
			if (isset($aValue['paramName'])) {
                if (is_array($aValue['value']))
					$aCurrParams[$aValue['paramName']] = $aValue['value'];
				elseif (mb_strlen($aValue['value']) > 0)
					$aCurrParams[$aValue['paramName']] = $aValue['value'];
			}
        }
        
        // add get params
        foreach ($_GET as $sKey => $sValue) {
        	if (!in_array($sKey, $aExclude))
                $aParams[rawurlencode($sKey)] = rawurlencode($sValue);
        }
        $aParams = array_merge($aParams, $aCurrParams);
        $aLinks = array('params'=>'', 'paginate'=>'');
        foreach ($aParams as $sKey => $sValue) {
            if ($sKey != 'page' && $sKey != 'per_page')
                $aLinks['params'] .= '&'.$sKey.'='.$sValue;
        }
        //paginate
        $aLinks['paginate'] .= '&page={page}';
        $aLinks['paginate'] .= '&per_page={per_page}';
        return $aLinks;
    }
    
    function clearFilters ($aPassParams = array(), $aPassJoins = array()) {
        //clear sorting
        $this->aCurrent['sorting'] = 'last';
        //clear restrictions
        foreach ($this->aCurrent['restriction'] as $sKey => $aValue) {
            if (!in_array($sKey, $aPassParams))
                $this->aCurrent['restriction'][$sKey]['value'] = '';
        }
        //clear unnecessary joins (remains only profile join)
        $aPassJoins[] = 'profile';
        $aTemp = array();
        foreach ($aPassJoins as $sValue) {
            if (isset($this->aCurrent['join'][$sValue]) && is_array($this->aCurrent['join'][$sValue]))
                $aTemp[$sValue] = $this->aCurrent['join'][$sValue]; 
        }
        $this->aCurrent['join'] = $aTemp;    
    }
    
    function fillFilters ($aParams) {
        // transform all given values to fields values
        if (is_array($aParams)) {
            foreach ($aParams as $sKey => $mixedValue) {
                if (isset($this->aCurrent['restriction'][$sKey]))
                    $this->aCurrent['restriction'][$sKey]['value'] = $mixedValue;
            }
        }
    }
    
    function getTopMenu ($aExclude = array()) {
    
    }
    
    function getBottomMenu ($sAllLinkType = 'browseAll', $iId = 0, $sUri = '', $aExclude = array()) {
        if (strpos($sAllLinkType, 'http') === false) {
	        if (isset($this->aConstants['linksTempl'][$sAllLinkType]))
	            $sAllUrl = $this->getCurrentUrl($sAllLinkType, $iId, $sUri);
	        else
	            $sAllUrl = $this->getCurrentUrl('browseAll', 0, '');
        }
        else
        	$sAllUrl = $sAllLinkType;
        $sModeName = $this->aCurrent['name'] . '_mode';
        $sMode = isset($_GET[$sModeName]) ? '&' . $sModeName . '=' . rawurlencode($_GET[$sModeName]) : $sModeName . '=' . $this->aCurrent['sorting'];
        $aLinkAddon = $this->getLinkAddByPrams($aExclude);
        $sLink = bx_html_attribute($_SERVER['PHP_SELF']);
        $oPaginate = new BxDolPaginate(array(
            'page_url' => $this->getCurrentUrl($sAllUrl, 0, ''),
            'count' => $this->aCurrent['paginate']['totalNum'],
            'per_page' => $this->aCurrent['paginate']['perPage'],
            'page' => $this->aCurrent['paginate']['page'],
            'per_page_changer' => true,
            'page_reloader' => true,
            'on_change_page' => 'return !loadDynamicBlock({id}, \'' . $sLink . '?' . $sMode . $aLinkAddon['params'] . $aLinkAddon['paginate'] . '\');',
        ));
        return $oPaginate->getSimplePaginate($sAllUrl);
    }
    
    function getBrowseBlock ($aParams, $aCustom = array(), $sMainUrl = '', $bClearJoins = true) {
        $aJoins = $bClearJoins ? array('albumsObjects', 'albums') : array_keys($this->aCurrent['join']);
		$this->clearFilters(array('activeStatus', 'albumType', 'album_status', 'ownerStatus'), $aJoins);
        $this->addCustomParts();
        $aCustomTmpl = array(
            'enable_center' => true,
            'unit_css_class' => ' > div:not(.clear_both)',
            'page' => 1,
            'per_page' => 10,
			'sorting' => 'last',
            'simple_paginate' => true,
            'dynamic_paginate' => true,
            'menu_top' => false,
            'menu_bottom' => true,
            'menu_bottom_type' => 'browseAll',
            'menu_bottom_param'=> ''
        );
        $aCustom = array_merge($aCustomTmpl, $aCustom);
        $this->aCurrent['paginate']['perPage'] = (int)$aCustom['per_page'];
        $this->aCurrent['paginate']['page'] = (int)$aCustom['page'];
		$this->aCurrent['sorting'] = $aCustom['sorting'];
        foreach ($aParams as $sKey => $mixedValues) {
            if (isset($this->aCurrent['restriction'][$sKey]))
                $this->aCurrent['restriction'][$sKey]['value'] = $mixedValues;
        }
        $aList = $this->getSearchData();
        $bWrap = true;
        if ($this->aCurrent['paginate']['totalNum'] > 0) {
            $bWrap = false;
        	foreach ($aList as $aData)
                $sCode .= $this->displaySearchUnit($aData);
            if ($aCustom['enable_center'])
                $sCode = $GLOBALS['oFunctions']->centerContent($sCode, $aCustom['unit_css_class']);
			if (mb_strlen($aCustom['wrapper_class']) > 0)
				$sCode = '<div class="' . $aCustom['wrapper_class'] . '">' . $sCode . '</div>';
            if ($aCustom['dynamic_paginate']) {
        		$aExclude = array($this->aCurrent['name'] . '_mode', 'r');
            	$aLinkAddon = $this->getLinkAddByPrams($aExclude);
                $sOnChange = 'return !loadDynamicBlock({id}, \'' . $sMainUrl . $aLinkAddon['params'] . $aLinkAddon['paginate'] . '\');';
            }
        }
        $aMenuTop = $aCustom['menu_top'] ? $this->getTopMenu($aExclude): array();
        $sMenuBottom = $aCustom['menu_bottom'] ? $this->getBottomMenu($aCustom['menu_bottom_type'], 0, $this->aCurrent['restriction'][$aCustom['menu_bottom_param']]['value']): array();
        return array('code' => $sCode, 'menu_top'=> $aMenuTop, 'menu_bottom' => $sMenuBottom, 'wrapper' => $bWrap);
    }
    
    function serviceGetBrowseBlock ($aParams, $sMainUrl = '', $aCustom = array()) {
        $aCode = $this->getBrowseBlock($aParams, $aCustom, $sMainUrl);
        return $aCode['code'] . $aCode['menu_bottom'];
    }
    
    /*
	 * Get number of all elements under specified search criterias
	 * @param array $aFilter - search criteria like 'restriction key'=>'rest. value'
	 * @param array $aJoin - list of joins elements from $this->aCurrent['join'] field which shouldn't be cleared, 
	   if empty then all current joins will be left
	 * @return integer number of found elements
	 */    
    function serviceGetAllCount ($aFilter, $aJoin = array()) {
    	if (is_array($aFilter)) {
    		// collect all current joins, but clear almost all search values
    		if (!is_array($aJoin) || empty($aJoin))
    			$aCurrJoins = array_keys($this->aCurrent['join']);
			else
				$aCurrJoins = $aJoin;
    		$this->clearFilters(array('activeStatus'), $aCurrJoins);
    		foreach ($aFilter as $sKey => $mixedValue) {
    			if (isset($this->aCurrent['restriction'][$sKey]))
    				$this->aCurrent['restriction'][$sKey]['value'] = $mixedValue;
    		}
    		return $this->getCount();
    	}
	}
}

?>
