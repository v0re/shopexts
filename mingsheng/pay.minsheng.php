<?php
require('paymentPlugin.php');
class pay_minsheng extends paymentPlugin{

    var $name = '民生银行[2.0.0版]';//民生银行
    var $logo = 'MS';
    var $version = 201008024;
    var $charset = 'gbk';
    var $submitUrl = 'https://ebank.cmbc.com.cn/weblogic/servlets/EService/CSM/B2C/servlet/ReceiveMerchantCMBCTxReqServlet'; //正式地址
    var $submitButton = 'http://img.alipay.com/pimg/button_alipaybutton_o_a.gif'; ##需要完善的地方
    var $supportCurrency = array("CNY"=>"001");
    var $supportArea = array("AREA_CNY");
    var $intro = "民生银行[2.0.0版]B2C支付网关可以使用在windows主机，请在申请民生银行网关接口时申请2.0.0版。";
    var $orderby = 8;
    function toSubmit($payment){
        $merId = $this->getConf($payment["M_OrderId"], 'member_id');
        $keyPass = $this->getConf($payment["M_OrderId"], 'keyPass');
        $minshengno = $this->getConf($payment["M_OrderId"],'minshengno');
        $pfxFile = $this->getConf($payment["M_OrderId"],'pfxFile');
        $certFile = $this->getConf($payment["M_OrderId"],'certFile');
        $ret_url = $this->callbackUrl;
        $charset = $this->system->loadModel('utility/charset');
        if (is_dir(dirname(__FILE__)."/home/upload/minsheng/"))
            $realpath = dirname(__FILE__)."/../../home/upload/minsheng/";
        elseif (is_dir(dirname(__FILE__)."/../../cert/minsheng/"))
            $realpath = dirname(__FILE__)."/../../cert/minsheng/";
        	$pfxurl = $realpath.$pfxFile;//商户证书
       		$certrul = $realpath.$certFile;//银行证书
       	//$pfxurl='E:\shop\shopex\home\upload\minsheng\xigou.pfx';
       	//$certrul='E:\shop\shopex\home\upload\minsheng\cmbc-2.cer';

        if(!file_exists($pfxurl)){ 
            die($pfxurl);
        }
         if(!file_exists($certrul)){ 
            die("minsheng Cert file not found!");
         }
         $billno=$merId.$order['M_OrderId'];
         $amount = number_format($payment['M_Amount'],2,".","");
         $adddate=date('Ymd',$payment['M_Time']);
		 $addstime=date('His',$payment['M_Time']);
		 
         $plaintext = 	$billno."|".$amount."|01|".$adddate."|".$addstime."|".$merId."|".$minshengno."|0|0|0|".$ret_url."|fuck";		
		 $plaintext=iconv("UTF-8", "GBK", $plaintext);
         $minshengpay = new COM("Newcom2.seServer.1") or die("Unable to instanciate Word");
		 $minshengpay->readcert($pfxurl);
		 $pfx = $minshengpay->cert();
		 
		 $minshengpay->readcert($certrul);
         $bankcert = $minshengpay->cert();
         
         $minshengpay->EnvelopData($plaintext,$bankcert,$pfx,$keyPass);
		if ($minshengpay->retCode()==0){
			$return['orderinfo'] = $minshengpay->EnveData();
			}
        return $return;
    }

    function callback($in,&$paymentId,&$money,&$message,&$tradeno){
        $merId = $this->getConf($payment["M_OrderId"], 'member_id');
        $keyPass = $this->getConf($payment["M_OrderId"], 'keyPass');
        $minshengno = $this->getConf($payment["M_OrderId"],'minshengno');
        $icbcFile = $this->getConf($payment["M_OrderId"],'pfxFile');
        $certFile = $this->getConf($payment["M_OrderId"],'certFile');
        $ret_url = $this->callbackUrl;
        $charset = $this->system->loadModel('utility/charset');
        if (is_dir(dirname(__FILE__)."/../../home/upload/minsheng/"))
            $realpath = dirname(__FILE__)."/../../home/upload/minsheng/";
        elseif (is_dir(dirname(__FILE__)."/../../cert/minsheng/"))
            $realpath = dirname(__FILE__)."/../../cert/minsheng/";
        $pfxurl = $realpath.$pfxFile;//商户证书
        $certrul = $realpath.$certFile;//银行证书
        
        if(!file_exists($pfxurl)){ 
            die("minsheng pfx file not found!");
        }
         if(!file_exists($certrul)){ 
            die("minsheng Cert file not found!");
         }
        
        
        $encryptdata=trim($_GET['payresult']);
		if(empty($encryptdata)){		
			return false;
		}
		$minshengpay = new COM("Newcom2.seServer.1") or die("Unable to instanciate Word");
		$minshengpay->readcert($pfxurl);
		$pfx = $minshengpay->cert();
		$minshengpay->readcert($certrul);
        $bankcert = $minshengpay->cert();	
		$minshengpay->DecryptData($encryptdata,$bankcert,$pfx,$keyPass);
		
		$test1=$minshengpay->DecrData();	
        if ($test1!=""){
        	$orderinfo = explode('|',iconv("GBK", "UTF-8", $minshengpay->DecrData()));
            
            $money = $orderinfo[2]; 
            $tradeno = substr($orderinfo[0],5);
            if ($orderinfo[5]==0){
                $message="支付成功！";
                return PAY_SUCCESS;
            }
            else{
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
                'member_id'=>array(
                     'label'=>'商户代码',
                     'type'=>'string',
                     'helpMsg'=>'此处填写您的支付帐号、客户号或客户id等，此帐号在支付服务提供商处取得；'
                 ),
                 'minshengno'=>array(
                     'label'=>'商户名称',
                     'type'=>'string',
                 ),
                 'pfxFile'=>array(
                     "label"=>'商户证书(.pfx) ',
                     "type"=>'file'
                 ),
                 'certFile'=>array(
                     "label"=>'银行证书(.cer)',
                     "type"=>'file'
                 ),
                 
                 'keyPass'=>array(
                     "label"=>'私钥文件口令',
                     "type"=>'string'
                 )
            );
    }
}
?>
