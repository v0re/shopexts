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

bx_import('BxDolModuleTemplate');

class BxWallTemplate extends BxDolModuleTemplate {
	/**
	 * Constructor
	 */
	function BxWallTemplate(&$oConfig, &$oDb) {
	    parent::BxDolModuleTemplate($oConfig, $oDb);

	    $this->_aTemplates = array('divider', 'balloon', 'comments', 'common_media');
	}

	function loadTemplates() {
	    parent::loadTemplates();
	    
	    $this->_aTemplates['common_link'] = '<div class="wall-post-link"><div class="wall-lnk-title">__title__</div><div class="wall-lnk-url"><a href="__url__" target="_blank">__url__</a></div><div class="wall-lnk-description">__description__</div></div>';
	}

	function displayProfileEdit($aEvent) {
	    $aOwner = $this->_oDb->getUser($aEvent['owner_id']);
	    if(empty($aOwner))
	       return "";

        if($aOwner['couple'] == 0 && $aOwner['sex'] == 'male')
            $sTxtEditedProfile = _t('_wall_edited_his_profile');
        else if($aOwner['couple'] == 0 && $aOwner['sex'] == 'female')
            $sTxtEditedProfile = _t('_wall_edited_her_profile');
        else if($aOwner['couple'] > 0)
            $sTxtEditedProfile = _t('_wall_edited_their_profile');

        return array(
            'title' => $aOwner['username'] . ' ' . $sTxtEditedProfile,
            'description' => '',
            'content' => $this->parseHtmlByName('p_edit.html', array(
                'cpt_user_name' => $aOwner['username'],
                'cpt_edited_profile' => $sTxtEditedProfile,        
                'cpt_info_url' => getProfileLink($aOwner['id']),
                'post_id' => $aEvent['id']
            ))
        );	    
	}
	function displayProfileEditStatusMessage($aEvent) {
	    $aOwner = $this->_oDb->getUser($aEvent['owner_id']);
	    if(empty($aOwner))
	       return "";

        if($aOwner['couple'] == 0 && $aOwner['sex'] == 'male')
            $sTxtEditedProfile = _t('_wall_edited_his_profile_status_message');
        else if($aOwner['couple'] == 0 && $aOwner['sex'] == 'female')
            $sTxtEditedProfile = _t('_wall_edited_her_profile_status_message');
        else if($aOwner['couple'] > 0)
            $sTxtEditedProfile = _t('_wall_edited_their_profile_status_message');
            
        $aParams = array();
        if(!empty($aEvent['content']))
            $aParams = unserialize($aEvent['content']);

        return array(
            'title' => $aOwner['username'] . ' ' . $sTxtEditedProfile,
            'description' => (isset($aParams[0]) ? $aParams[0] : ''),
            'content' => $this->parseHtmlByName('p_edit_status_message.html', array(
                'cpt_user_name' => $aOwner['username'],
                'cpt_edited_profile_status_message' => $sTxtEditedProfile,        
                'cnt_status_message' => (isset($aParams[0]) ? $aParams[0] : ''),
                'post_id' => $aEvent['id']
            ))
        );
	}
	
	function displayFriendAccept($aEvent) {
	    $aOwner = $this->_oDb->getUser($aEvent['owner_id']);
	    $aFriend = $this->_oDb->getUser($aEvent['object_id']);
	    if(empty($aOwner) || empty($aFriend))
            return "";

        return array(
            'title' => $aOwner['username'] . ' ' . _t('_wall_friends_with') . ' ' . $aFriend['username'],
            'description' => '',
            'content' => $this->parseHtmlByName('f_accept.html', array(
                'cpt_user_name' => $aOwner['username'],                
                'cpt_friend_url' => getProfileLink($aFriend['id']),
                'cpt_friend_name' => $aFriend['username'],
                'post_id' => $aEvent['id']
            ))
        );
	}
}
?>