<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head> 
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">     
    <style type=text/css>
        .fv table{
        	border-collapse:collapse;   
        }
        .fv td{
        	background:#ffc;
        	border:solid 1px #f90;
        	height:22px;
        }
        </style>
</head>
<body>
<?php

require "config.php";

$ret = get_file_list(DATA);


if($_GET['p'] && is_numeric($_GET['p'])){
	$p = intval($_GET['p']);
	$offset = $p * LIMIT;
	$length = LIMIT;
	$items = array_slice($ret,$offset,$length);
}else{
	$p = 1;
	$items = $ret;
}
echo "<b>".count($ret)."</b> in total~ &nbsp;<a href=?action=clear&p=all><b>delete all</b></a>&nbsp;&nbsp;<a href=?action=clear&p={$p}><b>delete page {$p}</b></a>";
if($_GET['action'] == 'clear'){
	if($_GET['p'] == 'all'){
		del_item($ret);
	}else{
		del_item($items);
	}
}

#重组数据
foreach($items as $k=>$item){
    $tmp = explode('.',$item['name']);
    $file = DATA."/".$item['name'];
    $acc = unserialize(file_get_contents($file));
  	$acc = array_pop($acc);
  	$item['wt'] = intval($acc['wt']);
  	$item['pmu'] = intval($acc['pmu']);
    $item['viewurl'] = VIEWURL."run=".$tmp[0]."&source=".$tmp[1];
    $items[$k] = $item;
}

#排序

if($_GET['sort'] ){
    sort_by_key($items,$_GET['sort']);
}else{
    sort_by_key($ret,'filetime');
}

echo "<hr>";
echo "<div class='fv'>";
echo "<table>";
if($p == 1){
    $pp ='';
}else{
    $pp = $p
}
echo "<tr><td>id</td><td>time</td><td width=80%><a href=?p={$pp}&sort=viewurl>url</a></td><td><a href=?p={$pp}&sort=wt>time cost(ms)</a></td><td><a href=?p={$pp}&sort=pmu>memory(byte)</a></td></tr>";
$id = 0;
foreach($items as $item){
    echo "<tr><td>".$id."</td><td>".date("Y-m-d H:i:s",$item['filetime'])."</td><td><a href=\"".$item['viewurl']."\" target=_blank>".$item['name']."</a></td><td>".number_format($item['wt'])."</td><td>".number_format($item['pmu'])."</td></tr>";
    $id++;
    if($id > LIMIT) break;
}
echo "</table>";
echo "</div>";
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
		if($i > 30){
		    $ret .= "……<a href=?p=$page>$page</a>&nbsp;";
		    break;
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

?>
</body>
</html>