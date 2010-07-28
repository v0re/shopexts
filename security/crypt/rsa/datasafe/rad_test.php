<?php 

function gen_string($len)
{ 
    $chars=’ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz’; // characters to build the password from 
    $string=”; 
    for(;$len>=1;$len–) 
    {
        $position=rand()%strlen($chars);
        $string.=substr($chars,$position,1); 
    }
    return $string; 
}


$config = "/etc/shopex/skomart.com/setting.conf.en";
$text = gen_string(1024);
$encrypt_text = null;

$encrypt_text = NULL;
shopex_data_encrypt_ex($config,$text,$encrypt_text);
var_dump($encrypt_text);

$decrypt_text = NULL;
shopex_data_decrypt_ex($config,$encrypt_text,$decrypt_text);
var_dump($decrypt_text);




?>