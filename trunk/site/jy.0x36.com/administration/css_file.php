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

define('BX_SECURITY_EXCEPTIONS', true);
$aBxSecurityExceptions = array(
    'POST.content',
    'REQUEST.content',
);

require_once( '../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'admin_design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
bx_import('BxTemplFormView');

$logged['admin'] = member_auth( 1, true, true );

$iNameIndex = 0;
$_page = array(
    'name_index' => $iNameIndex,
    'css_name' => array('forms_adv.css'),
    'header' => _t('_adm_page_cpt_css_edit'),
    'header_text' => _t('_adm_box_cpt_css_edit')
);
$_page_cont[$iNameIndex]['page_main_code'] = PageCodeEdit();
define('BX_PROMO_CODE', 'on' == getParam('feeds_enable') ? DesignBoxAdmin (_t('_adm_box_cpt_design_templates'), '
	<div class="RSSAggrCont" rssid="boonex_unity_market_templates" rssnum="5" member="0">
		<div class="loading_rss">
			<img src="' . getTemplateImage('loading.gif') . '" alt="' . _t('_loading ...') . '" />
		</div>
	</div>') : ''); 

PageCodeAdmin();

function PageCodeEdit() {
    $aForm = array(
        'form_attrs' => array(
            'id' => 'adm-css-edit',
            'name' => 'adm-css-edit',
            'action' => $GLOBALS['site']['url_admin'] . 'css_file.php',
            'method' => 'post',
            'enctype' => 'multipart/form-data',
        ),
        'params' => array (
            'db' => array(
                'table' => '',
                'key' => '',
                'uri' => '',
                'uri_title' => '',
                'submit_name' => 'adm-css-save'
            ),
        ),
        'inputs' => array (            
            'css_file' => array(
                'type' => 'select',
                'name' => 'css_file',
                'caption' => _t('_adm_txt_css_file'),
                'value' => '',
                'values' => array(),
                'attrs' => array(
                    'onchange' => "javascript:document.forms['adm-css-edit'].submit();"
                )
            ),
            'content' => array(
                'type' => 'textarea',
                'name' => 'content',
                'caption' => _t('_adm_txt_css_content', $sFileName),
                'value' => '',
                'db' => array (
                    'pass' => 'XssHtml',
                ),
            ),
            'adm-css-save' => array(
                'type' => 'submit',
                'name' => 'adm-css-save',
                'value' => _t('_adm_btn_css_save'),
            ),                
        )
    );
    
    //--- Get CSS files ---//
    $aItems = array();
    $sBasePath = BX_DIRECTORY_PATH_ROOT . "templates/tmpl_" . $GLOBALS['oSysTemplate']->getCode() . "/css/";

    $rHandle = opendir($sBasePath);
	while(($sFile = readdir($rHandle)) !== false)
		if(is_file($sBasePath . $sFile) && substr($sFile, -3) == 'css')
			$aItems[] = array('key' => $sFile, 'value' => $sFile);
	closedir($rHandle);

	$sCurrentFile = isset($_POST['css_file']) && preg_match("/^\w+\.css$/", $_POST['css_file']) ? $_POST['css_file'] : $aItems[0]['key'];
	$aForm['inputs']['css_file']['value'] = $sCurrentFile;
    $aForm['inputs']['css_file']['values'] = $aItems;

	//--- Get CSS file's content ---//
	$sContent = '';
	$sAbsolutePath = $sBasePath . $sCurrentFile;
	if(strlen($sCurrentFile) > 0 && is_file($sAbsolutePath) ) {
	   $rHandle = fopen($sAbsolutePath, 'r');	       	
		while(!feof($rHandle))
			$sContent .= fgets($rHandle, 4096);
		fclose($rHandle);
	}
	//$aForm['inputs']['content']['value'] = isset($_POST['content']) ? $_POST['content'] : $sContent;
	$aForm['inputs']['content']['value'] = $sContent;
	
    $oForm = new BxTemplFormView($aForm);
    $oForm->initChecker();

    if($oForm->isSubmittedAndValid()) {
        if(file_exists($sAbsolutePath) && isRWAccessible($sAbsolutePath) ) {
            $rHandle = fopen($sAbsolutePath, 'w');
            if($rHandle) {
            	fwrite($rHandle, clear_xss($_POST['content']));
            	fclose($rHandle);

            	$mixedResult = '_adm_txt_css_success_save';
            } else
            	$mixedResult = '_adm_txt_css_failed_save';
        } else
            $mixedResult = '_adm_txt_css_cannot_write';
    }

    $sResult = $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $oForm->getCode()));

    if ($mixedResult !== true && !empty($mixedResult))
        $sResult = MsgBox(_t($mixedResult, $sCurrentFile), 3) . $sResult;

    return $sResult;
}

?>
