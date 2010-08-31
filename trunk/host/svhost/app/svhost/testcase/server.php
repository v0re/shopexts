<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class server extends PHPUnit_Framework_TestCase{
    
    public function setUp() {
        $this->model = kernel::service('svhost_server', array('content_path'=>'svhost_server'));
    }

    public function testCreate(){
        $para = unserialize('a:8:{s:8:"vhost_id";s:1:"6";s:9:"server_id";s:1:"1";s:6:"domain";s:8:"dddd.com";s:2:"ip";s:13:"75.125.222.26";s:2:"db";a:5:{s:4:"host";s:9:"127.0.0.1";s:4:"port";s:4:"3306";s:4:"name";s:7:"ddddcom";s:4:"user";s:7:"ddddcom";s:8:"password";s:8:"A18bwdCb";}s:3:"ftp";a:2:{s:4:"user";s:7:"ddddcom";s:8:"password";s:8:"878pQHuJ";}s:8:"disabled";s:5:"false";s:8:"ordernum";N;}');
        $this->model->run_queue($id,$para);
    }

}
