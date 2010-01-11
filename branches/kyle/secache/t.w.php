<?php

require "c.php";
unlink(T.".php");
$obj->workat(T);

$size = $argv[1] ? $argv[1] : $_GET['size'];
$size = $size ? $size : 100;

$begin_time = microtime_float();

for($i=0;$i<$size ;$i++){

    $key = md5($i); 
    $value = str_repeat('No. <strong>'.$i.'</strong> is <em style="color:red">great</em>! ',rand(1,10)); // must be a *STRING*

    $obj->store($key,$value);
}

echo '<h2>Insert x {$size} = ' .( microtime_float() - $begin_time) .' ms</h2>';
echo '<hr /><h2>test read</h2>';




?>
