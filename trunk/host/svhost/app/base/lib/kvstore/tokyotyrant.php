<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

/*
 * @package base
 * @copyright Copyright (c) 2010, shopex. inc
 * @author edwin.lzh@gmail.com
 * @license 
 */
class base_kvstore_tokyotyrant extends base_kvstore_abstract implements base_interface_kvstore 
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
                    continue;   //暂不支持
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
        $data = $this->cacheObj->get($this->create_key($key));
        if($data !== false){
            $store = unserialize($data);    //todo：反序列化
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
        return $this->cacheObj->set($this->create_key($key), serialize($store), 0, 0);  //todo：不压缩，序列化
    }//End Function

    public function delete($key) 
    {
        return $this->cacheObj->delete($this->create_key($key));
    }//End Function

}//End Class