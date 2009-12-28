<?php
include_once('shopObject.php');
class mdl_askpric extends shopObject{
	var $defaultCols = 'member_id,goods_id,asktime';
	var $idColumn = 'ask_id';
	var $adminCtl = 'member/askpric';
	var $defaultOrder = array('ask_id','desc');
	var $tableName = 'sdb_member_pric';

	function getColumns(){
		$columns = array(
			'ask_id'=>array('label'=>'咨询商品','class'=>'span-3','readonly'=>true),    /* 会员id */
			'goods_id'=>array('label'=>'商品名称','class'=>'span-8','type'=>'object:goods:goodsname'),
			'member_id'=>array('label'=>'用户名','class'=>'span-2','type'=>'object:member:uname'),
			'asktime'=>array('label'=>'咨询时间','class'=>'span-3','type'=>'time:SDATE_STIME'),
			'ask_status'=>array('label'=>'报价状态','class'=>'span-2','type'=>'ask_status'),
		);
		return $columns;
	}
	
	function getFieldById($askId, $aField=array('*')){
		return $this->db->selectrow("SELECT ".implode(",", $aField)." FROM sdb_member_pric WHERE ask_id='{$askId}'");
    }

	function toShow($aData, $createBill=false){
		$askInfo['ask_status'] = intval($aData['status']);
        $aRs = $this->db->query('SELECT * FROM sdb_member_pric WHERE ask_id=\''.$aData['ask_id'].'\'');
        $sSql = $this->db->GetUpdateSql($aRs,$askInfo);
        if(!$sSql || $this->db->exec($sSql)){
            return true;
        }else{
            return false;
        }
		return true;
	}

	function modifier_ask_status(&$rows){
        $status = array(0=>'未报价',
                    1=>'已报价' );
        foreach($rows as $k => $v){
            $rows[$k] = $status[$v];
        }
    }
}
?>