<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_view_compiler{
    
    function compile_modifier_gimage($ident){
        list($ident) = explode(',',$ident);
        return "substr($ident,0,strpos($ident,'|'))";
    }

    function compile_modifier_cur($attrs) {
        //todo 需要将货币汇率也缓存
        if(!strpos($attrs,',') || false!==strpos($attrs,',')){
            return $attrs = 'app::get(\'ectools\')->model(\'currency\')->changer('.$attrs.')';
        }
    }
    
    function compile_modifier_cur_odr($attrs) {
        //todo 需要将货币汇率也缓存
        if(!strpos($attrs,',') || false!==strpos($attrs,',')){
            $arr_attributes = explode(',', $attrs);
            if (count($arr_attributes) <= 2)
                $attrs .= ',false,false,app::get(\'b2c\')->getConf(\'system.money.decimals\'),app::get(\'b2c\')->getConf(\'system.money.operation.carryset\')';
            elseif (count($arr_attributes) < 4)
            {
                $attrs .= ',false,app::get(\'b2c\')->getConf(\'system.money.decimals\'),app::get(\'b2c\')->getConf(\'system.money.operation.carryset\')';
            }
            else
            {
                $attrs .= ',app::get(\'b2c\')->getConf(\'system.money.decimals\'),app::get(\'b2c\')->getConf(\'system.money.operation.carryset\')';
            }
            return $attrs = 'app::get(\'ectools\')->model(\'currency\')->changer_odr('.$attrs.')';
        }
    }

    function compile_modifier_cur_name($attrs) {
        //todo 得到货币的cur_name
         if(!strpos($attrs,',') || false!==strpos($attrs,',')){
            return $attrs = 'app::get(\'ectools\')->model(\'currency\')->get_cur_name('.$attrs.')';
        }
    }
}
