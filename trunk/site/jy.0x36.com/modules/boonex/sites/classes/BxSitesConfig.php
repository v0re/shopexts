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

require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolConfig.php');

class BxSitesConfig extends BxDolConfig {
    var $_oDb;
    var $_bAutoapprove;
    var $_bComments;
    var $_sCommentsSystemName;
    var $_bVotes;
    var $_sVotesSystemName;
    var $_iPerPage;
    
    /**
     * Constructor
     */
    function BxSitesConfig($aModule) {
        parent::BxDolConfig($aModule);
    }
    
    function init(&$oDb) 
    {
        $this->_oDb = &$oDb;
        
        $this->_bAutoapprove = $this->_oDb->getParam('bx_sites_autoapproval') == 'on';
        $this->_bComments = $this->_oDb->getParam('bx_sites_comments') == 'on';
        $this->_sCommentsSystemName = "bx_sites";
        $this->_bVotes = $this->_oDb->getParam('bx_sites_votes') == 'on';
        $this->_sVotesSystemName = "bx_sites";
        $this->_iPerPage = (int)$this->_oDb->getParam('bx_sites_per_page');
    }
    
    function isAutoapprove() 
    {
        return $this->_bAutoapprove;
    }
    
    function isCommentsAllowed() 
    {
        return $this->_bComments;
    }
    
    function getCommentsSystemName() 
    {
        return $this->_sCommentsSystemName;
    }
    
    function isVotesAllowed() 
    {
        return $this->_bVotes;
    }
    
    function getVotesSystemName() 
    {
        return $this->_sVotesSystemName;
    }
    
    function getPerPage() 
    {
        return $this->_iPerPage;
    }
}
?>