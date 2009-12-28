<?php
require('paymentPlugin.php');
class pay_abchina extends paymentPlugin{

    var $name = '农行在线支付';
    var $logo = 'ABCHINA';
    var $version = 20091201;
    var $charset = 'utf8';
    var $submitUrl = 'http://www.968111.com/plugins/abchina/payment.php';
    var $supportCurrency = array("CNY"=>"001");
    var $supportArea = array("AREA_CNY");
    var $intro = "中国农业银行网上银行B2C支付网关。";
    var $orderby = 33;
    var $head_charset='utf-8';

    function toSubmit($payment){
        $merId = $this->getConf($payment['M_OrderId'], 'member_id'); //帐号
        $return = array();
		
		$OrderNo    = $payment["M_OrderId"];
		$OrderDesc    = '海都购物网订单';
		$OrderDate    = date('Y/m/d',time());
		$OrderTime    = date('H:i:s',time());
		$OrderAmount    = number_format($payment["M_Amount"],2,".","");
		$OrderURL    = 'http://www.968111.com/?member-orders.html';
		$ProductType    = 1;
		$PaymentType    = 1;	// 1：农行卡支付 2：国际卡支付 3：农行贷记卡支付
		$PaymentLinkType    = 1;	// 1：internet网络接入 2：手机网络接入 3:数字电视网络接入 4:智能客户端
		$NotifyType    = 0;		// 0：URL页面通知 1：服务器通知
		$ResultNotifyURL    = 'http://www.968111.com:8080/axis/MerchantResult.jsp';
		$MerchantRemarks    = $payment["M_OrderId"];
		$TotalCount    = 1;
		$productid    = $payment["M_OrderNO"];
		$productname    = '海都购物网商品';
		$uniteprice    = number_format($payment["M_Amount"],2,".","");
		$qty    = 1;

        $return['OrderNo'] = $OrderNo;
        $return['OrderDesc'] = $OrderDesc;
        $return['OrderDate'] = $OrderDate;
        $return['OrderTime'] = $OrderTime;        
        $return['OrderAmount'] = $OrderAmount;
        $return['OrderURL'] = $OrderURL;
        $return['ProductType'] = $ProductType;
        $return['PaymentType'] = $PaymentType;
        $return['PaymentLinkType'] = $PaymentLinkType;
        $return['NotifyType'] = $NotifyType;
        $return['ResultNotifyURL'] = $ResultNotifyURL;
        $return['MerchantRemarks'] = $MerchantRemarks;
        $return['TotalCount'] = $TotalCount;
        $return['productid[]'] = $productid;
        $return['productname[]'] = $productname;
        $return['uniteprice[]'] = $uniteprice;
        $return['qty[]'] = $qty;

        return $return;
    }

    function callback($in,&$paymentId,&$money,&$message){
		$OrderNo			= trim($in['OrderNo']);			// 订单号
		$Amount				= trim($in['Amount']);				// 订单金额
		$BatchNo			= trim($in['BatchNo']);			// 交易批次号
		$VoucherNo			= trim($in['VoucherNo']);			// 交易凭证号
		$HostDate			= trim($in['HostDate']);			// 银行交易日期
		$HostTime			= trim($in['HostTime']);			// 银行交易时间
		$MerchantRemarks	= trim($in['MerchantRemarks']);	// 商户备注信息
		$PayType			= trim($in['PayType']);			// 消费者支付方式
		$NotifyType			= trim($in['NotifyType']);			// 支付结果通知方式			
        $State				= trim($in['State']);				// 支付状态9成功,8失败
        $paymentId			= $OrderNo;
        $money				= $Amount;
		if($State == "9")
			return PAY_SUCCESS;
		else{
			$message = "交易失败";
			return PAY_FAILED;
		}
    }

    function getfields(){
            return array();
    }
}
?>
