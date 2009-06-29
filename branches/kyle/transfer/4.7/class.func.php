<?php

class func
{

	var $db;

	var $prefix;

	function  func()
	{
		//加载ShopEx的配置文件
		require_once("../include/mall_config.php");
		//加载数据库操作类
		require_once("class.dba.php");
		//生成实例
		$this->db=new dba($dbHost,$dbUser,$dbPass,$dbName);
	}

	function export_cat()
	{
	
		//清空商品分类表
		$sql = "TRUNCATE sdb_mall_offer_pcat";
		$this->db->query($sql);
		
		$data = array();
		
		//修改prop_cat_id可以为null
		$sql = "ALTER TABLE `sdb_mall_offer_pcat` CHANGE `prop_cat_id` `prop_cat_id` MEDIUMINT( 6 ) UNSIGNED NULL ";
		$this->db->query($sql);
		
		//迁移商品大类数据
		$sql = "insert into sdb_mall_offer_pcat (catid,offerid,pid,cat,catord,catiffb,categorydesc,meta_keywords) select cat_id,1,parent_id,cat_name,sort_order,1,cat_desc,keywords from ecs_category ";
		$this->db->query($sql);
		//统一处理catpath
		$sql = "select * from sdb_mall_offer_pcat order by catid";
		$this->db->query($sql);			
		while ($row = $this->db->row($this->db->rs)) 
		{
			$data['catpath'][$row['catid']] = ','.$this->intString($row['catord'], 5).','.$this->intString($row['catid'],6);
			if ($row['pid']!=0) 
			{	
				$data['catpath'][$row['catid']] = $data['catpath'][$row['pid']].$data['catpath'][$row['catid']];
			}			
			$sql = "update sdb_mall_offer_pcat set catpath='{$data['catpath'][$row['catid']]}' where catid='{$row['catid']}'";
			$this->db->innerquery($sql);		
		}		
		unset($data);
	}
	
	function export_prop_category()
	{
		//清空商品分类表
		$sql = "TRUNCATE sdb_mall_prop_category";
		$this->db->query($sql);
		
		$sql = "insert into sdb_mall_prop_category (prop_cat_id,offerid,cat_name,ordnum) select cat_id,1,cat_name,1 from ecs_goods_type ";
		$this->db->query($sql);
		
	}
	
	function export_brand()
	{
		//清空商品品牌表
		$sql = "TRUNCATE sdb_mall_brand";
		$this->db->query($sql);
		
		$sql = "insert into sdb_mall_brand (brand_id,offerid,sbid,brand_name,brand_desc,brand_logo,brand_site_url) select brand_id,1,0,brand_name,brand_desc,brand_logo,site_url from ecs_brand ";
		$this->db->query($sql);
	}
	
	function export_goods()
	{
		//清空商品表
		$sql = "TRUNCATE sdb_mall_goods";
		$this->db->query($sql);		
		//修改onsale字段的属性和ecshop的is_on_sale字段对应。
		$sql = "ALTER TABLE `sdb_mall_goods` CHANGE `onsale` `onsale` TINYINT( 1 ) NOT NULL DEFAULT '1'";
		$this->db->query($sql);	
		//导商品数据
		$sql = "Insert into sdb_mall_goods(gid,offerid,catid,bn,goods,brand_id,storage,weight,priceintro,price,alarmnum,meta_keywords,memo,smallimgremote,bigimgremote,multi_image,ifobject,onsale,linkgoods,uptime,offer_ord,new2,hot2,recommand2,last_modified) select goods_id,1,cat_id,goods_sn,goods_name,brand_id,goods_number,goods_weight,market_price,shop_price,warn_number,keywords,goods_desc,goods_thumb,goods_img,original_img,is_real,is_on_sale,is_linked,add_time,sort_order,is_new,is_hot,is_promote,last_update from ecs_goods";
		$this->db->query($sql);
		//将onsale字段改回枚举类型
		$sql = "ALTER TABLE `sdb_mall_goods` CHANGE `onsale` `onsale` ENUM( '0', '1' )  NOT NULL DEFAULT '1'";
		$this->db->query($sql);	
	}

	function export_users()
	{
		//清空 用户表
		$sql = "TRUNCATE sdb_mall_member";
		$this->db->query($sql);
		//修改regtime的字段的属性为varchar，以便插入字符型的数据
		$sql = "ALTER TABLE `sdb_mall_member` CHANGE `regtime` `regtime` VARCHAR( 50 )  NULL DEFAULT NULL";
		$this->db->query($sql);
		//处理用户
		$sql = "Insert into sdb_mall_member(userid,offerid,user,password,name,sex,birthday,province,addr,zip,tel,mov,oicq,regtime,level,pw_question,point) select userid,1,username,password,realname,sex,concat(year,'-',month,'-',day),tpl_prov,address,postcode,usertel,mobile,userqq, adddate,vip,quesion,score from user";
		$this->db->query($sql);
		//更新用户注册日期	
		$sql = "select userid,regtime from sdb_mall_member";
		$this->db->query($sql);
		while ($row = $this->db->row($this->db->rs)) 
		{
			$data['regtime'] = strtotime($row['regtime']);
			$sql = "update sdb_mall_member set regtime = '{$data['regtime']}' where userid='{$row['userid']}'";
			$this->db->innerquery($sql);
		}
		//将regtime改回原来的int类型
		$sql="ALTER TABLE `sdb_mall_member` CHANGE `regtime` `regtime` INT UNSIGNED NULL DEFAULT NULL ";
		$this->db->query($sql);
	

	}

	function export_article()
	{
		
		//清空文章列表
		$sql = "TRUNCATE sdb_mall_offer_ncon";
		$this->db->query($sql);
		//清空文章类别表
		$sql = "TRUNCATE sdb_mall_offer_ncat";
		$this->db->query($sql);
		
		//添加文章类别	
		$sql = "Insert into sdb_mall_offer_ncat(catid,offerid,cat) select cat_id,1,cat_name from ecs_article_cat";
		$this->db->query($sql);	
		//更新文章类别调用id
		$sql = "select catid from sdb_mall_offer_ncat";
		$this->db->query($sql);
		$pid = 1;
		while($row = $this->db->row($this->db->rs))
		{
			$sql = "update sdb_mall_offer_ncat set pid='$pid' where catid='{$row['catid']}'";
			$this->db->innerquery($sql);
			$pid++;
		}		
		//转移文章
		$sql = "Insert into sdb_mall_offer_ncon(newsid,offerid,catid,title,con,uptime,ifpub) select article_id,1,cat_id,title,content,add_time,is_open from ecs_article";
		$this->db->query($sql);
	}

	function export_orders()
	{
		$data=array();
		//修改uptime的字段的属性为varchar，以便插入字符型的数据
		$sql = "ALTER TABLE  sdb_mall_orders CHANGE ordertime ordertime VARCHAR( 50 )  NULL DEFAULT NULL";
		$this->db->query($sql);
		//转移订单
		$sql="insert into sdb_mall_orders(orderid,ordertime,offerid,userid,name,addr,zip,tel,email,item_amount,total_amount,memo,getpoint,ifsk,ordstate) select actionid,actiondate,1,userid,username,address,postcode,usertel,useremail,finalprice,paid,comments,score,0,state from orders";
		$this->db->query($sql);
		//整理订单
		$sql = "select orderid,ordertime,ordstate from sdb_mall_orders";
		$this->db->query($sql);
		//更新信息
		while ($row = $this->db->row($this->db->rs))
		{
			//更新日期
			$data['ordertime'] = strtotime($row['ordertime']);
			$sql = "update sdb_mall_orders set ordertime = '{$data['ordertime']}' where orderid='{$row['orderid']}'";
			$this->db->innerquery($sql);
			//修改订单状态
			switch($row['ordstate'])
			{
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
			$this->db->innerquery($sql);
		}
		//将订单日期改回int类型
		$sql="ALTER TABLE `sdb_mall_orders` CHANGE `ordertime` `ordertime` INT UNSIGNED NULL DEFAULT NULL ";
		$this->db->query($sql);		
		$this->db->scroll("处理订单完毕","ok");

	}



	
	function translate_local2utf($key, $fields, $table)
	{
		$str_fields = $key.','.implode(',', $fields);
	
		$str_query = "select {$str_fields} from {$table}";
		$result = mysql_query( $str_query );
		report_mysql_errors($str_query);
		$i=0;
		while ($row = mysql_fetch_array($result)) 
		{
		//	print_r( $row);
			unset($arr_fields);
			foreach ($fields as $v) {
	//			if ($row[$v]!='')
				$arr_fields[$v] = addslashes(stripslashes(local2utf($row[$v], 'zh')));
			}
	//		echo "<pre>";
	//		print_r($arr_fields);
	//		echo "<pre>";
			$db_string = compile_db_update_string($arr_fields);
			$str_query = "UPDATE {$table} SET $db_string WHERE {$key}='".$row[$key]."'";
	//		rptout($str_query);
			mysql_query( $str_query );
			report_mysql_errors($str_query);
			$i++;
			if ($i%60==0) rptout($i);
		}
		rptout($i);
	}
	
	function intString($intvalue,$len){
		$intstr=strval($intvalue);
		//echo strlen($intstr);
		for ($i=1;$i<=$len-strlen($intstr);$i++){
			$tmpstr .= "0";
		}
		return $tmpstr.$intstr;
	}
	
	function compile_db_update_string($data) {
		
		$return_string = "";
		
		foreach ($data as $k => $v)
		{
			//$v = preg_replace( "/'/", "\\'", $v );
			if($return_string=="")
			$return_string  = $k . "='".$v."'";
			else
			$return_string .= ",".$k . "='".$v."'";
	
		}
		
		//$return_string = preg_replace( "/,$/" , "" , $return_string );
		
		return $return_string;
	}
	
	function local2utf($string,$encoding)
	{
		global $lencodingtable;
	//				echo ;
		if(!trim($string)) return $string;
	
	 	if(!isset($lencodingtable[$encoding]))
		{
		    $filename=realpath(dirname(__FILE__)."/coding/".$encoding.".txt"); 
	
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
	
	function gc()
	{
		$this->db->gc();
		unset($this->db);		
	}

}
?>