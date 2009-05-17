<?php


require_once("../include/mall_config.php");
$link=mysql_connect($dbHost,$dbUser,$dbPass);
mysql_select_db($dbName);
mysql_query("set names utf8");

$sql = "select user from {$_tbpre}mall_offer ";
$rs = mysql_query($sql);
while($row = mysql_fetch_array($rs)){
	echo $row[0];
	echo "<br>";
}

if(isset($_POST['submit']))	{
	echo $sql = "update ".$_tbpre."mall_offer set password='".md5(trim($_POST['password']))."' where user='".trim($_POST['username'])."'";	
	echo "<br>";
	$rs=mysql_query($sql) or die(mysql_error());
	echo "<font color=red>change ".$_POST[username]." password to ".$_POST[password]."</font>";
}

	mysql_close($link);
?>


<html>
<body>
<form method="post">

”√ªß√˚£∫<input type=text name=username>
√‹¬Î£∫<input type=text name =password>

<input type="submit" name="submit">
</form>
</body>
</html>