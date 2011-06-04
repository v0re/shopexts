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

bx_import('BxDolTwigPageView');

class BxStorePageView extends BxDolTwigPageView {	

	function BxStorePageView(&$oMain, &$aDataEntry) {
		parent::BxDolTwigPageView('bx_store_view', $oMain, $aDataEntry);
	}
		
	function getBlockCode_Info() {
        return $this->_blockInfo ($this->aDataEntry, $this->_oTemplate->blockFields($this->aDataEntry));
    }

	function getBlockCode_Desc() {
        return $this->_oTemplate->blockDesc ($this->aDataEntry);
    }

	function getBlockCode_Photo() {
        return $this->_blockPhoto ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'images'), $this->aDataEntry['author_id']);
    }    

    function getBlockCode_Video() {
        return $this->_blockVideo ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'videos'), $this->aDataEntry['author_id']);
    }    

	function getBlockCode_Files() {
        return $this->_oTemplate->blockFiles ($this->aDataEntry);
    }    

    function getBlockCode_Rate() {
        bx_store_import('Voting');
        $o = new BxStoreVoting ('bx_store', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) return '';
        return $o->getBigVoting ($this->_oMain->isAllowedRate($this->aDataEntry));
    }        

    function getBlockCode_Comments() {    
        bx_store_import('Cmts');
        $o = new BxStoreCmts ('bx_store', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) return '';
        return $o->getCommentsFirst ();
    }            

    function getBlockCode_Actions() {
        global $oFunctions;

        if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {

            $oSubscription = new BxDolSubscription();
            $aSubscribeButton = $oSubscription->getButton($this->_oMain->_iProfileId, 'bx_store', '', (int)$this->aDataEntry['id']);

            $aInfo = array (
                'BaseUri' => $this->_oMain->_oConfig->getBaseUri(),
                'iViewer' => $this->_oMain->_iProfileId,
                'ownerID' => (int)$this->aDataEntry['author_id'],
                'ID' => (int)$this->aDataEntry['id'],
                'URI' => (int)$this->aDataEntry['uri'],
                'ScriptSubscribe' => $aSubscribeButton['script'],
                'TitleSubscribe' => $aSubscribeButton['title'],
                'TitleEdit' => $this->_oMain->isAllowedEdit($this->aDataEntry) ? _t('_bx_store_action_title_edit') : '',
                'TitleDelete' => $this->_oMain->isAllowedDelete($this->aDataEntry) ? _t('_bx_store_action_title_delete') : '',
                'TitleShare' => $this->_oMain->isAllowedShareProduct($this->aDataEntry) ? _t('_bx_store_action_title_share') : '',
                'TitleBroadcast' => $this->_oMain->isAllowedBroadcast($this->aDataEntry) ? _t('_bx_store_action_title_broadcast') : '',
                'AddToFeatured' => $this->_oMain->isAllowedMarkAsFeatured($this->aDataEntry) ? ($this->aDataEntry['featured'] ? _t('_bx_store_action_remove_from_featured') : _t('_bx_store_action_add_to_featured')) : '',
            );

            if (!$aInfo['TitleEdit'] && !$aInfo['TitleDelete'] && !$aInfo['TitleShare'] && !$aInfo['AddToFeatured'] && !$aInfo['TitleBroadcast'] && !$aInfo['TitleSubscribe']) 
                return '';

            return $oSubscription->getData() . $oFunctions->genObjectsActions($aInfo, 'bx_store');
        } 

        return '';
    }    

}

?>
