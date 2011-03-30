<?php
function widget_slideEffectBanner(&$setting,&$system){
    $output = &$system->loadModel('system/frontend');
    $theme_dir = $system->base_url().'themes/'.$output->theme;
    foreach($setting['pic'] as $k=>$v){
            $setting['pic'][$k] = str_replace('%THEME%',$theme_dir,$v);
    }
}
?>