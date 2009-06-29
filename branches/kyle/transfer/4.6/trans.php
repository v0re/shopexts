<?php 
header("Content-Type: text/html; charset=UTF-8");
set_time_limit(0);
error_reporting(E_ALL^E_NOTICE);
?>
<html>
<head>
<title>shopex安装</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
</head>
<body style="font-size:9px;">
<?php
	require_once("common.func.php");
	$func=new func();
	$func->clear_tables();
	// 处理商品分类及分类属性
	$func->export_cat();
	// 处理商品
	$func->export_goods();
	//处理注册用户
	$func->export_users();
	//处理文章
	$func->export_article();
	//处理订单
	//$func->export_orders();
	//回收资源
	$func->gc();
?>
</body>
</html>