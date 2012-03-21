<?php
require('paymentPlugin.php');
class pay_abc extends paymentPlugin{
    var $name = '中国农业银行B2C支付接口_JAVA_2.0.7';
    var $logo = 'ABC';
    var $version = 20070902;
    var $charset = 'gb2312';
	var $APIUrl0 = 'https://ebank.95559.com.cn/corporbank/NsTrans'; //正式API地址
	var $APIUrl1 = 'https://61.172.242.229/corporbank/NsTrans'; //测试API地址
	var $submitUrl0 = 'https://www.95599.cn/b2c/trustpay/ReceiveMerchantIERequestServlet'; //正式提交地址
	var $submitUrl1 = 'https://www.test.95599.cn/b2c/b2c/ReceiveMerchantIERequestServlet'; //测试提交地址
    var $supportCurrency = array("CNY"=>"001");
    var $supportArea = array("AREA_CNY");
    var $intro = "中国农业银行B2C支付接口_JAVA_2.0.7 php改写版";
    var $orderby = 100;
    var $head_charset="gb2312";
    function toSubmit($payment){
            $ret_url = 'http://lusen.vip.ishopex.cn/plugins/app/pay_abc/pay_abc.php';
            $this->submitUrl = $this->submitUrl0;
            $cert_file = dirname(__FILE__)."/cert/my.pem";
            $cert_file_password = '999999';
            $MerchantID = '233010300xxxxx';
            
            //设定订单对象的属性 begin
		   //订单编号(必要信息)
           $OrderNo = $payment['M_OrderId'];

		   //订单说明
		  //$tOrderDesc = iconv('GBK','UTF-8',$payment['M_Goods']);

		   //订单日期(必要信息 - YYYY/MM/DD)
		  $OrderDate = date('Y/m/d',$payment['M_Time']);

		   //订单时间(必要信息 - HH:MM:SS)
		  $OrderTime = date('H:i:s',$payment['M_Time']);

		   //订单金额(必要信息)
		  $OrderAmount = $payment['M_Amount'];

		   //订单查询网址(必要信息)
		  $OrderURL = $ret_url;

		   //买方IP地址信息
		  //$BuyIP = "";
		  //设定订单对象的属性 end
           
		  //设定支付请求对象的属性 begin
           //订单对象(必要信息)
		   //$Order = $payment['M_OrderId'];

		   //设定商品种类(必要信息)
		  $ProductType = 1;

		   //设定支付类型(必要信息)
		  $PaymentType = 1;
	       
		   //设定支付接入方式(必要信息)
           $PaymentLinkType = 1;

		   //设定支付结果通知方式(必要信息)
		   $NotifyType = 0;

		   //支付结果地址(必要信息)
		   $ResultNotifyURL = $ret_url;
		  //设定支付请求对象的属性 end

            $to_sign = "<Merchant><ECMerchantType>B2C</ECMerchantType><MerchantID>$MerchantID</MerchantID></Merchant><TrxRequest><TrxType>PayReq</TrxType><Order><OrderNo>$OrderNo</OrderNo><OrderAmount>$OrderAmount</OrderAmount><OrderDesc></OrderDesc><OrderDate>$OrderDate</OrderDate><OrderTime>$OrderTime</OrderTime><OrderURL>$OrderURL</OrderURL><BuyIP></BuyIP><OrderItems><OrderItem><ProductID>IP000001</ProductID><ProductName>cntel</ProductName><UnitPrice>100.0</UnitPrice><Qty>1</Qty></OrderItem><OrderItem><ProductID>IP000002</ProductID><ProductName>cnc</ProductName><UnitPrice>90.0</UnitPrice><Qty>2</Qty></OrderItem></OrderItems></Order><ProductType>$ProductType</ProductType><PaymentType>$PaymentType</PaymentType><NotifyType>$NotifyType</NotifyType><ResultNotifyURL>$ResultNotifyURL</ResultNotifyURL><MerchantRemarks></MerchantRemarks><PaymentLinkType>$PaymentLinkType</PaymentLinkType></TrxRequest>";
            
            $signature = $this->sign($to_sign,$cert_file,$cert_file_password );
            $msg = "<MSG><Message>$to_sign</Message>
            <Signature-Algorithm>SHA1withRSA</Signature-Algorithm>
            <Signature>$signature</Signature></MSG>";

            $a_req['Signature'] = $msg;
            $a_req['errorPage'] = $ret_url; 
			return $a_req;
    }

    function callback($in,&$paymentId,&$money,&$message,&$tradeno){
        $msg = base64_decode($in['MSG']);
        preg_match("/\<Message\>(.*)\<\/Message\>.*\<Signature\>(.*)\<\/Signature\>/i",$msg,$match);
        $contents = $match[1];
        $signature = $match[2];

        preg_match("/\<OrderNo\>(.*)\<\/OrderNo\>.*\<Amount\>(.*)\<\/Amount\>/i",$contents,$match);

        $paymentId = $match[1];
        $money = $match[2];
        
        error_log(var_export($paymentId,1),3,dirname(__FILE__)."/test.log");
        $p=$this->system->loadModel("trading/payment");
		$tmp=$p->getById($paymentId);
        error_log(var_export($tmp,1),3,dirname(__FILE__)."/test.log");
        if(!$tmp){
            $message = "无此支付ID！";
            return PAY_ERROR;
        }
        $pub_cert_file = dirname(__FILE__)."/cert/TrustPay.pem";
        if($this->verify($contents,$signature,$pub_cert_file)){
            $message="支付成功！";
            return PAY_SUCCESS;
        }elseif($ok == 0) {
            $message = "验证签名错误！";
            return PAY_ERROR;
        }else{
            $message = "验证签名错误！";
            return PAY_ERROR;
        }
            
    }
    function getfields(){
            return array(
                'member_id'=>array(
                     'label'=>'商户客户号',
                     'type'=>'string',
                     'helpMsg'=>'商户客户号是银行生成并提供的15位编号'
                 ),
                 'certFile'=>array(
                     "label"=>'商户证书文件(客户自己导出的证书,pfx格式)',
                     "type"=>'file'
                 ),
                 'rootFile'=>array(
                     "label"=>'银行根证书文件(一般是root.cer)',
                     "type"=>'file'
                 ),
                 'keyPass'=>array(
                     "label"=>'商户证书私钥加密密码',
                     "type"=>'string'
                 ),
				'bocomType'=>array(
					"label"=>'环境选择',
					 "type"=>'select',
					 "options"=>array("0"=>"正式环境","1"=>"测试环境"),
					 ),
				'enableLog'=>array(
					"label"=>'是否开启日志(日志文件保存在/cert/bocom/支付方式ID/目录下)',
					 "type"=>'select',
					 "options"=>array("0"=>"否","1"=>"是"),
					 )
            );
    }
	function createxml($aaData)
	{
		$aData=unserialize($aaData['config']);
		if($aData["bocomType"]=="0")
		{
			$this->APIUrl=$this->APIUrl0;
			$this->submitUrl=$this->submitUrl0;
		}
		else
		{
			$this->APIUrl=$this->APIUrl1;
			$this->submitUrl=$this->submitUrl1;
		}
		if($aData["enableLog"]=="0")
		{	
			$log="false";
		}
		else
		{
			$log="true";
		}
		$realPath=dirname(__FILE__)."/../../cert/bocom/".$aaData['pay_id']."/";
		$f=fopen($realPath.md5('bocomshopex'.$aaData['pay_id']).".xml",'w');
		$xmldata="<?xml version=\"1.0\"  encoding=\"gb2312\"?><ABCB2C><ApiURL>".$this->APIUrl."</ApiURL><OrderURL>".$this->submitUrl."</OrderURL><EnableLog>".$log."</EnableLog><LogPath>".$realPath."</LogPath><SettlementFilePath>".$realPath."</SettlementFilePath><MerchantCertFile>".$realPath.$aData["certFile"]."</MerchantCertFile><MerchantCertPassword>".$aData["keyPass"]."</MerchantCertPassword><RootCertFile>".$realPath.$aData["rootFile"]."</RootCertFile></ABCB2C>";
	  if (fwrite($f, $xmldata) === FALSE)
		{
			return false;
		}
	  else
		{
		  fclose($f);
		}
		return true;
	}
    
    function verify($data,$signature,$cert_file){
            $fp = fopen($cert_file, "r");
            $cert = fread($fp, 8192);
            fclose($fp);
            $pubkeyid = openssl_get_publickey($cert);
            $signature = base64_decode($signature);
            // state whether signature is okay or not
            $ok = openssl_verify($data, $signature, $pubkeyid);
            // free the key from memory
            openssl_free_key($pubkeyid);
            return $ok;
    }
        
	function sign($to_sign,$cert_file,$cert_file_password){
            $signature = null;
	        $fp = fopen($cert_file, "rb");
            $priv_key = fread($fp, 8192);
            fclose($fp);
            //获取pem文件中的私钥
            $pkeyid = openssl_get_privatekey($priv_key,$cert_file_password);
            //使用用户私钥对消息进行签名
            openssl_sign($to_sign, $signature, $pkeyid);
            // Free the key.
            openssl_free_key($pkeyid);
            return base64_encode( $signature );
	}        

	

}
?>