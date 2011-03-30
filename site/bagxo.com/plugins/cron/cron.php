<?php
ob_start();
if(include('../../config/config.php')){
    ob_end_clean();
    require(CORE_DIR.'/include/crontab.php');
    new crontab();
}else header('Location: install/');
