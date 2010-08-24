<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_mdl_goods extends dbeav_model{
    var $has_tag = true;
    var $defaultOrder = array('d_order',' DESC',',p_order',' DESC',',goods_id',' DESC');
    var $has_many = array(
        'product' => 'products:contrast',
        'rate' => 'goods_rate:replace:goods_id^goods_1',
        'keywords'=>'goods_keywords:replace',
        'images' => 'image_attach@image:contrast:goods_id^target_id',
//        'tag'=>'tag_rel@desktop:replace:goods_id^rel_id',
    );
    var $has_one = array(

    );
    var $subSdf = array(
            'default' => array(

                'keywords'=>array('*'),
                'product'=>array(
                    '*',array(
                        'price/member_lv_price'=>array('*')
                    )
                ),
                ':goods_type'=>array(
                    '*'
                ),
                ':goods_cat'=>array(
                    '*'
                ),
/*                'tag'=>array(
                    '*',array(
                        ':tag'=>array('*')
                    )
                ),*/
                'images'=>array(
                    '*',array(
                        ':image'=>array('*')
                    )
                )
            ),
            'delete' => array(

                'keywords'=>array('*'),
                'product'=>array(
                    '*',array(
                        'price/member_lv_price'=>array('*')
                    )
                ),
                'images'=>array(
                    '*'
                )
            )
        );

    function __construct($app){
        parent::__construct($app);
        //使用meta系统进行存储
        $this->use_meta();
    }
    var $ioSchema = array(
        'csv' => array(
            'bn:商品货号' => 'bn',
            'ibn:规格货号' => array('bn','product'),
            'col:分类' => 'category/cat_id',
            'col:品牌' => 'brand/brand_id',
            'col:市场价' => array('price/mktprice/price','product'),
            'col:成本价' => array('price/cost/price','product'),
            'col:销售价' => array('price/price/price','product'),
            'col:缩略图' => 'image_default_id',
            'col:图片文件' => '',
            'col:商品名称' => 'name',
            'col:上架' => 'status',
            'col:规格' => 'spec',
            'col:商品简介' => 'brief',
            'col:详细介绍' => 'description',
            'col:重量' => 'weight',
            'col:单位' => 'unit',
            'col:库存' => 'store'
        )
    );

    function io_title( $filter,$ioType='csv' ){
//        if( $this->ioTitle['csv'][$filter['type_id']] )
//            return $this->ioTitle['csv'][$filter['type_id']];
        $title = array();
        switch( $ioType ){
            case 'csv':
            default:
                $oGtype = $this->app->model('goods_type');
                if( $this->csvExportGtype[$filter['type_id']] )
                    $gType = $this->csvExportGtype[$filter['type_id']];
                else
                    $gType = $oGtype->dump($filter['type_id'],'*');
                $this->oSchema['csv'][$filter['type_id']] = array(
                    '*:'.$gType['name']=>'type/name',
                    'bn:商品货号' => 'bn',
                    'ibn:规格货号' => array('bn','product'),
                    'col:分类' => 'category/cat_name',
                    'col:品牌' => 'brand/brand_name',
                    'col:市场价' => array('price/mktprice/price','product'),
                    'col:成本价' => array('price/cost/price','product'),
                    'col:销售价' => array('price/price/price','product'),
                    'col:缩略图' => 'image_default_id',
                    'col:图片文件' => '',
                    'col:商品名称' => 'name',
                    'col:上架' => 'status',
                    'col:规格' => 'spec',
                    'col:库存' => 'store'
                );
                $oMlv = $this->app->model('member_lv');
                foreach( $oMlv->getList() as $mlv ){
                    $this->oSchema['csv'][$filter['type_id']]['price:'.$mlv['name']] = 'price/member_lv_price/'.$mlv['member_lv_id'].'/price';
                }
                $this->oSchema['csv'][$filter['type_id']] = array_merge(
                    $this->oSchema['csv'][$filter['type_id']],
                    array(
                        'col:商品简介' => 'brief',
                        'col:详细介绍' => 'description',
                        'col:重量' => 'weight',
                        'col:单位' => 'unit',
                    )
                );
                foreach( (array)$gType['props'] as $propsK => $props ){
                    $this->oSchema['csv'][$filter['type_id']]['props:'.$props['name']] = 'props/p_'.$propsK;
                }
                break;
        }
        $this->ioTitle['csv'][$filter['type_id']] = array_keys($this->oSchema['csv'][$filter['type_id']]);
        return $this->ioTitle['csv'][$filter['type_id']];
    }

    function dump($filter,$field = '*',$subSdf = null){
        $dumpData = &parent::dump($filter,$field,$subSdf);

        $oSpec = &$this->app->model('specification');
        if( $dumpData['spec_desc'] && is_array( $dumpData['spec_desc'] ) ){
            foreach( $dumpData['spec_desc'] as $specId => $spec ){
                $dumpData['spec'][$specId] = $oSpec->dump($specId,'*');
                foreach( $spec as $pSpecId => $specValue ){
                    $dumpData['spec'][$specId]['option'][$pSpecId] = array_merge( array('private_spec_value_id'=>$pSpecId), $specValue );
                }
            }
        }
        unset($dumpData['spec_desc']);
        if( $dumpData['product'] ){
            $aProduct = current( $dumpData['product']);
            $dumpData['current_price'] = $aProduct['price']['price']['current_price'];
        }else{
            $dumpData['current_price'] = $dumpData['price'];
        }
        return $dumpData;
    }

/*    function getList($cols='*',$filter=array(),$start=0,$limit=-1,$orderType=null){
        foreach(kernel::servicelist('b2c_goods_list_apps') as $object){
            return $object->goods_list($cols,$filter,$start,$limit,$orderType, $this);
        }
    }*/

    function _filter($filter,$tbase=''){
        return  kernel::single('b2c_goods_filter')->goods_filter($filter, $this);
    }

    function wFilter($words){
        $replace = array(",","+");
        $enStr=preg_replace("/[^chr(128)-chr(256)]+/is"," ",$words);
        $otherStr=preg_replace("/[chr(128)-chr(256)]+/is"," ",$words);
        $words=$enStr.' '.$otherStr;
        $return=str_replace($replace,' ',$words);
        $word=preg_split('/\s+/s',trim($return));
        $GLOBALS['search_array']=$word;
        foreach($word as $k=>$v){
            if($v){
                $goodsId = array();
                foreach($this->getGoodsIdByKeyword(array($v)) as $idv)
                    $goodsId[] = $idv['goods_id'];
                foreach( $this->db->select('SELECT goods_id FROM sdb_b2c_products WHERE bn = \''.trim($v).'\' ') as $pidv)
                    $goodsId[] = $pidv['goods_id'];
                $sql[]='(name LIKE \'%'.$word[$k].'%\' or bn like \''.$word[$k].'%\' '.( $goodsId?' or goods_id IN ('.implode(',',$goodsId).') ':'' ).')';
            }
        }
        return implode('and',$sql);
    }
    function getGoodsIdByKeyword($keywords , $searchType = 'tequal'){
        $where = '';
        switch( $searchType ){
            case 'has':
                $where = ' keyword LIKE "%'.implode( '%" AND keyword LIKE "%' ,$keywords ).'%" ';
                //like
                break;
            case 'nohas':
                $where = ' keyword NOT LIKE "%'.implode( '%" AND keyword NOT LIKE "%' ,$keywords ).'%" ';
                // not like
                break;
            case 'tequal':
            default:
                $where = ' keyword in ( "'.implode('","',$keywords).'" ) ';
                break;
        }
        return $this->db->select('SELECT goods_id FROM sdb_b2c_goods_keywords WHERE '.$where);
    }
    function save(&$goods,$mustUpdate = null){
        if( !$goods['bn'] ) $goods['bn'] = strtoupper(uniqid('g'));
        if( array_key_exists( 'spec',$goods ) ){
            if( $goods['spec'] )
                foreach( $goods['spec'] as $gSpecId => $gSpecOption ){
                    $goods['spec_desc'][$gSpecId] = $gSpecOption['option'];
                }
            else
                $goods['spec_desc'] = null;
        }
        $goodsStatus = false;
        $store = 0;
        is_array($goods['product']) or $goods['product'] = array();
        $bnList = array();
        foreach( $goods['product'] as $pk => $pv ){
            if( !$pv['bn'] ) $goods['product'][$pk]['bn'] = strtoupper(uniqid('p'));
            if( array_key_exists( $goods['product'][$pk]['bn'],$bnList ) ){
                return null;
            }
            $bnList[$goods['product'][$pk]['bn']] = 1;
            $goods['product'][$pk]['name'] = $goods['name'];
            if( $pv['status'] != 'false' ) $goodsStatus = true;
            if( $store !== null && ( $pv['store'] === null || $pv['store'] === '' ) ){
                $store = null;
            }else{
                $store += $pv['store'];
            }
        }
        $goods['store'] = $store;
        if( !$goodsStatus && !$goods['status'])
            $goods['status'] = 'false';
        unset($goods['spec']);
        $rs = parent::save($goods,$mustUpdate);
        $this->createSpecIndex($goods);
        return $rs;
    }

    function createSpecIndex($goods){
        $oSpecIndex = &$this->app->model('goods_spec_index');
        $oSpecIndex->delete( array('goods_id'=>$goods['goods_id']) );
        foreach( $goods['product'] as $pro ){
            if( $pro['spec_desc'] ){
                foreach( $pro['spec_desc']['spec_value_id'] as $specId => $specValueId ){
                    $data = array(
                        'type_id' => $goods['type']['type_id'],
                        'spec_id' => $specId,
                        'spec_value_id' => $specValueId,
                        'goods_id' => $goods['goods_id'],
                        'product_id' => $pro['product_id'],
                    );
                    $oSpecIndex->save($data);
                }
            }
        }
    }

    function delete($filter){
        $this->db->exec( 'DELETE FROM '.$this->table_name(1).' WHERE goods_id IN ('.$this->_filter($filter).') ' );
        return parent::delete($filter);;
    }

    /**
     * @params string goods_id
     * @params string product_id
     * @params string num
     */
    public function unfreez($goods_id, $product_id, $num){
        $oPro = &$this->app->model('products');
        $sdf_pdt = $oPro->dump($product_id, 'freez,store');
        $objMath = kernel::single('ectools_math');

        if(is_null($sdf_pdt['freez']) || $sdf_pdt['freez'] === ''){
            if (is_null($sdf_pdt['store']) || $sdf_pdt['store'] === '')
                return true;

            $sdf_pdt['freez'] = 0;
        }elseif($num < $sdf_pdt['freez']){
            $sdf_pdt['freez'] = $objMath->number_minus(array($sdf_pdt['freez'], $num));
            //$sdf_pdt['freez'] -= $num;
        }elseif($num >= $sdf_pdt['freez']){
            $sdf_pdt['freez'] = 0;
        }
        $sdf_pdt['product_id'] = $product_id;
        $sdf_pdt['last_modify'] = false;

        return $oPro->save($sdf_pdt);
    }


    function diff($gid){
        $oGtype = &$this->app->model('goods_type');
        if(!$gid) return array();
        foreach($gid as $t=>$v){
                $gid[$t]=intval($v);
        }

        $params = $this->getList('*',array('goods_id'=>$gid));
        foreach ($params as &$val) {
                $temp = $this->dump($val['goods_id'],'goods_id',array(
                    'product'=>array(
                        'product_id, spec_info, price, freez, store, goods_id',
                        array('price/member_lv_price'=>array('*'))
                    )
                )
                );
                $val['spec_desc_info'] = $temp['product'];
                if(is_array($temp['product']))
                    $tempPro = current( $temp['product'] );
                $val['current_price'] = $tempPro['price']['price']['current_price'];
        }

        $params2 = $this->db->select('select * from sdb_b2c_goods as A Left Join sdb_b2c_goods_type as B ON A.type_id = B.type_id where A.goods_id in ('.implode(',',$gid).')');
        foreach($params2 as $k =>$v){
            $row = $oGtype->dump($v['type_id'],'schema_id,setting,type_id');
            $params2[$k]['schema_id'] = $row['schema_id'];
            $params2[$k]['setting'] = $row['setting'];
            $params2[$k]['props'] = $row['props'];
        }

        foreach($params2 as $i=>$p){

            if( $params2[$i]['props']  ){
            foreach($params2[$i]['props'] as $group=>$items){
                foreach($items as $p_name=>$v){
                    $name = $items['name'];
                    if($p_name=="name" or $p_name=="options"){
                        $tResult=$params2[$i]['props'][$group][options][$params2[$i]["p_".$group]];
                            $gId = $p['goods_id'];
                            if($tResult){
                                $p_map[__('基本属性')][$name][$gId] = $tResult;

                            }else{
                                $p_map[__('基本属性')][$name][$gId] =$params2[$i]["p_".$group];
                            }
                        }
                    }
            }
            }
        }
        foreach($params as $i=>$p){
            $params[$i]['params']=unserialize($params[$i]['params']);
            $params2[$i]['params']=unserialize($params2[$i]['params']);
            $params[$i]['pdt_desc']=$params[$i]['pdt_desc'];
     /*       foreach($params[$i]['params'] as $group=>$items){
                    foreach($items as $p_name=>$v){
                        if(isset($params2[$i]['params'][$group][$p_name])){
                             $p_map[$group][$p_name][$p['goods_id']] = $v;
                        }
                    }

            }*/

        }//print_R($params);exit;
        return array('params'=>$p_map,'length'=>floor(80/count($gid)),'colp'=>count($gid)+1,'goods'=>$params,'cols'=>count($params)+1,'width'=>floor(100/(count($params)+1)).'%');
    }

    /**
     * 冻结产品的库存
     * @params string goods_id
     * @params string product_id
     * @params string num
     */
    public function freez($goods_id, $product_id, $num)
    {
        $oPro = &$this->app->model('products');
        $sdf_pdt = $oPro->dump($product_id, 'freez,store');
        $objMath = kernel::single('ectools_math');

        if(is_null($sdf_pdt['freez']) || $sdf_pdt['freez'] === ''){
            if (is_null($sdf_pdt['store']) || $sdf_pdt['store'] === '')
                return true;

            $sdf_pdt['freez'] = 0;
            $sdf_pdt['freez'] = $objMath->number_plus(array($sdf_pdt['freez'], $num));
            //$sdf_pdt['freez'] += $num;
        }elseif($objMath->number_plus(array($sdf_pdt['freez'], $num)) > $sdf_pdt['store'] ){
            $sdf_pdt['freez'] = $sdf_pdt['store'];
        }else{
            $sdf_pdt['freez'] = $objMath->number_plus(array($sdf_pdt['freez'], $num));
            //$sdf_pdt['freez'] += $num;
        }

        $sdf_pdt['product_id'] = $product_id;
        $sdf_pdt['last_modify'] = false;

        return $oPro->save($sdf_pdt);
    }

    function orderBy($id=null){
        $order=array(
            array('label'=>__('默认'),'sql'=>implode($this->defaultOrder,'')),
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

    function prepared_import_csv_row($row,$title,&$goodsTmpl,&$mark,&$newObjFlag,&$msg){
        if( substr($row[0],0,1) == '*' ){
            $mark = 'title';
            $newObjFlag = true;

            $oGType = &$this->app->model('goods_type');
            $goodsTmpl['gtype'] = $oGType->dump(array('name'=>ltrim($row[0],'*:')),'*','default');
            if( !$goodsTmpl['gtype'] ){
                $msg = array('error'=>'商品类型:'.ltrim( $row[0],'*:' ).' 不存在');
                return false;
            }
            if( $goodsTmpl['gtype']['props'] ){
                foreach( $goodsTmpl['gtype']['props'] as $propsk => $props ){
                    $this->ioSchema['csv']['props:'.$props['name']] = 'props/p_'.$propsk.'/value';
                    foreach( $props['options'] as $p => $v ){
                        $goodsTmpl['props_hash'][$props['name']][$v] = $p;
                    }
                }
            }

            $oMlv = &$this->app->model('member_lv');
            foreach( $oMlv->getList('member_lv_id,name','',0,-1) as $mlv ){
                $this->ioSchema['csv']['price:'.$mlv['name']] = array('price/member_lv_price/'.$mlv['member_lv_id'].'/price','product');
            }


            return array_flip($row);
        }else{
            $mark = 'contents';
            if( !$row[$title['ibn:规格货号']] || in_array($row[$title['col:规格']],array('','-')) ){
                $newObjFlag = true;
            }
            return $row;
        }
    }

    function ioSchema2sdf($data,$title,$csvSchema,$key = null){
        $rs = array();
        $subSdf = array();
        foreach( $csvSchema as $schema => $sdf ){
            $sdf = (array)$sdf;
            if( ( !$key && !$sdf[1] ) || ( $key && $sdf[1] == $key ) ){
                eval('$rs["'.implode('"]["',explode('/',$sdf[0])).'"] = $data[$title[$schema]];');
                unset($data[$title[$schema]]);
            /*}else if( ){
                eval('$rs["'.implode('"]["',explode('/',$sdf[0])).'"] = $data[$title[$schema]];');
                unset($data[$title[$schema]]);*/
            }else{
                $subSdf[$sdf[1]] = $sdf[1];
            }
        }
        if(!$key){
            foreach( $subSdf as $k ){
                foreach( $data[$k] as $v ){
                    $rs[$k][] = $this->ioSchema2sdf($v,$title,$csvSchema,$k);
                }
            }
        }
        foreach( $data as $orderk => $orderv ){
            if( substr($orderk,0,4 ) == 'col:' ){
                $rs[ltrim($orderk,'col:')] = $orderv;
            }
        }
        return $rs;

    }

    function checkProductBn($bn, $gid=0){
        if(empty($bn)){
            return false;
        }
        if($gid){
            $sql = 'SELECT count(*) AS num FROM sdb_b2c_products WHERE bn = \''.$bn.'\' AND goods_id != '.$gid;
            $Gsql = 'SELECT count(*) AS num FROM sdb_b2c_goods WHERE bn = \''.$bn.'\' AND goods_id != '.$gid;
        }else{
            $sql = 'SELECT count(*) AS num FROM sdb_b2c_products WHERE bn = \''.$bn.'\'';
            $Gsql = 'SELECT count(*) AS num FROM sdb_b2c_goods WHERE bn = \''.$bn.'\'';
        }
        $aTmp = $this->db->select($sql);
        $GaTmp = $this->db->select($Gsql);
        return $aTmp[0]['num']+$GaTmp[0]['num'];
    }

    function prepared_import_csv_obj($data,&$mark,$goodsTmpl,&$msg = ''){
        if( !$data['contents'] )return null;
        $mark = 'contents';
        $gData = &$data['contents'];
        $gTitle = $data['title'];
        $rs = array();
        //id
        if( $this->io->goodsBn && array_key_exists( $gData[0][$gTitle['bn:商品货号']] , $this->io->goodsBn ) ){
            $msg = array( 'error'=>'商品货号:'.$gData[0][$gTitle['bn:商品货号']].' 文件中有重复' );
            return false;
        }

        $goodsId = $this->dump(array('bn'=>$gData[0][$gTitle['bn:商品货号']]),'goods_id');
        if( $goodsId['goods_id'] )
            $gData[0]['col:goods_id'] = $goodsId['goods_id'];

        $gData[0][$gTitle['col:上架']] = (in_array( trim( $gData[0][$gTitle['col:上架']] ), array('Y','TRUE') )?'true':'false');

        foreach( $gTitle as $colk => $colv ){
            if( substr( $colk, 0,6 ) == 'props:' ){
                if( !$this->ioSchema['csv'][$colk] )
                    $msg['warning']['属性：'.ltrim($colk,'props:').'不存在'] = '';
                else{
                    if( $gData[0][$gTitle[$colk]] && !array_key_exists( $gData[0][$gTitle[$colk]], $goodsTmpl['props_hash'][ltrim($colk,'props:')] ) )
                        $msg['warning']['属性值：'.$gData[0][$gTitle[$colk]].'不存在'] = '';
                    $gData[0][$gTitle[$colk]] = $goodsTmpl['props_hash'][ltrim($colk,'props:')][$gData[0][$gTitle[$colk]]];
                }
            }
            if( (substr( $colk,0,6 ) == 'price:' || in_array( $colk , array('col:市场价','col:成本价','col:销售价') ) ) && $gData[0][$gTitle[$colk]] !== 0 && !$gData[0][$gTitle[$colk]] ){
                unset($gData[0][$gTitle[$colk]]);
            }
        }

        //分类
        $catPath = array();
        $oCat = &$this->app->model('goods_cat');
        $catId = 0;
        foreach( explode( '->',$gData[0][$gTitle['col:分类']] ) as $catName ){
            $aCatId = $oCat->dump(array('cat_name'=>$catName,'parent_id'=>$catId),'cat_id');
            if( $aCatId )
                $catId = $aCatId['cat_id'];
            else
                $catId = 0;
        }
        $catId = $oCat->dump($catId,'cat_id');
        if( $gData[0][$gTitle['col:分类']] && !$catId['cat_id'] )
            $msg['warning']['分类：'.$gData[0][$gTitle['col:分类']].'不存在'] = '';
        $gData[0][$gTitle['col:分类']] = intval( $catId['cat_id']);

        //品牌
        $oBrand = &$this->app->model('brand');
        if( !$gData[0][$gTitle['col:品牌']] ){
            $brandId = array('brand_id'=>0);
        }else{
            $brandId = $oBrand->dump(array('name'=>$gData[0][$gTitle['col:品牌']]),'brand_id');
            if( !$brandId['brand_id'] && $gData[0][$gTitle['col:品牌']] )
                $msg['warning']['品牌：'.$gData[0][$gTitle['col:品牌']].'不存在'] = '';
        }
        $gData[0][$gTitle['col:品牌']] = intval( $brandId['brand_id'] );

        //货品 处理return值
        $rs = $gData[0];
        $oPro = &$this->app->model('products');
        $spec = array();
        if( count( $gData ) == 1 ){
            unset($rs[$gTitle['col:规格']] );
            $gData[0][$gTitle['ibn:规格货号']] = $gData[0][$gTitle['bn:商品货号']];
            $proId = $oPro->dump( array('bn'=>$gData[0][$gTitle['bn:商品货号']] ),'product_id,goods_id' );

            if( ( !$rs['col:goods_id'] && $proId['product_id'] ) || ( $rs['col:goods_id'] && $rs['col:goods_id'] != $proId['goods_id'] ) ){
                $msg = array( 'error'=>'规格货号:'.$gData[0][$gTitle['bn:商品货号']].' 已存在' );
                return false;
            }
            $rs['product'][0] = $gData[0];
            if( $proId['product_id'] )
                $rs['product'][0]['col:product_id'] = $proId['product_id'];
        }else{
            reset($gData);
            $oSpec = &$this->app->model('specification');
            foreach( explode('|',$gData[0][$gTitle['col:规格']] ) as $speck => $specName ){
                $spec[$speck] = array(
                    'spec_name' => $specName,
                    'option' => array(),
                );
            }

            while( ( $aPro = next($gData) ) ){
                $aProk = key( $gData );
                $proId = $oPro->dump( array('bn'=>$aPro[$gTitle['ibn:规格货号']]),'product_id,goods_id' );

                if( ( !$rs['col:goods_id'] && $proId['product_id'] ) || ( $rs['col:goods_id'] && $rs['col:goods_id'] != $proId['goods_id'] ) ){
                    $msg = array( 'error'=>'规格货号:'.$aPro[$gTitle['ibn:规格货号']].' 已存在' );
                    return false;
                }
                $aPro['col:product_id'] = $proId['product_id'];
                $rs['product'][$aProk] = $aPro;
                foreach( explode('|',$aPro[$gTitle['col:规格']]) as $specvk => $specv ){
                    $spec[$specvk]['option'][$specv] = $specv;
                }
//                $gData[$aProk]['']
            }
            foreach($spec as $sk => $aSpec){
                $specIdList = $oSpec->getSpecIdByAll($aSpec);
                foreach( $specIdList as $sv ){
                    if( array_key_exists($sv['spec_id'],(array)$goodsTmpl['gtype']['spec'] ) ){
                        $spec[$sk]['spec_id'] = $sv['spec_id'];
                    }
                }
                if( !$spec[$sk]['spec_id'] )
                    $spec[$sk]['spec_id'] = $specIdList[0]['spec_id'];
                if( !$spec[$sk]['spec_id'] ){
                    $msg = array('error'=>'规格：'.$aSpec['spec_name'].'出现错误 请检查');
                    return false;
                }
                $spec[$sk]['option'] = $oSpec->getSpecValuesByAll($spec[$sk]);
            }
            $pItem = 0;
            foreach( $rs['product'] as $prok => $prov ){
                if( !($pItem++) )$rs['product'][$prok]['col:default'] = 1;
                $proSpec = explode('|',$prov[$gTitle['col:规格']]);
                $rs['product'][$prok]['col:spec_info'] = implode(',',$proSpec);

                foreach( $proSpec as $aProSpeck => $aProSpec ){
//                    foreach( $spec as $aaSpec ){
                    $rs['product'][$prok]['col:spec_desc']['spec_value'][$spec[$aProSpeck]['spec_id']] = $spec[$aProSpeck]['option'][$aProSpec]['spec_value'];
                    $rs['product'][$prok]['col:spec_desc']['spec_private_value_id'][$spec[$aProSpeck]['spec_id']] = $spec[$aProSpeck]['option'][$aProSpec]['private_spec_value_id'];
                    $rs['product'][$prok]['col:spec_desc']['spec_value_id'][$spec[$aProSpeck]['spec_id']] = $spec[$aProSpeck]['option'][$aProSpec]['spec_value_id'];
//                    }
                }
            }

            unset( $rs[$gTitle['col:规格']] );
            foreach( $spec as $sk => $sv ){
                foreach( $sv['option'] as $psk => $psv ){
                    $rs[$gTitle['col:规格']][$sv['spec_id']]['option'][$psv['private_spec_value_id']] = $psv;
                }
            }
       }

        $return =  $this->ioSchema2sdf( $rs,$gTitle, $this->ioSchema['csv'] );

        if( $gData[0][$gTitle['col:图片文件']] ){
            $oImage = &app::get('image')->model('image');
            $i = 0;
            foreach( explode( '#', $gData[0][$gTitle['col:图片文件']] ) as $image ){
                $image = explode('@',$image);
                if( count($image) == 2 ){
                    $imageId = $image[0];
                    $image = $image[1];
                }else{
                    $imageId = null;
                    $image = $image[0];
                }
                if( substr($image,0,4 ) == 'http' ){
                    $imageName = null;
                }else{
                    $imageName = null;
                    $image = ROOT_DIR.'/'.$image;
                }
                if( $imageId && !$oImage->dump($imageId) )
                    $imageId = null;
                $imageId = $oImage->store($image,$imageId,null,$imageName);
                $return['images'][] = array(
                    'target_type'=>'goods',
                    'image_id'=>$imageId
                );
                if( $i++ == 0 ){
                    $return['image_default_id'] = $imageId;
                }

            }
        }


        foreach( $return['product'] as $pk => $pv ){
            $return['product'][$pk]['name'] = $return['name'];
            foreach( $pv['price']['member_lv_price'] as $lvk => $lvv ){
                $return['product'][$pk]['price']['member_lv_price'][$lvk]['level_id'] = $lvk;
            }
        }

        $return['type']['type_id'] = intval( $goodsTmpl['gtype']['type_id'] );

        $this->io->goodsBn[$return['bn']] = null;
        return $return;
    }

    function getSparePrice(&$list,$memberLevel,$onMarketable = true){
        if(!function_exists('goods_get_spare_price')) require(CORE_INCLUDE_DIR.'/core/goods.get_spare_price.php');
        return goods_get_spare_price($list,$memberLevel,$onMarketable , $this);
    }

    function getProducts($gid, $pid=0){
        $sqlWhere = '';
        if($pid > 0) $sqlWhere = ' AND A.product_id = '.$pid;
        $sql = "SELECT A.*,B.image_default_id FROM sdb_b2c_products AS A LEFT JOIN sdb_b2c_goods AS B ON A.goods_id=B.goods_id WHERE A.goods_id=".intval($gid).$sqlWhere;
        return $this->db->select($sql);
    }

    function getGoodsIdByBn( $bn , $searchType = 'has') {

        switch($searchType){
            case'nohas':
                $goodsId = $this->db->select('SELECT g.goods_id FROM sdb_b2c_goods g LEFT JOIN sdb_b2c_products p ON g.goods_id = p.goods_id WHERE g.bn NOT LIKE "%'.$bn.'%" OR p.bn NOT LIKE "%'.$bn.'%"');
                break;
            case'tequal':
                $goodsId = $this->db->select('SELECT g.goods_id FROM sdb_b2c_goods g LEFT JOIN sdb_b2c_products p ON g.goods_id = p.goods_id WHERE g.bn in( "'.$bn.'") OR p.bn in( "'.$bn.'")');
                break;
            case'has':
            default:
                $goodsId = $this->db->select('SELECT g.goods_id FROM sdb_b2c_goods g LEFT JOIN sdb_b2c_products p ON g.goods_id = p.goods_id WHERE g.bn LIKE "%'.$bn.'%" OR p.bn LIKE "%'.$bn.'%"');
                break;
            case'head':
                $goodsId = $this->db->select('SELECT g.goods_id FROM sdb_b2c_goods g LEFT JOIN sdb_b2c_products p ON g.goods_id = p.goods_id WHERE g.bn LIKE "'.$bn.'%" OR p.bn LIKE "'.$bn.'%"');
                break;
            case'foot':
                $goodsId = $this->db->select('SELECT g.goods_id FROM sdb_b2c_goods g LEFT JOIN sdb_b2c_products p ON g.goods_id = p.goods_id WHERE g.bn LIKE "%'.$bn.'" OR p.bn LIKE "%'.$bn.'"');
                break;
        }

        $rs = array();
        foreach( $goodsId as $key=>$val) {
            if(!in_array($val['goods_id'],$rs)){
                $rs[] = $val['goods_id'];
            }
        }
        return $rs;
     }

    function getMarketableById($gid){
        return $this->db->selectrow('SELECT marketable FROM sdb_b2c_goods WHERE goods_id='.$gid);
    }

    function getPath($gid,$method=null){
        $gid['goods_id'] = $gid;
        $row = $this->dump($gid,'cat_id,name');
        $goods = &$this->app->Model('goods_cat');
        $ret = $goods->getPath($row['category']['cat_id'],$method);
        $ret[] = array('type'=>'goods','title'=>$row['name'],'link'=>'#');
        return $ret;
    }

    function fgetlist_csv(&$data,$filter,$offset){
        $subSdf = array(
            'product'=>array(
                '*',array('price/member_lv_price'=>array('*'))
            ),
            'images'=>array('*',array(':image'=>array('*'))),
            ':brand'=>array('*')
            //':goods_type'=>array('*')
        );
        $limit = 40;
        if( $filter['_gType'] ){
            $title = array();
            if(!$data['title'])$data['title'] = array();
            $data['title'][''.$filter['_gType']] = '"'.implode('","',$this->io->data2local( $this->io_title(array('type_id'=>$filter['_gType']))) ).'"';

            return false;
        }
        $oGtype = &$this->app->model('goods_type');
        if(!$goodsList = $this->getList('goods_id',$filter,$offset*$limit,$limit))return false;
        foreach( $goodsList as $aFilter ){
            $aGoods = $this->dump( $aFilter['goods_id'],'*',$subSdf );
            if( !$aGoods )continue;
            if( !$this->csvExportGtype[$aGoods['type']['type_id']] ){
                $this->csvExportGtype[$aGoods['type']['type_id']] = $oGtype->dump($aGoods['type']['type_id'],'*');
                //$data['title'][$aGoods['type']['type_id']];
                $data['title'][$aGoods['type']['type_id']] = '"'.implode('","',$this->io->data2local($this->io_title($aGoods['type']['type_id']))).'"';
            }
            $csvData = $this->sdf2csv($aGoods);
            $data['content'][$aGoods['type']['type_id']] = array_merge((array)$data['content'][$aGoods['type']['type_id']],(array)$csvData);
        }
        return true;

    }


    function export_csv($data){
        $output = array();
        foreach( $data['title'] as $k => $val ){
            $output[] = $val."\n".implode("\n",(array)$data['content'][$k]);
        }
        echo implode("\n",$output);
    }

    function getLinkList($goods_id){
        return $this->db->select('SELECT r.*, goods_id, bn, name FROM sdb_b2c_goods_rate r, sdb_b2c_goods
                WHERE ((goods_2 = goods_id AND goods_1='.intval($goods_id)
                .') OR (goods_1 = goods_id AND goods_2 = '.intval($goods_id)
                .' AND manual=\'both\')) AND rate > 99');
    }

    function sdf2csv( $sdfdata ){
        $rs = array();
//        $sdf = $this->_column();
//        $product = $sdfdata['product'];
        //        unset($sdfdata['product']);
        $conTmp = array();
        foreach( $this->io_title( $sdfdata['type']['type_id'] ) as $titleCol ){
            $conTmp[$titleCol] = '';
        }
        $gcontent = $conTmp;

        $this->oSchema['csv'][$sdfdata['type']['type_id']]['col:市场价'] = 'mktprice';
        $this->oSchema['csv'][$sdfdata['type']['type_id']]['col:成本价'] = 'cost';
        $this->oSchema['csv'][$sdfdata['type']['type_id']]['col:销售价'] = 'price';
        $sdfdata['type']['name'] = $this->csvExportGtype[$sdfdata['type']['type_id']]['name'];
        foreach( $this->oSchema['csv'][$sdfdata['type']['type_id']] as $title => $sdfpath ){
            if( !is_array($sdfpath) ){
                $tSdfCol = utils::apath($sdfdata,explode('/',$sdfpath));
                $gcontent[$title] = (is_array($tSdfCol)?$tSdfCol:$this->charset->utf2local($tSdfCol));
            }else{
                $gcontent[$title] = '';
            }
            if( substr($title,0,6) == 'props:' ){
                if( !$gcontent && $gcontent[$title]['value'] !== 0 ){
                    $gcontent[$title] = '';
                }else{
                    $k = explode('_',$sdfpath);
                    $k = $k[1];
                    if( $this->csvExportGtype[$sdfdata['type']['type_id']]['props'][$k]['options'] ){
                        $gcontent[$title] = $this->charset->utf2local( $this->csvExportGtype[$sdfdata['type']['type_id']]['props'][$k]['options'][$gcontent[$title]['value']] );
                    }else{
                        $gcontent[$title] = $this->charset->utf2local( $gcontent[$title]['value'] );
                    }
                }
            }
        }
        $cat = array();
        $oCat = &$this->app->model('goods_cat');
        $tcat = $oCat->dump($sdfdata['category']['cat_id'],'cat_path');
        $catPath = array();
        foreach( explode(',',$tcat['cat_path']) as $catv ){
            if( $catv )$catPath[] = $catv;
        }
        if( $catPath ){
            foreach( $oCat->getList('cat_name',array('cat_path'=>$catPath)) as $acat ){
                if( $acat ) $cat[] = $this->charset->utf2local( $acat['cat_name'] );
            }
            $gcontent['col:分类'] = implode('->',$cat);
        }else{
            $gcontent['col:分类'] = '';
        }
        $gcontent['col:上架'] = $gcontent['col:上架'] == 'true'?'Y':'N';
        if( $sdfdata['images'] ){
            $oImage = &app::get('image')->model('image');
            foreach( $sdfdata['images'] as $aImage ){
                $imageData = $oImage->dump($aImage['image_id'],'url');
                $gcontent['col:图片文件'][] = $aImage['image_id'].'@'.$imageData['url'];
            }
            $gcontent['col:图片文件'] = implode('#',$gcontent['col:图片文件']);
        }
        $this->oSchema['csv'][$sdfdata['type']['type_id']]['col:市场价'] = array('price/mktprice/price','product');
        $this->oSchema['csv'][$sdfdata['type']['type_id']]['col:成本价'] = array('price/cost/price','product');
        $this->oSchema['csv'][$sdfdata['type']['type_id']]['col:销售价'] = array('price/price/price','product');

        if( !$sdfdata['spec'] ){
            $product = current( (array)$sdfdata['product'] );
//            $proContent = $conTmp;
            foreach( $this->oSchema['csv'][$sdfdata['type']['type_id']] as $title => $sdfpath ){
                if( is_array($sdfpath) && $sdfpath[1] == 'product' ){
                    $tSdfCol = utils::apath($sdfdata,explode('/',$sdfpath));
                    $gcontent[$title] = (is_array($tSdfCol))?$tSdfCol:$this->charset->utf2local($tSdfCol);
                }
            }
            $gcontent['col:规格'] = '-';
            $rs[0] = '"'.implode('","',$gcontent).'"';
        }else{
            $spec = array();
            foreach( $sdfdata['spec'] as $aSpec ){
                $spec[] = $aSpec['spec_name'];
            }
            $gcontent['col:规格'] = $this->charset->utf2local( implode('|',$spec) );

            $oSpec = &$this->app->model('spec_values');

            $rs[0] = '"'.implode('","',$gcontent).'"';
            foreach( $sdfdata['product'] as $row => $aSdfdata ){
                $content = $conTmp;
                foreach( $this->oSchema['csv'][$sdfdata['type']['type_id']] as $title => $sdfpath ){
                    $content[$title] = $this->charset->utf2local(utils::apath($aSdfdata,explode('/',(!is_array($sdfpath)?$sdfpath:$sdfpath[0]))));
                }
                $specValue = array();
                foreach( $oSpec->getList('spec_value',array('spec_value_id'=>$aSdfdata['spec_desc']['spec_value_id']) ) as $aSpecValue ){
                    $specValue[] = $this->charset->utf2local($aSpecValue['spec_value']);
                }
                $content['col:规格'] = implode('|',$specValue);
                $content['bn:商品货号'] = $gcontent['bn:商品货号'];
                $content['col:上架'] = $content['col:上架'] == 'true'?'Y':'N';
                $content['*:'.$sdfdata['type']['name']] = $this->charset->utf2local( $sdfdata['type']['name']);
                $rs[$row] = '"'.implode('","',$content).'"';
            }
        }
        return $rs;
    }
    function searchOptions(){
        $arr = parent::searchOptions();
        return array_merge($arr,array(
                'bn'=>__('货号'),
                'keyword'=>__('商品关键字'),
            ));
    }
    /*
    function prepared_export($queueData){
        $queueData['params']['search_limit'] = 100;
        $queueData['params']['filename'] = 'goods-'.$queueData['params']['time'];
        return $queueData;
    }
     */
}
