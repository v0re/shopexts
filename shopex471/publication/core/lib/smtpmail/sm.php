<?php
/**
* 
* 发信类，支持php的mail函数和socket连smtp服务器两种发信方式
* 整个类统一采用base64对邮件内容进行编码
* @package		ShopEx网上商店系统
* @version		4.7
* @author		Zzz.<xuni@shopex.cn>
* @url			http://www.shopex.cn/
* @since		PHP 4.3
* @copyright	ShopEx.cn
*
**/
class smtp{
	var $debug = false;
	var $sendway = "SMTP";
	//连接状态跟踪信息
	var $state = "";
	//计数器
	var $counter = 0;
	//连接正常标识符，发送会刊只有$this->to是变的，其他都可以直接引用第一次连接是的内容
	var $ready = false;
	//socket资源号
	var $socket = 0;
	//字符编码相关
	var $encode = "utf8";
	var $lang = "zh";
	var $charset = '';
	//smtp服务器支持是否支持8bit标识，和smtp服务器握手时确定
	var $wb = false;
	//身份验证相关
	var $ssl = false;
	var $localhost = 'localhost';
	var $host = 'localhost';
	var $port = 25;
	var $auth = true;
	var $user = '';
	var $password = '';		
	//构造信件相关
	var $sender = '';
	var $sendername = '';
	var $to;
	var $subject = '';
	var $head = '';
	var $body = '';	
	//////////////////////////////////////End of member variable declare/////////////////////
	/**
	* constructor
	* @access public
	* @return void
	*/
	function smtp($user,$pass,$relay_host = "",$smtp_port = 25,$auth = true){              
		$this->ssl = $smtp_port == 465 ? 1 : 0;
		$this->host = $relay_host;
		$this->port = $smtp_port;
		$this->auth = isset($auth) ? $auth : true;
		$this->user = $user;
		$this->password = $pass;
	}
	/**
	* do mail send action
	* @access public
	* @return bool
	*/
	function sendmail($to, $from, $subject = "", $body = "", $mailtype, $mailmaster="Webmaster"){		
		$this->to = $to;
		//发会刊，如果服务器已经ready就不设置下面的值了
		if(!$this->ready){
			$this->sendername = $mailmaster == '' ? 'Webmaster' : $mailmaster;
			$this->sender = $from;
			$this->subject = $subject;
			$this->body = $body;		
			//设置全局变量$charset
			$this->setCharset();
			//发信，默认用smtp
		}
		if(strtoupper($this->sendway) == "MAIL"){
			$rt = $this->mailSend();
		}else{
			$rt =  $this->smtpSend();
		}
		
		return $rt;
	}	
	/**
	* set global char set
	* @access private
	* @return void
	* $this->charset should be established $this->charset will be used in mail biulding
	*/
	function setCharset(){
		//$this->encode in local or utf8
		//$this->lang in zh big5 or iso-8859-1
		if($this->encode == 'local'){
			switch($this->lang){
				case "zh":
					$this->charset = "GB2312";
					$this->subject = $this->getlocal($this->subject,$this->lang);
					$this->sendername = $this->getlocal($this->sendername,$this->lang);
					$this->body = $this->getlocal($this->body,$this->lang);	
					break;
				case "big5":
					$this->charset = "BIG5";
					$this->subject = $this->getlocal($this->subject,$this->lang);
					$this->sendername = $this->getlocal($this->sendername,$this->lang);
					$this->body = $this->getlocal($this->body,$this->lang);	
					break;
				case "en":
					$this->charset = "iso-8859-1";
					break;
				default:
					$this->charset = "UTF-8";
			}
		}else{
			$this->charset = "UTF-8";
		}
	}
	/**
	* Make encode transform.
	* @access private
	* @return string
	*/
	function  getLocal($instr,$enc){
		if(function_exists("iconv") && is_callable("iconv")){
			if($enc == "zh" || $enc == "big5") $enc = $this->charset;
			return iconv("UTF-8",$enc,$instr);
		}else{
			return utf2local($instr,$enc);
		}
	}
	/**
	* Send mail using the PHP mail() function.  
	* @access private
	* @return bool
	* values  transfer by global member variables
	*/
	function mailSend(){
		$this->head  = "To: ".substr($this->to,0,(strpos($this->to,'@')))." <".$this->to.">\n";
		$this->head .= "From:  \"".$this->sendername."\" <".$this->sender.">\n";
		$this->head .= "Date: ".date("r",time())."\n";
		$this->head .= "MIME-Version:  1.0\n";
		$this->head .= "Content-Transfer-Encoding:  base64\n";
		$this->head .= "Content-Type:  text/html; charset=\"".$this->charset."\"\n";
		$this->head .= "\n\n";
		
		if(!$this->ready){
			$this->body = chunk_split(base64_encode($this->body),76, "\r\n");
			$this->body = str_replace("\r","",$this->body);
		}
		
		$rt = @mail($this->to, $this->subject,$this->body,$this->head);
		//error_log($this->head."\r\n---\r\n".var_export($rt,1)."\r\n------------End---------\r\n",3,__FILE__.".mail.log");
		//如果成功，标识服务器已经准备好
		$this->ready = $rt;

		return $rt;
	}
	/**
	* Send mail using fsocket.  
	* @access private
	* @return bool
	* values  transfer by global member variables
	*/
	function smtpSend(){
		//多次发送，可以跳过此过程
		if(!$this->ready){
			if($this->ssl){
				$version = explode(".",function_exists("phpversion") ? phpversion() : "3.0.7");
				$php_version = intval($version[0])*1000000+intval($version[1])*1000+intval($version[2]);
				if($php_version<4003000 )
					$this->setError("establishing SSL sockets requires at least PHP version 4.3.0");
				if(!function_exists("extension_loaded")|| !extension_loaded("openssl") )
					$this->setError("establishing SSL sockets requires the OpenSSL extension enabled");
			}
			//需要用ssl连接的话，加上ssl://
			$this->host = ($this->ssl ? "ssl://" : "").$this->host;
			$this->socket = @fsockopen($this->host,$this->port);
			
			//检验是否成功连接到smtp服务器
			if(!is_resource($this->socket)){
				$this->setError("could not connect to the host \"".$this->host."\" on port ".$this->port);
				return false;
			}
					
			if($this->verify("220")){
				if($this->debug) $this->outputDebug("Connected to SMTP server \"".$this->host."\" on port ".$this->port."....");
				if($this->auth){
					//握手
					if($this->putLine("EHLO shopex")){
						$this->state = "Verify EHLO failed";
						if(!$this->verify("250")) return false;									
					}
					//告诉服务器我要验证
					if($this->putLine("AUTH LOGIN")){
						$this->state = "Verify AUTH LOGIN failed";
						if(!$this->verify("235,334")) return false;
					}
					//输入用户名
					if($this->putLine(base64_encode($this->user))){
						$this->state = "Verify User name failed";
						if(!$this->verify("235,334")) return false;
					}
					//输入密码
					if($this->putLine(base64_encode($this->password))){
						$this->state = "Verify Password failed";
						if(!$this->verify("235,334")) return false;
					}
				}else{
					//不需要验证
					if($this->putLine("HELO shopex")){
						$this->state = "Verify HELO failed may be this server need auth login";
						if(!$this->verify("250")) return false;
					}									
				}
			}
		}
		//告诉服务器谁要寄信
		if($this->putLine("MAIL FROM: <$this->sender>")){
				$this->state="MAIL FROM failed!";
				if(!$this->verify("250")) return false;		
		}
		//告诉服务器信要发给谁
		if($this->putLine("RCPT TO: <$this->to>")){
			$this->state="RCPT failed!";
			if(!$this->verify("250,251")) return false;
		}

		//告诉服务器要开始发信了,服务器要等到\r\n.\r\n才会认为信件已经结束
		if($this->putLine("DATA")){
			$this->state="DATA failed!";
			if(!$this->verify("354")) return false;	
		}
		//持续发送时，一定要保证$this->head要重新构造
		$this->head = "To: ".substr($this->to,0,(strpos($this->to,'@')))." <".$this->to.">\r\n";
		//如果支持宽字节，直接用明码发送，不支持则用base64，base64编码会被某些发垃圾邮件网关挡掉，并且会浪费一点点时间
		if($this->wb){
				$this->head .= "From: \"".$this->sendername."\" <".$this->sender.">\r\n";
				$this->head .= "Subject: ".$this->subject."\r\n";
				$this->head .= "Date: ".date("r",time())."\r\n";
				$this->head .= "MIME-Version:  1.0\r\n";
				$this->head .= "Content-Transfer-Encoding:  8bit\r\n";
				$this->head .= "Content-Type:  text/html; charset=\"".$this->charset."\"\r\n";
				$this->head .= "\r\n\r\n";
		}else{	
				$this->head .= "From:  \"=?".$this->charset."?B?".base64_encode($this->sendername)."?=\" <".$this->sender.">\r\n";
				$this->head .= "Subject:  =?".$this->charset."?B?".base64_encode($this->subject)."?=\r\n";
				$this->head .= "Date: ".date("r",time())."\r\n";
				$this->head .= "MIME-Version:  1.0\r\n";
				$this->head .= "Content-Transfer-Encoding:  base64\r\n";
				$this->head .= "Content-Type:  text/html; charset=\"".$this->charset."\"\r\n";
				$this->head .= "\r\n\r\n";
				//对信体进行编码：base64后拆分为76个字符每行,加入isready的判断，省点时间
				if(!$this->ready){
					$this->body = chunk_split(base64_encode($this->body),76, "\r\n");
				}				
		}

		//发送信头
		$this->putData($this->head);
		//发送信体
		$this->putData($this->body);
		////////////////////////////////
		//告诉服务器，信发完了
		if($this->putLine("\r\n.")){
			$this->state = "Endding failed";
			if(!$this->verify("250")) return false;
			//如果成功，标识服务器已经准备好
			$this->ready = true;
		}
		//超过5条就要重新连接一下
		$this->counter++;
		if($this->counter >= 5){
			$this->ready == false;
			$this->counter = 0;
			if($this->putLine("QUIT")){
				$this->state = "Disconnect failed!";
				if(!$this->verify("221")) return false;
				fclose($this->socket);
				$this->state='';
				if($this->debug) $this->outputDebug("Disconnected.");
			}
			usleep(100);
		}		
		//如果不采用长连接，则断开连接
		//$this->putline("RSET\r\n");
		/*if($this->putLine("QUIT") && !$this->pconnect){
		$this->state = "Disconnect failed!";
		if(!$this->verify("221")) return false;
			fclose($this->socket);
			$this->state='';
			if($this->debug) $this->outputDebug("Disconnected.");
		}		
		*/

		return true;
	}


	/**
	* verify the server reponse status
	* @access private
	* @return boolean
	* values  transfer by global member variables
	*/
	function verify($instr){
		//截取smtp server返回的code number
		$rst = $this->getLine();
		$code = substr($rst,0,3);
		//转字符串为数组
		$inarr = explode(",",$instr);
		foreach ($inarr as $value){
			//smtp服务器返回的状态码符合，返回真
			if(strcmp($code,$value) == 0){
				return true;
			}
		}
		//失败善后工作
		$this->setError($this->state);
		fclose($this->socket);
		$this->socket=0;
		return false;
	}	
	/**
	* get server return string.  
	* @access private
	* @return boolean
	* values  transfer by global member variables
	*/
	function getLine(){
		$time_start = time();
		$line = '';
		while($data = fgets($this->socket,512)){
			$line.= $data;
			if(substr($data,3,1)==" ") break;
			//防止死循环，smtp会话5秒钟已经足够
			if((time() - $time_start) > 5){
				$this->setError("it was too long to read line from the SMTP server");
				return false;
			}
		}
		$line = substr($line,0,strlen($line)-2);
		if($this->debug)	$this->outputDebug("S $line");
		//返回的内容是否有8bit的，以此判断smtp服务器是否支持宽字节
		if(stristr($line,"8BITMIME")) $this->wb = true;
		
		return $line;
	}
	/**
	* put smtp command to server  
	* @access private
	* @return boolean
	* values  transfer by global member variables
	*/
	function putLine($line){
		if($this->debug) $this->outputDebug("C ".$line);
		if(!@fputs($this->socket,$line."\r\n")){
			$this->setError("it was not possible to send a line to the SMTP server");
			return(false);
		}
		return(true);
	}
	/**
	* put big data to server ig. head or body etc.. 
	* @access private
	* @return boolean
	* values  transfer by global member variables
	*/
	function putData(&$data){
		if(strlen($data)){	
			if($this->debug) $this->outputDebug("C $data");
			if(!@fputs($this->socket,$data)){
				$this->setError("it was not possible to send data to the SMTP server");
				return(false);
			}
			return(true);
		}
		return false;
	}
	/**
	* ouput debug information
	* @access private
	* @return boolean
	* values  transfer by global member variables
	*/
	function outputDebug($instr){
		echo $instr."\r\n";
		//error_log($instr."\r\n",3,__FILE__.".1.log");
	}
	/**
	* ouput debug information
	* @access private
	* @return boolean
	* values  transfer by global member variables
	*/
	function setError($instr){
		$this->ready = false;
		//error_log($instr."\r\n",3,__FILE__.".2.log");
		if($this->debug){
			echo($instr."\r\n");
		}
		return false;
	}
};
	
?>