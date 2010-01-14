<?php
class testsimplestore extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $system = &$GLOBALS['system'];
        require CORE_INCLUDE_DIR."/simplestore.php";
        $this->model = new simplestore;  
        $name = HOME_DIR."/logs/".date("Y.m");
        unlink($name.".php");
        $this->model->workat($name);
        $this->db = &$system->database();

        $this->length = 11;
    }

    function randomstring($length)
    {
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ!@#$%^&*()_+';
        for($i=0;$i<$length;$i++)
        {
            $key .= $pattern{mt_rand(0,strlen($pattern))};    //生成php随机数
        }
        return $key;
    }

    function testSize(){
        $size = $this->model->size();

        $this->assertEquals($size,1);

    }

    function testStore(){
       for($i=0;$i<$this->length;$i++){
            $entry = array(
                'type'=>$i,
                'time'=>time(),
                'source'=>remote_addr(),
                'user'=>"kyle\txu\n",
                'event'=>'我很饿，我党是伟fsdfsfsfserfewrewrfefsfes大的是不朽的！',
            );       
            $this->model->store($entry);
       }
       $size = $this->model->size();
      
       $this->assertEquals($size,$lenght);    

    }

    function testfetch(){
        for($i=0;$i<1;$i++){
            $to = rand(0,$this->length);
            $ret = $this->model->fetch($to);

        }
    }
}
