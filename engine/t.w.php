<?php

set_time_limit();
$datafile = 'simplehash.hdb';
				
require('simplehash.php');

$hs = new simplehash;
$hs->workat($datafile);

$key = '12345678901234567890123456789012';

$begin_time = microtime_float();

for($i=0;$i<362272;$i++){
	$log = genItem();
	$key = genkey($log);
	$hs->set($key,$log);
}

echo '<h2>Insert x 362272 = ' .( microtime_float() - $begin_time) .' ms</h2>';

function &genItem(){
	return $value = array(
		'type'=>rand(1,9),
		'time'=>time(),
		'source'=>'127.0.0.1',
		'user'=>'好人好人',
		'event'=>"we,are we were ` \' \n",
	);
}

function genkey(&$data){
	$key = $data['type'].date('YmdHis',$data['time']);
	$substr = 32 - strlen($key);
	return $key.substr(md5(mt_rand()),0,$substr);
}


function microtime_float(){
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}




?>