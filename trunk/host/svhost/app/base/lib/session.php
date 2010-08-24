<?php
class base_session{
    
    var $sess_id;
    var $sess_key = 's';
    var $__session_started;
    
    function sess_id(){
        return $this->sess_id;
    }

    function start(){
        $cookie_path = kernel::base_url();
        $cookie_path = $cookie_path ? $cookie_path : "/";
    	if(isset($_GET['sess_id'])){
            $this->sess_id = $_GET['sess_id'];
            if($_COOKIE[$this->sess_key] != $_GET['sess_id'])
                setcookie($this->sess_key,$this->sess_id,null ,$cookie_path);
        }elseif($_COOKIE[$this->sess_key]){
            $this->sess_id = $_COOKIE[$this->sess_key];
       }elseif(!$this->sess_id){
            $this->sess_id = md5(microtime().base_request::get_remote_addr().mt_rand(0,9999));
            setcookie($this->sess_key,$this->sess_id,null,$cookie_path);
        }
        if(base_kvstore::instance('sessions')->fetch($this->sess_id, $_SESSION) === false){
            $_SESSION = array();
        }        
        $this->__session_started = true;
        register_shutdown_function(array(&$this,'close'));
        return true;
    }

    function close($writeBack = true){
        if(strlen($this->sess_id) != 32){
            return false;
        }
        if(!$this->__session_started){
            return false;
        }
        $this->__session_started = false;
        if(!$writeBack){
            return false;
        }
        base_kvstore::instance('sessions')->store($this->sess_id,$_SESSION);
        return true;
    }
    
    function destory(){
        if(!$this->__session_started){
            return false;
        }
        $this->__session_started = false;
        base_kvstore::instance('sessions')->store($this->sess_id, array(), 1);
    }

}
