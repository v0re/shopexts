<?php

error_log(var_export($_REQUEST,true),3,__FILE__.".log");

$returnmsg=explode("|",$_REQUEST['notifyMsg']);
#
$paymentId=$returnmsg[1];
$orderid = substr($paymentId,-6);	//支付ID
$orderno = substr($paymentId,0,14);	//支付ID
#
$signMsg = $returnmsg[14];
#
$srcMsg="";
for($i=0;$i<14;$i++)
{
	$srcMsg.=$returnmsg[$i]."|";
}
#
$ret = -100;
if (strtoupper(substr(PHP_OS,0,3))=="WIN")
{
	$bb = new COM("B2CClientCOMCtrl.B2CClientCOM");
	$rc=$bb->Initialize($realPath.md5('bocomshopex'.$tmp['payment']).".xml");
	if ($rc)
	{
		$err = $bb->GetLastErr();
		echo $err;
		exit;
	}
	$ret = $bb->Sign_detachverify($srcMsg,$signMsg);
}
else
{
	$ret = verify($srcMsg,$signMsg);
}

$money=$returnmsg[2];


$shoporder = newclass("Order");
if ( $ret == '0' )
{	
	#TRANRST,1成功
	if($returnmsg[9])
	{
		$shoporder->shopId = $INC_SHOPID;
		$shoporder->payid = $orderid;
		if($shoporder->getbyId($orderno))
		{
			$shoporder->payprocess();	//支付状态设成处理中
		}

		$state = 2 ;
		$strinfo = $PROG_TAGS["ptag_1281"];
	}
	else
	{
		$state = 1 ;
		$strinfo = $PROG_TAGS["ptag_1285"];
	}
}
else
{
	$state = 0 ;
	$strinfo = $PROG_TAGS["ptag_1284"];
}

header("Location: ./index.php?gOo=pay_reply.dwt&orderid=".$orderno."&state=".$state."&strinfo=".urlencode($strinfo));


function verify($src,$signed){
	$fp = fsockopen("127.0.0.1", 9555, $errno, $errstr, 30);
	if (!$fp) {
		echo "$errstr ($errno)<br />\n";
	} else {
		$out = "veri%{$src}%{$signed}\r\n";
		fwrite($fp, $out);
		while (!feof($fp)) {
			$ret .= fgets($fp, 128);
	}
	fclose($fp);
	return $ret;
	}
}

?>