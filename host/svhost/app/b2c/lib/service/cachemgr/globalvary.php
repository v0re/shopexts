<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

/**
 * 缓存数据键值
 */
class b2c_service_cachemgr_globalvary{
    
    function get_varys(){
        $aTmp = array(
                        'MLV' => $_COOKIE['MLV'],
                        'CUR' => $_COOKIE['CUR'],
                    );
       return $aTmp;
    }
}
