<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
function widget_brand($setting,&$app){
    //return; //todo 修正widget
    //echo 'brand';
    $oGoods=&app::get('b2c')->model('brand');
    $g=$oGoods->getAll();
    return $g;
}
?>
