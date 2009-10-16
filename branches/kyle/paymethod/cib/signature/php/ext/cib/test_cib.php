<?php

define('EXPECT','A8EAB40E');

$key = '12345678';
$message = 'kyle';

$mac = cibSign($key, $message);

echo $mac;

if( $mac === EXPECT ){
	echo "<br><b>pass!</b>";
}else{
	echo "<br><b>fail!</b>";
}

echo "<hr><b>test done!</b>";

?>