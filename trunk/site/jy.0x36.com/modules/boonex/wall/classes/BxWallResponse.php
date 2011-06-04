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

require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolAlerts.php');

class BxWallResponse extends BxDolAlertsResponse {        
    var $_oModule;

	/**
	 * Constructor
	 * @param  BxWallModule $oModule - an instance of current module
	 */
	function BxWallResponse($oModule) {
	    parent::BxDolAlertsResponse();

	    $this->_oModule = $oModule;
	}	
	/**
	 * Overwtire the method of parent class.
	 *
	 * @param BxDolAlerts $oAlert an instance of alert.
	 */
	function response($oAlert) {
	    $bFromWall = !empty($oAlert->aExtras) && (int)$oAlert->aExtras['from_wall'] == 1;
	    
        if($bFromWall) {
            $this->_oModule->_iOwnerId = (int)$oAlert->aExtras['owner_id'];
            $sMedia = strtolower(str_replace('bx_', '', $oAlert->sUnit));
            $aMediaInfo = $this->_oModule->_getCommonMedia($sMedia, $oAlert->iObject);
            
            $iOwnerId = $this->_oModule->_iOwnerId;
            $iObjectId = $this->_oModule->_getAuthorId();
            $sType = $this->_oModule->_oConfig->getCommonPostPrefix() . $sMedia;
            $sAction = '';
            $sContent = $aMediaInfo['content'];
            $sTitle = $aMediaInfo['title'];
            $sDescription = $aMediaInfo['description'];
        }
	    else {
	        $iOwnerId = $oAlert->iSender;
	        $iObjectId = $oAlert->iObject;
	        $sType = $oAlert->sUnit;
	        $sAction = $oAlert->sAction;
	        $sContent = is_array($oAlert->aExtras) && !empty($oAlert->aExtras) ? serialize($oAlert->aExtras) : '';
	        $sTitle = $sDescription = '';
	    }

	    if($oAlert->sUnit == 'profile' && $oAlert->sAction == 'delete') {
	    	$this->_oModule->_oDb->deleteEvent(array('owner_id' => $oAlert->iObject));
	    	$this->_oModule->_oDb->deleteEventCommon(array('object_id' => $oAlert->iObject));
	    	return;
	    }
	    else if($oAlert->sUnit == 'profile' && $oAlert->sAction == 'edit' && $iOwnerId != $iObjectId) {
	    	return;
	    }

	    //profile edit|1|2
        $iId = $this->_oModule->_oDb->insertEvent(array(
            'owner_id' => $iOwnerId,
            'object_id' => $iObjectId,
            'type' => $sType,
            'action' => $sAction,
            'content' => process_db_input($sContent, BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION),
            'title' => process_db_input($sTitle, BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION),
            'description' => process_db_input($sDescription, BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION),
        ));

        if($bFromWall)
            echo "<script>parent." . $this->_oModule->_sJsPostObject . "._getPost(null, " . $iId . ")</script>";

        if(!$bFromWall && $this->_oModule->_oConfig->useFullCompilation()) {
            $aEvents = $this->_oModule->_oDb->getEvents(array('type' => 'id', 'object_id' => $iId));            

            $sContent = $this->_oModule->getSystem($aEvents[0]);
            $this->_oDb->updateEvent(array('content' => $sContent), $aEvents[0]['id']);
        }
	}	
}
?>
