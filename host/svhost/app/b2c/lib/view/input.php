<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_view_input{

    function input_category($params){
        $render = new base_render(app::get('b2c'));
        $mdl = app::get('b2c')->model('goods_cat');
        $render->pagedata['category'] = array();
        $render->pagedata['params'] = $params;
        if($params['value']){
            $row = $mdl->getList('*',array('cat_id'=>$params['value']));
            $render->pagedata['category'] = $row[0];
        }
        return $render->fetch('admin/goods/category/input_category.html');
    }
    
    
    function input_goodsfilter($params){

        $render = new base_render(app::get('b2c'));

        $obj_type = app::get('b2c')->model('goods_type');
        parse_str($params['value']['items'],$value);
        $params =array(
                'gtype'=>$obj_type->getList('*',null,0,-1),
                'view' => 'admin/goods/finder_filter.html',
                'params' => $params['params'],
                'json' => json_encode($data),
                'data' => $value,
                'from'=>$params['value']['items'],
                'domid' => substr(md5(rand(0,time())),0,6)
        );
        $type_id = 1;
        $params['value']['items'] = $value;
        if($params['value']['items']['type_id']) $type_id = $params['value']['items']['type_id'];
        $render->pagedata['params'] = $params;

        $goods_filter = kernel::single('b2c_goods_goodsfilter');
        $return = $goods_filter->goods_goodsfilter($type_id,app::get('b2c'));
        $render->pagedata['filter'] = $return;
        $render->pagedata['type_id'] = $type_id;
        return $render->fetch('admin/goods/goods_filter.html');
    }
}
