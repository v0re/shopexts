<?php 
$plain_config_file = "/etc/shopex/skomart.com/setting.conf";
$config = file_get_contents($plain_config_file);
$save_file = "/etc/shopex/skomart.com/setting.conf.en";
shopex_set_config_ex($save_file,$config);