<?php
function widget_rankinglist(&$setting,&$system){
    $limit=intval($setting['limit'])?intval($setting['limit']):10;
    $maxlength=intval($setting['maxlength'])?intval($setting['maxlength']):55;
    //$setting['fontStyle']=type_check2($setting['fontStyle']);
    //$setting['fontStyle2']=type_check2($setting['fontStyle2']);
    $order=  array($setting['ranking'],'DESC');
    $viewer = $system->getConf('gallery.default_view');
    $o = &$system->loadModel('goods/products');
    $oSearch = $system->loadModel('goods/search');
    $rk['view_w_count']=4;
    $rk['view_count']=5;
    $rk['buy_count']=6;
    $rk['buy_w_count']=7;
    $rk['comments_count']=8;
    parse_str($setting['filter'],$filter);
    $filter['marketable']='true';
    $filter['disabled']='false';
    $setting['link']=$system->mkUrl('gallery',$viewer,array($filter['cat_id']?$filter['cat_id']:0,$oSearch->encode($filter),$rk[$setting['ranking']]));

    //$result=$o->getList("*",$filter,0,$limit,$c,$order);
    $result=$o->getList("*",$filter,0,$limit,$c,$order);
    return $result;
}
?>