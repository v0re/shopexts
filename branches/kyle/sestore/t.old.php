<?php
set_time_limit(0);

$dbfile = dirname(__FILE__)."/data/log";
$dh = opendir($dbfile);
while (($file = readdir($dh)) !== false) {
	if($file == '.' or $file == '..') continue;
	$daf = "$dbfile/$file";
	echo "delete $daf\n";
	if(file_exists($daf)){
		unlink($daf);
	}
}
closedir($dh);

require "sestore.php";


$sdb = new sestore;

$table = array(
	'log'=>array(
		array('id',INT,PRIMARY),
		array('type',CHAR,INDEX),
		array('user',CHAR,INDEX),
		array('source',CHAR,INDEX),
		array('event',TEXT),
	),
);

if(!$sdb->hastable('log')){
	$sdb->newtable($table);
}

echo "test writing\n";

for($i=1;$i<=100;$i++){
	$log = array(
	'id'=>$i,
	'type'=>'sys',
	'user'=>'kickout',
	'source'=>'127.0.0.1',
	'event'=>'what a fucking day',
	);
	
	#$sdb->store($log);
}

echo "test reading\n";

for($i=1;$i<=100;$i+=10){
	$data = $sdb->fetch($i);
	var_export($data);
	echo "\n";
}


echo "<hr>do to here!";