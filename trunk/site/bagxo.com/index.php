<?php
define('PERPAGE',10);
define('RUN_IN','FRONT_END');
ob_start();
error_reporting( E_ERROR | E_WARNING | E_PARSE );
if(file_exists('config/config.php')){
    require('config/config.php');
    ob_end_clean();
    require(CORE_DIR.'/include/shopCore.php');
    new shopCore();
}else header('Location: install/');
?>
