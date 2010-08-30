<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class vhostlist extends PHPUnit_Framework_TestCase{
    
    public function setUp() {
        $this->model= kernel::single('svhost_mdl_vhostlist');
    }

    public function testInsert(){
        $sdf = array(
            'domain'=>'test.com',
            'server_id'=>1,
            'ip'=>'127.10.10.1',
            'db'=>array(
                'host'=>'127.0.0.1',
                'port'=>'3306',
                'name'=>'test',
                'user'=>'test',
                'password'=>'test',
            ),
            'ftp'=>array(
                'user'=>'test',
                'password'=>'test',
            ),
        );
        $this->model->save($sdf);
    }

}
