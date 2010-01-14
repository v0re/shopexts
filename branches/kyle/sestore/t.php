<?php
set_time_limit(0);

$dbfile = dirname(__FILE__)."/data/log";
$dh = opendir($dbfile);
while (($file = readdir($dh)) !== false) {
	if($file == '.' or $file == '..' or $file == '.svn') continue;
	$daf = "$dbfile/$file";
	echo "delete $daf\n";
	if(file_exists($daf)){
		unlink($daf);
	}
}
closedir($dh);

require "sestore.php";

$sdb = new sestore;

$sdb->workat();

//echo "test add table\n";
//
//$table = array(
//	'log'=>array(
//		array('id',INT,PRIMARY),
//		array('type',CHAR,INDEX),
//		array('user',CHAR,INDEX),
//		array('source',CHAR,INDEX),
//		array('event',TEXT),
//	),
//);
//
//if(!$sdb->hastable('log')){
//	$sdb->newtable($table);
//}
//
//$table = array(
//	'user'=>array(
//		array('id',INT,PRIMARY),
//		array('name',CHAR,INDEX),
//		array('password',CHAR,INDEX),
//		array('gander',CHAR,INDEX),
//		array('desc',TEXT),
//	),
//);
//
//if(!$sdb->hastable('user')){
//	$sdb->newtable($table);
//}
//
//#测试一个表存在的情况，应该返回一个false
//
//var_export($sdb->newtable($table));
//
//echo "\ntest writing\n";

#数据必须是数组，并且要求字段名做键名，数据的单元的位置没有要求，'id'=>1这样单元放数组最后也没有关系
$length = 100;
for($i=1;$i<=$length;$i++){
	$entry = array(
		'type'=>rand(1,3),
		'user'=>'index.php',
		'source'=>'127.0.0.1',
		'event'=>'可是在我心里头忍不住爱上她的体贴温柔',
		'id'=>$i,
	);

	#表名，数据
	$sdb->store('log',$entry);
}





echo "\ntest reading\n";

for($i=1;$i<=10;$i++){
	$id =rand(1,$length);
	echo "we are going to fetch $id\n";
	$ret = $sdb->fetch($id);
	if($ret['id'] != $id){
		echo "test fail:\n";
		var_export($ret);
		exit();
	}
	echo "ok \n";
}


var_export($sdb);
echo "\ndone";

