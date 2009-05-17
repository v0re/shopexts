<?php 
set_time_limit(0);
error_reporting(E_ALL^E_NOTICE);
?>
<html>
<head>
<title>ShopEx 会员价格重置工具</title>
<META HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
</head>
<body style="font-size:12px;">

<?php

if($_REQUEST['action'] == 'start'){
	require_once("../include/mall_config.php");
	$link=mysql_connect($dbHost,$dbUser,$dbPass);
	mysql_select_db($dbName);
	mysql_query("set names utf8");
	$sqlfile = dirname(__FILE__)."/price.sql";
	$sqlfile = str_replace("\\","/",$sqlfile);
	
	if($_REQUEST['member'] == on){
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
			echo "等级 ".$otRow['name']." 数据生成完毕<br>";
			flush();
		}

		$sql = "LOAD DATA LOCAL INFILE '".$sqlfile."' INTO TABLE sdb_mall_member_price(offerid,levelid,gid,price);";
		mysql_query($sql) or die(mysql_error());
		@unlink($sqlfile);
		echo "<hr>会员价格数据已经全部导入数据库<br>";
		flush();
	}else{
		echo $_REQUEST['member'];
	}
		
}
	
	
?>

<form method="POST">
	更新会员价<input type=checkbox name='member'><br>
	更新商品价<input type=checkbox name='goods'><br>
	<input type=hidden name='action' value='start'>
	<input type=submit value='submint'>
</form>

</body>
</html>