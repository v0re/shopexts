<?php
require('paymentPlugin.php');
class pay_spdb extends paymentPlugin{

    var $name = '浦发银行';//浦发银行
    var $logo = 'SPDB';
    var $version = "1.0";
    var $charset = 'gbk';
    var $submitUrl = 'https://ebank.spdb.com.cn/payment/main'; //正式地址
	//var $submitUrl = 'http://220.250.30.210:8021/NetPayment.jsp'; //测试地址
    var $submitButton = 'http://img.alipay.com/pimg/button_alipaybutton_o_a.gif'; ##需要完善的地方
    var $supportCurrency = array("CNY"=>"001");
    var $supportArea = array("AREA_CNY");
    var $intro = "上海浦东发展银行是1992年8月28日经中国人民银行批准设立、于1993 年1月9日正式开业的股份制商业银行，总行设在上海。";
    var $head_charset="gbk";

    function toSubmit($payment){

			$merid = $this->getConf($payment["M_OrderId"], 'merid');
			$merkey = $this->getConf($payment["M_OrderId"], 'merkey');


            $aREQ['TranAbbr'] = 'IPER';	
            $aREQ['MasterID'] = $merid;  
            $aREQ['MercDtTm'] = date("Y-m-d",$payment['M_Time']);		
            $aREQ['TermSsn'] = $payment['M_OrderNO'].$payment["M_OrderId"];	
            $aREQ['OSttDate'] = '';
            $aREQ['OAcqSsn'] = 	'';
            $aREQ['MercCode'] = $merid;	
            $aREQ['TermCode'] = 000000;
            $aREQ['TranAmt'] =  $payment['M_Amount'];
            $aREQ['Remark1'] = '';
            $aREQ['Remark2'] = ''	
            $aREQ['MercUrl'] = $this->callbackUrl;	
            
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
			$message = '';
			if(is_array($aArr))
			foreach($aArr as $key=>$v){
				$message .= $key.'='.$v.'|';
			}
            $message = rtrim($message,'|');
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
