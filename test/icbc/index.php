<?php

define("MERCCERTFILE",dirname(__FILE__)."/LSSMKJ.crt");
define("MERCRRVIFILE",dirname(__FILE__)."/LSSMKJ.key");
define("ICBCCERTFILE",dirname(__FILE__)."/admin.key");
define("MERCPASSWORD",'88870705');
define("MERID",'1203EC20001060');
define("MERACCT",'1203202019045150941');
define("MERID",'1203EC20001060');


$action = $_GET['action'];

$icbcURL = 'https://mybank.icbc.com.cn/servlet/ICBCINBSEBusinessServlet';

//$tranData = file_get_contents(dirname(__FILE__)."/req.xml");
//$tranData = trim($tranData);
$merURL = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];



if(!$action){
	//接口名称固定为“ICBC_PERBANK_B2C”
	$aREQ["interfaceName"] = "ICBC_PERBANK_B2C"; 
	//接口版本目前为“1.0.0.0”
	$aREQ["interfaceVersion"] = "1.0.0.7";
	//商城代码，ICBC提供
	$aREQ["merID"] = MERID;
	//商户帐号，ICBC提供
	$aREQ["merAcct"] = MERACCT;
	//分期付款期数
	$aREQ["installmentTimes"] = '12';
	//
	$aREQ["merURL"] = $merURL;
	//HS方式实时发送通知；AG方式不发送通知；
	$aREQ["notifyType"] = "HS";
	//订单号商户端产生，一天内不能重复,拼接上订单号和支付号。
	$aREQ["orderid"] = time();
	//金额以分为单位
	$aREQ["amount"] = 100;
	//币种目前只支持人民币，代码为“001”
	$aREQ["curType"] = "001"; 
	//对于HS方式“0”：发送成功或者失败信息；“1”，只发送交易成功信息。
	$aREQ["resultType"] = 0;
	//14位时间戳
	$aREQ["orderDate"] = date("YmdHis",time());
	//以上五个字段用于客户支付页面显示
	$aREQ["goodsID"] = "";
	//网关只认GB2312
	$aREQ["goodsName"]  = 'fuck me';
	$aREQ["goodsNum"] = 1;
	//运费金额以分为单位
	$aREQ["carriageAmt"] = 0;
	$aREQ["merHint"] = "";
	//备注
	$aREQ["remark1"] = "";
	//备注2
	$aREQ["remark2"] = "";
	//“1”判断该客户是否与商户联名；取值“0”不检验客户是否与商户联名。
	$aREQ["verifyJoinFlag"] = 0;
	//语言版本。
	$aREQ["Language"] = 'ZH_CN';

	echo "<form action=?action=go method=post target='go'><table>";
	foreach($aREQ as $k=>$v){
		echo "<tr><td>{$k}</td><td><input name=\"{$k}\" type=\"text\" value=\"{$v}\"  size='100'/></td></tr>";
	}
	echo "<tr><td clospan=2><input type=submit value=submit></td></tr><table>";
	echo "</form>";
	
}

if($action == 'go'){

	//xml
	$tranData = "<?xml version=\"1.0\" encoding=\"GBK\" standalone=\"no\"?><B2CReq><interfaceName>".$_REQUEST["interfaceName"]."</interfaceName><interfaceVersion>".$_REQUEST["interfaceVersion"]."</interfaceVersion><orderInfo><orderDate>".$_REQUEST["orderDate"]."</orderDate><curType>".$_REQUEST["curType"]."</curType><merID>".$_REQUEST["merID"]."</merID><subOrderInfoList><subOrderInfo><orderid>".$_REQUEST["orderid"]."</orderid><amount>".$_REQUEST["amount"]."</amount><installmentTimes>".$_REQUEST["installmentTimes"]."</installmentTimes><merAcct>".$_REQUEST["merAcct"]."</merAcct><goodsID>".$_REQUEST["goodsID"]."</goodsID><goodsName>".$_REQUEST["goodsName"]."</goodsName><goodsNum>".$_REQUEST["goodsNum"]."</goodsNum><carriageAmt>".$_REQUEST["carriageAmt"]."</carriageAmt></subOrderInfo></subOrderInfoList></orderInfo><custom><verifyJoinFlag>".$_REQUEST["verifyJoinFlag"]."</verifyJoinFlag><Language>".$_REQUEST["Language"]."</Language></custom><message><merHint>".$_REQUEST["merHint"]."</merHint><remark1>".$_REQUEST["remark1"]."</remark1><remark2>".$_REQUEST["remark2"]."</remark2><merURL>".$_REQUEST["merURL"]."</merURL><merVAR>".$_REQUEST["merVAR"]."</merVAR></message></B2CReq>";

	//商户签名数据BASE64编码
	$cmd = "/bin/icbc_sign '".MERCRRVIFILE."' '".MERCPASSWORD."' '{$tranData}'";
	$handle = popen($cmd, 'r');
	$merSignMsg = fread($handle, 2096);
	pclose($handle);
	//
	$fp = fopen(MERCCERTFILE,"rb");
	$merCert = fread($fp,filesize(MERCCERTFILE));
	$merCert = base64_encode($merCert);
	fclose($fp);
	//
	$tranData = base64_encode($tranData);

	//

	echo <<<EOF

	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US" dir="ltr">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	</head>
	<body>
	<script>
		function decodetext(){
			document.getElementById('frm').submit();
		}
	</script>
	<form id=frm action=?action=decode target='_blank' method=post>
		tranData（双击加密文本查看原始数据）:<br>
		<textarea name=tranData cols=100 rows=5 ondblclick="decodetext();">{$tranData}</textarea><br>
		merSignMsg:<br>
		<textarea cols=100 rows=5>{$merSignMsg}</textarea><br>
		merCert:<br>
		<textarea cols=100 rows=5>{$merCert}</textarea>
	</form>

	<form id="payment" action="{$icbcURL}" method="post" target='_blank'>
	<input name="interfaceName" type="hidden" value="ICBC_PERBANK_B2C" />
	<input name="interfaceVersion" type="hidden" value="1.0.0.7" />
	<input name="tranData" type="hidden" value="{$tranData}" />
	<input name="merSignMsg" type="hidden" value="{$merSignMsg}" />
	<input name="merCert" type="hidden" value="{$merCert}" />
	<input type='submit' value='提交至工行'>
	</form>

	</body>
	</html>

EOF;

}

if($action == 'decode'){
	echo base64_decode($_REQUEST['tranData']);
}

?>