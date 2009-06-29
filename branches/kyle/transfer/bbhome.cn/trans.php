<?php 
set_time_limit(0);
error_reporting(E_ALL^E_NOTICE);
?>
<html>
<head>
<title>shopex安装</title>
<META HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
</head>
<body style="font-size:16px;">
<?php
	require_once("funs.php");
	// 处理商品分类及分类属性
	export_cat();
	scroll("处理分类完毕","ok");
	// 处理商品品牌
	export_brand();
	scroll("处理商品品牌完毕","ok");
	// 处理商品
	export_goods();
	scroll("处理商品完毕","ok");
	//处理注册用户
	export_users();
	scroll("处理用户完毕","ok");
	//export_review();
	//scroll("处理商品评论完毕","ok");
	//处理文章
	//export_article();
	//scroll("处理文章完毕","ok");
	//处理订单
	//export_orders();
	//scroll("处理订单完毕","ok");
	//处理订单商品
	//export_order_items();
	//scroll("处理订单商品完毕","ok");
	//处理友情连接
	//export_friendlink();
	//scroll("处理友情链接完毕","ok");
	//处理留言
	//export_guestbook();
	//scroll("处理留言完毕","ok");
?>
</body>
</html>