<?php

require "config.php";

$ret = get_file_list(DATA);
sort_by_key($ret,'filetime');

if($_GET['p'] && is_numeric($_GET['p'])){
	$p = intval($_GET['p']);
	$offset = $p * LIMIT;
	$length = LIMIT;
	$items = array_slice($ret,$offset,$length);
}else{
	$p = 1;
	$items = $ret;
}
echo "<b>".count($ret)."</b> in tatal~ &nbsp;<a href=?action=clear&p=all><b>delete all</b></a>&nbsp;&nbsp;<a href=?action=clear&p={$p}><b>delete page {$p}</b></a>";
if($_GET['action'] == 'clear'){
	if($_GET['p'] == 'all'){
		del_item($ret);
	}else{
		del_item($items);
	}
}
echo "<hr>";
echo "<table>";
$id = 0;
foreach($items as $item){
    $tmp = explode('.',$item['name']);
    $file = DATA."/".$item['name'];
    $acc = unserialize(file_get_contents($file));
  	$acc = array_pop($acc);
    $viewurl = VIEWURL."run=".$tmp[0]."&source=".$tmp[1];
    echo "<tr><td>".$id."</td><td>".date("Y-m-d H:i:s",$item['filetime'])."</td><td><a href=\"".$viewurl."\" target=_blank>".$item['name']."</a></td><td>".$acc['wt']."</td><td>".$acc['pmu']."</td></tr>";
    $id++;
    if($id > LIMIT) break;
}
echo "</table>";
echo "<hr>";
echo gen_pager(count($ret),$p);

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

function gen_pager($count,$curent){
	$page = intval($count / LIMIT);
	for($i=1;$i<=$page;$i++){
		if($i == $current){
			$ret .= "<a href=?p=$i><b>$i</b></a>&nbsp;";
		}else{
			$ret .= "<a href=?p=$i>$i</a>&nbsp;";
		}
	}
	return $ret;
}

function del_item($items){
	foreach($items as $item){
		$file = DATA."/".$item['name'];
		unlink($file);
	}
}