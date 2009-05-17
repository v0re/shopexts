<?php

define('PATH','plugins\widgets');

if ($handle = opendir(PATH)) {
	$i = 1;
	while (false !== ($file = readdir($handle))) {
		$settingfile = PATH."/".$file."/widgets.php";
		if ($file != "." && $file != ".." && is_file($settingfile)) {
			include($settingfile);
			
			$aTmp['NO'] = intstring($i++);
			$aTmp['dirname'] = $file;
			$aTmp['author'] = $setting['author'];
			$aTmp['name'] =	$setting['name'];
			$aTmp['version'] = $setting['version'];
			$aTmp['catalog'] = $setting['catalog'];
			$aTmp['create'] = $setting['stime'];
			$aTmp['description'] = $setting['description'];
			
			$rowstr .= makeRow($aTmp);

			unset($setting);
		}
	}
	closedir($handle);
}

$header = array_keys($aTmp);
$header = makeRow($header);

$rowstr = $header.$rowstr;

makeTable($rowstr);

function makeTable($rowstr){
	echo <<<EOF
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
		<table border='1'>
		{$rowstr}
		</table>
</body>
</html>
EOF;

}


function makeRow($aData){
	$rowstr = "<tr>";
	foreach($aData as $v){
		$v = $v ? $v : "&nbsp;";
		$rowstr .= "<td>{$v}</td>";
	}
	$rowstr .= "</tr>";

	return $rowstr;
}

function intstring($num){
	$tmp = '00000'.$num;
	
	return substr($tmp,-4);
}

?>