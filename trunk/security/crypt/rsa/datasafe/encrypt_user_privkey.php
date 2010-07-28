<?php 
$plain_config_file = "/etc/shopex/skomart.com/sec.pem.z";
$config = file_get_contents($plain_config_file);
$save_file = "/etc/shopex/skomart.com/sec.pem.en";
shopex_set_config_ex($save_file,$config);