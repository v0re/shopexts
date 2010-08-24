<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class sample extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
      
    }

    public function tearDown(){

    }
    
    public function testSample(){
        echo "do some thins here";
    }   
    
    public function testEqual(){
        $this->assertEquals(0,false);
    }
    
    public function testNotEqual(){
        $this->assertEquals(1,false);
    }
    
}
