<?php
require('paymentPlugin.php');
class pay_xpay extends paymentPlugin{

    var $name = 'XPAY 易付通';//XPAY 易付通
    var $logo = 'XPAY';
    var $version = 20070902;
    var $charset = 'gb2312';
    var $submitUrl = 'http://pay.xpay.cn/pay.aspx'; //  
    var $submitButton = 'http://img.alipay.com/pimg/button_alipaybutton_o_a.gif'; ##需要完善的地方
    var $supportCurrency =  array("CNY"=>"1");
    var $supportArea =  array("AREA_CNY");
    var $desc = '';
    var $orderby = 22;
        
    function toSubmit($payment){
        $merId = $this->getConf($payment["M_OrderId"], 'member_id');
        $ikey = $this->getConf($payment["M_OrderId"], 'PrivateKey');//私钥值，商户可上99BILL快钱后台自行设定

        $order->M_Language = "gb2312";
        $card = "bank";                
        $scard = "";
        $actioncode = "sell";
        $actionParameter = "";
        $ver = "2.0";
        //$msign = md5($ikey.":".$order->M_Amount.",".$merId.$order->M_OrderId.",".$merId.",".$card.",".$scard.",".$actioncode.",".$actionParameter.",".$ver);// 
        $msign = md5($ikey.":".$payment["M_Amount"].",".$merId.$payment["M_OrderId"].",".$merId.",".$card.",".$scard.",".$actioncode.",".$actionParameter.",".$ver);// 
        $msign = strtolower($msign);
        
        $return["prc"] = $payment["M_Amount"];//$order->M_Amount;
        $return["bid"] = $merId.$payment["M_OrderId"];//$order->M_OrderId;
        $return["tid"] = $merId;
        $return["card"] = $card;
        $return["scard"] = $scard;
        $return["actionCode"] = $actioncode;
        $return["actionparameter"] = "";
        $return["ver"] = $ver;
        $return["pdt"] = $payment["M_OrderNO"];//$order->M_OrderNO;
        $return["username"] = $payment["R_Name"];//$order->R_Name;
        $return["lang"] = $payment["M_Language"];//$order->M_Language;
        $return["url"] = $this->callbackUrl;
        $return["remark1"] = $payment["M_Remark"];//$order->M_Remark;
        $return["md"] = $msign;
        $return["sitename"] = $this->getConf('system.shopname');
        $return["siteurl"] = $this->getConf('system.shopurl');
                
        return $return;
    }

    function callback($in,&$paymentId,&$money,&$message){    
        $tid = $in["?tid"];//   '商户唯一交易号）
        $bid = $in["bid"];//     '商户网站订单号
        $sid = $in["sid"];//     '易付通交易成功 流水号
        $prc = $in["prc"];//    '支付的金额
        $actionCode = $in["actioncode"];//    '交易码
        $actionParameter = $in["actionparameter"];//    '业务代码
        $card = $in["card"];//    '支付方式
        $success = $in["success"];//    '成功标志，
        $bankcode = $in["bankcode"];//   '支付银行
        $remark1 = $in["remark1"];//     '备注信息
        $username = $in["username"];//  '商户网站支付用户
        $md = $in["md"];//               '32位md5加密数据

        $paymentId = $bid;
        $money = $prc;

        $key = $this->getConf($tid, 'PrivateKey');

        $text = md5($key . ":" . $bid . "," . $sid . "," . $prc . "," . $actionCode  ."," . $actionParameter . "," . $tid . "," . $card . "," . $success);

        $mymd5 = strtolower($text);
        $orderid = substr($bid, -6);
        $date = date("Ymd", mktime());

        if (strtolower($md) == $mymd5){
            if ($success=="true"){
                //成功支付
                return PAY_SUCCESS;
            }else{
                $message = '支付失败,请立即与商店管理员联系';
                return PAY_FAILED;
            }
        }else{
            $message = '签名认证失败,请立即与商店管理员联系';
            return PAY_ERROR;
        }

        //mt_srand((double)microtime()*1000000);
        //$randval = mt_rand(10000000,99999999);
        //$signstr = substr(hexdec(md5($tmp_orderno.$state.mktime().$randval)), 0, 10);
        //$Order->updateOrderSign($tmp_orderno, $signstr);
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
}
?>
