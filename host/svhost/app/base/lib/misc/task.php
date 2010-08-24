<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class base_misc_task{

    function week(){

    }

    function minute(){

    }

    function hour(){

    }

    function day(){
        $this->auto_delete_kvstore();
    }

    function month(){

    }

    private function auto_delete_kvstore() 
    {
        $rows = kernel::database()->select('SELECT `prefix`, `key` FROM sdb_base_kvstore WHERE ttl>0 AND (dateline+ttl)<'.time());
        foreach($rows AS $row){
            $single = base_kvstore::instance($row['prefix']);
            $single->get_controller()->delete($row['key']);
            if(get_class($single->get_controller()) != 'base_kvstore_mysql'){
                kernel::single('base_kvstore_mysql', $row['prefix'])->delete($row['key']);
            }//todo: 删除持久化数据
        }
    }//End Function 

}
