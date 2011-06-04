<?php
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

bx_import('BxDolPageView');

class BxAvaPageMain extends BxDolPageView {

    var $_oMain;
    var $_oTemplate;
    var $_oConfig;
    var $_oDb;

    function BxAvaPageMain(&$oMain) {
        $this->_oMain = &$oMain;
        $this->_oTemplate = $oMain->_oTemplate;
        $this->_oConfig = $oMain->_oConfig;
        $this->_oDb = $oMain->_oDb;
		parent::BxDolPageView('bx_avatar_main');
	}

    function getBlockCode_Tight() {
        $aMyAvatars = array ();
        $aVars = array (
            'my_avatars' => $this->_oMain->serviceGetMyAvatars ($this->_oMain->_iProfileId),
            'bx_if:is_site_avatars_enabled' => array (
                'condition' => 'on' == getParam('bx_avatar_site_avatars'),
                'content' => array (
                    'site_avatars' => getParam('bx_avatar_site_avatars') ? $this->_oMain->serviceGetSiteAvatars (0) : _t('_Empty'),
                ),
            ),
        );
        return $this->_oTemplate->parseHtmlByName('block_tight', $aVars);
    }

    function getBlockCode_Wide() {

        $sUploadErr = '';        

        if (isset($_FILES['image'])) {
            $sUploadErr = $this->_oMain->_uploadImage () ? '' : _t('_bx_ava_upload_error');
            if (!$sUploadErr)
                send_headers_page_changed();
        }

        $aVars = array (
            'action' => $this->_oConfig->getBaseUri(),
            'avatar' => $GLOBALS['oFunctions']->getMemberThumbnail ($this->_oMain->_iProfileId),
            'upload_error' => $sUploadErr,            
            'crop_tool' => $this->_oMain->serviceCropTool (array (
                    'dir_image' => BX_AVA_DIR_TMP . $this->_oMain->_iProfileId . BX_AVA_EXT,
                    'url_image' => BX_AVA_URL_TMP . $this->_oMain->_iProfileId . BX_AVA_EXT . '?' . time(),
                )),
            'bx_if:display_premoderation_notice' => array (
                'condition' => getParam('autoApproval_ifProfile') != 'on',
                'content' => array (),
            ),
        );
        return $this->_oTemplate->parseHtmlByName('block_wide', $aVars);
    }    
}

?>
