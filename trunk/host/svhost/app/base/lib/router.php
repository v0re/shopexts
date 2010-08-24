<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class base_router implements base_interface_router{

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
        $controller = array_shift($query_args);
        $action = array_shift($query_args);
        if($controller == 'index.php'){
            $controller = '';
        }
        foreach($query_args as $i=>$v){
            if($i%2){
                $k = $v;
            }else{
                $params[$k] = $v;
            }
        }

        $controller = $controller?$controller:'default';
        $action = $action?$action:'index';
        $controller = $this->app->controller($controller);
        kernel::request()->set_params($params);

        $controller->$action();
    }

}
