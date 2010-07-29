<?php

class base_cache_memcached extends base_cache_abstract implements base_interface_cache
{
    private $cacheObj = null;

    function __construct() 
    {
        $this->cacheObj = new Memcached;
        $this->connect();
        $this->check_vary_list();
    }//End Function

    public function connect() 
    {
        if(defined('CACHE_MEMCACHE_CONFIG') && constant('CACHE_MEMCACHE_CONFIG')){
            $config = explode(',', CACHE_MEMCACHE_CONFIG);
            foreach($config AS $row){
                $row = trim($row);
                if(strpos($row, 'unix://') === 0){
                    //$this->cacheObj->addServer($row, 0);  todo:memcached不支持unix://
                }else{
                    $tmp = explode(':', $row);
                    $this->cacheObj->addServer($tmp[0], $tmp[1]);
                }
            }
        }else{
            trigger_error('can\'t load CACHE_MEMCACHE_CONFIG, please check it', E_USER_ERROR);
        }
    }//End Function

    public function fetch($key, &$result) 
    {
        $result = $this->cacheObj->get($key);
        if($this->cacheObj->getResultCode() == Memcached::RES_NOTFOUND){
            return false;
        }else{
            return true;
        }
    }//End Function

    public function store($key, $value) 
    {
        return $this->cacheObj->set($key, $value);
    }//End Function

    public function status() 
    {
        $status = $this->cacheObj->getStats();
        foreach($status AS $key=>$value){
            $return['服务器']   = $key;
            $return['缓存获取'] = $value['cmd_get'];
            $return['缓存存储'] = $value['cmd_set'];
            $return['可使用缓存'] = $value['limit_maxbytes'];
        }
        return $return;
    }//End Function
    
}//End Class
