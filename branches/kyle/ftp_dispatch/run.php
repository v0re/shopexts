<?php

$a_conf = parse_ini_file('site.conf',true);

$instance  = new ftp_dispatch;
foreach($a_conf as $site_name=>$conf){
	echo $header = str_repeat('-',10).$site_name .str_repeat('-',10)."\n";
	$url = $conf['url'];
	$instance->set_url($url);
	$instance->run();
	echo $footer = str_repeat('-',10)."End";
	echo str_repeat('-',strlen($header)-strlen($footer))."\n\n";
}

class ftp_dispatch{
	
	static  $url;
	static  $deport;
	static  $uploader;
	
	function __construct($url=''){
		self::$url = $url;
		self::$deport = "deport";
		#wput命令会自动判断要上传的文件是否发生了变动
		self::$uploader = "..\bin\wput.exe -B ";
		set_time_limit(0);
		chdir(self::$deport);
	}
	
	function set_url($url){		
		self::$url = $url;
	}
	
	function run(){
		self::dispatch();
	}
	
	function dispatch(){		
		$cmd = self::$uploader." . ".self::$url;
		system($cmd);
	}	
}
