<?php
class base_setting{

    var $_cfg;
    
    function __construct($app){
        $this->app = $app;
    }

    function &source(){
        include($this->app->app_dir.'/setting.php');
        return $setting;
    }
}
