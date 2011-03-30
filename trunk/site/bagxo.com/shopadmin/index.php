<?php
define('RUN_IN','BACK_END');
ob_start();
error_reporting( E_ERROR | E_WARNING | E_PARSE );
if(!include('../config/config.php')){
    header('Location: ../install/');
    exit();
}
ob_end_clean();

require(CORE_DIR.'/include/adminCore.php');
new adminCore();
?>
