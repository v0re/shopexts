<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
function widget_cfg_goods($app){
    $o = &app::get('b2c')->model('goods');
    return $o->orderBy();
}
?>
