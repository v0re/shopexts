<?php
require('paymentPlugin.php');
class pay_bocom extends paymentPlugin{
		var $name = '交通银行网上支付平台B2C[1.0.0.0版]';//工商银行
		var $logo = 'BOCOM';
		var $version = 20070902;
		var $charset = 'gb2312';
		var $APIUrl0 = 'https://ebank.95559.com.cn/corporbank/NsTrans'; //正式API地址
		var $APIUrl1 = 'https://ebanktest.95559.com.cn/corporbank/NsTrans'; //测试API地址
		var $submitUrl0 = 'https://pbank.95559.com.cn/netpay/MerPayB2C'; //正式提交地址
		var $submitUrl1 = 'https://pbanktest.95559.com.cn/netpay/MerPayB2C'; //测试提交地址
		var $supportCurrency = array("CNY"=>"001");
		var $supportArea = array("AREA_CNY");
		var $intro = "交通银行网上支付平台B2C[1.0.0.0版]，只可以使用在windows主机上。";
		var $orderby = 100;
		var $head_charset="gb2312";
		function toSubmit($payment){
			$merId = $this->getConf($payment["M_OrderId"], 'member_id');
			$keyPass = $this->getConf($payment["M_OrderId"], 'keyPass');
			$certFile = $this->getConf($payment["M_OrderId"],'certFile');
			$rootFile = $this->getConf($payment["M_OrderId"],'rootFile');
			$bocomType=$this->getConf($payment["M_OrderId"],'bocomType');
			if($bocomType=="0")
			{
				$this->APIUrl=$this->APIUrl0;
				$this->submitUrl=$this->submitUrl0;
			}
			else
			{
				$this->APIUrl=$this->APIUrl1;
				$this->submitUrl=$this->submitUrl1;
			}
			$charset = $this->system->loadModel('utility/charset');
			$realpath = dirname(__FILE__)."/../../cert/bocom/";
			$cert = $realpath.$certFile;//私钥文件
			$root = $realpath.$rootFile;//公钥文件
			//接口版本目前为“1.0.0.0”
			$aREQ["interfaceVersion"] = "1.0.0.0";
			//商户客户号，15位，BOCOM提供
			$aREQ["merID"] = $merId;
			//订单号商户端产生，三个月内不能重复。
			$aREQ["orderid"] = $payment['M_OrderId'];//$payment['M_OrderNO']."-".substr(trim($payment['M_OrderId']),0,10);
			//	$aREQ["orderid"] =time();
			//商户定单日期 Yyyymmdd
			$aREQ["orderDate"] = date("Ymd",empty($payment['M_Time'])?time():$payment['M_Time']);
			//商户定单时间 Hhmmss 非必须
			$aREQ["orderTime"] ="";
			//交易类别 0:B2C
			$aREQ['tranType']="0";
			//金额以元为单位 15位整数带2位小数
			$aREQ["amount"] = $payment['M_Amount'] ;
			//币种目前只支持人民币，代码为“CNY”
			$aREQ["curType"] = "CNY"; 
			//定单内容，非必填
			$aREQ["orderContent"]="";
			//商家备注 ，非必填
			$aREQ["orderMono"]="";
			//物流配送标志，非必填 0非物流 1物流配送
			$aREQ["phdFlag"]=0;
			//通知方式 0不通知，1通知，2转页面方式进行通知；
			$aREQ["notifyType"] = 1;
			//主动通知URL，为空不发通知，非必填
			$aREQ["merURL"] = $this->callbackUrl;
			//取货URL，为空不显示按钮，不显示跳转，非必填
			$aREQ["goodsURL"]="";
			//自动跳转时间,非必填
			$aREQ["jumpSeconds"]=1;
			//商户批次号 商户自己填写，对帐用，非必填
			$aREQ["payBatchNo"]="";
			//代理商家名称 ，非必填
			$aREQ["proxyMerName"]="";
			//代理商家证件类型，非必填
			$aREQ["proxyMerType"]="";
			//代理商家证件号码，非必填
			$aREQ["proxyMerCredentials"]="";
			//渠道 固定为0
			$aREQ['netType']=0;
			$srcMsg=implode("|",$aREQ);

			
			$merSignMsg = $this->bocomm_sign($realpath.md5('bocomshopex').".xml",$srcMsg);
			//error_log($merSignMsg,3,dirname(__FILE__)."/../bocom.merSignMsg.log");

			 $aREQ['merSignMsg'] = $merSignMsg;
			 return $aREQ;
    }
    function callback($in,&$paymentId,&$money,&$message,&$tradeno){
			$returnmsg=explode("|",$in['notifyMsg']);
			$realPath=dirname(__FILE__)."/../../cert/bocom/";
			$signMsg = array_pop($returnmsg);
			$srcMsg = implode("|",$returnmsg);
			$srcMsg .= "|";
			$paymentId=$returnmsg[1];
			$money=$returnmsg[2];
			$tradeno="";

			$ret = -100;
			$ret = $this->bocomm_verify($realPath.md5('bocomshopex').".xml",$srcMsg,$signMsg);

			if ($ret == 0){				
				$message="支付成功！";
				return PAY_SUCCESS;          
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
					"label"=>'是否开启日志',
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
		$realPath=dirname(__FILE__)."/../../cert/bocom/";
		$f=fopen($realPath.md5('bocomshopex').".xml",'w');
		$xmldata="<?xml version=\"1.0\"  encoding=\"gb2312\"?><BOCOMB2C><ApiURL>".$this->APIUrl."</ApiURL><OrderURL>".$this->submitUrl."</OrderURL><EnableLog>".$log."</EnableLog><LogPath>".$realPath."</LogPath><SettlementFilePath>".$realPath."</SettlementFilePath><MerchantCertFile>".$realPath.$aData["certFile"]."</MerchantCertFile><MerchantCertPassword>".$aData["keyPass"]."</MerchantCertPassword><RootCertFile>".$realPath.$aData["rootFile"]."</RootCertFile></BOCOMB2C>";
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

	function bocomm_sign($config,$message){
		$cmd  = "java  bocomm_sign \"{$config}\" \"{$message}\"";
		$handle = popen($cmd, 'r');
		while(!feof($handle)){ 
			$merSignMsg .= fread($handle,1024);
		}
		pclose($handle);
		if(preg_match('/<message>(.+)<\/message>/',$merSignMsg,$match)){
			$merSignMsg = $match[1];
			return $merSignMsg;
		}
		return false;
		
	}

	function bocomm_verify($config,$src,$sigend){
		$cmd  = "java  bocomm_verify {$config} \"{$src}\" \"{$sigend}\"";
		//error_log($cmd,3,dirname(__FILE__)."/../bocom.cmd.log");
		$handle = popen($cmd, 'r');
		while(!feof($handle)){ 
			$merSignMsg .= fread($handle,1024);
		}
		pclose($handle);
		if(preg_match('/<message>(.+)<\/message>/',$merSignMsg,$match)){
			$verifycode = $match[1];
			return $verifycode;
		}
		return false;
		
	}
}
?>