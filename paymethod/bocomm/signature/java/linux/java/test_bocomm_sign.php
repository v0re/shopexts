<?php


$message = 'bocomm';


$ini_0 = "ini/B2CMerchant.xml";
$ini_1 = "green3c/B2CMerchant.xml";

echo "load config {$ini_0}<br>";
$merSignMsg = bocomm_sign($ini_0,$message);
var_export($merSignMsg);
echo "<hr>";

echo "load config {$ini_1}<br>";
$merSignMsg = bocomm_sign($ini_1,$message);
var_export($merSignMsg);
echo "<hr>";

function bocomm_sign($config,$message){
	$cmd  = "java  bocomm_sign {$config} {$message}";
	echo $cmd;
	$handle = popen($cmd, 'r');
	while(!feof($handle)){ 
		$merSignMsg .= fread($handle,1024);
	}
	pclose($handle);
	if(preg_match('/<message>(.+)<\/message>/',$merSignMsg,$match)){
		$merSignMsg = $match[1];
		return $merSignMsg;
	}
	return false;
	
}



?>