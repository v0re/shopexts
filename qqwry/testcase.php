<?php
include "mdl.qqwry.php";
$instance = new mdl_qqwry;

echo "当前数据库版本是: ".$instance->getDBVersion('255.255.255.255');
echo "<hr>";
echo $instance->getInfoByIP('219.238.235.10');
//输出: 北京市 电信通
echo "<br>";
echo $instance->getInfoByIP('23.56.82.12');
//输出：IANA
echo "<br>";
echo $instance->getInfoByIP('250.69.52.0');
//输出：IANA保留地址
echo "<br>";
echo $instance->getInfoByIP('238.69.52.0');
//输出：IANA保留地址 用于多点传送
echo "<br>";
echo $instance->getInfoByIP('192.168.0.1');
//输出：局域网 对方和您在同一内部网
echo "<br>";

echo "<br>";

#测试区域判断
$vistorip = $instance->getIP();
$location = '上海';
if($instance->isAt($vistorip,$location)){
	echo $vistorip." 来自 ".$location;	
}else{
	echo $vistorip." 不是来自 ".$location;
}
echo "<br><br>";
$vistorip = '116.228.220.98';
$location = '上海';
if($instance->isAt($vistorip,$location)){
	echo $vistorip." 来自 ".$location;	
}else{
	echo $vistorip." 不是来自 ".$location;
}

echo "<br>";

echo "<br>";

echo "测试完毕";


?>