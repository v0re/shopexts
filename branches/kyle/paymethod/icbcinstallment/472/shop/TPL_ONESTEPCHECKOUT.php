<?php
/**
 * 
 * 程序模块文件
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

//得出当前币别的汇率
$ShopCurrency = newclass("ShopCurrency");
$ShopCurrency->shopId = $INC_SHOPID;
$nowrate = $ShopCurrency->getCurRate($INC_USERCURRENCY);

$shopSetup = newclass("shopSetup");
$shopSetup->shopId = $INC_SHOPID;

//*******************XML 输出 BEGIN*****************/
include_once (dirname(__FILE__)."/../include/xajax.inc.php");

$xajax = new xajax("index.php?gOo=".$_GET["gOo"]);
$xajax->registerFunction("chgArea");
$xajax->registerFunction("chgDelivery");
$xajax->registerFunction("chgPayment");
$xajax->registerFunction("selectProtect");
$xajax->registerFunction("checkInvoice");
$xajax->registerFunction("chgConsumePoint");
$xajax->registerFunction("chgMemcCode");


$xajax->processRequests();

//选择配送地区触发
//, nowconsumepoint, nowcouponsfee
//$consumepoint, $totalCouponsDiscount)
function chgArea($areaid, $totalmoney,$postcode,$consumepoint, $totalCouponsDiscount,$taxratio=0)
{
	global $INC_SHOPID;
	global $INC_SHOPDIR;
	global $PROG_TAGS;
	global $ShopCurrency;
	global $nowrate;


	$shopDelivery = newclass("shopDelivery");
	$alertStr = $shopDelivery->getListbyArea($INC_SHOPID, $areaid);

	$DeliverExp = newclass("DeliverExp");
	$DeliverExp->shopId = $INC_SHOPID;
	$cart_arr = getArrayCart();
	$weight = $cart_arr['weight'];
	//配送方式列表
	$i=0;
	$showdelivery = "<table width=\"100%\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\" class=\"border-b\">\n";

	while ($shopDelivery->next())
	{
		//判断计算配送方式费用
		if ($shopDelivery->type == 0){
			$tmp_exp = "";
			$arr_areaexp = explode("@", $shopDelivery->areaexp);
			$tmp_num = count($arr_areaexp);
			for($j = 1; $j < $tmp_num; $j++)
			{
				$tmp_arr = explode(",", $arr_areaexp[$j]);
				if ($tmp_arr[0] == $areaid)
				{
					$tmp_exp = $tmp_arr[1];
					break;
				}
			}
			if (!empty($tmp_exp))
			{
				if ($DeliverExp->getbyId($tmp_exp))
				{
					$dprice = cal_fee($DeliverExp->expressions,$weight,$totalmoney);
				}
				else
					$dprice = 0;
			}
			else
				$dprice = 0;
		}
		elseif($shopDelivery->type == 1)
			$dprice = $shopDelivery->price;
		else  //外部接口
		{
			if($postcode=='')
			{
				$objResponse = new xajaxResponse();
				$objResponse->addAssign("areaid","innerHTML",$showdelivery);
				$objResponse->addScript("alert('".$PROG_TAGS["inputpostcode"]."');document.receiver_area.selectedIndex=0;");
				return $objResponse->getXML();
			}
			$dprice=0;
			$gateway = $shopDelivery->gateway;
			$shopInterface=newclass("shopInterface");
			$shopInterface->shopId=$INC_SHOPID;
			$shopInterface->getbyId($gateway);
			$wildSetup = newclass("wildSetup");
			$gate_arr = $wildSetup->decode($shopInterface->config);
			if(file_exists(PLUGIN_PATH."freight_calculation_".$shopInterface->code.".php"))
			{
				include(PLUGIN_PATH."freight_calculation_".$shopInterface->code.".php");
			}
		}
		$dprice *= $nowrate;
		//断计算配送方式费用

		if ($shopDelivery->protect == 1)
		{
			$protectprice = ($shopDelivery->minprice > ($totalmoney * $shopDelivery->protectrate)?$shopDelivery->minprice:($totalmoney * $shopDelivery->protectrate));
			$protectshow = $PROG_TAGS["ptag_protectfee"]." (".$ShopCurrency->sign.($protectprice * $nowrate).")";
		}
		else
			$protectshow = "";

		$showdelivery .= "<tr class=\"showitem\">";
		$showdelivery .= "<td width=\"3%\" height=\"30\"><input type=\"radio\" name=\"deliveryid\" value=\"".$shopDelivery->sdId."\" ".($i==0?"checked=\"checked\"":"")." onclick=\"chgDelivery(".$shopDelivery->sdId.", $dprice)\" /></td>";
		$showdelivery .= "<td width=\"50%\" height=\"30\"><strong>".$shopDelivery->name."</strong></td>";
		$showdelivery .= "<td width=\"20%\" nowrap=\"nowrap\" height=\"30\"><strong>".$PROG_TAGS["ptag_deliveryfee"].$ShopCurrency->sign.$dprice."</strong></td>";
		$showdelivery .= "<td width=\"30%\" height=\"30\"><strong>$protectshow</strong></td>";
		$showdelivery .= "</tr>";
		$showdelivery .= "<tr> ";
		$showdelivery .= "<td width=\"3%\"></td>";
		$showdelivery .= "<td width=\"100%\" colspan=\"3\" class=\"showitemdetail\">".$shopDelivery->detail."</td>";
		$showdelivery .= "</tr>";
		if ($i == 0)
		{
			$firstprice = $dprice;
			$delid = $shopDelivery->sdId;
		}
		$i++;
	}

	$showdelivery .= "</table>";


	$objResponse = new xajaxResponse();

	if($i > 0){

		if(LICENSE_8 == 2){
			$showdelivery = preg_replace('/="pictures\//', "=\"".$INC_SHOPDIR."pictures/", $showdelivery);
			$showdelivery = preg_replace('/=\'pictures\//', "='".$INC_SHOPDIR."pictures/", $showdelivery);
			$showdelivery = preg_replace('/=pictures\//', "=".$INC_SHOPDIR."pictures/", $showdelivery);
		}
		$objResponse->addAssign("areaid","innerHTML",$showdelivery);
		$script = "xajax_chgDelivery($delid, $firstprice, 0, 0, $totalmoney, $taxratio,$consumepoint, $totalCouponsDiscount);";
		//$consumepoint, $totalCouponsDiscount)
		$objResponse->addScript($script);
	}
	else{
		$script = "alert('{$PROG_TAGS["ptag_1307"]}');document.receiver_area.selectedIndex=0;";
		$objResponse->addScript($script);
		
		$tmpLog = dirname(__FILE__)."/../include/upgrade.log";
		$firstPart = file_get_contents($tmpLog);
		$fw = @fopen($tmpLog, "wb");
		fwrite($fw, $firstPart."\n".$alertStr);
		chmod($tmpLog, 0666);
		fclose($fw);
	}

	return $objResponse->getXML();
}

//选择配送方式触发
function chgDelivery($delid, $deliveryprice, $protectprice, $paymentprice, $commoprice, $taxratio, $consumepoint, $totalCouponsDiscount)
{
	global $INC_SHOPID;
	global $INC_SHOPDIR;
	global $PROG_TAGS;
	global $ShopCurrency;
	global $nowrate;
	global $INC_USERID;
	global $INC_USERISREG;

	$shopDelivery = newclass("shopDelivery");
	$shopDelivery->shopId = $INC_SHOPID;
	$shopDelivery->getbyId($delid);
	$protectprice = 0;
	if ($shopDelivery->protect)
	{
		$protectprice = ($shopDelivery->minprice > ($commoprice * $shopDelivery->protectrate)?$shopDelivery->minprice:($commoprice * $shopDelivery->protectrate));
		$protectprice *= $nowrate;
		$deliveryprotectcheck = $PROG_TAGS["ptag_1807"]."<input type=\"checkbox\" name=\"deliveryprotect\" value=\"1\" onclick=\"selectProtect()\" />";
	}

	$strnum = strlen($shopDelivery->strpayment);
	if ($strnum > 2)
	{
		$shopPayment = newclass("shopPayment");
		$shopPayment->shopId = $INC_SHOPID;
		$shopPayment->keyspid = substr($shopDelivery->strpayment, 1, $strnum-2);
		$alertStr = $shopPayment->getListbyShop();
		$showpayment = "<table width=\"100%\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\" class=\"border-b\">\n";
		$i=0;
		while ($shopPayment->next()){
			//如果是点击直接购买按钮进入，则过滤其它支付方式
			if(intval($_COOKIE["COOKIE_BUTTON"]) > 0 && $_COOKIE["COOKIE_BUTTON"]!=$shopPayment->spId){
				continue;
			}

			if ($shopPayment->type != "ADVANCE" || ($shopPayment->type == "ADVANCE" && $INC_USERID > 0 && $INC_USERISREG > 0))
			{
				if (!empty($shopPayment->expression))
				{
					$tmpPrice = $commoprice+$deliveryprice+($commoprice * $taxratio / 100)-$consumepoint-$totalCouponsDiscount;
					$paymentprice = cal_fee($shopPayment->expression, 0, $tmpPrice, 1);
					$paymentprice *= $nowrate;
				}
				else
					$paymentprice = 0;
				
				if ($shopPayment->type == "ADVANCE" && $INC_USERID > 0 && $INC_USERISREG > 0)
				{
					$User = newclass("User");
					$User->getbyId($INC_USERID);
					$advanceDesc = " (".$PROG_TAGS['leftadvance'].$ShopCurrency->sign.($User->advance * $nowrate).")";
				}
				else $advanceDesc = "";

				$showpayment .= "<tr class=\"showitem\">";
				$showpayment .= "<td width=\"3%\" height=\"30\"><input type=\"radio\" name=\"paymentid\" value=\"".$shopPayment->spId."\" ".($i==0?"checked=\"checked\"":"")." onclick=\"chgPayment(".$shopPayment->spId.", $paymentprice)\" /></td>";
				$showpayment .= "<td width=\"50%\" height=\"30\"><strong>".$shopPayment->name.$advanceDesc."</strong></td>";
				$showpayment .= "<td width=\"20%\" nowrap=\"nowrap\" height=\"30\"><strong>".$PROG_TAGS["ptag_paymentfee"].$ShopCurrency->sign.$paymentprice."</strong></td>";
				$showpayment .= "<td width=\"30%\" height=\"30\"></td>";
				$showpayment .= "</tr>";
				$showpayment .= "<tr>";
				$showpayment .= "<td width=\"3%\"></td>";
				$showpayment .= "<td colspan=\"3\" class=\"showitemdetail\">".$shopPayment->detail."</td>";
				$showpayment .= "</tr>";
				//////////////////////////////////////////////////////////////////////
				if($shopPayment->type == "ICBC_INST"){
					$showpayment .= "
					<tr>
						<td></td>
						<td id=\"icbc_per\">
						请选择分期数: <select name=\"icbc_period\">
							<option value=\"1\" selected>一期</option>
							<option value=\"3\">三期</option>
							<option value=\"6\">六期</option>
							<option value=\"12\">十二期</option>
						</select>
						</td>
					</tr>";
				}
				///////////////////////////////////////////////////////////////////////
				if ($i == 0)
				{
					$firstprice = $paymentprice;
					$paymentid = $shopPayment->spId;
				}
				$i++;
			}
		}
		$showpayment .= "</table>";
	}
	else $showpayment = "";

	$objResponse = new xajaxResponse();
	$objResponse->addAssign("ordercarriage", "innerHTML", $ShopCurrency->sign.$deliveryprice);
	$objResponse->addAssign("deliveryprotect","innerHTML",$deliveryprotectcheck);
	$objResponse->addAssign("orderprotect","innerHTML",$ShopCurrency->sign."0");
	$objResponse->addAssign("hiddendelivery","innerHTML","<input type=\"hidden\" name=\"deliveryfee\" value=\"".$deliveryprice."\" />");
	$objResponse->addAssign("hiddenprotect","innerHTML","<input type=\"hidden\" name=\"protectfee\" value=\"".$protectprice."\" />");
	if ($i > 0)
	{
		if(LICENSE_8 == 2){
			$showpayment = preg_replace('/="pictures\//', "=\"".$INC_SHOPDIR."pictures/", $showpayment);
			$showpayment = preg_replace('/=\'pictures\//', "='".$INC_SHOPDIR."pictures/", $showpayment);
			$showpayment = preg_replace('/=pictures\//', "=".$INC_SHOPDIR."pictures/", $showpayment);
		}
		$objResponse->addAssign("paymentinput", "innerHTML", $showpayment);
		$script = "xajax_chgPayment({$paymentid}, {$deliveryprice}, 0, {$firstprice}, {$commoprice}, {$taxratio},{$consumepoint}, {$totalCouponsDiscount});";
		$objResponse->addScript($script);
	}
	else{
		$ordertotal = moneyFormat($deliveryprice+$paymentprice+$commoprice*$nowrate+($commoprice * $taxratio / 100)*$nowrate-$consumepoint*$nowrate-$totalCouponsDiscount*$nowrate, $INC_SHOPID);
		$objResponse->addAssign("ordertotal", "innerHTML", $ShopCurrency->sign.$ordertotal);
		$script = "alert('{$PROG_TAGS["ptag_1308"]}');";
		$objResponse->addScript($script);
		
		$tmpLog = dirname(__FILE__)."/../include/upgrade.log";
		$firstPart = file_get_contents($tmpLog);
		$fw = @fopen($tmpLog, "wb");
		fwrite($fw, $firstPart."\n".$alertStr."\nCOOKIE=".$_COOKIE["COOKIE_BUTTON"]."\nINC_USERID=".$INC_USERID."\nINC_USERISREG=".$INC_USERISREG);
		chmod($tmpLog, 0666);
		fclose($fw);
	}

	return $objResponse->getXML();
}

//选择配送保价触发
function selectProtect($deliveryprice, $protectprice, $paymentprice, $commoprice, $taxratio, $consumepoint, $totalCouponsDiscount)
{
	global $ShopCurrency;
	global $INC_SHOPID;
	global $INC_USERCURRENCY;
	global $nowrate;

	//	$ShopCurrency = newclass("ShopCurrency");
	//	$ShopCurrency->shopId = $INC_SHOPID;
	//	$nowrate = $ShopCurrency->getCurRate($INC_USERCURRENCY);

	$objResponse = new xajaxResponse();
	$objResponse->addAssign("orderprotect", "innerHTML", $ShopCurrency->sign.$protectprice);
	$ordertotal = moneyFormat($deliveryprice+$protectprice+$paymentprice+$commoprice*$nowrate+($commoprice * $taxratio / 100)*$nowrate-$consumepoint*$nowrate-$totalCouponsDiscount*$nowrate, $INC_SHOPID);
	$objResponse->addAssign("ordertotal", "innerHTML", $ShopCurrency->sign.$ordertotal);
	//	$objResponse->addAssign("hiddenprotect","innerHTML","<input type=\"hidden\" name=\"protectfee\" value=\"".$protectprice."\" />");

	return $objResponse->getXML();
}

//选择支付方式触发
function chgPayment($payid, $deliveryprice, $protectprice, $paymentprice, $commoprice, $taxratio, $consumepoint, $totalCouponsDiscount)
{
	global $INC_SHOPID;
	global $PROG_TAGS;
	global $INC_USERCURRENCY;
	global $nowrate;

	$shopPayment = newclass("shopPayment");
	$shopPayment->shopId = $INC_SHOPID;
	$shopPayment->getbyId($payid);

	$ShopCurrency = newclass("ShopCurrency");
	$ShopCurrency->shopId = $INC_SHOPID;
	$returnvalue = $ShopCurrency->getCurbyPayment(0, "", $INC_USERCURRENCY, $shopPayment->curpay);
	$nowrate = $ShopCurrency->getCurRate($INC_USERCURRENCY);

	$objResponse = new xajaxResponse();
	$objResponse->addAssign("paycurrency","innerHTML",$returnvalue);
	$objResponse->addAssign("orderpayment", "innerHTML", $ShopCurrency->sign.$paymentprice);
	$ordertotal = moneyFormat($deliveryprice+$protectprice+$paymentprice+$commoprice*$nowrate+($commoprice * $taxratio / 100)*$nowrate-$consumepoint*$nowrate-$totalCouponsDiscount*$nowrate, $INC_SHOPID);
	$objResponse->addAssign("ordertotal", "innerHTML", $ShopCurrency->sign.$ordertotal);
	$objResponse->addAssign("hiddenpayment","innerHTML","<input type=\"hidden\" name=\"paymentfee\" value=\"".$paymentprice."\" />");

	return $objResponse->getXML();
}

//选择配送报价触发
//	xajax_checkInvoice(nowdeliveryfee, nowprotectfee, nowpaymentfee, commoprice, nowtaxratio, nowcouponsfee);
function checkInvoice($deliveryprice, $protectprice, $paymentprice, $commoprice, $taxratio, $consumepoint, $totalCouponsDiscount)
{
	global $ShopCurrency;
	global $INC_SHOPID;
	global $nowrate;

	$objResponse = new xajaxResponse();
	$objResponse->addAssign("orderprotect", "innerHTML", $ShopCurrency->sign.$protectprice);
	$ordertotal = moneyFormat($deliveryprice+$protectprice+$paymentprice+$commoprice*$nowrate+($commoprice * $taxratio / 100)*$nowrate-$consumepoint*$nowrate-$totalCouponsDiscount*$nowrate, $INC_SHOPID);
	$objResponse->addAssign("ordertotal", "innerHTML", $ShopCurrency->sign.$ordertotal);

	return $objResponse->getXML();
}

function chgConsumePoint($deliveryprice, $protectprice, $paymentprice, $commoprice, $taxratio, $consumepoint, $totalCouponsDiscount)
{

	global $INC_SHOPID;
	global $PROG_TAGS;
	global $INC_USERCURRENCY;
	global $nowrate;
	global $shopSetup;
	global $ShopCurrency;
	global $INC_USERID;
	global $INC_USERLEVEL;

	if ($consumepoint<0)
	{
		$consumepoint = 0;
	}

	$objResponse = new xajaxResponse();

	//特殊会员禁止使用抵扣积分 20070820 bryant
	$memberLevel = newclass("MemberLevel");
	$memberLevel->shopId = $INC_SHOPID;
	$memberLevel->getbyId($INC_USERLEVEL);
	if ($memberLevel->point == -1) {
		$objResponse->addScript("alert('".$PROG_TAGS['ptag_special_level_refuse']."');");
		return $objResponse->getXML();
	}

	$cart_arr = getArrayCart();
	$totalConsumePoint = $cart_arr['consumepoint'];
	$actualConsumePoint = $totalConsumePoint > $consumepoint?$consumepoint :$totalConsumePoint;
	
	$User = newclass("User");
	$User->getbyId($INC_USERID);
	//如果用户积分不足
	$tempOrderTotal = $commoprice*$nowrate+($commoprice * $taxratio / 100)*$nowrate-$pointToMoney*$nowrate-$totalCouponsDiscount*$nowrate;
	if ($User->point>0 && $User->point>=$actualConsumePoint && $actualConsumePoint>= 0)
	{
		$pointToMoney = $actualConsumePoint / $shopSetup->getValue('point_rate');

		$ordertotal = $commoprice*$nowrate+($commoprice * $taxratio / 100)*$nowrate-$pointToMoney*$nowrate-$totalCouponsDiscount*$nowrate;
		$ordertotal = moneyFormat($deliveryprice+$protectprice+$paymentprice+$commoprice*$nowrate+($commoprice * $taxratio / 100)*$nowrate-$pointToMoney*$nowrate-$totalCouponsDiscount*$nowrate, $INC_SHOPID);
		$ordertotalBasic = moneyFormat($deliveryprice+$protectprice+$paymentprice, $INC_SHOPID);
		$ordertotalExceptBasic = moneyFormat($commoprice*$nowrate+($commoprice * $taxratio / 100)*$nowrate-$pointToMoney*$nowrate-$totalCouponsDiscount*$nowrate, $INC_SHOPID);
		if ($ordertotalExceptBasic < 0)
		{
			$ordertotal = $ordertotalBasic;
			$pointToMoney =  moneyFormat($commoprice*$nowrate+($commoprice * $taxratio / 100)*$nowrate-$totalCouponsDiscount*$nowrate, $INC_SHOPID);
			$actualConsumePoint = $pointToMoney * $shopSetup->getValue('point_rate');
			
		}
		$objResponse->addAssign("point_money", "innerHTML", $ShopCurrency->sign.moneyFormat($pointToMoney*$nowrate, $INC_SHOPID));//2006-06-15 修改,增加 nowrate
		$objResponse->addAssign("consumepointshow", "innerHTML", intval($actualConsumePoint)*$nowrate);

		$objResponse->addAssign("ordertotal", "innerHTML", $ShopCurrency->sign.$ordertotal);

		$objResponse->addAssign("hiddenconsumepoint", "innerHTML", "<input class=\"inputstyle\" name=\"hconsumepoint\" value=\"".$pointToMoney."\" />");
		$objResponse->addAssign("hiddennconusmepoint", "innerHTML", "<input class=\"inputstyle\" name=\"hnconsumepoint\" value=\"".$actualConsumePoint."\" />");
	}
	return $objResponse->getXML();
}

function chgMemcCode($memccode, $memclist, $deliveryprice, $protectprice, $paymentprice, $commoprice, $taxratio, $consumepoint)
{
	global $INC_SHOPID;
	global $INC_USERID;
	global $PROG_TAGS;
	global $INC_USERCURRENCY;
	global $nowrate;
	global $shopSetup;
	global $ShopCurrency;

	//初始化
	$memccode = trim($memccode);

	$Coupon = newclass('Coupon');
	$Coupon->shopId = $INC_SHOPID;

	$objResponse = new xajaxResponse();
	$memcListStr = '';
	
	$totalCouponsDiscount = 0;//订单中使用优惠券价值总额
	$totalCouOrderDiscount = 0;//订单中优惠券已消费(订单金额大于等于)金额总计
	
	$memclistArr = array();
	if (!empty($memclist))
	{
		$memclistArr = explode(',', $memclist);	 
		foreach ($memclistArr as $v)
		{			
			$cpns_prefix = substr($v, 0, strlen($v) - 8);
			if  ($Coupon->getbyPrefix($cpns_prefix))
			{
				$ordertotalExceptCoupons = $commoprice*$nowrate+($commoprice * $taxratio / 100)*$nowrate-$consumepoint*$nowrate;
				//判断优惠券的金额是否大于目前订单金额
				if ($ordertotalExceptCoupons - $totalCouponsDiscount*$nowrate - $Coupon->cpns_discount*$nowrate < 0){
					$temp = $ordertotalExceptCoupons - $totalCouponsDiscount*$nowrate;
					$memcListStr .= addMemcHtml($v, $temp, $Coupon);
					$totalCouponsDiscount += $temp;
					$memcerror = $PROG_TAGS["ptag_orderlesszero"];//订单商品总价为0,不能使用优惠券;
					$objResponse->addAssign("memcerror", "innerHTML", $memcerror);  
				}else{
					$memcListStr .= addMemcHtml($v, $Coupon->cpns_discount*$nowrate, $Coupon);
					$totalCouponsDiscount += $Coupon->cpns_discount;
				}
				$totalCouOrderDiscount += $Coupon->cpns_use_order_limit;
			}
		}
	}

	$couponOrderLimit = $shopSetup->getValue('coupon_order_limit');
	if ($couponOrderLimit>0 && count($memclistArr)==$couponOrderLimit){
		$memcerror = $PROG_TAGS["ptag_orderlimitcouponnums"].$couponOrderLimit;//一个订单限制使用优惠券数量为{$couponOrderLimit}
		$objResponse->addAssign("memcerror", "innerHTML", $memcerror);  //$PROG_TAGS["ptag_1305"]
	}else if (in_array($memccode, $memclistArr)){
		$memcerror = $PROG_TAGS["ptag_orderforbidsamecoupon"];		//同一个订单中不能多次使用同一优惠券
		$objResponse->addAssign("memcerror", "innerHTML", $memcerror); 
	}else{
		$shopCart = newclass("shoppingCart");
		$shopCart->loadAll();	
		$gidArr = array();
		while ($shopCart->next()){	
			if ($shopCart->Item["shopid"]==$INC_SHOPID){
				$gidArr[] = $shopCart->Item["goodsid"];
			}
		}
		$memberCoupon = newclass('memberCoupon');
		$memberCoupon->shopId = $INC_SHOPID;
		//使用优惠券. 最后一个参数代表订单目前金额(商品总额-换购金额-优惠券价值
		$memcerror = $memberCoupon->useUserCoupons($memccode, $INC_USERID, $gidArr, 0, '', $commoprice-$consumepoint-$totalCouOrderDiscount);
		if ($memcerror!==true){
			$objResponse->addAssign("memcerror", "innerHTML", $memcerror);
		}else{
			$cpns_prefix = substr($memccode, 0, strlen($memccode) - 8);
			if  ($Coupon->getbyPrefix($cpns_prefix)){
				//判断增加新得优惠券后订单是否为负
				$ordertotalExceptCoupons = $commoprice*$nowrate+($commoprice * $taxratio / 100)*$nowrate-$consumepoint*$nowrate;
				if (($ordertotalExceptCoupons - $totalCouponsDiscount*$nowrate) > 0){
					if ($ordertotalExceptCoupons - $totalCouponsDiscount*$nowrate - $Coupon->cpns_discount*$nowrate < 0){
						$temp = $ordertotalExceptCoupons - $totalCouponsDiscount*$nowrate;
						$memcListStr .= addMemcHtml($memccode, $temp, $Coupon);
						$totalCouponsDiscount = $ordertotalExceptCoupons;
					}else{
						$totalCouponsDiscount += $Coupon->cpns_discount;
						$memcListStr .= addMemcHtml($memccode, $Coupon->cpns_discount*$nowrate, $Coupon);
					}
				}
			}
			$objResponse->addAssign("memcerror", "innerHTML", ''); 
		}
	}
	$objResponse->addAssign("memclist", "innerHTML", $memcListStr);
	$ordertotal = moneyFormat($deliveryprice+$protectprice+$paymentprice+$commoprice*$nowrate+($commoprice * $taxratio / 100)*$nowrate-$consumepoint*$nowrate -$totalCouponsDiscount*$nowrate, $INC_SHOPID);
	$objResponse->addAssign("ordertotal", "innerHTML", $ShopCurrency->sign.$ordertotal);

	return $objResponse->getXML();
}
$headjs = $xajax->getJavascript();

//*******************XML 输出 END*****************/

/* 实例化购物车类 */
$shopCart = newclass("shoppingCart");
/* 读取购物车信息 */
$shopCart->loadAll();

/* 实例化购物车类 */
$giftCart = newclass("giftCart");

/* 读取购物车信息 */
$giftCart->loadAll();

/* 如果购物车中没有商品，转到出错页面 */
if ($shopCart->nf()==0&&$giftCart->nf()==0){
	header("Location: index.php?gOo=error.dwt&errinfo=".base64_urlencode($PROG_TAGS["ptag_1305"])."&hrefurl=".base64_urlencode("shop.dwt")."&hrefname=".base64_urlencode($PROG_TAGS["ptag_1306"]));
	exit;
}

/* 如果用户ID为0，转到是否注册页面 */
if ($INC_USERID == 0){
	header("Location: index.php?gOo=isregister.dwt&dwtfile=".urlencode($_GET["gOo"]));
	exit;
}

/* 如果是注册用户登陆，读取收货人信息*/
if ($INC_USERISREG <> 0){
	$MemberReceiver = newclass("MemberReceiver");
	$MemberReceiver->shopId = $INC_SHOPID;
	$MemberReceiver->memberId = $INC_USERID;
	$tmp_tag = $MemberReceiver->getDefault();
	if($tmp_tag){
		$MemberReceiver->getbyId($tmp_tag);

		$tmp_receiveid = $MemberReceiver->receiveId;
		$tmp_email = $MemberReceiver->email;
		$tmp_name = $MemberReceiver->name;
		$tmp_address = $MemberReceiver->address;
		$tmp_zip = $MemberReceiver->zipcode;
		$tmp_mobile = $MemberReceiver->mobile;
		$tmp_telphone = $MemberReceiver->telphone;
		$tmp_province = $MemberReceiver->province;
	}else{
		$receiveruser = newclass("User");
		$receiveruser->userId = $INC_USERID;
		$receiveruser->getbyId();

		$tmp_receiveid = "";
		$tmp_email = $receiveruser->email;
		$tmp_name = $receiveruser->name;
		$tmp_address = $receiveruser->address;
		$tmp_zip = $receiveruser->zip;
		$tmp_province = $receiveruser->province;
		$tmp_mobile = $receiveruser->mobile;
		$tmp_telphone = $receiveruser->tel;
	}
	$tmp_show = "";
	$t->set_hidden("receiverselect", false);
	$t->set_hidden("receiversave", false);
}else{
	$t->set_hidden("receiverselect", true);
	$t->set_hidden("receiversave", true);
	$tmp_show = "none";
}

$DeliverArea = newclass("DeliverArea");
$DeliverArea->shopId = $INC_SHOPID;
/* 输出收货人信息 */
$t->set_var("receiverinput_show",$tmp_show);
$t->set_var("recv_id",$tmp_receiveid);		/*收货人ID*/
$t->set_var("recv_email",$tmp_email);		/*收货人email*/
$t->set_var("recv_name",$tmp_name);		/*收货人姓名*/
$t->set_var("recv_address",$tmp_address);		/*收货人地址*/
$t->set_var("recv_zip",$tmp_zip);		/*收货人邮编*/
$t->set_var("recv_tel",$tmp_telphone);		/*收货人电话*/
$t->set_var("recv_mobile",$tmp_mobile);		/*收货人手机*/
$t->set_var("recv_area",$DeliverArea->getSelectArea(1, $PROG_TAGS["ptag_1781"], $tmp_province));		/*收货人所属配送区域*/
$t->set_var("recv_province",makecitychooser("receiver_province",$tmp_province,$PROG_TAGS["ptag_35"],0));		/*收货人所在地区*/

/****************收货人信息，结束*****************/

$shopCart = newclass("shoppingCart");
$shopCart->loadAll();

$goods = newclass("Product");

$i=0;
$t->pushNode("orderdetails_giftsrow");

$Gift = newclass("Gift");
$giftWeightAll = 0;
$giftPointAll = 0;

while ($giftCart->next()){
	if ($giftCart->Item["shopid"]==$INC_SHOPID){
		$t->pushNode($i);
		$Gift->shopId = $giftCart->Item["shopid"];
		$Gift->getGiftbyId($giftCart->Item["goodsid"]);

		$t->set_var("gift_small_img", $Gift->getSmallImgTag());

		$Gift->getGiftById($giftCart->Item["goodsid"]);

		$giftWeight = $giftCart->Item["quantity"] * $Gift->weight;
		$giftWeightAll += $giftWeight;
		$giftPoint = $giftCart->Item["quantity"] * $Gift->point;
		$giftPointAll += $giftPoint;

		$t->set_var("gift_name", $Gift->name);
		$t->set_var("order_gift_weight", $giftWeight);
		$t->set_var("gift_quantity", $giftCart->Item["quantity"]);
		$t->set_var("gift_price", intval($Gift->price));
		$t->set_var("gift_point", $Gift->point);
		$t->set_var("line_gift_amount", $Gift->point * $giftCart->Item["quantity"]);

		$i++;
		$t->popNode();
	}
}
$t->popNode();
$t->set_var("order_consume_point", $giftPointAll);

$i = 0;
$totalmoney = 0;	//商品总金额
$totalConsumePoint = 0;
$t->pushNode("orderdetails_goodsrow");

while ($shopCart->next()){	
	if ($shopCart->Item["shopid"]==$INC_SHOPID){
		$t->pushNode($i);
		$goods->shopId = $shopCart->Item["shopid"];
		$goods->getbyId($shopCart->Item["goodsid"]);

		if (file_exists("../../home/shop/$INC_SHOPID/".$goods->smallImgUrl)){
			$t->set_var("goods_small_image","<img src=\"".$goods->smallImgUrl."\" width=\"".$INC_THUMBNAILWIDTH."\" height=\"".$INC_THUMBNAILHEIGHT."\" border=\"0\" alt=\"\" />");		/*商品小图*/
		}else{
			$t->set_var("goods_small_image","");		/*商品小图*/
		}

		$discreteness = "";
		if (isset($shopCart->Item["goodsinfo"]["props"])){
			$strvalueid = implode(",", $shopCart->Item["goodsinfo"]["props"]);
			$GoodsProp = newclass("GoodsProp");
			$GoodsProp->shopId = $INC_SHOPID;
			$arr_name = $GoodsProp->getNamebyStrid($strvalueid);
			/* 循环分解每个属性 */
			if(is_array($arr_name))
				foreach($arr_name as $propname)
				{
					$discreteness .= " + ".$propname;
				}
		}
		if (isset($shopCart->Item["goodsinfo"]["adjunct"])){
			/* 循环分解每个配件 */
			foreach ($shopCart->Item["goodsinfo"]["adjunct"] as $propid => $arr_adj){
				if (is_array($arr_adj)){
					$strvalueid = implode(",", array_keys($arr_adj));
					$PropGoods = newclass("PropGoods");
					$PropGoods->shopId = $INC_SHOPID;
					$arr_name = $PropGoods->getNamebyStrid($strvalueid);
					if (is_array($arr_name))
						foreach($arr_name as $adjgid => $adjname){
							$discreteness .= " + ".$adjname." (".$arr_adj[$adjgid].") ";
						}
				}
			}
		}
		$discreteness = substr($discreteness, 3);

		$memprice = $goods->getPrice($shopCart->Item['goodsinfo'],$INC_USERLEVEL,$shopCart->Item['quantity']);

		//todo:原始总价格如下
		//$totalprice = $goods->getTotalPrice($shopCart->Item['goodsinfo']);
		
		$t->set_var("goods_bn",$goods->bn);		/*商品编号*/
		$t->set_var("goods_id",$shopCart->Item["goodsid"]);		/*商品ID*/
		$t->set_var("goods_name",$goods->productName);		/*商品名称*/
		$t->set_var("discreteness",$discreteness);		/*组合商品附加文字*/ 
		if($frontShow->marketprice == 1){
			$mktPrice = $ShopCurrency->sign.(number_format($goods->priceDesc * $nowrate, 2, ".", ""));
		}else{
			$mktPrice = "";
		}
		$t->set_var("goods_price1",$mktPrice);		/*商品市场价*/
		$t->set_var("goods_price2",$ShopCurrency->sign.(number_format($memprice * $nowrate, 2, ".", "")));		/*商品结算价*/
		$t->set_var("quantity",$shopCart->Item["quantity"]);		/*商品数量*/
		$t->set_var("line_amount",$ShopCurrency->sign.number_format($memprice * $nowrate * $shopCart->Item["quantity"], 2, '.', ''));		/*商品价格小计*/

		$zjweight = $goods->getAdjWeight($shopCart->Item['goodsinfo']);
		//todo:组建重量需要统计到 $zjweight
		$t->set_var("order_weight",($goods->weight + $zjweight) * $shopCart->Item["quantity"]);		/*订单商品总重量*/
		$i++;
		$totalmoney += $memprice * $shopCart->Item["quantity"];
		$t->popNode();
	}
}
$t->popNode();
$User = newclass("User");
$User->getbyId($INC_USERID);

$cart_arr = getArrayCart();
$totalConsumePoint = $cart_arr['consumepoint'];
$t->set_var("allow_consume_point_total", $totalConsumePoint);
$t->set_var("point_rate", $shopSetup->getValue('point_rate'));
$t->set_var("user_point", $User->point);

//$t->set_var("point_rate", $shopSetup->getValue('point_rate');
/* 输出订单各项金额 */
$t->set_var("order_freight",$ShopCurrency->sign."0.00");		/*订单运费*/
$t->set_var("orderdetails_paymentprice",$ShopCurrency->sign."0.00");
$t->set_var("order_weight",$weight);		/*订单商品总重量*/
$t->set_var("order_protect",$ShopCurrency->sign."0.00");		/*订单配送保价金额*/
$t->set_var("commoditymoney",$totalmoney);		/*商品总价*/
$t->set_var("order_paymoney",$ShopCurrency->sign.moneyFormat($totalmoney * $nowrate, $INC_SHOPID));		/*订单支付总金额*/

if(!isset($shopinfo)){
	$shopinfo = newclass("indexShop");
	$shopinfo->getbyId($INC_SHOPID);
}
if ($shopinfo->iftax == 1){
	$t->set_hidden("invoiceshow", false);
	$orderdetails_taxmoney = $dprice + $totalmoney * (1 + $shopinfo->taxratio);
}else{
	$t->set_hidden("invoiceshow", true);
}

$frontShow = newclass("frontShow");
$frontShow->shopId = $INC_SHOPID;
$frontShow->loadFrontShow();
if ($frontShow->deliverytime < 0)
	$t->set_hidden("sendtime", true);
else{
	$t->set_hidden("sendtime", false);

	$deliverytime = time() + $frontShow->deliverytime;
	$receiver_year = date("Y", $deliverytime);
	$receiver_month = date("n", $deliverytime);
	$receiver_day = date("j", $deliverytime);
	if (date("i", $deliverytime) <= 30)
		$receiver_minute = 30;
	else
		$receiver_minute = 60;
	$receiver_hour = date("G", $deliverytime) * 3600 + $receiver_minute * 60;

	$tmp_year = array(date("Y", mktime()), date("Y", mktime())+1);
	$tmp_month = range(1, 12);
	$tmp_day = range(1, 31);
	for($i=0; $i<84700; $i+=1800) $tmp_hourvalue[] = $i;
	$tmp_hourshow = array("00:00", "00:30", "01:00", "01:30", "02:00", "02:30",
		"03:00", "03:30", "04:00", "04:30", "05:00", "05:30",
		"06:00", "06:30", "07:00", "07:30", "08:00", "08:30",
		"09:00", "09:30", "10:00", "10:30", "11:00", "11:30",
		"12:00", "12:30", "13:00", "13:30", "14:00", "14:30",
		"15:00", "15:30", "16:00", "16:30", "17:00", "17:30",
		"18:00", "18:30", "19:00", "19:30", "20:00", "20:30",
		"21:00", "21:30", "22:00", "22:30", "23:00", "23:30");
	$receiver_year = makedateselect(0, "", $tmp_year, $receiver_year, $tmp_year);
	$receiver_month = makedateselect(0, "", $tmp_month, $receiver_month, $tmp_month);
	$receiver_day = makedateselect(0, "", $tmp_day, $receiver_day, $tmp_day);
	$receiver_hour = makedateselect(0, "", $tmp_hourshow, $receiver_hour, $tmp_hourvalue);
	$t->set_var("recv_sel_year", $receiver_year);		/*要求送货时间下拉(年)*/
	$t->set_var("recv_sel_month", $receiver_month);		/*要求送货时间下拉(月)*/
	$t->set_var("recv_sel_day", $receiver_day);		/*要求送货时间下拉(日)*/
	$t->set_var("recv_sel_hour", $receiver_hour);		/*要求送货时间下拉(时)*/
}

if ($shopinfo->pointtype == 2){
	$cart_arr = getArrayCart();
	$orderdetails_point = $cart_arr['pointnum'];
}
else if($shopinfo->pointtype == 1){
	$orderdetails_point = Floor($totalmoney * $shopinfo->pointnum);
}
else{
	$orderdetails_point = 0;
}
$t->set_var("order_point", $orderdetails_point);		/*订单应得积分*/
$t->set_var("order_tax", $ShopCurrency->sign.(sprintf ("%01.2f", $orderdetails_taxmoney * $nowrate)));		/*订单税额*/
$t->set_var("taxratio", $shopinfo->taxratio * 100);		/*税率*/
$t->set_var("order_currency", "");		/*订单支付货币*/

$t->set_var("form_hidden_value", tplautodo("neworder_act.php", "0", '', '').$headjs);		/*表单的隐藏域内容*/

//生成选择菜单的函数
function makedateselect($mark=0,$markname,$arr_name,$hadselected,$arry_value){
	$num = count($arr_name) ;
	if($mark <> 0){
		$x = "<option value=\"0\">- ".$markname." -</option>\n";
	}
	for($i=0 ; $i < $num ; $i++){
		if ($hadselected ==  $arry_value[$i]) {
			$x .= "<option selected value='$arry_value[$i]'>".$arr_name[$i]."</option>\n" ;
		}else{
			$x .= "<option value='$arry_value[$i]'>".$arr_name[$i]."</option>\n"  ;	   	
		}
	}
	return $x ;
}

function getArrayCart(){
	global $INC_SHOPID, $shopinfo, $INC_USERLEVEL;
	$shopCart = newclass("shoppingCart");
	$shopCart->loadAll();

	$giftCart = newclass("giftCart");
	$giftCart->loadAll();

	$totalmoney = 0;	//商品总价格
	$weight = 0;		//商品总重量
	$pointnum = 0;

	$shopSetup = newclass("shopSetup");
	$shopSetup->shopId = $INC_SHOPID;
	$totalConsumePoint = 0;  //消费积分
	$goodsQuantity = 0;    //商品总数量

	while ($giftCart->next()){
		if ($giftCart->Item["shopid"]==$INC_SHOPID){
			$Gift = newclass("Gift");
			$Gift->shopId = $giftCart->Item["shopid"];
			$Gift->getGiftbyId($giftCart->Item["goodsid"]);

			$weight += ($Gift->weight) * $giftCart->Item["quantity"];
		}
	}
	while ($shopCart->next()){
		if ($shopCart->Item["shopid"]==$INC_SHOPID){
			$goods = newclass("Product");
			$goods->shopId = $shopCart->Item["shopid"];
			$goods->getbyId($shopCart->Item["goodsid"]);

			/*{{{*/
			//    组合商品
			//    $zjweight = 0;		//组件商品总重量
			//    if ($goods->ifdiscreteness == 1 && !empty($shopCart->Item["addgid"]))
			//    {
			//      /***********读出组件商品价格数组*************/
			//      for ($iLoop=1; $iLoop<=40; $iLoop++)
			//      {
			//        $attrfield="attr".$iLoop;
			//        if (substr($goods->$attrfield, 0, 1) == "@")
			//        {
			//          $attr_zj = explode("@", $goods->$attrfield);
			//          $tmp_num = count($attr_zj);
			//          for ($jLoop = 1; $jLoop < $tmp_num-1; $jLoop++)
			//          {	//遍历组合商品组件
			//            $good_price = explode(",", $attr_zj[$jLoop]);
			//            $arr_zjprice[$good_price[0]] = $good_price[1];	//组件价格数组
			//          }
			//        }
			//      }
			//      /***********读出组件商品价格数组 END*************/

			//      /***********计算组件商品价格和重量*************/
			//      $arr_zh = explode("@", $shopCart->Item["addgid"]);
			//      $tmp_num = count($arr_zh);
			//      $GoodInfo = newclass("Product");
			//      $GoodInfo->shopId = $shopCart->Item["shopid"];
			//      $zj_price = 0;		//组件商品总价格
			//      $discreteness = "";	//组件商品组合显示字串
			//      for ($iLoop = 1; $iLoop < $tmp_num; $iLoop++)
			//      {
			//        $arr_zhnum = explode("^", $arr_zh[$iLoop]);
			//        if (count($arr_zhnum) < 2) $arr_zhnum[1] = 1;
			//        $zj_price += $arr_zjprice[$arr_zhnum[0]] * $arr_zhnum[1];
			//        $GoodInfo->getbyId($arr_zhnum[0]);
			//        $discreteness .= "+ ".$GoodInfo->productName." (".$arr_zhnum[1].") ";
			//        $zjweight += $GoodInfo->weight * $arr_zhnum[1];
			//      }
			//      $discreteness = "<br />".$discreteness;
			//      $saleprice = $goods->basicprice;
			//      $defzj_price = $goods->price - $goods->basicprice;	//默认组件价格
			//      /***********计算组件商品价格和重量 END*************/
			//    }
			//    else{
			//      $discreteness = "";
			//      $zj_price = 0;
			//      $saleprice = $goods->price;
			//      $defzj_price = 0;
			//    }
			//    $weight += ($goods->weight + $zjweight) * $shopCart->Item["quantity"];

			//    /***********计算商品会员价格*************/
			//    if ($INC_USERLEVEL > 0)
			//    {
			//      $MemberPrice = newclass("MemberPrice");
			//      $arr_price = $MemberPrice->getMemPrice($shopCart->Item["goodsid"]);

			//      if ($arr_price != false)
			//      {
			//        $memprice = $arr_price[$INC_USERLEVEL] - $defzj_price;
			//        if ($memprice === "")
			//          $memprice = $saleprice ;
			//        $memprice += $zj_price;
			//      }
			//      else
			//        $memprice = $saleprice + $zj_price;
			//    }
			//    else
			//      $memprice = $saleprice + $zj_price;
			//    /***********计算商品会员价格 END*************/
			//    if($goods->wholesale_scheme>0) //如果批发商品
			//    {
			//      include(dirname(__FILE__)."/inc.wholesale.php");

			//    }
			/*}}}*/

			$memprice = $goods->getPrice($shopCart->Item['goodsinfo'],$INC_USERLEVEL,$shopCart->Item['quantity']);

			//todo:原始总价格如下
			//$totalprice = $goods->getTotalPrice($shopCart->Item['goodsinfo']);

			//--------------------------------------------
			//重量累加
			//todo:组建重量
			// $zjweight
			
			$zjweight = $goods->getAdjWeight($shopCart->Item['goodsinfo']);
			$weight += ($goods->weight + $zjweight) * $shopCart->Item["quantity"];
			//积分累加
			if ($shopinfo->pointtype == 2) $pointnum += ($goods->point * $shopCart->Item["quantity"]);
			//商品价格累加
			$totalmoney += $memprice * $shopCart->Item["quantity"];
			/***********计算消费积分 BEGIN*************/

			if ($shopSetup->getValue('point_policy') == 'local')
			{
				switch ($goods->pointConsumeMethod)
				{
				case 'value':
					$totalConsumePoint += intval($goods->pointConsumeMethodValue) * $shopCart->Item["quantity"];
					break;
				case 'percent':
					//totalprice为单个商品加上配件总价
					$totalConsumePoint += intval($goods->pointConsumeMethodPercent / 100 * $totalmoney * $shopSetup->getValue('point_rate'));
					break;
				default:
					break;					
				}
			}
		

			$goodsQuantity = $goodsQuantity + $shopCart->Item["quantity"];
			/***********计算消费积分 END*************/
		}
	}
	if ($shopSetup->getValue('point_policy') == 'global')
	{
		switch ($shopSetup->getValue('point_method'))
		{
			case 'value':						
				$totalConsumePoint += intval($shopSetup->getValue('point_method_value')) * $goodsQuantity;
				break;
			case 'percent':
				$totalConsumePoint += intval($shopSetup->getValue('point_method_percent') / 100 * $totalmoney * $shopSetup->getValue('point_rate'));
				break;
			default:
				break;
		}
	}

	$ret['consumepoint'] = $totalConsumePoint;
	$ret['weight'] = $weight;
	$ret['totalmoney'] = $totalmoney;
	$ret['pointnum'] = $pointnum;
	return $ret;
}

function getGiftPrice()
{
	global $INC_SHOPID;
	$giftCart = newclass("giftCart");
	$giftCart->loadAll();
	$price = 0;
	while ($giftCart->next())
	{
		if ($giftCart->Item["shopid"]==$INC_SHOPID)
		{
			$Gift = newclass("Gift");
			$Gift->shopId = $giftCart->Item["shopid"];
			$Gift->getGiftbyId($giftCart->Item["goodsid"]);

			$price += ($Gift->price) * $giftCart->Item["quantity"];

		}

	}
	return $price;
}
//统计此订单允许换购的积分点数


function getConsumePoint()
{

	global $INC_SHOPID, $INC_USERLEVEL;
	$shopSetup = newclass("shopSetup");
	$shopSetup->shopId = $INC_SHOPID;

	$goods = newclass("Product");
	$shopCart = newclass("shoppingCart");
	$shopCart->loadAll();	

	$totalConsumePoint = 0;

	while ($shopCart->next())
	{	
		$goods->shopId = $shopCart->Item["shopid"];
		$memprice = $goods->getPrice($shopCart->Item['goodsinfo'],$INC_USERLEVEL,$shopCart->Item['quantity']);

		if ($shopCart->Item["shopid"]==$INC_SHOPID)
		{

			if ($shopSetup->getValue('point_policy') == 'global')
			{
				switch ($shopSetup->getValue('point_method'))
				{
					case 'value':

						$totalConsumePoint += intval($shopSetup->getValue('point_method_value')) * $shopCart->Item["quantity"];
						break;
					case 'percent':
						$totalConsumePoint += intval($shopSetup->getValue('point_method_percent') / 100 * $memprice * $shopCart->Item["quantity"] * $shopSetup->getValue('point_rate'));
						break;
					default:
						break;
				}
			}
			else if ($shopSetup->getValue('point_policy') == 'local')
			{
				$goods->getbyId($shopCart->Item["goodsid"]);
				switch ($goods->pointConsumeMethod)
				{
					case 'value':
						$totalConsumePoint += intval($goods->pointConsumeMethodValue) * $shopCart->Item["quantity"];
						break;
					case 'percent':
						$totalConsumePoint += intval($goods->pointConsumeMethodPercent / 100 * $memprice * $shopCart->Item["quantity"]) * $shopSetup->getValue('point_rate');
						break;
					default:
						break;					
				}
			}
		}
	}
	return $totalConsumePoint;
}

function addMemcHtml($memccode, $memcdiscount,$coupon)
{
	global $ShopCurrency;
	global $INC_SHOPID;
	global $nowrate;

	$discountStr = $ShopCurrency->sign.($coupon->cpns_discount * $nowrate);

	$tempStr = "<div><span class=\"mcode\">".trim($memccode)."</span><input type=\"hidden\" name=\"memcdiscount[]\" value=\"".$memcdiscount."\" />
		<input type =\"hidden\" name=\"memccodes[]\" value=\"".trim($memccode)."\" /><span>{$coupon->cpns_name}</span><span>{$discountStr}</span><span onclick=\"delmemc(this)\">[x]</span></div>";
	return $tempStr;
}
?>