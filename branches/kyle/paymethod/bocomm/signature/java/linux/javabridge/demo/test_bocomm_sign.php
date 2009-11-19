<?php

require_once("java/Java.inc"); //php调用java的接口，必须的

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
	 //获得java对象
	$BOCOMSetting=java("com.bocom.netpay.b2cAPI.BOCOMSetting");
	$client=new java("com.bocom.netpay.b2cAPI.BOCOMB2CClient");
	$ret=$client->initialize(CONFIG);
	$ret = java_values($ret);
	if ($ret != "0")
	{
			$err=$client->getLastErr();
			//为正确显示中文对返回java变量进行转换，如果用java_set_file_encoding进行过转换则不用再次转换
			//$err = java_values($err->getBytes("GBK")); 
			$err=java_values($err);
			print "初始化失败,错误信息：" . $err . "<br>";
			exit(1);
	}

	$sourceMsg=new java("java.lang.String", $message);

	//下为生成数字签名
	$nss=new java("com.bocom.netpay.b2cAPI.NetSignServer");

	$merchantDN=$BOCOMSetting->MerchantCertDN;
	$nss->NSSetPlainText($sourceMsg->getBytes("GBK"));

	$bSignMsg=$nss->NSDetachedSign($merchantDN);
	$signMsg=new java("java.lang.String", $bSignMsg, "GBK");

	return $signMsg;


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