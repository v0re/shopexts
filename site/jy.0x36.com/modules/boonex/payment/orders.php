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

bx_import('Module', $aModule);
bx_import('BxDolPageView');

class BxPmtOrdersPage extends BxDolPageView {
    var $_oPayments;
    var $_sType;

    function BxPmtOrdersPage($sType, &$oPayments) {
        parent::BxDolPageView('bx_pmt_orders');

        $this->_sType = $sType;
        $this->_oPayments = &$oPayments;
    }
    function getBlockCode_Orders() {
        if(empty($this->_sType))
            $this->_sType = BX_PMT_ORDERS_TYPE_PROCESSED;

        return $this->_oPayments->getOrdersBlock($this->_sType);
    }
}

global $_page;
global $_page_cont;
global $logged;

$iIndex = 3;
$_page['name_index'] = $iIndex;
$_page['css_name'] = 'orders.css';
$_page['js_name'] = 'orders.js';

check_logged();

$sType = '';
if(!empty($aRequest)) 
    $sType = process_db_input(array_shift($aRequest), BX_TAGS_STRIP);

$oPayments = new BxPmtModule($aModule);
$oOrdersPage = new BxPmtOrdersPage($sType, $oPayments);
$_page_cont[$iIndex]['page_main_code'] = $oOrdersPage->getCode();
$_page_cont[$iIndex]['more_code'] = $oPayments->getMoreWindow();
$_page_cont[$iIndex]['manual_order_code'] = $oPayments->getManualOrderWindow();
$_page_cont[$iIndex]['js_code'] = $oPayments->getExtraJs('orders');

$oPayments->_oTemplate->setPageTitle(_t('_payment_pcaption_view_orders'));
PageCode($oPayments->_oTemplate);
?>