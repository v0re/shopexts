<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
function widget_search(&$setting,&$smarty){
    $setting['search']=$GLOBALS['search'];
    $data = app::get('b2c')->getConf('search.show.range');
    //error_log($data,3,'log.log');
    return $data;
}
?>
