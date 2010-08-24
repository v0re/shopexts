<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class base_mdl_rpcnotify extends base_db_model{

    function filter($filter){
        unset($filter['use_like']);
        return parent::filter($filter);
    }
}
