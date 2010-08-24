<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
function widget_logo($setting,&$system){
    $logo_id = app::get('b2c')->getConf('site.logo');
    $result['logo_image'] = base_storager::image_path($logo_id);
    return $result;
}
?>
