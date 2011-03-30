<?php
function widget_flashview(&$setting,&$system){
    $setting[allimg]="";
    $setting[allurl]="";
    $output = &$system->loadModel('system/frontend');
    $theme_dir = $system->base_url().'themes/'.$output->theme;
    if(!$setting['flash']){
       foreach($setting['img'] as $value){
            $rvalue = str_replace('%THEME%',$theme_dir,$value);
            $setting[allimg].=$rvalue."|";
            $setting[allurl].=urlencode($value["url"])."|";
       }
    }else{
        foreach($setting['flash'] as $key=>$value){
            if($value['pic']){
                if($value["url"]){
                    $value["link"]=$value["url"];
                }
                $rvalue = str_replace('%THEME%',$theme_dir,$value['pic']);
                $setting[allimg].=$rvalue."|";
                $setting[allurl].=urlencode($value["link"])."|";
            }
        }
    }
    return $setting;
}
?>
