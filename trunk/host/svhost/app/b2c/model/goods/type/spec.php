<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_mdl_goods_type_spec extends dbeav_model{
    var $has_many = array(
    );

    function get_type_spec($type_id){
        return $this->getList('*',array('type_id'=>$type_id));
    }
}
