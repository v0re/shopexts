<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class APP_ID_ctl_DEFAULT_CTL extends desktop_controller{
    function index(){
        $this->pagedata['my_location'] = realpath(dirname(__FILE__)."/../view/default.html");
        $this->display('default.html');
    }
}