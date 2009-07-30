<?php
/**
* 
* 程序处理文件
*
* @package  ShopEx网上商店系统
* @version  4.6
* @author   ShopEx.cn <develop@shopex.cn>
* @url		http://www.shopex.cn/
* @since    PHP 4.3
* @copyright ShopEx.cn
*
**/
if (!defined("ISSHOP"))
{
	Header("Location:../index.php");
	exit;
}

//=========================== 把商家的相关信息返回去 =======================
$transactionStatus	= $_REQUEST['transactionStatus'];			//商店订单号
$customerOrderID		= $_REQUEST['customerOrderID'];		//商户号
$transactionAmount	= $_REQUEST['transactionAmount'];	//状态

$payid = substr($customerOrderID,-6);


//检查签名
//$shopPayment = newclass("shopPayment");
//$key = $shopPayment->getKey($INC_SHOPID, $mer_id, "2CHECKOUT");

//整合md5加密
//$text = $key.$mer_id.$orderid.$amount;
//$md5digest = strtoupper(md5($text));

if (true){
		$Order = newclass("Order");
		$Order->shopId = $INC_SHOPID;
		$Order->payid = $payid;
		$tmp_orderno = $Order->getorderidbyPayid($Order->payid);	//拿出商店订单号
		
		if ($Order->paymoney <= $transactionAmount){
			$arr_paytime = getUnixtime();	//支付时间
			$Order->onlinePayed($arr_paytime[0], $arr_paytime[1]);
			$state = 2 ;
			$strinfo = $PROG_TAGS["ptag_1334"];
		}	else {
			$state = 1 ;
			$strinfo = $PROG_TAGS["ptag_1335"];
		}
} else {
	$state = 0 ;
	$strinfo = $PROG_TAGS["ptag_1336"];
}

mt_srand((double)microtime()*1000000);
$signstr = mt_rand(10000000,99999999);
$Order->updateOrderSign($tmp_orderno, $signstr);
header("Location: index.php?gOo=pay_reply.dwt&signstr=".$signstr."&orderid=".$tmp_orderno."&state={$state}&strinfo=".urlencode($strinfo));

?>