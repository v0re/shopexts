<?php

include "sm.php";

$to = array('xuni@shopex.cn','xuni@zovatech.com','danny@zovatech.com');
$smtp = new smtp("helloxui@126.com","usst103","smtp.126.com",25,true);
$smtp->debug = false;

echo "Test publication by smtp<br>";
$smtp->sendway = "SMTP";
foreach($to as $val){
	if($smtp->sendmail($val,"helloxui@126.com","This is subject","This is body",'nothing',"This is sender")){
		echo "<font color=green>".$val."  ok</font><br>";
	}else{
		echo "<font color=red>".$val." failed!</font><br>";
	}
}

echo "<br><hr><br>";

echo "Test publication by mail<br>";
$smtp->sendway = "MAIL";
foreach($to as $val){
	if($smtp->sendmail($val,"helloxui@126.com","This is subject","This is body",'nothing',"This is sender")){
		echo "<font color=green>".$val." ok</font><br>";
	}else{
		echo "<font color=red>".$val."  failed!</font><br>";
	}
}

echo "<br><hr><br><b>All done!</b>";


?>