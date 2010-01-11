<?php

require "c.php";
$obj->workat(T);

foreach(array(1,10,100,1000,10000,100000,1000000) as $i){
		$key = md5($i); //You must *HASH* it by your self
	if($obj->fetch($key,$value)){
			echo '<li>'.$key.'=>'.$value.'</li>';
	}else{
			echo '<li>Data get failed! <b>'.$key.'</b></li>';
	}
}



echo '<h2>get = ' .( microtime_float() - $begin_time) .' ms</h2>';
echo '<hr /><h2>test read</h2>';




