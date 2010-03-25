<?php
function widget_folderview(&$setting,&$system){
	file_put_contents(dirname(__FILE__)."/setting.conf",serialize($setting));
	$dir = realpath(BASE_DIR."/home/shujubao/");
	$d = dir($dir);
	$i=1;
	while (false !== ($entry = $d->read())) {
		if(in_array($entry,array('.','..','index.php'))) continue;
		$name = "$dir/$entry";
		$lastmodify = filemtime($name);
		$lastmodify = date('Y-m-d H:i:s',$lastmodify);
		$url = $system->base_url().str_replace(realpath(BASE_DIR)."/",'',$dir)."/$entry";
		$alias = $setting['alias'][$entry] ? $setting['alias'][$entry] : $entry;
		$aFileList[$i] = array('url'=>$url,'name'=>$name,'alias'=>$alias,'lastmodify'=>$lastmodify);		
		$i++;
	}
	$d->close();
    $aFileList = m_array_sort($aFileList,'lastmodify');
    $data['files'] = $aFileList;
    return $data;
}

function m_array_sort(&$array,$sortkey){
	foreach($array as $key=>$row){
		$keyvalue[$key] = $row[$sortkey];
	}
	arsort($keyvalue);
	foreach($keyvalue as $key=>$value){
		$ret[$key] = $array[$key];
	}

	return $ret;
}


?>
