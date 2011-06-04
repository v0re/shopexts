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

bx_import ('BxDolPageView');

/**
 * Base entry view class for modules like events/groups/store
 */
class BxDolTwigPageView extends BxDolPageView {

    var $_oTemplate;
    var $_oMain;
    var $_oDb;
    var $_oConfig;
    var $aDataEntry;

    function BxDolTwigPageView($sName, &$oMain, &$aDataEntry) {
        parent::BxDolPageView($sName);
        $this->_oMain = $oMain;
        $this->_oTemplate = $oMain->_oTemplate;
        $this->_oDb = $oMain->_oDb;
        $this->_oConfig = $oMain->_oConfig;
        $this->aDataEntry = &$aDataEntry;
    }

    function _blockInfo ($aData, $sFields = '') {

        $aAuthor = getProfileInfo($aData['author_id']);

        $aVars = array (
            'author_thumb' => get_member_thumbnail($aAuthor['ID'], 'none'),
            'date' => getLocaleDate($aData['created'], BX_DOL_LOCALE_DATE_SHORT),
            'date_ago' => defineTimeInterval($aData['created']),
            'cats' => $this->_oTemplate->parseCategories($aData['categories']),
            'tags' => $this->_oTemplate->parseTags($aData['tags']),
            'fields' => $sFields,
            'author_username' => $aAuthor['NickName'],
            'author_url' => $aAuthor ? getProfileLink($aAuthor['ID']) : 'javascript:void(0)',
        );
        return $this->_oTemplate->parseHtmlByName('entry_view_block_info', $aVars);
    }

    function _blockPhoto (&$aReadyMedia, $iAuthorId, $sPrefix = false) {

        if (!$aReadyMedia)
            return '';

        $aVars = array (
            'image_url' => false,
            'title' => false,
            'prefix' => $sPrefix ? $sPrefix : 'id'.time().'_'.rand(1, 999999), 
            'bx_repeat:images_icons' => array (),
        );

        foreach ($aReadyMedia as $iMediaId) {

            $a = array ('ID' => $iAuthorId, 'Avatar' => $iMediaId);

            $aImageFile = BxDolService::call('photos', 'get_image', array($a, 'file'), 'Search');            
            if ($aImageFile['no_image']) 
                continue;

            $aImageIcon = BxDolService::call('photos', 'get_image', array($a, 'icon'), 'Search');
            if ($aImageIcon['no_image']) 
                continue;

            if (!$aVars['image_url']) {
                $aVars['image_url'] = $aImageFile['file'];
                $aVars['title'] = $aImageFile['title'];
            }

            $aVars['bx_repeat:images_icons'][] = array (
                'icon_url' => $aImageIcon['file'],
                'image_url' => $aImageFile['file'],
                'title' => $aImageIcon['title'],
            );
        }

        if (!$aVars['bx_repeat:images_icons'])
            return '';

        return $this->_oTemplate->parseHtmlByName('entry_view_block_images', $aVars);
    }

    function _blockVideo ($aReadyMedia, $iAuthorId, $sPrefix = false) {

        if (!$aReadyMedia)
            return '';

        $aVars = array (
            'title' => false,
            'prefix' => $sPrefix ? $sPrefix : 'id'.time().'_'.rand(1, 999999), 
            'bx_repeat:videos' => array (),
            'bx_repeat:icons' => array (),
        );

        foreach ($aReadyMedia as $iMediaId) {

            $a = BxDolService::call('videos', 'get_video_array', array($iMediaId), 'Search');
			$a['ID'] = $iMediaId;

            $aVars['bx_repeat:videos'][] = array (
                'style' => false === $aVars['title'] ? '' : 'display:none;',
                'id' => $iMediaId,
                'video' => BxDolService::call('videos', 'get_video_concept', array($a), 'Search'),
            );            
            $aVars['bx_repeat:icons'][] = array (
                'id' => $iMediaId,
                'icon_url' => $a['file'],
                'title' => $a['title'],
            );
            if (false === $aVars['title'])
                $aVars['title'] = $a['title'];
        }

        if (!$aVars['bx_repeat:icons'])
            return '';

        return $this->_oTemplate->parseHtmlByName('entry_view_block_videos', $aVars);
    }    

    function _blockFiles ($aReadyMedia, $iAuthorId = 0) {        

        if (!$aReadyMedia)
            return '';

        $aVars = array (
            'bx_repeat:files' => array (),
        );

        foreach ($aReadyMedia as $iMediaId) {        

            $a = BxDolService::call('files', 'get_file_array', array($iMediaId), 'Search');
            if (!$a['date'])
                continue;

            bx_import('BxTemplFormView');
            $oForm = new BxTemplFormView(array());

            $aInputBtnDownload = array (
                'type' => 'submit',
                'name' => 'download', 
                'value' => _t ('_download'), 
                'attrs' => array(
                    'onclick' => "window.open ('" . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "download/".$this->aDataEntry[$this->_oDb->_sFieldId]."/{$iMediaId}','_self');",
                ),
            );

            $aVars['bx_repeat:files'][] = array (
                'id' => $iMediaId,
                'title' => $a['title'],
                'icon' => $a['file'],                
                'date' => defineTimeInterval($a['date']),
                'btn_download' => $oForm->genInputButton ($aInputBtnDownload),
            );            
        }

        if (!$aVars['bx_repeat:files'])
            return '';

        return $this->_oTemplate->parseHtmlByName('entry_view_block_files', $aVars);
    }

    function _blockSound ($aReadyMedia, $iAuthorId, $sPrefix = false) {

        if (!$aReadyMedia)
            return '';

        $aVars = array (
            'title' => false,
            'prefix' => $sPrefix ? $sPrefix : 'id'.time().'_'.rand(1, 999999), 
            'bx_repeat:sounds' => array (),
            'bx_repeat:icons' => array (),
        );

        foreach ($aReadyMedia as $iMediaId) {

            $a = BxDolService::call('sounds', 'get_music_array', array($iMediaId, 'browse'), 'Search');

            $aVars['bx_repeat:sounds'][] = array (
                'style' => false === $aVars['title'] ? '' : 'display:none;',
                'id' => $iMediaId,
                'sound' => getApplicationContent('mp3', 'player', array('id' => $iMediaId, 'user' => $_COOKIE['memberID'], 'password' => $_COOKIE['memberPassword']), true),
            );            
            $aVars['bx_repeat:icons'][] = array (
                'id' => $iMediaId,
                'icon_url' => $a['file'],
                'title' => $a['title'],
            );
            if (false === $aVars['title'])
                $aVars['title'] = $a['title'];
        }

        if (!$aVars['bx_repeat:icons'])
            return '';

        return $this->_oTemplate->parseHtmlByName('entry_view_block_sounds', $aVars);
    }        

    function _blockFans($iPerPage, $sFuncIsAllowed = 'isAllowedViewFans', $sFuncGetFans = 'getFans') {

        if (!$this->_oMain->$sFuncIsAllowed($this->aDataEntry)) 
            return '';
        
        $iPage = (int)$_GET['page'];
        if( $iPage < 1)
            $iPage = 1;
        $iStart = ($iPage - 1) * $iPerPage;

        $aProfiles = array ();
        $iNum = $this->_oDb->$sFuncGetFans($aProfiles, $this->aDataEntry[$this->_oDb->_sFieldId], true, $iStart, $iPerPage);
        if (!$iNum || !$aProfiles)
            return MsgBox(_t("_Empty"));
        $iPages = ceil($iNum / $iPerPage);

        bx_import('BxTemplSearchProfile');
        $oBxTemplSearchProfile = new BxTemplSearchProfile();
        $sMainContent = '';
        foreach ($aProfiles as $aProfile) {
            $sMainContent .= $oBxTemplSearchProfile->displaySearchUnit($aProfile);
        }
        $ret .= $GLOBALS['oFunctions']->centerContent($sMainContent, '.searchrow_block_simple');

        $aDBBottomMenu = array();
        if ($iPages > 1) {
            $sUrlStart = BX_DOL_URL_ROOT . $this->_oMain->_oConfig->getBaseUri() . "view/".$this->aDataEntry[$this->_oDb->_sFieldUri];
            $sUrlStart .= (false === strpos($sUrlStart, '?') ? '?' : '&');            
            if ($iPage > 1)
                $aDBBottomMenu[_t('_Back')] = array('href' => $sUrlStart . "page=" . ($iPage - 1), 'dynamic' => true, 'class' => 'backMembers', 'icon' => getTemplateIcon('sys_back.png'), 'icon_class' => 'left', 'static' => false);
            if ($iPage < $iPages) {                                
                $aDBBottomMenu[_t('_Next')] = array('href' => $sUrlStart . "page=" . ($iPage + 1), 'dynamic' => true, 'class' => 'moreMembers', 'icon' => getTemplateIcon('sys_next.png'), 'static' => false);
            }
        }
        //$aDBBottomMenu[_t('_View All')] = array('href' => BX_DOL_URL_ROOT . $this->_oMain->_oConfig->getBaseUri() . "fans/".$this->aDataEntry['uri'], 'class' => 'view_all', 'static' => true);

		$ret .= '<div class="clear_both"></div>';

		return array($ret, array(), $aDBBottomMenu);
    }                

    function _blockFansUnconfirmed($iFansLimit = 1000) {

        if (!$this->_oMain->isEntryAdmin($this->aDataEntry)) 
            return '';        

        $aProfiles = array ();
        $iNum = $this->_oDb->getFans($aProfiles, $this->aDataEntry[$this->_oDb->_sFieldId], false, 0, $iFansLimit);
        if (!$iNum)
            return MsgBox(_t('_Empty'));

        $sActionsUrl = BX_DOL_URL_ROOT . $this->_oMain->_oConfig->getBaseUri() . "view/" . $this->aDataEntry[$this->_oDb->_sFieldUri] . '?ajax_action=';
        $aButtons = array (
            array (
                'type' => 'submit',
                'name' => 'fans_reject',
                'value' => _t('_sys_btn_fans_reject'),
                'onclick' => "onclick=\"getHtmlData('sys_manage_items_unconfirmed_fans_content', '{$sActionsUrl}reject&ids=' + sys_manage_items_get_unconfirmed_fans_ids(), false, 'post'); return false;\"",
            ),
            array (
                'type' => 'submit',
                'name' => 'fans_confirm',
                'value' => _t('_sys_btn_fans_confirm'),
                'onclick' => "onclick=\"getHtmlData('sys_manage_items_unconfirmed_fans_content', '{$sActionsUrl}confirm&ids=' + sys_manage_items_get_unconfirmed_fans_ids(), false, 'post'); return false;\"",
            ),
        );
        bx_import ('BxTemplSearchResult');
        $sControl = BxTemplSearchResult::showAdminActionsPanel('sys_manage_items_unconfirmed_fans', $aButtons, 'sys_fan_unit');
        $aVars = array(
            'suffix' => 'unconfirmed_fans',
            'content' => $this->_oMain->_profilesEdit($aProfiles),
            'control' => $sControl,
	    );
        return $this->_oMain->_oTemplate->parseHtmlByName('manage_items_form', $aVars); 
    }    
}
