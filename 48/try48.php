<?php
	if(isset($_POST['submit']))
	{
		require_once("./config/config.php");

		$link=mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
		mysql_select_db(DB_NAME);
		mysql_query("set names utf8");
		//print "mysql ok";
		//$query="select cat from ".$_tbpre."mall_offer_pcat";
		$query=trim($_POST['sql']);
		if($query==' ') die("查询语句为空");
		if($query=="fq")
		{
			$array=file("data.sql");
			foreach($array as $value)
			{
				print $value."<br>";
				mysql_query($value);
			}
			die("导入文件完毕");
		}
		if(get_magic_quotes_gpc()){
			$query = stripslashes($query);
		}
		$rs=mysql_query($query,$link)
		or die(mysql_error());
		if($_POST['haveret'])
		{
			while($row=mysql_fetch_array($rs,MYSQL_ASSOC))
			{
				print_r($row);
				print "<br>";
			}
		}
			mysql_close($link);
		
	}
?>
<html>
<body>
<form method="post">
select查询<input type="checkbox" name="haveret" checked >
<br>
<textarea name="sql" rows=9 cols=100><?php  echo $query ?></textarea>
<br>
<input type="submit" name="submit">
</form>
</body>
</html>