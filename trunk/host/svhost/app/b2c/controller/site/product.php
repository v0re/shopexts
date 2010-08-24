<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */

class b2c_ctl_site_product extends b2c_frontpage{

    var $_call = 'call';
    var $type = 'goods';
    var $seoTag = array('shopname','brand','goods_name','goods_cat','goods_intro','goods_brief','brand_kw','goods_kw','goods_price','update_time','goods_bn');
    function __construct($app){
        parent::__construct($app);
        $this->title    = $this->app->getConf('site.goods_title');
        $this->keywords = $this->app->getConf('site.goods_meta_key_words');
        $this->desc     = $this->app->getConf('site.goods_meta_desc');
    }

    function call(){
        $args = func_get_args();
        $action = array_shift($args);
        if(method_exists($this,$action)){
            call_user_func_array(array(&$this,$action),$args);
        }else{
            $objSchema = &$this->app->model('goods/schema');
            $gid = array_shift($args);
            if(!is_int($gid)) {
                echo 'Invalid Schema calling';
                die();
            }
            $objSchema->runFunc($gid,$action,$args);
        }
    }

    function getVirtualCatById($cat_id=0){
        $vobjCat = &$this->app->model('goods/virtualcat');
        $xml = &$this->app->model('utility/xml');
        $result=$vobjCat->getVirtualCatById(intval($cat_id));

        $searchtools = &$this->app->model('goods/search');
        foreach($result as $k=>$v){
            $filter=$vobjCat->_mkFilter($result[$k]['filter']);
            $cat_id=$filter['cat_id'];
            $filter=$searchtools->encode($filter);
            $result[$k]['url']=$this->system->mkUrl('gallery',$this->app->getConf('gallery.default_view'),array(implode(',',$cat_id),$filter,'0','','',$result[$k]['virtual_cat_id']));
        }
        echo json_encode($result);
    }
    public function index() {  //$gid,$specImg='',$spec_id=''

        $_getParams = $this->_request->get_params();
//        $params = $this->_request->get_post():
        $gid = $_getParams[0];
        $specImg = $_getParams[1];
        $spec_id = $_getParams[2];
        $this->id = $gid;
        $this->customer_source_type='product';
        $this->customer_template_type='product';
        $this->customer_template_id=$gid;
//        $member_lv = intval($this->system->request['member_lv']);

        $objGoods = &$this->app->model('goods');
        $objProducts = &$this->app->model('products');
        $GLOBALS['runtime']['path'] = $objGoods->getPath($gid,'');
        $subsdf = array(
            'product'=>array(
                '*',array(
                        'price/member_lv_price'=>array('*')
                    )
            ),
            ':brand'=>array('*'),
            ':goods_type'=>array('*'),
            'keywords'=> array('*'),
            'images' => array('*',array('image'=>array('*')))
        );

        $aGoods = $objGoods->dump($gid,'*',$subsdf);
        if($mlv = $GLOBALS['_REQUEST']['MLV']){
            foreach($aGoods['product'] as $ak=>$av){
                $aGoods['price'] = $av['price']['member_lv_price'][$mlv]['price'];
                if(!$this->app->getConf('site.member_price_display')&&isset($av['price']['member_lv_price'][$mlv])){
                    unset($aGoods['product'][$ak]['price']['member_lv_price']);
                    $aGoods['product'][$ak]['price']['member_lv_price'][$mlv] = $av['price']['member_lv_price'][$mlv];
                }
                $aGoods['product'][$ak]['price']['price']['current_price'] = $av['price']['member_lv_price'][$mlv]['price'];
            }
        }
        if(!$aGoods){
            echo '无效商品';exit;
        }
        $blocks = array('promotion'=>array('goods_id'=>$aGoods['goods_id']));
        foreach(kernel::servicelist('b2c_site_goods_detail_block') as $object){
            $promotionMsg = $object->get_blocks($blocks);   //todo check is right?
        }
        if(!is_array($aGoods['adjunct']))
            $aGoods['adjunct'] = unserialize($aGoods['adjunct']);
        else
            $aGoods['adjunct'] = $aGoods['adjunct'];

        if(is_array($aGoods['adjunct'])){
            foreach($aGoods['adjunct'] as $key => $rows){    //loop group
                if($rows['set_price'] == 'minus'){
                    $cols = 'product_id,goods_id,name, spec_info, store, freez, price, price-'.intval($rows['price']).' AS adjprice,marketable';
                }else{
                    $cols = 'product_id,goods_id,name, spec_info, store, freez, price, price*'.($rows['price']?$rows['price']:1).' AS adjprice,marketable';
                }
                if($rows['type'] == 'goods'){
                    if(!$rows['items']['product_id']) $rows['items']['product_id'] = array(-1);
                    $arr = $rows['items'];
                }else{
                    parse_str($rows['items'].'&dis_goods[]='.$gid, $arr);
                }
                if(isset($arr['type_id'])){
                    if(is_array($arr['props'])){
                        $c = 1;
                        foreach($arr['props'] as $pk=>$pv){
                            $p_id= 'p_'.$c;
                             foreach($pv as $sv){
                                 if($sv == '_ANY_'){
                                     unset($pv);
                                 }
                             }
                             if(isset($pv))
                                 $arr[$p_id] = $pv;
                             $c++;
                        }
                        unset($arr['props']);
                    }

                    $gId = $objGoods->getList('goods_id',$arr,0,-1);
                    if(is_array($gId)){
                        foreach($gId as $gv){
                            $gfilter['goods_id'][] = $gv['goods_id'];
                        }
                        if(empty($gfilter))
                        $gfilter['goods_id'] = '-1';
                    }
                }else{

                    $gfilter = $arr;
                }
                if($aAdj = $objProducts->getList($cols,$gfilter,0,-1)){
                    $aGoods['adjunct'][$key]['items'] = $aAdj;
                }else{
                    unset($aGoods['adjunct'][$key]);
                }
            }
        }


        if( $aGoods['product'] ){
            foreach($aGoods['product'] as $pkey => $p){
                if( $p['status'] == 'false' ){
                    unset( $aGoods['product'][$pkey] );
                    continue;
                }
                if( $p['spec_desc']['spec_private_value_id'] )
                foreach($p['spec_desc']['spec_private_value_id'] as $key=>$spec_private_value_id){
                    $used_spec[$spec_private_value_id] = 1;
                }
            }
        }
        $aGoods['used_spec'] = $used_spec;
        if(!$aGoods || $aGoods['disabled'] == 'true' || empty($aGoods['product'])){
            $this->_response->set_http_response_code(404)->send_headers();
            exit;
        }
        $objCat = &$this->app->model('goods_cat');
        $aCat = $objCat->dump($aGoods['category']['cat_id'],'cat_name,addon');
        $aCat['addon'] = unserialize($aCat['addon']);
        if($aGoods['seo']['meta_keywords']){
            if(empty($this->keyWords))
            $this->keyWords = $aGoods['seo']['meta_keywords'];
        }else{
            if(trim($aCat['addon']['meta']['keywords'])){
                $this->keyWords = trim($aCat['addon']['meta']['keywords']);
            }
        }
        if($aGoods['seo']['meta_description']){
            $this->metaDesc = $aGoods['seo']['meta_description'];
        }else{
            if(trim($aCat['addon']['meta']['description'])){
                $this->metaDesc = trim($aCat['addon']['meta']['description']);
            }
        }
        $tTitle=(empty($aGoods['seo']['seo_title']) ? $aGoods['name'] : $aGoods['seo']['seo_title']).(empty($aCat['cat_name'])?"":" - ".$aCat['cat_name']);
        if(empty($this->title))
        $this->title = $tTitle;

        //初始化货品

        if(!empty($aGoods['product'])){
            foreach($aGoods['product'] as $key => $products){
                $a = array();
                if( $products['props']['spec'] )
                foreach($products['props']['spec'] as $k=>$v){
                    $a[] = trim($k).':'.trim($v);
                }
                $aGoods['product'][$key]['params_tr'] = implode('-',$a);
                $aPdtIds[] = $products['product_id'];
                if($aGoods['price'] > $products['price']){
                    $aGoods['price'] = $products['price'];//前台默认进来显示商品的最小价格
                }
            }
        }else{
            $aPdtIds[] = $aGoods['product_id'];
        }
        if($this->app->getConf('site.show_mark_price')=='true'){
            $aGoods['setting']['mktprice'] = $this->app->getConf('site.show_mark_price');
            if( !isset( $aGoods['mktprice'] ) )
                $aGoods['mktprice'] = $objProducts->getRealMkt($aGoods['price']);
        }else{
            $aGoods['setting']['mktprice'] = 0;
        }
        $aGoods['setting']['saveprice'] = $this->app->getConf('site.save_price');
        $aGoods['setting']['buytarget'] = $this->app->getConf('site.buy.target');
        $aGoods['setting']['score'] = $this->app->getConf('point.get_policy');
        $aGoods['setting']['scorerate'] = $this->app->getConf('point.get_rate');
        if($aGoods['setting']['score'] == 1){
            $aGoods['score'] = intval($aGoods['price'] * $aGoods['setting']['scorerate']);
        }

        /*--------------规格关联商品图片--------------*/
        if (!empty($specImg)){
            $tmpImgAry=explode("_",$specImg);
            if (is_array($tmpImgAry)){
                foreach($tmpImgAry as $key => $val){
                    $tImgAry = explode("@",$val);
                    if (is_array($tImgAry)){
                          $spec[$tImgAry[0]]=$val;
                          $imageGroup[]=substr($tImgAry[1],0,strpos($tImgAry[1],"|"));
                          $imageGstr .= substr($tImgAry[1],0,strpos($tImgAry[1],"|")).",";
                          $spec_value_id = substr($tImgAry[1],strpos($tImgAry[1],"|")+1);
                          if ($aGoods['specVdesc'][$tImgAry[0]]['value'][$spec_value_id]['spec_value'])
                            $specValue[]=$aGoods['specVdesc'][$tImgAry[0]]['value'][$spec_value_id]['spec_value'];
                          if ($aGoods['FlatSpec']&&array_key_exists($tImgAry[0],$aGoods['FlatSpec']))
                              $aGoods['FlatSpec'][$tImgAry[0]]['value'][$spec_value_id]['selected']=true;
                          if ($aGoods['SelSpec']&&array_key_exists($tImgAry[0],$aGoods['SelSpec']))
                              $aGoods['SelSpec'][$tImgAry[0]]['value'][$spec_value_id]['selected']=true;
                    }
                }
                if ($imageGstr){
                    $imageGstr=substr($imageGstr,0,-1);
                }
            }

            /****************设置规格链接地址**********************/
            if (is_array($aGoods['specVdesc'])){
                foreach($aGoods['specVdesc'] as $gk => $gv){
                    if (is_array($gv['value'])){
                        foreach($gv['value'] as $gkk => $gvv){
                            if(is_array($spec)){
                                $specId = substr($gvv['spec_goods_images'],0,strpos($gvv['spec_goods_images'],"@"));
                                foreach($spec as $sk => $sv){
                                    if ($specId != $sk){
                                        $aGoods['specVdesc'][$gk]['value'][$gkk]['spec_goods_images'].="_".$sv;
                                        if ($aGoods['FlatSpec']&&array_key_exists($gk,$aGoods['FlatSpec'])){
                                            $aGoods['FlatSpec'][$gk]['value'][$gkk]['spec_goods_images'].="_".$sv;
                                        }
                                        if ($aGoods['SelSpec']&&array_key_exists($gk,$aGoods['SelSpec'])){
                                            $aGoods['SelSpec'][$gk]['value'][$gkk]['spec_goods_images'].="_".$sv;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            /*************************************/


            //页面提示所选规格名称
            $this->pagedata['SelectSpecValue'] = array('value'=>implode("、",$specValue),'selected'=>1);
        }
        else{
            /*
            if (is_array($aGoods['FlatSpec'])&&count($aGoods['FlatSpec'])>0){
                foreach($aGoods['FlatSpec'] as $agk => $agv){
                    $specValue[]=$agv['name'];
                }
            }
            if (is_array($aGoods['SelSpec'])&&count($aGoods['SelSpec'])>0){
                foreach($aGoods['SelSpec'] as $agk => $agv){
                    $specValue[]=$agv['name'];
                }
            }
            by smith*/
             if (is_array($aGoods['spec'])&&count($aGoods['spec'])>0){
                 foreach($aGoods['spec'] as $agk => $agv){
                    $specValue[]=$agv['spec_name'];
                }
             }
            $this->pagedata['SelectSpecValue'] = array('value'=>implode("、",(array)$specValue),'selected'=>0);
        }
        $this->pagedata['specShowItems'] =$specValue;
        /*--------------*/
        //$gImages=$this->goodspics($gid,$imageGroup,$imageGstr);
        if (is_array($gImages)&&count($gImages)>0){
            $this->pagedata['images']['gimages'] = $gImages;
        }
        else{
            /*-------------商品图片--------------*/
            /*$gimage = &$this->app->model('gimages');
            $this->pagedata['images']['gimages'] = $gimage->dump($gid,'*');*/
            /*----------------- 8< --------------*/
        }



        /********-------------------*********/
        foreach($aGoods['product'] as $product_id=>$product){
            if( $product['member_lv_price'] )
            foreach($product['member_lv_price'] as $k=>$v){
                $mprice[$v['level_id']] = $v['price'];
            }
            $product2spec[$product_id] = array(
                    'bn'=>$product['bn'],
                    'price'=>$product['price']['price']['current_price'],
                    'mktprice'=>$product['price']['mktprice']['price'],
                    'mprice'=>$mprice,
                    'store'=>$product['store'],
                    'weight'=>$product['weight'],
                    'spec_private_value_id'=>$product['spec_desc']['spec_private_value_id'],
                );
            if( $product['spec_desc']['spec_private_value_id'] )
            foreach($product['spec_desc']['spec_private_value_id'] as $k=>$v){
                $spec2product[$v]['product_id'][] = $product_id;
                $spec2product[$v]['images'] = array();
            }
        }

        $aGoods['product2spec'] = json_encode( $product2spec );

        $aGoods['spec2product'] = json_encode( $spec2product );

        $this->pagedata['goods'] = $aGoods;

        if ($this->pagedata['goods']['product']){
            $priceArea = array();

            if ($_COOKIE['MLV'])
                $MLV = $_COOKIE['MLV'];
            else{
                $level=&$this->app->model('member_lv');
                $MLV=$level->get_default_lv();
            }
            if ($MLV){
                foreach($this->pagedata['goods']['product'] as $gpk => $gpv){
                   $priceArea[]=$gpv['price']['price']['current_price'];//销售价区域
                   $mktpriceArea[]=$gpv['price']['mktprice']['price'];//市场价区域
                }
                if (count($priceArea)>1){
                    $minprice = min($priceArea);
                    $maxprice = max($priceArea);
                    if ($minprice<>$maxprice){
                        $this->pagedata['goods']['minprice'] = $minprice;
                        $this->pagedata['goods']['maxprice'] = $maxprice;
                    }
                }
                if (count($mktpriceArea)>1){
                    $mktminprice = min($mktpriceArea);
                    $mktmaxprice = max($mktpriceArea);
                    if ($mktminprice<>$mktmaxprice){
                        $this->pagedata['goods']['minmktprice'] = $mktminprice;
                        $this->pagedata['goods']['maxmktprice'] = $mktmaxprice;

                    }
                }
            }
          //计算货品冻结库存总和
            foreach($this->pagedata['goods']['product'] as $key => $val){
                $totalFreez += $val['freez'];
            }

        }
        else{
            $totalFreez = $this->pagedata['goods']['freez'];
        }
        $oMlv = $this->app->model('member_lv');
        $mLevelList = $oMlv->getList('*','',0,-1);
        $this->pagedata['mLevel'] = $mLevelList;
        $aData = $this->get_current_member();
        if(!$aData['member_id']){
            $this->pagedata['login'] = 'nologin';
        }
###############
/**** begin 商品评论 ****/
        $aComment['switch']['ask'] = $this->app->getConf('comment.switch.ask');
        $aComment['switch']['discuss'] = $this->app->getConf('comment.switch.discuss');
        foreach($aComment['switch'] as $item => $switchStatus){
            if($switchStatus == 'on'){
                $objComment= kernel::single('b2c_message_disask');
                $commentList = $objComment->getGoodsIndexComments($gid,$item);
                $aComment['list'][$item] = $commentList['data'];
                $aComment[$item.'Count'] = $commentList['total'];
                $aId = array();
                if ($commentList['total']){
                    foreach($aComment['list'][$item] as $rows){
                        $aId[] = $rows['comment_id'];
                    }
                    if(count($aId)) $aReply = $objComment->getCommentsReply($aId, true);
                    reset($aComment['list'][$item]);
                    foreach($aComment['list'][$item] as $key => $rows){
                        foreach($aReply as $rkey => $rrows){
                            if($rows['comment_id'] == $rrows['for_comment_id']){
                                $aComment['list'][$item][$key]['items'][] = $aReply[$rkey];
                            }
                        }
                        reset($aReply);
                    }
                }else{
                    $aComment['null_notice'][$item] = $this->app->getConf('comment.null_notice.'.$item);;
                }
            }
        }
        /**** begin 相关商品 ****/
        $aLinkId['goods_id'] = array();
        foreach($objGoods->getLinkList($gid) as $rows){
            if($rows['goods_1']==$gid) $aLinkId['goods_id'][] = $rows['goods_2'];
            else $aLinkId['goods_id'][] = $rows['goods_1'];
        }
        if(count($aLinkId['goods_id'])>0){
            $aLinkId['marketable'] = 'true';
            $this->pagedata['goods']['link'] = $objGoods->getList('*',$aLinkId,0,500);
            $this->pagedata['goods']['link_count'] = count($aLinkId['goods_id']);
        }
        /**** end 相关商品 ****/


        $this->pagedata['comment'] = $aComment;

        /**** end 商品评论 ****/
#####################################
        $cur = app::get('ectools')->model('currency');
        $this->pagedata['readingGlass'] = $this->app->getConf('site.reading_glass');
        $this->pagedata['readingGlassWidth'] = $this->app->getConf('site.reading_glass_width');
        $this->pagedata['readingGlassHeight'] = $this->app->getConf('site.reading_glass_height');
        $this->pagedata['goods']['product_freez'] = $totalFreez;
        $this->pagedata['promotionMsg'] = $promotionMsg;
        $this->pagedata['sellLog'] = $sellLogSetting;
        $this->pagedata['sellLogList'] = $sellLogList;
        $this->pagedata['money_format'] = json_encode($cur->getFormat($this->app->getConf('site.currency.defalt_currency')));
        $this->pagedata['askshow'] = $this->app->getConf('comment.verifyCode.ask');
        $this->pagedata['goodsBnShow'] = $this->app->getConf('goodsbn.display.switch');
        $this->pagedata['discussshow'] = $this->app->getConf('comment.verifyCode.discuss');
        $this->pagedata['showStorage'] = $this->app->getConf('site.show_storage');
        $this->pagedata['specimagewidth'] = $this->app->getConf('spec.image.width');
        $this->pagedata['specimageheight'] = $this->app->getConf('spec.image.height');
        $this->pagedata['goodsproplink'] = 1;
        //$this->pagedata['goodsproplink'] = $this->app->getConf('goodsprop.display.switch');
  //      $this->getGlobal($this->seoTag,$this->pagedata);

        $GLOBALS['pageinfo']['goods'] = &$GLOBALS['runtime']['goods_name'];
        $GLOBALS['pageinfo']['brand'] = &$GLOBALS['runtime']['brand'];
        $GLOBALS['pageinfo']['gcat'] = &$GLOBALS['runtime']['goods_cat'];
        if( !$aGoods['images'] ){
            $imageDefault = app::get('image')->getConf('image.set');
            $this->pagedata['goods']['images'][]['image_id'] = $imageDefault['M']['default_image'];
            $this->pagedata['goods']['image_default_id'] = $imageDefault['M']['default_image'];
        }else{
            foreach($aGoods['images'] as $key=>$val){
                if(isset($val['image_id'])){
                     $this->pagedata['goods']['image_default_id'] = $val['image_id'];
                }
            }
        }

        $this->pagedata['spec_default_pic'] = $this->app->getConf('spec.default.pic');
        $this->setSeo('site_product','index',$this->prepareSeoData($this->pagedata));
        $this->set_tmpl('product');
        $this->page('site/product/index.html');
    }

    function prepareSeoData($data){
        return array(
            'goods_name'=>$data['goods']['name'],
            'goods_brand'=>$data['goods']['brand']['brand_name'],
        );
    }

       /*
       @author litie@shopex.cn


       $gids like:  2,3,4,5,6,7

       @return like:
       [{"goods_id":"39","thumbnail_pic":"http:\/\/pic.shopex.cn\/pictures\/gimages\/77900fbf8fcc94de.jpg","small_pic":"http:\/\/pic.shopex.cn\/pictures\/gimages\/4d927b00ab29b199.jpg","big_pic":"http:\/\/pic.shopex.cn\/pictures\/gimages\/389e97389f1616f7.jpg"},{"goods_id":"42","thumbnail_pic":"http:\/\/pic.shopex.cn\/pictures\/gimages\/54d1c53bc455244f.jpg","small_pic":"http:\/\/pic.shopex.cn\/pictures\/gimages\/9dce731f131aab5e.jpg","big_pic":"http:\/\/pic.shopex.cn\/pictures\/gimages\/ac4420118e680927.jpg"}]
    */
    function picsJson(){
        $gids = explode(',',$_GET['gids']);

        if(!$gids)return '';
        $o = $this->app->model('goods');
        $imageDefault = app::get('image')->getConf('image.set');

        $data = $o->db_dump(current($gids),'image_default_id');
        if( !$data['image_default_id'] ){
            $data = base_storager::image_path( $imageDefault['S']['default_image'],'s' );
        }else{
            $img = base_storager::image_path($data['image_default_id'],'s' );
            if( $img )
                $data = $img;
            else
                $data = base_storager::image_path( $imageDefault['S']['default_image'],'s' );
        }
        echo json_encode($data);

    }

     function diff(){
        $imageDefault = app::get('image')->getConf('image.set');
        $comare=explode("|",$_COOKIE['S']['GCOMPARE']);

        foreach($comare as $ci){
           $ci = json_decode($ci,true);
           $gids[] = $ci['gid'];
        }

        $oGoods = &$this->app->model('goods');
         $aData = $this->get_current_member();
        if(!$aData['member_id']){
            $this->pagedata['login'] = 'nologin';
        }

        $this->pagedata['diff'] = $oGoods->diff($gids);

        foreach($this->pagedata['diff']['goods'] as $key=>$row){
             $this->pagedata['diff']['goods'][$key]['defaultImage'] = $imageDefault['S']['default_image'];
             $goods_name[] = $row['name'];
        }
        if(is_array($goods_name))
            $this->pagedata['goods']['name'] = implode(',',$goods_name);
        $this->page('site/product/diff.html');
    }


    function viewpic($goodsid, $selected='def'){
        $objGoods = &$this->app->model('goods');
        $o = &app::get('image')->model('image_attach');
        $dImg = $o->getList('*',array('target_id'=>$goodsid));
        $aGoods = $objGoods->dump($goodsid,'name');
        $this->pagedata['goods_name'] = urlencode(htmlspecialchars($aGoods['name'],ENT_QUOTES));
        $this->pagedata['goods_name_show'] = $aGoods['name'];
        $this->pagedata['company_name'] = str_replace("'","&apos;",htmlspecialchars($this->app->getConf('system.shopname')));
        if(!$dImg){
        $imageDefault = app::get('image')->getConf('image.set');
            $dImg[]['image_id'] = $imageDefault['L']['image_id'];
            /*
            $selected=0;
            $id=rand(0,10);
            $dImg[$id]=array(
                'gimage_id'=>$id,
                'goods_id'=>$goodsid,
                'small'=>($this->app->getConf('site.default_small_pic')),
                'big'=>($this->app->getConf('site.default_big_pic')),
                'thumbnail'=>($this->app->getConf('site.default_thumbnail_pic'))
            );*/
        }

        $this->pagedata['image_file'] = $dImg;
        if($selected=='def'){
            $selected=current($dImg);
            $selected=$selected['target_id'];
        }
        $this->pagedata['selected'] = $selected;
        $this->page('site/product/viewpic.html',true);

    }


    function photo(){
    }

    function pic(){
    }

    function gnotify($goods_id=0,$product=0){
        if($_POST['goods']['goods_id']){
            $goods_id = $_POST['goods']['goods_id'];
            $product_id = $_POST['goods']['product_id'];
        }
        $this->id =$goods_id;
        $objGoods = &$this->app->model('goods');
        $aProduct = $objGoods->getProducts($goods_id, $product_id);
        $this->pagedata['goods'] = $aProduct[0];
        if($this->member[member_id]){
            #$objMember = &$this->system->loadModel('member/member');
            #$aMemInfo = $objMember->getFieldById($this->member[member_id], array('email'));
            $this->pagedata['member'] = $aMemInfo;
        }

        $this->page('site/product/gnotify.html');
    }

    function toNotify(){
        $back_url = $this->gen_url(array('app'=>'b2c','ctl'=>'site_product','act'=>'index','args'=>array($_POST['item'][0]['goods_id'])));
        $member_goods = $this->app->model('member_goods');
        if($member_goods->check_gnotify($_POST)){
            $this->splash('failed',$back_url,__('不能重复登记'));
        }
        else{
            $member_data = $this->get_current_member();
            if($member_goods->add_gnotify($member_data['member_id']?$member_data['member_id']:null,$_POST['item'][0]['goods_id'],$_POST['item'][0]['product_id'],$_POST['email'])){
            $this->splash('success',$back_url,__('登记成功'));
        }
        else{
           $this->splash('failed',$back_url,__('登记失败'));
        }
          }
        /*
        $this->begin($this->app->mkUrl("index"));
        $this->_verifyMember(false);
        $aTemp = array();
        $oNotify = $this->app->model('member_goods');
        if(!ereg("^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+",$_POST['email']))
                $this->end(false, __('邮箱格式错误'));
        else{
                foreach($_POST['item'] as $key=>$val){

                    $aTemp['goods_id'] = $val['goods_id'];
                    $aTemp['product_id'] = $val['product_id'];
                    $aTemp['member_id'] = isset($this->member['member_id']) && $this->member['member_id'] != ''?$this->member['member_id']:0;
                    $aTemp['email'] = $_POST['email']!=''? $_POST['email'] : $this->member['email'];
                    $aTemp['creat_time'] = time();

                    $oNotify->add_gnotify($aTemp);
                }

                $this->end(true, __('提交成功'));

        }*/
    }

    function goodspics($goodsId,$images=array(),$imgGstr=''){
        $Goods=&$this->app->model('goods/gimage');
        $objGoods = &$this->app->model('trading/goods');
        $gimg=$_POST['gimages'];
        $goodsId=$_POST['goodsId'];
        if ($gimg){
            $tmpGimg=explode(",",$_POST['gimages']);
            if ($tmpGimg){
                foreach($tmpGimg as $key => $val){
                    if (!$val)
                        unset($tmpGimg[$key]);
                }
                $tmpImage=$Goods->get_by_gimage_id($goodsId,$tmpGimg);
            }
            $this->pagedata['imgtype'] = 'spec';
       }
        else{
            $tmpImage = $Goods->get_by_goods_id($goodsId);
        }
        $this->pagedata['images']['gimages']=$tmpImage;
        $this->pagedata['goods'] = $objGoods->getGoods($goodsId);
        $this->__tmpl='product/goodspics.html';
        $this->output();
    }

    function get_brand($result){
        return $result['goods']['brand'];
    }
    function get_goods_name($result){
        return $result['goods']['name'];
    }
    function get_goods_bn($result){
        return $result['goods']['bn'];
    }
    function get_goods_cat($result){
        //$pcat=$this->app->model('goods/productCat');
        //$row=$pcat->instance($result['goods']['cat_id'],'cat_name');
         //return $row['cat_name'];
    }
    function get_goods_intro($result){
        $intro= strip_tags($result['goods']['intro']);
        if (strlen($intro)>50)
            $intro=substr($intro,0,50);
        return $intro;
    }
    function get_goods_brief($result){
        $brief= strip_tags($result['goods']['brief']);
        //$brief=preg_split('/(<[^<>]+>)/',$result['goods']['brief'],-1);
        if (strlen($brief)>50)
            $brief=substr($brief,0,50);
        return $brief;
    }
    function get_brand_kw($result){
        $brand=$this->app->model('goods/brand');
        $row=$brand->instance($result['goods']['brand_id'],'brand_keywords');
        return $row['brand_keywords'];
    }
    function get_goods_kw($result){
        /*
        $goods=$this->app->model('trading/goods');
        $row=$goods->getKeywords($result['goods']['goods_id']);
        if ($row){
            foreach($row as $key => $val){
                $tmpRow[]=$val['keyword'];
            }
            return implode(",",$tmpRow);
        }*/
            return;
    }
    function get_goods_price($result){
        return $result['goods']['price'];
    }
    function get_update_time($result){
        return date("c",$result['goods']['last_modify']);
    }

    function recooemd(){
        $back_url = $this->gen_url(array('app'=>'b2c','ctl'=>'site_product','act'=>'index','arg'=>$_POST['goods_id']));
        $app = app::get('desktop');
        $aTmp['usermail'] = $app->getConf('email.config.usermail');
        $aTmp['smtpport'] = $app->getConf('email.config.smtpport');
        $aTmp['smtpserver'] = $app->getConf('email.config.smtpserver');
        $aTmp['smtpuname'] = $app->getConf('email.config.smtpuname');
        $aTmp['smtppasswd'] = $app->getConf('email.config.smtppasswd');
        $aTmp['sendway'] = $app->getConf('email.config.sendway');
        $aTmp['acceptor'] = $user_email;     //收件人邮箱
        $aTmp['shopname'] = $this->app->getConf('system.shopname');
        $acceptor = $_POST['email'];     //收件人邮箱
        $subject = __("来自[").$_POST['name'].__("]的商品推荐");
        $url = &app::get('site')->router()->gen_url(array('app'=>'b2c','ctl'=>'site_product','full'=>1,'act'=>'index','arg'=>$_POST['goods_id']));
        $body = __("尊敬的客户,您的好友").$_POST['name'].__(',为您推荐了一款商品,请您点击查看').'<a href='.$url.'>'.$_POST['goods_name'].'</a>';
        $email = kernel::single('desktop_email_email');
        if ($email->ready($aTmp)){
        $res = $email->send($acceptor,$subject,$body,$aTmp);
        if ($res) {
            $this->splash('success',$back_url,__('发送成功'));
        }
        else{
           $this->splash('failed',$back_url,__('发送失败,请联系管理员'));
        }
      }
      else{
          $this->splash('failed',$back_url,__('发送失败,请联系管理员'));
      }
    }
}
