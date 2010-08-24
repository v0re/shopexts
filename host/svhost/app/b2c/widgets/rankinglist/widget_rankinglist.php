<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
function widget_rankinglist(&$setting,&$smarty){

    $limit=intval($setting['limit'])?intval($setting['limit']):10;
    $maxlength=intval($setting['maxlength'])?intval($setting['maxlength']):55;
    //$setting['fontStyle']=type_check2($setting['fontStyle']);
    //$setting['fontStyle2']=type_check2($setting['fontStyle2']);
    $order=  array($setting['ranking'],'DESC');
    $viewer = app::get('b2c')->getConf('gallery.default_view');
    $o = app::get('b2c')->model('goods');
    $oSearch = app::get('b2c')->model('search');
    $rk['view_w_count']=4;
    $rk['view_count']=5;
    $rk['buy_count']=6;
    $rk['buy_w_count']=7;
    $rk['comments_count']=8;
    parse_str($setting['filter'],$filter);
    $filter['marketable']='true';
    $filter['disabled']='false';

    $setting['link']=kernel::router()->gen_url(array('app'=>'b2c', 'ctl'=>'site_gallery', 'act'=>'index','full'=>1,array($filter['cat_id'],$oSearch->encode($filter),$rk[$setting['ranking']]?$rk[$setting['ranking']]:0)));

    //$result=$o->getList("*",$filter,0,$limit,$c,$order);
    if(!empty($setting['ranking']))
        $result=$o->getList("*",$filter,0,$limit,$order);
    else
        $result=$o->getList("*",$filter,0,$limit);
    return $result;
}
?>
