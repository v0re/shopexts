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

bx_import('BxDolRate');
require_once('BxSoundsSearch.php');

class BxSoundsRate extends BxDolRate {
    var $oMedia;
    function BxSoundsRate () {
        $this->sType = 'bx_sounds';
        $this->oMedia = new BxSoundsSearch();
        $this->aPageInfo = array(
            'header' => '_bx_sounds_rate_header',
            'header_text' => '_bx_sounds_rate_header_text',
        );
        parent::BxDolRate($this->sType);
    }
    
    function getRateObject () {
        $aVotedItems = $this->getVotedItems();
        $this->oMedia->clearFilters(array('activeStatus', 'allow_view', 'album_status', 'albumType'), array('albumsObjects', 'albums'));
        $this->oMedia->aCurrent['restriction']['id'] = array(
            'value' => $aVotedItems,
            'field' => 'ID',
            'operator' => 'not in'
        );
        $this->oMedia->aCurrent['paginate']['perPage'] = 1;
        $this->oMedia->aCurrent['sorting'] = 'rand';
        $aData = $this->oMedia->getSearchData();
        return $aData;
    } 
    
    function getBlockCode_RatedSet () {
        $this->oMedia->clearFilters(array('activeStatus', 'allow_view', 'album_status', 'albumType'), array('albumsObjects', 'albums'));
        $this->oMedia->aCurrent['join']['rateTrack'] = array(
            'type' => 'inner',
            'table' => 'bx_sounds_voting_track',
            'mainField' => 'ID',
            'onField' => 'gal_id',
            'joinFields' => array('gal_ip', 'gal_date')
        );
        
        $this->oMedia->aCurrent['paginate']['perPage'] = getParam($this->oMedia->aGlParamsSettings['previousRatedNumber']);
        $this->oMedia->aCurrent['sorting'] = 'voteTime';
        $sIp = getVisitorIP();
        $this->oMedia->aCurrent['restriction']['ip'] = array(
            'value' => $sIp,
            'field' => 'gal_ip',
            'table' => 'bx_sounds_voting_track',
            'operator' => '='
        );
        $sCode = $this->oMedia->displayResultBlock(); 
        if (strlen($sCode) > 0)
            $sCode = $GLOBALS['oFunctions']->centerContent($sCode, '.sys_file_search_unit');
        else
            $sCode = MsgBox(_t("_Empty"));
        return $sCode;
    }
    
    function getBlockCode_RateObject () {
        $this->oMedia->oModule->_defineActions();
	    $aCheck = checkAction($this->iViewer, $this->oMedia->oModule->_defineActionName('view'));
		if ($aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED)
			$sCode = MsgBox(_t('_' . $this->sType . '_forbidden'));
		else {
			$aData = $this->getRateObject();
	        if (count($aData) > 0) {
	            $oFile = &$this->oMedia->oModule;
	            $oFile->_oTemplate->addCss('rate_object.css');
	            $iInfoWidth = (int)getParam($this->sType . '_file_width');
	            $oVotingView = new BxTemplVotingView ($this->sType, $aData[0]['id']);
	            $aUnit = array(
	                'url' => BX_DOL_URL_ROOT . $oFile->_oConfig->getBaseUri() . 'rate',
	                'fileBody' => $this->oMedia->oTemplate->getFileConcept($aData[0]['id']),
	                'ratePart' => $oVotingView->isEnabled() ? $oVotingView->getBigVoting(): '',
	                'fileTitle' => $aData[0]['title'],
	                'fileUri' => $this->oMedia->getCurrentUrl('file', $aData[0]['id'], $aData[0]['uri']),
	                'fileWhen' => defineTimeInterval($aData[0]['date']),
	                'fileFrom'  => $aData[0]['ownerName'],
	                'fileFromLink' => getProfileLink($aData[0]['ownerId']),
	                'infoWidth' => $iInfoWidth > 0 ? $iInfoWidth + 2: '',
	            );
	            $sCode = $this->oMedia->oTemplate->parseHtmlByName('rate_object.html', $aUnit);
				checkAction($this->iViewer, $this->oMedia->oModule->_defineActionName('view'), true);
	        }
	        else
	            $sCode = MsgBox(_t('_bx_sounds_no_file_for_rate'));
		}
        return $sCode;
    }
}

?>