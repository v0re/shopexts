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

require_once( '../inc/header.inc.php' );

$GLOBALS['iAdminPage'] = 1;

require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'admin_design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
require_once( BX_DIRECTORY_PATH_PLUGINS . 'Services_JSON.php' );

bx_import('BxTemplSearchResult');
bx_import('BxTemplBrowse');
bx_import('BxTemplTags');
bx_import('BxTemplFunctions');
bx_import('BxDolAlerts');
bx_import('BxDolEmailTemplates');

define('BX_DOL_ADM_MP_CTL', 'qlinks');
define('BX_DOL_ADM_MP_VIEW', 'geeky');
define('BX_DOL_ADM_MP_JS_NAME', 'oMP');
define('BX_DOL_ADM_MP_PER_PAGE', 32);
define('BX_DOL_ADM_MP_PER_PAGE_STEP', 16);

$logged['admin'] = member_auth( 1, true, true );

$sCtlType = isset($_POST['adm-mp-members-ctl-type']) && in_array($_POST['adm-mp-members-ctl-type'], array('qlinks', 'browse', 'calendar', 'tags', 'search')) ? $_POST['adm-mp-members-ctl-type'] : BX_DOL_ADM_MP_CTL;
$sViewType = isset($_POST['adm-mp-members-view-type']) && in_array($_POST['adm-mp-members-view-type'], array('geeky', 'simple', 'extended')) ? $_POST['adm-mp-members-view-type'] : BX_DOL_ADM_MP_VIEW;

//--- Process Actions ---//
if(isset($_POST['adm-mp-activate']) && (bool)$_POST['members']) {
    $GLOBALS['MySQL']->query("UPDATE `Profiles` SET `Status`='Active' WHERE `ID` IN ('" . implode("','", $_POST['members']) . "')");

    $oEmailTemplate = new BxDolEmailTemplates();
    foreach($_POST['members'] as $iId) {
    	createUserDataFile((int)$iId);
    	reparseObjTags('profile', (int)$iId);

    	$aProfile = getProfileInfo($iId);
		$aMail = $oEmailTemplate->parseTemplate('t_Activation', array(), $iId);
		sendMail($aProfile['Email'], $aMail['subject'], $aMail['body']);

        $oAlert = new BxDolAlerts('profile', 'change_status', (int)$iId, 0, array('status' => 'Active'));
        $oAlert->alert();
    }
    echo "<script>window.parent." . BX_DOL_ADM_MP_JS_NAME . ".reload();</script>";
    exit;
} 
else if(isset($_POST['adm-mp-deactivate']) && (bool)$_POST['members']) {
    $GLOBALS['MySQL']->query("UPDATE `Profiles` SET `Status`='Approval' WHERE `ID` IN ('" . implode("','", $_POST['members']) . "')");
    foreach($_POST['members'] as $iId) {
    	createUserDataFile((int)$iId);
    	reparseObjTags('profile', (int)$iId);
        $oAlert = new BxDolAlerts('profile', 'change_status', (int)$iId, 0, array('status' => 'Approval'));
        $oAlert->alert();
    }

    echo "<script>window.parent." . BX_DOL_ADM_MP_JS_NAME . ".reload();</script>";
    exit;
} 
else if(isset($_POST['adm-mp-ban']) && (bool)$_POST['members']) {
    foreach($_POST['members'] as $iId)
        $GLOBALS['MySQL']->query("REPLACE INTO `sys_admin_ban_list` SET `ProfID`='" . $iId . "', `Time`='0',  `DateTime`=NOW()");

	echo "<script>window.parent." . BX_DOL_ADM_MP_JS_NAME . ".reload();</script>";
    exit;
} 
else if(isset($_POST['adm-mp-unban']) && (bool)$_POST['members']) {    
    $GLOBALS['MySQL']->query("DELETE FROM `sys_admin_ban_list` WHERE `ProfID` IN ('" . implode("','", $_POST['members']) . "')");

    echo "<script>window.parent." . BX_DOL_ADM_MP_JS_NAME . ".reload();</script>";
    exit;
} 
else if(isset($_POST['adm-mp-delete']) && (bool)$_POST['members']) {    
    foreach($_POST['members'] as $iId)
        $bResult = profile_delete((int)$iId);

	echo "<script>window.parent." . BX_DOL_ADM_MP_JS_NAME . ".reload();</script>";
    exit;
} 
else if(isset($_POST['adm-mp-confirm']) && (bool)$_POST['members']) {    
    foreach($_POST['members'] as $iId)
        activation_mail((int)$iId, 0);

	echo "<script>alert('" . _t('_adm_txt_mp_activation_sent') . "')</script>";
    exit;
}
else if(isset($_POST['action']) && $_POST['action'] == 'get_members') {
    $aParams = array();
    if(is_array($_POST['ctl_value']))
        foreach($_POST['ctl_value'] as $sValue) {
            $aValue = explode('=', $sValue);
            $aParams[$aValue[0]] = $aValue[1];
        }

    $oJson = new Services_JSON();   
    echo $oJson->encode(array('code' => 0, 'content' => getMembers(array(
        'view_type' => $_POST['view_type'], 
        'view_start' => (int)$_POST['view_start'],
        'view_per_page' => (int)$_POST['view_per_page'],
        'view_order' => $_POST['view_order'],
        'ctl_type' => $_POST['ctl_type'],
        'ctl_params' => $aParams
    ))));
    exit;
}
else if(isset($_POST['action']) && $_POST['action'] == 'get_controls') {
	$oJson = new Services_JSON();

	$sCtlType = process_db_input($_POST['ctl_type'], BX_TAGS_STRIP);
	$sMethodName = 'getBlock' . ucfirst($sCtlType);
	if(!function_exists($sMethodName)) {
		echo '{}';
		exit;
	}

    echo $oJson->encode(array(
    	'code' => 0, 
    	'content' => $oAdmTemplate->parseHtmlByName('mp_ctl_type_' . $sCtlType . '.html', $sMethodName($sCtlType))
    ));
    exit;
}

$iNameIndex = 10;
$_page = array(
    'name_index' => $iNameIndex,
    'css_name' => array('forms_adv.css', 'profiles.css'),
    'js_name' => array('profiles.js'),
    'header' => _t('_adm_page_cpt_manage_members')
);
$_page_cont[$iNameIndex] = array(
    'page_code_controls' => PageCodeControls($sCtlType),
    'page_code_members' => PageCodeMembers($sCtlType, $sViewType),
    'obj_name' => BX_DOL_ADM_MP_JS_NAME,
    'actions_url' => $GLOBALS['site']['url_admin'] . 'profiles.php',
    'sel_control' => $sCtlType,
    'sel_view' => $sViewType,
    'per_page' => BX_DOL_ADM_MP_PER_PAGE,
    'order_by' => ''
);

PageCodeAdmin();

function PageCodeControls($sDefault = BX_DOL_ADM_MP_CTL) {
    global $oAdmTemplate;    

    $aTopMenu = array(            
        'ctl-type-qlinks' => array('href' => 'javascript:void(0)', 'onclick' => 'javascript:' . BX_DOL_ADM_MP_JS_NAME . '.changeTypeControl(this);', 'title' => _t('_adm_btn_mp_qlinks'), 'active' => $sDefault == 'qlinks' ? 1 : 0),
        //'ctl-type-browse' => array('href' => 'javascript:void(0)', 'onclick' => 'javascript:' . BX_DOL_ADM_MP_JS_NAME . '.changeTypeControl(this);', 'title' => _t('_adm_btn_mp_browse'), 'active' => $sDefault == 'browse' ? 1 : 0),
        //'ctl-type-calendar' => array('href' => 'javascript:void(0)', 'onclick' => 'javascript:' . BX_DOL_ADM_MP_JS_NAME . '.changeTypeControl(this);', 'title' => _t('_adm_btn_mp_calendar'), 'active' => $sDefault == 'calendar' ? 1 : 0),
        'ctl-type-tags' => array('href' => 'javascript:void(0)', 'onclick' => 'javascript:' . BX_DOL_ADM_MP_JS_NAME . '.changeTypeControl(this);', 'title' => _t('_adm_btn_mp_tags'), 'active' => $sDefault == 'tags' ? 1 : 0),
        'ctl-type-search' => array('href' => 'javascript:void(0)', 'onclick' => 'javascript:' . BX_DOL_ADM_MP_JS_NAME . '.changeTypeControl(this);', 'title' => _t('_adm_btn_mp_search'), 'active' => $sDefault == 'search' ? 1 : 0)
    );	    

    $aParams = array_merge(
    	getBlockQlinks($sDefault),
    	getBlockBrowse($sDefault),
    	getBlockCalendar($sDefault),
    	getBlockTags($sDefault),
    	getBlockSearch($sDefault),
    	array(
    		'loading' => LoadingBox('adm-mp-controls-loading')
    	)
    );
    return DesignBoxAdmin(_t('_adm_box_cpt_mp_controls'), $oAdmTemplate->parseHtmlByName('mp_controls.html', $aParams), $aTopMenu);
}
function getBlockQlinks($sDefault) {
    global $MySQL;
    
    $aResult = array();
    $sBaseUrl = $GLOBALS['site']['url_admin'] . 'profiles.php?type=qlinks&value=';
    
    $aItems = array();
    $aItems = array_merge($aItems, $MySQL->getAll("SELECT 'all' AS `by`, 'all' AS `value`, COUNT(`ID`) AS `count` FROM `Profiles` WHERE 1 AND (`Couple`='0' OR `Couple`>`ID`)"));
    $aItems = array_merge($aItems, $MySQL->getAll("SELECT 'status' AS `by`, `Status` AS `value`, COUNT(`ID`) AS `count` FROM `Profiles` WHERE 1 AND (`Couple`='0' OR `Couple`>`ID`) GROUP BY `Status`"));
    $aItems = array_merge($aItems, $MySQL->getAll("SELECT 'featured' AS `by`, 'featured' AS `value`, COUNT(`ID`) AS `count` FROM `Profiles` WHERE `Featured`='1'"));
    $aItems = array_merge($aItems, $MySQL->getAll("SELECT 'banned' AS `by`, 'banned' AS `value`, COUNT(`ProfID`) AS `count` FROM `sys_admin_ban_list` WHERE `Time`='0' OR (`Time`<>'0' AND DATE_ADD(`DateTime`, INTERVAL `Time` HOUR)>NOW())"));
    $aItems = array_merge($aItems, $MySQL->getAll("SELECT 'membership' AS `by`, `tl`.`Name` AS `value`, COUNT(`tlm`.`IDMember`) AS `count` FROM `sys_acl_levels` AS `tl` LEFT JOIN `sys_acl_levels_members` AS `tlm` ON `tl`.`ID`=`tlm`.`IDLevel` WHERE `tl`.`Active`='yes' AND (`tl`.`Purchasable`='yes' OR `tl`.`Name`='Promotion') AND `tlm`.`DateStarts` < NOW() AND (`tlm`.`DateExpires`>NOW() || ISNULL(`tlm`.`DateExpires`)) GROUP BY `tl`.`ID`"));
    $aItems = array_merge($aItems, $MySQL->getAll("SELECT 'sex' AS `by`, `Sex` AS `value`, COUNT(`ID`) AS `count` FROM `Profiles` WHERE NOT ISNULL(`Sex`) AND `Sex` <> '' GROUP BY `Sex`"));
    $aItems = array_merge($aItems, $MySQL->getAll("SELECT 'type' AS `by`, 'single' AS `value`, COUNT(`ID`) AS `count` FROM `Profiles` WHERE `Couple`='0'"));
    $aItems = array_merge($aItems, $MySQL->getAll("SELECT 'type' AS `by`, 'couple' AS `value`, COUNT(`ID`) AS `count` FROM `Profiles` WHERE `Couple`<>'0' AND `Couple`>`ID`"));
    $aItems = array_merge($aItems, $MySQL->getAll("SELECT 'role' AS `by`, 'admins' AS `value`, COUNT(`ID`) AS `count` FROM `Profiles` WHERE `Role` & " . BX_DOL_ROLE_ADMIN . ""));
        
    foreach($aItems as $aItem)
        $aResult[] = array('link' => 'javascript:void(0)', 'on_click' => 'javascript:' . BX_DOL_ADM_MP_JS_NAME . '.changeFilterQlinks(\'' . strtolower($aItem['by']) . '\', \'' . strtolower($aItem['value']) . '\')', 'title' => _t('_adm_txt_mp_' . strtolower($aItem['value'])), 'count' => $aItem['count']);

    return array( 
		'styles_qlinks' => $sDefault != 'qlinks' ? "display: none;" : "",
		'bx_repeat:content_qlinks' => $aResult
    );
}
function getBlockBrowse($sDefault) {
	return array(
		'styles_browse' => $sDefault != 'browse' ? "display: none;" : "",
		'content_browse' => ''
	);
}
function getBlockCalendar($sDefault) {
	return array(
		'styles_calendar' => $sDefault != 'calendar' ? "display: none;" : "",
		'content_calendar' => ''
	);
}
function getBlockTags($sDefault) {
    $oTags = new BxTemplTags();
    $oTags->setTemplateContent('<span class="one_tag" style="font-size:__tagSize__px;"><a href="javascript:void(0)" onclick="javascript:__tagHref__" title="__countCapt__: __countNum__">__tag__</a></span>');

    $aTags = $oTags->getTagList(array('type' => 'profile'));
    return array(
		'styles_tags' => $sDefault != 'tags' ? "display: none;" : "",
		'content_tags' => $oTags->getTagsView($aTags, BX_DOL_ADM_MP_JS_NAME . '.changeFilterTags(\'{tag}\')')
    );
}
function getBlockSearch($sDefault) {
    $aForm = array(
        'form_attrs' => array(
            'id' => 'adm-mp-search',
            'action' => $GLOBALS['site']['url_admin'] . 'profiles.php',
            'method' => 'post',
            'enctype' => 'multipart/form-data',
        ),
        'inputs' => array (
            'adm-mp-filter' => array(
                'type' => 'text',
                'name' => 'adm-mp-filter',
                'caption' => _t('_adm_txt_mp_filter'),
                'value' => '',
            ),
            'search' => array(
                'type' => 'button',
                'name' => 'search',
                'value' => _t('_adm_btn_mp_search'),
                'attrs' => array(
                    'onclick' => 'javascript:' . BX_DOL_ADM_MP_JS_NAME . '.changeFilterSearch()'
                )
            ),                
        )
    );

    $oForm = new BxTemplFormView($aForm);
    return array(
		'styles_search' => $sDefault != 'search' ? "display: none;" : "",
		'content_search' => $oForm->getCode()
    );
}

function PageCodeMembers($sDefaultCtl = BX_DOL_ADM_MP_CTL, $sDefaultView = BX_DOL_ADM_MP_VIEW) {
    //--- Get Controls ---//
    $aButtons = array(
        'adm-mp-activate' => _t('_adm_btn_mp_activate'),
        'adm-mp-deactivate' => _t('_adm_btn_mp_deactivate'),
        'adm-mp-ban' => _t('_adm_btn_mp_ban'),
        'adm-mp-unban' => _t('_adm_btn_mp_unban'),        
        'adm-mp-confirm' => _t('_adm_btn_mp_confirm'),
        'adm-mp-delete' => _t('_adm_btn_mp_delete'),
    );    
    $sControls = BxTemplSearchResult::showAdminActionsPanel('adm-mp-members-form', $aButtons, 'members');
    
    $aTopMenu = array(
        'view-type-simple' => array('href' => 'javascript:void(0)', 'onclick' => 'javascript:' . BX_DOL_ADM_MP_JS_NAME . '.changeTypeView(this);', 'title' => _t('_adm_btn_mp_simple'), 'active' => $sDefaultView == 'simple' ? 1 : 0),        
        'view-type-extended' => array('href' => 'javascript:void(0)', 'onclick' => 'javascript:' . BX_DOL_ADM_MP_JS_NAME . '.changeTypeView(this);', 'title' => _t('_adm_btn_mp_extended'), 'active' => $sDefaultView == 'extended' ? 1 : 0),        
        'view-type-geeky' => array('href' => 'javascript:void(0)', 'onclick' => 'javascript:' . BX_DOL_ADM_MP_JS_NAME . '.changeTypeView(this);', 'title' => _t('_adm_btn_mp_geeky'), 'active' => $sDefaultView == 'geeky' ? 1 : 0)
    );

    $oPaginate = new BxDolPaginate(array(
        'per_page' => BX_DOL_ADM_MP_PER_PAGE,
        'per_page_step' => BX_DOL_ADM_MP_PER_PAGE_STEP,
        'on_change_per_page' => BX_DOL_ADM_MP_JS_NAME . '.changePerPage(this);'
    ));    

    $aResult = array(
    	'action_url' => $GLOBALS['site']['url_admin'] . 'profiles.php',
        'ctl_type' => $sDefaultCtl,
        'view_type' => $sDefaultView,
        'change_order' => BX_DOL_ADM_MP_JS_NAME . '.changeOrder(this);',
        'per_page' => $oPaginate->getPages(),
        'control' => $sControls,
        'loading' => LoadingBox('adm-mp-members-loading')
    );

    foreach(array('simple', 'extended', 'geeky') as $sType)
        if($sType == $sDefaultView) 
            $aResult = array_merge($aResult, array('style_' . $sType => '', 'content_' . $sType => getMembers(array('view_type' => $sType))));
        else 
            $aResult = array_merge($aResult, array('style_' . $sType => 'display: none;', 'content_' . $sType => ''));

    return DesignBoxAdmin(_t('_adm_box_cpt_mp_members'), $GLOBALS['oAdmTemplate']->parseHtmlByName('mp_members.html', $aResult), $aTopMenu);
}

function getMembers($aParams) {
    if(!isset($aParams['view_start']) || empty($aParams['view_start']))
        $aParams['view_start'] = 0;

    if(!isset($aParams['view_per_page']) || empty($aParams['view_per_page']))
        $aParams['view_per_page'] = BX_DOL_ADM_MP_PER_PAGE;

	$aParams['view_order_way'] = 'ASC';
    if(!isset($aParams['view_order']) || empty($aParams['view_order']))
        $aParams['view_order'] = 'ID';
	else {
		$aOrder = explode(' ', $aParams['view_order']);
		if(count($aOrder) > 1) {
			$aParams['view_order'] = $aOrder[0];
			$aParams['view_order_way'] = $aOrder[1]; 
		}
	}

    $sDateFormat = getLocaleFormat(BX_DOL_LOCALE_DATE, BX_DOL_LOCALE_DB);

    $sSelectClause = $sJoinClause = $sWhereClause = $sGroupClause = '';
    switch($aParams['ctl_type']) {
        case 'qlinks':
            switch($aParams['ctl_params']['by']) {
                case 'status':
                    $sWhereClause .= " AND `tp`.`Status`='" . ucfirst($aParams['ctl_params']['value']) . "'";
                    break;
                case 'featured':
                    $sWhereClause .= " AND `tp`.`Featured`='1'";
                    break;
                case 'banned':
                    $sWhereClause .= " AND (`tbl`.`Time`='0' OR (`tbl`.`Time`<>'0' AND DATE_ADD(`tbl`.`DateTime`, INTERVAL `tbl`.`Time` HOUR)>NOW()))";
                    break;
                case 'type':                    
                    $sWhereClause .= $aParams['ctl_params']['value'] == 'single' ? " AND `tp`.`Couple`='0'" : " AND `tp`.`Couple`<>'0' AND `tp`.`Couple`>`tp`.`ID`";
                    break;
                case 'role':
                    $iRole = BX_DOL_ROLE_MEMBER;
                    if($aParams['ctl_params']['value'] == 'admins')
                        $iRole = BX_DOL_ROLE_ADMIN;

                    $sWhereClause .= " AND `tp`.`Role` & " . $iRole . "";
                    break;
                case 'sex':
                    $sWhereClause .= " AND LOWER(`tp`.`Sex`)='" . strtolower($aParams['ctl_params']['value']) . "'";
                    break;
                case 'membership':
                    $sWhereClause .= " AND LOWER(`tl`.`Name`)='" . strtolower($aParams['ctl_params']['value']) . "'";
                    break;
            }
            break;

        case 'tags':
            $sWhereClause .= " AND `tp`.`Tags` LIKE '%" . $aParams['ctl_params']['value'] . "%'";
            break;

        case 'search':
            $sWhereClause .= " AND (
            	`tp`.`ID` LIKE '%" . $aParams['ctl_params']['value'] . "%' OR
                `tp`.`NickName` LIKE '%" . $aParams['ctl_params']['value'] . "%' OR 
                `tp`.`Email` LIKE '%" . $aParams['ctl_params']['value'] . "%' OR 
                `tp`.`Headline` LIKE '%" . $aParams['ctl_params']['value'] . "%' OR 
                `tp`.`DescriptionMe` LIKE '%" . $aParams['ctl_params']['value'] . "%' OR 
                `tp`.`Tags` LIKE '%" . $aParams['ctl_params']['value'] . "%' OR 
                `tp`.`DateReg` LIKE '%" . $aParams['ctl_params']['value'] . "%' OR 
                `tp`.`DateLastLogin` LIKE '%" . $aParams['ctl_params']['value'] . "%'
            )";
            break;
    }
    
    //--- Get Paginate ---//
    $oPaginate = new BxDolPaginate(array(
        'start' => $aParams['view_start'],
        'count' => (int)db_value("SELECT COUNT(`tp`.`ID`) FROM `Profiles` AS `tp` LEFT JOIN `sys_admin_ban_list` AS `tbl` ON `tp`.`ID`=`tbl`.`ProfID` LEFT JOIN `sys_acl_levels_members` AS `tlm` ON `tp`.`ID`=`tlm`.`IDMember` AND `tlm`.`DateStarts` < NOW() AND (`tlm`.`DateExpires`>NOW() || ISNULL(`tlm`.`DateExpires`)) LEFT JOIN `sys_acl_levels` AS `tl` ON `tlm`.`IDLevel`=`tl`.`ID` " . $sJoinClause . " WHERE 1 AND (`tp`.`Couple`=0 OR `tp`.`Couple`>`tp`.`ID`)" . $sWhereClause),
        'per_page' => $aParams['view_per_page'],
        'page_url' => $GLOBALS['site']['url_admin'] . 'profiles.php?start={start}',
        'on_change_page' => BX_DOL_ADM_MP_JS_NAME . '.changePage({start})'
    ));
    $sPaginate = $oPaginate->getPaginate();    

    //--- Get Items ---//
    $sQuery = "
    	SELECT
    		`tp`.`ID` as `id`,
    		`tp`.`NickName` AS `username`,
    		`tp`.`Headline` AS `headline`,
    		`tp`.`Sex` AS `sex`,
    		`tp`.`DateOfBirth` AS `date_of_birth`,
    		`tp`.`Country` AS `country`,
    		`tp`.`City` AS `city`,
    		`tp`.`DescriptionMe` AS `description`,
    		`tp`.`Email` AS `email`,
    		DATE_FORMAT(`tp`.`DateReg`,  '" . $sDateFormat . "' ) AS `registration`,
    		DATE_FORMAT(`tp`.`DateLastLogin`,  '" . $sDateFormat . "' ) AS `last_login`,
    		`tp`.`Status` AS `status`,
    		IF(`tbl`.`Time`='0' OR DATE_ADD(`tbl`.`DateTime`, INTERVAL `tbl`.`Time` HOUR)>NOW(), 1, 0) AS `banned`, 
    		`tl`.`ID` AS `ml_id`, 
    		IF(ISNULL(`tl`.`Name`),'', `tl`.`Name`) AS `ml_name`
    		" . $sSelectClause . "
    	FROM `Profiles` AS `tp` 
        LEFT JOIN `sys_admin_ban_list` AS `tbl` ON `tp`.`ID`=`tbl`.`ProfID`
        LEFT JOIN `sys_acl_levels_members` AS `tlm` ON `tp`.`ID`=`tlm`.`IDMember` AND `tlm`.`DateStarts` < NOW() AND (`tlm`.`DateExpires`>NOW() || ISNULL(`tlm`.`DateExpires`))  
        LEFT JOIN `sys_acl_levels` AS `tl` ON `tlm`.`IDLevel`=`tl`.`ID` 
    	" . $sJoinClause . "
    	WHERE
    		1 AND (`tp`.`Couple`=0 OR `tp`.`Couple`>`tp`.`ID`)" . $sWhereClause . "
    	" . $sGroupClause . "
    	ORDER BY `tp`.`" . $aParams['view_order'] . "` " . $aParams['view_order_way'] . "
    	LIMIT " . $aParams['view_start'] . ", " . $aParams['view_per_page'];
    $aProfiles = $GLOBALS['MySQL']->getAll($sQuery);
    
    //--- Display ---//
    $sFunction = 'getMembers' . ucfirst($aParams['view_type']);
    return $sFunction($aProfiles, $sPaginate);
}

function getMembersGeeky($aProfiles, $sPaginate) {
    $iEmailLength = 20; 
    $aItems = array();
    foreach($aProfiles as $aProfile){
        $sEmail = ( mb_strlen($aProfile['email']) > $iEmailLength ) ? mb_substr($aProfile['email'], 0, $iEmailLength) . '...' : $aProfile['email'];

        $aItems[$aProfile['id']] = array(
            'id' => $aProfile['id'],
            'username' => $aProfile['username'],
            'email' => $sEmail,
            'full_email' => $aProfile['email'],
            'edit_link' => $GLOBALS['site']['url'] . 'pedit.php?ID=' . $aProfile['id'],
            'edit_class' => (int)$aProfile['banned'] == 1 ? 'adm-mp-banned' : ($aProfile['status'] != 'Active' ? 'adm-mp-inactive' : 'adm-mp-active'),
            'registration' => $aProfile['registration'],
            'last_login' => $aProfile['last_login'],            
            'status' => $aProfile['status'],
            'ml_id' => !empty($aProfile['ml_id']) ? (int)$aProfile['ml_id'] : 2,
            'ml_name' => !empty($aProfile['ml_name']) ? $aProfile['ml_name'] : 'Standard'
        );
    }
    
    return $GLOBALS['oAdmTemplate']->parseHtmlByName('mp_members_geeky.html', array(
        'bx_repeat:items' => array_values($aItems),
        'paginate' => $sPaginate
    ));
}
function getMembersSimple($aProfiles, $sPaginate) {
    $aItems = array();
    foreach($aProfiles as $aProfile)
        $aItems[$aProfile['id']] = array(
            'id' => $aProfile['id'],
            'thumbnail' => get_member_thumbnail($aProfile['id'], 'none'),
            'edit_link' => $GLOBALS['site']['url'] . 'pedit.php?ID=' . $aProfile['id'],
            'edit_class' => (int)$aProfile['banned'] == 1 ? 'adm-mp-banned' : ($aProfile['status'] != 'Active' ? 'adm-mp-inactive' : 'adm-mp-active'),
            'edit_width' => defined('BX_AVA_W') ? BX_AVA_W : 70,
            'username' => $aProfile['username']
        );
        
    return $GLOBALS['oAdmTemplate']->parseHtmlByName('mp_members_simple.html', array(
        'bx_repeat:items' => array_values($aItems),
        'paginate' => $sPaginate
    ));
}
function getMembersExtended($aProfiles, $sPaginate) {
    $aItems = array();
    foreach($aProfiles as $aProfile)
        $aItems[$aProfile['id']] = array(
            'id' => $aProfile['id'],
            'thumbnail' => get_member_thumbnail($aProfile['id'], 'none'),
            'edit_link' => $GLOBALS['site']['url'] . 'pedit.php?ID=' . $aProfile['id'],
            'edit_class' => (int)$aProfile['banned'] == 1 ? 'adm-mp-banned' : ($aProfile['status'] != 'Active' ? 'adm-mp-inactive' : 'adm-mp-active'),
            'username' => $aProfile['username'],
            'headline' => $aProfile['headline'],
            'sex_link' => $GLOBALS['oFunctions']->genSexIcon($aProfile['sex']),
            'age' => $aProfile['date_of_birth'] != "0000-00-00" ? _t("_y/o", age($aProfile['date_of_birth'])) : "",
            'bx_if:flag' => array(
                'condition' => !empty($aProfile['country']),
                'content' => array(
                    'flag_link' => $GLOBALS['site']['flags'] . strtolower($aProfile['country']) . '.gif'
                )
            ),
            'city' => $aProfile['city'],
            'country' => _t($GLOBALS['aPreValues']['Country'][$aProfile['country']]['LKey']),
            'description' => $aProfile['description'],
        );
    return $GLOBALS['oAdmTemplate']->parseHtmlByName('mp_members_extended.html', array(
        'bx_repeat:items' => array_values($aItems),
        'paginate' => $sPaginate
    ));
}
?>
