<?php
class ctl_brand extends shopPage{
    function showList($page=1){
        $pageLimit = 24;
        $oGoods=$this->system->loadModel('goods/brand');
        //$result=$oGoods->getAll($page*$pageLimit,$pageLimit);
        $result=$oGoods->getList('*', '',($page-1)*$pageLimit,$pageLimit,$brandCount);
        
        $oSearch = $this->system->loadModel('goods/search');
        foreach($result as $k=>$v){
            $result[$k]['link']=$this->system->mkUrl('gallery','index',array('',$oSearch->encode(array('brand_id'=>array($v['brand_id'])))));
        }

        $this->path[]=array('title'=>'Brand');
        $this->pagedata['pager'] = array(
            'current'=>$page,
            'total'=>ceil($brandCount/$pageLimit),
            'link'=>$this->system->mkUrl('brand','showList',array(($tmp = time()))),
            'token'=>$tmp);
        if($page > $this->pagedata['pager']['total']){
            trigger_error('查询数为空',E_USER_NOTICE);
        }
        $this->pagedata['data'] = $result;
        
        $this->output();
    }
    function index($brand_id) {
        
        $oGoods=$this->system->loadModel('goods/brand');
        $argu=array("brand_id","brand_name","brand_url","brand_desc","brand_logo");
        $result= $oGoods->getFieldById($brand_id,$argu);
        $this->title=$result['brand_name'];
        $this->pagedata['data'] = $result;
        
        $this->path[] = array('title'=>'Brand','link'=>$this->system->mkUrl('brand','showList'));
        $this->path[] = array('title'=>$result['brand_name']);        
        
        $view = $this->system->getConf('gallery.default_view');
        if($view=='index') $view='list';
        $this->pagedata['view'] = 'gallery/type/'.$view.'.html';
        
        $objGoods  = &$this->system->loadModel('goods/products');

        //$aProduct  = $objGoods->getList(null,array('brand_id'=>$brand_id,'marketable'=>'true'),0,20,$count);
		$aProduct = $objGoods->getAll(null,array('brand_id'=>$brand_id,'marketable'=>'true'),$count);
        $this->pagedata['count'] = $count+0;
		//
		$oGtag = $this->system->loadModel('goods/gtag');
		$aTags = array();
		foreach($aProduct as $key=>$product){
			$tags = $oGtag->getTagsByGoodsId($product['goods_id']);
			//没有标签的不要显示
			if($tags != ''){
				$aProduct[$key] = $product;
				$aProduct[$key]['tags'] = $tags;
				$aTags = array_merge($aTags,explode(',',$tags));
			}
		}
		$aTags = array_unique($aTags);
		sort($aTags);
		$aTagGoods = array();
		foreach($aTags as $tagno=>$tagname){
			foreach($aProduct as $pkey=>$product){
				if(strstr($product['tags'],$tagname)){
					$aTagGoods[$tagno]['products'][$pkey] = $product;
				}
			}
		}
        //error_log(var_export($aTagGoods,true),3,__FILE__.".log");
		//
        if(is_array($aProduct) && count($aProduct) > 0){
            $objGoods->getSparePrice($aProduct, $GLOBALS['runtime']['member_lv']);
            $setting['mktprice'] = $this->system->getConf('site.market_price');
            $setting['saveprice'] = $this->system->getConf('site.save_price');
            $setting['buytarget'] = $this->system->getConf('site.buy.target');
            $this->pagedata['setting'] = $setting;
            $this->pagedata['products'] = $aProduct;
			#
			$this->pagedata['taglist'] = $aTags;
			$this->pagedata['taggoods'] = $aTagGoods;
			#
        }
        
        $oSearch = $this->system->loadModel('goods/search');
        $this->pagedata['link'] = $this->system->mkUrl('gallery','grid',array('',$oSearch->encode(array('brand_id'=>array($brand_id)))));
        $this->output();
    }
}
?>