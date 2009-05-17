<?php
require_once("include/mall_config.php");
$link = mysql_connect($dbHost,$dbUser,$dbPass) or die(mysql_errno());
mysql_select_db($dbName);

$row=array($_tbpre."op_sessions",$_tbpre."sessions");
echo "<table>";
for($i=0;$i<count($row);$i++)
{
	$rss=mysql_query("repair table ".$row[$i]);	
	$rnt=mysql_fetch_array($rss);
	echo "<tr><td>".$rnt[0]."</td><td>".$rnt[3]."</td></tr>";	
}
echo "</table>";
mysql_close($link);
	
?>

