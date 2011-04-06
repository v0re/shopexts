<?php
require('shopCore.php');
class shopPreview extends shopCore{

    function run(){
    }

    function setConf($key,$value){
        $this->__cfg[$key] = $value;
    }

    function getConf($key){
        if(isset($this->__cfg[$key])){
            return $this->__cfg[$key];
        }else{
            return parent::getConf($key);
        }
    }

    function view($request){
        $this->display($this->_frontend($request));
    }

}
?>
