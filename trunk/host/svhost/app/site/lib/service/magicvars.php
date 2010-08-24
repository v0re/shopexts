<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class site_service_magicvars implements site_interface_controller_content{
    public function modify(&$html){
        $mdl = app::get('desktop')->model('magicvars');
        $list = $mdl->getList('*',null,0,-1);
        foreach($list as $k=>$row){
            $find = $row['var_name'];
            $replace = $row['var_value'];
        }
        $html = str_replace($find,$replace,$html);
    }
}
