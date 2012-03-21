<?php
require('paymentPlugin.php');
class pay_abc extends paymentPlugin{
    var $name = '�й�ũҵ����B2C֧���ӿ�_JAVA_2.0.7';
    var $logo = 'ABC';
    var $version = 20070902;
    var $charset = 'gb2312';
	var $APIUrl0 = 'https://ebank.95559.com.cn/corporbank/NsTrans'; //��ʽAPI��ַ
	var $APIUrl1 = 'https://61.172.242.229/corporbank/NsTrans'; //����API��ַ
	var $submitUrl0 = 'https://www.95599.cn/b2c/trustpay/ReceiveMerchantIERequestServlet'; //��ʽ�ύ��ַ
	var $submitUrl1 = 'https://www.test.95599.cn/b2c/b2c/ReceiveMerchantIERequestServlet'; //�����ύ��ַ
    var $supportCurrency = array("CNY"=>"001");
    var $supportArea = array("AREA_CNY");
    var $intro = "�й�ũҵ����B2C֧���ӿ�_JAVA_2.0.7 php��д��";
    var $orderby = 100;
    var $head_charset="gb2312";
    function toSubmit($payment){
            $ret_url = 'http://lusen.vip.ishopex.cn/plugins/app/pay_abc/pay_abc.php';
            $this->submitUrl = $this->submitUrl0;
            $cert_file = dirname(__FILE__)."/cert/my.pem";
            $cert_file_password = '999999';
            $MerchantID = '233010300xxxxx';
            
            //�趨������������� begin
		   //�������(��Ҫ��Ϣ)
           $OrderNo = $payment['M_OrderId'];

		   //����˵��
		  //$tOrderDesc = iconv('GBK','UTF-8',$payment['M_Goods']);

		   //��������(��Ҫ��Ϣ - YYYY/MM/DD)
		  $OrderDate = date('Y/m/d',$payment['M_Time']);

		   //����ʱ��(��Ҫ��Ϣ - HH:MM:SS)
		  $OrderTime = date('H:i:s',$payment['M_Time']);

		   //�������(��Ҫ��Ϣ)
		  $OrderAmount = $payment['M_Amount'];

		   //������ѯ��ַ(��Ҫ��Ϣ)
		  $OrderURL = $ret_url;

		   //��IP��ַ��Ϣ
		  //$BuyIP = "";
		  //�趨������������� end
           
		  //�趨֧�������������� begin
           //��������(��Ҫ��Ϣ)
		   //$Order = $payment['M_OrderId'];

		   //�趨��Ʒ����(��Ҫ��Ϣ)
		  $ProductType = 1;

		   //�趨֧������(��Ҫ��Ϣ)
		  $PaymentType = 1;
	       
		   //�趨֧�����뷽ʽ(��Ҫ��Ϣ)
           $PaymentLinkType = 1;

		   //�趨֧�����֪ͨ��ʽ(��Ҫ��Ϣ)
		   $NotifyType = 0;

		   //֧�������ַ(��Ҫ��Ϣ)
		   $ResultNotifyURL = $ret_url;
		  //�趨֧�������������� end

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
            $message = "�޴�֧��ID��";
            return PAY_ERROR;
        }
        $pub_cert_file = dirname(__FILE__)."/cert/TrustPay.pem";
        if($this->verify($contents,$signature,$pub_cert_file)){
            $message="֧���ɹ���";
            return PAY_SUCCESS;
        }elseif($ok == 0) {
            $message = "��֤ǩ������";
            return PAY_ERROR;
        }else{
            $message = "��֤ǩ������";
            return PAY_ERROR;
        }
            
    }
    function getfields(){
            return array(
                'member_id'=>array(
                     'label'=>'�̻��ͻ���',
                     'type'=>'string',
                     'helpMsg'=>'�̻��ͻ������������ɲ��ṩ��15λ���'
                 ),
                 'certFile'=>array(
                     "label"=>'�̻�֤���ļ�(�ͻ��Լ�������֤��,pfx��ʽ)',
                     "type"=>'file'
                 ),
                 'rootFile'=>array(
                     "label"=>'���и�֤���ļ�(һ����root.cer)',
                     "type"=>'file'
                 ),
                 'keyPass'=>array(
                     "label"=>'�̻�֤��˽Կ��������',
                     "type"=>'string'
                 ),
				'bocomType'=>array(
					"label"=>'����ѡ��',
					 "type"=>'select',
					 "options"=>array("0"=>"��ʽ����","1"=>"���Ի���"),
					 ),
				'enableLog'=>array(
					"label"=>'�Ƿ�����־(��־�ļ�������/cert/bocom/֧����ʽID/Ŀ¼��)',
					 "type"=>'select',
					 "options"=>array("0"=>"��","1"=>"��"),
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
            //��ȡpem�ļ��е�˽Կ
            $pkeyid = openssl_get_privatekey($priv_key,$cert_file_password);
            //ʹ���û�˽Կ����Ϣ����ǩ��
            openssl_sign($to_sign, $signature, $pkeyid);
            // Free the key.
            openssl_free_key($pkeyid);
            return base64_encode( $signature );
	}        

	

}
?>