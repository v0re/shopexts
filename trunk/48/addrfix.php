<?php

include('config/config.php');
mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
mysql_select_db(DB_NAME);

$sql = "select * from sdb_member_addrs where area<>''";
$rs = mysql_query($sql);
while($row = mysql_fetch_array($rs) ){

	$addr = $row['addr'];
	//$str = 'mainland:云南/昆明市/官渡区:2991';
	$area = $row['area'];
	$aTmp = explode(':',$area);
	$aTmp = explode('/',$aTmp[1]);

	foreach($aTmp as $value){
		if(strstr($addr,$value)){
			break;
		}else{
			$find = false;	
		}
	}

	if(!$find){
		$rent[] = $row;
	}
}

if(count($rent) > 0){
	error_log(var_export($rent,true),3,__FILE__.".log");
}

?>