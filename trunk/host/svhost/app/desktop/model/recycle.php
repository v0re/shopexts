<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_mdl_recycle extends dbeav_model{

    function  save(&$data,$mustUpdate = null){
        $return = parent::save($data,$mustUpdate);
    }
    function get_item_type(){
        $rows = $this->db->select('select distinct(item_type) from '.$this->table_name(true).' ');
        return $rows;
    }
}
