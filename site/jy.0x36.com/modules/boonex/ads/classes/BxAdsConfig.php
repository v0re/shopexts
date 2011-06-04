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

bx_import('BxDolConfig');

class BxAdsConfig extends BxDolConfig {

	var $_iAnimationSpeed;

	var $bUseFriendlyLinks;
	var $bAdminMode;
	var $sCurrBrowsedFile;

	//path to image with Point
	var $sSpacerPath;

	// SQL tables

	var $sSQLPostsTable;
	var $sSQLPostsMediaTable;
	var $sSQLCatTable;
	var $sSQLSubcatTable;

    var $_sCommentSystemName;

	/*
	* Constructor.
	*/
	function BxAdsConfig($aModule) {
		parent::BxDolConfig($aModule);

		$this->_iAnimationSpeed = 'normal';

		$this->sSpacerPath = getTemplateIcon('spacer.gif');

		$this->sSQLPostsTable = 'bx_ads_main';
		$this->sSQLPostsMediaTable = 'bx_ads_main_media';
		$this->sSQLCatTable = 'bx_ads_category';
		$this->sSQLSubcatTable = 'bx_ads_category_subs';

        $this->_sCommentSystemName = "ads";
	}

	function getAnimationSpeed() {
		return $this->_iAnimationSpeed;
	}

	function getCommentSystemName() {
	    return $this->_sCommentSystemName;
	}
}

?>