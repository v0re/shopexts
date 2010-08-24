<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
function widget_goods_show(&$setting,&$render)
{
    $o = &app::get('b2c')->model('goods');
    $limit = (intval($setting['limit'])>0)?intval($setting['limit']):6;
    $orderby=$setting['goods_orderby']?$o->orderBy($setting['goods_orderby']):null;
    $filter = gs_getFilter($setting['g_filter']);

        if(!is_array($filter['cat_id'])&&$filter['cat_id']){
            $filter['cat_id']=array($filter['cat_id']);
        }
        if(!$filter['cat_id']){
            unset($filter['cat_id']);
        }
        if($filter['type_id'] && !is_array($filter['type_id'])){
            $filter['type_id']=array($filter['type_id']);
        }
        if($filter['pricefrom']){
                $filter['price'][0]=$filter['pricefrom'];
        }
        if($filter['priceto']){
                if(!$filter['price'][0]){
                    $filter['price'][0]=0;
                }
                $filter['price'][1]=$filter['priceto'];
        }
        
        $result = $o->getList('*',$filter,0,$limit,$orderby['sql']);
        if('on' == $setting['showMore']){
            $oSearch = &app::get('b2c')->model('search');
            $act = &app::get('b2c')->getConf('gallery.default_view');
            $result['link']= &kernel::router()->gen_url(
                array(
                'app'=>'b2c',
                'ctl'=>'gallery',
                'act'=>$act,
                'args'=>array(implode(",",$filter['cat_id']),$oSearch->encode($filter),($setting['goods_orderby']?$setting['goods_orderby']:0))
                )
            );
        }
        return $result;

}

function gs_getFilter($filter){
    $filter = (array)$filter;
    $filter = array_merge(array('marketable'=>"true",'disabled'=>"false",'goods_type'=>"normal"),$filter);
    if($GLOBALS['runtime']['member_lv']){
        $filter['mlevel'] = $GLOBALS['runtime']['member_lv'];
    }
    if($filter['props']){
        foreach($filter['props'] as $k=>$v){
            $filter['p_'.$k]=$v[0];
        }
    }
    $a = $filter;
    foreach( $a as $k => $v ){
        if( !$v && $v !== 0 )
            unset($filter[$k]);
    }
    return $filter;
}
?>
