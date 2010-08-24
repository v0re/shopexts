<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

final class ectools_payment_plugin_99bill extends ectools_payment_app {

    public $name = '快钱网上支付';//快钱网上支付
    public $app_name = '快钱支付接口';
	public $app_key = '99bill';
    public $display_name = '快钱';
    public $curname = 'CNY';
	public $ver = '1.0';

    function is_fields_valiad(){
        return true;
    }
    function intro(){
        return '<b><h3>ShopEx联合快钱推出：免费签约，1%优惠费率，更有超值优惠的信用卡支付。</h3></b><bR>快钱是国内领先的独立第三方支付企业，旨在为各类企业及个人提供安全、便捷和保密的支付清算与账务服务，其推出的支付产品包括但不限于人民币支付，外卡支付，神州行卡支付，联通充值卡支付，VPOS支付等众多支付产品, 支持互联网、手机、电话和POS等多种终端, 以满足各类企业和个人的不同支付需求。截至2009年6月30日，快钱已拥有4100万注册用户和逾31万商业合作伙伴，并荣获中国信息安全产品测评认证中心颁发的“支付清算系统安全技术保障级一级”认证证书和国际PCI安全认证。<b><h3>注：本接口为银行直连，数据显示，可以提升78%的潜在消费者完成购买行为。</h3></b>';
    }
    function admin_intro(){
        return 'ShopEx联合快钱推出：免费签约，1%优惠费率，更有超值优惠的信用卡支付。<bR>快钱是国内领先的独立第三方支付企业，旨在为各类企业及个人提供安全、便捷和保密的支付清算与账务服务，其推出的支付产品包括但不限于人民币支付，外卡支付，神州行卡支付，联通充值卡支付，VPOS支付等众多支付产品, 支持互联网、手机、电话和POS等多种终端, 以满足各类企业和个人的不同支付需求。截至2009年6月30日，快钱已拥有4100万注册用户和逾31万商业合作伙伴，并荣获中国信息安全产品测评认证中心颁发的“支付清算系统安全技术保障级一级”认证证书和国际PCI安全认证。';
    }
    
	public function __construct($app)
	{
		parent::__construct($app);
		
         //$this->callback_url = $this->app->base_url(true)."/apps/".basename(dirname(__FILE__))."/".basename(__FILE__);
		$this->callback_url = kernel::api_url('api.ectools_payment/parse/' . $this->app->app_id . '/ectools_payment_plugin_99bill', 'callback');
        $this->submit_url = 'https://www.99bill.com/gateway/recvMerchantInfoAction.htm';
        $this->submit_method = 'POST';
        $this->submit_charset = 'utf-8';
    }
	
	public function dopay($payment){
        $merId = $this->getConf('mer_id', __CLASS__);
        $ikey = $this->getConf('PrivateKey', __CLASS__);//私钥值，商户可上99BILL快钱后台自行设定 
        $connecttype = $this->getConf('ConnectType', __CLASS__);
        if ($connecttype){
            $bankId = $payment['payExtend']['bankId'];
            $payType='10';
        }
        $payment['M_Amount']=ceil($payment['cur_money'] * 100);
        $orderTime = date('YmdHis',$payment['orders'][0]['rel_id']?$payment['orders'][0]['rel_id']:time());
        $return['inputCharset']="1";
        $return['bgUrl'] = $this->submit_url;
        $return['version'] = "v2.0";
        $return['language']="1";
        $return['signType']="1";
        $return['merchantAcctId'] = $merId;
        $return['payerName']=$payment['pay_name'];
        $return['payerContactType']="1";//支付人联系方式类型.固定选择值，目前只能为电子邮件
        $return['payerContact']=$payment['P_Email'];//支付人联系方式
        $return['orderId']= $payment['payment_id'];
        $return['orderAmount'] = $payment['M_Amount'];
        $return['orderTime'] = $orderTime;
        $return['productName'] = $payment['orders'][0]['rel_id'];
        $return['productNum'] = "1";
        $return['productId'] = "";
        $return['productDesc'] = $payment['M_Remark'];
        $return['ext1']= "";
        $return['ext2'] = "";
        $return['payType'] = $payType?$payType:"00";
        $return['bankId'] = $bankId?$bankId:'';
        $return['redoFlag'] = 1;//是否重复提交同一个订单
        $return['pid'] = "10017518267";//合作ID
        foreach($return as $k=>$v){
            if ($v)
                $str.=$k."=".$v."&";
        }
        $signMsg=strtoupper(md5(substr($str,0,strlen($str)-1)."&key=".$ikey));
        $return['signMsg']=$signMsg;
        foreach($return as $key=>$val) {
            $this->add_field($key,$val);
        }
        if($this->is_fields_valiad()){
            //header('Content-type: text/html;charset=gb2312',false);
            echo $this->get_html();exit;
        }else{
            return false;
        }
    }

    function callback(&$in){
		$objMath = kernel::single('ectools_math');
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
    
        $key=$orderId ? $orderId : $this->getConf('PrivateKey', __CLASS__);

        //生成加密串。必须保持如下顺序。
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal, "merchantAcctId",$merchantAcctId);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal, "version",$version);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal, "language",$language);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal, "signType",$signType);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal, "payType",$payType);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal, "bankId",$bankId);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal, "orderId",$orderId);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal, "orderTime",$orderTime);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal, "orderAmount",$orderAmount);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal, "dealId",$dealId);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal, "bankDealId",$bankDealId);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal, "dealTime",$dealTime);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal, "payAmount",$payAmount);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal, "fee",$fee);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal, "ext1",$ext1);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal, "ext2",$ext2);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal, "payResult",$payResult);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal, "errCode",$errCode);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal, "key",$key);
		
		$ret = array();
		$ret['payment_id'] = $in['orderId'];
		$ret['account'] = $merchantAcctId;
		$ret['bank'] = $bankId;
		$ret['pay_account'] = '付款帐号';
		$ret['currency'] = 'CNY';
		$ret['money'] = $objMath->number_multiple(array($in['orderAmount'], 0.01));	
		$ret['paycost'] = $objMath->number_multiple(array($fee, 0.01));
		$ret['cur_money'] = $objMath->number_multiple(array($in['orderAmount'], 0.01));
		$ret['trade_no'] = $in['dealId'];
		$ret['t_payed'] = strtotime($in['orderTime']);
		$ret['pay_app_id'] = "99bill";
		$ret['pay_type'] = 'online';
		$ret['memo'] = '99bill';
	
        $merchantSignMsg= md5($merchantSignMsgVal);
        $paymentId=$orderId;
        $money = $payAmount/100;
        $tradeno = $dealId;
        $sUrl = $this->app->base_url(true);
        ///首先进行签名字符串验证
        if(strtoupper($signMsg) == strtoupper($merchantSignMsg)){
            switch($payResult){ 
                  case "10":
                    $rtnOk=1;
                    $rtnUrl=$sUrl.$url."?payment_id=".$orderId;
                    echo "<result>".$rtnOk."</result><redirecturl>".$rtnUrl."</redirecturl>";
					$ret['status'] = 'succ'; 
                    break;
                  default:
                    $rtnOk=1;
                    $rtnUrl=$sUrl.$url."?payment_id=".$orderId;
                    echo "<result>".$rtnOk."</result><redirecturl>".$rtnUrl."</redirecturl>";
					$ret['status'] = 'failed'; 
                    break;
            }
        }else{
            $message="签名认证失败！";
            $rtnOk=1;
            $rtnUrl=$sUrl.$url."?payment_id=".$orderId;
            echo "<result>".$rtnOk."</result><redirecturl>".$rtnUrl."</redirecturl>";
			$ret['status'] = 'failed';
        }
		
		// 改变支付单据的状态
		/*$this->app->model("payments")->update(
			array(
				"trade_no" => '99bill trade no.' . $ret['trade_no'], 
				"t_payed" => $ret['t_payed'],
				"status" => $ret['status'],
			),
			array("payment_id" => $paymentId)
		);*/
		
		return $ret;
    }

    function setting(){
        return array(
                'pay_name'=>array(
                        'title'=>'支付方式名称',
                        'type'=>'string',
						'validate_type' => 'required',
                ),
                'mer_id'=>array(
                        'title'=>'客户号',
                        'type'=>'string',
						'validate_type' => 'required',
                ),
                'PrivateKey'=>array(
                        'title'=>'私钥',
                        'type'=>'string',
						'validate_type' => 'required',
                ),
                'ConnectType'=>array(
                    'title'=>'顾客付款类型',
                    'type'=>'radio',
                    'options'=>array('0'=>'登录快钱支付','1'=>'银行直接支付'),
                    'event'=>'showbank',
                    'eventscripts'=>'<script>function showbank(obj){if (obj.value==1){$(\'bankShow\').show();}else {$(\'bankShow\').hide();}}</script>',
                    'extendcontent'=>array(
                        array(
                            "property"=>array(
                                "type"=>"checkbox",//后台显示方式
                                "name"=>"bankId",
                                "size"=>6,
                                "extconId"=>"bankShow",
                                "display"=>0,
                                "fronttype"=>"radio", //前台显示方式
                                "frontsize"=>6,
                                "frontname"=>"showbank"
                            ),
                            "value"=>array(
                                array("value"=>"ICBC","imgname"=>"bank_icbc.gif","name"=>"中国工商银行"),
                                array("value"=>"CMB","imgname"=>"bank_cmb.gif","name"=>"招商银行"),
                                array("value"=>"ABC","imgname"=>"bank_abc.gif","name"=>"中国农业银行"),
                                array("value"=>"CCB","imgname"=>"bank_ccb.gif","name"=>"中国建设银行"),
                                array("value"=>"SPDB","imgname"=>"bank_spdb.gif","name"=>"上海浦东发展银行"),
                                array("value"=>"BCOM","imgname"=>"bank_bcom.gif","name"=>"交通银行"),
                                array("value"=>"CMBC","imgname"=>"bank_cmbc.gif","name"=>"中国民生银行"),
                                array("value"=>"SDB","imgname"=>"bank_sdb.gif","name"=>"深圳发展银行"),
                                array("value"=>"GDB","imgname"=>"bank_gdb.gif","name"=>"广东发展银行"),
                                array("value"=>"CITIC","imgname"=>"bank_citic.gif","name"=>"中信银行"),
                                array("value"=>"HXB","imgname"=>"bank_hxb.gif","name"=>"华夏银行"),
                                array("value"=>"CIB","imgname"=>"bank_cib.gif","name"=>"兴业银行"),
                                array("value"=>"GZRCC","imgname"=>"bank_gzrcc.gif","name"=>"广州市农村信用合作社"),
                                array("value"=>"GZCB","imgname"=>"bank_gzcb.gif","name"=>"广州市商业银行"),
                                array("value"=>"SHRCC","imgname"=>"bank_shrcc.gif","name"=>"上海农村商业银行"),
                                array("value"=>"POST","imgname"=>"bank_post.gif","name"=>"中国邮政储蓄"),
                                array("value"=>"BOB","imgname"=>"bank_bob.gif","name"=>"北京银行"),
                                array("value"=>"BOC","imgname"=>"bank_boc.gif","name"=>"中国银行"),
                                array("value"=>"CBHB","imgname"=>"bank_cbhb.gif","name"=>"渤海银行"),
                                array("value"=>"BJRCB","imgname"=>"bank_bjrcb.gif","name"=>"北京农村商业银行"),
                                array("value"=>"CEB","imgname"=>"bank_ceb.gif","name"=>"中国光大银行"),
                                array("value"=>"NJCB","imgname"=>"bank_njcb.gif","name"=>"南京银行"),
                                array("value"=>"BEA","imgname"=>"bank_bea.gif","name"=>"东亚银行"),
                                array("value"=>"NBCB","imgname"=>"bank_nbcb.gif","name"=>"宁波银行"),
                                array("value"=>"HZB","imgname"=>"bank_hzb.gif","name"=>"杭州银行"),
                                array("value"=>"PAB","imgname"=>"bank_pab.gif","name"=>"平安银行")
                            )
                        )

                    )
                ),
				'support_cur'=>array(
					'title'=>'支持币种',
					'type'=>'text hidden',
					'options'=>array('1'=>'人民币','2'=>'其他','3'=>'商店默认货币')
				),
                'pay_fee'=>array(
                    'title'=>'交易费率',
                    'type'=>'pecentage',
					'validate_type' => 'number',
                ),
                'pay_desc'=>array(
                    'title'=>'描述',
					'type'=>'html',
					'includeBase' => true,
                ),
				'pay_type'=>array(
					 'title'=>'支付类型(是否在线支付)',
					 'type'=>'hidden',
					 'name' => 'pay_type',
				),
				'status'=>array(
					'title'=>'是否开启此支付方式',
					'type'=>'radio',
					'options'=>array('false'=>'否','true'=>'是'),
					'name' => 'status',
				),
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
    function gen_form(){
          $certid=$this->app->getConf('certificate.id');		  
          $url=urlencode($this->app->base_url());
		  
          $tmp_form='<a href="http://service.shopex.cn/checkcert.php?pay_id=2&certi_id='.$certid.'&url='.$url.'
" target="_blank"><img src="'.$this->app->base_url().'plugins/payment/images/99bill_apply.gif"></a>&nbsp;&nbsp;&nbsp;<font color="green">绿卡用户申请快钱支付，费率更低，</font><a href="http://www.shopex.cn/shopex_price/sq/index.html" target="_blank"><font color="green">点击了解绿卡</font></a>';

          return $tmp_form;

    }
}

?>
