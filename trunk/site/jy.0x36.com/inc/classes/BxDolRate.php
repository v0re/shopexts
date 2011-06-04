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

require_once(BX_DIRECTORY_PATH_INC . 'db.inc.php');
require_once(BX_DIRECTORY_PATH_INC . 'design.inc.php');
require_once(BX_DIRECTORY_PATH_INC . 'utils.inc.php');
require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolPageView.php');

class BxDolRate extends BxDolPageView { 
    var $sType;
	var $iViewer;
    // array of headers for rate page
    var $aPageCaption = array();
    function BxDolRate ($sType) {
        parent::BxDolPageView($sType . '_rate');
		$this->iViewer = getLoggedId();
    }
    
    function getVotedItems () {
        $ip = getVisitorIP();
        $oDolVoting = new BxDolVoting($this->sType, 0, 0);
        $aVotedItems = $oDolVoting->getVotedItems ($ip);
        return $this->reviewArray($aVotedItems, $oDolVoting->_aSystem['row_prefix'].'id');
    }

    function reviewArray ($aFiles, $sKey = '') {
        $aList = array();
        if (is_array($aFiles)) {
            foreach ($aFiles as $iKey => $aValue) {
                $aList[$iKey] = $aValue[$sKey];
            }
        }
        return $aList;
    }
    
    //get array or previous rated objects 
    function getRatedSet () {

    }

    //get array or previous rated objects
    function getRateObject () {
    
    } 
}

?>