<?php

class Person{
    var $_database = './protypedata.php';
    function openDb($file='./protypedata.php'){
        $this->_database = $file;
        $this->_database = fopen($this->_database,'ab+');
    }

    function writeRecord($data){
        $name = pack('a8',$data['name']);
        $age = pack('S',$data['age']);
        $email = pack('a30',$data['email']);
        fwrite($this->_database,$name.$age.$email);
    }

    function read($count=0){
        rewind($this->_database);
        fseek($this->_database,40 * $count);
        $return = array();
        $return = unpack("a8name/S1age/a30email",fread($this->_database,40));
       

        $return = unpack("V1offset",'a');

        print_r($return);

        die();
//        $return['name'] = unpack('a8', fread($this->_database, 8));
//   
//        $return['name'] = $return['name'][1];
//        $return['age'] = unpack('S', fread($this->_database, 2));
//        $return['age'] = $return['age'][1];
//        $return['email'] = unpack('a30', fread($this->_database, 30));
//        $return['email'] = $return['email'][1];
        
        return $return;
    }

}

$me = array(
    'name'=>'ĞìÏòÇ°',
    'age'=>99,
    'email'=>'kyle@shopcare.net',
);

$p = new Person;
$p->openDb();
$p->writeRecord($me);
print_r($p->read(1));