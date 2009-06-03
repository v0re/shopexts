<?php 

set_time_limit(0);
ini_set("display_errors",true);
error_reporting(E_ALL^E_NOTICE);
header('Content-Type: text/html; charset=utf-8');

function printForm($str)
{
	
	$rdomain = "shop146024.p06.shopex.cn";
	$rdb_addr = "127.0.0.1";
	$rdb_name = "shop146024";
	$rdb_username = "shop146024_f";
	$rdb_password ="a9828197b4ee";

	$rftp_ip = "124.74.193.217";
	$rftp_port = "21";
	$rftp_username = "webmaster@fadhomme.cn";
	$rftp_password = "a9828197b4ee";
	$rftp_path = "/";

	echo <<<EOF
<HTML>
<HEAD>
<TITLE> ShopEx服务器转移工具 </TITLE>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</HEAD>

<BODY>
<h1>ShopEx服务器转移工具</h1>
<font color=red>{$str}</font>
<FORM METHOD=POST ACTION="">
<b>请填写源服务器信息</b><br>
<table><tr><td>
数据库用户名：</td><td><INPUT TYPE="text" NAME="sdbuser" size=30 value=""></td></tr>
<tr><td>
数据库密码：</td><td><INPUT TYPE="password" NAME="sdbpass" size=30 value=""></td></tr>
<input type="hidden" name="step" value=2>
<tr height=28><td colspan=2></td></tr>
<tr><td colspan=2>
<b>请填写目标服务器信息</b></td></tr>
<tr><td>
访问URL：</td><td><INPUT TYPE="text" NAME="domain" size=50 value="{$rdomain}">http://domain</td></tr>
<tr><td>
数据库服务器：</td><td><INPUT TYPE="text" NAME="dbserver" size=30 value="{$rdb_addr}"></td></tr>
<tr><td>
数据库名：</td><td><INPUT TYPE="text" NAME="dbname" size=30 value="$rdb_name"></td></tr>
<tr><td>
数据库用户名：</td><td><INPUT TYPE="text" NAME="dbuser" size=30 value="{$rdb_username}"></td></tr>
<tr><td>
数据库密码：</td><td><INPUT TYPE="password" NAME="dbpass" size=30 value="{$rdb_password}"></td></tr>
<tr><td>
FTP服务器：</td><td><INPUT TYPE="text" NAME="server" size=30 value="{$rftp_ip}"></td></tr>
<tr><td>
FTP端口：</td><td><INPUT TYPE="text" NAME="port" size=4 value="21" value="{$rftp_port}"></td></tr>
<tr><td>
FTP用户名：</td><td><INPUT TYPE="text" NAME="user" size=30 value="{$rftp_username}"></td></tr>
<tr><td>
FTP密码：</td><td><INPUT TYPE="password" NAME="pass" size=30 value="{$rftp_password}"></td></tr>
<tr><td>
FTP目标目录：</td><td><INPUT TYPE="text" NAME="remotepath" size=30 value="{$rftp_path}">格式示例:1. test 2.空  3. test/test</td></tr>
<tr><td colspan=2>
<input type="checkbox" name="log" value="y" checked>转移访问记录表数据
<input type="checkbox" name="bypass" value="y">跳过数据备份</td></tr>
</table>
<br>
<INPUT TYPE="submit" value='开始转移'>
</FORM>
</BODY>
</HTML>
EOF;
exit;
}


if($_REQUEST["step"]=='2')
{

	setcookie("POST_VARS",serialize($_POST));
	?>
	<HTML>
	<HEAD>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<TITLE> 正在迁移中，请勿关闭本窗口 </TITLE>

	</HEAD>

	<BODY style="font-size:12px;">

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




	$dirs_num = 0;
	$files_num= 0;
	$filefail_num= 0;
	$start = time();

	function vuptree($dir,$remote) {

		global $ftp,$dirs_num,$files_num,$filefail_num;
		$dirhandle=@opendir($dir);
		while($file_name=@readdir($dirhandle)){
				if ($file_name!="." && $file_name!=".."&& $file_name!="mall_config.php"){
				if (is_dir("$dir/$file_name")){
					@$ftp->mkdir($remote."/".$file_name);
					rptout("<font color=green>".$remote."/".$file_name."目录已建立</font>");
					if(!strstr($remote."/".$file_name,'home/cache/1')) //取消cache
						uptree($dir."/".$file_name,$remote."/".$file_name);
				}else{
					if(filesize($dir."/".$file_name)<5000000)
					{
						if($ftp->write($remote."/".$file_name, $dir."/".$file_name))
						{
							rptout($remote."/".$file_name." 成功上传");
							usleep(100);
						}
						else
						{
							rptout("<font color=red>".$remote."/".$file_name." 上传失败</font>");
						}
					}

				}
			}
		}
		@closedir($dirhandle);
	}

	//测试mall_config.php是否存在
	if(!file_exists('include/mall_config.php')) printForm('mall_config.php不存在');
	include_once('include/mall_config.php');
	//if($_POST['sdbuser']!=$dbUser||$_POST['sdbpass']!=$dbPass) printForm('源数据库或密码填写错误!');
	include_once('include/mysql_db.php');
	//检查FTP信息
	if($_POST['server']==""||$_POST['port']==""||$_POST['user']==""||$_POST['pass']=="")  printForm('FTP服务器信息不得为空');
	
	$ftp=new FTP(); 
	if(!$ftp->connect($_POST['server'],$_POST['port']))		 die($ftp->error());
	echo nl2br($ftp->greet());
	$ftp->login($_POST['user'], $_POST['pass']);
	echo "<br>";
	echo $ftp->error();
	echo "<br>";
	echo nl2br($ftp->features());
	$ftp->setType('FTP_BINARY');
	echo "<br>Connect Ok<br>";



	//开始备份mysql库
	$filename = md5($dbPass);
	if($_POST['bypass']!='y')
	{
		$sizelimit = 1024;
		$fileid = 0;
		$tableid = 0;
		$startid = -1;
		rptout("开始数据库备份...\n");
		$dumper = new Mysqldumper($dbHost, $dbUser, $dbPass, $dbName);
		$dumper->setDroptables(true);
		$finished = false;
		while(!$finished){
			$dumper->tableid = $tableid;
			$dumper->startid = $startid;
			if($_POST['log']=='y')
				$dumper->nodata = array();
			else
				$dumper->nodata = array('stream');

			$finished = $dumper->multiDump($filename,$fileid,$savetype,$sizelimit);
			$fileid++;
			rptout("数据库备份文件 mig_".$filename."_".$fileid.".sql 已生成\n");
			$tableid = $dumper->tableid;
			$startid = $dumper->startid;
		}
		rptout("数据库备份完毕\n");
	}

	$cont = "<"."?php
	\$dbHost = \"".$_POST["dbserver"]."\";
	\$dbName = \"".$_POST["dbname"]."\";
	\$dbUser = \"".$_POST["dbuser"]."\";
	\$dbPass = \"".$_POST["dbpass"]."\";
	\$SITE_EODING = \"UTF-8\";
	\$BACKEND_LANG = \"".$BACKEND_LANG."\";
	\$portNumber = \"80\";
	\$_tbpre = \"".$_tbpre."\";
	error_reporting(E_ALL^E_NOTICE);
	define(\"MYSQL_CHARSET_NAME\",\"utf8\");
	?".">";

	$fp = fopen("include/mall_config.tmp.php","w");
	@fwrite($fp,$cont);
	@fclose($fp);
	@chmod("include/mall_config.tmp.php",0666);

	$_POST['localpath'] = '.';
	if(trim($_POST['localpath'])=="") exit('local path cannot be empty!');

	if(is_dir($_POST['localpath']))
	{
		if(trim($_POST['remotepath'])!="")
		{
			@$ftp->mkdir($_POST['remotepath']);
			rptout("<font color=green>".$_POST['remotepath']."目录已建立</font>");
		}
		else
		{
			$_POST['remotepath'] = ".";
		}

		$fp_list = fopen('migtask.txt','w');
		uptree($_POST['localpath'],$_POST['remotepath']);
		fclose($fp_list);
	}

	?>
	<script language="JavaScript">
	<!--
		location= 'mig.php?step=3&startline=1';
	//-->
	</script>
	</BODY>
	</HTML>
	<?php

}
elseif($_REQUEST["step"]=='3')   //恢复数据
{
	?>
	<HTML>
	<HEAD>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<TITLE> 正在迁移中，请勿关闭本窗口 </TITLE>

	</HEAD>

	<BODY style="font-size:12px;">
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
	$batch = 3000;  //每一批次处理行数
	$startline = $_GET['startline'];  //起始行

	$_POST = unserialize(stripslashes($_COOKIE['POST_VARS']));

	rptout("开始文件传输...\n");



	//ftp_pasv($conn_id, true);


	$ftp=new FTP(); 
	if(!$ftp->connect($_POST['server'],$_POST['port']))		 die($ftp->error());
	echo nl2br($ftp->greet());
	$ftp->login($_POST['user'], $_POST['pass']);
	echo "<br>";
	echo $ftp->error();
	echo "<br>";
	echo nl2br($ftp->features());
	$ftp->setType('FTP_BINARY');
	echo "<br>Connect Ok<br>";


	$fp_list = fopen('migtask.txt','r');
	for($i=1;$i<$startline;$i++) fgetline($fp_list);  //跳至起始行
	$session_count = 0;
	while ($buffer = fgetline($fp_list))
	{
		$startline++;
		$session_count++;
		if(substr($buffer,0,4)=='DIR:')
		{
			@$ftp->mkdir ($_POST['remotepath'].substr($buffer,5));
			rptout("<font color=green>".$_POST['remotepath'].substr($buffer,5)."目录已建立</font>");
			$dirs_num++;
		}
		if(substr($buffer,0,4)=='FIL:')
		{
					if(filesize(substr($buffer,4))<5000000)
					{
						if(@$ftp->write ($_POST['remotepath'].substr($buffer,5), substr($buffer,4)))
						{
							rptout($_POST['remotepath'].substr($buffer,5)." 成功上传");
							usleep(10000);
							$files_num++;
						}
						else
						{
							$theline = $startline-1;
							rptout("<font color=red>".$_POST['remotepath'].substr($buffer,5)." 上传失败 line:{$theline}</font>");
							echo "<br><a href=\"mig.php?step=3&startline={$theline}\">点击重试</a>";
							exit;
							$filefail_num++;
						}
					}
		}
		if($session_count>=3000)
		{
			?>
			<script language="JavaScript">
			<!--
				location= 'mig.php?step=3&startline=<?php echo $startline?>';
			//-->
			</script>
			</BODY>
			</HTML>
			<?php
			fclose($fp_list);
			$ftp->close();
			exit;
		}
	}
	fclose($fp_list);

	rptout("文件上传完毕");

	unlink('include/mall_config.tmp.php');
	unlink('migtask.txt');
	$dirhandle=@opendir('databack');
	while($file_name=@readdir($dirhandle))
		if (substr($file_name,0,4)=='mig_') unlink("databack/{$file_name}");
	$filename = md5($_POST['sdbpass']);

	//文件上传完毕,开始恢复数据


	?>
	<script language="JavaScript">
	<!--
		location= '<?php echo $_POST['domain']?>/mig.php?step=4&filename=<?php echo $filename?>';
	//-->
	</script>
	</BODY>
	</HTML>
	<?php

}
elseif($_REQUEST["step"]=='4')   //恢复数据
{
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
	rptout("正在恢复数据中...\n");
	if(file_exists(('include/mall_config.php')))
	{
		rptout("数据已执行过恢复,如需重新执行请删除include/mall_config.php\n");
		exit;
		rptout("您已恢复过数据!\n");
		exit;
	}
	copy('include/mall_config.tmp.php','include/mall_config.php');
	include("include/mall_config.php");
	$fileid = 1;
	$link = mysql_connect($dbHost, $dbUser, $dbPass,true)
					or die("Could not connect : " . mysql_error($link)); 
	while(true)
	{
		$bakfile = "databack/mig_".$_REQUEST["filename"]."_".$fileid.".sql";

		if(!file_exists($bakfile))
		{
			rptout("数据库恢复完毕!所有迁移完成");
			break;
		}

		mysql_select_db($dbName,$link) or die("Could not select database");
		if(mysql_get_server_info()>'5.0.1') mysql_query("SET sql_mode=''",$link);
		if(defined("MYSQL_CHARSET_NAME"))
					mysql_query("SET NAMES '".MYSQL_CHARSET_NAME."'",$link);
		$fp = fopen($bakfile, "r");
		$i = 0;
		while (($buffer = fgetline($fp))!==false)
		{
				if (trim($buffer) != "" && substr($buffer, 0, 1) != "#")
				{
					$buffer = trim($buffer);
					if(substr(trim($buffer),-1)==";") $buffer = substr(trim($buffer),0,strlen($buffer)-1);

					//替换前缀定义
					$buffer = str_replace('{shopexdump_table_prefix}',$GLOBALS['_tbpre'],$buffer);
					if(defined("MYSQL_CHARSET_NAME"))
						$buffer = str_replace('{shopexdump_create_specification}',' DEFAULT CHARACTER SET '.MYSQL_CHARSET_NAME,$buffer);
					else
						$buffer = str_replace('{shopexdump_create_specification}','',$buffer);

					if(!mysql_query($buffer,$link)) echo mysql_error($link)."<br>";
					usleep(5);
					$i++;
				}
		}
		fclose($fp);
		rptout("数据文件".$bakfile."恢复成功!\n");

		$fileid++;
	}
	mysql_close($link);
	unlink('include/mall_config.tmp.php');
	unlink('migtask.txt');
	unlink('mig.php');
	$dirhandle=@opendir('databack');
	while($file_name=@readdir($dirhandle))
		if (substr($file_name,0,4)=='mig_') unlink("databack/{$file_name}");
}
else
{
	printForm('');
}




//mysqldump

class Mysqldumper {
	var $_host;
	var $_dbuser;
	var $_dbpassword;
	var $_dbname;
	var $_isDroptables;
	var $tableid;	//数据表ID
	var $startid;
	var $tablearr;
	var $nodata;

	
	function Mysqldumper($host = "localhost", $dbuser = "", $dbpassword = "", $dbname = "") {
		$this->setHost($host);
		$this->setDBuser($dbuser);
		$this->setDBpassword($dbpassword);
		$this->setDBname($dbname);
		// Don't drop tables by default.
		$this->setDroptables(false);
	}
	
	function setHost($host) {
		$this->_host = $host;
	}
	
	function getHost() {
		return $this->_host;
	}
	
	function setDBname($dbname) {
		$this->_dbname = $dbname;
	}
	
	function getDBname() {
		return $this->_dbname;
	}
	
	function getDBuser() {
		return $this->_dbuser;
	}
	
	function setDBpassword($dbpassword) {
		$this->_dbpassword = $dbpassword;
	}
	
	function getDBpassword() {
		return $this->_dbpassword;
	}
	
	function setDBuser($dbuser) {
		$this->_dbuser = $dbuser;
	}
	
	// If set to true, it will generate 'DROP TABLE IF EXISTS'-statements for each table.
	function setDroptables($state) {
		$this->_isDroptables = $state;
	}
	
	function isDroptables() {
		return $this->_isDroptables;
	}
	


	function multiDump($filename,$fileid,$savetype,$sizelimit) {
		// Set line feed
		$ret = true;
		$lf = "\r\n";
		$lencount = 0;
		$bakfile = "databack/mig_".$filename."_".($fileid+1).".sql";

		$fw = @fopen($bakfile, "wb");
		if(!$fw) exit("export file write failed");
		$resource = mysql_connect($this->getHost(), $this->getDBuser(), $this->getDBpassword(),true);
		mysql_select_db($this->getDbname(), $resource);
		if(defined("MYSQL_CHARSET_NAME"))
				mysql_query("SET NAMES '".MYSQL_CHARSET_NAME."'",$resource);
		$result = mysql_query("SHOW TABLES");
		$tables = $this->result2Array(0, $result);
		$this->tablearr = $tables;

		// Set header
		fwrite($fw, "#". $lf);
		fwrite($fw,  "# SHOPEX SQL MultiVolumn Dump ID:".($fileid+1) . $lf);
		fwrite($fw,  "# Version ". $GLOBALS['SHOPEX_THIS_VERSION']. $lf);
		fwrite($fw,  "# ". $lf);
		fwrite($fw,  "# Host: " . $this->getHost() . $lf);
		fwrite($fw,  "# Server version: ". mysql_get_server_info() . $lf);
		fwrite($fw,  "# PHP Version: " . phpversion() . $lf);
		fwrite($fw,  "# Database : `" . $this->getDBname() . "`" . $lf);
		fwrite($fw,  "#");

		// Generate dumptext for the tables.
		$i=0;
		for($j=$this->tableid;$j<count($this->tablearr);$j++){
			$tblval = $this->tablearr[$j];
			$table_prefix = (isset($GLOBALS['_tbpre']))?$GLOBALS['_tbpre']:'';
			$written_tbname = '{shopexdump_table_prefix}'.substr($tblval,strlen($table_prefix));
			if($this->startid ==-1)
			{
				fwrite($fw,  $lf . $lf . "# --------------------------------------------------------" . $lf . $lf);
				$lencount += strlen($lf . $lf . "# --------------------------------------------------------" . $lf . $lf);
				fwrite($fw,  "#". $lf . "# Table structure for table `$tblval`" . $lf);
				$lencount += strlen("#". $lf . "# Table structure for table `$tblval`" . $lf);
				fwrite($fw,  "#" . $lf . $lf);
				$lencount += strlen("#". $lf . "# Table structure for table `$tblval`" . $lf);
				// Generate DROP TABLE statement when client wants it to.
				mysql_query("ALTER TABLE `$tblval` comment ''");
				if($this->isDroptables()) {
					fwrite($fw,  "DROP TABLE IF EXISTS `$written_tbname`;" . $lf);
					$lencount += strlen("DROP TABLE IF EXISTS `$written_tbname`;" . $lf);
				}
				$result = mysql_query("SHOW CREATE TABLE `$tblval`");
				$createtable = $this->result2Array(1, $result);
				$tmp_value = str_replace("\n", '', $this->formatcreate($createtable[0]));
				$pos = strpos($tmp_value,$tblval);
				$tmp_value = substr($tmp_value,0,$pos).$written_tbname.substr($tmp_value,$pos+strlen($tblval));
				fwrite($fw,  $tmp_value. $lf.$lf);
				$lencount += strlen($tmp_value. $lf.$lf);
				$this->startid = 0;
			}
			if($lencount>$sizelimit*1000)
			{
				$this->tableid = $j;
				$this->startid = 0;
				$ret = false;
				break;
			}
			fwrite($fw,  "#". $lf . "# Dumping data for table `$tblval`". $lf . "#" . $lf);
			$lencount += strlen("#". $lf . "# Dumping data for table `$tblval`". $lf . "#" . $lf);
			$tbr_name = substr($tblval,strlen($table_prefix));
			if(in_array($tbr_name,$this->nodata))
				$result = mysql_query("SELECT * FROM `$tblval` where 1<>1");
			else
				$result = mysql_query("SELECT * FROM `$tblval`");
			if(!@mysql_data_seek($result,$this->startid))
			{
				$this->startid = -1;
				continue;
			}

			while ($row = mysql_fetch_object($result)) {
					$insertdump = $lf;
					$insertdump .= "INSERT INTO `$written_tbname` VALUES (";
					$arr = $this->object2Array($row);

					foreach($arr as $key => $value) {
						if(!is_null($value))
						{
							$value = $this->utftrim(mysql_escape_string($value));
							$insertdump .= "'$value',";
						}
						else
							$insertdump .= "NULL,";
					}
					$insertline = rtrim($insertdump,',') . ");";
					fwrite($fw,  $insertline);
					usleep(5);
					$lencount += strlen($insertline);
					$this->startid++;
					if($lencount>$sizelimit*1000)
					{
						$ret = false;
						$this->tableid = $j;
						break 2;
					}
			}
			$this->startid = -1;
			$i++;
//			if ($i== 5) break;
		}
		mysql_close($resource);
		fclose($fw);
		chmod($bakfile, 0666);
		return $ret;
	}


	// Private function object2Array.
	function object2Array($obj) {
		$array = null;
		if(is_object($obj)) {
			$array = array();
			foreach (get_object_vars($obj) as $key => $value) {
				if(is_object($value))
					$array[$key] = $this->object2Array($value);
				else
					$array[$key] = $value;
			}
		}
		return $array;
	}
	
	// Private function loadObjectList.
	function loadObjectList($key='', $resource) {
		$array = array();
		while ($row = mysql_fetch_object($resource)) {
			if ($key)
				$array[$row->$key] = $row;
			else
				$array[] = $row;
		}
		mysql_free_result($resource);
		return $array;
	}
	
	// Private function result2Array.
	function result2Array($numinarray = 0, $resource) {
		$array = array();
		while ($row = mysql_fetch_row($resource)) {
			$array[] = $row[$numinarray];
		}
		mysql_free_result($resource);
		return $array;
	}

	function formatcreate($str)
	{
			return substr($str,0,strrpos($str,")")+1)." TYPE=MyISAM{shopexdump_create_specification};";
	}

	//截最后一个是否是半个UTF-8中文
	function utftrim($str)
	{
		$found = false;
		for($i=0;$i<4&&$i<strlen($str);$i++)
		{
			$ord = ord(substr($str,strlen($str)-$i-1,1));
			//UTF-8中文分{四/三/二字节码},第一位分别为11110xxx(>192),1110xxxx(>192),110xxxxx(>192);接下去的位数都是10xxxxxx(<192)
			//其他ASCII码都是0xxxxxxx
			if($ord> 192)
			{
				$found = true;
				break;
			}
			if ($i==0 && $ord < 128){
				break;
			}
		}
		
		if($found)
		{
			if($ord>240)
			{
				if($i==3) return $str;
				else return substr($str,0,strlen($str)-$i-1);
			}
			elseif($ord>224)
			{
				if($i>=2) return $str;
				else return substr($str,0,strlen($str)-$i-1);
			}
			else
			{
				if($i>=1) return $str;
				else return substr($str,0,strlen($str)-$i-1);
			}
		}
		else return $str;
	}
}
function rptout($str)
{
	echo $str."<br><script>s();</script>";
	flush();
}

function fgetline($handle)
{
	$buffer = fgets($handle, 4096);
	if (!$buffer)
	{
		return false;
	}

	if(( 4095 > strlen($buffer)) || ( 4095 == strlen($buffer) && '\n' == $buffer{4094} ))
	{
		$line = $buffer;
	}
	else
	{
		$line = $buffer;
		while( 4095 == strlen($buffer) && '\n' != $buffer{4094} )
		{
			$buffer = fgets($handle,4096);
			$line.=$buffer;
		}
	}
	return trim($line);
}


function uptree($dir,$remote) {

    global $fp_list,$dirs_num,$files_num,$filefail_num;
	$dirhandle=@opendir($dir);
	while($file_name=@readdir($dirhandle)){
        	if ($file_name!="." && $file_name!=".."&& $file_name!="mall_config.php"){
			if (is_dir("$dir/$file_name")){
				fwrite($fp_list,"DIR:$dir/$file_name\n");
				$dirs_num++;
				//rptout("<font color=green>".$remote."/".$file_name."目录已建立</font>");
				if(!strstr($remote."/".$file_name,'home/cache/1')) //取消cache
					uptree($dir."/".$file_name,$remote."/".$file_name);
			}else{
				if(filesize($dir."/".$file_name)<5000000)
				{
					fwrite($fp_list,"FIL:$dir/$file_name\n");
				}

			}
		}
	}
	@closedir($dirhandle);
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