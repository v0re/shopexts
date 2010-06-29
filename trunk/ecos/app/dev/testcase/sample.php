<?php
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
