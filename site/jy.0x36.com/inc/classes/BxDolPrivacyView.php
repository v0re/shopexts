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

bx_import('BxDolTemplate');
bx_import('BxDolPrivacyQuery');
bx_import('BxDolPrivacySearch');

class BxDolPrivacyView extends BxDolTemplate {    
    var $_iOwnerId;
    var $_oDb;
    
	/**
	 * constructor
	 */
	function BxDolPrivacyView($iOwnerId) {
	    parent::BxDolTemplate();

	    $this->_iOwnerId = (int)$iOwnerId;
	    $this->_oDb = new BxDolPrivacyQuery();
	}
    function deleteGroups($aValues) {
        $this->_oDb->deleteGroupsById($aValues);
    }    
    function searchMembers($sValue) {
        $oSearch = new BxDolPrivacySearch($this->_iOwnerId, $sValue);
        return $oSearch->displayResultBlock();
	}
	function addMembers($iGroupId, $aValues) {
        $this->_oDb->addToGroup($iGroupId, $aValues);
	}
	function deleteMembers($iGroupId, $aValues) {
        $this->_oDb->deleteFromGroup($iGroupId, $aValues);
	}	
    function setDefaultGroup($iGroupId) {
        $this->_oDb->setDefaultGroup($this->_iOwnerId, $iGroupId);
        createUserDataFile($this->_iOwnerId);
    }
    function setDefaultValues($aValues) {
        $aActions = $this->_oDb->getActions($this->_iOwnerId);

        foreach($aActions as $aAction) {
            $sName = 'ps-default-values_' . $aAction['action_id'];
            
            if(isset($aValues[$sName]))
                $this->_oDb->replaceDefaulfValue($this->_iOwnerId, $aAction['action_id'], (int)$aValues[$sName]);
        }
    }
    
    function _getSelectItems($aParams) {
	    $aGroups = $this->_oDb->getGroupsBy($aParams);

	    $aValues = array();
	    foreach($aGroups as $aGroup) {
	        if((int)$aGroup['owner_id'] == 0 && $this->_oDb->getParam('sys_ps_enabled_group_' . $aGroup['id']) != 'on')
	           continue;

            $aValues[] = array('key' => $aGroup['id'], 'value' => ((int)$aGroup['owner_id'] == 0 ? $this->_oDb->getParam('sys_ps_group_' . $aGroup['id'] . '_title') : $aGroup['title']));
        }

        return $aValues;
	}
}