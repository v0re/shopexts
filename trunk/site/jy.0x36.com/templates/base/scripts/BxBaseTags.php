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

require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolTags.php');

class BxBaseTags extends BxDolTags {
    
    var $_sTagTmplName;
    var $_sTagTmplContent;
    
    function BxBaseTags () {
        parent::BxDolTags();
        
        $this->_sTagTmplName = 'view_tags.html';
        $this->_sTagTmplContent = '';
    }
    
    function getTagsView ($aTotalTags, $sHrefTempl) {
        global $oTemplConfig;
        global $oSysTemplate;
        
        if (empty($aTotalTags))
            return MsgBox(_t('_Empty'));
        
        $iMinFontSize = $oTemplConfig -> iTagsMinFontSize;
        $iMaxFontSize = $oTemplConfig -> iTagsMaxFontSize;
        $iFontDiff = $iMaxFontSize - $iMinFontSize;

        $iMinRating = min( $aTotalTags );
        $iMaxRating = max( $aTotalTags );

        $iRatingDiff = $iMaxRating - $iMinRating;
        $iRatingDiff = ($iRatingDiff==0)? 1:$iRatingDiff;

        $sCode = '<div class="tags_wrapper">';
        $aUnit = array();
        foreach( $aTotalTags as $sTag => $iCount ) {
            $aUnit['tagSize'] = $iMinFontSize + round( $iFontDiff * ( ( $iCount - $iMinRating ) / $iRatingDiff ) );
            $aUnit['tagHref'] = str_replace( '{tag}', urlencode(title2uri($sTag)), $sHrefTempl);
            $aUnit['countCapt'] = _t( '_Count' );
            $aUnit['countNum'] = $iCount;
            $aUnit['tag'] = htmlspecialchars_adv( $sTag );
            if ($this->_sTagTmplContent)
                $sCode .= $oSysTemplate->parseHtmlByContent($this->_sTagTmplContent, $aUnit);
            else
                $sCode .= $oSysTemplate->parseHtmlByName($this->_sTagTmplName, $aUnit);
        }
        $sCode .= '</div>';
        $sCode .= '<div class="clear_both"></div>';      
        return $sCode;
    }

    function getTagsTopMenu ($aParam, $sAction = '') 
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
            
            $aTopMenu[$sName] = array('href' => $sHref, 'key' => $sKey, 'dynamic' => true, 'active' => ( $sKey == $aParam['type']));
        }
            
        return $aTopMenu;
    }
    
    function getTagsTopMenuHtml ($aParam, $iBoxId, $sAction = '') 
    {
        $aItems = array();
        
        $aTopMenu = $this->getTagsTopMenu($aParam, $sAction);
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
    
    function getTagsInternalMenuHtml ($aParam, $iBoxId, $sAction = '')
    {
        global $oSysTemplate;
        $sCode = '';
        $sMenu = '';
        
        $sMenu = _t('_tags_caption_module') . ' <select onchange="loadDynamicBlock(' . $iBoxId . ', this.value);">';
        $aMenu = $this->getTagsTopMenu($aParam, $sAction);
        
        foreach ($aMenu as $sName => $aItem)
        {
            $sMenu .= '<option value="' . $aItem['href'] . '" '. 
                ($aItem['key'] == $aParam['type'] ? 'selected' : '') .'>' . 
                $sName . '</option>';
        }
        
        $sMenu .= '</select>';
        
        $sCode = $oSysTemplate->parseHtmlByName('top_block.html', array('code' => $sMenu));
        
        return $sCode;
    }
    
    function display($aParam, $iBoxId, $sAction = '', $sUrl = '')
    {
        $sPaginate = '';
        
        if (!isset($aParam['type']) || !$aParam['type'])
            return MsgBox(_t( '_Empty' ));
        
        if (isset($aParam['pagination']) && $aParam['pagination'])
        {
            bx_import('BxDolPaginate');
            $sPageUrl = $sUrl ? $sUrl : bx_html_attribute($_SERVER['PHP_SELF']);
            $sPageUrl .= '?tags_mode=' . $aParam['type'] . '&page={page}&per_page={per_page}';
                    
            if (isset($aParam['filter']) && $aParam['filter'])
                $sPageUrl .= '&filter=' . $aParam['filter'];
            if ($sAction)
                $sPageUrl .= '&action=' . $sAction;
            if (isset($aParam['date']) && $aParam['date'])
            {
                $sPageUrl .= '&year=' . $aParam['date']['year'] . 
                    '&month=' . $aParam['date']['month'] . 
                    '&day=' . $aParam['date']['day'];
            }
                
            $aPaginate = array(
                'page_url' => $sPageUrl,
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
            return $this->getTagsView($aTotalTags, $sHrefTmpl) . $sPaginate;
        else
            return MsgBox(_t( '_Empty' ));
    }
    
    function setTemplateName($sTmplName)
    {
        $this->_sTagTmplName = $sTmplName;
    }
    
    function setTemplateContent($sTmplContent)
    {
        $this->_sTagTmplContent = $sTmplContent;
    }
}

?>
