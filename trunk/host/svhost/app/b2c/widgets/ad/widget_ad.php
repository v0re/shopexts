<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
function widget_ad(&$setting,&$app){
    return;
    $output = &$app->model('system/frontend');
    if($theme=$output->theme){
        $theme_dir = $app->base_url().'themes/'.$theme;
    }else{
        $theme_dir = $app->base_url().'themes/'.$app->getConf('system.ui.current_theme');
    }
    foreach($setting['ad'] as $ad){
        $ad['link'] = str_replace('%THEME%',$theme_dir,$ad['link']);
        $data[] = $ad;
    }
    return $data;

}

?>
