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

function bx_sites_import ($sClassPostfix, $aModuleOverwright = array()) {
    global $aModule;
    $a = $aModuleOverwright ? $aModuleOverwright : $aModule;
    if (!$a || $a['uri'] != 'sites') {
        $oMain = BxDolModule::getInstance('BxSitesModule');
        $a = $oMain->_aModule;
    }
    bx_import ($sClassPostfix, $a) ;
    
}

function getEntryUri($sTitle)
{
    $sUri = preg_replace('/[^a-zA-Z0-9]/', ' ', $sTitle);
    $sUri = preg_replace('/ +/', '_', trim($sUri));
     
    return $sUri;
}

bx_import('BxDolModule');

require_once('BxSitesPrivacy.php');

/**
 * Sites module
 *
 * This module allow users to post description sites, 
 * users can rate, comment, discuss it.
 *
 * 
 *
 * Profile's Wall:
 * 'add site' site are displayed in profile's wall
 *
 *
 *
 * Spy:
 * 'add site' site is displayed in spy
 *
 *
 *
 * Memberships/ACL:
 * sites view - BX_SITES_VIEW
 * sites browse - BX_SITES_BROWSE
 * sites edit any site - BX_SITES_EDIT_ANY_SITE
 * sites delete any site - BX_SITES_DELETE_ANY_SITE
 * sites mark as featured - BX_SITES_MARK_AS_FEATURED
 *
 * 
 *
 * Alerts:
 * Alerts type/unit - 'bx_sites'
 * The following alerts are rised
 *
 *  add - new site was added
 *      $iObjectId - site id
 *      $iSenderId - creator of an site
 *      $aExtras['Status'] - status of added site
 *
 *  change - site's info was changed
 *      $iObjectId - site id
 *      $iSenderId - editor user id
 *      $aExtras['Status'] - status of changed site
 *
 *  delete - site was deleted
 *      $iObjectId - site id
 *      $iSenderId - deleter user id
 *
 *  mark_as_featured - site was marked/unmarked as featured
 *      $iObjectId - site id
 *      $iSenderId - performer id
 *      $aExtras['Featured'] - 1 - if site was marked as featured and 0 - if site was removed from featured
 *      
 *       
 *  Using service for get thumbnail sites
 *  
 *  1. Register on site "http://www.shrinktheweb.com"
 *  2. Login and get "Access Key ID" and "Secret Key"(see in block "Website Thumbnails" section "Your Access Keys")
 *  3. Insert them in Administration -> Extensions -> Sites -> Settings "Access key id" and "Password" respectively
 *
 */
class BxSitesModule extends BxDolModule
{
    var $oPrivacy;
    var $iOwnerId;

    /**
     * Constructor
     */
    function BxSitesModule($aModule)
    {
        parent::BxDolModule($aModule);
        $this->_oConfig->init($this->_oDb);
        $this->oPrivacy = new BxSitesPrivacy($this);
        $this->iOwnerId = $GLOBALS['logged']['member'] || $GLOBALS['logged']['admin'] ? $_COOKIE['memberID'] : 0;
        $GLOBALS['oBxSitesModule'] = &$this;
    }

    function actionHome()
    {
        bx_sites_import ('PageMain');
        $oPage = new BxSitesPageMain ($this);
        $this->_oTemplate->addCss(array('main.css', 'block_percent.css'));
        $this->_oTemplate->pageStart();
        echo $oPage->getCode();
        $this->_oTemplate->pageCode(_t('_bx_sites_caption_home'), false, false);
    }

    function actionCalendar($iYear = '', $iMonth = '')
    {
        bx_sites_import('Calendar');
        $oCalendar = new BxSitesCalendar($iYear, $iMonth, $this);
        $this->_oTemplate->pageStart();
        echo $oCalendar->display();
        $this->_oTemplate->pageCode(_t('_bx_sites_caption_browse_calendar'), true, false);
    }

    function actionDelete($iSiteId)
    {
        $iSiteId = (int)$iSiteId;
        
        if (!($aSite = $this->_oDb->getSiteById($iSiteId))) {
            $this->_oTemplate->displayPageNotFound (_t('_bx_sites_action_title_delete'));
            return;
        }

        if (!$this->isAllowedDelete($aSite)) {
            echo MsgBox(_t('_bx_events_msg_access_denied')) . genAjaxyPopupJS($iSiteId, 'ajaxy_popup_result_div');
            exit;
        }

        if ($this->deleteSite($iSiteId))
        {
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/my';
            $sJQueryJS = genAjaxyPopupJS($iSiteId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox(_t('_bx_sites_site_was_deleted')) . $sJQueryJS;
            exit;
        }

        echo MsgBox(_t('_bx_sites_error_occured')) . genAjaxyPopupJS($iSiteId, 'ajaxy_popup_result_div');
        exit;
    }

    function actionEdit($iSiteId)
    {
        $iSiteId = (int)$iSiteId;
        
        if (!($aSite = $this->_oDb->getSiteById($iSiteId))) 
        {
            $this->_oTemplate->displayPageNotFound (_t('_bx_site_caption_edit'));
            return;
        }

        if (!$this->isAllowedEdit($aSite)) 
        {
            $this->_oTemplate->displayAccessDenied (_t('_bx_site_caption_edit'));
            return;
        }

        bx_sites_import('FormEdit');
        $oForm = new BxSitesFormEdit($this, $aSite);
        $oForm->initChecker($aSite);

        $this->_oTemplate->addCss(array('main.css'));

        if ($oForm->isSubmittedAndValid ())
        {
            $sStatus = $this->_oDb->getParam('bx_sites_autoapproval') == 'on' || $this->isAdmin() ? 'approved' : 'pending';
            $sCategories = implode(';', array_unique(explode(';', $oForm->getCleanValue('categories'))));
            unset($oForm->aInputs['categories']);
            $aValsAdd = array (
                'photo' => $oForm->checkUploadPhoto(),
                'categories' => $sCategories,
                'status' => $sStatus
            );

            if ($oForm->update($iSiteId, $aValsAdd))
            {
                $this->isAllowedEdit($aSite, true);
                $this->onSiteChanged($iSiteId, $sStatus);
                if ($sStatus == 'approved')
                    header('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aSite['entryUri']);
                else
                    header('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri());
            }
            else
            {
                $this->_oTemplate->pageStart();
                echo MsgBox(_t('_bx_sites_err_edit_site'));
            }
        }
        else
        {
            $this->_oTemplate->pageStart();
            echo $oForm->getCode();
        }

        $this->_oTemplate->pageCode(_t('_bx_site_caption_edit'));
    }

    function actionView($mixedVar)
    {
        $GLOBALS['oTopMenu']->setCustomSubHeader(_t('_bx_sites'));        
        
        $aSite = is_numeric($mixedVar) ? $this->_oDb->getSiteById($mixedVar) : $this->_oDb->getSiteByEntryUri($mixedVar);
        
        if (empty($aSite)) {
            $this->_oTemplate->displayPageNotFound (_t('_bx_sites'));
            return;
        }
        
        if (!$this->isAllowedView($aSite))
        {
            $this->_oTemplate->displayAccessDenied($aSite['title']);
            return;
        }

        if ($aSite['status'] == 'pending' && !$this->isAdmin() && !($aSite['ownerid'] == $this->iOwnerId && $aEvent['ownerid']))  {
            $this->_oTemplate->displayAccessDenied($aSite['title']);
            return;
        }

        if ($aSite['Status'] == 'pending') {
            $this->_oTemplate->displayPendingApproval($aSite['title']);
            return;
        }

        bx_sites_import ('PageView');
        $oPage = new BxSitesPageView ($this, $aSite);
        $this->_oTemplate->addCss(array('main.css', 'cmts.css'));
        $this->_oTemplate->pageStart();
        echo $oPage->getCode();
        $GLOBALS['oTopMenu']->setCustomSubHeader($aSite['title']);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_bx_sites') => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aSite['title'] => '',
        ));
        $this->_oTemplate->pageCode($aSite['title'], false, false);

        bx_import ('BxDolViews');
        new BxDolViews('bx_sites', $aSite['id']);
        
        $this->isAllowedView($aSite, true);
    }

    function actionFeatured($iSiteId)
    {
        $iSiteId = (int)$iSiteId;
        
        if (!($aSite = $this->_oDb->getSiteById($iSiteId))) {
            $this->_oTemplate->displayPageNotFound (_t('_bx_sites_featured_top_menu_sitem'));
            return;
        }

        if (!$this->isAllowedMarkAsFeatured($aSite)) {
            echo MsgBox(_t('_bx_events_msg_access_denied')) . genAjaxyPopupJS($iSiteId, 'ajaxy_popup_result_div');
            exit;
        }

        if ($this->_oDb->markFeatured($iSiteId)) {
            $this->isAllowedMarkAsFeatured($aSite, true);
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aSite['entryUri'];
            $sJQueryJS = genAjaxyPopupJS($iSiteId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox($aSite['featured'] ? _t('_bx_sites_msg_removed_from_featured') : _t('_bx_sites_msg_added_to_featured')) . $sJQueryJS;
            exit;
        }

        echo MsgBox(_t('_bx_sites_error_occured')) . genAjaxyPopupJS($iSiteId, 'ajaxy_popup_result_div');
        exit;
    }

    function actionShare($iSiteId)
    {
    }

    function actionHon()
    {
        bx_sites_import('PageHon');
        $oPage = new BxSitesPageHon($this);
        $this->_oTemplate->addCss(array('main.css', 'block_percent.css'));
        $this->_oTemplate->pageStart();
        echo $oPage->getCode();
        $this->_oTemplate->pageCode(_t('_bx_sites_hon'), false, false);
    }

    function actionSearch()
    {
        if (!$this->isAllowedSearch()) 
        {
            $this->_oTemplate->displayAccessDenied(_t('_bx_sites_caption_browse_search'), false);
            return;
        }
        
        bx_sites_import ('FormSearch');
        $oForm = new BxSitesFormSearch($this->_oConfig);
        $oForm->initChecker();

        $this->_oTemplate->addCss(array('main.css'));

        if ($oForm->isSubmittedAndValid ()) 
        {

            bx_sites_import('SearchResult');
            $o = new BxSitesSearchResult('search', $oForm->getCleanValue('Keyword'));

            if ($o->isError) 
            {
                $this->_oTemplate->displayPageNotFound (_t('_bx_sites_caption_browse_search'));
                return;
            }

            if ($s = $o->processing()) 
            {
                $this->_oTemplate->pageStart();
                echo $s;
            } 
            else 
            {
                $this->_oTemplate->displayNoData (_t('_bx_sites_caption_browse_search'));
                return;
            }

            $this->isAllowedSearch(true);
            $this->_oTemplate->pageCode($o->aCurrent['title'], false, false);

        } 
        else 
        {
            $this->_oTemplate->pageStart();
            echo $oForm->getCode ();
            $this->_oTemplate->pageCode(_t('_bx_sites_caption_browse_search'));
        }
    }

    function actionBrowse($sMode = '', $sValue = '', $sValue2 = '', $sValue3 = '')
    {
        $bAjaxMode = isset($_SERVER['HTTP_X_REQUESTED_WITH']) and $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ? true : false;

        if (('user' == $sMode || 'my' == $sMode) && $this->iOwnerId > 0) 
        {
            $aProfile = getProfileInfo($this->iOwnerId);
            if (0 == strcasecmp($sValue, $aProfile['NickName']) || 'my' == $sMode) 
            {
                $this->browseMy ($aProfile, process_db_input($sValue));
                return;
            }
        }

        if (!$this->isAllowedBrowse() || ('my' == $sMode && $this->iOwnerId == 0)) 
        {
            $this->_oTemplate->displayAccessDenied(_t('_bx_sites'), $bAjaxMode);
            return;
        }

        bx_sites_import ('SearchResult');
        $o = new BxSitesSearchResult(
                process_db_input($sMode),
                process_db_input($sValue),
                process_db_input($sValue2),
                process_db_input($sValue3)
            );
        $this->_oTemplate->addCss(array('main.css'));

        if ($o->isError) 
        {
            $this->_oTemplate->displayNoData($o->aCurrent['title'], $bAjaxMode);
            return;
        }

        if(bx_get('rss') !== false && bx_get('rss')) {
            echo $o->rss();
            exit;
        }
        
        $s = $bAjaxMode ? $o->displayResultBlock(true, true) : $o->processing();

        if ($s) 
        {
            if (!$bAjaxMode)
            {
                $this->_oTemplate->pageStart();
                echo $s;
                $this->_oTemplate->pageCode($o->aCurrent['title'], false, false);
            }
            else
                echo $s;
        } 
        else 
            $this->_oTemplate->displayNoData($o->aCurrent['title'], $bAjaxMode);
    }

    function actionDeleteProfileSites ($iProfileId) 
    {
        $iProfileId = (int)$iProfileId;
        
        if (!$iProfileId || !defined('BX_SITES_ON_PROFILE_DELETE'))
            return;
            
        $aSites = $this->_oDb->getSitesByAuthor($iProfileId);
        foreach ($aSites as $aSiteRow)
            $this->deleteSite($aSiteRow['id']);
    }

    function actionSharePopup ($iSiteId) 
    {
        $iSiteId = (int)$iSiteId;
        
        if (!($aSite = $this->_oDb->getSiteById($iSiteId, 0, true))) {
            echo '<div class="bx_sites_share_popup">'.MsgBox(_t('_Empty')).'</div>';
            return;
        }

        require_once (BX_DIRECTORY_PATH_INC . "shared_sites.inc.php");
        $aSitesPrepare = getSitesArray (BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aSite['entryUri']);
        $sIconsUrl = getTemplateIcon('digg.png');
        $sIconsUrl = str_replace('digg.png', '', $sIconsUrl);
        $aSites = array ();
        foreach ($aSitesPrepare as $k => $r) {
            $aSites[] = array(
                'icon' => $sIconsUrl . $r['icon'],
                'name' => $k,
                'url' => $r['url']
            );
        }

        $aVars = array (
            'title' => _t('_bx_sites_caption_share_site'),
            'icon_close_url' => getTemplateImage ('close.gif'),            
            'bx_repeat:sites' => $aSites,
        );

        echo $GLOBALS['oFunctions']->transBox($this->_oTemplate->parseHtmlByName('share_popup.html', $aVars), true);
        exit;
    }

    function actionIndex()
    {
        echo $this->_getSitesIndex();
    }
    
    function actionProfile($sNickName)
    {
        echo $this->_getSitesProfile($sNickName);
    }

    function actionAdministration($sUrl = '')
    {
        if (!$this->isAdmin()) 
        {
            $this->_oTemplate->displayAccessDenied (_t('_bx_sites'));
            return;
        }

        $aMenu = array(
            'home' => array(
                'title' => _t('_bx_sites_pending_approval'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/home', 
                '_func' => array ('name' => '_actionAdministrationManage', 'params' => array(false)),
            ),
                'admin_entries' => array(
                    'title' => _t('_bx_sites_administration_admin_sites'), 
                    'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/admin_entries',
                    '_func' => array ('name' => '_actionAdministrationManage', 'params' => array(true)),
            ),
                'add' => array(
                    'title' => _t('_bx_sites_administration_add_site'), 
                    'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/add',
                    '_func' => array ('name' => '_actionAdministrationAdd', 'params' => array()),
            ),
                'settings' => array(
                    'title' => _t('_bx_sites_administration_settings'), 
                    'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/settings',
                    '_func' => array ('name' => '_actionAdministrationSettings', 'params' => array()),
            ),
        );

        if (empty($aMenu[$sUrl]))
            $sUrl = 'home';

        $aMenu[$sUrl]['active'] = 1;
        $sContent = call_user_func_array(array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']);

        $this->_oTemplate->pageStart();
        echo $this->_oTemplate->adminBlock ($sContent, _t('_bx_sites_administration'), $aMenu);
        $this->_oTemplate->addCssAdmin('forms_adv.css');
        $this->_oTemplate->addCssAdmin('main.css');
        $this->_oTemplate->pageCodeAdmin (_t('_bx_sites_administration'));
    }

    function actionAdd()
    {
        if (!$this->isAllowedAdd()) 
        {
            $this->_oTemplate->displayAccessDenied(_t('_bx_sites'));
            return;
        }
        
        $this->_oTemplate->addCss(array('main.css'));
        $this->_oTemplate->pageStart();
        echo $this->_addSiteForm();
        $this->_oTemplate->pageCode(_t('_bx_sites_bcaption_site_add'), true, false);
    }

    function actionTags()
    {
        bx_import('BxTemplTagsModule');
        $aParam = array(
            'type' => 'bx_sites',
            'orderby' => 'popular'
            );
            $oTags = new BxTemplTagsModule($aParam, '', BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'tags');
            $this->_oTemplate->pageStart();
            echo $oTags->getCode();
            $this->_oTemplate->pageCode(_t('_bx_sites_caption_browse_tags'), false, false);
    }

    function actionCategories()
    {
        bx_import('BxTemplCategoriesModule');
        $aParam = array(
            'type' => 'bx_sites'
            );
            $oCateg = new BxTemplCategoriesModule($aParam, '', BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'categories');
            $this->_oTemplate->pageStart();
            echo $oCateg->getCode();
            $this->_oTemplate->pageCode(_t('_bx_sites_caption_browse_categories'), false, false);
    }

    /**
     * Service methods
     */
    function serviceIndexBlock() 
    {
        return $this->_getSitesIndex();
    }
    
    function serviceProfileBlock($sNickName) 
    {
        return $this->_getSitesProfile($sNickName);
    }

    function serviceGetWallPost ($aEvent) {

        if (!($aProfile = getProfileInfo($aEvent['owner_id'])))
            return '';
        if (!($aSite = $this->_oDb->getSiteById($aEvent['object_id'])))
            return '';

        $sCss = '';
        if($aEvent['js_mode'])
            $sCss = $this->_oTemplate->addCss('wall_post.css', true);
        else
            $this->_oTemplate->addCss('wall_post.css');

        $aVars = array(
                'cpt_user_name' => $aProfile['NickName'],
                'cpt_added_new' => _t('_bx_sites_wall_added_new'),
                'cpt_object' => _t('_bx_sites_wall_object'),
                'cpt_site_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aSite['entryUri'],
                'post_id' => $aEvent['id'],
                'site_title' => $aSite['title'],
                'site_description' => $aSite['description']
        );
        return array(
            'title' => $aProfile['username'] . ' ' . _t('_bx_sites_wall_added_new') . ' ' . _t('_bx_sites_wall_object'),
            'description' => $aSite['description'],
            'content' => $sCss . $this->_oTemplate->parseHtmlByName('wall_post.html', $aVars)
        );
    }

    function serviceGetWallData () 
    {
        return array(
            'handlers' => array(
                array('alert_unit' => 'bx_sites', 'alert_action' => 'add', 'module_uri' => 'sites', 'module_class' => 'Module', 'module_method' => 'get_wall_post')
            ),
            'alerts' => array(
                array('unit' => 'bx_sites', 'action' => 'add')
            )
        );
    }

    function serviceGetSpyData () {
        return array(
            'handlers' => array(
                array('alert_unit' => 'bx_sites', 'alert_action' => 'add', 'module_uri' => 'sites', 'module_class' => 'Module', 'module_method' => 'get_spy_post'),
                array('alert_unit' => 'bx_sites', 'alert_action' => 'change', 'module_uri' => 'sites', 'module_class' => 'Module', 'module_method' => 'get_spy_post'),
                array('alert_unit' => 'bx_sites', 'alert_action' => 'rate', 'module_uri' => 'sites', 'module_class' => 'Module', 'module_method' => 'get_spy_post'),
                array('alert_unit' => 'bx_sites', 'alert_action' => 'commentPost', 'module_uri' => 'sites', 'module_class' => 'Module', 'module_method' => 'get_spy_post')
            ),
            'alerts' => array(
                array('unit' => 'bx_sites', 'action' => 'add'),
                array('unit' => 'bx_sites', 'action' => 'change'),
                array('unit' => 'bx_sites', 'action' => 'rate'),
                array('unit' => 'bx_sites', 'action' => 'commentPost')
            )
        );
    }
    
    function serviceGetSpyPost($sAction, $iObjectId, $iSenderId, $aExtraParams = array())
    {
        $aRet = array();

        switch($sAction) {
            case 'add' :
            case 'change' :
            case 'rate' :
            case 'commentPost' :
                $aSite = $this->_oDb->getSiteById($iObjectId);
                if (!empty($aSite))
                    $aRet = array(
                        'lang_key'  => '_bx_sites_poll_' . $sAction,
                        'params'    => array(
                            'profile_link' => $iSenderId ? getProfileLink($iSenderId) : 'javascript:void(0)',
                            'profile_nick' => $iSenderId ? getNickName($iSenderId) : _t('_Guest'),
                            'site_url' => !empty($aSite) ? $this->_oConfig->getBaseUri() . 'view/' . $aSite['entryUri'] : '',
                            'site_caption' => !empty($aSite) ? $aSite['title'] : ''
                        ),
                        'recipient_id' => $aSite['ownerid'],
                        'spy_type' => 'content_activity',
                    );
                break;
                
        }

        return $aRet;
    }
    
    function browseMy($aProfile, $sValue = '')
    {
        bx_sites_import ('PageProfile');
        if (strlen($sValue))
            $sTitle = _t('_bx_sites_caption_browse_' . $sValue);
        else
            $sTitle = _t('_bx_sites_caption_browse_my');
        $oPage = new BxSitesPageProfile($this, $aProfile, $sValue);
        $this->_oTemplate->addCss(array('main.css'));
        $this->_oTemplate->pageStart();
        echo $oPage->getCode();
        $this->_oTemplate->pageCode($sTitle, false, false, true);
    }

    function isAdmin()
    {
        return isAdmin($this->iOwnerId);
    }

    function isAllowedEdit($aSite, $isPerformAction = false)
    {
        if ($this->isAdmin() || ($GLOBALS['logged']['member'] && $aSite['ownerid'] == $this->iOwnerId && isProfileActive($this->iOwnerId))) 
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->iOwnerId, BX_SITES_EDIT_ANY_SITE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }
    
    function isAllowedAdd ($isPerformAction = false) 
    {
        if ($this->isAdmin()) 
            return true;
        if (!$GLOBALS['logged']['member']) 
            return false;
        $this->_defineActions();
        $aCheck = checkAction($this->iOwnerId, BX_SITES_ADD, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }
     
    function isAllowedMarkAsFeatured($aSite, $isPerformAction = false)
    {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->iOwnerId, BX_SITES_MARK_AS_FEATURED, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedDelete(&$aSite, $isPerformAction = false)
    {
        if ($this->isAdmin() || ($GLOBALS['logged']['member'] && $aSite['ownerid'] == $this->iOwnerId && isProfileActive($this->iOwnerId))) 
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->iOwnerId, BX_SITES_DELETE_ANY_SITE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }
     
    function isAllowedShareSite(&$aSite)
    {
        return true;
    }

    function isAllowedView ($aSite, $isPerformAction = false) 
    {
        // admin and owner always have access
        if ($this->isAdmin() || $aSite['ownerid'] == $this->iOwnerId) 
            return true;

        // check admin acl
        $this->_defineActions();
        $aCheck = checkAction($this->iOwnerId, BX_SITES_VIEW, $isPerformAction);
        if ($aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED)
            return false;
            
        // check user group 
        return $this->oPrivacy->check('view', $aSite['id'], $this->iOwnerId); 
    }

    function isAllowedBrowse ($isPerformAction = false) {
        if ($this->isAdmin()) return true;
        $this->_defineActions();
        $aCheck = checkAction($this->iOwnerId, BX_SITES_BROWSE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }
    
    function isAllowedSearch ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->iOwnerId, BX_SITES_SEARCH, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }
    
    function deleteSite($iSiteId)
    {
        $aSite = $this->_oDb->getSiteById($iSiteId);

        if (count($aSite) > 0 && $this->_oDb->deleteSiteById($iSiteId))
        {
            if ($aSite['photo'] != 0)
                BxDolService::call('photos', 'remove_object', array($aSite['photo']), 'Module');

            $this->isAllowedDelete($aSite, true);
            $this->onSiteDeleted($iSiteId);

            return true;
        }

        return false;
    }

    function setStatusSite($iSiteId, $sStatus)
    {
        $this->_oDb->setStatusSite($iSiteId, $sStatus);
        $this->onSiteChanged($iSiteId, $sStatus);
    }

    function _defineActions () {
        defineMembershipActions(array('sites view', 'sites browse', 'sites search', 'sites add', 'sites edit any site', 'sites delete any site', 'sites mark as featured', 'sites approve'));
    }

    // ================================== tags/cats reparse functions

    function reparseTags ($iSiteId) {
        bx_import('BxDolTags');
        $o = new BxDolTags ();
        $o->reparseObjTags('bx_sites', $iSiteId);
    }

    function reparseCategories ($iSiteId) {
        bx_import('BxDolCategories');
        $o = new BxDolCategories ();
        $o->reparseObjTags('bx_sites', $iSiteId);
    }

    // ================================== events

    function onSiteCreate ($iSiteId, $sStatus) {
        
        if ('approved' == $sStatus) {
            $this->reparseTags ($iSiteId);
            $this->reparseCategories ($iSiteId);
        }

        bx_import('BxDolAlerts');
        $oAlert = new BxDolAlerts('bx_sites', 'add', $iSiteId, $this->iOwnerId, array('Status' => $sStatus));
        $oAlert->alert();
    }

    function onSiteChanged ($iSiteId, $sStatus) {
        $this->reparseTags ($iSiteId);
        $this->reparseCategories ($iSiteId);

        bx_import('BxDolAlerts');
        $oAlert = new BxDolAlerts('bx_sites', 'change', $iSiteId, $this->iOwnerId, array('Status' => $sStatus));
        $oAlert->alert();
    }

    function onSiteDeleted ($iSiteId) {

        // delete associated tags and categories
        $this->reparseTags ($iSiteId);
        $this->reparseCategories ($iSiteId);

        // delete sites votings
        bx_import('BxDolVoting');
        $oVotingProfile = new BxDolVoting ('bx_sites', 0, 0);
        $oVotingProfile->deleteVotings ($iSiteId);

        // delete sites comments
        bx_import('BxDolCmts');
        $oCmts = new BxDolCmts ('bx_sites', $iSiteId);
        $oCmts->onObjectDelete ();

        // delete views
        bx_import ('BxDolViews');
        $oViews = new BxDolViews('bx_sites', $iSiteId, false);
        $oViews->onObjectDelete($iSiteId);

        // arise alert
        bx_import('BxDolAlerts');
        $oAlert = new BxDolAlerts('bx_sites', 'delete', $iSiteId, $this->iOwnerId);
        $oAlert->alert();
    }

    function onSiteMarkAsFeatured ($aSite) {

        // arise alert
        bx_import('BxDolAlerts');
        $oAlert = new BxDolAlerts('bx_sites', 'mark_as_featured', $aSite['id'], $aSite['Featured']);
        $oAlert->alert();
    }

    // private functions

    function _actionAdministrationManage($isAdminEntries)
    {
        if ($_POST['action_activate'] && is_array($_POST['entry'])) {
            foreach ($_POST['entry'] as $iSiteId)
            	$this->setStatusSite($iSiteId, 'approved');
        } elseif ($_POST['action_delete'] && is_array($_POST['entry'])) {
            foreach ($_POST['entry'] as $iSiteId)
            	$this->deleteSite($iSiteId);
        }

        $aButtons = array(
            'action_delete' => '_bx_sites_admin_delete'
            );

            if (!$isAdminEntries)
            $aButtons['action_activate'] = '_bx_sites_admin_activate';

            $sForm = $this->_manageSites($isAdminEntries ? 'admin' : 'adminpending', '', $aButtons);
            return $this->_oTemplate->parseHtmlByName('my_sites_manage.html', array('form' => $sForm));
    }

    function _actionAdministrationAdd()
    {
        return $this->_addSiteForm();
    }

    function _actionAdministrationSettings()
    {
        $iId = $this->_oDb->getSettingsCategory();

        if(empty($iId))
            return MsgBox(_t('_sys_request_page_not_found_cpt'));

        bx_import('BxDolAdminSettings');

        $mixedResult = '';
        if(isset($_POST['save']) && isset($_POST['cat'])) {
            $oSettings = new BxDolAdminSettings($iId);
            $mixedResult = $oSettings->saveChanges($_POST);
        }

        $oSettings = new BxDolAdminSettings($iId);
        $sResult = $oSettings->getForm();

        if($mixedResult !== true && !empty($mixedResult))
            $sResult = $mixedResult . $sResult;
        else
            $sResult = $this->_oTemplate->parseHtmlByName('settings_info.html', array()) . $sResult;

        $aVars = array (
            'content' => $sResult
        );

        return $this->_oTemplate->parseHtmlByName('default_padding.html', $aVars);
    }

    function _addSiteForm()
    {
        bx_sites_import('FormAdd');
        $oForm = new BxSitesFormAdd($this);
        $sMsgBox = '';

        if (isset($_POST['url']))
        {
            if (isset($_POST['title']))
            {
                $aParam = array('url' => $_POST['url']);
                if (isset($_POST['thumbnail_url']))
                $this->_addThumbToForm($_POST['thumbnail_url'], $aParam);
                $oForm = new BxSitesFormAdd($this, $aParam);
                $oForm->initChecker();
                if ($oForm->isSubmittedAndValid())
                {
                    $sCategories = implode(';', array_unique(explode(';', $oForm->getCleanValue('categories'))));
                    $sEntryUri = getEntryUri($_POST['title']);
                    unset($oForm->aInputs['categories']);
                    $aValsAdd = array (
                        'date' => time(),
                        'entryUri' => $oForm->generateUri(),
                        'status' => $this -> _oConfig -> _bAutoapprove || $this->isAdmin() ? 'approved' : 'pending',
                        'categories' => $sCategories
                    );

                    if (isset($_FILES['photo']['tmp_name']) && $_FILES['photo']['tmp_name'])
                    $aValsAdd['photo'] = $oForm->uploadPhoto($_FILES['photo']['tmp_name']);
                    else if (isset($_POST['thumbnail_url']))
                    $aValsAdd['photo'] = $oForm->uploadPhoto($_POST['thumbnail_url'], true);

                    $aValsAdd['ownerid'] = $this->iOwnerId;

                    if ($iSiteId = $oForm->insert($aValsAdd))
                    {
                        $this->isAllowedAdd(true);
                        $this->onSiteCreate($iSiteId, $aValsAdd['status']);
                        header('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/my');
                    }
                    else
                        $sMsgBox = MsgBox(_t('_bx_sites_error_occured'));
                }
            }
            else
            {
                // check enter URL if available
                // preg_match("/^(http:\/\/{1})((\w+\.){1,})\w{2,}$/i", $url)
                $oForm->initChecker();
                if ($oForm->isSubmittedAndValid())
                {
                    $sUrl = $_POST['url'];
                    $sUrlFull = strncasecmp($sUrl, 'http://', 7) != 0 ? 'http://' . $sUrl : $sUrl;
                    $aSite = $this->_oDb->getSiteByUrl($sUrl);

                    if (count($aSite) == 0)
                    {
                        $aInfo = getSiteInfo($sUrlFull);

                        if (!empty($aInfo))
                        {
                            $aParam = array(
                                'url' => $sUrl, 
                                'title' => $aInfo['title'],
                                'description' => $aInfo['description']
                            );

                            $sThumbUrl = $this->_queryRemoteThumbnail($sUrlFull, array('Size' => 'lg'));
                            if ($sThumbUrl)
                            $this->_addThumbToForm($sThumbUrl, $aParam);

                            $oForm = new BxSitesFormAdd($this, $aParam);
                        }
                        else
                        {
                            $sMsgBox = MsgBox(_t('_bx_sites_site_link_error'));
                            $oForm->aInputs['url']['value'] = $sUrl;
                        }
                    }
                    else
                        header('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aSite['entryUri']);
                }
            }
        }

        return $sMsgBox . $this->_oTemplate->parseHtmlByName('form.html', array('form' => $oForm->getCode()));
    }

    function _addThumbToForm($sThumbUrl, &$aParam)
    {
        $aParam['thumbnail'] = $this->_oTemplate->parseHtmlByName('thumb200.html',
        array(
                'image' => $sThumbUrl,
                'spacer' => getTemplateIcon('spacer.gif')
        ));
        $aParam['thumbnail_url'] = $sThumbUrl;
    }

    function _make_http_request($url){
        $lines = file($url);
        return implode("", $lines);
    }

    function _queryRemoteThumbnail($url, $args = null) {
        $args = is_array($args) ? $args : array();

        $defaults["Service"] = getParam('bx_sites_thumb_service');
        $defaults["Action"] = getParam('bx_sites_thumb_action');
        $defaults["STWAccessKeyId"] = getParam('bx_sites_thumb_access_key');
        $defaults["stwu"] = getParam('bx_sites_thumb_pswd');

        foreach ($defaults as $k=>$v)
        if (!isset($args[$k]))
        $args[$k] = $v;

        $args["stwUrl"] = $url;
        $request_url = urldecode(getParam('bx_sites_thumb_url') . '?' . $this->_httpParseQuery($args));
        $line = $this->_make_http_request($request_url);
        $regex = '/<[^:]*:Thumbnail\\s*(?:Exists=\"((?:true)|(?:false))\")?[^>]*>([^<]*)<\//';

        if (preg_match($regex, $line, $matches) == 1 && $matches[1] == "true")
        return $matches[2];

        return null;
    }

    function _httpParseQuery($aParam, $sConvention = '%s')
    {
        $sQuery = '';

        if (!function_exists('http_build_query'))
        {
            foreach ($aParam as $sKey => $sValue)
            {
                if (!is_array($sValue))
                {
                    $sKey = urlencode($sKey);
                    $sValue = urlencode($sValue);
                    $sQuery .= sprintf($sConvention, $sKey) . "=$sValue&";
                }
                else
                $sQuery .= http_parse_query($sValue, sprintf($sConvention, $sKey) . '[%s]');
            }
        }
        else
        $sQuery = http_build_query($aParam);

        return $sQuery;
    }

    function _getSitesIndex()
    {
        require_once(BX_DIRECTORY_PATH_MODULES . '/boonex/sites/classes/BxSitesSearchResult.php');
        $this->_oTemplate->addCss(array('main.css'));
        $o = new BxSitesSearchResult('index');

        return $o->displayResultBlock(true, true);
    }
    
    function _getSitesProfile($sNickName)
    {
        require_once(BX_DIRECTORY_PATH_MODULES . '/boonex/sites/classes/BxSitesSearchResult.php');
        $this->_oTemplate->addCss(array('main.css'));
        $o = new BxSitesSearchResult('profile', $sNickName);

        return $o->displayResultBlock(true, true);
    }

    function _manageSites($sMode, $sValue, $aButtons)
    {
        bx_sites_import('SearchResult');
        $oSearchResult = new BxSitesSearchResult($sMode, $sValue);
        $oSearchResult->sUnitTemplate = 'unit_admin';
        $sActionsPanel = '';

        $sFormName = 'manageSitesForm';

        if ($sContent = $oSearchResult->displayResultBlock(true))
        $sActionsPanel = $oSearchResult->showAdminActionsPanel($sFormName, $aButtons);
        else
        $sContent = MsgBox(_t('_Empty'));

        $aVars = array(
            'form_name' => $sFormName,
            'content' => $sContent,
            'actions_panel' => $sActionsPanel
        );

        return $this->_oTemplate->parseHtmlByName('manage.html', $aVars);
    }
}
?>
