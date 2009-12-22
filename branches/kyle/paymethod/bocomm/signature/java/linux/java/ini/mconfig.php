<?php

$realpath = dirname(__FILE__);
define('CONFIG',$realpath."/B2CMerchant.xml");

$aConfig = array(
'ApiURL'=>'https://ebanktest.95559.com.cn/corporbank/NsTrans',
'OrderURL'=>'https://pbanktest.95559.com.cn/netpay/MerPayB2C',
'EnableLog'=>'false',
'LogPath'=>'',
'SettlementFilePath'=>'',
'MerchantCertFile'=>'',
'MerchantCertPassword'=>'',
'RootCertFile'=>'',
);
$aConfig['LogPath'] = $realpath;
$aConfig['SettlementFilePath'] = $realpath;
$aConfig['MerchantCertFile'] = $realpath."/301310063009501.PFX";
$aConfig['MerchantCertPassword'] = '12345678';
$aConfig['RootCertFile'] = $realpath."/test_root.cer";

$message = 'bocomm';

makeConfig(CONFIG,$aConfig);


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