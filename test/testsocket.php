<?php
$fp = fsockopen("smtp.126.com", 25, $errno, $errstr, 30);
if (!$fp) {
    echo "$errstr ($errno)<br />\n";
} else {
	  echo fgets($fp, 1024);
		echo "<br>";
    $out = "ehlo localhost\r\n";
    fwrite($fp, $out);
       echo fgets($fp, 1024);
    
    fclose($fp);
}
?> 