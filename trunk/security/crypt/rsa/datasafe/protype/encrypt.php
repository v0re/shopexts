<?php

$source = $argv[1];
$fp=fopen ("publkey","r");
$pub_key=fread($fp,8192);
fclose($fp);
openssl_get_publickey($pub_key);
openssl_public_encrypt($source,$crypttext,$pub_key);
$crypttext = base64_encode($crypttext);
echo $crypttext;
