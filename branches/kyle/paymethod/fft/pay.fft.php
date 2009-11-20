<?php
require('paymentPlugin.php');
class pay_fft extends paymentPlugin{

    var $name = '付费通';//工商银行
    var $logo = 'CIB';
    var $version = "1.0";
    var $charset = 'gbk';
    var $submitUrl = 'http://218.242.177.180:8001/gateway/transForwardAll.jsp'; //测试地址
	//var $submitUrl = 'http://220.250.30.210:8021/NetPayment.jsp'; //测试地址
    var $submitButton = 'http://img.alipay.com/pimg/button_alipaybutton_o_a.gif'; ##需要完善的地方
    var $supportCurrency = array("CNY"=>"001");
    var $supportArea = array("AREA_CNY");
    var $intro = "付费通网关是付费通商户和付费通核心平台相连的唯一桥梁，给商户提供统一的交易接口。商户使用网关交易平台，无需再考虑具体的交易机构，大大减少了商户因为连接具体机构而造成的工作负担,实现消费者、商户和银行间的网上安全支付。";
    var $head_charset="gbk";

    function toSubmit($payment){

		$merid = $this->getConf($payment["M_OrderId"], 'merid');
		$merkey = $this->getConf($payment["M_OrderId"], 'merkey');

		$merid = '222222222222222';
		$merkey = '2sfft007wang8w8ch8';

		$aREQ['VERSION'] = '00';
		$aREQ['TXCODE'] = 'A0160';
		$aREQ['TXSUBCODE'] = '0';
		$aREQ['MERCHANTID'] = $merid;
		$aREQ['TRANSDATE'] = date("YmdHis",$payment['M_Time']);
		$aREQ['TRANSFLW'] = $payment["M_OrderId"];
		$aREQ['ORDERID'] = $payment['M_OrderNO'];
		$aREQ['CURCODE'] = '156';
		$aREQ['AMOUNT'] = $payment['M_Amount'] * 100;
		$aREQ['TOTROW'] = '0';
		$aREQ['RTNURL'] = $this->callbackUrl;
		#
		$message = $this->getMessage($aREQ);			
		#商户MAC
		$aREQ["SIGN"] = $this->getMAC($message,$merkey);

		return $aREQ;
    }

    function callback($in,&$paymentId,&$money,&$message,&$tradeno){		
		//error_log(var_export($in,true),3,dirname(__FILE__)."/../in.log");
		$paymentId = substr($in['orderID'],14);			
		/*
		*商户代号+订单号+订单金额+订单币种+网银流水号+支付状态编号
		*/
		$aMess['merchantID'] = $in['merchantID'];
		$aMess['orderID'] = $in['orderID'];
		$aMess['orderAmount'] = $in['orderAmount'];
		$aMess['orderCurrencyCode'] = $in['orderCurrencyCode'];
		$aMess['netBankTraceNo'] = $in['netBankTraceNo'];
		$aMess['payStatus'] = $in['payStatus'];
		#
		$signstr = $this->getMessage($aMess);		
		$merkey = $this->getConf($paymentId, 'merkey');
		//error_log($merkey,3,dirname(__FILE__)."/../merkey.log");
		$localmac = $this->getMAC($signstr,$merkey);
		$bankmac = $in['mac']; 
		#
        if ($bankmac == $localmac){
			$paystatus = intval($in['payStatus']);
			if($paystatus === 1){
				$money = $in['orderAmount'];
                $message="支付成功！";
                return PAY_SUCCESS;
            }else{
                $message = "支付失败！";
                return PAY_FAILED;
            }
        }
        else{
            $message = "验证签名错误！";
            return PAY_ERROR;
        }
    }
    
		function getfields(){
			return array(
				'merid'=>array(
					'label'=>'商户号',
					'type'=>'string',
					'helpMsg'=>'由兴业银行分配的商户号'
				),
				'merkey'=>array(
					'label'=>'密钥',
					'type'=>'string',
					'helpMsg'=>'由兴业银行分配的密钥'
				), 
			);
    }

		function getMessage($aArr){
			//商户编号+订单号+订单生成日期+订单金额+商户通知类型+订单币种+订单返回的URL+支付方式
			$message = '';
			if(is_array($aArr)){
				unset($aREQ['TOTROW']);
				unset($aREQ['RTNURL']);
				foreach($aArr as $v){
					$message .= $v;
				}
			}

			return trim($message);
			
		}

		function getMAC($message,$merkey){

			$message .= $merkey;
			$mac = md5($message);

			return $mac;
		}

}
?>
