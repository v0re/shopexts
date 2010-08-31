<?php

require "config.php";

$ret = get_file_list(DATA);
echo "<table>";
$id = 0;
foreach($ret as $item){
    $tmp = explode('.',$item);
    $viewurl = VIEWURL."run=".$tmp[0]."&source=".$tmp[1];
    echo "<tr><td>".date("Y-m-d H:i:s",filemtime(DATA."/".$item))."</td><td><a href=\"".$viewurl."\" target=_blank>$item</a></td></tr>";
    $id++;
    if($id > LIMIT) break;
}
echo "</table>";


function get_file_list($dir){
	$dir_obj = dir($dir);
	while(($file = $dir_obj->read()) !== null){
		$ret[]  = $file;
	}
	
	return $ret;
}