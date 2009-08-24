<?php
include("config.inc.php");

define('TMPDIR',dirname(__FILE__)."/../home/cache/1");
$csvfile = TMPDIR."/up.csv";
move_uploaded_file($_FILES['upfile']['tmp_name'],$csvfile);
if(!file_exists($csvfile)) die('CSV file not Found!');
$handle = fopen($csvfile,"r");
while ($data = fgetcsv($handle, 1000, ",")) {
	 goodsDownByBN($data[0]); 		
}
fclose($handle);

#
function goodsDownByBN($bn){
	$sql = "UPDATE ".$GLOBALS["_tbpre"]."mall_goods SET shop_iffb='0' WHERE bn='".$bn."'";
	$db = new YQ_SQL;
	$db->query($sql);
	
}

function goodsUpbyBN($bn){
	$sql = "UPDATE ".$GLOBALS["_tbpre"]."mall_goods SET shop_iffb='1' WHERE bn='".$bn."'";
	$db = new YQ_SQL;
	$db->query($sql);
}

 header("Location: http://"
		. $_SERVER['HTTP_HOST']
		.dirname($_SERVER['PHP_SELF']). "/admin_goods_list.php");




?>