<?php

require "config.php";

$ret = get_file_list(DATA);
sort_by_key($ret,'filetime');
if($_GET['p']){
	$p = intval($_GET['p']);
	$offset = $p * LIMIT;
	$length = LIMIT;
	$ret = array_slice($ret,$offset,$length);
}
echo "<table>";
$id = 0;
foreach($ret as $item){
    $tmp = explode('.',$item['name']);
    $viewurl = VIEWURL."run=".$tmp[0]."&source=".$tmp[1];
    echo "<tr><td>".date("Y-m-d H:i:s",$item['filetime'])."</td><td><a href=\"".$viewurl."\" target=_blank>".$item['name']."</a></td></tr>";
    $id++;
    if($id > LIMIT) break;
}
echo "</table>";
echo "<hr>";
echo gen_pager(count($ret));

function get_file_list($dir){
	$dir_obj = dir($dir);
	while(($file = $dir_obj->read()) !== false){
		if(substr($file,0,1) == '.' ) continue;
		$item['name']  = $file;
		$item['filetime'] = filemtime($dir."/".$file);
		$ret[] = $item;
	}
	
	return $ret;
}

function sort_by_key(&$data,$key){
	foreach($data as $k=>$v){
		$flag_array[$k] = $v[$key];		
	}
	arsort($flag_array);
	foreach($flag_array as $k=>$v){
		$ret[] = $data[$k];
	}
	$data = $ret;
}

function gen_pager($count){
	$page = ceil($count / LIMIT);
	for($i=1;$i<=$page;$i++){
		$ret .= "<a href=?p=$i>$i&nbsp;</a>";
	}
	return $ret;
}