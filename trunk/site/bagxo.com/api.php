<?php
if(file_exists('config/config.php')){
    require('config/config.php');
    require(CORE_DIR.'/api/shop_api.php');
    new shop_api();
}else{
    header('HTTP/1.1 404 Not Found',true,'404');
}
?>