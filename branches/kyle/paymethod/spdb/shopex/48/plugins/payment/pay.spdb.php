<?php
require('paymentPlugin.php');
class pay_spdb extends paymentPlugin{

    var $name = '浦发银行';//浦发银行
    var $logo = 'SPDB';
    var $version = "1.0";
    var $charset = 'gbk';
   // var $submitUrl = 'https://ebank.spdb.com.cn/payment/main'; //正式地址
	var $submitUrl = 'http://124.74.239.32/payment/main'; //测试地址
    var $submitButton = 'http://img.alipay.com/pimg/button_alipaybutton_o_a.gif'; ##需要完善的地方
    var $supportCurrency = array("CNY"=>"001");
    var $supportArea = array("AREA_CNY");
    var $intro = "上海浦东发展银行是1992年8月28日经中国人民银行批准设立、于1993 年1月9日正式开业的股份制商业银行，总行设在上海。";
    var $head_charset="gbk";
    var $method = "POST";

    function toSubmit($payment){

			$merccode = $this->getConf($payment["M_OrderId"], 'merccode');
            $masterid = '2000615499';
            $merccode = '930299990000101';

            $aREQ['TranAbbr'] = 'IPER';	
            #$aREQ['MasterID'] = $masterid;  
            $aREQ['MercDtTm'] = date("YmdHis",$payment['M_Time']);		
            $aREQ['TermSsn'] = substr($payment["M_OrderId"],2);	
            $aREQ['OSttDate'] = '';
            $aREQ['OAcqSsn'] = 	'';
            $aREQ['MercCode'] = $merccode;	
            $aREQ['TermCode'] = '000000';
            $aREQ['TranAmt'] =  $payment['M_Amount'];
            $aREQ['Remark1'] = '';
            $aREQ['Remark2'] = '';	
            $aREQ['MercUrl'] = $this->callbackUrl;	
            
			$message = $this->getMessage($aREQ);
			
			//商户MAC
			$mac = $this->getMAC($message);

            $ret['transName'] = 'IPER';
            $ret['Plain'] = $message;
            $ret['Signature'] = $mac;
            $url = $this->submitUrl.'?';
            foreach($ret as $k=>$v){
                $url .= "$k=$v&";
            }
            $url = rtrim($url,'&');
            #header("Location: ".$url);
            return $ret;
    }

    function callback($in,&$paymentId,&$money,&$message,&$tradeno){		
		error_log(var_export($in,true),3,dirname(__FILE__)."/../in.log");
        $ret = explode('|',$in['Plain']);
        $paymentId = '12'.$ret['TermSsn'];			
	    	
			
		$merccode = $this->getConf($paymentId, 'merccode');
		
        if ($this->verify($in['Plain'],$in['Signature'])){
			if($ret['RespCode'] == '00'){
				$money = $ret['TranAmt'];
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
				'merccode'=>array(
					'label'=>'商户号',
					'type'=>'string',
					'helpMsg'=>'由银行分配的商户号'
				),
				/*'merccode'=>array(
					'label'=>'客户号',
					'type'=>'string',
					'helpMsg'=>'由银行分配的密钥'
                ),*/ 
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

		function getMAC($message){
            $cmd = "java -jar /bin/spdbcmd.jar sign \"{$message}\" ";
            exec($cmd,$ret);
			return $ret[0];
        }

        function verify($plain,$signed){
            $cmd = "java -jar /bin/spdbcmd.jar verify  \"{$plain}\" \"{$signed}";
            error_log($cmd,3,dirname(__FILE__)."/../cmd.log");
            exec($cmd,$ret);
            error_log(var_export($ret,true),3,dirname(__FILE__)."/../ret.log");
            return $ret[0];
        }

}
?>
