<?php
/**
 * mdl_products
 *
 * @uses modelFactory
 * @package
 * @version $Id: mdl.products.php 2042 2008-04-29 05:31:30Z ever $
 * @copyright 2003-2007 ShopEx
 * @author Wanglei <flaboy@zovatech.com>
 * @license Commercial
 */
include_once('shopObject.php');
class mdl_products extends shopObject{

    var $idColumn = 'goods_id';
    var $textColumn = 'name';
    var $defaultCols = 'bn,name,cat_id,price,store,marketable,brand_id,weight,d_order,uptime,type_id';
    var $appendCols = 'goods_id,image_default,thumbnail_pic,brief,pdt_desc,mktprice';
    var $adminCtl = 'goods/product';
    var $defaultOrder = array('d_order',' DESC',',p_order',' DESC');
    var $tableName = 'sdb_goods';
    var $hasTag = true;
    var $typeName = 'goods';
    var $keywordsColumn='bn';
    var $globalTmp;        //零时全局变量

    function getColumns($filter){

        $columns = array(
            'bn'=>array('label'=>'商品编号','class'=>'span-3','fuzzySearch'=>1,'required'=>true, 'primary' => true),    /* 商品货号 */
            'name'=>array('label'=>'商品名称','class'=>'span-8','fuzzySearch'=>1,'required'=>true, 'primary' => true,'type'=>'goods_name'),    /* 商品名称 */
            'cat_id'=>array('label'=>'分类','class'=>'span-2','type'=>'object:goodscat:getMapTree' ),    /* 分类ID */
            'type_id'=>array('label'=>'类型','class'=>'span-2','type'=>'object:gtype','readonly'=>true),    /* 类型id */
            'mktprice'=>array('label'=>'市场价','class'=>'span-2','type'=>'money','vtype'=>'positive'),    /* 市场价 */
            'cost'=>array('label'=>'成本价','class'=>'span-2','type'=>'money','readonly'=>true),    /* 成本价 */
            'price'=>array('label'=>'销售价','class'=>'span-2','type'=>'money','vtype'=>'bbsales','readonly'=>true),    /* 商品销售价 */
            'store'=>array('label'=>'库存','class'=>'span-1','readonly'=>true),    /* 商品库存 =
            min(
                (货品库存-货品冻结库存)/货品单次购买量
            ) */
            'd_order'=>array('label'=>'排序','class'=>'span-1','type'=>'digits'),    /* 排序 */
            'goods_type'=>array('label'=>'销售类型（normal：正常；bind：捆绑商品）','class'=>'span-3','readonly'=>true),    /* 商品类型（normal：正常；bind：捆绑商品） */
            'goods_id'=>array('label'=>'商品id','class'=>'span-3','readonly'=>true),    /* 商品id */
            'brand_id'=>array('label'=>'品牌','class'=>'span-2','type'=>'object:brand'),    /* 品牌id */
            'brand'=>array('label'=>'品牌','class'=>'span-3','readonly'=>true),    /* 品牌id */
            'spec'=>array('label'=>'规格','class'=>'span-3'),    /* 品牌id */
            'pdt_desc'=>array('label'=>'物品','class'=>'span-3'),    /* 物品序列化 */
            'spec_desc'=>array('label'=>'物品','class'=>'span-3'),    /* 物品序列化 */
            's_goods_id'=>array('label'=>'Shopex商品id','class'=>'span-3'),    /* Shopex商品id */
            'image_default'=>array('label'=>'默认图片','class'=>'span-2','readonly'=>true),    /* 默认图片 */
            'udfimg'=>array('label'=>'是否用户自定义图','class'=>'span-3','readonly'=>true),    /* 是否用户自定义图 */
            'thumbnail_pic'=>array('label'=>'缩略图','class'=>'span-3','readonly'=>true),    /* 缩略图 */
            'image_file'=>array('label'=>'图片文件','class'=>'span-3','readonly'=>true),    /* 图片文件 */
            'intro'=>array('label'=>'详细介绍','class'=>'span-3','readonly'=>true),    /* 商品描述简介 */
            'brief'=>array('label'=>'商品简介','class'=>'span-3'),    /* 商品简介 */

            'marketable'=>array('label'=>'上架','class'=>'span-1','type'=>'bool'),    /* 是否销售 */
            'weight'=>array('label'=>'重量','class'=>'span-2','readonly'=>true),    /* 单件重量 */
            'unit'=>array('label'=>'单位','class'=>'span-1','readonly'=>true),    /* 单位 */

            'score'=>array('label'=>'积分','class'=>'span-1','type'=>'number'),    /* 积分值 */
            'uptime'=>array('label'=>'上架时间','class'=>'span-3','type'=>'time'),    /* 上传时间 */
            'downtime'=>array('label'=>'下架时间','class'=>'span-3','type'=>'time'),    /* 下架时间 */
            'last_modify'=>array('label'=>'最后更新时间','class'=>'span-3','readonly'=>true),    /* 最后更新时间 */
            'notify_num'=>array('label'=>'缺货登记','class'=>'span-3','readonly'=>true),    /* 到货通知数量 */
        );
        return $columns;
    }

    function getProperty($cols,$filter){
        $sql = 'SELECT '.$cols.' FROM '.$this->tableName.' WHERE '.$this->_filter($filter);
        return ($this->db->select($sql));
    }

    function getfilterProperty($type_id){
        $sqlString = 'SELECT t.props,t.schema_id,t.setting,t.type_id FROM sdb_goods_type t WHERE t.type_id = '.intval($type_id);

        $row = $this->db->selectrow($sqlString);
        if($row['props']) $row['props'] = unserialize($row['props']);
        //if($row['tabs']) $row['tabs'] = unserialize($row['tabs']);
        if($row['setting']) $row['setting'] = unserialize($row['setting']);

        if($row['type_id']){
            $row['brand'] = $this->db->select('SELECT b.brand_id,b.brand_name,brand_url,brand_logo FROM sdb_type_brand t
                    LEFT JOIN sdb_brand b ON b.brand_id=t.brand_id
                    WHERE disabled="false" AND t.type_id='.$row['type_id'].' ORDER BY brand_order');
        }else{
            $oBrand = $this->system->loadModel('goods/brand');
            $row['brand'] = $oBrand->getList('*', '', 0, -1);
        }

        $dftList = array(
                '图文列表'=>'list',
                '橱窗'=>'grid',
                '文字'=>'text',
            );
        if(isset($row['setting']['list_tpl']) && is_array($row['setting']['list_tpl']))
            foreach($row['setting']['list_tpl'] as $k=>$tpl){
                if(!in_array($tpl,$dftList)){
                    if(!file_exists(SCHEMA_DIR.$row['schema_id'].'/view/'.$tpl.'.html')){
                        unset($row['setting']['list_tpl'][$k]);
                    }
                }
            }
        if(!isset($row['setting']['list_tpl']) || !is_array($row['setting']['list_tpl']) || count($row['setting']['list_tpl'])==0){
            $row['setting']['list_tpl'] = $dftList;
        }

        if($view=='index')$view = current($row['setting']['list_tpl']);
        if(in_array($view,$dftList)){
            if (defined('CUSTOM_CORE_DIR')&&file_exists(CUSTOM_CORE_DIR.'/shop/view/gallery/type/'.$view.'.html'))
                $row['tpl'] = realpath(CUSTOM_CORE_DIR.'/shop/view/gallery/type/'.$view.'.html');
            else
                $row['tpl'] = realpath(CORE_DIR.'/shop/view/gallery/type/'.$view.'.html');
        }else{
            $row['tpl'] = realpath(SCHEMA_DIR.$row['schema_id'].'/view/'.$view.'.html');
        }

        $row['dftView'] = $view;
        $row['setting']['list_tpl'][key($row['setting']['list_tpl'])] = 'index';
        return $row;
    }
    function getList($cols,$filter='',$start=0,$limit=20,&$count,$orderType=null){
        $ident=md5($cols.print_r($filter,true).$start.$limit);
        if(!$this->_dbstorage[$ident]){
            if(!$cols){
                $cols = $this->defaultCols;
            }
            if($this->appendCols){
                $cols.=','.$this->appendCols;
            }
            $sql = 'SELECT '.$cols.' FROM '.$this->tableName.' WHERE '.$this->_filter($filter);
            if(is_array($orderType)){
                $orderType = trim(implode($orderType,' '))?$orderType:$this->defaultOrder;
                if($orderType){
                    $sql.=' ORDER BY '.implode($orderType,' ');
                }
            }elseif($orderType){
                $sql .= ' ORDER BY ' . $orderType;
            }else{
                $sql.=' ORDER BY '.implode($this->defaultOrder,' ');
            }
            $count = $this->db->_count($sql);

            $rows = $this->db->selectLimit($sql,$limit,$start);
            if(isset($filter['mlevel']) && $filter['mlevel']){
                $oLv = $this->system->loadModel('member/level');
                if($level = $oLv->getFieldById($filter['mlevel'])){
                    foreach($rows as $k=>$r){
                        $arrMp[$r['goods_id']] = &$rows[$k]['price'];
                        if($level['dis_count'] > 0){
                            $rows[$k]['price'] *= $level['dis_count'];
                        }
                    }
                    if(count($arrMp)>0){
                        $sql = 'SELECT goods_id,MIN(price) AS mprice FROM sdb_goods_lv_price WHERE goods_id IN ('
                                .implode(',', array_keys($arrMp)).') AND level_id='.intval($filter['mlevel']).' GROUP BY goods_id';
                        foreach($this->db->select($sql) as $k=>$r){
                            $arrMp[$r['goods_id']] = $r['mprice'];
                        }
                    }
                }
            }
            $this->_dbstorage[$ident]=$rows;
        }
        return $this->_dbstorage[$ident];
    }

    function _filter($filter,$tbase=''){
        $where = array();
        if($filter['list_type']=='lack'){
                $oProduct = $this->system->loadModel('goods/finderPdt');
                $filter_p['store_alarm'] = $this->system->getConf('system.product.alert.num');
                foreach($oProduct->getList('goods_id', $filter_p, 0, -1) as $row){
                    $filter['goods_id'][] = $row['goods_id'];
                }
        }

        if($filter['cat_id']){
            if(!is_array($filter['cat_id'])){
                $filter['cat_id']=array($filter['cat_id']);
            }
            foreach($filter['cat_id'] as $vCat_id){
                if($vCat_id!='_ANY_' && $vCat_id !== ''){
                    $aCat_id[] = intval($vCat_id);
                }
            }
            $filter['cat_id']=$aCat_id;
            if(!isset($this->__show_goods)){
                $this->__show_goods = $this->system->getConf('system.category.showgoods');
            }
            if($this->__show_goods){
                if(count($filter['cat_id'])>0)
                    $where[] = 'cat_id in ('.implode($filter['cat_id'],' , ').')';
            }else{
                $oCat = $this->system->loadModel('goods/productCat');
                $aCat = $oCat->getFieldById($filter['cat_id'], array('cat_path','cat_id'));
                $pathplus='';
                if(count($aCat)){
                    foreach($aCat as $v){
                        $pathplus.=' cat_path LIKE \''
                                .($v['cat_path']==','?'':$v['cat_path']).$v['cat_id'].',%\' OR';
                    }
                }
                if($aCat){
                    foreach($this->db->select('SELECT cat_id FROM sdb_goods_cat WHERE '.$pathplus.' cat_id in ('.implode($filter['cat_id'],' , ').')') as $rows){
                        $aCatid[] = $rows['cat_id'];
                    }
                }else{
                    unset($aCatid);
                }
               
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
                foreach($filter['goods_id'] as $goods_id){
                    if($goods_id!='_ANY_'){
                        $goods[] = intval($goods_id);
                    }
                }
            }else{
                $goods[] = intval($filter['goods_id']);
            }
        }
        unset($filter['goods_id']);
        if(isset($filter['tag']) && is_array($filter['tag'])){

            foreach($filter['tag'] as $tag){
                if($tag!='_ANY_'){
                    $aTag[] = intval($tag);
                }

            }

            if(count($aTag)>0){
                $tagId[] = -1;
                foreach($this->db->select('SELECT rel_id FROM sdb_tag_rel r
                    LEFT JOIN sdb_tags t ON r.tag_id=t.tag_id
                    WHERE t.tag_type = \'goods\' AND r.tag_id IN('.implode(',', $aTag).')') as $rows){
                        $tagId[] = $rows['rel_id'];
                }
                if($goods) $goods = array_intersect($goods, $tagId);
                else $goods = $tagId;
            }
            $filter['tag'] = null;
        }

        if(isset($filter['keyword']) && $filter['keyword']) {
            $filter['keywords'] = array($filter['keyword']);
        }
        unset($filter['keyword']);

        if(isset($filter['keywords']) && $filter['keywords'] && !in_array('_ANY_',$filter['keywords'])) {
            $oGoods = $this->system->loadModel('trading/goods');
            $keywordsList = $oGoods->getGoodsIdByKeyword($filter['keywords']);
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
                $sBn = $filter['bn'][0];
            }else{
                $sBn = $filter['bn'];
            }
            $bnGoodsId = $this->getGoodsIdByBn($sBn);

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
            $filter['bn'] = null;
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
                }elseif(isset($v) && $v!=''){
                    $where[] = $tPre.$k.'=\''.$v.'\'';
                }
            }

            else if( substr($k,0,2) == 's_' ){
                $sSpecId = array();
                if( is_array( $v ) ){
                    foreach( $v as $n ){
                        if( $n !== '_ANY_' && $n != false ){
                            $sSpecId[] = $n;
                        }
                    }
                }

                if( count( $sSpecId )>0 ){
                    $sGoodsId = $this->db->select( 'SELECT goods_id FROM sdb_goods_spec_index WHERE spec_value_id IN ( '.implode( ',',$sSpecId ).' )' );
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
            /*
            if($filter['price'][0] && $filter['price'][1]){
                $where[] = 'price >= '.min($filter['price']).' AND price <= '.max($filter['price']);
            }*/
          
            unset($filter['price']);
        }else if(($filter['pricefrom']==0 || $filter['pricefrom']) && ($filter['priceto'] || $filter['priceto'])){
            $where[] = 'price >= '.$filter['pricefrom'].' AND price <= '.$filter['priceto'];
            unset($filter['pricefrom']);
            unset($filter['priceto']);
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
                    $filter['name']=stripslashes($filter['name']);
                    $filter['name']=preg_replace('/[\'|\"]/','+',$filter['name']);
                    
                    $GLOBALS['search']=$filter['name'];
                    $where[]=$this->wFilter($filter['name']);
                   
                }
            }else{ //后台搜索
                $GLOBALS['search']=$filter['name'];
                $where[] = 'name LIKE \'%'.$filter['name'].'%\'';
            }
            $filter['name'] = null;
        }

        $filter['goods_type'] = 'normal';
         
        return parent::_filter($filter,$tbase,$where);

    }

    function getGoodsIdByBn( $bn ) {
        $goodsId = $this->db->select('SELECT g.goods_id FROM sdb_goods g INNER JOIN sdb_products p ON g.goods_id = p.goods_id WHERE g.bn = "'.$bn.'" OR p.bn = "'.$bn.'"');
        $rs = array();
        $goodsId = array_unique($goodsId);
        foreach( $goodsId as $key=>$val) {
            $rs[] = $val['goods_id'];
        }
        return $rs;
    }

    function wFilter($words){
        $replace = array(",","+");
        $enStr=preg_replace("/[^chr(128)-chr(256)]+/is"," ",$words);
        $otherStr=preg_replace("/[chr(128)-chr(256)]+/is"," ",$words);
        $words=$enStr.' '.$otherStr;
        $return=str_replace($replace,' ',$words);
        $word=preg_split('/\s+/s',trim($return));
        $GLOBALS['search_array']=$word;

        $oGoods = $this->system->loadModel('trading/goods');
        foreach($word as $k=>$v){
            if($v){
                $goodsId = array();
                foreach($oGoods->getGoodsIdByKeyword(array($v)) as $idv)
                    $goodsId[] = $idv['goods_id'];
                foreach( $this->db->select('SELECT goods_id FROM sdb_products WHERE bn = \''.trim($v).'\' ') as $pidv)
                    $goodsId[] = $pidv['goods_id'];
                $sql[]='(name LIKE \'%'.$word[$k].'%\' or bn = \''.$word[$k].'\' '.( $goodsId?' or goods_id IN ('.implode(',',$goodsId).') ':'' ).')';
            }
        }
        return implode('and',$sql);
    }

    function searchOptions(){
        return array(
            'bn'=>'货号',
            'name'=>'商品名称',
            'keyword'=>'商品关键词',
        );
    }
    function getCountBrand($search,$cat_id){
        $brand = implode(',',$search);
        $sql='SELECT  count(brand_id) as sumBrand,brand_id from sdb_goods where cat_id = '.$cat_id.' and brand_id in ("'.$brand.'") and disabled=false GROUP BY brand_id';
        $result = $this->db->select($sql);
        return $result;
    }

    function getSparePrice(&$list,$memberLevel){
        if(count($list)>0){
            $level='';
            if($memberLevel){
                $level='and B.level_id='.$memberLevel;

                $oLv = $this->system->loadModel('member/level');
                $aLevel = $oLv->getFieldById($memberLevel, array('dis_count'));
                if(floatval($aLevel['dis_count']) <= 0) $aLevel['dis_count'] = 1;
            }
            $id=array();
            foreach($list as $p=>$q){
                $id[]=intval($q['goods_id']);
            }
            $all=implode(",",$id);
            $sql='SELECT A.product_id,A.goods_id,A.pdt_desc,A.price,B.price as m_price,A.store,A.freez FROM sdb_products A';
            $sql.=' LEFT JOIN sdb_goods_lv_price B ON A.product_id=B.product_id '.$level;
            $sql.=' WHERE A.goods_id IN ('.$all.') order by A.goods_id';
            $price_gid=array();
            $store_gid=array();
            $freez_gid=array();
            $oMath = $this->system->loadModel('system/math');
            foreach($this->db->select($sql) as $q1=>$v1){
                $price_gid[$v1['product_id']]=$v1['m_price']?$v1['m_price']:($v1['price']*$aLevel['dis_count']);
                $price_gid[$v1['product_id']]= $oMath->getOperationNumber($price_gid[$v1['product_id']]);
                $price_goodsid[$v1['goods_id']]= $oMath->getOperationNumber($price_gid[$v1['product_id']]);
                $store_gid[$v1['product_id']]=$v1['store'];
                $freez_gid[$v1['product_id']]=$v1['freez'];
            }
            foreach($list as $k => $aRow){
                $list[$k]['pdt_desc'] = unserialize($list[$k]['pdt_desc']);
                if(is_array($list[$k]['pdt_desc'])){
                    foreach($list[$k]['pdt_desc'] as $q=>$v){
                            $list[$k]['pdt_desc']['price'][$q]=$price_gid[$q];
                            $list[$k]['pdt_desc']['store'][$q]=$store_gid[$q];
                            $list[$k]['pdt_desc']['freez'][$q]=$freez_gid[$q];
                    }
                }else{
                    $list[$k]['price'] = $price_goodsid[$aRow['goods_id']];
                }
            }
        }
        return $list;
    }

    function orderBy($id=null){
        $order=array(
            array('label'=>'默认','sql'=>implode($this->defaultOrder,'')),
            array('label'=>'按发布时间 新->旧','sql'=>'last_modify desc'),
            array('label'=>'按发布时间 旧->新','sql'=>'last_modify'),
            array('label'=>'按价格 从高到低','sql'=>'price desc'),
            array('label'=>'按价格 从低到高','sql'=>'price'),
            array('label'=>'访问周次数','sql'=>'view_w_count desc'),
            array('label'=>'总访问次数','sql'=>'view_count desc'),
            array('label'=>'周购买次数','sql'=>'buy_count desc'),
            array('label'=>'总购买次数','sql'=>'buy_w_count desc'),
            array('label'=>'评论次数','sql'=>'comments_count desc'),
        );
        if($id){
            return $order[$id];
        }else{
            return $order;
        }

    }

    function getLastModify($_time){
        if(is_array($_time)){
            $result = $this->db->selectRow('SELECT last_modify FROM sdb_goods WHERE goods_id IN ("'.$_time[0].'","'.$_time[1].'") Order By last_modify Desc');
        }else{
            $result = $this->db->selectRow("SELECT last_modify FROM sdb_goods Order By last_modify Desc");
        }

        return $result['last_modify'];
    }
    function getPath($gid,$method=null){
        $row = $this->db->selectrow('select cat_id,name from sdb_goods where goods_id='.intval($gid));
        $goods = &$this->system->loadModel('goods/productCat');
        $ret = $goods->getPath($row['cat_id'],$method);
        $ret[] = array('type'=>'goods','title'=>$row['name']);
        return $ret;
    }
    function getFilterByTypeId($p){
        $cat = $this->system->loadModel('goods/productCat');
        if(!$this->catMap){
            $this->catMap = $cat->get_cat_list();

        }
        $return['cats'] = $this->catMap;
        $cat_id=$p['type_id'];
        if($p = $this->getfilterProperty($cat_id)){
            $return['props'] = $p['props'];
            $brand = $this->system->loadModel('goods/brand');
            $return['brands'] = $brand->getAll();
            $return['cat_id'] = $p['cat_id'];

            $row = $this->db->selectrow('SELECT max(price) as max,min(price) as min FROM sdb_goods where type_id='.intval($cat_id));
        }else{
            $brand = $this->system->loadModel('goods/brand');
            $return['brands'] = $brand->getAll();

            $row = $this->db->selectrow('SELECT max(price) as max,min(price) as min FROM sdb_products ');
        }
        $modTag = $this->system->loadModel('system/tag');
        $return['type_id'] = $cat_id;
        $return['tags'] = $modTag->tagList('goods');
        $return['prices'] = steprange($row['min'],$row['max'],5);
        return $return;
    }
    function getFilter($p){

        /*
        $return['cats']=1;
        $return['brands']=2;
        $return['tags']=3;
        $return['prices']=4;
        return $return;
        */
        $cat = &$this->system->loadModel('goods/productCat');
        if(!$this->catMap){
            $this->catMap = $cat->get_cat_list();
        }

        $return['cats'] = $this->catMap;

        if($cat_id = $p['cat_id']){

            $p = $cat->get($cat_id);
            $return['props'] = $p['props'];
            $brand = $this->system->loadModel('goods/brand');
            $return['brands'] = $brand->getAll();
            $return['cat_id'] = $p['cat_id'];

            $row = $this->db->selectrow('SELECT max(price) as max,min(price) as min FROM sdb_goods where cat_id='.intval($cat_id));
        }else{
            $brand = $this->system->loadModel('goods/brand');
            $return['brands'] = $brand->getAll();

            $row = $this->db->selectrow('SELECT max(price) as max,min(price) as min FROM sdb_products ');
        }

        $modTag = $this->system->loadModel('system/tag');
        $return['tags'] = $modTag->tagList('goods');

        if($p['goods_id']){
            $oGoods = $this->system->loadModel('trading/goods');
            $return['keywords'] = $oGoods->getKeywords($p['goods_id']);
        }

        $return['prices'] = steprange($row['min'],$row['max'],5);
        return $return;
    }

    function setEnabled($finderResult,$status){
        $where = $finderResult? $this->_filter($finderResult):'goods_id in ('.implode(',',$finderResult['goods_id']).')';
        $sql = 'update sdb_goods set marketable="'.($status?'true':'false').'" where '.$where;
        $this->db->exec($sql);
        $status = $this->system->loadModel('system/status');
        $status->count_goods_online();
        $status->count_goods_hidden();
        $status->count_galert();

        return true;
    }

    function setDisabled($aGid, $status='true'){
        foreach($aGid as $gid){
            $this->db->exec('UPDATE sdb_products SET disabled = \''.$status.'\' WHERE goods_id = '.intval($gid));
        }
        $status = $this->system->loadModel('system/status');
        $status->count_goods_online();
        $status->count_goods_hidden();
        $status->count_galert();
        return true;
    }

    function setOrder($aGid, $ordLevel){
        $sql = 'UPDATE sdb_goods SET p_order = '.intval($ordLevel).', d_order = '.intval($ordLevel).' WHERE goods_id IN ('.implode(',',$aGid).')';
        $this->db->exec($sql);
    }

    function editAll($filter,$data){

        if($filter['items']){
            $where = 'goods_id in ('.implode(',',$filter['items']).')';
        }else{
            $where = $this->_filter($this->tofilter($filter));
        }
        $rs = $this->db->exec('select * from sdb_goods where '.$where);
        $sql = $this->db->getUpdateSQL($rs,$data,false,null,true);
        return (!$sql || $this->db->exec($sql));
    }

    /**
     * getFieldById
     *
     * @param array $aFeild
     * @param int $id
     * @access public
     * @return void
     */
    function getFieldById($id, $aFeild=array('*')){
        $sqlString = "SELECT ".implode(',', $aFeild)." FROM sdb_products WHERE product_id = ".intval($id);
        return $this->db->selectrow($sqlString);
    }

    function toUpdateStore($productId, $goodsId=0, $number=0, $gtype='goods'){
        if($gtype=='goods'){
            $aProduct = $this->getFieldById($productId, array('goods_id,store,freez'));
            if($aProduct['store'] !== null){
                $this->db->exec("UPDATE sdb_products SET store = ".($aProduct['store']>intval($number) ? "store - ".intval($number) : 0)
                        .", freez = ".($aProduct['freez']>intval($number) ? "freez - ".intval($number) : 0)
                        ." WHERE product_id = ".intval($productId));
            }
        }else{
            $aProduct['goods_id'] = $productId;
        }

        $g = $this->system->loadModel('trading/goods');
        $aGoods = $g->getFieldById($aProduct['goods_id'], array('store'));
        if($aGoods['store'] !== null){
            $this->db->exec("UPDATE sdb_goods SET store = ".($aGoods['store']>intval($number) ? "store - ".intval($number) : 0)
                    ." WHERE goods_id = ".intval($aProduct['goods_id']));
        }
        $status = $this->system->loadModel('system/status');
        $status->count_galert();
    }

    function toFreezStore($productId, $number=0){
        $this->db->exec("UPDATE sdb_products SET freez = freez + ".intval($number)." WHERE product_id = ".intval($productId));
    }

    function updateRate($aGid){
        $aBakid = $aGid;
        foreach($aGid as $gid1){
            $aInsert['goods_1'] = $gid1;
            $aInsert['manual'] = 'both';
            foreach($aBakid as $gid2){
                if($gid1 != $gid2){
                    $aRet = $this->db->select('SELECT rate FROM sdb_goods_rate
                        WHERE ((goods_1 = '.intval($gid1).' AND goods_2 = '.intval($gid2)
                        .') OR (goods_1 = '.intval($gid2).' AND goods_2 = '.intval($gid1).')) AND rate < 100');
                    $aInsert['goods_2'] = $gid2;
                    if(count($aRet) > 0){
                        $aInsert['rate'] = ($aRet[0]['rate']>98 ? 99: $aRet[0]['rate']+1);
                        $this->db->exec('UPDATE sdb_goods_rate SET rate = '.$aInsert['rate']
                            .' WHERE (goods_1 = '.intval($gid1).' AND goods_2 = '.intval($gid2)
                            .') OR (goods_1 = '.intval($gid2).' AND goods_2 = '.intval($gid1).')');
                    }else{
                        $aInsert['rate'] = 1;
                        $rs = $this->db->exec('SELECT * FROM sdb_goods_rate WHERE 0=1');
                        $sql = $this->db->getUpdateSQL($rs, $aInsert);
                        if($sql) $this->db->exec($sql);
                    }
                }
            }
            reset($aBakid);
        }
        return true;
    }

    function toInsertLink($gid, $aData){
        $aLinked = $this->db->select("SELECT * FROM sdb_goods_rate WHERE goods_1 = '".intval($gid)."'");
        if(empty($aLinked)){
            if(!empty($aData)){
                foreach($aData as $rows){
                    $aInsert = $rows;
                    $aInsert['goods_1'] = $gid;
                    $aInsert['rate'] = 100;
                    $rs = $this->db->exec('SELECT * FROM sdb_goods_rate WHERE 0=1');
                    $sql = $this->db->getInsertSQL($rs, $aInsert);
                    if($sql)$this->db->exec($sql);
                }
            }
        }else{
            if(empty($aData)){
                foreach($aLinked as $rows){
                    if($rows['goods_1'] == $gid){
                        $this->db->exec('DELETE FROM sdb_goods_rate WHERE goods_1 = '.$gid.' AND goods_2 = '.$rows['goods_2']);
                        if($rows['manual'] == 'both'){
                            $aInsert['goods_1'] = $rows['goods_2'];
                            $aInsert['goods_2'] = $gid;
                            $aInsert['manual'] = 'left';
                            $aInsert['rate'] = 100;
                            $rs = $this->db->exec('SELECT * FROM sdb_goods_rate WHERE 0=1');
                            $sql = $this->db->getInsertSQL($rs, $aInsert);
                            if($sql) $this->db->exec($sql);
                        }
                    }else{
                        if($rows['manual'] == 'both'){
                            $rs = $this->db->exec('SELECT * FROM sdb_goods_rate WHERE goods_1 = '.$rows['goods_1'].' AND goods_2 = '.$rows['goods_2']);
                            $sql = $this->db->getUpdateSQL($rs, array('manual' => 'left'));
                            if($sql) $this->db->exec($sql);
                        }
                    }
                }
            }else{
                $aResult = array();
                foreach($aLinked as $rows){
                    $deleteMark = 1;
                    foreach($aData as $k => $news){
                        if($rows['goods_1'] == $gid){
                            if($rows['goods_2'] == $news['goods_2']){
                                if($rows['manual'] != $news['manual']){
                                    $rows['manual'] = $news['manual'];
                                    $aResult[] = $rows;
                                }else{
                                    $deleteMark = 0;
                                }
                                unset($aData[$k]);
                            }
                        }else{
                            if($rows['goods_1'] == $news['goods_2']){
                                if($rows['manual'] == 'both'){
                                    if($news['manual'] == 'left'){
                                        $rows['manual'] = 'left';
                                        $rows['goods_1'] = $gid;
                                        $rows['goods_2'] = $news['goods_2'];
                                        $aResult[] = $rows;
                                    }else{
                                        $deleteMark = 0;
                                    }
                                }else{
                                    $rows['manual'] = $news['manual'];
                                    $rows['goods_1'] = $gid;
                                    $rows['goods_2'] = $news['goods_2'];
                                    $aResult[] = $rows;
                                }
                                unset($aData[$k]);
                            }
                        }
                    }
                    if($deleteMark){
                        $this->db->exec('DELETE FROM sdb_goods_rate WHERE goods_1 = '.$rows['goods_1'].' AND goods_2 = '.$rows['goods_2']);
                    }
                }
                $aResult = array_merge($aData, $aResult);
                if(count($aResult)){
                    foreach($aResult as $rows){
                        $rs = $this->db->exec('SELECT * FROM sdb_goods_rate WHERE 0=1');
                        $sql = $this->db->getInsertSQL($rs, $rows);
                        if($sql) $this->db->exec($sql);
                    }
                }
            }
        }
    }

    //for export
    function getTypeTitles($typeid){
        $g = $this->system->loadModel('goods/gtype');
        $gtype = $g->getTypeObj($typeid,$name);
        $return = array();
        foreach($gtype['props'] as $k=>$v){
            $return['p_'.$k] = 'props:'.$gtype['props'][$k]['name'];
        }
        foreach($gtype['params'] as $k=>$group){
            if($group['groupitems']&&is_array($group['groupitems'])){
                foreach($group['groupitems'] as $k1=>$v1){
                    $return['a_'.$group['groupname'].'->'.$v1['itemname']] = 'params:'.$group['groupname'].'->'.$v1['itemname'];
                }
            }
        }
        return $return;
    }

    function getMarketGoods($type){
        $aRet = $this->db->selectRow('SELECT count(goods_id) as goodscount FROM sdb_goods where marketable="'.$type.'" and goods_type="normal" and disabled="false" ');
        return $aRet['goodscount'];
    }


    function getColAlias($col,$props,$params){
        $tag = explode(':',$col,2);
        switch($tag[0]){
            case 'props':
                foreach($props as $k=>$v){
                    if($props[$k]['name']==$tag[1]){    //一定要是名称，不能是别名
                        return explode('|',$props[$k]['alias']);
                    }
                }
                break;
            case 'params':
                foreach($params as $group){
                    foreach($group as $k => $alias){
                        if($k==$tag[1]) return explode('|',$alias);
                    }
                }
                break;
        }
        return array();
    }

    function getSevenDaysSales(){


    }
    //for export
    function getColTitles($cols=''){
        $cols = explode(',',$cols);
        $columns = $this->getColumns();
        $return = array();
        foreach($columns as $k=>$v){
            if(in_array($k,$cols)){
                $return[$k] = 'col:'.$v['label'];
            }
        }
        return $return;
    }

    //for export
    function getPriceTitles(){
        $l = $this->system->loadModel('member/level');
        $level = $l->getMLevel();
        $return = array();
        foreach($level as $k=>$v){
            $return['m_'.$v['member_lv_id']] = 'price:'.$v['name'];
        }
        return $return;
    }

    //返回商品导出数组  其中 参数表的key为 a_组名|||参数名
    function getGoodsExportData($v,$proto,$t_name,$props,$params){
        $goods = $this->system->loadModel('trading/goods');
        $objCat = $this->system->loadModel('goods/productCat');
        $row[0] = $proto;
        $v['t_name'] = $t_name;
        $params = $v['params']?unserialize($v['params']):array();
        if($v['pdt_desc']&&count(unserialize($v['pdt_desc']))){  //如果是多货品商品
            //ever: 2009-02-16
            if($v['spec_desc'] = unserialize($v['spec_desc'])){
                foreach($v['spec_desc'] as $spec_id => $spec_value){
                    $aTmp[] = $spec_id;
                }
                $aSpec = $v['spec_desc'];
                if($aTmp){
                    $objSpec = $this->system->loadModel('goods/specification');
                    $v['spec'] = implode('|',$objSpec->getArrayById($aTmp));
                }else{
                    //如果存在商品的规格被删除
                }
            }else{
                $v['spec'] = implode('|',unserialize($v['spec']));
            }
            $member_price = false;
        }
        else{
            $aTmp = $goods->getMemberPrice($v['goods_id']);
            $member_price = $aTmp['mprice'];
            $v['spec'] = '-';
        }

        foreach($proto as $k1=>$v1){
            $tag = substr($k1,0,2);
            switch($tag){
            case 'm_':  //处理会员价
                if($member_price){
                    $temp = explode('_',$k1);
                    $row[0][$k1] = $member_price[$temp[1]];
                }else $row[0][$k1] = '-';
                break;
            case 'p_':  //处理类型
                $temp = explode('_',$k1);
                $t_prop = $props[$temp[1]];
                if($t_prop['type']=='select'){
                    $row[0][$k1] = $t_prop['options'][$v[$k1]];
                }else{
                    $row[0][$k1] = $v[$k1];
                }
                break;
            case 'a_':  //处理参数表
                $temp = explode('_',$k1,2);
                $p_keys = explode('->',$temp[1]);
                $row[0][$k1] = $params[$p_keys[0]][$p_keys[1]];
                break;
            default:
                if($k1 == 'cat_id'){
                    $row[0][$k1] = $objCat->getNamePathById($v[$k1]);
                }elseif($k1 == 'marketable'){
                    if($v[$k1] == 'true'){
                        $row[0][$k1] = 'Y';
                    }else{
                        $row[0][$k1] = 'N';
                    }
                }else{
                    $row[0][$k1] = $v[$k1];
                }
            }
        }
        $gimage = $this->system->loadModel('goods/gimage');
        $aImg = $gimage->get_by_goods_id($v['goods_id']);
        foreach($aImg as $k => $item){
            if($item['is_remote'] == 'true'){
                $item['source'] = $item['small'];
            }
            $aImgFile[$item['gimage_id']] = $item['gimage_id'].'@'.$item['source'];
            if($item['thumbnail'] == $v['thumbnail_pic']){
                $v['thumbnail_pic'] = $item['gimage_id'].'@'.$item['source'];
            }
        }
        $row[0]['image_file'] = implode('#', $aImgFile);

        if($v['pdt_desc'] = unserialize($v['pdt_desc'])){    //如果是多物品商品
            $s = array_keys($s['pdt_desc']);
            foreach($goods->getProducts($v['goods_id']) as $product){
                if(trim($product['bn'])=='') continue;
                $pdt_line = $proto;
                $pdt_line['t_name'] = $v['t_name'];
                $pdt_line['bn'] = $v['bn'];
                $pdt_line['i_bn'] = $product['bn'];
                $product['props'] = unserialize($product['props']);
                //ever: 2009-02-16
                if($product['props']['spec_private_value_id']){
                    foreach($product['props']['spec_private_value_id'] as $k => $valid){
                        if($aSpec[$k][$valid]['spec_value_id']){
                            $aTmp = $objSpec->getValueById($aSpec[$k][$valid]['spec_value_id'], array('spec_value'));
                            if($aSpec[$k][$valid]['spec_value'] !== $aTmp['spec_value']){
                                $product['props']['spec_private_value_id'][$k] = trim($aTmp['spec_value']).':'.$aSpec[$k][$valid]['spec_value'];
                            }else{
                                $product['props']['spec_private_value_id'][$k] = trim($aTmp['spec_value']);
                            }
                        }
                    }
                    $pdt_line['spec'] = implode('|',$product['props']['spec_private_value_id']);
                }else{
                    if($product['props']['spec']&&count($product['props']['spec'])){
                        foreach($product['props']['spec'] as $k => $specValue){
                            $product['props']['spec'][$k] = trim($specValue);
                        }
                        $pdt_line['spec'] = implode('|',$product['props']['spec']);
                    }else{
                        $pdt_line['spec'] = '-';
                    }
                }
                $aTmp = $goods->getMemberPrice($v['goods_id'], $product['product_id']);
                $product['mprice'] = $aTmp['mprice'];
                foreach($product['mprice'] as $level=>$price){
                    $pdt_line['m_'.$level] = $price;
                }
                $pdt_line['price'] = $product['price'];
                $pdt_line['cost'] = $product['cost'];
                $pdt_line['store'] = $product['store'];
                $pdt_line['weight'] = $product['weight'];
                if($levelid && isset($product['mprice'][$levelid])){
                    $product['price'] = $product['mprice'][$levelid];
                }
                $row[] = $pdt_line;
            }
        }
        return $row;
    }

    function getTypeExportTitle(&$gtype){
        $id_title = array('t_name'=>'','bn'=>'bn:商品货号','i_bn'=>'ibn:规格货号');
        $g = $this->system->loadModel('goods/gtype');
        $id_title['t_name'] = '*:'.$gtype['name'];
        $col_title1 = $this->getColTitles('name,cat_id,marketable,brand,spec,mktprice,cost,price');
        $mp_title = $this->getPriceTitles();
        $col_title2 = $this->getColTitles('store,weight,unit,brief,intro,thumbnail_pic,image_file');
        if($gtype['type_id']){
            $type_title = $this->getTypeTitles($gtype['type_id']);
        }
        return array_merge($id_title,$col_title1,$mp_title,$col_title2,$type_title);
    }

    function checkImportData($aData=array(), $aFile=array()){
        if($aData['type']=='csv'){
            if(substr($aFile['upload']['name'],-4)!='.csv'){
                trigger_error(__('文件格式有误'),E_USER_ERROR);
                exit;
            }
            $content = file_get_contents($aFile['upload']['tmp_name']);
            if(substr($content,0,3)=="\xEF\xBB\xBF"){
                $content = substr($content,3);    //去BOM头
                $handle = fopen($aFile['upload']['tmp_name'],'wb');
                fwrite($handle,$content);
                fclose($handle);
            }
            $handle = fopen($aFile['upload']['tmp_name'],'r');
        }elseif(substr($aData['type'],0,4)=='site'){
            $handle['url'] = $aData['url'];
            $handle['count'] = 0;
        }

        $dataio = $this->system->loadModel('system/dataio');
        $g = $this->system->loadModel('goods/gtype');
        while($data = $dataio->import_row($aData['type'],$handle)){
            $goMark = true;
            foreach($data as $v){
                if(trim($v)){
                    $goMark = false;
                    break;
                }
            }
            if($goMark){
                continue;
            }
            if($data[0]{0}=='*'){    //检测类型定义行
                $type_name = explode(':',$data[0],2);
                if($gtype = $g->getTypebyAlias('*',$type_name[1])){    //if exist goods type for $type_name[1]
                    $type_valid = true;
                    $type_id = $gtype['type_id'];
                    $gtype['props'] = unserialize($gtype['props']);
                    $gtype['params'] = unserialize($gtype['params']);
                    $title_array = $this->getTypeExportTitle($gtype);
                    $title_array_flip = array_flip($title_array);
                    unset($proto);
                    unset($rel);
                    $proto['type_id'] = $type_id;

                    //进行数据赋值
                    foreach($data as $k=>$v){
                        //echo $v.'%'.$title_array_flip[$v].'|';
                        if(strstr($v,'props:') && !$title_array_flip[$v]){
                            trigger_error('商品类型“'.$gtype['name'].'”中的“'.$v.'”属性并不存在！',E_USER_ERROR);
                            exit;
                        }
                        if(strstr($v,'params:') && !$title_array_flip[$v]){
                            trigger_error('商品类型“'.$gtype['name'].'”中的“'.$v.'”参数并不存在！',E_USER_ERROR);
                            exit;
                        }
                        if($v!=''&&$title_array_flip[$v]){
                            $proto[$title_array_flip[$v]] = &$rel[$k] ;
                        }
                    }
                }else{
                    $type_valid = false;
                    $this->csvLog('warning','商品类型“'.$type_name[1].'”在商店中并不存在，该类型下的商品数据不能导入！');
                }
                continue;
            }
            //开始检测商品数据行，前提是：必须有商品类型，商品数据才可以继续读取
            if($type_valid && $type_id){
                foreach($data as $k=>$v){
                    $rel[$k] = trim($v);
                }

                //含有物品记录时，必须要该物品对应的商品记录
                if($proto['i_bn']==''){
                    unset($proto['goods_pdt']);
                    unset($proto['goods_spec']);
                    if($last_g_bn){
                        $this->writeData();
                    }
                    $last_g_bn = $this->importGoods($proto,$gtype);
                    $proto['do_goods'] = false;
                    //判断是否是单货品商品
                    if($proto['spec']=='-' || $proto['spec']=='' || (is_array($proto['spec'])&&count($proto['spec'])==0)){
                        $proto['i_bn'] = $proto['bn'];
                        $this->importProduct($proto);
                    }else{
                        $proto['goods_spec'] = $proto['spec'];  //商品规格数组array(spec_id=>spec_name)
                    }
                }else{
                    $this->importProduct($proto, true);
                }
                //Ever: 记录商品标识，当到达下一个商品时，将前一个商品的spec值（根据他下面的货品）补充完整
            }
            $iLoop++;

            usleep(20);
        }

        if($last_g_bn){
            $this->writeData();
        }

        return true;
    }

    function writeData(){
        $objSpec = $this->system->loadModel('goods/specification');
        //todo：插入商品行
        $aGoodsData = file(HOME_DIR.'/tmp/uploadGoodsCsvTmp');
        unlink (HOME_DIR.'/tmp/uploadGoodsCsvTmp');

        $this->globalTmp['tmp']['g'] = unserialize($aGoodsData[1]);
        $this->globalTmp['tmp']['p'] = array();
        foreach($aGoodsData as $k => $p_data){
            if($k > 1) $this->globalTmp['tmp']['p'][] = unserialize($p_data);
        }

        if($this->globalTmp['tmp']['g']){
            $aGoods = $this->globalTmp['tmp']['g'];
            $aGoods['spec'];    //todo: 按照这个数组（规格）的顺序将规格值依次附加进去
            foreach($this->globalTmp['tmp']['p'] as $data){
                $i = 0;
                foreach($data['spec'] as $v){
                    $aTmp[$i][] = $v;   //key 是1，2，3，4 和货品数组的key相同
                    $i++;
                }
                //todo：插入货品行
            }
            $i = 0;
            $aNewSpec = array();
            foreach($aGoods['spec_desc'] as $spec_id => $item){     //遍历规格
                if(is_array($item)){   //如果商品原来已经存在规格
                    $aNewSpec[$spec_id] = $item;
                    foreach($aTmp[$i] as $k => $v){      //红色：玫瑰红|黄色：土黄
                        if(strstr($v, ":")){
                            $aDef = explode(':', $v);
                            $true_name = $aDef[0];
                            $alias_name = $aDef[1];
                        }else{
                            $true_name = $v;
                            $alias_name = $v;
                        }
                        $v_id = $objSpec->getValueidByName($spec_id, $true_name) + 0;
                        $tmp_mark = true;
                        foreach($item as $s => $spec_value){  //是否存在商品规格组当中
                            if($spec_value['spec_value_id'] == $v_id && $spec_value['spec_value'] == $alias_name){
                                if($aExist[$v_id][$alias_name]){
                                    $uniqid = $aExist[$v_id][$alias_name];
                                }else{
                                    $uniqid = strtoupper(uniqid('spec'));
                                    $aNewSpec[$spec_id][$uniqid] = $spec_value;
                                    $aNewSpec[$spec_id][$uniqid]['sign'] = 'Y';    //标识新的，否则删除
                                }
                                $this->globalTmp['tmp']['p'][$k]['props']['spec'][$spec_id] = $true_name;
                                $this->globalTmp['tmp']['p'][$k]['props']['spec_value_id'][$spec_id] = $v_id;
                                $this->globalTmp['tmp']['p'][$k]['props']['spec_private_value_id'][$spec_id] = $uniqid;
                                $tmp_mark = false;
                                $aExist[$v_id][$alias_name] = $uniqid;
                                break;
                            }
                        }
                        if($tmp_mark){
                            if($aExist[$v_id][$alias_name]){
                                $m = $aExist[$v_id][$alias_name];
                            }else{
                                $m = strtoupper(uniqid('spec'));
                                $aNewSpec[$spec_id][$m]['spec_value_id'] = $v_id;
                                $aNewSpec[$spec_id][$m]['spec_value'] = $alias_name;
                                $aNewSpec[$spec_id][$m]['sign'] = 'Y';    //标识新的，否则删除
                                $aExist[$v_id][$alias_name] = $m;
                            }
                            $this->globalTmp['tmp']['p'][$k]['props']['spec'][$spec_id] = $true_name;
                            $this->globalTmp['tmp']['p'][$k]['props']['spec_value_id'][$spec_id] = $v_id;
                            $this->globalTmp['tmp']['p'][$k]['props']['spec_private_value_id'][$spec_id] = $m;
                        }
                    }
                }else{
                    $aExist = array();
                    foreach($aTmp[$i] as $k => $v){      //红色：玫瑰红|黄色：土黄
                        if(strstr($v, ":")){
                            $aDef = explode(':', $v);
                            $true_name = $aDef[0];
                            $alias_name = $aDef[1];
                        }else{
                            $true_name = $v;
                            $alias_name = $v;
                        }
                        $v_id = $objSpec->getValueidByName($spec_id, $true_name);
                        if($aExist[$v_id][$alias_name]){
                            $uniqid = $aExist[$v_id][$alias_name];
                        }else{
                            $uniqid = strtoupper(uniqid('spec'));
                            $aExist[$v_id][$alias_name] = $uniqid;
                            $aNewSpec[$spec_id][$uniqid]['spec_value_id'] = $v_id;
                            $aNewSpec[$spec_id][$uniqid]['spec_value'] = $alias_name;
                            $aNewSpec[$spec_id][$uniqid]['sign'] = 'Y';    //标识
                        }
                        $this->globalTmp['tmp']['p'][$k]['props']['spec'][$spec_id] = $true_name;
                        $this->globalTmp['tmp']['p'][$k]['props']['spec_value_id'][$spec_id] = $v_id;
                        $this->globalTmp['tmp']['p'][$k]['props']['spec_private_value_id'][$spec_id] = $uniqid;
                    }
                }
                $i++;
            }

            foreach($aNewSpec as $spec_id => $spec_item){
                foreach($spec_item as $k => $spec_value){
                    if($spec_value['sign']){
                        unset($aNewSpec[$spec_id][$k]['sign']);
                    }else{
                        unset($aNewSpec[$spec_id][$k]);
                    }
                }
            }
            $aGoods['spec_desc'] = $aNewSpec;

            $this->csvLog('data',array('name'=>'goods','content'=>$aGoods));
            foreach($this->globalTmp['tmp']['p'] as $data){
                $this->csvLog('data',array('name'=>'product','content'=>$data));
            }
            unset($this->globalTmp['tmp']);
        }
    }

    function importGoods(&$proto,$gtype){
        if(empty($proto['name'])){
            trigger_error('编号为“'.$proto['bn'].'”的商品没有名称！',E_USER_ERROR);
            exit;
        }
        if(in_array($proto['bn'], $this->globalTmp['g'])){
            trigger_error('商品“'.$proto['name'].'”的编号在文件中重复！',E_USER_ERROR);
            exit;
        }else{
            $member_price = array();
            $t_params = array();
            $brand = $this->system->loadModel('goods/brand');
            $cat = $this->system->loadModel('goods/productCat');
            //参数表对应
            $params = $gtype['params'];
            $props = $gtype['props'];
            foreach($proto as $k=>$v){
                $tag = substr($k,0,2);
                switch($tag){
                case 'p_':    //属性处理,属性在CSV中的属性可以颠倒或者跳过
                    $temp = explode('_',$k);
                    if($props[$temp[1]]['type']=='select'){
                        $interp = array_flip($props[$temp[1]]['options']);
                        $alias = $props[$temp[1]]['optionAlias'];
                        foreach($alias as $k1=>$v1){
                            if(!empty($v1)){
                                $the_alias = explode('|',$v1);
                                foreach($the_alias as $v2){
                                    $interp[$v2] = $k1;
                                }
                            }
                        }
                        if(array_key_exists($v, $interp)){
                            $proto[$k] = $interp[$v];
                        }else{
                            if($v){
                                trigger_error('商品“'.$proto['name'].'”中的属性值“'.$v.'”并不存在！',E_USER_ERROR);
                                exit;
                            }
                        }
                    }
                    break;
                case 'a_':   //参数处理
                    $temp = explode('_',$k);
                    $t_params = explode('->',$temp[1]);
                    $params[$t_params[0]][$t_params[1]] = $v;
                    unset($proto[$k]);
                    break;
                default:
                    if($k=='brand'){
                        if(!$v){
                            $proto['brand_id'] = 0;
                            $proto['brand'] = '';
                        }elseif($b = $brand->getBrandbyAlias('brand_id',trim($v))){
                            $proto['brand_id'] = $b['brand_id'];
                            $proto['brand'] = $v;
                        }else{
                            $proto['brand_id'] = 0;
                            $this->csvLog('warning','品牌错误');
                        }
                    }
                    if($k=='cat_id'){
                        if($catid = $cat->getCatidbyAlias($v)){
                            $proto['cat_id'] = $catid;
                        }else{
                            $proto['cat_id'] = 0;
                            $this->csvLog('warning','商品“'.$proto['name'].'”的分类不存在，导入后的商品分类为空');
                        }
                    }
                    if($k=='marketable'){
                        if($v == 'Y' || $v == 'y'){
                            $proto['marketable'] = 'true';
                        }else{
                            $proto['marketable'] = 'false';
                        }
                    }
                }
            }
            $proto['params'] = $params;
            $proto['goods_name'] = $proto['name'];
            $this->checkGoodsSpec($proto);
        }

        $this->globalTmp['g'][] = $proto['bn'];
        $this->csvLog('tmp',$proto);
        return $proto['bn'];
    }

    function checkGoodsSpec(&$proto){
        if($list = $this->getList('goods_id,spec,pdt_desc,spec_desc',array('bn'=>$proto['bn']))){    //编辑商品
            $proto['goods_id'] = $list[0]['goods_id'];
            $aSpec = array();
            if($aEditSpec = unserialize($list[0]['spec_desc'])){
                foreach($aEditSpec as $spec_id => $items){
                    if($items){
                        $aTmpid[] = $spec_id;
                        $data_spec[$spec_id] = $items;
                    }
                }
                $objSpec = $this->system->loadModel('goods/specification');
                $aRet = $objSpec->getArrayById($aTmpid);
                foreach($aTmpid as $specid){    //读取最新的规格
                    foreach($aRet as $id => $v){
                        if($id == $specid){
                            $aSpec[$id] = $v;
                            break;
                        }
                    }
                }
            }else{
                $aSpec = unserialize($list[0]['spec']);
                $data_spec = $this->get_arr_specid_by_name($aSpec);
            }

            if($proto['spec'] == '-' || (is_array($proto['spec']) && count($proto['spec']) == 0)){
                $proto['spec']='';
                $proto['spec_desc']='';
            }
            if(implode('|',$aSpec) != $proto['spec']){    //比较规格项是否一致
                trigger_error('商品“'.$proto['name'].'”的规格跟原来的不一致！',E_USER_ERROR);
                exit;
            }else{
                if($proto['spec']){
                    $objPdt = $this->system->loadModel('goods/finderPdt');
                    if($ap = $objPdt->getList('bn,pdt_desc',array('goods_id'=>$proto['goods_id']),0,-1)){
                        foreach($ap as $row){
                            $proto['goods_pdt'][$row['bn']] = $row['pdt_desc'];    //商品中含有的物品
                        }
                    }
                    $proto['spec'] = $aSpec;
                    $proto['spec_desc'] = $data_spec;
                }
            }
        }else{        //新商品
            $proto['goods_id'] = 0;
            if($proto['spec']!='-' && $proto['spec']){
                $aSpec = explode('|',$proto['spec']);
                $aSpec = $this->get_arr_specid_by_name($aSpec);
                $proto['spec'] = $aSpec;
                foreach($aSpec as $k=>$v){
                    $proto['spec_desc'][$k] = $v;
                    if(!trim($v)){
//                        $proto['spec'][$k+1] = trim($v);
//                    }else{
                        trigger_error('商品“'.$proto['name'].'”的规格格式不正确！',E_USER_ERROR);
                        exit;
                    }
                }
            }
        }
    }

    function get_arr_specid_by_name($data){
        $objSpec = $this->system->loadModel('goods/specification');
        $aSpec = $objSpec->getSpecidListByName($data);
        foreach($data as $v){
            foreach($aSpec as $rows){
                if($rows['spec_name'] == $v){
                    $aTmp[$rows['spec_id']] = $rows['spec_name'];
                    break;
                }
            }
        }
        return $aTmp;
    }

    //$isSpec是否多规格商品；false否 true=是
    function importProduct($proto, $isSpec=false){
        if(empty($proto['goods_name'])){
            trigger_error('规格货品“'.$proto['i_bn'].'”没有所属的商品存在！',E_USER_ERROR);
            exit;
        }
        $proto['name'] = $proto['name']?$proto['name']:$proto['goods_name'];
        $proto['bn'] = $proto['i_bn'];
        if(in_array($proto['bn'], $this->globalTmp['p'])){
            trigger_error('商品“'.$proto['goods_name'].'”中的货品编号“'.$proto['bn'].'”在文件中重复！',E_USER_ERROR);
            exit;
        }else{
            if($isSpec){
                $sSpec = $proto['spec'];    //规格值：白色:银白色|38:中码
                $proto['spec'] = array();
                $aSpec = explode('|',$sSpec);
                //ever: 2009-02-16
                if(count($aSpec) == count($proto['goods_spec'])){
                    $i = 0;
                    foreach($proto['goods_spec'] as $spec_id => $v){
                        if(trim($aSpec[$i])){
                            $proto['spec'][$spec_id] = trim($aSpec[$i]);
                        }else{
                            trigger_error('商品“'.$proto['goods_name'].'”中的规格值“'.$sSpec.'”不能为空！',E_USER_ERROR);
                            exit;
                        }
                        $i++;
                    }
                }else{
                    trigger_error('商品“'.$proto['goods_name'].'”中的规格值“'.$sSpec.'”跟规格项不一致！',E_USER_ERROR);
                    exit;
                }
                $ap = $this->db->selectrow('SELECT count(*) AS num FROM sdb_products WHERE bn= \''.$proto['bn'].'\'');
                if(($ap['num'] > 0 && !isset($proto['goods_pdt'][$proto['bn']])) || $ap['num'] > 1){
                    trigger_error('商品“'.$proto['goods_name'].'”中的货品编号“'.$proto['bn'].'”在数据库中已存在！',E_USER_ERROR);
                    exit;
                }
                $pdtDesc = implode(' ',$proto['spec']);
                if(in_array($pdtDesc, $proto['goods_pdt']) && $proto['bn'] != array_search($pdtDesc, $proto['goods_pdt'])){
                    trigger_error('商品“'.$proto['goods_name'].'”中的规格值“'.$pdtDesc.'”重复！',E_USER_ERROR);
                    exit;
                }
                $proto['goods_pdt'][$proto['bn']] = $pdtDesc;
                $proto['pdt_desc'] = $pdtDesc;
//                $proto['props']['spec'] = $proto['spec'];
            }else{
                if($proto['do_goods']){
                    trigger_error('商品“'.$proto['goods_name'].'”不应该存在货品！',E_USER_ERROR);
                    exit;
                }else{
                    $proto['do_goods'] = true;
                }
            }
        }
        $this->globalTmp['p'][] = $proto['bn'];
        $this->csvLog('tmp',$proto);
    }

    function insertCsvData(){
        $handle = @fopen(HOME_DIR.'/tmp/uploadGoodsCsvData', "r");
        if($handle){
            while (!feof($handle)) {
                $buffer = fgets($handle, 32768);
                $aData = unserialize($buffer);
                if($aData['name'] == 'goods'){
                    if($goodsMark && $aPdtDesc){
                        $sql = 'UPDATE sdb_goods SET store = '.$aPdtDesc['store'].',pdt_desc=\''.serialize($aPdtDesc['pdt_desc'])
                                .'\' WHERE goods_id='.$goodsId;
                        $this->db->exec($sql);
                        $aPdtDesc = array();
                    }
                    if(!($goodsId = $this->importGoodsLine($aData['content'])) && $aData['content']['goods_id']){
                        $goodsId = $aData['content']['goods_id'];
                    }
                    $goodsMark = true;
                }
                if($aData['name'] == 'product'){
                    $aData['content']['goods_id'] = $goodsId;
                    $this->importProductLine($aData['content'], $aPdtDesc);
                }
            }

            if($goodsId && $aPdtDesc){
                $sql = 'UPDATE sdb_goods SET store = '.$aPdtDesc['store'].',pdt_desc=\''.serialize($aPdtDesc['pdt_desc'])
                            .'\' WHERE goods_id='.$goodsId;
                $this->db->exec($sql);
            }
            fclose($handle);
        }
        @unlink(HOME_DIR.'/tmp/uploadGoodsCsvData');
    }

    function csvLog($errType, $aData){
        switch($errType){
            case 'error':
            $fp = fopen(HOME_DIR.'/tmp/uploadGoodsCsvError','a');
            $out = $aData;
            fwrite($fp,$out);
            fclose($fp);
            break;
            case 'warning':
            $fp = fopen(HOME_DIR.'/tmp/uploadGoodsCsvWarning','a');
            $out = $aData;
            fwrite($fp,$out);
            fclose($fp);
            break;
            case 'data':
            $fp = fopen(HOME_DIR.'/tmp/uploadGoodsCsvData','a');
            $out = "\n".serialize($aData);
            fwrite($fp,$out);
            fclose($fp);
            break;
            case 'tmp':
            $fp = fopen(HOME_DIR.'/tmp/uploadGoodsCsvTmp','a');
            $out = "\n".serialize($aData);
            fwrite($fp,$out);
            fclose($fp);
            break;
        }
    }

    function importGoodsLine(&$aData){
        $aData['intro'] = str_replace('\n',"\n",$aData['intro']);
        $aData['intro'] = addslashes($aData['intro']);
        $aData['brief'] = str_replace('\n',"\n",$aData['brief']);
        $aData['brief'] = addslashes($aData['brief']);
        $aData['name'] = addslashes($aData['name']);
        $aData['last_modify'] = time();
        $aData['cost'] += 0;

        if($aData['goods_id']){    //编辑商品
            $rs = $this->db->query('SELECT * FROM sdb_goods WHERE goods_id='.$aData['goods_id']);
            $sql = $this->db->GetUpdateSQL($rs, $aData);
            if($sql && !$this->db->exec($sql)){
                trigger_error('SQL Error:'.$sql,E_USER_NOTICE);
                return false;
            }
        }else{    //新增商品
            $aData['cat_id'] = intval($aData['cat_id']);
            if(!$aData['price'])$aData['price'] = 0;
            $aData['uptime'] = time();
            unset($aData['goods_id']);
            $rs = $this->db->query('SELECT * FROM sdb_goods WHERE 0=1');
            $sql = $this->db->GetInsertSQL($rs, $aData);
            if($sql && !$this->db->exec($sql)){
                trigger_error('SQL Error:'.$sql,E_USER_NOTICE);
                return false;
            }
            $aData['goods_id'] = $this->db->lastInsertId();
            $aData['p_order'] = $aData['goods_id'];
            $rs = $this->db->query('SELECT * FROM sdb_goods WHERE goods_id='.$aData['goods_id']);
            $sql = $this->db->GetUpdateSQL($rs, $aData);
            if($sql && !$this->db->exec($sql)){
                trigger_error('SQL Error:'.$sql,E_USER_NOTICE);
                return false;
            }
            $status = $this->system->loadModel('system/status');
            $status->add('GOODS_ADD');
        }

        //图片处理
        if($aData['image_file'] || $aData['thumbnail_pic']){
            $image_change = false;
            if($aData['image_file']){
                $images = explode('#',$aData['image_file']);
                $images = array_unique($images);
								$this->sortByPre(&$images);
            }else{
                $images = array();
                $aData['image_default'] = 0;
            }
            $image_file = array();
            $gimage = $this->system->loadModel('goods/gimage');
            if(is_array($images)&&count($images)>0){
                $storager = $this->system->loadModel('system/storager');
                $aData['udfimg'] = in_array($aData['thumbnail_pic'], $images)?'false':'true';   //如果小图不存在图片地址中，为自定义

                $i = 0;
                foreach($images as $k=>$image){
                    if(!$image){
                        continue;
                    }
                    //如果没有@字符的说明是本地上传图片
                    $gimage_id = null;
                    if(strstr($image,'@')){
                        $aTmp = explode('@', $image);
                        $gimage_id = $aTmp[0];
                        if(!$gimage_id){
                            $gimage_id = $gimage->get_img_by_source($image, 'gimage_id');
                        }
                    }elseif(substr($image,0,4)=='http'){
                        $gimage_id = $gimage->insert_new(array(
                            'is_remote'=>'true',
                            'source'=>'N',
                            'src_size_width'=>100,
                            'src_size_height'=>100,
                            'big'=>$image,
                            'small'=>$image,
                            'thumbnail'=>$image,
                            'up_time'=>time()
                            ),$aData['goods_id']);
                    }elseif(file_exists(HOME_DIR.'/upload/'.$image)){
                        $pic['tmp_name'] = HOME_DIR.'/upload/'.$image;
                        $pic['goods_id'] = $aData['goods_id'];
                        $aImg = $gimage->save_upload($pic);
                        $gimage_id = $aImg['gimage_id'];
                    }
                    $image_file[] = $gimage_id;
                    if($i == 0){    //默认图为第一张图片
                        $aData['image_default'] = $gimage_id;
                        $i++;
                    }
                }
            }
            if(substr($aData['thumbnail_pic'],0,4)!='http' && file_exists(HOME_DIR.'/upload/'.$aData['thumbnail_pic'])){
                $thumbnail_pic['goods_thumbnail_pic']['name'] = HOME_DIR.'/upload/'.$aData['thumbnail_pic'];
                $thumbnail_pic['goods_thumbnail_pic']['img_source'] = 'local';
                $image_change = true;
            }else{
                if(count($images) == 0 && substr($aData['thumbnail_pic'],0,4)=='http'){
                    $aData['udfimg'] = 'true';
                }
                if(substr($aData['thumbnail_pic'],0,4)=='http'){
                    $thumbnail_pic = $aData['thumbnail_pic'];
                }else{
                    $thumbnail_pic = array();
                }
            }
            $gimage->saveImage($aData['goods_id'], '', $aData['image_default'], $image_file, $aData['udfimg'], $thumbnail_pic);
        }
        return $aData['goods_id'];
    }


		function sortByPre($images){
			$flag = array();
			foreach($images as $k=>$v){
				$pre = substr($v,0,strpos($v,'.'));
				$flag[$k] = $pre;
			}
			asort($flag);
			$aTmp = array();
			foreach($flag as $k=>$v){
				$aTmp[] = $images[$k];
			}

			$images = $aTmp;

		}
    //导入货品行
    function importProductLine(&$aData, &$aPdtDesc){
        if($list = $this->db->selectrow('select product_id from sdb_products where bn=\''.$aData['bn'].'\'')){
            $aData['product_id'] = $list['product_id'];
        }
        $aData['last_modify'] = time();
        if(!$aData['price'])$aData['price'] = 0;
        if($aData['product_id']){
            $rs = $this->db->query('SELECT * FROM sdb_products WHERE product_id='.$aData['product_id']);
            $sql = $this->db->GetUpdateSQL($rs, $aData);
            if($sql && !$this->db->exec($sql)){
                trigger_error('SQL Error:'.$sql,E_USER_NOTICE);
                return false;
            }
        }else{
            $aData['uptime'] = time();
            $rs = $this->db->query('SELECT * FROM sdb_products WHERE 0=1');
            $sql = $this->db->GetInsertSQL($rs, $aData);
            if($sql && !$this->db->exec($sql)){
                trigger_error('SQL Error:'.$sql,E_USER_NOTICE);
                return false;
            }
            $aData['product_id'] = $this->db->lastInsertId();
        }
        //非单货品商品，则处理商品表货品定义列
        if($aData['pdt_desc']){
            $aPdtDesc['pdt_desc'][$aData['product_id']] = $aData['pdt_desc'];
            $aPdtDesc['store'] += $aData['store'];
        }
        //处理会员价
        $mprice[0]['goods_id'] = $aData['goods_id'];
        $mprice[0]['product_id'] = $aData['product_id'];
        $mprice[0]['price'] = array();
        foreach($aData as $k=>$v){
            if(substr($k,0,2)=='m_'){
                $mprice[0]['price'][intval(substr($k,2))] = $v;
            }
        }
        $goods = $this->system->loadModel('trading/goods');

        $goods->addMemberPrice($mprice);
    }

    function makeGoodsObj($goods,$products,$goodstype){
        $proto = array();
        $props = unserialize($goodstype['props']);
        $params = unserialize($goodstype['params']);
        $proto['supplier_goods_id'] = $goods['goods_id'];
        $proto['brand_id'] = $goods['brand_id'];
        $proto['brand_name'] = $goods['brand'];
        $proto['type_id'] = $goods['type_id'];
        $proto['type_name'] = $goodstype['name'];
        $proto['supplier_bn'] = $goodstype['bn'];
        $proto['goods_name'] = $goods['name'];
        $proto['weight'] = $goods['weight'];
        $proto['unit'] = $goods['unit'];
        $proto['intro'] = $goods['intro'];
        $proto['brief'] = $goods['brief'];
        $proto['mktprice'] = $goods['mktprice'];
        $proto['price'] = $goods['price'];
        $proto['store'] = $goods['store'];
        $proto['image_file'] = $goods['image_file'];
        $proto['last_modify'] = $goods['last_modify'];
        $proto['params'] = unserialize($goods['params']);
        $proto['spec'] = unserialize($goods['spec']);

        foreach($goods as $k=>$v){
            $tag = substr($k,0,2);
            switch($tag){
                case 'p_':    //属性处理
                    $temp = explode('_',$k);
                    if($props[$temp[1]]['type']=='select'){
                        $proto['props'][$props[$temp[1]]['name']] = $props[$temp[1]]['options'][$v];
                    }elseif($props[$temp[1]]['type']=='input'){
                        $proto['props'][$props[$temp[1]]['name']] = $v;
                    }
            }
        }
        $pdt = array();
        foreach($products as $product){
            $pdt_line = array();
            $pdt_line['product_id'] = $product['product_id'];
            $pdt_line['bn'] = $product['bn'];
            $pdt_line['name'] = $product['name'];
            $pdt_line['price'] = $product['price'];
            $pdt_line['store'] = $product['store'];
            $pdt_line['props'] = $product['props'];
            $pdt[] = $pdt_line;
        }
        $proto['products'] = $pdt;
        return $proto;
    }
    function countGoodsNum(){
        $conunt= $this->db->selectRow('select count("goods_id") as goodsnum from sdb_goods');
        return $conunt['goodsnum'];
    }
    //输出同类型商品的Json
    function getGoodsJson($goods){
        $gtype = $this->system->loadModel('goods/gtype');
        $goodstype = $gtype->instance($goods[0]['type_id']);
        $proto = array();
        foreach($goods as $k=>$goods_line){
            $products = $this->db->select('select * from sdb_products where goods_id='.$goods_line['goods_id']);
            $proto[] = $this->makeGoodsObj($goods_line,$products,$goodstype);
            unset($goods[$k]);
        }
        return json_encode($proto);
    }
    function getProductLevel($productId){
        $oLevel = $this->system->loadModel('member/level');
        $levelItem = $oLevel->getFieldById(intval($this->system->request['member_lv']));
        $priceDisplayType = 0;
        if($levelItem['lv_type'] == 'retail') //零售会员
            $priceDisplayType = $this->system->getConf('site.retail_member_price_display');
        else if($levelItem['lv_type'] == 'wholesale') //批发会员
            $priceDisplayType = $this->system->getConf('site.wholesale_member_price_display');
        else
            return null;

        $sql = 'SELECT member_lv_id, name FROM sdb_member_lv WHERE disabled = "false" ';
        switch ( intval($priceDisplayType) ){
            case 0:
                return null;
                break;
            case 1:
                $sql.=' AND lv_type = "retail" ';
                break;
            case 2:
                $sql.=' AND lv_type = "wholesale" ';
                break;
            case 3:
                break;
        }
        if(intval($priceDisplayType))
            return $this->db->select($sql);
        return null;
    }

    function addSellLog($data){

        $orderData = $this->db->selectrow('SELECT o.member_id, m.uname,o.ship_email FROM sdb_orders o LEFT JOIN sdb_members m ON o.member_id = m.member_id WHERE o.order_id = '.$data['order_id']);
        $orderItem = $this->db->select('SELECT p.price, p.goods_id, i.product_id, p.name,p.pdt_desc, i.nums FROM sdb_order_items i LEFT JOIN sdb_products p ON p.product_id = i.product_id WHERE i.order_id = '.$data['order_id']);
        foreach( $orderItem as $iKey => $iValue ){
            $sql = 'INSERT INTO sdb_sell_logs (member_id,name,price,goods_id,product_id,product_name,pdt_desc,number,createtime) VALUES ( "'.($orderData['member_id']?$orderData['member_id']:0).'", "'.($orderData['uname']?$orderData['uname']:$orderData['ship_email']).'", "'.$iValue['price'].'", "'.$iValue['goods_id'].'", "'.$iValue['product_id'].'", "'.$iValue['name'].'", "'.$iValue['pdt_desc'].'" , "'.$iValue['nums'].'", "'.time().'" )';
            $this->db->exec($sql);
        }
    }

    function getGoodsSellLogList($gid,$page,$limit=20){
        $sql = 'SELECT * FROM sdb_sell_logs WHERE goods_id = '.$gid.' ORDER BY log_id DESC ';
        return $this->db->select_f($sql,$page,$limit);
    }

    function batchUpdateByOperator( $goods_id, $tableName, $updateName , $updateValue, $operator=null , $fromName = null ){
        $sql = '';
        if( $operator == '-' ){
            
            $sql = 'UPDATE '.$tableName.' SET '.$updateName.' = '.( $fromName?$fromName:$updateName ).' '.$operator.' '.$updateValue.' WHERE '.( strstr($tableName, ',')?' a.goods_id = b.goods_id AND a.':'' ).' goods_id IN ('.implode(',',$goods_id).') AND '.$updateName.' > '.( $fromName?$fromName:$updateName ).' '.$operator.' '.$updateValue;
            $this->db->exec($sql);

            $sql = 'UPDATE '.$tableName.' SET '.$updateName.' = 0 WHERE '.( strstr($tableName, ',')?' a.goods_id = b.goods_id AND a.':'' ).' goods_id IN ('.implode(',',$goods_id).') AND '.$updateName.' < '.( $fromName?$fromName:$updateName ).' '.$operator.' '.$updateValue;
            $this->db->exec($sql);

        }else{
            $sql = 'UPDATE '.$tableName.' SET '.$updateName.' = '.( $operator?( $fromName?$fromName:$updateName ).' '.$operator.' '.$updateValue:'"'.$updateValue.'"' ).' WHERE '.( strstr($tableName, ',')?' a.goods_id = b.goods_id AND a.':'' ).' goods_id IN ('.implode(',',$goods_id).') ';
            $this->db->exec($sql);
        }
        return true;
    }

    function batchUpdateMemberPriceByOperator( $goods_id, $updateLvId, $updateValue, $operator=null , $fromName = null ){
        $aallProductId = $this->db->select('SELECT product_id,goods_id FROM sdb_products WHERE goods_id IN ('.implode(',',$goods_id).')');
        $aupdateProductId = $this->db->select('SELECT product_id,goods_id FROM sdb_goods_lv_price WHERE goods_id IN ('.implode(',',$goods_id).') AND level_id = '.$updateLvId);

        $allProductId = array();
        $updateProductId = array();
        foreach( $aallProductId as $allv )
            $allProductId[$allv['product_id']] = $allv['goods_id'];
        foreach( $aupdateProductId as $alluv )
            $updateProductId[$alluv['product_id']] = $alluv['goods_id'];
        unset($aallProductId, $aupdateProductId);
        $insertProductId = array_diff_assoc( $allProductId, $updateProductId);

        if( $operator ){
            if( $updateValue ){
                if( $fromName && is_numeric($fromName) ){        //用会员价修改会员价
                    foreach( $updateProductId as $upProId => $upGoodsId ){
                        $dataRow = $this->db->selectrow('SELECT price FROM sdb_goods_lv_price WHERE level_id = '.$fromName.' AND product_id = '.$upProId.' AND goods_id = '.$upGoodsId);
                        $this->db->exec('UPDATE sdb_goods_lv_price SET price = '.$dataRow['price'].$operator.floatval($updateValue).' WHERE goods_id = '.$upGoodsId.' AND level_id = '.$updateLvId.' AND product_id = '.$upProId);
                    }
                    foreach( $insertProductId as $inProId => $inGoodsId ){
                        $dataRow = $this->db->selectrow('SELECT price FROM sdb_goods_lv_price WHERE level_id = '.$fromName.' AND product_id = '.$inProId.' AND goods_id = '.$inGoodsId);
                        $this->db->exec('INSERT INTO sdb_goods_lv_price ( product_id, level_id, goods_id, price ) VALUES ('.$inProId.', '.$updateLvId.', '.$inGoodsId.', '.$dataRow['price'].$operator.floatval($updateValue).')');
                    }
                }else{          //用市场价、销售价、成本价修改会员价
                    foreach( $updateProductId as $upProId => $upGoodsId ){
                        $dataRow = array();
                        if( $fromName == 'price' )
                            $dataRow = $this->db->selectrow('SELECT '.$fromName.' AS price FROM sdb_products WHERE product_id = '.$upProId);
                        else
                            $dataRow = $this->db->selectrow('SELECT '.$fromName.' AS price FROM sdb_goods WHERE goods_id = '.$upGoodsId);
                        $this->db->exec('UPDATE sdb_goods_lv_price SET price = '.$dataRow['price'].$operator.floatval($updateValue).' WHERE product_id = '.$upProId.' AND goods_id = '.$upGoodsId.' AND level_id = '.$updateLvId);
                    }
                    foreach( $insertProductId as $inProId => $inGoodsId ){
                        $dataRow = array();
                        if( $fromName == 'price' )
                            $dataRow = $this->db->selectrow('SELECT '.$fromName.' AS price FROM sdb_products WHERE product_id = '.$inProId);
                        else
                            $dataRow = $this->db->selectrow('SELECT '.$fromName.' AS price FROM sdb_goods WHERE goods_id = '.$inGoodsId);
                        $this->db->exec('INSERT INTO sdb_goods_lv_price ( product_id, level_id, goods_id, price ) VALUES ('.$inProId.', '.$updateLvId.', '.$inGoodsId.', '.$dataRow['price'].$operator.floatval($updateValue).')');
                     }
                }
            }

        }else{
             if( $updateValue != null && $updateValue !='' ){
                foreach( $updateProductId as $upProId => $upGoodsId ){
                    $this->db->exec( 'UPDATE sdb_goods_lv_price SET price = '.$updateValue.' WHERE goods_id = '.$upGoodsId.' AND level_id = '.$updateLvId.' AND product_id = '.$upProId );
                }
                foreach( $insertProductId as $inProId => $inGoodsId ){
                    $this->db->exec( 'INSERT INTO sdb_goods_lv_price ( product_id, level_id, goods_id, price ) VALUES ('.$inProId.', '.$updateLvId.', '.$inGoodsId.', '.$updateValue.')') ;
                }
             }else{
                $this->db->exec('DELETE FROM sdb_goods_lv_price WHERE goods_id IN ( '.implode(',',$goods_id).' ) AND level_id = '.$updateLvId);
             }
        }
        return true;
    }

    function synchronizationStore($goods_id){
        $storeSum = $this->db->select('SELECT goods_id, sum(store) as storesum FROM sdb_products WHERE goods_id in ('.implode(',',$goods_id).') GROUP BY goods_id');
        foreach($storeSum as $v){
            $this->db->exec('UPDATE sdb_goods SET store = '.$v['storesum'].' WHERE goods_id = '.$v['goods_id']);
        }
        return true;
    }

    function batchUpdateText( $goods_id, $updateType , $updateName , $updateValue ){
        $sql = 'UPDATE sdb_goods SET ';
        switch($updateType){
            case 'name':
                $sql .= $updateName.' = "'.$updateValue.'" WHERE goods_id in ('.implode(',',$goods_id).')';
                break;

            case 'add':
                $sql .= $updateName.' = CONCAT("'.$updateValue['front'].'",'.$updateName.',"'.$updateValue['after'].'") WHERE goods_id in ('.implode(',',$goods_id).')';
                break;

            case 'replace':
                $sql .= $updateName.' = REPLACE( '.$updateName.', "'.$updateValue['front'].'" , "'.$updateValue['after'].'" ) WHERE goods_id in ('.implode(',',$goods_id).') AND REPLACE( '.$updateName.', "'.$updateValue['front'].'" , "'.$updateValue['after'].'" ) != "" ';
                break;
        }
        $this->db->exec($sql);
        return true;
    }

    function batchUpdateInt( $goods_id, $updateName, $updateValue , $tableName = '' ){
        $sql = 'UPDATE '.( $tableName?$tableName:'sdb_goods').' SET '.$updateName.' = '.$updateValue.' WHERE goods_id in ( '.implode(',', $goods_id).' )';
        $this->db->exec($sql);
        return true;
    }
    function modifier_goods_name(&$rows){
        foreach($rows as $k=>$v){
             $rows[$k]=htmlspecialchars($rows[$k]);
        }
    }
    function modifier_money(&$rows){
        $cur=$this->system->loadModel('system/cur');
        foreach($rows as $k=>$v){
            $rows[$k]=$cur->changer($v);
        }
    }
    function batchUpdateArray( $goods_id , $tableName, $updateName, $updateValue ){
        $addSql = array();
        foreach( $updateName as $k => $v )
            $addSql[] = $v.' = "'.$updateValue[$k].'" ';
        $sql = 'UPDATE '.$tableName.' SET '.implode(',', $addSql).' WHERE goods_id in ('.implode(',',$goods_id).') ';
        $this->db->exec($sql);
        return true;
    }

    function getGoodsIdByFilter($filter){
        $sql = 'SELECT goods_id FROM sdb_goods WHERE '.$this->_filter($filter);
        $goodsList = $this->db->select($sql);
        $func=create_function('$r','return$r["goods_id"];');
        return array_map($func,$goodsList);
    }

    function getProductLvPrice($goodsId){
        $sql = 'SELECT goods_id, bn, pdt_desc, product_id, cost, price FROM sdb_products WHERE goods_id IN ('.implode(',',$goodsId).')';
        $proList = $this->db->select($sql);

        $levelList = $this->db->select('SELECT goods_id, product_id, level_id, price AS mprice FROM sdb_goods_lv_price WHERE goods_id IN ('.implode(',',$goodsId).')');
        $returnData = array();
        $lvPrice = array();
        foreach( $levelList as $level )
            $lvPrice[$level['product_id']][$level['level_id']] = $level['mprice'] ;

        foreach( $proList as $pro )
            $returnData[$pro['goods_id']][] = array('product_id'=>$pro['product_id'],'bn'=>$pro['bn'], 'pdt_desc'=>$pro['pdt_desc'], 'price'=>$pro['price'], 'lv_price'=>$lvPrice[$pro['product_id']], 'cost'=>$pro['cost'] );

        return $returnData;
    }

    function getProductStore($goodsId){
        $sql = 'SELECT goods_id, bn, pdt_desc, product_id, store FROM sdb_products WHERE goods_id IN ('.implode(',',$goodsId).')';
        $proList = $this->db->select($sql);
        $returnData = array();
        foreach( $proList as $pro )
            $returnData[$pro['goods_id']][] = array( 'product_id'=>$pro['product_id'],'bn'=>$pro['bn'], 'pdt_desc'=>$pro['pdt_desc'], 'store'=>$pro['store'] );
        return $returnData;
    }

    function batchUpdateStore($store){
        foreach( $store as $goods )
            foreach( $goods as $proId => $pstore )
                $this->db->exec('UPDATE sdb_products SET store = '.(intval($pstore)<0?0:intval($pstore)).' WHERE product_id = '.$proId);
        return true;
    }

    function batchUpdatePrice($pricedata){
        foreach( $pricedata as $updateName => $data ){
            if( in_array( $updateName , array( 'price', 'cost' ) ) ) {
                foreach( $data as $goodsId => $goodsItem )
                    foreach( $goodsItem as $proId => $price ){
                        $this->db->exec( 'UPDATE sdb_products SET '.$updateName.' = '.floatval($price).' WHERE product_id = '.$proId );
                        $this->db->exec( 'UPDATE sdb_goods SET '.$updateName.' = '.floatval($price).' WHERE goods_id = '.$goodsId );
                    }
            }else{
                foreach( $data as $goodsId => $goodsItem )
                    foreach( $goodsItem as $proId => $price ){
                        if( $price == null || $price == '' ){
                            $this->db->exec('DELETE FROM sdb_goods_lv_price WHERE product_id = '.$proId.' AND level_id = '.$updateName.' AND goods_id = '.$goodsId);
                            continue;
                        }
                        $datarow = $this->db->selectrow('SELECT count(*) as c FROM sdb_goods_lv_price WHERE product_id = '.$proId.' AND level_id = '.$updateName.' AND goods_id = '.$goodsId);
                        if($datarow['c'] > 0)
                            $this->db->exec('UPDATE sdb_goods_lv_price SET price = '.floatval($price).' WHERE product_id = '.$proId.' AND level_id = '.$updateName.' AND goods_id = '.$goodsId);
                        else
                            $this->db->exec('INSERT INTO sdb_goods_lv_price (product_id, level_id, goods_id, price ) VALUES ( '.$proId.', '.$updateName.', '.$goodsId.', '.floatval($price).' )');
                    }
            }
        }
        return true;
    }

}
