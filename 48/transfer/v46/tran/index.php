<?php
/*
ShopEx48版本数据转换框架，
采用工厂流水线模式：
	1. 先将大体的数据转过来，
	2. 根据ShopEx系统要求进行修正
第一步一般只需要在每一项的$aMap进行修改即可，第二步要根据实际情况修改

*/
set_time_limit(0);
error_reporting(E_ALL^E_NOTICE);
include_once("../config/config.php");
$link=mysql_connect(DB_HOST,DB_USER,DB_PASSWORD); 
mysql_select_db(DB_NAME);

mysql_query("set names utf8");

?>
<html>
<head>
<title>ShopEx数据转换程序</title>
<META HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
</head>
<body style="font-size:16px;">

<?php

$do = new transfer;

$do->orderDetail();
$do->orderItems();

$do->memberLevel();
$do->memberDetail();


class transfer{
	var $dbPrefix = DB_PREFIX;

	function orderDetail(){
		//清空会员等级相关表
		$aTable = array(
			$this->dbPrefix.'orders',
			$this->dbPrefix.'order_items',
			$this->dbPrefix.'order_log',
			$this->dbPrefix.'order_pmt',
		);		
		$this->truncate($aTable);
		
		////////////////////////////////////////////////////
		$aMap = array(
			'order_id'				=>	'orderid',
			'member_id'				=>	'userid',
			'confirm'				=>	'',
			'status'				=>	'',
			'pay_status'			=>	'ifsk',
			'ship_status'			=>	'',
			'user_status'			=>	'',
			'is_delivery'			=>	'',
			'shipping_id'			=>	'ttype',
			'shipping'				=>	'',
			'shipping_area'			=>	'',
			'payment'				=>	'ptype',
			'weight'				=>	'weight',
			'tostr'					=>	'',
			'itemnum'				=>	'',
			'acttime'				=>	'paytime',
			'createtime'			=>	'ordertime',
			'refer_id'				=>	'',
			'refer_url'				=>	'',
			'ip'					=>	'ip',
			'ship_name'				=>	'name',
			'ship_area'				=>	'',
			'ship_addr'				=>	'addr',
			'ship_zip'				=>	'zip',
			'ship_tel'				=>	'tel',
			'ship_email'			=>	'email',
			'ship_time'				=>	'sendtime',
			'ship_mobile'			=>	'mobile',
			'cost_item'				=>	'item_amount',
			'is_tax'				=>	'',
			'cost_tax'				=>	'',
			'tax_company'			=>	'',
			'cost_freight'			=>	'freight',
			'is_protect'			=>	'',
			'cost_protect'			=>	'',
			'cost_payment'			=>	'',
			'currency'				=>	'paycur',
			'cur_rate'				=>	'',
			'score_u'				=>	'orderpoint',
			'score_g'				=>	'getpoint',
			'advance'				=>	'',
			'discount'				=>	'',
			'use_pmt'				=>	'',
			'total_amount'			=>	'total_amount',
			'final_amount'			=>	'total_amount',
			'pmt_amount'			=>	'',
			'payed'					=>	'paymoney',
			'markstar'				=>	'',
			'memo'					=>	'memo',
			'print_status'			=>	'',
			'mark_text'				=>	'',
			'disabled'				=>	'',
			'last_change_time'		=>	'',
			'use_registerinfo'		=>	'',
			'mark_type'				=>	'',
		);
		$to = $this->dbPrefix.'orders';
		$from = 'sdb_mall_orders';
		$where = '1=1';
		///////////////////////////////////////////////////////
		$insertsql = $this->getInsertSQL($aMap,$to,$from,$where);
		$rs = $this->dbQ($insertsql);
		$updatesql = 'UPDATE '.$to. ' SET confirm=\'Y\',status=\'finish\',ship_status=1';
		$rs = $this->dbQ($updatesql,false);
		echo "订单转换完毕!<br>";
	
	}

	function orderItems(){
		//清空会员等级相关表
		$aTable = array(
			$this->dbPrefix.'order_items',
		);		
		$this->truncate($aTable);
		
		////////////////////////////////////////////////////
		$aMap = array(
			'item_id'				=>	'id',
			'order_id'				=>	'orderid',
			'product_id'			=>	'gid',
			'dly_status'			=>	'',
			'type_id'				=>	'',
			'bn'					=>	'bn',
			'name'					=>	'goods',
			'cost'					=>	'',
			'price'					=>	'price',
			'amount'				=>	'oof',
			'score'					=>	'',
			'nums'					=>	'nums',
			'minfo'					=>	'',
			'sendnum'				=>	'sendnum',
			'addon'					=>	'',
			'is_type'				=>	'',
			'point'					=>	'',
		);
		$to = $this->dbPrefix.'order_items';
		$from = 'sdb_mall_items';
		$where = '1=1';
		///////////////////////////////////////////////////////
		$insertsql = $this->getInsertSQL($aMap,$to,$from,$where);
		$rs = $this->dbQ($insertsql);	
		echo "订单商品转换完毕!<br>";
	
	}

	function memberLevel(){
		//清空会员等级相关表
		$aTable = array(
			$this->dbPrefix.'member_lv',
		);		
		$this->truncate($aTable);
		
		////////////////////////////////////////////////////
		$aMap = array(
			'member_lv_id'			=>	'levelid',
			'name'					=>	'name',
			'dis_count'				=>	'discount',
			'point'					=>	'point',
			'default_lv'			=>	'recsts',
		);
		$to = $this->dbPrefix.'member_lv';
		$from = 'sdb_mall_member_level';
		$where = '1=1';
		///////////////////////////////////////////////////////
		$insertsql = $this->getInsertSQL($aMap,$to,$from,$where);
		$rs = $this->dbQ($insertsql);	
		echo "会员等级转换完毕!<br>";
	
	}

	function memberDetail(){
		//清空会员相关表
		$aTable = array(
			$this->dbPrefix.'members',
			$this->dbPrefix.'member_addrs',
			$this->dbPrefix.'member_attr',
			$this->dbPrefix.'member_coupon',
			$this->dbPrefix.'member_dealer',
			$this->dbPrefix.'member_mattrvalue'
		);		
		$this->truncate($aTable);
		
		////////////////////////////////////////////////////
		$aMap = array(
			'member_id'				=>	'userid',		
			'member_lv_id'			=>	'level', //在memberLevel方法中指定
			'uname'					=>	'user',
			'name'					=>	'name',
			'lastname'				=>	'',
			'firstname'				=>	'',
			'password'				=>	'password',
			'area'					=>	'',
			'mobile'				=>	'mov',
			'tel'					=>	'tel',
			'email'					=>	'email',
			'zip'					=>	'zip',
			'addr'					=>	'addr',
			'province'				=>	'province',
			'city'					=>	'city',
			'order_num'				=>	'',
			'b_year'				=>	'',
			'b_month'				=>	'',
			'b_day'					=>	'',
			//'sex'					=>	'sex',
			'advance'				=>	'advance',
			'point_history'			=>	'',
			'point'					=>	'point',
			'reg_ip'				=>	'ip',
			'regtime'				=>	'regtime',
			'pw_answer'				=>	'pw_answer',
			'pw_question'			=>	'pw_question'
		);
		$to = $this->dbPrefix.'members';
		$from = 'sdb_mall_member';
		$where = '1=1';
		///////////////////////////////////////////////////////
		$insertsql = $this->getInsertSQL($aMap,$to,$from,$where);
		$rs = $this->dbQ($insertsql);	
		echo "会员转换完毕!<br>";

	}



	/////////////////////工具函数区///////////////
	//查询数据库
	function dbQ($sql,$show=true){
		if(is_string($sql)){
			$rs = mysql_query($sql) or  die("<br><font color=red>".mysql_error()."</font><hr>".$sql);	
			if($show){
				if(strtoupper(substr($sql,0,6)) != "SELECT"){
					echo " <font color=red>该查询影响 ".mysql_affected_rows()." 行</font><br>";
				}else{
					echo " <font color=red>该查询影响 ".mysql_num_rows($rs)." 行</font><br>";
				}
			}
			return $rs;
		}else{
			return false;
		}
	}

	
	//
	function getInsertSQL($aMap,$to,$from,$where){
		if(!is_array($aMap)){
			return false;
		}else{
			$colStr = '( ';
			$valStr = ' ';
			foreach($aMap as $col=>$val){
				if(empty($val)) continue;
				$colStr .= '`'.$col.'`,';
				if(is_numeric($val)){
					$valStr .= "'".$val."',";
				}else{
					$valStr .= $val.",";
				}
			}
			//
			$colStr = rtrim($colStr,",").")";
			$valStr = rtrim($valStr,",")." ";
		}

		$sql = "INSERT INTO `".$to."` ".$colStr." SELECT ".$valStr." FROM ".$from." WHERE ".$where;

		return $sql;
	}

	function ubbtohtml(&$string) { 
		$string = preg_replace("/\[(\/?b)\]/i","<\\1>",$string);
		$string = preg_replace("/\[(\/?u)\]/i","<\\1>",$string);
		$string = preg_replace("/\[(\/?i)\]/i","<\\1>",$string);
		$string = preg_replace("/\[(\/?center)\]/i","<\\1>",$string);
		$string = preg_replace("/\[align=([a-zA-Z]+)\]/i","<p align=\\1>",$string);
		$string = preg_replace("/\[(\/align)\]/i","<\\1>",$string);
		//$string = preg_replace("/\[url\=(.+)\](.+)\[\/url\]/i","<a href=\\1 target=\"_blank\">\\2</a>",$string);
		//$string = preg_replace("/\[url\](.+)\[\/url\]/i","<a href=\\1 target=\"_blank\">\\1</a>",$string);
		//替换掉[url]标签
		$string = preg_replace("/\[url\=(.+)\](.+)\[\/url\]/i","\\2",$string);
		$string = preg_replace("/\[url\](.+)\[\/url\]/i","\\1",$string);
		//统一给url加上链接
		$string = preg_replace("/(.*)\s*(http:\/\/\S+)\s*(.*)/i","<a href=\\2 target=_blank>\\2</a>",$string);
		//
		$string = preg_replace("/\[img\](.+)\[\/img\]/i","<a href=\\1 target=\"_blank\"><img src=\\1 onload=\"javascript:if(this.width>500)this.width=500\"/></a>",$string);
		$string = preg_replace("/\[color=(.+)\](.+)\[\/color\]/i","<font color=\\1>\\2</font>",$string);
		$string = preg_replace("/\[size=([0-9]{1})\](.+)\[\/size\]/i","<font size=\\1>\\2</font>",$string);
		$string = nl2br($string);
	}
	
	//清除表数据，可以传入数组或者字符串
	function truncate($table){
		if(is_array($table)){
			foreach($table as $val){
				$sql = "TRUNCATE $val";
				$this->dbQ($sql,false);
			}
		}else{
			$sql = "TRUNCATE $table";
			$this->dbQ($sql,false);
		}

		return true;
	}
}



?>

</body>
</html>