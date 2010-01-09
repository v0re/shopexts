<?php

require('secache/secache.php');
$cache = new secache;
$cache->workat('cachedata');

function microtime_float(){
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

set_time_limit(0);




foreach(array(1,100,1000,10000) as $i){
		$key = md5($i); //You must *HASH* it by your self
	if($cache->fetch($key,$value)){
			echo '<li>'.$key.'=>'.$value.'</li>';
	}else{
			echo '<li>Data get failed! <b>'.$key.'</b></li>';

	}
}



echo '<h2>get = ' .( microtime_float() - $begin_time) .' ms</h2>';
echo '<hr /><h2>test read</h2>';




