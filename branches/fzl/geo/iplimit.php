<?php
	ob_start();
	include("geo/geoip.inc");
	$gi = geoip_open("geo/GeoIP.dat",GEOIP_MEMORY_CACHE);
	//从文本取得所有IP字符串
	$getIPStr = file_get_contents('geo/allow_ip.txt');
	//用,分割IP字符串
	$allowIp = explode('|',$getIPStr);
	//获得远程IP
	$ip = $_SERVER['REMOTE_ADDR'];
	//根据远程IP地址获取国家名代号
	$country = geoip_country_code_by_addr($gi,$ip);
	//是否在允许的IP访问队列内
	if(!is_array($allowIp)||!in_array($ip,$allowIp))
	{
		if ($country == 'CN' || $country=='HK' || $$country=='TW')
		{
			header("Location: http://www.baidu.com/");
		}
	}
?>