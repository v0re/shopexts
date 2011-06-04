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

require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolModuleDb.php' );

class BxWallDb extends BxDolModuleDb {
    var $_oConfig;
	/*
	 * Constructor.
	 */
	function BxWallDb(&$oConfig) {
		parent::BxDolModuleDb();

		$this->_oConfig = $oConfig;
		$this->_sPrefix = $oConfig->getDbPrefix();
	}
	function insertData($aData) {
	    //--- Update Wall Handlers ---//
	    foreach($aData['handlers'] as $aHandler)	       
            $this->query("INSERT INTO `" . $this->_sPrefix . "handlers`(`alert_unit`, `alert_action`, `module_uri`, `module_class`, `module_method`) VALUES('" . $aHandler['alert_unit'] . "', '" . $aHandler['alert_action'] . "', '" . $aHandler['module_uri'] . "', '" . $aHandler['module_class'] . "', '" . $aHandler['module_method'] . "')");
	       
        //--- Update System Alerts ---//
        $iHandlerId = (int)$this->getOne("SELECT `id` FROM `sys_alerts_handlers` WHERE `name`='" . $this->_oConfig->getAlertSystemName() . "' LIMIT 1");
	    
	    foreach($aData['alerts'] as $aAlert)
	       $this->query("INSERT INTO `sys_alerts`(`unit`, `action`, `handler_id`) VALUES('" . $aAlert['unit'] . "', '" . $aAlert['action'] . "', '" . $iHandlerId . "')");
	}
	function deleteData($aData) {
	    //--- Update Wall Handlers ---//
	    foreach($aData['handlers'] as $aHandler)
	       $this->query("DELETE FROM `" . $this->_sPrefix . "handlers` WHERE `alert_unit`='" . $aHandler['alert_unit'] . "' AND `alert_action`='" . $aHandler['alert_action'] . "' AND `module_uri`='" . $aHandler['module_uri'] . "' AND `module_class`='" . $aHandler['module_class'] . "' AND `module_method`='" . $aHandler['module_method'] . "' LIMIT 1");
	       
        //--- Update System Alerts ---//
        $iHandlerId = (int)$this->getOne("SELECT `id` FROM `sys_alerts_handlers` WHERE `name`='" . $this->_oConfig->getAlertSystemName() . "' LIMIT 1");
	    
	    foreach($aData['alerts'] as $aAlert)
	       $this->query("DELETE FROM `sys_alerts` WHERE `unit`='" . $aAlert['unit'] . "' AND `action`='" . $aAlert['action'] . "' AND `handler_id`='" . $iHandlerId . "' LIMIT 1");
	}
	function insertEvent($aParams) {
        if((int)$this->query("INSERT INTO `" . $this->_sPrefix . "events`(`" . implode("`, `", array_keys($aParams)) . "`, `date`) VALUES('" . implode("', '", array_values($aParams)) . "', UNIX_TIMESTAMP())") <= 0)
        	return 0;

        $iId = (int)$this->lastId();
        if($iId > 0 && isset($aParams['owner_id']) && (int)$aParams['owner_id'] > 0) {
	       	//--- Wall -> Update for Alerts Engine ---//
			bx_import('BxDolAlerts');
			$oAlert = new BxDolAlerts('bx_' . $this->_oConfig->getUri(), 'update', $aParams['owner_id']);
			$oAlert->alert();
			//--- Wall -> Update for Alerts Engine ---//
        }

        return $iId;
	}	
	function updateEvent($aParams, $iId) {
	    $aUpdate = array();
	    foreach($aParams as $sKey => $sValue)
	       $aUpdate[] = "`" . $sKey . "`='" . $sValue . "'";
        $sSql = "UPDATE `" . $this->_sPrefix . "events` SET " . implode(", ", $aUpdate) . " WHERE `id`='" . $iId . "'";
        return $this->query($sSql);
	}
	function deleteEvent($aParams, $sWhereAddon = "") {
	    $aWhere = array();
	    foreach($aParams as $sKey => $sValue)
	       $aWhere[] = "`" . $sKey . "`='" . $sValue . "'";
        $sSql = "DELETE FROM `" . $this->_sPrefix . "events` WHERE " . implode(" AND ", $aWhere) . $sWhereAddon;
        return $this->query($sSql);
	}
	function deleteEventCommon($aParams) {
		return $this->deleteEvent($aParams, " AND `type` LIKE '" . $this->_oConfig->getCommonPostPrefix() . "%'");
	}
	function getUser($mixed, $sType = 'id') {
	    switch($sType) {
            case 'id':
                $sWhereClause = "`ID`='" . $mixed . "'";
                break;
            case 'username':
                $sWhereClause = "`NickName`='" . $mixed . "'";
                break;
        } 
	    
	    $sSql = "SELECT `ID` AS `id`, `Couple` AS `couple`, `NickName` AS `username`, `Password` AS `password`, `Email` AS `email`, `Sex` AS `sex` FROM `Profiles` WHERE " . $sWhereClause . " LIMIT 1";
	    $aUser = $this->getRow($sSql);

	    if(empty($aUser))
	        $aUser = array('id' => 0, 'couple' => 0, 'username' => _t('_wall_anonymous'), 'password' => '', 'email' => '', 'sex' => 'male');

	    return $aUser;
	}
	
	//--- View Events Functions ---//
	function getHandlers() {	    
	    $sSql = "SELECT 
	               `alert_unit` AS `alert_unit`, 
	               `alert_action` AS `alert_action`, 
	               `module_uri` AS `module_uri`,
	               `module_class` AS `module_class`,
	               `module_method` AS `module_method` 
                FROM `" . $this->_sPrefix . "handlers`";
	    return $this->getAll($sSql);
	}
	function getEvents($aParams) {
	    global $sHomeUrl;

        switch($aParams['type']) {
            case 'id':
                $sWhereClause = "`id`='" . $aParams['object_id'] . "' ";
                $sLimitClause = 'LIMIT 1';
                break;
            case 'owner':
                $sWhereClause = "`owner_id`='" . $aParams['owner_id'] . "' " . (isset($aParams['filter']) ? $this->_getFilterAddon($aParams['owner_id'], $aParams['filter']) : '');
                $sOrderClause = isset($aParams['order']) ? ' ORDER BY `date` ' . strtoupper($aParams['order']) : '';
                $sLimitClause = isset($aParams['count']) ? ' LIMIT ' . $aParams['start'] . ', ' . $aParams['count'] : '';                
                break;            
        }
        	    
	    $sSql = "SELECT 
                `id` AS `id`, 
                `owner_id` AS `owner_id`, 
                `object_id` AS `object_id`, 
                `type` AS `type`, 
                `action` AS `action`, 
                `content` AS `content`,
                `title` AS `title`,
                `description` AS `description`,
                `date` AS `date`,
                DATE_FORMAT(FROM_UNIXTIME(`date`), '" . $this->_oConfig->getDividerDateFormat() . "') AS `print_date`, 
                DAYOFYEAR(FROM_UNIXTIME(`date`)) AS `days`, 
                DAYOFYEAR(NOW()) AS `today`,
                (UNIX_TIMESTAMP() - `date`) AS `ago`
            FROM `" . $this->_sPrefix . "events`
            WHERE " . $sWhereClause . $sOrderClause . $sLimitClause;

	    $aEvents = array();
	    $aEvent = $this->getFirstRow($sSql);
	    while($aEvent) {
            $aEvent['content'] = str_replace("[ray_url]", $sHomeUrl, $aEvent['content']);
            $aEvent['ago'] = _format_when($aEvent['ago']);
            $aEvents[] = $aEvent;

            $aEvent = $this->getNextRow();
	    }

        return $aEvents;
	}
	function getEventsCount($iOwnerId, $sFilter) {
	    $sSql = "SELECT COUNT(*) FROM `" . $this->_sPrefix . "events` WHERE `owner_id`='" . $iOwnerId . "'" . $this->_getFilterAddon($iOwnerId, $sFilter) . " LIMIT 1";
	    return $this->getOne($sSql);
	}

	//--- Comment Functions ---//
	function getCommentsCount($iId) {
        $sSql = "SELECT COUNT(`cmt_id`) FROM `" . $this->_sPrefix . "comments` WHERE `cmt_object_id`='" . $iId . "' AND `cmt_parent_id`='0' LIMIT 1";
        return (int)$this->getOne($sSql);
	}

	//--- Shared Media Functions ---//	
	function getSharedCategory($sType, $iId) {
	    $aType2Db = array(
            'sharedPhoto' => array('table' =>'bx_shared_photo_files', 'id' => 'medID'), 
            'sharedMusic' => array('table' => 'RayMp3Files', 'id' => 'ID'), 
            'sharedVideo' => array('table' => 'RayVideoFiles', 'id' => 'ID')
        );
	    
	    $sSql = "SELECT `Categories` FROM `" . $aType2Db[$sType]['table'] . "` WHERE `" . $aType2Db[$sType]['id'] . "`='" . $iId . "' LIMIT 1";
	    return $this->getOne($sSql);
	}
	
	//--- Private functions ---//
	function _getFilterAddon($iOwnerId, $sFilter) {	    
	    switch($sFilter) {
	        case BX_WALL_FILTER_OWNER:
	            $sFilterAddon = " AND `action`='' AND `object_id`='" . $iOwnerId . "' ";
	            break;
	        case BX_WALL_FILTER_OTHER:
	            $sFilterAddon = " AND `action`='' AND `object_id`<>'" . $iOwnerId . "' ";
	            break;
	        case BX_WALL_FILTER_ALL:
	        default:
	            $sFilterAddon = "";
	    }
	    return $sFilterAddon;
	}
}
?>
