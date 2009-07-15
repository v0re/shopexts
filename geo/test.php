<?php
	ob_start();
	include("geoip.inc");
	$gi = geoip_open("GeoIP.dat",GEOIP_MEMORY_CACHE);
	//根据远程IP地址获取国家名代号
	$country = geoip_country_code_by_addr($gi,$_SERVER['REMOTE_ADDR']);
	if($country == 'CN' || $country=='HK' || $$country=='TW')
	{	//如果是在中国，跳转到百度
		//echo $_SERVER['REMOTE_ADDR'];
		//header("Location: http://www.baidu.com/");
	}
	else
	{	//否则跳转到google
		echo $_SERVER['REMOTE_ADDR'];
		echo '不在中国';
	}
?>