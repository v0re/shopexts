<?php

ini_set("memory_limit","64M");

define("FILENAME",'3mtest');
define("TESTSIZE",5000000);

$rnt = str_repeat("0",TESTSIZE);

$fp = fopen(FILENAME,"wb+");
fwrite($fp,$rnt);
fclose($fp);
if(file_exists(FILENAME)){
	$filesize = filesize(FILENAME);
	if($filesize == TESTSIZE){
		echo "Test OK!<br>The file size is ".$filesize." byte";
	}else{
		echo "Test fail!<br>The file size is ".$filesize." byte";
	}
	unlink(FILENAME);
}else{
	echo FILENAME."not exists!";
}

?>