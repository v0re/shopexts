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
//#����һ������ڵ������Ӧ�÷���һ��false
//
//var_export($sdb->newtable($table));
//
//echo "\ntest writing\n";

#���ݱ��������飬����Ҫ���ֶ��������������ݵĵ�Ԫ��λ��û��Ҫ��'id'=>1������Ԫ���������Ҳû�й�ϵ
$length = 100;
for($i=1;$i<=$length;$i++){
	$entry = array(
		'type'=>rand(1,3),
		'user'=>'index.php',
		'source'=>'127.0.0.1',
		'event'=>'������������ͷ�̲�ס����������������',
		'id'=>$i,
	);

	#����������
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

