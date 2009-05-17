<html>
<head>
<META HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
</head>
<body style="font-size:16px;">

<?php
include "config/config.php";

mysql_connect(DB_HOST,DB_USER,DB_PASSWORD) or die(mysql_error());
mysql_select_db(DB_NAME);
$sql = "select is_physical from sdb_goods_type where type_id='1'";
$rs = mysql_query($sql) or die(mysql_error());
$row = mysql_fetch_array($rs);
if($row['is_physical'] != 1){
	echo "通用类型的设置不正确，需要修正......<br>";
	$sql = "update sdb_goods_type set is_physical='1' where type_id='1'";
	if(mysql_query($sql)){
		echo "修正类型成功";
	}else{
		echo "修正类型失败";
	}
}else{
	echo "一切正常，不需要任何修改!<br>";
}

?>
</body>
</html>