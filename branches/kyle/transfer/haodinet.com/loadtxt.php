<?php

require_once("../include/mall_config.php");
$link=mysql_connect($dbHost,$dbUser,$dbPass);
mysql_select_db($dbName);
mysql_query("set names utf8");

$txtfile = dirname(__FILE__)."/brand.txt";
$txtfile = str_replace("\\","/",$txtfile);

$sql = "TRUNCATE temp_brand";
mysql_query($sql) or  err(__LINE__." ".mysql_error());	

$sql = 'LOAD DATA LOCAL INFILE \''.$txtfile.'\' INTO TABLE temp_brand FIELDS TERMINATED BY \',\' ENCLOSED BY \'"\' LINES TERMINATED BY \'\n\';';
$rs = mysql_query($sql) or die(mysql_error());

echo "done!";
?>
