<?php
require('secache/sestore.php');

$sestore = new sestore;
$sestore->workat('log');


set_time_limit(0);

$begin_time = microtime_float();


for($i=0;$i<10000 ;$i++){

    $key = md5($i); //You must *HASH* it by your self
    $value = str_repeat('No. <strong>'.$i.'</strong> is <em style="color:red">great</em>! ',rand(1,10)); // must be a *STRING*

    $sestore->store($key,$value);
}

echo '<h2>Insert x 362272 = ' .( microtime_float() - $begin_time) .' ms</h2>';
echo '<hr /><h2>test read</h2>';


function microtime_float(){
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

?>
