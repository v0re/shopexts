<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
function widget_goods(&$setting,&$render){ 
    $limit = (intval($setting['limit'])>0)?intval($setting['limit']):6;
    $goods=&app::get('b2c')->model('goods');
    $goods->defaultCols='bn,name,cat_id,price,store,marketable,brand_id,weight,d_order,uptime,type_id';
    $goods->appendCols ='goods_id,thumbnail_pic,brief,mktprice,image_default_id';
    $config=app::get('b2c')->getConf('site.save_price');
    $data['onSelect']=$setting['onSelect']?$setting['onSelect']:0;
    $setting['max_length']=$setting['max_length']?$setting['max_length']:35;
    $setting['view'] = app::get('b2c')->getConf('gallery.default_view');
    $imageDefault = app::get('image')->getConf('image.set');
    $search = &app::get('b2c')->model('search');
    $setting['str'] = $search->encode($filter);
    $setting['restrict']=$setting['restrict']?$setting['restrict']:'on';
    $order=$setting['goods_orderby']?orderBy($setting['goods_orderby']):null;


    if($setting['columNum']>1){
        for($i=0;$i<$setting['columNum'];$i++){
            parse_str($setting['adjunct']['items'][$i],$filter[$i]);
            $filter[$i] = getFilter($filter[$i]);
            
            $result['link'][($i-1)]= &kernel::router()->gen_url(
                array(
                    'app'=>'b2c',
                    'ctl'=>'gallery',
                    'act'=>$setting['view'],
                    'args'=>array($str_cat_id,$search->encode($filter[$i]),($setting['goods_orderby']?$setting['goods_orderby']:0))
                )
            );
            $result['goods'][]=$goods->getList(null,$filter[$i],0,$limit,$order['sql']);

            $result['defaultImage'] = $imageDefault['S']['default_image'];
            unset($filter[$i]);
        }

        return $result;

    }else{
        parse_str($setting['adjunct']['items'][0],$filter);
        $filter = getFilter($filter);

        $result['link']= &kernel::router()->gen_url(
            array(
                'app' => 'b2c',
                'ctl'=>'site_gallery',
                'act'=>$setting['view'],
                'args' => array(implode(",",(array)$filter['cat_id']),$search->encode($filter),($setting['goods_orderby']?$setting['goods_orderby']:0))
            )
        );

        $result['goods']=$goods->getList('*',$filter,0,$limit,$order['sql']);
        $result['defaultImage'] = $imageDefault['S']['default_image'];
        return $result;
    }
}

function getFilter($filter){
    $filter = array_merge(array('marketable'=>"true",'disabled'=>"false",'goods_type'=>"normal"),$filter);
    if($GLOBALS['runtime']['member_lv']){
        $filter['mlevel'] = $GLOBALS['runtime']['member_lv'];
    }
    if($filter['props']){
        foreach($filter['props'] as $k=>$v){
            $filter['p_'.$k]=$v[0];
        }
    }
    return $filter;
}
function orderBy($id=null){
    $order=array(
//        array('label'=>__('默认'),'sql'=>implode($this->defaultOrder,'')),
        array('label'=>__('按发布时间 新->旧'),'sql'=>'last_modify desc'),
        array('label'=>__('按发布时间 旧->新'),'sql'=>'last_modify'),
        array('label'=>__('按价格 从高到低'),'sql'=>'price desc'),
        array('label'=>__('按价格 从低到高'),'sql'=>'price'),
        array('label'=>__('访问周次数'),'sql'=>'view_w_count desc'),
        array('label'=>__('总访问次数'),'sql'=>'view_count desc'),
        array('label'=>__('周购买次数'),'sql'=>'buy_count desc'),
        array('label'=>__('总购买次数'),'sql'=>'buy_w_count desc'),
        array('label'=>__('评论次数'),'sql'=>'comments_count desc'),
    );
    if($id){
        return $order[$id];
    }else{
        return $order;
    }

}
?>
