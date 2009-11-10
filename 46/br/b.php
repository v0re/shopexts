<?php 

set_time_limit(0);

	echo <<<EOF
<HTML>
<HEAD>
<TITLE> 商品表备份恢复工具 </TITLE>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
</HEAD>
<BODY>
<FORM>
<table>
<tr><td>Backup</td><td><input type=radio name=action value="backup" checked></td></tr>
<tr height=18><td colspan=2></td></tr>
<tr><td colspan=2><INPUT TYPE="submit" value='submit'></td></tr>
</table>
</FORM>
<br><hr><br>
EOF;

//测试mall_config.php是否存在
if(!file_exists('../include/mall_config.php')) rptout('mall_config.php不存在');
include_once('../include/mall_config.php');
include_once('../include/mysql_db.php');

$link = mysql_connect($dbHost, $dbUser, $dbPass,true)		or die("Could not connect : " . mysql_error($link)); 
mysql_select_db($dbName,$link) or die("Could not select database");
if(mysql_get_server_info() > '5.0.1') mysql_query("SET sql_mode=''",$link);
if(defined("MYSQL_CHARSET_NAME"))	mysql_query("SET NAMES '".MYSQL_CHARSET_NAME."'",$link);

if($_REQUEST["action"] == 'backup')
{

	$sql = "SELECT bn FROM {$GLOBALS['_tbpre']}mall_goods GROUP BY bn HAVING (count( * )) >1";
	$rs = mysql_query($sql);
	if(mysql_num_rows($rs) > 0){
		rptout("下面商品编号不唯一<ol>");
		while($row = mysql_fetch_array($rs)){
			if($row['bn'] == '') continue;
			rptout("<li>".$row['bn']);
		}
		rptout("</ol><br>下面的商品编号为空<ol>");
		$sql = "SELECT gid,goods from {$GLOBALS['_tbpre']}mall_goods where bn=''";
		$rs = mysql_query($sql);
		while($row = mysql_fetch_array($rs)){
			rptout("<li>商品id是".$row['gid']);
		}
		rptout("</ol><hr>");
		rptout("<font color=red>请登陆网店后台重新设置商品编号，只有商品编号唯一的情况下才能进行下一步操作</font>");
		die();
	}

	//开始备份mysql库
	$filename = md5($dbPass);

	$sizelimit = 1024;
	$fileid = 0;
	$tableid = 0;
	$startid = -1;
	rptout("开始数据库备份...\n");
	$dumper = new Mysqldumper($dbHost, $dbUser, $dbPass, $dbName);
	$dumper->setDroptables(true);
	$dumper->nodata = array();
	$finished = false;
	while(!$finished){
		$dumper->tableid = $tableid;
		$dumper->startid = $startid;
		$finished = $dumper->multiDump($filename,$fileid,$savetype,$sizelimit);
		$fileid++;
		rptout("数据库备份文件 data_".$fileid.".sql 已生成\n");
		$tableid = $dumper->tableid;
		$startid = $dumper->startid;
	}
	rptout("<hr><font color=green>数据库备份完毕</font>\n");
}


if($_REQUEST["action"] == 'restore')   //恢复数据
{
	rptout("备份本地商品表....\n");
	$sql = "DROP TABLE IF EXISTS {$GLOBALS['_tbpre']}mall_goods_bak";
	mysql_query($sql);
	$sql = "Alter  TABLE {$GLOBALS['_tbpre']}mall_goods RENAME TO {$GLOBALS['_tbpre']}mall_goods_bak";
	mysql_query($sql);
	
	$fileid = 1;
	while(true)
	{
		$bakfile = "databack/data_".$fileid.".sql";
		if(!file_exists($bakfile)){
			break;
		}
		$fp = fopen($bakfile, "r");
		$i = 0;
		while (fgetline($fp,$buffer)!==false)
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
		rptout("导入数据文件".$bakfile."成功!\n");

		$fileid++;
	}
	rptout("同步订单中的商品id....\n<hr>");
	$aMap = getMap();
	if($syncnum = count($aMap) > 0 ){
		//$aMap 是$old_gid=>$new_gid
		foreach($aMap as $key=>$value){
			rptout("同步 $value 为 $key");
			$sql .= "update {$GLOBALS['_tbpre']}mall_items set gid='".$value."' where gid='".$key."';";
		}
		mysql_query($sql);
	}else{
		rptout("没有需要同步的商品");
	}
	mysql_close($link);
	rptout("<hr><font color=green>全部工作结束</font>");
}



function rptout($str)
{
	echo $str."<br><script>s();</script>";
	flush();
}

function fgetline($handle,&$line)
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


function getMap(){
	//获取源数据表bn->gid数组
	$sql = "select gid,bn from sdb_mall_goods";
	$rs = mysql_query($sql);
	while($row = mysql_fetch_array($rs)){
		$new[$row['bn'] ] = $row['gid'];
	}
	//获取当前数据表bn->gid数组
	$sql = "select gid,bn from sdb_mall_goods_bak";
	$rs = mysql_query($sql);
	//根据bn，保存old_gid和new_gid的映射关系
	while($row = mysql_fetch_array($rs)){
		if($new[$row['bn']] != '' && $row['gid'] != $new[$row['bn']]){
			$rnt[$row['gid']] = $new[$row['bn']]; 
		}
	}

	return $rnt;
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
		$bakfile = "databack/data_".($fileid+1).".sql";

		$fw = @fopen($bakfile, "wb+");
		if(!$fw) exit("export file write failed");
		$resource = mysql_connect($this->getHost(), $this->getDBuser(), $this->getDBpassword(),true);
		mysql_select_db($this->getDbname(), $resource);
		if(defined("MYSQL_CHARSET_NAME")) 	mysql_query("SET NAMES '".MYSQL_CHARSET_NAME."'",$resource);
		$result = mysql_query("SHOW TABLES");
		$i = 0;
		//get goods relative tables
		$tables = $this->result2Array(0, $result);
		foreach($tables as $tb_val){ 
	    if(strstr($tb_val,"goods") or strstr($tb_val,"pcat") or strstr($tb_val,"brand")){ 
				$this->tablearr[$i++] = $tb_val; 
      } 
    } 

		
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
			$table_prefix = (isset($GLOBALS['_tbpre'])) ? $GLOBALS['_tbpre'] : '';
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

?>