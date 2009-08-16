<?php

class cmd_order extends mdl_order{   

    function delOrderMsg($aData){
			$orderid=$aData['rel_order'];
			//$aRs = $this->db->query("DELETE FROM sdb_orders WHERE order_id = '$orderid' LIMIT 1");
			$this->toCancel($orderid);

       return true;
    }

		function isMarked($order_id){
			$sql = "SELECT rel_id FROM sdb_tag_rel WHERE rel_id='".$order_id."'";
		
			if($this->db->selectrow($sql)){
				return true;
			}else{
				return false;
			}
		}
}
?>