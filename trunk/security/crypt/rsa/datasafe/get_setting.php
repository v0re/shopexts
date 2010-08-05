<?php 
$setting = array(
'/etc/shopex/skomart.com/pub.pem',
'/etc/shopex/skomart.com/sec.pem.en',
'/srv/http/security/crypt/rsa/datasafe/test.php',
);

$config_file = "setting.conf";
$fp = fopen($config_file,"wb+");
fwrite($fp,trim($setting[0])."\n");
fwrite($fp,trim($setting[1])."\n");
for($i=2;$i<count($setting);$i++){
	$setting[$i] = trim($setting[$i]);
	$md5 = md5_file($setting[$i]);
	fwrite($fp,$setting[$i].":".$md5."\n");
}
fclose($fp);