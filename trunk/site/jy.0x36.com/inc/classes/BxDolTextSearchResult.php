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

bx_import('BxDolModule');
bx_import('BxTemplSearchResultText');

class BxDolTextSearchResult extends BxTemplSearchResultText {
	var $aCurrent = array(
		'name' => '',
		'title' => '',
		'table' => '',
		'ownFields' => array('uri'),
		'searchFields' => array('caption', 'content', 'tags', 'categories'),
		'restriction' => array(
			'active1' => array('value' => '1', 'field' => 'status', 'operator' => '<>'),
			'active2' => array('value' => '2', 'field' => 'status', 'operator' => '<>'),
			'caption' => array('value' => '', 'field' => 'caption', 'operator' => 'like'),
			'content' => array('value' => '', 'field' => 'content', 'operator' => 'like'),
			'tag' => array('value' => '', 'field' => 'tags', 'operator' => 'against'),
			'category' => array('value' => '', 'field' => 'categories', 'operator' => 'against')
		),
		'paginate' => array('perPage' => 4, 'page' => 1, 'totalNum' => 10, 'totalPages' => 1),
		'sorting' => 'last'
	);

	var $_oModule;

	function BxDolTextSearchResult(&$oModule) {
		parent::BxTemplSearchResultText();

		$this->_oModule = $oModule;

		$sModuleUri = $this->_oModule->_oConfig->getUri();
		$this->aCurrent['name'] = 'bx_' . $sModuleUri;
		$this->aCurrent['title'] = '_' . $sModuleUri . '_lcaption_search_object';
        $this->aCurrent['table'] = $this->_oModule->_oDb->getPrefix() . 'entries';
	}

	function displaySearchUnit($aData) {
	    return $this->_oModule->_oTemplate->displayList(array(
            'sample_type' => 'search_unit',
            'viewer_type' => 0,
            'uri' => $aData['uri']
	    ));
	}

	function displayResultBlock() {
	    $sResult = parent::displayResultBlock();

	    $sModuleUri = $this->_oModule->_oConfig->getUri();
        if($this->aCurrent['paginate']['totalNum'] == 0)
            $sResult = MsgBox(_t('_' . $sModuleUri . '_msg_no_results'));

        return $sResult;
	}

	function addCustomParts() {
	    parent::addCustomParts();

	    $this->_oModule->_oTemplate->addCss(array('view.css'));
	}

	function getAlterOrder() {
		return array('order' => 'ORDER BY `when` DESC');
	}
}
?>