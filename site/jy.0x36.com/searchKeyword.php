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

require_once('inc/header.inc.php');
require_once(BX_DIRECTORY_PATH_INC . 'design.inc.php');
require_once(BX_DIRECTORY_PATH_INC . 'languages.inc.php');

bx_import('BxDolSearch');
bx_import('BxTemplFormView');

check_logged();
$member['ID'] = getLoggedId();

$_page['name_index'] = 81;
$_page['css_name'] = array('searchKeyword.css');

$_page['header'] = _t( "_Search" );
$_page['header_text'] = _t("_Search");

ob_start();
?>
<script language="javascript">
	$(document).ready( function() {
		 $('#searchForm').bind( 'submit', function() {
				bx_loading('searchForm', true);
		 		var sQuery = $('input', '#searchForm').serialize();
		 		$.post('searchKeywordContent.php', sQuery, function(data) {
		 				$('#searchArea').html(data);
		 				bx_loading('searchForm', false);
					}
				);
	  	  return false;
		  }
		 );
	  }
	);
</script>
<?
$sCode = '';
$_page['extra_js'] = ob_get_clean();

$_ni = $_page['name_index'];

$oZ = new BxDolSearch();
if (bx_get('keyword')) {
	$sCode = $oZ->response();
	if (mb_strlen($sCode) == 0)
		$sCode = $oZ->getEmptyResult();
}

$sForm = getSearchForm();
$sSearchArea = '<div id="searchArea">'.$sCode.'</div>';

$_page_cont[$_ni]['page_main_code'] = $sForm . $sSearchArea;

$aVars = array ();
$GLOBALS['oTopMenu']->setCustomSubActions($aVars, '');

PageCode();

function getSearchForm () {
    $aList = $GLOBALS['MySQL']->fromCache('sys_objects_search', 'getAllWithKey',
	       'SELECT `ID` as `id`,
                   `Title` as `title`,
                   `ClassName` as `class`,
                   `ClassPath` as `file`,
                   `ObjectName`
	        FROM `sys_objects_search`', 'ObjectName'
	);
    $aValues = array();
    foreach ($aList as $sKey => $aValue) {
        $aValues[$sKey] = _t($aValue['title']);
        if (!class_exists($aValue['class'])) {
            $sPath = BX_DIRECTORY_PATH_ROOT . str_replace('{tmpl}', $GLOBALS['tmpl'], $aValue['file']);
            require_once($sPath);
        }
        $oClass = new $aValue['class']();
        $oClass->addCustomParts();
    }    
    
    if (isset($_GET['type'])) {
        $aValue = strip_tags($_GET['type']); 
    }
    else
        $aValue = array_keys($aValues);
    
    $aForm = array(
        'form_attrs' => array(
           'id' => 'searchForm',
           'action' => '',
           'method' => 'post',
           'onsubmit' => '',
        ),
        'inputs' => array(
            'section' => array(
                'type' => 'checkbox_set',
                'name' => 'section',
                'caption' => _t('_Section'),
                'values' => $aValues,
                'value' => $aValue,
            ),
            'keyword' => array(
                'type' => 'text',
                'name' => 'keyword',
                'caption' => _t('_Keyword')
            ),
            'search' => array(
                'type' => 'submit',
                'name' => 'search',
                'value' => _t('_Search')
            )
        )
    );
    
    $oForm = new BxTemplFormView($aForm);
    $sFormVal = $oForm->getCode();

    return DesignBoxContent(_t( "_Search" ), $sFormVal, 1);
}

?>
