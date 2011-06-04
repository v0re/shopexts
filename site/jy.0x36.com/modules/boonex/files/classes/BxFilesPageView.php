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

bx_import('BxDolPageView');
bx_import('BxDolViews');
require_once('BxFilesSearch.php');

class BxFilesPageView extends BxDolPageView {
    var $iProfileId;
    var $aFileInfo;
    
    var $oTemplate;
    var $oConfig;
    var $oDb;
    var $oSearch;
    
    function BxFilesPageView (&$oShared, &$aFileInfo) {
        parent::BxDolPageView('bx_files_view');
        $this->aFileInfo = $aFileInfo;
        $this->iProfileId = &$oShared->_iProfileId;
        
        $this->oTemplate = $oShared->_oTemplate;
        $this->oConfig = $oShared->_oConfig;
        $this->oDb = $oShared->_oDb;
        $this->oSearch = new BxFilesSearch();
        $this->oTemplate->addCss('view.css');
        new BxDolViews('bx_files', $this->aFileInfo['medID']);
    }
    
    function getBlockCode_ActionList () {
        $sCode = null;
    	bx_import('BxDolSubscription');
        $oSubscription = new BxDolSubscription();
        $aButton = $oSubscription->getButton($this->iProfileId, $this->oConfig->getMainPrefix(), '', (int)$this->aFileInfo['medID']);        
        $aReplacement = array(
            'moduleUrl' => BX_DOL_URL_ROOT . $this->oConfig->getBaseUri(),
            'fileUri' => $this->aFileInfo['medUri'],
            'iViewer' => $this->iProfileId,
            'favorited' => $this->aFileInfo['favorited'] == false ? '' : 'favorited',
            'ID' => (int)$this->aFileInfo['medID'],
            'Owner' => (int)$this->aFileInfo['medProfId'],
            'OwnerName' => $this->aFileInfo['NickName'],
            'AlbumUri' => $this->aFileInfo['albumUri'],
			'sbs_' . $this->oConfig->getMainPrefix() . '_title' => $aButton['title'], 
            'sbs_' . $this->oConfig->getMainPrefix() . '_script' => $aButton['script']
        );
        $sActionsList = $GLOBALS['oFunctions']->genObjectsActions($aReplacement, $this->oConfig->getMainPrefix());
        if (!is_null($sActionsList))
        	$sCode = $oSubscription->getData() . $sActionsList;
        return $sCode;
    }
    
    function getBlockCode_FileInfo () {
        return $this->oTemplate->getFileInfo($this->aFileInfo);
    }
    
    function getBlockCode_LastAlbums () {
        $sPref        = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri();
        $sSimpleUrl   = $sPref . 'albums/browse/owner/' . $this->aFileInfo['NickName']; 
        $sPaginateUrl = $sPref . 'view/' . $this->aFileInfo['medUri'];
        return $this->oSearch->getAlbumsBlock(array('owner' => $this->aFileInfo['medProfId']), array(), array('paginate_url' => $sPaginateUrl, 'simple_paginate_url' => $sSimpleUrl));
    }
    
    function getBlockCode_RelatedFiles () {
        $this->oSearch->clearFilters(array('activeStatus', 'albumType', 'allow_view', 'album_status'), array('albumsObjects', 'albums'));
        $bLike = getParam('useLikeOperator');
        if ($bLike != 'on') {
            $aRel = array($this->aFileInfo['medTitle'], $this->aFileInfo['medDesc'], $this->aFileInfo['medTags'], $this->aFileInfo['Categories']); 
        	$sKeywords = getRelatedWords($aRel);
            if (strlen($sKeywords) > 0) {
                $this->oSearch->aCurrent['restriction']['keyword'] = array(
                    'value' => $sKeywords,
                    'field' => '',
                    'operator' => 'against'
                );
            }
        }
        else {
            $sKeywords = $this->aFileInfo['medTitle'].' '.$this->aFileInfo['medTags'];
            $aWords = explode(' ', $sKeywords);
            foreach (array_unique($aWords) as $iKey => $sValue) {
                if (strlen($sValue) > 2) {
                    $this->oSearch->aCurrent['restriction']['keyword'.$iKey] = array(
                        'value' => trim(addslashes($sValue)),
                        'field' => '',
                        'operator' => 'against'
                    );
                }
            }
        }
        $this->oSearch->aCurrent['restriction']['id'] = array(
            'value' => $this->aFileInfo['medID'],
            'field' => $this->oSearch->aCurrent['ident'],
            'operator' => '<>',
            'paramName' => 'fileID'
        ); 
        $this->oSearch->aCurrent['sorting'] = 'score';
        $iLimit = (int)$this->oConfig->getGlParam('number_related');
        $iLimit = $iLimit == 0 ? 2 : $iLimit;
        
        $this->oSearch->aCurrent['paginate']['perPage'] = $iLimit;
        $this->oSearch->aCurrent['view'] = 'short';
        $sCode = $this->oSearch->displayResultBlock();
        $aBottomMenu = array();
        if (strlen($sCode) > 0)
            $aBottomMenu = $this->oSearch->getBottomMenu('category', 0, $this->aFileInfo['Categories']);
        return array($sCode, array(), $aBottomMenu, ''); 
    }
    
    function getBlockCode_ViewComments () {
        bx_import('BxTemplCmtsView');
        $this->oTemplate->addCss('cmts.css');
        $oCmtsView = new BxTemplCmtsView('bx_files', $this->aFileInfo['medID']);
        if (!$oCmtsView->isEnabled()) return '';
            return $oCmtsView->getCommentsFirst ();
    }
    
    function getBlockCode_ViewFile () {
        $oVotingView = new BxTemplVotingView('bx_files', $this->aFileInfo['medID']);
        if ($this->aFileInfo['prevItem'] > 0)
            $aPrev = $this->oDb->getFileInfo(array('fileId'=>$this->aFileInfo['prevItem']), true, array('medUri', 'medTitle'));
        if ($this->aFileInfo['nextItem'] > 0)
            $aNext = $this->oDb->getFileInfo(array('fileId'=>$this->aFileInfo['nextItem']), true, array('medUri', 'medTitle'));
		//icon
		$sIcon = $this->oDb->getTypeIcon($this->aFileInfo['Type']);
		if (!$sIcon)
			$sIcon = 'default.png';
        $aUnit = array(
            'pic' => $this->oTemplate->getIconUrl($sIcon),
            'fileTitle' => $this->aFileInfo['medTitle'],
            'fileSize' => (int)$this->aFileInfo['medSize'] > 0 ? (int)($this->aFileInfo['medSize']/1024) : 0,
            'fileExt' => $this->aFileInfo['medExt'],
            'rate' => $oVotingView->isEnabled() ? $oVotingView->getBigVoting(1, $this->aFileInfo['Rate']): '',
            'favIcon' => $this->oTemplate->getIconUrl('action_fave.png'),
            'favInfo' => $this->oDb->getFavoritesCount($this->aFileInfo['medID']),
            'viewIcon' => $this->oTemplate->getIconUrl('eye.png'),
            'viewInfo' => $this->aFileInfo['medViews'],
            'albumUri' => BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'browse/album/' . $this->aFileInfo['albumUri'] . '/owner/' . $this->aFileInfo['NickName'],
            'albumCaption' => $this->aFileInfo['albumCaption'],
            'bx_if:prev' => array(
                'condition' => $this->aFileInfo['prevItem'] > 0,
                'content' => array(
                    'linkPrev'  => BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'view/' . $aPrev['medUri'],
                    'titlePrev' => $aPrev['medTitle'],
                    'percent' => $this->aFileInfo['nextItem'] > 0 ? 50 : 100,
                )
            ),
            'bx_if:next' => array(
                'condition' => $this->aFileInfo['nextItem'] > 0,
                'content' => array(
                    'linkNext'  => BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'view/' . $aNext['medUri'],
                    'titleNext' => $aNext['medTitle'],
                    'percent' => $this->aFileInfo['prevItem'] > 0 ? 50 : 100,
                )
            ),
        );
        return $sCode = $this->oTemplate->parseHtmlByName('view_unit.html', $aUnit);
    }
    
    function getBlockCode_MainFileInfo () {
        return $this->oTemplate->getFileInfoMain($this->aFileInfo);
    }
}

?>