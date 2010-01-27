<?php
/**
 * mdl_shopMailSetup
 * 
 * @uses modelFactory
 * @package 
 * @version $id$
 * @copyright 2003-2007 ShopEx
 * @author Liujy <ever@zovatech.com> 
 * @license Commercial
 */
class mdl_shopMailSetup extends modelFactory{
	var $smtpact;
	
	/**
	 * getMailModel 
	 *	
	 * @access public
	 * @return array
	 */
	function getSelEmailLang(){
			return array('zh'=>'简体中文','big5'=>'繁体中文','en'=>'English');	 //不需进语言包
	}

	/**
	 * getEmailSetting 
	 *	
     * @access public
	 * @return array
	 */
	function getEmailSetting(){
		return $this->db->selectrow("SELECT 
									offer_smtp_server,
									offer_smtp_user,
									offer_smtp_password,
									offer_smtp_email,
									offer_smtp_port,
									offer_smtp_ifcheck,
									offer_name,
									mail_lang,
									mail_enc,
									offer_smtp_sendmode
								FROM sdb_mall_offer
								WHERE offerid=".$this->shopId);
	}
    /**
	 * toInsertSetting 
	 *	
	 * @param mixed $aValue
	 * @access public
	 * @return array
	 */
	function toInsertSetting($aValue,$check=true){
		
		if($check){
			if(trim($aValue['offer_smtp_server']) == '') return -1; //SMTP Server 为空
			if(trim($aValue['offer_smtp_user']) == '') return -2;	//SMTP User 为空
			if(trim($aValue['offer_smtp_port']) == '') return -3;	//SMTP 端口为空
		}		
		$rs = $this->db->query("SELECT 
									offer_name,
									offer_smtp_server,
									offer_smtp_user,
									offer_smtp_password,
									offer_smtp_email,
									offer_smtp_port,
									offer_smtp_ifcheck,
								  	mail_enc,
									mail_lang,
									offer_smtp_sendmode
								FROM sdb_mall_offer 
								WHERE offerid=".$this->shopId);		
		$sql = $this->db->GetUpdateSql($rs,$aValue);
		if(!$sql || $this->db->exec($sql))
			return true;
		else
			return false;
	}

	/**
	 * getTitleList 
	 *	
	 * @access public
	 * @return array
	 */
	function getTitleList(){
		$this->system->set_mo_pkg('default');
		return array(
				'user_register' => __('Member Register'), 			 //会员注册
				'user_chpass' => __('Change Password'),  			 //密码修改
				'user_passback' => __('Pssword Repair'), 			 //用户密码取回
				'user_bbsback' => __('Reply'),  					 //留言回复
				'user_commentback' => __('Product Comments'),  //商品评论回复
				'order_create' => __('Customer-order Build'), 		 //前台订单生成
				'pay_success' => __('Order payment succeed!'),		 //前台订单支付成功
				'order_check' => __('Shopadmin-order Paid'), 		 //后台订单收款
				'order_confirm' => __('Shopadmin-order Verify'),	 //后台订单确认
				'order_cancel' => __('Shopadmin-order Cancle'), 	 //后台订单作废
				'order_delivery' => __('Shopadmin-order Shipped'),   //后台订单发货
				'sendtofriend' => __('Sent to Friend'),  			 //前台发送给朋友
				'goods_notify' => __('Product Arrival Notification'),//商品到货通知
				'order_create_notify_shopman' => __('New Order Inform Administrator'),	//订单生成通知店主
				'comment_create_notify_shopman' => __('New message notification shopkeepers.'),	//新留言通知店主
				'admin_order_feedback' => __('Order Feedback')		 //管理员订单反馈
			);
	}
	
	/**
	 * getEmailContentIntro 
	 *	
     * @access public
	 * @return array
	 */
	 function getEmailContentIntro(){
		return array(
				'user_register' => __('@shopname@ Store Name; ').__('@username@ User ID; ').__('@passwd@ Password; ').__('@truename@ User Name'), 			 //会员注册
				'user_chpass' => __('@shopname@ Store Name; ').__('@username@ User ID; ').__('@newpass@ New Password; ').__('@truename@ User Name'),  			 //密码修改
				'user_passback' => __('@shopname@ Store Name; ').__('@username@ User ID; ').__('@newpass@ New Password; ').__('@truename@ User Name'), 			 //用户密码取回
				'user_bbsback' => __('@shopname@ Store Name; ').__('@question@ Message Content; ').__('@reply@ Reply Content; ').__('@truename@ User Name'),  					 //留言回复
				'user_commentback' => __('@shopname@ Store Name; ').__('@question@ Comment Content; ').__('@reply@ Reply Content; ').__('@truename@ User Name; ').__('@goodsname@ Product Name; ').__('@goodslink@ Product URL'),  //商品评论回复
				'order_create' => __('@shopname@ Store Name; ').__('@truename@ Consignee Name; ').__('@orderid@ Order Item; ').__('@orderamount@ Order Total; ').__('@receiver_address@ Shipping Address'), 		 //前台订单生成
				'pay_success' => __('@shopname@ Store Name; ').__('@truename@ Consignee Name; ').__('@orderid@ Order Item; ').__('@receiver_address@ Shipping Address; ').__('@reply_title@ Reply Title; ').__('@reply_result@ Reply Content(include virtual product info)'),		 //前台订单支付成功
				'order_check' => __('@shopname@ Store Name; ').__('@truename@ Consignee Name; ').__('@orderid@ Order Item; ').__('@receiver_address@ Shipping Address'), 		 //后台订单收款
				'order_confirm' => __('@shopname@ Store Name; ').__('@truename@ Consignee Name; ').__('@orderid@ Order Item; ').__('@receiver_address@ Shipping Address'),	 //后台订单确认
				'order_cancel' => __('@shopname@ Store Name; ').__('@truename@ Consignee Name; ').__('@orderid@ Order Item; ').__('@receiver_address@ Shipping Address'), 	 //后台订单作废
				'order_delivery' => __('@shopname@ Store Name; ').__('@truename@ Consignee Name; ').__('@orderid@ Order Item; ').__('@receiver_address@ Shipping Address; ').__('@orderinfo@ Order Information(include virtual product info)'),   //后台订单发货
				'sendtofriend' => __('@shopname@ Store Name; ').__('@yname@ Recommender; ').__('@yemail@ Recommender Email; ').__('@note@ Remark; ').__('@goodsname@ Product Name; ').__('@goodslink@ Product URL'),  			 //前台发送给朋友
				'goods_notify' => __('@shopname@ Store Name; ').__('@username@ User ID; ').__('@truename@ User Name; ').__('@goodsname@ Product Name; ').__('@goodslink@ Product Link'),	//商品到货通知
				'order_create_notify_shopman' => __('@shopname@ Store Name; ').__('@orderid@ Order Item'),		//订单生成通知店主
				'comment_create_notify_shopman' => __('@shopname@ Store Name'),			//新留言通知店主
				'admin_order_feedback' => __('@orderid@ Order Item; ').__('@question@ Order Message; ').__('@reply@ Reply')		 //管理员订单反馈
				);
	 }
	
	/**
	 * getEmailList 
	 *	
	 * @param boolean $flg
	 * @access public
	 * @return array
	 */
	function getEmailList(){
		$aTemp = $this->db->select("SELECT id,mail_type,mail_title as title,mail_enable as status FROM sdb_mall_offer_mail where offerid=".$this->shopId);
		$aEmailList = $this->getTitleList();
		if($aTemp){
			foreach($aTemp as $key => $val){
				$aTemp[$key]['title'] = $aEmailList[$val['mail_type']];
				$aTemp[$key]['status'] = $val['status']?__('open'):__('close');
			}
		}
		return $aTemp;
	}
	
	/**
	 * getContentById 
	 *	
	 * @param int $id
	 * @access public
	 * @return array
	 */
	function getContentById($id){
		return $this->db->selectrow("SELECT * FROM sdb_mall_offer_mail WHERE id=".$id." AND offerid=".$this->shopId);	
	}
	
	/**
	 * toUpdate 
	 *	
	 * @param mixed $aValue
	 * @access public
	 * @return array
	 */
	function toUpdate($aValue){
		if(trim($aValue['mail_title']) == '') return 'TitleLost';
		if($aValue['mid']){
			$rs = $this->db->query("SELECT * FROM sdb_mall_offer_mail WHERE id=".$aValue['mid']."  AND offerid=".$this->shopId);
			$sql = $this->db->getUpdateSql($rs,$aValue);
			if(!$sql || $this->db->exec($sql)){
				return 'true';
			}else
				return 'false';
		}else
			return 'midLost';
	}
	
	/**
	 * sendMail 
	 * 
	 * @access public
	 * @return boolean
	 */
	function sendMail($mailType='', $aPara){
		$aMail = $this->getMailContent($mailType);
		if($aMail['mail_enable'] == 1 || $mailType == '')
		{
			$aRet = $this->getEmailSetting();
			$smtpserver = $aRet['offer_smtp_server'];	//SMTP服务器
			$smtpserverport = $aRet['offer_smtp_port'];	//SMTP服务器端口
			$smtpusermail = $aRet['offer_smtp_email'];	//SMTP服务器的用户邮箱(您的邮箱)
			$smtpuser = $aRet['offer_smtp_user'];		//SMTP服务器的用户帐号
			$smtppass = $aRet['offer_smtp_password'] ;	//SMTP服务器的用户密码
			$shopname = $aRet['offer_name'];
			$smtpcheck = $aRet['offer_smtp_ifcheck'];
			
			switch($mailType){
				case 'order_delivery':	//订单发货
					$objOrder = $this->system->loadModel('order');
					$aOrder = $objOrder->getById($aPara['linkid']);
					$this->set_mailvar("shopname", $shopname);
					$this->set_mailvar("orderid", $aPara['linkid']);
					$this->set_mailvar("receiver_address", $aOrder['addr']);
					$this->set_mailvar("truename", $aOrder['name']);
					$this->set_mailvar("orderinfo", $objOrder->getPayresult($aPara['linkid']),$this->shopId);
					$this->getrealinfo();
					$toMail = $aOrder['email'];
				break;
				case 'order_check':	//订单收款
					$objOrder = $this->system->loadModel('order');
					$aOrder = $objOrder->getById($aPara['linkid']);
					$this->set_mailvar("shopname", $shopname);
					$this->set_mailvar("orderid", $aPara['linkid']);
					$this->set_mailvar("receiver_address", $aOrder['addr']);
					$this->set_mailvar("truename", $aOrder['name']);
					$this->set_mailvar("orderinfo", $objOrder->getPayresult($aPara['linkid']),$this->shopId);
					$this->getrealinfo();
					$toMail = $aOrder['email'];
				break;
				case 'order_confirm':	//订单确认
					$objOrder = $this->system->loadModel('order');
					$aOrder = $objOrder->getById($aPara['linkid']);
					$this->set_mailvar("shopname", $shopname);
					$this->set_mailvar("orderid", $aPara['linkid']);
					$this->set_mailvar("receiver_address", $aOrder['addr']);
					$this->set_mailvar("truename", $aOrder['name']);
					$this->getrealinfo();
					$toMail = $aOrder['email'];
				break;
				case 'order_cancel':	//订单取消
					$objOrder = $this->system->loadModel('order');
					$aOrder = $objOrder->getById($aPara['linkid']);
					$this->set_mailvar("shopname", $shopname);
					$this->set_mailvar("orderid", $aPara['linkid']);
					$this->set_mailvar("receiver_address", $aOrder['addr']);
					$this->set_mailvar("truename", $aOrder['name']);
					$this->getrealinfo();
					$toMail = $aOrder['email'];
				break;
				case 'admin_order_feedback':	//订单留言回复
					$objOrder = $this->system->loadModel('order');
					$aOrder = $objOrder->getById($aPara['linkid']);
					$this->set_mailvar("shopname", $shopname);
					$this->set_mailvar("orderid", $aPara['linkid']);
					$this->set_mailvar("question", $aPara['msg']);
					$this->set_mailvar("reply", $aPara['reply']);
					$this->set_mailvar("truename", $aOrder['name']);
					$this->getrealinfo();
					$toMail = $aOrder['email'];
				break;
				default:
					$toMail = $aPara['tomail'];
					$this->mail_realtitle = $aPara['mailtitle'];
					$this->mail_realcontent = $aPara['mailcontent'];
				break;
			}

			if(!is_object($this->smtpact)){
				$this->smtpact = $this->system->smtp($smtpuser, $smtppass, $smtpserver, $smtpserverport, $smtpcheck);
				//$smtp = $this->__smtp($smtpuser, $smtppass, $smtpserver, $smtpserverport, $smtpcheck);
				//$this->smtpact->debug = true;
				$this->smtpact->encode = $aRet['mail_enc'];
				$this->smtpact->lang = $aRet['mail_lang'];
				$this->smtpact->sendway = ($aRet['offer_smtp_sendmode']==1 ? "MAIL" : "SMTP");
			}
			return $this->smtpact->sendmail($toMail, $smtpusermail, $this->mail_realtitle , $this->mail_realcontent, "TXT", $shopname);
		}
	}
	
	/**
	 * getMailContent 
	 * 
	 * @access public
	 * @return boolean
	 */
	function getMailContent($mtype)
	{
		$query = "SELECT * FROM sdb_mall_offer_mail WHERE offerid='".$this->shopId."' AND mail_type='".$mtype."'";
		$aTemp = $this->db->selectrow($query);
		{
			$this->mail_enable = $aTemp["mail_enable"];
			$this->mail_title = $aTemp["mail_title"];
			$this->mail_content = $aTemp["mail_content"];
		}
		$this->mail_type = $mtype;
		return $aTemp;
	}
	
	/**
	 * getMailContent 
	 * 
	 * @access public
	 * @return boolean
	 */
	function getrealinfo()
	{
		$this->mail_realtitle = $this->repl($this->mail_title);
		$this->mail_realcontent = $this->repl($this->mail_content);
	}

	function set_mailvar($key,$val)
	{
		$this->var_arr[$key] = $val;
	}

	function repl($str)
	{
		global $INC_SCHEME;
		reset($this->var_arr);
		while(list($k,$v) = each($this->var_arr))
		{
			$str = str_replace("@".$k."@",$v,$str);
		}

		 if(empty($INC_SCHEME))
		 {
			 if(!empty($_SERVER["SCRIPT_URI"]))
			 {
				 $arr_uri = parse_url($_SERVER["SCRIPT_URI"]);
				 $INC_SCHEME = $arr_uri["scheme"];
			 }
			 else
			 {
				$INC_SCHEME = "http";
			 }
		 }

		if(strstr($_SERVER["PHP_SELF"],"/syssite/shopadmin/"))
		{
			$needle = $INC_SCHEME."://".$_SERVER['HTTP_HOST']."/syssite/home/shop/".$this->shopid."/";
		}
		elseif(strstr($_SERVER["PHP_SELF"],"/shopadmin/"))
		{
			$needle = $INC_SCHEME."://".$_SERVER['HTTP_HOST']."/home/shop/".$this->shopid."/";
		}
		elseif(file_exists("syssite"))
		{
			$needle = $INC_SCHEME."://".$_SERVER['HTTP_HOST']."/syssite/home/shop/".$this->shopid."/";
		}
		else
		{
			if(LICENSE_8==2)
			{
				$needle = $INC_SCHEME."://".$_SERVER['HTTP_HOST']."/home/shop/".$this->shopid."/";
			}
			else
			{
				if(strstr($_SERVER["PHP_SELF"],"/home/shop/"))
				{
					$needle = $INC_SCHEME."://".$_SERVER['HTTP_HOST']."/home/shop/".$this->shopid."/";
				}
				else
				{
					$needle = $INC_SCHEME."://".$_SERVER['HTTP_HOST']."/";
				}
			}
		}

		$str = preg_replace('/="pictures\//', "=\"".$needle."pictures/", $str);
		$str = preg_replace('/=\'pictures\//', "='".$needle."pictures/", $str);
		$str = preg_replace('/=pictures\//', "=".$needle."pictures/", $str);

		return $str;
	}
} 
?>
