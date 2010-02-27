<?php


$message = "TranAmt=1.8|transName=MerchantTest|MercCode=990108160003311|TranAbbr=IPER|TermSsn=15144037|OAcqSsn=|MercDtTm=20090615144037|TermCode=001|Remark1=|Remark2=|submit=%3F%A8%A2%3F%3F|OSttDate=|MercUrl=http%3A%2F%2F10.112.9.182%3A81%2Flocal%2Freceive.asp";

$cmd = "java spdbsign \"{$message}\" ";

exec($cmd,$ret);

$signed = $ret[0];
echo $signed;

echo "\n";

unset($ret);
$signed = '4a0532b6c873a683e95968ef9888baa568872c37202d2fd55bc5584b85e110a36b6eeeb2300179ff49c6a6cfb96dd509dea495a48d0c2141738d0cdb8813198af5267e467f8beecfa421bd4ebcc70111ed601feb53aa8d2d7715333c3c4d9090b89c7af1de3b633ac45ff564f1820a8ae16be82f32b8aea6bf16f26a110aa12a';
$plain = 'TranAbbr=IPER|AcqSsn=000000073601|MercDtTm=20090615144037|TermSsn=15144037|RespCode=00|TermCode=00000000|MercCode=990108160003311|TranAmt=1.80|SettDate=20090608';
$cmd = "java spdbverify  \"{$signed}\" \"{$plain}";

exec($cmd,$ret);

var_export($ret);
?>