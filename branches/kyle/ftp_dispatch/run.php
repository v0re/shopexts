<?php

$a_conf = parse_ini_file('site.conf',true);

$instance  = new ftp_dispatch;
foreach($a_conf as $site_name=>$conf){
	echo $header = str_repeat('-',10).$site_name .str_repeat('-',10)."\n";
	$ftp_url = $conf['ftp_url'];
	$instance->set_url($ftp_url);
	$instance->run();
	echo $footer = str_repeat('-',10)."End";
	echo str_repeat('-',strlen($header)-strlen($footer))."\n\n";
}

class ftp_dispatch{
	
	static  $ftp_url;
	static  $deport;
	static  $uploader;
	
	function __construct($ftp_url=''){
		self::$ftp_url = $ftp_url;
		self::$deport = "deport";
		#wput命令会自动判断要上传的文件是否发生了变动
		self::$uploader = "..\bin\wput.exe -B --reupload ";
		set_time_limit(0);
		chdir(self::$deport);
	}
	
	function set_url($ftp_url){		
		self::$ftp_url = $ftp_url;
	}
	
	function run(){
		self::dispatch();
	}
	
	function dispatch(){		
		$cmd = self::$uploader." . ".self::$ftp_url;
		system($cmd);
	}	
}
