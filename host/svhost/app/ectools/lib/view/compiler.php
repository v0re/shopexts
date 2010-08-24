<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class ectools_view_compiler{

    function compile_modifier_cur($attrs,&$compile) {
        //todo ҪһҲ
        if(!strpos($attrs,',') || false!==strpos($attrs,',')){
            return $attrs = 'app::get(\'ectools\')->model(\'currency\')->changer('.$attrs.')';
        }
    }
	
	public function compile_modifier_cur_name($attrs,&$compile) {
		//todo õҵcur_name
		 if(!strpos($attrs,',') || false!==strpos($attrs,',')){
            return $attrs = 'app::get(\'ectools\')->model(\'currency\')->get_cur_name('.$attrs.')';
        }
	}
}
