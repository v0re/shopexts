<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>abchina - 支付请求</title>
</head>
<body>
<?php 
	require(dirname(__FILE__) . '/cls_abchina.php');

	$add = "http://127.0.0.1:8080/axis/services/B2CWarpper?wsdl";
	//$add = "http://187.61.1.5:8080/axis/services/B2CWarpper?wsdl";

	$tOrderNo = $_POST['OrderNo'];
	$tOrderDesc = $_POST['OrderDesc'];
	$tOrderDate = $_POST['OrderDate'];
	$tOrderTime = $_POST['OrderTime'];
	$tOrderAmountStr = $_POST['OrderAmount'];
	$tOrderURL = $_POST['OrderURL'];
	$tProductType = $_POST['ProductType'];
	$tPaymentType = $_POST['PaymentType'];	// 1：农行卡支付 2：国际卡支付 3：农行贷记卡支付
	$tPaymentLinkType = $_POST['PaymentLinkType'];	// 1：internet网络接入 2：手机网络接入 3:数字电视网络接入 4:智能客户端
	$tNotifyType = $_POST['NotifyType'];		// 0：URL页面通知 1：服务器通知
	$tResultNotifyURL = $_POST['ResultNotifyURL'];
	$tMerchantRemarks = $_POST['MerchantRemarks'];
	$tOrderItems=array();
	$tTotalCount = $_POST['TotalCount'];
	for($i=0;$i<$tTotalCount;$i++)
	{
		//print("<br>".$_POST['productname'][$i]."</br>");
		$tOrderItems[]=array($_POST['productid'][$i], $_POST['productname'][$i], $_POST['uniteprice'][$i],$_POST['qty'][$i]);
	}
	
	//$merchantPayment = new MerchantPayment($add,$tOrderNo,$tOrderDesc,$tOrderDate,$tOrderTime,$tOrderAmountStr,$tOrderURL,$tProductType,$tPaymentType,$tNotifyType,$tResultNotifyURL,$tMerchantRemarks,$tPaymentLinkType);
	$merchantPaymentRequest = new MerchantPaymentRequest($tOrderNo,$tOrderDesc,$tOrderDate,$tOrderTime,$tOrderAmountStr,$tOrderURL,$tProductType,$tPaymentType,$tNotifyType,$tResultNotifyURL,$tMerchantRemarks,$tPaymentLinkType,$tOrderItems);
	$merchantPayment = new MerchantPayment($add,$merchantPaymentRequest);
	$merchantPaymentResult = $merchantPayment->invoke();
	//$merchantPayment->showResult();
	//显示结果
	if($merchantPaymentResult->isSucess==TRUE)
	{
		$PaymentURL = $merchantPaymentResult->paymentURL;
	}
	else
	{
		print("<br>Failed!!!"."</br>");
		print("<br>return code:".$merchantPaymentResult->returnCode."</br>"); 
		print("<br>Error Message:".$merchantPaymentResult->ErrorMessage."</br>");
	}


?>
	<script language=javascript>
	//	支付请求页面跳转
		var redirectURL="<?=$PaymentURL?>";
		if(redirectURL!=null&&redirectURL!="")
		{
			location.href='<?=$PaymentURL?>';
		}
	</script>
</body>
</html>