<?php 

echo confirm_datasafe_compiled("datasafe");
echo "\n";
$config = "/etc/shopex/skomart.com/setting.conf";
$text = 'hi man';
$encrypt_text = null;
$encrypt_text = shopex_data_encrypt($config,$text);
var_dump($encrypt_text);
/*
$decrypt_text = shopex_data_decrypt($encrypt_text);
var_dump($decrypt_text);
*/
/*
$text = '中午';
$encrypt_text = shopex_data_encrypt($text);
var_dump($encrypt_text);
$decrypt_text = shopex_data_decrypt($encrypt_text);
var_dump($decrypt_text);
*/

echo "\ntest done!\n";
