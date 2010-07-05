
<?php
$res=openssl_pkey_new();
// // Get private key
openssl_pkey_export($res, $privkey);
file_put_contents('privkey',$privkey);
// // Get public key
$pubkey=openssl_pkey_get_details($res);
$pubkey=$pubkey["key"];
file_put_contents('publkey',$pubkey);
