<?php
class ctl_product extends shopPage{

    var $_call = 'call';
    var $type = 'goods';

    function call(){
        $args = func_get_args();
        $action = array_shift($args);
        if(method_exists($this,$action)){
            call_user_func_array(array(&$this,$action),$args);
        }else{
            $objSchema = $this->system->loadModel('goods/schema');
            $gid = array_shift($args);
            if(!is_int($gid)) {
                echo 'Invalid Schema calling';
                die();
            }
            $objSchema->runFunc($gid,$action,$args);
        }
    }

    function album(){
        echo '相册';
        return;
    }
    function getVirtualCatById($cat_id=0){
        $vobjCat = $this->system->loadModel('goods/virtualcat');
        $xml = $this->system->loadModel('utility/xml');
        $result=$vobjCat->getVirtualCatById(intval($cat_id));

        $searchtools = $this->system->loadModel('goods/search');
        foreach($result as $k=>$v){
            $filter=$vobjCat->_mkFilter($result[$k]['filter']);
            $cat_id=$filter['cat_id'];
            $filter=$searchtools->encode($filter);
            $result[$k]['url']=$this->system->mkUrl('gallery',$this->system->getConf('gallery.default_view'),array(implode(',',$cat_id),$filter,'0','','',$result[$k]['virtual_cat_id']));
        }
        echo json_encode($result);
    }
    function index($gid,$specImg='',$spec_id='') {
        $this->id = $gid;
        $member_lv = intval($this->system->request['member_lv']);
        $objProduct = $this->system->loadModel('goods/products');
        $objGoods = $this->system->loadModel('trading/goods');
        if(!$aGoods = $objGoods->getGoods($gid,$member_lv)){
            $this->system->responseCode(404);
        }

        if($aGoods['goods_type'] == 'bind'){    //如果捆绑商品跳转到捆绑列表
            $this->redirect('package','index');
            exit;
        }
        if(!$aGoods || $aGoods['marketable'] == 'false' || $aGoods['disabled'] == 'true' || (empty($aGoods['products']) && empty($aGoods['product_id']))){
            $this->system->error(404);
            exit;
        }


        $objCat = $this->system->loadModel('goods/productCat');
        $aCat = $objCat->getFieldById($aGoods['cat_id'], array('cat_name','addon'));
        $aCat['addon'] = unserialize($aCat['addon']);
        if($aGoods['seo']['meta_keywords']){
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
        $this->title = $tTitle;
        $objPdtFinder = $this->system->loadModel('goods/finderPdt');
        foreach($aGoods['adjunct'] as $key => $rows){    //loop group
            if($rows['set_price'] == 'minus'){
                $cols = 'product_id,goods_id,name, pdt_desc, store, freez, price, price-'.intval($rows['price']).' AS adjprice';
            }else{
                $cols = 'product_id,goods_id,name, pdt_desc, store, freez, price, price*'.($rows['price']?$rows['price']:1).' AS adjprice';
            }
            if($rows['type'] == 'goods'){
                if(!$rows['items']['product_id']) $rows['items']['product_id'] = array(-1);
                $arr = $rows['items'];
            }else{
                parse_str($rows['items'].'&dis_goods[]='.$gid, $arr);
            }
            if($aAdj = $objPdtFinder->getList($cols, $arr,0,-1)){
                $aAdjGid = array();
                foreach($aAdj as $item){
                    $aAdjGid['goods_id'][] = $item['goods_id'];
                }
                if(!empty($aAdjGid)){
                    foreach($objProduct->getList('marketable,disabled',$aAdjGid,0,1000) as $item){
                        $aAdjGid[$item['goods_id']] = $item;
                    }
                    foreach($aAdj as $k => $item){
                        $aAdj[$k]['marketable'] = $aAdjGid[$item['goods_id']]['marketable'];
                        $aAdj[$k]['disabled'] = $aAdjGid[$item['goods_id']]['disabled'];
                    }
                }
                $aGoods['adjunct'][$key]['items'] = $aAdj;
            }else{
                unset($aGoods['adjunct'][$key]);
            }
        }
        $smarty = &$this->system->loadModel('system/frontend');
        $smarty->register_function("selector", array(&$this,'_selector'));
        //初始化货品
        if(!empty($aGoods['products'])){
            foreach($aGoods['products'] as $key => $products){
                $a = array();
                foreach($products['props']['spec'] as $k=>$v){
                    $a[] = trim($k).':'.trim($v);
                }
                $aGoods['products'][$key]['params_tr'] = implode('-',$a);
                $aPdtIds[] = $products['product_id'];
                if($aGoods['price'] > $products['price']){
                    $aGoods['price'] = $products['price'];//前台默认进来显示商品的最小价格
                }
            }
        }else{
            $aPdtIds[] = $aGoods['product_id'];
        }
        if($this->system->getConf('site.show_mark_price')){
            $aGoods['setting']['mktprice'] = $this->system->getConf('site.market_price');
        }else{
            $aGoods['setting']['mktprice'] = 0;
        }
        $aGoods['setting']['saveprice'] = $this->system->getConf('site.save_price');
        $aGoods['setting']['buytarget'] = $this->system->getConf('site.buy.target');
        $aGoods['setting']['score'] = $this->system->getConf('point.get_policy');
        $aGoods['setting']['scorerate'] = $this->system->getConf('point.get_rate');
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
            $this->pagedata['SelectSpecValue'] = array('value'=>implode("、",$specValue),'selected'=>0);
        }
        /*--------------*/
        //$gImages=$this->goodspics($gid,$imageGroup,$imageGstr);
        if (is_array($gImages)&&count($gImages)>0){
            $this->pagedata['images']['gimages'] = $gImages;
        }
        else{
            /*-------------商品图片--------------*/
            $gimage = $this->system->loadModel('goods/gimage');
            $this->pagedata['images']['gimages'] = $gimage->get_by_goods_id($gid);
            /*----------------- 8< --------------*/
        }
        /********-------------------*********/
        $aGoods['product2spec'] = json_encode( $aGoods['product2spec'] );
        $aGoods['spec2product'] = json_encode( $aGoods['spec2product'] );
        $this->pagedata['goods'] = $aGoods;
        if ($this->pagedata['goods']['products']){
            $priceArea = array();
            if ($_COOKIE['MLV'])
                $MLV = $_COOKIE['MLV'];
            else{
                $level=$this->system->loadModel('member/level');
                $MLV=$level->getDefauleLv();
            }
            if ($MLV){
                foreach($this->pagedata['goods']['products'] as $gpk => $gpv){
                   $priceArea[]=$gpv['mprice'][$MLV];
                }
                if (count($priceArea)>1){
                    $minprice = min($priceArea);
                    $maxprice = max($priceArea);
                    if ($minprice<>$maxprice){
                        $this->pagedata['goods']['minprice'] = $minprice;
                        $this->pagedata['goods']['maxprice'] = $maxprice;
                    }
                }
            }
        }
        $mLevelList = $objProduct->getProductLevel($aPdtIds);
        $this->pagedata['mLevel'] = $mLevelList;

        /**** begin 商品品牌 ****/
        if($this->pagedata['goods']['brand_id'] > 0){
            $brandObj = $this->system->loadModel('goods/brand');
            $aBrand = $brandObj->getFieldById($this->pagedata['goods']['brand_id'], array('brand_name'));
        }
        $this->pagedata['goods']['brand_name'] = $aBrand['brand_name'];
        /**** begin 商品品牌 ****/

        /**** begin 商品评论 ****/
        $aComment['switch']['ask'] = $this->system->getConf('comment.switch.ask');
        $aComment['switch']['discuss'] = $this->system->getConf('comment.switch.discuss');
        foreach($aComment['switch'] as $item => $switchStatus){
            if($switchStatus == 'on'){
                $objComment= $this->system->loadModel('comment/comment');
                $commentList = $objComment->getGoodsIndexComments($gid, $item);
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
                    $aComment['null_notice'][$item] = $this->system->getConf('comment.null_notice.'.$item);;
                }
            }
        }
        $this->pagedata['comment'] = $aComment;
        /**** end 商品评论 ****/

        /**** begin 相关商品 ****/
        $aLinkId['goods_id'] = array();
        foreach($objGoods->getLinkList($gid) as $rows){
            if($rows['goods_1']==$gid) $aLinkId['goods_id'][] = $rows['goods_2'];
            else $aLinkId['goods_id'][] = $rows['goods_1'];
        }
        if(count($aLinkId['goods_id'])>0){
            $aLinkId['marketable'] = 'true';
            $objProduct = $this->system->loadModel('goods/products');
            $this->pagedata['goods']['link'] = $objProduct->getList('*',$aLinkId,0,500,$count);
            $this->pagedata['goods']['link_count'] = $count;
        }
        /**** end 相关商品 ****/

        //更多商品促销活动
        $PRICE = $this->pagedata['goods']['price'];//todo 此处PRICE 为会员价格,需要统一接口
        $oPromotion = $this->system->loadModel('trading/promotion');
        $aPmt = $oPromotion->getGoodsPromotion($gid, $this->pagedata['goods']['cat_id'], $this->pagedata['goods']['brand_id'], $member_lv);

        if ($aPmt){
            $this->pagedata['goods']['pmt_id'] = $aPmt['pmt_id'];
            $this->pagedata['promotions'] = $oPromotion->getPromotionList($aPmt['pmta_id']);
            $aTrading = array (
                'price' => $this->pagedata['goods']['price'],
                'score' => $this->pagedata['goods']['score'],
                'gift'  => array(),
                'coupon' => array()
            );
            $oPromotion->apply_single_pdt_pmt($aTrading, unserialize($aPmt['pmt_solution']),$member_lv);
            $oGift = $this->system->loadModel('trading/gift');
            if (!empty($aTrading['gift'])) {
                $this->pagedata['gift'] = $oGift->getGiftByIds($aTrading['gift']);
            }
            $oCoupon = $this->system->loadModel('trading/coupon');
            if (!empty($aTrading['coupon'])) {
                $this->pagedata['coupon'] = $oCoupon->getCouponByIds($aTrading['coupon']);
            }
            $this->pagedata['trading'] = $aTrading;
        }
        $oPackage = $this->system->loadModel('trading/package');
        if (!empty($aPdtIds)) {
            $aPkgList = $oPackage->findPmtPkg($aPdtIds);
            foreach($aPkgList as $k => $row){
                $aPkgList[$k]['items'] = $oPackage->getPackageProducts($row['goods_id']);
            }
            $this->pagedata['package'] = $aPkgList;
        }
        if($GLOBALS['runtime']['member_lv']<0){
            $this->pagedata['login'] = 'nologin';
        }
        $cur = $this->system->loadModel('system/cur');
        $this->pagedata['readingGlass'] = $this->system->getConf('site.reading_glass');
        $this->pagedata['readingGlassWidth'] = $this->system->getConf('site.reading_glass_width');
        $this->pagedata['readingGlassHeight'] = $this->system->getConf('site.reading_glass_height');

        $sellLogList = $objProduct->getGoodsSellLogList($gid,0,$this->system->getConf('selllog.display.listnum'));
        $sellLogSetting['display'] = array(
                'switch'=>$this->system->getConf('selllog.display.switch') ,
                'limit'=>$this->system->getConf('selllog.display.limit') ,
                'listnum'=>$this->system->getConf('selllog.display.listnum')
            );
        $this->pagedata['sellLog'] = $sellLogSetting;
        $this->pagedata['sellLogList'] = $sellLogList;
        $this->pagedata['money_format'] = json_encode($cur->getFormat($this->system->request['cur']));
        $this->pagedata['askshow'] = $this->system->getConf('comment.verifyCode.ask');
        $this->pagedata['goodsBnShow'] = $this->system->getConf('goodsbn.display.switch');
        $this->pagedata['discussshow'] = $this->system->getConf('comment.verifyCode.discuss');
        $this->pagedata['showStorage'] = intval($this->system->getConf('site.show_storage'));
        $this->pagedata['specimagewidth'] = $this->system->getConf('spec.image.width');
        $this->pagedata['specimageheight'] = $this->system->getConf('spec.image.height');
        $this->pagedata['goodsproplink'] = $this->system->getConf('goodsprop.display.switch');
        $this->pagedata['goodspropposition'] = $this->system->getConf('goodsprop.display.position');
        $this->output();
    }

    function viewpic($goodsid, $selected='def'){
        $objGoods = $this->system->loadModel('trading/goods');
        $gImg = $this->system->loadModel('goods/gimage');
        $dImg=$gImg->get_by_goods_id($goodsid);
        $aGoods = $objGoods->getFieldById($goodsid, array('name'));

        $this->pagedata['goods_name'] = urlencode(htmlspecialchars($aGoods['name'],ENT_QUOTES));
        $this->pagedata['goods_name_show'] = $aGoods['name'];
        $this->pagedata['company_name'] = str_replace("'","&apos;",htmlspecialchars($this->system->getConf('system.shopname')));
        if(!$dImg){
            $selected=0;
            $storager = $this->system->loadModel('system/storager');
            $id=rand(0,10);
            $dImg[$id]=array(
                'gimage_id'=>$id,
                'goods_id'=>$goodsid,
                'small'=>$storager->getUrl($this->system->getConf('site.default_small_pic')),
                'big'=>$storager->getUrl($this->system->getConf('site.default_big_pic')),
                'thumbnail'=>$storager->getUrl($this->system->getConf('site.default_thumbnail_pic'))
            );
        }
      
        $this->pagedata['image_file'] = $dImg;
        if($selected=='def'){
            $selected=current($dImg);
            $selected=$selected['gimage_id'];
        }
        $this->pagedata['selected'] = $selected;
        $this->__tmpl='product/viewpic.html';
        $this->output();
    }

    function diff(){
        $max_length=4;
        $error=0;
        $comare=explode(",",$_COOKIE['c_product']);

        if($_COOKIE['c_type']){
            if($_COOKIE['c_type'] !=intval($_POST[product_type])){
                $error=2;
                unset($_POST[product_compare]);
            }
        }else{
            $this->system->setCookie('c_type',$_POST[product_type]);
        }
        if($_POST[product_compare] && !array_search($_POST[product_compare],$comare)){
            if(count($comare)>=$max_length){
                $error=1;
            }else{
                if($_COOKIE['c_product']){
                    $this->system->setCookie('c_product',$_COOKIE['c_product'].",".intval($_POST[product_compare]));

                }else{
                    $this->system->setCookie('c_product',$_POST[product_compare]);
                }
                $comare=explode(",",$_COOKIE['c_product']);
            }
        }

        $oGoods = $this->system->loadModel('trading/goods');
        if($GLOBALS['runtime']['member_lv']<0){
            $this->pagedata['LOGIN'] = 'nologin';
        }

        $this->pagedata['diff'] = $oGoods->diff($comare,$error);

        $this->pagedata['setting']['buytarget'] = $this->system->getConf('site.buy.target');
        $this->output();
    }

    function photo(){
    }

    function pic(){
    }

    function gnotify($goods_id=0,$product_id=0){

        if($_POST['goods']['goods_id']){
            $goods_id = $_POST['goods']['goods_id'];
            $product_id = $_POST['goods']['product_id'];
        }
            $this->id =$goods_id;
        $objGoods = $this->system->loadModel('trading/goods');
        $aProduct = $objGoods->getProducts($goods_id, $product_id);

        $this->pagedata['goods'] = $aProduct[0];
        if($this->member[member_id]){
            $objMember = $this->system->loadModel('member/member');
            $aMemInfo = $objMember->getFieldById($this->member[member_id], array('email'));
            $this->pagedata['member'] = $aMemInfo;
        }

        $this->output();
    }

    function toNotify(){
        $this->begin($this->system->mkUrl("index"));
        $this->_verifyMember(false);
        $aTemp = array();
        $oNotify = $this->system->loadModel('goods/goodsNotify');
        foreach($_POST['item'] as $key=>$val){

            $aTemp['goods_id'] = $val['goods_id'];
            $aTemp['product_id'] = $val['product_id'];
            $aTemp['member_id'] = isset($this->member['member_id']) && $this->member['member_id'] != ''?$this->member['member_id']:0;
            $aTemp['email'] = $_POST['email']!=''? $_POST['email'] : $this->member['email'];
            $aTemp['creat_time'] = time();

            $oNotify->createNotify($aTemp);
        }
        $this->end(true, __('提交成功'));
    }

    function selllog($gid,$nPage){
        $nPage = $nPage?$nPage:1;
        $oPro = $this->system->loadModel('goods/products');
        $sellLogList = $oPro->getGoodsSellLogList($gid, $nPage-1 );
        $this->pagedata['sellLogList'] = $sellLogList;
        $this->pagedata['pager'] = array(
                'current'=> $nPage,
                'total'=> $sellLogList['page'],
                'link'=> $this->system->mkUrl('product','selllog', array($gid,($tmp = time()))),
                'token'=>$tmp);

        $this->id = $gid;
        $member_lv = intval($this->system->request['member_lv']);
        $objGoods = $this->system->loadModel('trading/goods');
        if(!$aGoods = $objGoods->getGoods($gid,$member_lv)){
            $this->system->responseCode(404);
        }
        if(!$aGoods || $aGoods['marketable'] == 'false' || $aGoods['disabled'] == 'true' || (empty($aGoods['products']) && empty($aGoods['product_id']))){
            $this->system->error(404);
            exit;
        }

        $objCat = $this->system->loadModel('goods/productCat');
        $aCat = $objCat->getFieldById($aGoods['cat_id'], array('cat_name','addon'));
        $aCat['addon'] = unserialize($aCat['addon']);
        if($aGoods['seo']['meta_keywords']){
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
        $this->title = (empty($aGoods['seo']['seo_title']) ? $aGoods['name'] : $aGoods['seo']['seo_title']).(empty($aCat['cat_name'])?"":" - ".$aCat['cat_name'].' » 销售记录');
        $this->output();
    }
    function goodspics($goodsId,$images=array(),$imgGstr=''){
        $Goods=$this->system->loadModel('goods/gimage');
        $objGoods = $this->system->loadModel('trading/goods');
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
        }
        else{
            $tmpImage = $Goods->get_by_goods_id($goodsId);
        }
        $this->pagedata['imgtype'] = 'spec';
        $this->pagedata['images']['gimages']=$tmpImage;
        $this->pagedata['goods'] = $objGoods->getGoods($goodsId);
        $this->__tmpl='product/goodspics.html';
        $this->output();
    }
    function _selector($params,&$smarty){
        if ($params['type']<>'b'){
            $args[0]='';
            $args[1] = 'tp,'.$params['type']."_".$params['key'].','.$params['value']."_p,0";
            $args[4] = 6;
            return $this->system->mkUrl('gallery',$smarty->_tpl_vars['curView'],$args);
        }
        else{
            $args[0]=$params['key'];
            /*$args[1]='b,'.$params['key'];
            $args[2]=0;
            $args[3]='';
            $args[4]=1;
            $args[5]='';*/
            return $this->system->mkUrl('brand',$smarty->_tpl_vars['curView'],$args);
        }
        
    }
}
?>
