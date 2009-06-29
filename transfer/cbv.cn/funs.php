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
		$sql = "insert into sdb_mall_offer_pcat (catid,offerid,pid,cat,catiffb,catord) SELECT typeID,1,0,type_name,1,xu FROM `type_info` where shopID=205 and CHARACTER_LENGTH(typeNO) between 4 and 5";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());
	
		//迁移商品小类数据
		$sql = "insert into sdb_mall_offer_pcat (catid,offerid,pid,cat,catiffb,catord) SELECT typeID,1,SUBSTRING(typeNO,1,4),type_name,1,xu FROM `type_info` where shopID=205 and CHARACTER_LENGTH(typeNO) > 5 ";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());

		$sql = "select typeID,typeNO from type_info where shopID=205 and CHARACTER_LENGTH(typeNO) between 4 and 5";
		$rs = mysql_query($sql);
		while($row = mysql_fetch_array($rs)){
			$sql = "update sdb_mall_offer_pcat set pid='".$row['typeID']."' where pid='".$row['typeNO']."'";
			mysql_query($sql);
		}

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

	$sql = "insert into sdb_mall_brand (offerid,brand_name) SELECT 1,brand FROM `goods_info`  where shopID='205' and brand<>'' and `show`=-1 group by brand ";
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


		//导商品数据
		$sql = "Insert into sdb_mall_goods(gid,offerid,catid,goods,brand_id,bn,storage,priceintro,basicprice,price,danwei,weight,memo,smallimgremote,bigimgremote,onsale,uptime,attr1,recommand2,hot2,tejia2,new2) select goodsID,1,typeNO,goods_name,0,goodsNO,mark_leave,common_price,member_price,member_price,units,0,concat(goods_desc,'<br/>',REPLACE(goods_explain,'【','<br/>【'),'<br/><br/>',after_service),concat('pic/',small_pic),concat('pic/',big_pic),1,unix_timestamp(create_time),brand from goods_info where `show`=-1 order by create_time DESC";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());
		
		$sql = "select typeID,typeNO from type_info where shopID=205";
		$rs = mysql_query($sql);
		while($row = mysql_fetch_array($rs)){
			$sql = "update sdb_mall_goods set catid='".$row['typeID']."' where catid='".$row['typeNO']."'";
			mysql_query($sql);
		}
		//处理品牌
		$sql = "select brand_id,brand_name from sdb_mall_brand";
		$rs = mysql_query($sql);
		while($row = mysql_fetch_array($rs)){
			$sql = "update sdb_mall_goods set brand_id='".$row['brand_id']."' where attr1='".$row['brand_name']."'";
			mysql_query($sql);
		}
		$sql = "update sdb_mall_goods set attr1=''";
		mysql_query($sql);
		
		//将onsale字段改回枚举类型
		$sql = "ALTER TABLE `sdb_mall_goods` CHANGE `onsale` `onsale` ENUM( '0', '1' )  NOT NULL DEFAULT '1'";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());	
}

function export_review(){
		//清空商品评论表
		$sql = "TRUNCATE sdb_mall_comment";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());	

		$sql = "insert into sdb_mall_comment (offerid,gid,userid,comment_user,question,reply,uptime,replytime,userstate,status) select 1,goodsID,'1660','sunny2503@hotmail.com',content,answer,unix_timestamp(create_time),unix_timestamp(answer_time),1,1 from goods_comment where shopID='205'";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());

//		$sql = "select userid,user from sdb_mall_member";
//		$rs = mysql_query($sql);
//		while($row = mysql_fetch_array($rs)){
//			$sql = "update sdb_mall_comment set comment_user='".$row['user']."' where userid='".$row['userid']."'";
//			mysql_query($sql);
//		}

}

function export_users() {
		//清空会员等级列表
		$sql = "TRUNCATE sdb_mall_member_level";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());	
		$sql = "insert into sdb_mall_member_level (offerid,levelid,name,point,discount,recsts) values ('1','1','注册会员','0','1','1') ";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());	
		//清空会员表
		$sql = "TRUNCATE `sdb_mall_member`";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());	
		 
		//处理用户
		$sql = "Insert into sdb_mall_member(userid,offerid,user,password,name,regtime,sex,email,oicq,mov,tel,zip,addr,level,advance) select managerid,1,admin_name,LOWER(admin_password),real_name,unix_timestamp(create_time),sex,admin_email,oicq,mobile,phone,zipcode,address,1,admin_count  from manager_info  order by create_time DESC";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());

}

function export_article(){		
	//清空文章列表
		$sql = "TRUNCATE sdb_mall_offer_ncon";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());

		
		//转移文章
		$sql = "Insert into sdb_mall_offer_ncon(newsid,offerid,catid,title,con,uptime,ifpub) select newsID,1,1,news_title,news_desc,unix_timestamp(create_time),1 from center_news_info order by create_time DESC";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());
}

function export_orders(){
		//清空订单列表
		$sql = "TRUNCATE sdb_mall_orders";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());

		//转移订单
		$sql="insert into sdb_mall_orders(orderid,ordertime,offerid,userid,name,addr,zip,tel,mobile,email,item_amount,total_amount,memo,ifsk,ordstate,paytime,paymoney,weight,sendtime,area,ttype,delivery,ptype,payment,paycur,freight) select orderno,dtime,1,memberid,s_name,s_addr,s_postcode,s_tel,s_mobi,s_email,goodstotal,totaloof,items,1,3,paytime,paytotal,totalweight,yuntime,yunzoneid,'0',yuntype,payid,paytype,payhb,yunfei from mpshop_order";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());		

}

function export_order_items(){
		//清空订单商品列表
		$sql = "TRUNCATE sdb_mall_items";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());
		//转移订单商品
		$sql="insert into sdb_mall_items(id,offerid,userid,orderid,gid,bn,goods,price,nums,oof,sendnum,status) select id,1,memberid,orderid,gid,bn,goods,price,nums,jine,'0','0' from mpshop_order_items";
		mysql_query($sql) or  err(__LINE__." ".mysql_error());

		//修正orderid
		$sql = "select orderid,orderno from mpshop_order";
		$rs = mysql_query($sql) or  err(__LINE__." ".mysql_error());
		while($row = mysql_fetch_array($rs)){
			$sql = "update sdb_mall_items set orderid='".$row['orderno']."' where orderid='".$row['orderid']."'";
			mysql_query($sql) or  err(__LINE__." ".mysql_error());
		}
}


function export_delivery(){
	//清空配送地区表
	$sql = "TRUNCATE sdb_mall_offer_deliverarea";
	mysql_query($sql) or  err(__LINE__." ".mysql_error());
	//转移配送地区
	$sql = "insert into sdb_mall_offer_deliverarea (areaid,offerid,name) select id,'1',zone from mpshop_yun_zone";
	mysql_query($sql) or  err(__LINE__." ".mysql_error());
	//清空配送方式表
	$sql = "TRUNCATE sdb_mall_offer_t";
	mysql_query($sql) or  err(__LINE__." ".mysql_error());
	//转移配送地区
	$sql = "insert into sdb_mall_offer_t (id,offerid,tmethod,tdetail,tprice,type) select id,'1',yunname ,'',yunfei,'1' from mpshop_yun";
	mysql_query($sql) or  err(__LINE__." ".mysql_error());

}

function export_ptype(){
	//清空支付方式表
	$sql = "TRUNCATE sdb_mall_offer_p";
	mysql_query($sql) or  err(__LINE__." ".mysql_error());
	//转移配送地区
	$sql = "insert into sdb_mall_offer_p (id,offerid,payment,pdetail,ptype,pmerid,pkey,curpay) select id,'1',pcenter ,intro,'',pcenteruser,pcenterkey,'@CNY' from mpshop_paycenter";
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

function make_memprice(){

	$sqlfile = dirname(__FILE__)."/price.sql";
	$sqlfile = str_replace("\\","/",$sqlfile);

	//清空价格表
	$sql = "truncate sdb_mall_member_price";
	mysql_query($sql) or die(mysql_error());


	$sql = "select name,levelid,point,discount from sdb_mall_member_level";
	$otRs = mysql_query($sql);
	while ($otRow = mysql_fetch_array($otRs)){
		//取出第一个元素
		$levelid = intval($otRow['levelid']);
		$tmpDiscunt = $otRow['discount'];

		$sql = "select gid,price from sdb_mall_goods";
		$rs = mysql_query($sql) or die(mysql_error());	
		$instSql = "";
		while ($row = mysql_fetch_array($rs)){
			$instSql .= "1\t".$levelid."\t".$row['gid']."\t".($row['price'] * $tmpDiscunt)."\r\n";
		}
		error_log($instSql,3,$sqlfile);
		scroll("等级 ".$otRow['name']." 数据生成完毕",'ok');
	}

	$sql = "LOAD DATA LOCAL INFILE '".$sqlfile."' INTO TABLE sdb_mall_member_price(offerid,levelid,gid,price);";
	mysql_query($sql) or die(mysql_error());
	@unlink($sqlfile);
}

?>