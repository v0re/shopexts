<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */

class site_route_static
{
    private $_kvprefix = 'site_route/statics';
    
    public function set_dispath($key, $val) 
    {
        return base_kvstore::instance($this->_kvprefix.'/dispath')->store($key, $val);
    }//End Function

    public function get_dispath($key) 
    {
       if(base_kvstore::instance($this->_kvprefix.'/dispath')->fetch($key, $val)){
           return $val;
       }else{
           return false;
       }
    }//End Function

    public function del_dispath($key) 
    {
        return base_kvstore::instance($this->_kvprefix.'/dispath')->delete($key);
    }//End Function

    public function set_genurl($key, $val) 
    {
        return base_kvstore::instance($this->_kvprefix.'/genurl')->store($key, $val);
    }//End Function

    public function get_genurl($key) 
    {
       if(base_kvstore::instance($this->_kvprefix.'/genurl')->fetch($key, $val)){
           return $val;
       }else{
           return false;
       }
    }//End Function

    public function del_genurl($key) 
    {
        return base_kvstore::instance($this->_kvprefix.'/genurl')->delete($key);
    }//End Function

}//End Class