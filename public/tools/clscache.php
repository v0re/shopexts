<?php
if(isset($_POST['submit']))
{
	require_once("./include/mall_config.php");
	$link=mysql_connect($dbHost,$dbUser,$dbPass);
	mysql_select_db($dbName);
	mysql_query("set names utf8");
	$sql = '';
	if($_POST['datacache'] == 'on'){
		$datsql .= "update sdb_mall_offer_setup set value='0' where keyword='shop_data_cache'";
		if(mysql_query($datsql)){
			echo "<hr><br>关闭数据缓存成功<br>";
		}else{
			echo mysql_error();
		}
	}else{
		$datsql .= "update sdb_mall_offer_setup set value='1' where keyword='shop_data_cache'";
		if(mysql_query($datsql)){
			echo "<hr><br>开启数据缓存成功<br>";
		}else{
			echo mysql_error();
		}
	}
	if($_POST['tmplcache'] == 'on'){
		$tplsql .= "update sdb_mall_offer_setup set value='0' where keyword='shop_tpl_cache'";
		if(mysql_query($tplsql)){
			echo "<br>关闭模板缓存成功<hr>";
		}else{
			echo mysql_error();
		}
	}else{
		$tplsql .= "update sdb_mall_offer_setup set value='1' where keyword='shop_tpl_cache'";
				if(mysql_query($datsql)){
			echo "<br>开启模板缓存成功<hr>";
		}else{
			echo mysql_error();
		}
	}

	mysql_close($link);
	
}
?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<body>
<form method="post">
关闭数据缓存<input type="checkbox" name="datacache">
<br>
关闭模板缓存<input type="checkbox" name="tmplcache">
<br>
<input type="submit" name="submit">
</form>
</body>
</html>