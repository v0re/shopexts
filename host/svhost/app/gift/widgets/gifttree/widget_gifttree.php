<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
function widget_gifttree(&$setting,&$system){
    //$o=$system->loadModel('content/article');
    $gift = app::get('gift')->model('cat');
    $result=$gift->getList('*', array('ifpub'=>'true'));
    foreach($result as $k=>$v){
        $return[$v['cat_id']]['link']=$v['cat_id'];
        $return[$v['cat_id']]['cat_name']=$v['cat_name'];
        $return[$v['cat_id']]['sub'] = app::get('gift')->model('goods')->getList('*', array('goods_type'=>'gift', 'marketable'=>'true'));
    }
    return $return;
    
}
?>
