<?php
$a='';
for($i=0;$i<=4071;$i++) {
    $a .= '/';
}
$a = 'test.txt'.$a;                   //������·��Ϊ/var/www/test/test.txt
echo $a;
require_once($a.'.php');
?>