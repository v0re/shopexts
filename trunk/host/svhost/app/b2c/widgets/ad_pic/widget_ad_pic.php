<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
function widget_ad_pic(&$setting,&$app){
    return $setting;
    $output = $app->model('system/frontend');
    if($output->theme){
        $theme_dir = kernel::base_url().'themes/'.$output->theme;
    }else{
        $theme_dir = kernel::base_url().'themes/'.$app->getConf('system.ui.current_theme');
    }
    $setting['ad_pic'] = str_replace('%THEME%',$theme_dir,$setting['ad_pic']);
}
?>
