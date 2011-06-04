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

bx_import('BxDolFilesTemplate');

class BxVideosTemplate extends BxDolFilesTemplate {
    function BxVideosTemplate (&$oConfig, &$oDb) {
        parent::BxDolFilesTemplate($oConfig, $oDb);
    }
    
    function getFileConcept ($iFileId, $aExtra = array()) {
        $iFileId = (int)$iFileId;
        if(empty($aExtra['ext']))
            $sPlayer = getApplicationContent('video','player',array('id' => $iFileId, 'user' => $this->iViewer, 'password' => clear_xss($_COOKIE['memberPassword'])),true);
        else {
            $sPlayer = str_replace("#video#", $aExtra['ext'], YOUTUBE_VIDEO_PLAYER);
            $sPlayer = str_replace("#wmode#", getWMode(), $sPlayer);
            $sPlayer = str_replace("#autoplay#", (getSettingValue("video", "autoPlay") == TRUE_VAL ? "1" : "0"), $sPlayer);
        }
        $iWidth = (int)$this->_oConfig->getGlParam('file_width');
        return '<div class="viewFile" style="width: ' . ($iWidth + 2) . 'px;">' . $sPlayer . '</div>';
    }
    
    function getViewFile (&$aInfo) {
        $oVotingView = new BxTemplVotingView('bx_' . $this->_oConfig->getUri(), $aInfo['medID']);
        $iWidth = (int)$this->_oConfig->getGlParam('file_width');
        if ($aInfo['prevItem'] > 0)
            $aPrev = $this->_oDb->getFileInfo(array('fileId'=>$aInfo['prevItem']), true, array('medUri', 'medTitle'));
        if ($aInfo['nextItem'] > 0)
            $aNext = $this->_oDb->getFileInfo(array('fileId'=>$aInfo['nextItem']), true, array('medUri', 'medTitle'));
        
        $aUnit = array(
            'file' => $this->getFileConcept($aInfo['medID'], array('ext'=>$aInfo['medExt'], 'source'=>$aInfo['medSource'])),
            'width_ext' => $iWidth + 2,
            'width' => $iWidth,
            'fileUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aInfo['medUri'],
            'fileTitle' => $aInfo['medTitle'],
            'rate' => $oVotingView->isEnabled() ? $oVotingView->getBigVoting(1, $aInfo['Rate']): '',
            'favInfo' => isset($aInfo['favCount']) ? $aInfo['favCount'] : '',
            'viewInfo' => $aInfo['medViews'],
            'albumUri' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/album/' . $aInfo['albumUri'] . '/owner/' . $aInfo['NickName'],
            'albumCaption' => $aInfo['albumCaption'],
            'bx_if:prev' => array(
                'condition' => $aInfo['prevItem'] > 0,
                'content' => array(
                    'linkPrev'  => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aPrev['medUri'],
                    'titlePrev' => $aPrev['medTitle'],
                    'percent' => $aInfo['nextItem'] > 0 ? 50 : 100,
                )
            ),
            'bx_if:next' => array(
                'condition' => $aInfo['nextItem'] > 0,
                'content' => array(
                    'linkNext'  => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aNext['medUri'],
                    'titleNext' => $aNext['medTitle'],
                    'percent' => $aInfo['prevItem'] > 0 ? 50 : 100,
                )
            ),
        );
        return $this->parseHtmlByName('view_unit.html', $aUnit);
    }
    
    function getEmbedCode ($iFileId, $aExtra = array()) {
        $iFileId = (int)$iFileId;
		switch ($aExtra["source"]) {
			case "":
				$sEmbedCode = getEmbedCode('video', 'player', array('id'=>$iFileId));
				break;
			case "youtube":
				$sEmbedCode = str_replace("#video#", $sVideo, YOUTUBE_VIDEO_EMBED);
				$sEmbedCode = str_replace("#wmode#", getWMode(), $sEmbedCode);
				$sEmbedCode = str_replace("#autoplay#", (getSettingValue("video", "autoPlay") == TRUE_VAL ? "1" : "0"), $sEmbedCode);
				break;
			default:
				$sEmbedCode = video_getCustomEmbedCode($aExtra["source"], $aExtra["video"]);
				break;
		}
        return $sEmbedCode;
    }
    
    function getCompleteFileInfoForm (&$aInfo, $sUrlPref = '') {
        $aMain = $this->getBasicFileInfoForm($aInfo, $sUrlPref);
        $aAdd = array('embed' => array(
                'type' => 'text',
                'value' => $this->getEmbedCode($aInfo['medID'], array('video'=>$aInfo['medExt'], 'source'=>$aInfo['medSource'])),
                'attrs' => array(
                  'onclick' => 'this.focus(); this.select();',
                  'readonly' => 'readonly',
                ),
                'caption'=> _t('_Embed')
            ),
        );
        return array_merge($aMain, $aAdd);
    }
    
    function getItemType () {
    	return 'type="video/x-flv"';
    }
    
    function getAlbumPreview ($sAlbumLink) {
    	
    }    
}

?>