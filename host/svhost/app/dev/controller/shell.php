<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class dev_ctl_shell extends base_controller{
    
    function index(){
        $this->display('shell.html');
    }

    function exec(){
        $shell = new base_shell_webproxy;
        $shell->exec_command($_GET['cmd']);
    }

}