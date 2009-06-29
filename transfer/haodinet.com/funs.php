<?php

//加载ShopEx的配置文件
include_once("../include/mall_config.php");

mysql_connect($dbHost, $dbUser, $dbPass);
mysql_select_db($dbName);
mysql_query("set names utf8");


function export_cat(){	
		//清空商品分类表
		$sql = "TRUNCATE sdb_mall_offer_pcat";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());		
		$data = array();		
		//修改prop_cat_id可以为null
		$sql = "ALTER TABLE `sdb_mall_offer_pcat` CHANGE `prop_cat_id` `prop_cat_id` MEDIUMINT( 6 ) UNSIGNED NULL ";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());		
		//迁移商品大类数据
		$sql = "insert into sdb_mall_offer_pcat (catid,offerid,pid,cat,catord,catiffb) select concat(1,categoryid),1,0,category,(100-orders),1  from `产品中类` order by orders DESC ";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());
		//迁移商品小类数据
		$sql = "insert into sdb_mall_offer_pcat (catid,offerid,pid,cat,catord,catiffb) select `产品小类ID`,1 ,concat(1,`产品中类ID`),`产品小类名称`,(100-`产品小类序号`),1  from `产品小类` order by `产品小类序号` DESC ";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());
		//统一处理catpath
		$sql = "select * from sdb_mall_offer_pcat order by catid";
		$rs = mysql_query($sql) or  err(__LINE__." ".mysql_error());			
		while ($row = mysql_fetch_array($rs)){
			$data['catpath'][$row['catid']] = ','.intString($row['catord'], 5).','.intString($row['catid'],6);
			if ($row['pid']!=0){	
				$data['catpath'][$row['catid']] = $data['catpath'][$row['pid']].$data['catpath'][$row['catid']];
			}			
			$sql = "update sdb_mall_offer_pcat set catpath='{$data['catpath'][$row['catid']]}' where catid='{$row['catid']}'";
			mysql_query($sql) or  err(__LINE__." ".mysql_error());		
		}		
		unset($data);
}
	
function export_prop_category(){
	//清空商品分类表
	$sql = "TRUNCATE sdb_mall_prop_category";
	mysql_query($sql) or  err(__LINE__." ".mysql_error());
	
	$sql = "insert into sdb_mall_prop_category (prop_cat_id,offerid,cat_name,ordnum) select cat_id,1,cat_name,1 from ecs_goods_type ";
	mysql_query($sql) or  err(__LINE__." ".mysql_error());
		
}
	
function export_brand(){
	//清空商品品牌表
	$sql = "TRUNCATE sdb_mall_brand";
	mysql_query($sql) or  err(__LINE__." ".mysql_error());
	
	$sql = "insert into sdb_mall_brand (brand_id,offerid,sbid,brand_name) select bid,1,0,brand_name from temp_brand";
	mysql_query($sql) or  err(__LINE__." ".mysql_error());
}
	
function export_goods(){
		//清空商品表
		$sql = "TRUNCATE sdb_mall_goods";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());		
		//清空相关商品表
		$sql = "TRUNCATE sdb_mall_offer_linkgoods";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());		
		//修改onsale字段的属性和ecshop的is_on_sale字段对应。
		$sql = "ALTER TABLE `sdb_mall_goods` CHANGE `onsale` `onsale` TINYINT( 1 ) NOT NULL DEFAULT '1'";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());	
		//修改uptime的字段的属性为varchar，以便插入字符型的数据
		$sql = "ALTER TABLE `sdb_mall_goods` CHANGE `uptime` `uptime` VARCHAR( 50 )  NULL DEFAULT NULL";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());	
		//导商品数据
		$sql = "Insert into sdb_mall_goods(gid,offerid,catid,goods,brand_id,storage,priceintro,price,danwei,intro,memo,smallimgremote,bigimgremote,onsale,uptime) select `产品ID`,1,`产品类型ID`,`产品名称`,`产品品牌ID`,99,`产品市场价`,`产品好抵价`,`产品型号`,`产品简介`,`产品介绍`,`产品图片`,`产品图片`,1,unix_timestamp(`产品上传时间`) from `产品列表` order by `产品上传时间` DESC";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());
		
	
//		$sql = "select gid,goods,memo,intro from sdb_mall_goods";
//		$outrs = mysql_query($sql) or  err(__LINE__." ".mysql_error());	
//		while($row = mysql_fetch_array($outrs)){
//			//处理链接商品
//			$sql = "insert into sdb_mall_offer_linkgoods (offerid,pgid,sgid) select 1,{$row['gid']},id from shop_product where hidden=1 and (keyes like '%{$row['intro']}%') and id<>{$row['gid']} order by id desc"; 
//			mysql_query($sql) or  err(__LINE__." ".mysql_error());	
//
//			ubbtohtml($row['memo']);
//			$row['memo'] = addslashes(stripslashes($row['memo']));
//			$sql = "update sdb_mall_goods set intro='',uptime='".strtotime($row['uptime'])."',memo='".$row['memo']."' where gid='".$row['gid']."'";
//			mysql_query($sql) or  err(__LINE__." ".mysql_error());	
//		}

		//将时间改回数字类型
		$sql="ALTER TABLE `sdb_mall_goods` CHANGE `uptime` `uptime` INT UNSIGNED NULL DEFAULT NULL ";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());
		//将onsale字段改回枚举类型
		$sql = "ALTER TABLE `sdb_mall_goods` CHANGE `onsale` `onsale` ENUM( '0', '1' )  NOT NULL DEFAULT '1'";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());	
}

function export_review(){
		//清空商品评论表
		$sql = "TRUNCATE sdb_mall_comment";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());	

		//修改uptime的字段的属性为varchar，以便插入字符型的数据
		$sql = "ALTER TABLE sdb_mall_comment CHANGE `uptime` `uptime` VARCHAR( 50 )  NULL DEFAULT NULL";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());
		$sql = "ALTER TABLE sdb_mall_comment CHANGE replytime replytime VARCHAR( 50 )  NULL DEFAULT NULL";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());

		$sql = "insert into sdb_mall_comment (offerid,gid,userid,comment_user,question,reply,uptime,replytime,userstate,status) select 1,re_id,0,rusername,rword,reword,rtim,retim,1,1 from review where rsort='shop' order by rid desc ";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());

		//处理用户管理id
		$sql = "select comment_user from sdb_mall_comment group by comment_user";
		$outrs = mysql_query($sql) or  err(__LINE__." ".mysql_error());
		while($row = mysql_fetch_array($outrs)){
			$sql = "select userid from sdb_mall_member where user='".$row['comment_user']."'";
			$inrs = mysql_query($sql) or  err(__LINE__." ".mysql_error());
			$inrow = mysql_fetch_array($inrs);
			if($inrow['userid'] == '') $inrow['userid'] = 0;
			$sql = "update sdb_mall_comment set userid='".$inrow['userid']."' where comment_user='".$row['comment_user']."'";
			mysql_query($sql) or  err(__LINE__." ".mysql_error());
		}

		$sql = "select id,uptime,replytime from sdb_mall_comment";
		$outrs = mysql_query($sql) or  err(__LINE__." ".mysql_error());
		while($row = mysql_fetch_array($outrs)){
			if($row['replytime'] == ''){
				$sql = "update sdb_mall_comment set uptime='".strtotime($row['uptime'])."' where id='".$row['id']."';";
			}else{
				$sql = "update sdb_mall_comment set uptime='".strtotime($row['uptime'])."',replytime='".strtotime($row['replytime'])."' where id='".$row['id']."';";
			}
			mysql_query($sql) or  err(__LINE__." ".mysql_error());
		}


		//将时间改回数字类型
		$sql="ALTER TABLE sdb_mall_comment CHANGE `uptime` `uptime` INT UNSIGNED NULL DEFAULT NULL ";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());
		//将时间改回数字类型
		$sql="ALTER TABLE sdb_mall_comment CHANGE replytime replytime INT UNSIGNED NULL DEFAULT NULL ";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());
}

function export_users() {
		//清空 用户表
		$sql = "TRUNCATE sdb_mall_member";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());
		
		//清空 用户表积分历史表
		$sql = "TRUNCATE sdb_mall_point_history";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());
		
		//处理用户
		$sql = "Insert into sdb_mall_member(offerid,user,password,name,regtime,email,oicq,mov,tel) select 1,`会员帐号`,`会员密码`,`会员姓名`,unix_timestamp(`会员注册时间`),`会员邮箱`,`会员QQ号码`,`会员手机号码`,`会员固定电话` from `会员列表` order by `会员注册时间` DESC";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());

		//生成用户名=>用户id的映射
		$sql = "select userid,user from sdb_mall_member";
		$rs = mysql_query($sql) or  err(__LINE__." ".mysql_error());
		while($row = mysql_fetch_array($rs)){
			$map[$row['user']] = $row['userid'];
		}

		//生成积分历史表，同时计算用户积分
		$sql = "select 1,`会员帐号`,`积分值`,`登记时间`,`登记备注`,'admin' from `会员积分记录`";
		$point = array();
		$rs = mysql_query($sql) or  err(__LINE__." ".mysql_error());
		while($row = mysql_fetch_array($rs)){
			$point[$row[1]] += $row[2];
			$row[1] = $map[$row[1]] == '' ? 0 : $map[$row[1]];
			$row[3] = strtotime($row[3]);
			$ph_insert_sql = "Insert into sdb_mall_point_history(`offerid`,`userid`,`point`,`time`,`describe`,`operator`) values ({$row[0]},{$row[1]},{$row[2]},{$row[3]},'{$row[4]}','{$row[5]}');\r\n";
			mysql_query($ph_insert_sql) or  err(__LINE__." ".mysql_error());
		}

		//更新会员积分
		foreach($point as $key=>$value){
			if ($key == '') continue;
			$member_point_sql = "update sdb_mall_member set point='{$value}' where user='{$key}'";
			mysql_query($member_point_sql) or  err(__LINE__." ".mysql_error());
		}
		
}

function export_article(){		
	//清空文章列表
		$sql = "TRUNCATE sdb_mall_offer_ncon";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());

		
		//转移文章
		$sql = "Insert into sdb_mall_offer_ncon(newsid,offerid,catid,title,con,uptime,ifpub) select newsid,1,7,newsname,newscontent,unix_timestamp(adddate),1 from `新闻` order by adddate DESC";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());
}

function export_orders(){
		//清空订单列表
		$sql = "TRUNCATE sdb_mall_orders";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());
		//修改uptime的字段的属性为varchar，以便插入字符型的数据
		$sql = "ALTER TABLE  sdb_mall_orders CHANGE ordertime ordertime VARCHAR( 50 )  NULL DEFAULT NULL";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());
		//转移订单
		$sql="insert into sdb_mall_orders(orderid,ordertime,offerid,userid,name,addr,zip,tel,email,item_amount,total_amount,memo,ifsk,ordstate,ifinvoice) select order_id,order_time,1,user_id,user_id,address,zipcode,tel,email,goods_amount,money_paid,referer,0,order_status,invoice_no from ecs_order_info";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());
		//整理订单
		$sql = "select orderid,ordertime,ordstate from sdb_mall_orders";
		$rs = mysql_query($sql) or  err(__LINE__." ".mysql_error());
		//更新信息
		while ($row = mysql_fetch_array($rs)){
			//更新日期
			$data['ordertime'] = strtotime($row['ordertime']);
			$sql = "update sdb_mall_orders set ordertime = '{$data['ordertime']}' where orderid='{$row['orderid']}'";
			mysql_query($sql) or  err(__LINE__." ".mysql_error());
			//修改订单状态
			switch($row['ordstate'])			{
					case 1:
						$sql="update sdb_mall_orders set ordstate='0'  where orderid='{$row['orderid']}'";
						break;
					case 2:
						$sql="update sdb_mall_orders set ordstate='1'  where orderid='{$row['orderid']}'";
						break;
					case 3:
						$sql="update sdb_mall_orders set ifsk='1'  where orderid='{$row['orderid']}'";
						break;
					case 4:
						$sql="update sdb_mall_orders set ordstate='3'  where orderid='{$row['orderid']}'";
						break;
					case 5:
						$sql="update sdb_mall_orders set ordstate='4'  where orderid='{$row['orderid']}'";
						break;
			}
			mysql_query($sql) or  err(__LINE__." ".mysql_error());
		}
		//将订单日期改回int类型
		$sql="ALTER TABLE `sdb_mall_orders` CHANGE `ordertime` `ordertime` INT UNSIGNED NULL DEFAULT NULL ";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());	
}

function export_order_items(){
		//清空订单商品列表
		$sql = "TRUNCATE sdb_mall_items";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());
		//转移订单商品
		$sql="insert into sdb_mall_items(id,offerid,orderid,gid,bn,goods,price,nums,sendnum) select rec_id,1,order_id,goods_id,goods_sn,goods_name,goods_price,goods_number,send_number from ecs_order_goods";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());
}



	
function db_fields_local2utf($key, $fields, $table){
	$str_fields = $key.','.implode(',', $fields);

	$sql = "select {$str_fields} from {$table}";
	$rs = mysql_query($sql) or  err(__LINE__." ".mysql_error());
	
	$i=0;		
	while($row = mysql_fetch_array($rs)){
		unset($arr_fields);
		foreach ($fields as $v) {
			//$arr_fields[$v] = addslashes(stripslashes(local2utf($row[$v], 'zh')));
			$arr_fields[$v] = addslashes(stripslashes(iconv("GB2312","UTF-8",$row[$v])));
		}
		$db_string = compile_db_update_string($arr_fields);
		$sql = "UPDATE {$table} SET $db_string WHERE {$key}='".$row[$key]."'";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());	
	}
}	

	
function compile_db_update_string($data) {
	$return_string = "";		
	foreach ($data as $k => $v){
		if($return_string=="")
		$return_string  = $k . "='".$v."'";
		else
		$return_string .= ",".$k . "='".$v."'";

	}		
	return $return_string;
}
	
function local2utf($string,$encoding){
	$lencodingtable = array();
	
	if(!trim($string)) return $string;

 	if(!isset($lencodingtable[$encoding]))
	{
	    $filename=realpath(dirname(__FILE__)."/encode/".$encoding.".txt"); 

		if(!file_exists($filename)||$filename=="")
		{

		   return $string;
		}
		$tmp=file($filename);
		$codetable=array();
		while(list($key,$value)=each($tmp))
			$codetable[hexdec(substr($value,0,6))]=hexdec(substr($value,7,6));
		$lencodingtable[$encoding] = $codetable;
	}
	else
	{
		$codetable = $lencodingtable[$encoding];
	}

	$ret="";
	while(strlen($string)>0) {
	    if( ord(substr($string,0,1)) > 127 ) {
			$t=substr($string,0,2);
			$string=substr($string,2);
			$ret .= u2utf8($codetable[hexdec(bin2hex($t))]);
	    }
	    else 
		{ 
			$t=substr($string,0,1);
			$string=substr($string,1);
			$ret .= u2utf8($t);
	    }
	}
	return $ret;
}

function u2utf8($c) {
	$str='';
	if ($c < 0x80) {
	    $str.=$c;
	    }
	else if ($c < 0x800) {
	    $str.=chr(0xC0 | $c>>6);
	    $str.=chr(0x80 | $c & 0x3F);
	    }
	else if ($c < 0x10000) {
	    $str.=chr(0xE0 | $c>>12);
	    $str.=chr(0x80 | $c>>6 & 0x3F);
		$str.=chr(0x80 | $c & 0x3F);
	}
	else if ($c < 0x200000) {
	    $str.=chr(0xF0 | $c>>18);
	    $str.=chr(0x80 | $c>>12 & 0x3F);
	    $str.=chr(0x80 | $c>>6 & 0x3F);
	    $str.=chr(0x80 | $c & 0x3F);
	}
	return $str;
}

function intString($intvalue,$len){
	$intstr=strval($intvalue);
	//echo strlen($intstr);
	for ($i=1;$i<=$len-strlen($intstr);$i++){
		$tmpstr .= "0";
	}
	return $tmpstr.$intstr;
}


function ubbtohtml(&$string) { 
	$string = preg_replace("/\[(\/?b)\]/i","<\\1>",$string);
	$string = preg_replace("/\[(\/?u)\]/i","<\\1>",$string);
	$string = preg_replace("/\[(\/?i)\]/i","<\\1>",$string);
	$string = preg_replace("/\[align=([a-zA-Z]+)\]/i","<p align=\\1>",$string);
  $string = preg_replace("/\[(\/align)\]/i","<\\1>",$string);
	$string = preg_replace("/\[url=(.+)\](.+)\[\/url\]/i","<a href=\\1>\\2</a>",$string);
	$string = preg_replace("/\[img\](.+)\[\/img\]/i","<img src=\\1 />",$string);
	$string = preg_replace("/\[color=(.+)\](.+)\[\/color\]/i","<font color=\\1>\\2</font>",$string);
	$string = preg_replace("/\[size=([0-9]{1})\](.+)\[\/size\]/i","<font size=\\1>\\2</font>",$string);
	$string = nl2br($string);
} 
	
function scroll($str,$state){
	if($state=="ok"){
		$str="<font color=\"green\">".$str."</font><br>";
	}
	if($state=="fail"){
		$str="<font color=\"red\" >".$str."</font><br>";
	}
	$str.=<<<MSG
	<script language="JavaScript">
		if(document.body.scrollHeight>document.body.clientHeight-30){
			 scroll(0,document.body.scrollHeight-document.body.clientHeight+30);
		}
	</script>
MSG;
	echo $str;
	flush();
}	

function err($imsg){
	$msg="<span style='color:red;font-size:12px'>".$imsg."</span><br>";
	die($msg);
}

?>