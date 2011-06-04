<?php

/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -----------------
*     begin                : Mon Mar 23 2008
*     copyright            : (C) 2008 BoonEx Group
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

require_once( '../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'admin_design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'admin.inc.php' );

bx_import('BxDolPaginate');
bx_import('BxDolSubscription');
bx_import('BxTemplSearchResult');
	
$logged['admin'] = member_auth( 1, true, true );

$oSubscription = new BxDolSubscription();

//--- Process actions
if(isset($_POST['adm-ms-delete'])) {
    foreach($_POST['members'] as $iMemberId)
        $oSubscription->unsubscribe(array(
            'type' => 'visitor',
            'id' => $iMemberId
        ));
}

$sPageTitle = _t('_adm_page_cpt_manage_subscribers');

$iNameIndex = 0;
$_page = array(
    'name_index' => $iNameIndex,
    'css_name' => array('forms_adv.css', 'manage_subscribers.css'),
    'header' => $sPageTitle,
    'header_text' => $sPageTitle
);
$_page_cont[$iNameIndex]['page_main_code'] = PageCodeSubscribers($oSubscription);

PageCodeAdmin();

function PageCodeSubscribers($oSubscription) {
    $iStart = bx_get('start') !== false ? (int)bx_get('start') : 0;
    $iPerPage = 20;
    $oPaginate = new BxDolPaginate(array(
        'start' => $iStart,
        'per_page' => $iPerPage,
        'count' => $oSubscription->getSubscribersCount(),
        'page_url' => $GLOBALS['site']['url_admin'] . 'manage_subscribers.php?start={start}'
        
    ));
    
    $sControls = BxTemplSearchResult::showAdminActionsPanel('adm-ms-form', array(
        'adm-ms-delete' => _t('_adm_btn_ms_delete')
    ), 'members');
    
    $aSubscribers = $oSubscription->getSubscribers(BX_DOL_SBS_TYPE_VISITOR, $iStart, $iPerPage);

    return $GLOBALS['oAdmTemplate']->parseHtmlByName('manage_subscribers.html', array(
        'bx_repeat:items' => is_array($aSubscribers) && !empty($aSubscribers) ? $aSubscribers : MsgBox(_t('_Empty')),
        'paginate' => $oPaginate->getPaginate(),
        'controls' => $sControls
    ));
}
?>