<?php
class cmd_member extends mdl_member{
	function addAsk($nMid,$nGid){
		if($nMid!='' && $nGid!=''){
			$askPric['member_id'] = intval($nMid);
			$askPric['goods_id'] = intval($nGid);
			$askPric['asktime'] = time();
			$aRs = $this->db->query("SELECT * FROM sdb_member_pric WHERE member_id=".intval($nMid)." and goods_id=".intval($nGid));
			$sqlString = $this->db->GetUpdateSQL($aRs, $askPric,true);
			return (!$sqlString || $this->db->exec($sqlString));
        }else{
            return false;
        }
    }

	function getAskPric($nMemberId,$nPage){
		$oGood = $this->system->loadModel('trading/goods');
		return $oGood->getAskPric($nMemberId,$nPage);
    }
}
?>
