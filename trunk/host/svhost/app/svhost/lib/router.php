<?php
class ttnote_router implements base_interface_router{

    function __construct($app){
        $this->app = $app;
    }

    function gen_url($params=array(),$full=false){

        $url_array = array(
            $params['ctl']?$params['ctl']:'default',
            $params['act']?$params['act']:'index',
            );

        unset($params['ctl'],$params['act']);

        foreach($params as $k=>$v){
            $url_array[] = $k;
            $url_array[] = $v;
        }

        return $this->app->base_url($full).implode('/',$url_array);
    }

    function dispatch($query){
        $query_args = explode('/',$query);
        $action = array_shift($query_args);
        $action = $action ? $action : 'index';
        $params = $query_args;

        $controller = $this->app->controller('default');   
        kernel::request()->set_params($params);		
        
        if(!in_array($action,get_class_methods($controller))){
        	die('not good really');
        }
        if(is_array($params)){
        	call_user_func_array(array($controller,$action),$params);
    	}else{    		
        	$controller->$action();
    	}
    }

}
