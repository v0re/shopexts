<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_goods_filter extends dbeav_filter{
    var $name = 'B2C商品筛选器';
    function goods_filter(&$filter, &$object){
        $filter = utils::addslashes_array($filter);

        $where = array();
        if( isset( $filter['marketable'] ) ){
            if( $filter['marketable'] === 'true' )
                $filter['marketable'] = 'true';
            if( $filter['marketable'] === 'false' )
                $filter['marketable'] = 'false';
        }
        if($filter['type_id'] == '_ANY_')
            unset($filter['type_id']);


        if($filter['cat_id'] || $filter['cat_id'] === 0){
            if(!is_array($filter['cat_id'])){
                $filter['cat_id']=array($filter['cat_id']);
            }
            foreach($filter['cat_id'] as $vCat_id){
                if($vCat_id !== '_ANY_' && $vCat_id !== ''){
                    $aCat_id[] = intval($vCat_id);
                }
            }

            $filter['cat_id']=$aCat_id;

             if(!isset($object->__show_goods)){
                $object->__show_goods = $object->app->getConf('system.category.showgoods');
            }
            if($object->__show_goods){
                if(count($filter['cat_id'])>0)
                    $where[] = 'cat_id in ('.implode($filter['cat_id'],' , ').')';
            }else{
                if($filter['cat_id']){
                    $oCat = $object->app->model('goods_cat');
                    $fcat_id=$filter['cat_id'][0];
                    $aCat = $oCat->dump($fcat_id,'cat_path,cat_id');
                }
                $pathplus='';
                if(count($aCat)){
                    foreach($aCat as $v){
                        $pathplus.=' cat_path LIKE \''
                                .($aCat['cat_path']).$aCat['cat_id'].',%\' OR';
                    }
                }
                if($aCat){
                    foreach($object->db->select('SELECT cat_id FROM sdb_b2c_goods_cat WHERE '.$pathplus.' cat_id in ('.implode($filter['cat_id'],' , ').')') as $rows){
                        $aCatid[] = $rows['cat_id'];
                    }
                }else{
                    unset($aCatid);
                }
/*                if(in_array('0', $filter['cat_id'])){
                    $aCatid[] = 0;
                }*/
                if(!is_null($aCatid)){
                    $where[] = 'cat_id IN ('.implode(',', $aCatid).')';
                }else if($filter['cat_id'] && $filter['cat_id'][0]){

                    $where[] = 'cat_id IN ('.implode(',', $filter['cat_id']).')';
                }
            }
            $filter['cat_id'] = null;
        }

        if(isset($filter['area']) && $filter['area']){
            $where[] = 'goods_id < '.$filter['area'][0]. ' and goods_id >'.$filter['area'][1];
            //$where[] = 'and goods_id < 1000';
            unset($filter['area']);
        }
        if($filter['type_id']=="_ANY_" || empty($filter['type_id'][0])){
            unset($filter['type_id']);
        }
        if(is_array($filter['tag'])){
            foreach($filter['tag'] as $tk=>$tv){
                if($tv == '_ANY_')
                    unset($filter['tag'][$tk]);
            }
        }

        if(isset($filter['brand_id']) && $filter['brand_id']){
            if(is_array($filter['brand_id'])){
                foreach($filter['brand_id'] as $brand_id){
                    if($brand_id!='_ANY_'){
                        $aBrand[] = intval($brand_id);
                    }
                }
                if(count($aBrand)>0){
                    $where[] = 'brand_id IN('.implode(',', $aBrand).')';
                }
            }elseif($filter['brand_id'] > 0){
                $where[] = 'brand_id = '.$filter['brand_id'];
            }
            unset($filter['brand_id']);
        }
        if(isset($filter['goods_id']) && $filter['goods_id']){
            if(is_array($filter['goods_id'])){
                if( $filter['goods_id'][0] != '_ALL_' ){
                    foreach($filter['goods_id'] as $goods_id){
                        if($goods_id!='_ANY_'){
                            $goods[] = intval($goods_id);
                        }
                    }
                }
            }else{
                $goods[] = intval($filter['goods_id']);
            }
        }
        unset($filter['goods_id']);

        if(isset($filter['keyword']) && $filter['keyword']) {
            $filter['keywords'] = array($filter['keyword']);
        }
        unset($filter['keyword']);

        if(isset($filter['keywords']) && $filter['keywords'] && !in_array('_ANY_',$filter['keywords'])) {
            $keywordsList = $object->getGoodsIdByKeyword($filter['keywords'],$filter['_keyword_search']);
            $keywordsGoods = array();
            foreach($keywordsList as $keyword)
                $keywordsGoods[] = intval($keyword['goods_id']);
            if(!empty($keywordsGoods) && !empty($goods)){
                $keywordsGoods = array_intersect($keywordsGoods, $goods);
                if(empty($keywordsGoods))
                    $goods = array('-1');
                else
                    $goods = $keywordsGoods;
            }else{
                if(!empty($keywordsGoods)){
                    $goods = $keywordsGoods;
                }else{
                    $goods = array('-1');
                }
            }
        }
        unset($filter['keywords']);

        if(isset($filter['bn']) && $filter['bn']){
            $sBn = '';
            if(is_array($filter['bn'])){
                $sBn = trim($filter['bn'][0]);
            }else{
                $sBn = trim($filter['bn']);
            }
            $bnGoodsId = $object->getGoodsIdByBn($sBn,$filter['_bn_search']);

            if(!empty($bnGoodsId) && !empty($goods)){
                $bnGoodsId = array_intersect($bnGoodsId, $goods);
                if(empty($bnGoodsId))
                    $goods = array('-1');
                else
                    $goods = $bnGoodsId;
            }else{
                if(!empty($bnGoodsId)){
                    $goods = $bnGoodsId;
                }else{
                    $goods = array('-1');
                }
            }
            unset( $filter['bn'] );
        }
        $filter = (array)$filter;
        foreach($filter as $k=>$v){

            if(substr($k,0,2)=='p_'){
                $ac = array();
                if(is_array($v)){
                    foreach($v as $m){
                        if($m!=='_ANY_' && $m!==''){
                            $ac[] = $tPre.$k.'=\''.$m.'\'';
                        }
                    }
                    if(count($ac)>0){
                        $where[] = '('.implode($ac,' or ').')';
                    }
                }elseif(isset($v) && $v!='' && $v!='_ANY_'){
                    $where[] = $tPre.$k.'=\''.$v.'\'';
                }
                unset($filter[$k]);
            }

            else if( substr($k,0,2) == 's_' ){
                $sSpecId = array();
                if( is_array( $v ) ){
                    foreach( $v as $n ){
                        if( $n !== '_ANY_' && $n != false ){
                            $sSpecId[] = $n;
                        }
                    }
                    unset($filter[$k]);
                }

                if( count( $sSpecId )>0 ){
                    $sGoodsId = $object->db->select( 'SELECT goods_id FROM sdb_b2c_goods_spec_index WHERE spec_value_id IN ( '.implode( ',',$sSpecId ).' )' );
                    $sgid = array();
                    foreach( $sGoodsId as $si )
                        $sgid[] = $si['goods_id'];
                    if(!empty($goods))
                        $sgid = array_intersect( $sgid , $goods);
                    if(!empty($sgid)){
                        $goods = $sgid;
                    }else{
                        $goods = array(-1);
                    }
                }

            }

        }

      foreach($filter as $k=>$v){
        if(substr($k,0,2)=='p_'){
            $ac = array();
            if(is_array($v)){
                foreach($v as $m){
                    if($m!=='_ANY_' && $m!==''){

                        $ac[] = $tPre.$k.'=\''.$m.'\'';
                    }
                }
                if(count($ac)>0){
                    $where[] = '('.implode($ac,' or ').')';
                }
            }elseif(isset($v) && $v!='' && $v!='_ANY_'){
                $where[] = $tPre.$k.'=\''.$v.'\'';
            }
            unset($filter[$k]);
        }

        else if( substr($k,0,2) == 's_' ){
            $sSpecId = array();
            if( is_array( $v ) ){
                foreach( $v as $n ){
                    if( $n !== '_ANY_' && $n != false ){
                        $sSpecId[] = $n;
                    }
                }
                unset($filter[$k]);
            }

            if( count( $sSpecId )>0 ){
                $sGoodsId = $object->db->select( 'SELECT goods_id FROM sdb_goods_spec_index WHERE spec_value_id IN ( '.implode( ',',$sSpecId ).' )' );
                $sgid = array();
                foreach( $sGoodsId as $si )
                    $sgid[] = $si['goods_id'];
                if(!empty($goods))
                    $sgid = array_intersect( $sgid , $goods);
                if(!empty($sgid)){
                    $goods = $sgid;
                }else{
                    $goods = array(-1);
                }
            }

        }

    }

        if(isset($goods) && count($goods)>0){
            $where[] = 'goods_id IN ('.implode(',', $goods).')';
        }

        if(isset($filter['price']) && is_array($filter['price'])){
            if($filter['price'][0]==0 || $filter['price'][0]){
                $where[] = 'price >= '.intval($filter['price'][0]);
            }

            if($filter['price'][1]=='0' || $filter['price'][1]){
                $where[] = 'price <= '.intval($filter['price'][1]);
            }
            if(!is_numeric($filter['price'][0])||!is_numeric($filter['price'][1])){
                unset($filter['price']);
            }
            /*
            if($filter['price'][0] && $filter['price'][1]){
                $where[] = 'price >= '.min($filter['price']).' AND price <= '.max($filter['price']);
            }*/
            unset($filter['price']);
        }else if(($filter['pricefrom']==0 || $filter['pricefrom']) && ($filter['priceto'] || $filter['priceto'])){
            $where[] = 'price >= '.$filter['pricefrom'].' AND price <= '.$filter['priceto'];
            unset($filter['pricefrom']);
            unset($filter['priceto']);
        }else{
            unset($filter['pricefrom']);
            unset($filter['priceto']);
        }
        if(isset($filter['cost'])){
            if(!is_numeric($filter['cost'])){
                unset($filter['cost']);
            }
        }
        if(isset($filter['mktprice'])){
            if(!is_numeric($filter['mktprice'])){
                unset($filter['mktprice']);
            }
        }
            if(isset($filter['store'])){
            if(!is_numeric($filter['store'])){
                unset($filter['store']);
            }
        }
        if(isset($filter['gkey']) && trim($filter['gkey'])){
            $filter['name'] = trim($filter['gkey']);
        }
        if($filter['searchname']){
           $filter['name'][]=$filter['searchname'];
        }
        if(isset($filter['name']) && $filter['name']){
            if(is_array($filter['name'])){
                $filter['name']=implode('+',$filter['name']);
                if($filter['name']){
                    $filter['name']=str_replace('%xia%','_',$filter['name']);
                    $filter['name']=preg_replace('/[\'|\"]/','+',$filter['name']);
                    $GLOBALS['search']=$filter['name'];
                    $where[]=$object->wFilter($filter['name']);
                }
            }else{ //后台搜索
                $GLOBALS['search']=$filter['name'];
                $where[] = 'name LIKE \'%'.trim($filter['name']).'%\'';
            }
            $filter['name'] = null;
        }

        if( isset($filter['spec_desc']) ){
            if( $filter['spec_desc'] === 'true' ){
                $where[] = '(spec_desc IS NOT NULL && spec_desc != \'\' && spec_desc != \'a:0:{}\')';
            }
            if( $filter['spec_desc'] === 'false' ){
                $where[] = '(spec_desc IS NULL || spec_desc = \'\' || spec_desc = \'a:0:{}\')';
            }
            unset($filter['spec_desc']);
        }
        if(!$filter['goods_type'])
        $filter['goods_type'] = 'normal';
//print_R($where);exit;
        return parent::dbeav_filter_parser($filter,null,$where,$object);
        }
}
