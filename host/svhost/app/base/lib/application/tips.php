<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class base_application_tips{
    
    static function tip_apps(){
        $apps = array();
        $lang = app::get_lang();
        if ($handle = opendir(APP_DIR)) {
            while (false !== ($file = readdir($handle))) {
                if($file{0}!='.' && is_dir(APP_DIR.'/'.$file) && file_exists(APP_DIR.'/'.$file.'/lang/'.$lang.'/tips.txt')){
                    $apps[] = $file;
                }
            }
            closedir($handle);
        }
        return $apps;
    }
    
    static function tips_item_by_app($app_id){
        $lang = app::get_lang();
        $tips = array();
        foreach(file(APP_DIR.'/'.$app_id.'/lang/'.$lang.'/tips.txt')  as $tip){
            $tip = trim($tip);
            if($tip){
                $tips[] = $tip;
            }
        }
        return $tips;
    }
    
    static function tip(){
        
        $apps = self::tip_apps();
        $key = array_rand($apps);
        $app_id = $apps[$key];
        
        $tips = self::tips_item_by_app($app_id);
        $key = array_rand($tips);
        return $tips[$key];
    }
    
}