<?php

$para = "Succeed=Y&BillNo=9102357423&Amount=5.10&Date=20091023&Msg=00100063142009102309302359700000002340&Signature=126|3|78|18|144|142|249|99|26|243|124|38|111|24|6|63|94|242|98|209|71|228|138|80|80|17|157|145|77|70|182|103|33|8|82|129|100|213|250|237|212|148|197|228|73|196|82|188|224|123|14|14|194|105|3|76|185|37|54|86|155|152|19|117|";



$cmd = "java -jar Check.jar \"{$para}\"";
$handle = popen($cmd, 'r');
$isok = fread($handle, 8);
pclose($handle);


var_export($isok);

?>