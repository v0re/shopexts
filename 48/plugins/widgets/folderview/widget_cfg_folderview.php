<?php
function widget_cfg_folderview(&$system){
	$setting = unserialize(file_get_contents(dirname(__FILE__)."/setting.conf"));
	var_dump($setting);

	$dir = BASE_DIR."/config";
	$d = dir($dir);
	$i=1;
	while (false !== ($entry = $d->read())) {
		if(in_array($entry,array('.','..','index.php'))) continue;
		$name = "$dir/$entry";
		$lastmodify = filemtime($name);
		$lastmodify = date('Y-m-d H:i:s',$lastmodify);
		$url = $system->base_url().str_replace(BASE_DIR."/",'',$dir)."/$entry";
		$alias = $setting['alias'][$entry] ? $setting['alias'][$entry] : $entry;
 		$aFileList[$i] = array('url'=>$url,'name'=>$entry,'alias'=>$alias,'lastmodify'=>$lastmodify);		
		$i++;
	}
	$d->close();
    $data['files'] = $aFileList;
	return $data;
}
?>
