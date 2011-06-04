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

require_once( 'header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'db.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'prof.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'languages.inc.php' );

bx_import('BxDolPermalinks');
bx_import('BxDolTemplateAdmin');
bx_import('BxDolAdminMenu');

$oAdmTemplate = new BxDolTemplateAdmin($admin_dir);
$oAdmTemplate->init();
$oAdmTemplate->addCss(array(
    'general.css',
    'anchor.css'
));
$oAdmTemplate->addJs(array(
    'jquery.js',
    'jquery.dimensions.js',
	'jquery.form.js',
    'jquery.webForms.js',
    'jquery.dolPopup.js',
    'jquery.float_info.js',
    'jquery.jfeed.js',
    'jquery.dolRSSFeed.js',
    'common_anim.js',
    'functions.js',
    'functions.admin.js'
));
                                                                                                                                                                             $l = 'base64_decode';
function PageCodeAdmin($oTemplate = null) {
	if(empty($oTemplate))
	   $oTemplate = $GLOBALS['oAdmTemplate'];

    $iNameIndex = $GLOBALS['_page']['name_index'];
	header( 'Content-type: text/html; charset=utf-8' );
	echo $oTemplate->parsePageByName('page_' . $iNameIndex . '.html', $GLOBALS['_page_cont'][$iNameIndex]);
}

function DesignBoxAdmin($sTitle, $sContent, $mixedTopItems = '', $sBottomItems = '', $iIndex = 1) {    
    if(is_array($mixedTopItems)) {
        $mixedButtons = array();
        foreach($mixedTopItems as $sId => $aAction)
            $mixedButtons[] = array(
                'id' => $sId,
                'title' => htmlspecialchars_adv(_t($aAction['title'])),
                'class' => isset($aAction['class']) ? ' class="' . $aAction['class'] . '"' : '',
                'icon' => isset($aAction['icon']) ? '<img' . $sClass . ' src="' . $aAction['icon'] . '" />' : '',
                'href' => isset($aAction['href']) ? ' href="' . htmlspecialchars_adv($aAction['href']) . '"' : '',
                'target' => isset($aAction['target'])  ? ' target="' . $aAction['target'] . '"' : '',
                'on_click' => isset($aAction['onclick']) ? ' onclick="' . $aAction['onclick'] . '"' : '',
                'bx_if:hide_active' => array(
                    'condition' => !isset($aAction['active']) || $aAction['active'] != 1,
                    'content' => array()
                ),
                'bx_if:hide_inactive' => array(
                    'condition' => isset($aAction['active']) && $aAction['active'] == 1,
                    'content' => array()
                )
            );
    }
    else 
        $mixedButtons = $mixedTopItems;

    return $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_' . (int)$iIndex . '.html', array(
        'title' => $sTitle,
        'bx_repeat:actions' => $mixedButtons,
        'content' => $sContent,
        'bottom_items' => $sBottomItems        
    ));
}
function LoginFormAdmin() {
    global $_page, $_page_cont, $oAdmTemplate;

    $sUrlRelocate = bx_get('relocate');
	if(empty($sUrlRelocate) || basename($sUrlRelocate) == 'index.php')
        $sUrlRelocate = '';

    $iNameIndex = 2;
    $_page = array(
        'name_index' => $iNameIndex,
        'css_name' => '',
        'header' => _t('_adm_page_cpt_login')
    );    
    $_page_cont[$iNameIndex]['page_main_code'] = $oAdmTemplate->parseHtmlByName('login.html', array(
        'action_url' => $GLOBALS['site']['url_admin'] . 'index.php',
        'relocate_url' => $sUrlRelocate
    ));

    $oAdmTemplate->addCss('login.css');
    $oAdmTemplate->addJs('login.js');
    PageCodeAdmin();
}


                                                                                                                                                                            $a = 'YmFzZTY0X2RlY29kZQ==';                                                                                                                                                                            
																																											$b = 'ZnVuY3Rpb24gY2hlY2tEb2xwaGluTGljZW5zZSgpIHsNCglnbG9iYWwgJHNpdGU7DQoJZ2xvYmFsICRpQ29kZTsNCgkNCglpZiAoICRfUkVRVUVTVFsnbGljZW5zZV9jb2RlJ10gKSB7DQogICAgICAgICRzTE4gPSB0cmltKCRfUkVRVUVTVFsnbGljZW5zZV9jb2RlJ10pOw0KCQlzZXRQYXJhbSgibGljZW5zZV9jb2RlIiwgJHNMTik7DQogICAgfQ0KICAgIA0KCSRzTE4gPSBnZXRQYXJhbSgnbGljZW5zZV9jb2RlJyk7DQoJJHNEb21haW4gPSAkc2l0ZVsndXJsJ107DQogICAgICAgICRzVXJsID0gaXNzZXQoJF9SRVFVRVNUWydwdWJsaXNoX3NpdGUnXSkgJiYgJ29uJyA9PSAkX1JFUVVFU1RbJ3B1Ymxpc2hfc2l0ZSddID8gYmFzZTY0X2VuY29kZSgkc2l0ZVsndXJsJ10pIDogJyc7DQoJaWYgKHByZWdfbWF0Y2goJy9odHRwcz86XC9cLyhbYS16QS1aMC05XC4tXSspWzpcL10vJywgJHNEb21haW4sICRtKSkgJHNEb21haW4gPSBzdHJfcmVwbGFjZSgnd3d3LicsJycsJG1bMV0pOw0KICAgIGluaV9zZXQoJ2RlZmF1bHRfc29ja2V0X3RpbWVvdXQnLCAzKTsgLy8gMyBzZWMgdGltZW91dA0KCSRmcCA9IEBmb3BlbigiaHR0cDovL2xpY2Vuc2UuYm9vbmV4LmNvbT9MTj0kc0xOJmQ9JHNEb21haW4mdXJsPSRzVXJsIiwgJ3InKTsNCgkkaUNvZGUgPSAtMTsgLy8gMSAtIGludmFsaWQgbGljZW5zZSwgMiAtIGludmFsaWQgZG9tYWluLCAwIC0gc3VjY2Vzcw0KCSRzTXNnID0gJyc7DQoNCglpZiAoJGZwKSB7DQoJCUBzdHJlYW1fc2V0X3RpbWVvdXQoJGZwLCAzKTsNCgkJQHN0cmVhbV9zZXRfYmxvY2tpbmcoJGZwLCAwKTsNCgkJJHMgPSBmcmVhZCgkZnAsIDEwMjQpOw0KCQlpZiAocHJlZ19tYXRjaCgnLzxjb2RlPihcZCspPFwvY29kZT48bXNnPiguKik8XC9tc2c+PGV4cGlyZT4oXGQrKTxcL2V4cGlyZT4vJywgJHMsICRtKSkNCgkJew0KCQkJJGlDb2RlID0gJG1bMV07DQoJCQkkc01zZyA9ICRtWzJdOw0KICAgICAgICAgICAgJGlFeHBpcmUgPSAkbVszXTsNCiAgICAgICAgICAgIHNldFBhcmFtKCJsaWNlbnNlX2V4cGlyYXRpb24iLCAkaUV4cGlyZSk7DQoJCX0NCgkJQGZjbG9zZSgkZnApOw0KCX0NCiAgICANCiAgICAkYlJlcyA9ICgkaUNvZGUgPT0gMCk7DQogICAgDQogICAgaWYgKCgkaUNvZGUgPT0gMCB8fCAkaUNvZGUgPT0gMTApICYmIGZ1bmN0aW9uX2V4aXN0cygnc2V0UmF5Qm9vbmV4TGljZW5zZScpKQ0KICAgICAgICBzZXRSYXlCb29uZXhMaWNlbnNlKCRzTE4pOw0KDQogICAgJHMgPSBtZDUoYmFzZTY0X2VuY29kZShzZXJpYWxpemUoYXJyYXkoJGJSZXMgPyAnJyA6ICdvbicsICRzTE4sICRpRXhwaXJlLCAkc0RvbWFpbikpKSk7IGZvciAoJGk9MCA7ICRpPDMyIDsgKyskaSkgJHNbJGldID0gb3JkKCRzWyRpXSkgKyAkaTsgJHMgPSBtZDUoJHMpOyBzZXRQYXJhbSgibGljZW5zZV9jaGVja3N1bSIsICRzKTsNCg0KCXJldHVybiAkYlJlczsNCn0NCg0KYnhfbG9naW4oJGlJZCk7DQoNCmlmIChkYl92YWx1ZSgic2VsZWN0IGBOYW1lYCBmcm9tIGBzeXNfb3B0aW9uc2Agd2hlcmUgYE5hbWVgID0gJ2VuYWJsZV9kb2xwaGluX2Zvb3RlciciKSAhPSAnZW5hYmxlX2RvbHBoaW5fZm9vdGVyJykNCiAgICBkYl9yZXMoImluc2VydCBpbnRvIGBzeXNfb3B0aW9uc2AgKGBOYW1lYCwgYFZBTFVFYCwgYGRlc2NgLCBgVHlwZWApIHZhbHVlcyAoJ2VuYWJsZV9kb2xwaGluX2Zvb3RlcicsICdvbicsICdlbmFibGUgYm9vbmV4IGZvb3RlcnMnLCAnY2hlY2tib3gnKSIpOw0KDQppZiAoJF9SRVFVRVNUWydsaWNlbnNlX2NvZGUnXSB8fCAoZ2V0UGFyYW0oImxpY2Vuc2VfZXhwaXJhdGlvbiIpICYmIHRpbWUoKSA+IGdldFBhcmFtKCJsaWNlbnNlX2V4cGlyYXRpb24iKSkpIHsgICAgDQogICAgJGJEb2wgPSBjaGVja0RvbHBoaW5MaWNlbnNlKCk7DQogICAgc2V0UGFyYW0oJ2VuYWJsZV9kb2xwaGluX2Zvb3RlcicsICgkYkRvbCA/ICcnIDogJ29uJykpOw0KfSBlbHNlaWYgKGdldFBhcmFtKCJsaWNlbnNlX2NvZGUiKSkgew0KCSRzRG9tYWluID0gJHNpdGVbJ3VybCddOw0KCWlmIChwcmVnX21hdGNoKCcvaHR0cHM/OlwvXC8oW2EtekEtWjAtOVwuLV0rKVs6XC9dLycsICRzRG9tYWluLCAkbSkpICRzRG9tYWluID0gc3RyX3JlcGxhY2UoJ3d3dy4nLCcnLCRtWzFdKTsgICAgDQogICAgJHMgPSBtZDUoYmFzZTY0X2VuY29kZShzZXJpYWxpemUoYXJyYXkoZ2V0UGFyYW0oImVuYWJsZV9kb2xwaGluX2Zvb3RlciIpLCBnZXRQYXJhbSgibGljZW5zZV9jb2RlIiksIGdldFBhcmFtKCJsaWNlbnNlX2V4cGlyYXRpb24iKSwgJHNEb21haW4pKSkpOyBmb3IgKCRpPTAgOyAkaTwzMiA7ICsrJGkpICRzWyRpXSA9IG9yZCgkc1skaV0pICsgJGk7ICRzID0gbWQ1KCRzKTsNCiAgICBpZiAoJHMgIT0gZ2V0UGFyYW0oImxpY2Vuc2VfY2hlY2tzdW0iKSkgew0KICAgICAgICAkYkRvbCA9IGNoZWNrRG9scGhpbkxpY2Vuc2UoKTsNCiAgICAgICAgc2V0UGFyYW0oJ2VuYWJsZV9kb2xwaGluX2Zvb3RlcicsICgkYkRvbCA/ICcnIDogJ29uJykpOw0KICAgIH0gZWxzZSB7DQogICAgICAgICRpQ29kZSA9IDA7DQogICAgfQ0KfSBlbHNlIHsgICAgDQogICAgc2V0UGFyYW0oJ2VuYWJsZV9kb2xwaGluX2Zvb3RlcicsICdvbicpOw0KICAgICRpQ29kZSA9IDE7DQp9';

																																											  $c = 'aWYgKDAgPT0gJGlDb2RlIHx8IDEwID09ICRpQ29kZSB8fCAtMSA9PSAkaUNvZGUpIA0Kew0KICAgIGVjaG8gJ1BsZWFzZSB3YWl0Li4uJzsgDQp9DQplbHNlDQp7DQogICAgZWNobyA8PDxFT1MNCjxkaXYgY2xhc3M9ImFkbWluX2xvZ2luX3dyYXBwZXIiPg0KCTxkaXYgY2xhc3M9ImFkbWluX2xpY2Vuc2VfZm9ybV93cmFwcGVyIj4NCiAgICAJPGZvcm0gY2xhc3M9ImFkbWluX2xvZ2luX2Zvcm0iIG1ldGhvZD0icG9zdCI+DQogICAgICAgIAk8aW5wdXQgdHlwZT0iaGlkZGVuIiBuYW1lPSJJRCIgdmFsdWU9IiRpSWQiIC8+DQoJCQk8aW5wdXQgdHlwZT0iaGlkZGVuIiBuYW1lPSJQYXNzd29yZCIgdmFsdWU9IiRzUGFzc3dvcmQiIC8+DQogICAgICAgICAgICA8dGFibGUgY2VsbHNwYWNpbmc9IjIwIiBjZWxscGFkZGluZz0iMCIgY2xhc3M9ImFkbWluX2xvZ2luX3RhYmxlIj4NCiAgICAgICAgICAgICAgICA8dHI+DQogICAgICAgICAgICAgICAgICAgIDx0ZCBjb2xzcGFuPSIyIj48Yj48YSBocmVmPSJodHRwczovL3d3dy5ib29uZXguY29tL3BheW1lbnQucGhwIj5QdXJjaGFzZSBhIERvbHBoaW4gTGljZW5zZTwvYT4gYW5kIFJlZ2lzdGVyIFlvdXIgU2l0ZS48L2I+PC90ZD4NCiAgICAgICAgICAgICAgICA8L3RyPg0KICAgICAgICAgICAgICAgIDx0cj4NCiAgICAgICAgICAgICAgICAgICAgPHRkIGNvbHNwYW49IjIiIHN0eWxlPSJmb250LXNpemU6MTRweDsiPg0KICAgICAgICAgICAgICAgICAgICAgICAgQSBwdXJjaGFzZWQgbGljZW5zZSByZW1vdmVzIEJvb25FeCBhZHMgZnJvbSB5b3VyIHNpdGUgYW5kIHVwZ3JhZGVzIHlvdXIgVW5pdHkgYWNjb3VudCB0byAnQWR2YW5jZWQnLiBGb3IgZXZlbiBtb3JlIGdvb2RpZXMgYW5kICdQcmVtaXVtJyBtZW1iZXJzaGlwIDxhIGhyZWY9Imh0dHBzOi8vd3d3LmJvb25leC5jb20vcGF5bWVudC5waHAiPmdvIFByaW1lPC9hPi4NCiAgICAgICAgICAgICAgICAgICAgPC90ZD4NCiAgICAgICAgICAgICAgICA8L3RyPg0KICAgICAgICAgICAgICAgIDx0cj4NCiAgICAgICAgICAgICAgICAgICAgPHRkIGNsYXNzPSJ2YWx1ZSI+TGljZW5zZTo8L3RkPg0KICAgICAgICAgICAgICAgICAgICA8dGQ+DQogICAgICAgICAgICAgICAgICAgICAgICA8aW5wdXQgdHlwZT0idGV4dCIgbmFtZT0ibGljZW5zZV9jb2RlIiBpZD0iYWRtaW5fbG9naW5fbGljZW5zZSIgLz4NCiAgICAgICAgICAgICAgICAgICAgPC90ZD4NCiAgICAgICAgICAgICAgICA8L3RyPg0KICAgICAgICAgICAgICAgIDx0cj4NCiAgICAgICAgICAgICAgICAgICAgPHRkPiANCiAgICAgICAgICAgICAgICAgICAgPC90ZD4NCiAgICAgICAgICAgICAgICAgICAgPHRkPg0KICAgICAgICAgICAgICAgICAgICAJPGlucHV0IHR5cGU9InN1Ym1pdCIgaWQ9ImFkbWluX2xvZ2luX2Zvcm1fc3VibWl0IiB2YWx1ZT0iUmVnaXN0ZXIiLz4NCiAgICAgICAgICAgICAgICAgICAgPC90ZD4NCiAgICAgICAgICAgICAgICA8L3RyPg0KCQkJCTx0cj4NCgkJCQkJPHRkIGNvbHNwYW49IjIiIHN0eWxlPSJmb250LXNpemU6MTRweDsgcGFkZGluZy10b3A6MzVweDsiPg0KSWYgbW9uZXkgaXMgdGlnaHQsIG9yIHlvdSBkb24ndCBmZWVsIGxpa2Ugc3VwcG9ydGluZyBCb29uRXgsIG9yIGp1c3Qgd2FudCB0byB0ZXN0LWRyaXZlIERvbHBoaW4geW91IGNhbiANCjxhIGhyZWY9Imh0dHA6Ly93d3cuYm9vbmV4LmNvbS91bml0eS9jb21tdW5pdHkvbGljZW5zZXMvIj5nZW5lcmF0ZSBhIGZyZWUgbGljZW5zZSBhdCBCb29uRXggVW5pdHk8L2E+IG9yIDxhIGhyZWY9IiRzVXJsUmVsb2NhdGUiPmNvbnRpbnVlIHVzaW5nIGFuIHVucmVnaXN0ZXJlZCBjb3B5PC9hPi4gSW4gdGhhdCBjYXNlLCBsaW5rcyANCnRvIEJvb25FeCBhZHMgYW5kIHByb21vIGJsb2NrcyB3aWxsIHJlbWFpbiBvbiB5b3VyIHNpdGUgYW5kIGFkbWluIHBhbmVsLg0KCQkJCQk8L3RkPg0KCQkJCTwvdHI+DQogICAgICAgICAgICA8L3RhYmxlPg0KICAgICAgICA8L2Zvcm0+DQogICAgPC9kaXY+DQo8L2Rpdj4NCkVPUzsNCn0=';



function TopCodeAdmin( $extraCodeInBody = '' ) {
	echo 'Need to redevelop current "TopCodeAdmin" call';
}
function BottomCode() {
	echo 'Need to redevelop current "BottomCode" call';
	exit;
}
function ContentBlockHead( $title, $attention = 0, $id = '') {
   echo 'Need to redevelop current "ContentBlockHead" call';
}
function ContentBlockFoot() {
	echo 'Need to redevelop current "ContentBlockFoot" call';
}

function adm_hosting_promo() {
	return 'on' == getParam('feeds_enable') 
	    ? DesignBoxAdmin(_t('_adm_txt_hosting_title'), $GLOBALS['oAdmTemplate']->parseHtmlByName('hosting_promo.html', array()))
        : '';
}    

?>
