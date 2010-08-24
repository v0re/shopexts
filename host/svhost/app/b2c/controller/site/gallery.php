<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */

class b2c_ctl_site_gallery extends b2c_frontpage{

    var $_call = 'index';
    var $type='goodsCat';
    var $seoTag=array('shopname','goods_amount','goods_cat','goods_cat_p','goods_type','brand','sort_path');

    public function index($cat_id='',$urlFilter=null,$orderBy=0,$tab=null,$page=1,$cat_type=null,$view=null) {
        $urlFilter=htmlspecialchars(urldecode($urlFilter));
        if($_GET['scontent'])
            $urlFilter = $_GET['scontent'];
        if($cat_id == '_ANY_'){
            unset($cat_id);
        }
        if($cat_id){
            $cat_id=explode(",",$cat_id);
            foreach($cat_id as $k=>$v){
                if($v) $cat_id[$k]=intval($v);
            }
            $this->id = implode(",",$cat_id);
        }
        if( !$cat_id  ){
            $cat_id = array('');
            $this->id = '';
        }
        $pageLimit = $this->app->getConf('gallery.display.listnum');
        $pageLimit = ($pageLimit ? $pageLimit : -1);
        $this->pagedata['pdtPic']=array('width'=>100,'heigth'=>100);

        $productCat = &$this->app->model('goods_cat');
        $GLOBALS['runtime']['path'] = $productCat->getPath($cat_id[0],'');
        $this->pagedata['childnode'] = $productCat->getCatParentById($cat_id,'index');

        $objGoods = $this->app->model('goods');
        if(empty($view))
            $view = $this->app->getConf('gallery.default_view')?$this->app->getConf('gallery.default_view'):'index';
        $goods_cat = &$this->app->model('goods_cat');
        if( $cat_id ){
            $type_filter = $goods_cat->dump(array('cat_id'=>$cat_id),'type_id');
        }
        $cat = kernel::service('b2c_site_goods_list_viewer_apps')->get_view($cat_id,$view,$type_filter['type_id']);
        $this->pagedata['args'] = array($this->id,urlencode($urlFilter),$orderBy,$tab,$page,$cat_type,$view);

        $this->pagedata['curView'] = $view;

        if($this->app->getConf('system.seo.noindex_catalog'))
            $this->header .= '<meta name="robots" content="noindex,noarchive,follow" />';

        $searchtools = &$this->app->model('search');
        $path =array();

        $filter = $searchtools->decode($urlFilter,$path,$cat);
        if(is_array($filter)){
            $filter=array_merge(array('cat_id'=>$cat_id,'marketable'=>'true'),$filter);
            if( ($filter['cat_id'][0] === '' || $filter['cat_id'][0] === null ) && !isset( $filter['cat_id'][1] ) )
                unset($filter['cat_id']);
            if( ($filter['tag'][0] === '' || $filter['tag'][0] === null ) && !isset( $filter['tag'][1] ) )
                unset($filter['tag']);
            if( ($filter['brand_id'][0] ==='' || $filter['brand_id'][0] === null) && !isset( $filter['brand_id'][1] ))
                unset($filter['brand_id']);
        }else{
            $filter = array('cat_id'=>$cat_id,'marketable'=>'true');
        }
        //--------获取类型关联的规格
        $type_id = $type_filter['type_id'];

        $gType = &$this->app->model('goods_type');
        $SpecList = $gType->getSpec($type_id,1);//获取关联的规格

        $oGoodsTypeSpec = $this->app->model('goods_type_spec');
        $type_spec = $oGoodsTypeSpec->get_type_spec($type_id);

        $oSpecification = &$this->app->model('specification');
        $filter['cat_id'] = $cat_id;
        $filter['goods_type'] = 'normal';
        $filter['marketable'] = 'true';
        //-----查找当前类别子类别的关联类型ID
        if ($urlFilter){
            if($vcat['type_id']){
                //$filter['type_id']=$vcat['type_id'];
                $filter['type_id']=null;

            }
        }
        //--------
        $this->pagedata['tabs'] = $cat['tabs'];
        $this->pagedata['cat_id'] = implode(",",$cat_id);
        $views = $cat['setting']['list_tpl'];
        foreach($views as $key=>$val){
            $this->pagedata['views'][$key] = array($this->id,urlencode($urlFilter),$orderBy,$tab,$page,$cat_type,$val);

        }


        if($cat['tabs'][$tab]){
            parse_str($cat['tabs'][$tab]['filter'],$_filter);
            $filter = array_merge($filter,$_filter);
        }
        if(isset($this->pagedata['orderBy'][$orderBy])){
            $orderby = $this->pagedata['orderBy'][$orderBy]['sql'];
        }

        $selector = array();
        $search = array();

        if((!is_array($cat_id) && $cat_id) || $cat_id[0] || $cat_type){
            $goods_relate=$objGoods->getList("*",$filter,0,-1);
        }


        if ($goods_relate){
            unset($tmpSpecValue);
            foreach($goods_relate as $grk => $grv){
                if ($grv['spec_desc']){
                    $tmpSdesc=$grv['spec_desc'];
                    if(is_array($tmpSdesc)){
                        foreach($tmpSdesc as $tsk => $tsv){
                            foreach($tsv as $tk => $tv){
                                if(is_array($tv['spec_value_id'])){
                                    if (!in_array($tv['spec_value_id'],$tmpSpecValue))
                                    $tmpSpecValue[]=$tv['spec_value_id'];
                                }
                            }
                        }
                    }
                }
            }
        }
        /***********************/

        if ($SpecList){
            if ($curSpec)
                $curSpecKey=array_keys($curSpec);
            foreach($SpecList as $spk => $spv){
                $selected=0;
                if ($curSpecKey&&in_array($spk,$curSpecKey)){
                    $spv['spec_value'][$curSpec[$spk]]['selected']=true;
                    $selected=1;
                }
                if ($spv['spec_style']=="select"){ //下拉
                    $SpecSelList[$spk] = $spv;
                    if ($selected)
                        $SpecSelList[$spk]['selected'] = true;
                }
                elseif ($spv['spec_style']=="flat"){
                    $SpecFlatList[$spk] = $spv;
                    if ($selected)
                        $SpecFlatList[$spk]['selected'] = true;
                }
            }
        }

        $this->pagedata['filter'] = $this->filter;
        $this->pagedata['SpecFlatList'] = $SpecFlatList;
        $this->pagedata['specimagewidth'] = $this->app->getConf('spec.image.width');
        $this->pagedata['specimageheight'] = $this->app->getConf('spec.image.height');
        /************************/

        if (is_array($cat['brand'])){
            foreach($cat['brand'] as $bk => $bv){
                $bCount=0;
                if(is_array($filter['brand_id']))
                     $bid = array_flip($filter['brand_id']);
                $brand = array('name'=>__('品牌'),'value'=>$bid);
                if(is_array($goods_relate)){
                    foreach($goods_relate as $gk => $gv){
                        if ($gv['brand_id']){

                            if ($gv['brand_id']==$bv['brand_id']){
                                $bCount++;
                            }
                        }
                    }
                }
                if ($bCount>0){

                    $tmpOp[$bv['brand_id']]=$bv['brand_name']."<span class='num'>(".$bCount.")</span>";
                }
            }

            $brand['options'] = $tmpOp;
            $selector['brand_id'] = $brand;


        }

        foreach((array)$cat['props'] as $prop_id=>$prop){

            if($prop['type']=='select'){
                $prop['value'] = $filter['p_'.$prop_id][0];
                $searchSelect[$prop_id] = $prop;
            }elseif($prop['search']=='input'){
                $prop['value'] = ($filter['p_'.$prop_id][0]);
                $searchInput[$prop_id] = $prop;
            }elseif($prop['search']=='nav'){

                if(is_array($filter['brand_id'])&&isset($filter['p_'.$prop_id]))
                    $prop['value'] = array_flip($filter['p_'.$prop_id]);
                $plugadd=array();

                foreach($goods_relate as $k=>$v){

                    if($v["p_".$prop_id]!=null){

                        if($plugadd[$v["p_".$prop_id]]){
                            $plugadd[$v["p_".$prop_id]]=$plugadd[$v["p_".$prop_id]]+1;
                        }else{
                            $plugadd[$v["p_".$prop_id]]=1;
                        }
                    }
                    $aFilter['goods_id'][] = $v['goods_id'];    //当前的商品结果集
                }
                $navselector=0;

                foreach($prop['options'] as $q=>$e){
                    if($plugadd[$q]){
                        $prop['options'][$q]=$prop['options'][$q]."<span class='num'>(".$plugadd[$q].")</span>";
                        if (!$navselector)
                            $navselector=1;
                    }else{
                        unset($prop['options'][$q]);
                    }
                }
                $selector[$prop_id] = $prop;
            }
        }

        if ($navselector){
            $nsvcount=0;
            $noshow=0;
            foreach($selector as $sk => $sv){
                if ($sv['value']){
                    $nsvcount++;
                }
                if (is_numeric($sk)&&!$sv['show']){
                    $noshow++;
                }
            }
            if ($nsvcount==intval(count($selector)-$noshow))
                $navselector=0;
        }
        foreach((array)$cat['spec'] as $spec_id=>$spec_name){
            $sId['spec_id'][] = $spec_id;
        }

        $cat['ordernum'] = $cat['ordernum']?$cat['ordernum']:array(''=>2);
        if ($cat['ordernum']){
            if ($selector){
                foreach($selector as $key => $val){
                    if(!in_array($key,$cat['ordernum'])&&$val){
                        $selectorExd[$key]=$val;
                    }
                }
            }
        }
        $this->pagedata['orderBy'] = $objGoods->orderBy();//排序方式
        if(!isset($this->pagedata['orderBy'][$orderBy])){
            $this->_response->set_http_response_code(404);
        }else{
            $orderby = $this->pagedata['orderBy'][$orderBy]['sql'];
        }
        $selector['ordernum'] = $cat['ordernum'];
        //$objGoods->appendCols .= 'big_pic';/*appendCols big_pic update 2009年9月25日13:46:45*/
        if($this->app->getConf('system.product.zendlucene')=='true'){
            search_core::segment();

            $filter['from'] = $pageLimit*($page-1);     //分页
            $filter['to'] = $pageLimit;
            $filter['order'] = $orderby;

            if(is_dir(ROOT_DIR . '/data/search/zend/lucene/'))
                $luceneIndex = search_core::instance('b2c_goods')->link();
            else
                $luceneIndex = search_core::instance('b2c_goods')->create();

            $query = search_core::instance('b2c_goods')->query($filter);
            $rfilter = search_core::instance('b2c_goods')->commit();
            $count = count($rfilter['goods_id']);
            if(count($rfilter['goods_id'])==0)
                $rfilter['goods_id']='-1';

            $aProduct = $objGoods->getList('*',$rfilter);
        }else{

            $aProduct = $objGoods->getList('*',$filter,$pageLimit*($page-1),$pageLimit,$orderby);
            $count = $objGoods->count($filter);
        }





        //对商品进行预处理
        $this->pagedata['mask_webslice'] = $this->app->getConf('system.ui.webslice')?' hslice':null;
        $this->pagedata['searchInput'] = &$searchInput;
        $this->pagedata['selectorExd'] = $selectorExd;
        $this->cat_id = $cat_id;
        $this->_plugins['function']['selector'] = array(&$this,'_selector');
        $this->pagedata['pager'] = array(
            'current'=>$page,
            'total'=>ceil($count/$pageLimit),
            'link'=>  $this->gen_url(array('app'=>'b2c', 'ctl'=>'site_gallery','full'=>1,'act'=>'index','args'=>array(implode(',',$cat_id),urlencode($p['str']),$orderBy,$tab,($tmp=time()),'',$view))),
            'token'=>$tmp);
        if($page != 1 && $page > $this->pagedata['pager']['total']){
            $this->_response->set_http_response_code(404);
        }
        if(!$count){
            $this->pagedata['emtpy_info']=$this->app->getConf('errorpage.searchempty');
        }
        $this->pagedata['searchtotal']=$count;
        if(is_array($aProduct) && count($aProduct) > 0){
            $objProduct = $this->app->model('products');
            if($this->app->getConf('site.show_mark_price')=='true'){
                $setting['mktprice'] = $this->app->getConf('site.show_mark_price');
                if(isset($aProduct)){
                    foreach($aProduct as $pk=>$pv){
                        $aProduct[$pk]['mktprice'] = $objProduct->getRealMkt($pv['price']);
                    }
                }
            }else{
                $setting['mktprice'] = 0;
            }
            $setting['saveprice'] = $this->app->getConf('site.save_price');
            $setting['buytarget'] = $this->app->getConf('site.buy.target');
            $this->pagedata['setting'] = $setting;
            //spec_desc
            $siteMember = $this->get_current_member();
            $this->site_member_lv_id = $siteMember['member_lv'];
            $oGoodsLv = &$this->app->model('goods_lv_price');
            $oMlv = &$this->app->model('member_lv');
            $mlv = $oMlv->db_dump( $this->site_member_lv_id,'dis_count' );

            foreach ($aProduct as &$val) {
                $temp = $objProduct->getList('product_id, spec_info, price, freez, store, goods_id',array('goods_id'=>$val['goods_id']));
                if( $this->site_member_lv_id ){
                    $tmpGoods = array();
                    foreach( $oGoodsLv->getList( 'product_id,price',array('goods_id'=>$val['goods_id'],'level_id'=>$this->site_member_lv_id ) ) as $k => $v ){
                        $tmpGoods[$v['product_id']] = $v['price'];
                    }
                    foreach( $temp as &$tv ){
                        $tv['price'] = (isset( $tmpGoods[$tv['product_id']] )?$tmpGoods[$tv['product_id']]:( $mlv['dis_count']*$tv['price'] ));
                    }
                    $val['price'] = $tv['price'];
                }
                $val['spec_desc_info'] = $temp;
            }
            $this->pagedata['products'] = &$aProduct;
        }
        $aData = $this->get_current_member();
        if(!$aData['member_id']){
            $this->pagedata['login'] = 'nologin';
        }
        if($SpecSelList){
            $this->pagedata['SpecSelList'] = $SpecSelList;
        }
        if($searchSelect){
            $this->pagedata['searchSelect'] = &$searchSelect;
        }
        $this->pagedata['selector'] = &$selector;
        $this->pagedata['cat_type'] = $cat_type;
        if($GLOBALS['search_array']){
            $this->pagedata['search_array'] = implode("+",$GLOBALS['search_array']);
        }
        $this->pagedata['_PDT_LST_TPL'] = $cat['tpl'];
        $this->pagedata['filter'] = $filter;
        $this->set_tmpl('gallery.html');
        $this->pagedata['gallery_display'] = $this->app->getConf('gallery.display.grid.colnum');
        $imageDefault = app::get('image')->getConf('image.set');
        $this->pagedata['image_set'] = $imageDefault;
        $this->pagedata['defaultImage'] = $imageDefault['S']['default_image'];
        $this->page('site/gallery/index.html');
    }



}
