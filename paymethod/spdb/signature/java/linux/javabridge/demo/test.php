<?php
require_once("java/Java.inc");
java_require("test.jar;spdbmerchant.jar;spdbmerchant.jar;bcprov-jdk14-127.jar;jsse.jar;jnet.jar;jcert.jar;jce.jar;");

header("content-type:text/html; charset=utf-8");
// get instance of Java class java.lang.System in PHP 
$system = new Java('java.lang.System'); 

// demonstrate property access 
print 'Java version='.$system->getProperty('java.version').' <br>'; 
print 'Java vendor=' .$system->getProperty('java.vendor').' <br>'; 
print 'OS='.$system->getProperty('os.name').' '. 
$system->getProperty('os.version').' on '. 
$system->getProperty('os.arch').' <br>'; 

// java.util.Date example 
$formatter = new Java('java.text.SimpleDateFormat', 
"EEEE, MMMM dd, yyyy 'at' h:mm:ss a zzzz"); 

print $formatter->format(new Java('java.util.Date')); 

//mERCHANT SIGNATURE
$merverify = new Java('com.csii.payment.client.core.MerchantSignVerify');

$plain="TranAmt=1.8|transName=MerchantTest|MercCode=990108160003311|TranAbbr=IPER|TermSsn=15144037|OAcqSsn=|MercDtTm=20090615144037|TermCode=001|Remark1=|Remark2=|submit=%3F%A8%A2%3F%3F|OSttDate=|MercUrl=http%3A%2F%2F10.112.9.182%3A81%2Flocal%2Freceive.asp";
$signature=$merverify->merchantSignData_ABA($plain);

print $signature.'<br>';

//VERIFY PAYGATE SIGNATURE

$plainfrompaygate="TranAbbr=IPER|AcqSsn=000000073601|MercDtTm=20090615144037|TermSsn=15144037|RespCode=00|TermCode=00000000|MercCode=990108160003311|TranAmt=1.80|SettDate=20090608";
$signaturefrompaygate="4a0532b6c873a683e95968ef9888baa568872c37202d2fd55bc5584b85e110a36b6eeeb2300179ff49c6a6cfb96dd509dea495a48d0c2141738d0cdb8813198af5267e467f8beecfa421bd4ebcc70111ed601feb53aa8d2d7715333c3c4d9090b89c7af1de3b633ac45ff564f1820a8ae16be82f32b8aea6bf16f26a110aa12a";

$verify=$merverify->merchantVerifyPayGate_ABA($signaturefrompaygate,$plainfrompaygate);

//1-true,none-false
print $verify;



?>