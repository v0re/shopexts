<?php
include_once('shopObject.php');
class mdl_askpric extends shopObject{
	var $idColumn = 'ask_id';
    var $textColumn = 'order_name';
	var $adminCtl = 'goods/askpric';
	var $defaultOrder = array('ask_id','desc');
	var $defaultCols = 'order_name,add_time';
	var $tableName = 'sdb_ask_orders';

	function getColumns(){
		$columns = array(
			'ask_id'=>array('label'=>'询价单id','class'=>'span-3','readonly'=>true),    /* 询价单id */
			'order_name'=>array('label'=>'名称','class'=>'span-8','type'=>'order_name'),
			'add_time'=>array('label'=>'上传时间','class'=>'span-3','type'=>'time'),
		);
		return $columns;
	}
	
	function getFieldById($askId, $aField=array('*')){
		return $this->db->selectrow("SELECT ".implode(",", $aField)." FROM sdb_ask_orders WHERE ask_id='{$askId}'");
    }
	
	function getAskInfo(){
		return $this->db->select_b("SELECT * FROM sdb_ask_orders where disabled='false' ORDER BY ask_id desc");
    }

	function save(&$aData){
		$rs = $this->db->query('SELECT * FROM sdb_ask_orders WHERE 0=1');
		$sql = $this->db->GetInsertSQL($rs, $aData);
		if($sql && !$this->db->exec($sql)){
			trigger_error('SQL Error:'.$sql,E_USER_NOTICE);
			return false;
		}
		$aData['ask_id'] = $this->db->lastInsertId();
		return $aData['ask_id'];
	}
}
?>