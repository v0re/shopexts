<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_ctl_admin_goods extends desktop_controller{

    var $workground = 'b2c_ctl_admin_goods';

    public $use_buildin_import = true;


    function index(){
        $this->finder('b2c_mdl_goods',array(
            'title'=>'商品列表',
            'actions'=>array(
                            array('label'=>'添加商品','icon'=>'add.gif','href'=>'index.php?app=b2c&ctl=admin_goods_editor&act=add','target'=>'_blank'),
                        ),'use_buildin_set_tag'=>true,'use_buildin_filter'=>true,'use_buildin_export'=>true,'allow_detail_popup'=>true,'use_view_tab'=>true,
            ));
    }

   function _views(){
        $mdl_goods = $this->app->model('goods');
        //已下架商品
        $filter = array('marketable'=>'false');
        $market_count = $mdl_goods->count($filter);
        $sub_menu[0] = array('label'=>__('已下架商品'),'optional'=>true,'filter'=>$filter,'addon'=>$market_count,'href'=>'index.php?app=b2c&ctl=admin_goods&act=index&view=0&view_from=dashboard');
        //缺货商品
        $filter = array('marketable'=>'true','nostore_sell'=>0,'store'=>0);
        $lack_goods = $mdl_goods->count($filter);
        $sub_menu[1] = array('label'=>__('缺货商品'),'optional'=>true,'filter'=>$filter,'addon'=>$lack_goods,'href'=>'index.php?app=b2c&ctl=admin_goods&act=index&view=1&view_from=dashboard');
        //库存报警
        $alert_num = $this->app->getConf('system.product.alert.num');
        $filter = array('_store_search'=>'lthan','store'=>$alert_num);
        $mdl_goods = $this->app->model('goods');        
        $alert_num_count = $mdl_goods->count($filter);
        $sub_menu[1] = array('label'=>__('库存报警'),'optional'=>true,'filter'=>$filter,'addon'=>$alert_num_count,'href'=>'index.php?app=b2c&ctl=admin_goods&act=index&view=3&view_from=dashboard');

         foreach($sub_menu as $k=>$v){
            if($v['optional']==false){
            }elseif(($_GET['view_from']=='dashboard')&&$k==$_GET['view']){
                $show_menu[$k] = $v;
            }
        }
        return $show_menu;       
    }

    function import(){
        $this->pagedata['thisUrl'] = 'index.php?app=b2c&ctl=admin_goods&act=index';
        $import = new desktop_finder_builder_import($this);
        $import->main();
        $oGtype = $this->app->model('goods_type');
        $this->pagedata['gtype'] = $oGtype->getList('type_id,name');
        echo $this->page('admin/goods/download.html');
    }
    function showfilter($type_id){

        $goods_filter = kernel::single('b2c_goods_goodsfilter');
        $return = $goods_filter->goods_goodsfilter($type_id,$this->app);
        $this->pagedata['filter'] = $return;


        $this->pagedata['filter_interzone'] = $_POST;
        $this->pagedata['view'] = $_POST['view'];
        $this->display('admin/goods/filter_addon.html');
    }


}
