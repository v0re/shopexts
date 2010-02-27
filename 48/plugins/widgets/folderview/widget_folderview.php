<?php
function widget_folderview(&$setting,&$system){
    $output = $system->loadModel('system/frontend');
    if($output->theme){
        $theme_dir = $system->base_url().'themes/'.$output->theme;
    }else{
        $theme_dir = $system->base_url().'themes/'.$system->getConf('system.ui.current_theme');
    }
    $setting['ad_pic'] = str_replace('%THEME%',$theme_dir,$setting['ad_pic']);
    return $setting;
}
?>
