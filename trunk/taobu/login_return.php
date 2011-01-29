<?php
$str = "top_appkey=12056631&top_parameters=aWZyYW1lPTEmdHM9MTI4MzkzODE0MTc0OSZ2aWV3X21vZGU9ZnVsbCZ2aWV3X3dpZHRoPTAmdmlzaXRvcl9pZD00MDgxNTQxNjAmdmlzaXRvcl9uaWNrPczUzfjJzLmkvt8%3D&top_session=141604a59960ef7e6524a99c47437a7502509&top_sign=ZDz0l1DMzBI5FL4jeWeH3g%3D%3D&sign=737A0BE370F629377E2558EEB19F232F&leaseId=21578&versionNo=1";

foreach( explode('&',$str) as $item){
    $tmp = explode('=',$item);
    $_GET[$tmp[0]] = $tmp[1];   
}

var_dump($_GET);

$top_appkey     = $_GET['top_appkey'];
$top_session    = $_GET['top_session'];
$top_sign       = $_GET['top_sign'];
$top_parameters = $_GET['top_parameters'];
$APP_SECRET='ff7a26a5d0106a59f6e6351d4e116247';
if ($top_sign == base64_encode(md5($top_appkey.$top_parameters.$top_session.$APP_SECRET,true))){
    $tmpparams=base64_decode($top_parameters);
    parse_str($tmpparams,$parameters_info);
    $to_params['sign']=$top_sign;
    $to_params['top_session']=$top_session;
    $to_params['nick']=$parameters_info['visitor_nick'];
    $nickname=$to_params['nick'];
    var_dump($nickname);
}