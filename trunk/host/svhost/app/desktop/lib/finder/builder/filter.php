<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_finder_builder_filter extends desktop_finder_builder_prototype{

    function main(){
    
                $o = new desktop_finder_builder_filter_render();
                return $o->main($this->object->table_name(),$this->app,$filter,$this->controller);
 
    }

}
