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

require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolModule.php');

class BxQuotesModule extends BxDolModule {

	// constructor
    function BxQuotesModule(&$aModule) {
        parent::BxDolModule($aModule);
    }

    function serviceGetQuoteUnit() {
        $oQuoteUnit = $this->_oDb->getRandomQuote();

        $sUnitText = process_text_output($oQuoteUnit['Text']);
        $sUnitAuthor = process_line_output($oQuoteUnit['Author']);

        $aVariables = array (
            'unit_text' => $sUnitText,
            'author' => $sUnitAuthor
        );

        $this->_oTemplate->addCss('unit.css');
        return $this->_oTemplate->parseHtmlByTemplateName('unit', $aVariables);
    }

    function actionAdministration($sSubaction = '', $sID = 0) {
        global $_page, $_page_cont;

        require_once(BX_DIRECTORY_PATH_INC . 'admin_design.inc.php');
        $logged['admin'] = member_auth( 1, true, true );

        $iUnitID = ($sSubaction == 'edit' && (int)$sID > 0) ? (int)$sID : 0;
        $iUnitID = (bx_get('action') == 'edit' && (int)bx_get('ID') > 0) ? (int)bx_get('ID') : $iUnitID;

        if (isset($_POST['quotes_list'])  && is_array($_POST['quotes_list'])) { // manage subactions
        	foreach($_POST['quotes_list'] as $sQuoteId) {
        		$iQuoteId = (int)$sQuoteId;
         		switch (true) {
        			case isset($_POST['action_delete']):
        				$this->_oDb->deleteUnit($iQuoteId);
        				break;
        		}
         	}
        }

        $iNameIndex = 9;
        $_page = array(
            'name_index' => $iNameIndex,
            'css_name' => array(),
            'js_name' => array(),
            'header' => _t('_adm_page_cpt_quotes'),
            'header_text' => _t('_adm_box_cpt_quotes')
        );
        $_page_cont[$iNameIndex]['page_main_code'] = $this->getPostForm($iUnitID);
        $_page_cont[$iNameIndex]['page_main_code'] .= $this->getQuotesList();

        PageCodeAdmin();
    }

    function getPostForm($iUnitID = 0) {
        $sAddNewC = _t('_bx_quotes_add_new');

        $sAction = 'add';
        $sQText = $sQText = '';

        if ($iUnitID) {
            $aQinfo = $this->_oDb->getQuote($iUnitID);
            $sQText = $aQinfo['Text'];
            $sQAuthor = $aQinfo['Author'];
            $sAction = 'edit';
        }

		$aForm = array(
			'form_attrs' => array(
				'name' => 'create_quotes_form',
				'action' => BX_DOL_URL_ROOT . 'modules/?r=quotes/administration/',
				'method' => 'post',
			),
			'params' => array (
				'db' => array(
					'table' => 'bx_quotes_units',
					'key' => 'ID',
					'submit_name' => 'add_button',
				),
			),
			'inputs' => array(
                'action' => array(
                    'type' => 'hidden',
                    'name' => 'action',
                    'value' => $sAction,
                ),
				'Text' => array(
					'type' => 'textarea',
					'name' => 'Text',
					'caption' => _t('_bx_quotes_text'),
					'required' => true,
                    'value' => $sQText,
					'checker' => array (
						'func' => 'length',
						'params' => array(3,1024),
						'error' => _t('_bx_quotes_text_err', 1024),
					),
					'db' => array (
						'pass' => 'Xss', 
					),
				),
				'Author' => array(
					'type' => 'text',
					'name' => 'Author',
					'caption' => _t('_bx_quotes_author'),
					'required' => true,
                    'value' => $sQAuthor,
					'checker' => array (
						'func' => 'length',
						'params' => array(3,128),
						'error' => _t('_bx_quotes_author_err', 128),
					),
					'db' => array (
						'pass' => 'Xss', 
					),
				),
				'add_button' => array(
					'type' => 'submit',
					'name' => 'add_button',
					'value' => _t('_Submit'),
				),
            ),
        );

        if ($iUnitID) {
			$aForm['inputs']['hidden_unitid'] = array(
				'type' => 'hidden',
				'name' => 'ID',
				'value' => $iUnitID,
			);
        }

        $sCode = '';

		$oForm = new BxTemplFormView($aForm);
		$oForm->initChecker();
		if ($oForm->isSubmittedAndValid()) {

            $sCode = MsgBox(_t('_bx_quotes_fail'), 1);

			$aValsAdd = array ();

			$iLastId = -1;
			if ($iUnitID>0) {
				$oForm->update($iUnitID, $aValsAdd);
				$iLastId = $iUnitID;
                $sCode = MsgBox(_t('_bx_quotes_edited_success'), 1);
			} else {
				$iLastId = $oForm->insert($aValsAdd);
                $sCode = MsgBox(_t('_bx_quotes_success'), 1);
			}
		}

        return DesignBoxAdmin($sAddNewC, $sCode . $oForm->getCode());
    }

    function getQuotesList() {
        $sExistedC = _t('_bx_quotes_existed_list');

        if (isset($GLOBALS['oAdmTemplate'])) {
			$GLOBALS['oAdmTemplate']->addDynamicLocation($this->_oConfig->getHomePath(), $this->_oConfig->getHomeUrl());
        	$GLOBALS['oAdmTemplate'] -> addCss('unit.css');
        }
        
        $sAction = BX_DOL_URL_ROOT . 'modules/?r=quotes/administration/';

        $iCnt = 0;
        $aAllQuotes = $this->_oDb->getAllQuotes();
        foreach ($aAllQuotes as $iID => $aQuoteInfo) {
            $sBgClr = ($iID % 2 == 0) ? '#EEE' : '#FFF';
    		$iQId = (int)$aQuoteInfo['ID'];
    		$sQText = process_line_output($aQuoteInfo['Text']);
    		$sQAuthor = process_line_output($aQuoteInfo['Author']);

			$aVariables = array (
				'bg_clr' => $sBgClr,
				'unit_id' => $iQId,
				'unit_author' => $sQAuthor,
				'action_url' => $sAction,
				'unit_text' => $sQText
			);
			$sQuotes .= $this->_oTemplate->parseHtmlByTemplateName('adm_unit', $aVariables);
    	}

        bx_import('BxTemplSearchResult');
        $oSearchResult = new BxTemplSearchResult();
        $sAdmPanel = $oSearchResult->showAdminActionsPanel('quotes_box', array('action_delete' => '_Delete'), 'quotes_list');

        $sCode = <<<EOF
<form action="{$sAction}" method="post" name="quotes_moderation">
    <div class="adm-db-content-wrapper">
    	<div id="quotes_box">
    		{$sQuotes}
    		<div class="clear_both"></div>
    	</div>
    </div>
	{$sAdmPanel}
</form>
EOF;

        bx_import('BxDolPageView');
        $sActions = /*BxDolPageView::getBlockCaptionMenu(mktime(),*/ array(
            'add_unit' => array('href' => $sAction, 'title' => _t('_bx_quotes_add_new'), 'onclick' => '', 'active' => 0),
        )/*)*/;
        return DesignBoxAdmin($sExistedC, $sCode, $sActions);
    }
}

?>