<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class site_theme_base 
{

    public function set_default($theme){
        $theme_sytle = $this->get_theme_style($theme);
        if(empty($theme_sytle)){
            $this->set_theme_style($theme, '');
        }//todo：如果无样式或没有选择过样式，强制设置为空字符串
        app::get('site')->model('themes')->update(array('is_used'=>'false'), array());
        app::get('site')->model('themes')->update(array('is_used'=>'true'), array('theme'=>$theme));
        return app::get('site')->setConf('current_theme', $theme);
    }

    public function get_default() 
    {
        return app::get('site')->getConf('current_theme');
    }//End Function

    public function update_theme($aData) 
    {
        return app::get('site')->model('themes')->save($aData);
    }//End Function

    public function set_theme_style($theme, $style) 
    {
        return app::get('site')->setConf('theme_style.'.$theme, $style);
    }//End Function

    public function get_theme_style($theme) 
    {
        return app::get('site')->getConf('theme_style.'.$theme);
    }//End Function

    public function get_view($theme) 
    {
        if ($handle=opendir(THEME_DIR.'/'.$theme)){
            $views = array();
            while(false!==($file=readdir($handle))){
                if ($file{0}!=='.' && $file{0}!=='_' && is_file(THEME_DIR.'/'.$theme.'/'.$file) && (($t=strtolower(strstr($file,'.')))=='.html' || $t=='.htm')){
                    $views[] = $file;
                }
            }
            closedir($handle);
            return $views;
        }else{
            return false;
        }
    }//End Function
        
    /*old functions*/

    public function get_border_from_themes($theme){
        $wights_border=Array();
        $path = THEME_DIR.'/'.$theme;
        if(!is_dir($path))  return array();
        $workdir = getcwd();
        chdir($path);
        $xml = kernel::single('site_utility_xml');
        $content=file_get_contents('info.xml');
        $config = $xml->xml2arrayValues($content);
        foreach($config['theme']['borders']['set'] as $k=>$v){
            $wights_border[$v['attr']['tpl']]=file_get_contents($v['attr']['tpl']);
        }
        chdir($workdir);
        return $wights_border;
    }

    public function get_theme_styles($theme) 
    {
        $aConfig = app::get('site')->model('themes')->select()->columns(array('config'))->where('theme = ?', $theme)->instance()->fetch_one();
        if(is_array($aConfig['config'])){
            foreach($aConfig['config'] AS $key=>$value){
                //if($value['type'] != 'fullstyle')   continue;
                $styles[] = $value;
            }
        }
        return $styles;
    }//End Function

    public function get_theme_borders($theme){
        $aConfig = app::get('site')->model('themes')->select()->columns(array('config'))->where('theme = ?', $theme)->instance()->fetch_one();
        for($i=0;$i<count($aConfig['borders']);$i++){
            $aData[$aConfig['borders'][$i]['tpl']]=$aConfig['borders'][$i]['key'];
        }
        return $aData;
    }

    public function get_theme_info($theme) 
    {
        return app::get('site')->model('themes')->select()->where('theme = ?', $theme)->instance()->fetch_row();
    }//End Function
}//End Class
