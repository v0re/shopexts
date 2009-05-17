<?php
require_once("include/mall_config.php");
$link = mysql_connect($dbHost,$dbUser,$dbPass) or die(mysql_errno());
mysql_select_db($dbName);
$rs=mysql_query("show tables") or die(mysql_error());
echo "<table>";
while($row = mysql_fetch_array($rs,MYSQL_NUM))
{
	$rss = mysql_query("repair table ".$row[0]);	
	mysql_query("optimize table ".$row[0]);
	$rnt = mysql_fetch_array($rss);
	echo "<tr><td>".$rnt[0]."</td><td>".$rnt[3]."</td></tr>";	
}
echo "</table>";
mysql_close($link);
	
?>

