<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class base_mdl_apps extends base_db_model{

    function filter($filter){
        $addons = array();
        if(isset($filter['installed'])){
            $addons[] = $filter['installed']?'status!="uninstalled"':'status="uninstalled"';
            unset($filter['installed']);
        }
        $addons = implode(' AND ',$addons);
        if($addons) $addons.=' AND ';
        return $addons.parent::filter($filter);
    }

}
