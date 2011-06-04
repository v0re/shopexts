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

bx_import ('BxDolModuleTemplate');

class BxAdsTemplate extends BxDolModuleTemplate {
	/*
	* Constructor.
	*/
	function BxAdsTemplate(&$oConfig, &$oDb) {
	    parent::BxDolModuleTemplate($oConfig, $oDb);

		$this->_aTemplates = array('unit_ads', 'category_unit', 'admin_manage_classifieds_form', 'filter_form', 'unit_subject_block', 'ad_of_day');
	}

	function loadTemplates() {
	    parent::loadTemplates();
	}

	function parseHtmlByTemplateName($sName, $aVariables) {
	    return $this->parseHtmlByContent($this->_aTemplates[$sName], $aVariables);
	}

    function displayAccessDenied () {
        return MsgBox(_t('_bx_ads_msg_access_denied'));
    }

    function pageCode($aPage = array(), $aPageCont = array(), $aCss = array(), $aJs = array(), $bAdminMode = false, $isSubActions = true) {
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
        if (!empty($aJs))
            $this->addJs($aJs);

        /*if ($isSubActions) {
            $aVars = array ('BaseUri' => $this->_oConfig->getBaseUri());
            $GLOBALS['oTopMenu']->setCustomSubActions($aVars, $this->_oConfig->getMainPrefix() . '_title', false);
        }*/

        if (!$bAdminMode)
            PageCode($this);
        else
            PageCodeAdmin();
    }

}

?>