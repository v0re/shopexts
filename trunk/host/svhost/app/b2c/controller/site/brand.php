<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */

class b2c_ctl_site_brand extends b2c_frontpage{

    var $seoTag=array('shopname','brand');

    public function showList($page=1){

        $pageLimit = 24;
        $oGoods=&$this->app->model('brand');
        $result=$oGoods->getList('*', '',($page-1)*$pageLimit,$pageLimit);
        $brandCount = $oGoods->count();

        $oSearch = &$this->app->model('search');
        foreach($result as $k=>$v){
            //$result[$k]['link']=$this->gen_url('gallery','index',array('',$oSearch->encode(array('brand_id'=>array($v['brand_id'])))));
        }
        $this->path[] = array('title'=>__('品牌专区'),'link'=>$this->gen_url(array('app'=>'b2c', 'ctl'=>'site_brand', 'act'=>'showlist','full'=>1)));
        $GLOBALS['runtime']['path'] = $this->path;
        //$sitemapts=&$this->app->model('sitemap');
        #$title=$sitemapts->getTitleByAction('brand:showList');
        $title=$title['title']?$title['title']:__('品牌');
        $this->pagedata['pager'] = array(
            'current'=>$page,
            'total'=>ceil($brandCount/$pageLimit),
            'link'=>$this->gen_url(array('app'=>'b2c', 'ctl'=>'site_brand', 'act'=>'index','full'=>1,'args'=>array(($tmp = time())))),
            'token'=>$tmp
            );
//        if($page > $this->pagedata['pager']['total']){
//            trigger_error(__('查询数为空'),E_USER_NOTICE);
//        }
        $this->pagedata['data'] = $result;
     //   $this->getGlobal($this->seoTag,$this->pagedata,1);
//print_R(123);exit;
        $this->page('site/brand/showList.html');
    }

    public function index($brand_id, $page=1) {
        #$oseo = &$this->app->model('system/seo');
        #$seo_info=$oseo->get_seo('brand',$brand_id);

        $oGoods=&$this->app->model('brand');
        $this->path[] = array('title'=>__('品牌专区'),'link'=>$this->gen_url(array('app'=>'b2c', 'ctl'=>'site_brand', 'act'=>'showlist','full'=>1)));

        $argu=array("brand_id","brand_name","brand_url","brand_desc","brand_logo","brand_setting");
        $argu=implode(",",$argu);
        //$result = $oGoods->getFieldById($brand_id,$argu);
        $result = $oGoods->getList($argu,array('brand_id'=>$brand_id));
        $result = $result[0];
//        $result['brand_setting'] = unserialize($result['brand_setting']);
        $this->set_tmpl('brand');
        if( $result['brand_setting']['brand_template'] )
            $this->set_tmpl_file($result['brand_setting']['brand_template']);
        $this->pagedata['data'] = $result;

        $view = $this->app->getConf('gallery.default_view');
        if($view=='index')
        $view='list';
        $this->pagedata['view'] = 'site/gallery/type/'.$view.'.html';

        $GLOBALS['runtime']['path'] = $this->path;
        $objGoods  = &$this->app->model('goods');
        $filter = array();
        if($mlv = $_COOKIE['MLV']){
            $filter['mlevel'] = $mlv;
        }
        $filter['brand_id'] = $brand_id;
        $filter['marketable'] = 'true';

        $pageLimit = 20;
        $start = ($page-1)*$pageLimit;

        $aProduct  = $objGoods->getList('*',$filter,$start,$pageLimit);

        $productCount = $objGoods->count($filter);
        $this->pagedata['count'] = $productCount;

        $this->pagedata['pager'] = array(
            'current'=>$page,
            'total'=>ceil($productCount/$pageLimit),
            'link'=>$this->gen_url(array('app'=>'b2c', 'ctl'=>'site_brand','full'=>1,'act'=>'index','args'=>array($brand_id,$tmp=time()))),
            'token'=>$tmp);
/*        if($page > $this->pagedata['pager']['total']){
            trigger_error(__('查询数为空'),E_USER_NOTICE);
        }*/

        if(is_array($aProduct) && count($aProduct) > 0){
            $setting['mktprice'] = $this->app->getConf('site.market_price');
            $setting['saveprice'] = $this->app->getConf('site.save_price');
            $setting['buytarget'] = $this->app->getConf('site.buy.target');
            $this->pagedata['setting'] = $setting;
            $this->pagedata['products'] = $aProduct;
        }

        $oSearch = &$this->app->model('search');
        $this->pagedata['link'] = $this->gen_url('gallery',$this->app->getConf('gallery.default_view'),array('',$oSearch->encode(array('brand_id'=>array($brand_id)))));
        $this->setSeo('site_brand','index',$this->prepareSeoData($this->pagedata));
        $this->page('site/brand/index.html');

    }

    function prepareSeoData($data){
        return array(
            'brand_name'=>$data['data']['brand_name'],
            'brand_url'=>$data['data']['brand_url'],
        );
    }

    private function get_brand(&$result,$list=0){
        if($list){
            foreach($result['data'] as $k => $v)
                $brandName[]=$v['brand_name'];
            return implode(",",$brandName);
        }else{
            return $result['data']['brand_name'];
        }
    }

    private function get_goods_amount(&$result,$list=0){
        return $result['count'];
    }

    private function get_brand_intro(&$result,$list=0){
        $brand_desc=preg_split('/(<[^<>]+>)/',$result['data']['brand_desc'],-1);
        if (strlen($brand_desc)>50)
            $brand_desc=substr($brand_desc,0,50);
        return $result['data']['brand_desc'];
    }

    private function get_brand_kw(&$result,$list=0){
        $brand = $this->app->model('goods/brand');
        $row=$brand->instance($result['data']['brand_id'],'brand_keywords');
        return $row['brand_keywords'];
    }
}
