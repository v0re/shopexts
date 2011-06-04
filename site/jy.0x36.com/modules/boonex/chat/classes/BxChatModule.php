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

require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolModule.php');

class BxChatModule extends BxDolModule {    
	/**
	 * Constructor
	 */
	function BxChatModule($aModule) {
	    parent::BxDolModule($aModule);
	    
	    //--- Define Membership Actions ---//
        $aActions = $this->_oDb->getMembershipActions();
        foreach($aActions as $aAction) {
            $sName = 'ACTION_ID_' . strtoupper(str_replace(' ', '_', $aAction['name']));
            if(!defined($sName))
                define($sName, $aAction['id']);
        }
	}
	function getContent($iId) {
		$sPassword = $iId > 0 ? $_COOKIE['memberPassword'] : "";

		$aResult = checkAction($iId, ACTION_ID_USE_CHAT, true);
		if($aResult[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED)
			$sResult = getApplicationContent('chat', 'user', array('id' => $iId, 'password' => $sPassword), true);
		else
			$sResult = MsgBox($aResult[CHECK_ACTION_MESSAGE]);
		
		$sResult = DesignBoxContent(_t('_chat_box_caption'), $sResult, 1);
	    
    	return $sResult;
	}
}
?>