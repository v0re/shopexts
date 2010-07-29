<?php
class base_kvstore_dba extends base_kvstore_abstract implements base_interface_kvstore 
{
    private $rs = null;

    function __construct($prefix) 
    {
        $this->rs = dba_popen(DATA_DIR.'/kvstore/dba.db','c');
    }//End Function

    public function fetch($key, &$value, $timeout_version=null) 
    {
        $store = dba_fetch($this->create_key($key),$this->rs);
        $store = unserialize($store);
        if($store !== false && $timeout_version < $store['dateline']){
            if($store['ttl'] > 0 && ($store['dateline']+$store['ttl']) < time()){
                return false;
            }
            $value = $store['value'];
            return true;
        }
        return false;
    }//End Function

    public function store($key, $value, $ttl=0) 
    {
        $store['value'] = $value;
        $store['dateline'] = time();
        $store['ttl'] = $ttl;
        return dba_replace($this->create_key($key), serialize($store), $this->rs);
    }//End Function

    public function delete($key) 
    {
        return dba_delete($this->create_key($key),$this->rs);
    }//End Function

}//End Class