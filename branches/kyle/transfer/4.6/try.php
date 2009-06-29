<?php
	if(isset($_GET['submit']))
	{
		require_once("include/mall_config.php");
		$link=mysql_connect($dbHost,$dbUser,$dbPass);
		mysql_select_db($dbName);
		//print "mysql ok";
		//$query="select cat from ".$_tbpre."mall_offer_pcat";
		$query=trim($_GET['sql']);
		$rs=mysql_query($query,$link);
		while($row=mysql_fetch_array($rs,MYSQL_ASSOC))
		{
			print_r($row);
		}
		mysql_close($link);
	}
?>
<html>
<body>
<form>
select≤È—Ø<input type="checkbox" name="haveret" >
<br>
<textarea name="sql" rows=9 cols=100></textarea>
<br>
<input type="submit" name="submit">
</form>
</body>
</html>