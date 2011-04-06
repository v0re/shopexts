<?php
class ctl_gallery extends shopPage{

    var $_call = 'index';
    var $type='goodsCat';
    
    function index($view,$cat_id=0,$urlFilter=null,$orderBy=0,$tab=null,$page=1,$cat_type=null) {
        if($orderBy==5 || $orderBy==6){
            $this->noCache = true;
        }
        if($cat_type){
            $this->type='virtualcat';
            $this->cat_type=$cat_type;
            $virtualCat=$this->system->loadModel('goods/virtualcat');
            $vcat=$virtualCat->instance($cat_type);
            parse_str($vcat['filter'],$type_filter);
        }
        $urlFilter=htmlspecialchars(urldecode($urlFilter));
        $cat_id=explode(",",$cat_id);
       
        if($cat_id){
            foreach($cat_id as $k=>$v){
                $cat_id[$k]=intval($v);
            }
        }
        //{{{初始化操作
        $this->id = implode(",",$cat_id);
        $pageLimit = 20;
        $this->pagedata['pdtPic']=array('width'=>100,'heigth'=>100);
        $this->pagedata['args'] = array(implode(",",$cat_id),urlencode($urlFilter),$orderBy,$tab,$page,$cat_type);
        $this->pagedata['curView'] = $view;
        $productCat = $this->system->loadModel('goods/productCat');
        if($cat_type){
            $this->pagedata['childnode'] = $virtualCat->getCatParentById($cat_type,$view);
        }
        else{
            $this->pagedata['childnode'] = $productCat->getCatParentById($cat_id,$view);
        }
        $brandGroup=$this->system->loadModel('goods/brand');
        $objGoods = $this->system->loadModel('goods/products');
    
        $brandResult=$brandGroup->getBrandGroup($cat_id);
        $this->productCat = &$productCat;
        $cat = $productCat->get($cat_id,$view,$type_filter['type_id']);
        if(!in_array($view,$cat['setting']['list_tpl'])){
           header('Location: '.$this->system->mkUrl('gallery',current($cat['setting']['list_tpl']),$this->pagedata['args']),true,301);
        }

        if($cat_type){
          
            $vcat['addon'] = unserialize($vcat['addon']);
            if(trim($vcat['addon']['meta']['keywords'])){
                $this->keyWords = trim($vcat['addon']['meta']['keywords']);
            }
            if(trim($vcat['addon']['meta']['description'])){
                $this->metaDesc = trim($vcat['addon']['meta']['description']);
            }
        }else{
            if(trim($cat['addon'])){
                $cat['addon'] = unserialize($cat['addon']);
                if(trim($cat['addon']['meta']['keywords'])){
                    $this->keyWords = trim($cat['addon']['meta']['keywords']);
                }
                if(trim($cat['addon']['meta']['description'])){
                    $this->metaDesc = trim($cat['addon']['meta']['description']);
                }
            }
        }

        if($this->system->getConf('system.seo.noindex_catalog'))
            $this->header .= '<meta name="robots" content="noindex,noarchive,follow" />';
    
        $searchtools = &$this->system->loadModel('goods/search');
        $path =array();
        $filter    = $searchtools->decode($urlFilter,$path,$cat);
        
        $this->filter = &$filter;
        if(!$cat_type){
            $this->title = $cat['cat_name'];
        }

        if(is_array($filter)){
            $filter = array_merge(array('cat_id'=>$cat_id,'marketable'=>'true'),$filter);
        }else{
            $filter = array('cat_id'=>$cat_id,'marketable'=>'true');
        }
        //--------获取类型关联的规格
        if ($vcat['type_id']){
            $type_id = $vcat['type_id'];
        }
        else{
            $type=$productCat->getFieldById($this->id,array('type_id'));
            $type_id=$type['type_id'];
        }
        $gType = $this->system->loadModel('goods/gtype');
        $SpecList = $gType->getSpec($type_id,1);
        //--------
        foreach($path as $p){
            $arg = unserialize(serialize($this->pagedata['args']));
            $arg[1] = $p['str'];
            $title = array();
            if(is_numeric($p['type'])){
                foreach($p['data'] as $i){
                    $name = $cat['props'][$p['type']]['options'][$i];
                    $title[] = $name?$name:$i;
                    $tip = $cat['props'][$p['type']]['name'];
                }
            }elseif($p['type']=='brand_id'){
                $brand = array();
                
                foreach($cat['brand'] as $b){
                    $brand[$b['brand_id']] = $b['brand_name'];
                }
                foreach($p['data'] as $i){
                    $title[] = $brand[$i];
                    $tip = "Brand";
                }
                unset($brand);
            }elseif(substr($p['type'],0,2)=='s_'){
                $spec = array();
                foreach($p['data'] as $spk => $spv){
                    $tmp=explode(",",$spv);
                    $tip = $SpecList[$tmp[0]]['name'];
                    $title[]=$SpecList[$tmp[0]]['spec_value'][$tmp[1]]['spec_value'];
                }
                $curSpec[$tmp[0]]=$tmp[1];
            }
            $title = implode(',',$title);
            if($title){
                $this->title=' '.$title;
                $this->path[] = array('title'=>" ".$title,'link'=>$this->system->mkUrl('gallery',$view,$arg),'tips'=>$tip);
            }
        }  
        //-----------
        if($this->system->getConf('system.seo.noindex_catalog'))
            $this->header .= '<meta name="robots" content="noindex,noarchive,follow" />';

        $filter['cat_id'] = $cat_id;
        $filter['goods_type'] = 'normal';
        $filter['marketable'] = 'true';
        //-----查找当前类别子类别的关联类型ID
        if ($urlFilter){
            if($vcat['type_id']){
                $filter['type_id']=$vcat['type_id'];
            }
        }
        //--------
        $this->pagedata['tabs'] = $cat['tabs'];
        $this->pagedata['cat_id'] = implode(",",$cat_id);
        $this->pagedata['views'] = $cat['setting']['list_tpl'];
        
        $this->pagedata['orderBy'] = $objGoods->orderBy();
        if($cat['tabs'][$tab]){
            parse_str($cat['tabs'][$tab]['filter'],$_filter);
            $filter = array_merge($filter,$_filter);
        }
        if($GLOBALS['runtime']['member_lv']){
            $filter['mlevel'] = $GLOBALS['runtime']['member_lv'];
        }
        if(!isset($this->pagedata['orderBy'][$orderBy])){
            $this->system->error(404);
        }else{
            $orderby = $this->pagedata['orderBy'][$orderBy]['sql'];
        }
        
        foreach($brandResult as $v=>$k){
            $brand_count[$k['brand_id']]['plus']=$k['brand_cat'];
        }
        $selector = array();
        $search = array();
        if((!is_array($cat_id) && $cat_id) || $cat_id[0] || $cat_type){
            $goods_relate=$objGoods->getList("*",$filter,0,-1,$c);
        }
        if ($goods_relate){
            unset($tmpSpecValue);
            foreach($goods_relate as $grk => $grv){
                if ($grv['spec_desc']){
                    $tmpSdesc=unserialize($grv['spec_desc']);
                    if(is_array($tmpSdesc)){
                        foreach($tmpSdesc as $tsk => $tsv){
                            foreach($tsv as $tk => $tv){
                                if (!in_array($tv['spec_value_id'],$tmpSpecValue))
                                $tmpSpecValue[]=$tv['spec_value_id'];
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
                /*
                $existsSV=0;
                foreach($spv['spec_value'] as $spvk => $spvv){
                    if (!in_array($spvk,$tmpSpecValue))
                        unset($spv['spec_value'][$spvk]);
                    else 
                        $existsSV=1;
                } 
                if ($existsSV){*/
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
                //}
            }
        } 
        $this->pagedata['SpecFlatList'] = $SpecFlatList;
        $this->pagedata['specimagewidth'] = $this->system->getConf('spec.image.width');
        $this->pagedata['specimageheight'] = $this->system->getConf('spec.image.height');
        /************************/
        if (is_array($cat['brand'])){
            foreach($cat['brand'] as $bk => $bv){
                $bCount=0;
                $brand = array('name'=>'Brand','value'=>array_flip($filter['brand_id']));
                foreach($goods_relate as $gk => $gv){
                    if ($gv['brand_id']){
                        if ($gv['brand_id']==$bv['brand_id']){
                            $bCount++;
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
        foreach($cat['props'] as $prop_id=>$prop){
            if($prop['search']=='select'){
                $prop['options'] = array_merge($prop['options']);
                $prop['value'] = $filter['p_'.$prop_id][0];
                $searchSelect[$prop_id] = $prop;
            }elseif($prop['search']=='input'){
                $prop['value'] = ($filter['p_'.$prop_id][0]);
                $searchInput[$prop_id] = $prop;
            }elseif($prop['search']=='nav'){
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
        foreach($cat['spec'] as $spec_id=>$spec_name){
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
        
        $selector['ordernum'] = $cat['ordernum'];
       
        $aProduct = $objGoods->getList(null,$filter,$pageLimit*($page-1),$pageLimit,$count,$orderby);
        $this->pagedata['mask_webslice'] = $this->system->getConf('system.ui.webslice')?' hslice':null;
        $this->pagedata['searchInput'] = &$searchInput;
        $this->pagedata['selectorExd'] = $selectorExd;
        $this->cat_id = $cat_id;
        $smarty = &$this->system->loadModel('system/frontend');
        $smarty->register_function("selector", array(&$this,'_selector')); 
        $this->pagedata['pager'] = array(
            'current'=>$page,
            'total'=>ceil($count/$pageLimit),
            'link'=>$this->system->mkUrl('gallery',$view,array(implode(',',$cat_id),urlencode($p['str']),$orderBy,$tab,($tmp = time()),$cat_type)),
            'token'=>$tmp);
        if($page != 1 && $page > $this->pagedata['pager']['total']){
            $this->system->error(404);
        }
        if(!count($aProduct)){
            $this->pagedata['emtpy_info']=stripslashes($this->system->getConf('errorpage.searchempty'));
        }
        $objImage = $this->system->loadModel('goods/gimage');
        $this->pagedata['searchtotal']=$count;
        if(is_array($aProduct) && count($aProduct) > 0){
            $objGoods->getSparePrice($aProduct, $GLOBALS['runtime']['member_lv']);
            if($this->system->getConf('site.show_mark_price')){
                $setting['mktprice'] = $this->system->getConf('site.market_price');
            }else{
                $setting['mktprice'] =0;
            }
            $setting['saveprice'] = $this->system->getConf('site.save_price');
            $setting['buytarget'] = $this->system->getConf('site.buy.target');
            $this->pagedata['setting'] = $setting;
            $this->pagedata['products'] = $aProduct;
        }
        if($GLOBALS['runtime']['member_lv']<0){
            $this->pagedata['LOGIN'] = 'nologin';
        }
        if($SpecSelList){
            $this->pagedata['SpecSelList'] = $SpecSelList;
        }
        if($searchSelect){
            $this->pagedata['searchSelect'] = &$searchSelect;
        }
        $this->pagedata['selector'] = &$selector;
        $this->pagedata['cat_type'] = $cat_type;
        $this->pagedata['search_array'] = implode("+",$GLOBALS['search_array']);
        $this->pagedata['_PDT_LST_TPL'] = 'file:'.$cat['tpl'];
        $this->pagedata['_MAIN_'] = 'gallery/index.html';
        $this->output();
    }

    function _selector($params, &$smarty){
        $filter = unserialize(serialize($this->filter));
        if(is_numeric($params['key'])){
            $data = &$filter['p_'.$params['key']];
        }elseif ($params['key']=="spec"){
            $tmp=explode(",",$params['value']);
            $data = &$filter['s_'.$tmp[0]];
        }else{
            $data = &$filter[$params['key']];
        }

        if($params['mod']=='append'){
            $data[] = $params['value'];
        }elseif($params['mod']=='remove'){
            $data = array_flip($data);
            unset($data[$params['value']]);
            $data = array_flip($data);
        }else{
            if ($params['key']=="spec"){
                $tmpData = explode(",",$params['value']);
                $data = array($tmpData[1]);
            }
            else
                $data = array($params['value']);
        }

        $searchtools = &$this->system->loadModel('goods/search');
        $args = unserialize(serialize($this->pagedata['args']));
        
        $args[1] = $searchtools->encode($filter);
        $args[4]=1;
        return $this->system->mkUrl('gallery',$smarty->_tpl_vars['curView'],$args);
    }
    
    function _get_schema_template($tpl_name, &$tpl_source, &$smarty) { 
        $tpl_source = file_get_contents(SCHEMA_DIR.$tpl_name.'/view/gallery.html');
        if (!is_bool($tpl_source)) {
                return true;
        } else {
                return false;
        }

    }

    function _get_schema_timestamp($tpl_name, &$tpl_timestamp, &$smarty) { 
        $tpl_timestamp = filemtime(SCHEMA_DIR.$tpl_name.'/view/gallery.html');
        if (!is_bool($tpl_timestamp)) { 
                return true; 
        } else { 
                return false; 
        } 
    }

    function _get_secure($tpl_name, &$smarty) { return true; } 
    function _get_trusted($tpl_name, &$smarty) {;} 
}
?>
