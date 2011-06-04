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

require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolPageView.php');

class BxSitesPageProfile extends BxDolPageView {
        
    var $_oSites;
    var $_oDb;
    var $_oTemplate;
    var $_oConfig;
    var $_sSubMenu;
    var $_aProfile;
    
    function BxSitesPageProfile(&$oSites, $aProfile, $sSubMenu) 
    {
        parent::BxDolPageView('bx_sites_profile');
        
        $GLOBALS['oTopMenu']->setCurrentProfileNickName($aProfile['NickName']);                
        $this->_oSites = &$oSites;
        $this->_oDb = $oSites->_oDb;
        $this->_oTemplate = $oSites->_oTemplate;
        $this->_oConfig = $oSites->_oConfig;
        $this->_aProfile = $aProfile;
        $this->_sSubMenu = $sSubMenu;
    }

    function getBlockCode_Administration() 
    {
        $sContent = '';
        
        switch ($this->_sSubMenu)
        {
            case 'add':
                $sContent = $this->getBlockCode_Add();
                break;
                
            case 'manage':
                $sContent = $this->getBlockCode_Manage();
                break;
                
            case 'pending':
                $sContent = $this->getBlockCode_Pending();
                break;
                
            default:
                $sContent = $this->getBlockCode_Main();
        }
        
        $sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/my';
        
        $aMenu = array(
            _t('_bx_sites_block_submenu_main') => array('href' => $sBaseUrl, 'active' => !$this->_sSubMenu),
            _t('_bx_sites_block_submenu_add_site') => array('href' => $sBaseUrl . '/add', 'active' => $this->_sSubMenu == 'add'),
            _t('_bx_sites_block_submenu_manage_sites') => array('href' => $sBaseUrl . '/manage', 'active' => $this->_sSubMenu == 'manage'),
            _t('_bx_sites_block_submenu_pending_sites') => array('href' => $sBaseUrl . '/pending', 'active' => $this->_sSubMenu == 'pending'),
        );
        
        return array($sContent, $aMenu, '', '');
    }
    
    function getBlockCode_Owner() 
    {
        bx_sites_import('SearchResult');
        $oSearchResult = new BxSitesSearchResult('user', process_db_input($this->_aProfile['NickName'], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION));
            
        if ($s = $oSearchResult->displayResultBlock(true))
            return $s;
        else
            return MsgBox(_t('_Empty'));
    }
    
    function getBlockCode_Main()
    {
        $iActive = $this->_oDb->getCountByOwnerAndStatus($this->_aProfile['ID'], 'approved');
        $iPending = $this->_oDb->getCountByOwnerAndStatus($this->_aProfile['ID'], 'pending');
        $sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "browse/my";
        $aVars = array ('msg' => '');
        
        if ($iActive)
            $sActive = sprintf(_t('_bx_sites_msg_you_have_active_sites'), $sBaseUrl . '/manage', $iActive);
        if ($iPending)
            $sPending = ($iActive ? ', ' : '') . sprintf(_t('_bx_sites_msg_you_have_pending_sites'), $sBaseUrl . '/pending', $iPending);
        
        if (isset($sActive) || isset($sPending))
            $aVars['msg'] = sprintf(_t('_bx_sites_msg_you_have_sites'), 
                isset($sActive) ? $sActive : '', isset($sPending) ? $sPending : '');
        else
            $aVars['msg'] = _t('_bx_sites_msg_no_sites');

        if ($this->_oSites->isAllowedAdd())
            $aVars['msg'] .= (strlen($aVars['msg']) ? ' ' : '') . sprintf(_t('_bx_sites_msg_add_more_sites'), $sBaseUrl . '/add');
            
        return $this->_oTemplate->parseHtmlByName('my_sites_main.html', $aVars);
        
    }
    
    function getBlockCode_Add()
    {
        if ($this->_oSites->isAllowedAdd())
            return $this->_oSites->_addSiteForm();
        else
            return MsgBox(_t('_bx_sites_msg_access_denied'));
    }
    
    function getBlockCode_Manage()
    {
        // check delete sites
        if ($_POST['action_delete'] && is_array($_POST['entry']))
            foreach ($_POST['entry'] as $iSiteId)
                $this->_oSites->deleteSite($iSiteId);
        
        $aButtons = array(
            'action_delete' => '_bx_sites_admin_delete'
        );
        $sForm = $this->_oSites->_manageSites('user', $this->_aProfile['NickName'], $aButtons);
        $aVars = array ('form' => $sForm);
        
        return $this->_oTemplate->parseHtmlByName('my_sites_manage.html', $aVars);
    }
    
    function getBlockCode_Pending()
    {
        // check delete sites
        if ($_POST['action_delete'] && is_array($_POST['entry'])) 
            foreach ($_POST['entry'] as $iSiteId)
                $this->_oSites->deleteSite($iSiteId);
        
        $aButtons = array(
            'action_delete' => '_bx_sites_admin_delete'
        );
        $sForm = $this->_oSites->_manageSites('my_pending', '', $aButtons);
        $aVars = array ('form' => $sForm);
        
        return $this->_oTemplate->parseHtmlByName('my_sites_manage.html', $aVars);
    }
}
?>
