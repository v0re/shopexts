<?php
if (!defined("ISSHOP"))
{
	Header("Location:../index.php");
	exit;
}

#�̻���Կ�ļ�
define("MERCCERTFILE",dirname(__FILE__)."/../cert/ICBC/user.crt");
#�̻�˽Կ�ļ�
define("MERCRRVIFILE",dirname(__FILE__)."/../cert/ICBC/user.key");
#���й�Կ�ļ�
define("ICBCCERTFILE",dirname(__FILE__)."/../cert/ICBC/ebb2cpublic.crt");
define("MERCPASSWORD",'xxx');
define("MERID",'xxxx');
define("MERACCT",'xxxxxxxx');


$notifyData = $_POST['notifyData'];
$notifyData = base64_decode($notifyData);
$signMsg = $_POST['signMsg'];

//windowsϵͳ
if (strtoupper(substr(PHP_OS,0,3))=="WIN"){
	$icbcutil = new COM("ICBCEBANKUTIL.B2CUtil");					
	$rc = $icbcutil->init(MERCCERTFILE, MERCCERTFILE, MERCRRVIFILE, MERCPASSWORD);
	#����0��ʾǩ����ȷ
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
//�����з���һ����ַ��������������
if(intval($isok) == 1){
	//ȡ��orderid��tranStat
	preg_match("/\<orderid\>(.*)\<\/orderid\>\<amount\>(.*)\<\/amount\>.+\<tranSerialNo\>(.*)\<\/tranSerialNo\>.+\<tranStat\>(.*)\<\/tranStat\>/i",$notifyData,$rnt);
  $orderid = $rnt[1];
	$money = $rnt[2] / 100;
 	$tranStat = $rnt[4];
	#
	//�ֲ�֧���źͶ�����
	//ShopEx֧����
	$payid = substr($orderid,-6);	
	//ShopEx������
	$orderid = substr($orderid,0,14);	
	
	if($tranStat == 1){
		$state = 2 ;
		/*
		* ������
		*/
		$shoporder = newclass("Order");
		$shoporder->shopId = $INC_SHOPID;
		$shoporder->payid = $payid;
		//֧��ʱ��
		$arr_paytime = getUnixtime();	
		//����֧��
		$shoporder->onlinePayed($arr_paytime[0], $arr_paytime[1]);
		////////////////����ܷ��ص�pay_reply.dwt����������ע����������///////
		$strinfo = $PROG_TAGS["ptag_1281"];
	}else{
		/*
		* ����
		*/
		$state = 1 ;
		$strinfo = $PROG_TAGS["ptag_1285"];
	}
}else{
		$state = 0;
		$strinfo = $PROG_TAGS["ptag_1284"];
}

//$strinfo .= "(".$comment.")";
//���ɻ�����ַ
$Order = newclass("Order");
$Order->shopId = $INC_SHOPID;
mt_srand((double)microtime()*1000000);
$signstr = mt_rand(10000000,99999999);
$Order->updateOrderSign($orderid, $signstr);

$retURL = $INC_DOMAIN."index.php?gOo=pay_reply.dwt&signstr=".$signstr."&orderid=".$orderid."&state=".$state."&strinfo=".urlencode($strinfo);

header("Location: ".$retURL);

?>