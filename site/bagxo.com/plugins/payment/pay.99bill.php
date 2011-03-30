<?php
require('paymentPlugin.php');
class pay_99bill extends paymentPlugin{

    var $name = '快钱网上支付';//快钱网上支付
    var $logo = '99BILL';
    var $version = 20070902;
    var $charset = 'utf8';
    var $applyUrl = '';
    //var $submitUrl = 'https://www.99bill.com/webapp/receiveMerchantInfoAction.do';
    var $submitUrl = 'https://www.99bill.com/gateway/recvMerchantInfoAction.htm';
    var $submitButton = 'http://img.alipay.com/pimg/button_alipaybutton_o_a.gif'; ##需要完善的地方
    var $supportCurrency =  array("CNY"=>"1");
    var $supportArea =  array("AREA_CNY");
    var $desc = '上海快钱信息服务有限公司是国内第一家提供基于EMAIL和手机号码的网上收付费平台的互联网企业，已覆盖27亿张国际国内银行卡，同时支持邮政线下支付、银行转帐和跨国结算。';
    var $intro = '上海快钱信息服务有限公司是国内第一家提供基于EMAIL和手机号码的网上收付费平台的互联网企业，已覆盖27亿张国际国内银行卡，同时支持邮政线下支付、银行转帐和跨国结算。';
    var $orderby = 7;
    var $head_charset="utf-8";
    function toSubmit($payment){
        $merId = $this->getConf($payment['M_OrderId'], 'member_id');
        $ikey = $this->getConf($payment['M_OrderId'], 'PrivateKey');//私钥值，商户可上99BILL快钱后台自行设定    
        $payment['M_Amount']=ceil($payment['M_Amount'] * 100);
        $orderTime = date('YmdHis',$payment['M_Time']?$payment['M_Time']:time());
        $return['inputCharset']="1";
        $return['bgUrl'] = $this->callbackUrl;
        $return['version'] = "v2.0";
        $return['language']="1";
        $return['signType']="1";
        $return['merchantAcctId'] = $merId;
        $return['payerName']=$payment['P_Name'];
        $return['payerContactType']="1";//支付人联系方式类型.固定选择值，目前只能为电子邮件
        $return['payerContact']=$payment['P_Email'];//支付人联系方式
        $return['orderId']= $payment['M_OrderId'];
        $return['orderAmount'] = $payment['M_Amount'];
        $return['orderTime'] = $orderTime;
        $return['productName'] = $payment['M_OrderNO'];
        $return['productNum'] = "1";
        $return['productId'] = "";
        $return['productDesc'] = $payment['M_Remark'];
        $return['ext1']= "";
        $return['ext2'] = "";
        $return['payType'] = "00";
        $return['redoFlag'] = 1;//是否重复提交同一个订单
        $return['pid'] = "";//合作ID
        foreach($return as $k=>$v){
            if ($v)
                $str.=$k."=".$v."&";
        }
        $signMsg=strtoupper(md5(substr($str,0,strlen($str)-1)."&key=".$ikey));
        $return['signMsg']=$signMsg;
        return $return;
    }

    function callback($in,&$paymentId,&$money,&$message,&$tradeno){
        $system = &$GLOBALS['system'];
        $url = $system->mkUrl('paycenter',$act='result');
        $merchantAcctId=trim($in['merchantAcctId']);
        $version=trim($in['version']);
        $language=trim($in['language']);
        $signType=trim($in['signType']);
        $payType=trim($in['payType']);
        $orderId=trim($in['orderId']);
        $orderTime=trim($in['orderTime']);
        $bankId = trim($in['bankId']);
        //获取原始订单金额
        ///订单提交到快钱时的金额，单位为分。
        ///比方2 ，代表0.02元
        $orderAmount=trim($in['orderAmount']);
        $dealId=trim($in['dealId']); //获取该交易在快钱的交易号
        $bankDealId=trim($in['bankDealId']); //如果使用银行卡支付时，在银行的交易号。如不是通过银行支付，则为空
        $dealTime=trim($in['dealTime']);
        //获取实际支付金额
        ///单位为分
        ///比方 2 ，代表0.02元
        $payAmount=trim($in['payAmount']);
        //获取交易手续费
        ///单位为分
        ///比方 2 ，代表0.02元
        $fee=trim($in['fee']);
        //获取处理结果
        ///10代表 成功; 11代表 失败
        ///00代表 下订单成功（仅对电话银行支付订单返回）;01代表 下订单失败（仅对电话银行支付订单返回）
        $payResult=trim($in['payResult']);
        $errCode=trim($in['errCode']);
        $signMsg=trim($in['signMsg']);    //获取加密签名串
    
        $key=$this->getConf($orderId,'PrivateKey');

        //生成加密串。必须保持如下顺序。
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"merchantAcctId",$merchantAcctId);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"version",$version);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"language",$language);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"signType",$signType);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"payType",$payType);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"bankId",$bankId);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"orderId",$orderId);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"orderTime",$orderTime);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"orderAmount",$orderAmount);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"dealId",$dealId);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"bankDealId",$bankDealId);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"dealTime",$dealTime);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"payAmount",$payAmount);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"fee",$fee);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"ext1",$ext1);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"ext2",$ext2);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"payResult",$payResult);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"errCode",$errCode);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"key",$key);
        $merchantSignMsg= md5($merchantSignMsgVal);
        $paymentId=$orderId;
        $money = $payAmount/100;
        $tradeno = $dealId;
        $system = &$GLOBALS['system'];
        $sUrl = $system->base_url();
        $url = $system->mkUrl('paycenter',$act='result');
        ///首先进行签名字符串验证
        if(strtoupper($signMsg) == strtoupper($merchantSignMsg)){
            switch($payResult){ 
                  case "10":
                    $rtnOk=1;
                    $rtnUrl=$sUrl.$url."?payment_id=".$orderId;
                    echo "<result>".$rtnOk."</result><redirecturl>".$rtnUrl."</redirecturl>";
                    return PAY_SUCCESS;
                    break;
                  default:
                    $rtnOk=1;
                    $rtnUrl=$sUrl.$url."?payment_id=".$orderId;
                    echo "<result>".$rtnOk."</result><redirecturl>".$rtnUrl."</redirecturl>";
                    return PAY_FAIL;
                    break;
            }
        }else{
            $message="签名认证失败！";
            $rtnOk=1;
            $rtnUrl=$sUrl.$url."?payment_id=".$orderId;
            echo "<result>".$rtnOk."</result><redirecturl>".$rtnUrl."</redirecturl>";
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
                )
        );
    }

    function appendParam($returnStr,$paramId,$paramValue){
        if($returnStr != ""){
            if($paramValue != ""){
                $returnStr.="&".$paramId."=".$paramValue;
            }
        }else{
            If($paramValue!=""){
                $returnStr=$paramId."=".$paramValue;
            }
        }
        return $returnStr;
    }
}
?>
