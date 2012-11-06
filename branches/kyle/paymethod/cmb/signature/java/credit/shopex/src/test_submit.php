<?php

$obj_test = new pay_cmbchina;
$payment = array(
'M_OrderId'=>'20121106114858',
'M_Time'=>time(),
'M_Amount'=>100,
);

$content = $obj_test->toSubmit($payment);
$submit_url = $obj_test->submitUrl;
$submit_charset = $obj_test->head_charset;

?>

<!doctype html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>招行支付网关测试 power@qy_ken</title>
</head>
<body>
<form action="<?php echo $submit_url; ?>" method=post target="_blank">
<?php foreach ($content as $key => $value) {
	echo "<input  type=text name='".$key."' value='".$value."' size=180 />\r\n";
	echo "<br/>";	
}
?>
<input type=submit value="提交" />
</form>
</body>
</html>

<?php

class paymentPlugin{

	function __construct(){
		$this->config_arr = array(
			'member_id'=>'123456',
			'PrivateKey'=>'fjsdfjsdflsd',
		);

		$this->callbackUrl = "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI'])."/test_callback.php";
	}

	function getConf($config_id,$key){
		return $this->config_arr[$key];
	}

}

class pay_cmbchina extends paymentPlugin{

    var $name = '招商银行';
    var $logo = 'CMBC';
    var $version = 20050802;
    var $charset = 'gbk';
    //var $submitUrl = 'https://netpay.cmbchina.com/netpayment/BaseHttp.dll?PrePayC2'; //正式地址
    var  $submitUrl = 'https://netpay.cmbchina.com/cdpay/cdpay.dll?cdpay';
    var $submitButton = 'http://img.alipay.com/pimg/button_alipaybutton_o_a.gif'; ##需要完善的地方
    var $supportCurrency = array("CNY"=>"001");
    var $supportArea = array("AREA_CNY");
    var $intro = "1987年，招商银行作为中国第一家由企业创办的商业银行，以及中国政府推动金融改革的试点银行，在中国改革开放的最前沿----深圳经济特区成立。2002年，招商银行在上海证券交易所上市；2006年，在香港联合交易所上市。";
    var $orderby = 35;
    var $head_charset="gbk";

    function toSubmit($payment){
        $merId = $this->getConf($payment["M_OrderId"], 'member_id');   
        $secret_key = $this->getConf($payment["M_OrderId"], 'PrivateKey');

        
        $return['Request'] = $this->get_request($payment,$merId,$secret_key);   

        return $return;
    }

    function get_request($payment,$merId,$secret_key){
      

        $header .= "<Head>";
        $header .= "<Version>1.0.0.0</Version>";
        $header .= "<TestFlag>N</TestFlag>";
        $header .= "</Head>";
      
      	$quote_header_string = $this->quote_string($header);

        $body .= "<Body>";
        $body .= "<MchMchNbr>".$merId."</MchMchNbr>";
        $body .= "<MchBllNbr>".$this->intString($payment["M_OrderId"],10)."</MchBllNbr>";
        $body .= "<MchBllDat>".date('Ymd',$payment['M_Time']?$payment['M_Time']:time())."</MchBllDat>";
        $body .= "<MchBllTim>".date('His',$payment['M_Time']?$payment['M_Time']:time())."</MchBllTim>";
        $body .= "<TrxTrxCcy>156</TrxTrxCcy>";
        $body .= "<TrxBllAmt>".$payment['M_Amount']."</TrxBllAmt>";
        $body .= "<TrxPedCnt>3</TrxPedCnt>";
        $body .= "<UIHidePed>N</UIHidePed>";
        $body .= "<MchUsrIdn>".$merId."</MchUsrIdn>";
        $body .= "<MchNtfUrl>".$this->callbackUrl."</MchNtfUrl>";
        $body .= "<MchNtfPam></MchNtfPam>";
        $body .= "<TrxGdsGrp>".$this->intString($payment["M_OrderId"],8)."</TrxGdsGrp>";
        $body .= "<TrxGdsNam>NA</TrxGdsNam>";
        $body .= "<TrxPstAdr>NA</TrxPstAdr>";
        $body .= "<TrxPstTel>51086858</TrxPstTel>";
        $body .= "</Body>";

        $quote_body_string = $this->quote_string($body);

     	$sign = $this->sign($quote_header_string.$quote_body_string,$secret_key);
        $quote_sign_string = $this->quote_string("<Signature>".$sign."</Signature>");

        $tran_data = $this->quote_string("<CDPRequest_Pay>");
        $tran_data .= $quote_header_string;
        $tran_data .= $quote_body_string;
      	$tran_data .= $quote_sign_string;
        $tran_data .= $this->quote_string("</CDPRequest_Pay>"); 

        return $tran_data;
        
    }

    private function quote_string($in_str){
    	$str = str_replace(array('<','>'),array('<$','$>'),$in_str);    
    	return str_replace('<$/','</$',$str);

    }

    private function sign($in_str,$secret_key){
    	return strtolower(sha1($secret_key.$in_str));
    }

    private function StrToHex($string)
    {
       $hex="";
       for($i=0;$i<strlen($string);$i++){ $hex.=dechex(ord($string[$i])); }
       $hex=strtoupper($hex);
       return $hex;
    }
    
    function HextoStr($hex) 
    {  
    	$string='';
    	for ($i=0; $i < strlen($hex)-1; $i+=2)
    	{
        	$string .= chr(hexdec($hex[$i].$hex[$i+1]));
    	}
    	
    	return $string;
    }
    

    function callback($in,&$paymentId,&$money,&$message,&$tradeno){
        $Succeed = $in["Succeed"];
        $para = http_build_query($in);
        $MerPk = $this->getConf($paymentId,'MerPrk');
        $isok = 'true';
        if($isok =='true'){
            if($Succeed == "Y"){
                $message = "支付成功！";
                return PAY_SUCCESS;
            }else{
                $message = "支付失败！";
                return PAY_FAILED;
            }
        }else{
            $message = "签名验证错误！";
            return PAY_ERROR;
        }
    }

    function getfields(){
            return array(
                'member_id'=>array(
                        'label'=>'客户号',
                        'type'=>'string'
                    ),
                'PrivateKey'=>array(
                        'label'=>'私钥',
                        'type'=>'string'
                    ),
                'MerPrk'=>array(
                        'label'=>'公钥文件',
                        'type'=>'file'
                ),

            );
    }
    function intString($intvalue,$len){
        $intstr=intval($intvalue);
        if(strlen($intvalue)<$len){
            for ($i=1;$i<=$len-strlen($intstr);$i++){
                $tmpstr .= "0";
            }
            return $tmpstr.$intstr;
        }else if(strlen($intvalue)>$len){
            return substr($intvalue,-$len);
        }else
            return $intvalue;
    }
}
