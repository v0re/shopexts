<?php
error_reporting( E_ERROR | E_WARNING | E_PARSE );
ob_start();
if(include(dirname(__FILE__).'/../config/config.php')){
    define('PHP_SELF',dirname(dirname($_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'])));
    ob_end_clean();
    require(CORE_DIR.'/include/shopCore.php');
    require_once(CORE_DIR.'/func_ext.php');
    class pluginCore extends shopCore{
        function run(){}
    }

    $system = new pluginCore(array());
}else{
    header('HTTP/1.1 503 Service Unavailable',true,503);
    die('<h1>Service Unavailable</h1>');
}
?>
