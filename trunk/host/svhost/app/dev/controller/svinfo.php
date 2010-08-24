<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class dev_ctl_svinfo extends desktop_controller{
function index(){
        
        $svinfo = kernel::single('dev_serverinfo');
        $this->pagedata['info'] = $svinfo->run();
        $this->display('svinfo.html');
    }
}