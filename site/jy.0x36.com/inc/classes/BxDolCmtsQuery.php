<?php

require_once (BX_DIRECTORY_PATH_CLASSES . 'BxDolDb.php');

/**
 * @see BxDolCmts
 */ 
class BxDolCmtsQuery extends BxDolDb
{
	var $_aSystem; // current voting system
    var $_sTable;
    var $_sTableTrack;

	function BxDolCmtsQuery(&$aSystem)
	{
        $this->_aSystem = &$aSystem;
        $this->_sTable = $this->_aSystem['table_cmts'];
        $this->_sTableTrack = $this->_aSystem['table_track'];
		parent::BxDolDb();
	}

    function getTableName ()
    {
        return $this->_sTable;
    }    

	function getComments ($iId, $iCmtParentId = 0, $iAuthorId = 0, $sCmtOrder = 'ASC', $iStart = 0, $iCount = -1)
    {
		global $sHomeUrl;
        
		$sFields = "'' AS `cmt_rated`,";
		$sJoin = '';
		if ($iAuthorId) {
			$sFields = '`r`.`cmt_rate` AS `cmt_rated`,';
			$sJoin = "LEFT JOIN {$this->_sTableTrack} AS `r` ON (`r`.`cmt_system_id` = ".$this->_aSystem['system_id']." AND `r`.`cmt_id` = `c`.`cmt_id` AND `r`.`cmt_rate_author_id` = $iAuthorId)";
		}
		$a = $this->getAll("SELECT 
				$sFields
				`c`.`cmt_id`, 
				`c`.`cmt_parent_id`, 
				`c`.`cmt_object_id`, 
				`c`.`cmt_author_id`, 
				`c`.`cmt_text`, 
				`c`.`cmt_mood`,
				`c`.`cmt_rate`, 
				`c`.`cmt_rate_count`, 
				`c`.`cmt_replies`, 
				(UNIX_TIMESTAMP() - UNIX_TIMESTAMP(`c`.`cmt_time`)) AS `cmt_secs_ago`,
				`p`.`NickName` AS `cmt_author_name`				
			FROM {$this->_sTable} AS `c`
			LEFT JOIN `Profiles` AS `p` ON (`p`.`ID` = `c`.`cmt_author_id`)
			$sJoin
			WHERE `c`.`cmt_object_id` = '$iId' AND `c`.`cmt_parent_id` = '$iCmtParentId'
			ORDER BY `c`.`cmt_time` " . (strtoupper($sCmtOrder) == 'ASC' ? 'ASC' : 'DESC') . ($iCount != -1 ? ' LIMIT ' . $iStart . ', ' . $iCount : ''));

		//LEFT JOIN `media` AS `m` ON (`m`.`med_id` = `p`.`Avatar` AND `m`.`med_status` = 'active')

		for(reset($a) ; list ($k) = each ($a) ; ) {
		    $a[$k]['cmt_text'] = str_replace("[ray_url]", $sHomeUrl, $a[$k]['cmt_text']);
		 	$a[$k]['cmt_ago'] = _format_when ($a[$k]['cmt_secs_ago']);
		}
		 	
		return $a;
	}

	function getComment ($iId, $iCmtId, $iAuthorId = 0)
	{
	    global $sHomeUrl;
	    
		$sFields = "'' AS `cmt_rated`,";
		$sJoin = '';
		if ($iAuthorId) {
			$sFields = '`r`.`cmt_rate` AS `cmt_rated`,';
			$sJoin = "LEFT JOIN {$this->_sTableTrack} AS `r` ON (`r`.`cmt_system_id` = ".$this->_aSystem['system_id']." AND `r`.`cmt_id` = `c`.`cmt_id` AND `r`.`cmt_rate_author_id` = $iAuthorId)";
		}		
		$aComment = $this->getRow("SELECT 
				$sFields
				`c`.`cmt_id`, 
				`c`.`cmt_parent_id`, 
				`c`.`cmt_object_id`, 
				`c`.`cmt_author_id`, 
				`c`.`cmt_text`, 
				`c`.`cmt_mood`,
				`c`.`cmt_rate`, 
				`c`.`cmt_rate_count`, 
				`c`.`cmt_replies`, 
				(UNIX_TIMESTAMP() - UNIX_TIMESTAMP(`c`.`cmt_time`)) AS `cmt_secs_ago`,
				`p`.`NickName` AS `cmt_author_name`				
			FROM {$this->_sTable} AS `c`
			LEFT JOIN `Profiles` AS `p` ON (`p`.`ID` = `c`.`cmt_author_id`)
			$sJoin
			WHERE `c`.`cmt_object_id` = '$iId' AND `c`.`cmt_id` = '$iCmtId' 
			LIMIT 1");

		$aComment['cmt_text'] = str_replace("[ray_url]", $sHomeUrl, $aComment['cmt_text']);
		$aComment['cmt_ago'] = _format_when($aComment['cmt_secs_ago']);

		return $aComment;
	}
	
	function getCommentSimple ($iId, $iCmtId)
	{
		return $this->getRow("
			SELECT 
				*, (UNIX_TIMESTAMP() - UNIX_TIMESTAMP(`c`.`cmt_time`)) AS `cmt_secs_ago`
			FROM {$this->_sTable} AS `c`
			WHERE `cmt_object_id` = '$iId' AND `cmt_id` = '$iCmtId' 
			LIMIT 1");
	}
		
	function addComment ($iId, $iCmtParentId, $iAuthorId, $sText, $iMood)	
	{
		if (!$this->query("INSERT INTO {$this->_sTable} SET 
			`cmt_parent_id` = '$iCmtParentId',
			`cmt_object_id` = '$iId',
			`cmt_author_id` = '$iAuthorId',
			`cmt_text` = '$sText',
			`cmt_mood` = '$iMood',
			`cmt_time` = NOW()"))
		{
			return false;
		}
		
		$iRet = $this->lastId();
		
		if ($iCmtParentId)
			$this->query ("UPDATE {$this->_sTable} SET `cmt_replies` = `cmt_replies` + 1 WHERE `cmt_id` = '$iCmtParentId' LIMIT 1");
		
		return $iRet;
	}
	
	function removeComment ($iId, $iCmtId, $iCmtParentId)	
	{
		if (!$this->query("DELETE FROM {$this->_sTable} WHERE `cmt_object_id` = '$iId' AND `cmt_id` = '$iCmtId' LIMIT 1"))
			return false;
			
		$this->query ("UPDATE {$this->_sTable} SET `cmt_replies` = `cmt_replies` - 1 WHERE `cmt_id` = '$iCmtParentId' LIMIT 1");
		
		return true;
	}
	
	function updateComment ($iId, $iCmtId, $sText, $iMood)
	{
		return $this->query("UPDATE {$this->_sTable} SET `cmt_text` = '$sText', `cmt_mood` = '$iMood'  WHERE `cmt_object_id` = '$iId' AND `cmt_id` = '$iCmtId' LIMIT 1");
	}
	
	function rateComment ($iSystemId, $iCmtId, $iRate, $iAuthorId, $sAuthorIp)
	{
		if ($this->query("INSERT IGNORE INTO {$this->_sTableTrack} SET 
			`cmt_system_id` = '$iSystemId',
			`cmt_id` = '$iCmtId',
			`cmt_rate` = '$iRate',
			`cmt_rate_author_id` = '$iAuthorId',
			`cmt_rate_author_nip` = INET_ATON('$sAuthorIp'),
			`cmt_rate_ts` = UNIX_TIMESTAMP()"))
		{
			$this->query("UPDATE {$this->_sTable} SET `cmt_rate` = `cmt_rate` + $iRate, `cmt_rate_count` = `cmt_rate_count` + 1 WHERE `cmt_id` = '$iCmtId' LIMIT 1");
			return true;
		}
		
		return false;
    }

    function deleteAuthorComments ($iAuthorId)
    {
        $isDelOccured = 0;
        $a = $this->getAll ("SELECT `cmt_id`, `cmt_parent_id` FROM {$this->_sTable} WHERE `cmt_author_id` = '$iAuthorId' AND `cmt_replies` = 0");
        for ( reset($a) ; list (, $r) = each ($a) ; )
        {            
            $this->query ("DELETE FROM {$this->_sTable} WHERE `cmt_id` = '{$r['cmt_id']}'");
            $this->query ("UPDATE {$this->_sTable} SET `cmt_replies` = `cmt_replies` - 1 WHERE `cmt_id` = '{$r['cmt_parent_id']}'");
            $isDelOccured = 1;
        }
        $this->query ("UPDATE {$this->_sTable} SET `cmt_author_id` = 0 WHERE `cmt_author_id` = '$iAuthorId' AND `cmt_replies` != 0");
        if ($isDelOccured)
            $this->query ("OPTIMIZE TABLE {$this->_sTable}");
    }

    function deleteObjectComments ($iObjectId)
    {
        $this->query ("DELETE FROM {$this->_sTable} WHERE `cmt_object_id` = '$iObjectId'");
        $this->query ("OPTIMIZE TABLE {$this->_sTable}");
    }    

    function getObjectCommentsCount ($iObjectId, $iParentId = -1)
    {
        return $this->getOne ("SELECT COUNT(*) FROM `" . $this->_sTable ."` WHERE `cmt_object_id`='" . $iObjectId . "'" . ($iParentId != -1 ? " AND `cmt_parent_id`='" . $iParentId . "'" : ""));
    }

    function updateTriggerTable($iId, $iCount) 
    {
        return $this->query("UPDATE `{$this->_aSystem['trigger_table']}` SET `{$this->_aSystem['trigger_field_comments']}` = '$iCount' WHERE `{$this->_aSystem['trigger_field_id']}` = '$iId' LIMIT 1");
    }    

}
?>
