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

class BxBlogsCmts extends BxTemplCmtsView {
    
	/**
	 * Constructor
	 */
    function BxBlogsCmts($sSystem, $iId) {
	    parent::BxTemplCmtsView($sSystem, $iId);
    }

    function getMain() {
        $aPathInfo = pathinfo(__FILE__);
        require_once ($aPathInfo['dirname'] . '/BxBlogsSearchUnit.php');
        return BxBlogsSearchUnit::getBlogsMain();
    }

    function isPostReplyAllowed() {
        if (!parent::isPostReplyAllowed())
            return false;        
        $oMain = $this->getMain();        
        $aBlogPost = $oMain->_oDb->getPostInfo($this->getId(), 0, true);
        return $oMain->isAllowedComments($aBlogPost);
    }

    function isEditAllowedAll() {       
        $oMain = $this->getMain();
        $aBlogPost = $oMain->_oDb->getPostInfo($this->getId(), 0, true);
        if ($oMain->isAllowedCreatorCommentsDeleteAndEdit($aBlogPost))
            return true;
        return parent::isEditAllowedAll();
    }

    function isRemoveAllowedAll() {
        $oMain = $this->getMain();
        $aBlogPost = $oMain->_oDb->getPostInfo($this->getId(), 0, true);
        if ($oMain->isAllowedCreatorCommentsDeleteAndEdit($aBlogPost))
            return true;
        return parent::isRemoveAllowedAll();
    }
}


?>
