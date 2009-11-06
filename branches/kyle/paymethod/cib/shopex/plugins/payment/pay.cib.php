<?php
require('paymentPlugin.php');
class pay_cib extends paymentPlugin{

    var $name = '兴业银行';//工商银行
    var $logo = 'CIB';
    var $version = "1.0";
    var $charset = 'gbk';
    var $submitUrl = 'https://www.cib.com.cn/NetPayment.jsp'; //正式地址
	//var $submitUrl = 'http://220.250.30.210:8021/NetPayment.jsp'; //测试地址
    var $submitButton = 'http://img.alipay.com/pimg/button_alipaybutton_o_a.gif'; ##需要完善的地方
    var $supportCurrency = array("CNY"=>"001");
    var $supportArea = array("AREA_CNY");
    var $intro = "兴业银行，提供网上个人银行，网上企业银行，信用卡服务，网上商城，各种人民币及外汇业务";
    var $head_charset="gbk";

    function toSubmit($payment){

			$merid = $this->getConf($payment["M_OrderId"], 'merid');
			$merkey = $this->getConf($payment["M_OrderId"], 'merkey');

			//商户名称
			$aREQ["NetPaymentMerchantID"] = $merid; 
			//不多于:32位,ShopEx订单号+支付单号
			$aREQ["NetPaymentMerchantTraceNo"] = $payment['M_OrderNO'].$payment["M_OrderId"];
			//格式为yyyyMMdd,如果为空，系统默认为当前日期
			$aREQ["orderDate"] = date("Y-m-d",$payment['M_Time']);
			//小数点后保留2位小数
			$aREQ["AmountNumber"] = $payment['M_Amount'];
			//商户通知类型
			$aREQ["notifyType"] = '1';
			//订单币种
			$aREQ["orderCurrencyCode"] = "CNY";
			//订单返回URL
			$aREQ["merchantURL"] = $this->callbackUrl;
			//支付方式
			$aREQ["paymethod"] = 'merchant';

			$message = $this->getMessage($aREQ);
			
			//商户MAC
			$aREQ["merchantMac"] = $this->getMAC($message,$merkey);

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
			if(is_array($aArr))
			foreach($aArr as $v){
				$message .= $v;
			}

			return trim($message);
			
		}

		function getMAC($message,$merkey){
			if (strtoupper(substr(PHP_OS,0,3))=="WIN"){
				$crypto = new COM('fjxycrypto.crypto');
				$mac = $crypto->ANSIX99($merkey,$message,strlen($message));
			}elseif(is_callable('cibSign')){
				$mac = cibSign($merkey, $message);
			}else{
				$cmd = "java /bin/cibsign \"{$message}\" \"{$merkey}\"";
				$handle = popen($cmd, 'r');
				$mac = fread($handle, 32);
				pclose($handle);
			}

			return $mac;
		}

}
?>
