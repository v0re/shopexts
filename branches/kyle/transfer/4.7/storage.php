<?php
	
	if(isset($_GET['submit']))
	{
		if(!is_numeric($_GET['storage']))
			die("输入的不是数值");
		require("include/mall_config.php");
		set_time_limit(0);
		$link=mysql_connect($dbHost,$dbUser,$dbPass); 
		mysql_select_db($dbName);	
		$query="update mall_goods  set storage='".intval($_GET['storage'])."'";
		mysql_query($query) or die(mysql_error());
		print "成功将所有库存修改为".$_GET['storage'];
		print "<meta http-equiv=refresh content=3;URL=index.php>";
	}
?>
<form >
<input type="text" name="storage">
<input type="submit" name="submit" >
</form>