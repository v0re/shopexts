<?php
include "mdl.qqwry.php";
$instance = new mdl_qqwry;

echo "��ǰ���ݿ�汾��: ".$instance->getDBVersion('255.255.255.255');
echo "<hr>";
echo $instance->getInfoByIP('219.238.235.10');
//���: ������ ����ͨ
echo "<br>";
echo $instance->getInfoByIP('23.56.82.12');
//�����IANA
echo "<br>";
echo $instance->getInfoByIP('250.69.52.0');
//�����IANA������ַ
echo "<br>";
echo $instance->getInfoByIP('238.69.52.0');
//�����IANA������ַ ���ڶ�㴫��
echo "<br>";
echo $instance->getInfoByIP('192.168.0.1');
//����������� �Է�������ͬһ�ڲ���
echo "<br>";

echo "<br>";

#���������ж�
$vistorip = $instance->getIP();
$location = '�Ϻ�';
if($instance->isAt($vistorip,$location)){
	echo $vistorip." ���� ".$location;	
}else{
	echo $vistorip." �������� ".$location;
}
echo "<br><br>";
$vistorip = '116.228.220.98';
$location = '�Ϻ�';
if($instance->isAt($vistorip,$location)){
	echo $vistorip." ���� ".$location;	
}else{
	echo $vistorip." �������� ".$location;
}

echo "<br>";

echo "<br>";

echo "�������";


?>