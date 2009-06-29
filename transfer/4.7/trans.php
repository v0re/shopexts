<?php 
set_time_limit(0);
error_reporting(E_ALL^E_NOTICE);
?>
<html>
<head>
<title>shopex安装</title>
</head>
<body style="font-size:16px;">
<?php
	require_once("class.func.php");
	$func=new func();
	// 处理商品分类及分类属性
	$func->export_cat();
	$func->db->scroll("处理分类完毕","ok");
	// 处理商品品牌
	$func->export_brand();
	$func->db->scroll("处理商品品牌完毕","ok");
	// 处理商品
	$func->export_goods();
	$func->db->scroll("处理商品完毕","ok");
	//处理注册用户
	$func->export_users();
	$func->db->scroll("处理用户完毕","ok");
	//处理文章
	$func->export_article();
	$func->db->scroll("处理文章完毕","ok");
	//处理订单
	//$func->export_orders();
	//回收资源
	$func->gc();
?>
</body>
</html>