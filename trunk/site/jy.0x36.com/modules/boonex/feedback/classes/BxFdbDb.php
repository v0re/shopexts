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

bx_import('BxDolTextDb');

class BxFdbDb extends BxDolTextDb {	
	function BxFdbDb(&$oConfig) {
		parent::BxDolTextDb($oConfig);
	}
	function getEntries($aParams) {
	    switch($aParams['sample_type']) {
	        case 'id':
	            $sMethod = 'getRow';
	            $sSelectClause = "`te`.`content` AS `content`, ";
	            $sWhereClause = " AND `te`.`id`='" . $aParams['id'] . "'";
	            $sOrderClause = "`te`.`date` DESC";
	            $sLimitClause = "LIMIT 1";
	            break;
	        case 'uri':
	            $sMethod = 'getRow';
	            $sSelectClause = "`te`.`content` AS `content`, ";
	            $sWhereClause = " AND `te`.`uri`='" . $aParams['uri'] . "'";
	            $sOrderClause = "`te`.`date` DESC";
	            $sLimitClause = "LIMIT 1";
	            break;
            case 'view':
	            $sMethod = 'getAll';
	            $sSelectClause = "`te`.`content` AS `content`, ";
	            $sWhereClause = " AND `te`.`uri`='" . $aParams['uri'] . "' AND `te`.`status`='" . BX_FDB_STATUS_ACTIVE . "'";
	            $sOrderClause = "`te`.`date` DESC";
	            $sLimitClause = "LIMIT 1";
	            break;
            case 'search_unit':
	            $sMethod = 'getAll';
	            $sSelectClause = "SUBSTRING(`te`.`content`, 1, " . $this->_oConfig->getSnippetLength() . ") AS `content`, ";
	            $sWhereClause = " AND `te`.`uri`='" . $aParams['uri'] . "'";
	            $sOrderClause = "`te`.`date` DESC";
	            $sLimitClause = "LIMIT 1";
	            break;
            case 'archive':
	            $sMethod = 'getAll';
	            $sSelectClause = "SUBSTRING(`te`.`content`, 1, " . $this->_oConfig->getSnippetLength() . ") AS `content`, ";
	            $sWhereClause = " AND `te`.`status`='" . BX_FDB_STATUS_ACTIVE . "'";
	            $sOrderClause = "`te`.`date` DESC";
	            $sLimitClause = "LIMIT " . $aParams['start'] . ', ' . $aParams['count'];
	            break;	            
	        case 'owner':
	            $sMethod = 'getAll';
	            $sSelectClause = "SUBSTRING(`te`.`content`, 1, " . $this->_oConfig->getSnippetLength() . ") AS `content`, ";
	            $sWhereClause = " AND `te`.`author_id`='" . $aParams['sample_params']['owner_id'] . "'";
	            $sOrderClause = "`te`.`date` DESC";
	            $sLimitClause = "LIMIT " . $aParams['start'] . ', ' . $aParams['count'];
	            break;	        
	        case 'admin':
	            $sMethod = 'getAll';
	            $sSelectClause = "SUBSTRING(`te`.`content`, 1, " . $this->_oConfig->getSnippetLength() . ") AS `content`, ";
	            $sWhereClause = !empty($aParams['filter_value']) ? " AND (`tp`.`NickName` LIKE '%" . $aParams['filter_value'] . "%' OR `te`.`caption` LIKE '%" . $aParams['filter_value'] . "%' OR `te`.`content` LIKE '%" . $aParams['filter_value'] . "%' OR `te`.`tags` LIKE '%" . $aParams['filter_value'] . "%')" : "";
	            $sOrderClause = "`te`.`date` DESC";
	            $sLimitClause = "LIMIT " . $aParams['start'] . ', ' . $aParams['count'];
	            break;            
            case 'all':
	            $sMethod = 'getAll';
	            $sSelectClause = "SUBSTRING(`te`.`content`, 1, " . $this->_oConfig->getSnippetLength() . ") AS `content`, ";	            
	            $sWhereClause = " AND `te`.`status`='" . BX_FDB_STATUS_ACTIVE . "'";
	            $sOrderClause = "`te`.`date` DESC";
	            break;	        
	    }
	    $sSql = "SELECT 
	               " . $sSelectClause . "
	               `te`.`id` AS `id`,
	               `tp`.`ID` AS `author_id`,
	               `tp`.`NickName` AS `author_username`,
	               `te`.`caption` AS `caption`,
	               `te`.`tags` AS `tags`,
	               `te`.`uri` AS `uri`,
	               `te`.`allow_comment_to` AS `allow_comment_to`,
	               `te`.`allow_vote_to` AS `allow_vote_to`,
	               DATE_FORMAT(FROM_UNIXTIME(`te`.`date`), '" . $this->_oConfig->getDateFormat() . "') AS `date_uf`,
	               `te`.`date` AS `date`,
	               `te`.`status` AS `status`	               
                FROM `" . $this->_sPrefix . "entries` AS `te`
                LEFT JOIN `Profiles` AS `tp` ON `te`.`author_id`=`tp`.`ID`
                WHERE 1 " . $sWhereClause . "
                ORDER BY " . $sOrderClause . " " . $sLimitClause;

	    $aResult = $this->$sMethod($sSql);
	    
	    if(!in_array($aParams['sample_type'], array('id', 'uri', 'view')))
	       for($i = 0; $i < count($aResult); $i++)
	           $aResult[$i]['content'] = strip_tags($aResult[$i]['content']);

	    return $aResult;
	}
}
?>