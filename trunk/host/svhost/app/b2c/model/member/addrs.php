<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_mdl_member_addrs extends dbeav_model{

    function save(&$data,$mustUpdate=null){
        if($data['area'])
        $data['area'] = $data['area']['area_type'].':'.implode('/',$data['area']['sar']).':'.$data['area']['id'];    

        return parent::save($data,$mustUpdate);
    }

}  
