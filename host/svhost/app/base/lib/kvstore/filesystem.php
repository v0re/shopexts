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
class base_kvstore_filesystem extends base_kvstore_abstract implements base_interface_kvstore 
{

    public $header = '<?php exit(); ?>';

    function __construct($prefix) 
    {
        $this->prefix= $prefix;
        $this->header_length = strlen($this->header);
    }//End Function

    public function store($key, $value, $ttl=0) 
    {
        $this->check_dir();
        $data = array();
        $data['value'] = $value;
        $data['expire'] = ($ttl) ? time()+$ttl : 0;
        $org_file = $this->get_store_file($key);
        $tmp_file = $org_file . '.' . str_replace(' ', '.', microtime()) . '.' . mt_rand();
        if(file_put_contents($tmp_file, $this->header.serialize($data))){
            if(copy($tmp_file, $org_file)){
                @unlink($tmp_file);
                return true;
            }
        }
        return false;
    }//End Function

    public function fetch($key, &$value, $timeout_version=null) 
    {
        $file = $this->get_store_file($key);
        if(file_exists($file) && $timeout_version < filemtime($file) ){
            $data = unserialize(substr(file_get_contents($file),$this->header_length));
            if($data['expire'] == 0 || $data['expire'] >= time()){
                $value = $data['value'];
                return true;
            }
        }
        return false;
    }//End Function

    public function delete($key) 
    {
        $file = $this->get_store_file($key);
        if(file_exists($file)){
            return @unlink($file);
        }
        return false;
    }//End Function

    private function check_dir() 
    {
        if(!is_dir(ROOT_DIR.'/data/kvstore/'.$this->prefix)){
            utils::mkdir_p(ROOT_DIR.'/data/kvstore/'.$this->prefix);
        }
    }//End Function

    private function get_store_file($key) 
    {
        return ROOT_DIR.'/data/kvstore/'.$this->prefix.'/'.$this->create_key($key).'.php';
    }//End Function
}//End Class
