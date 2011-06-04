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

bx_import('BxDolTwigTemplate');

/*
 * Store module View
 */
class BxStoreTemplate extends BxDolTwigTemplate {

    var $_iPageIndex = 500;  
    
	/**
	 * Constructor
	 */
	function BxStoreTemplate(&$oConfig, &$oDb) {
        parent::BxDolTwigTemplate($oConfig, $oDb);
    }

    function unit ($aData, $sTemplateName, &$oVotingView) {

        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxStoreModule');

        if (!$this->_oMain->isAllowedView ($aData)) {
            $aVars = array ('extra_css_class' => 'bx_store_unit');
            return $this->parseHtmlByName('browse_unit_private', $aVars);
        }        

        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 

        $sPrice = '';
        if ('Free' == $aData['price_range'])
            $sPrice = _t ('_bx_store_free_product');
        else
            $sPrice = str_replace('.00', '', sprintf ($aData['price_range'], getParam('pmt_default_currency_sign'), getParam('pmt_default_currency_sign')));

        $aVars = array (            
            'id' => $aData['id'],
            'thumb_url' => $sImage ? $sImage : $this->getIconUrl('no-photo.png'), 
            'product_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
            'product_title' => $aData['title'],
            'created' => defineTimeInterval($aData['created']),
            'author' => $aData['author_id'] ? $aData['NickName'] : _t('_bx_store_admin'),
            'author_url' => $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);',
            'price_range' => $sPrice,
        );        

        $aVars['rate'] = $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;';

        return $this->parseHtmlByName($sTemplateName, $aVars);
    }

    // ======================= ppage compose block functions 

    function blockDesc (&$aDataEntry) {
        $aVars = array (
            'description' => $aDataEntry['desc'],
        );
        return $this->parseHtmlByName('block_description', $aVars);
    }

    function blockFiles (&$aData) {

        $iEntryId = $aData['id'];
        $aReadyMedia = array ();
        if ($iEntryId)
            $aReadyMedia = $GLOBALS['oBxStoreModule']->_oDb->getFiles($iEntryId, true);

        if (!$aReadyMedia)
            return '';

        $aVars = array (
            'bx_repeat:files' => array (),
        );

        $sCurrencySign = getParam('pmt_default_currency_sign');
        foreach ($aReadyMedia as $r) {

            $iMediaId = $r['media_id'];

            $a = BxDolService::call('files', 'get_file_array', array($iMediaId), 'Search');
            if (!$a['date'])
                continue;

            bx_import('BxTemplFormView');
            $oForm = new BxTemplFormView(array());

            $aInputBtnDownload = array (
                'type' => 'submit',
                'name' => 'bx_store_download',
                'value' => _t ('_bx_store_download'),
                'attrs' => array(
                    'onclick' => "window.open ('" . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "download/{$r['id']}','_self');",
                ),
            );

            $aVars['bx_repeat:files'][] = array (
                'id' => $iMediaId,
                'title' => $a['title'],
                'icon' => $a['file'],
                'price' => $sCurrencySign . ' ' . $r['price'],
                'for_group' => sprintf(_t('_bx_store_for_group'), $GLOBALS['oBxStoreModule']->getGroupName($r['allow_purchase_to'])),
                'date' => defineTimeInterval($a['date']),
                'bx_if:purchase' => array (
                    'condition' => $GLOBALS['oBxStoreModule']->isAllowedPurchase($r), 
                    'content' => array (
                        'btn_purchase' => BxDolService::call('payment', 'get_add_to_cart_link', array($r['author_id'], $this->_oConfig->getId(), $r['id'], 1)),
                    ),
                ),                
                'bx_if:download' => array (
                    'condition' => $GLOBALS['oBxStoreModule']->isAllowedDownload($r),
                    'content' => array (
                        'btn_download' => $oForm->genInputButton ($aInputBtnDownload),
                    ),
                ),
            );            
        }

        if (!$aVars['bx_repeat:files'])
            return '';

        return $this->parseHtmlByName('block_files', $aVars);
    }

    function blockFields (&$aDataEntry) {
        $sRet = '<table class="bx_store_fields">';
        bx_store_import ('FormAdd');        
        $oForm = new BxStoreFormAdd ($GLOBALS['oBxStoreModule'], $_COOKIE['memberID']);
        foreach ($oForm->aInputs as $k => $a) {
            if (!isset($a['display'])) continue;
            $sRet .= '<tr><td class="bx_store_field_name" valign="top">' . $a['caption'] . '<td><td class="bx_store_field_value">';
            if (is_string($a['display']) && is_callable(array($this, $a['display'])))
                $sRet .= call_user_func_array(array($this, $a['display']), array($aDataEntry[$k]));
            else
                $sRet .= $aDataEntry[$k];
            $sRet .= '<td></tr>';
        }
        $sRet .= '</table>';
        return $sRet;
    }
}

?>
