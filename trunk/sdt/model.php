<?php



class Mysqldumper {
	var $_host;
	var $_dbuser;
	var $_dbpassword;
	var $_dbname;
	var $_isDroptables;
	var $_backupfilename;
	//���ݱ�ID
	var $tableid;
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

	function setBackupFilename($filename) {
		$this->_backfilename = $filename
	}

	function setBackupTable($tables){
		$this->_tablearr = $tables;		
	}

	function setDBCharset(){

	}

	function getShopExVersion(){
		return "484";
	}

	function multiDump($filename,$fileid,$sizelimit) {
		// Set line feed
		$ret = true;
		$lf = "\r\n";
		$lencount = 0;

		$resource = mysql_connect($this->getHost(), $this->getDBuser(), $this->getDBpassword(),true);
		mysql_select_db($this->getDbname(), $resource);
		if(defined("MYSQL_CHARSET_NAME"))
			mysql_query("SET NAMES '".MYSQL_CHARSET_NAME."'",$resource);
		#��ȡ��Ҫ���ݵı�
		if(!$this->_tablearr){
			$result = mysql_query("SHOW TABLES");
			$this->_tablearr = $this->result2Array(0, $result);
		}
		#
		$bakfile = $this->_backfilename;
		$fw = @fopen($bakfile, "wb+");
		if(!$fw) exit("export file write failed");	
		// Set header
		fwrite($fw, "#". $lf);
		fwrite($fw,  "# SHOPEX SQL MultiVolumn Dump ID:".($fileid+1) . $lf);
		fwrite($fw,  "# Version ". $this->getShopExVersion(). $lf);
		fwrite($fw,  "# ". $lf);
		fwrite($fw,  "# Host: " . $this->getHost() . $lf);
		fwrite($fw,  "# Server version: ". mysql_get_server_info() . $lf);
		fwrite($fw,  "# PHP Version: " . phpversion() . $lf);
		fwrite($fw,  "# Database : `" . $this->getDBname() . "`" . $lf);
		fwrite($fw,  "#");

		// Generate dumptext for the tables.
		$i = 0;
		for($j = $this->tableid; $j < count($this->_tablearr); $j++){
			$tblval = $this->_tablearr[$j];
			$table_prefix = $this->getTablePrefix();
			$written_tbname = '{shopexdump_table_prefix}'.substr($tblval,strlen($table_prefix));
			#д�������
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
			if($lencount > $sizelimit*1000)
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

	//�����һ���Ƿ��ǰ��UTF-8����
	function utftrim($str)
	{
		$found = false;
		for($i=0;$i<4&&$i<strlen($str);$i++)
		{
			$ord = ord(substr($str,strlen($str)-$i-1,1));
			//UTF-8���ķ�{��/��/���ֽ���},��һλ�ֱ�Ϊ11110xxx(>192),1110xxxx(>192),110xxxxx(>192);����ȥ��λ������10xxxxxx(<192)
			//����ASCII�붼��0xxxxxxx
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
