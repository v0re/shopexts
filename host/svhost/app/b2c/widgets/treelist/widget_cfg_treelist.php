<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
function widget_cfg_treelist($app){
    return '';
    $o=$app->model('content/sitemap');
    return $o->getList();
}
?>
