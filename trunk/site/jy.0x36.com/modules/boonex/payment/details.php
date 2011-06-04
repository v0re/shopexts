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

class BxPmtDetailsPage extends BxDolPageView {
    var $_oPayments;

    function BxPmtDetailsPage(&$oPayments) {
        parent::BxDolPageView('bx_pmt_details');

        $this->_oPayments = &$oPayments;
    }
    function getBlockCode_Details() {
        return $this->_oPayments->getDetailsForm();
    }    
}

global $_page;
global $_page_cont;
global $logged;

$iIndex = 4;
$_page['name_index']	= $iIndex;
$_page['css_name']		= array();

check_logged();

$oPayments = new BxPmtModule($aModule);
$oDetailsPage = new BxPmtDetailsPage($oPayments);
$_page_cont[$iIndex]['page_main_code'] = $oDetailsPage->getCode();

$oPayments->_oTemplate->setPageTitle(_t('_payment_pcaption_details'));
PageCode($oPayments->_oTemplate);
?>