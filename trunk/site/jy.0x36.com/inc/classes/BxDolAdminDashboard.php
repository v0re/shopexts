<?
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

bx_import('BxDolMistake');
bx_import('BxDolMailBox');

/**
 * Admin dashboard content.
 * 
 * Is needed to add blocks on the admin panel dashboard.
 * 
 * Example of usage:
 * 1. Register the block in the `sys_admin_dashboard` table. 
 *    Note. You don't need to modify the class. Use Service method to get the content of the block from your module.
 *
 *
 * Memberships/ACL:
 * Doesn't depend on user's membership.
 *
 * 
 * Alerts:
 * no alerts available
 *
 */
class BxDolAdminDashboard extends BxDolMistake {

	/**
	 * constructor
	 */
	function BxDolAdminDashboard() {
	    parent::BxDolMistake();
	}
    function getCode() {
        $iColumns = $GLOBALS['MySQL']->getOne('SELECT MAX(`column`) FROM `sys_admin_dashboard` WHERE 1');
        
        $aColumns = array();
        for($i=1; $i<=$iColumns; $i++) {
            $aBoards = array();
            
            $aItems = $GLOBALS['MySQL']->getAll("SELECT `name`, `content` FROM `sys_admin_dashboard` WHERE `column`='" . $i . "' ORDER BY `order`");
            foreach($aItems as $aItem) {
        		$aData = eval($aItem['content']);
				if (false === $aData) continue;
                $aBoards[] = array(
                    'icon' => $aData['icon'],
                    'bx_if:link' => array(
                        'condition' => isset($aData['link']) && !empty($aData['link']),
                        'content' => array(
                            'link' => $aData['link'],
                            'title' => $aData['title']
                        )
                    ),
                    'bx_if:text' => array(
                        'condition' => !isset($aData['link']) || empty($aData['link']),
                        'content' => array(
                            'title' => $aData['title']
                        )
                    ),
                    'content' => $aData['content']
                );
            }

            $aColumns[] = array(
                'bx_repeat:boards' => $aBoards
            );
        }
        
        return $GLOBALS['oAdmTemplate']->parseHtmlByName('dashboard.html', array('bx_repeat:columns' => $aColumns));
    }
	function getAdminBlock() {
	    global $oAdmTemplate;
	    
	    $aInfo = $GLOBALS['MySQL']->getRow("SELECT `NickName` AS `username`, `Password` AS `Password`, DATE_FORMAT(`DateLastLogin`, '" . getLocaleFormat(BX_DOL_LOCALE_DATE, BX_DOL_LOCALE_DB) . "') AS `last_login` FROM `Profiles` WHERE `ID`='" . $_COOKIE['memberID'] . "' LIMIT 1");
	    
	    return array(
	       'icon' => $oAdmTemplate->getIconUrl('dashboard_admin.png'),
	       'title' => ucfirst($aInfo['username']),
	       'url' => '',
	       'content' => _t('_adm_txt_dashboard_last_login') . ': ' . $aInfo['last_login'] . '<br /><a href="' . $GLOBALS['site']['url_admin'] . 'settings.php?cat=ap">' . _t('_adm_txt_dashboard_change_password') . '</a>'
	    );
	}
	function getInfoBlock() {
	    global $oAdmTemplate, $MySQL, $site;

	    $sDaysLeft = '';
	    $iExpirationDays = 0;

	    $sLicense = getParam('license_code');
	    if(!empty($sLicense)) {
		    $iExpirationDate = (int)getParam('license_expiration');
		    $iExpirationDays = (int)ceil(($iExpirationDate - mktime())/86400);
		    $sDaysLeft = $iExpirationDate != 0 ? _t('_adm_txt_dashboard_license', $iExpirationDays) : _t('_adm_txt_dashboard_license_unlimit');
	    }

	    $iExtensionsCount = (int)$MySQL->getOne('SELECT COUNT(`id`) FROM `sys_modules` WHERE 1');
	    $sExtensionsLink = $site['url_admin'] . 'index.php?cat=extensions';

	    $iAlertsCount = 0;

	    $sCurrentVersion = $GLOBALS['site']['ver'] . '.' . $GLOBALS['site']['build'];
	    return array(
            'icon' => $oAdmTemplate->getIconUrl('dashboard_info.png'),
            'title' => 'Dolphin ' . $sCurrentVersion,
            'url' => '',
            'content' => $oAdmTemplate->parseHtmlByName('dashboard_content_info.html', array(
	    		'current_version' => $sCurrentVersion,
                'cell_11' => '<a href="' . $sExtensionsLink . '">' . _t('_adm_txt_dashboard_extensions') . '</a> (' . $iExtensionsCount . ')',
                'cell_12' => $sDaysLeft,
	    		'cell_12_class' => $iExpirationDays > 0 && $iExpirationDays <= 5 ? 'warning' : '',
                'cell_21' => '',
                'cell_22' => '' //'<a href="#">' . _t('_adm_txt_dashboard_alerts') . '</a> (' . $iAlertsCount . ')'
            ))
	    );
	}
	function getUsersBlock() {
	    global $oAdmTemplate, $MySQL, $site;
        $iUsersCountAll = (int)$MySQL->getOne("SELECT COUNT(`ID`) FROM `Profiles` WHERE 1");
        $iUsersCountUnapproved = (int)$MySQL->getOne("SELECT COUNT(`ID`) FROM `Profiles` WHERE `Status`<>'Active'");
        
	    return array(
            'icon' => $oAdmTemplate->getIconUrl('dashboard_users.png'),
            'title' => '<a href="' . $site['url_admin'] . 'profiles.php">' . _t('_adm_txt_dashboard_users') . '</a>',
            'url' => '',
            'content' => $oAdmTemplate->parseHtmlByName('dashboard_content.html', array(
                'cell_11' => '<a href="' . $site['url_admin'] . 'profiles.php?type=all">' . _t('_adm_txt_dashboard_users_all') . '</a> (' . $iUsersCountAll . ')',
                'cell_12' => '',
                'cell_21' => '<a href="' . $site['url_admin'] . 'profiles.php?type=unapproved">' . _t('_adm_txt_dashboard_users_unapproved') . '</a> (' . $iUsersCountUnapproved . ')',
                'cell_22' => ''
            ))
	    );	    
	}
	function getMailsBlock() {
	    global $oAdmTemplate;

        $iAdminId    = ( isset($_COOKIE['memberID']) ) ? (int) $_COOKIE['memberID'] : 0;
	    $iInboxCount = BxDolMailBox::getCountInboxMessages($iAdminId);
	    $iSentCount  = BxDolMailBox::getCountSentMessages($iAdminId);
	    $iTrashCount = BxDolMailBox::getCountTrashedMessages($iAdminId);

	    return array(
            'icon' => $oAdmTemplate->getIconUrl('dashboard_mails.png'),
            'title' => '<a href="#">' . _t('_adm_txt_dashboard_mails') . '</a>',
            'url' => '',
            'content' => $oAdmTemplate->parseHtmlByName('dashboard_content.html', array(
                'cell_11' => '<a href="' . BX_DOL_URL_ROOT . 'mail.php?&mode=inbox">'   . _t('_adm_txt_dashboard_mails_inbox')   . '</a> (' . $iInboxCount . ')',
                'cell_12' => '<a href="' . BX_DOL_URL_ROOT . 'mail.php?&mode=outbox">'  . _t('_adm_txt_dashboard_mails_sent')    . '</a> (' . $iSentCount . ')',
                'cell_21' => '<a href="' . BX_DOL_URL_ROOT . 'mail.php?&mode=trash">'   . _t('_adm_txt_dashboard_mails_trash')   . '</a> (' . $iTrashCount . ')',
                'cell_22' => '<a href="' . BX_DOL_URL_ROOT . 'mail.php?&mode=compose">' . _t('_adm_txt_dashboard_mails_compose') . '</a>',
            ))
	    );	    
	}
	function getCacheBlock() {
	    global $oAdmTemplate;

	    return array(
            'icon' => $oAdmTemplate->getIconUrl('dashboard_cache.png'),
            'title' => _t('_adm_txt_dashboard_cache'),
            'url' => '',
            'content' => $oAdmTemplate->parseHtmlByName('dashboard_content_cache.html', array(
                'cell_11' => '<a href="javascript:void(0)" onclick="javascript:clearCache(\'all\');">'   . _t('_adm_txt_dashboard_cache_all')   . '</a>',
                'cell_12' => '<a href="javascript:void(0)" onclick="javascript:clearCache(\'db\');">'  . _t('_adm_txt_dashboard_cache_db')    . '</a>',
                'cell_13' => '<a href="javascript:void(0)" onclick="javascript:clearCache(\'pb\');">'  . _t('_adm_txt_dashboard_cache_pb')    . '</a>',
                'cell_21' => '<a href="javascript:void(0)" onclick="javascript:clearCache(\'template\');">'   . _t('_adm_txt_dashboard_cache_template')   . '</a>',
                'cell_22' => '<a href="javascript:void(0)" onclick="javascript:clearCache(\'js_css\');">' . _t('_adm_txt_dashboard_cache_js_css') . '</a>',
                'cell_23' => '<a href="javascript:void(0)" onclick="javascript:clearCache(\'users\');">' . _t('_adm_txt_dashboard_cache_users') . '</a>',
            ))
	    );	    
	}
	function getLicenseBlock ($sType) {
	    global $oAdmTemplate;

		$a = array (
			'unlim' => array ('title' => 'Unlimited Time License', 'text' => 'Removes BoonEx links from 1 domain for unlimited time.', 'url' => 'http://www.boonex.com/payment.php'),
			'prime' => array ('title' => 'Prime', 'text' => 'BoonEx links removal, mobile apps re-branding, installation and <a href="http://www.boonex.com/products/prime/">more</a>.', 'url' => 'http://www.boonex.com/payment.php'),
			'premium' => array ('title' => 'Premium Membership', 'text' => 'Upgrade your Unity account status to receive <a href="http://www.boonex.com/unity/txt/membership">Premium Membership Benefits</a>.', 'url' => 'http://www.boonex.com/unity/txt/membership'),
		);

		if (!isset($a[$sType]))
			return false;

		if (!getParam('enable_dolphin_footer') && $sType != 'premium')
			return false;

	    return array(
            'icon' => $oAdmTemplate->getIconUrl('dashboard_bx_' . $sType . '.png'),
            'title' => '<a target="_blank" href="' . $a[$sType]['url'] . '">' . $a[$sType]['title'] . '</a>',
            'url' => '',
            'content' => $a[$sType]['text'],
	    );	    
	}
}
