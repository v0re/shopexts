<?php
/*****************************************************
    IPAY在线支付		涉及数据表：无
    Programer:Nick
    Create:2004-1-8
    Last updated:2003-1-9
******************************************************/
include_once(dirname(__FILE__)."/PayFunction.php");
//双参数md5供首信易
function shopex_hmac_md5($data,$key)
{
        if (strlen($key) > 64) {
           $key =  pack('H32', md5($key));
        }
        if (strlen($key) < 64) {
            $key = str_pad($key, 64, chr(0));
        }
        
        $_ipad = (substr($key, 0, 64) ^ str_repeat(chr(0x36), 64));
        $_opad = (substr($key, 0, 64) ^ str_repeat(chr(0x5C), 64));
		return md5($_opad . pack('H32', md5($_ipad . $data)));
}

class Pay {
	var $type = "";
	var $action = "";
    var $merid = "";		 	//4位数字，必填，由IPAY负责分配
    var $orderno = "";  		//商店订单编号
    var $orderid = "";  		//支付订单编号（流水号）
	var $amount = "";			//必填，单位元，小数点后保留两位，如10或12.34
	var $dispamount = "";		//显示支付的货币金额（只作显示用）
	var $rname = "";			//收货人姓名
	var $remail = "";			//收货人email
	var $rtel = "";				//收货人电话
	var $rpost = "";			//收货人邮编
	var $raddr = "";			//收货人地址
	var $buyer_mobile = "";		//收货人移动
	var $rnote = "";			//备注
	var $orderdetail = "";		//订单明细
	var $payname = "";			//付款人姓名
	var $ikey = "";				//私钥值
	var $secondkey = "";		//第二私钥值
	var $currency = "01";		//币种
	var $transport = "2";		//ALIPAY运输方式
	var $lang = "1";			//语言
	var $url = "";				//指定返回网址
	var $shopname = "";				//商店名称
	var $shopexid ="";			//shopex用户号	平台ID 5位 + 网店ID 5位
	var $ordertime ;
	var $ordinary_fee = "0.00" ;
	var $express_fee = "0.00" ;
	var $test_mode = false;

    function Pay()
	{
		if(defined("PAYMENT_TEST"))
		{
			if(PAYMENT_TEST) $this->test_mode = true;
		}
	}

	//var $md5string = "MD5字符串值";	//数字签名，确保是商户提供的订单

//	"http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/admin_comment_list.php";
	//生成提交给支付网关的 hidden 域
	function getString()
	{
		global $PROG_TAGS,$INC_SHOPID,$PAYMENT_KEYROOT,$FRONTEND_LANG;
		switch ($this->type){

			case "IPS":		//IPS支付
				$md5string=md5($this->merid.$this->orderid.$this->amount.date("Ymd",$this->ordertime).$this->ikey);
				$this->rname = $this->dolocal($this->rname,"zh");
				$this->remail = $this->dolocal($this->remail,"zh");
				$this->raddr = $this->dolocal($this->raddr,"zh");
				$this->rpost = $this->dolocal($this->rpost,"zh");
				$this->rtel = $this->dolocal($this->rtel,"zh");
				$this->rnote = $this->dolocal($this->rnote,"zh");
				$this->payname = $this->dolocal($this->payname,"zh");
				if ($this->currency == "01") $this->lang = 1;
				else $this->lang = 2;
				$hiddenString = "<input type=hidden name=shopex_encoding value=\"gb2312\">";
				$hiddenString.= "<input type=hidden name=Mer_code value=\"".$this->merid."\">";
				$hiddenString.= "<input type=hidden name=Billno value=\"".$this->merid.$this->orderid."\">";
				$hiddenString.= "<input type=hidden name=Amount value=\"".$this->amount."\">";
				$hiddenString.= "<input type=hidden name=DispAmount value=\"".$this->dispamount."\">";
				$hiddenString.= "<input type=hidden name=Date value=\"".date("Ymd",$this->ordertime)."\">";
				$hiddenString.= "<input type=hidden name=Currency value=\"".$this->currency."\">";
				$hiddenString.= "<input type=hidden name=Attach value=\"".$this->rnote."\">";
				$hiddenString.= "<input type=hidden name=Lang value=\"".$this->lang."\">";
				$hiddenString.= "<input type=hidden name=RetEncodeType value=\"2\">";
				$hiddenString.= "<input type=hidden name=OrderEncodeType value=\"1\">";
				$hiddenString.= "<input type=hidden name=SignMD5 value=\"".$md5string."\">";
				$hiddenString.= "<input type=hidden name=merchanturl value= \"".$this->url."index.php?gOo=ips_reply.do&\">";
				$this->action = "https://www.ips.com.cn/ipay/ipayment.asp";
				return $hiddenString;
				break;

			case "IPS3":
				$tmp_url = $this->url."index.php?gOo=ips3_reply.do&";
				$tmp_urlserver = $this->url."index.php?gOo=ips3_server.do&";
				$orderdate = date("Ymd",$this->ordertime);

				$StrMd5 = md5($this->orderid.$this->amount.$orderdate."RMB".$this->ikey);
				switch($FRONTEND_LANG)
				{
					case "zh":
						$lang = 1;
						break;
					case "big5":
						$lang = 3;
						break;
					default:
						$lang = 2;
				}

				$hiddenString = "<input type=HIDDEN name=\"Mer_code\" value=\"".$this->merid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"Billno\" value=\"".$this->orderid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"Amount\" value=\"".$this->amount."\">";
				$hiddenString.= "<input type=HIDDEN name=\"Date\" value=\"".$orderdate."\">";
				$hiddenString.= "<input type=HIDDEN name=\"Currency_Type\" value=\"RMB\">";
				$hiddenString.= "<input type=HIDDEN name=\"Gateway_Type\" value=\"".$this->currency."\">";
				$hiddenString.= "<input type=HIDDEN name=\"Lang\" value=\"".$lang."\">";
				$hiddenString.= "<input type=HIDDEN name=\"Merchanturl\" value=\"".$tmp_url."\">";
				$hiddenString.= "<input type=HIDDEN name=\"FailUrl\" value=\"".$tmp_url."\">";
				$hiddenString.= "<input type=hidden name=\"DispAmount\" value=\"".$this->dispamount."\">";
				$hiddenString.= "<input type=HIDDEN name=\"OrderEncodeType\" value=\"1\">";
				$hiddenString.= "<input type=HIDDEN name=\"RetEncodeType\" value=\"12\">";
				$hiddenString.= "<input type=HIDDEN name=\"Rettype\" value=\"1\">";
				$hiddenString.= "<input type=HIDDEN name=\"ServerUrl\" value=\"".$tmp_urlserver."\">";
				$hiddenString.= "<input type=HIDDEN name=\"SignMD5\" value=\"".$StrMd5."\">";
				
				$this->action = "https://pay.ips.com.cn/ipayment.aspx";
				return $hiddenString;
				break;
				
			case "IPAY":	//IPAY
//				if(empty($this->ikey)) $this->ikey = "test";
				$v_mobile = "13800138000";
				$md5string=md5($this->merid.$this->orderid.$this->amount.$this->remail.$v_mobile.$this->ikey);
				$this->rname = $this->dolocal($this->rname,"zh");
				$this->remail = $this->dolocal($this->remail,"zh");
				$this->raddr = $this->dolocal($this->raddr,"zh");
				$this->rpost = $this->dolocal($this->rpost,"zh");
				$this->rtel = $this->dolocal($this->rtel,"zh");
				$this->rnote = $this->dolocal($this->rnote,"zh");
				$this->payname = $this->dolocal($this->payname,"zh");
				$tmp_url = $this->url."index.php?gOo=ipay_reply.do&";
				
				$hiddenString = "<input type=hidden name=shopex_encoding value=\"gb2312\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_mid\" value=\"".$this->merid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_oid\" value=\"".$this->orderid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_amount\" value=\"".$this->amount."\">";
				$hiddenString.= "<input type=hidden name=\"v_date\" value=\"".date("Ymd",$this->ordertime)."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_rname\" value=\"".$this->rname."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_email\" value=\"".$this->remail."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_mobile\" value=\"$v_mobile\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_rtel\" value=\"".$this->rtel."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_rpost\" value=\"".$this->rpost."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_raddr\" value=\"".$this->raddr."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_rnote\" value=\"".$this->rnote."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_payname\" value=\"".$this->payname."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_url\" value=\"".$tmp_url."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_md5\" value=\"".$md5string."\">";

				$this->action = "http://www.ipay.cn/4.0/bank.shtml";
				return $hiddenString;
				break;

			case "SHOUXIN":	//SHOUXIN
				if ($this->currency == "1")
				{
					$this->action = (!$this->test_mode)?"http://pay.beijing.com.cn/prs/e_user_payment.checkit":"";
				}
				else
				{
					$this->action = (!$this->test_mode)?"http://pay.beijing.com.cn/prs/user_payment.checkit":"";
				}
				
				$mallConfig = newclass("mallConfig");
				$mallConfig->getInfo();

				if(empty($this->ikey)) $this->ikey = "test";
				$tmp_url = $this->url."shouxin_autoredirect.php";
				$oid = date("Ymd",$this->ordertime)."-".$this->merid."-".$this->orderid;
				$this->rname = $this->merid;
				$sourcedata = $this->currency.date("Ymd",$this->ordertime).$this->amount.$this->rname.$oid.$this->merid.$tmp_url;

				$this->raddr = $this->dolocal($this->raddr,"zh");
				$this->rpost = $this->dolocal($this->rpost,"zh");
				$this->rtel = $this->dolocal($this->rtel,"zh");
				$this->rnote = $this->dolocal($this->rnote,"zh");
				$this->payname = $this->dolocal($this->payname,"zh");
				$hiddenString = "<input type=hidden name=shopex_encoding value=\"gb2312\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_mid\" value=\"".$this->merid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_oid\" value=\"".$oid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_amount\" value=\"".$this->amount."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_rcvname\" value=\"".$this->rname."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_rcvtel\" value=\"".$this->rtel."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_rcvpost\" value=\"".$this->rpost."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_rcvaddr\" value=\"".$this->raddr."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_ymd\" value=\"".date("Ymd",$this->ordertime)."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_orderstatus\" value=\"0\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_ordername\" value=\"".$this->payname."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_moneytype\" value=\"".$this->currency."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_md5info\" value=\"".shopex_hmac_md5($sourcedata,$this->ikey)."\">";
				$hiddenString.= "<input type=hidden name=\"v_url\" value=\"".$tmp_url."\">";

				return $hiddenString;
				break;
			
			case "6688":	//6688
				$this->rnote = utf2local(strtoupper($this->rnote),"zh");
				$md5string=md5("tmbrid=" .$this->merid. "&tsummoney=" .$this->amount. "&tcontent1=" .$this->rnote. "&todrid=" .$this->orderid. "&tpwd=" .$this->ikey);

				$this->rname = $this->dolocal($this->rname,"zh");
				$this->remail = $this->dolocal($this->remail,"zh");
				$this->raddr = $this->dolocal($this->raddr,"zh");
				$this->rpost = $this->dolocal($this->rpost,"zh");
				$this->rtel = $this->dolocal($this->rtel,"zh");
				$this->rnote = $this->dolocal($this->rnote,"zh");
				$this->payname = $this->dolocal($this->payname,"zh");
				$tSupperComRegflag = 0;
				$tmp_url = $this->url."index.php?gOo=6688_reply.do&";

				$hiddenString = "<input type=hidden name=shopex_encoding value=\"gb2312\">";
				$hiddenString.= "<input type=hidden name=\"tmbrid\"  value=\"".$this->merid."\">";
				$hiddenString.= "<input type=hidden name=\"toname\" value=\"".$this->payment."\">";
				$hiddenString.= "<input type=hidden name=\"tsummoney\" value=\"".$this->amount."\">";
				$hiddenString.= "<input type=hidden name=\"trname\" value=\"".$this->rname."\">";
				$hiddenString.= "<input type=hidden name=\"traddress\" value=\"".$this->raddr."\">";
				$hiddenString.= "<input type=hidden name=\"todrid\" value=\"".$this->orderid."\">";
				$hiddenString.= "<input type=hidden name=\"temail\" value=\"".$this->remail."\">";
				$hiddenString.= "<input type=hidden name=\"trphone\" value=\"".$this->rtel."\">";
				$hiddenString.= "<input type=hidden name=\"trzipcode\" value=\"".$this->rpost."\">";
				$hiddenString.= "<input type=hidden name=\"tuserurl\" value=\"".$tmp_url."\">";
				$hiddenString.= "<input type=hidden name=\"tcontent1\" value=\"".$this->rnote."\">";
				$hiddenString.= "<input type=hidden name=\"tSupperComRegflag\" value=\"".$tSupperComRegflag."\">";
				$hiddenString.= "<input type=hidden name=\"mac\" value=\"".$md5string."\">";

				$this->action = "http://pay.6688.com/paygate/frame.asp";
				return $hiddenString;
				break;

			case "8848":	//8848
				$this->payname = utf2local(strtoupper($this->payname),"zh");
				$this->rnote = utf2local(strtoupper($this->rnote),"zh");
				$tmp_dir = dirname(__FILE__);
				include_once $tmp_dir."/nusoap.php";
				$tmp_url = $this->url."index.php?gOo=8848_reply.do&";
				$src_string = "USERNAME=".$this->payname."|SHOPID=".$this->merid."|ORDERID=".$this->orderid."|ORDERMONEY=".$this->amount."|RETURNFLAG=1|RETURNURL=".$tmp_url."|MEMO=".$this->rnote;
				
				$this->rname = $this->dolocal($this->rname,"zh");
				$this->remail = $this->dolocal($this->remail,"zh");
				$this->raddr = $this->dolocal($this->raddr,"zh");
				$this->rpost = $this->dolocal($this->rpost,"zh");
				$this->rtel = $this->dolocal($this->rtel,"zh");
				$this->rnote = $this->dolocal($this->rnote,"zh");
				$this->payname = $this->dolocal($this->payname,"zh");
				
				//生成参数
				$parameters=array(
					"sStrFromStore" => $src_string,
					"sForward" => "SEND",
				);
				//初始化soapclient
				$soapclient=new soapclient("http://estore.8848.com/PayGateway.asmx?WSDL");
				//处理
				$mac = $soapclient->call('PayGatewayEncrypt',$parameters,"http://estore.8848.com/","http://estore.8848.com/PayGatewayEncrypt");

				$hiddenString = "<input type=hidden name=shopex_encoding value=\"gb2312\">";
				$hiddenString .= "<input type=hidden name=\"SHOPID\"  value=\"".$this->merid."\">";
				$hiddenString.= "<input type=hidden name=\"ORDERID\" value=\"".$this->orderid."\">";
				$hiddenString.= "<input type=hidden name=\"USERNAME\" value=\"".$this->payname."\">";
				$hiddenString.= "<input type=hidden name=\"ORDERMONEY\" value=\"".$this->amount."\">";
				$hiddenString.= "<input type=hidden name=\"RETURNFLAG\" value=\"1\">";
				$hiddenString.= "<input type=hidden name=\"RETURNURL\" value=\"".$tmp_url."\">";
				$hiddenString.= "<input type=hidden name=\"MEMO\" value=\"".$this->rnote."\">";
				$hiddenString.= "<input type=hidden name=\"MAC\" value=\"".$mac."\">";

				$this->action = "http://estore.8848.com/paygateway/bankslist.aspx";
				return $hiddenString;
				break;

			case "EPAY":
				$this->amount *= 100 ;
				$tmp_url = $this->url."index.php?gOo=epay_reply.do&";
				$lnkStr = trim($this->merid).":".trim($this->secondkey).":".trim($this->orderid).":".trim($this->amount).":".trim($this->ikey);
				$strCountSignature = MD5($lnkStr);
				
				$hiddenString = "<input type=hidden name=shopex_encoding value=\"gb2312\">";
				$hiddenString.= "<input type=HIDDEN name=\"epayClientMerchID\" value=\"".$this->merid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"epayClientMerchPwd\" value=\"".$this->secondkey."\">";
				$hiddenString.= "<input type=HIDDEN name=\"epayClientOrderNum\" value=\"".$this->orderid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"epayClientOrderAmount\" value=\"".$this->amount."\">";
				$hiddenString.= "<input type=HIDDEN name=\"signature\" value=\"".$strCountSignature."\">";

				$this->action = "http://www.ipost.cn/pay/pay.aspx";
				return $hiddenString;
				break;			
			
			case "NPS":
				$this->rname = utf2local($this->rname, "zh");
				$this->raddr = utf2local($this->raddr, "zh");
				$this->rnote = utf2local($this->rnote, "zh");
				$this->lang = "1" ;
				$state = "0" ;
				$tmp_url = $this->url."index.php?gOo=nps_reply.do&";
				if ($this->rname == "") $this->rname = "NA";
				if ($this->raddr == "") $this->raddr = "NA";
				if ($this->rpost == "") $this->rpost = "NA";
				if ($this->rtel == "") $this->rtel = "NA";
				if ($this->remail == "") $this->remail = "NA";
				
				$m_info = $this->merid."|".$this->orderid."|".$this->amount."|".$this->currency."|".$tmp_url."|".$this->lang ;
				$s_info = $this->rname."|".$this->raddr."|".$this->rpost."|".$this->rtel."|".$this->remail ;
				$r_info = $this->rname."|".$this->raddr."|".$this->rpost."|".$this->rtel."|".$this->remail."|".$this->rnote."|".$state."|".date("Ymd",$this->ordertime) ;

				$OrderInfo = $m_info."|".$s_info."|".$r_info ;
				$OrderInfo = StrToHex($OrderInfo);
				$digest = strtoupper(md5($OrderInfo.$this->ikey));

				$hiddenString = "<input type=hidden name=shopex_encoding value=\"gb2312\" />";
				$hiddenString.= "<input type=hidden name=\"M_ID\" value=\"".$this->merid."\" />";
				$hiddenString.= "<input type=hidden name=\"digest\" value=\"".$digest."\" />";
				$hiddenString.= "<input type=hidden name=\"OrderMessage\" value=\"".$OrderInfo."\" />";

				$this->action = "https://payment.nps.cn/PHPReceiveMerchantAction.do";
				return $hiddenString;
				break;
			
			case "NPS_OUT":
				$this->rname = utf2local($this->rname, "zh");
				$this->raddr = utf2local($this->raddr, "zh");
				$this->rnote = utf2local($this->rnote, "zh");
				
				$this->lang = "1" ;
				$state = "0" ;
				$tmp_url = $this->url."index.php?gOo=npsout_reply.do&";
				if ($this->rname == "") $this->rname = "NA";
				if ($this->raddr == "") $this->raddr = "NA";
				if ($this->rpost == "") $this->rpost = "NA";
				if ($this->rtel == "") $this->rtel = "NA";
				if ($this->remail == "") $this->remail = "NA";
				
				$m_info = $this->merid."|".$this->orderid."|".$this->amount."|".$this->currency."|".$tmp_url."|".$this->lang ;
				$s_info = $this->rname."|".$this->raddr."|".$this->rpost."|".$this->rtel."|".$this->remail ;
				$r_info = $this->rname."|".$this->raddr."|".$this->rpost."|".$this->rtel."|".$this->remail."|".$this->rnote."|".$state."|".date("Ymd",$this->ordertime) ;

				$OrderInfo = $m_info."|".$s_info."|".$r_info ;
//				$OrderInfo = StrToHex($OrderInfo);
				$OrderInfo = stringToHex (des ($this->ikey, $OrderInfo, 1, 1, null));
				$digest = md5($OrderInfo.$this->ikey);
				
				$hiddenString = "<input type=hidden name=shopex_encoding value=\"gb2312\">";
				$hiddenString.= "<input type=HIDDEN name=\"M_ID\" value=\"".$this->merid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"procode\" value=\"php\" />";
				$hiddenString.= "<input type=HIDDEN name=\"md5info\" value=\"null\" />";
				$hiddenString.= "<input type=HIDDEN name=\"digest\" value=\"".$digest."\">";
				$hiddenString.= "<input type=HIDDEN name=\"OrderMessage\" value=\"".$OrderInfo."\">";

				$this->action = "https://payment.nps.cn/ReceiveI18NMerchantOutcardAction.do";
				return $hiddenString;
				break;

			//网银
			case "WANGJIN":
				if ($this->currency == "") $this->currency = "0";
				$tmp_url = $this->url."index.php?gOo=wangjin_reply.do&";
				$md5string=md5($this->amount.$this->currency.$this->orderid.$this->merid.$tmp_url.$this->ikey);
				$md5string = strtoupper($md5string);
				
				$this->rname = $this->dolocal($this->rname,"zh");
				$this->remail = $this->dolocal($this->remail,"zh");
				$this->raddr = $this->dolocal($this->raddr,"zh");
				$this->rpost = $this->dolocal($this->rpost,"zh");
				$this->rtel = $this->dolocal($this->rtel,"zh");
				$this->rnote = $this->dolocal($this->rnote,"zh");
				$this->payname = $this->dolocal($this->payname,"zh");

				$hiddenString = "<input type=hidden name=shopex_encoding value=\"gb2312\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_mid\" value=\"".$this->merid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_oid\" value=\"".$this->orderid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_amount\" value=\"".$this->amount."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_moneytype\" value=\"".$this->currency."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_url\" value=\"".$tmp_url."\">";
				$hiddenString.= "<input type=HIDDEN name=\"style\" value=\"0\" />";
				$hiddenString.= "<input type=HIDDEN name=\"v_md5info\" value=\"".$md5string."\" />";
				$hiddenString.= "<input type=HIDDEN name=\"remark1\" value=\"".$this->rnote."\" />";
				$hiddenString.= "<input type=HIDDEN name=\"remark2\" value=\"".$this->raddr."\" />";
				$hiddenString.= "<input type=HIDDEN name=\"v_rcvname\" value=\"".$this->rname."\" />";
				$hiddenString.= "<input type=HIDDEN name=\"v_ordername\" value=\"".$this->payname."\" />";
				$hiddenString.= "<input type=HIDDEN name=\"v_orderemail\" value=\"".$this->remail."\" />";

				$this->action = "https://pay3.chinabank.com.cn/PayGate";
				return $hiddenString;
				break;

			//网银外卡
			case "WANGJIN_OUT":
				if ($this->currency == "") $this->currency = "CNY";
				$this->lang = "EN";
				$tmp_url = $this->url."index.php?gOo=wangjinout_reply.do&";
				$orderdate = date("Ymd",$this->ordertime);

				$md5string=md5($this->amount.$this->currency.$this->orderid.$this->merid.$tmp_url.$this->ikey);
				$md5string = strtoupper($md5string);
				
				$this->rname = $this->dolocal($this->rname,"zh");
				$this->remail = $this->dolocal($this->remail,"zh");
				$this->raddr = $this->dolocal($this->raddr,"zh");
				$this->rpost = $this->dolocal($this->rpost,"zh");
				$this->rtel = $this->dolocal($this->rtel,"zh");
				$this->rnote = $this->dolocal($this->rnote,"zh");
				$this->payname = $this->dolocal($this->payname,"zh");

				$hiddenString = "<input type=hidden name=shopex_encoding value=\"gb2312\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_mid\" value=\"".$this->merid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_oid\" value=\"".$this->orderid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_amount\" value=\"".$this->amount."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_rcvname\" value=\"".$this->rname."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_rcvtel\" value=\"".$this->rtel."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_rcvpost\" value=\"".$this->rpost."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_rcvaddr\" value=\"".$this->raddr."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_ordername\" value=\"".$this->payname."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_ymd\" value=\"".$orderdate."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_orderemail\" value=\"".$this->remail."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_orderstatus\" value=\"0\">";
				$hiddenString.= "<input type=HIDDEN name=\"bankid\" value=\"3D\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_moneytype\" value=\"".$this->currency."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_language\" value=\"".$this->lang."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_url\" value=\"".$tmp_url."\">";
				$hiddenString.= "<input type=HIDDEN name=\"v_md5info\" value=\"".$md5string."\">";

				$this->action = "https://pay3.chinabank.com.cn/PayGate";
				return $hiddenString;
				break;

			case "YEEPAY":
				$tmp_url = $this->url."index.php?gOo=yeepay_reply.do&";
				
				$b = 64;
				if (strlen($this->ikey) > $b){
					$this->ikey = pack("H*",md5($this->ikey));
				}
				$this->ikey = str_pad($this->ikey, $b, chr(0x00));
				$ipad = str_pad('', $b, chr(0x36));
				$opad = str_pad('', $b, chr(0x5c));
				$k_ipad = $this->ikey ^ $ipad;
				$k_opad = $this->ikey ^ $opad;
				$data = "Buy".$this->merid.$this->orderid.$this->amount.$this->currency.$this->orderno.$this->remail.$tmp_url."0".$this->merid;
				$hmac = md5($k_opad . pack("H*",md5($k_ipad.$data)));
				
				$this->remail = $this->dolocal($this->remail,"zh");
				$this->raddr = $this->dolocal($this->raddr,"zh");
				$this->rnote = $this->dolocal($this->rnote,"zh");

				$hiddenString = "<input type=hidden name=shopex_encoding value=\"gb2312\">";
				$hiddenString.= "<input type=HIDDEN name=\"p0_Cmd\" value=\"Buy\">";
				$hiddenString.= "<input type=HIDDEN name=\"p1_MerId\" value=\"".$this->merid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"p2_Order\" value=\"".$this->orderid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"p3_Amt\" value=\"".$this->amount."\">";
				$hiddenString.= "<input type=HIDDEN name=\"p4_Cur\" value=\"".$this->currency."\">";
				$hiddenString.= "<input type=HIDDEN name=\"p5_Pid\" value=\"".$this->orderno."\">";
				$hiddenString.= "<input type=HIDDEN name=\"p6_Pcat\" value=\"".$this->remail."\">";
				$hiddenString.= "<input type=HIDDEN name=\"p7_Pdesc\" value=\"\">";
				$hiddenString.= "<input type=HIDDEN name=\"p8_Url\" value=\"".$tmp_url."\">";
				$hiddenString.= "<input type=HIDDEN name=\"p9_SAF\" value=\"0\">";
				$hiddenString.= "<input type=HIDDEN name=\"pa_MP\" value=\"".$this->merid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"pd_FrpId\" value=\"\">";
				$hiddenString.= "<input type=HIDDEN name=\"hmac\" value=\"".$hmac."\">";

				$this->action = "https://www.yeepay.com/app-merchant-proxy/node";
				return $hiddenString;
				break;

			case "CHINAPAY":
				$this->rname = $this->dolocal($this->rname,"zh");
				$this->remail = $this->dolocal($this->remail,"zh");
				$this->raddr = $this->dolocal($this->raddr,"zh");
				$this->rpost = $this->dolocal($this->rpost,"zh");
				$this->rtel = $this->dolocal($this->rtel,"zh");
				$this->rnote = $this->dolocal($this->rnote,"zh");
				$this->payname = $this->dolocal($this->payname,"zh");

				$mallConfig = newclass("mallConfig");
				$mallConfig->getInfo();

				$this->type = "0001";
//				$this->currency = "156";
				$this->orderid = "0000".substr($this->merid, -5)."0".$this->orderid;
				$this->amount = intString($this->amount * 100, 12);
				$tmp_date = date("Ymd",$this->ordertime);
				$tmp_url = $this->url."index.php?gOo=chinapay_reply.do&";
				
				if($mallConfig->own_sys == "windows")
				{
					$secre = new COM("ChinaPay.NetPayClient");
					$ChkValue = $secre->sign($this->merid,$this->orderid,$this->amount,$this->currency,$tmp_date,$this->type);
				}
				else
				{
					if(class_exists("Java"))
					{
						$private_key = new Java("chinapay.PrivateKey"); 
						$flag=$private_key->buildKey($this->merid,0,$PAYMENT_KEYROOT.$INC_SHOPID."/MerPrk.key");
						if ($flag==false) 
						{
							echo("build key error!");
							exit;
						}
						$t_chinapay =new Java("chinapay.SecureLink",$private_key);
						$ChkValue = $t_chinapay->signOrder($this->merid,$this->orderid,$this->amount,$this->currency,$tmp_date,$this->type);
					}
					else
					{
					   echo $PROG_TAGS["ptag_1048"]." You also can activate it by config PHP with JAVA support";
					   exit ;
					}
				}
				
				$hiddenString = "<input type=hidden name=shopex_encoding value=\"gb2312\">";
				$hiddenString.= "<input type=HIDDEN name=\"MerId\" value=\"".$this->merid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"OrdId\" value=\"".$this->orderid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"TransAmt\" value=\"".$this->amount."\">";
				$hiddenString.= "<input type=HIDDEN name=\"CuryId\" value=\"".$this->currency."\">";
				$hiddenString.= "<input type=HIDDEN name=\"TransDate\" value=\"".$tmp_date."\">";
				$hiddenString.= "<input type=HIDDEN name=\"TransType\" value=\"".$this->type."\">";
				$hiddenString.= "<input type=HIDDEN name=\"Version\" value=\"20010606\">";
				$hiddenString.= "<input type=HIDDEN name=\"RecvUrl\" value=\"".$tmp_url."\">";
				$hiddenString.= "<input type=HIDDEN name=\"ChkValue\" value=\"".$ChkValue."\">";

				$this->action = "http://payment.chinapay.com:8081/pay/TransGet";
				return $hiddenString;
				break;

			case "PAYPAL":
				if($this->currency == "CNY")
				{
					$this->rname = $this->dolocal($this->rname,"zh");
					$this->remail = $this->dolocal($this->remail,"zh");
					$this->raddr = $this->dolocal($this->raddr,"zh");
					$this->rpost = $this->dolocal($this->rpost,"zh");
					$this->rtel = $this->dolocal($this->rtel,"zh");
					$this->rnote = $this->dolocal($this->rnote,"zh");
					$this->payname = $this->dolocal($this->payname,"zh");
					$this->orderno = $this->dolocal($this->orderno,"zh");
				}
				$tmp_url = $this->url."index.php?gOo=paypal_reply.do&";
				$tmpr_url = $this->url."index.php?gOo=paypal_reply_return.do&";

				$hiddenString = "";
				if($this->currency == "CNY")
				{
					$hiddenString .= "<input type=hidden name=shopex_encoding value=\"gb2312\">";
				}
				$hiddenString.= "<input type=HIDDEN name=\"cmd\" value=\"_xclick\">";
				$hiddenString.= "<input type=HIDDEN name=\"business\" value=\"".$this->merid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"item_name\" value=\"".$this->orderno."\">";
				$hiddenString.= "<input type=HIDDEN name=\"item_number\" value=\"".$this->orderid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"amount\" value=\"".$this->amount."\">";
				$hiddenString.= "<input type=HIDDEN name=\"currency_code\" value=\"".$this->currency."\">";
				$hiddenString.= "<input type=HIDDEN name=\"return\" value=\"".$tmpr_url."\">";
				$hiddenString.= "<input type=HIDDEN name=\"notify_url\" value=\"".$tmp_url."\">";
				$hiddenString.= "<input type=HIDDEN name=\"lc\" value=\"US\">";
				
				$this->action = "https://www.paypal.com/cgi-bin/webscr";
				return $hiddenString;
				break;

			case "PAYPAL_EC":
				if($this->currency == "CNY")
				{
					$this->rname = $this->dolocal($this->rname,"zh");
					$this->remail = $this->dolocal($this->remail,"zh");
					$this->raddr = $this->dolocal($this->raddr,"zh");
					$this->rpost = $this->dolocal($this->rpost,"zh");
					$this->rtel = $this->dolocal($this->rtel,"zh");
					$this->rnote = $this->dolocal($this->rnote,"zh");
					$this->payname = $this->dolocal($this->payname,"zh");
					$this->orderno = $this->dolocal($this->orderno,"zh");
				}
				$tmp_url = $this->url."index.php?gOo=paypal_reply.do&";
				$tmpr_url = $this->url."index.php?gOo=paypal_reply_return.do&";

				$hiddenString = "";
				if($this->currency == "CNY")
				{
					$hiddenString .= "<input type=hidden name=shopex_encoding value=\"gb2312\">";
				}
				$hiddenString.= "<input type=HIDDEN name=\"cmd\" value=\"_xclick\">";
				$hiddenString.= "<input type=HIDDEN name=\"business\" value=\"".$this->merid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"item_name\" value=\"".$this->orderno."\">";
				$hiddenString.= "<input type=HIDDEN name=\"item_number\" value=\"".$this->orderid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"amount\" value=\"".$this->amount."\">";
				$hiddenString.= "<input type=HIDDEN name=\"currency_code\" value=\"".$this->currency."\">";
				$hiddenString.= "<input type=HIDDEN name=\"return\" value=\"".$tmpr_url."\">";
				$hiddenString.= "<input type=HIDDEN name=\"notify_url\" value=\"".$tmp_url."\">";

				$this->action = "https://www.paypal.com/cgi-bin/webscr";
				return $hiddenString;
				break;
			
			case "PAYPAL_CN":
				if($this->currency == "CNY")
				{
					$this->rname = $this->dolocal($this->rname,"zh");
					$this->remail = $this->dolocal($this->remail,"zh");
					$this->raddr = $this->dolocal($this->raddr,"zh");
					$this->rpost = $this->dolocal($this->rpost,"zh");
					$this->rtel = $this->dolocal($this->rtel,"zh");
					$this->rnote = $this->dolocal($this->rnote,"zh");
					$this->payname = $this->dolocal($this->payname,"zh");
					$this->orderno = $this->dolocal($this->orderno,"zh");
				}
				$tmp_url = $this->url."index.php?gOo=paypal_noipn_reply.do&";

				$hiddenString = "";
				if($this->currency == "CNY")
				{
					$hiddenString .= "<input type=hidden name=shopex_encoding value=\"gb2312\">";
				}
				$hiddenString.= "<input type=HIDDEN name=\"cmd\" value=\"_xclick\">";
				$hiddenString.= "<input type=HIDDEN name=\"business\" value=\"".$this->merid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"item_name\" value=\"".$this->orderno."\">";
				$hiddenString.= "<input type=HIDDEN name=\"item_number\" value=\"".$this->orderid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"amount\" value=\"".$this->amount."\">";
				$hiddenString.= "<input type=HIDDEN name=\"currency_code\" value=\"".$this->currency."\">";
				$hiddenString.= "<input type=HIDDEN name=\"bn\" value=\"shopex\">";
				$hiddenString.= "<input type=HIDDEN name=\"return\" value=\"".$tmp_url."\">";

				$this->action = "https://www.paypal.com/cgi-bin/webscr";
				return $hiddenString;
				break;
			
			case "HOMEWAY":
				$this->currency = "2002";
				$mer_key="asdfghjk12345678";
				$this->amount *= 100;
				$info = $this->merid.$this->amount.$this->orderid.date("Ymd",$this->ordertime).$this->currency.$this->ikey;
				$msign = md5($info);
				
				$hiddenString = "<input type=hidden name=shopex_encoding value=\"gb2312\">";
				$hiddenString.= "<input type=HIDDEN name=\"MerchID\" value=\"".$this->merid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"OrderNum\" value=\"".$this->orderid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"Amount\" value=\"".$this->amount."\">";
				$hiddenString.= "<input type=HIDDEN name=\"TransType\" value=\"".$this->currency."\">";
				$hiddenString.= "<input type=HIDDEN name=\"TransDate\" value=\"".date("Ymd",$this->ordertime)."\">";
				$hiddenString.= "<input type=HIDDEN name=\"Signature\" value=\"".$msign."\">";
				
				$this->action = "http://payment.homeway.com.cn/pay/pay_new.php3";
				return $hiddenString;
				break;
				
			case "NOCHEK":
				$tmp_url = $this->url."index.php?gOo=nochek_reply.do&";
				
				$hiddenString = "";
				$hiddenString.= "<input type=HIDDEN name=\"email\" value=\"".$this->merid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"ordernumber\" value=\"".$this->orderid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"amount\" value=\"".$this->amount."\">";
				$hiddenString.= "<input type=HIDDEN name=\"responderurl\" value=\"".$tmp_url."\">";
				$hiddenString.= "<input type=HIDDEN name=\"description\" value=\"".$this->rnote."\">";
				
				$this->action = "https://www.nochex.com/nochex.dll/checkout";
				return $hiddenString;
				break;
			
			case "99BILL":
//				$tmp_url = $this->url."index.php?gOo=99bill_reply.do&";
				$tmp_url = $this->url."99billdo.php";
				
				$key = $this->ikey;  //私钥值，商户可上99BILL快钱后台自行设定
				$text="merchant_id=".$this->merid."&orderid=".$this->orderid."&amount=".$this->amount."&merchant_url=".$tmp_url."&merchant_key=".$key;
				$mac = strtoupper(md5($text)); //对参数串进行私钥加密取得值
				
				$hiddenString = "<input type=hidden name=shopex_encoding value=\"utf-8\">";
				$hiddenString.= "<input type=HIDDEN name=\"merchant_id\" value=\"".$this->merid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"orderid\" value=\"".$this->orderid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"amount\" value=\"".$this->amount."\">";
				$hiddenString.= "<input type=HIDDEN name=\"merchant_url\" value=\"".$tmp_url."\">";
				$hiddenString.= "<input type=HIDDEN name=\"merchant_param\" value=\"99bill_reply.do\">";
				$hiddenString.= "<input type=HIDDEN name=\"pname\" value=\"".$this->rname."\">";
				$hiddenString.= "<input type=HIDDEN name=\"currency\" value=\"".$this->currency."\">";
				$hiddenString.= "<input type=HIDDEN name=\"isSupportDES\" value=\"2\">";
				$hiddenString.= "<input type=HIDDEN name=\"mac\" value=\"".$mac."\">";
				$hiddenString.= "<input type=HIDDEN name=\"pid\" value=\"879905060102977462\">";
				
				$this->action = "https://www.99bill.com/webapp/receiveMerchantInfoAction.do";
				return $hiddenString;
				break;
			
			case "PAYDOLLAR":
				$this->lang = "E";
				$tmp_url = $this->url."index.php?gOo=paydollar_reply.do&";

				$this->rnote = $this->dolocal($this->rnote,"zh");
				
				$key = $this->ikey;  //私钥值，商户可上99BILL快钱后台自行设定
				$text="merchant_id=".$this->merid."&orderid=".$this->orderid."&amount=".$this->amount."&merchant_url=".$tmp_url."&merchant_key=".$key;
				$mac = strtoupper(md5($text)); //对参数串进行私钥加密取得值
				
				$hiddenString = "";
				$hiddenString.= "<input type=HIDDEN name=\"merchantId\" value=\"".$this->merid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"orderRef\" value=\"".$this->orderid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"amount\" value=\"".$this->amount."\">";
				$hiddenString.= "<input type=HIDDEN name=\"currCode\" value=\"".$this->currency."\">";
				$hiddenString.= "<input type=HIDDEN name=\"lang\" value=\"".$this->lang."\">";
				$hiddenString.= "<input type=HIDDEN name=\"successUrl\" value=\"".$tmp_url."\">";
				$hiddenString.= "<input type=HIDDEN name=\"failUrl\" value=\"".$tmp_url."\">";
				$hiddenString.= "<input type=HIDDEN name=\"cancelUrl\" value=\"".$tmp_url."\">";
				$hiddenString.= "<input type=HIDDEN name=\"payType\" value=\"N\">";
				$hiddenString.= "<input type=HIDDEN name=\"payMethod\" value=\"ALL\">";
				$hiddenString.= "<input type=HIDDEN name=\"remark\" value=\"".$this->rnote."\">";
				
				$this->action = "https://www.paydollar.com/b2c2/eng/payment/payForm.jsp";
				return $hiddenString;
				break;

			case "ALIPAY": //支付宝

				$ret_url = $this->url."index.php?gOo=alipaynew_reply.do";
				$server_url = $this->url."alipaynew_autoredirectserver.php";

				$key = $this->ikey;  //私钥值，
				$this->amount = number_format($this->amount,2,".","");
				$subject = $this->shopname." No:".$this->orderno;
				$subject = str_replace("'",'`',trim($subject));
				$subject = str_replace('"','`',$subject);
				$this->orderdetail = str_replace("'",'`',trim($this->orderdetail));
				$this->orderdetail = str_replace('"','`',$this->orderdetail);
				$ali_arr = array();
				switch($this->transport)
				{
					case 1:		//免运费
						$ali_arr['service'] = 'trade_create_by_buyer';
						$ali_arr['logistics_type'] = "POST";
						$ali_arr['logistics_fee'] = $this->ordinary_fee+$this->express_fee;
						$ali_arr['logistics_payment'] = "BUYER_PAY";
						break;
					case 2:		//免支付费率
						$ali_arr['service'] = 'trade_create_by_buyer';
						$ali_arr['logistics_type'] = "EXPRESS";
						$ali_arr['logistics_fee'] = $this->ordinary_fee+$this->express_fee;
						$ali_arr['logistics_payment'] = "BUYER_PAY";
						break;
					case 3:		//虚拟商品交易
						if(defined("ALIPAY_DIRECT"))
							$ali_arr['service'] = 'create_direct_pay_by_user';
						else
							$ali_arr['service'] = 'create_digital_goods_trade_p';
						break;
				}
				$ali_arr['agent'] = '2088002003028751';
				$ali_arr['payment_type'] = 1;
				$ali_arr['partner'] = $this->merid;
				$ali_arr['return_url'] = $ret_url;
				$ali_arr['notify_url'] = $server_url;
				$ali_arr['subject'] = $subject;
				$ali_arr['body'] = ($this->orderdetail=='' ? 'Order Information' : $this->orderdetail);
				$ali_arr['show_url'] = $this->url."index.php";
				$ali_arr['out_trade_no'] = $this->orderno.$this->orderid;
				$ali_arr['price'] = $this->amount;
				$ali_arr['quantity'] = 1;
				
				$ali_arr['seller_id'] = $this->merid;
				$ali_arr['buyer_msg'] = $this->rnote;
				$ali_arr['_input_charset'] = "utf-8";
				ksort($ali_arr);
				reset($ali_arr);
				
				$mac= "";
				while(list($k,$v)=each($ali_arr))
				{
						$mac .= "&{$k}={$v}";
						if($k!='_input_charset')
							$hiddenString.= "<input type=hidden name=\"{$k}\" value=\"{$v}\">"; //创建交易
				}
				$mac = substr($mac,1);
				$hiddenString.= "<input type=hidden name=\"sign\" value=\"".md5($mac.$this->ikey)."\">";  //验证信息
				$hiddenString.= "<input type=hidden name=\"sign_type\" value=\"MD5\">";  //验证信息
				
				$this->action = "https://www.alipay.com/cooperate/gateway.do?_input_charset=utf-8";

				return $hiddenString;
				break;

			case "XPAY":
				$tmp_url = $this->url."index.php?gOo=xpay_reply.do&";
				$this->lang = "gb2312";
				$card = "bank";
				//$scard = "bank,unicom,xpay,ebilling,ibank";
				$this->rname = $this->dolocal($this->rname,"zh");
				$this->rnote = $this->dolocal($this->rnote,"zh");
				$this->shopname = $this->dolocal($this->shopname,"zh");
				$scard = "";
				$actioncode = "sell";
				$actionParameter = "";
				$ver = "2.0";
				$msign = md5($this->ikey.":".$this->amount.",".$this->merid.$this->orderid.",".$this->merid.",".$card.",".$scard.",".$actioncode.",".$actionParameter.",".$ver);// 
				$msign = strtolower($msign);
				
				$hiddenString = "<input type=hidden name=shopex_encoding value=\"gb2312\">";
				$hiddenString.= "<input type=HIDDEN name=\"prc\" value=\"".$this->amount."\">";
				$hiddenString.= "<input type=HIDDEN name=\"bid\" value=\"".$this->merid.$this->orderid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"tid\" value=\"".$this->merid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"card\" value=\"".$card."\">";
				$hiddenString.= "<input type=HIDDEN name=\"scard\" value=\"".$scard."\">";
				$hiddenString.= "<input type=HIDDEN name=\"actionCode\" value=\"".$actioncode."\">";
				$hiddenString.= "<input type=HIDDEN name=\"actionparameter\" value=\"\">";
				$hiddenString.= "<input type=HIDDEN name=\"ver\" value=\"".$ver."\">";
				$hiddenString.= "<input type=HIDDEN name=\"pdt\" value=\"".$this->orderno."\">";
				$hiddenString.= "<input type=HIDDEN name=\"username\" value=\"".$this->rname."\">";
				$hiddenString.= "<input type=HIDDEN name=\"lang\" value=\"".$this->lang."\">";
				$hiddenString.= "<input type=HIDDEN name=\"url\" value=\"".$tmp_url."\">";
				$hiddenString.= "<input type=HIDDEN name=\"remark1\" value=\"".$this->rnote."\">";
				$hiddenString.= "<input type=HIDDEN name=\"md\" value=\"".$msign."\">";
				$hiddenString.= "<input type=HIDDEN name=\"sitename\" value=\"".$this->shopname."\">";
				$hiddenString.= "<input type=HIDDEN name=\"siteurl\" value=\"".$this->url."\">";
				
				$this->action = "http://pay.xpay.cn/pay.aspx";
				return $hiddenString;
				break;
				
				
			case "NPAY":
				$tmp_url = $this->url."index.php?gOo=npay_reply.do&";
				$this->lang = "gb2312";

				$md5string=md5($this->merid . $this->orderid . $this->amount . $this->remail . $this->buyer_mobile . $this->ikey);
				$md5string = strtoupper($md5string);
				
				$hiddenString ="<input type=\"hidden\" name=shopex_encoding value=\"gb2312\">";
				$hiddenString.="<input type=\"hidden\" name=\"v_mid\" value=\"".$this->merid."\">"; 
				$hiddenString.="<input type=\"hidden\" name=\"v_oid\" value=\"".$this->orderid."\">";
				$hiddenString.="<input type=\"hidden\" name=\"v_amount\" value=\"".$this->amount."\">"; 
				$hiddenString.="<input type=\"hidden\" name=\"v_email\" value=\"".$this->remail."\">"; 
				$hiddenString.="<input type=\"hidden\" name=\"v_mobile\" value=\"".$this->buyer_mobile."\">"; 
				$hiddenString.="<input type=\"hidden\" name=\"v_md5\" value=\"".$md5string."\">"; 
				$hiddenString.="<input type=\"hidden\" name=\"v_url\" value=\"".$tmp_url."\">"; 
	
				$this->action = "http://www.npay.com.cn/4.0/bank.shtml";
				return $hiddenString;
				break;
				
			case "EGOLD":
				$tmp_url = $this->url."index.php?gOo=egold_reply.do&";
				$tmp2_url = $this->url."index.php";//EGOLD支付成功跟失败在前台都会有返回，而且必须指定地址，如果支付不成功，就让他返回网店首页好了。
				
				$this->lang = "gb2312";
	
		
				$hiddenString = "<input type=hidden name=shopex_encoding value=\"gb2312\">";

				$hiddenString.= "<input type=HIDDEN name=\"PAYMENT_METAL_ID\" value=\"1\">";
				$hiddenString.= "<input type=HIDDEN name=\"PAYMENT_ID\" value=\"".$this->orderid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"PAYEE_ACCOUNT\" value=\"".$this->merid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"PAYEE_NAME\" value=\"".$_SERVER["HTTP_HOST"]."\">";
				$hiddenString.= "<input type=HIDDEN name=\"PAYMENT_AMOUNT\" value=\"".$this->amount."\">";
				$hiddenString.= "<input type=HIDDEN name=\"PAYMENT_UNITS\" value=\"1\">";
				$hiddenString.= "<input type=HIDDEN name=\"PAYMENT_URL\" value=\"".$tmp_url."\">";
				$hiddenString.= "<input type=HIDDEN name=\"PAYMENT_URL_METHOD\" value=\"POST\">";
				$hiddenString.= "<input type=HIDDEN name=\"NOPAYMENT_URL\" value=\"".$tmp2_url."\">";
				$hiddenString.= "<input type=HIDDEN name=\"NOPAYMENT_URL_METHOD\" value=\"POST\">";
				$hiddenString.= "<input type=HIDDEN name=\"BAGGAGE_FIELDS\" value=\"\">";
				$hiddenString.= "<input type=HIDDEN name=\"PRODUCTNAME\" value=\"\">";

				$this->action = "https://www.e-gold.com/sci_asp/payments.asp";
				return $hiddenString;
				break;						
				
			case "IEPAY":
				$tmp_url = $this->url."index.php?gOo=iepay_reply.php&";
				$this->lang = "gb2312";

				$msign = md5($this->ikey.":".$this->amount.",".$this->merid.$this->orderid.",".$this->merid.",".$card.",".$scard.",".$actioncode.",".$actionParameter.",".$ver);// 
				$msign = strtolower($msign);

					//storeid:授權商店代碼 相当于商户号
					//password:密碼 相当于商户密钥
					//orderid:訂單編號(12碼以內)
					//account:金額
					//remark:訂單註解
					//storename:顯示的商店名稱
			
				$hiddenString = "<input type=hidden name=shopex_encoding value=\"gb2312\">";
				$hiddenString.= "<input type=HIDDEN name=\"storeid\" value=\"".$this->merid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"password\" value=\"".$this->ikey."\">";
				$hiddenString.= "<input type=HIDDEN name=\"account\" value=\"".$this->amount."\">";
				$hiddenString.= "<input type=HIDDEN name=\"remark\" value=\"".$this->rnote."\">";
				$hiddenString.= "<input type=HIDDEN name=\"orderid\" value=\"".$this->orderid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"storename\" value=\"".$this->shopname."\">";				
				
				$this->action = "https://www.epay.cc/creditcard/cardfinance.php";
				return $hiddenString;
				break;

			case "ECS":		//ECS Payment Gateway
//				$this->currency = "LVL";
				//$tmp_url = $this->url."index.php?gOo=ecs_reply.php");
				$tmp_url = $_SERVER['HTTP_HOST'];
				
				$key = $this->ikey;  //私钥值，
				$mac="goodsTitle".$this->orderid."goodsBid".$this->amount."ordinaryFee0.00expressFee0.00sellerEmail".$this->merid."no".$this->orderid."memo".$key;
				$mac = md5($mac); //对参数串进行私钥加密取得值
				$hiddenString = "";
				$hiddenString.= "<input type=HIDDEN name=\"Merchant\" value=\"".$this->merid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"Account\" value=\"".$this->ikey."\">";
				$hiddenString.= "<input type=HIDDEN name=\"Service\" value=\"".$this->secondkey."\">";
				$hiddenString.= "<input type=HIDDEN name=\"OrderID\" value=\"".$this->orderno."\">";
				$hiddenString.= "<input type=HIDDEN name=\"ReferenceID\" value=\"".$this->orderid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"Amount\" value=\"".$this->amount."\">";
				$hiddenString.= "<input type=HIDDEN name=\"Currency\" value=\"".$this->currency."\">";
				$hiddenString.= "<input type=HIDDEN name=\"Description\" value=\"".$this->rnote."\">";
				$hiddenString.= "<input type=HIDDEN name=\"Customer\" value=\"".$this->rname."\">";
				$hiddenString.= "<input type=HIDDEN name=\"Email\" value=\"".$this->remail."\">";
				$hiddenString.= "<input type=HIDDEN name=\"IP\" value=\"".$_SERVER["REMOTE_ADDR"]."\">";
				$hiddenString.= "<input type=HIDDEN name=\"Site\" value=\"".$tmp_url."\">";
				
				$this->action = "https://secure.cps.lv/scripts/rprocess.dll?authorize";
				return $hiddenString;
				break;

			case "TWV":
				$tmp_url = $this->url."index.php?gOo=twv_reply.do&";
				$this->lang = "tchinese";
				$this->amount = Floor($this->amount);
				$verify = md5($this->ikey."|".$this->merid."|".$this->orderid."|".$this->amount."|".$this->secondkey);
				$this->rname = $this->dolocal($this->rname,"big5");
				$this->raddr = $this->dolocal($this->raddr,"big5");

				$hiddenString = "<input type=hidden name=shopex_encoding value=\"big5\">";
				$hiddenString.= "<input type=HIDDEN name=\"mid\" value=\"".$this->merid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"ordernum\" value=\"".$this->orderid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"txid\" value=\"".$this->orderid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"iid\" value=\"0\">";
				$hiddenString.= "<input type=HIDDEN name=\"amount\" value=\"".$this->amount."\">";
				$hiddenString.= "<input type=HIDDEN name=\"cname\" value=\"".$this->rname."\">";
				$hiddenString.= "<input type=HIDDEN name=\"caddress\" value=\"".$this->raddr."\">";
				$hiddenString.= "<input type=HIDDEN name=\"language\" value=\"".$this->lang."\">";
				$hiddenString.= "<input type=HIDDEN name=\"version\" value=\"1.0\">";
				$hiddenString.= "<input type=HIDDEN name=\"return_url\" value=\"".$tmp_url."\">";
				$hiddenString.= "<input type=HIDDEN name=\"verify\" value=\"".$verify."\">";
				
				$this->action = "https://www.twv.com.tw/openpay/pay.php";
				return $hiddenString;
				break;
			
			case "GWPAY":
				$tmp_url = $this->url."index.php?gOo=gwpay_reply.do&";
				if ($FRONTEND_LANG == "en")
					$posturl = "https://gwpay.com.tw/form_Sc_to5e.php";
				else
					$posturl = "https://gwpay.com.tw/form_Sc_to5.php";
				$this->amount = Floor($this->amount);

				$hiddenString = "<input type=hidden name=shopex_encoding value=\"big5\">";
				$hiddenString.= "<input type=HIDDEN name=\"act\" value=\"auth\">";
				$hiddenString.= "<input type=HIDDEN name=\"client\" value=\"".$this->merid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"od_sob\" value=\"".$this->orderid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"amount\" value=\"".$this->amount."\">";
				$hiddenString.= "<input type=HIDDEN name=\"email\" value=\"".$this->remail."\">";
				$hiddenString.= "<input type=HIDDEN name=\"roturl\" value=\"".$tmp_url."\">";
				
				$this->action = $posturl;
				return $hiddenString;
				break;

			case "ENETS":         //https://www.enetspayments.com.sg/
				$tmp_url = $this->url."index.php?gOo=enet_reply.do&";
				$this->amount = $this->amount;

				$hiddenString.= "<input type=HIDDEN name=\"mid\" value=\"".$this->merid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"amount\" value=\"".$this->amount."\">";
				$hiddenString.= "<input type=HIDDEN name=\"txnRef\" value=\"".$this->orderid."\">";				
				$this->action = "https://www.enetspayments.com.sg/masterMerchant/collectionPage.jsp";
				return $hiddenString;
				break;

			case "CNCARD":          //云网
				$this->currency = "0";
				$tmp_url = $this->url."cncard_autoredirect.php";
				$orderdate = date("Ymd",$this->ordertime);
				$md5string = md5($this->merid.$this->orderid.$this->amount.$orderdate."0"."1".$tmp_url."0"."0".$this->ikey);
			
				$this->rname = $this->dolocal($this->rname,"zh");
				$this->remail = $this->dolocal($this->remail,"zh");
				$this->raddr = $this->dolocal($this->raddr,"zh");
				$this->rpost = $this->dolocal($this->rpost,"zh");
				$this->rtel = $this->dolocal($this->rtel,"zh");
				$this->rnote = $this->dolocal($this->rnote,"zh");
				$this->payname = $this->dolocal($this->payname,"zh");

				$hiddenString = "<input type=hidden name=shopex_encoding value=\"gb2312\">";
				$hiddenString.= "<input type=HIDDEN name=\"c_mid\" value=\"".$this->merid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"c_order\" value=\"".$this->orderid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"c_email\" value=\"".$this->remail."\">";
				$hiddenString.= "<input type=HIDDEN name=\"c_orderamount\" value=\"".$this->amount."\">";
				$hiddenString.= "<input type=HIDDEN name=\"c_ymd\" value=\"".$orderdate."\">";
				$hiddenString.= "<input type=HIDDEN name=\"c_moneytype\" value=\"".$this->currency."\">";
				$hiddenString.= "<input type=HIDDEN name=\"c_retflag\" value=\"1\">";
				$hiddenString.= "<input type=HIDDEN name=\"c_returl\" value=\"".$tmp_url."\">";
				$hiddenString.= "<input type=HIDDEN name=\"c_language\" value=\"0\">";
				$hiddenString.= "<input type=HIDDEN name=\"notifytype\" value=\"0\">";
				$hiddenString.= "<input type=HIDDEN name=\"c_signstr\" value=\"".$md5string."\">";

				$this->action = "https://www.cncard.net/purchase/getorder.asp";
				return $hiddenString;
				break;
			case "TENPAY":          //腾讯

				$this->currency = "1";
				$tmp_url = $this->url."tenpay_autoredirectserver.php";
				$orderdate = date("Ymd",$this->ordertime);	
				$this->amount = ceil($this->amount*100);
			    $v_orderid = $this->merid.$orderdate."0000".$this->orderid;
				$md5string=strtoupper(md5("cmdno=1&date=".$orderdate."&bargainor_id=".$this->merid."&transaction_id=".$v_orderid."&sp_billno=".$this->orderno."&total_fee=".$this->amount."&fee_type=1&return_url=".$tmp_url."&attach=1&key=".$this->ikey));

				$this->rname = $this->dolocal($this->rname,"zh");
				$this->remail = $this->dolocal($this->remail,"zh");
				$this->raddr = $this->dolocal($this->raddr,"zh");
				$this->rpost = $this->dolocal($this->rpost,"zh");
				$this->rtel = $this->dolocal($this->rtel,"zh");
				$this->rnote = $this->dolocal($this->rnote,"zh");
				$this->payname = $this->dolocal($this->payname,"zh");
				$subject = $this->dolocal($this->shopname." No:".$this->orderno,"zh");

				$hiddenString = "<input type=hidden name=shopex_encoding value=\"gb2312\">";
				$hiddenString.= "<input type=HIDDEN name=\"cmdno\" value=\"1\">";
				$hiddenString.= "<input type=HIDDEN name=\"date\" value=\"".$orderdate."\">";
				$hiddenString.= "<input type=HIDDEN name=\"bank_type\" value=\"0\">";
				$hiddenString.= "<input type=HIDDEN name=\"desc\" value=\"".$subject."\">";
				$hiddenString.= "<input type=HIDDEN name=\"purchaser_id\" value=\"\">";
				$hiddenString.= "<input type=HIDDEN name=\"bargainor_id\" value=\"".$this->merid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"transaction_id\" value=\"".$v_orderid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"sp_billno\" value=\"".$this->orderno."\">";
				$hiddenString.= "<input type=HIDDEN name=\"total_fee\" value=\"".$this->amount."\">";
				$hiddenString.= "<input type=HIDDEN name=\"fee_type\" value=\"".$this->currency."\">";
				$hiddenString.= "<input type=HIDDEN name=\"return_url\" value=\"".$tmp_url."\">";
				$hiddenString.= "<input type=HIDDEN name=\"attach\" value=\"1\">";
				$hiddenString.= "<input type=HIDDEN name=\"sign\" value=\"".$md5string."\">";

				$this->action = "http://portal.tenpay.com/cfbiportal/cgi-bin/cfbiin.cgi";
				return $hiddenString;
				break;
			
			case "PAY100":          //百付通
//				$this->currency = 1001;
				$tmp_url = $this->url."index.php?gOo=pay100_reply.do&";
				$orderdate = date("Y-m-d H:i:s",$this->ordertime);
				$strRnote = utf2local(strtoupper($this->rnote),"zh");
				$StrContent = "1001".$this->merid.$this->orderid.$this->amount.$this->currency.$orderdate
							.$this->orderno.$strRnote."1"."1"."1".$tmp_url.$tmp_url.$this->ikey;
				$this->rnote = $this->dolocal($this->rnote,"zh");

				$hiddenString = "<input type=hidden name=shopex_encoding value=\"gb2312\">";
				$hiddenString.= "<input type=HIDDEN name=\"OrderType\" value=\"1001\">";
				$hiddenString.= "<input type=HIDDEN name=\"CoagentID\" value=\"\">";
				$hiddenString.= "<input type=HIDDEN name=\"InceptUserName\" value=\"".$this->merid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"OrderNumber\" value=\"".$this->orderid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"Amount\" value=\"".$this->amount."\">";
				$hiddenString.= "<input type=HIDDEN name=\"MoneyCode\" value=\"".$this->currency."\">";
				$hiddenString.= "<input type=HIDDEN name=\"TransDateTime\" value=\"".$orderdate."\">";
				$hiddenString.= "<input type=HIDDEN name=\"Title\" value=\"".$this->orderno."\">";
				$hiddenString.= "<input type=HIDDEN name=\"Content\" value=\"".$this->rnote."\">";
				$hiddenString.= "<input type=HIDDEN name=\"CompleteReturn\" value=\"1\">";
				$hiddenString.= "<input type=HIDDEN name=\"FailReturn\" value=\"1\">";
				$hiddenString.= "<input type=HIDDEN name=\"ReturnValidate\" value=\"1\">";
				$hiddenString.= "<input type=HIDDEN name=\"ReturnUrl\" value=\"".$tmp_url."\">";
				$hiddenString.= "<input type=HIDDEN name=\"RedirectUrl\" value=\"".$tmp_url."\">";
				$hiddenString.= "<input type=HIDDEN name=\"SignCode\" value=\"".strtoupper(md5($StrContent))."\">";
				
				$this->action = "https://www.pay100.com/interface/Professional/paypre.aspx";
//				$this->action = "http://tech.pay100.com/interface/Professional/paypre.aspx";
				return $hiddenString;
				break;
			
			case "MONEYBOOKERS":          //MONEYBOOKERS
				$tmp_url = $this->url."index.php?gOo=moneybookers_reply.do&";
				$orderdate = date("Y-m-d H:i:s",$this->ordertime);
				$strRnote = utf2local(strtoupper($this->rnote),"zh");
				$StrContent = md5($this->merid.$this->orderid.$this->ikey.$this->amount.$this->currency."1");
				$this->rnote = $this->dolocal($this->rnote,"zh");

				$hiddenString.= "<input type=HIDDEN name=\"pay_to_email\" value=\"".$this->merid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"transaction_id\" value=\"".$this->orderid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"amount\" value=\"".$this->amount."\">";
				$hiddenString.= "<input type=HIDDEN name=\"currency\" value=\"".$this->currency."\">";
				$hiddenString.= "<input type=HIDDEN name=\"pay_from_email\" value=\"".$this->remail."\">";
				$hiddenString.= "<input type=HIDDEN name=\"language\" value=\"en\">";
				$hiddenString.= "<input type=HIDDEN name=\"detail1_description\" value=\"".$this->orderno."\">";
				$hiddenString.= "<input type=HIDDEN name=\"detail1_text\" value=\"".$this->orderno."\">";
				$hiddenString.= "<input type=HIDDEN name=\"address\" value=\"".$this->raddr."\">";
				$hiddenString.= "<input type=HIDDEN name=\"postal_code\" value=\"".$this->rpost."\">";
				$hiddenString.= "<input type=HIDDEN name=\"firstname\" value=\"".$this->rname."\">";
				$hiddenString.= "<input type=HIDDEN name=\"confirmation_note\" value=\"".$this->rnote."\">";
				$hiddenString.= "<input type=HIDDEN name=\"status_url\" value=\"".$tmp_url."\">";
				$hiddenString.= "<input type=HIDDEN name=\"return_url\" value=\"".$tmp_url."\">";
				$hiddenString.= "<input type=HIDDEN name=\"cancel_url\" value=\"".$tmp_url."\">";
				
				$this->action = "https://www.moneybookers.com/app/payment.pl";
				return $hiddenString;
				break;
			
			case "2CHECKOUT":          //2CHECKOUT
				//$tmp_url = $this->url."index.php?gOo=2checkout_reply.do&";
				$tmp_url = $this->url."index.php";
				$orderdate = date("Y-m-d H:i:s",$this->ordertime);
				$strRnote = utf2local(strtoupper($this->rnote),"zh");
				$StrContent = md5($this->merid.$this->orderid.$this->ikey.$this->amount.$this->currency."1");
				$this->rnote = $this->dolocal($this->rnote,"zh");
				$this->raddr = $this->dolocal($this->raddr,"zh");

				$hiddenString.= "<input type=HIDDEN name=\"sid\" value=\"".$this->merid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"cart_order_id\" value=\"".$this->orderid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"quantity\" value=\"1\">";
				$hiddenString.= "<input type=HIDDEN name=\"invoice_num\" value=\"".$this->orderno."\">";
				$hiddenString.= "<input type=HIDDEN name=\"total\" value=\"".$this->amount."\">";
				$hiddenString.= "<input type=HIDDEN name=\"card_holder_name\" value=\"".$this->remail."\">";
//				$hiddenString.= "<input type=HIDDEN name=\"c_prod\" value=\"".$this->orderid."\">";
//				$hiddenString.= "<input type=HIDDEN name=\"id_type\" value=\"2\">";
				$hiddenString.= "<input type=HIDDEN name=\"lang\" value=\"en\">";
				$hiddenString.= "<input type=HIDDEN name=\"email\" value=\"".$this->remail."\">";
				$hiddenString.= "<input type=HIDDEN name=\"return_url\" value=\"".$tmp_url."\">";
				
				$this->action = "https://www.2checkout.com/2co/buyer/purchase";
				return $hiddenString;
				break;
			
			case "MOBILE88":          //MOBILE88
				$tmp_url = $this->url."index.php?gOo=mobile88_reply.do&";
				$ordAmount = number_format($this->amount, 2, ".", "");
				$tmpOrdAmount = str_replace(".", "", $ordAmount);
				include_once(dirname(__FILE__)."/class.Shafunction.php");
				$sha1 = new Shafunction();
				
				$Signature = base64_encode($sha1->sha1($this->ikey.$this->merid.$this->orderid.$tmpOrdAmount.$this->currency, true));
				$this->rname = $this->dolocal($this->rname,"zh");
				$this->raddr = $this->dolocal($this->raddr,"zh");

				$hiddenString.= "<input type=HIDDEN name=\"MerchantCode\" value=\"".$this->merid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"RefNo\" value=\"".$this->orderid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"PaymentId\" value=\"2\">";
				$hiddenString.= "<input type=HIDDEN name=\"Amount\" value=\"".$ordAmount."\">";
				$hiddenString.= "<input type=HIDDEN name=\"Currency\" value=\"".$this->currency."\">";
				$hiddenString.= "<input type=HIDDEN name=\"ProdDesc\" value=\"".$this->orderno."\">";
				$hiddenString.= "<input type=HIDDEN name=\"UserName\" value=\"".$this->rname."\">";
				$hiddenString.= "<input type=HIDDEN name=\"UserEmail\" value=\"".$this->remail."\">";
				$hiddenString.= "<input type=HIDDEN name=\"UserContact\" value=\"".$this->raddr."\">";
				$hiddenString.= "<input type=HIDDEN name=\"Remark\" value=\"\">";
				$hiddenString.= "<input type=HIDDEN name=\"Signature\" value=\"".$Signature."\">";
				$hiddenString.= "<input type=HIDDEN name=\"return_url\" value=\"".$tmp_url."\">";
				
				$this->action = "https://www.mobile88.com/epayment/entry.asp";
				return $hiddenString;
				break;

			case "GOOGLE":          //google checkout
				require_once(dirname(__FILE__)."/func_google.php");
				$this->url = str_replace("http://", "https://", $this->url);
				$tmp_url = $this->url."index.php?gOo=google_reply.do&";
				$ordAmount = number_format($this->amount, 2, ".", "");
				
			    $cart =  new GoogleCart($this->merid, $this->ikey, "checkout"); 
			    $item1 = new GoogleItem($this->orderid, $this->orderno, 1, $ordAmount);
			    $cart->AddItem($item1);
			    
			    $hiddenString = $cart->CheckoutButtonCode("large");
				
				$this->action = "https://checkout.google.com/cws/v2/Merchant/".$this->merid."/checkout";
				return $hiddenString;
				break;
			
			case "UDPAY":          //UDPAY
				include_once(dirname(__FILE__)."/func_udpay.php");
				$tmp_url = $this->url."index.php?gOo=udpay_reply.do&";
				$ordAmount = floor($this->amount * 100);

				$msg = "txCode=TP001&merchantId=".$this->merid."&transDate=".date("Ymd",$this->ordertime)
						."&transFlow=".$this->orderno."&orderId=".$this->orderid."&curCode=156&amount=".$ordAmount
						."&orderInfo=".$this->shopname."&comment=&merURL=".$tmp_url."&interfaceType=5";

				if (!file_exists(dirname(__FILE__)."/../plugin/paykey/udpay/udpay.key")){
					echo("read key file error!");
					exit;
				}
				$arr_key = file(dirname(__FILE__)."/../plugin/paykey/udpay/udpay.key");
				$privatekey = substr(trim($arr_key[1]), 19);
				$privateModulus = substr(trim($arr_key[2]), 23);
				$privateExponent = substr(trim($arr_key[3]), 24);
				$publicKey = substr(trim($arr_key[4]), 14);
				$publicModulus = substr(trim($arr_key[5]), 18);
				$publicExponent = substr(trim($arr_key[6]), 19);

			    $testRsaDecrypt = generateSigature($msg, $privateExponent, $privateModulus);
//				$verifySigature = verifySigature($msg, $testRsaDecrypt, $publicExponent, $publicModulus);

				$hiddenString = "<input type=hidden name=shopex_encoding value=\"gb2312\">";
				$hiddenString.= "<input type=HIDDEN name=\"txCode\" value=\"TP001\">";
				$hiddenString.= "<input type=HIDDEN name=\"merchantId\" value=\"".$this->merid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"transDate\" value=\"".date("Ymd",$this->ordertime)."\">";
				$hiddenString.= "<input type=HIDDEN name=\"transFlow\" value=\"".$this->orderno."\">";
				$hiddenString.= "<input type=HIDDEN name=\"orderId\" value=\"".$this->orderid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"curCode\" value=\"156\">";
				$hiddenString.= "<input type=HIDDEN name=\"amount\" value=\"".$ordAmount."\">";
				$hiddenString.= "<input type=HIDDEN name=\"orderInfo\" value=\"".$this->shopname."\">";
				$hiddenString.= "<input type=HIDDEN name=\"comment\" value=\"\">";
				$hiddenString.= "<input type=HIDDEN name=\"interfaceType\" value=\"5\">";
				$hiddenString.= "<input type=HIDDEN name=\"sign\" value=\"".$testRsaDecrypt."\">";
				$hiddenString.= "<input type=HIDDEN name=\"merURL\" value=\"".$tmp_url."\">";
				
//				$this->action = "http://124.42.2.165/gateway/transForward.jsp";
				$this->action = "https://www.udpay.com.cn/gateway/transForward.jsp";
				return $hiddenString;
				break;
			
			case "CHINAPNR":          //汇付天下
				$ret_url = $this->url."index.php?gOo=chinapnr_reply.do&";
				$ret_server_url = $this->url."index.php?gOo=chinapnr_server.do&";
				$ordAmount = number_format($this->amount, 2, '.', '');
				$MerDate = date("Ymd",$this->ordertime);
				$MerKeyFile = dirname(__FILE__)."/../plugin/paykey/chinapnr/MerPrK".$this->merid.".key";
				$PgKeyFile = dirname(__FILE__)."/../plugin/paykey/chinapnr/PgPubk.key";

				$pnrObj = new COM("ChinaPnr.NetpayClient");
				if(strtolower(substr($_ENV["OS"],0,7)) == "windows"){
					$ChkValue = $pnrObj->SignOrder0($this->merid, $MerKeyFile, $this->orderid, $ordAmount, $MerDate, "P", "", $this->orderno, $ret_server_url, $ret_url);
				}else{
					$ChkValue = $pnrObj->SignOrder($this->merid, $MerKeyFile, $this->orderid, $ordAmount, $MerDate, "P", "", $this->orderno, $ret_server_url, $ret_url);
				}				
				$hiddenString = "<input type=hidden name=shopex_encoding value=\"gb2312\">";
				$hiddenString.= "<input type=HIDDEN name=\"Version\" value=\"10\">";
				$hiddenString.= "<input type=HIDDEN name=\"MerId\" value=\"".$this->merid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"MerDate\" value=\"".$MerDate."\">";
				$hiddenString.= "<input type=HIDDEN name=\"OrdId\" value=\"".$this->orderid."\">";
				$hiddenString.= "<input type=HIDDEN name=\"TransAmt\" value=\"".$ordAmount."\">";
				$hiddenString.= "<input type=HIDDEN name=\"TransType\" value=\"P\">";
				$hiddenString.= "<input type=HIDDEN name=\"GateId\" value=\"\">";
				$hiddenString.= "<input type=HIDDEN name=\"MerPriv\" value=\"".$this->orderno."\">";
				$hiddenString.= "<input type=HIDDEN name=\"ChkValue\" value=\"".$ChkValue."\">";
				$hiddenString.= "<input type=HIDDEN name=\"PageRetUrl\" value=\"".$ret_url."\">";
				$hiddenString.= "<input type=HIDDEN name=\"BgRetUrl\" value=\"".$ret_server_url."\">";
				
				//$this->action = "http://tech.chinapnr.com/pay/TransGet";
				$this->action = "https://payment.chinapnr.com/pay/TransGet";
				return $hiddenString;
				break;
				
			case "ADVANCE":
				$hiddenString.= "<input type=hidden name=orderid value=\"".$this->orderno."\">";
				$hiddenString.= "<input type=hidden name=amount value=\"".$this->amount."\">";
				$hiddenString.= "<input type=hidden name=date value=\"".date("Ymd",$this->ordertime)."\">";

				$hiddenString.= "<input type=hidden name=rname value=\"".$this->rname."\">";
				$hiddenString.= "<input type=hidden name=remail value=\"".$this->remail."\">";
				$hiddenString.= "<input type=hidden name=rtel value=\"".$this->rtel."\">";
				$hiddenString.= "<input type=hidden name=rpost value=\"".$this->rpost."\">";
				$hiddenString.= "<input type=hidden name=raddr value=\"".$this->raddr."\">";
				$hiddenString.= "<input type=hidden name=rnote value=\"".$this->rnote."\">";
				$hiddenString.= "<input type=hidden name=pname value=\"".$this->payname."\">";
				$hiddenString.= "<input type=hidden name=currency value=\"".$this->currency."\">";
				$hiddenString.= "<input type=hidden name=lang value=\"".$this->lang."\">";
				
				$this->action = "./index.php?gOo=advance_reply.do&";
				return $hiddenString;
				break;

			case "ICBC":
				$tmp_url = $this->url."index.php?gOo=icbc_reply.do";

				$root_path = substr(dirname(__FILE__), 0, -7) . "syssite/shopadmin/images/";
				$bb = new COM("ICBCEBANKUTIL.B2CUtil");
				
				$rc = $bb->init($root_path . "user.crt", $root_path . "user.crt", $root_path . "user.key", $this->ikey);
				$src = $this->merid . $tmp_url  . "HS" . $this->orderid . $this->amount . $this->currency . "0";
				$ssrc = $bb->signC($src, strlen($src));
				
				$rc = $bb->verifySignC($src, strlen($src), $ssrc, strlen($ssrc)); //数据签名
				$cert = $bb->getCert(1); //商户证书
				
				$hiddenString .= "<input type=hidden name=merchantid value=\"" . $this->merid . "\" >";
				$hiddenString .= "<input type=hidden name=interfaceType value=\"HS\" >";
				$hiddenString .= "<input type=hidden name=merURL value=\"" . $tmp_url . "\" >";
				$hiddenString .= "<input type=hidden name=orderid value=\"" . $this->orderid . "\" >";
				$hiddenString .= "<input type=hidden name=amount value=\"" . $this->amount . "\" >";
				$hiddenString .= "<input type=hidden name=curType value=\"" . $this->currency . "\" >";
				$hiddenString .= "<input type=hidden name=hsmsgType value=\"0\" >";
				$hiddenString .= "<input type=hidden name=signMsg value=\"" . $ssrc . "\" >";
				$hiddenString .= "<input type=hidden name=cert value=\"" . $cert . "\" >";

				$this->action = "https://mybank.icbc.com.cn/servlet/com.icbc.inbs.b2c.pay.B2cMerPayReqServlet";
				return $hiddenString;
				break;

			case "GSPAY":
				$tmp_url = $this->url."index.php?gOo=gspay_reply.do";

				$hiddenString .= "<input type=\"hidden\" name=\"siteID\" value=\"".$this->merid."\">";
				$hiddenString .= "<input type=\"hidden\" name=\"OrderID\" value=\"".$this->orderid."\">";

        $OrderItem = newclass("orderItems");
        $OrderItem->shopId = 1;
        $OrderItem->orderId = $this->orderno;
        $OrderItem->getlist();
				$goodstotalprice = 0;
				$iterator = 1;
				while($OrderItem->next())
				{
					$hiddenString .= "<input type=\"hidden\" name=\"OrderDescription%5B".$iterator."%5D\" value=\"".$OrderItem->name."(".$this->orderno.")\">";
					$hiddenString .= "<input type=\"hidden\" name=\"Amount%5B".$iterator."%5D\" value=\"".$OrderItem->price."\">";
					$hiddenString .= "<input type=\"hidden\" name=\"Qty%5B".$iterator."%5D\" value=\"".$OrderItem->num."\">";
					$goodstotalprice += ( $OrderItem->price * $OrderItem->num );
					$iterator++;
				}
				$shippingfee =  $this->amount - $goodstotalprice;
				if($shippingfee > 0 ){
					$hiddenString .= "<input type=\"hidden\" name=\"OrderDescription%5B".$iterator."%5D\" value=\"Shipping Fee\">";		
					$hiddenString .= "<input type=\"hidden\" name=\"Amount%5B".$iterator."%5D\" value=\"".$shippingfee."\">";
					$hiddenString .= "<input type=\"hidden\" name=\"Type%5B".$iterator."%5D\" value=\"Shipping\">";
				}
				$hiddenString .= "<input type=\"hidden\" name=\"returnUrl\" value=\"".$tmp_url."\">";
				$this->action = "https://secure.rdgateway.com/payment/pay.php";
				return $hiddenString;

		}
	}

	function dolocal($str,$encoding)
	{
		global $SITE_EODING;
		if(strtoupper($SITE_EODING)=="UTF-8")
			return "local_shopex_encode".base64_encode(utf2local($str, $encoding));
		else
			return $str;

	}
}
?>