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
require_once("config.php");
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

$do = new transfer();

//$do->goodsGimages();			//商品图片gimages
//$do->goodsTag();				//商品标签
//$do->goodsCategory();			//商品分类
//$do->goodsBrand();			//商品品牌
//$do->goodsType();				//商品类型
//$do->goodsDetail();			//商品详细
//$do->goodsKeywords();			//商品关键词
//$do->relaGoods();				//相关商品
//$do->goodsImage();			//商品图片
//$do->goodsComment();			//商品评论
//$do->rebuilidCatPath();		//修正分类路径
//$do->memberLevel();			//会员等级
//$do->memberDetail();			//会员详细
//$do->memberCustomPro();			//自定义注册项
//$do->convertMessage();		//消息
//$do->convertArticles();		//文章
//$do->convertdelivery();		//配送方式
//$do->convertOrders();			//订单


class transfer{

		//转换商品标签函数 function goodsTag()
		function goodsTag()
		{
			//清空标签相关表
			$aTable = array(
				'sdb_tags',
				'sdb_tag_rel'
			);
			$this->truncate($aTable);

			////////////////////////////请修改这个区域///////////////////////////////
			$aMap = array(
				'tag_id'				=>	'tags_id',
				'tag_name'				=>	'tags_name',
				'tag_type'				=>	1,
				'rel_count'				=>	'tags_num'
			);
			$to = 'sdb_tags';
			$from = 'lebi_tags';
			$where = '1=1';
			//////////////////////////////////////////////////////////

			$insertsql = $this->getInsertSQL($aMap,$to,$from,$where);
			$rs = $this->dbQ($insertsql);
			echo "商品标签转换完毕!<br>";
			return true;
		}

		//转换商品图片函数 function goodsImages()
		function goodsGimages()
		{
			//清空sdb_gimages表
			$this->truncate('sdb_gimages');

			////////////////////////////请修改这个区域///////////////////////////////
			$aMap = array(
				'gimage_id'			=>	'image_default',
				'goods_id'			=>	'goods_id',
				'source'			=>	'big_pic',
				'src_size_width'	=>	300,
				'src_size_height'	=>	300,
				'small'				=>	'small_pic',
				'big'				=>	'big_pic',
				'thumbnail'			=>	'thumbnail_pic',
				//'up_time'			=>	'UNIX_TIMESTAMP("2009-06-18 17:40:00")'
				'up_time'			=>	'uptime'
			);
			$to = 'sdb_gimages';
			$from = 'sdb_goods';
			$where = '1';
			//////////////////////////////////////////////////////////

			$insertsql = $this->getInsertSQL($aMap,$to,$from,$where);
			$rs = $this->dbQ($insertsql);
			echo "商品图片转换完毕!<br>";
			return true;
		}

		//转换商品类型 function goodsType()
		function goodsType(){
		//清空商品类型表
		//$this->truncate('sdb_goods_type');

		////////////////////////////请修改这个区域///////////////////////////////
		$aMap = array(
			'type_id'				=>	'sortsid',
			'name'					=>	'sorts',
			'schema_id'				=>	'1'
		);
		$to = 'sdb_goods_type';
		$from = 'sorts';
		$where = '1=1';
		//////////////////////////////////////////////////////////

		$insertsql = $this->getInsertSQL($aMap,$to,$from,$where);
		$rs = $this->dbQ($insertsql);
		echo "商品类型转换完毕!<br>";
		return true;
	}

	function goodsCategory(){
		//清空商品分类表
		$this->dbQ('TRUNCATE sdb_goods_cat',false);

		///////////////////////请修改这个区域///////////////////////////
		$aMap = array(
			'cat_id'					=>	'ID+189',
			'parent_id'					=>	0,
			'supplier_id'				=>	'',
			'supplier_cat_id'			=>	'',
			'cat_path'					=>	'',
			'is_leaf'					=>	true,
			'type_id'					=>	7,
			'cat_name'					=>	'bclass',
			//'disabled'					=>	'false',
			'p_order'					=>	'',
			'goods_count'				=>	0,
			'tabs'						=>	'',
			'finder'					=>	'',
			'addon'						=>	'',
			'child_count'				=>	0
		);
		$to = 'sdb_goods_cat';
		$from = 'bclass';
		$where = '1=1';
		//////////////////////////////////////////////////

		$insertsql = $this->getInsertSQL($aMap,$to,$from,$where);
		$rs = $this->dbQ($insertsql);
		echo "商品父分类转换完毕!<br>";

		/////////////////////////请修改这个区域////////////////////////
		$aMap = array(
			'cat_id'				=>	'ID',
			'parent_id'			=>	'bclassid+189',
			'cat_path'			=>	'',
			'is_leaf'				=>	true,
			'type_id'				=>	7,
			'cat_name'			=>	'sclass',
			'disabled'			=>	'',
			'p_order'				=>	'',
			'goods_count'		=>	0,
			'tabs'					=>	'',
			'finder'				=>	'',
			'addon'					=>	'',
			'child_count'		=>	0
		);
		$to = 'sdb_goods_cat';
		$from = 'sclass';
		$where = '1=1';
		//////////////////////////////////////////////////

		$insertsql = $this->getInsertSQL($aMap,$to,$from,$where);
		$rs = $this->dbQ($insertsql);
		echo "商品子分类转换完毕!<br>";

		//修正catpath
		$this->rebuilidCatPath();
	}


	function goodsBrand(){
		//清空商品品牌表
		$this->truncate('sdb_brand');

		////////////////////////////请修改这个区域///////////////////////////////
		$aMap = array(
			'brand_id'				=>	'MarkID',
			'brand_name'			=>	'MarkName',
			'brand_url'				=>	'',
			'brand_desc'			=>	'',
			'brand_logo'			=>	'',
			'brand_keywords'		=>	'',
			'ordernum'				=>	'MarkNum'
		);
		$to = 'sdb_brand';
		$from = 'timesmark';
		$where = '1=1';
		//////////////////////////////////////////////////////////

		$insertsql = $this->getInsertSQL($aMap,$to,$from,$where);
		$rs = $this->dbQ($insertsql);
		echo "商品品牌转换完毕!<br>";
		return true;
	}


	function goodsDetail(){
		//清空商品相关表
		$aTable = array(
			'sdb_goods',
			'sdb_goods_keywords',
			'sdb_goods_lv_price',
			'sdb_goods_memo',
			'sdb_goods_rate',
			'sdb_goods_spec_index',
			'sdb_goods_virtual_cat',
			'sdb_products'
		);
		$this->truncate($aTable);
		/////////////////////////////////////////插入商品表数据/////////////////////////////////
		$aMap = array(
			'goods_id'			=>	'ID',			//商品ID
			'cat_id'			=>	'sclassid',		//商品分类
			'type_id'			=>	7,				//通用商品都是1
			'goods_type'		=>	'',
			'brand_id'			=>	'',				//商品品牌ID
			'brand'				=>	'',				//商品品牌名
			'image_default'		=>	'ID',				//默认图片
			'udfimg'			=>	'',				//是否自定义缩略图
			'thumbnail_pic'		=>	'concat("images/goods/20090808/",img)',				//缩略图
			'small_pic'			=>	'concat("images/goods/20090808/",img_b)',			//小图
			'big_pic'			=>	'concat("images/goods/20090808/",img_b)',			//大图
			'image_file'		=>	'',				//图片文件
			'brief'				=>	'',				//简介
			'intro'				=>	'content',		//商品详细介绍
			'mktprice'			=>	'price_jin',	//市场价
			'price'				=>	'price',		//销售价
			'name'				=>	'pname',		//商品名称
			//'bn'				=>	'concat(\'BN-\',substring_index(rand()*10000,\'.\',1),\'-\',substring_index(rand()*10000,\'.\',1))',
			'bn'				=>	'myitem',		//货号
			'marketable'		=>	'',		//是否上架
			'weight'			=>	'weight',		//重量
			'unit'				=>	'danwei',		//单位
			'store'				=>	'kucun',		//库存
			'score_setting'		=>	'',				//积分设置
			'score'				=>	'',				//积分
			'spec'				=>	'',
			'pdt_desc'			=>	'',
			'params'			=>	'',
			'uptime'			=>	'unix_timestamp(addtime)',	//更新时间
			'downtime'			=>	'',				//下架时间
			'last_modify'		=>	'',				//最后修改时间
			'disabled'			=>	'',				//是否下架
			'notify_num'		=>	'',
			'rank'				=>	'',
			'rank_count'		=>	'', //临时借用，稍后清空
			'comments_count'	=>	'',
			'view_w_count'		=>	'',				//周访问次数
			'view_count'		=>	'hit',			//访问次数
			'buy_count'			=>	'',				//购买次数
			'buy_w_count'		=>	'',				//周购买次数
			'count_stat'		=>	'',
			'p_21'				=>	'guige',		//类型属性1
			'p_22'				=>	'zhuangxiang',	//类型属性2
			'p_23'				=>	'caizhi',		//类型属性3
			'p_order'			=>	'',		//排序
			'd_order'			=>	''
		);
		$to = 'sdb_goods';
		$from = 'pro_noduplication';
		$where = '1=1 group by ID ';
		//////////////////////////////////////////////////////////////////////////////////////////
		$insertsql = $this->getInsertSQL($aMap,$to,$from,$where);
		$rs = $this->dbQ($insertsql);
		/////////////////////////////////////////货品表也要插入数据///////////////////////////////
		$aMap = array(
			'product_id'		=>	'',
			'goods_id'			=>	'goods_id',
			'barcode'			=>	'',
			'title'				=>	'',
			'bn'				=>	'bn',
			'price'				=>	'price',
			'cost'				=>	'',
			'name'				=>	'name',
			'weight'			=>	'',
			'unit'				=>	'',
			'store'				=>	'store',
			'freez'				=>	'',
			'pdt_desc'			=>	'',
			'props'				=>	'',
			'uptime'			=>	'uptime',
			'last_modify'		=>	'',
		);
		$to = 'sdb_products';
		$from = 'sdb_goods';
		$where = ' 1=1 ORDER BY uptime DESC';
		//////////////////////////////////////////////////////////////////////////////////////////
		$insertsql = $this->getInsertSQL($aMap,$to,$from,$where);
		$rs = $this->dbQ($insertsql);

		echo "商品转换完毕!<br>";

		//更新品牌id号
		$sql = "SELECT brand_id,brand_name FROM sdb_brand";
		$rs = $this->dbQ($sql,false);
		while($row = mysql_fetch_array($rs)){
			$sql = "UPDATE sdb_goods SET brand_id='".$row['brand_id']."' WHERE brand='".$row['brand_name']."'";
			$this->dbQ($sql,false);
		}
		//替换详细介绍中的ubb表情
		$sql = "SELECT goods_id,intro,rank_count FROM sdb_goods";
		$rs = $this->dbQ($sql,false);
		while($row = mysql_fetch_array($rs)){
			$this->ubbtohtml($row['intro']);
			$row['intro'] = addslashes(stripslashes($row['intro']));
			//
			if($row['rank_count'] == 1){
				$marketable = '\'true\'';
			}else{
				$marketable = '\'false\'';
			}
			$sql = "UPDATE sdb_goods SET intro='".$row['intro']."',marketable=".$marketable.",rank_count='0' WHERE goods_id='".$row['goods_id']."'";
			$this->dbQ($sql,false);
		}
		/*
		//更新原商品编号
		$sql = "SELECT gid,goods FROM `sp_goods` WHERE goods <> '' GROUP BY `gid`";
		$rs = $this->dbQ($sql,false);
		while($row = mysql_fetch_array($rs)){
			$sql = "UPDATE sdb_goods SET bn='".$row['name']."' WHERE goods_id='".$row['id']."'";
			$this->dbQ($sql,false);
		}*/
	}
	//处理相关商品
	function relaGoods(){
		$this->truncate('sdb_goods_rate');

		$sql = "SELECT goods_id,brief FROM sdb_goods";
		$rs = $this->dbQ($sql,false);
		while($row = mysql_fetch_array($rs)){
			if($row['brief'] != ''){
				$sql = "INSERT INTO sdb_goods_rate (goods_1,goods_2,manual,rate) select {$row['goods_id']},id,'left',100 FROM product WHERE  keyes='".$row['brief']."' AND id<>'".$row['goods_id']."' GROUP BY id ORDER BY id desc";
				$this->dbQ($sql,false);
				//清空临时占用的brief字段
				$sql = "UPDATE sdb_goods SET brief='' WHERE goods_id='".$row['goods_id']."'";
				$this->dbQ($sql,false);
			}
		}

		echo "相关商品处理完毕";

		return true;
	}


	function goodsImage(){
		$sql = "SELECT goods_id,small_pic,big_pic FROM sdb_goods";
		$rs = $this->dbQ($sql,false);
		while($row = mysql_fetch_array($rs)){
			$rent = array();
			$this->makeImage($row['goods_id'],$row['small_pic'],$row['big_pic'],$thumb,$rent);
			$sql = "UPDATE sdb_goods SET thumbnail_pic='{$rent[thumbnail_pic]}',udfimg={$rent[udfimg]},image_default='{$rent[image_default]}',image_file='{$rent[image_file]}' WHERE goods_id='".$row['goods_id']."'";
			$this->dbQ($sql,false);
		}
	}

	//商品关键词
	function goodsKeywords()
	{
		$aMap = array(
			'goods_id'		=>	'bookid',
			'keyword'		=>	'guanjian'
			);

		$to = 'sdb_goods_keywords';
		$from = 'shop_books';
		$where = 'guanjian is not null';
		//////////////////////////////////////////////////////////////////////////////////////////
		$insertsql = $this->getInsertSQL($aMap,$to,$from,$where);
		$rs = $this->dbQ($insertsql);

		echo "商品关键词转换完毕!<br>";
	}

	function goodsComment(){
		//清空商品评论表
		$this->truncate('sdb_comments');

		////////////////////////////////////////////////////
		$aMap = array(
			'comment_id'			=>	'rid',
			'for_comment_id'	=>	'',	//null表示为主题，comment_id表示为comment_id主题的回复（包括管理员的）
			'goods_id'				=>	're_id',
			'object_type'			=>	'\'discuss\'',	//枚举类型ask:,discuss:评论,buy:
			'author_id'				=>	'',
			'author'					=>	'rusername',
			'levelname'				=>	'',
			'contact'					=>	'remail',
			'mem_read_status'	=>	'',
			'adm_read_status'	=>	'',
			'time'						=>	'unix_timestamp(rtim)',
			'lastreply'				=>	'',
			'reply_name'			=>	'',
			'title'						=>	'',
			'comment'					=>	'rword',
			'ip'							=>	'',
			'display'					=>	'\'true\''
		);
		$to = 'sdb_comments';
		$from = 'review';
		$where = 'rsort=\'shop\' ORDER BY rid DESC';
		///////////////////////////////////////////////////////
		$insertsql = $this->getInsertSQL($aMap,$to,$from,$where);
		$rs = $this->dbQ($insertsql);
		//生成回复记录
		$sql = "SELECT rid FROM review WHERE reword<>''";
		$rs = $this->dbQ($sql);
		while($row = mysql_fetch_array($rs)){
			$aMap['comment_id']	=	'';
			$aMap['author']	=	'';
			$aMap['for_comment_id']	=	$row['rid'];
			$aMap['contact']	=	'';
			$aMap['adm_read_status'] = '\'true\'';
			$aMap['time'] = '';
			$aMap['lastreply'] = 'unix_timestamp(rtim)';
			$aMap['comment'] = 'reword';
			///////////////////////////////////////////////////////
			$to = 'sdb_comments';
			$from = 'review';
			$where = ' rid=\''.$row[rid].'\' ORDER BY rid DESC';
			///////////////////////////////////////////////////////
			$insertsql = $this->getInsertSQL($aMap,$to,$from,$where);
			$this->dbQ($insertsql,false);

		}

		echo "商品评论转换完毕!<br>";

		return true;
	}

	/*
	function memberLevel(){
		$this->truncate('sdb_member_lv');

		$sql = "INSERT into sdb_member_lv (member_lv_id,name,dis_count,default_lv,lv_type) values('1','注册会员','1.00','1','retail')";

		$this->dbQ($sql);

	}
	*/

	//会员自定义注册项
	function memberCustomPro()
	{
		$this->truncate('sdb_member_mattrvalue');
		////////////////////////////////////////////////////
		$aMap = array(
			'attr_id'			=>	11,			//会员自定义注册项ID
			'member_id'			=>	'id',		//会员ID
			'value'				=>	'qq'		//会员自定义注册项值
		);
		$to = 'sdb_member_mattrvalue';
		$from = 'userlist';
		$where = '1=1';
		///////////////////////////////////////////////////////
		$insertsql = $this->getInsertSQL($aMap,$to,$from,$where);
		$rs = $this->dbQ($insertsql);
		echo "会员自定义注册项转换完毕!<br>";
	}

	//会员详细
	function memberDetail(){
		//清空会员相关表
		$aTable = array(
			'sdb_members',
			'sdb_member_addrs',
			//'sdb_member_attr',
			'sdb_member_coupon',
			'sdb_member_dealer',
			'sdb_member_mattrvalue'
		);
		$this->truncate($aTable);

		////////////////////////////////////////////////////
		$aMap = array(
			'member_id'				=>	'id',				//会员ID
			'member_lv_id'			=>	1,					//在memberLevel方法中指定
			'uname'					=>	'username',			//会员用户名
			'name'					=>	'contact',			//会员名
			'lastname'				=>	'',
			'firstname'				=>	'',
			'password'				=>	'password',			//会员密码
			'area'					=>	'',					//会员地区
			'mobile'				=>	'mobile',			//移动电话
			'tel'					=>	'tel',				//固定电话
			'email'					=>	'email',			//邮箱
			'zip'					=>	'zipcode',			//邮编
			'addr'					=>	'address',			//住址
			'province'				=>	'',
			'city'					=>	'',
			'order_num'				=>	'',
			'b_year'				=>	'',					//生日年份
			'b_month'				=>	'',					//生日月份
			'b_day'					=>	'',					//生日当天
			//'sex'					=>	'sex',				//性别
			'advance'				=>	'',
			'point_history'			=>	'',					//积分历史
			'point'					=>	'',					//积分
			'reg_ip'				=>	'',					//注册IP
			'regtime'				=>	'unix_timestamp(regtime)',	//注册时间
			'pw_question'			=>	'question',			//安全问题
			'pw_answer'				=>	'answer'			//安全问题答案
			//'disabled'				=>	'closed'			//是否禁用
		);
		$to = 'sdb_members';
		$from = 'userlist';
		$where = '1=1';
		///////////////////////////////////////////////////////
		$insertsql = $this->getInsertSQL($aMap,$to,$from,$where);
		$rs = $this->dbQ($insertsql);
		echo "会员转换完毕!<br>";

	}

	//处理留言
	function convertMessage()
	{
		//清空留言
		$this->truncate('sdb_message');

		///////////////////////////////////////////
		$aMap = array(
			'msg_id'	=>	'messid',
			'msg_from'	=>	'messusername',
			'subject'	=>	'messsubject',
			'message'	=>	'messcontent',
			'email'	=>	'messemail',
			'tel'	=>	'messtel',
			'msg_ip'	=>	'messip',
			'date_line'	=>	'unix_timestamp(messdtm)',
			'folder'	=>	'',
			'is_sec'	=>	'',
			'del_status'	=>	'',
			'msg_type'	=>	''
			);
		$to = 'sdb_message';
		$from = 'mess';
		$where = '1=1';
		///////////////////////////////////////
		$insertsql = $this->getInsertSql($aMap,$to,$from,$where);
		$rs = $this->dbQ($insertsql);
		echo '留言转换完毕';
	}

	//处理文章
	function convertArticles()
	{
		$this->truncate('sdb_articles');
		$aMap = array(
			'node_id'	=>	'100',
			'title'		=>	'newsname',
			'content'	=>	'newscontent',
			'uptime'	=>	'unix_timestamp(adddate)'
		);
		$to = 'sdb_articles';
		$from  = 'news';
		$where = '1=1';
		///////////////////////////////////////
		$insertsql = $this->getInsertSql($aMap,$to,$from,$where);
		$rs = $this->dbQ($insertsql);
		echo '文章转换完毕';
	}

	//处理配送方式
	function convertdelivery()
	{
		//清空配送方式相关表
		$aTable = array(
			'sdb_dly_type',
			'sdb_dly_h_area'
			);
		$this->truncate($aTable);
		$aMap = array(
			'dt_id'		=>	'deliveryid',
			'dt_name'	=>	'subject',
			'price'		=>	1,
			'ordernum'	=>	'deliveryidorder'
			);
		$to = 'sdb_dly_type';
		$from = 'delivery';
		$where = 'methord=0';
		///////////////////////////////////////
		$insertsql = $this->getInsertSql($aMap,$to,$from,$where);
		$rs = $this->dbQ($insertsql);
		echo '配送方式转换完毕';
	}


	//处理订单
	function convertOrders()
	{
		//清空订单表
		$this->truncate('sdb_orders');
		$aMap = array(
			'order_id'		=>	'b.actionid',
			'member_id'		=>	'a.UserID',
			'memo'			=>	'b.comments',
			'ship_email'		=>	'b.useremail',
			'ship_mobile'		=>	'b.usertel',
			'ship_name'		=>	'b.receipt',
			'score_g'		=>	'b.score',
			);
		$to = 'sdb_orders';
		$from = 'dv_user as a inner join orders as b';
		$where = 'a.UserName = b.username';
		///////////////////////////////////////
		$insertsql = $this->getInsertSql($aMap,$to,$from,$where);
		$rs = $this->dbQ($insertsql);
		echo '订单转换完毕';
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


	function rebuilidCatPath(){
		//修正cat_path
		//要根据cat_id进行排序，保证子分类要出现在母分类之后
		$sql = "select cat_id,parent_id,cat_path from sdb_goods_cat order by cat_id ASC";
		$rs = $this->dbQ($sql);
		while($row = mysql_fetch_array($rs)){
			//母分类

			if($row['parent_id'] == 0){
				$data['cat_path'][$row['cat_id']] = ',';
			}else{
				$data['cat_path'][$row['cat_id']] = $row['parent_id'].',';
				if($data['cat_path'][$row['parent_id']] != ',')
				$data['cat_path'][$row['cat_id']] = $data['cat_path'][$row['parent_id']].$data['cat_path'][$row['cat_id']];
			}
			$sql = "update sdb_goods_cat set cat_path='{$data['cat_path'][$row['cat_id']]}' where cat_id='{$row['cat_id']}'";
			$this->dbQ($sql,false);
		}
	}

	function makeImage($goods_id,$small,$big,$thumb,&$row){
		//获取图片的前缀
		$prefix = 'goods/'.$this->getHashDir($goods_id)."/";
		$destination = "../images/$prefix";
		if(!file_exists($destination)){
			$this->mkdirp($destination);
		}
		//处理列表图
		//获取后缀
		$picpostfix = $this->getImageExt($small);
		if($row['small_pic'] == $row['big_pic'] && false){
			$picname = 'gpic_'.md5($goods_id).'_thumbnail'.$picpostfix;
			if(!$this->imageResample($small,$destination.$picname)){
				//缩小失败就直接拷贝
				$this->imageCopy($small,$destination.$picname);
			}
			$row['thumbnail_pic'] = 'images/'.$prefix.$picname."|".$prefix.$picname."|fs_storage";
			//打标识
			$row['udfimg'] = 'false';
		}else{
			$picname = 'gupic_'.md5($goods_id).$picpostfix;
			if(!$this->imageResample($small,$destination.$picname)){
				//缩小失败就直接拷贝
				$this->imageCopy($small,$destination.$picname);
			}
			$row['thumbnail_pic'] = 'images/'.$prefix.$picname."|".$prefix.$picname."|fs_storage";
			//打标识
			$row['udfimg'] = 'true';
		}
		//处理详细页图
		//获取后缀
		$picpostfix = $this->getImageExt($big);
		$picname = 'gpic_'.md5($row['goods_id']).$picpostfix;
		$this->imageCopy($big,$destination.$picname);
		$row['image_default'] = 'images/'.$prefix.$picname."|".$prefix.$picname."|fs_storage";
		//处理相册
		$row['image_file'] = $row['image_default'];
	}
	//根据商品id获取哈希值
	function getHashDir($goods_id){
		$goods_id = intval($goods_id);
		$goods_id = $goods_id % 10000;
		$pref = intval($goods_id / 100);
		$post = $goods_id % 100;
		//位数不够补0
		if($pref < 10){
			$pref = "0".$pref;
		}
		//位数不够补0
		if($post < 10){
			$post = "0".$post;
		}

		return $pref."/".$post;
	}
	//实现mkdir -p，自动创建不存在的父目录
	function mkdirp($dir){
		$aDir = explode("/",$dir);
		if(is_array($aDir)){
			$nowpos = getcwd();
			foreach($aDir as $v){
				if(!file_exists($v)){
					@mkdir($v,0755);
				}
				@chdir($v);
			}
			@chdir($nowpos);
		}
	}
	//拷贝图片
	function imageCopy($source,$destination){
		if(file_exists($destination)){
			unlink($destination);
		}
		copy($source,$destination);
	}
	//等比缩小图片
	function imageResample($source,$destination){
		if(!file_exists($source)) return false;
		//设置最大高宽
		$width = 180;
		$height = 135;

		//计算新图片的大小
		$aInfo = getimagesize($source);
		$width_orig = $aInfo[0];
		$height_orig = $aInfo[1];
		//if($width_orig < $width and $height_orig < $height){
		//	return false;
		//}
		//获取文件类型
		$mime = $aInfo['mime'];
		$mime = explode('/',$mime);
		$mime = $mime[1];
		//检测getimagesize调用是否成功
		if($mime == '') return false;

		if ($width && ($width_orig < $height_orig)) {
				$width = ($height / $height_orig) * $width_orig;
		} else {
				$height = ($width / $width_orig) * $height_orig;
		}

		//$image_s = imagecreatefromjpeg($source);
		//动态调用函数减少判断结构
		$call = "imagecreatefrom".$mime;
		$image_s = $call($source);

		$image_t = imagecreatetruecolor($width, $height);

		imagecopyresampled($image_t, $image_s, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
		//保存生成的新图片
		if(file_exists($destination)){
			unlink($destination);
		}
		error_log($width."|".$height."|".$width_orig."|".$height_orig."|".$destination."\r\n",3,__FILE__.".log");

		//imagejpeg($image_t, $destination, 100);
		//动态调用函数减少判断结构
		$call = "image".$mime;
		$call($image_t, $destination, 100);

		return true;
	}
	//获取图片文件后缀
	function getImageExt($srcFile){
		if(!file_exists($srcFile) or !function_exists('getimagesize'))
			return substr($srcFile,strrpos($srcFile,'.'));

		$info = getimagesize($srcFile);

		switch($info[2]){
			case 1: //gif
				$ext = '.gif';
			break;
			case 2: //jpg
				$ext = '.jpg';
			break;
			case 3: //png
				$ext = '.png';
			break;
			case 6: //bmp
				$ext = '.bmp';
			break;
			case 15: //wbmp
				$ext = '.wbmp';
			break;
			case 16: //xbm
				$ext = '.xbm';
			break;
			default:
				$ext = substr($srcFile,strrpos($srcFile,'.'));
		}

			return $ext;
		}

		//生成标准商品编号
    function genGoodsBn($goods_id){
			$str = $goods_id.time().rand(1,10000);
			return 'BN-'.strtoupper(substr(md5($str),0,8));
    }

}
?>

</body>
</html>