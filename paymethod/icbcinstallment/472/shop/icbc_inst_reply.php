<?php
if (!defined("ISSHOP"))
{
	Header("Location:../index.php");
	exit;
}

#商户公钥文件
define("MERCCERTFILE",dirname(__FILE__)."/../cert/ICBC/user.crt");
#商户私钥文件
define("MERCRRVIFILE",dirname(__FILE__)."/../cert/ICBC/user.key");
#工行公钥文件
define("ICBCCERTFILE",dirname(__FILE__)."/../cert/ICBC/ebb2cpublic.crt");
define("MERCPASSWORD",'xxx');
define("MERID",'xxxx');
define("MERACCT",'xxxxxxxx');


$notifyData = $_POST['notifyData'];
$notifyData = base64_decode($notifyData);
$signMsg = $_POST['signMsg'];

//windows系统
if (strtoupper(substr(PHP_OS,0,3))=="WIN"){
	$icbcutil = new COM("ICBCEBANKUTIL.B2CUtil");					
	$rc = $icbcutil->init(MERCCERTFILE, MERCCERTFILE, MERCRRVIFILE, MERCPASSWORD);
	#返回0表示签名正确
	if($icbcutil->verifySignC($tranData, strlen($tranData), $signMsg, strlen($signMsg)) == 0){
		$isok = 1;
	}else{
		$isok = 0;
	}
}else{
	$cmd = "/bin/icbc_verify '".ICBCCERTFILE."' '".$notifyData."' '".$signMsg."'";
	$handle = popen($cmd, 'r');
	$isok = fread($handle, 8);
	pclose($handle);
}

//////////////////////////////////////////////////////////////////////////////
//给工行返回一个地址，工行跳回来用
if(intval($isok) == 1){
	//取出orderid和tranStat
	preg_match("/\<orderid\>(.*)\<\/orderid\>\<amount\>(.*)\<\/amount\>.+\<tranSerialNo\>(.*)\<\/tranSerialNo\>.+\<tranStat\>(.*)\<\/tranStat\>/i",$notifyData,$rnt);
  $orderid = $rnt[1];
	$money = $rnt[2] / 100;
 	$tranStat = $rnt[4];
	#
	//分拆支付号和订单号
	//ShopEx支付号
	$payid = substr($orderid,-6);	
	//ShopEx订单号
	$orderid = substr($orderid,0,14);	
	
	if($tranStat == 1){
		$state = 2 ;
		/*
		* 处理订单
		*/
		$shoporder = newclass("Order");
		$shoporder->shopId = $INC_SHOPID;
		$shoporder->payid = $payid;
		//支付时间
		$arr_paytime = getUnixtime();	
		//在线支付
		$shoporder->onlinePayed($arr_paytime[0], $arr_paytime[1]);
		////////////////如果能返回到pay_reply.dwt处理订单，请注释上面的语句///////
		$strinfo = $PROG_TAGS["ptag_1281"];
	}else{
		/*
		* 报错
		*/
		$state = 1 ;
		$strinfo = $PROG_TAGS["ptag_1285"];
	}
}else{
		$state = 0;
		$strinfo = $PROG_TAGS["ptag_1284"];
}

//$strinfo .= "(".$comment.")";
//生成回跳地址
$Order = newclass("Order");
$Order->shopId = $INC_SHOPID;
mt_srand((double)microtime()*1000000);
$signstr = mt_rand(10000000,99999999);
$Order->updateOrderSign($orderid, $signstr);

$retURL = $INC_DOMAIN."index.php?gOo=pay_reply.dwt&signstr=".$signstr."&orderid=".$orderid."&state=".$state."&strinfo=".urlencode($strinfo);

header("Location: ".$retURL);

?>