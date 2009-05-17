<?php

$str = "This is a test content";
$file = "ok.cer";
$size = strlen($str);

/*
header("Cache-Control: no-cache, must-revalidate"); 
header("Content-Type: application/octet-stream");
header("Content-Type: application/force-download");
header("Content-Length: $size");
if(preg_match("/MSIE 5.5/", $_SERVER["HTTP_USER_AGENT"])){
	header("Content-Disposition: filename= $file","cer");
}
else
{
	header("Content-Disposition: attachment; filename=$file","cer");
}

*/


print $str;

?>