<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
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
