<?php

define('DATA','./data');
define('VIEWURL','http://192.168.0.12/xhprof/xhprof_html/index.php?');
define('LIMIT',30);

exec("ls -t ".DATA,$ret);

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
