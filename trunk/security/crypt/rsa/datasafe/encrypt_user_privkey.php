<?php 
$plain_config_file = "/etc/shopex/skomart.com/sec.pem.z";
$plain_private_key = shopex_get_user_private_key($plain_config_file);
$save_file = "/etc/shopex/skomart.com/sec.pem.en";
shopex_set_config_ex($save_file,$plain_private_key);