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

require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolCategories.php');

class BxBaseCategories extends BxDolCategories 
{
    var $_sCategTmplName;
    var $_sCategTmplContent;
    
    function BxBaseCategories () 
    {
        parent::BxDolCategories();
        
        $this->_sCategTmplName = 'view_categ.html';
        $this->_sCategTmplContent = '';
    }
    
    function getCategoriesView ($aTotalCategories, $sHrefTempl, $iColumns) 
    {
        global $oSysTemplate;
        
        if (empty($aTotalCategories))
            return MsgBox(_t( '_Empty' ));
            
        if (!$iColumns)
            $iColumns = 1;
        
        $iCount = count($aTotalCategories);
        $iRowCount = floor($iCount / $iColumns) + (($iCount % $iColumns) ? 1 : 0);
        $iWidthPr = floor(100 / $iColumns);
        $i = 0;
        $sCode = '<div class="categories_wrapper">';
        
        foreach( $aTotalCategories as $sCategory => $iCatCount )
        {
            if (!($i % $iRowCount))
            {
                if ($i)
                    $sCode .= '</div>';
                $sCode .= '<div class="categories_col" style="width: ' . $iWidthPr . '%">';
            }

            $aUnit['catHref'] = str_replace( '{tag}', urlencode(title2uri($sCategory)), $sHrefTempl);
            $aUnit['category'] = htmlspecialchars_adv($sCategory );
            $aUnit['count'] = $iCatCount;
            
            if ($this->_sCategTmplContent)
                $sCode .= $oSysTemplate->parseHtmlByContent($this->_sCategTmplContent, $aUnit);
            else
                $sCode .= $oSysTemplate->parseHtmlByName($this->_sCategTmplName, $aUnit);
            
            $i++;
        }
        
        $sCode .= '</div></div>';
        
        return $sCode;
    }
    
    function getCategTopMenu ($aParam, $sAction = '') 
    {
        $aTopMenu = array();
        $aParamTmp = $aParam;
        
        foreach ($this->aTagObjects as $sKey => $aTagUnit)
        {
            $sName = _t($aTagUnit['LangKey']);
            $sHref = bx_html_attribute($_SERVER['PHP_SELF']) . "?tags_mode=$sKey" . ($sAction ? '&action=' . $sAction : '');
            
            if (isset($aParam['filter']) && $aParam['filter'])
            {
                $aParamTmp['type'] = $sKey;
                $sName .= '(' . $this->getTagsCount($aParamTmp) . ')';
                $sHref .= '&filter=' . $aParam['filter'];
            }
            
            if (isset($aParam['date']) && $aParam['date'])
                $sHref .= '&year=' . $aParam['date']['year'] . 
                    '&month=' . $aParam['date']['month'] . 
                    '&day=' . $aParam['date']['day'];
            
            $aTopMenu[$sName] = array('href' => $sHref, 'dynamic' => true, 'active' => ( $sKey == $aParam['type']));
        }
            
        return $aTopMenu;
    }
    
    function getCategTopMenuHtml($aParam, $iBoxId, $sAction = '') 
    {
        $aItems = array();
        
        $aTopMenu = $this->getCategTopMenu($aParam, $sAction);
        foreach ($aTopMenu as $sName => $aItem)
        {
            $aItems[$sName] = array(                
                'dynamic' => true,
                'active' => $aItem['active'],
                'href' => $aItem['href']
            );
        }        
        return BxDolPageView::getBlockCaptionItemCode($iBoxId, $aItems);
    }
    
    function display($aParam, $iBoxId, $sAction = '', $bOrderPanel = false, $iColumns, $sUrl = '')
    {
        if (!isset($aParam['type']) || !$aParam['type'])
            return MsgBox(_t( '_Empty' ));
            
        $sPaginate = '';
        $sCode = '';
        $sPageUrl = $sUrl ? $sUrl : bx_html_attribute($_SERVER['PHP_SELF']);
        $sPageUrl .= '?tags_mode=' . $aParam['type'];
        
        if (!isset($aParam['orderby']) && isset($_REQUEST['orderby']) && $_REQUEST['orderby'])
            $aParam['orderby'] = $_REQUEST['orderby'];
                
        if (isset($aParam['filter']) && $aParam['filter'])
            $sPageUrl .= '&filter=' . $aParam['filter'];
        if (isset($aParam['orderby']) && $aParam['orderby'])
            $sPageUrl .= '&orderby=' . $aParam['orderby'];
        if ($sAction)
            $sPageUrl .= '&action=' . $sAction;
        if (isset($aParam['date']) && $aParam['date'])
        {
            $sPageUrl .= '&year=' . $aParam['date']['year'] . 
                '&month=' . $aParam['date']['month'] . 
                '&day=' . $aParam['date']['day'];
        }
        
        if (isset($aParam['pagination']) && $aParam['pagination'])
        {
            bx_import('BxDolPaginate');
            
            $aPaginate = array(
                'page_url' => $sPageUrl . '&page={page}&per_page={per_page}',
                'info' => true,
                'page_links' => true,
                'on_change_page' => "!loadDynamicBlock($iBoxId, this.href)"
            );
            
            $aParam['limit'] = $aPaginate['per_page'] = $aParam['pagination'];
            $aPaginate['count'] = $this->getTagsCount($aParam);
            $aPaginate['page'] = isset($_REQUEST['page']) ? (int)$_REQUEST['page'] : 1;
            $aParam['start'] = $aParam['limit'] * ($aPaginate['page'] - 1);
			if ($aParam['start'] <=0)
				$aParam['start'] = 0;
            
            $oPaginate = new BxDolPaginate($aPaginate);
            $sPaginate = '<div class="clear_both"></div>'.$oPaginate->getPaginate();
        }
        
        $sHrefTmpl = $this->getHrefWithType($aParam['type']);
        $aTotalTags = $this->getTagList($aParam);
        
        if ($aTotalTags)
        {
            if ($bOrderPanel)
                $sCode .= $this->_getTopBox($aParam, $iBoxId, $sPageUrl);
            $sCode .= $this->getCategoriesView($aTotalTags, $sHrefTmpl, $iColumns) . $sPaginate;
        }
        else
            return MsgBox(_t( '_Empty' ));
            
        return $sCode;
    }
    
    function setTemplateName($sTmplName)
    {
        $this->_sCategTmplName = $sTmplName;
    }
    
    function setTemplateContent($sTmplContent)
    {
        $this->_sCategTmplContent = $sTmplContent;
    }
    
    function _getTopBox($aParam, $iBoxId, $sPageUrl)
    {
        global $oSysTemplate;
        $sCode = '';
        
        $aValues = array(
            'none' => _t('_categ_order_none'), 
            'popular' => _t('_categ_order_popular'),
            'recent' => _t('_categ_order_recent')
        );
        $sTopCode = _t('_categ_order_order_by');
        $sTopCode .= '<select onchange="loadDynamicBlock(' . $iBoxId . ', \'' . $sPageUrl . '&orderby=\' + this.value + \'' . '\')">';
        foreach ($aValues as $sKey => $sVal)
            $sTopCode .= '<option value="' . $sKey . '" ' . ($sKey == $aParam['orderby'] ? 'selected' : '') . '>' . $sVal . '</option>';
        $sTopCode .= '</select>';
        $sCode = $oSysTemplate->parseHtmlByName('top_block.html', array('code' => $sTopCode));
        
        return $sCode;
    }
}

?>
