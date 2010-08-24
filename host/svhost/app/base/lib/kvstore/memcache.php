<?php

class base_kvstore_memcache extends base_kvstore_abstract implements base_interface_kvstore 
{
    private $cacheObj = null;

    function __construct($prefix) 
    {
        $this->connect();
        $this->prefix = $prefix;
    }//End Function

    public function connect() 
    {
        $this->cacheObj = new Memcache;
        if(defined('KVSTORE_MEMCACHE_CONFIG') && constant('KVSTORE_MEMCACHE_CONFIG')){
            $config = explode(',', KVSTORE_MEMCACHE_CONFIG);
            foreach($config AS $row){
                $row = trim($row);
                if(strpos($row, 'unix:///') === 0){
                    $this->cacheObj->addServer($row, 0);
                }else{
                    $tmp = explode(':', $row);
                    $this->cacheObj->addServer($tmp[0], $tmp[1]);
                }
            }
        }else{
            trigger_error('can\'t load KVSTORE_MEMCACHE_CONFIG, please check it', E_USER_ERROR);
        }
    }//End Function

    public function fetch($key, &$value, $timeout_version=null) 
    {
        $store = $this->cacheObj->get($this->create_key($key));
        if($store !== false){
            if($timeout_version < $store['dateline']){
                if($store['ttl'] > 0 && ($store['dateline']+$store['ttl']) < time()){
                    return false;
                }
                $value = $store['value'];
                return true;
            }
        }
        return false;
    }//End Function

    public function store($key, $value, $ttl=0) 
    {
        $store['value'] = $value;
        $store['dateline'] = time();
        $store['ttl'] = $ttl;
        return $this->cacheObj->set($this->create_key($key), $store, MEMCACHE_COMPRESSED, 0);
    }//End Function

    public function delete($key) 
    {
        return $this->cacheObj->delete($this->create_key($key));
    }//End Function

}//End Class