<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
function widget_cfg_virtualcat($app){
    $o = &app::get('b2c')->model('goods_virtual_cat');
    return $o->getMapTree(0,'');
}
?>
