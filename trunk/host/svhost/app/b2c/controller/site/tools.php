<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_ctl_site_tools extends b2c_frontpage{

    function __construct($app) {
        parent::__construct($app);
        $this->app = $app;
        $this->_response->set_header('Cache-Control', 'no-store');
    }

    function selRegion()
    {
        $arrGet = $this->_request->get_get();
        $path = $arrGet['path'];
        $depth = $arrGet['depth'];
        
        //header('Content-type: text/html;charset=utf8');
        $local = kernel::single('ectools_regions_select');
        $ret = $local->get_area_select(app::get('ectools'),$path,array('depth'=>$depth));
        if($ret){
            echo '&nbsp;-&nbsp;'.$ret;exit;
        }else{
            echo '';exit;
        }
    }

    function history(){
        $this->title=__('浏览过的商品');
        $this->page('site/tools/history.html');
    }

    
    function products(){
        $objGoods  = &$this->app->model('goods');

        $filter = array();
        foreach(explode(',',$_POST['goods']) as $gid){
            $filter['goods_id'][] = $gid;
         }

        $this->pagedata['products'] = $objGoods->getList('*,find_in_set(goods_id,"'.$_POST['goods'].'") as rank',$filter,0,-1,array('rank','asc'));
        $aData = $this->get_current_member();
        if(!$aData['member_id']){
            $this->pagedata['login'] = 'nologin';
        }
        $view = $this->app->getConf('gallery.default_view');
        if($view=='index') $view='list';
        $this->page('site/gallery/type/'.$view.'.html',true);
    }

}
