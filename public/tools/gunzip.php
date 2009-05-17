<?php


$fhost = "127.0.0.1";
$fport = "21";
$fuser = $_POST['user'];
$fpass = $_POST['pass'];
$fdir = "/htdocs";

HtmlHead("选择解压文件:") ;

if ( !IsSet($HTTP_POST_VARS['submit']) )
{
	$gzip_info = "" ;
	echo "check zlib support... " ;
	if ( !function_exists("gzopen") )
	{
		$gzip_info = "<font color=\"red\">注意! 您的空间没有zlib支持，因此用
		<a href=\"http://www.isphp.net/\" target=\"_blank\"><font color=\"blue\">phpZip</font></a> 
		压缩文件时，不要选择“压缩成Gzip格式”，否则将无法正确解压!</font>" ;
	}
	else
	{
		$gzip_info = "<font color=\"blue\">恭喜! 您的空间支持zlib压缩，强烈建议用 
		<a href=\"http://www.isphp.net/\"><font color=\"red\" target=\"_blank\">phpZip</font></a> 
		压缩文件时，选中“压缩成Gzip格式”，将会大大减少文件大小!</font>" ;
	}
	echo " ----------------- OK!<br>\n" . $gzip_info ;
	
	echo "<br><br><br><br>
<form action=\"{$_SERVER["PHP_SELF"]}\" method=\"post\" enctype=\"multipart/form-data\">
<table align=\"center\" width=\"450\">
<tr><td height=\"20\" colspan=\"2\">请先选择压缩文件的位置，然后点击“确定”按钮： <p></td></tr>


<tr><td colspan=2 height=10></td></tr>

<tr>
<td>指定服务器上文件：</td>
<td><input name=\"server_filename\" value=\"data.dat.gz\" style=\"color:#0000ff\">(可以用\".\"表示当前目录)</td>
<td>FTP用户名：</td>
<td><input name=\"user\" value=\"\" style=\"color:#0000ff\"></td>
<td>FTP密码：</td>
<td><input name=\"pass\" value=\"\" style=\"color:#0000ff\"></td>
</tr>

<tr><td colspan=\"2\" align=center><br><input type=\"submit\" name=\"submit\" value=\"确定\"></td></tr>
</table>
</form>
" ;
	HtmlFoot() ;
	exit ;
}
?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function s()
{
	if(document.body.scrollHeight>document.body.clientHeight-30)
	{
		scroll(0,document.body.scrollHeight-document.body.clientHeight+30);
	}
}
//-->
</SCRIPT>
<?php
function rptout($str)
{
	echo $str."<br><script>s();</script>";
	flush();
}

function doread($handle,$length)
{
	if ( function_exists("gzopen") && is_callable('gzopen') ){
		$ret = gzread($handle, $length);
	}
	else{
		$ret = fread($handle, $length);
	}
	return $ret;
}

$tmpfile = basename($_POST['server_filename']) ;




if ( !$tmpfile )
{
	exit("无效的文件或文件不存在,可能原因有文件大小太大，上传失败或没有指定服务器端文件等") ;	
}

$bgzExist = FALSE ;
if ( function_exists("gzopen") )
{
	$bgzExist = TRUE ;
}

if(function_exists('ftp_connect')){
	$conn_id = ftp_connect($fhost,$fport) or die("Couldn't connect to ".$fhost."@".$fport);	
	if (@ftp_login($conn_id, $fuser, $fpass)) {
		rptout("用户".$fuser."@".$fpass."已连接服务器\n");
	} else {
		rptout("用户 ".$fuser."无法连接服务器\n");
		exit;
	}	
	ftp_chdir($conn_id,$fdir);
}else{
	$ftp=new FTP(); 
	if(!$ftp->connect($fhost,$fport))		 die($ftp->error());
	echo nl2br($ftp->greet());
	$ftp->login($fuser,$fpass);
	echo "<br>";
	echo $ftp->error();
	echo "<br>";
	echo nl2br($ftp->features());
	$ftp->chdir($fdir);
	$ftp->setType('FTP_BINARY');
	echo "<br>Connect Ok<br>";
}

$alldata = "" ;
$pos = 0 ;

$gzp = $bgzExist ? @gzopen($tmpfile, "rb") : @fopen($tmpfile, "rb") ;


$nFileCount = doread($gzp, 16) ;
$pos += 16 ;

$size = doread($gzp, 16) ;
$pos += 16 ;

echo "<hr>";
var_dump($gzp);
var_dump($size);
echo "<hr>";

$info = doread($gzp, $size-1) ;		// strip the last '\n'

$pos += $size ;


$info_array = explode("\n", $info) ;

$c_file = 0 ;
$c_dir = 0 ;
 
 doread($gzp, 1) ;

foreach ($info_array as $str_row)
{
	list($filename, $attr) = explode("|", $str_row);
	if ( substr($attr,0,6) == "[/dir]" )
	{
		echo "End of dir $filename<br>";
		continue;
	}
	
	if ( substr($attr,0,5)=="[dir]" )
	{
		$state = function_exists('ftp_mkdir')?@ftp_mkdir ($conn_id, $filename):@$ftp->mkdir($filename);
		if ($state)	 rptout("Make dir $filename");
		$c_dir++ ;
	}
	else
	{
		$fp = @fopen('upfile_tmp.txt', "wb") or exit("不能新建文件 $filename ,因为没有写权限，请修改权限");
		@fwrite($fp, doread($gzp,$attr) );
		$pos += $attr ;
		fclose($fp);
		echo "Create file $filename<br>";
		$state = function_exists('ftp_put')?@ftp_put($conn_id,$filename,'upfile_tmp.txt' , FTP_BINARY):$ftp->write($filename,'upfile_tmp.txt');
		if($state){
					rptout($filename." 成功上传");
					$files_num++;
		}
		else
		{
					rptout("<font color=red>".$filename." 上传失败</font>");
					$filefail_num++;
		}

		$c_file++ ;
	}
}
$bgzExist ? @gzclose($gzp) : @fclose($gzp) ;

function_exists('ftp_close')?@ftp_close($conn_id):$ftp->close();



echo "<h1>操作完毕! 共解出文件 $c_file 个， 文件夹 $c_dir 个，谢谢使用!</h1><p>" ;
HtmlFoot() ;




function HtmlHead($title="", $css_file="")
{
	echo "<html>\n"
		. "\n"
		. "<head>\n"
		. "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=gb2312\">\n"
		. "<title>$title</title>\n"
		. "<style type=\"text/css\">\n"
		. "body,pre,td {font-size:12px; background-color:#fcfcfc; font-family:Tahoma,verdana,Arial}\n"
		. "input,textarea{font-size:12px; background-color:#f0f0f0; font-family:Tahoma,verdana,Arial}\n"
		. "</style>\n"
		. "</head>\n"
		. "\n"
		. "<body>\n" ;
}

function HtmlFoot()
{
	echo "<center><font size=\"5\" face=\"楷体_GB2312\" color=\"red\">使用完请立即删除本文件，以避免被其它人发现使用!</font></center>\n"
		. "<br><hr color=\"#003388\">\n"
		. "<center>\n"
		. "<p style=\"font-family:verdana; font-size:12px\">Contact us: \n"
		. "<a href=\"http://www.isphp.net/\" target=\"_blank\">http://www.isphp.net/</a></p>\n"
		. "</center>\n"
		. "</body>\n"
		. "\n"
		. "</html>" ;
}











		/*
 			FTP 
			Date - Feb 14,2005
			Author - Harish Chauhan
			Email - harishc@ultraglobal.biz

			ABOUT
			This PHP script will use for connect ftp server . You can 
			upload ,download from this class.this class has implemented 
			most of FTP Commands.
		*/


		if(!defined('CRLF')) define('CRLF',"\r\n");
		if(!defined("FTP_AUTOASCII")) define("FTP_AUTOASCII", -1);
		if(!defined("FTP_BINARY")) define("FTP_BINARY", 1);
		if(!defined("FTP_ASCII")) define("FTP_ASCII", 0);
		if(!defined('FTP_FORCE')) define('FTP_FORCE', TRUE);
		define('FTP_OS_Unix','u');
		define('FTP_OS_Windows','w');
		define('FTP_OS_Mac','m');


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
				$out=preg_split("/[".CRLF."]+/", $out, -1, PREG_SPLIT_NO_EMPTY);
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