<?php

/**
* 
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

$action = $_POST["shopex_action"];

$shopex_pay_type = $_POST["shopex_pay_type"];
$shopex_encoding = $_POST["shopex_encoding"];
if($shopex_encoding=="")
{
	header("Content-Type: text/html; charset=UTF-8");
}
else
{
	header("Content-Type: text/html; charset=".$shopex_encoding);
}
$req_type = ($shopex_pay_type == "XPAY")?"GET":"POST";
if ($shopex_pay_type<>"TENPAYTRAD")
{
	echo "<form name=\"payform\" action=".$action." method=".$req_type.">";
	while(list($k,$v)=each($_POST))
	{
		if($shopex_pay_type == 'GSPAY'){
				$k = urldecode($k);
				$v = urldecode($v);
		}
		if(substr($k,0,7)!="shopex_"&&$k!="submit"&&$k!="send"&&$k!="gOo")
		{
			if(substr($v,0,19)=="local_shopex_encode") $v = base64_decode(substr($v,19));
			echo "<input type=\"hidden\" name=\"{$k}\" value=\"".$v."\" />";
		}
	}	
	echo "</form>";
	echo "Please wait....";
	echo "<script type=\"text/javascript\">";
	echo    "payform.submit();";
	echo "</script>"; 
} 
else{
	$version 			 =  $_POST['version'];																//版本号 2
	$cmdno	 			 =  $_POST['cmdno'];											    //任务代码，定值：12
	$encode_type 		 =  $_POST['encode_type'];								//编码标准
	$chnid				 =  $_POST['chnid'];										//平台提供者,代理商的财付通账号
	$mch_type			 =  $_POST['mch_type'];									//交易类型：1、实物交易，2、虚拟交易。
	$seller				 =  $_POST['seller'];									//卖家帐号
	$sp_key				 =  $_POST['ikey'];										//密钥
	//$site_name			 = utf2local("来自".$_POST['shopname']."订单:","zh");	//商户网站名称
	$attach				 =  $_POST['attach'];													//支付附加数据，非中文标准字符
	$pay_url			 =  $action;								 				//财付通支付网关地址
	$need_buyerinfo		 =  $_POST['needbuyerinfo'];
	$mch_returl			 =  $_POST['mch_returl'];								//通知URL
	$show_url			 =  $_POST['show_url'];									//返回URL		
	function AddParameter($buffer,$parameterName,$parameterValue)
	{	
		if ($parameterValue=="")
			return $buffer;
		if (empty($buffer))
			$buffer = $parameterName . "=". $parameterValue;
		else
			$buffer = $buffer . "&" . $parameterName . "=" .$parameterValue;
		return $buffer;
	}
	$buffer = AddParameter($buffer, "attach", 		$attach);
	$buffer = AddParameter($buffer, "chnid", 		$chnid);
	$buffer = AddParameter($buffer, "cmdno", 		$cmdno);
	$buffer = AddParameter($buffer, "encode_type", 	$encode_type);
	$buffer = AddParameter($buffer, "mch_desc", 	utf2local($_POST['mch_desc'],"zh"));
	$buffer = AddParameter($buffer, "mch_name", 	utf2local($_POST['mch_name'],"zh"));
	$buffer = AddParameter($buffer, "mch_price", 	$_POST['mch_price']);
	$buffer = AddParameter($buffer, "mch_returl", 	$mch_returl);
	$buffer = AddParameter($buffer, "mch_type", 	$mch_type);
	$buffer = AddParameter($buffer, "mch_vno", 		$_POST['mch_vno']);
	$buffer = AddParameter($buffer, "need_buyerinfo",$need_buyerinfo);
	$buffer = AddParameter($buffer, "seller", 		$seller);
	$buffer = AddParameter($buffer, "show_url", 	$show_url);
	$buffer = AddParameter($buffer, "transport_desc",utf2local($transport_desc,"zh"));
	$buffer = AddParameter($buffer, "transport_fee", $_POST['transport_fee']);						
	$buffer = AddParameter($buffer, "version", 		 $version);
	$md5_sign 	= strtoupper(md5($buffer."&key=".$sp_key));	
	$url=$pay_url."?".$buffer."&sign=".$md5_sign;
	echo "please wait....";
	echo "<script type=\"text/javascript\">";
	echo "window.location.href='".$url."';";
	echo "</script>";
}
?>
