<?php
	
	if(isset($_GET['submit']))
	{
		if(!is_numeric($_GET['storage']))
			die("����Ĳ�����ֵ");
		require("include/mall_config.php");
		set_time_limit(0);
		$link=mysql_connect($dbHost,$dbUser,$dbPass); 
		mysql_select_db($dbName);	
		$query="update mall_goods  set storage='".intval($_GET['storage'])."'";
		mysql_query($query) or die(mysql_error());
		print "�ɹ������п���޸�Ϊ".$_GET['storage'];
		print "<meta http-equiv=refresh content=3;URL=index.php>";
	}
?>
<form >
<input type="text" name="storage">
<input type="submit" name="submit" >
</form>