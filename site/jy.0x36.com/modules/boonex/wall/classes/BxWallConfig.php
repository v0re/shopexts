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

class BxWallConfig extends BxDolConfig {
    var $_oDb;
    var $_bAllowDelete;
    var $_bFullCompilation;    
    var $_sDividerDateFormat;
    var $_sCommonPostPrefix;
    var $_sCommentSystemName;
    var $_iPerPage;
    var $_sAnimationEffect;
    var $_iAnimationSpeed;
    var $_iRssLength;

	/**
	 * Constructor
	 */
	function BxWallConfig($aModule) {
	    parent::BxDolConfig($aModule);
	    
	    $this->_bFullCompilation = false;
	    
	    $this->_sAlertSystemName = "bx_wall";
	    $this->_sCommonPostPrefix = 'wall_common_';	    
	    $this->_sCommentSystemName = "bx_wall";
	    $this->_sAnimationEffect = 'fade';
	    $this->_iAnimationSpeed = 'slow';
	    $this->_sDividerDateFormat = getLocaleFormat(BX_DOL_LOCALE_DATE_SHORT, BX_DOL_LOCALE_DB);
	}
	function init(&$oDb) {
	    $this->_oDb = &$oDb;
	    
	    $this->_bAllowDelete = $this->_oDb->getParam('wall_enable_delete') == 'on';
	    $this->_iPerPage = (int)$this->_oDb->getParam('wall_events_per_page');
	    $this->_iRssLength = (int)$this->_oDb->getParam('wall_rss_length');
	}
	function useFullCompilation() {
	    return $this->_bFullCompilation;
	}
	function getDividerDateFormat() {
	    return $this->_sDividerDateFormat;
	}
	function getAlertSystemName() {
	    return $this->_sAlertSystemName;
	}
	function getCommonPostPrefix() {
	    return $this->_sCommonPostPrefix;
	}
	function getCommentSystemName() {
	    return $this->_sCommentSystemName;
	}
	function getPerPage() {
	    return $this->_iPerPage;
	}
	function getAnimationEffect() {
	    return $this->_sAnimationEffect;
	}
	function getAnimationSpeed() {
	    return $this->_iAnimationSpeed;
	}
	function getRssLength() {
	    return $this->_iRssLength;
	}
}
?>