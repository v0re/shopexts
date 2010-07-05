<?php
$msg = $argv[1];
$crypttext = base64_decode($msg);
$fp=fopen("privkey","r");
$priv_key=fread($fp,8192);
fclose($fp);
$passphrase = null;
$res = openssl_get_privatekey($priv_key,$passphrase);
openssl_private_decrypt($crypttext,$newsource,$res);
echo $newsource;

