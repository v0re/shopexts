<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

function widget_menutree($setting, &$smarty){
   
    //$sitemap = &$app->model('content/sitemap');
    //$map = $sitemap->getMap($setting['depth']);
    //$html = _menutree_make_html($map,0,$app->navPath);
    
    //Ecos中这里没有层级了

    return '';

    $path = array();
    $html = _menutree_make_html($map, 0, $path);

    return $html;
}

function _menutree_make_html($map, $level, &$path){
    foreach($map as $item){
        $html.='<div style="padding-left:'.($level*20).'px"'.($path[$item['link']]?' class="current"':'').'><a href="'.$item['link'].'">'.$item['title'].'</a></div>';
        if(is_array($item['items']) && count($item['items'])>0){
            $html.='<div class="'.($path[$item['link']]?'open':'close').'">'._menutree_make_html($item['items'],$level+1,$path).'</div>';
        }
    }
    return $html;
}

?>
