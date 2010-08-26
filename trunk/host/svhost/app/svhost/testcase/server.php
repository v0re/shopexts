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
        $domain = 'test.com';
        $message = null;
        $this->model->create($domain,$message);
    }

}
