<?php 

echo confirm_datasafe_compiled("datasafe");
echo "\n";
$config = "/etc/shopex/skomart.com/setting.conf";
#$config = "skomart.com";
$text = 'hi man';
$encrypt_text = null;
/*
$encrypt_text = shopex_data_encrypt($config,$text);
var_dump($encrypt_text);

$decrypt_text = shopex_data_decrypt($config,$encrypt_text);
var_dump($decrypt_text);
*/

$encrypt_text = NULL;
$isok = shopex_data_encrypt_ex($config,$text,$encrypt_text);
var_dump($encrypt_text);

$decrypt_text = NULL;
$decrypt_text = shopex_data_decrypt($config,$encrypt_text,$decrypt_text);
var_dump($decrypt_text);

echo "\ntest done!\n";

?>