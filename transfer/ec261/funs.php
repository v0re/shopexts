<?php

//加载ShopEx的配置文件
include_once("../include/mall_config.php");

mysql_connect($dbHost, $dbUser, $dbPass);
mysql_select_db($dbName);
mysql_query("set names utf8");


function export_cat(){	
		//清空商品分类表
		$sql = "TRUNCATE sdb_mall_offer_pcat";
		mysql_query($sql) or  err(mysql_error());		
		$data = array();		
		//修改prop_cat_id可以为null
		$sql = "ALTER TABLE `sdb_mall_offer_pcat` CHANGE `prop_cat_id` `prop_cat_id` MEDIUMINT( 6 ) UNSIGNED NULL ";
		mysql_query($sql) or  err(mysql_error());		
		//迁移商品大类数据
		$sql = "insert into sdb_mall_offer_pcat (catid,offerid,pid,cat,catord,catiffb,categorydesc,meta_keywords) select cat_id,1,parent_id,cat_name,sort_order,1,cat_desc,keywords from ecs_category ";
		mysql_query($sql) or  err(mysql_error());
		//统一处理catpath
		$sql = "select * from sdb_mall_offer_pcat order by catid";
		$rs = mysql_query($sql) or  err(mysql_error());			
		while ($row = mysql_fetch_array($rs)){
			$data['catpath'][$row['catid']] = ','.intString($row['catord'], 5).','.intString($row['catid'],6);
			if ($row['pid']!=0){	
				$data['catpath'][$row['catid']] = $data['catpath'][$row['pid']].$data['catpath'][$row['catid']];
			}			
			$sql = "update sdb_mall_offer_pcat set catpath='{$data['catpath'][$row['catid']]}' where catid='{$row['catid']}'";
			mysql_query($sql) or  err(mysql_error());		
		}		
		unset($data);
}
	
function export_prop_category(){
	//清空商品分类表
	$sql = "TRUNCATE sdb_mall_prop_category";
	mysql_query($sql) or  err(mysql_error());
	
	$sql = "insert into sdb_mall_prop_category (prop_cat_id,offerid,cat_name,ordnum) select cat_id,1,cat_name,1 from ecs_goods_type ";
	mysql_query($sql) or  err(mysql_error());
		
}
	
function export_brand(){
	//清空商品品牌表
	$sql = "TRUNCATE sdb_mall_brand";
	mysql_query($sql) or  err(mysql_error());
	
	$sql = "insert into sdb_mall_brand (brand_id,offerid,sbid,brand_name,brand_desc,brand_logo,brand_site_url) select brand_id,1,0,brand_name,brand_desc,concat('images/brandlogo/',brand_logo),site_url from ecs_brand ";
	mysql_query($sql) or  err(mysql_error());
}
	
function export_goods(){
		//清空商品表
		$sql = "TRUNCATE sdb_mall_goods";
		mysql_query($sql) or  err(mysql_error());		
		//修改onsale字段的属性和ecshop的is_on_sale字段对应。
		$sql = "ALTER TABLE `sdb_mall_goods` CHANGE `onsale` `onsale` TINYINT( 1 ) NOT NULL DEFAULT '1'";
		mysql_query($sql) or  err(mysql_error());	
		//导商品数据
		$sql = "Insert into sdb_mall_goods(gid,offerid,catid,bn,goods,brand_id,storage,weight,priceintro,price,alarmnum,meta_keywords,intro,memo,smallimgremote,bigimgremote,multi_image,ifobject,onsale,uptime,offer_ord,new2,hot2,recommand2,last_modified) select goods_id,1,cat_id,goods_sn,goods_name,brand_id,goods_number,goods_weight,market_price,shop_price,warn_number,keywords,goods_brief,goods_desc,goods_thumb,goods_img,original_img,is_real,is_on_sale,add_time,sort_order,is_new,is_hot,is_promote,last_update from ecs_goods where is_delete='0'";
		mysql_query($sql) or  err(mysql_error());
		
		//将onsale字段改回枚举类型
		$sql = "ALTER TABLE `sdb_mall_goods` CHANGE `onsale` `onsale` ENUM( '0', '1' )  NOT NULL DEFAULT '1'";
		mysql_query($sql) or  err(mysql_error());	
}

function export_users(){
		//清空 用户表
		$sql = "TRUNCATE sdb_mall_member";
		mysql_query($sql) or  err(mysql_error());
		//清空 用户等级表
		$sql = "TRUNCATE sdb_mall_member_level";
		mysql_query($sql) or  err(mysql_error());

		$sql = "insert into sdb_mall_member_level(offerid,levelid,name,point,discount,recsts) select  1,rank_id,rank_name,max_points,discount,show_price from ecs_user_rank";
		mysql_query($sql) or  err(mysql_error());


		//处理用户
		$sql = "Insert into sdb_mall_member(userid,offerid,user,name,password,sex,birthday,province,city,addr,tel,mov,oicq,zip,regtime,email,ip,point,point_history,level,advance) select u.user_id,1,u.user_name,ud.consignee,u.password,(u.sex + 1),u.birthday,province.region_name,city.region_name,ud.address,u.home_phone,u.mobile_phone,u.qq,ud.zipcode,u.reg_time,u.email,u.last_ip,u.pay_points,u.rank_points,u.user_rank,u.user_money  FROM `ecs_users` as u  left join ecs_user_address as ud on u.address_id=ud.address_id left join ecs_region as province on ud.province = province.region_id  left join ecs_region as city on ud.city = city.region_id";
		mysql_query($sql) or  err(mysql_error());
}

function export_article(){		
	//清空文章列表
		$sql = "TRUNCATE sdb_mall_offer_ncon";
		mysql_query($sql) or  err(mysql_error());
		//清空文章类别表
		$sql = "TRUNCATE sdb_mall_offer_ncat";
		mysql_query($sql) or  err(mysql_error());
		
		//添加文章类别	
		$sql = "Insert into sdb_mall_offer_ncat(catid,offerid,cat) select cat_id,1,cat_name from ecs_article_cat";
		mysql_query($sql) or  err(mysql_error());	
		//更新文章类别调用id
		$sql = "select catid from sdb_mall_offer_ncat";
		$rs = mysql_query($sql) or  err(mysql_error());
		$pid = 1;
		while($row = mysql_fetch_array($rs)){
			$sql = "update sdb_mall_offer_ncat set pid='$pid' where catid='{$row['catid']}'";
			mysql_query($sql) or  err(mysql_error());
			$pid++;
		}		
		//转移文章
		$sql = "Insert into sdb_mall_offer_ncon(newsid,offerid,catid,title,con,uptime,ifpub) select article_id,1,cat_id,title,content,add_time,is_open from ecs_article";
		mysql_query($sql) or  err(mysql_error());
}

function export_orders(){
		//清空订单列表
		$sql = "TRUNCATE sdb_mall_orders";
		mysql_query($sql) or  err(mysql_error());
		//修改uptime的字段的属性为varchar，以便插入字符型的数据
		$sql = "ALTER TABLE  sdb_mall_orders CHANGE ordertime ordertime VARCHAR( 50 )  NULL DEFAULT NULL";
		mysql_query($sql) or  err(mysql_error());
		//转移订单
		$sql="insert into sdb_mall_orders(orderid,ordertime,offerid,userid,name,addr,zip,tel,email,item_amount,total_amount,memo,ifsk,ordstate,ifinvoice) select order_id,add_time,1,user_id,consignee,address,zipcode,tel,email,goods_amount,money_paid,referer,0,order_status,invoice_no from ecs_order_info";
		mysql_query($sql) or  err(mysql_error());
		//整理订单
		$sql = "select orderid,ordertime,ordstate from sdb_mall_orders";
		$rs = mysql_query($sql) or  err(mysql_error());

		//将订单日期改回int类型
		$sql="ALTER TABLE `sdb_mall_orders` CHANGE `ordertime` `ordertime` INT UNSIGNED NULL DEFAULT NULL ";
		mysql_query($sql) or  err(mysql_error());	
}

function export_order_items(){
		//清空订单商品列表
		$sql = "TRUNCATE sdb_mall_items";
		mysql_query($sql) or  err(mysql_error());
		//转移订单商品
		$sql="insert into sdb_mall_items(id,offerid,orderid,gid,bn,goods,price,nums,sendnum) select rec_id,1,order_id,goods_id,goods_sn,goods_name,goods_price,goods_number,send_number from ecs_order_goods";
		mysql_query($sql) or  err(mysql_error());
}



	
function db_fields_local2utf($key, $fields, $table){
	$str_fields = $key.','.implode(',', $fields);

	$sql = "select {$str_fields} from {$table}";
	$rs = mysql_query($sql) or  err(mysql_error());
	
	$i=0;		
	while($row = mysql_fetch_array($rs)){
		unset($arr_fields);
		foreach ($fields as $v) {
			//$arr_fields[$v] = addslashes(stripslashes(local2utf($row[$v], 'zh')));
			$arr_fields[$v] = addslashes(stripslashes(iconv("GB2312","UTF-8",$row[$v])));
		}
		$db_string = compile_db_update_string($arr_fields);
		$sql = "UPDATE {$table} SET $db_string WHERE {$key}='".$row[$key]."'";
		mysql_query($sql) or  err(mysql_error());	
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