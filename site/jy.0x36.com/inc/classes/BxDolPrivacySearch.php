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

bx_import('BxTemplSearchResultText');

class BxDolPrivacySearch extends BxTemplSearchResultText {
    var $_sSearchUnitTmpl;
	var $aCurrent = array(
		'name' => 'ps_search',
		'title' => '_ps_search_object',
		'table' => 'Profiles',
		'ownFields' => array('ID', 'DateReg'),
		'searchFields' => array('NickName', 'City', 'Headline', 'DescriptionMe', 'Tags'),
		'restriction' => array(
			'active' => array('value' => 'Active', 'field' => 'Status', 'operator' => '='),
			'owner' => array('value' => '', 'field' => 'ID', 'operator' => '!='),
			'keyword' => array('value' => '', 'field' => '', 'operator' => 'against')
		),
		'paginate' => array(
			'totalNum' => 0,
			'totalPages' => 0,
			'perPage' => 1000000
		)
	);
	
	function BxDolPrivacySearch($iOwnerId, $sValue) {
	    parent::BxTemplSearchResultText();        
	    
	    global $oSysTemplate;	          

	    $this->aCurrent['restriction']['owner']['value'] = $iOwnerId;
        $this->aCurrent['restriction']['keyword']['value'] = process_db_input($sValue, BX_TAGS_STRIP);
        
        $this->_sSearchUnitTmpl = $oSysTemplate->getHtml('ps_search_unit.html');
	}
	
	function displaySearchUnit($aData) {
	    global $oSysTemplate;
	    
	    return $oSysTemplate->parseHtmlByContent($this->_sSearchUnitTmpl, array(
            'action' => 'add',
            'member_id' => $aData['id'],
            'member_thumbnail' => get_member_thumbnail($aData['id'], 'none', true)
	    ));
	}
	
	function displayResultBlock() {
	    $sResult = parent::displayResultBlock();

        if(empty($sResult))
            $sResult = MsgBox(_t('_Empty'));

        return $sResult;
	}
	function _getPseud () {
	    return array(  
            'id' => 'ID',
            'date' => 'DateReg'
        );
	}
}

?>
