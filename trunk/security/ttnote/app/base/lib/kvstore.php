<?php

/*
 * @package base
 * @copyright Copyright (c) 2010, shopex. inc
 * @author edwin.lzh@gmail.com
 * @license 
 * 为了数据安全，请确保persistent方法的调用正确
 */
class base_kvstore{

    /*
     * @var string $__instance
     * @access static private
     */
    static private $__instance = array();

    /*
     * @var string $__controller
     * @access private
     */
    private $__controller = null;

    /*
     * @var string $__prefix
     * @access private
     */
    private $__prefix = null;

    /*
     * 构造
     * @var string $prefix
     * @access public
     * @return void
     */
    function __construct($prefix){
        if(defined('FORCE_KVSTORE_STORAGE') && constant('FORCE_KVSTORE_STORAGE')){
            $this->set_controller(kernel::single(FORCE_KVSTORE_STORAGE, $prefix));
        }else{
            if(defined('KVSTORE_STORAGE') && constant('KVSTORE_STORAGE')){
                $this->set_controller(kernel::single(KVSTORE_STORAGE, $prefix));
            }else{
                $this->set_controller(kernel::single('base_kvstore_filesystem', $prefix));
            }
        }
        $this->set_prefix($prefix);
    }

    /*
     * 返回KV_PREFIX
     * @access public
     * @return string
     */
    static public function kvprefix() 
    {
        return (defined('KV_PREFIX')) ? KV_PREFIX : 'default';
    }//End Function

    /*
     * 实例一个kvstore
     * @var string $prefix
     * @access public
     * @return object
     */
    static public function instance($prefix){
        if(!isset(self::$__instance[$prefix])){
            self::$__instance[$prefix] = new base_kvstore($prefix);
        }
        return self::$__instance[$prefix];
    }

    /*
     * 设置prefix
     * @var string $prefix
     * @access public
     * @return void
     */
    public function set_prefix($prefix) 
    {
        $this->__prefix = $prefix;
    }//End Function

    /*
     * 取得prefix
     * @access public
     * @return string
     */
    public function get_prefix() 
    {
        return $this->__prefix;
    }//End Function

    /*
     * 设置kvstore控制器
     * @var object $controller
     * @access public
     * @return void
     */
    public function set_controller($controller) 
    {
        $this->__controller = $controller;
    }//End Function

    /*
     * 得到kvstore控制器
     * @access public
     * @return object
     */
    public function get_controller() 
    {
        return $this->__controller;
    }//End Function

    /*
     * 获取key的内容
     * @var string $key
     * @var mixed &$value
     * @var int $timeout_version
     * @access public
     * @return boolean
     */
    public function fetch($key, &$value, $timeout_version=null){
        if($this->get_controller()->fetch($key, $value, $timeout_version)){
            return true;
        }else{
            return false;
        }
    }

    /*
     * 设置key的内容
     * @var string $key
     * @var mixed &$value
     * @var int $ttl
     * @access public
     * @return boolean
     */
    public function store($key, $value, $ttl=0){
        $this->persistent($key, $value, $ttl);
        return $this->get_controller()->store($key, $value, $ttl);
    }

    /*
     * 删除key的内容
     * @var string $key
     * @var int $ttl
     * @access public
     * @return boolean
     */
    public function delete($key, $ttl=1) 
    {
        if($this->fetch($key, $value)){
            return $this->store($key, $value, ($ttl>0)?$ttl:1);    //todo: 不实际删除，由cron统一处理delete
        }
        return true;
    }//End Function

    /*
     * 数据持久化
     * @var string $key
     * @var mixed &$value
     * @var int $ttl
     * @access public
     * @return void
     */
    public function persistent($key, $value, $ttl=0) 
    {
        if(get_class($this->get_controller()) != 'base_kvstore_mysql' && kernel::is_online()){
            kernel::single('base_kvstore_mysql', $this->get_prefix())->store($key, $value, $ttl);  //todo: 持久化
        }
    }//End Function
}