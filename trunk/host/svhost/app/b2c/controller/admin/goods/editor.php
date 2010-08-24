<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_ctl_admin_goods_editor extends desktop_controller{
    var $simpleGoodsId = 1;

    function nospec($cat_id=0){
        $this->_editor($_POST['type_id']);
        $this->display('admin/goods/detail/spec/nospec.html');
    }

   //新增商品页面ctl
    function add(){
        $this->pagedata['title'] = '添加商品';
        $this->pagedata['cat']['type_id'] = $this->simpleGoodsId;
        $this->pagedata['goods']['type']['type_id'] = $this->simpleGoodsId;
        $this->_editor($this->simpleGoodsId);

        header("Cache-Control:no-store");
        $this->singlepage('admin/goods/detail/frame.html');
    }

    function _editor($type_id){
        $cat = &$this->app->model('goods_cat');
        $this->pagedata['cats'] = $cat->getMapTree(0,'');
        $this->pagedata['goodsbn_display_switch'] = ($this->app->getConf('goodsbn.display.switch') == 'true');
        $objGtype = &$this->app->model('goods_type');
        $this->pagedata['gtype'] = $objGtype->getList('*','',0,-1);
        if( !$this->pagedata['gtype'] ){
            echo '请先添加商品类型';
            exit;
        }

//        $gimage = &$this->app->model('gimages');
//        $this->pagedata['uploader'] = $gimage->uploader();

/*{{{*/
        $prototype = $objGtype->dump($type_id,'*',array('brand'=>array('*',array(':brand'=>array('brand_id,brand_name')))));
        if( $type_id == 1 ){
            $oBrand = &$this->app->model('brand');
            $this->pagedata['brandList'] = $oBrand->getList('brand_id,brand_name','',0,-1);
        }else if($prototype['setting']['use_brand']){
            if(!empty($prototype['brand'])){
                foreach( $prototype['brand'] as $typeBrand ){
                    $this->pagedata['brandList'][] = $typeBrand['brand'];
                }
            }
        }

        $this->pagedata['sections'] = array();
        $sections = array(
            'basic'=>array(
                'label'=>__('基本信息'),
                'options'=>'',
                'file'=>'admin/goods/detail/basic.html',
            ),
            'adj'=>array(
                'label'=>__('配件'),
                'options'=>'',
                'file'=>'admin/goods/detail/adj.html',
            ),
            'content'=>array(
                'label'=>__('详细介绍'),
                'options'=>'',
                'file'=>'admin/goods/detail/content.html',
            ),
            'params'=>array(
                'label'=>__('属性参数'),
                'options'=>'',
                'file'=>'admin/goods/detail/params.html',
            ),
            'rel'=>array(
                    'label'=>__('相关商品'),
                    'options'=>'',
                    'file'=>'admin/goods/detail/rel.html',
                ),
                'seo' => array(
                        'label' => __('自定义url'),
                        'options'=>'',
                        'file'=>'admin/goods/detail/seo.html'
                    ),
        );
        if( !$prototype['setting']['use_props'] && !$prototype['setting']['use_params'] )
            unset( $sections['params'] );
        foreach($sections as $key=>$section){
            if (!isset($prototype['setting']['use_'.$key]) || $prototype['setting']['use_'.$key] ){
                if(method_exists($this,($func = '_editor_'.$key))){
                    $this->$func();
                }
                $this->pagedata['sections'][$key] = $section;
            }
        }
        $this->pagedata['goods']['type']['type_id'] = $type_id;
        if($this->pagedata['goods']['spec']){ // || $prototype['spec']
            $prototype['setting']['use_spec'] = 1;
            if(!$this->pagedata['goods']['products']){
                $this->pagedata['goods']['products'] = array(1);
            }
        }
        $this->pagedata['goods']['type'] = $prototype;
/*}}}*/
        $this->pagedata['point_setting'] = $this->app->getConf('point.get_policy');
        $this->pagedata['url'] = dirname($_SERVER['PHP_SELF']);
        $memberLevel = &$this->app->model('member_lv');
        $this->pagedata['mLevels'] = $memberLevel->getList('member_lv_id,dis_count');
        $oTag = &app::get('desktop')->model('tag');
        $this->pagedata['tagList'] = $oTag->getList('*',array('tag_mode'=>'normal','tag_type'=>'goods'),0,-1);
        $this->pagedata['image_dir'] = &app::get('image')->res_url;
        $this->pagedata['storeplace'] = $this->app->getConf('storeplace.display.switch');

        $this->pagedata['spec_default_pic'] = $this->app->getConf('spec.default.pic');
    }

    function _prepareGoodsData( &$data ){
        $objGoods = $this->app->model('goods');
        $objGtype = $this->app->model('goods_type');
        $lastGoodsId = $objGoods->getlist('goods_id',array(),0,1,'goods_id desc');

        if( !$data['cat_id'] ) $data['cat_id'] = 0;
        $lastGoodsId = $lastGoodsId[0]['goods_id'];

        $goods = $data['goods'];
        if(is_numeric($goods['type']['type_id'])){
            $floatstore = $objGtype->getlist('floatstore',array('type_id'=>$goods['type']['type_id']));
            if(!$floatstore[0]['floatstore']){
                foreach($goods['product'] as $key=>$val){
                    if( $val['store'] )
                        $goods['product'][$key]['store']= intval($val['store']);
                }
            }
        }



        $goods['adjunct'] = $data['adjunct'];
        $goods['image_default_id'] = $data['image_default'];
        foreach( explode( '|', $data['keywords']) as $keyword ){
            $goods['keywords'][] = array(
                'keyword' => $keyword,
                'res_type' => 'goods'
            );
        }
        if( $goods['spec'] ){
            $goods['spec'] = unserialize($goods['spec'] );
        }else{
            $goods['spec'] = null;
        }
        //处理配件

        if( !$goods['min_buy'] )unset( $goods['min_buy'] );
        if( !$goods['brand']['brand_id'] )unset( $goods['brand'] );
        $images = array();
        foreach( (array)$goods['images'] as $imageId ){
            $images[] = array(
                'target_type'=>'goods',
                'image_id'=>$imageId,
                );
        }
        $goods['images'] = $images;
        unset($images);
        if(isset($goods['adjunct']['name'])){
           foreach($goods['adjunct']['name'] as $key => $name){
                $aItem['name'] = $name;
                $aItem['type'] = $goods['adjunct']['type'][$key];
                $aItem['min_num'] = $goods['adjunct']['min_num'][$key];
                $aItem['max_num'] = $goods['adjunct']['max_num'][$key];
                $aItem['set_price'] = $goods['adjunct']['set_price'][$key];
                $aItem['price'] = $goods['adjunct']['price'][$key];
                if($aItem['type'] == 'goods') $aItem['items']['product_id'] = $goods['adjunct']['items'][$key];
                else $aItem['items'] = $goods['adjunct']['items'][$key];//.'&dis_goods[]='.$aData['goods_id']
                $aAdj[] = $aItem;
            }
        }
        $goods['adjunct'] = $aAdj;
        $goods['product'][key($goods['product'])]['default'] = '1';
        foreach( $goods['product'] as $prok => $pro ){
            if($goods['unit'])
                $goods['product'][$prok]['unit'] = $goods['unit'];
            if( !$pro['product_id'] || substr( $pro['product_id'],0,4 ) == 'new_' )
                unset( $goods['product'][$prok]['product_id'] );
            if( $pro['status'] != 'true' )
                $goods['product'][$prok]['status'] = 'false';
            $mprice = array();
            if( $pro['weight'] === '' )
                $goods['product'][$prok]['weight'] = '0';
            if( $pro['store'] === '' )
                $goods['product'][$prok]['store'] = null;
            foreach( (array)$pro['price']['member_lv_price'] as $mLvId => $mLvPrice )
                if( $mLvPrice )
                    $mprice[] = array( 'level_id'=>$mLvId,'price'=>$mLvPrice );
            $goods['product'][$prok]['price']['member_lv_price'] = $mprice;
            foreach( array('mktprice','cost','price') as $pCol ){
                if( !$pro['price'][$pCol]['price'] && $pro['price'][$pCol]['price'] !== 0 ){
                    $goods['product'][$prok]['price'][$pCol]['price'] = '0';
                }
            }
        }
        if(is_array($data['linkid'])){
            foreach($data['linkid'] as $k => $id){
                if(!empty($goods['goods_id']))
                    $lastId = $goods['goods_id'];
                else
                    $lastId = intval($lastGoodsId)+1;
                $aLink[] = array('goods_1' => $lastId, 'goods_2' => $id, 'manual' => $data['linktype'][$id], 'rate' => 100);
            }
            $goods['rate'] = $aLink;
        }
        $goods['rate'] = $aLink;
        if( !$goods['category']['cat_id']) $goods['category']['cat_id'] = 0;
        if( !$goods['tag'] ) $goods['tag'] = array();
        if( !$goods['adjunct'] ) $goods['adjunct'] = array();
        if( !$goods['rate'] ) $goods['rate'] = array();
        if( $goods['score'] === '' ) unset($goods['score']);
        if( empty($goods['package_scale']) ) $goods['package_scale'] = '1';
        if( empty($goods['package_unit']) ) $goods['package_unit'] = '';
        if( $goods['props'] ){
            foreach( $goods['props'] as $pk => $pv ){
                if( substr($pk,2) <= 20 && $pv['value'] === '' )
                    $goods['props'][$pk]['value'] = null;
            }
        }
        return $goods;
    }

    function toAdd(){


        $goods = $this->_prepareGoodsData($_POST);
        if( $goods['udfimg'] == 'true' && !$goods['thumbnail_pic'] ){
            $goods['udfimg'] = 'false';
        }

            $this->begin('');
        if( count( $goods['product'] ) == 0 ){
            //$this->end(false,'货品未添加');
            exit;
        }
        if( strlen($goods['brief']) > 255 ){
            $this->end(false,__( '商品介绍请不要超过255字节' ));
        }
        $oGoods = &$this->app->model('goods');
        if( $goods['bn']  ){
            if( $oGoods->checkProductBn($goods['bn'], $goods['goods_id']) ){
                $this->end(false,__('您所填写的商品编号已被使用，请检查！'));
            }
        }
        foreach($goods['product'] as $k => $p){
            if(empty($p['bn'])) continue;
            if($oGoods->checkProductBn($p['bn'], $goods['goods_id']) ){
                $this->end(false,__('您所填写的货号已被使用，请检查！'));
            }
        }

        $oUrl = kernel::single('site_route_app');

        if ( !$oGoods->save($goods) ){
            $this->end(false,__('您所填写的货号重复，请检查！'));
        }else{
            if( $goods['images'] ){
                $oImage = &app::get('image')->model('image');
                foreach($goods['images'] as $k=>$v){
                    $test = $oImage->rebuild($v['image_id'],array('S','M','L'),true);
                }
            }

            if( $_POST['goods_static'] ){
                $url = $oUrl->fetch_static( array( 'static'=>$_POST['goods_static'] ) );
                $goods_url = app::get('site')->router()->gen_url( array( 'app'=>'b2c','real'=>1,'ctl'=>'site_product','args'=>array($goods['goods_id']) ) );

                $goods_url = substr( $goods_url , strlen( app::get('site')->base_url() ) );
                $goods_url_info = $oUrl->fetch_static( array( 'url'=>$goods_url ) );
                $goods_url_info['url'] = $goods_url;
                $goods_url_info['static'] = $_POST['goods_static'];
                if( $url['url'] && $url['url'] != $goods_url_info['url'] ){
                    $this->end(false,__('您填写的自定义链接已存在'));
                }
                $oUrl->store_static( $goods_url_info );
           }else{
                $oUrl->delete_static( array( 'static' => $_POST['goods_static'] ) );
           }

        }
        if($this->app->getConf('system.product.zendlucene')=='true'){
             $obj = search_core::segment();
            if(is_dir(ROOT_DIR . '/data/search/zend/lucene/')){
                $luceneIndex = search_core::instance('b2c_goods')->link();
            }else{
                $luceneIndex = search_core::instance('b2c_goods')->create();
            }
            $luceneIndex = search_core::instance('b2c_goods')->update($goods);
        }
        $this->end(true,__('操作成功'),null,array('goods_id'=>$goods['goods_id'] ) );


    }

    function edit($goods_id){
        $this->goods_id = $goods_id;
        $oGoods = &$this->app->model('goods');
        $goods = $oGoods->dump($goods_id,'*','default');
           
        $this->_editor($goods['type']['type_id']);
        if(is_numeric($goods['store'])) $goods['store'] = (float)$goods['store'];
        if(is_array($goods['product'])){
            foreach($goods['product'] as $k=>$v){
                $goods['product'][$k]['store'] = $v['store']!==null ? (float)$v['store'] : '';
            }
        }
        $this->pagedata['goods'] = $goods;
        $this->pagedata['app_dir'] = app::get('b2c')->app_dir;
        if(!is_array($goods['adjunct']))
            $this->pagedata['goods']['adjunct'] = unserialize($goods['adjunct']);
        else
            $this->pagedata['goods']['adjunct'] = $goods['adjunct'];
        foreach($oGoods->getLinkList($goods_id) as $rows){
            if($rows['goods_1'] == $goods_id){
                $aLinkList[] = $rows['goods_2'];
                $linkType[$rows['goods_2']] = array('manual'=>$rows['manual']);
            }else{
                $aLinkList[] = $rows['goods_1'];
                $linkType[$rows['goods_1']] = array('manual'=>$rows['manual']);
            }
        }

        $oUrl = kernel::single('site_route_app');
        $goods_url = app::get('site')->router()->gen_url( array( 'app'=>'b2c','real'=>1,'ctl'=>'site_product','args'=>array($goods_id) ) );
        $goods_url = substr( $goods_url , strlen( app::get('site')->base_url() ) );

        $url = $oUrl->fetch_static( array( 'url'=>$goods_url ) );
        $this->pagedata['goods_static'] = $url['static'];

        $this->pagedata['goods']['glink']['items'] = $aLinkList;
        $this->pagedata['goods']['glink']['moreinfo'] = $linkType;

        $this->singlepage('admin/goods/detail/frame.html');
    }

    function _set_type_spec($typeId){
        $oGtype = &$this->app->model('goods_type');
        $spec = (array)$oGtype->dump($typeId,'type_id',array(
                'spec'=>array('spec_id',
                    array(
                        'spec:specification'=>array('*',
                            array(
                                'spec_value' =>array('*')
                            )
                        )
                    )
                )
            )
        );
        $this->pagedata['spec'] = $spec['spec'];
    }

    function _set_spec($spec){
        $oSpec = &$this->app->model('specification');
        $subSdf = array(
            'spec_value' =>array('*')
        );
        $this->pagedata['spec'] = $oSpec->batch_dump( array('spec_id'=>array_keys($spec)), '*' , $subSdf, 0 ,-1 );
        $this->pagedata['goods_spec'] = $spec;
    }

    function set_spec(){
        $typeId = $_GET['p'][0]['type_id'];
        $_POST['spec'] = unserialize($_POST['spec']);
        if( $_POST['spec'] ){
            $this->_set_spec($_POST['spec']);
        }else{
            $this->_set_type_spec($typeId);
        }
        $this->pagedata['spec_default_pic'] = $this->app->getConf('spec.default.pic');
        $this->display('admin/goods/detail/spec/set_spec.html');
    }

    function set_spec_desc(){
        $spec = $_POST['spec'];
        $spec[$_POST['addSpecId']] = null;
        $this->_set_spec( $spec );
//        $oSpec = &$this->app->model('specification');
//        $this->pagedata['specs'] = $oSpec->getList('spec_id,spec_name,spec_memo',null,0,-1);

        $this->pagedata['spec_default_pic'] = $this->app->getConf('spec.default.pic');
        $this->display('admin/goods/detail/spec/set_spec_desc.html');
    }

    function addSpecValue(){
        $_POST = utils::stripslashes_array($_POST);
        $this->pagedata['aSpec'] = array(
            'spec_type' => $_POST['spec']['specType'],
            'spec_id' => $_POST['spec']['specId']
        );
        $this->pagedata['specValue'] = array(
            'spec_value_id' => $_POST['spec']['specValueId'],
            'spec_value' => $_POST['spec']['specValue'],
            'private_spec_value_id'=>time().$_POST['sIteration'],
            'spec_image'=>$_POST['spec']['specImage'],
//            'spec_image_id' => $_POST['spec']['specImageId'],
            'spec_goods_images'=>$_POST['spec']['specGoodsImages']
        );

        $this->pagedata['spec_default_pic'] = $this->app->getConf('spec.default.pic');
        $this->display('admin/goods/detail/spec/spec_value.html');
    }

    function doAddSpec(){
        $oImage = app::get('image')->model('image');//fetch($_POST['']);

        $this->pagedata['goods']['spec'] = &$_POST['spec'];
        if( $_GET['create'] == 'true' ){
            $pro = $this->_doCreatePro( $pro, $_POST['spec'] );
            $this->pagedata['fromType'] = 'create';
            $this->pagedata['goods']['product'] = $pro;
        }
        $this->_set_spec( $_POST['spec'] );
        $this->pagedata['spec_tmpl'] = $this->pagedata['spec'];
        $this->pagedata['needUpValue'] = json_encode($_POST['needUpValue']);
//        $this->pagedata['spec_default_pic'] = $this->app->getConf('spec.default.pic');
        $memberLevel = &$this->app->model('member_lv');
        $this->pagedata['mLevels'] = $memberLevel->getList('member_lv_id,dis_count');
        $this->pagedata['app_dir'] = app::get('b2c')->app_dir;
        
        $this->pagedata['spec_default_pic'] = $this->app->getConf('spec.default.pic');

        $this->display('admin/goods/detail/spec/spec.html');
    }

    function _doCreatePro( $pro, $spec ){
        if( empty( $spec ) ){
            $res = array();
            foreach( $pro as $pk => $pv ){
                foreach( $pv as $pvk => $pvv ){
                    $res['new_'.$pk]['spec_desc']['spec_value'][$pvv['spec_id']] = $pvv['spec_value'];
                    $res['new_'.$pk]['spec_desc']['spec_private_value_id'][$pvv['spec_id']] = $pvv['private_spec_value_id'];
                    $res['new_'.$pk]['spec_desc']['spec_value_id'][$pvv['spec_id']] = $pvv['spec_value_id'];
                }
            }
            return $res;
        }
        $firstSpec = array_shift( $spec );

        $rs = array();
        foreach( $firstSpec['option'] as $sitem ){
            foreach( (array)$pro as $pitem ){
                $apitem = $pitem ;
                array_push( $apitem , array('spec_id'=>$firstSpec['spec_id']) + $sitem );
                $rs[] = $apitem;
            }
            if( empty($pro) )
                $rs[] = array( array_merge( array('spec_id'=>$firstSpec['spec_id']) , $sitem) );
        }
       return $this->_doCreatePro( $rs, $spec );
    }


    function update(){
        $goods = $this->_prepareGoodsData($_POST);
        $oType = &$this->app->model('goods_type');
        $goods['type'] = $oType->dump($goods['type']['type_id'],'*');
        unset($goods['spec'],$goods['product']);
        $this->_editor($goods['type']['type_id']);
        $this->pagedata['goods'] = $goods;
        $this->display('admin/goods/detail/page.html');
    }

    function addGrp(){
        $this->pagedata['aOptions'] = array('goods'=>__('选择几件商品作为配件'), 'filter'=>__('选择一组商品搜索结果作为配件'));
        $this->display('admin/goods/detail/adj/info.html');
    }

    function doAddGrp(){
        $this->pagedata['adjunct'] =array('name'=>$_POST['name'],'type'=>$_POST['type']);
        $this->pagedata['key'] = time();
        $this->display('admin/goods/detail/adj/row.html');
    }

    function specValue(){
        $specId = $_GET['spec_id'];
        $objSpec = &$this->app->model('specification');

        $this->pagedata['aSpec'] = $objSpec->dump($specId,'*','default');
        $this->pagedata['spec_default_pic'] = $this->app->getConf('spec.default.pic');
        $this->display('admin/goods/detail/spec/spec_value_tmpl.html');
    }

    function showfilter($type_id){
        $obj = &$this->app->model('goods');
        $this->pagedata['filter'] = $obj->getFilterByTypeId(array('type_id'=>$type_id));
        $this->pagedata['filter_interzone'] = $_POST;
        $this->pagedata['view'] = $_POST['view'];
        $this->display('admin/goods/filter_addon.html');
    }

    function selAlbumsImg(){
        $this->pagedata['selImgs'] = explode(',',$_POST['selImgs']);
        $this->pagedata['img'] = $_POST['img'];
        $this->display('admin/goods/detail/spec/spec_selalbumsimg.html');
    }

    function set_mprice(){
        $memberLevel = &$this->app->model('member_lv');
        foreach($memberLevel->getList('member_lv_id,name,dis_count,name') as $level){
            $level['dis_count'] = ($level['dis_count']>0 ? $level['dis_count'] : 1);
            $level['price'] = $_POST['level'][$level['member_lv_id']];
            $this->pagedata['mPrice'][$level['member_lv_id']] = $level;
        }
        $this->display('admin/goods/detail/level_price.html');
    }
}
