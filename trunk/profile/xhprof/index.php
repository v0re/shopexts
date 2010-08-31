<?php

require "config.php";

$ret = get_file_list(DATA);
sort_by_key($ret,'filetime');
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