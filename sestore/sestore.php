<?php

/*2<<n = 
1=>16
2=>32
3=>64
4=>128
5=>256
6=>512
7=>1024
8=>2048
9=>4096
10=>8192
11=>16384
12=>32768
13=>65536
14=>131072
15=>262144
16=>524288
17=>1048576
18=>2097152
19=>4194304
20=>8388608
21=>16777216
22=>33554432
23=>67108864
24=>134217728
25=>268435456
26=>536870912
27=>1073741824
*/


define(INT,1);
define(CHAR,2);
define(TEXT,3);
define(INDEX,8);
define(PRIMARY,9);



class sestore {

	var $orgin;
	var $db;
	var $fp;
	var $version;
	
	function sestore(){
		$this->db = dirname(__FILE__)."/data/log/sestore.data.php";
		touch($this->db);
		$this->fp = fopen($this->db,"rb+");
		$this->version = '0.0.1';
		$this->pagedefine = '1:1024';
		#结构信息在128的地方开始
		$this->orgin = 2<<4;
		#索引在1,048,576的地方开始，表结构有1M的数据
		$this->indexhome = 2<<17;
		#数据在4,194,304的地方开始，索引文件有3M的大小
		$this->datahome = 2<<18;
		$this->meta = $this->getmeta();		
	}

	function create(){
		$stopcode = "<?php exit(); ?>";
		$header = $stopcode.$this->version.$this->pagedefine;
		rewind($this->fp);
		fwrite($this->fp,$header);
	}

	function getmeta(){
		fseek($this->fp,$this->orgin);
		#获取一整行
		if(!$metades = fgets($this->fp)){
			#还没有表格，先建立结构
			$this->create();
			return array('table'=>array());
		}
		return unserialize(trim($metades));
	
	}

	function setmeta(){
		$metades = serialize($this->meta);
		$metades .= "\n";
		$size = strlen($metades);
		#检查是否越界
		if($this->orgin + $size > $this->indexhome){
			$this->error('meta data ommit');
		}
		fseek($this->fp,$this->orgin);	
		fwrite($this->fp,$metades);		
	}

	function hastable($tblname){
		$tblnames = array_keys($this->meta['table']);
		return in_array($tblname,$tblnames);
	}

	function newtable($table){
		#获取表名和表结构
		list($name,$desc) = each($table);
		if($this->hastable($name)) return false;
		$this->meta['table'][$name]['desc'] = $desc;		
		$this->setmeta();
	}

   
	function store($tblname,$value){	
		$key = $this->getkey($tblname,$value);
		$posinfo = $this->wdata(serialize($value));		
		$this->windx($key,$posinfo);
    }

	function getkey($tblname,$value){
		$desc = $this->meta['table'][$tblname]['desc'];
		foreach($desc as $st){
			$name = $st[0];
			$ext = $st[2];
			#目前只用主键做索引，其他字段做索引需要用B+Tree的数据结构
			if($ext == PRIMARY){
				return $value[$name];
			}
		}
	}

	function wdata($content){
		fseek($this->fp,0,SEEK_END);
		$offset = ftell($this->fp);
		#第一次在数据区写数据
		if($offset < $this->datahome){
			fseek($this->fp,$this->datahome);
			$offset = $this->datahome;
		}
		$size = $this->put($content);
		#返回数据块信息:开始位置和长度
		return array($offset,$size);
	}

	function windx($key,$posinfo){
		$index = $this->rindx();
		$index[$key] = $posinfo;
		$content = serialize($index);
		$content .= '\n';
		$size = strlen($content);
		#检查是否越界
		if($this->indexhome + $size > $this->datahome){
			$this->error('index data ommit');
		}
		#在index长度减少情况下会产生垃圾
		fseek($this->fp,$this->indexhome);
		$this->put($content);
	}


	function put($content){
		flock($this->fp,LOCK_EX);
		$size = fwrite($this->fp,$content);
		flock($this->fp,LOCK_UN);
		return $size;
	}

	function fetch($key){
		if($index = $this->getindx($key)){
			list($offset,$size) = $index;
			$content = $this->rdata($offset,$size);

			return unserialize($content);
		}
		
		return false;
	}

	function getindx($key){
		$index = $this->rindx();
		return $index[$key] ? $index[$key] : false;	
	}
	
	function rindx(){		
		fseek($this->fp,$this->indexhome);
		$index = unserialize(fgets($this->fp));
		if(is_array($index)){
			return $index;
		}

		return array();
	}

	function rdata($offset,$size){
		fseek($this->fp,$offset);
		$content = $this->get($size);
		return $content;
	}

	function get($size=0){
		flock($this->fp,LOCK_SH);
		$ret = fread($this->fp,$size);
		flock($this->fp,LOCK_UN);
		return $ret;
	}


	function error($str){
		error_log($str,3,__FILE__.".fatal.log");
		exit();
	}

}