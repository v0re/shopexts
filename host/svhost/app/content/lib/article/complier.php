<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class content_article_complier 
{
    
    function compile_widgets($tag_args, &$smarty){

        $args = $tag_args;
 
        return '$s=$this->_files[0];$i+=0;
        $i = intval($this->_wgbar[$s]++);
        echo \'<div class="shopWidgets_panel" base_file="\'.$s.\'" base_slot="\'.$i.\'" base_id="'.substr($args['id'],1,-1).'"  >\';
        kernel::single(\'site_theme_widget\')->admin_load($s,$i'.$id.');echo \'</div>\';';
    }
}//End Class
