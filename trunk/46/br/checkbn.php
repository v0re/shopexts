<?php
    
//����mall_config.php�Ƿ����
if(!file_exists('../include/mall_config.php')) echo('mall_config.php������');
include_once('../include/mall_config.php');

$link = mysql_connect($dbHost, $dbUser, $dbPass,true)		or die("Could not connect : " . mysql_error($link)); 
mysql_select_db($dbName,$link) or die("Could not select database");
if(mysql_get_server_info() > '5.0.1') mysql_query("SET sql_mode=''",$link);
if(defined("MYSQL_CHARSET_NAME"))	mysql_query("SET NAMES '".MYSQL_CHARSET_NAME."'",$link);

$flag = 0;

$sql = "SELECT bn FROM {$GLOBALS['_tbpre']}mall_goods GROUP BY bn HAVING (count( * )) >1";
$rs = mysql_query($sql);
if(mysql_num_rows($rs) > 0){
	echo("������Ʒ��Ų�Ψһ<ol>");
	while($row = mysql_fetch_array($rs)){
		if($row['bn'] == '') continue;
		echo("<li>".$row['bn']);
	}
	$flag++;
}

$sql = "SELECT gid,goods from {$GLOBALS['_tbpre']}mall_goods where bn=''";
$rs = mysql_query($sql);
if(mysql_num_rows($rs) > 0){
	echo("</ol><br>�������Ʒ���Ϊ��<ol>");
	while($row = mysql_fetch_array($rs)){
		echo("<li>��Ʒid��".$row['gid']);
	}
	$flag++;
}

echo("</ol><hr>");
if($flag != 0){
	echo("<font color=red>���½�����̨����������Ʒ��ţ�ֻ����Ʒ���Ψһ������²��ܽ�����һ������</font>");
}else{
	echo("<font color=green>��Ʒ��Ŷ���Ψһ��</font>");
}

?>
