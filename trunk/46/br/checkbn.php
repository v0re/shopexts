<?php
    
//测试mall_config.php是否存在
if(!file_exists('../include/mall_config.php')) echo('mall_config.php不存在');
include_once('../include/mall_config.php');

$link = mysql_connect($dbHost, $dbUser, $dbPass,true)		or die("Could not connect : " . mysql_error($link)); 
mysql_select_db($dbName,$link) or die("Could not select database");
if(mysql_get_server_info() > '5.0.1') mysql_query("SET sql_mode=''",$link);
if(defined("MYSQL_CHARSET_NAME"))	mysql_query("SET NAMES '".MYSQL_CHARSET_NAME."'",$link);

$flag = 0;

$sql = "SELECT bn FROM {$GLOBALS['_tbpre']}mall_goods GROUP BY bn HAVING (count( * )) >1";
$rs = mysql_query($sql);
if(mysql_num_rows($rs) > 0){
	echo("下面商品编号不唯一<ol>");
	while($row = mysql_fetch_array($rs)){
		if($row['bn'] == '') continue;
		echo("<li>".$row['bn']);
	}
	$flag++;
}

$sql = "SELECT gid,goods from {$GLOBALS['_tbpre']}mall_goods where bn=''";
$rs = mysql_query($sql);
if(mysql_num_rows($rs) > 0){
	echo("</ol><br>下面的商品编号为空<ol>");
	while($row = mysql_fetch_array($rs)){
		echo("<li>商品id是".$row['gid']);
	}
	$flag++;
}

echo("</ol><hr>");
if($flag != 0){
	echo("<font color=red>请登陆网店后台重新设置商品编号，只有商品编号唯一的情况下才能进行下一步操作</font>");
}else{
	echo("<font color=green>商品编号都是唯一的</font>");
}

?>
