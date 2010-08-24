<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_service_view_menu{
    function function_menu(){
        $shop_base = app::get('site')->router()->gen_url(array('app'=>'site', 'ctl'=>'default'));
        $html[] = "<a href='$shop_base' target='_blank'>浏览商店</a>";
        return $html;
    
    }
}