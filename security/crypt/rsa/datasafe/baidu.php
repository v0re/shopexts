<?php 

$config = "baidu.com";

$text = 'hi man';

$encrypt_text = NULL;
shopex_data_encrypt_ex($config,$text,$encrypt_text);
var_dump($encrypt_text);


$decrypt_text = NULL;
shopex_data_decrypt_ex($config,$encrypt_text,$decrypt_text);
var_dump($decrypt_text);


echo "\ntest done!\n";

?>