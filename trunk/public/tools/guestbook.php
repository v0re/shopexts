<?php

require("config/config.php");
$link=mysql_connect(DB_HOST,DB_USER,DB_PASSWORD); 
mysql_select_db(DB_NAME);

mysql_query("set names utf8");

$sql = "select member_id,uname from sdb_members";
$rs = mysql_query($sql);
while($row = mysql_fetch_array($rs)){
	$aMap[$row['member_id']] = $row['uname'];
}


$sql = "select from_id from sdb_message group by from_id";
$rs = mysql_query($sql);
while($row = mysql_fetch_array($rs)){
	$uname = $aMap[$row['from_id']]; 
	if($uname != ''){
		$sql = "update sdb_message set msg_from='".$uname."' where from_id='".$row['from_id']."'";
		mysql_query($sql);
	}
}

echo "all done!";

?>