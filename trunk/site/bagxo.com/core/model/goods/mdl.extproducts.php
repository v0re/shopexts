<?php
include_once('shopObject.php');
class mdl_extproducts extends shopObject{
	
	function dumpUnMarketAble(&$trading){
		foreach($trading['products'] as $key=>$product){
			$goodsid = $product['goods_id'];
			if(!$this->isMarketAble($goodsid)){
				unset($trading['products'][$key]);
			}
		}
	}

	function isMarketAble($goodsid){
		$sqlString = "SELECT marketable FROM sdb_goods where goods_id='{$goodsid}'";
		$row = $this->db->selectrow($sqlString);
		return $row['marketable'] == 'true' ? true : false;
	}

}

?>