<?php
/**
* 
* �������ļ�
*
* @package  ShopEx�����̵�ϵͳ
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

//=========================== ���̼ҵ������Ϣ����ȥ =======================
$transactionStatus	= $_REQUEST['transactionStatus'];			//�̵궩����
$customerOrderID		= $_REQUEST['customerOrderID'];		//�̻���
$transactionAmount	= $_REQUEST['transactionAmount'];	//״̬


//���ǩ��
//$shopPayment = newclass("shopPayment");
//$key = $shopPayment->getKey($INC_SHOPID, $mer_id, "2CHECKOUT");

//����md5����
//$text = $key.$mer_id.$orderid.$amount;
//$md5digest = strtoupper(md5($text));

if (true){
		$Order = newclass("Order");
		$Order->shopId = $INC_SHOPID;
		$Order->payid = $customerOrderID;
		$tmp_orderno = $Order->getorderidbyPayid($Order->payid);	//�ó��̵궩����
		
		if ($Order->paymoney <= $transactionAmount){
			$arr_paytime = getUnixtime();	//֧��ʱ��
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

Header("Location: ./index.php?gOo=pay_reply.dwt&orderid=".$tmp_orderno."&state=".$state."&strinfo=".urlencode($strinfo));

?>