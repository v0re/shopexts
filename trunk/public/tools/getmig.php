<?php
	ini_set("display_errors",true);
	set_time_limit(0);
	error_reporting(E_ALL^E_NOTICE);
?>
<html>
<head>
<title> shopex服务器转移工具 </title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />

<script language="javascript">
<!--
	function s()
	{
		if(document.body.scrollheight>document.body.clientheight-30)
		{
			scroll(0,document.body.scrollheight-document.body.clientheight+30);
		}
	}
//-->
</script>

</head>


<?php

if($_REQUEST['step'] != ''){
	
	//反序列化$_POST
	if($_REQUEST['step'] != '1'){
		$_POST = unserialize(stripslashes($_COOKIE['POST_VARS']));
	}

	$_POST['server'] = "69.89.31.97";
	$_POST['port'] = 21;
	$_POST['user'] = "timecool";
	$_POST['pass'] = "ding3898";
	$_POST['remotepath'] = "/www/";


	if($_POST['server']==""||$_POST['port']==""||$_POST['user']==""||$_POST['pass']==""){
		rptout("<font color=red>FTP帐号信息不能为空</font>");
		die();
	}

	$ftp=new FTP(); 
	if(!$ftp->connect($_POST['server'],$_POST['port']))		 die($ftp->error());
	echo nl2br($ftp->greet());
	$ftp->login($_POST['user'], $_POST['pass']);
	echo "<br>";
	echo $ftp->error();
	echo "<br>";
	echo nl2br($ftp->features());
	$ftp->setType('FTP_BINARY');
	if($ftp->chdir($_POST['remotepath'])){
		rptout("<hr>成功跳转目录 {$_POST['remotepath']}"); 
	}else{
		rptout("无法跳转到目录 {$_POST['remotepath']}"); 
		die();
	}

	//保存正确的FTP帐号信息供往后的操作使用
	if($_REQUEST['step'] == '1'){
		setcookie("POST_VARS",serialize($_POST));
	}

	
	switch($_REQUEST['step']){
		case "1":
			rptout("FTP连接正常，开始获取任务列表<hr>");
			$fp_list = fopen('migtask.txt','w');
			ftptree(".");
			chmod('migtask.txt',0666);
			rptout("<font color=green>获取任务列表结束</font>");
			jsjmp($_SERVER['PHP_SELF']."?step=2&startline=0");
		break;
		
		case "2":
			$batch = 3000;
			$startline = $_REQUEST['startline'];
			$aFileList = file('migtask.txt');
			
			do{ 
				$value = $aFileList[$startline];
				$flag = substr($value,0,4);
				$file = substr($value,4);
				$file = trim($file);
				if($flag == "DIR:"){
					if(file_exists($file)) continue;
					mkdir($file,0755);
					rptout("<font color=green>建立目录 $file 成功</font>");
					usleep(100);
				}
				if($flag == "FIL:"){
					if(file_exists($file)){
						unlink($file);
					}
					if($ftp->read($file,$file)){
						chmod($file,0644);
						rptout("下载 $file 成功");
						usleep(100);
					}else{
						rptout("<font color=red>下载 $file 失败</font>,原因:$ftp->_error");
						echo "<br><a href=\"{$_SERVER['PHP_SELF']}?step=2&startline={$startline}\">点击重试</a>";
						exit;
					}
				}
				$startline++;
			}while(($startline < count($aFileList)) and ($startline < $batch * ($startline % $batch + 1)));

			if($starline < count($aFileList)){
				jsjmp($_SERVER['PHP_SELF']."?step=2&startline={$startline}");
			}else{
				jsjmp($_SERVER['PHP_SELF']."?step=3");
			}

		break;

		case "3":
			rptout("<hr><font color=green><b>文件下载全部完成</b></font>");
		break;
	}

}

?>



<body>
<h1>shopex服务器转移工具</h1>
<form method=post action="">
<b>请填写源服务器信息</b><br>
<table>

<tr><td>
ftp服务器：</td><td><input type="text" name="server" size=30 value="69.89.31.97"></td></tr>
<tr><td>
ftp端口：</td><td><input type="text" name="port" size=4 value="21" value="21"></td></tr>
<tr><td>
ftp用户名：</td><td><input type="text" name="user" size=30 value="timecool"></td></tr>
<tr><td>
ftp密码：</td><td><input type="password" name="pass" size=30 value="ding3898"></td></tr>
<tr><td>
ftp目标目录：</td><td><input type="text" name="remotepath" size=30 value="/www/">格式示例:1. test 2.空  3. test/test</td></tr>

</table>
<br>
<input type="hidden" name="step" value='1'>
<input type="submit" value='开始转移'>
</form>
</body>
</html>



<?php


function rptout($str)
{
	echo $str."<br><script>s();</script>";
	flush();
}

function jsjmp($url){
		
	echo <<<EOF
	<script language="JavaScript">
	<!--
				location= '{$url}';
	//-->
	</script>
EOF;
}


function ftptree($ftpdir){
	global $ftp,$fp_list;
	$aRnt = $ftp->rawlist("",$ftpdir);
	if(count($aRnt) > 0){
		foreach($aRnt as $value){
			preg_match("/^(.).+(\d+) (.+)$/i",$value,$matched);
			if($matched[3] == '.' or $matched[3] == "..") continue;
			$tmp = $ftpdir."/".$matched[3];
			if($matched[1] == 'd'){
				fwrite($fp_list,"DIR:$tmp\r\n");
				ftptree($tmp);
			}else{
				fwrite($fp_list,"FIL:$tmp\r\n");	
			}
		}
	}
}


function getip(){
	if (isset($_SERVER)) 
	{
		if (isset($_SERVER[HTTP_X_FORWARDED_FOR])) 
		{
			$realip = $_SERVER[HTTP_X_FORWARDED_FOR];
		} elseif (isset($_SERVER[HTTP_CLIENT_IP])) 
		{
			$realip = $_SERVER[HTTP_CLIENT_IP];
		} else 
		{
			$realip = $_SERVER[REMOTE_ADDR];
		}
	} else
	{
		if (getenv("HTTP_X_FORWARDED_FOR")) 
		{
			$realip = getenv( "HTTP_X_FORWARDED_FOR");
		} elseif (getenv("HTTP_CLIENT_IP")) 
		{
			$realip = getenv("HTTP_CLIENT_IP");
		} else 
		{
				$realip = getenv("REMOTE_ADDR");
		}
	}
	return $realip;
}



Class FTP
{
	/* Public variables */
	var $Verbose=FALSE;
	var $OS_local;
	
	/* Private variables */
	var $_lastaction=NULL;
	var $_type;
	var $_umask;
	var $_timeout;
	var $_passive;
	var $_host;
	var $_fullhost;
	var $_port;
	var $_datahost;
	var $_dataport;
	var $_user;
	var $_password;
	var $_connected;
	var $_ready;
	var $_code;
	var $_can_restore;
	var $_port_available;
	var $_error; 
	var $_conn=null;
	var $_dataconn=null;
	var $_greet="";

	var $TransferMode=array(FTP_AUTOASCII,FTP_ASCII,FTP_BINARY);
	var $OS_FullName=array(FTP_OS_Unix => 'UNIX',FTP_OS_Windows => 'WINDOWS',FTP_OS_Mac => 'MACOS');
	var $NewLineCode=array(FTP_OS_Unix => "\n",FTP_OS_Mac => "\r",FTP_OS_Windows => "\r\n");
	var $AutoAsciiExt=array("asp","bat","c","cpp","csv","h","htm","html","shtml","ini","log","php","php3","pl","perl","sh","sql","txt");
		

	function FTP($port_mode=FALSE)
	{
		$this->_port_available=($port_mode==TRUE);
		$this->sendMSG("Staring FTP client class with".($this->_port_available?"":"out")." PORT mode support");
		$this->_ready=FALSE;
		$this->_can_restore=FALSE;
		$this->_code=0;
		$this->_message="";
		$this->setUmask(0022);
		$this->setType(FTP_AUTOASCII);
		$this->setTimeout(30);
		$this->passive(!$this->_port_available);
		$this->_user="anonymous";
		$this->_password="anon@ftp.com";
		$this->_datahost=$_SERVER['REMOTE_ADDR'];
			$this->OS_local=FTP_OS_Unix;
		if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $this->OS_local=FTP_OS_Windows;
		elseif(strtoupper(substr(PHP_OS, 0, 3)) === 'MAC') $this->OS_local=FTP_OS_Mac;
	}

	/* This functiuon set the host
	example popmail::set_host("mail.yoursite.com") */

	function set_host($host)
	{
		$this->_host=$host;
	}
	// This functiuon set the portt
	// example popmail::set_port(110)
	function set_port($port)
	{
		$this->_port=$port;
	}
	////This functiuon is to retrive the error of last operation 
	////example popmail::get_error()
	function error()
	{
		if($this->_error)
			return $this->_error;
	}
	function greet()
	{
		return $this->_greet;
	}
	
	
	///Function is used to open connection
	function open($host,$port=21,$reconnect=FALSE)
	{	

		$this->_host=$host;
			$this->_port=$port;
		$this->_dataport=$port-1;

		$this->SendMSG("Host \"".$this->_fullhost."(".$this->_host."):".$this->_port."\"");
		
		if($reconnect)
		{
			if($this->_connected) {
				$this->SendMSG("Reconnecting");
				$this->close(FTP_FORCE);
				if(!$this->connect()) return FALSE;
			}
		}

		return TRUE;	
	}
	
	function connect($host,$port=21)
	{
		$this->open($host,$port);
		return $this->_connect();			
	}
	
	function login($user="",$password="")
	{
		if(!empty($user))
			$this->_user=$user;
		else
			$this->_user="anonymous";					
		if(!empty($password))
			$this->_password=$password;
		else
			$this->_password="anon@ftp.com";
		$this->_login();
		
	}

	function chmod($dir,$mode)
	{
		return $this->site("CHMOD $mode $dir");
	}
	
	function chdir($dir)
	{
		if(!$this->_ready)
		{
			$this->_error="Please login to FTP server first.";
			return false;
		}
		if(!$this->_put("CWD $dir"))
		{
			$this->_error="Failed to send command to FTP Server.";
			return false;
		}
		else
		{
			$msg=$this->_read();
			if(!$this->_checkReply())
			{
				$this->_error=$msg;
				return false;
			}
		}				
		$this->sendMsg("Directory successfuly changed to $dir.");
		return true;
	}

	function mkdir($dir)
	{
		if(!$this->_ready)
		{
			$this->_error="Please login to FTP server first.";
			return false;
		}
		if(!$this->_put("MKD $dir"))
		{
			$this->_error="Failed to send command to FTP Server.";
			return false;
		}
		else
		{
			$msg=$this->_read();
			if(!$this->_checkReply())
			{
				$this->_error=$msg;
				return false;
			}
		}				
		$this->sendMsg($msg);
		return true;
	}
	
	function rmdir($dir)
	{
		if(!$this->_ready)
		{
			$this->_error="Please login to FTP server first.";
			return false;
		}
		if(!$this->_put("RMD $dir"))
		{
			$this->_error="Failed to send command to FTP Server.";
			return false;
		}
		else
		{
			$msg=$this->_read();
			if(!$this->_checkReply())
			{
				$this->_error=$msg;
				return false;
			}
		}				
		$this->sendMsg($msg);
		return true;
	}

	function site($cmd="")
	{
		if(!$this->_ready)
		{
			$this->_error="Please login to FTP server first.";
			return false;
		}
		if(!$this->_put("SITE $cmd"))
		{
			$this->_error="Failed to send command to FTP Server.";
			return false;
		}
		else
		{
			$msg=$this->_read();
			if(!$this->_checkReply())
			{
				$this->_error=$msg;
				return false;
			}
		}				
		$this->sendMsg($msg);
		return true;
	}

	function features()
	{
		if(!$this->_ready)
		{
			$this->_error="Please login to FTP server first.";
			return false;
		}
		if(!$this->_put("FEAT"))
		{
			$this->_error="Failed to send command to FTP Server.";
			return false;
		}
		else
		{
			$msg=$this->_read();
			if(!$this->_checkReply())
			{
				$this->_error=$msg;
				return false;
			}
		}				
		$msg=preg_split("/".$this->NewLineCode[$this->OS_local]."/",$msg,-1,PREG_SPLIT_NO_EMPTY);
		$msg[0]=substr($msg[0],4);
		array_pop($msg);
		return implode($this->NewLineCode[$this->OS_local],$msg);
	}

	function mdtm($path)
	{
		if(!$this->_ready)
		{
			$this->_error="Please login to FTP server first.";
			return false;
		}
		if(!$this->_put("MDTM ".$path))
		{
			$this->_error="Failed to send command to FTP Server.";
			return false;
		}
		else
		{
			$msg=$this->_read();
			if(!$this->_checkReply())
			{
				$this->_error=$msg;
				return false;
			}
		}				
		$msg=trim(substr($msg,4));
		list($year,$month,$day,$hr,$min,$sec)=sscanf($msg,"%4d%2d%2d%2d%2d%2d");
		return mktime($hr,$min,$sec,$month,$day,$year);
	}

	function help($arg="")
	{
		if(!$this->_ready)
		{
			$this->_error="Please login to FTP server first.";
			return false;
		}
		if(!$this->_put("HELP $arg"))
		{
			$this->_error="Failed to send command to FTP Server.";
			return false;
		}
		else
		{
			$msg=$this->_read();
			if(!$this->_checkReply())
			{
				$this->_error=$msg;
				return false;
			}
		}				
		$msg=preg_split("/".$this->NewLineCode[$this->OS_local]."/",$msg,-1,PREG_SPLIT_NO_EMPTY);
		$msg[0]=substr($msg[0],4);
		array_pop($msg);
		return implode($this->NewLineCode[$this->OS_local],$msg);
	}
	
	function noop()
	{
		if(!$this->_ready)
		{
			$this->_error="Please login to FTP server first.";
			return false;
		}
		if(!$this->_put("NOOP"))
		{
			$this->_error="Failed to send command to FTP Server.";
			return false;
		}
		else
		{
			$msg=$this->_read();
			if(!$this->_checkReply())
			{
				$this->_error=$msg;
				return false;
			}
		}
		$this->sendMsg($msg);
		return true;				
	}
	
	
	function system()
	{
		if(!$this->_ready)
		{
			$this->_error="Please login to FTP server first.";
			return false;
		}
		if(!$this->_put("SYST"))
		{
			$this->_error="Failed to send command to FTP Server.";
			return false;
		}
		else
		{
			$msg=$this->_read();
			if(!$this->_checkReply())
			{
				$this->_error=$msg;
				return false;
			}
		}				
		return substr(preg_replace("/\"/","" ,$msg ),4);
	}
	
	function pwd()
	{
		if(!$this->_ready)
		{
			$this->_error="Please login to FTP server first.";
			return false;
		}
		if(!$this->_put("PWD"))
		{
			$this->_error="Failed to send command to FTP Server.";
			return false;
		}
		else
		{
			$msg=$this->_read();
			if(!$this->_checkReply())
			{
				$this->_error=$msg;
				return false;
			}
		}				
		$this->sendMsg($msg);
		return substr(preg_replace("/\"/","" ,$msg ),4);
	}

	function rawlist($arg="",$path="")
	{
		if(!$this->_ready)
		{
			$this->_error="Please login to FTP server first.";
			return false;
		}
		
		$arg=($arg?" ".$arg:"").($path?" ".$path:"");
		return $this->_list($arg);
	}

	function namelist($arg="",$path="")
	{
		if(!$this->_ready)
		{
			$this->_error="Please login to FTP server first.";
			return false;
		}
		
		$arg=($arg?" ".$arg:"").($path?" ".$path:"");
		return $this->_list($arg,"NLST");
	}
	
	function cdup()
	{
		if(!$this->_ready)
		{
			$this->_error="Please login to FTP server first.";
			return false;
		}
		if(!$this->_put("CDUP"))
		{
			$this->_error="Failed to send command to FTP Server.";
			return false;
		}
		else
		{
			$msg=$this->_read();
			if(!$this->_checkReply())
			{
				$this->_error=$msg;
				return false;
			}
		}				
		$this->sendMsg("Directory successfuly changed to parent Directory.");
		return true;
	}

	function delete($file)
	{
		if(!$this->_ready)
		{
			$this->_error="Please login to FTP server first.";
			return false;
		}
		if(!$this->_put("DELE $file"))
		{
			$this->_error="Failed to send command to FTP Server.";
			return false;
		}
		else
		{
			$msg=$this->_read();
			if(!$this->_checkReply())
			{
				$this->_error=$msg;
				return false;
			}
		}				
		$this->sendMsg($msg);
		return true;
	}
	
	function abort()
	{
		if(!$this->_ready)
		{
			$this->_error="Please login to FTP server first.";
			return false;
		}
		if(!$this->_put("ABOR"))
		{
			$this->_error="Failed to send command to FTP Server.";
			return false;
		}
		else
		{
			$msg=$this->_read();
			if(!$this->_checkReply())
			{
				$this->_error=$msg;
				return false;
			}
		}				
		$this->sendMsg($msg);
		return true;
	}

	function rename($from,$to)
	{
		if(!$this->_ready)
		{
			$this->_error="Please login to FTP server first.";
			return false;
		}
		if(!$this->_put("RNFR $from"))
		{
			$this->_error="Failed to send command to FTP Server.";
			return false;
		}
		else
		{
			$msg=$this->_read();
			if(!$this->_checkReply())
			{
				$this->_error=$msg;
				return false;
			}
		}
		$this->sendMsg($msg);
		if($this->_code==350) 
		{
			if(!$this->_put("RNTO $to"))
			{
				$this->_error="Failed to send command to FTP Server.";
				return false;
			}
			else
			{
				$msg=$this->_read();
				if(!$this->_checkReply())
				{
					$this->_error=$msg;
					return false;
				}
			}
		}
		else return FALSE;

		$this->sendMsg($msg);
		return true;
	}
	
	function restart($point)
	{
		if(!$this->_ready)
		{
			$this->_error="Please login to FTP server first.";
			return false;
		}
		if(!$this->_put("REST $point"))
		{
			$this->_error="Failed to send command to FTP Server.";
			return false;
		}
		else
		{
			$msg=$this->_read();
			if(!$this->_checkReply())
			{
				$this->_error=$msg;
				return false;
			}
		}				
		$this->sendMsg($msg);
		return true;
	}
	
	function read($remoteFile,$localFile=NULL)
	{
		$fp=null;
		if(!$this->_ready)
		{
			$this->_error="Please login to FTP server first.";
			return false;
		}
		preg_match("/\..+/",$remoteFile,$matches);
		$ext=strtolower(substr($matches[0],1));
		if($this->_type==FTP_ASCII or ($this->_type==FTP_AUTOASCII and in_array($ext,$this->AutoAsciiExt)))
		 $mode=FTP_ASCII;
		else
		 $mode=FTP_BINARY;

		$mode=FTP_BINARY;
		$this->_mode($mode); 	

		if(!$this->_listen())
		{
			$this->sendMsg("Failed to listen to server.");
			return false;
		}
		if($localFile==true)
			$localFile=$remoteFile;
		if(@file_exists($localFile))
			$this->sendMsg("$localFile will be overwritten.");

		if(!empty($localFile))					
		{
			$fp=fopen($localFile,"w");
			if(!$fp)
			{
				$this->_error="Couldn't open $localFile to write.";
				return false;
			}
		}
		
		if(!$this->_put("RETR $remoteFile"))
		{
			$this->_error="Failed to send command to FTP Server.";
			@fclose($fp);
			$this->_data_close();
			return false;
		}
		$msg=$this->_read();
		if(!$this->_checkReply())
		{
			$this->_error=$msg;
			@fclose($fp);
			$this->_data_close();
			return false;
		}
		$this->sendMsg($msg);
		$out=$this->_read_data($mode,$fp);

		@fclose($fp);
		$this->_data_close();

		$msg=$this->_read();
		if(!$this->_checkReply())
		{
			$this->_error=$msg;
			return false;
		}
		$this->sendMsg($msg);
		$this->sendMsg("File successfuly read.");
		return $out;
	}

	function write($remoteFile=NULL,$localFile,$unique=false)
	{
		$fp=NULL;
		if(!$this->_ready)
		{
			$this->_error="Please login to FTP server first.";
			return false;
		}
		preg_match("/\..+/",$localFile,$matches);
		$ext=strtolower(substr($matches[0],1));
		if($this->_type==FTP_ASCII or ($this->_type==FTP_AUTOASCII and in_array($ext,$this->AutoAsciiExt)))
		 $mode=FTP_ASCII;
		else
		 $mode=FTP_BINARY;

		$this->_mode($mode); 	
		$this->restart(0);

		if(!$this->_listen())
		{
			$this->sendMsg("Failed to listen to server.");
			return false;
		}

		if(is_null($remoteFile))
			$remoteFile=$localFile;

		if(@file_exists($localFile))					
		{
			$fp=fopen($localFile,"r");
			if(!$fp)
			{
				$this->_error="Couldn't read $localFile.";
				$this->_data_close();
				return false;
			}
		}
		else
		{
			$this->_error="Couldn't find $localFile.";
			$this->_data_close();
			return false;
		}
		
		if($unique)
			$cmd="STOU";
		else
			$cmd="STOR";
		
		if(!$this->_put("$cmd $remoteFile"))
		{
			$this->_error="Failed to send command to FTP Server.";
			$this->_data_close();
			@fclose($fp);
			return false;
		}
		$msg=$this->_read();
		if(!$this->_checkReply())
		{
			$this->_error=$msg;
			$this->_data_close();
			@fclose($fp);
			return false;
		}
		$this->sendMsg($msg);
		
		if(!$this->_write_data($mode,$fp)) return false;
		
		@fclose($fp);
		$this->_data_close();
		
		if(($msg=$this->_read())===false) return false;
		if(!$this->_checkReply())
		{
			$this->_error=$msg;
			return false;
		}
		$this->sendMsg($msg);

		return true;
	}


	function quit()
	{
		if(!$this->_ready)
		{
			$this->_error="Please login to FTP server first.";
			return false;
		}
		if(!$this->_put("QUIT"))
		{
			$this->_error="Failed to send command to FTP Server.";
			return false;
		}
		else
		{
			$msg=$this->_read();
			if(!$this->_checkReply())
			{
				$this->_error=$msg;
				return false;
			}
		}				
		$this->_ready=false;
		$this->sendMsg("Successfuly logged out.");
		return true;
	}
	
	function setTimeOut($time)
	{
		$this->_timeout=$time;
		if(!is_null($this->conn))
		{
			if(!stream_set_timeout($this->_conn,$time))
			{
				$this->_error="Couldn't set timeout.";
				$this->close();
				return false;
			}
		}
		return true;	
	}
	function setUmask($mask)
	{
		@umask(decoct($mask));
		$this->sendMsg("UMASK $mask");
	}
	
	function sendMsg($message="")
	{
		if($this->Verbose)
			return $this->_message;
		else
				$this->_message.=$message.CRLF;
	}

	function setType($mode=FTP_AUTOASCII) 
	{
		if(!in_array($mode, $this->TransferMode)) 
		{
			$this->SendMSG("Wrong type");
			return FALSE;
		}
		$this->_type=$mode;
		$this->SendMSG("Transfer type: ".($this->_type==FTP_BINARY?"binary":($this->_type==FTP_ASCII?"ASCII":"auto ASCII") ) );
		return TRUE;
	}

	/// close the active connection
	function close($force=false)
	{
		if(!$this->quit() && !$force) return false;
		@fclose($this->_conn);
		$this->_conn=null;
		$this->_connected=false;
		$this->sendMsg("Bye.");
		return true;
	}
		
	/////Private Functions/////////			
	
	// This function is used to get response line from server
	function _get()
	{
		while(!feof($this->_conn))
		{
			$line.=@fgets($this->_conn);
			if(preg_match("/\\r?\\n$/i",$line))
				return(preg_replace("/\\r?\\n$/i","",$line));
		}
	}
	
	function _read()
	{
		$firstLine=true;
		$break=false;
		do{
			$line=$this->_get();
			if($firstLine)
			{
				$code=substr($line,0,3);
				$firstLine=false;
			}
			if(preg_match("/^($code )/",$line))
				$break=true;
			$msg.=$line.$this->NewLineCode[$this->OS_local];
		}while(!$break);
		$this->_code=$code;
		return $msg;
		//echo $this->_get();
	}

	////This functiuon is to send the command to server
	function _put($msg="")
	{
		$this->_lastaction=time();
		return fputs($this->_conn,"$msg\r\n");
	}
	function passive($pasv=NULL) 
	{
		if(is_null($pasv)) $this->_passive=!$this->_passive;
		else $this->_passive=$pasv;
		if(!$this->_port_available and !$this->_passive) {
			$this->SendMSG("Only passive connections available!");
			$this->_passive=TRUE;
			return FALSE;
		}
		$this->sendMSG("Passive mode ".($this->_passive?"on":"off"));
		return TRUE;
	}

	function _connect()
	{
		$this->sendMsg('Local OS : '.$this->OS_FullName[$this->OS_local]);
		$this->_conn=fsockopen($this->_host,$this->_port,$errno,$errstr,$this->_timeout);
		if(!$this->_conn)
		{
			$this->_error="Couldn't open a connection to ".$this->_host."@".$this->_port;
			$this->_error.="\nError($errno):$errstr";
			return false;
		}
		$this->_greet=$this->_read();
		$this->_connected=true;
		return TRUE;
	}
	function _login()
	{
		if(!$this->_connected)
		{
			$this->_error="Please connect to FTP server first.";
			return false;
		}
		if(!$this->_put("USER ".$this->_user))
		{
			$this->_error="Failed to send command to FTP Server.";
			return false;
		}
		else
		{
			$msg=$this->_read();
			if(!$this->_checkReply($msg))
			{
				$this->_error=$msg;
				return false;
			}
		}
		if(!$this->_put("PASS ".$this->_password))
		{
			$this->_error="Failed to send command to FTP Server.";
			return false;
		}
		else
		{
			$msg=$this->_read();
			if(!$this->_checkReply())
			{
				$this->_error=$msg;
				return false;
			}
		}
		$this->sendMsg("Login Correct.");
		$this->_ready=true;
		return true;
	}
	function _checkReply() 
	{
		return ($this->_code<400 && $this->_code>0);
	}
	function _mode($mode)
	{
		if($mode==FTP_BINARY) {
			if(!$this->_put("TYPE I")) return FALSE;
		} else {
		if(!$this->_put("TYPE A")) return FALSE;
		}
		$msg=$this->_read();
		$this->sendMsg($msg);
	}
	function _listen()
	{
		if($this->_port_available)
		{
			$this->_datahost=$_SERVER['REMOTE_ADDR'];
			if(($this->_dataconn=@socket_create(AF_INET,SOCK_STREAM,SOL_TCP))<0)
			{
				$this->sendMsg("Couldn't make sockets for port.");
				break;
			}
			@socket_set_option($this->_dataconn,1,SO_RCVTIMEO,array("sec"=>$this->_timeout, "usec"=>0));
			@socket_set_option($this->_dataconn,1,SO_SNDTIMEO,array("sec"=>$this->_timeout, "usec"=>0));
			
			if(($ret = @socket_bind($this->_dataconn ,$this->_datahost)) < 0) 
			{
				$this->sendMsg("Couldn't bind to local socket.");
			}
			if(($ret = @socket_listen($this->_dataconn)) < 0) 
			{
				$this->sendMsg("Couldn't listen to local socket.");
			}
			if(!@socket_getsockname($this->_dataconn,$this->_datahost, $this->_dataport))
			{
				$this->sendMsg("Couldn't get information to local socket.");
			}
			$port="PORT ".preg_replace("/\./",",",$this->_datahost).",".($this->_dataport>>8).",".($this->_dataport&0x00FF);

			if($this->_put($port))
			{
				$msg=$this->_read();
				if($this->_checkReply())
				{
					$this->sendMsg($port);
					return true;
				}
				else
					$this->sendMsg($msg);
			}
		}
		if(!$this->_put("PASV"))
		{
			$this->_error="Failed to send command to Server.";
			return false;
		}
		$msg=$this->_read();
		if(!$this->_checkReply())
		{
			$this->sendMsg($msg);
			return false;
		}
		preg_match("/(\d{1,3},){5}\d{1,3}/",$msg,$matches);
		$ip_port=preg_split("/,/",$matches[0]);
		$this->_datahost=$ip_port[0].".".$ip_port[1].".".$ip_port[2].".".$ip_port[3];
					$this->_dataport=(((int)$ip_port[4])<<8) + ((int)$ip_port[5]);
		$this->_dataconn=@fsockopen($this->_datahost,$this->_dataport,$errno,$errstr,$this->_timeout);
		if(!$this->_dataconn)
		{
			$this->_error="Couldn't open data connection to server.";
			return false;
		}
		$this->sendMsg($msg);
		$this->_passive=true;
		return true;
	}
	
	function _read_data($mode=FTP_ASCII,$fp=NULL)
	{
		if($this->_passive)	
		{
			$out="";
			if($mode!=FTP_BINARY) 
			{
				while (!feof($this->_dataconn)) 
				{
					$line.=fread($this->_dataconn, 4096);
					if(!preg_match("/".CRLF."$/", $line)) continue;
						$line=rtrim($line,CRLF).$NewLine;
					if(is_resource($fp))
					 $out+=fwrite($fp, $line, strlen($line));
					else $out.=$line;
					$line="";
				}
			} 
			else
			{
				while (!feof($this->_dataconn)) 
				{
					$block=fread($this->_dataconn, 4096);
					if(is_resource($fp))
					 $out+=fwrite($fp, $block, strlen($block));
					else
					 $out.=$block;
				}
			}
			return $out;
		}
		else
		{
			$out="";
			$ftp_temp_sock=socket_accept($this->_dataconn);
			if($this->_ftp_temp_sock===FALSE)
			{
				$this->_error= socket_strerror(socket_last_error($ftp_temp_sock));
				@socket_close($this->_dataconn);
				return FALSE;
			}
			if($mode!=FTP_BINARY) 
			{
				while(($tmp=socket_read($ftp_temp_sock, 8192,PHP_NORMAL_READ))!==false) 
				{
					$line.=$tmp;
					if(!preg_match("/".CRLF."$/", $line)) continue;
					$line=rtrim($line,CRLF).$NewLine;
					if(is_resource($fp)) $out+=fwrite($fp, $line, strlen($line));
					else $out.=$line;
					echo $line;
					$line="";
				}
			} 
			else 
			{
				while($block=@socket_read($ftp_temp_sock, 8192, PHP_BINARY_READ)) 
				{
					if(is_resource($fp)) $out+=fwrite($fp, $block, strlen($block));
					else $out.=$block;
				}
			}
			@socket_close($ftp_temp_sock);
			return $out;
		}
	}
	
	function _write_data($mode=FTP_ASCII,$fp=NULL)
	{	
		if($this->_passive)
		{
			$NewLine=$this->NewLineCode[$this->OS_local];
			if(is_resource($fp)) 
			{
				while(!feof($fp)) 
				{
					$line=fgets($fp, 4096);
					if($mode!=FTP_BINARY) $line=rtrim($line, CRLF).CRLF;
					do {
						if(($res=@fwrite($this->_dataconn, $line))===FALSE) 
						{
							$this->_error="Can't write to socket";
							return FALSE;
						}
						$line=substr($line, $res);
					}while($line!="");
				}
			} 
			else
			{
				if($mode!=FTP_BINARY) $fp=rtrim($fp, $NewLine).CRLF;
				do {
					if(($res=@fwrite($this->_dataconn, $fp))===FALSE) {
						$this->_error="Can't write to socket";
						return FALSE;
					}
					$fp=substr($fp,$res);
				}while($fp!="");
			}
			return TRUE;
		}
		else
		{
			$NewLine=$this->NewLineCode[$this->OS_local];
			$ftp_temp_sock=socket_accept($this->_dataconn);
			if($this->_ftp_temp_sock===FALSE)
			{
				$this->_error= socket_strerror(socket_last_error($ftp_temp_sock));
				@socket_close($this->_dataconn);
				return FALSE;
			}
			if(is_resource($fp)) 
			{
				while(!feof($fp)) 
				{
					$line=fgets($fp, 4096);
					if($mode!=FTP_BINARY) $line=rtrim($line, CRLF).CRLF;
					do {
						if(($res=@socket_write($ftp_temp_sock, $line))===FALSE) 
						{
							$this->_error="Can't write to socket.<br>Error:".socket_strerror(socket_last_error($this->_ftp_temp_sock));
							return FALSE;
						}
						$line=substr($line, $res);
					}while($line!="");
				}
			} 
			else
			{
				if($mode!=FTP_BINARY) $fp=rtrim($fp, $NewLine).CRLF;
				do {
					if(($res=@socket_write($ftp_temp_sock, $fp))===FALSE) 
					{
						$this->_error="Can't write to socket.<br>Error:".socket_strerror(socket_last_error($this->_ftp_temp_sock));
						return FALSE;
					}
					$fp=substr($fp,$res);
				}while($fp!="");
			}
			return TRUE;
		}
		@socket_close($ftp_temp_sock);
	}
	
	function _list($arg="",$cmd="LIST")
	{
		$mode=FTP_BINARY;
		$this->_mode($mode);
		if(!$this->_listen()) return false;
		if(!$this->_put($cmd.$arg))
		{
			$this->_error="Failed to send command to FTP Server.";
			return false;
		}
		$msg=$this->_read();
		if(!$this->_checkReply())
		{
			$this->_error=$msg;
			$this->_data_close();
			return false;
		}
		$this->sendMsg($msg);
		$out=$this->_read_data($mode);
		$out=preg_split("/[\r\n]+/", $out, -1, PREG_SPLIT_NO_EMPTY);
		$msg=$this->_read();
		$this->_data_close();
		if(!$this->_checkReply())
		{
			$this->_error=$msg;
			return false;
		}
		$this->sendMsg($msg);
		/*$msg=$this->_read();
		if(!$this->_checkReply())
		{
			$this->_error=$msg;
			return false;
		}
		$this->sendMsg($msg);*/
		return $out;		
	}
	function _data_close()
	{
		if($this->_passive)
			@fclose($this->_dataconn);
		else
			@socket_close($this->_dataconn);
	}
}

?>