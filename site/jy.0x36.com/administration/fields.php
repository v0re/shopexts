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

define ('BX_SECURITY_EXCEPTIONS', true);
$aBxSecurityExceptions = array ();
$aBxSecurityExceptions[] = 'POST.Check';
$aBxSecurityExceptions[] = 'REQUEST.Check';
$aBxSecurityExceptions[] = 'POST.Values';
$aBxSecurityExceptions[] = 'REQUEST.Values';
$aBxSecurityExceptions[] = 'POST.Desc';
$aBxSecurityExceptions[] = 'REQUEST.Desc';

require_once( '../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'admin_design.inc.php' );

$logged['admin'] = member_auth(1, true, true);

$iNameIndex = 11;
$_page = array(
    'name_index' => $iNameIndex,
    'css_name' => array('fields.css'),
    'js_name' => array('ui.core.js', 'ui.tabs.js', 'ui.sortable.js', 'fields.js'),
    'header' => _t('_adm_fields_title')    
);
$_page_cont[$iNameIndex]['page_main_code'] = DesignBoxAdmin(
    _t('_adm_fields_box_title'),
    $GLOBALS['oAdmTemplate']->parseHtmlByName('fields.html', array()),
    array(
        'adm-fb-ctl-m1' => array(
            'title' => _t('_adm_fields_join_form'),            
            'href' => 'javascript:void(0)',
            'onclick' => 'javascript:changeType(this)',
            'active' => 1
        ),
        'adm-fb-ctl-edit-tab' => array(
            'title' => _t('_adm_fields_edit_profile'),
            'href' => 'javascript:void(0)',
            'onclick' => 'javascript:changeType(this)',
            'active' => 0
        ),
        'adm-fb-ctl-view-tab' => array(
            'title' => _t('_adm_fields_view_profile'),
            'href' => 'javascript:void(0)',
            'onclick' => 'javascript:changeType(this)',
            'active' => 0
        ),
        'adm-fb-ctl-search-tab' => array(
            'title' => _t('_adm_fields_search_profiles'),
            'href' => 'javascript:void(0)',
            'onclick' => 'javascript:changeType(this)',
            'active' => 0
        )
    )
);

$GLOBALS['oAdmTemplate']->addJsTranslation(array(
	'_adm_mbuilder_active_items',
	'_adm_txt_pb_inactive_blocks',
	'_adm_mbuilder_inactive_items'
));

PageCodeAdmin();
?>
