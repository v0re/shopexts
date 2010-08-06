#!/usr/bin/php
<?php
class mgr{
	
	var $name;
	var $config_file_path;
	var $public_file_name = "pub.pem";
	var $private_file_name = "sec.pem";
	var $private_file_name_encrypted = "sec.pem.en";
	var $setting = array();
	var $setting_file = 'setting.conf';
	var $setting_file_encrypted = 'setting.conf.en';
	
	function __construct($name){
		$this->name = $name;
		if(!file_exists($name)){
			mkdir($name);
		}
		chdir($name);
		$this->setting[0] = "/etc/shopex/$name/$this->public_file_name";
		$this->setting[1] = "/etc/shopex/$name/$this->private_file_name_encrypted";
	}	
	
	function gen_key(){
		shopex_gen_keypair($this->public_file_name,$this->private_file_name);
	}
	
	function encrypt_private_key(){
		$plain_private_key = realpath($this->private_file_name);
		$plain_private_key = shopex_get_user_private_key($plain_private_key);
		$encrypted = NULL;
		shopex_public_encrypt($plain_private_key,$encrypted);
		file_put_contents($this->private_file_name_encrypted,$encrypted);
	}
	
	function gen_conf($setting){
		$config_file = $this->setting_file ;
		$this->setting = array_merge($this->setting, $setting);
		$fp = fopen($config_file,"wb+");
		fwrite($fp,trim($this->setting[0])."\n");
		fwrite($fp,trim($this->setting[1])."\n");

		for($i=2;$i<count($this->setting);$i++){
			$this->setting[$i] = trim($this->setting[$i]);
			$md5 = md5_file($this->setting[$i]);
			fwrite($fp,$this->setting[$i].":".$md5."\n");
		}
		fclose($fp);
	}
	
	function encrypt_conf(){
		$plain_config_file = realpath($this->setting_file);
		$config = file_get_contents($plain_config_file);
		$encrypted = NULL;
		shopex_public_encrypt($config,$encrypted);
		file_put_contents($this->setting_file_encrypted,$encrypted);
	}

}

function help_info(){
	$__FILE__ = basename(__FILE__);
	echo <<<EOF
	
	usage:  {$__FILE__}  option target
	option:
	-n	create a new site
	-u	update setting.conf
	
	for example:
	{$__FILE__} shopex.cn -n
	\n
EOF;
}


if( $_SERVER['argc'] != 3){
	help_info();
	exit;
}

$ib = new mgr('skomart.com');
switch($_SERVER['argv'][2]){
	case "-n":
		$ib->gen_key();
		$ib->encrypt_private_key();
	break;
	case "-u":
		$setting = array(
			'/srv/http/security/crypt/rsa/datasafe/test.php',
		);
		$ib->gen_conf($setting);
		$ib->encrypt_conf();
	break;
}

echo "all done!";

