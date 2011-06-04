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
for ($i=1; $i<255 ; ++$i) {
    $aBxSecurityExceptions[] = 'POST.string_for_'.$i;
    $aBxSecurityExceptions[] = 'REQUEST.string_for_'.$i;
}

require_once( '../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'admin_design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
require_once( BX_DIRECTORY_PATH_PLUGINS . 'Services_JSON.php' );
bx_import('BxTemplSearchResult');

$logged['admin'] = member_auth( 1, true, true );

//--- Process submit ---//
$mixedResultSettings = '';
$mixedResultCreate = '';
$mixedResultAvailable = '';
$mixedResultKeys = '';
//--- Change settings ---//
if(isset($_POST['save_settings'])) {
    setParam('lang_default', $_POST['lang_default']);
    $mixedResultSettings = '_adm_txt_settings_success';
}

//--- Create/Edit/Delete/Recompile/Export/Import Languages ---//
if(isset($_POST['create_language'])) {
    $mixedResultCreate = createLanguage($_POST);
} 
else if(isset($_POST['import_language'])) {
    $mixedResultCreate = importLanguage($_POST, $_FILES);
}
else if(isset($_POST['adm-lang-compile']) && !empty($_POST['langs'])) {
    foreach($_POST['langs'] as $iLangId)
        if(!compileLanguage((int)$iLangId)) {
            $mixedResultAvailable = '_adm_txt_langs_cannot_compile';
            break;
        }
    if(empty($mixedResultAvailable))
        $mixedResultAvailable = '_adm_txt_langs_success_compile';
} 
else if(isset($_POST['adm-lang-delete']) && !empty($_POST['langs'])) {
    $sNameDefault = getParam('lang_default');
    foreach($_POST['langs'] as $iLangId) {    
        $sName = getLanguageName($iLangId);
        if($sName == $sNameDefault) {
            $mixedResultAvailable = '_adm_txt_langs_cannot_delete_default';
    		break;
        }

		if(!deleteLanguage((int)$iLangId)){
            $mixedResultAvailable = '_adm_txt_langs_cannot_delete';
            break;
		}
    }

    if(empty($mixedResultAvailable))
        $mixedResultAvailable = '_adm_txt_langs_success_delete';
}
else if(isset($_GET['action']) && $_GET['action'] == 'export' && isset($_GET['id'])) {
    $aLanguage = $GLOBALS['MySQL']->getRow("SELECT `Name`, `Flag`, `Title` FROM `sys_localization_languages` WHERE `ID`='" . (int)$_GET['id'] . "' LIMIT 1");
    
    $aContent = array();
    $aItems = $GLOBALS['MySQL']->getAll("SELECT `tlk`.`Key` AS `key`, `tls`.`String` AS `string` FROM `sys_localization_keys` AS `tlk` LEFT JOIN `sys_localization_strings` AS `tls` ON `tlk`.`ID`=`tls`.`IDKey` WHERE `tls`.`IDLanguage`='" . (int)$_GET['id'] . "'");    
    foreach($aItems as $aItem)
        $aContent[$aItem['key']] = $aItem['string'];

    $sName = 'lang_' . $aLanguage['Name'] . '.php';
    $sContent = "<?php\n\$aLangInfo=" . var_export($aLanguage, true) . ";\n\$aLangContent=" . var_export($aContent, true) . ";\n?>";
    
    header ("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header ("Content-type: application/octet-stream");
    header ("Content-Length: " . strlen($sContent));
    header ("Content-Disposition: attachment; filename=" . $sName);
    echo $sContent;
    exit;
}
else if(isset($_POST['action']) && $_POST['action'] == 'get_edit_form_language') {
    $oJson = new Services_JSON();
    echo $oJson->encode(array('code' => PopupBox('adm-langs-wnd-edit', _t('_adm_box_cpt_lang_edit_language'), _getLanguageCreateForm())));
    exit;
}

//--- Create/Delete/Edit Language Key ---//
if(isset($_POST['action']) && $_POST['action'] == 'get_edit_form_key') {
    $oJson = new Services_JSON();   
    echo $oJson->encode(array('code' => PageCodeKeyEdit((int)$_POST['id'])));
    exit;
}
if(isset($_POST['create_key'])) {
    $sName = process_db_input($_POST['name']);
    $iCategoryId = (int)$_POST['category'];

    $mixedResult = $GLOBALS['MySQL']->query("INSERT INTO `sys_localization_keys`(`IDCategory`, `Key`) VALUES('" . $iCategoryId . "', '" . $sName . "')", false);
    if($mixedResult !== false) {
        $bCompiled = true;
        $iKeyId = (int)$GLOBALS['MySQL']->lastId();
        $aLanguages = $GLOBALS['MySQL']->getAll("SELECT `ID` AS `id`, `Title` AS `title` FROM `sys_localization_languages`");
        foreach($aLanguages as $aLanguage)
            if(isset($_POST['string_for_' . $aLanguage['id']])) {
                $GLOBALS['MySQL']->query("INSERT INTO `sys_localization_strings`(`IDKey`, `IDLanguage`, `String`) VALUES('" . $iKeyId . "', '" . $aLanguage['id'] . "', '" . process_db_input($_POST['string_for_' . $aLanguage['id']]) . "')");
                
                $bCompiled = $bCompiled && compileLanguage((int)$aLanguage['id']);
            }
        
        $aResult = $bCompiled ? array('code' => 0, 'message' => '_adm_txt_langs_success_key_save') : array('code' => 1, 'message' => '_adm_txt_langs_cannot_compile');
    } else
        $aResult = array('code' => 2, 'message' => '_adm_txt_langs_already_exists');

    $aResult['message'] = MsgBox(_t($aResult['message']));
    
    $oJson = new Services_JSON();   
    echo "<script>parent.onResult('add', " . $oJson->encode($aResult) . ");</script>";
    exit;
} else if(isset($_POST['edit_key'])) {
    $iId = (int)$_POST['id'];

    $bCompiled = true;
    $aLanguages = $GLOBALS['MySQL']->getAll("SELECT `ID` AS `id`, `Title` AS `title` FROM `sys_localization_languages`");
    foreach($aLanguages as $aLanguage)
        if(isset($_POST['string_for_' . $aLanguage['id']])) {
            $GLOBALS['MySQL']->query("REPLACE INTO `sys_localization_strings`(`IDKey`, `IDLanguage`, `String`) VALUES('" . $iId . "', '" . $aLanguage['id'] . "', '" . process_db_input($_POST['string_for_' . $aLanguage['id']]) . "')");

            $bCompiled = $bCompiled && compileLanguage((int)$aLanguage['id']);
        }
    $aResult = $bCompiled ? array('code' => 0, 'message' => '_adm_txt_langs_success_key_save') : array('code' => 1, 'message' => '_adm_txt_langs_cannot_compile');
    $aResult['message'] = MsgBox(_t($aResult['message']));

    $oJson = new Services_JSON();   
    echo "<script>parent.onResult('edit', " . $oJson->encode($aResult) . ");</script>";
    exit;
}

if(isset($_POST['adm-lang-key-delete']) && is_array($_POST['keys'])) {
	foreach($_POST['keys'] as $iKeyId)
        $GLOBALS['MySQL']->query("DELETE FROM `sys_localization_keys`, `sys_localization_strings` USING `sys_localization_keys`, `sys_localization_strings` WHERE `sys_localization_keys`.`ID`=`sys_localization_strings`.`IDKey` AND `sys_localization_keys`.`ID`='" . $iKeyId . "'");
}
$iNameIndex = 5;
$_page = array(
    'name_index' => $iNameIndex,
    'css_name' => array('forms_adv.css', 'lang_file.css'),
    'js_name' => array('lang_file.js'),
    'header' => _t('_adm_page_cpt_lang_file'),    
);

$sLangRssFeed = 'on' == getParam('feeds_enable') 
    ?  DesignBoxAdmin (_t('_adm_box_cpt_lang_files'), '
		<div class="RSSAggrCont" rssid="boonex_unity_lang_files" rssnum="5" member="0">
			<div class="loading_rss">
				<img src="' . getTemplateImage('loading.gif') . '" alt="' . _t('_loading ...') . '" />
			</div>
		</div>')
    : '';

$_page_cont[$iNameIndex] = array(
    'page_result_code' => $sResult,    
    'page_code_settings' => PageCodeSettings($mixedResultSettings),
    'page_code_create' => PageCodeCreate($mixedResultCreate),
    'page_code_keys' => PageCodeKeys($mixedResultKeys),
    'page_code_key' => PageCodeKeyCreate() . $sLangRssFeed,
);

PageCodeAdmin();

function PageCodeSettings($mixedResult) {
    $aForm = array(
        'form_attrs' => array(
            'id' => 'adm-settings-form-settings',
            'name' => 'adm-settings-form-settings',
            'action' => $GLOBALS['site']['url_admin'] . 'lang_file.php',
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ),
        'params' => array(),
        'inputs' => array(
            'lang_default' => array(
                'type' => 'select',
                'name' => 'lang_default',                
                'caption' => _t('_adm_txt_langs_def_lang'),
                'values' => array(),
                'value' => getParam('lang_default'),
            ),
            'save_settings' => array(
                'type' => 'submit',
                'name' => 'save_settings',
                'value' => _t("_adm_btn_settings_save"),
            )
        )
    );
    $aLangs = getLangsArr();
	foreach($aLangs as $sName => $sTitle )
	    $aForm['inputs']['lang_default']['values'][] = array('key' => $sName, 'value' => htmlspecialchars_adv( $sTitle ));

    $oForm = new BxTemplFormView($aForm);
    $sResult = $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $oForm->getCode()));
    
    if($mixedResult !== true && !empty($mixedResult))
        $sResult = MsgBox(_t($mixedResult), 3) . $sResult;

    return DesignBoxAdmin(_t('_adm_box_cpt_lang_settings'), $sResult);
}
function PageCodeCreate($mixedResult) {
    $aTopItems = array(
    	'adm-langs-btn-files' => array('href' => 'javascript:void(0)', 'onclick' => 'javascript:onChangeType(this)', 'title' => _t('_adm_txt_langs_files'), 'active' => 1),
        'adm-langs-btn-create' => array('href' => 'javascript:void(0)', 'onclick' => 'javascript:onChangeType(this)', 'title' => _t('_adm_txt_langs_create'), 'active' => 0),
        'adm-langs-btn-import' => array('href' => 'javascript:void(0)', 'onclick' => 'javascript:onChangeType(this)', 'title' => _t('_adm_txt_langs_import'))
    );

    $sResult = $GLOBALS['oAdmTemplate']->parseHtmlByName('langs.html', array(
    	'content_files' => _getLanguagesList(),
        'content_create' => _getLanguageCreateForm(),
        'content_import' => _getLanguageImportForm()
    ));

    if($mixedResult !== true && !empty($mixedResult))
        $sResult = MsgBox(_t($mixedResult), 3) . $sResult;
           
    return DesignBoxAdmin(_t('_adm_box_cpt_lang_available'), $sResult, $aTopItems);
}
function _getLanguagesList() {
    //--- Get Items ---//
    $aItems = array();
    $sNameDefault = getParam('lang_default');
     
    $aLangs = $GLOBALS['MySQL']->getAll("SELECT `ID` AS `id`, `Name` AS `name`, `Title` AS `title`, `Flag` AS `flag` FROM `sys_localization_languages` ORDER BY `Name`");    
    foreach($aLangs as $aLang)
        $aItems[] = array(
            'name' => $aLang['name'],
            'value' => $aLang['id'],
            'title' => $aLang['title'],
            'icon' => $GLOBALS['site']['flags'] . $aLang['flag'] . '.gif',
            'default' => $aLang['name'] == $sNameDefault ? '(' . _t('_adm_txt_langs_default') . ')' : '',
            'edit_link' => $GLOBALS['site']['url_admin'] . 'lang_file.php?action=edit&id=' . $aLang['id'],
            'export_link' => $GLOBALS['site']['url_admin'] . 'lang_file.php?action=export&id=' . $aLang['id']
        );
    
    //--- Get Controls ---//
    $aButtons = array(
        'adm-lang-compile' => _t('_adm_txt_langs_compile'),
        'adm-lang-delete' => _t('_adm_txt_langs_delete')
    );    
    $sControls = BxTemplSearchResult::showAdminActionsPanel('adm-langs-form', $aButtons, 'langs');

    $sResult = $GLOBALS['oAdmTemplate']->parseHtmlByName('langs_files.html', array('bx_repeat:items' => $aItems, 'controls' => $sControls));
    return $sResult;
}
function _getLanguageCreateForm(){
	if(isset($_POST['action']) && $_POST['action'] == 'get_edit_form_language' && isset($_POST['id']))
        $aLanguage = $GLOBALS['MySQL']->getRow("SELECT `ID` AS `id`, `Name` AS `name`, `Flag` AS `flag`, `Title` AS `title` FROM `sys_localization_languages` WHERE `ID`='" . (int)$_POST['id'] . "' LIMIT 1");

    //--- Create language form ---//
    $aFormCreate = array(
        'form_attrs' => array(
            'id' => 'adm-settings-form-files',
            'name' => 'adm-settings-form-files',
            'action' => $GLOBALS['site']['url_admin'] . 'lang_file.php',
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ),
        'inputs' => array(
            'CopyLanguage_Title' => array(
                'type' => 'text',
                'name' => 'CopyLanguage_Title',
                'caption' => _t('_adm_txt_langs_title'),
                'value' => isset($aLanguage['title']) ? $aLanguage['title'] : '',
            ),
            'CopyLanguage_Name' => array(
                'type' => 'text',
                'name' => 'CopyLanguage_Name',
                'caption' => _t('_adm_txt_langs_code'),
                'value' => isset($aLanguage['name']) ? $aLanguage['name'] : '',
            ),
            'Flag' => array(
                'type' => 'select',
                'name' => 'Flag',
                'caption' => _t('_adm_txt_langs_flag'),
                'values' => array(),
                'value' => isset($aLanguage['flag']) ? $aLanguage['flag'] : strtolower(getParam('default_country')),
            ),
            'CopyLanguage_SourceLangID' => array(
                'type' => 'select',
                'name' => 'CopyLanguage_SourceLangID',                
                'caption' => _t('_adm_txt_langs_copy_from'),
                'values' => array()
            ),
            'create_language' => array(
                'type' => 'submit',
                'name' => 'create_language',
                'value' => _t("_adm_btn_lang_save"),
            )
        )
    );
    //--- Copy from ---//
    $aLangs = getLangsArr(false, true);
	foreach($aLangs as $iId => $sName)
	    $aFormCreate['inputs']['CopyLanguage_SourceLangID']['values'][] = array('key' => $iId, 'value' => htmlspecialchars_adv( $sName ));
    
    //--- Flags ---//
	$aCountries = $GLOBALS['MySQL']->getAll("SELECT `ISO2` AS `code`, `Country` AS `title` FROM `sys_countries` ORDER BY `Country`");	
	foreach($aCountries AS $aCountry) {
        $sCode = strtolower($aCountry['code']);
        $aFormCreate['inputs']['Flag']['values'][] = array('key' => $sCode, 'value' => $aCountry['title']);
	}
	
	if(!empty($aLanguage)) {
        unset($aFormCreate['inputs']['CopyLanguage_SourceLangID']);
        $aFormCreate['inputs']['id'] = array(
            'type' => 'hidden',
            'name' => 'id',
            'value' => $aLanguage['id']
        );
	}
	$oForm = new BxTemplFormView($aFormCreate);

    return $GLOBALS['oAdmTemplate']->parseHtmlByName('langs_form_create.html', array(
    	'display' => !empty($aLanguage) ? 'block' : 'none',
    	'form' => $oForm->getCode()
    ));
}
function _getLanguageImportForm() {
    $aFormImport = array(
        'form_attrs' => array(
            'id' => 'adm-settings-form-import',
            'name' => 'adm-settings-form-import',
            'action' => $GLOBALS['site']['url_admin'] . 'lang_file.php',
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ),        
        'inputs' => array(
            'ImportLanguage_File' => array(
                'type' => 'file',
                'name' => 'ImportLanguage_File',                
                'caption' => _t('_adm_txt_langs_file'),
            ),
            'import_language' => array(
                'type' => 'submit',
                'name' => 'import_language',
                'value' => _t('_adm_btn_lang_import'),
            )
        )
    );    
	$oForm = new BxTemplFormView($aFormImport);

	return $GLOBALS['oAdmTemplate']->parseHtmlByName('langs_form_import.html', array(
    	'display' => !empty($aLanguage) ? 'block' : 'none',
    	'form' => $oForm->getCode()
    ));
}
function PageCodeKeys($mixedResult) {
    $sFilter = '';
    $aItems = array();
    if(isset($_GET['filter'])) {
        $sFilter = process_db_input($_GET['filter'], BX_TAGS_STRIP);

        $aKeys = $GLOBALS['MySQL']->getAll("SELECT `tk`.`ID` AS `id`, `tk`.`Key` AS `key`, `tc`.`Name` AS `category` FROM `sys_localization_keys` AS `tk` LEFT JOIN `sys_localization_strings` AS `ts` ON `tk`.`ID`=`ts`.`IDKey` LEFT JOIN `sys_localization_categories` AS `tc` ON `tk`.`IDCategory`=`tc`.`ID` WHERE `tk`.`Key` LIKE '%" . $sFilter . "%' OR `ts`.`String` LIKE '%" . $sFilter . "%' GROUP BY `tk`.`ID`");
        foreach($aKeys as $aKey)
            $aItems[] = array(
                'id' => $aKey['id'],
                'key' => $aKey['key'],
                'category' => $aKey['category'],
                'admin_url' => $GLOBALS['site']['url_admin']
            );
    }
    
    //--- Get Controls ---//
    $aButtons = array(        
        'adm-lang-key-delete' => _t('_adm_txt_langs_delete')
    );    
    $sControls = BxTemplSearchResult::showAdminActionsPanel('adm-keys-form', $aButtons, 'keys');
    
    $sResult = $GLOBALS['oAdmTemplate']->parseHtmlByName('langs_keys.html', array(
        'filter_value' => $sFilter,
        'filter_checked' => !empty($sFilter) ? 'checked="checked"' : '',
        'bx_repeat:items' => !empty($aItems) ? $aItems : MsgBox(_t('_Empty')),
        'control' => $sControls,
        'url_admin' => $GLOBALS['site']['url_admin']
    ));
    
    if($mixedResult !== true && !empty($mixedResult))
        $sResult = MsgBox(_t($mixedResult), 3) . $sResult;
        
    return DesignBoxAdmin(_t('_adm_box_cpt_lang_keys'), $sResult);
}
function PageCodeKeyCreate() {
    $aForm = array(
        'form_attrs' => array(
            'id' => 'adm-langs-add-key-form',
            'name' => 'adm-langs-add-key-form',
            'action' => $GLOBALS['site']['url_admin'] . 'lang_file.php',
            'method' => 'post',
            'enctype' => 'multipart/form-data',
            'target' => 'adm-langs-add-key-iframe'
        ),
        'params' => array(),
        'inputs' => array(            
            'name' => array(
                'type' => 'text',
                'name' => 'name',
                'caption' => _t('_adm_txt_keys_name'),
                'value' => '',
            ),
            'category' => array(
                'type' => 'select',
                'name' => 'category',
                'caption' => _t('_adm_txt_keys_category'),
                'value' => '',
                'values' => array()
            ),            
        )
    );

    $aCategories = $GLOBALS['MySQL']->getAll("SELECT `ID` AS `id`, `Name` AS `title` FROM `sys_localization_categories`");
    foreach($aCategories as $aCategory) 
        $aForm['inputs']['category']['values'][] = array('key' => $aCategory['id'], 'value' => $aCategory['title']);

    $aLanguages = $GLOBALS['MySQL']->getAll("SELECT `ID` AS `id`, `Title` AS `title` FROM `sys_localization_languages`");
    foreach($aLanguages as $aLanguage)
        $aForm['inputs']['string_for_' . $aLanguage['id']] = array(
            'type' => 'textarea',
            'name' => 'string_for_' . $aLanguage['id'],
            'caption' => _t('_adm_txt_keys_string_for', $aLanguage['title']),
            'value' => '',
        );

    $aForm['inputs']['create_key'] = array(
        'type' => 'submit',
        'name' => 'create_key',
        'value' => _t("_adm_btn_lang_save"),
    );

    $oForm = new BxTemplFormView($aForm);
    return $GLOBALS['oAdmTemplate']->parseHtmlByName('langs_key.html', array('type' => 'add', 'content' => $oForm->getCode()));
}
function PageCodeKeyEdit($iId) {
    $aForm = array(
        'form_attrs' => array(
            'id' => 'adm-langs-edit-key-form',
            'name' => 'adm-langs-edit-key-form',
            'action' => $GLOBALS['site']['url_admin'] . 'lang_file.php',
            'method' => 'post',
            'enctype' => 'multipart/form-data',
            'target' => 'adm-langs-edit-key-iframe'
        ),
        'params' => array(),
        'inputs' => array(            
            'id' => array(
                'type' => 'hidden',
                'name' => 'id',
                'value' => $iId
            ),
            'name' => array(
                'type' => 'text',
                'name' => 'name',
                'caption' => _t('_adm_txt_keys_name'),
                'value' => $GLOBALS['MySQL']->getOne("SELECT `Key` FROM `sys_localization_keys` WHERE `ID`='" . $iId . "' LIMIT 1"),
                'attrs' => array(
                    'disabled' => 'disabled'
                )
            ),
        )
    );    

    $aStrings = $GLOBALS['MySQL']->getAllWithKey("SELECT CONCAT('string_for_', `IDLanguage`) AS `key`, `String` AS `value` FROM `sys_localization_strings` WHERE `IDKey`='" . $iId . "'", "key");
    $aLanguages = $GLOBALS['MySQL']->getAll("SELECT `ID` AS `id`, `Title` AS `title` FROM `sys_localization_languages`");
    foreach($aLanguages as $aLanguage) {
        $sKey = 'string_for_' . $aLanguage['id'];

        $aForm['inputs'][$sKey] = array(
            'type' => 'textarea',
            'name' => 'string_for_' . $aLanguage['id'],
            'caption' => _t('_adm_txt_keys_string_for', $aLanguage['title']),
            'value' => $aStrings[$sKey]['value'],
        );
    }
    $aForm['inputs']['edit_key'] = array(
        'type' => 'submit',
        'name' => 'edit_key',
        'value' => _t("_adm_btn_lang_save"),
    );

    $oForm = new BxTemplFormView($aForm);
    return $GLOBALS['oAdmTemplate']->parseHtmlByName('langs_key.html', array('type' => 'edit', 'content' => $oForm->getCode()));
}
function createLanguage(&$aData) {
    global $MySQL;
    
	$sTitle = process_db_input($aData['CopyLanguage_Title']);
	$sName  = mb_strtolower( process_db_input($aData['CopyLanguage_Name']) );
	$sFlag = htmlspecialchars_adv($aData['Flag']);
	$iSourceId = isset($aData['CopyLanguage_SourceLangID']) ? (int)$aData['CopyLanguage_SourceLangID'] : 0;

	if(strlen($sTitle) <= 0) 
        return '_adm_txt_langs_empty_title';
	if(strlen($sName) <= 0)
        return '_adm_txt_langs_empty_name';

    if(isset($aData['id']) && (int)$aData['id'] != 0) {
        $MySQL->query("UPDATE `sys_localization_languages` SET `Name`='" . $sName . "', `Flag`='" . $sFlag . "', `Title`='" . $sTitle . "' WHERE `ID`='" . (int)$aData['id'] . "'");

        return '_adm_txt_langs_success_updated';
    }
    

	$mixedResult = $MySQL->query("INSERT INTO `sys_localization_languages` (`Name`, `Flag`, `Title`) VALUES ('{$sName}', '{$sFlag}', '{$sTitle}')");
	if($mixedResult === false) 
        return '_adm_txt_langs_cannot_create';

	$iId = db_last_id();
	$aStrings = $MySQL->getAll("SELECT	`IDKey`, `String` FROM	`sys_localization_strings` WHERE	`IDLanguage` = $iSourceId");

	foreach($aStrings as $aString){
		$aString['String'] = addslashes($aString['String']);
		$MySQL->query("INSERT INTO `sys_localization_strings`(`IDKey`, `IDLanguage`, `String`) VALUES ('{$aString['IDKey']}', $iId, '{$aString['String']}')");
		
		if( !db_affected_rows() )
			return '_adm_txt_langs_cannot_add_string';
	}

	return '_adm_txt_langs_success_create';
}
function importLanguage(&$aData, &$aFiles) {    
    global $MySQL;

	$sTmpPath = $GLOBALS['dir']['tmp'] . mktime() . ".php";
    if(!file_exists($aFiles['ImportLanguage_File']['tmp_name']) || !move_uploaded_file($aFiles['ImportLanguage_File']['tmp_name'], $sTmpPath)) 
        return '_adm_txt_langs_cannot_upload_file';
	
    require_once($sTmpPath);
	
	$aLangInfo = isset($aLangInfo) ? $aLangInfo : $LANG_INFO;
	$aLangContent = isset($aLangContent) ? $aLangContent : $LANG;
	if (empty($aLangInfo) || empty($aLangContent)) {
		return '_adm_txt_langs_cannot_create';
	}

    $mixedResult = $MySQL->query("INSERT INTO `sys_localization_languages` (`Name`, `Flag`, `Title`) VALUES ('" . $aLangInfo['Name'] . "', '" . $aLangInfo['Flag'] . "', '" . $aLangInfo['Title'] . "')");
	if($mixedResult === false) {
		@unlink($sTmpPath); 
        return '_adm_txt_langs_cannot_create';
	}

    $iId = (int)$MySQL->lastId();
    $aKeys = $MySQL->getAllWithKey("SELECT `ID` AS `id`, `Key` AS `key` FROM `sys_localization_keys`", "key");
    foreach($aLangContent as $sKey => $sString) {
        if(!isset($aKeys[$sKey]))
            continue;

		$MySQL->query("INSERT INTO `sys_localization_strings`(`IDKey`, `IDLanguage`, `String`) VALUES ('" . $aKeys[$sKey]['id'] . "', " . $iId . ", '" . addslashes($sString) . "')");
	}
    
	compileLanguage($iId);
	
	@unlink($sTmpPath);
	return '_adm_txt_langs_success_import';
}
function getLanguageName($iId) {
	return $GLOBALS['MySQL']->getOne("SELECT `Name` FROM `sys_localization_languages` WHERE `ID`='" . (int)$iId . "' LIMIT 1");
}

?>
