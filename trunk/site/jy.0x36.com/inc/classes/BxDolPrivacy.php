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

bx_import('BxDolMistake');
bx_import('BxDolPrivacyQuery');

define('BX_DOL_PG_DEFAULT', '1');
define('BX_DOL_PG_NOBODY', '2');
define('BX_DOL_PG_ALL', '3');
define('BX_DOL_PG_MEMBERS', '4');
define('BX_DOL_PG_FRIENDS', '5');
define('BX_DOL_PG_FAVES', '6');
define('BX_DOL_PG_CONTACTS', '7');

/**
 * Privacy settings for any content.
 *
 * Integration of the content with privacy engine allows site member 
 * to organize the access to his content.
 *
 * Related classes:
 *  BxDolPrivacyQuery - database queries.
 *  BxDolPrivacySearch - organize members search using necessary criteria.
 *  BxDolPrivacyView - super class for all representations.
 *  BxBasePrivacyView - base template representation.
 *  BxTemplPrivacyView - custom template representation.
 *
 * Example of usage:
 * 1. Register your privacy actions in `sys_privacy_actions` database table.
 * 2. Add one privacy field(with INT type) in the table with your items for each action. 
 *    For example, for action 'comment', the field name should be 'allow_comment_to'.
 * 3. Add group choosers for necessary actions in the form, which is used to add new items.
 *
 *    $oPrivacy = new BxDolPrivacy();
 *    $oPrivacy->getGroupChooser($iItemOwnerId, $sModuleUri, $sModuleAction);
 *
 * 4. Check privacy when any user tries to view an item.
 *
 *    $oPrivacy = new BxDolPrivacy($sTable, $sFieldId, $sFieldOwnerId);
 *    if($oPrivacy->check($sAction, $iObjectId, $iViewerId)) {
 *     //show necessary content
 *    }
 *
 *    @see an example of integration in the default Dolphin's modules(feedback, events, sites, etc)
 *
 *
 * Memberships/ACL:
 * Doesn't depend on user's membership.
 *
 *
 * Alerts:
 * no alerts available
 *
 */
class BxDolPrivacy extends BxDolMistake {
    var $_oDb;

	/**
	 * constructor
	 */
	function BxDolPrivacy($sTable = '', $sFieldId = '', $sFieldOwnerId = '') {
	    parent::BxDolMistake();

	    $this->_oDb = new BxDolPrivacyQuery($sTable, $sFieldId, $sFieldOwnerId);
	}
	
	/**
	 * Get Select element with available groups. 
	 * 
	 * @param integer $iOwnerId object's owner ID.
	 * @param string $sModuleUri module's unique URI.
	 * @param string $sActionName action name.
	 * @param array $aDynamicGroups an array of array('key' => group_id, 'value' => group_title).
	 * @param string $sTitle the title to be used for generated field.
	 * @return an array with Select element description.
	 */
	function getGroupChooser($iOwnerId, $sModuleUri, $sActionName, $aDynamicGroups = array(), $sTitle = "") {
	    if(empty($sActionName))
            return array();

        $sValue = $this->_oDb->getDefaultValue($iOwnerId, $sModuleUri, $sActionName);
        
        if(empty($sValue))
            $sValue = $this->_oDb->getDefaultValueModule($sModuleUri, $sActionName);

        $aValues = array();
        $aGroups = $this->_oDb->getGroupsBy(array('type' => 'owner', 'owner_id' => $iOwnerId, 'full' => true));	    
	    foreach($aGroups as $aGroup) {
	        if((int)$aGroup['owner_id'] == 0 && $this->_oDb->getParam('sys_ps_enabled_group_' . $aGroup['id']) != 'on')
	           continue;

            $aValues[] = array('key' => $aGroup['id'], 'value' => ((int)$aGroup['owner_id'] == 0 ? $this->_oDb->getParam('sys_ps_group_' . $aGroup['id'] . '_title') : $aGroup['title']));
	    }
        $aValues = array_merge($aValues, $aDynamicGroups);
        
        $sName = $this->getFieldAction($sActionName);
        $sCaption = $this->_oDb->getFieldActionTitle($sModuleUri, $sActionName);
        return array(
            'type' => 'select',
            'name' => $sName,
            'caption' => (!empty($sTitle) ? $sTitle : _t(!empty($sCaption) ? $sCaption : '_' . $sName)),
            'value' => $sValue,
            'values' => $aValues,
            'checker' => array(
                'func' => 'avail',
                'error' => _t('_ps_ferr_incorrect_select')
            ),
            'db' => array(
                'pass' => 'Int'
            )
        );
	}

	/**
	 * Check whether the viewer can make requested action.
	 *
	 * @param string $sAction action name from 'sys_priacy_actions' table.
	 * @param integer $iObjectId object ID the action to be performed with.
	 * @param integer $iViewerId viewer ID.
	 * @return boolean result of operation.
	 */
	function check($sAction, $iObjectId, $iViewerId = 0) {
	    if(empty($iViewerId))
            $iViewerId = getLoggedId();

        if(isLogged() && !isProfileActive($iViewerId))
            return false;

        $aObject = $this->_oDb->getObjectInfo($this->getFieldAction($sAction), $iObjectId);        
        if(empty($aObject) || !is_array($aObject))
            return false;
        
        if($iViewerId == $aObject['owner_id'])
            return true;

        if($this->_oDb->isGroupMember($aObject['group_id'], $aObject['owner_id'], $iViewerId))
            return true;
        
        return $this->isDynamicGroupMember($aObject['group_id'], $aObject['owner_id'], $iViewerId, $iObjectId);
	}

	/**
	 * Get database field name for action.
	 *
	 * @param string $sAction action name.
	 * @return string with field name.
	 */
	function getFieldAction($sAction) {
	    return 'allow_' . strtolower(str_replace(' ', '-', $sAction)) . '_to';
	}
	
	/**
	 * Check whethere viewer is a member of dynamic group.
	 *
	 * @param mixed $mixedGroupId dynamic group ID.
	 * @param integer $iObjectOwnerId object owner ID.
	 * @param integer $iViewerId viewer ID.
	 * @return boolean result of operation.
	 */
	function isDynamicGroupMember($mixedGroupId, $iObjectOwnerId, $iViewerId, $iObjectId) {
	    return false;
	}
}
