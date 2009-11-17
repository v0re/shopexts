<?php

$realpath = dirname(__FILE__);
define('CONFIG',$realpath."/ini/B2CMerchant.xml");

$aConfig = array(
'ApiURL'=>'https://ebanktest.95559.com.cn/corporbank/NsTrans',
'OrderURL'=>'https://pbanktest.95559.com.cn/netpay/MerPayB2C',
'EnableLog'=>'true',
'LogPath'=>'',
'SettlementFilePath'=>'',
'MerchantCertFile'=>'',
'MerchantCertPassword'=>'',
'RootCertFile'=>'',
);
$aConfig['LogPath'] = $realpath."/log";
$aConfig['SettlementFilePath'] = $realpath."/settlement";
$aConfig['MerchantCertFile'] = $realpath."/cert/301310063009501.PFX";
$aConfig['MerchantCertPassword'] = '12345678';
$aConfig['RootCertFile'] = $realpath."/cert/test_root.cer";

$message = 'bocomm';

makeConfig(CONFIG,$aConfig);
$merSignMsg = bocomm_sign(CONFIG,$message);

print $merSignMsg;

echo "<hr>all done";

function bocomm_sign($config,$message){
	$cmd = "java bocomm_sign {$config}  {$message}";
	$handle = popen($cmd, 'r');
	while(!feof($handle)){ 
		$merSignMsg .= fread($handle,1024);
	}
	pclose($handle);
	if(preg_match('/<message>(.+)<\/message>/',$merSignMsg,$match)){
		$merSignMsg = $match[1];
	}
	var_export($merSignMsg);
}


function makeConfig($file,$arr){
	$xmlstring = arrayToXml($arr);
	$xml = <<<EOF
<?xml version="1.0" encoding="gb2312"?>
<BOCOMB2C>	
{$xmlstring}
</BOCOMB2C>
EOF;
	file_put_contents($file,$xml);
}

function arrayToXml($arr){
	foreach($arr as $key=>$val){
		$ret .= "\t<$key>$val</$key>\r\n";
	}
	return $ret;
}

?>