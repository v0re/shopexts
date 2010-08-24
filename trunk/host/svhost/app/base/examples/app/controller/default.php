<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class %*APP_NAME*%_ctl_default extends base_controller{
    
    function index(){
        $this->pagedata['project_name'] = '%*APP_NAME*%';
        $this->display('default.html');
    }
    
}