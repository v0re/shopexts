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

bx_import('BxTemplCmtsView');

class BxAdsCmts extends BxTemplCmtsView {
    
	/**
	 * Constructor
	 */
    function BxAdsCmts($sSystem, $iId) {
	    parent::BxTemplCmtsView($sSystem, $iId);
    }

    function getMain() {
        $aPathInfo = pathinfo(__FILE__);
        require_once ($aPathInfo['dirname'] . '/BxAdsSearchUnit.php');
        return BxAdsSearchUnit::getAdsMain();
    }

    function isPostReplyAllowed() {
        if (!parent::isPostReplyAllowed())
            return false;        
        $oMain = $this->getMain();        
        $aAdPost = $oMain->_oDb->getAdInfo($this->getId());
        return $oMain->isAllowedComments($aAdPost);
    }
}


?>
