<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class base_vcode {
	
	var $use_gd = false;

	function __construct(){
		if($this->use_gd){
			$this->obj = kernel::single('base_vcode_gd');	
		}else{
			$this->obj = kernel::single('base_vcode_gif');
		}
	}

    function length($len) {
		$this->obj->length($len);
        return true;
    }    
    
    function verify_key($key){
    	kernel::single('base_session')->start();
    	$_SESSION[$key] = $this->obj->get_code();
    }
    
	static function verify($key,$value){
		kernel::single('base_session')->start();
    	if( $_SESSION[$key] == $value ){
    		return true;
    	}
    	return false;
    }
    
    function display(){
    	$this->obj->display();
    }
}
