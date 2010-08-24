<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class base_shopnode
{
    static $snode= null;
    
    static function register($app_id, $data=null){
        $obj_app = app::get($app_id);
        // 生成参数...
		$project_name = str_replace($_SERVER['DOCUMENT_ROOT'], "", str_replace("\\", "/", realpath(dirname(__FILE__).'/../../../../')));
        $data = array(
            'certi_app'=>'node.reg',
            'certificate_id' => base_certificate::certi_id(),
            'node_type' => 'ecos.' . $app_id,
			'url' => $_SERVER['SERVER_NAME'] . $project_name,
        );
        $data['certi_ac'] = self::gen_sign_ac($data);
        
        // 申请获取一个唯一node_id
        $http = kernel::single('base_httpclient');
        $http->timeout = 3;
        $result = $http->post(
            LICENSE_CENTER,
            $data);
        
        $result = json_decode($result, true);
        if ($result['res'] == 'succ')
        {
            self::set_node_id($result['info'], $app_id);
        }
        else
        {
            return false;
        }
    }
    
    static function get($code='node_id', $app_id='b2c'){
        
        if(!function_exists('get_node_id')){
            if(self::$snode===null){
                if($shopnode = app::get($app_id)->getConf('shop_site_node_id')){
                    self::$snode = unserialize($shopnode);
                }else{
					self::$snode = array();
                }
            }
        }else{
            self::$snode = get_node_id();
        }
        
        return self::$snode[$code];
    }
    
    static function active($app_id='b2c'){
        if(self::get('node_id', $app_id)){
            kernel::log('Using exists shopnode: kvstore shop_site_node_id');
        }else{
            kernel::log('Request new shopnode');
            self::register($app_id);
        }
    }
    
    static function set_node_id($node_id, $app_id='b2c'){
        if(!function_exists('set_node_id')){
            // 存储kvstore.
            return app::get($app_id)->setConf('shop_site_node_id', serialize($node_id));
        }else{
            return set_node_id($node_id, $app_id);
        }
    }
    
    static function delete_node_id($app_id='b2c')
    {
        if (!function_exists('delete_node_id'))
        {
            return app::get($app_id)->setConf('shop_site_node_id', '');
        }
        else
        {
            return delete_node_id($app_id);
        }
    }
    
    /**
     * 转给接口ac验证用
     * @param array 需要验证的参数
     * @return string 结构sign
     */
    static function gen_sign_ac($params=array())
    {
        if ($params)
        {
            $sign = self::assemble_params_ac($params);
            return strtoupper(md5($sign.base_certificate::token()));
        }
        
        return '';
    }
    
    static function assemble_params_ac($params, $level=false) 
    {
        if(!is_array($params))  return null;
        $sign = '';
        ksort($params);
        foreach($params AS $value){
            if($level == true){
                $sign .= sprintf('{"%s"}', (is_array($value)) ? self::assemble_params_ac($value, true) : addslashes($value));
            }else{
                $sign .= (is_array($value)) ? self::assemble_params_ac($value, true) : $value;
            }
        }
        return $sign;
    }//End Function
    
    static function node_id($app_id='b2c'){ return self::get('node_id', $app_id); }
    
    static function node_type($app_id='b2c'){ return self::get('node_type', $app_id); }
}