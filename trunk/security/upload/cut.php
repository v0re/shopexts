<?php
$a='';
for($i=0;$i<=4071;$i++) {
    $a .= '/';
}
$a = 'test.txt'.$a;                   //完整的路径为/var/www/test/test.txt
echo $a;
require_once($a.'.php');
?>