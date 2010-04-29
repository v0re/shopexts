<?php

define(DEPORT,'deport');



class ftp_dispatch{
	
	static  $deport;
	static  $uploader;
	
	function __construct($deport){		
		self::$deport = $deport;
		self::$uploader = $uploader;
		chdir(self::$deport);
	}
	
	function set_uploader($uploader){		
		self::$uploader = $uploader;
	}
	
	function run(){		
		self::dispatch();
	}
	
	function dispatch(){		
		$src = "info.php";
		$des = "info.php";
		self::$uploader->put($src,$des);
	}	
}

interface uploader{
	function put($src,$des);
}

class ftp_uploader implements uploader{
	
	var $rs;
	var $ftp_url;
	
	function __construct($ftp_url=''){
		$this->ftp_url = $ftp_url;
	}	
	
	function connect(){
		$a_conf = parse_url($this->ftp_url);
		try{
			$this->rs = ftp_connect($a_conf['host']);
			$ret = ftp_login($this->rs, $a_conf['user'], $a_conf['pass']);
			if($ret){
				echo "connect to ".$a_conf['host']."\n";
			}
		}catch(Exception $e){
			echo $e->getMessage();
		}
	}
	
	function put($src,$des){
		if(is_resource($this->rs)){
			echo "puting ".$src."\n";
			$ret = ftp_put($this->rs, $des, $src, FTP_BINARY);
			if($ret){
				echo "done!\n";
			}
		}else{
			throw new Exception('lost connection during upload');
		}
	}
}

class wput_uploader implements uploader{
	
	static $ftp_url;
	static $bin;
	
	function __construct($ftp_url){
		self::$ftp_url = $ftp_url;
		#wput无法做覆盖上传，需要先删除后，再上传才能实现更新
		self::$bin = "..\bin\wput.exe -B  ";
		set_time_limit(0);
	}
	
	function put($src,$des){
		$cmd = self::$bin." ".$src." ".self::$ftp_url;
		system($cmd);
	}
}


$a_conf = parse_ini_file('site.conf',true);
$instance  = new ftp_dispatch(DEPORT);
foreach($a_conf as $site_name=>$conf){
	echo $header = str_repeat('-',10).$site_name .str_repeat('-',10)."\n";
	$ftp_url = $conf['ftp_url'];
	$uploader = new ftp_uploader($ftp_url);
	$uploader->connect();
	$instance->set_uploader($uploader);
	$instance->run();
	echo $footer = str_repeat('-',10)."End";
	echo str_repeat('-',strlen($header)-strlen($footer))."\n\n";
}
