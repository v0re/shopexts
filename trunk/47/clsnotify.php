<?php
	if(isset($_POST['submit'])){
		require_once("include/mall_config.php");
		$link=mysql_connect($dbHost,$dbUser,$dbPass);
		mysql_select_db($dbName);
		mysql_query("set names utf8");
		$query = "truncate table ".$_tbpre."mall_goodsnotify";
		$rs=mysql_query($query,$link)		or die(mysql_error());
		mysql_close($link);
	}
?>
<html>
<body>
<form method="post">
<input type="submit" name="submit">
</form>
</body>
</html>