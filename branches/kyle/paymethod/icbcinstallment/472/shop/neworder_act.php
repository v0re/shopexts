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

$shopCart = newclass("shoppingCart");
$shopCart->loadAll();
$giftCart = newclass("giftCart");
$giftCart->loadAll();
$Gift = newclass("Gift");
$User = newclass("User");
$User->getbyId($INC_USERID);
$userPoint = $User->point;

$shopSetup = newclass("shopSetup");
$shopSetup->shopId = $INC_SHOPID;

if ($shopCart->nf()==0&&$giftCart->nf()==0){/*{{{*/
	header("Location: ./index.php?gOo=error.dwt&errinfo=".base64_urlencode($PROG_TAGS["ptag_1305"])."&hrefname=".base64_urlencode($PROG_TAGS["ptag_1306"])."&hrefurl=".urlencode("./index.php?gOo=shop.dwt"));
	exit;
}

//如果有赠品,开始检查库存,计算总重,合计积分

$giftWeightAll = 0;  //初始化重量合计
$giftPointAll = 0;	//初始化积分合计
$giftPriceAll = 0;	//初始化价格合计

if($giftCart->nf()>0)
{
	while ($giftCart->next())
	{
		if ($giftCart->Item["shopid"] == $INC_SHOPID)
		{
			$Gift->shopId = intval($giftCart->Item["shopid"]);		
			$Gift->getGiftById($giftCart->Item["goodsid"]);
			//查询当前赠品的库存
			if ($giftCart->Item["quantity"] >$Gift->storage)	//当购买后的赠品数量大于库存量
			{
				echo "<script type=\"text/javascript\">";
				echo "alert('".$PROG_TAGS["ptag_gift_stock_out"]."');";
				echo "history.back();";
				echo "</script>";
				exit;
			}
			//检查购买量限制
			if ($giftCart->Item["quantity"] > $Gift->limitNum && $Gift->limitNum>0)	//当购买后的赠品数量大于最大购买量 
			{
				echo "<script type=\"text/javascript\">";
				echo "alert('".$PROG_TAGS["ptag_gift_over_limit"]."');";
				echo "history.back();";
				echo "</script>";
				exit;

			}
			//检查礼品积是否为负
			if ($Gift->point<0){
				alertback($PROG_TAGS["ptag_gift_point_less_zero"]);
				exit;
			}

			//检查级别 2007-07-25 bryant
			$memberLevel = newclass("MemberLevel");
			$memberLevel->shopId = $INC_SHOPID;
			$memberLevel->getbyId($INC_USERLEVEL);
			$luserPoint = $memberLevel->point;//取得级别所需积分
			$memberLevel->getbyId($Gift->limitLevel);
			$reqPoint = $memberLevel->point;  //取得换购赠品需求级别的最低积分
			if($Gift->limitLevel>0){
				if($luserPoint<$reqPoint){
					echo "<script type=\"text/javascript\">";
					echo "alert('".$PROG_TAGS["ptag_gift_levellimit"]."');";
					echo "history.back();";
					echo "</script>";
					exit;
				}
			}
			$giftWeightAll += $Gift->weight*$giftCart->Item["quantity"];  //重量合计
			$giftPointAll += $Gift->point*$giftCart->Item["quantity"];	//积分合计
			$giftPriceAll += $Gift->price*$giftCart->Item["quantity"];	//初始化价格合计
		}
	}
}


//换购积分检查
if ($_POST['hnconsumepoint']<0)
{
	alertback($PROG_TAGS["ptag_consumpoint_less_zero"]);
	exit;
}
//用户积分检查
if($userPoint<$giftPointAll+$_POST['hnconsumepoint'])
{
	alertback($PROG_TAGS["ptag_point_not_enough"]);
	exit;
}
$shopPayment = newclass("shopPayment");
$shopPayment->shopId = $INC_SHOPID ;

if (!$shopPayment->getbyId($_POST["paymentid"]+0)){
	echo "<script type=\"text/javascript\">";
	echo "alert('".$PROG_TAGS["ptag_006"]."');";
	echo "location.href=document.referrer;";
	echo "</script>";
	exit ;
}

if (!isset($shopinfo)){
	$shopinfo = newclass("indexShop");
	$shopinfo->getbyId($INC_SHOPID);
}
//得出当前币别的汇率
$ShopCurrency = newclass("ShopCurrency");
$ShopCurrency->shopId = $INC_SHOPID;
$nowrate = $ShopCurrency->getCurRate($_POST["paycur"]);

$goods = newclass("Product");
$Order = newclass("Order");
$orderItems = newclass("orderItems");

$DeliverArea = newclass("DeliverArea");
$DeliverArea->shopId = $INC_SHOPID;
$DeliverArea->getbyId($_POST["receiver_area"]);

/*************新增订单***************/
if (isset($_POST["receiver_year"]))
{
	$sendtime = mktime(0, 0, 0, $_POST["receiver_month"]+0, $_POST["receiver_day"]+0, $_POST["receiver_year"]+0);
	$sendtime += $_POST["receiver_hour"];
}
else
	$sendtime = mktime();

$Order->shopId = $INC_SHOPID;
$Order->userId = $INC_USERID;
$Order->name = $_POST["receiver_name"];
$Order->address = $_POST["receiver_address"];
$Order->zip = $_POST["receiver_post"];
$Order->tel = $_POST["receiver_tele"];
$Order->email = $_POST["receiver_email"];
$Order->mobile = $_POST["receiver_mobile"];
$Order->area = intval($_POST["receiver_area"]);
$Order->deliveryArea = $DeliverArea->name;
$Order->freight = 0 ;
$Order->totalAmount = 0;
$Order->delivery = $_POST["deliveryid"];
$Order->payment = $_POST["paymentid"];
$Order->confirmed = 0;
$Order->delivered = 0;
$Order->payed = 0;
$Order->logged = 0;
$Order->memo = $_POST["receiver_memo"];
$Order->discount = 1;
$Order->Ip =$_SERVER["REMOTE_ADDR"];
$Order->ifinvoice = $_POST["ifinvoice"];
$Order->invoiceform = $_POST["invoiceform"];
$Order->paycur = $_POST["paycur"];
$Order->sendtime = $sendtime;
$Order->province = $_POST["receiver_province"];
$orderid = $Order->toInsert();
#设置分期数
$cookiename = "icbc_period[".$orderid."]";
setcookie($cookiename,$_POST["icbc_period"],time() + (60 * 60 * 24 * 7));
#
/*************新增订单 END***************/

if (!isset($frontShow)){
	$frontShow = newclass("frontShow");
	$frontShow->shopId = $INC_SHOPID;
	$frontShow->loadFrontShow();
}

//处理定单中赠品数据


//更新用户积分
//updateUserPoint, addGiftItemsHistory

//定单中商品数据
$orderItems->orderId = $orderid;
$orderItems->shopId = $INC_SHOPID;
$orderItems->userId = $INC_USERID;
$totalmoney=0;	//商品总金额
$weight=$giftWeightAll;		//商品总重量(初始化为赠品总重)
$pointnum = 0;  //初始化订单应得积分
$totalConsumePoint = 0; //订单消费总积分
$gidArr = array();

while ($shopCart->next()){
	if ($shopCart->Item["shopid"] == $INC_SHOPID){
		$goods->shopId = $shopCart->Item["shopid"];
		$goods->getbyId($shopCart->Item["goodsid"]);
		
		$infostr = array_pop(explode('.',$shopCart->Item['infostr']));
		$zjweight = 0;		//组件重量
		$grpid = 0;
		/*{{{判断是否带有属性或配件的商品*/
		if (!empty($infostr) && substr($infostr, 0, 1) != '#'){
			$GoodsPropGrp = newclass('GoodsPropGrp');
			$GoodsPropGrp->shopId = $INC_SHOPID;
			$grpid = $GoodsPropGrp->getGrpidByInfostr($shopCart->Item["goodsid"], $infostr);
			$GoodsPropGrp->getGrpbyId($grpid);
			if(!is_null($GoodsPropGrp->storage)){
				$goods->storage = $GoodsPropGrp->storage;
			}
			
			/*
			$GoodInfo = newclass("Product");
			$GoodInfo->shopId = $shopCart->Item["shopid"];
			if (is_array($shopCart->Item["goodsinfo"]["adjunct"])) {
				foreach($shopCart->Item["goodsinfo"]["adjunct"] as $arr_adj){
					foreach($arr_adj as $adjgid => $arrcount){
						$GoodInfo->getbyId($adjgid, false);
						$zjweight += $GoodInfo->weight * $arrcount;
					}
				}
			}*/
		}
		$zjweight = $goods->getAdjWeight($shopCart->Item['goodsinfo']);

		//查询当前商品的库存/*{{{*/
		if(!is_null($goods->storage)){
			if($frontShow->orderstorage == 1){
				$GoodsNotify = newclass("GoodsNotify");
				$GoodsNotify->shopId = $INC_SHOPID;
				$buyingnum = $GoodsNotify->getPendStorage($shopCart->Item["goodsid"], $grpid);
				$nowstorage = $goods->storage - $buyingnum;
			}else{
				$nowstorage = $goods->storage;
			}
		}else{
			$nowstorage = 99999;
		}

		if ($shopCart->Item["quantity"] > $nowstorage){
			$Order->orderId = $orderid;
			$Order->toRemove();
			echo "<script type=\"text/javascript\">";
			echo "alert('".$goods->productName.": ".$PROG_TAGS["ptag_1823"]."');";
			echo "location.href=document.referrer;";
			echo "</script>";
			exit;
		}

		//插入订单库存数据表
		$GoodsNotify = newclass("GoodsNotify");
		$GoodsNotify->shopId = $INC_SHOPID;
		$GoodsNotify->userid = $grpid;		//属性组ID
		$GoodsNotify->gid = $shopCart->Item["goodsid"];
		$GoodsNotify->email = $orderid;		//订单号
		$GoodsNotify->uptime = $shopCart->Item["quantity"];	//商品数量
		$GoodsNotify->state = 9;
		$GoodsNotify->toInsert();

		if ($shopinfo->pointtype == 2) $pointnum += $goods->point * $shopCart->Item["quantity"];/*}}}*/ //2007-06-06 bryant

		$memprice = $goods->getPrice($shopCart->Item['goodsinfo'],$INC_USERLEVEL,$shopCart->Item['quantity']);
		$orderItems->price = $memprice * $nowrate;

		$orderItems->productId = $shopCart->Item["goodsid"];
		$orderItems->bn = $goods->bn;
		$orderItems->name = $goods->productName;
		$orderItems->digital_file = $goods->digital_file;
		$orderItems->num = $shopCart->Item["quantity"];
		$orderItems->infostr = $infostr;
		$tmp_itemid = $orderItems->toInsert();

		//***********计算消费积分局部方式 BEGIN*************/*{{{*/
		if ($shopSetup->getValue('point_policy') == 'local'){
			switch ($goods->pointConsumeMethod){
			case 'value':
				$totalConsumePoint += intval($goods->pointConsumeMethodValue) * $shopCart->Item["quantity"];
				break;
			case 'percent':
				$totalConsumePoint += intval($goods->pointConsumeMethodPercent / 100 * $memprice) * $shopCart->Item["quantity"] * $shopSetup->getValue('point_rate');
				break;
			default:
				break;					
			}
		}

		$goodsQuantity = $goodsQuantity + $shopCart->Item["quantity"];
		/***********计算消费积分局部方式 END*************/

		$weight += ($goods->weight + $zjweight) * $shopCart->Item["quantity"];
		$totalmoney += $memprice * $shopCart->Item["quantity"];	//商品总金额

		$gidArr[] = $shopCart->Item["goodsid"];
		
		//记录该用户是否已经购买过该商品
		$comment=newclass("Comment");
		$comment->shopId=$INC_SHOPID;
		$comment->userId=$INC_USERID;
		$comment->goodsId=$shopCart->Item["goodsid"];
	}
}

/*{{{*/
/***********计算消费积分全局方式 BEGIN*************/
if ($shopSetup->getValue('point_policy') == 'global')
{
	switch ($shopSetup->getValue('point_method'))
	{
		case 'value':	

			$totalConsumePoint = intval($shopSetup->getValue('point_method_value')) * $goodsQuantity;
			break;
		case 'percent':
			$totalConsumePoint = intval($shopSetup->getValue('point_method_percent') / 100 * $totalmoney * $shopSetup->getValue('point_rate'));
//			echo $shopSetup->getValue('point_method_percent').'*'.$totalprice.'*'.$goodsQuantity.'='.$totalConsumePoint;
			break;
		default:
			break;
	}
}

if ($_POST['hnconsumepoint']>0)
{
	$actualConsumePoint = $_POST['hnconsumepoint'] > $totalConsumePoint?$totalConsumePoint : $_POST['hnconsumepoint'];
	$consumePointToMoney = sprintf("%0.2f", $actualConsumePoint / $shopSetup->getValue('point_rate'));
} 
else 
{
	$actualConsumePoint = 0;
	$consumePointToMoney = 0;
}
/***********计算消费积全局方式 END*************/

//积分计算方式为按订单价格计算时


//计算配送费用

$shopDelivery = newclass("shopDelivery");
$shopDelivery->shopId = $INC_SHOPID;
$shopDelivery->getbyId($_POST["deliveryid"]);
if ($shopDelivery->type == 0)
{
	$tmp_exp = "";
	$arr_areaexp = explode("@", $shopDelivery->areaexp);
	$tmp_num = count($arr_areaexp);
	for($j = 1; $j < $tmp_num; $j++)
	{
		$tmp_arr = explode(",", $arr_areaexp[$j]);
		if ($tmp_arr[0] == $_POST["receiver_area"])
		{
			$tmp_exp = $tmp_arr[1];
			break;
		}
	}
	if (!empty($tmp_exp))
	{
		$DeliverExp = newclass("DeliverExp");
		$DeliverExp->shopId = $INC_SHOPID ;
		$DeliverExp->getbyId($tmp_exp);

		$dprice = cal_fee($DeliverExp->expressions,$weight,$totalmoney);
	}
	else
		$dprice = 0;
}
elseif($shopDelivery->type == 1)
	$dprice = $shopDelivery->price;
else  //外部接口
{
	$postcode = $_POST["receiver_post"];
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

if ($shopinfo->iftax == 1 && $_POST["ifinvoice"] == 1)
	$taxmoney = sprintf ("%01.2f",$totalmoney * $shopinfo->taxratio);
else
	$taxmoney = 0;

if ($_POST["deliveryprotect"] && $shopDelivery->protect)
{	//保价配送(商品+赠品总价)*百分比
	$protect = 1;
	$dprice += ($shopDelivery->minprice > (($totalmoney) * $shopDelivery->protectrate)?$shopDelivery->minprice:(($totalmoney) * $shopDelivery->protectrate));
}
else $protect=0;


/***********优惠券订单统计 BEGIN*************/
$totalAmountExceptCoupons = moneyFormat($totalmoney - $consumePointToMoney, $INC_SHOPID);
$memberCoupon = newclass("memberCoupon");
$memberCoupon->shopId = $INC_SHOPID;
$totalCouponsDiscount = $memberCoupon->useAllUserCoupons($_POST['memccodes'], $INC_USERID, $gidArr, 0, $orderid, $totalAmountExceptCoupons);
//echo("totalCouponsDiscount:".$totalCouponsDiscount.'|');
//echo("totalAmountExceptCoupons:".$totalAmountExceptCoupons.'|');
//exit;
/***********优惠券订单统计 END*************/

$shopPayment = newclass("shopPayment");
$shopPayment->shopId = $INC_SHOPID ;
$shopPayment->getbyId($_POST["paymentid"]) ;
//计算支付手续费  -- Last Update by Guhaiguo  2007-04-23
if (!empty($shopPayment->expression))
	$pprice = cal_fee($shopPayment->expression, 0, $totalmoney+$dprice+$taxmoney-$consumePointToMoney-$totalCouponsDiscount, 1);
//	$pprice = cal_fee($shopPayment->expression, 0, $totalmoney+$taxmoney-$consumePointToMoney-$totalCouponsDiscount, 1);
else
	$pprice = 0;

//积分计算方式为按订单价格计算时
if ($shopinfo->pointtype == 1){
	$pointnum = Floor($totalmoney * $shopinfo->pointnum);
}

//使用优惠券是否送积分
$shopSetup = newclass("shopSetup");
$shopSetup->shopId = $INC_SHOPID;
if ($totalCouponsDiscount > 0) //如果使用了优惠券, 使用优惠券是否送积分又是否.则将poingnum 置0;
{
	if ($shopSetup->getValue('point_set_coupon')=='N')
	{
		$pointnum = 0;
	}
}

$Order->orderId = $orderid ;
$Order->itemAmount = $totalmoney ;
$Order->freight = floatval($dprice) ;
$Order->paymentrate = $pprice ;
$Order->protect = $protect;
$Order->totalAmount = moneyFormat($totalmoney + $dprice + $pprice + $taxmoney - $consumePointToMoney -$totalCouponsDiscount, $INC_SHOPID);
//debug_log($totalmoney.'-'.$dprice.'-'.$pprice.'-'.$taxmoney.'-'.$consumePointToMoney.'-'.$totalCouponsDiscount);
$Order->paymoney = moneyFormat($Order->totalAmount * $nowrate, $INC_SHOPID) ;
$Order->weight = $weight;
$Order->_delivery = $shopDelivery->name;
$Order->_payment = $shopPayment->name;
$Order->orderpoint = $pointnum;
$Order->consumepoint = $giftPointAll;
$Order->chgpoint = intval($actualConsumePoint);
$Order->chgpointmoney = $consumePointToMoney;
$Order->chgpointrate = $shopSetup->getValue('point_rate');
$Order->couponsprice = $totalCouponsDiscount;
$Order->toUpdate();

//预存款支付
if ($shopPayment->type == "ADVANCE")
{
	$MemberAdvance = newclass("MemberAdvance");
	$MemberAdvance->shopId = $INC_SHOPID;
	if ($MemberAdvance->getAdvance($INC_USERID) < $Order->totalAmount)
	{
		$Order->orderId = $orderid;
		$Order->toRemove();
		//删除商品相关处理表
		echo "<script type=\"text/javascript\">";
		echo "alert('".$PROG_TAGS["ptag_1532"]."');";
		echo "location.href=document.referrer;";
		echo "</script>";
		exit ;
	}
}

//
/*************换购,赠品集中积分处理,及增加history,处理赠品items BEGIN***************/
//优惠券订单处理
$memberCoupon->useAllUserCoupons($_POST['memccodes'], $INC_USERID, $gidArr, 1, $orderid, $totalAmountExceptCoupons);
addGiftItemsHistory($orderid, $giftCart); //购赠品积分历史/增加商品订单items 
if ($actualConsumePoint > 0 || $giftPointAll > 0)
{
	//增加消费积分历史
	if ($actualConsumePoint > 0)
	{
		addConsumePointHistory($orderid ,$actualConsumePoint);
	}
	//扣除用户积分重新计算等级
	updateUserPoint($User, $giftPointAll, $actualConsumePoint);
}
/*************换购,赠品集中积分处理,及增加history,处理赠品items END***************/

include_once './include/mall_config.php';
include_once './include/mysql_db.php';
include_once './include/class.sms.php';
$nTypeId = 16;
$nMemberId = $INC_USERID;
$nId = $orderid;
$sPassword = '';
$oSMS = newclass('sms');
$oSMS->shopId = 1;
$oSMS->sendByFront($nTypeId, $nMemberId, $nId, $sPassword);

//发送订单MAIL
$mailset = newclass("shopMailSetup");
$mailset->shopid= $INC_SHOPID;
$mailset->getinfo("order_create");

if($mailset->mail_enable == 1)
{
	$mailset->set_mailvar("shopname",$shopinfo->name);
	$mailset->set_mailvar("orderid",$orderid);
	$mailset->set_mailvar("receiver_address",$_POST["receiver_address"]);
	$mailset->set_mailvar("truename",$_POST["receiver_name"]);
	$mailset->set_mailvar("orderamount",$ShopCurrency->sign.$Order->paymoney);

	$mailset->getrealinfo();

	include_once($INC_SYSHOMEDIR."syssite/smtpmail/sm.php");
	$smtpserver = $shopinfo->smtp_server ;		//SMTP服务器
	$smtpserverport = $shopinfo->smtp_port;		//SMTP服务器端口
	$smtpusermail = $shopinfo->smtp_email ;		//SMTP服务器的用户邮箱(您的邮箱)
	$smtpuser = $shopinfo->smtp_user ;		//SMTP服务器的用户帐号
	$smtppass = $shopinfo->smtp_password ;		//SMTP服务器的用户密码

	$smtp = new smtp($smtpuser,$smtppass,$smtpserver,$smtpserverport,$shopinfo->smtp_ifcheck);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
	//$smtp->debug = true;//是否显示发送的调试信息
	$smtp->encode = $shopinfo->mail_enc; 
	$smtp->lang = $FRONTEND_LANG;
	$smtp->sendway = ($shopinfo->smtp_sendmode==1?"MAIL":"SMTP"); 
	$smtp->sendmail($_POST["receiver_email"], $smtpusermail, $mailset->mail_realtitle , $mailset->mail_realcontent, "TXT", $shopinfo->name);//（$attachment=>附件的编码，$hasfile=>是否含有附件，$file_name=>附件的名称，$content_type=>附件的类型）
}


include_once './include/mall_config.php';
include_once './include/mysql_db.php';
include_once './include/class.sms.php';
$nTypeId = 23;
$nMemberId = $INC_USERID;
$nId = $orderid;
$sPassword = '';
$oSMS = newclass('sms');
$oSMS->shopId = 1;
$oSMS->sendByFront($nTypeId, $nMemberId, $nId, $sPassword);

$mailset->getinfo("order_create_notify_shopman");
if($mailset->mail_enable == 1)
{
	$mailset->set_mailvar("shopname",$shopinfo->name);
	$mailset->set_mailvar("orderid",$orderid);

	$mailset->getrealinfo();

	include_once($INC_SYSHOMEDIR."syssite/smtpmail/sm.php");
	$smtpserver = $shopinfo->smtp_server ;		//SMTP服务器
	$smtpserverport = $shopinfo->smtp_port;		//SMTP服务器端口
	$smtpusermail = $shopinfo->smtp_email ;		//SMTP服务器的用户邮箱(您的邮箱)
	$smtpuser = $shopinfo->smtp_user ;		//SMTP服务器的用户帐号
	$smtppass = $shopinfo->smtp_password ;		//SMTP服务器的用户密码

	$smtp = new smtp($smtpuser,$smtppass,$smtpserver,$smtpserverport,$shopinfo->smtp_ifcheck);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
	//$smtp->debug = true;//是否显示发送的调试信息
	$smtp->encode = $shopinfo->mail_enc; 
	$smtp->lang = $FRONTEND_LANG;
	$smtp->sendway = ($shopinfo->smtp_sendmode==1?"MAIL":"SMTP"); 
	$smtp->sendmail($shopinfo->email, $smtpusermail, $mailset->mail_realtitle , $mailset->mail_realcontent, "TXT", $shopinfo->name);//（$attachment=>附件的编码，$hasfile=>是否含有附件，$file_name=>附件的名称，$content_type=>附件的类型）
}

if($_POST["receiver_check"])
{
	$MemberReceiver = newclass("MemberReceiver");

	$MemberReceiver->receiveId = $_POST["receiver_id"];
	$MemberReceiver->memberId = $INC_USERID;
	$MemberReceiver->shopId = $INC_SHOPID;
	$MemberReceiver->email = $_POST["receiver_email"];
	$MemberReceiver->name = $_POST["receiver_name"];
	$MemberReceiver->address = $_POST["receiver_address"];
	$MemberReceiver->zipcode = $_POST["receiver_post"];
	$MemberReceiver->telphone = $_POST["receiver_tele"];
	$MemberReceiver->mobile = $_POST["receiver_mobile"];
	$MemberReceiver->memo = $_POST["receiver_memo"];

	if($MemberReceiver->checkExist()){
		$MemberReceiver->toUpdate();
	}
	else{
		$MemberReceiver->recsts = 1;
		$recId = $MemberReceiver->toInsert();
		$MemberReceiver->receiveId = $recId;
		$MemberReceiver->toUpdateDefault();
	}
}
/*}}}*/

//清除购物车cookie

$shopCart = newclass("shoppingCart");
$shopCart->loadAll();
$shopCart->clearAll();

$giftCart = newclass("giftCart");
$giftCart->loadAll();
$giftCart->clearAll();
Header("Location: ./index.php?gOo=finish_order.dwt&orderid=".$orderid."&ordermd5=".md5($orderid."ShopExOrder"));
function addGiftItemsHistory($orderid, &$giftCart)
{
	global $INC_SHOPID, $INC_USERID;

	$Gift = newclass("Gift");
	$giftCart->loadAll();

	$orderGiftItems = newclass("orderGiftItems");
	$orderGiftItems->orderId = $orderid;
	$orderGiftItems->shopId = $INC_SHOPID;
	$orderGiftItems->userid = $INC_USERID;



	//插入订单赠品项,记录积分消费历史
	$pointHistory = newclass("pointHistory");
	while ($giftCart->next())
	{
		if ($giftCart->Item["shopid"] == $INC_SHOPID)
		{
			//插入定单赠品项
			$Gift->shopId = $giftCart->Item["shopid"];
			$Gift->getGiftById($giftCart->Item["goodsid"]);
			$orderGiftItems->giftid = $Gift->giftId;
			$orderGiftItems->orderid = $orderid;
			$orderGiftItems->gift_name = $Gift->name;
			$orderGiftItems->point = $Gift->point;
			$orderGiftItems->nums = $giftCart->Item["quantity"];
			$orderGiftItems->oof = $Gift->point*$giftCart->Item["quantity"];
			$orderGiftItems->send_num = 0;
			$itemId = $orderGiftItems->toInsert();

			//记录商品的积分消费历史项
			$pointHistory->userId = $INC_USERID;
			$pointHistory->shopId = $INC_SHOPID;
			$pointHistory->point = 0 - ($Gift->point * $giftCart->Item["quantity"]);//修正赠品积分历史,没将数量考虑在内问题
			$pointHistory->reason = 'consume_gift';
			$pointHistory->relatedId = $orderid;
			$pointHistory->addHistory();
			$Gift->toChgstorage($itemId, $Gift->giftId, 1, $orderGiftItems->nums);//2007-9-23 bryant 直接扣库存
		}
	}
}
function updateUserPoint(&$User, $giftPointAll, $actualConsumePoint)
{

	//扣除用户积分(赠品)重新计算等级
	$User->point = $User->point-$giftPointAll;
	$User->toUpdatelevel();

	$User->point = $User->point-$actualConsumePoint;
	$User->toUpdatelevel();
}

function addConsumePointHistory($orderid ,$actualConsumePoint)
{
	global $INC_USERID, $INC_SHOPID;

	$pointHistory = newclass("pointHistory");
	$pointHistory->userId = $INC_USERID;
	$pointHistory->shopId = $INC_SHOPID;
	$pointHistory->point =  0 - $actualConsumePoint;
	$pointHistory->reason = 'change_goods';
	$pointHistory->relatedId = $orderid;
	$pointHistory->addHistory();
}

?>